<script>
document.domain= 'Mycompany.com';
var referrer=parent.location.toString();

if (referrer.indexOf('optout') === -1)
	window.location='/subscriptions';
</script>
<h1>MarketWatch Opt Out Request</h1>
<div id='container'>
	<?php if (!empty($success) && $success == 'true'): ?>
		<p>You are now opted out of MarketWatch. Thank you. </p>
		<p><a href='auth/logout'>Logout</a></p>
	<?php elseif (!empty($success) && $success == 'false'): ?>
		<p>You have chosen not to opt out of MarketWatch. Thank you.</p>
		<p><a href='auth/logout'>Logout</a></p>
	<?php else: ?>
	<?php echo form_open_multipart('', array('id'=>'optout_form')); ?>
	 <div class='field'>
			By clicking this checkbox and submiting this form, you are verifying that you want to opt out of MarketWatch:  
			<input type='checkbox' name='optout' checked='checked' /> 
	</div>
	<div class='field'>
		<input type="hidden" name="submitted" value="true" >
		<input type='submit' name="submit" value='Submit' id='submit'/> 
		<input type='submit' name='submit' value='Cancel'/>		
	</div>
	<?php endif; ?>
</div>