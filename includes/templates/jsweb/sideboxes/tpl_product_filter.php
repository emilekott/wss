<?php
/**
* tpl_prodcut_filter.php
*
*Zen Cart product filter module
  *Johnny Ye, Oct 2007
 *
 */

   	$content = "";
	$content .= '<div id="' . str_replace('_', '-', $box_id . 'Content') . '" class="sideBoxContent centeredContent">';
	$content .= zen_draw_form('product_filter_from', zen_href_link('product_filter_result', '', 'NONSSL', false), 'get');
	$content .= zen_draw_hidden_field('main_page','product_filter_result');

	/*start categories drop down*************************************************/
	if(SHOW_CATEGORIES){
		$content .= zen_draw_pull_down_menu('categories_id', zen_get_categories(array(array('id' => '', 'text' => PRODUCT_LISTING_SEARCH_TEXT_ALL_CATEGORIES)), '0' ,'', '1'), (isset($_GET['categories_id']) ? $_GET['categories_id'] : ''), '') . zen_hide_session_id();
	}
	/*end categories drop down*************************************************/
	
	/*start  price range drop down*************************************************/
	if(SHOW_PRICE_RANGE){
		$prices =  array (array("id"=> 0, "text" => "Price Range"),
				array("id"=> 1, "text" => PRANGE1_WORD),
				array("id"=> 2, "text" => PRANGE2_WORD),
				array("id"=> 3, "text" => PRANGE3_WORD),
				array("id"=> 4, "text" => PRANGE4_WORD),
				array("id"=> 5, "text" => PRANGE5_WORD));
		$content .= zen_draw_pull_down_menu('price_range', $prices, (isset($_GET['price_range']) ? $_GET['price_range'] : ''), '') . zen_hide_session_id();
	}
	/*end  price range drop down*************************************************/

	/*start attributes drop down*************************************************/
	if(SHOW_ATTRIBUTES){
		$option_names = $db->Execute("SELECT products_options_id,products_options_name FROM ". TABLE_PRODUCTS_OPTIONS);
		while (!$option_names->EOF) {
		$options_array = array (array("id"=> "", "text" => $option_names->fields['products_options_name']));
		$option_names_values = $db->Execute("select pov.products_options_values_id, pov.products_options_values_name from ". TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS." povp,".TABLE_PRODUCTS_OPTIONS_VALUES." pov where povp.products_options_values_id = pov.products_options_values_id AND povp.products_options_id =".$option_names->fields['products_options_id']);
			while (!$option_names_values->EOF) {
				$options_array = array_pad($options_array,sizeof($options_array )+1,array("id"=>$option_names_values->fields['products_options_values_id'],"text"=>$option_names_values->fields['products_options_values_name']));
				$option_names_values->MoveNext();
			}
		$content .= zen_draw_pull_down_menu('options_'.$option_names->fields['products_options_id'], $options_array, (isset($_GET['options_'.$option_names->fields['products_options_id']]) ? $_GET['options_'.$option_names->fields['products_options_id']] : ''), '') . zen_hide_session_id();
		$option_names->MoveNext();
		}  	
	}
	/*end  attributes drop down*************************************************/
	/*start available drop down*************************************************/
	if(SHOW_AVAILABLE)
	{
		$available =  array (array("id"=> 0, "text" => "All Items"),
					array("id"=> 'yes', "text" => "Available"));
		$content .= zen_draw_pull_down_menu('available', $available, (isset($_GET['available']) ? $_GET['available'] : 'yes'), '') . zen_hide_session_id();
	}
	/*end available drop down*************************************************/
	
	/* sort drop down *************************************************/
	if(SHOW_SORT){
		$sort =  array (array("id"=> 0, "text" => "Newest Product"),
					array("id"=> 1, "text" => "Oldest Product"),
					array("id"=> 2, "text" => "Price Low to High"),
					array("id"=> 3, "text" => "Price High to Low")
					);	
		$content .= zen_draw_pull_down_menu('sort', $sort, (isset($_GET['sort']) ? $_GET['sort'] : ''), '') . zen_hide_session_id();
	}
	/*end  sort drop down *************************************************/
	$content .= '<br /><input type="submit" value="' . PRODUCT_FILTER_BUTTON_NAME . '" style="width: 65px" />';
	$content .= "</form>";
	$content .= '</div>';
?>

