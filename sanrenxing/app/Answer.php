<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    //添加回答api
    public function add()
    {
    	//检查用户是否登录
    	if(!user_ins()->is_logged())
    		return ['status'=>0,'msg'=>'login required'];
    	//检查参数中是否存在question_id,content
    	if(!rq('question_id')||!rq('content'))
    		return ['status'=>0,'msg'=>'question_id and content are required'];

    	//检测问题是否存在
    	$question = question_ins()->find(rq('question_id'));
  
    	if(!$question)
    		return ['status'=>0,'msg'=>'question is not exists'];
    	//检查是否重复回答
    	$answered = $this
    	->where(['question_id'=>rq('question_id'),'user_id'=>session('user_id')])
    	->count();
    	if($answered)
    		return['status'=>0,'msg'=>'duplicate_answers'];

    	$this->content = rq('content');
    	$this->question_id = rq('question_id');
    	$this->user_id = session('user_id');

    	return $this->save()?
    	['status'=>1,'id'=>$this->id]:
    	['status'=>0,'msg'=>'db insert failed'];
    }
    //更新回答api
    public function change()
    {
    	if(!user_ins()->is_logged())
    		return ['status'=>0,'msg'=>'login required'];
    	if(!rq('id')||!rq('content'))
    		return ['status'=>0,'msg'=>'id and content are required'];
    	$answer = $this->find(rq('id'));
    	if(!$answer)
    		return ['status'=>0,'msg'=>'answer is not exists'];
    	if($answer->user_id!=session('user_id'))
    		return ['status'=>0,'msg'=>'permission denied'];
    	$answer->content = rq('content');
    	return $answer->save()?
    	['status'=>1]:
    	['status'=>0,'msg'=>'db update failed'];

    }

    public function read_by_user_id($user_id)
    {
        $user = user_ins()->find($user_id);
        if(!$user)
            return err('用户不存在');
        $r = $this
        ->with('question')
        ->where('user_id',$user_id)
        ->get()
        ->keyBy('id')
        ->toArray();
        return suc($r);
    }
    //查看回答api
    public function read()
    {
    	if(!rq('id')&&!rq('question_id') && !rq('user_id'))
    		return ['status'=>0,'msg'=>'id or question_id is required'];

        if(rq('user_id'))
        {
            $user_id = rq('user_id') === 'self' ?
            session('user_id'):
            rq('user_id');
            return $this->read_by_user_id($user_id);
        }
    	if(rq('id'))
    	{
            //查看单个回答
    		$answer = $this
            ->with('user')
            ->with('users')
            ->with('question')
            ->find(rq('id'))
            ;
    		if(!$answer)
    			return ['status'=>0,'msg'=>'answer not exists'];
            $answer = $this->count_vote($answer);
    		return['status'=>1,'data'=>$answer];

    	}

    	if(!question_ins()->find(rq('question_id')))
    		return ['status'=>0,'msg'=>'question not exists'];
    	$answers = $this
    	->where('question_id',rq('question_id'))
    	->get()
    	->keyBy('id');

    	return ['status'=>1,'data'=>$answers];

    }


    //投票api
    public function vote()
    {
        if(!user_ins()->is_logged())
            return ['status'=>0,'msg'=>'login required'];
        if(!rq('id')||!rq('vote'))
            return ['status'=>0,'msg'=>'id and vote are required'];
        $answer = $this->find(rq('id'));
        if(!$answer)
            return ['status'=>0,'msg'=>'answer not exists'];
        //1：赞同，2：反对 3：清空

        $vote = rq('vote');
        if($vote != 1 && $vote != 2 && $vote != 3)
            return err('错误的投票');

        //检查此用户又没有投过票，有就删除投票
        $answer
        ->users()
        ->newPivotStatement()
        ->where('user_id',session('user_id'))
        ->where('answer_id',rq('id'))
        ->delete();

        if($vote == 3)
            return suc();

        //在连接表中增加数据
        $answer->users()
        ->attach(session('user_id'),['vote'=>$vote]);
        return ['status'=>1];

    }

    //删除回答api
    public function remove(){
        if(!user_ins()->is_logged()){
            return err('需要登录');
        }
        if(!rq('id')){
            return err('需要问题id');
        }
        $answer = $this->find(rq('id'));
        if(!$answer){
            return err('问题不存在');
        }
        if(session('user_id') != $answer->user_id){
            return err('没有权限删除');
        }
        // dd($answer->first());
        // 先删除和这条回答的投票信息，才能删除这条回答
        $answer
        ->users()
        ->newPivotStatement()
        ->where('answer_id',rq('id'))
        ->delete();
        return $answer->delete()?
        suc():
        err('数据更新失败');
    }
    //计算投票
    public function count_vote($answer){
        $upvote_count = 0;
        $downvote_count = 0;
        foreach ($answer->users as $user) {
            if($user->pivot->vote == 1){
                $upvote_count++;
            }else {
                $downvote_count++;
            }
        }
        $answer->upvote_count = $upvote_count;
        $answer->downvote_count = $downvote_count;
        return $answer;
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function users()
    {
        return $this
        ->belongsToMany('App\User')
        ->withPivot('vote')
        ->withTimestamps();
        
    }

    public function question()
    {
        return $this
        ->belongsTo('App\Question');
    }
}
