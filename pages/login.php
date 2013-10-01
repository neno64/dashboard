<!-- Wrapper -->
<div id="login">

	<div class="container">
	
		<div class="wrapper">
		
			<h1 class="glyphicons lock">Ergo Admin <i></i></h1>
		
			<!-- Box -->
			<div class="widget">
				
				<div class="widget-head">
					<h3 class="heading">Login area</h3>
					<div class="pull-right">
						
						<a href="<?php echo getURL(array('signup')); ?>" class="btn btn-inverse btn-mini">Sign up</a>
					</div>
				</div>
				<div class="widget-body">
				
					<!-- Form -->
					<form method="post" action="<?php echo getURL(array('index')); ?>">
						<label>Username or Email</label>
						<input id="login-username" type="text" class="input-block-level" placeholder="Your Username or Email address"/> 
						<label>Password <a class="password" href="">forgot your password?</a></label>
						<input id="login-password" type="password" class="input-block-level margin-none" placeholder="Your Password" />
						<div class="separator bottom"></div> 
						<div class="row-fluid">
							<div class="span8">
								<div class="uniformjs"><label class="checkbox"><input id="login-remember" type="checkbox" value="remember-me">Remember me</label></div>
							</div>
							<div class="span4 center">
								<div id="login-button" class="btn btn-block btn-primary" type="submit">Sign in</div>
							</div>
						</div>
					</form>
					<!-- // Form END -->
							
				</div>
				<div class="widget-footer">
					<p id="login-error2" class="glyphicons restart" style="display:none;"><i></i>Please enter your username and password ...</p>
					<p id="login-error1" class="glyphicons restart" style="display:none;"><i></i>Your Username or Password is incorrect ...</p>
				</div>
			</div>
			<!-- // Box END -->
			<div class="innerAll center">
                                <!--
				<p>Alternatively</p>
				<a href="<?php echo getURL(array('index')); ?>" class="btn btn-icon-stacked btn-block btn-facebook glyphicons facebook"><i></i><span>Join using your</span><span class="strong">Facebook Account</span></a>
				<p>or</p>
				<a href="<?php echo getURL(array('index')); ?>" class="btn btn-icon-stacked btn-block btn-google glyphicons google_plus"><i></i><span>Join using your</span><span class="strong">Google Account</span></a>
                                -->
				<p>Having troubles? <a href="<?php echo getURL(array('faq')); ?>">Get Help</a></p>
			</div>
			
		</div>
		
	</div>
	
</div>
<script>
$(document).ready(function(){
  $('#login-button').click( function() {
    login();
  });
});
function login(){
  var mode = 0;
  var un = $('#login-username').val();
  var pw = $('#login-password').val();
  if (un == '' || pw == ''){ $('#login-error2').show(); $('#login-error1').hide(); }
  else {
    if ($('#login-remember').is(':checked')) { mode = 1; }
    loginResult = (function () {
	loginResult = null;
	$.ajax({
	    'async': false,
	    'global': false,
	    'url': "lib/xenHook.php?action=login&username="+un+"&password="+pw+"&m="+mode,
	    'dataType': "text",
	    'success': function (data) {
	    loginResult = data;
	  }
	});
      return loginResult;
    })(); 
    if(loginResult==null){return false;}
    if(loginResult=="0"){ $('#login-error1').show(); $('#login-error2').hide(); }
    else { location.reload(); }
  }
}
</script>
<!-- // Wrapper END -->