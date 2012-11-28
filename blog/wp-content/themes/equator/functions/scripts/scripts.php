<script src="<?php echo SYS32_URL; ?>/js/jquery.js" type="text/javascript"></script>
<script type="text/javascript">
/* <![CDATA[ */
//Our '.hero' and '.villain' in an epic battle to be styled the best!
$(document).ready(function(){

	//And our little animated sliding area uptop of the design.
	
	$('#tabContent>li:gt(0)').hide();
	$('#tabsNav li:first').addClass('active');
	$('#tabsAndContent #tabsNav li').bind('click', function() {
		$('li.active').removeClass('active');
		$(this).addClass('active');
		var target = $('a', this).attr('href');
		$(target).slideDown(400).siblings().slideUp(400);
		return false;
	});
});
	/* ]]> */
</script>
<script src="<?php echo SYS32_URL; ?>/js/jquery.modalbox-1.0.0.js" type="text/javascript"></script>
<script src="<?php echo SYS32_URL; ?>/js/jquery.checkbox.js" type="text/javascript"></script>
	<script type="text/javascript" src="<?php echo SYS32_URL; ?>/js/colorpicker.js"></script>
    <script type="text/javascript" src="<?php echo SYS32_URL; ?>/js/eye.js"></script>
    <script type="text/javascript" src="<?php echo SYS32_URL; ?>/js/utils.js"></script>
    <script type="text/javascript" src="<?php echo SYS32_URL; ?>/js/layout.js?ver=1.0.2"></script>
<script type="text/javascript" src="<?php echo SYS32_URL; ?>/js/jquery.easing.js"></script>
<script type="text/javascript">
/* <![CDATA[ */
			$(document).ready(function() {
				$('input:checkbox:not([safari])').checkbox();
				$('input[safari]:checkbox').checkbox({cls:'jquery-safari-checkbox'});
				$('input:radio').checkbox();
			});

			displayForm = function (elementId)
			{
				var content = [];
				$('#' + elementId + ' input').each(function(){
					var el = $(this);
					if ( (el.attr('type').toLowerCase() == 'radio'))
					{
						if ( this.checked )
							content.push([
								'"', el.attr('name'), '": ',
								'value="', ( this.value ), '"',
								( this.disabled ? ', disabled' : '' )
							].join(''));
					}
					else
						content.push([
							'"', el.attr('name'), '": ',
							( this.checked ? 'checked' : 'not checked' ), 
							( this.disabled ? ', disabled' : '' )
						].join(''));
				});
				alert(content.join('\n'));
			}
			
			changeStyle = function(skin)
			{
				jQuery('#myform :checkbox').checkbox((skin ? {cls: skin} : {}));
			}
	/* ]]> */			
</script>
<link rel="stylesheet" type="text/css" href="<?php echo SYS32_URL; ?>/css/admin.css" />
<link rel="stylesheet" type="text/css" href="<?php echo SYS32_URL; ?>/css/jquery.modalbox-1.0.0.css" />
<link rel="stylesheet" type="text/css" href="<?php echo SYS32_URL; ?>/css/jquery.checkbox.css" />
<link rel="stylesheet" type="text/css" href="<?php echo SYS32_URL; ?>/css/jquery.safari-checkbox.css" />
<link rel="stylesheet" type="text/css" href="<?php echo SYS32_URL; ?>/css/layout.css" />
<link rel="stylesheet" type="text/css" href="<?php echo SYS32_URL; ?>/css/colorpicker.css" />