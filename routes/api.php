<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\MoisController;
use App\Http\Controllers\PortesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


/** --------- Public Routes ---------- **/
Route::post("/register", [AuthController::class, 'register']);
Route::post("/login", [AuthController::class, 'login']);

/** --------- Protected Routes --------- **/
Route::group(['middleware' => ['auth:sanctum']], function(){

    /** ---------- Users ---------- */
    Route::get('/users', [AuthController::class, "show"]);
    Route::put('/users', [AuthController::class, "update"]);
    Route::post('/logout', [AuthController::class, "logout"]);

    /** ---------- Portes --------- */
    Route::get("/portes", [PortesController::class, 'index']); // Récupérer tous les portes
    Route::post("/portes", [PortesController::class, 'store']); // Créer une porte
    Route::get("/portes/{id}", [PortesController::class, 'show']); // Afficher une porte
    Route::put("/portes/{id}", [PortesController::class, 'update']); // Modifier une porte
    Route::delete("/portes/{id}", [PortesController::class, 'delete']); // Supprimer une porte
    
    /** ---------- Mois --------- */
    Route::get("/mois", [MoisController::class, 'index']); // Récupérer tous les mois
    Route::post("/mois", [MoisController::class, 'store']); // Créer un mois
    Route::get("/mois/{id}", [MoisController::class, 'show']); // Afficher un mois
    Route::put("/mois/{id}", [MoisController::class, 'update']); // Modifier un mois
    Route::delete("/mois/{id}", [MoisController::class, 'delete']); // Supprimer un mois
    
    /** ---------- Index --------- */
    Route::get("/index", [IndexController::class, 'index']); // Récupérer tous les index
    Route::post("/index", [IndexController::class, 'store']); // Créer un index
    Route::get("/index/{id}", [IndexController::class, 'show']); // Afficher un index
    Route::put("/index/{id}", [IndexController::class, 'update']); // Modifier un index
    Route::delete("/index/{id}", [IndexController::class, 'delete']); // Supprimer un index
});
