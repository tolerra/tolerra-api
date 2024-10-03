<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'thread_id', 
        'name',
        'content'
    ];

    public function thread()
    {
        return $this->belongsTo(Thread::class);
    }

}
