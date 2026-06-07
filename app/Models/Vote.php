<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    protected $fillable = ['user_id', 'poll_option_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pollOption()
    {
        return $this->belongsTo(PollOption::class);
    }
}
