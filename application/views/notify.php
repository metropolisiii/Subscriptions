

<?php echo form_open_multipart('', array('id'=>'preview_form')); ?>
	<div class='field jumbotron'>
		<h2>Send file (whitepaper, document) to subscribers with matching interests.</h2>
	</div>
	
	<div class='field' id='errors'>
		<?php echo validation_errors(); ?>
	</div>
	<div class='field' id='errors'>
		<?php if (!empty($success)) echo $success; ?>
	</div>
	<div id='reports'>
		<?php if ($this->session->userdata('role') == 'Administrator'): ?>
			<a class="btn btn-sm btn-warning" href='reports'>Reports</a>
		<?php endif; ?>
	</div>
	<div class='field' id='notifyees'>
		<?php 
			if (!empty($notifyees)) 
				echo "Users notified: <br/> ".(empty($notifyees)?"None":$notifyees); 
			?>
	</div>
	<div class='field'>
		<div class='col-md-4'>Email subject line:*</div><div class='col-md-4'><input type='text' name='email_subject' id='email_subject' class='required' value="<?php echo set_value('email_subject'); ?>" size='40' /></div>
	 </div>
	<div class='field'>
		<div class='col-md-4'>Email body:*</div>
		<div class='col-md-12'><textarea name='email_body' id='email_body' class='required'  cols='60' rows='10'> <?php echo set_value('email_body'); ?></textarea></div>
	 </div>
	 <div class='field'>
		<div class='col-md-4'>Link to File:*</div><div class='col-md-4'><input type='url' name='filelink' id='filelink' class='required' value="<?php echo set_value('filelink');?>" size='40'/></div>
	 </div>
	 <div class='field'>
		<div class='col-md-4'>Contact name:*</div><div class='col-md-4'><input type='text' name='email_contact' id='email_contact' class='required' value="<?php if (set_value('email_contact')) echo set_value('email_contact'); else echo $user_fullname; ?>" size='40' /></div>
	 </div>
	 <div class='field'>
		<div class='col-md-4'>Contact title:</div><div class='col-md-4'><input type='text' name='contact_title' id='contact_title' value="<?php if (set_value('contact_title')) echo set_value('contact_title'); else echo $contact_title; ?>" size='40' /></div>
	 </div>
	  <div class='field'>
		<div class='col-md-4'>Contact email:*</div><div class='col-md-4'><input type='email' name='contact_email' id='contact_email' value="<?php if (set_value('contact_email')) echo set_value('contact_email'); else echo $contact_email; ?>" size='40' /></div>
	 </div>
	 <div class='field col-md-6' style='display:none'>
		Content Categories:*<br/>
		<?php foreach($content_categories as $cc): ?>				
		<?php 
			$checked=false;
			 if (!empty($content_categories_subscribers[$cc['id']]))
				$checked=true;  
		 ?>
		<input type='checkbox' name='content_categories[]' checked='checked' <?php echo set_checkbox('content_categories', $cc['id'], $checked); ?> value='<?php echo $cc['id']; ?>' class='required'/> <?php echo $cc['name']; ?>
		<?php endforeach; ?>
	</div>
	
	 <div class='field col-md-6'>
		 Select one or more focal areas: <br/>
		 <?php foreach($themes as $theme): ?>
		<?php 
			$checked=false;
			 if (!empty($themes_subscribers[$theme['id']]))
				$checked=true;  
		 ?>
		<input type='checkbox' name='themes[]' value='<?php echo $theme['id']; ?>' <?php echo set_checkbox('themes', $theme['id'], $checked); ?>> <?php echo $theme['name'];?><br/>
		<?php endforeach; ?>
	</div>
	
	
	
	
	<div class='field col-md-12'>
		Click 'submit' to send the email to subscribers matching one or more of these focal areas selected above.
	</div>
	<div class='field col-md-12' id='willbenotified'>
	
	</div>
	<div class='field col-md-12'>
		<button class="btn btn-primary" type='button' id='preview'>Preview</button> <button class="btn btn-primary" formtarget="_blank" type='submit' name='TestSubmit'>Test Submission (will only send to you)</button> <input class="btn btn-danger" type='submit' name='Submit Subscription' value='Submit' id='submit_admin'/>
	</div>
	<div class='field col-md-12'>
		<a href='auth/logout?admin=true'>Logout</a>
	</div>
</form>

<div id='loading'>
	<img src='<?php echo base_url();?>images/spinnnnnn.gif'/>
</div>
