<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Justification extends Model
{
    use SoftDeletes, Notifiable;
    protected $fillable = [ 'cause', 'state'];

    public function clock(){
        return $this->belongsTo(Clock::class);
    }
}
