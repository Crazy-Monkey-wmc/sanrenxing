<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Request;
use Hash;

class User extends Model
{
    //注册api
    public function signup()
    {
        $check = $this->has_username_and_password();
        if(!$check)
            return err('用户名和密码皆不可为空');
        else{
            $username = $check[0];
            $password = $check[1];
        }
        
        /*检查用户名是否存在*/
        $user_exists = $this
            ->where('username',$username)
            ->exists();
        if($user_exists)
            return err('用户名已存在');
    
        //加密密码
        $hashed_password = bcrypt($password);

        
        //存入数据库
        $user = $this;
        $user->password = $hashed_password;
        $user->username = $username;
        if($user->save()){
            return suc(['id'=>$user->id]);
        }else{
            return err('存入数据库失败');
        }
        return 1;        
    }

    //获取用户信息api
    public function read()
    {
        if(!rq('id'))
            return err('需要用户id');
        if(rq('id') === 'self'){
            if(!$this->is_logged()){
                return err('需要登录');
            }else{
                $id = session('user_id');
            }            
        }else{
           $id = rq('id'); 
        }

        $get = ['id','username','avatar_url','intro'];
        $user = $this->find($id,$get);
        $data = $user->toArray();
        
        $answer_count = answer_ins()->where('user_id',$id)->count();
        $question_count = question_ins()->where('user_id',$id)->count();
        $data['answer_count'] = $answer_count;
        $data['question_count'] = $question_count;
        // dd($data);
        return suc($data);
    }
    //登录api
    public function login()
    {
        //检查用户名和密码是否存在
        $check = $this->has_username_and_password();
        if(!$check)
            return err('用户名和密码皆不可为空');
        $username = $check[0];
        $password = $check[1];
        //检查用户是否存在
        $user = $this->where('username',$username)->first();
        if(!$user)
            return err('用户不存在');
        //检查密码是否正确
        $hashed_password = $user->password;
        if(!Hash::check($password,$hashed_password))
            return err('密码错误');
        //将用户信息写入session
        session()->put('username',$user->username);
        session()->put('user_id',$user->id);
        //dd(session()->all());
        return suc();

    }
    //登出api
    public function logout()
    {
        session()->forget('username');
        session()->forget('user_id');

        // session()->put('username',null);
        // session()->put('user_id',null);
        return suc();
    }

    public function has_username_and_password()
    {
        $username = rq('username');
        $password = rq('password');
        /*检查用户名和密码是否为空*/
        if($username&&$password)
            return [$username,$password];
        return false;
            
    }
    //检测用户是否登录
    public function is_logged()
    {
        //如果session中存在user_id,就返回user_id,否则返回false
        return session('user_id') ?:false;

    }
    //修改密码api
    public function change_password()
    {
        if(!$this->is_logged())
            return err('需要登录');
        if(!rq('old_password')||!rq('new_password'))
            return err('需要旧密码和新密码');
        $user = $this->find(session('user_id'));

        if(!Hash::check(rq('old_password'),$user->password))
            return err('旧密码错误');
        $user->password = bcrypt(rq('new_password'));
        return $user->save()?
        suc():
        err('数据更新失败');
    }
    //找回密码api
    public function reset_password()
    {
        if($this->is_robot())
            return err('操作频繁');

        if(!rq('phone'))
            return err('需要电话号码');
        $user = $this->where('phone',rq('phone'))->first();
        if(!$user)
            return err('电话号码不存在');

        //生成验证码
        $captcha = $this->generate_captcha();
        $user->phone_captcha = $captcha;
        if($user->save()){
            //如果验证码保存成功则发送短信
            $this->send_sms();
            $this->update_robot_time();
            return suc();            
        }else{
            return err('数据更新失败');
        }
    }
    //检查是否是机器人,默认设置10秒
    public function is_robot($time = 10)
    {
        if(!session('$last_action_time'))
            return false;
        $current_time = time();
        
        $last_active_time = session('$last_action_time');

        $realtime = $current_time - $last_active_time;
        return ($realtime < $time);
    }
    //更新机器人行为时间
    public function update_robot_time()
    {
        session()->put('$last_action_time',time());



    }
    //验证验证码api
    public function validate_reset_password()
    {
        if($this->is_robot(2))
            return err('操作频繁');
        if(!rq('phone')||!rq('phone_captcha')||!rq('new_password'))
            return err('没有传入电话号码、验证码和新密码');
        $user = $this->where(['phone'=>rq('phone'), 
            'phone_captcha'=>rq('phone_captcha')
            ])->first();
        if(!$user) return err('电话号码或验证码错误');

        $user->password = bcrypt(rq('new_password'));
        $this->update_robot_time();
        return $user->save()?
        suc():err('数据更新失败');

    }
    //用户名是否存在
    public function exists()
    {
        $count = $this->where(rq())->count();
        $data=['count'=>$count];
        return suc($data);
    }
    
    //发送短信
    public function send_sms()
    {
        return true;
    }
    //生成验证码
    public function generate_captcha()
    {
        return rand(1000,9999);
    }

    public function answers()
    {
        return $this
        ->belongsToMany('App\Answer')
        ->withPivot('vote')
        ->withTimestamps();
    }

}
