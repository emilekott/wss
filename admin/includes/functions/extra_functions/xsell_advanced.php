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
 
	function add_new_cross_product($products_id, $pid) {
	  global $db, $messageStack;
		// Make sure the 2 products exist
		if (XSELL_FORM_INPUT_TYPE == "model"){
			// For some reason the union query does not work in mysql 4.1 so we have to select 1 by 1
			$first_cross_product = $db->Execute("SELECT products_id FROM " . TABLE_PRODUCTS . " WHERE products_model = '$products_id'");
			$second_cross_product = $db->Execute("SELECT products_id FROM ". TABLE_PRODUCTS . " WHERE products_model = '$pid'");
			}
		else{
			$first_cross_product = $db->Execute("SELECT products_id FROM " . TABLE_PRODUCTS . " WHERE products_id = $products_id");
			$second_cross_product = $db->Execute("SELECT products_id FROM ". TABLE_PRODUCTS . " WHERE products_id = $pid");
			}
			// We should get back 2 products_id
		if ($first_cross_product->RecordCount() != 1)
			$messageStack->add(sprintf(CROSS_SELL_PRODUCT_NOT_FOUND, $products_id), 'error');
		elseif ($second_cross_product->RecordCount() != 1)
			$messageStack->add(sprintf(CROSS_SELL_PRODUCT_NOT_FOUND, $pid), 'error');
		else{
			$first_record = $first_cross_product->fields['products_id'];
			$second_record = $second_cross_product->fields['products_id'];

			$check_xsell = $db->Execute("select count(products_id) as records from " . TABLE_PRODUCTS_XSELL . " where products_id = '" . $first_record . "' and xsell_id = '" . $second_record . "'");
			if ($check_xsell->fields['records'] > 0) {
				$messageStack->add(sprintf(CROSS_SELL_ALREADY_ADDED, $pid, $products_id), 'error');
			} 
			else {
				$insert_array = array('products_id'	=>	$first_record,
									'xsell_id'		=>	$second_record,
									'sort_order'	=>	'1'
									);
				zen_db_perform(TABLE_PRODUCTS_XSELL, $insert_array);
				$messageStack->add(sprintf(CROSS_SELL_ADDED, $pid, $products_id), 'success');
			}
		}		
	}
	
	function search_cross_product($pid) {
		global $db, $messageStack, $languages_id;
		$result = array('product_lookup' => null,
						'xsell_items' => null,
						'product_check' => null,
						);
		if (XSELL_FORM_INPUT_TYPE == "model")
			$result['product_lookup'] = $db->Execute("select p.products_id from " . TABLE_PRODUCTS . " p " . 
									 "where p.products_model = '$pid' LIMIT 1");
		else
			$result['product_lookup'] = $db->Execute("select p.products_id from " . TABLE_PRODUCTS . " p " . 
									 "where p.products_id = $pid LIMIT 1");
			
									 
		if ($result['product_lookup']->RecordCount() > 0) {
			$result['product_check'] = $db->Execute(  "select p.products_id, p.products_model, pd.products_name, count(p.products_id) as xsells from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd " . 
												"where p.products_id = pd.products_id and p.products_id = '" . $result['product_lookup']->fields['products_id'] . "' and pd.language_id ='".(int)$languages_id."' group by p.products_id");

			$result['xsell_items'] = $db->Execute("select p.products_id, p.products_model, pd.products_name, px.ID, px.sort_order from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_PRODUCTS_XSELL . " px " . 
											"where p.products_id = pd.products_id and p.products_id = px.xsell_id and px.products_id = '" . $result['product_lookup']->fields['products_id'] . "' and pd.language_id ='".(int)$languages_id."' group by p.products_id");
											
		}
		else
			$messageStack->add(sprintf(CROSS_SELL_PRODUCT_NOT_FOUND, $pid), 'warning');
		
		return $result;		
	}
?>