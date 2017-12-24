<div class="login container" ng-controller="LoginController">
	    <div class="card">
	    	<h1>登录</h1>
	    	<form name="login_form" ng-submit="User.login()">
	    		<div class="input-group">
	    			<label>用户名：</label>
	    			<input type="text" 
	    				   name="username"
	    				   ng-model="User.login_data.username"
	    				   required
	    				   >
	    			
	    		</div>
	    		<div class="input-group">
	    			<label>密码：</label>
	    			<input type="password" 
	    				   name="password"
	    				   ng-model="User.login_data.password"
	    				   required
	    				   >
	    			
	    		</div>
	    		<div class="input-err-set"
	    			 ng-if="User.login_failed">
	    			 用户名或密码有误
	    			
	    		</div>
	    		<button 
	    				type="submit"
	    				ng-disabled="login_form.$invalid"
	    				class="primary">
	    		登录</button>
	    	</form>
	    </div>
</div>