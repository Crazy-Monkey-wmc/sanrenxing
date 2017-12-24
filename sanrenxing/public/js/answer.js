;(function(){
	'use strict';
	angular.module('answer',[])
	.service('AnswerService',[
		'$http',
		'$state',
		function($http,$state){
			var me = this;
			me.data = {};
			me.answer_form = {};
				/**
				 * 统计票数
				 * @answers array 用于统计的票数
				 * 先判断是问题还是回答
				 * 如果是问题则跳过统计
				 */
				 me.count_vote = function(answers){
				 	for(var i = 0;i <answers.length;i++){
				 		var votes,item = answers[i];
						//如果没有question_id 和users键则不是回答
						//或者回答没有票数
						if(!item['question_id'])
							continue;

						me.data[item.id] = item;

						if(!item['users'])
							continue;
						//把赞同票和反对票的初始值设为0
						item.upvote_count = 0;
						item.downvote_count = 0;
						votes = item['users'];
						// console.log(votes);
						for(var j = 0;j<votes.length;j++){
							var v = votes[j];
							//如果vote等于1则赞同票加1
							if(v['pivot'].vote === 1)
								item.upvote_count++;
							//如果vote等于2则反对票加1
							if(v['pivot'].vote === 2)
								item.downvote_count++;
						}
					}
					return answers;
				}

				//更新或添加回答
				me.add_or_update = function(question_id)
				{
					console.log(me.answer_form);
					if(!question_id){
						console.log('需要问题id');
						return;
					}
					me.answer_form.question_id = question_id;
					if(me.answer_form.id)
						$http.post('api/answer/change',me.answer_form)
					.then(function(r){
						if(r.data.status){
							me.answer_form = {};
							$state.reload();
							console.log('1');
						}
					})
					else
						$http.post('api/answer/add',me.answer_form)
					.then(function(r){
						if(r.data.status){
							me.answer_form = {};
							$state.reload();
							console.log('1');
						}
					});

				}
				//删除回答
				me.delete = function(id)
				{
					if(!id){
						console.error('需要id');
						return;
					}
					$http.post('api/answer/remove',{id:id})
					.then(function(r){
						if(r.data.status){

							$state.reload();
							console.log('删除成功');
						}
					})
				}

				me.vote = function(conf)
				{
					if(!conf.id || !conf.vote)
					{
						console.log('id and vote are required');
						return;
					}
					// console.log(me.data[conf.id]);
					var answer = me.data[conf.id],
					users = answer.users;

					if(answer.user_id == his.id)
					{
						console.log('在自恋的道路上你也是没谁了');
						return false;
					}
					for(var i = 0;i<users.length;i++){
						if(users[i].id == his.id && conf.vote == users[i].pivot.vote)
							conf.vote =3;
					}

					return $http.post('/api/answer/vote',conf)
					.then(function(r){
						if(r.data.status){
							return true;
						}else if(r.data.msg = 'login required'){
							$state.go('login');
						}
						else return false;
						
					},function(e){
						return false;
					});
				}

				me.update_data = function(id)
				{
					return $http.post('api/answer/read',{id:id})
					.then(function(r){
						// console.log(r.data.data);
						me.data[id] = r.data.data;
					})
				}

				me.read = function(params)
				{
					return $http.post('api/answer/read',params)
					.then(function(r){
						if(r.data.status){
							me.data = angular.merge({},me.data,r.data.data);
							return r.data.data;
						}
					})
				}
				me.add_comment = function()
				{
					return $http.post('api/comment/add',me.new_comment)
					.then(function(r){
						if(r.data.status){
							return true;
						}
						else
							return false;
					})
				}
			}])
	.directive('commentBlock',[
		'AnswerService',
		'$http',
		function(AnswerService,$http){
			var o = {};
			o.templateUrl = 'comment.tpl';
			o.scope = {
				answer_id:'=answerId',
			}
				//sco:这个指令指定的域，ele：用jquery选择后的标签
				o.link = function(sco,ele,attr)
				{
					sco.Answer = AnswerService;
					sco._ = {};
					sco.data = {};
					sco.helper = helper;

					function get_comment_list(){
						return $http.post('api/comment/read',{answer_id:sco.answer_id})
						.then(function(r){
							if(r.data.status){
								sco.data = angular.merge({},sco.data,r.data.data);
							}
						})
					}
					if(sco.answer_id){
						get_comment_list();
					}
					sco._.add_comment = function(){
						AnswerService.new_comment.answer_id = sco.answer_id;
						AnswerService.add_comment()
						.then(function(r){
							if(r){
								AnswerService.new_comment = {};
								get_comment_list();

							}
						})
					}
				}
				return o;
			}])
})();