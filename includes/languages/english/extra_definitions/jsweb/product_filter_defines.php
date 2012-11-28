<?php
/**
* product_filter_defines.php
*
*Zen Cart product filter module
  *Johnny Ye, Oct 2007
  */
define('FILENAME_PRODUCT_FILTER_RESULT', 'product_filter_result');
define('BOX_HEADING_FILTER', 'My Filter');

//define('HEADING_TITLE','Products');
define('PRODUCT_FILTER_BUTTON_NAME', 'Go');
  
define('MIN_PRICE','0');
define('MAX_PRICE','1000000');

define('PRANGE1_WORD','Below �10');
define('PRANGE1_MIN',MIN_PRICE);
define('PRANGE1_MAX',9.99);

define('PRANGE2_WORD','�10 -- �20');
define('PRANGE2_MIN',10.00);
define('PRANGE2_MAX',19.99);

define('PRANGE3_WORD','�20 -- �30');
define('PRANGE3_MIN',20.00);
define('PRANGE3_MAX',29.99);

define('PRANGE4_WORD','�30 -- �40');
define('PRANGE4_MIN',30.00);
define('PRANGE4_MAX',49.99);

define('PRANGE5_WORD','Above �50');
define('PRANGE5_MIN',50.00);
define('PRANGE5_MAX',MAX_PRICE);

define('SHOW_CATEGORIES',true);
define('SHOW_ATTRIBUTES',true);
define('SHOW_PRICE_RANGE',true);
define('SHOW_AVAILABLE',true);
define('SHOW_SORT',true);
?>