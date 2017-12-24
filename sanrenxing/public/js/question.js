;(function(){
	'use strict';
	angular.module('question',[])
		.service('QusertionService',[
		'$http',
		'$state',
		'AnswerService',
		function($http,$state,AnswerService){
			var me = this;
			me.data = {};
			me.read = function(params)
			{
				return $http.post('api/question/read',params)
				.then(function(r){
					if(r.data.status){
						if(params.id){
							me.data[params.id] = me.current_question = r.data.data;
							me.its_answers = me.current_question.answers_with_user_info;
							me.its_answers = AnswerService.count_vote(me.its_answers);
						}else{
							me.data = angular.merge({},me.data,r.data.data);
						}
						
						return r.data.data;
					}
					return false;

					
				})
			}
			me.go_add_question = function(){
				$state.go('question.add');
			}
			me.add = function(){
				if(!me.new_question.title)
					return;
				$http.post('api/question/add',me.new_question)
					.then(function(r){
						if(r.data.status){
							me.new_question = {};
							$state.go('home');
							// location.href='/';
						}else{
							me.is_not_logged = true;
						}
						
					},function(e){

					})
			}
			//问题详情页中的投票
			me.vote = function(conf){
				//调用核心投票功能
				var $r = AnswerService.vote(conf)
				if($r)
				$r.then(function(r){
					if(r){
						// console.log(r);
						me.update_answer(conf.id);
					}

				})
			}
			me.update_answer = function(answer_id)
			{
				$http.post('api/answer/read',{id:answer_id})
				.then(function(r){
					if(r.data.status){
						for(var i = 0;i<me.its_answers.length; i++)
						{
							var answer = me.its_answers[i];
							// console.log(answer);
							if(answer.id == answer_id)
							{
								// console.log('r.data.data',r.data.data);
								me.its_answers[i] = r.data.data;
								AnswerService.data[answer_id] = r.data.data;
							}
						}
					}
				})
			}
		}])

	.controller('QuestionController',[
		'$scope',
		'QusertionService',
		function($scope,QusertionService){
			$scope.Question = QusertionService;
		}])
	.controller('QuestionAddController',[
		'$scope',
		'QusertionService',
		function($scope,QusertionService){
			$scope.Question = QusertionService;
		}])
	.controller('QuestionDetailController',[
		'$scope',
		'$stateParams',
		'QusertionService',
		'AnswerService',
		function($scope,$stateParams,QusertionService,AnswerService){
			$scope.Answer = AnswerService;
			$scope.Question = QusertionService;
			QusertionService.read($stateParams);
			if($stateParams.answer_id)
				QusertionService.current_answer_id = $stateParams.answer_id;
			else
				QusertionService.current_answer_id = null;

		}])

})()