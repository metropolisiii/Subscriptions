<?php if (empty($email)): $email=''; endif; ?>
<script>
document.domain= 'Mycompany.com';
//var referrer=parent.location.toString();

//if (referrer.indexOf('admin') !== -1)
	//window.location='/subscriptions/admin';

</script>
<div id='container'>
	<div class="jumbotron">Mycompany provides a Subscription service so that you can receive technical research reports in a timely manner.    You simply select the topic areas that you are interested in, and we email you the research publication as soon as it is published.   This is a convenient way to stay current on all research from our R&D and Strategy groups.   </div>
	<?php echo form_open(''); ?>
		
		<div class='field' id='errors'>
			<?php echo validation_errors(); ?>
		</div>
		<div class='field' id='errors'>
			<?php if (!empty($success)) echo $success; ?>
		</div>
		 <div class='field'>
			Email Address:* <input <?php if ( $is_authenticated): ?> READONLY="readonly" <?php endif; ?> type='text' name='email' value="<?php echo $email ?>" class='required' size='40'/>
		 </div>
		 <div class='field' style='display:none'>
			Content Categories:*<br/>
			<?php //foreach($content_categories as $cc): ?>				
			<?php 
				//$checked=false;
				// if (!empty($content_categories_subscribers[$cc['id']]))
				//	$checked=true;  
			 ?>
			<!--<input type='checkbox' name='content_categories[]' checked='checked' <?php //echo set_checkbox('content_categories', $cc['id'], $checked); ?> value='<?php //echo $cc['id']; ?>' class='required'/> <?php //echo $cc['name']; ?>-->
			<?php //endforeach; ?>
		</div>
		 <div class='field'>
			 Select one or more Mycompany focal areas: <br/>
			 <?php foreach($themes as $theme): ?>
			<?php 
				$checked=false;
				 if (!empty($themes_subscribers[$theme['id']]))
					$checked=true;  
			 ?>
			<input type='checkbox' name='themes[]' value='<?php echo $theme['id']; ?>' <?php if ($checked) echo "checked='checked'";?>> <span title='<?php echo $theme['description'];?>'><?php echo $theme['name'];?></span><br/>
			<?php endforeach; ?>
		</div>
		<div class='field'>
			 Select one or more Mycompany publications of interest: <br/>
			 <?php foreach($publications as $publication): ?>
			<?php 
				$checked=false;
				 if (!empty($themes_subscribers[$publication['id']]))
					$checked=true;  
			 ?>
			<input type='checkbox' name='publications[]' value='<?php echo $publication['id']; ?>' <?php if ($checked) echo "checked='checked'";?>> <span title='<?php echo $publication['description'];?>'><?php echo $publication['name'];?> - <?php echo $publication['description']; ?></span><br/>
			<?php endforeach; ?>
		</div>
		<?php if ( !$is_authenticated): ?>
		<div class='field bordered'>
			<p>To subscribe to exclusive content, you must first <a href='auth'>login</a></p>
		</div>
		<?php endif; ?>
		<div class='field'>
			Click 'Submit' to begin receiving Mycompany publications and materials about the interests indicated.
		</div>
		<div class='field'>
			<input type='submit' value='Submit' id='submit'/>
		</div>
		<?php if ( $is_authenticated): ?>
		<div class='field'>
			<a href='auth/logout'>Logout</a>
		</div>
		<?php endif; ?>
	</form>	
</div>