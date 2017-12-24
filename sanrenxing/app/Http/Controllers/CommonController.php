<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CommonController extends Controller
{
    //
    public function timeline()
    {
    	//时间线api
    	list($limit,$skip) = paginate(rq('page'),rq('limit'));
    	//获取问题数据
    	$questions = question_ins()
    	->with('user')
    	->limit($limit)
    	->skip($skip)
    	->orderBy('created_at','desc')
    	->get();
    	//获取回答数据
    	$answers = answer_ins()
    	->with('users')
    	->with('user')
        ->with('question')
    	->limit($limit)
    	->skip($skip)
    	->orderBy('created_at','desc')
    	->get();

    	$data = $questions->merge($answers);
    	$data = $data->sortByDesc(function($item){
    		return $item->created_at;
    	});
    	$data = $data->toArray();
    	// $data = $data->values()->all();
    	return suc($data);
    }
}
