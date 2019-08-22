<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $guarded = [];

    /**
     * Check user_id == 0 for the Task
     */
    public function isClosed()
    {
        return $this->user_id != 0;
    }

    /**
     * Set user_id and status to 0
     */
    public function clear()
    {
        return $this->update([
            'user_id' => 0,
            'status' => 0,
        ]);
    }

    /**
     * Set user_id and status == 1
     */
    public function take(int $user)
    {
        return $this->update([
            'user_id' => $user,
            'status' => 1,
        ]);
    }

    /**
     * Set status == 1
     */
    public function work()
    {
        return $this->update([
            'status' => 1,
        ]);
    }

    /**
     * Set status == 2
     */
    public function check()
    {
        return $this->update([
            'status' => 2,
        ]);
    }

    /**
     * Set status == 3
     */
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
