<?php echo (isset($yield_header)) ? $yield_header : "" ; ?>
<div id="main" class="">
	<?php echo (isset($yield_left)) ? $yield_left : "" ; ?>
	<div id="content">
		<?php echo $yield; ?>
	</div>
</div>
<?php echo (isset($yield_footer)) ? $yield_footer : ""; ?>