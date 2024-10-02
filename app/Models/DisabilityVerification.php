<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DisabilityVerification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'file_path',
        'is_verified'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}