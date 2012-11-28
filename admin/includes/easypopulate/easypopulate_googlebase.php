<?php
/*
Easy Populate Google Base defaults

Use the $ep_googlebase array to set your default values. for each setting you want to activate, change 'false' to 'true'. Easy Populate derives values from the store for those defaults below that are true, but with a value 'IGNORED'. Other values below that are active ('true') with no value are recommended by Google Base - please enter a value for these, or deactivate them ('false').

You may also add any new values as long as you follow the naming convention. Values entered this way will apply for all products.

See here for more information: http://base.google.com/base/help/tab_instructions.html

This configuration file will be migrated to a more user friendly method at some time - probably a databse table - suggestions are welcome - http://www.zencartbuilder.com
*/

// REQUIRED SETTING!!
// export prices with tax? 'true' or 'false'
$googlebase_price_inc_tax = false;

// defaults to product name - value ignored here
if ( true ) $ep_googlebase['title'] = 'IGNORED';

// defaults to product description - value ignored here
if ( true ) $ep_googlebase['description'] = 'IGNORED';

// defaults to store url - value ignored here
if ( true ) $ep_googlebase['link'] = 'IGNORED';

// defaults to image url - value ignored here
if ( true ) $ep_googlebase['image_link'] = 'IGNORED';

// defaults to product id - value ignored here
if ( true ) $ep_googlebase['id'] = 'IGNORED';

// default value below is set to 30 days
// values for mktime: (hour, minute, second, month, day, year, is_dst). See: http://php.net/manual/en/function.mktime.php
if ( true ) $ep_googlebase['expiration_date'] = date('Y-m-d', mktime(0, 0, 0, date("m"), date("d")+30, date("Y")));

// defaults to product category path - enter your own values to override ALL products
if ( true ) $ep_googlebase['label'] = '';

// price - value ignored here
if ( true ) $ep_googlebase['price'] = 'IGNORED';

// value can only be 'negotiable' or 'starting'
if ( true ) $ep_googlebase['price_type'] = 'starting';

// defaults to store default currency - value ignored here
if ( true ) $ep_googlebase['currency'] = 'IGNORED';

// include at least one of: "Cash," "Check," "GoogleCheckout," "Visa," "MasterCard," "AmericanExpress," "Discover," or "WireTransfer." If you accept more than one method, separate each method with a comma.
if ( true ) $ep_googlebase['payment_accepted'] = 'Cash';

// Additional instructions to explain your payment policies - Commas allowed: eg. "Cash only".
if ( true ) $ep_googlebase['payment_notes'] = '';

// defaults to store default product quantity - value ignored here
if ( true ) $ep_googlebase['quantity'] = 'IGNORED';

// defaults to store manufacturer - value ignored here
if ( true ) $ep_googlebase['brand'] = 'IGNORED';

// defaults to store manufacturer - value ignored here
if ( true ) $ep_googlebase['manufacturer'] = 'IGNORED';

// defaults to store products model - value ignored here
if ( true ) $ep_googlebase['manufacturer_id'] = 'IGNORED';

// defaults to store products model - value ignored here
if ( true ) $ep_googlebase['model_number'] = 'IGNORED';

// defaults to store products weight - value ignored here
if ( true ) $ep_googlebase['weight'] = 'IGNORED';

// Condition of products eg. 'new', 'used'
if ( true ) $ep_googlebase['condition'] = 'new';

// choose to use either your stores top category for this product, or the immediate category for this product, or use a name to override all products
// Either '1' for top category, or '0' for immediate category, or anything else to override all
if ( true ) $ep_googlebase['product_type'] = '1';

// See http://base.google.com/base/help/tab_attributes.html#location
if ( true ) $ep_googlebase['location'] = '';

// not yet implemented in Easy Populate - activation of these will add the value to ALL products in export
if ( false ) $ep_googlebase['upc'] = '';
if ( false ) $ep_googlebase['isbn'] = '';
if ( false ) $ep_googlebase['memory'] = '';
if ( false ) $ep_googlebase['processor_speed'] = '';
if ( false ) $ep_googlebase['size'] = '';
if ( false ) $ep_googlebase['color'] = '';
if ( false ) $ep_googlebase['actor'] = '';
if ( false ) $ep_googlebase['artist'] = '';
if ( false ) $ep_googlebase['author'] = '';
if ( false ) $ep_googlebase['format'] = '';
