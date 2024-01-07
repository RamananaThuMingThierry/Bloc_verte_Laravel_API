<?php

namespace App\Models;

use App\Models\Mois;
use App\Models\User;
use App\Models\Portes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Index extends Model
{
    use HasFactory;

    public $table = "indices";

    protected $fillable = [
        "nouvel_index",
        "ancien_index",
        "payer",
        "user_id",
        "portes_id",
        "mois_id"
    ];
    
    public function portes(){
        return $this->belongsTo(Portes::class);
    }

    public function mois(){
        return $this->belongsTo(Mois::class);
    }
    
    public function users(){
        return $this->belongsTo(User::class);
    }
}
