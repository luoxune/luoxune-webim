<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Jenssegers\Mongodb\Eloquent\Model as Moloquent;

class Record extends Moloquent
{
    use HasFactory, Notifiable;
    protected $connection = 'mongodb';
    protected $collection = 'records';    //文档名
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
//    protected $primaryKey = 'id';
    protected $fillable = [
        'from_id',
        'from_name',
        'from_avatar',
        'to_id',
        'content',
        'push',
        'type',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [

    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
}
