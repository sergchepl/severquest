<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'login', 'password', 'read_rules', 'score',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function isAdmin()
    {
        return (int) $this->is_admin === 1;
    }

    public function readRules($score)
    {
        return $this->update([
            'score' => $score,
            'read_rules' => true,
        ]);
    }

    public function clear()
    {
        return $this->update([
            'score' => 0,
        ]);
    }

    public function addScore($score)
    {
        return $this->update([
            'score' => $this->score + $score,
        ]);
    }

    //Relations

    public function task()
    {
        return $this->hasOne('App\Task');
    }

    public function completed_tasks()
    {
        return $this->hasMany('App\Task')->whereStatus(3);
    }
    public function bans()
    {
        return $this->hasMany('App\Ban');
    }
}
