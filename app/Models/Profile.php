<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Profile extends Model
{
    use SoftDeletes, Notifiable;
    protected $fillable = ['name', 'tier', 'extern_clock'];

    public function user(){
        return $this->hasMany(User::class);
    }
}
