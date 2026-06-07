<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Poll extends Model
{
    protected $fillable = ['trip_id', 'title', 'type', 'is_closed'];

    protected $casts = [
        'is_closed' => 'boolean',
    ];

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    public function options()
    {
        return $this->hasMany(PollOption::class);
    }

    public function hasVoted(User $user): bool
    {
        return $this->options()
            ->whereHas('votes', fn($q) => $q->where('user_id', $user->id))
            ->exists();
    }
}
