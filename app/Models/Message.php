<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Jenssegers\Mongodb\Eloquent\Model as Moloquent;

class Message extends Moloquent
{
    use HasFactory, Notifiable;
    protected $connection = 'mongodb';
    protected $collection = 'messages';    //文档名
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
//    protected $primaryKey = 'id';
    protected $fillable = [
        'content',
        'uid',
        'from',
        'read',
        'agree',
        'remark',
        'from_group',
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
