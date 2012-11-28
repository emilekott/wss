<form method="post" id="myform" enctype="multipart/form-data">
<ul id="tabContent">
<?php foreach ($options as $value) { 
switch ( $value['type'] ) {
case "open":
?>

<?php break; case "close": ?>
</li>
<?php break; case "title": ?>

<li id="<?php echo $value['name']; ?>">
<?php break; ?>

<?php case 'select':?>

<table class="admintable"  cellpadding="0" cellspacing="0">
<tr>
<td width="100%">
	<h2><?php echo $value['name']; ?> - 
		<a class="modalboxLink" href="javascript:void(0);">
		<img src="<?php echo SYS32_URL?>/img/help.png" width="16" height="16" alt="Need Help?" />
		<span class="modalboxContent">
			<span class="info">
				<p class="info"><?php echo $value['name']; ?></p>
				<?php echo $value['desc']; ?>
			</span>
		</span>
		</a>
	</h2>

	<select style="width:240px;" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>">
		<?php foreach ($value['options'] as $option) { ?>
		<option <?php if ( get_settings( $value['id'] ) == $option) { echo ' selected="selected"'; } elseif ($option == $value['std']) { echo ' selected="selected"'; } ?>>
		<?php echo $option; ?>
		</option>
		<?php } ?>
	</select>
</td>
</tr>
</table>

<?php break; case 'multifull':?>


<table class="admintable"  cellpadding="0" cellspacing="0">
<tr>
<td width="100%">
	<h2><?php echo $value['name']; ?> - 
		<a class="modalboxLink" href="javascript:void(0);">
		<img src="<?php echo SYS32_URL?>/img/help.png" width="16" height="16" alt="Need Help?" />
		<span class="modalboxContent">
			<span class="info">
				<p class="info"><?php echo $value['name']; ?></p>
				<?php echo $value['desc']; ?>
			</span>
		</span>
		</a>
	</h2>

	<select style="width:240px;" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>">
		<?php foreach ($value['options'] as $keys =>$values) { ?>
		<option value="<?php echo $keys; ?>"
		  <?php if ( get_settings( $value['id'] ) == $keys) { echo ' selected="selected"'; } elseif ($keys == $value['std']) { echo ' selected="selected"'; } ?>>
		<?php echo $values; ?>
		</option>
		<?php } ?>
	</select>
</td>
</tr>
</table>


<?php break; case 'text': ?>

<table  class="admintable" cellpadding="0" cellspacing="0">
<tr>
<td width="100%">
	<h2><?php echo $value['name']; ?> - 
		<a class="modalboxLink" href="javascript:void(0);">
		<img src="<?php echo SYS32_URL?>/img/help.png" width="16" height="16" alt="Need Help?" />
		<span class="modalboxContent">
			<span class="info">
				<p class="info"><?php echo $value['name']; ?></p>
				<?php echo $value['desc']; ?>
			</span>
		</span>
		</a>
	</h2>

	<input style="width:300px;" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_settings( $value['id'] ) != "") { echo stripslashes(get_settings( $value['id'] )); } else { echo $value['std']; } ?>" />
	
</td>
</tr>
</table>

<?php break; case 'logo':?>

<table  class="admintable" cellpadding="0" cellspacing="0">
<tr>
<td width="100%">
	<h2><?php echo $value['name']; ?> - 
		<a class="modalboxLink" href="javascript:void(0);">
		<img src="<?php echo SYS32_URL?>/img/help.png" width="16" height="16" alt="Need Help?" />
		<span class="modalboxContent">
			<span class="info">
				<p class="info"><?php echo $value['name']; ?></p>
				<?php echo $value['desc']; ?>
			</span>
		</span>
		</a>
	</h2>

	<input class="button" type="file" name="logourl" id="logourl" /><br />
	<input type="hidden" name="logourl" value="<?php echo get_option('imglogourl');?>">
	<?php if(get_option('imglogourl')) { echo '<img src="'; echo get_option('imglogourl'); echo '"   />'; } ?>

</td>
</tr>
</table>

<?php break; case 'textarea':?>

<table  class="admintable" cellpadding="0" cellspacing="0">
<tr>
<td width="100%">
	<h2><?php echo $value['name']; ?> - 
		<a class="modalboxLink" href="javascript:void(0);">
		<img src="<?php echo SYS32_URL?>/img/help.png" width="16" height="16" alt="Need Help?" />
		<span class="modalboxContent">
			<span class="info">
				<p class="info"><?php echo $value['name']; ?></p>
				<?php echo $value['desc']; ?>
			</span>
		</span>
		</a>
	</h2>

	<textarea cols="" rows="" name="<?php echo $value['id']; ?>" style="width:350px; height:100px;" type="<?php echo $value['type']; ?>">
	<?php if ( get_settings( $value['id'] ) != "") { echo stripslashes(get_settings( $value['id'] )); } else { echo $value['std']; } ?></textarea>
</td>
</tr>
</table>

<?php break; case 'coloroptions': ?>

<table  class="admintable" cellpadding="0" cellspacing="0">
<tr>
<td width="100%">
	<h2><?php echo $value['name']; ?> - 
		<a class="modalboxLink" href="javascript:void(0);">
		<img src="<?php echo SYS32_URL?>/img/help.png" width="16" height="16" alt="Need Help?" />
		<span class="modalboxContent">
			<span class="info">
				<p class="info"><?php echo $value['name']; ?></p>
				<?php echo $value['desc']; ?>
			</span>
		</span>
		</a>
	</h2>
	
	<?php if (get_option("colorpickerField1")){$colorpickerFields = get_option("colorpickerField1"); } ?> 
	<input type="text"  name="colorpickerField1" maxlength="6" size="6" id="colorpickerField1" value="<?php echo $colorpickerFields; ?>" />	

</td>
</tr>
</table>

<?php  break; case "checkbox":?>

<table  class="admintable" cellpadding="0" cellspacing="0">
<tr>
<td width="100%">
	<h2><?php echo $value['name']; ?> - 
		<a class="modalboxLink" href="javascript:void(0);">
		<img src="<?php echo SYS32_URL?>/img/help.png" width="16" height="16" alt="Need Help?" />
		<span class="modalboxContent">
			<span class="info">
				<p class="info"><?php echo $value['name']; ?></p>
				<?php echo $value['desc']; ?>
			</span>
		</span>
		</a>
	</h2>

	<? if(get_settings($value['id'])){ $checked = "checked=\"checked\""; }else{ $checked = ""; } ?>
	<input type="checkbox" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" value="true" <?php echo $checked; ?> />
		
</td>
</tr>
</table>

<?php break; case "checkbox1": ?>

<table class="admintable" cellpadding="0" cellspacing="0">
<tr>
<td width="100%">
	<h2><?php echo $value['name']; ?> - 
		<a class="modalboxLink" href="javascript:void(0);">
		<img src="<?php echo SYS32_URL?>/img/help.png" width="16" height="16" alt="Need Help?" />
		<span class="modalboxContent">
			<span class="info">
				<p class="info"><?php echo $value['name']; ?></p>
				<?php echo $value['desc']; ?>
			</span>
		</span>
		</a>
	</h2>

	<?php foreach ($value['options'] as $keys =>$values) { 
	$checked = ""; 
		if (get_option( $value['id'])) { 
			if (in_array($keys, get_option($value['id'] ))) $checked = "checked=\"checked\"";
		} 
	else {
	} 
	?>	

	<label class="button">
	<input type="checkbox" name="<?php echo $value['id']; ?>[]" id="<?php echo $keys; ?>" value="<?php echo $keys; ?>" <?php echo $checked; ?> />
	<?php echo $values; ?>
	</label>
	<?php } ?>

</td>
</tr>
</table>

<?php break; case "radio":?>

<table class="admintable" cellpadding="0" cellspacing="0">
<tr>
<td width="100%">
	<h2><?php echo $value['name']; ?> - 
		<a class="modalboxLink" href="javascript:void(0);">
		<img src="<?php echo SYS32_URL?>/img/help.png" width="16" height="16" alt="Need Help?" />
		<span class="modalboxContent">
			<span class="info">
				<p class="info"><?php echo $value['name']; ?></p>
				<?php echo $value['desc']; ?>
			</span>
		</span>
		</a>
	</h2>

	<?php
	foreach ($value['options'] as $key=>$option) { 
		if(get_settings($value['id'])){
		if ($key == get_settings($value['id']) ) {
		$checked = " checked=\"checked\"";
	} else {
		$checked = "";
		}
	} else {
		if($key == $value['std']) {
		$checked = " checked=\"checked\"";
	} else {
		$checked = "";
		}
	} ?>

	<label class="button">
	<input type="radio" name="<?php echo $value['id']; ?>" value="<?php echo $key; ?>"<?php echo $checked; ?> />
	<?php echo '&nbsp;'.$option; ?>
	</label>
	<?php } ?>

</td>
</tr>
</table>

<?php break; } }?>
</ul>
</div>