<div ng-controller="QuestionDetailController" class="container question-detail">
	<div class="card">
		<h1>[:Question.current_question.title:]</h1>
		<div class="desc">[:Question.current_question.desc:]</div>
		<div>
			<span class="gray">
				回答数：[:Question.current_question.answers_with_user_info.length:]
			</span>
		</div>
		<div class="hr"></div>
		<!-- <div class="feed item clearfix"> -->
			<div ng-if="!Question.current_answer_id || 
			Question.current_answer_id == item.id " ng-repeat="item in Question.current_question.answers_with_user_info">
				<div class="feed item clearfix">
					<div class="vote">
						<div ng-click="Question.vote({id:item.id,vote:1})" class="up">赞 [:item.upvote_count:]</div>
						<div ng-click="Question.vote({id:item.id,vote:2})" class="down">踩 [:item.downvote_count:]</div>
					</div>
					<div class="feed-item-content">
						<div>
							<span ui-sref="user({id:item.user.id})">[:item.user.username:]</span>
						</div>
				
						<div>[:item.content:]</div>
						<div class="action-set">
							<span class="anchor" ng-click="item.show_comment = !item.show_comment">
								<span ng-if="item.show_comment">收起</span>评论
							</span>
							<span class="gray">
								<a ng-if="item.user_id == his.id" 
								   ng-click="Answer.answer_form = item"
								   class="anchor">
									编辑
								</a>
								<a ng-if="item.user_id == his.id" 
								   ng-click="Answer.delete(item.id)"
								   class="anchor">
									删除
								</a>
		    					<a ui-sref="question.detail({id:Question.current_question.id,answer_id:item.id})">
		    						[:item.updated_at:]
	    						</a>
	    					
	    					</span>
						</div>
					
						
					
					</div>
				</div>
				<div ng-if="item.show_comment" comment-block answer-id = "item.id">
					
				</div>
				<div class="hr"></div>

				
				
			</div>
		<!-- </div> -->
		<!-- <div class="hr"></div> -->
		<div>
			<form ng-submit="Answer.add_or_update(Question.current_question.id)" class="answer_form" name="answer_form">
				<div class="input-group">
					<textarea 
						name="content"
						type="text"
						ng-model="Answer.answer_form.content"
						required 
						>
							
					</textarea>
				</div>
				<div class="input-group">
					<button class="primary" 
							type="submit"
							ng-disabled="answer_form.$invalid">
						回答
					</button>
					
				</div>
			</form>
		</div>
		
	</div>
	
</div>