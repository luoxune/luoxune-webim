<?php

use App\Http\Controllers\Api\AppController;
use App\Http\Controllers\Api\AuthorizationsController;
use App\Http\Controllers\Api\CaptchasController;
use App\Http\Controllers\Api\GatewayEventsController;
use App\Http\Controllers\Api\FriendsController;
use App\Http\Controllers\Api\GroupsController;
use App\Http\Controllers\Api\ImagesController;
use App\Http\Controllers\Api\UsersController;
use App\Http\Controllers\Api\VerificationCodesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v1')
    //命名空间
    ->namespace('Api')
    //别名
    ->name('api.v1.')
    //中间件
    //->middleware('change-locale')
    ->group(function() {
        Route::middleware('throttle:' . config('api.rate_limits.sign'))->group(function() {
            // 短信验证码
            Route::post('verificationCodes', [VerificationCodesController::class, 'store']) ->name('verificationCodes.store');
            // 用户注册
            Route::put('users', [UsersController::class, 'store']) ->name('users.store');
            // 图片验证码
            Route::post('captchas', [CaptchasController::class, 'store'])->name('captchas.store');;
            // 登录
            Route::post('authorizations', [AuthorizationsController::class, 'store'])->name('authorizations.store');

            Route::post('users/show', [UsersController::class, 'show']) ->name('user.show');

            Route::post('groups', [GroupsController::class, 'show']) ->name('group.show');

            // 上传图片
            Route::post('images', [ImagesController::class, 'store'])
                ->name('images.store');

            // 上传文件
            Route::post('files', [AppController::class, 'files'])
                ->name('file.store');
        });
        Route::middleware('throttle:' . config('api.rate_limits.access'))
            ->middleware('token.refresh')
            ->group(function () {
                // 刷新token
                Route::put('authorizations/current', [AuthorizationsController::class, 'update'])->name('authorizations.update');

                // 删除token
                Route::delete('authorizations/current', [AuthorizationsController::class, 'destroy'])->name('authorizations.destroy');
                /**
                 *
                 */
                Route::get('users', [UsersController::class, 'me']) ->name('user.me');

                // update user info
                Route::patch('users', [UsersController::class, 'update']) ->name('users.update');
                //find user

                Route::post('users/find', [UsersController::class, 'find']) ->name('users.find');
                // friends list

                Route::get('list', [AppController::class, 'list']) ->name('lists.show');

                //run app
                Route::post('run', [GatewayEventsController::class, 'run']) ->name('app.run');

                //change sign
                Route::patch('sign', [UsersController::class, 'changeSign']) ->name('app.changeSign');
                /**
                 *
                 */
                // friend add
                Route::post('friends', [FriendsController::class, 'addFriend']) ->name('friend.add');
                // agreeFriend
                Route::put('friends', [FriendsController::class, 'agreeFriend']) ->name('friend.agree');
                // deleteFriend
                Route::delete('friends', [FriendsController::class, 'deleteFriend']) ->name('friend.delete');
                // refuseFriend
                Route::post('friends/refuse', [FriendsController::class, 'refuseFriend']) ->name('friend.refuse');
                // moveFriend
                //Route::post('friends/move', [FriendsController::class, 'moveFriendByID']) ->name('friend.move');
                // moveFriendByName
                Route::post('friends/move', [FriendsController::class, 'moveFriendByName']) ->name('friend.move');
                /**
                 *
                 */
                // create group
                Route::put('groups', [GroupsController::class, 'store']) ->name('groups.create');
                // group
                Route::post('groups', [GroupsController::class, 'show']) ->name('group.show');
                // update group info
                Route::patch('groups', [GroupsController::class, 'update']) ->name('groups.update');
                // destroy group
                Route::delete('groups', [GroupsController::class, 'delete']) ->name('groups.delete');

                // group
                Route::post('groups/add', [GroupsController::class, 'addGroup']) ->name('group.add');

                // agreeGroup
                Route::post('groups/agree', [GroupsController::class, 'agreeGroup']) ->name('group.agree');

                Route::post('groups/refuse', [GroupsController::class, 'refuseGroup']) ->name('group.refuse');
                // destroy group

                Route::post('groups/destroy', [GroupsController::class, 'destroy']) ->name('groups.destroy');

                //find group
                Route::post('groups/find', [GroupsController::class, 'findGroup']) ->name('app.findgroup');



                /**
                 *
                 */
                // message box
                Route::patch('msgbox', [AppController::class, 'msgboxRead']) ->name('msgbox.read');

                // message box
                Route::post('msgbox', [AppController::class, 'msgboxJson']) ->name('msgbox.json');

                // no read
                Route::post('msgbox/noread', [AppController::class, 'msgboxNoread']) ->name('msgbox.noread');

                // chatlog
                Route::post('chatlogs', [AppController::class, 'chatlogs']) ->name('app.chatlog_json');



                // 获取群员列表
                Route::post('members', [GroupsController::class, 'members'])
                    ->name('members');
            });
});
