<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    //添加评论api
    public function add()
    {
    	//判断是否登录
    	if(!user_ins()->is_logged())
    		return ['status'=>0,'msg'=>'login required'];
    	//检查是否有评论内容
    	if(!rq('content'))
    		return ['status'=>0,'msg'=>'empty content'];
    	//检查question_id 和answer_id 有且只能有一个
    	if((!rq('question_id')&&!rq('answer_id'))||
    		(rq('question_id')&&rq('answer_id')))
    		return ['status'=>0,'msg'=>'question_id or answer_id is required one'];

    	
    	if(rq('question_id')){
    		$question = question_ins()->find(rq('question_id'));
    		//检查问题是否存在
    		if(!$question)
    			return ['status'=>0,'msg'=>'question not exists'];
    		$this->question_id = rq('question_id');
    	}else{
    		$answer = answer_ins()->find(rq('answer_id'));
    		//检查回答是否存在
    		if(!$answer)
    			return ['status'=>0,'msg'=>'answer not exists'];
    		$this->answer_id = rq('answer_id');
    	}
    	//检查是否在回复评论
    	if(rq('reply_to')){

    		$target = $this->find(rq('reply_to'));
    		//检查目标是否存在
    		if(!$target)
    			return ['staus'=>0,'msg'=>'target comment not exists'];
    		//检查是否在自己回答自己
    		if($target->user_id == session('user_id'))
    			return ['status'=>0,'msg'=>'can not reply to youself'];
    		$this->reply_to = rq('reply_to');
    	}
    	$this->content = rq('content');
    	$this->user_id = session('user_id');
    	//存入数据
    	return $this->save()?
    	['status'=>1,'id'=>$this->id]:
    	['status'=>0,'msg'=>'db insert failed'];



    }
    //查看评论api
    public function read()
    {
    	if((!rq('question_id')&&!rq('answer_id'))||
    		(rq('question_id')&&rq('answer_id')))
    		return ['status'=>0,'msg'=>'question_id or answer_id is required one'];
    	if(rq('question_id')){
    		$question = question_ins()
            ->with('user')
            ->find(rq('question_id'));
    		if(!$question)
    			return ['status'=>0,'msg'=>'question not exists'];
    		$data = $this
            ->with('user')
            ->where('question_id',rq('question_id'));
    	}else{
    		$answer = answer_ins()
            ->with('user')
            ->find(rq('answer_id'));
    		if(!$answer)
    			return ['status'=>0,'msg'=>'answer not exists'];
    		$data = $this
            ->with('user')
            ->where('answer_id',rq('answer_id'));
    	}
    	return ['status'=>1,'data'=>$data->get()->keyBy('id')];
    }
    //删除评论api
    public function remove()
    {
    	if(!user_ins()->is_logged())
    		return ['status'=>0,'msg'=>'login required'];
    	if(!rq('id'))
    		return['status'=>0,'msg'=>'id is required'];
    	$comment = $this->find(rq('id'));
    	if(!$comment)
    		return ['status'=>0,'msg'=>'comment not exists'];
    	if($comment->user_id != session('user_id'))
    		return ['status'=>0,'msg'=>'rermission denied'];
    	//先删除此评论下所有的回复
    	$this->where('reply_to',rq('id'))->delete();
    	return $comment->delete()?
    	['status'=>1]:
    	['status'=>0,'msg'=>'db delete failed'];
    }

    public function user(){
        return $this->belongsTo('App\User');
    }
}
