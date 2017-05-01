
<div id="form_container">
  <h2 class="form-signin-heading">Please sign in</h2>
  <?php echo validation_errors();?>

  <?php echo form_open('auth/login', array('id' => 'loginform')); ?>
  <?php
  
  $table = array(array('', ''),
	  array(form_label('Username:', 'username'),
			form_input(array('name' => 'username', 'id' => 'username',
				 'class' => 'formfield'))),
	  array(form_label('Password', 'password'),
			form_password(array('name' => 'password', 'id' => 'password',
				 'class' => 'formfield'))),
	  array(form_hidden('page', $this->input->get('page')))
				 
	);
	  echo $this->table->generate($table);
  ?>

  <?php echo form_label('Remember me', 'remember'); ?>
  <?php echo form_checkbox(array('name' => 'remember', 'id' => 'remember',
	 'value' => 1,  'checked' => FALSE, 'disabled' => TRUE)); ?>
  <br />
  
  <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
  <?php echo form_close(); ?>
  <?php echo form_fieldset_close(); ?>
</div>
<p>Please login using your Mycompany username and password. If you do not have a login, please register <a href='http://www.Mycompany.com/dz'>here</a> (access for members) </p>
<p>Follow this <a href='https://www.Mycompany.com/doczone/publications/current_publications'>link</a> to view past publications:  (Mycompany username and password required; If you do not have a login, please register <a href='http://www.Mycompany.com/dz'>here</a> (access for Mycompany members only).</p>

