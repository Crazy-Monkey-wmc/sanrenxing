;(function(){
	'use strict'
	angular.module('common',[])
		.service('TimelineService',[
		'$http',
		'AnswerService',
		function($http,AnswerService){
			var me = this;
			me.data = [];
			me.current_page = 1;
			me.no_more_data = false;

			
			me.get = function(conf){

				if(me.pending || me.no_more_data) return;
				me.pending = true;

				conf = conf || {page:me.current_page}

				$http.post('/api/timeline',conf)
				.then(function(r){
					// console.log(r);
					if(r.data.status){
						
						if(r.data.data.length){

							me.data = me.data.concat(r.data.data);
							me.data = AnswerService.count_vote(me.data);
							
							me.current_page++;
						}
						else{
							me.no_more_data = true;
						}
						
					}

					else console.log('network error');
				},function(e){
					console.log('network error');
				})
				.finally(function(){
					me.pending = false;
				})
			}

			me.vote = function(conf){
				var $r = AnswerService.vote(conf)
				if($r)
				$r.then(function(r){
					if(r)
						AnswerService.update_data(conf.id);

				})
			}

			me.reset_state = function(){
				me.data = [];
				me.current_page = 1;
				me.no_more_data = false;
			}
		}])
	.controller('HomeController',[
		'$scope',
		'TimelineService',
		'AnswerService',
		function($scope,TimelineService,AnswerService){
			$scope.Timeline = TimelineService;
			TimelineService.reset_state();
			TimelineService.get();

			$(window).on('scroll',function(){
				if($(window).scrollTop()-($(document).height() -$(window).height()) == 0){
					TimelineService.get();
				}
			})
			$scope.$watch(function(){
				return AnswerService.data;
			},function(new_data,old_data){
				
				for(var k in new_data)
				{


					for(var i = 0; i<TimelineService.data.length;i++ )
					{

						if(k == TimelineService.data[i].id){
							TimelineService.data[i] = new_data[k];
						}
					}
				}
				TimelineService.data = AnswerService.count_vote(TimelineService.data);
			},true)
		}])
})()