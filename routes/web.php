<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/', function (){
    return view('index');
})->name('index');
Route::get('login', function (){
    return view('login');
})->name('login.view');

Route::get('reg',function (){
    return view('reg');
})->name('reg.view');
Route::prefix('pc/v1')
    //命名空间
    ->namespace('PC')
    //别名
    ->name('web.v1')
    //中间件
    //->middleware('change-locale')
    ->group(function() {
        Route::get('app', function () {
            return view('pc.app');
        })->name('pc.app.view');

        Route::get('msgbox', function (){
            return view('pc.pages.msgbox');
        }) ->name('pc.msgbox.view');

        Route::get('chatlogs', function (){
            return view('pc.pages.chatlog');
        }) ->name('pc.chatlogs.view');

        Route::get('users', function (){
            return view('pc.pages.mine_info');
        })->name('pc.users.info.view');

        Route::get('friends', function (){
            return view('pc.pages.friend_info');
        })->name('pc.friends.info.view');

        Route::get('groups', function (Request $request){
            return view('pc.pages.group_info');
        })->name('pc.groups.info.view');

        Route::get('groups/edit', function (Request $request){
            return view('pc.pages.group_edit');
        })->name('pc.groups.edit.view');

        Route::get('groups/create', function (){
            return view('pc.pages.group_create');
        })->name('pc.groups.create.view');
    });
Route::prefix('mobile/v1')
    //命名空间
    ->namespace('Mobile')
    //别名
    ->name('mobile.v1')
    //中间件
    //->middleware('change-locale')
    ->group(function() {
        Route::get('login', function (Request $request){
            return view('mobile.pages.login');
        })->name('mobile.login.view');

        Route::get('reg',function (Request $request){
            return view('mobile.pages.reg');
        })->name('mobile.reg.view');

        Route::get('app', function (Request $request) {
            return view('mobile.app');
        })->name('mobile.app.view');

        Route::get('msgbox', function (){
            return view('mobile.pages.msgbox');
        }) ->name('msgbox.view');

        Route::get('chatlogs', function (){
            return view('mobile.pages.chatlog');
        }) ->name('mobile.chatlogs.view');

        Route::get('groups/info', function (Request $request){
            return view('mobile.pages.group_info');
        })->name('mobile.groups.info.view');

        Route::get('groups/edit', function (Request $request){
            return view('mobile.pages.group_edit');
        })->name('mobile.groups.edit.view');

        Route::get('groups/create', function (){
            return view('mobile.pages.group_create');
        })->name('mobile.groups.create.view');
    });
