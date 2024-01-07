<?php

namespace App\Http\Controllers;

use App\Models\Portes;
use Illuminate\Http\Request;

class PortesController extends Controller
{
    // Récupérer tous les portes
    public function index(){
        return response()->json([
            'portes' => Portes::orderBy('numero_porte')->with('user:id,pseudo,image')->get()
        ], 200);
    }

    // Afficher une porte
    public function show($porte_id){
        $porte = Portes::find($porte_id);

        if($porte){
            if($porte->users_id == auth()->user()->id){
                return response()->json([
                    'porte' => Portes::where('id', $porte_id)->with('user:id,pseudo,image')->get()
                ], 200);
            }else{
                return response()->json([
                    'message' => 'Veuillez authentifier !'
                ], 404);    
            }
        }else{
            return response()->json([
                'message' => 'Le numéro de porte introuvable!'
            ], 422);
        }
    }
}
