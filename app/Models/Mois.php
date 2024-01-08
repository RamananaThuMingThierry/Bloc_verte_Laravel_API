<?php

namespace App\Models;

use App\Models\User;
use App\Models\Index;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Mois extends Model
{
    use HasFactory;

    public $table = "mois";

    protected $fillable = [
        "nom_mois",
        "date_mois",
        "montant_mois",
        "nouvel_index",
        "ancien_index",
        "payer",
        "user_id"
    ];

    public function users(){
        return $this->belongsTo(User::class);
    }

    public function index(){
        return $this->hasMany(Index::class);
    }
}
