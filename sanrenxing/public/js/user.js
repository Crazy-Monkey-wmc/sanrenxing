;(function(){
	'use strict';
	angular.module('user',[
		'answer'])
		.service('UserService',['$http',
			'$state',
			function($http,$state){
				var me = this;
				me.signup_data = {};
				me.data = {};

				me.read = function(params){
					return $http.post('api/user/read',params)
					.then(function(r){
						// console.log('r',r);
						if(r.data.status){
							me.current_user = r.data.data;
							me.data[params.id] = r.data.data;

							
						}else{
							if(r.data.msg == '需要登录')
								$state.go('login');
						}
						
					})
				}

				me.signup = function(){
					$http.post('api/signup',
						me.signup_data)
					.then(function(r){
						if(r.data.status)
						{
							me.signup_data = {};
							$state.go('login');
						}

					},function(e){

					})
					
				}
				me.username_exists = function(){
					$http.post('/api/user/exists',
						{username:me.signup_data.username})
					.then(function(r){
						if(r.data.status && r.data.data.count)
							me.signup_username_exists = true;
						else
							me.signup_username_exists = false;

					},function(e){
						console.log('e',e);
					})
				}

				me.login_data = {};
				me.login = function(){
					$http.post('api/login',
						me.login_data)
					.then(function(r){
						if(r.data.status)
						{
							location.href='/';

						}
						else{
							me.login_failed = true;
						}

					},function(e){

					})
				}
			}
			])
		.controller('SignupController',[
			'$scope','UserService',
			function($scope,UserService){
				$scope.User = UserService;

				$scope.$watch(function(){
					return UserService.signup_data;
				},function(n,o){
					if(n.username != o.username)
						UserService.username_exists();
				},true)
			}
			])
		.controller('LoginController',[
		'$scope','UserService',
		function($scope,UserService){
			$scope.User = UserService;


		}
		])
		.controller('UserController',[
			'$scope',
			'$stateParams',
			'AnswerService',
			'UserService',
			'QusertionService',
			function($scope,$stateParams,AnswerService,UserService,QusertionService){
				$scope.User = UserService;
				console.log('$stateParams',$stateParams);
				UserService.read($stateParams);
				AnswerService.read({user_id:$stateParams.id})
				.then(function(r){
					if(r)
						UserService.his_answers = r;
				});
				QusertionService.read({user_id:$stateParams.id})
				.then(function(r){
					if(r)
						UserService.his_question = r;
				})
			}])
})();