<!DOCTYPE html>
<html>
<head>
	<title>Mycompany Subscriptions</title>
	<script src="<?php echo base_url();?>js/jquery-1.3.2.min.js" type="text/javascript"></script>
	 <script src="<?php echo base_url();?>js/jquery-ui.js"></script>
	<script src="<?php echo base_url();?>js/scripts.js" type="text/javascript"></script>
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
	<link type="text/css" rel="stylesheet" href="<?php echo base_url();?>css/styles.css">
	<link type="text/css" rel="stylesheet" href="<?php echo base_url();?>css/jquery-ui.css">
	<script src="//cloud.tinymce.com/stable/tinymce.min.js?apiKey=52x7112wg5v45siku2227pr5szs449xn8uutuks7ihbwgcwo"></script>
	<script>
	tinymce.init({
		  selector: "textarea",
		  height: 500,
		  plugins: [
			"advlist autolink autosave link image lists charmap print preview hr anchor pagebreak spellchecker",
			"searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
			"table contextmenu directionality emoticons template textcolor paste fullpage textcolor colorpicker textpattern"
		  ],

		  toolbar1: "newdocument fullpage | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | styleselect formatselect fontselect fontsizeselect",
		  toolbar2: "cut copy paste | searchreplace | bullist numlist | outdent indent blockquote | undo redo | link unlink anchor image media code | insertdatetime preview | forecolor backcolor",
		  toolbar3: "table | hr removeformat | subscript superscript | charmap | print fullscreen | ltr rtl | spellchecker | visualchars visualblocks nonbreaking template pagebreak restoredraft",

		  menubar: false,
		  toolbar_items_size: 'small',

		  style_formats: [{
			title: 'Bold text',
			inline: 'b'
		  }, {
			title: 'Red text',
			inline: 'span',
			styles: {
			  color: '#ff0000'
			}
		  }, {
			title: 'Red header',
			block: 'h1',
			styles: {
			  color: '#ff0000'
			}
		  }, {
			title: 'Example 1',
			inline: 'span',
			classes: 'example1'
		  }, {
			title: 'Example 2',
			inline: 'span',
			classes: 'example2'
		  }, {
			title: 'Table styles'
		  }, {
			title: 'Table row 1',
			selector: 'tr',
			classes: 'tablerow1'
		  }],

		  templates: [{
			title: 'Test template 1',
			content: 'Test 1'
		  }, {
			title: 'Test template 2',
			content: 'Test 2'
		  }],
		  content_css: [
			'//fast.fonts.net/cssapi/e6dc9b99-64fe-4292-ad98-6974f93cd2a2.css',
			'//www.tinymce.com/css/codepen.min.css'
		  ]
		});
	</script>
</head>
<body>
	<div class="container theme-showcase" role="main">
	<h1>Mycompany Subscriptions</h1>
