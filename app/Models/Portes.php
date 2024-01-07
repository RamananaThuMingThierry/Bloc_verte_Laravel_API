<?php

namespace App\Models;

use App\Models\User;
use App\Models\Index;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Portes extends Model
{
    use HasFactory;

    public $table = "portes";

    protected $fillable = [
        'numero_porte',
        'nom',
        'contact',
        'image',
        'users_id'
    ];

    public function users(){
        return $this->belongsTo(User::class);
    }

    public function index(){
        return $this->hasMany(Index::class);
    }
}
