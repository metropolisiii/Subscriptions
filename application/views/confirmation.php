<div id='container'>
 <div id='confirmation'>
	<?php 
		if (!empty($success)){
			echo $success; 
	?>
	<div class='field' id='notifyees'>
	<?php 
				if (!empty($notifyees)) 
					echo "Users notified: <br/> ".(empty($notifyees)?"None":$notifyees); 
				?>
		</div>
		<?php
			}
		else 
			echo "File was not uploaded. There may have been a problem with the file or it may have been too large. Please go back and try again."; 
	?>	
 </div>
</div>