<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class  Member extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
//    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'user_id',
        'groupname',
        'user_avatar',
        'avatar' ,
        'sign',
        'profile',

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
    public function  group(){
        return $this->belongsTo(Group::class,'user_id', 'id');
    }
}
