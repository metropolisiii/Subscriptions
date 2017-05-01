$('document').ready(function(){
	 $( document ).tooltip();
	$('#preview').click( function() {
		$.post( '/subscriptions/ajax/preview', $('#preview_form').serialize(), function(data) {
			 $('#willbenotified').html(data);
		   }
		);
	});
	$('#submit_admin').click(function(){
		if ($('#preview_form').checkValidity && !$('#preview_form').checkVAlidity()){
			$('#loading').show();
			$(this).prop('disabled',true);
			$('body').css('backgroundColor', '#CCCCCC');
			$('#preview_form').submit();
		}		
	});
});