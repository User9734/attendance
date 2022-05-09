<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Clock extends Model
{
    use SoftDeletes, Notifiable;
    /* protected $fillable = ['hour_start', 'hour_end']; */
    public function user(){
        return $this->belongsTo(User::class);
    }

    public function justif(){
        return $this->hasMany(Justification::class, 'clock_id');
    }
}
