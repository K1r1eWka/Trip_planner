<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function ownedTrips()
    {
        return $this->hasMany(Trip::class, 'owner_id');
    }

    public function trips()
    {
        return $this->belongsToMany(Trip::class, 'trip_members');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }
}
