<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ban extends Model
{
    protected $guarded = [];
    
    public static function banTask(int $task)
    {
        return self::create([
            'user_id' => Auth::user()->id,
            'task_id' => $task,
        ]);
    }

    //Relations

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function task()
    {
        return $this->belongsTo('App\Task', 'task_id');
    }
}
