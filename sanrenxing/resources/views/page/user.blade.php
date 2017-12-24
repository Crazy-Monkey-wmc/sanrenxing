<div ng-controller = "UserController">
	<div class="user card container">
		<h1>用户详情</h1>
		<div class="hr"></div>
		<div class="basic">
			<div class="info_item clearfix">
				<div>username</div>
				<div>[:User.current_user.username:]</div>
			</div>
			<div class="info_item clearfix">
				<div>intro</div>
				<div>[:User.current_user.intro || '暂无介绍':]</div>
			</div>
		</div>
		<h2>用户提问</h2>
		<div ng-repeat = "item in User.his_question">
			[:item.title:]
			
		</div>
		<h2>用户回答</h2>
		<div class="feed item" ng-repeat = "item in User.his_answers">
			<div class="title">[:item.question.title:]</div>
			[:item.content:]
			<div class="action-set">
				<div class="comment">[:item.updated_at:]</div>
			</div>
			
		</div>	
	</div>
	
	
</div>