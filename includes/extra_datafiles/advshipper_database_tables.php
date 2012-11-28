<?php

/**
 * Ceon Advanced Shipper Database Table Name Definitions
 *
 * @author     Conor Kerr <zen-cart.advshipper@dev.ceon.net>
 * @copyright  Copyright 2007-2009 Ceon
 * @copyright  Portions Copyright 2003-2006 Zen Cart Development Team
 * @copyright  Portions Copyright 2003 osCommerce
 * @link       http://dev.ceon.net/web/zen-cart/advshipper
 * @license    http://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 * @version    $Id: advshipper_database_tables.php 382 2009-06-22 18:49:29Z Bob $
 */

if (!defined('DB_PREFIX')) {
    define('DB_PREFIX', '');
}

define('TABLE_ADVANCED_SHIPPER_CONFIGS', DB_PREFIX . 'advshipper_configs');
define('TABLE_ADVANCED_SHIPPER_METHOD_CONFIGS', DB_PREFIX . 'advshipper_method_configs');
define('TABLE_ADVANCED_SHIPPER_METHOD_TITLES', DB_PREFIX . 'advshipper_method_titles');
define('TABLE_ADVANCED_SHIPPER_METHOD_CATEGORIES', DB_PREFIX . 'advshipper_method_categories');
define('TABLE_ADVANCED_SHIPPER_METHOD_MANUFACTURERS', DB_PREFIX . 'advshipper_method_manufacturers');
define('TABLE_ADVANCED_SHIPPER_METHOD_PRODUCTS', DB_PREFIX . 'advshipper_method_products');
define('TABLE_ADVANCED_SHIPPER_REGION_CONFIGS', DB_PREFIX . 'advshipper_region_configs');
define('TABLE_ADVANCED_SHIPPER_REGION_UPS_CONFIGS', DB_PREFIX . 'advshipper_region_ups_configs');
define('TABLE_ADVANCED_SHIPPER_REGION_USPS_CONFIGS', DB_PREFIX . 'advshipper_region_usps_configs');
define('TABLE_ADVANCED_SHIPPER_REGION_TITLES', DB_PREFIX . 'advshipper_region_titles');
define('TABLE_ADVANCED_SHIPPER_REGION_SURCHARGE_TITLES', DB_PREFIX . 'advshipper_region_surcharge_titles');
define('TABLE_ADVANCED_SHIPPER_ORDERS', DB_PREFIX . 'advshipper_orders');

?>