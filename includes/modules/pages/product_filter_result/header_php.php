<?php
/**
* header_php.php
*
*Zen Cart product filter module
  *Johnny Ye, Oct 2007
  */


require(DIR_WS_MODULES . zen_get_module_directory('require_languages.php'));
$error = false;
$missing_one_input = false;


if (isset($_GET['categories_id'])  && !is_numeric($_GET['categories_id'])){
  $error = true;
  $messageStack->add_session('filter', ERROR_AT_LEAST_ONE_INPUT);
} else {
	$categories_id ='';
}

$available = 'yes';

$option_ids = array();
$option_values = array();
foreach($_GET as $key => $value){
    if(substr_count($key,'options')>0)
	{
		$option_ids[sizeof($option_ids)] = str_replace('options_','',$key);
		$option_values[sizeof($option_values)] = $value;
	}
}
  
  
  if (isset($_GET['categories_id'])) {
    $categories_id = $_GET['categories_id'];
  }
  if (isset($_GET['price_range'])) {
    $price_range = $_GET['price_range'];
  }
  if (isset($_GET['available'])) {
    $available = $_GET['available'];
  }
  if (isset($_GET['sort'])) {
    $sort = $_GET['sort'];
  }
  
  $price_check_error = false;
  if (zen_not_null($pfrom)) {
    if (!settype($pfrom, 'float')) {
      $error = true;
      $price_check_error = true;

      $messageStack->add_session('filter', ERROR_PRICE_FROM_MUST_BE_NUM);
    }
  }

  
$define_list = array('PRODUCT_LIST_MODEL' => PRODUCT_LIST_MODEL,
                     'PRODUCT_LIST_NAME' => PRODUCT_LIST_NAME,
                     'PRODUCT_LIST_MANUFACTURER' => PRODUCT_LIST_MANUFACTURER,
                     'PRODUCT_LIST_PRICE' => PRODUCT_LIST_PRICE,
                     'PRODUCT_LIST_QUANTITY' => PRODUCT_LIST_QUANTITY,
                     'PRODUCT_LIST_WEIGHT' => PRODUCT_LIST_WEIGHT,
                     'PRODUCT_LIST_IMAGE' => PRODUCT_LIST_IMAGE);

asort($define_list);

$column_list = array();
reset($define_list);
while (list($column, $value) = each($define_list)) {
  if ($value) $column_list[] = $column;
}

$select_column_list = '';

for ($col=0, $n=sizeof($column_list); $col<$n; $col++) {
  if (($column_list[$col] == 'PRODUCT_LIST_NAME') || ($column_list[$col] == 'PRODUCT_LIST_PRICE')) {
    continue;
  }

  if (zen_not_null($select_column_list)) {
    $select_column_list .= ', ';
  }

  switch ($column_list[$col]) {
    case 'PRODUCT_LIST_MODEL':
    $select_column_list .= 'p.products_model';
    break;
    case 'PRODUCT_LIST_MANUFACTURER':
    $select_column_list .= 'm.manufacturers_name';
    break;
    case 'PRODUCT_LIST_QUANTITY':
    $select_column_list .= 'p.products_quantity';
    break;
    case 'PRODUCT_LIST_IMAGE':
    $select_column_list .= 'p.products_image';
    break;
    case 'PRODUCT_LIST_WEIGHT':
    $select_column_list .= 'p.products_weight';
    break;
  }
}

// always add quantity regardless of whether or not it is in the listing for add to cart buttons
if (PRODUCT_LIST_QUANTITY < 1) {
  if (empty($select_column_list)) {
    $select_column_list .= ' p.products_quantity ';
  } else  {
    $select_column_list .= ', p.products_quantity ';
  }
}

if (zen_not_null($select_column_list)) {
  $select_column_list .= ', ';
}

// Notifier Point


$select_str = "SELECT DISTINCT " . $select_column_list .
              " m.manufacturers_id, p.products_id, pd.products_name, p.products_price, p.products_tax_class_id, p.products_price_sorter, p.products_qty_box_status ";


$from_str = " FROM " . TABLE_PRODUCTS ." p, " . TABLE_PRODUCTS_DESCRIPTION . "  pd, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c, " . TABLE_PRODUCTS_ATTRIBUTES . " pa, " . TABLE_PRODUCTS_OPTIONS . " po , " . TABLE_PRODUCTS_OPTIONS_VALUES . " pov ";


$order_str='';

$listing_sql = $select_str . $from_str . $where_str . $order_str;
$listing_sql = "select DISTINCT ";
$listing_sql .= "p.products_id, pd.products_name, p.products_price, p.products_tax_class_id, p.products_price_sorter, p.products_qty_box_status ";

 
$where_str = " WHERE (p.products_status = 1 ";
$where_str .=" AND p.products_id = pd.products_id";
$where_str .=" AND pd.language_id = ".$_SESSION['languages_id'];
$where_str .=" AND p.products_id = p2c.products_id	";
if($categories_id !=''){
	$where_str .=" AND p2c.categories_id = ".$categories_id;
}
$enable_price_filter = false;
switch($price_range){
	case 0:
		$pfrom = MIN_PRICE;
		$pto = MAX_PRICE;
	break;
	case 1:
		$pfrom = PRANGE1_MIN;
		$pto = PRANGE1_MAX;
	break;
	case 2:
		$pfrom = PRANGE2_MIN;
		$pto = PRANGE2_MAX;
	break;
	case 3:
		$pfrom = PRANGE3_MIN;
		$pto = PRANGE3_MAX;
	break;
	case 4:
		$pfrom = PRANGE4_MIN;
		$pto = PRANGE4_MAX;
	break;
	case 5:
		$pfrom = PRANGE5_MIN;
		$pto = PRANGE5_MAX;
	break;
}
if(SHOW_SORT){
	switch($sort){
		case 0:
			$order_str = " order by p.products_date_added DESC, p.products_date_available DESC, products_price ASC ";
		break;
		case 1:
			$order_str = " order by p.products_date_added ASC, p.products_date_available ASC, products_price ASC ";
		break;
		case 2:
			$order_str = " order by P.products_price ASC, p.products_date_added DESC, p.products_date_available DESC ";
		break;
		case 3:
			$order_str = " order by products_price DESC, p.products_date_added DESC, p.products_date_available DESC  ";
		break;
		$order_str = " order by p.products_date_added DESC, p.products_date_available DESC, products_price ASC ";
	}
}


$enable_attribute_filter = false;
if(sizeof($option_ids)>0){
	for($i=0;$i<sizeof($option_ids);$i++){
		if(is_numeric($option_values[$i])){
			$where_str .= " AND pa.options_id = ".$option_ids[$i];
			$where_str .= " AND pa.options_values_id = ".$option_values[$i];
			$enable_attribute_filter = true;
		}
	}
}

if($enable_price_filter){
	$where_str .= " AND p.products_price >= ".$pfrom;
	$where_str .= " AND p.products_price <= ".$pto;
}

if($enable_attribute_filter){
	$where_str .= " AND p.products_id = pa.products_id ";
}
if($available=='yes'){
	$where_str .= " AND p.products_status =1 ";
	$where_str .= " AND p.products_quantity > 0 ";	
}


$where_str .=" )";

$listing_sql .= $from_str;
$listing_sql .= $where_str;
$listing_sql .= $order_str;

//$breadcrumb->add('title');


// This should be last line of the script:
?>