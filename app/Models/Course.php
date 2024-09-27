<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Rating;
use App\Models\Category;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'instructor_id',
        'desc',
        'brief',
        'image',
        'category_id',
        'isValidated',
    ];

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function averageRating()
    {
        return $this->ratings()->avg('rating');
    }

}
