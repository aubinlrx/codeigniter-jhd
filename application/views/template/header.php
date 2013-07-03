<!DOCTYPE html>
<html lang="fr-FR">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<link rel="shortcut icon" href="/favicon.ico" type="favicon.png" />
		<title>JHD groupe Kardol - Support Client</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="JHD Groupe Kardol - Application de support au logiciel métier"/>
		<?php foreach ($stylesheets as $value) { ?>
			<?= css_asset($value); ?>
		<?php } ?>
		<!--[if lte IE 8]>
			<link rel="stylesheet" type="text/css" href="<?= base_url() ?>assets/css/ie8-and-down.css" />
		<![endif]-->
		<script type="text/javascript">
			var CI = {
				base_url : <?= "'".base_url()."'" ?>, 
				language : <?= "'".$this->session->userdata('language')."'"?>,
			}
		</script>
		<?php foreach ($javascripts as $value) { ?>
			<?= js_asset($value) ?>
		<?php } ?>
		<?php if($jsons != null) {?> 
			<script>
			<?php foreach ($jsons as $name => $value) { ?>
				<?= $value ?>;
			<?php } ?>
			</script>
		<?php } ?>

		<script>
			$(document).ready(function(){
				$('.menu-log ul li a.click').click(function(e){
					e.preventDefault();
					var el = $(this).data('toggle');
					$('#nav-profil').toggleClass('visible');
				})

				$('#alert-message .close').click(function(e){
					$(this).parent().hide();
				});
			});
		</script>
	</head>
	
	<body id="<?= (isset($id_body) ? $id_body ?>">
		<header id="header" class="">
			
		</header>