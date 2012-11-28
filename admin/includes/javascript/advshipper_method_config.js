<?php

/**
 * @author     Conor Kerr <zen-cart.advshipper@dev.ceon.net>
 * @copyright  Copyright 2007-2009 Ceon
 * @copyright  Portions Copyright 2003-2006 Zen Cart Development Team
 * @copyright  Portions Copyright 2003 osCommerce
 * @link       http://dev.ceon.net/web/zen-cart/advshipper
 * @version    $Id: advshipper_method_config.js 382 2009-06-22 18:49:29Z Bob $
 */

?>

/**
 * Variables holds information about categories, manufacturers and products for which this method
 * applies (if any)
 */
var categories = new Array();
var manufacturers = new Array();
var products = new Array();

/**
 * Variable holds information for all regions for this method
 */
var regions = new Array();

var session_language_id = <?php echo $_SESSION['languages_id']; ?>;


function entity(str)
{
    var e = document.createElement("div");
    e.innerHTML=String(str);
    return e.innerHTML;
}

if (!window.Node) {
	var Node = {
		ELEMENT_NODE: 1,
		ATTRIBUTE_NODE: 2,
		TEXT_NODE: 3
	}
}

var submitting_form = false;

/**
 * Submit form if enter pressed.. set variable to prevent other buttons from processing their
 * onclick actions in firefox
 */
function advshipperCheckEnterPressed(e)
{
	characterCode = e.keyCode
	
	if (characterCode == 13) {
		submitting_form = true;
		document.advshipper.submit();
		return true;
	}
}


function advshipperMethodAvailabilitySchedulingSelected(value)
{
	method_once_only_start_date_header_el = document.getElementById('method_once_only_start_date_header');
	method_once_only_start_date_field_el = document.getElementById('method_once_only_start_date_field');
	method_once_only_end_date_header_el = document.getElementById('method_once_only_end_date_header');
	method_once_only_end_date_field_el = document.getElementById('method_once_only_end_date_field');
	
	method_availability_recurring_mode_header_el = document.getElementById('method_availability_recurring_mode_header');
	method_availability_recurring_mode_field_el = document.getElementById('method_availability_recurring_mode_field');
	method_availability_weekly_start_day_and_time_header_el = document.getElementById('method_availability_weekly_start_day_and_time_header');
	method_availability_weekly_start_day_and_time_field_el = document.getElementById('method_availability_weekly_start_day_and_time_field');
	method_availability_weekly_cutoff_day_and_time_header_el = document.getElementById('method_availability_weekly_cutoff_day_and_time_header');
	method_availability_weekly_cutoff_day_and_time_field_el = document.getElementById('method_availability_weekly_cutoff_day_and_time_field');
	
	method_usage_limit_header_el = document.getElementById('method_usage_limit_header');
	method_usage_limit_field_el = document.getElementById('method_usage_limit_field');
	
	method_once_only_shipping_date_header_el = document.getElementById('method_once_only_shipping_date_header');
	method_once_only_shipping_date_field_el = document.getElementById('method_once_only_shipping_date_field');
	
	method_availability_weekly_shipping_scheduling_header_el = document.getElementById('method_availability_weekly_shipping_scheduling_header');
	method_availability_weekly_shipping_scheduling_field_el = document.getElementById('method_availability_weekly_shipping_scheduling_field');
	method_availability_weekly_shipping_show_num_weeks_header_el = document.getElementById('method_availability_weekly_shipping_show_num_weeks_header');
	method_availability_weekly_shipping_show_num_weeks_field_el = document.getElementById('method_availability_weekly_shipping_show_num_weeks_field');
	method_availability_weekly_shipping_regular_weekday_day_and_time_header_el = document.getElementById('method_availability_weekly_shipping_regular_weekday_day_and_time_header');
	method_availability_weekly_shipping_regular_weekday_day_and_time_field_el = document.getElementById('method_availability_weekly_shipping_regular_weekday_day_and_time_field');
	
	if (value == <?php echo ADVSHIPPER_AVAILABILITY_SCHEDULING_ALWAYS; ?>) {
		method_once_only_start_date_header_el.style.display = 'none';
		method_once_only_start_date_field_el.style.display = 'none';
		method_once_only_end_date_header_el.style.display = 'none';
		method_once_only_end_date_field_el.style.display = 'none';
		
		method_availability_recurring_mode_header_el.style.display = 'none';
		method_availability_recurring_mode_field_el.style.display = 'none';
		method_availability_weekly_start_day_and_time_header_el.style.display = 'none';
		method_availability_weekly_start_day_and_time_field_el.style.display = 'none';
		method_availability_weekly_cutoff_day_and_time_header_el.style.display = 'none';
		method_availability_weekly_cutoff_day_and_time_field_el.style.display = 'none';
		
		method_usage_limit_header_el.style.display = 'none';
		method_usage_limit_field_el.style.display = 'none';
		
		method_once_only_shipping_date_header_el.style.display = 'none';
		method_once_only_shipping_date_field_el.style.display = 'none';
		
		method_availability_weekly_shipping_scheduling_header_el.style.display = 'none';
		method_availability_weekly_shipping_scheduling_field_el.style.display = 'none';
		method_availability_weekly_shipping_show_num_weeks_header_el.style.display = 'none';
		method_availability_weekly_shipping_show_num_weeks_field_el.style.display = 'none';
		method_availability_weekly_shipping_regular_weekday_day_and_time_header_el.style.display = 'none';
		method_availability_weekly_shipping_regular_weekday_day_and_time_field_el.style.display = 'none';
	} else if (value == <?php echo ADVSHIPPER_AVAILABILITY_SCHEDULING_ONCE_ONLY; ?>) {
		method_once_only_start_date_header_el.style.display = '';
		method_once_only_start_date_field_el.style.display = '';
		method_once_only_end_date_header_el.style.display = '';
		method_once_only_end_date_field_el.style.display = '';
		
		method_availability_recurring_mode_header_el.style.display = 'none';
		method_availability_recurring_mode_field_el.style.display = 'none';
		method_availability_weekly_start_day_and_time_header_el.style.display = 'none';
		method_availability_weekly_start_day_and_time_field_el.style.display = 'none';
		method_availability_weekly_cutoff_day_and_time_header_el.style.display = 'none';
		method_availability_weekly_cutoff_day_and_time_field_el.style.display = 'none';
		
		method_usage_limit_header_el.style.display = '';
		method_usage_limit_field_el.style.display = '';
		
		method_once_only_shipping_date_header_el.style.display = '';
		method_once_only_shipping_date_field_el.style.display = '';
		
		method_availability_weekly_shipping_scheduling_header_el.style.display = 'none';
		method_availability_weekly_shipping_scheduling_field_el.style.display = 'none';
		method_availability_weekly_shipping_show_num_weeks_header_el.style.display = 'none';
		method_availability_weekly_shipping_show_num_weeks_field_el.style.display = 'none';
		method_availability_weekly_shipping_regular_weekday_day_and_time_header_el.style.display = 'none';
		method_availability_weekly_shipping_regular_weekday_day_and_time_field_el.style.display = 'none';
	} else {
		method_once_only_start_date_header_el.style.display = 'none';
		method_once_only_start_date_field_el.style.display = 'none';
		method_once_only_end_date_header_el.style.display = 'none';
		method_once_only_end_date_field_el.style.display = 'none';
		
		method_availability_recurring_mode_header_el.style.display = '';
		method_availability_recurring_mode_field_el.style.display = '';
		method_availability_weekly_start_day_and_time_header_el.style.display = '';
		method_availability_weekly_start_day_and_time_field_el.style.display = '';
		method_availability_weekly_cutoff_day_and_time_header_el.style.display = '';
		method_availability_weekly_cutoff_day_and_time_field_el.style.display = '';
		
		method_usage_limit_header_el.style.display = '';
		method_usage_limit_field_el.style.display = '';
		
		method_once_only_shipping_date_header_el.style.display = 'none';
		method_once_only_shipping_date_field_el.style.display = 'none';
		
		method_availability_weekly_shipping_scheduling_header_el.style.display = '';
		method_availability_weekly_shipping_scheduling_field_el.style.display = '';
		method_availability_weekly_shipping_show_num_weeks_header_el.style.display = 'none';
		method_availability_weekly_shipping_show_num_weeks_field_el.style.display = 'none';
		method_availability_weekly_shipping_regular_weekday_day_and_time_header_el.style.display = 'none';
		method_availability_weekly_shipping_regular_weekday_day_and_time_field_el.style.display = 'none';
	}
	
	// Reset values to defaults
	try {
		document.advshipper.method_once_only_start_date.value = '';
		document.advshipper.method_once_only_start_time.value = '00:00';
		document.advshipper.method_once_only_end_date.value = '';
		document.advshipper.method_once_only_end_time.value = '00:00';
		
		document.getElementById('method_availability_weekly_start_day').selectedIndex = 0;
		document.getElementById('method_availability_weekly_start_time').value = '00:00';
		document.getElementById('method_availability_weekly_cutoff_day').selectedIndex = 0;
		document.getElementById('method_availability_weekly_cutoff_time').value = '00:00';
		
		document.getElementById('method_usage_limit').value = '';
		
		document.advshipper.method_once_only_shipping_date.value = '';
		document.getElementById('method_once_only_shipping_time').value = '00:00';
		
		document.getElementById('method_availability_weekly_shipping_show_num_weeks').value = '1';
		
		document.getElementById('method_availability_weekly_shipping_scheduling' + '_' + 'none').checked = true;
		document.getElementById('method_availability_weekly_shipping_scheduling' + '_' + 'regular_weekday').checked = false;
		
		document.getElementById('method_availability_weekly_shipping_regular_weekday_day').selectedIndex = 0;
		document.getElementById('method_availability_weekly_shipping_regular_weekday_time').value = '00:00';
	} catch (e) {
		document.advshipper.eval('method_once_only_start_date').value = '';
		document.advshipper.eval('method_once_only_start_time').value = '00:00';
		document.advshipper.eval('method_once_only_end_date').value = '';
		document.advshipper.eval('method_once_only_end_time').value = '00:00';
		
		document.advshipper.eval('method_availability_weekly_start_day').selectedIndex = 0;
		document.advshipper.eval('method_availability_weekly_start_time').value = '00:00';
		document.advshipper.eval('method_availability_weekly_cutoff_day').selectedIndex = 0;
		document.advshipper.eval('method_availability_weekly_cutoff_time').value = '00:00';
		
		document.advshipper.eval('method_usage_limit').value = '';
		
		document.advshipper.eval('method_once_only_shipping_date').value = '';
		document.advshipper.eval('method_once_only_shipping_time').value = '00:00';
		
		document.advshipper.eval('method_availability_weekly_shipping_show_num_weeks').value = '1';
		
		document.advshipper.eval('method_availability_weekly_shipping_scheduling' + '_' + 'none').checked = true;
		document.advshipper.eval('method_availability_weekly_shipping_scheduling' + '_' + 'regular_weekday').checked = false;
		
		document.advshipper.eval('method_availability_weekly_shipping_regular_weekday_day').selectedIndex = 0;
		document.advshipper.eval('method_availability_weekly_shipping_regular_weekday_time').value = '00:00';
		}
}

function advshipperAvailabilityWeeklyStartDay()
{
	// Get the selected start day (if any)
	try {
		start_day_index = document.getElementById('method_availability_weekly_start_day').selectedIndex;
	} catch (e) {
		start_day_index = document.advshipper.eval('method_availability_weekly_start_day').selectedIndex;
	}
	
	method_availability_weekly_shipping_show_num_weeks_header_el = document.getElementById('method_availability_weekly_shipping_show_num_weeks_header');
	method_availability_weekly_shipping_show_num_weeks_field_el = document.getElementById('method_availability_weekly_shipping_show_num_weeks_field');
	
	// Find out if "No Shipping Scheduling" is selected
	try {
		method_availability_weekly_shipping_scheduling_none = document.getElementById('method_availability_weekly_shipping_scheduling' + '_' + 'none').checked;
	} catch (e) {
		method_availability_weekly_shipping_scheduling_none = document.advshipper.eval('method_availability_weekly_shipping_scheduling' + '_' + 'none').checked;
	}
	
	if (start_day_index == 0 && method_availability_weekly_shipping_scheduling_none != true) {
		// Enable repeating method
		method_availability_weekly_shipping_show_num_weeks_header_el.style.display = '';
		method_availability_weekly_shipping_show_num_weeks_field_el.style.display = '';
	} else {
		// Start day is selected so can't repeat method weekly
		method_availability_weekly_shipping_show_num_weeks_header_el.style.display = 'none';
		method_availability_weekly_shipping_show_num_weeks_field_el.style.display = 'none';
		
		try {
			document.getElementById('method_availability_weekly_shipping_show_num_weeks').value = '2';
		} catch (e) {
			document.advshipper.eval('method_availability_weekly_shipping_show_num_weeks').value = '2';
		}
	}
}

function advshipperAvailabilityWeeklyCutoffDay()
{
	// Get the selected cutoff day (if any)
	try {
		cutoff_day_index = document.getElementById('method_availability_weekly_cutoff_day').selectedIndex;
	} catch (e) {
		cutoff_day_index = document.advshipper.eval('method_availability_weekly_cutoff_day').selectedIndex;
	}
	
	method_availability_weekly_shipping_scheduling_regular_weekday_div_el = document.getElementById('method_availability_weekly_shipping_scheduling_regular_weekday_div');
	
	if (cutoff_day_index == 0) {		
		// Reset options for shipping scheduling as no cutoff has been specified
		try {
			document.getElementById('method_availability_weekly_shipping_scheduling' + '_' + 'none').checked = true;
		} catch (e) {
			document.advshipper.eval('method_availability_weekly_shipping_scheduling' + '_' + 'none').checked = true;
		}
		
		advshipperMethodAvailabilityWeeklyDeliverySchedulingSelected(<?php echo ADVSHIPPER_AVAILABILITY_WEEKLY_SHIPPING_SCHEDULING_NONE; ?>);
		
		// Hide option for regular weekday shipping
		method_availability_weekly_shipping_scheduling_regular_weekday_div_el.style.display = 'none';
	} else {
		// Show option for regular weekday shipping
		method_availability_weekly_shipping_scheduling_regular_weekday_div_el.style.display = '';
	}
}


function advshipperMethodAvailabilityWeeklyDeliverySchedulingSelected(value)
{
	method_availability_weekly_shipping_show_num_weeks_header_el = document.getElementById('method_availability_weekly_shipping_show_num_weeks_header');
	method_availability_weekly_shipping_show_num_weeks_field_el = document.getElementById('method_availability_weekly_shipping_show_num_weeks_field');
	method_availability_weekly_shipping_regular_weekday_day_and_time_header_el = document.getElementById('method_availability_weekly_shipping_regular_weekday_day_and_time_header');
	method_availability_weekly_shipping_regular_weekday_day_and_time_field_el = document.getElementById('method_availability_weekly_shipping_regular_weekday_day_and_time_field');
	
	if (value == <?php echo ADVSHIPPER_AVAILABILITY_WEEKLY_SHIPPING_SCHEDULING_NONE; ?>) {
		advshipperAvailabilityWeeklyStartDay();
		
		method_availability_weekly_shipping_show_num_weeks_header_el.style.display = 'none';
		method_availability_weekly_shipping_show_num_weeks_field_el.style.display = 'none';
		method_availability_weekly_shipping_regular_weekday_day_and_time_header_el.style.display = 'none';
		method_availability_weekly_shipping_regular_weekday_day_and_time_field_el.style.display = 'none';
		
		try {
			document.getElementById('method_availability_weekly_shipping_regular_weekday_day').selectedIndex = 0;
			document.getElementById('method_availability_weekly_shipping_regular_weekday_time').value = '00:00';
		} catch (e) {
			document.advshipper.eval('method_availability_weekly_shipping_regular_weekday_day').selectedIndex = 0;
			document.advshipper.eval('method_availability_weekly_shipping_regular_weekday_time').value = '00:00';
		}
	} else if (value == <?php echo ADVSHIPPER_AVAILABILITY_WEEKLY_SHIPPING_SCHEDULING_REGULAR_WEEKDAY; ?>) {
		advshipperAvailabilityWeeklyStartDay();
		
		method_availability_weekly_shipping_regular_weekday_day_and_time_header_el.style.display = '';
		method_availability_weekly_shipping_regular_weekday_day_and_time_field_el.style.display = '';
	}
}


var advshipper_method_config_popup = null;

function advshipperCategorySelection(URLStr)
{
	if (submitting_form) {
		return true;
	}
	
	if (advshipper_method_config_popup) {
		if(!advshipper_method_config_popup.closed) advshipper_method_config_popup.close();
	}
	advshipper_method_config_popup = open(URLStr, 'advshipper_method_config_popup', 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=600,height=400,left=150,top=120,screenX=150,screenY=150');
}

function advshipperManufacturerSelection(URLStr)
{
	if (submitting_form) {
		return true;
	}
	
	if (advshipper_method_config_popup) {
		if(!advshipper_method_config_popup.closed) advshipper_method_config_popup.close();
	}
	advshipper_method_config_popup = open(URLStr, 'advshipper_method_config_popup', 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=600,height=400,left=150,top=120,screenX=150,screenY=150');
}

function advshipperProductSelection(URLStr)
{
	if (submitting_form) {
		return true;
	}
	
	if (advshipper_method_config_popup) {
		if (!advshipper_method_config_popup.closed) advshipper_method_config_popup.close();
	}
	advshipper_method_config_popup = open(URLStr, 'advshipper_method_config_popup', 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=600,height=570,left=150,top=120,screenX=150,screenY=150');
}

function advshipperRegionConfig(URLStr)
{
	if (submitting_form) {
		return true;
	}
	
	if (advshipper_method_config_popup) {
		if (!advshipper_method_config_popup.closed) {
			advshipper_method_config_popup.close();
		}
		advshipper_method_config_popup = null;
	}
	
	advshipper_method_config_popup = window.open(URLStr, 'advshipper_method_config_popup', 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=750,height=570,left=150,top=120,screenX=150,screenY=150');
}



// Category Management Functions ///////////////////////////////////////////////////////////////////

/**
 * Add the selected categories to the list of categories
 */
function advshipperAddCategories(category_ids_selected_string)
{
	var _add_categories_return_values = new Array();
	
	var _base_url = '<?php echo addslashes(zen_href_link(FILENAME_ADVANCED_SHIPPER_CATEGORIES_INFO, zen_get_all_get_params(array("action", "method_num", "config_id", "page", "request_uri")))); ?>';
	if (_base_url.indexOf('?') == -1) {
		_base_url += '?';
	} else {
		_base_url += '&';
	}
	var _zc_session_string = '&<?php echo addslashes(zen_session_name() . "=" . zen_session_id());?>';
	var _url = '';
	
	// Get the list of categories selected
	var _categories_selected = new Array();
	_categories_selected = category_ids_selected_string.split('_');
	
	var _num_categories_selected = _categories_selected.length;
	
	// Only look up the info for 50 categories at a time, to limit the length of the request string
	var _categories_selected_batches = new Array();
	if (_num_categories_selected > 50) {
		for (var i = 0, n = _num_categories_selected; i < n; i++) {
			var _current_batch_index = i / 50;
			
			if (_categories_selected_batches[_current_batch_index] == undefined) {
				_categories_selected_batches[_current_batch_index] = new Array();
			}
			
			_categories_selected_batches[_current_batch_index].push(_categories_selected[i]);
		}
	} else {
		_categories_selected_batches[0] = _categories_selected;
	}
	
	// Variable stores information about categories which are already in the list
	var _categories_already_in_list = new Array();
	
	for (var cat_batch_i = 0, num_cat_batches = _categories_selected_batches.length;
			cat_batch_i < num_cat_batches; cat_batch_i++) {
		
		var current_category_ids_selected_string =
			_categories_selected_batches[cat_batch_i].join('_');
		
		_url = _base_url + 'category_ids_string=' + current_category_ids_selected_string +
			_zc_session_string;
		
		// create CBA object (unless exists)
		if (!_cba) _cba = new cbaRequest();
		// query
		_cba.query( _url,
			function()
			{
				_add_categories_return_values = _cba.answer;
				
				if (_add_categories_return_values == '-1') {
					// Unable to add categories
				} else {
					var _categories_info = _add_categories_return_values.split('||');
					
					for (var categories_info_i = 0, num_categories_info = _categories_info.length;
							categories_info_i < num_categories_info; categories_info_i++) {
						var _category_info = _categories_info[categories_info_i].split('|');
						
						// Make sure this category isn't already in the list
						var _category_in_list = false;
						for (category_i = 0, num_categories = categories.length;
								category_i < num_categories; category_i++) {
							if (categories[category_i].category_id ==
									_category_info[0]) {
								// Category already in list
								_categories_already_in_list.push(_category_info[1]);
								
								_category_in_list = true;
								
								break;
							}
						}
						
						if (!_category_in_list) {
							// Add the information for this category to the list of categories
							var new_category_i = categories.length;
							categories[new_category_i] = new Object();
							categories[new_category_i].category_id = _category_info[0];
							categories[new_category_i].name = _category_info[1];
							
							advshipperUpdateCategoryList(categories[new_category_i].category_id,
								categories[new_category_i].name);
						}
					}
					
					var _num_categories_already_in_list = _categories_already_in_list.length;
					
					if (_num_categories_already_in_list > 0) {
						if (_num_categories_already_in_list == 1) {
							if (_num_categories_selected == 1) {
								alert('<?php echo JS_TEXT_CATEGORY_IN_LIST_SELECTED; ?>');
							} else {
								alert('<?php echo JS_TEXT_CATEGORY_IN_LIST_SINGLE; ?>' + '\n\n' +
									_categories_already_in_list);
							}
						} else {
							if (_num_categories_already_in_list == _num_categories_selected) {
								alert('<?php echo JS_TEXT_CATEGORIES_IN_LIST_ALL; ?>');
							} else {
								alert('<?php echo JS_TEXT_CATEGORIES_IN_LIST; ?>' + '\n\n' +
								_categories_already_in_list.join('\n'));
							}
						}
					}
				}
			},
			false );
	}
}


function advshipperUpdateCategoryList(category_id, category_name)
{
	_current_categories_el = document.getElementById('current_categories');
	_current_categories_el.style.display = '';
	
	// Add new category's info and delete button to current categories section
	try {
		new_p_el = document.createElement('<p id="category_name_' + category_id + '">');
	} catch (e) {
		new_p_el = document.createElement('p');
		new_p_el.setAttribute('id', 'category_name_' + category_id);
	}
	_current_categories_el.appendChild(new_p_el);
	
	new_text_node_el = document.createTextNode(category_name + ' ');
	new_p_el.appendChild(new_text_node_el);
	
	try {
		new_category_delete_el = document.createElement('<input name="category_delete_' + category_id + '" id="category_delete_' + category_id + '" type="submit" value="<?php echo addslashes(IMAGE_DELETE); ?>" onClick="javascript:advshipperDeleteCategory(\'' + category_id + '\');return false;" />');
	} catch (e) {
		new_category_delete_el = document.createElement('input');
		new_category_delete_el.setAttribute('id', 'category_delete_' + category_id);
		new_category_delete_el.setAttribute('Name', 'category_delete_' + category_id);
		new_category_delete_el.setAttribute('type', 'submit');
		new_category_delete_el.setAttribute('value', '<?php echo addslashes(IMAGE_DELETE); ?>');
		new_category_delete_el.setAttribute('onClick', 'javascript:advshipperDeleteCategory(\'' + category_id + '\');return false;');
	}
	
	new_p_el.appendChild(new_category_delete_el);
	
	advshipperUpdateCategorySelection();
}


function advshipperDeleteCategory(category_selected)
{
	if (submitting_form) {
		return true;
	}

	// Remove the selected category from the list of categories 
	_category_el = document.getElementById('category_name_' + category_selected);
	
	if (_category_el != undefined) {
		_category_el.parentNode.removeChild(_category_el);
	}
	
	// Update the list of categories
	num_categories = categories.length;
	
	for (i = 0; i < num_categories; i++) {
		if (categories[i].category_id == category_selected) {
			categories.splice(i, 1);
			break;
		}
	}
	
	// Was this the last category in the list? If so, hide the list's container
	if (categories.length == 0) {
		_current_categories_el = document.getElementById('current_categories');
		_current_categories_el.style.display = 'none';
	}
	
	advshipperUpdateCategorySelection();
}


function advshipperUpdateCategorySelection()
{
	var num_categories = categories.length;
	
	_categories_el = document.getElementById('categories');
	_categories_el.value = '';
	
	for (category_i = 0; category_i < num_categories; category_i++) {
		if (_categories_el.value != '') {
			_categories_el.value += '||';
		}
		_categories_el.value += categories[category_i].category_id + '|' + categories[category_i].name;
	}
}



// Manufacturer Management Functions ///////////////////////////////////////////////////////////////

function advshipperAddManufacturers(manufacturer_ids_selected_string)
{
	var _add_manufacturers_return_values = new Array();
	
	var _base_url = '<?php echo addslashes(zen_href_link(FILENAME_ADVANCED_SHIPPER_MANUFACTURERS_INFO, zen_get_all_get_params(array("action", "method_num", "config_id", "page", "request_uri")))); ?>';
	if (_base_url.indexOf('?') == -1) {
		_base_url += '?';
	} else {
		_base_url += '&';
	}
	var _zc_session_string = '&<?php echo addslashes(zen_session_name() . "=" . zen_session_id());?>';
	var _url = '';
	
	// Get the list of manufacturers selected
	var _manufacturers_selected = new Array();
	_manufacturers_selected = manufacturer_ids_selected_string.split('_');
	
	var _num_manufacturers_selected = _manufacturers_selected.length;
	
	// Only look up the info for 50 manufacturers at a time, to limit the length of the request
	// string
	var _manufacturers_selected_batches = new Array();
	if (_num_manufacturers_selected > 50) {
		for (var i = 0, n = _num_manufacturers_selected; i < n; i++) {
			var _current_batch_index = i / 50;
			
			if (_manufacturers_selected_batches[_current_batch_index] == undefined) {
				_manufacturers_selected_batches[_current_batch_index] = new Array();
			}
			
			_manufacturers_selected_batches[_current_batch_index].push(_manufacturers_selected[i]);
		}
	} else {
		_manufacturers_selected_batches[0] = _manufacturers_selected;
	}
	
	// Variable stores information about manufacturers which are already in the list
	var _manufacturers_already_in_list = new Array();
	
	for (var cat_batch_i = 0, num_cat_batches = _manufacturers_selected_batches.length;
			cat_batch_i < num_cat_batches; cat_batch_i++) {
		
		var current_manufacturer_ids_selected_string =
			_manufacturers_selected_batches[cat_batch_i].join('_');
		
		_url = _base_url + 'manufacturer_ids_string=' + current_manufacturer_ids_selected_string +
			_zc_session_string;
		
		// create CBA object (unless exists)
		if (!_cba) _cba = new cbaRequest();
		// query
		_cba.query( _url,
			function()
			{
				_add_manufacturers_return_values = _cba.answer;
				
				if (_add_manufacturers_return_values == '-1') {
					// Unable to add manufacturers
				} else {
					var _manufacturers_info = _add_manufacturers_return_values.split('||');
					
					for (var manufacturers_info_i = 0, num_manufacturers_info =
							_manufacturers_info.length;
							manufacturers_info_i < num_manufacturers_info; manufacturers_info_i++) {
						var _manufacturer_info =
							_manufacturers_info[manufacturers_info_i].split('|');
						
						// Make sure this manufacturer isn't already in the list
						var _manufacturer_in_list = false;
						for (manufacturer_i = 0, num_manufacturers = manufacturers.length;
								manufacturer_i < num_manufacturers; manufacturer_i++) {
							if (manufacturers[manufacturer_i].manufacturer_id ==
									_manufacturer_info[0]) {
								// Manufacturer already in list
								_manufacturers_already_in_list.push(_manufacturer_info[1]);
								
								_manufacturer_in_list = true;
								
								break;
							}
						}
						
						if (!_manufacturer_in_list) {
							// Add the information for this manufacturer to the list of
							// manufacturers
							var new_manufacturer_i = manufacturers.length;
							manufacturers[new_manufacturer_i] = new Object();
							manufacturers[new_manufacturer_i].manufacturer_id =
								_manufacturer_info[0];
							manufacturers[new_manufacturer_i].name = _manufacturer_info[1];
							
							advshipperUpdateManufacturerList(
								manufacturers[new_manufacturer_i].manufacturer_id,
								manufacturers[new_manufacturer_i].name);
						}
					}
					
					var _num_manufacturers_already_in_list = _manufacturers_already_in_list.length;
					
					if (_num_manufacturers_already_in_list > 0) {
						if (_num_manufacturers_already_in_list == 1) {
							if (_num_manufacturers_selected == 1) {
								alert('<?php echo JS_TEXT_MANUFACTURER_IN_LIST_SELECTED; ?>');
							} else {
								alert('<?php echo JS_TEXT_MANUFACTURER_IN_LIST_SINGLE; ?>' +
									'\n\n' + _manufacturers_already_in_list);
							}
						} else {
							if (_num_manufacturers_already_in_list == _num_manufacturers_selected) {
								alert('<?php echo JS_TEXT_MANUFACTURERS_IN_LIST_ALL; ?>');
							} else {
								alert('<?php echo JS_TEXT_MANUFACTURERS_IN_LIST; ?>' + '\n\n' +
								_manufacturers_already_in_list.join('\n'));
							}
						}
					}
				}
			},
			false );
	}
}


function advshipperUpdateManufacturerList(manufacturer_id, manufacturer_name)
{
	_current_manufacturers_el = document.getElementById('current_manufacturers');
	_current_manufacturers_el.style.display = '';
	
	// Add new manufacturer's info and delete button to current manufacturers section
	try {
		new_p_el = document.createElement('<p id="manufacturer_name_' + manufacturer_id + '">');
	} catch (e) {
		new_p_el = document.createElement('p');
		new_p_el.setAttribute('id', 'manufacturer_name_' + manufacturer_id);
	}
	_current_manufacturers_el.appendChild(new_p_el);
	
	new_text_node_el = document.createTextNode(manufacturer_name + ' ');
	new_p_el.appendChild(new_text_node_el);
	
	try {
		new_manufacturer_delete_el = document.createElement('<input name="manufacturer_delete_' + manufacturer_id + '" id="manufacturer_delete_' + manufacturer_id + '" type="submit" value="<?php echo addslashes(IMAGE_DELETE); ?>" onClick="javascript:advshipperDeletemanufacturer(\'' + manufacturer_id + '\');return false;" />');
	} catch (e) {
		new_manufacturer_delete_el = document.createElement('input');
		new_manufacturer_delete_el.setAttribute('id', 'manufacturer_delete_' + manufacturer_id);
		new_manufacturer_delete_el.setAttribute('Name', 'manufacturer_delete_' + manufacturer_id);
		new_manufacturer_delete_el.setAttribute('type', 'submit');
		new_manufacturer_delete_el.setAttribute('value', '<?php echo addslashes(IMAGE_DELETE); ?>');
		new_manufacturer_delete_el.setAttribute('onClick', 'javascript:advshipperDeleteManufacturer(\'' + manufacturer_id + '\');return false;');
	}
	
	new_p_el.appendChild(new_manufacturer_delete_el);
	
	advshipperUpdateManufacturerSelection();
}


function advshipperDeleteManufacturer(manufacturer_selected)
{
	if (submitting_form) {
		return true;
	}

	// Remove the selected manufacturer from the list of manufacturers 
	_manufacturer_el = document.getElementById('manufacturer_name_' + manufacturer_selected);
	
	if (_manufacturer_el != undefined) {
		_manufacturer_el.parentNode.removeChild(_manufacturer_el);
	}
	
	// Update the list of manufacturers
	num_manufacturers = manufacturers.length;
	
	for (i = 0; i < num_manufacturers; i++) {
		if (manufacturers[i].manufacturer_id == manufacturer_selected) {
			manufacturers.splice(i, 1);
			break;
		}
	}
	
	// Was this the last manufacturer in the list? If so, hide the list's container
	if (manufacturers.length == 0) {
		_current_manufacturers_el = document.getElementById('current_manufacturers');
		_current_manufacturers_el.style.display = 'none';
	}
	
	advshipperUpdateManufacturerSelection();
}


function advshipperUpdateManufacturerSelection()
{
	var num_manufacturers = manufacturers.length;
	
	_manufacturers_el = document.getElementById('manufacturers');
	_manufacturers_el.value = '';
	
	for (manufacturer_i = 0; manufacturer_i < num_manufacturers; manufacturer_i++) {
		if (_manufacturers_el.value != '') {
			_manufacturers_el.value += '||';
		}
		_manufacturers_el.value += manufacturers[manufacturer_i].manufacturer_id + '|' + manufacturers[manufacturer_i].name;
	}
}



// Product Management Functions ////////////////////////////////////////////////////////////////////

function advshipperAddProduct(product_id_selected)
{
	// Add the selected product to the list of products
	var _add_product_return_values = new Array();
	
	var _url = '<?php echo addslashes(zen_href_link(FILENAME_ADVANCED_SHIPPER_PRODUCT_INFO, zen_get_all_get_params(array("action", "method_num", "config_id", "page", "request_uri")))); ?>';
	if (_url.indexOf('?') == -1) {
		_url += '?';
	} else {
		_url += '&';
	}
	_url += 'product_id_string=' + escape(product_id_selected);
	_url += '&<?php echo addslashes(zen_session_name() . "=" . zen_session_id());?>';
	
	// create CBA object (unless exists)
    if (!_cba) _cba = new cbaRequest();
    // query
    _cba.query( _url,
		function()
		{
			_add_product_return_values = _cba.answer;
			
			if (_add_product_return_values == '-1') {
				// Unable to add product
			} else {
				var add_product_return_values = _add_product_return_values.split('|');
				
				// Make sure this product isn't already in the list
				var num_products = products.length;
				
				for (product_i = 0; product_i < num_products; product_i++) {
					if (products[product_i].product_id == add_product_return_values[0]) {
						// Product already in list
						current_product_id = products[product_i].product_id;
						if (current_product_id.indexOf('<?php echo addslashes(ADVSHIPPER_PRODUCT_OPTIONS_SEPARATOR); ?>') != -1) {
							alert('<?php echo JS_TEXT_PRODUCT_AND_ATTRIBUTES_IN_LIST; ?>');
						} else {
							alert('<?php echo JS_TEXT_PRODUCT_IN_LIST; ?>');
						}
						return;
					}
				}
				
				// Add the information for this product to the list of products
				var new_product_i = products.length;
				products[new_product_i] = new Object();
				products[new_product_i].product_id = add_product_return_values[0];
				products[new_product_i].name = add_product_return_values[1];
				
				advshipperUpdateProductList(products[new_product_i].product_id, products[new_product_i].name);
			}
		},
		false );
}


function advshipperUpdateProductList(product_id, product_name)
{
	_current_products_el = document.getElementById('current_products');
	_current_products_el.style.display = '';
	
	// Add new product's info and delete button to current products section
	try {
		new_p_el = document.createElement('<p id="product_name_' + product_id + '">');
	} catch (e) {
		new_p_el = document.createElement('p');
		new_p_el.setAttribute('id', 'product_name_' + product_id);
	}
	_current_products_el.appendChild(new_p_el);
	
	new_text_node_el = document.createTextNode(product_name + ' ');
	new_p_el.appendChild(new_text_node_el);
	
	try {
		new_product_delete_el = document.createElement('<input name="product_delete_' + product_id + '" id="product_delete_' + product_id + '" type="submit" value="<?php echo addslashes(IMAGE_DELETE); ?>" onClick="javascript:advshipperDeleteProduct(\'' + product_id + '\');return false;" />');
	} catch (e) {
		new_product_delete_el = document.createElement('input');
		new_product_delete_el.setAttribute('id', 'product_delete_' + product_id);
		new_product_delete_el.setAttribute('Name', 'product_delete_' + product_id);
		new_product_delete_el.setAttribute('type', 'submit');
		new_product_delete_el.setAttribute('value', '<?php echo addslashes(IMAGE_DELETE); ?>');
		new_product_delete_el.setAttribute('onClick', 'javascript:advshipperDeleteProduct(\'' + product_id + '\');return false;');
	}
	
	new_p_el.appendChild(new_product_delete_el);
	
	advshipperUpdateProductSelection();
}


function advshipperDeleteProduct(product_selected)
{
	if (submitting_form) {
		return true;
	}
	
	// Remove the selected product from the list of products 
	_product_el = document.getElementById('product_name_' + product_selected);
	
	if (_product_el != undefined) {
		_product_el.parentNode.removeChild(_product_el);
	}
	
	// Update the list of products
	num_products = products.length;
	
	for (i = 0; i < num_products; i++) {
		if (products[i].product_id == product_selected) {
			products.splice(i, 1);
			break;
		}
	}
	
	// Was this the last product in the list? If so, hide the list's container
	if (products.length == 0) {
		_current_products_el = document.getElementById('current_products');
		_current_products_el.style.display = 'none';
	}
	
	advshipperUpdateProductSelection();
}


function advshipperUpdateProductSelection()
{
	var num_products = products.length;
	
	_products_el = document.getElementById('products');
	_products_el.value = '';
	
	for (product_i = 0; product_i < num_products; product_i++) {
		if (_products_el.value != '') {
			_products_el.value += '||';
		}
		_products_el.value += products[product_i].product_id + '|' + products[product_i].name;
	}
}



// Region Management Functions /////////////////////////////////////////////////////////////////////

function advshipperInsertRegion(region_num, admin_titles_string, titles_string, definition_method, countries_postcodes, countries_zones_string, countries_states_string, countries_cities_string, distance, tax_class, rates_include_tax, rate_limits_inc, total_up_price_inc_tax, table_of_rates, max_weight_per_package, packaging_weights, surcharge, surcharge_titles_string, ups_calc_string, usps_calc_string)
{
	num_regions = regions.length;
	
	admin_titles = new Array();
	if (admin_titles_string != null) {
		var temp_titles = admin_titles_string.split('||');
		var num_titles = temp_titles.length;
		for (i = 0; i < num_titles; i++) {
			admin_titles[i] = temp_titles[i].split('|');
			
			if (admin_titles[i][1] == 'null') {
				admin_titles[i][1] = '';
			}
		}
	}
	
	titles = new Array();
	if (titles_string != null) {
		var temp_titles = titles_string.split('||');
		var num_titles = temp_titles.length;
		for (i = 0; i < num_titles; i++) {
			titles[i] = temp_titles[i].split('|');
			
			if (titles[i][1] == 'null') {
				titles[i][1] = '';
			}
		}
	}
	
	surcharge_titles = new Array();
	if (surcharge_titles_string != null) {
		var temp_titles = surcharge_titles_string.split('||');
		var num_titles = temp_titles.length;
		for (i = 0; i < num_titles; i++) {
			surcharge_titles[i] = temp_titles[i].split('|');
			
			if (surcharge_titles[i][1] == 'null') {
				surcharge_titles[i][1] = '';
			}
		}
	}
	
	countries_zones = new Array();
	if (countries_zones_string != null) {
		var temp_zones = countries_zones_string.split('||');
		var num_zones = temp_zones.length;
		for (i = 0; i < num_zones; i++) {
			country_zone_info = temp_zones[i].split('|');
			countries_zones[i] = new Object();
			countries_zones[i].zone_id = country_zone_info[0];
			countries_zones[i].name = country_zone_info[1];
		}
	}
	
	countries_states = new Array();
	if (countries_states_string != null) {
		var temp_states = countries_states_string.split('||');
		var num_states = temp_states.length;
		for (i = 0; i < num_states; i++) {
			country_state_info = temp_states[i].split('|');
			countries_states[i] = new Object();
			countries_states[i].locality_id = country_state_info[0];
			countries_states[i].name = country_state_info[1];
		}
	}
	
	countries_cities = new Array();
	if (countries_cities_string != null) {
		var temp_cities = countries_cities_string.split('||');
		var num_cities = temp_cities.length;
		for (i = 0; i < num_cities; i++) {
			country_city_info = temp_cities[i].split('|');
			countries_cities[i] = new Object();
			countries_cities[i].locality_id = country_city_info[0];
			countries_cities[i].name = country_city_info[1];
		}
	}
	
	new_region = new Object();
	new_region.admin_titles = admin_titles;
	new_region.titles = titles;
	new_region.definition_method = definition_method;
	new_region.countries_postcodes = countries_postcodes;
	new_region.countries_zones = countries_zones;
	new_region.countries_states = countries_states;
	new_region.countries_cities = countries_cities;
	new_region.distance = distance;
	new_region.tax_class = tax_class;
	new_region.rates_include_tax = rates_include_tax;
	new_region.rate_limits_inc = rate_limits_inc;
	new_region.total_up_price_inc_tax = total_up_price_inc_tax;
	new_region.table_of_rates = table_of_rates;
	new_region.max_weight_per_package = max_weight_per_package;
	new_region.packaging_weights = packaging_weights;
	new_region.surcharge = surcharge;
	new_region.surcharge_titles = surcharge_titles;
	new_region.ups_calc_string = ups_calc_string;
	new_region.usps_calc_string = usps_calc_string;
	
	if (region_num == num_regions) {
		regions[num_regions] = new_region;
	} else {
		// Insert the region at an appropriate place
		regions.splice(region_num, 0, new_region);
	}
}

function advshipperUpdateRegion(region_num, admin_titles_string, titles_string, definition_method, countries_postcodes, countries_zones_string, countries_states_string, countries_cities_string, distance, tax_class, rates_include_tax, rate_limits_inc, total_up_price_inc_tax, table_of_rates, max_weight_per_package, packaging_weights, surcharge, surcharge_titles_string, ups_calc_string, usps_calc_string)
{
	num_regions = regions.length;
	
	admin_titles = new Array();
	if (admin_titles_string != null) {
		var temp_titles = admin_titles_string.split('||');
		var num_titles = temp_titles.length;
		for (i = 0; i < num_titles; i++) {
			admin_titles[i] = temp_titles[i].split('|');
			
			if (admin_titles[i][1] == 'null') {
				admin_titles[i][1] = '';
			}
		}
	}
	
	titles = new Array();
	if (titles_string != null) {
		var temp_titles = titles_string.split('||');
		var num_titles = temp_titles.length;
		for (i = 0; i < num_titles; i++) {
			titles[i] = temp_titles[i].split('|');
			
			if (titles[i][1] == 'null') {
				titles[i][1] = '';
			}
		}
	}
	
	surcharge_titles = new Array();
	if (surcharge_titles_string != null) {
		var temp_titles = surcharge_titles_string.split('||');
		var num_titles = temp_titles.length;
		for (i = 0; i < num_titles; i++) {
			surcharge_titles[i] = temp_titles[i].split('|');
			
			if (surcharge_titles[i][1] == 'null') {
				surcharge_titles[i][1] = '';
			}
		}
	}
	
	countries_zones = new Array();
	if (countries_zones_string != null) {
		var temp_zones = countries_zones_string.split('||');
		var num_zones = temp_zones.length;
		for (i = 0; i < num_zones; i++) {
			country_zone_info = temp_zones[i].split('|');
			countries_zones[i] = new Object();
			countries_zones[i].zone_id = country_zone_info[0];
			countries_zones[i].name = country_zone_info[1];
		}
	}
	
	countries_states = new Array();
	if (countries_states_string != null) {
		var temp_states = countries_states_string.split('||');
		var num_states = temp_states.length;
		for (i = 0; i < num_states; i++) {
			country_state_info = temp_states[i].split('|');
			countries_states[i] = new Object();
			countries_states[i].locality_id = country_state_info[0];
			countries_states[i].name = country_state_info[1];
		}
	}
	
	countries_cities = new Array();
	if (countries_cities_string != null) {
		var temp_cities = countries_cities_string.split('||');
		var num_cities = temp_cities.length;
		for (i = 0; i < num_cities; i++) {
			country_city_info = temp_cities[i].split('|');
			countries_cities[i] = new Object();
			countries_cities[i].locality_id = country_city_info[0];
			countries_cities[i].name = country_city_info[1];
		}
	}
	
	updated_region = new Object();
	updated_region.admin_titles = admin_titles;
	updated_region.titles = titles;
	updated_region.definition_method = definition_method;
	updated_region.countries_postcodes = countries_postcodes;
	updated_region.countries_zones = countries_zones;
	updated_region.countries_states = countries_states;
	updated_region.countries_cities = countries_cities;
	updated_region.distance = distance;
	updated_region.tax_class = tax_class;
	updated_region.rates_include_tax = rates_include_tax;
	updated_region.total_up_price_inc_tax = total_up_price_inc_tax;
	updated_region.rate_limits_inc = rate_limits_inc;
	updated_region.table_of_rates = table_of_rates;
	updated_region.max_weight_per_package = max_weight_per_package;
	updated_region.packaging_weights = packaging_weights;
	updated_region.surcharge = surcharge;
	updated_region.surcharge_titles = surcharge_titles;
	updated_region.ups_calc_string = ups_calc_string;
	updated_region.usps_calc_string = usps_calc_string;
	
	regions[region_num] = updated_region;
}

/**
 * Prevent the accidential deletion of a region!
 */
function advshipperConfirmDeletion(region_i)
{
	if (submitting_form) {
		return true;
	}
	
	var perform_deletion = confirm('<?php echo addslashes(TEXT_JS_DELETE_CONFIRMATION); ?>');
	if (perform_deletion) {
		advshipperDeleteRegion(region_i);
	}
	
	return false;
}

function advshipperDeleteRegion(region_num)
{
	regions.splice(region_num, 1);
	
	advshipperRebuildRegionsPanel();
}

function advshipperMoveRegionUp(region_i)
{
	if (submitting_form) {
		return true;
	}
	
	var region_to_be_moved_copy = regions[region_i];
	
	regions.splice((region_i - 1), 0, region_to_be_moved_copy);
	
	regions.splice((region_i + 1), 1);
	
	advshipperRebuildRegionsPanel();
}

function advshipperMoveRegionDown(region_i)
{
	if (submitting_form) {
		return true;
	}
	
	var region_to_be_moved_copy = regions[region_i];
	
	regions.splice((region_i + 2), 0, region_to_be_moved_copy);
	
	regions.splice(region_i, 1);
	
	advshipperRebuildRegionsPanel();
}


function advshipperRebuildRegionsPanel()
{
	advshipperRemoveRegionsPanel();
	
	advshipperBuildRegionsPanel();
}


function utf8(wide)
{
	var c,s;
	var enc = "";
	var i = 0;
	
	while (i < wide.length) {
		c = wide.charCodeAt(i++);
		
		if (c >= 0xDC00 && c < 0xE000) {
			continue;
		}
		if (c >= 0xD800 && c < 0xDC00) {
			if ( i >= wide.length) {
				continue;
			}
			s = wide.charCodeAt(i++);
			
			if (s < 0xDC00 || c >= 0xDE00) {
				continue;
			}
			c = ((c - 0xD800) << 10) + (s - 0xDC00) + 0x10000;
		}
	
		if (c < 0x80) {
			enc += String.fromCharCode(c);
		} else if (c < 0x800) {
			enc += String.fromCharCode(0xC0 + (c >> 6), 0x80 + (c & 0x3F));
		} else if (c < 0x10000) {
			enc += String.fromCharCode(0xE0 + (c >> 12), 0x80 + (c >> 6 & 0x3F), 0x80 + (c & 0x3F));
		} else {
			enc += String.fromCharCode(0xF0 + (c >> 18), 0x80 + (c >> 12 & 0x3F), 0x80 +
				(c >> 6 & 0x3F), 0x80 + (c & 0x3F));
		}
	}
	
	return enc;
}

var hexchars="0123456789ABCDEF";
function toHex(n)
{
	return hexchars.charAt(n >> 4) + hexchars.charAt(n & 0xF);
}

var okURIchars="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789_-";
function encodeURIComponentNew(s)
{
	var text = utf8(s);
	var c;
	var enc = "";
	
	for (var i = 0; i < text.length; i++) {
		if (okURIchars.indexOf(text.charAt(i)) == -1) {
			enc += "%" + toHex(text.charCodeAt(i));
		} else {
			enc += text.charAt(i);
		}
	}
	
	return enc;
}

function encodeUTF8String(s)
{
	if (typeof encodeURIComponent == "function") {
		encoded_string = encodeURIComponent(s);
	} else {
		encoded_string = encodeURIComponentNew(s);
	}
	
	return encoded_string;
}



function advshipperRemoveRegionsPanel()
{
	_regions_panel_el = document.getElementById('regions_panel');
	
	var regions_panel_children = _regions_panel_el.childNodes;
	
	var num_regions_panel_children = regions_panel_children.length;
	
	var children_to_remove = new Array();
	
	for (i = 0; i < num_regions_panel_children; i++) {
		if (regions_panel_children[i] != undefined && regions_panel_children[i].nodeType == Node.ELEMENT_NODE) {
			var id_value = '';
			if (regions_panel_children[i].hasAttribute) {
				if (regions_panel_children[i].hasAttribute('id')) {
					id_value = regions_panel_children[i].getAttribute('id');
				}
			} else {
				id_value = regions_panel_children[i].getAttribute('id');
			}
			if (id_value == 'no_regions_defined') {
				children_to_remove.push(regions_panel_children[i]);
			}
			if (id_value.substr(0, 14) == 'region_config_') {
				children_to_remove.push(regions_panel_children[i]);
			}
		}
	}
	
	var num_children_to_remove = children_to_remove.length;
	
	for (i = 0; i < num_children_to_remove; i++) {
		_regions_panel_el.removeChild(children_to_remove[i]);
	}
}

function advshipperBuildRegionsPanel()
{
	_regions_panel_el = document.getElementById('regions_panel');
	
	_region_add_holder_el = document.getElementById('region_add_holder');
	
	// Reset information currently stored about regions
	_regions_info_el = document.getElementById('regions_info');
	_regions_info_el.value = '';
	
	num_regions = regions.length;
	
	if (num_regions == 0) {
		try {
			new_p_el = document.createElement('<p id="no_regions_defined">');
		} catch (e) {
			new_p_el = document.createElement('p');
			new_p_el.setAttribute('id', 'no_regions_defined');
		}
		new_text_node_el = document.createTextNode('<?php echo addslashes(TEXT_NO_REGIONS_DEFINED); ?>');
		new_p_el.appendChild(new_text_node_el);
		
		try {
			_regions_panel_el.insertBefore(new_p_el, _region_add_holder_el);
		} catch (e) {
			_regions_panel_el.appendChild(new_p_el);
		}
	} else {
		for (region_i = 0; region_i < num_regions; region_i++) {
			try {
				new_region_config_el = document.createElement('<fieldset id="region_config_' + region_i + '" class="<?php echo "AdvancedShipperMethod" . ($method_num % 2 == 0 ? "Even" : "Odd") . "RegionsConfiguration"; ?>' + ((region_i + 1) % 2 == 0 ? 'Even' : 'Odd') + '">');
			} catch (e) {
				new_region_config_el = document.createElement('fieldset');
				new_region_config_el.setAttribute('id', 'region_config_' + region_i);
				new_region_config_el.setAttribute('class', '<?php echo "AdvancedShipperMethod" . ($method_num % 2 == 0 ? "Even" : "Odd") . "RegionsConfiguration"; ?>' + ((region_i + 1) % 2 == 0 ? 'Even' : 'Odd'));
			}
			
			new_title_el = document.createElement('legend');
			
			// Get the text for the region's title for the current language
			region_title = '';
			num_titles = regions[region_i].admin_titles.length;
			for (title_i = 0; title_i < num_titles; title_i++) {
				if (regions[region_i].admin_titles[title_i][0] == session_language_id) {
					if (regions[region_i].admin_titles[title_i][1] != undefined) {
						region_title = regions[region_i].admin_titles[title_i][1];
					}
					break;
				}
			}
			if (region_title.length > 0) {
				region_title = ' - ' + entity('&ldquo;') + region_title + entity('&rdquo;');
			}
			
			new_text_node_el = document.createTextNode('<?php echo addslashes(TEXT_REGION); ?> ' + (region_i + 1) + region_title);
			new_title_el.appendChild(new_text_node_el);
			
			new_region_config_el.appendChild(new_title_el);
			
			try {
				new_table_el = document.createElement('<table width="100%">');
			} catch (e) {
				new_table_el = document.createElement('table');
				new_table_el.setAttribute('width', '100%');
			}
			try {
				new_tbody_el = document.createElement('<tbody width="100%">');
			} catch (e) {
				new_tbody_el = document.createElement('tbody');
				new_tbody_el.setAttribute('width', '100%');
			}
			try {
				new_tr_el = document.createElement('<tr>');
			} catch (e) {
				new_tr_el = document.createElement('tr');
			}
			try {
				new_td_left_el = document.createElement('<td class="AdvancedShipperConfigDesc">');
			} catch (e) {
				new_td_left_el = document.createElement('td');
				new_td_left_el.setAttribute('class', 'AdvancedShipperConfigDesc');
			}
			try {
				new_td_button_panel_el = document.createElement('<td class="AdvancedShipperConfigButtonPanel">');
			} catch (e) {
				new_td_button_panel_el = document.createElement('td');
				new_td_button_panel_el.setAttribute('class', 'AdvancedShipperConfigButtonPanel');
			}
			
			// Create the definition method overview
			try {
				definition_method_el = document.createElement('<fieldset class="AdvancedShipperRegionSummaryOdd">');
			} catch (e) {
				definition_method_el = document.createElement('fieldset');
				definition_method_el.setAttribute('class', 'AdvancedShipperRegionSummaryOdd');
			}
			
			new_title_el = document.createElement('legend');
			
			
			if (regions[region_i].definition_method == <?php echo ADVSHIPPER_DEFINITION_METHOD_ADDRESS_MATCHING; ?>) {
				new_text_node_el = document.createTextNode('<?php echo addslashes(TEXT_DEFINITION_METHOD . ' - ' . TEXT_ADDRESS_MATCHING); ?>');
				new_title_el.appendChild(new_text_node_el);
				
				definition_method_el.appendChild(new_title_el);
				
				try {
					definition_method_table_el = document.createElement('<table width="100%">');
				} catch (e) {
					definition_method_table_el = document.createElement('table');
					definition_method_table_el.setAttribute('width', '100%');
				}
				try {
					definition_method_tbody_el = document.createElement('<tbody width="100%">');
				} catch (e) {
					definition_method_tbody_el = document.createElement('tbody');
					definition_method_tbody_el.setAttribute('width', '100%');
				}
				
				definition_method_el.appendChild(definition_method_table_el);
				definition_method_table_el.appendChild(definition_method_tbody_el);
				
				
				// Add any postcode information
				if (regions[region_i].countries_postcodes.length > 0) {
					try {
						definition_method_tr_el = document.createElement('<tr>');
					} catch (e) {
						definition_method_tr_el = document.createElement('tr');
					}
					try {
						definition_method_td_left_el = document.createElement('<td class="AdvancedShipperConfigLabel">');
					} catch (e) {
						definition_method_td_left_el = document.createElement('td');
						definition_method_td_left_el.setAttribute('class', 'AdvancedShipperConfigLabel');
					}
					try {
						definition_method_td_right_el = document.createElement('<td class="AdvancedShipperConfigField">');
					} catch (e) {
						definition_method_td_right_el = document.createElement('td');
						definition_method_td_right_el.setAttribute('class', 'AdvancedShipperConfigField');
					}
					try {
						label_el = document.createElement('<label>');
					} catch (e) {
						label_el = document.createElement('label');
					}
					
					new_text_node_el = document.createTextNode('<?php echo addslashes(TEXT_LABEL_COUNTRIES_POSTCODES); ?>');
					label_el.appendChild(new_text_node_el);
					
					definition_method_td_left_el.appendChild(label_el);
					
					new_text_node_el = document.createTextNode(regions[region_i].countries_postcodes.replace(/,/g, ', '));
					
					definition_method_td_right_el.appendChild(new_text_node_el);
					
					definition_method_tbody_el.appendChild(definition_method_tr_el);
					definition_method_tr_el.appendChild(definition_method_td_left_el);
					definition_method_tr_el.appendChild(definition_method_td_right_el);
				}
				
				// Add any countries/zones information
				if (regions[region_i].countries_zones != null && regions[region_i].countries_zones.length > 0) {
					try {
						definition_method_tr_el = document.createElement('<tr>');
					} catch (e) {
						definition_method_tr_el = document.createElement('tr');
					}
					try {
						definition_method_td_left_el = document.createElement('<td class="AdvancedShipperConfigLabel">');
					} catch (e) {
						definition_method_td_left_el = document.createElement('td');
						definition_method_td_left_el.setAttribute('class', 'AdvancedShipperConfigLabel');
					}
					try {
						definition_method_td_right_el = document.createElement('<td class="AdvancedShipperConfigField">');
					} catch (e) {
						definition_method_td_right_el = document.createElement('td');
						definition_method_td_right_el.setAttribute('class', 'AdvancedShipperConfigField');
					}
					try {
						label_el = document.createElement('<label>');
					} catch (e) {
						label_el = document.createElement('label');
					}
					
					new_text_node_el = document.createTextNode('<?php echo addslashes(TEXT_LABEL_COUNTRIES_ZONES); ?>');
					label_el.appendChild(new_text_node_el);
					
					definition_method_td_left_el.appendChild(label_el);
					
					var num_countries_zones = regions[region_i].countries_zones.length;
					for (zone_i = 0; zone_i < num_countries_zones; zone_i++) {
						if (zone_i > 0) {
							try {
								br_el = document.createElement('<br>');
							} catch (e) {
								br_el = document.createElement('br');
							}
							definition_method_td_right_el.appendChild(br_el);
						}
						
						new_text_node_el = document.createTextNode(regions[region_i].countries_zones[zone_i].name);
						
						definition_method_td_right_el.appendChild(new_text_node_el);
					}
					
					definition_method_tbody_el.appendChild(definition_method_tr_el);
					definition_method_tr_el.appendChild(definition_method_td_left_el);
					definition_method_tr_el.appendChild(definition_method_td_right_el);
				}
				
				// Add any countries/states information
				if (regions[region_i].countries_states != null && regions[region_i].countries_states.length > 0) {
					try {
						definition_method_tr_el = document.createElement('<tr>');
					} catch (e) {
						definition_method_tr_el = document.createElement('tr');
					}
					try {
						definition_method_td_left_el = document.createElement('<td class="AdvancedShipperConfigLabel">');
					} catch (e) {
						definition_method_td_left_el = document.createElement('td');
						definition_method_td_left_el.setAttribute('class', 'AdvancedShipperConfigLabel');
					}
					try {
						definition_method_td_right_el = document.createElement('<td class="AdvancedShipperConfigField">');
					} catch (e) {
						definition_method_td_right_el = document.createElement('td');
						definition_method_td_right_el.setAttribute('class', 'AdvancedShipperConfigField');
					}
					try {
						label_el = document.createElement('<label>');
					} catch (e) {
						label_el = document.createElement('label');
					}
					
					new_text_node_el = document.createTextNode('<?php echo addslashes(TEXT_LABEL_COUNTRIES_STATES); ?>');
					label_el.appendChild(new_text_node_el);
					
					definition_method_td_left_el.appendChild(label_el);
					
					var num_countries_states = regions[region_i].countries_states.length;
					for (state_i = 0; state_i < num_countries_states; state_i++) {
						if (state_i > 0) {
							try {
								br_el = document.createElement('<br>');
							} catch (e) {
								br_el = document.createElement('br');
							}
							definition_method_td_right_el.appendChild(br_el);
						}
						
						new_text_node_el = document.createTextNode(regions[region_i].countries_states[state_i].name);
						
						definition_method_td_right_el.appendChild(new_text_node_el);
					}
					
					definition_method_tbody_el.appendChild(definition_method_tr_el);
					definition_method_tr_el.appendChild(definition_method_td_left_el);
					definition_method_tr_el.appendChild(definition_method_td_right_el);
				}
				
				// Add any countries/cities information
				if (regions[region_i].countries_cities != null && regions[region_i].countries_cities.length > 0) {
					try {
						definition_method_tr_el = document.createElement('<tr>');
					} catch (e) {
						definition_method_tr_el = document.createElement('tr');
					}
					try {
						definition_method_td_left_el = document.createElement('<td class="AdvancedShipperConfigLabel">');
					} catch (e) {
						definition_method_td_left_el = document.createElement('td');
						definition_method_td_left_el.setAttribute('class', 'AdvancedShipperConfigLabel');
					}
					try {
						definition_method_td_right_el = document.createElement('<td class="AdvancedShipperConfigField">');
					} catch (e) {
						definition_method_td_right_el = document.createElement('td');
						definition_method_td_right_el.setAttribute('class', 'AdvancedShipperConfigField');
					}
					try {
						label_el = document.createElement('<label>');
					} catch (e) {
						label_el = document.createElement('label');
					}
					
					new_text_node_el = document.createTextNode('<?php echo addslashes(TEXT_LABEL_COUNTRIES_CITIES); ?>');
					label_el.appendChild(new_text_node_el);
					
					definition_method_td_left_el.appendChild(label_el);
					
					var num_countries_cities = regions[region_i].countries_cities.length;
					for (city_i = 0; city_i < num_countries_cities; city_i++) {
						if (city_i > 0) {
							try {
								br_el = document.createElement('<br>');
							} catch (e) {
								br_el = document.createElement('br');
							}
							definition_method_td_right_el.appendChild(br_el);
						}
						
						new_text_node_el = document.createTextNode(regions[region_i].countries_cities[city_i].name);
						
						definition_method_td_right_el.appendChild(new_text_node_el);
					}
					
					definition_method_tbody_el.appendChild(definition_method_tr_el);
					definition_method_tr_el.appendChild(definition_method_td_left_el);
					definition_method_tr_el.appendChild(definition_method_td_right_el);
				}
			} else {
				new_text_node_el = document.createTextNode('<?php echo addslashes(TEXT_DEFINITION_METHOD . ' - ' . TEXT_GEOLOCATION); ?>');
				new_title_el.appendChild(new_text_node_el);
				
				definition_method_el.appendChild(new_title_el);
				
				try {
					definition_method_table_el = document.createElement('<table width="100%">');
				} catch (e) {
					definition_method_table_el = document.createElement('table');
					definition_method_table_el.setAttribute('width', '100%');
				}
				try {
					definition_method_tbody_el = document.createElement('<tbody width="100%">');
				} catch (e) {
					definition_method_tbody_el = document.createElement('tbody');
					definition_method_tbody_el.setAttribute('width', '100%');
				}
				try {
					definition_method_tr_el = document.createElement('<tr>');
				} catch (e) {
					definition_method_tr_el = document.createElement('tr');
				}
				try {
					definition_method_td_left_el = document.createElement('<td class="AdvancedShipperConfigLabel">');
				} catch (e) {
					definition_method_td_left_el = document.createElement('td');
					definition_method_td_left_el.setAttribute('class', 'AdvancedShipperConfigLabel');
				}
				try {
					definition_method_td_right_el = document.createElement('<td class="AdvancedShipperConfigField">');
				} catch (e) {
					definition_method_td_right_el = document.createElement('td');
					definition_method_td_right_el.setAttribute('class', 'AdvancedShipperConfigField');
				}
				
				// Add the information about the distance that defines this region
				try {
					label_el = document.createElement('<label>');
				} catch (e) {
					label_el = document.createElement('label');
				}
				
				new_text_node_el = document.createTextNode('<?php echo addslashes(TEXT_LABEL_DISTANCE); ?>');
				label_el.appendChild(new_text_node_el);
				
				definition_method_td_left_el.appendChild(label_el);
				
				new_text_node_el = document.createTextNode(regions[region_i].distance);
				
				definition_method_td_right_el.appendChild(new_text_node_el);
				
				definition_method_el.appendChild(definition_method_table_el);
				definition_method_table_el.appendChild(definition_method_tbody_el);
				definition_method_tbody_el.appendChild(definition_method_tr_el);
				definition_method_tr_el.appendChild(definition_method_td_left_el);
				definition_method_tr_el.appendChild(definition_method_td_right_el);
			}
			
			new_td_left_el.appendChild(definition_method_el);
			
			// Create the table of rates overview
			try {
				table_of_rates_el = document.createElement('<fieldset class="AdvancedShipperRegionSummaryOdd">');
			} catch (e) {
				table_of_rates_el = document.createElement('fieldset');
				table_of_rates_el.setAttribute('class', 'AdvancedShipperRegionSummaryOdd');
			}
			
			new_title_el = document.createElement('legend');
			
			new_text_node_el = document.createTextNode('<?php echo addslashes(TEXT_TABLE_OF_RATES); ?>');
			new_title_el.appendChild(new_text_node_el);
			
			table_of_rates_el.appendChild(new_title_el);
			
			if (regions[region_i].table_of_rates.length == 0) {
				new_text_node_el = document.createTextNode('<?php echo addslashes(TEXT_REGION_HAS_NO_RATES); ?>');
			} else {
				new_text_node_el = document.createTextNode(regions[region_i].table_of_rates.replace(/,/g, ', '));
			}
			table_of_rates_el.appendChild(new_text_node_el);
			
			new_td_left_el.appendChild(table_of_rates_el);
			
			
			// Create the surcharge overview (if necessary)
			if (regions[region_i].surcharge.length > 0) {
				try {
					surcharge_el = document.createElement('<fieldset class="AdvancedShipperRegionSummaryOdd">');
				} catch (e) {
					surcharge_el = document.createElement('fieldset');
					surcharge_el.setAttribute('class', 'AdvancedShipperRegionSummaryOdd');
				}
				
				new_title_el = document.createElement('legend');
				
				new_text_node_el = document.createTextNode('<?php echo addslashes(TEXT_SURCHARGE); ?>');
				new_title_el.appendChild(new_text_node_el);
				
				surcharge_el.appendChild(new_title_el);
				
				new_text_node_el = document.createTextNode(regions[region_i].surcharge.replace(/,/g, ', '));
				
				surcharge_el.appendChild(new_text_node_el);
				
				new_td_left_el.appendChild(surcharge_el);
			}
			
			
			// Create the edit button panel
			try {
				edit_region_el = document.createElement('<fieldset class="AdvancedShipperRegionSummaryOdd">');
			} catch (e) {
				edit_region_el = document.createElement('fieldset');
				edit_region_el.setAttribute('class', 'AdvancedShipperRegionSummaryOdd');
			}
			
			new_title_el = document.createElement('legend');
			
			new_text_node_el = document.createTextNode('<?php echo addslashes(TEXT_EDIT_REGION); ?>');
			new_title_el.appendChild(new_text_node_el);
			
			edit_region_el.appendChild(new_title_el);
			
			edit_url = '<?php echo addslashes(zen_href_link(FILENAME_ADVANCED_SHIPPER_REGION_CONFIG, "method=" . $method_num . "&region={region_i}&update_region=true")); ?>';
			
			edit_url = edit_url.replace('{region_i}', region_i);
			
			try {
				edit_region_button_el = document.createElement('<input name="edit_region_' + region_i + '" id="edit_region_' + region_i + '" type="submit" value="<?php echo addslashes(IMAGE_EDIT); ?>" onClick="javascript:advshipperRegionConfig(\'' + edit_url + '\');return false;" />');
			} catch (e) {
				edit_region_button_el = document.createElement('input');
				edit_region_button_el.setAttribute('id', 'edit_region_' + region_i);
				edit_region_button_el.setAttribute('Name', 'edit_region_' + region_i);
				edit_region_button_el.setAttribute('type', 'submit');
				edit_region_button_el.setAttribute('value', '<?php echo addslashes(IMAGE_EDIT); ?>');
				edit_region_button_el.setAttribute('onClick', 'javascript:advshipperRegionConfig(\'' + edit_url + '\');return false;');
			}
			
			edit_region_el.appendChild(edit_region_button_el);
			
			new_td_button_panel_el.appendChild(edit_region_el);
			
			
			// Create the insert button panel
			try {
				insert_region_el = document.createElement('<fieldset class="AdvancedShipperRegionSummaryOdd">');
			} catch (e) {
				insert_region_el = document.createElement('fieldset');
				insert_region_el.setAttribute('class', 'AdvancedShipperRegionSummaryOdd');
			}
			
			new_title_el = document.createElement('legend');
			
			new_text_node_el = document.createTextNode('<?php echo addslashes(TEXT_INSERT_REGION); ?>');
			new_title_el.appendChild(new_text_node_el);
			
			insert_region_el.appendChild(new_title_el);
			
			insert_url = '<?php echo addslashes(zen_href_link(FILENAME_ADVANCED_SHIPPER_REGION_CONFIG, "method=" . $method_num . "&region={region_i}")); ?>';
			
			insert_url = insert_url.replace('{region_i}', region_i);
			
			try {
				insert_region_button_el = document.createElement('<input name="insert_region_' + region_i + '" id="insert_region_' + region_i + '" type="submit" value="<?php echo addslashes(IMAGE_INSERT); ?>" onClick="javascript:advshipperRegionConfig(\'' + insert_url + '\');return false;" />');
			} catch (e) {
				insert_region_button_el = document.createElement('input');
				insert_region_button_el.setAttribute('id', 'insert_region_' + region_i);
				insert_region_button_el.setAttribute('Name', 'insert_region_' + region_i);
				insert_region_button_el.setAttribute('type', 'submit');
				insert_region_button_el.setAttribute('value', '<?php echo addslashes(IMAGE_INSERT); ?>');
				insert_region_button_el.setAttribute('onClick', 'javascript:advshipperRegionConfig(\'' + insert_url + '\');return false;');
			}
			
			insert_region_el.appendChild(insert_region_button_el);
			
			new_td_button_panel_el.appendChild(insert_region_el);
			
			
			// Create the delete button panel
			try {
				delete_region_el = document.createElement('<fieldset class="AdvancedShipperRegionSummaryOdd">');
			} catch (e) {
				delete_region_el = document.createElement('fieldset');
				delete_region_el.setAttribute('class', 'AdvancedShipperRegionSummaryOdd');
			}
			
			new_title_el = document.createElement('legend');
			
			new_text_node_el = document.createTextNode('<?php echo addslashes(TEXT_DELETE_REGION); ?>');
			new_title_el.appendChild(new_text_node_el);
			
			delete_region_el.appendChild(new_title_el);
			
			try {
				delete_region_button_el = document.createElement('<input name="delete_region_' + region_i + '" id="delete_region_' + region_i + '" type="submit" value="<?php echo addslashes(IMAGE_DELETE); ?>" onClick="javascript:advshipperConfirmDeletion(' + region_i + ');return false;" />');
			} catch (e) {
				delete_region_button_el = document.createElement('input');
				delete_region_button_el.setAttribute('id', 'delete_region_' + region_i);
				delete_region_button_el.setAttribute('Name', 'delete_region_' + region_i);
				delete_region_button_el.setAttribute('type', 'submit');
				delete_region_button_el.setAttribute('value', '<?php echo addslashes(IMAGE_DELETE); ?>');
				delete_region_button_el.setAttribute('onClick', 'javascript:advshipperConfirmDeletion(' + region_i + ');return false;');
			}
			
			delete_region_el.appendChild(delete_region_button_el);
			
			new_td_button_panel_el.appendChild(delete_region_el);
			
			
			// Create the region ordering button panel
			if (num_regions > 1) {
				try {
					order_region_el = document.createElement('<fieldset class="AdvancedShipperRegionSummaryOdd">');
				} catch (e) {
					order_region_el = document.createElement('fieldset');
					order_region_el.setAttribute('class', 'AdvancedShipperRegionSummaryOdd');
				}
				
				new_title_el = document.createElement('legend');
				
				new_text_node_el = document.createTextNode('<?php echo addslashes(TEXT_REGION_ORDERING); ?>');
				new_title_el.appendChild(new_text_node_el);
				
				order_region_el.appendChild(new_title_el);
				
				if (region_i > 0) {
					try {
						move_region_up_button_el = document.createElement('<input name="move_region_up_' + region_i + '" id="move_region_up_' + region_i + '" type="submit" value="<?php echo addslashes(TEXT_MOVE_REGION_UP); ?>" onClick="javascript:advshipperMoveRegionUp(' + region_i + ');return false;" />');
					} catch (e) {
						move_region_up_button_el = document.createElement('input');
						move_region_up_button_el.setAttribute('id', 'move_region_up_' + region_i);
						move_region_up_button_el.setAttribute('Name', 'move_region_up_' + region_i);
						move_region_up_button_el.setAttribute('type', 'submit');
						move_region_up_button_el.setAttribute('value', '<?php echo addslashes(TEXT_MOVE_REGION_UP); ?>');
						move_region_up_button_el.setAttribute('onClick', 'javascript:advshipperMoveRegionUp(' + region_i + ');return false;');
					}
					
					order_region_el.appendChild(move_region_up_button_el);
				}
				
				if (region_i < (num_regions - 1)) {
					try {
						move_region_down_button_el = document.createElement('<input name="move_region_down_' + region_i + '" id="move_region_down_' + region_i + '" type="submit" value="<?php echo addslashes(TEXT_MOVE_REGION_DOWN); ?>" onClick="javascript:advshipperMoveRegionDown(' + region_i + ');return false;" />');
					} catch (e) {
						move_region_down_button_el = document.createElement('input');
						move_region_down_button_el.setAttribute('id', 'move_region_down_' + region_i);
						move_region_down_button_el.setAttribute('Name', 'move_region_down_' + region_i);
						move_region_down_button_el.setAttribute('type', 'submit');
						move_region_down_button_el.setAttribute('value', '<?php echo addslashes(TEXT_MOVE_REGION_DOWN); ?>');
						move_region_down_button_el.setAttribute('onClick', 'javascript:advshipperMoveRegionDown(' + region_i + ');return false;');
					}
					
					order_region_el.appendChild(move_region_down_button_el);
				}
				
				new_td_button_panel_el.appendChild(order_region_el);
			}
			
			// Add this region to the region panel
			new_region_config_el.appendChild(new_table_el);
			new_table_el.appendChild(new_tbody_el);
			new_tbody_el.appendChild(new_tr_el);
			new_tr_el.appendChild(new_td_left_el);
			new_tr_el.appendChild(new_td_button_panel_el);
			
			// Add the region panel to the method configuration page
			try {
				_regions_panel_el.insertBefore(new_region_config_el, _region_add_holder_el);
			} catch (e) {
				_regions_panel_el.appendChild(new_region_config_el);
			}
			
			// Record the information about this region for saving later ///////////////////////////
			if (_regions_info_el.value != '') {
				_regions_info_el.value += '(())';
			}
			// Encode the admin titles
			var _admin_titles_encoded = new Array();
			for (title_i = 0; title_i < num_titles; title_i++) {
				if (regions[region_i].admin_titles[title_i][1] != undefined &&
						regions[region_i].admin_titles[title_i][1].length > 0) {
					current_region_admin_title = regions[region_i].admin_titles[title_i][1];
					current_region_admin_title = current_region_admin_title.replace(/\+/g, '--plus--');
				} else {
					current_region_admin_title = 'null';
				}
				_admin_titles_encoded[title_i] = regions[region_i].admin_titles[title_i][0] + '|' +
					current_region_admin_title;
			}
			_admin_titles_encoded = _admin_titles_encoded.join('||');
			_regions_info_el.value += encodeUTF8String(_admin_titles_encoded) + '[[]]';
			
			// Encode the titles
			var _titles_encoded = new Array();
			for (title_i = 0; title_i < num_titles; title_i++) {
				if (regions[region_i].titles[title_i][1] != undefined &&
						regions[region_i].titles[title_i][1].length > 0) {
					current_region_title = regions[region_i].titles[title_i][1];
					current_region_title = current_region_title.replace(/\+/g, '--plus--');
				} else {
					current_region_title = 'null';
				}
				_titles_encoded[title_i] = regions[region_i].titles[title_i][0] + '|' +
					current_region_title;
			}
			_titles_encoded = _titles_encoded.join('||');
			_regions_info_el.value += encodeUTF8String(_titles_encoded) + '[[]]';
			
			_regions_info_el.value += regions[region_i].definition_method + '[[]]';
			_regions_info_el.value += escape(regions[region_i].countries_postcodes) + '[[]]';
			
			// Encode the zones
			_countries_zones = regions[region_i].countries_zones;
			if (_countries_zones != null) {
				var num_countries_zones = _countries_zones.length;
				var countries_zones_zone_ids = new Array();
				for (zone_i = 0; zone_i < num_countries_zones; zone_i++) {
					countries_zones_zone_ids[zone_i] = _countries_zones[zone_i].zone_id;
				}
				_countries_zones_encoded = countries_zones_zone_ids.join(',');
			} else {
				_countries_zones_encoded = '';
			}
			_regions_info_el.value += escape(_countries_zones_encoded) + '[[]]';
			
			// Encode the states
			_countries_states = regions[region_i].countries_states;
			if (_countries_states != null) {
				var num_countries_states = _countries_states.length;
				var countries_states_locality_ids = new Array();
				for (state_i = 0; state_i < num_countries_states; state_i++) {
					countries_states_locality_ids[state_i] = _countries_states[state_i].locality_id;
				}
				_countries_states_encoded = countries_states_locality_ids.join(',');
			} else {
				_countries_states_encoded = '';
			}
			_regions_info_el.value += escape(_countries_states_encoded) + '[[]]';
			
			// Encode the cities
			_countries_cities = regions[region_i].countries_cities;
			if (_countries_cities != null) {
				var num_countries_cities = _countries_cities.length;
				var countries_cities_locality_ids = new Array();
				for (city_i = 0; city_i < num_countries_cities; city_i++) {
					countries_cities_locality_ids[city_i] = _countries_cities[city_i].locality_id;
				}
				_countries_cities_encoded = countries_cities_locality_ids.join(',');
			} else {
				_countries_cities_encoded = '';
			}
			_regions_info_el.value += escape(_countries_cities_encoded) + '[[]]';
			
			
			_regions_info_el.value += regions[region_i].distance + '[[]]';
			_regions_info_el.value += regions[region_i].tax_class + '[[]]';
			_regions_info_el.value += regions[region_i].rates_include_tax + '[[]]';
			_regions_info_el.value += regions[region_i].rate_limits_inc + '[[]]';
			_regions_info_el.value += regions[region_i].total_up_price_inc_tax + '[[]]';
			_regions_info_el.value += escape(regions[region_i].table_of_rates.replace(/\+/g, '--plus--')) + '[[]]';
			_regions_info_el.value += regions[region_i].max_weight_per_package + '[[]]';
			_regions_info_el.value += escape(regions[region_i].packaging_weights.replace(/\+/g, '--plus--')) + '[[]]';
			_regions_info_el.value += escape(regions[region_i].surcharge.replace(/\+/g, '--plus--')) + '[[]]';
			
			// Encode the surcharge titles
			var _surcharge_titles_encoded = new Array();
			for (title_i = 0; title_i < num_titles; title_i++) {
				if (regions[region_i].surcharge_titles[title_i][1] != undefined &&
						regions[region_i].surcharge_titles[title_i][1].length > 0) {
					current_surcharge_title = regions[region_i].surcharge_titles[title_i][1];
					current_surcharge_title = current_surcharge_title.replace(/\+/g, '--plus--');
				} else {
					current_surcharge_title = 'null';
				}
				_surcharge_titles_encoded[title_i] = regions[region_i].surcharge_titles[title_i][0] + '|' +
					current_surcharge_title;
			}
			_surcharge_titles_encoded = _surcharge_titles_encoded.join('||');
			_regions_info_el.value += encodeUTF8String(_surcharge_titles_encoded) + '[[]]';
			
			_regions_info_el.value += regions[region_i].ups_calc_string + '[[]]';
			_regions_info_el.value += regions[region_i].usps_calc_string;
		}
	}
}