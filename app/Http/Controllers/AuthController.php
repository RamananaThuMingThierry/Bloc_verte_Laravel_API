<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|regex:/^[a-zA-Z0-9\.\-\_]+@[a-zA-Z0-9\.\-\_]+\.[a-zA-Z]+$/',
            'mot_de_passe' => 'required|min:6',
        ]
        // ,
        //  [
        //     'email.required' => 'Le champ d\'email est obligatoire.',
        //     'email.email' => 'Le champ d\'email doit être une adresse email valide.',
        //     'mot_de_passe.required' => 'Le champ de mot de passe est obligatoire.',
        //     'mot_de_passe.min' => 'Le champ de mot de passe doit avoir au mois 6 caractère.',
        // ]
    );

        if($validator->fails()){
            return response()->json([
                'Validation_errors' => $validator->messages(),
            ]);
        }else{
       
            $user = User::where('email', $request->email)->first();

            if(!$user || !Hash::check($request->mot_de_passe, $user->mot_de_passe)){
                return response()->json([
                    'message' => 'Informations d\'identification invalides',
                ], 401);

            }else{
                
                $token = $user->createToken($user->email.'_Token')->plainTextToken;
            
                return response()->json([
                    'pseudo' => $user->pseudo,
                    'token' => $token,
                    'message' => 'Connexion avec succès !',
                ]);
            }

            
        }
    }

    public function register(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'pseudo' => 'required|max:255|unique:users',
            'email' => 'required|email|max:255|unique:users|regex:/^[a-zA-Z0-9\.\-\_]+@[a-zA-Z0-9\.\-\_]+\.[a-zA-Z]+$/',
            'contact' => 'required|string|min:10|max:10|unique:contact',
            'mot_de_passe' => 'required|min:6|confirmed',
        ]
        // , [
        //     'pseudo.required' => 'Le champ pseudo est obligatoire',
        //     'email.required' => 'Le champ email est obligatoire',
        //     'email.unique' => 'L\'adresse email existe déjà!',
        //     'pseudo.unique' => 'Le pseudo existe déjà!',
        //     'mot_de_passe.required' => 'Le mot de passe est obligatoire',
        //     'mot_de_passe.min' => 'Le mot de passe doit avoir au moins 8 caractères!',
        // ]
        );

        if($validator->fails()){
            return response()->json([
                'Validation_errors' => $validator->messages(),
            ]);
        }else{

            $user = User::create([
                'pseudo' => $request->pseudo,
                'email' => $request->email,
                'mot_de_passe' => Hash::make($request->mot_de_passe)
            ]);

           $token = $user->createToken($user->email.'_Token')->plainTextToken;

            return response()->json([
               'pseudo' => $user->pseudo,
               'token' => $token,
                'message' => 'Inscription avec succès !',
            ], 200);
        }
    }
 
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pseudo' => 'required|max:255',
            'contact' => 'required|string|max:10|min:10',
            'genre' => 'required|string',
            'roles' => 'required|string',
            'adresse' => 'required|string',
            'email' => 'required|email|max:255|regex:/^[a-zA-Z0-9\.\-\_]+@[a-zA-Z0-9\.\-\_]+\.[a-zA-Z]+$/',
            'mot_de_passe' => 'required|min:6|confirmed',
        ]);

        if($validator->fails()){
            return response()->json([
                'Validation_errors' => $validator->messages(),
            ]);
        }else{

            $image = $this->saveImage($request->image, 'profiles');

            auth()->user()->update([
                'pseudo' => $request->pseudo,
                'contact' => $request->contact,
                'genre' => $request->genre,
                'roles' => $request->roles,
                'image' => $image,
                'adresse' => $request->adresse,
                'email' => $request->email,
                'mot_de_passe' => Hash::make($request->mot_de_passe)
            ]);
           
            return response()->json([
                'message' => 'Modification avec succès !',
            ], 200);
        }
    }

    public function show(){
        $user = auth()->user();
        if($user){
            return response()->json([
                'user' => $user
            ], 200);
        }else{
            return response()->json([
                'user' => 'Vous n\'êtes pas authentifier'
            ], 404);
        }
    }

    public function logout(){
        auth()->user()->tokens()->delete();
        return response()->json([
            'message' => "Déconnexion effectuée",
        ], 200);
    }
}
