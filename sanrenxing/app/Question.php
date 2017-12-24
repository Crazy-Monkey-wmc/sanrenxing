<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    //创建问题api
    public function add()
    {
    	//检查用户是否登录
    	if(!user_ins()->is_logged())
    		return ['status'=>0,'msg'=>'login required'];
    	//检查是否存在标题
    	if(!rq('title'))
    		return ['status'=>0,'msg'=>'required title'];
    	$this->title = rq('title');
    	$this->user_id = session('user_id');
    	//检查是否有问题描述
    	if(rq('desc'))
    		$this->desc = rq('desc');

    	return $this->save()?
    	['status'=>1,'id'=>$this->id]:
    	['status'=>0,'msg'=>'db insert failed'];
    	
    }
    //更新问题api
    public function change(){
    	//检查用户是否登录
    	if(!user_ins()->is_logged())
    		return ['status'=>0,'msg'=>'login required'];
    	//检查传参中是否有id
    	if(!rq('id'))
    		return ['status'=>0,'msg'=>'id is required'];
    	//获取指定id的model
    	$question = $this->find(rq('id'));
    	//判断问题是否存在
    	if(!$question)
    		return ['status'=>0,'msg'=>'question is not exists'];
    	//判断此用户是否有权限更改
    	if($question->user_id != session('user_id'))
    		return ['status'=>0,'msg'=>'permission denied'];
    	if(rq('title'))
    		$question->title = rq('title');
    	if(rq('desc'))
    		$question->desc = rq('desc');
    	//存入数据库
    	return $question->save()?
    	['status'=>1]:
    	['status'=>0,'msg'=>'db update failed'];

    }

    public function read_by_user_id($user_id)
    {
        $user = user_ins()->find($user_id);
        if(!$user)
            return err('用户不存在');
        $r = $this->where('user_id',$user_id)
        ->get()
        ->keyBy('id')
        ->toArray();
        return suc($r);
    }
    //查看问题api
    public function read()
    {
    	//检查有没有传入id，有则返回指定问题
    	if(rq('id')){
            $r = $this
            ->with('answers_with_user_info')
            ->find(rq('id'));
    		return ['status'=>1,'data'=>$r];
        }

        if(rq('user_id'))
        {
            $user_id = rq('user_id') === 'self'?
            session('user_id'):
            rq('user_id');
            return $this->read_by_user_id($user_id);
        }
    	//限制每一页有多少数据
    	// $limit = rq('limit')?:15;
    	//skip条件，用于分页
    	// $skip = (rq('page')? rq('page')-1:0)*$limit;
        list($limit,$skip) = paginate(rq('page'),rq('limit'));
    	//构建query并返回collection数据
    	$r = $this
    	->orderBy('created_at')
    	->limit($limit)
    	->skip($skip)
    	->get(['id','title','desc','user_id','created_at','updated_at'])
    	->keyBy('id');
        $r = $r->toArray();
    	return suc($r);


    }
    //删除问题api
    public function remove()
    {
    	if(!user_ins()->is_logged())
    		return ['status'=>0,'msg'=>'login required'];
    	if(!rq('id'))
    		return ['status'=>0,'msg'=>'id is required'];
    	$question = $this->find(rq('id'));
    	if(!$question)
    		return ['status'=>0,'msg'=>'question is not exists'];
    	if(session('user_id') != $question->user_id)
    		return ['status'=>0,'msg'=>'permission denied'];

    	return $question->delete()?
    	['status'=>1]:
    	['status'=>0,'msg'=>'db delete failed'];
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function answers()
    {
        return $this->hasMany('App\Answer');
    }
    public function answers_with_user_info()
    {
        return $this
        ->answers()
        ->with('user')
        ->with('users');
    }
}
