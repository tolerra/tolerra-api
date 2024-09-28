<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Progress extends Model
{
    use HasFactory;

    protected $fillable = [
        'enrollment_id', 
        'chapter_id', 
        'isFinish'
    ];

    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }
}
