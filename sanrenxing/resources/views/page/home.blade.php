<div ng-controller="HomeController" class="home container card">
	<h1>最近动态</h1>
	<div class="hr"></div>
	<div ng-repeat="item in Timeline.data track by $index" class="item-set">
		<div class="feed item clearfix">
			<div class="vote" ng-if="item.question_id">
				<div ng-click="Timeline.vote({id:item.id,vote:1})" class="up">赞 [:item.upvote_count:]</div>
				<div ng-click="Timeline.vote({id:item.id,vote:2})" class="down">踩 [:item.downvote_count:]</div>
			</div>
			<div class="feed-item-content">
				<div ng-if="item.question_id" class="content-act">
					<a ui-sref="user({id:item.user.id})" >[:item.user.username:] </a>添加了回答
				</div>
				<div ng-if="!item.question_id" class="content-act">
					<a ui-sref="user({id:item.user.id})" >[:item.user.username:] </a>添加了提问
				</div>
				<div ng-if="item.question_id" class="title" ui-sref="question.detail({id:item.question.id})">
					[:item.question.title:]
				</div>
				<div ui-sref="question.detail({id:item.id})" class="title">[:item.title:]</div>
				<div class="content-owner">[:item.user.username:]
					<span class="desc">Lorem ipsum dolor sit amet</span></div>
					<div class="content-main">
						[:item.content:]
						<div class="gray">
							<a ng-if="item.question_id" ui-sref="question.detail({id:item.question_id,answer_id:item.id})">[:item.updated_at:]</a>

						</div>
					</div>
					<div class="action-set">
					<span class="anchor" ng-click="item.show_comment = !item.show_comment">
							<span ng-if="item.show_comment">收起</span>评论
						</span>    				
					</div>
					<div ng-if="item.show_comment" comment-block answer-id = "item.id">

					</div>
	    			<!-- <div class="comment-block">
	    				
	    				<div class="conment-item-set">
	    					<div class="rect"></div>
	    					<div class="comment-item clearfix">
	    						<div class="user">白凤</div>
	    						<div class="comment-content">
	    							Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
	    							tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
	    							quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
	    							consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
	    							cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
	    							proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
	    						</div>

	    					</div>
	    				</div>
	    				<div class="conment-item-set">
	    					<div class="comment-item clearfix">
	    						<div class="user">疯子</div>
	    						<div class="comment-content">
	    							Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
	    							tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
	    							quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
	    						
	    						</div>

	    					</div>
	    				</div>
	    				<div class="conment-item-set">
	    					<div class="comment-item clearfix">
	    						<div class="user">秦广王</div>
	    						<div class="comment-content">
	    							Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
	    							tempor incididunt ut labore et dolore magna deserunt mollit anim id est laborum.
	    						</div>

	    					</div>
	    				</div>
	    			</div> -->
	    		</div>	 				 
	    	</div>
	    	<div class="hr"></div> 
	    </div>	
	    <div ng-if="!Timeline.no_more_data" class="tac">正在加载</div>
	    <div ng-if="Timeline.no_more_data" class="tac">已经到头啦</div>

	</div>