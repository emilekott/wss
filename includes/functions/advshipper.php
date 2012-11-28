<?php

/**
 * advshipper Defines and Functions
 *
 * This file contains defines and functions necessary for operation of Advanced Shipper module.
 *
 * @author     Conor Kerr <zen-cart.advshipper@dev.ceon.net>
 * @copyright  Copyright 2007-2009 Ceon
 * @link       http://dev.ceon.net/web/zen-cart/advshipper
 * @license    http://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 * @version    $Id: advshipper.php 382 2009-06-22 18:49:29Z Bob $
 */

/**
 * Global defines for calculation methods
 */
define('ADVSHIPPER_CALC_METHOD_WEIGHT', 'weight');
define('ADVSHIPPER_CALC_METHOD_PRICE', 'price');
define('ADVSHIPPER_CALC_METHOD_NUM_ITEMS', 'numitems');
define('ADVSHIPPER_CALC_METHOD_SHIPPING_RATE', 'shipping');
define('ADVSHIPPER_CALC_METHOD_UPS', 'ups');
define('ADVSHIPPER_CALC_METHOD_USPS', 'usps');
define('ADVSHIPPER_CALC_METHOD_NUM_PACKAGES', 'numpackages');
define('ADVSHIPPER_CALC_METHOD_TOTAL_ORDER_PRICE', 'totalorderprice');

/**
 * Global defines for product selection setting
 */
define('ADVSHIPPER_SELECT_PRODUCT_FALLOVER', 1);
define('ADVSHIPPER_SELECT_PRODUCT_SPECIFIC', 2);


/**
 * Global defines for availability selection options
 */
define('ADVSHIPPER_AVAILABILITY_SCHEDULING_ALWAYS', 1);
define('ADVSHIPPER_AVAILABILITY_SCHEDULING_ONCE_ONLY', 2);
define('ADVSHIPPER_AVAILABILITY_SCHEDULING_RECURRING', 3);


/**
 * Global defines for availability recurring mode options
 */
define('ADVSHIPPER_AVAILABILITY_RECURRING_MODE_WEEKLY', 1);


/**
 * Global defines for weekly shipping scheduling options
 */
define('ADVSHIPPER_AVAILABILITY_WEEKLY_SHIPPING_SCHEDULING_NONE', 1);
define('ADVSHIPPER_AVAILABILITY_WEEKLY_SHIPPING_SCHEDULING_REGULAR_WEEKDAY', 2);


/**
 * Global defines for region definition options
 */
define('ADVSHIPPER_DEFINITION_METHOD_ADDRESS_MATCHING', 1);
define('ADVSHIPPER_DEFINITION_METHOD_GEOLOCATION', 2);


/**
 * Global defines for rates include tax options
 */
define('ADVSHIPPER_RATES_INC_TAX_INC', 1);
define('ADVSHIPPER_RATES_INC_TAX_EXC', 2);


/**
 * Global defines for rate limits inclusivity options
 */
define('ADVSHIPPER_RATE_LIMITS_INC_INC', 1);
define('ADVSHIPPER_RATE_LIMITS_INC_EXC', 2);


/**
 * Global defines for price totalling options
 */
define('ADVSHIPPER_TOTAL_UP_PRICE_INC_TAX_INC', 1);
define('ADVSHIPPER_TOTAL_UP_PRICE_INC_TAX_EXC', 2);

?>