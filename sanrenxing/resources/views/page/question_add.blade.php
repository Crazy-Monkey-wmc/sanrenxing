<div ng-controller='QuestionAddController' class="question-add container">
		<div class="card">
			<form ng-submit="Question.add()" name='question_add_form'>
				<div class="input-group">
					<h1>问题标题:</h1>
					<input type="text"
						   ng-model="Question.new_question.title"
						   ng-minlength="4"
	   					   ng-maxlength='24'
	   					   name="title"
						   required
						   >
					<div class="input-err-set" ng-if="question_add_form.title.$touched">
						<div ng-if="question_add_form.title.$error.minlength">
							您的问题标题太短了哦
						</div>
						<div ng-if=" question_add_form.title.$error.maxlength">
							您的问题标题太长了哦
						</div>
						<div ng-if="question_add_form.title.$error.required">
							问题标题必须要填哦
						</div>
					</div>
					
				</div>
				<div class="input-group">
					<h1>问题描述:</h1>
					<textarea type="text"
							  name="desc"
							  ng-model="Question.new_question.desc"
						      
						      >
						      
						
					</textarea>
				</div>
				<div class="input-group">
					<button type="submit"
					        class="primary"
							ng-disabled="question_add_form.$invalid"
					        >提交
					</button>
					<div class="input-err-set" ng-if="Question.is_not_logged">
						您需要登录才能提问哦
					</div>

				</div>
			</form>
		</div>
</div>