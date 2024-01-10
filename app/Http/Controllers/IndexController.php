<?php

namespace App\Http\Controllers;

use App\Models\Index;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class IndexController extends Controller
{
    public function index(){
        return response()->json([
            'index' => Index::orderBy('created_at')->with('users:id,pseudo,image,contact')->get()
        ], 200);
    }

    public function store(Request $request){

        $autorisation = false;

        $nouvel_index = $request->nouvel_index;
        $ancien_index = $request->ancien_index;
        $payer = $request->payer;
        $portes_id = $request->portes_id;
        $mois_id = $request->mois_id;

        $validator = Validator::make($request->all(), [
            'nouvel_index' => 'required|numeric|regex:/^\\d+(\\.\\d{1,2})?$/',
            'ancien_index' => 'required|numeric|regex:/^\\d+(\\.\\d{1,2})?$/',
            'payer' => 'required|boolean',
            'portes_id' => 'required|numeric',
            'mois_id' => 'required|numeric',
        ]);

        if($validator->fails()){
            return response()->json([
                'validator_erros' => $validator->messages()
            ]);
        }else{

            // Vérification portes_id si existe 
            $verification_porte = DB::table('portes')
                ->where('id', $portes_id)
                ->exists();

            if($verification_porte == false){
                return response()->json([
                    'message' => 'Ce porte n\'existe pas!'
                ], 403);
            }
            
            // Vérification mois si existe 
            $verification_mois = DB::table('mois')
                ->where('id', $mois_id)
                ->exists();

            if($verification_mois == false){
                return response()->json([
                    'message' => 'Ce mois n\'existe pas!'
                ], 403);
            }

            // Vérifions si la porte_id et mois_id existe dans la base de données
            $verification_porte_mois = DB::table('indices')
                ->where('portes_id', $portes_id)
                ->where('mois_id', $mois_id)
                ->exists();
                
            if($verification_porte_mois == false){
                $autorisation = true;
            }

            if($autorisation){

                if($ancien_index > $nouvel_index){

                    //*********************************** */
                    return response()->json([
                        'message' => 'La valeur de l\'ancien index doit-être toujours inférieur ou égale à la valeur du nouvel index'
                    ], 403);

                }else{
                    
                    $index = Index::create([
                        'nouvel_index' => $nouvel_index,
                        'ancien_index' => $ancien_index,
                        'payer' => $payer,
                        'mois_id' => $mois_id,
                        'portes_id' => $portes_id,
                        'users_id' => auth()->user()->id
                    ]);
        
                    return response()->json([
                        'index' => $index,
                        'message' => 'Enregistrement effectuée!'
                    ], 200);

                }

            }else{
                return response()->json([
                    'message' => 'Cet index existe déjà dans la base de donées!'
                ], 403);
            }
        }
    }

    public function show($id){
       
        $index = Index::find($id);

        if($index){
            if($index->users_id == auth()->user()->id){
                return response()->json([
                    'index' => Index::where('id', $id)->with('users:id,pseudo,contact,image')->get()
                ], 200);
            }else{
                return response()->json([
                    'message' => 'Autorisation refusée!'
                ], 400);
            }
        }else{
            return response()->json([
                'message' => 'Cet index n\'existe pas dans la base de données!'
            ], 400);
        }
    }

    public function update(Request $request, $id){

        $get_index_update = Index::find($id);

        $autorisation = false;
        
        $nouvel_index = $request->nouvel_index;
        $ancien_index = $request->ancien_index;
        $payer = $request->payer;
        $portes_id = $request->portes_id;
        $mois_id = $request->mois_id;


        if($get_index_update){
            $validator = Validator::make($request->all(), [
                'nouvel_index' => 'required|numeric|regex:/^\\d+(\\.\\d{1,2})?$/',
                'ancien_index' => 'required|numeric|regex:/^\\d+(\\.\\d{1,2})?$/',
                'payer' => 'required|boolean',
                'portes_id' => 'required|numeric',
                'mois_id' => 'required|numeric',
            ]);
    
            if($validator->fails()){
                return response()->json([
                    'validator_erros' => $validator->messages()
                ]);
            }else{
    
                // Vérification portes_id si existe 
                $verification_porte = DB::table('portes')
                    ->where('id', $portes_id)
                    ->exists();
    
                if($verification_porte == false){
                    return response()->json([
                        'message' => 'Ce porte n\'existe pas!'
                    ], 403);
                }
                
                // Vérification mois si existe 
                $verification_mois = DB::table('mois')
                    ->where('id', $mois_id)
                    ->exists();
    
                if($verification_mois == false){
                    return response()->json([
                        'message' => 'Ce mois n\'existe pas!'
                    ], 403);
                }
    
                // Vérifions si la porte_id et mois_id existe dans la base de données
                $verification_porte_mois = DB::table('indices')
                    ->where('portes_id', $portes_id)
                    ->where('mois_id', $mois_id)
                    ->exists();
                    
                if($verification_porte_mois){
                    $getPortesMois = DB::table('indices')
                    ->where('portes_id', $portes_id)
                    ->where('mois_id', $mois_id)
                    ->first();
                    
                    if($getPortesMois->portes_id == $get_index_update->portes_id){
                        if($get_index_update->mois_id == $getPortesMois->mois_id){
                            $autorisation = true;
                        }
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
                        
                        $get_index_update->update([
                            'nouvel_index' => $nouvel_index,
                            'ancien_index' => $ancien_index,
                            'payer' => $payer,
                            'mois_id' => $mois_id,
                            'portes_id' => $portes_id,
                            'users_id' => auth()->user()->id
                        ]);
            
                        return response()->json([
                            'index' => $get_index_update,
                            'message' => 'Modification effectuée!'
                        ], 200);
    
                    }

                }else{
                    return response()->json([
                        'message' => 'Cet index existe déjà dans la base de donées!'
                    ], 403);
                }
    
            }
        }else{
            return response()->json([
                'message' => 'Cet index est introuvable'
            ], 403);
        }
    }

    public function delete($id){
        $index = Index::find($id);

        if($index){
            if($index->users_id == auth()->user()->id){
                $index->delete();
                return response()->json([
                    'message' => 'Suppression effecutée!'
                ], 200);
            }else{
                return response()->json([
                    'message' => 'Autorisation refusée!'
                ], 400);
            }
        }else{
            return response()->json([
                'message' => 'Cet index n\'existe pas dans la base de données!'
            ], 400);
        }
    }
}
