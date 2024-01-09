<?php

namespace App\Http\Controllers;

use App\Models\Mois;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MoisController extends Controller
{
    public function index(){
        return response()->json([
            'mois' => Mois::orderBy('created_at')->with('users:id,pseudo,image,contact')->get()
        ], 200);
    }

    public function store(Request $request){

        
        $autorisation = false;
       
        $nom_mois = $request->nom_mois;
        $date_mois = $request->date_mois;

        $date = Carbon::parse($date_mois);
        $year = $date->format("Y");

        $montant_mois = $request->montant_mois;
        $nouvel_index = $request->nouvel_index;
        $ancien_index = $request->ancien_index;
        $payer = $request->payer;
        
        $validator = Validator::make($request->all(), [
            'nom_mois' => 'required|string',
            'date_mois' => 'required|date',
            'montant_mois' => 'required|numeric|regex:/^\\d+(\\.\\d{1,2})?$/',
            'nouvel_index' => 'required|numeric|regex:/^\\d+(\\.\\d{1,2})?$/',
            'ancien_index' => 'required|numeric|regex:/^\\d+(\\.\\d{1,2})?$/',
            'payer' => 'required|boolean'
        ]);

        if($validator->fails()){
            return response()->json([
                'validator_erros' => $validator->messages()
            ]);
        }else{

            $verification_nom_mois = DB::table('mois')->where('nom_mois', $nom_mois)->exists();

            
            if($verification_nom_mois){
                $get_mois = DB::table('mois')->where('nom_mois', $nom_mois)->first();

                $get_date = Carbon::parse($get_mois->date_mois);
                $get_year = $get_date->format("Y");

                if($year != $get_year){
                    $autorisation = true;
                }
            }else{

                $autorisation = true;
            }

            if($autorisation){

                if($ancien_index > $nouvel_index){

                    //*********************************** */
                    return response()->json([
                        'message' => 'La valeur de l\'ancien index doit-être toujours inférieur ou égale à la valeur du nouvel index'
                    ], 403);

                }else{
                    
                    $mois = Mois::create([
                        'nom_mois' => $nom_mois,
                        'date_mois' => $date_mois,
                        'montant_mois' => $montant_mois,
                        'nouvel_index' => $nouvel_index,
                        'ancien_index' => $ancien_index,
                        'payer' => $payer,
                        'users_id' => auth()->user()->id
                    ]);
        
                    return response()->json([
                        'mois' => $mois,
                        'message' => 'Enregistrement effectuée!'
                    ], 200);

                }

            }else{

                return response()->json([
                    'message' => 'Ce mois existe déjà!'
                ], 403);

            }
        }
    }

    public function show($mois_id){

        $mois = Mois::find($mois_id);

        if($mois){
            if($mois->users_id == auth()->user()->id){
                return response()->json([
                    'mois' => Mois::where('id', $mois_id)->with('users:id,pseudo,contact,image')->get()
                ], 200);
            }else{
                return response()->json([
                    'message' => 'Autorisation refusée!'
                ], 400);
            }
        }else{

        }
    }

    public function update(Request $request, $mois_id){

        $autorisation = false;
        
        $get_mois_update = Mois::find($mois_id);
       
        $get_date_mois = $get_mois_update->date_mois;
        $get_date = Carbon::parse($get_date_mois);
        $get_year = $get_date->format("Y");

        $nom_mois = $request->nom_mois;
        $date_mois = $request->date_mois;

        $date = Carbon::parse($date_mois);
        $year = $date->format('Y');

        $montant_mois = $request->montant_mois;
        $nouvel_index = $request->nouvel_index;
        $ancien_index = $request->ancien_index;
        $payer = $request->payer;

        // Vérifier si ce mois existe ou pas
        if($get_mois_update){

            if($get_mois_update->users_id == auth()->user()->id){
                
                $validator = Validator::make($request->all(), [
                    'nom_mois' => 'alpha|required|string',
                    'date_mois' => 'required|date',
                    'montant_mois' => 'required|numeric|regex:/^\\d+(\\.\\d{1,2})?$/',
                    'nouvel_index' => 'required|numeric|regex:/^\\d+(\\.\\d{1,2})?$/',
                    'ancien_index' => 'required|numeric|regex:/^\\d+(\\.\\d{1,2})?$/',
                    'payer' => 'required|boolean', 
                ]);
        
                if($validator->fails()){
                    return response()->json([
                        'validator_erros' => $validator->messages()
                    ]);
                }else{


                    // Verification nom mois et année
                    if($nom_mois == $get_mois_update->nom_mois){
                        $autorisation = true;
                    }else{
                        // Verification si le nom mois existe dans la base de données
                        $verification_nom_mois = DB::table('mois')->where('nom_mois', $nom_mois)->exists();

                        if($verification_nom_mois){
                            
                            $get_mois_verifier = DB::table('mois')->where('nom_mois', $nom_mois)->first();

                            $get_mois_verifier_date = Carbon::parse($get_mois_verifier->date_mois);
                            $get_mois_verifier_year = $get_mois_verifier_date->format("Y");

                            if($get_mois_verifier_year != $year){
                                $autorisation = true;
                            }
                        }else{
                            $autorisation = true;
                        }
                    }
                    
                    if($autorisation){

                        if($ancien_index > $nouvel_index){

                            //*********************************** */
                            return response()->json([
                                'message' => 'La valeur de l\'ancien index doit-être toujours inférieur ou égale à la valeur du nouvel index'
                            ], 403);
        
                        }else{
                            $get_mois_update->update([
                                'nom_mois' => $nom_mois,
                                'date_mois' => $date_mois,
                                'montant_mois' => $montant_mois,
                                'nouvel_index' => $nouvel_index,
                                'ancien_index' => $ancien_index,
                                'payer' => $payer,
                                'users_id' => auth()->user()->id
                            ]);
                
                            return response()->json([
                                'mois' => $get_mois_update,
                                'message' => 'Enregistrement effectuée!'
                            ], 200);
                        }        
                    }else{
                        return response()->json([
                            'message' => 'Ce mois existe déjà!'
                        ],  403);
                    }
                }
            }else{
                return response()->json([
                    'message' => 'Autorisation refusée.'
                ],  403);
            }
        }else{
            return response()->json([
                'message' => 'Ce mois est introuvable!'
            ],  403);
        }

    }
   
    public function delete($mois_id){

        $mois = Mois::find($mois_id);

        if($mois){
            if($mois->users_id == auth()->user()->id){
                $mois->delete();
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
                'message' => 'Ce mois est un introuvable!'
            ], 403);
        }
    }

}
