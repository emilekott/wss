<?php

/**
 * advshipper Configuration Generation Functions
 *
 * This file contains functions necessary to generate the configuration options for the Advanced
 * Shipper shipping module.
 *
 * @author     Conor Kerr <zen-cart.advshipper@dev.ceon.net>
 * @copyright  Copyright 2007-2009 Ceon
 * @link       http://dev.ceon.net/web/zen-cart/advshipper
 * @license    http://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 * @version    $Id: advshipper.php 382 2009-06-22 18:49:29Z Bob $
 */

require_once(DIR_FS_CATALOG . DIR_WS_FUNCTIONS . 'advshipper.php');

/**
 * Global define for separator for a product's options
 */
define('ADVSHIPPER_PRODUCT_OPTIONS_SEPARATOR', '___');


// {{{ advshipper_cfg_pull_down_day_of_week()

/**
 * Builds a selection gadget to allow selection of a single day of the week.
 *
 * @access  public
 * @param   string   $name         The name for the selection gadget.
 * @param   integer  $day_of_week  The selected day of the week.
 * @return  string   The HTML for the day of week selection gadget.
 */
if (!function_exists('advshipper_cfg_pull_down_day_of_week')) {
	function advshipper_cfg_pull_down_day_of_week($name, $day_of_week, $parameters = '')
	{
		$day_of_week_array = array(
			array(
				'id' => '-1',
				'text' => TEXT_NONE
				),
			array(
				'id' => '1',
				'text' => TEXT_MONDAY
				),
			array(
				'id' => '2',
				'text' => TEXT_TUESDAY
				),
			array(
				'id' => '3',
				'text' => TEXT_WEDNESDAY
				),
			array(
				'id' => '4',
				'text' => TEXT_THURSDAY
				),
			array(
				'id' => '5',
				'text' => TEXT_FRIDAY
				),
			array(
				'id' => '6',
				'text' => TEXT_SATURDAY
				),
			array(
				'id' => '0',
				'text' => TEXT_SUNDAY
				)
			);
		
		return zen_draw_pull_down_menu($name, $day_of_week_array, $day_of_week, $parameters);
	}
}

// }}}


// {{{ advshipper_cfg_pull_down_tax_classes()

/**
 * Builds a selection gadget to allow selection of a tax class.
 *
 * @access  public
 * @param   string   $name          The name for the selection gadget.
 * @param   integer  $tax_class_id  The selected tax class.
 * @return  string   The HTML for the day of week selection gadget.
 */
function advshipper_cfg_pull_down_tax_classes($name, $tax_class_id, $parameters)
{
	global $db;
	
	$tax_class_array = array(array('id' => '0', 'text' => TEXT_NONE));
	
	$tax_class = $db->Execute(
		"SELECT
			tax_class_id, tax_class_title
		FROM
			" . TABLE_TAX_CLASS . "
		ORDER BY
			tax_class_title"
		);
	
	while (!$tax_class->EOF) {
		$tax_class_array[] = array(
			'id' => $tax_class->fields['tax_class_id'],
			'text' => $tax_class->fields['tax_class_title']
			);
		$tax_class->MoveNext();
	}
	
	return zen_draw_pull_down_menu($name, $tax_class_array, $tax_class_id, $parameters);
}

// }}}


if (!function_exists('array_insert')) {
	function array_insert(&$array, $position, $insert_array)
	{
		if (!is_int($position)) {
			$i = 0;
			foreach ($array as $key => $value) {
				if ($key == $position) {
					$position = $i;
					break;
				}
				$i++;
			}
		}
		$first_array = array_splice ($array, 0, $position);
		$array = array_merge ($first_array, $insert_array, $array);
	}
}


// {{{ advshipper_parse_time_string()

/**
 * Ensures the string passed is a valid time.
 *
 * @access  public
 * @param   string   $time_string   The time string to be checked.
 * @return  mixed    The validated time string or false if the time string isn't valid.
 */
function advshipper_parse_time_string($time_string)
{
	$time_string = trim($time_string);
	
	if (!ereg('[0-2][0-9]\:[0-5][0-9]', $time_string)) {
		return false;
	}
	
	return $time_string;
}

// }}}


// {{{ advshipper_get_generated_category_path()

/**
 * Returns a neatly formatted version of the entire category path for the specified category.
 *
 * @access  public
 * @param   integer   $id   The ID of the category.
 * @return  string    The category path.
 */
function advshipper_get_generated_category_path($id)
{
	$calculated_category_path_string = '';
	$calculated_category_path = zen_generate_category_path($id);
	for ($i = 0, $n = sizeof($calculated_category_path); $i < $n; $i++) {
		for ($j = 0, $k = sizeof($calculated_category_path[$i]); $j < $k; $j++) {
			$calculated_category_path_string = $calculated_category_path[$i][$j]['text'] . ' :: ' . $calculated_category_path_string;
		}
		$calculated_category_path_string = substr($calculated_category_path_string, 0, -4);
	}
	//$calculated_category_path_string = substr($calculated_category_path_string, 0, -4);
	
	return $calculated_category_path_string;
}

// }}}


// {{{ advshipper_get_manufacturer_name()

/**
 * Returns the name of the manufacturer.
 *
 * @access  public
 * @param   integer   $id   The ID of the manufacturer.
 * @return  string    The manufacturer's name.
 */
function advshipper_get_manufacturer_name($id)
{
	global $db;
	
	$manufacturer_name = '';
	
	$manufacturer_name_query = "
		SELECT
			m.manufacturers_name
		FROM
			" . TABLE_MANUFACTURERS . " m
		WHERE
			m.manufacturers_id = '" . (int) $id . "';";
	
	$manufacturer_name_result = $db->Execute($manufacturer_name_query);
	
	if (!$manufacturer_name_result->EOF) {
		$manufacturer_name =
			$manufacturer_name_result->fields['manufacturers_name'];
	}
	
	return $manufacturer_name;
}

// }}}


// {{{ advshipper_get_manufacturer_name()

/**
 * Returns the name of the manufacturer.
 *
 * @access  public
 * @param   integer   $id   The ID of the manufacturer.
 * @return  string    The manufacturer's name.
 */
function advshipper_get_manufacturers()
{
	global $db;
	
	$manufacturers_array = array();
	
	$manufacturers_query = "
		SELECT
			manufacturers_id,
			manufacturers_name
		FROM
			" . TABLE_MANUFACTURERS . "
		ORDER BY
			manufacturers_name";
	
	$manufacturers = $db->Execute($manufacturers_query);
	
	while (!$manufacturers->EOF) {
		$manufacturers_array[] = array(
			'id' => $manufacturers->fields['manufacturers_id'],
			'text' => $manufacturers->fields['manufacturers_name']
			);
		$manufacturers->MoveNext();
	}
	
	return $manufacturers_array;
}

// }}}


// {{{ advshipper_draw_radio_field()

/**
 * Compatibility function supports adding parameters to a radio field for Zen Cart versions from
 * 1.2.x upwards.
 *
 * @access  public
 * @param   string   $name        The name for the radio gadget.
 * @param   string   $value       The value for the radio gadget.
 * @param   boolean  $checked     Whether or not the radio gadget should be checked.
 * @param   string   $compare     A value to be compared with the value for the radio gadget.
 * @param   string   $parameters  A string of parameters to be added to the radio gadget.
 * @return  string   The source for the radio gadget.
 */
function advshipper_draw_radio_field($name, $value = '', $checked = false, $compare = '', $parameters = '')
{
	$radio_field_source = '';
	
	if (substr(PROJECT_VERSION_MAJOR, 0, 1) == "1" && substr(PROJECT_VERSION_MINOR, 0, 1) == "2") {
		$radio_field_source = zen_draw_radio_field($name, $value, $checked, $compare);
		$radio_field_source = str_replace('>', ' ' . $parameters . '>', $radio_field_source);
	} else {
		$radio_field_source = zen_draw_radio_field($name, $value, $checked, $compare, $parameters);
	}
	
	return $radio_field_source;
}

// }}}


// {{{ advshipper_draw_checkbox_field()

/**
 * Compatibility function supports adding parameters to a checkbox field for Zen Cart versions from
 * 1.2.x upwards.
 *
 * @access  public
 * @param   string   $name        The name for the checkbox gadget.
 * @param   string   $value       The value for the checkbox gadget.
 * @param   boolean  $checked     Whether or not the checkbox gadget should be checked.
 * @param   string   $compare     A value to be compared with the value for the checkbox gadget.
 * @param   string   $parameters  A string of parameters to be added to the checkbox gadget.
 * @return  string   The source for the checkbox gadget.
 */
function advshipper_draw_checkbox_field($name, $value = '', $checked = false, $compare = '', $parameters = '')
{
	$checkbox_field_source = '';
	
	if (substr(PROJECT_VERSION_MAJOR, 0, 1) == "1" && substr(PROJECT_VERSION_MINOR, 0, 1) == "2") {
		$checkbox_field_source = zen_draw_checkbox_field($name, $value, $checked, $compare);
		$checkbox_field_source = str_replace('>', ' ' . $parameters . '>', $checkbox_field_source);
	} else {
		$checkbox_field_source = zen_draw_checkbox_field($name, $value, $checked, $compare, $parameters);
	}
	
	return $checkbox_field_source;
}

// }}}


// {{{ UTF8URLDecode()

/**
 * Decodes a string encoded in UTF-8 pairs.
 *
 * @access  public
 * @param   string   $value   The encoded UTF-8 pairs to be decoded or an array of encoded UTF-8 
 *                            string pairs.
 * @return  string   The decoded strings/array of strings.
 */
function UTF8URLDecode($value)
{
	if (is_array($value)) {
		foreach ($value as $key => $val) {
			$value[$key] = UTF8URLDecode($val);
		}
	} else {
		$value = preg_replace('/%([0-9a-f]{2})/ie', 'chr(hexdec($1))', (string) $value);
	}
	
	return $value;
}

// }}}

?>