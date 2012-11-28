<?php
/**
 * Cross Sell products
 *
 * Derived from:
 * Original Idea From Isaac Mualem im@imwebdesigning.com <mailto:im@imwebdesigning.com>
 * Portions Copyright (c) 2002 osCommerce
 * Complete Recoding From Stephen Walker admin@snjcomputers.com
 * Released under the GNU General Public License
 *
 * Adapted to Zen Cart by Merlin - Spring 2005
 * Reworked for Zen Cart v1.3.0  03-30-2006
 *
 * Reworked again to change/add more features by yellow1912
 * Pay me a visit at RubikIntegration.com
 *
 */

require('includes/application_top.php');
//require(realpath(dirname(__FILE__).'/../includes/functions/extra_functions/yellow1912_extra_functions.php'));
require(DIR_WS_CLASSES . 'currencies.php');
$currencies = new currencies();
$languages_id = $_SESSION['languages_id'];

switch($_GET['action']){
	// Unlike the original version, we cross sell each and every PAIR from the given list
	case 'newcross_sell':
		$product_id_array = array_filter($_POST['product_id']);
		$product_id_array = array_unique($product_id_array);
		// re-index it
		$product_id_array = array_values($product_id_array);
		// clean it 
		$product_id_array = zen_db_prepare_input($product_id_array);
		if ($_POST['cross_sell_one_way'] == 1)
			if(count($product_id_array)>0){
				$_POST['main_product_id'] = zen_db_prepare_input($_POST['main_product_id']);
				if(empty($_POST['main_product_id']))
					$messageStack->add(CROSS_SELL_NO_MAIN_FOUND, 'error');
				else
					foreach ($product_id_array as $id => $pid)
						add_new_cross_product($_POST['main_product_id'], $pid);
			}
			else
				$messageStack->add(sprintf(CROSS_SELL_NO_INPUT_FOUND,1), 'warning');
		else
			if (count($product_id_array)>1){
				foreach ($product_id_array as $id => $pid)
					foreach ($product_id_array as $id2 => $pid2)
						if ($pid2 != $pid)
							add_new_cross_product($pid, $pid2);
				}
			else
				// Add error msg to stack
				$messageStack->add(sprintf(CROSS_SELL_NO_INPUT_FOUND,2), 'warning');		
	break;

	case 'editcross_sell':
		$search_result = search_cross_product(zen_db_prepare_input($_POST['cID']));
	break;

	case 'update':
		$xsell_array = $_POST['xsell'];
		// clean it 
		$xsell_array = zen_db_prepare_input($xsell_array);

		// Take care of the sort thing first, shall we?
		$sorted_product_array = array();
		$deleted_product_array = array();
		foreach ($xsell_array as $xsell){
			if((int)$xsell['delete'] == 1)
				$deleted_product_array[] = $xsell['id'];
			else
				if($xsell['old_sort_order'] != $xsell['new_sort_order'] && $xsell['new_sort_order'] >= 0){
					$db->Execute('UPDATE '.TABLE_PRODUCTS_XSELL.' SET sort_order = '.$xsell['new_sort_order'].' WHERE ID = '.$xsell['id'].' LIMIT 1');
					if (XSELL_FORM_INPUT_TYPE == "model")
						$sorted_product_array[] =  $xsell['product_model'];
					else
						$sorted_product_array[] =  $xsell['product_id'];
					}
			}
		if(count($sorted_product_array) > 0)
			$messageStack->add(sprintf(CROSS_SELL_SORT_ORDER_UPDATED, implode(',',$sorted_product_array)), 'success');
		else
			$messageStack->add(CROSS_SELL_SORT_ORDER_NOT_UPDATED, 'warning');
			
		if(count($deleted_product_array) > 0){
			$db->Execute('DELETE FROM '.TABLE_PRODUCTS_XSELL.' WHERE ID IN ('.implode(',',$deleted_product_array).')');
			$messageStack->add(sprintf(CROSS_SELL_PRODUCT_DELETED, mysql_affected_rows($db->link)), 'success');
		}
		else
			$messageStack->add(CROSS_SELL_PRODUCT_NOT_DELETED, 'warning');

		$search_result = search_cross_product(zen_db_prepare_input($_POST['cID']));
    break;
	
	case 'cleancross_sell':
		$db->Execute('DELETE FROM '.TABLE_PRODUCTS_XSELL.' WHERE products_id NOT IN (SELECT products_id FROM '.TABLE_PRODUCTS.' WHERE 1=1)');
		$messageStack->add(sprintf(CROSS_SELL_CLEANED_UP,mysql_affected_rows($db->link)),'success');
	break;
}
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<link rel="stylesheet" type="text/css" href="includes/cssjsmenuhover.css" media="all" id="hoverJS">
<script language="javascript" src="includes/menu.js"></script>
<script language="javascript" src="includes/general.js"></script>
<script language="javascript" src="/js/dynamic_input_field.js"></script>
<style type='text/css'>
.submit_link {
 color: #0000ff;
 background-color: transparent;
 text-decoration: none;
 border: none;
}
</style>
<script type="text/javascript">
<!--
function add_product_field(){

	prefix={type:'label',text:'more',innerText:'<?php echo TEXT_PRODUCT_ID ?>: ',attributes:{className:'inputLabel'}};
	sufix={type:'br'};
	addFields('xsellProducts','product_id[',-1,prefix,sufix);
}
function add_remove_main_product_field(chk){
	field_area = document.getElementById('mainProductField');
	if (chk.checked == 1){
		div_field = {type:'div',attributes:{id:'mainProductFieldContent'}};
		addSingleField(field_area, div_field);
		new_field_area = document.getElementById('mainProductFieldContent');
		field1={type:'label',innerText:'Main product: ',attributes:{className:'inputLabel'}};
		field2={type:'input',attributes:{type:'text',name:'main_product_id'}};
		addSingleField(new_field_area, field1);
		addSingleField(new_field_area, field2);
	}
  	else{
  		new_field_area = document.getElementById('mainProductFieldContent');
  		field_area.removeChild(new_field_area);
  	}
}

function init()
{
  cssjsmenu('navbar');
  if (document.getElementById)
  {
    var kill = document.getElementById('hoverJS');
    kill.disabled = true;
  }
}
function delete_confirmation() {
	var theForm = document.forms['update_cross'];
	var numOfCheckedBox = 0;

	for(i=0; i<theForm.elements.length; i++){
	if(theForm.elements[i].type == "checkbox" && theForm.elements[i].checked){
			numOfCheckedBox ++;
	    }
		
	}
	if (numOfCheckedBox > 0){
		var alertText = "You chose to deleted " + numOfCheckedBox + " cross-sell(s). Are you sure you want to delete them?";
		var answer = confirm(alertText)
		if (!answer){
			return false ;
		}
	}
	
	return true;
}
// -->
</script>
</head>
<body onLoad="init()">
<!-- header //-->
<div class="header_area">
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
</div>
<!-- header_eof //-->

  <table border="0" width="100%" cellspacing="0" cellpadding="0">
   <tr>
    <td><?php echo zen_draw_separator('pixel_trans.gif', '100%', '10');?></td>
   </tr>
   <tr>
    <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
   </tr>
   <tr>
    <td><?php echo zen_draw_separator('pixel_trans.gif', '100%', '15');?></td>
   </tr>
  </table>
<div style="padding:20px;padding-top:0px; float:left; width:40%;">
		<h3 style="color:#0066FF;">Clean up cross-sell</h3>
<?php	echo zen_draw_form('clean_cross', FILENAME_XSELL_ADVANCED_PRODUCTS, 'action=cleancross_sell', 'post'); ?>
		<fieldset style="width:100%;">
			<legend>Clean up cross-sell(s) of deleted products</legend><br />
			<div id="xsellCleanup" style="padding-left:15px;">
			Remeber to run this once in a while to clean up cross-sell table!
			</div>
			<div style="float:right"><?php echo zen_image_submit('button_update.gif', IMAGE_UPDATE); ?></div>
		</fieldset>
<?php 
		echo '</form>';
?>
		<br clear="all" />
		<br />
		<center><hr style="color:#cccccc;" size="1" width="80%" /></center>
		<br />
		
 		<h3 style="color:#0066FF;">New Cross-Sells</h3>
<?php	echo zen_draw_form('new_cross', FILENAME_XSELL_ADVANCED_PRODUCTS, 'action=newcross_sell', 'post'); ?>
		<fieldset style="width:100%;">
			<legend>New Cross Sell</legend><br />
			<label class="inputLabel">Product Cross-Sell applies to:&nbsp;</label><br />
			<div id="mainProductField" style="padding-left:15px;"></div>
			<div id="xsellProducts" style="padding-left:15px;">
			<label class="inputLabel"><?php echo TEXT_PRODUCT_ID ?>:</label>
			<?php echo zen_draw_input_field('product_id[0]'); ?><br />
			<label class="inputLabel"><?php echo TEXT_PRODUCT_ID ?>:</label>
			<?php echo zen_draw_input_field('product_id[1]'); ?><br />
			</div>	
			<span style="float:right"><?php echo zen_image_submit('button_insert.gif', IMAGE_INSERT); ?></span>

			<input type="button" value="Add Product Field" onclick="return add_product_field();" /><br />
			<input type="checkbox" name="cross_sell_one_way" value="1" onclick="return add_remove_main_product_field(cross_sell_one_way);" />Cross sell 1 way only? 
		</fieldset>
<?php 
		echo '</form>';
?>
		<br clear="all" />
		<br />
		<center><hr style="color:#cccccc;" size="1" width="80%" /></center>
		<br />
		<h3 style="color:#0066FF;">Edit Cross-Sells</h3>
<?php
		$xsell_products = $db->Execute( "select p.products_id, p.products_model, pd.products_name, count(p.products_id) as xsells from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_PRODUCTS_XSELL . " px " . 
										"where p.products_id = pd.products_id and p.products_id = px.products_id and pd.language_id ='".(int)$languages_id."' group by p.products_id");
		if ($xsell_products->EOF) {
		  echo 'No Cross Sells are currently active.';
		} else {
		  echo '<div style="float:left; width:100%;">';
			echo zen_draw_form('edit_cross', FILENAME_XSELL_ADVANCED_PRODUCTS, 'action=editcross_sell', 'post');
?>
			<fieldset>
				<legend>Edit Current Cross Sell</legend>
				<label class="inputLabel"><?php echo TEXT_PRODUCT_ID ?>:&nbsp;</label>
				<?php
						echo zen_draw_input_field('cID', $_POST['cID']);
				?>
				<span style="float:right"><?php echo zen_image_submit('button_search.gif', IMAGE_SEARCH); ?></span>
			</fieldset><br /><br />
<?php
			echo '</form>'; 
		  if (isset ($search_result['product_lookup']) && $search_result['product_lookup']->RecordCount() > 0) {

			echo zen_draw_form('update_cross', FILENAME_XSELL_ADVANCED_PRODUCTS, 'action=update', 'post'); 
			echo zen_draw_hidden_field('cID', zen_db_prepare_input($_POST['cID']));
?>
			<fieldset>
			  <legend>Product Cross-Sell for <?php echo $_POST['cID']; ?></legend><br />
			  <span style="padding:5px;"><span style="color:#0033CC">Product Name: </span><?php echo $search_result['product_check']->fields['products_name']; ?></span><br /><br />
			  <label class="inputLabel">Current Cross-Sells:&nbsp;<br /><br /></label>
			  <div style="padding-left:15px;">
<?php
		  	  echo '<table cellspacing="0" cellpadding="5" style="border:1px solid #cccccc; border-collapse: collapse;">';
				echo '<tr style="background-color:#dddddd;">';
				  echo '<td>Product ID</td>';
				  echo '<td>Product Model</td>';
				  echo '<td>Name</td>';
				  echo '<td>Sort order</td>';
				  echo '<td>Delete?</td>';
				echo '</tr>';
			
			for ($count = 0; !$search_result['xsell_items']->EOF; $count++) {
				echo '<tr>';
				echo '<td style="border-bottom:1px dashed #cccccc;">' . 
					$search_result['xsell_items']->fields['products_id'] . 
					zen_draw_hidden_field("xsell[$count][id]", $search_result['xsell_items']->fields['ID']) .
					zen_draw_hidden_field("xsell[$count][product_id]", $search_result['xsell_items']->fields['products_id']) . 
					'</td>';
				echo '<td style="border-bottom:1px dashed #cccccc;">' . 
					$search_result['xsell_items']->fields['products_model'] . 
					zen_draw_hidden_field("xsell[$count][product_model]", $search_result['xsell_items']->fields['products_model']) .
					'</td>';	
				echo '<td style="border-bottom:1px dashed #cccccc;">' . $search_result['xsell_items']->fields['products_name'] . '</td>';
				echo '<td style="border-bottom:1px dashed #cccccc;">';
					echo zen_draw_input_field("xsell[$count][new_sort_order]",$search_result['xsell_items']->fields['sort_order'],"size=1");
					echo zen_draw_hidden_field("xsell[$count][old_sort_order]",$search_result['xsell_items']->fields['sort_order']);
				echo '</td>';
				echo '<td style="border-bottom:1px dashed #cccccc;">';
					echo zen_draw_checkbox_field("xsell[$count][delete]", 1, false);
				echo '</td>';
				echo '</tr>';
			  $search_result['xsell_items']->MoveNext();
			}
			echo '<tr><td colspan="5">' .
					zen_image_submit('button_update.gif', IMAGE_UPDATE,'onClick="return delete_confirmation()"') .
				 '</td></tr>';
			echo '</table>';
?>
			</div>
		  </fieldset>
<?php 
		  echo '</form>';
		  }
?>
		</div>
</div>
		<div style="float:right; width:49%; padding-right:20px; padding-left:20px;">
		<h3 style="color:#0066FF;">Current Cross-Sells</h3>
<?php
		  echo '<table cellspacing="0" cellpadding="5" style="border-collapse: collapse; border:1px solid #cccccc; width:100%;">';

			echo '<tr style="background-color:#dddddd;">';
			echo '<td>Product id</td>';
			echo '<td>Product Model</td>';
			echo '<td>Product Name</td>';
			echo '<td>No. of Current Cross-Sells</td>';
			echo '<td>Action</td>';
			echo '</tr>';

		  while (!$xsell_products->EOF) {
			echo '<tr>';
			echo '<td style="border-bottom:1px dashed #cccccc;">' . $xsell_products->fields['products_id'] . '</td>';
			echo '<td style="border-bottom:1px dashed #cccccc;">' . $xsell_products->fields['products_model'] . '</td>';
			echo '<td style="border-bottom:1px dashed #cccccc;">' . $xsell_products->fields['products_name'] . '</td>';
			echo '<td align="center" style="border-bottom:1px dashed #cccccc;">' . $xsell_products->fields['xsells'] . '</td>';
			echo '<td style="border-bottom:1px dashed #cccccc;">';
				echo zen_draw_form('edit_cross', FILENAME_XSELL_ADVANCED_PRODUCTS,'action=editcross_sell', 'post'); 				
				if (XSELL_FORM_INPUT_TYPE == "id")
					echo zen_draw_hidden_field("cID", $xsell_products->fields['products_id']);
				else
					echo zen_draw_hidden_field("cID", $xsell_products->fields['products_model']);
				echo zen_draw_input_field('Edit', 'Edit', 'class="submit_link"', false, 'submit');
				echo '</form>';
			
			echo '</td>';
			echo '</tr>';
			$xsell_products->MoveNext();
		  }
		  echo '</table>';
		 ?></div><?php
		}
?>
<br clear="all" />
<!-- body_eof //-->
<!-- footer //-->
<div class="footer-area">
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
</div>
<!-- footer_eof //-->
<br />
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>