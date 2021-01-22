<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Jenssegers\Mongodb\Eloquent\Model as Moloquent;

class Number extends Moloquent
{
    use HasFactory, Notifiable;
    protected $connection = 'mongodb';
    protected $collection = 'numbers';    //文档名
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
//    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'used',
        'lock',
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
