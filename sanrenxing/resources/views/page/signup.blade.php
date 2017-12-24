<div ng-controller="SignupController" class="signup container">
	   	<div class="card">
	   		<h1>注册</h1>
	   		<!-- [:User.signup_data:] -->
	   		<form name="signup_form" ng-submit="User.signup()">
	   			<div  class="input-group">
	   				<label>用户名：</label>

	   				<input name="username" 
	   					   type="text" 
	   					   ng-model="User.signup_data.username"
	   					   ng-minlength="4"
	   					   ng-maxlength='24'
	   					   ng-model-options="{debounce:600}"
	   					   required
	   				>
	   				<div ng-if="signup_form.username.$touched" class="input-err-set">
	   					<div ng-if="signup_form.username.$error.required">
	   						用户名不能为空
	   					</div>
	   					<div ng-if="signup_form.username.$error.minlength || 
	   					signup_form.username.$error.maxlength">
	   						用户名需要在4-24位之间
	   					</div>
	   					<div ng-if="User.signup_username_exists">
	   						用户名已存在
	   					</div>
	   				</div>
	   				

	   			</div>
	   			<div class="input-group">
	   				<label>密码：</label>
	   				<input name="password"
	   					   type="password" 
	   					   ng-model="User.signup_data.password"
	   					   ng-minlength='6'
	   					   ng-maxlength='255'
	   					   required>
	   				<div ng-if="signup_form.password.$touched" class="input-err-set">
	   					<div ng-if="signup_form.password.$error.required
	   					">
	   						密码不能为空
	   					</div>
	   					<div ng-if="signup_form.password.$error.minlength || 
	   					signup_form.password.$error.maxlength">
	   						密码至少需要6位
	   					</div>
	   				</div>
	   			</div>
	   			
	   			<button ng-disabled="signup_form.$invalid" 
	   					type="submit"
	   					class="primary">
	   			注册</button>
	   			
	   		</form>
	   	</div>
</div>