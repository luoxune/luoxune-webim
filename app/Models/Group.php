<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
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
        'user_id',
        'number',
        'name',
        'avatar',
        'sign',
        'status',
        'setting',
        'groupname',
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
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function  members(){
        return $this->hasMany(Member::class, 'id', 'id');
    }
}
