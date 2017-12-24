<?php

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
function paginate($page = 1,$limit = 15)
{
	$limit = $limit? $limit:16;
	$skip = ($page? $page-1:0)*$limit;
	return [$limit,$skip];
}
function rq($key=null,$default=null)
{
	if(!$key) return Request::all();
	return Request::get($key,$default);
}
function user_ins(){
	return new App\User;
}
function question_ins(){
	return new App\Question;
}
function answer_ins(){
	return new App\Answer;
}
function comment_ins(){
	return new App\Comment;
}
function err($msg = null)
{
	return ['status'=>0,'msg'=>$msg];
}
function suc($data_to_merge = [])
{
	$data = ['status'=>1,'data'=>[]];
	if($data_to_merge){
		$data['data'] = array_merge($data['data'],$data_to_merge);
	}
		return $data;
		
}
function is_logged()
{
    //如果session中存在user_id,就返回user_id,否则返回false
    return session('user_id') ?:false;

}

Route::get('/', function () {
    return view('index');
});
//用户路由模块
Route::any('api/signup',function()
{
	
	return user_ins()->signup();
});
Route::any('api/login',function()
{
	return user_ins()->login();
});
Route::any('api/logout',function()
{
	return user_ins()->logout();
});
Route::any('api/user/change_password',function()
{
	return user_ins()->change_password();
});
Route::any('api/user/reset_password',function()
{
	return user_ins()->reset_password();
});
Route::any('api/user/validate_reset_password',function()
{
	return user_ins()->validate_reset_password();
});
Route::any('api/user/read',function()
{
	return user_ins()->read();
});
Route::any('api/user/exists',function()
{
	return user_ins()->exists();
});

//问题路由模块
Route::any('api/question/add',function()
{
	return question_ins()->add();
});
Route::any('api/question/change',function()
{
	return question_ins()->change();
});
Route::any('api/question/read',function()
{
	return question_ins()->read();
});
Route::any('api/question/remove',function()
{
	return question_ins()->remove();
});

//回答路由模块
Route::any('api/answer/add',function()
{
	return answer_ins()->add();
});
Route::any('api/answer/change',function()
{
	return answer_ins()->change();
});
Route::any('api/answer/read',function()
{
	return answer_ins()->read();
});

Route::any('api/answer/vote',function()
{
	return answer_ins()->vote();
});

Route::any('api/answer/remove',function()
{
	return answer_ins()->remove();
});

//评论模块路由
Route::any('api/comment/add',function()
{
	return comment_ins()->add();
});

Route::any('api/comment/read',function()
{
	return comment_ins()->read();
});
Route::any('api/comment/remove',function()
{
	return comment_ins()->remove();
});

Route::any('api/timeline','CommonController@timeline');


Route::any('test',function()
{
	dd(user_ins()->is_logged());
});

Route::get('tpl/page/home',function()
{
	return view('page.home');
});

Route::get('tpl/page/login',function()
{
	return view('page.login');
});

Route::get('tpl/page/signup',function()
{
	return view('page.signup');
});
Route::get('tpl/page/question_add',function()
{
	return view('page.question_add');
});
Route::get('tpl/page/question_detail',function()
{
	return view('page.question_detail');
});
Route::get('tpl/page/user',function()
{
	return view('page.user');
});


