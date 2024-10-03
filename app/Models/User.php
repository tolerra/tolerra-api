<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name', 
        'role', 
        'email', 
        'password'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function badges()
    {
        return $this->hasMany(Badge::class, 'instructor_id');
    }

    public function courses()
    {
        return $this->hasMany(Course::class, 'instructor_id');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'student_id');
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class, 'student_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function threads()
    {
        return $this->hasMany(Thread::class);
    }

    public function disabilityVerifications()
    {
        return $this->hasOne(DisabilityVerification::class);
    }
}