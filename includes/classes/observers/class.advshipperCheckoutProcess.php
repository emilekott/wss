<?php

/**
 * advshipper Checkout Process Shipping Date Record
 *
 * @author     Conor Kerr <zen-cart.advshipper@dev.ceon.net>
 * @copyright  Copyright 2007-2009 Ceon
 * @link       http://dev.ceon.net/web/zen-cart/advshipper
 * @license    http://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 * @version    $Id: class.advshipperCheckoutProcess.php 382 2009-06-22 18:49:29Z Bob $
 */

// {{{ class advshipperCheckoutProcess

/**
 * Records the shipping date for the current order, if a dated Advanced Shipper method was selected
 * as the shipping method for the order.
 *
 * @author     Conor Kerr <zen-cart.advshipper@dev.ceon.net>
 * @copyright  Copyright 2007-2009 Ceon
 * @link       http://dev.ceon.net/web/zen-cart/advshipper
 * @license    http://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 * @version    $Id: class.advshipperCheckoutProcess.php 382 2009-06-22 18:49:29Z Bob $
 */
class advshipperCheckoutProcess extends base
{
	
	function advshipperCheckoutProcess()
	{
		global $zco_notifier;
		
		$zco_notifier->attach($this,
			array(
				'NOTIFY_CHECKOUT_PROCESS_AFTER_ORDER_CREATE'
				)
			);
	}
	
	function update(&$callingClass, $notifier, $paramsArray)
	{
		global $db, $order, $shipping_modules, $insert_id;
		
		// Did the order use a dated Advanced Shipper Method?
		list($module, $method) = explode('_', $order->info['shipping_module_code']);
		
		if ($module == 'advshipper') {
			// Rebuild the quote so the timestamp can be identified/recorded			
			$quote = $shipping_modules->quote($method, $module);
			
			$shipping_ts = $quote[0]['methods'][0]['shipping_ts'];
			
			if (!is_null($shipping_ts)) {
				// Method has a shipping date so must be recorded!
				$order_shipping_record_query = "
					INSERT INTO
						" . TABLE_ADVANCED_SHIPPER_ORDERS . "
						(
						zen_order_id,
						shipping_ts,
						shipping_method
						)
					VALUES
						(
						:zen_order_id,
						:shipping_ts,
						:shipping_method
						);";
					
				$order_shipping_record_query = $db->bindVars($order_shipping_record_query, ':zen_order_id', $insert_id, 'integer');
				$order_shipping_record_query = $db->bindVars($order_shipping_record_query, ':shipping_ts', date('Y-m-d H:i:00', $shipping_ts), 'date');
				$order_shipping_record_query = $db->bindVars($order_shipping_record_query, ':shipping_method', $order->info['shipping_method'], 'string');
				$order_shipping_record_result = $db->Execute($order_shipping_record_query);
			}
		}
	}
}

// }}}
 
?>