<?php

namespace App\Http\Controllers;

use App\Models\Portes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PortesController extends Controller
{
    // Récupérer tous les portes
    public function index(){
        return response()->json([
            'portes' => Portes::orderBy('numero_porte')->with('users:id,pseudo,image,contact')->get()
        ], 200);
    }

    // Ajouter une porte
    public function store(Request $request){

        
        $numero_porte = $request->numero_porte;
        $nom = $request->nom;
        $contact = $request->contact;
        $image = $this->saveImage($request->image, "portes");

        $validator = Validator::make($request->all(), [
            'numero_porte' => 'required|numeric|unique:portes',
            'nom' => 'alpha|required|string|max:100',
            'contact' => 'required|string|max:10|min:10|unique:portes'
        ]);        
        
        if($validator->fails()){
            return response()->json([
                'validator_errors' => $validator->messages(),
            ]);
        }else{

            $porte = Portes::create([
                'numero_porte' => $numero_porte,
                'nom' => $nom,
                'contact' => $contact,
                'image' => $image,
                'users_id' => auth()->user()->id
            ]);

            return response()->json([
                'portes' => $porte,
                'message' => 'Enregistrement effectuée!'
            ], 200);
        }
    }

    // Modifier une porte
    public function update(Request $request, $porte_id){

        $porte = Portes::find($porte_id);
        $autorisation = false;
        $numero_porte = $request->numero_porte;
        $nom = $request->nom;
        $contact = $request->contact;
        $image = $this->saveImage($request->image, "portes");
        
        if($porte){

            if($porte->users_id == auth()->user()->id){
                
                $validator = Validator::make($request->all(), [
                    'numero_porte' => 'required|numeric',
                    'nom' => 'alpha|required|string|max:100',
                    'contact' => 'required|string|max:10|min:10'
                ]);        
                
                if($validator->fails()){
                    return response()->json([
                        'validator_errors' => $validator->messages(),
                    ]);
                }else{
        
                    $verifier_numero_porte = DB::table('portes')->where('numero_porte', $numero_porte)->exists();
                    
                    if($verifier_numero_porte){

                        $get_numero_porte = DB::table('portes')->where('numero_porte', $numero_porte)->first();

                        if($get_numero_porte->numero_porte == $porte->numero_porte){
                            $autorisation = true;
                        }
                    }else{
                            $autorisation = true;
                    }
                    
                    if($autorisation){
                        
                        $porte->update([
                            'numero_porte' => $numero_porte,
                            'nom' => $nom,
                            'contact' => $contact,
                            'image' => $image,
                            'users_id' => auth()->user()->id
                        ]);
            
                        return response()->json([
                            'portes' => $porte,
                            'message' => 'Modification effectuée!'
                        ], 200);

                    }else{
                        return response()->json([
                            'message' => 'Le numéro de porte apparient à un autre personne'
                        ], 403);
                    }
                }
            }else{
                return response()->json([
                    'message' => 'Autorisation refusée.'
                ],  403);
            }
        }else{
            return response()->json([
                'message' => 'Le numéro de porte introuvable!'
            ],  403);
        }
    }
    
    // Afficher une porte
    public function show($porte_id){
        $porte = Portes::find($porte_id);

        if($porte){
            if($porte->users_id == auth()->user()->id){
                return response()->json([
                    'porte' => Portes::where('id', $porte_id)->with('users:id,pseudo,contact,image')->get()
                ], 200);
            }else{
                return response()->json([
                    'message' => 'Veuillez authentifier !'
                ], 404);    
            }
        }else{
            return response()->json([
                'message' => 'Le numéro de porte introuvable!'
            ], 403);
        }
    }

    // Supprimer une porte
    public function delete($porte_id){
        $porte = Portes::find($porte_id);

        if($porte){
            if($porte->users_id == auth()->user()->id){
                $porte->delete();
                return response()->json([
                    'message' => 'Suppresion refusée!'
                ], 200);   
            }else{
                return response()->json([
                    'message' => 'Autorisation refusée!'
                ], 403);    
            }
        }else{
            return response()->json([
                'message' => 'Le numéro de porte introuvable!'
            ], 403);
        }
    }
}
