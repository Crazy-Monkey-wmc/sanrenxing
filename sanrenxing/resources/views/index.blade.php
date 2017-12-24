<!DOCTYPE html>
<html ng-controller="BaseController" lang="en" ng-app="xiaohu" user-id="{{session('user_id')}}">
<head>
	<meta charset="UTF-8">
	<title>三人行</title>
	<link rel="stylesheet" type="text/css" href="/node_modules/normalize-css/normalize.css">
	<link rel="stylesheet" type="text/css" href="/css/base.css">
	<script src="/node_modules/jquery/dist/jquery.js"></script>
	<script src="/node_modules/angular/angular.js"></script>
	<script src="/node_modules/angular-ui-router/release/angular-ui-router.js"></script>
	<script src="/js/base.js"></script>
	<script src="/js/common.js"></script>
	<script src="/js/user.js"></script>
	<script src="/js/question.js"></script>
	<script src="/js/answer.js"></script>
</head>
<body>
	<!-- 导航栏 -->
	<div class="navbar clearfix ">
		<div class="container">
			<div class="fl">
				<div ui-sref="home" class="navbar-item brand">三人行</div>
				<form id="quick_ask" ng-controller="QuestionAddController" ng-submit="Question.go_add_question()">
					<div class="navbar-item">
						<input type="text" >
					</div>
					<div class="navbar-item">
						<button type="submit" class="primary">提问</button>
					</div>
				</form>
				
			</div>
			<div class="fr">
				<a ui-sref="home" class="navbar-item">首页</a>
				@if(is_logged())
				<a class="navbar-item">{{session('username')}}</a>
				<a href="{{url('/api/logout')}}" class="navbar-item">退出</a>
				@else
				<a ui-sref="signup" class="navbar-item">注册</a>
				<a ui-sref="login" class="navbar-item">登录</a>
				@endif
			</div>
		</div>	
	</div>


	<div class="page">
		<div ui-view></div>
	</div>
	<script type="text/ng-template" id="comment.tpl">
		<div class="comment-block">
			<div ng-if="!helper.obj_length(data)" class="gray tac well">暂无评论</div>
			<div ng-if="helper.obj_length(data)" ng-repeat="item in data" class="conment-item-set">
				<div class="rect"></div>
				<div class="comment-item clearfix">
					<div class="user">[:item.user.username:]:</div>
					<div class="comment-content">
						[:item.content:]
					</div>

				</div>
				<!-- <div class="hr"></div> -->
			</div>


			
		</div>
		<div class="input-group">
			<form class="comment-form clearfix" ng-submit="_.add_comment()">
				<input type="text" 
				placeholder="等待你的评论。。。"
				ng-model="Answer.new_comment.content">
				<button class="primary" type="submit">评论</button>
			</form>
		</div>

	</script>

</body>

</html>