<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_type', 
        'user_id', 
        'msg', 
        'read'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
