<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $guarded = [];

    public function clear()
    {
        return $this->update([
            'user_id' => 0,
            'status' => 0,
        ]);
    }

    public function take(int $user)
    {
        return $this->update([
            'user_id' => $user,
            'status' => 1,
        ]);
    }

    public function work()
    {
        return $this->update([
            'status' => 1,
        ]);
    }

    public function check()
    {
        return $this->update([
            'status' => 2,
        ]);
    }

    public function done()
    {
        return $this->update([
            'status' => 3,
        ]);
    }

    // Relations

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function ban()
    {
        return $this->hasMany(Ban::class);
    }
}
