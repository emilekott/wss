<?php

/**
 * advshipper UPS Calculation class. 
 *
 * @author     Conor Kerr <zen-cart.advshipper@dev.ceon.net>
 * @copyright  Copyright 2007-2009 Ceon
 * @copyright  Portions Copyright 2003-2006 Zen Cart Development Team
 * @copyright  Portions Copyright 2003 osCommerce
 * @link       http://dev.ceon.net/web/zen-cart/advshipper
 * @license    http://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 * @version    $Id: class.AdvancedShipperUPSCalculator.php 382 2009-06-22 18:49:29Z Bob $
 */

/**
 * Load in the httpClient class if it hasn't already been loaded
 */
require_once(DIR_WS_CLASSES . 'http_client.php');


// {{{ AdvancedShipperUPSCalculator

/**
 * Connects to UPS online calculator and gets quotes for the shipping methods enabled in the
 * configuration.
 *
 * @author     Conor Kerr <zen-cart.advshipper@dev.ceon.net>
 * @copyright  Copyright 2007-2009 Ceon
 * @copyright  Portions Copyright 2003-2006 Zen Cart Development Team
 * @copyright  Portions Copyright 2003 osCommerce
 * @license    http://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 */
class AdvancedShipperUPSCalculator
{
	// {{{ properties
	
	/**
	 * The configuration settings for this instance.
	 *
	 * @var     array
	 * @access  public
	 */
	var $_config = null;
	
	// }}}
	
	
	// {{{ Class Constructor
	
	/**
	 * Create a new instance of the AdvancedShipperUPSCalculator class
	 *
	 * @param  array  $ups_config  An associative array with the configuration settings for this
	 *                             instance.
	 */
	function AdvancedShipperUPSCalculator($ups_config)
	{
		$this->_config = $ups_config;
	}
	
	// }}}
	
	
	// {{{ quote()

	/**
	 * Contacts UPS and gets a quote for the specified weight and configuration settings.
	 *
	 * @author Conor Kerr <zen-cart.advshipper@dev.ceon.net>
	 * @access public
	 * @param  float   $weight     The weight of the package to be shipped.
	 * @param  array   $min_max    Any minimum/maximum limits which should be applied to the final
	 *                             rate calculated.
	 * @return none
	 */
	function quote($weight, $min_max)
	{
		global $order;
		
		$rate_info = false;
		
		/*if ( (zen_not_null($method)) && (isset($this->types[$method])) ) {
		$prod = $method;
		} else if ($order->delivery['country']['iso_code_2'] == 'CA') {
		$prod = 'STD';
		} else {
		$prod = 'GNDRES';
		}*/
		
		//if ($method) $this->_upsAction('3'); // return a single quote
		$prod = 'GNDRES';
		$this->_upsProduct($prod);
		
		$weight = ($weight < 0.1 ? 0.1 : $weight);
		
		$country_name = zen_get_countries($this->_config['source_country'], true);
		$this->_upsOrigin($this->_config['source_postcode'], $country_name['countries_iso_code_2']);
		$this->_upsDest($order->delivery['postcode'], $order->delivery['country']['iso_code_2']);
		$this->_upsRate($this->_config['pickup_method']);
		$this->_upsContainer($this->_config['packaging']);
		$this->_upsWeight($weight);
		$this->_upsRescom($this->_config['delivery_type']);
		$upsQuote = $this->_upsGetQuote();
		
		if (!is_array($upsQuote)) {
			$upsQuote = strtolower($upsQuote);
			if (strpos($upsQuote, 'unsupported country') !== false)	{
				return false;
			} else if (strpos($upsQuote, 'missing consigneepostalcode') !== false) {
				return array(
					'error' => MODULE_ADVANCED_SHIPPER_ERROR_SPECIFY_POSTCODE
					);
			} else {
				return array(
					'error' => $upsQuote
					);
			}
		}
		
		$std_rcd = false;
		
		$qsize = sizeof($upsQuote);
		for ($i = 0; $i < $qsize; $i++) {
			list($type, $rate) = each($upsQuote[$i]);
			
			if ($type == 'STD') {
				if ($std_rcd) {
					continue;
				} else {
					$std_rcd = true;
				}
			}
			
			// Check if this ups service is to be used by store
			if ($this->_config['shipping_service_' . strtolower($type)] != 1) {
				continue;
			}
			
			if (!is_array($rate_info)) {
				$rate_info = array();
			}
			
			if ($min_max != false) {
				// Apply the limit(s) to the rate
				$rate_limited = advshipper::calcMinMaxValue($rate, $min_max['min'],
					$min_max['max']);
				
				if ($rate_limited != $rate) {
					$rate = $rate_limited;
				}
			}
			
			$rate_info[] = array(
				'rate' => $rate,
				'rate_components_info' => array(
						array(
							'value_band_total' => $rate,
							'individual_value' => null,
							'num_individual_values' => $weight,
							'additional_charge' => null,
							'calc_method' => ADVSHIPPER_CALC_METHOD_UPS
							)
					),
				'rate_extra_title' => TEXT_UPS_TITLE_PREFIX .
					constant('TEXT_UPS_SHIPPING_SERVICE_' . strtoupper($type))
				);
		}
		
		return $rate_info;
	}
	
	// }}}
  
  /**
   * Set UPS Product Code
   *
   * @param string $prod
   */
  function _upsProduct($prod){
    $this->_upsProductCode = $prod;
  }
  /**
   * Set UPS Origin details
   *
   * @param string $postal
   * @param string $country
   */
  function _upsOrigin($postal, $country){
    $this->_upsOriginPostalCode = substr($postal, 0, 5);
    $this->_upsOriginCountryCode = $country;
  }
  /**
   * Set UPS Destination information
   *
   * @param string $postal
   * @param string $country
   */
  function _upsDest($postal, $country){
    $postal = str_replace(' ', '', $postal);

    if ($country == 'US') {
      $this->_upsDestPostalCode = substr($postal, 0, 5);
    } else {
      $this->_upsDestPostalCode = substr($postal, 0, 6);
    }

    $this->_upsDestCountryCode = $country;
  }
  /**
   * Set UPS rate-quote method
   *
   * @param string $foo
   */
  function _upsRate($foo) {
    switch ($foo) {
      case 'RDP':
      $this->_upsRateCode = 'Regular+Daily+Pickup';
      break;
      case 'OCA':
      $this->_upsRateCode = 'On+Call+Air';
      break;
      case 'OTP':
      $this->_upsRateCode = 'One+Time+Pickup';
      break;
      case 'LC':
      $this->_upsRateCode = 'Letter+Center';
      break;
      case 'CC':
      $this->_upsRateCode = 'Customer+Counter';
      break;
    }
  }
  /**
   * Set UPS Container type
   *
   * @param string $foo
   */
  function _upsContainer($foo) {
    switch ($foo) {
      case 'CP': // Customer Packaging
        $this->_upsContainerCode = '00';
        break;
      case 'ULE': // UPS Letter Envelope
        $this->_upsContainerCode = '01';
        break;
      case 'UT': // UPS Tube
        $this->_upsContainerCode = '03';
        break;
      case 'UEB': // UPS Express Box
        $this->_upsContainerCode = '21';
        break;
      case 'UW25': // UPS Worldwide 25 kilo
        $this->_upsContainerCode = '24';
        break;
      case 'UW10': // UPS Worldwide 10 kilo
        $this->_upsContainerCode = '25';
        break;
    }
  }
  /**
   * Set UPS package weight
   *
   * @param string $foo
   */
  function _upsWeight($foo) {
    $this->_upsPackageWeight = $foo;
  }
  /**
   * Set UPS address-quote method (residential vs commercial)
   *
   * @param string $foo
   */
  function _upsRescom($foo) {
    switch ($foo) {
      case 'RES': // Residential Address
        $this->_upsResComCode = '1';
        break;
      case 'COM': // Commercial Address
        $this->_upsResComCode = '0';
        break;
    }
  }
  /**
   * Set UPS Action method
   *
   * @param string/integer $action
   */
  function _upsAction($action) {
    /* 3 - Single Quote
    4 - All Available Quotes */

    $this->_upsActionCode = $action;
  }
  /**
   * Sent request for quote to UPS via older HTML method
   *
   * @return array
   */
  function _upsGetQuote() {
    if (!isset($this->_upsActionCode)) $this->_upsActionCode = '4';

    $request = join('&', array('accept_UPS_license_agreement=yes',
                               '10_action=' . $this->_upsActionCode,
                               '13_product=' . $this->_upsProductCode,
                               '14_origCountry=' . $this->_upsOriginCountryCode,
                               '15_origPostal=' . $this->_upsOriginPostalCode,
                               '19_destPostal=' . $this->_upsDestPostalCode,
                               '22_destCountry=' . $this->_upsDestCountryCode,
                               '23_weight=' . $this->_upsPackageWeight,
                               '47_rate_chart=' . $this->_upsRateCode,
                               '48_container=' . $this->_upsContainerCode,
                               '49_residential=' . $this->_upsResComCode));
	
	advshipper::debug("Data being sent to UPS: \n\n" . str_replace('&', "<br />\n", $request),
		true);
	
    $http = new httpClient();
    if ($http->Connect('www.ups.com', 80)) {
      $http->addHeader('Host', 'www.ups.com');
      $http->addHeader('User-Agent', 'Zen Cart');
      $http->addHeader('Connection', 'Close');

      if ($http->Get('/using/services/rave/qcostcgi.cgi?' . $request)) $body = $http->getBody();

      $http->Disconnect();
    } else {
      return 'error';
    }

    // BOF: UPS USPS
    /*
    TEST by checking out in the catalog; try a variety of shipping destinations to be sure
    your customers will be properly served.  If you are not getting any quotes, try enabling
    more alternatives in admin. Make sure your store's postal code is set in Admin ->
    Configuration -> Shipping/Packaging, since you won't get any quotes unless there is
    a origin that UPS recognizes.

    If you STILL don't get any quotes, here is a way to find out exactly what UPS is sending
    back in response to rate quote request.  At line 278, you will find this statement in a
    comment block:

    mail('you@yourdomain.com','UPS response',$body,'From: <you@yourdomain.com>');
    */
    // EOF: UPS USPS

	advshipper::debug("Results from contacting UPS: \n\n" . str_replace('%', ' - ', nl2br($body)),
		true);

    $body_array = explode("\n", $body);
	
	if (strpos($body, 'Missing ConsigneePostalCode') !== false) {
		return 'Missing ConsigneePostalCode';
	}
	
/* //DEBUG ONLY
    $n = sizeof($body_array);
    for ($i=0; $i<$n; $i++) {
      $result = explode('%', $body_array[$i]);
      print_r($result);
    }
    die('END');
*/

    $returnval = array();
    $errorret = 'error'; // only return 'error' if NO rates returned

    $n = sizeof($body_array);
    for ($i=0; $i<$n; $i++) {
      $result = explode('%', $body_array[$i]);
      $errcode = substr($result[0], -1);
      switch ($errcode) {
        case 3:
        if (is_array($returnval)) $returnval[] = array($result[1] => $result[10]);
        break;
        case 4:
        if (is_array($returnval)) $returnval[] = array($result[1] => $result[10]);
        break;
        case 5:
        $errorret = $result[1];
        break;
        case 6:
        if (is_array($returnval)) $returnval[] = array($result[3] => $result[10]);
        break;
      }
    }
    if (empty($returnval)) $returnval = $errorret;

    return $returnval;
  }
}
?>