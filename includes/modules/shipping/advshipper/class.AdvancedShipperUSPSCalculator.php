<?php

/**
 * advshipper USPS Calculation class. 
 *
 * @author     Conor Kerr <zen-cart.advshipper@dev.ceon.net>
 * @copyright  Copyright 2007-2009 Ceon
 * @copyright  Portions Copyright 2003-2006 Zen Cart Development Team
 * @copyright  Portions Copyright 2003 osCommerce
 * @link       http://dev.ceon.net/web/zen-cart/advshipper
 * @license    http://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 * @version    $Id: class.AdvancedShipperUSPSCalculator.php 382 2009-06-22 18:49:29Z Bob $
 */

/**
 * Load in the httpClient class if it hasn't already been loaded
 */
require_once(DIR_WS_CLASSES . 'http_client.php');


// {{{ AdvancedShipperUSPSCalculator

/**
 * Connects to USPS online calculator and gets quotes for the shipping methods enabled in the
 * configuration.
 *
 * @author     Conor Kerr <zen-cart.advshipper@dev.ceon.net>
 * @copyright  Copyright 2007-2009 Ceon
 * @copyright  Portions Copyright 2003-2006 Zen Cart Development Team
 * @copyright  Portions Copyright 2003 osCommerce
 * @license    http://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 */
class AdvancedShipperUSPSCalculator
{
	// {{{ properties
	
	/**
	 * The configuration settings for this instance.
	 *
	 * @var     array
	 * @access  public
	 */
	var $_config = null;
	
	/**
	 * The total price of the items in package to be shipped.
	 *
	 * @var     float
	 * @access  protected
	 */
	var $_price = 0;
	
	// }}}
	
	
	// {{{ Class Constructor
	
	/**
	 * Create a new instance of the AdvancedShipperUSPSCalculator class
	 *
	 * @param  array  $ups_config  An associative array with the configuration settings for this
	 *                             instance.
	 */
	function AdvancedShipperUSPSCalculator($ups_config)
	{
		$this->_config = $ups_config;
		
    $this->types = array('EXPRESS' => 'Express Mail',
        'FIRST CLASS' => 'First-Class Mail',
        'PRIORITY' => 'Priority Mail',
        'PARCEL' => 'Parcel Post',
        'MEDIA' => 'Media Mail',
        'BPM' => 'Bound Printed Material',
        'LIBRARY' => 'Library'
        );

    $this->intl_types = array(
        'Global Express' => 'Global Express Guaranteed',
        'Global Express Non-Doc Rect' => 'Global Express Guaranteed Non-Document Rectangular',
        'Global Express Non-Doc Non-Rect' => 'Global Express Guaranteed Non-Document Non-Rectangular',
        'Global Express Envelopes' => 'USPS GXG Envelopes',
        'Express Mail Int' => 'Express Mail International (EMS)',
        'Express Mail Int Flat Rate Env' => 'Express Mail International (EMS) Flat-Rate Envelope',
        'Priority Mail International' => 'Priority Mail International',
        'Priority Mail Int Flat Rate Env' => 'Priority Mail International Flat-Rate Envelope',
        'Priority Mail Int Flat Rate Box' => 'Priority Mail International Flat-Rate Box',
        'Priority Mail Int Flat Rate Lrg Box' => 'Priority Mail International Large Flat Rate Box',
        'First Class Mail Int Lrg Env' => 'First Class Mail International Large Envelope',
        'First Class Mail Int Package' => 'First Class Mail International Package',
        'First Class Mail Int Letters' => 'First Class Mail International Letters',
        'First Class Mail Int Flats' => 'First Class Mail International Flats',
        'First Class Mail Int Parcels' => 'First Class Mail International Parcels'
        );


    $this->countries = $this->country_list();
	}
	
	// }}}
	
	
	// {{{ quote()

	/**
	 * Contacts USPS and gets a quote for the specified weight and configuration settings.
	 *
	 * @author Conor Kerr <zen-cart.advshipper@dev.ceon.net>
	 * @access public
	 * @param  float   $weight     The weight of the package to be shipped.
	 * @param  float   $price      The total price of the items in package to be shipped.
	 * @param  array   $min_max    Any minimum/maximum limits which should be applied to the final
	 *                             rate calculated.
	 * @return none
	 */
	function quote($weight, $price, $min_max)
	{
		global $order, $transittime;
		
		$rate_info = false;
		
		$this->_price = $price;
		
		// USPS doesnt accept zero weight
		$weight = ($weight < 0.1 ? 0.1 : $weight);
		$shipping_pounds = floor ($weight);
		$shipping_ounces = round(16 * ($weight - floor($weight)));
		
		// weight must be less than 35lbs and greater than 6 ounces or it is not machinable
		switch (true) {
			case ($shipping_pounds == 0 and $shipping_ounces < 6):
				// override admin choice too light
				$is_machinable = false;
				break;
			case ($weight > 35):
				// override admin choice too heavy
				$is_machinable = false;
				break;
			default:
				// admin choice on what to use
				$is_machinable = ($this->_config['machinable'] == 1 ? true : false);
		}
		
		$this->_setMachinable(($is_machinable ? 'True' : 'False'));
		$this->_setContainer('None');
		$this->_setSize('REGULAR');
		
		$this->_setWeight($shipping_pounds, $shipping_ounces);
		$uspsQuote = $this->_getQuote();
		
		if (is_array($uspsQuote)) {
			if (isset($uspsQuote['error'])) {
				if (strpos($uspsQuote['error'], 'Missing value for ZipDestination') !== false) {
					return array(
						'error' => MODULE_ADVANCED_SHIPPER_ERROR_SPECIFY_POSTCODE
						);
				}
				
				return array(
					'error' => MODULE_ADVANCED_SHIPPER_ERROR_USPS_SERVER . $uspsQuote['error'] .
						($this->_config['server'] == 't' ?
						MODULE_ADVANCED_SHIPPER_USPS_TEST_MODE_NOTICE : '')
					);
			} else {
				$methods = array();
				$size = sizeof($uspsQuote);
				
				for ($i = 0; $i < $size; $i++) {
					list($type, $rate) = each($uspsQuote[$i]);
					
					// BOF: USPS USPS
					switch ($type) {
						case 'EXPRESS':
						case 'FIRST CLASS':
						case 'PRIORITY':
						case 'PARCEL':
						case 'MEDIA':
						case 'BPM':
						case 'LIBRARY':
							$title = constant('TEXT_USPS_DOMESTIC_' .
								str_replace(' ', '_', strtoupper($type)));
							break;
						case 'Global Express Guaranteed':
							$title = TEXT_USPS_INTERNATIONAL_GE;
							break;
						case 'Global Express Guaranteed Non-Document Rectangular':
							$title = TEXT_USPS_INTERNATIONAL_GENDR;
							break;
						case 'Global Express Guaranteed Non-Document Non-Rectangular':
							$title = TEXT_USPS_INTERNATIONAL_GENDNR;
							break;
						case 'USPS GXG Envelopes':
							$title = TEXT_USPS_INTERNATIONAL_GEE;
							break;
						case 'Express Mail International (EMS)':
							$title = TEXT_USPS_INTERNATIONAL_EMI;
							break;
						case 'Express Mail International (EMS) Flat-Rate Envelope':
							$title = TEXT_USPS_INTERNATIONAL_EMIFRE;
							break;
						case 'Priority Mail International':
							$title = TEXT_USPS_INTERNATIONAL_PMI;
							break;
						case 'Priority Mail International Flat-Rate Envelope':
							$title = TEXT_USPS_INTERNATIONAL_PMIFRE;
							break;
						case 'Priority Mail International Flat-Rate Box':
							$title = TEXT_USPS_INTERNATIONAL_PMIFRB;
							break;
						case 'Priority Mail International Large Flat Rate Box':
							$title = TEXT_USPS_INTERNATIONAL_PMILFRB;
							break;
						case 'First Class Mail International Large Envelope':
							$title = TEXT_USPS_INTERNATIONAL_FCMILE;
							break;
						case 'First Class Mail International Package':
							$title = TEXT_USPS_INTERNATIONAL_FCMIP;
							break;
						case 'First Class Mail International Letters':
							$title = TEXT_USPS_INTERNATIONAL_FCMIL;
							break;
						case 'First Class Mail International Flats':
							$title = TEXT_USPS_INTERNATIONAL_FCMIF;
							break;
						case 'First Class Mail International Parcels':
							$title = TEXT_USPS_INTERNATIONAL_FCMIPAR;
							break;
						default:
							$title = $type;
							break;
					}
					
					$title = TEXT_USPS_TITLE_PREFIX . $title;
					
					if ($this->_config['display_transit_time'] == 1 && isset($transittime[$type])) {
						$title .= $transittime[$type];
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
									'calc_method' => ADVSHIPPER_CALC_METHOD_USPS
									)
							),
						'rate_extra_title' => $title
						);
				}
			}
		} else {
			// No quotes for current address
		}
		
		return $rate_info;
	}
	
	// }}}
  
  /**
   * Set USPS service mode
   *
   * @param string $service
   */
  function _setService($service) {
    $this->service = $service;
  }
  /**
   * Set USPS weight for quotation collection
   *
   * @param integer $pounds
   * @param integer $ounces
   */
  function _setWeight($pounds, $ounces=0) {
    $this->pounds = $pounds;
    $this->ounces = $ounces;
  }
  /**
   * Set USPS container type
   *
   * @param string $container
   */
  function _setContainer($container) {
    $this->container = $container;
  }
  /**
   * Set USPS package size
   *
   * @param integer $size
   */
  function _setSize($size) {
    $this->size = $size;
  }
  /**
   * Set USPS machinable flag
   *
   * @param boolean $machinable
   */
  function _setMachinable($machinable) {
    $this->machinable = $machinable;
  }
  /**
   * Get actual quote from USPS
   *
   * @return array of results or boolean false if no results
   */
  function _getQuote() {
    // BOF: UPS USPS
    global $order, $transittime;
	
    $transit = false;
	if ($this->_config['display_transit_time'] == 1) {
		$transit = true;
	}
    // EOF: UPS USPS

    if ($order->delivery['country']['id'] == $this->_config['source_country']) {
      $request  = '<RateRequest USERID="' . $this->_config['user_id'] . '">';
      $services_count = 0;

      if (isset($this->service)) {
        $this->types = array($this->service => $this->types[$this->service]);
      }

      $dest_zip = str_replace(' ', '', $order->delivery['postcode']);
      if ($order->delivery['country']['iso_code_2'] == 'US') $dest_zip = substr($dest_zip, 0, 5);

      reset($this->types);
      // BOF: UPS USPS
      //$allowed_types = explode(", ", MODULE_SHIPPING_USPS_TYPES);
      while (list($key, $value) = each($this->types)) {
        // BOF: UPS USPS
        //if ( !in_array($key, $allowed_types) ) continue;
		if ($this->_config['domestic_' . str_replace(' ' , '_', strtolower($key))] == 0) {
			continue;
		}
        $request .= '<Package ID="' . $services_count . '">' .
        '<Service>' . $key . '</Service>' .
        '<ZipOrigination>' . $this->_config['source_postcode'] . '</ZipOrigination>' .
        '<ZipDestination>' . $dest_zip . '</ZipDestination>' .
        '<Pounds>' . $this->pounds . '</Pounds>' .
        '<Ounces>' . $this->ounces . '</Ounces>' .
        '<Container>' . $this->container . '</Container>' .
        '<Size>' . $this->size . '</Size>' .
        '<Machinable>' . $this->machinable . '</Machinable>' .
        '</Package>';
        // BOF: UPS USPS
        if($transit){
          $transitreq  = 'USERID="' . $this->_config['user_id'] . '">' .
          '<OriginZip>' . $this->_config['source_postcode'] . '</OriginZip>' .
          '<DestinationZip>' . $dest_zip . '</DestinationZip>';

          switch ($key) {
            case 'EXPRESS':  $transreq[$key] = 'API=ExpressMail&XML=' .
            urlencode( '<ExpressMailRequest ' . $transitreq . '</ExpressMailRequest>');
            break;
            case 'PRIORITY': $transreq[$key] = 'API=PriorityMail&XML=' .
            urlencode( '<PriorityMailRequest ' . $transitreq . '</PriorityMailRequest>');
            break;
            case 'PARCEL':   $transreq[$key] = 'API=StandardB&XML=' .
            urlencode( '<StandardBRequest ' . $transitreq . '</StandardBRequest>');
            break;
            default:         $transreq[$key] = '';
            break;
          }
        }
        // EOF: UPS USPS
        $services_count++;
      }
      $request .= '</RateRequest>';

      $request = 'API=Rate&XML=' . urlencode($request);
    } else {
      $request  = '<IntlRateRequest USERID="' . $this->_config['user_id'] . '">' .
      '<Package ID="0">' .
      '<Pounds>' . $this->pounds . '</Pounds>' .
      '<Ounces>' . $this->ounces . '</Ounces>' .
      '<MailType>Package</MailType>' .
      '<Country>' . $this->countries[$order->delivery['country']['iso_code_2']] . '</Country>' .
      '</Package>' .
      '</IntlRateRequest>';

      $request = 'API=IntlRate&XML=' . urlencode($request);
    }

    switch ($this->_config['server']) {
      case 'p':
      $usps_server = 'production.shippingapis.com';
      $api_dll = 'shippingapi.dll';
      break;
      case 't':
      default:
      $usps_server = 'testing.shippingapis.com';
      $api_dll = 'ShippingAPI.dll';
      break;
    }
	
	advshipper::debug("Data being sent to USPS: \n\n" .
		str_replace('&amp;', "<br />\n", htmlentities(urldecode($request))), true);

    $body = '';

    $http = new httpClient();
    $http->timeout = 5;
    if ($http->Connect($usps_server, 80)) {
      $http->addHeader('Host', $usps_server);
      $http->addHeader('User-Agent', 'Zen Cart');
      $http->addHeader('Connection', 'Close');

      if ($http->Get('/' . $api_dll . '?' . $request)) $body = $http->getBody();
      //if (MODULE_SHIPPING_USPS_DEBUG_MODE == 'Email') mail(STORE_OWNER_EMAIL_ADDRESS, 'Debug: USPS rate quote response', $body, 'From: <' . EMAIL_FROM . '>');
      
	  advshipper::debug("Results from contacting USPS: \n\n" . nl2br(htmlentities($body)),
		true);
	  
	  // BOF: UPS USPS
      if ($transit && is_array($transreq) && ($order->delivery['country']['id'] == $this->_config['source_country'])) {
        while (list($key, $value) = each($transreq)) {
          if ($http->Get('/' . $api_dll . '?' . $value)) $transresp[$key] = $http->getBody();
        }
      }
      // EOF: UPS USPS

      $http->Disconnect();
    } else {
      return -1;
    }

    $response = array();
    while (true) {
      if ($start = strpos($body, '<Package ID=')) {
        $body = substr($body, $start);
        $end = strpos($body, '</Package>');
        $response[] = substr($body, 0, $end+10);
        $body = substr($body, $end+9);
      } else {
        break;
      }
    }

    $rates = array();
    if ($order->delivery['country']['id'] == $this->_config['source_country']) {
      if (sizeof($response) == 0) {
		$response = array($body);
	  }
	  if (sizeof($response) == '1') {
        if (ereg('<Error>', $response[0])) {
          $number = ereg('<Number>(.*)</Number>', $response[0], $regs);
          $number = $regs[1];
          $description = ereg('<Description>(.*)</Description>', $response[0], $regs);
          $description = $regs[1];

          return array('error' => $number . ' - ' . $description);
        }
      }

      $n = sizeof($response);
      for ($i=0; $i<$n; $i++) {
        if (strpos($response[$i], '<Postage>')) {
          $service = ereg('<Service>(.*)</Service>', $response[$i], $regs);
          $service = $regs[1];
          $postage = ereg('<Postage>(.*)</Postage>', $response[$i], $regs);
          $postage = $regs[1];

          $rates[] = array($service => $postage);

          // BOF: UPS USPS
          if ($transit) {
            switch ($service) {
              case 'EXPRESS':     $time = ereg('<MonFriCommitment>(.*)</MonFriCommitment>', $transresp[$service], $tregs);
              $time = $tregs[1];
              if ($time == '' || $time == 'No Data') {
                $time = '1 - 2 ' . TEXT_USPS_DAYS;
              } else {
                $time = 'Tomorrow by ' . $time;
              }
              break;
              case 'PRIORITY':    $time = ereg('<Days>(.*)</Days>', $transresp[$service], $tregs);
              $time = $tregs[1];
              if ($time == '' || $time == 'No Data') {
                $time = '2 - 3 ' . TEXT_USPS_DAYS;
              } elseif ($time == '1') {
                $time .= ' ' . TEXT_USPS_DAY;
              } else {
                $time .= ' ' . TEXT_USPS_DAYS;
              }
              break;
              case 'PARCEL':      $time = ereg('<Days>(.*)</Days>', $transresp[$service], $tregs);
              $time = $tregs[1];
              if ($time == '' || $time == 'No Data') {
                $time = '4 - 7 ' . TEXT_USPS_DAYS;
              } elseif ($time == '1') {
                $time .= ' ' . TEXT_USPS_DAY;
              } else {
                $time .= ' ' . TEXT_USPS_DAYS;
              }
              break;
              case 'FIRST CLASS': $time = '2 - 5 ' . TEXT_USPS_DAYS;
              break;
              default:            $time = '';
              break;
            }
            if ($time != '') $transittime[$service] = ' (' . $time . ')';
          }
          // EOF: UPS USPS
        }
      }
    } else {
      if (ereg('<Error>', $response[0])) {
        $number = ereg('<Number>(.*)</Number>', $response[0], $regs);
        $number = $regs[1];
        $description = ereg('<Description>(.*)</Description>', $response[0], $regs);
        $description = $regs[1];

        return array('error' => $number . ' - ' . $description);
      } else {
        $body = $response[0];
        $services = array();
        while (true) {
          if ($start = strpos($body, '<Service ID=')) {
            $body = substr($body, $start);
            $end = strpos($body, '</Service>');
            $services[] = substr($body, 0, $end+10);
            $body = substr($body, $end+9);
          } else {
            break;
          }
        }

        // BOF: UPS USPS
        //$allowed_types = array();
        //foreach( explode(", ", MODULE_SHIPPING_USPS_TYPES_INTL) as $value ) $allowed_types[$value] = $this->intl_types[$value];
        // EOF: UPS USPS

        $size = sizeof($services);
        for ($i=0, $n=$size; $i<$n; $i++) {
          if (strpos($services[$i], '<Postage>')) {
            $service = ereg('<SvcDescription>(.*)</SvcDescription>', $services[$i], $regs);
            $service = $regs[1];
            $postage = ereg('<Postage>(.*)</Postage>', $services[$i], $regs);
            $postage = $regs[1];
            // BOF: UPS USPS
            $time = ereg('<SvcCommitments>(.*)</SvcCommitments>', $services[$i], $tregs);
            $time = $tregs[1];
            $time = preg_replace('/Weeks$/', TEXT_USPS_WEEKS, $time);
            $time = preg_replace('/Days$/', TEXT_USPS_DAYS, $time);
            $time = preg_replace('/Day$/', TEXT_USPS_DAY, $time);

            //if( !in_array($service, $allowed_types) ) continue;
           
			// Convert the service name to a database field so it can determined if the service
			// should have a quote shown to the customer
			$column_name = null;
			switch ($service) {
				case 'Global Express Guaranteed':
					$column_name = 'GE';
					break;
				case 'Global Express Guaranteed Non-Document Rectangular':
					$column_name = 'GENDR';
					break;
				case 'Global Express Guaranteed Non-Document Non-Rectangular':
					$column_name = 'GENDNR';
					break;
				case 'USPS GXG Envelopes':
					$column_name = 'GEE';
					break;
				case 'Express Mail International (EMS)':
					$column_name = 'EMI';
					break;
				case 'Express Mail International (EMS) Flat-Rate Envelope':
					$column_name = 'EMIFRE';
					break;
				case 'Priority Mail International':
					$column_name = 'PMI';
					break;
				case 'Priority Mail International Flat-Rate Envelope':
					$column_name = 'PMIFRE';
					break;
				case 'Priority Mail International Flat-Rate Box':
					$column_name = 'PMIFRB';
					break;
				case 'Priority Mail International Large Flat Rate Box':
					$column_name = 'PMILFRB';
					break;
				case 'First Class Mail International Large Envelope':
					$column_name = 'FCMILE';
					break;
				case 'First Class Mail International Package':
					$column_name = 'FCMIP';
					break;
				case 'First Class Mail International Letters':
					$column_name = 'FCMIL';
					break;
				case 'First Class Mail International Flats':
					$column_name = 'FCMIF';
					break;
				case 'First Class Mail International Parcels':
					$column_name = 'FCMIPAR';
					break;
			}
			if (is_null($column_name) || 
					$this->_config['international_' . strtolower($column_name)] == 0) {
				continue;
			}
			
			//if ($_SESSION['cart']->total > 400 && strstr($services[$i], 'Priority Mail International Flat Rate Envelope')) continue; // skip value > $400 Priority Mail International Flat Rate Envelope
			if ($this->_price > 400 && strstr($services[$i], 'Priority Mail International Flat Rate Envelope')) continue; // skip value > $400 Priority Mail International Flat Rate Envelope
			
            // EOF: UPS USPS
            if (isset($this->service) && ($service != $this->service) ) {
              continue;
            }

            $rates[] = array($service => $postage);
            // BOF: UPS USPS
            if ($time != '') $transittime[$service] = ' (' . $time . ')';
            // EOF: UPS USPS
          }
        }
      }
    }

    return ((sizeof($rates) > 0) ? $rates : false);
  }
  /**
   * USPS Country Code List
   * This list is used to compare the 2-letter ISO code against the order country ISO code, and provide the proper/expected
   * spelling of the country name to USPS in order to obtain a rate quote
   *
   * @return array
   */
  function country_list() {
    $list = array('AF' => 'Afghanistan',
    'AL' => 'Albania',
    'DZ' => 'Algeria',
    'AD' => 'Andorra',
    'AO' => 'Angola',
    'AI' => 'Anguilla',
    'AG' => 'Antigua and Barbuda',
    'AR' => 'Argentina',
    'AM' => 'Armenia',
    'AW' => 'Aruba',
    'AU' => 'Australia',
    'AT' => 'Austria',
    'AZ' => 'Azerbaijan',
    'BS' => 'Bahamas',
    'BH' => 'Bahrain',
    'BD' => 'Bangladesh',
    'BB' => 'Barbados',
    'BY' => 'Belarus',
    'BE' => 'Belgium',
    'BZ' => 'Belize',
    'BJ' => 'Benin',
    'BM' => 'Bermuda',
    'BT' => 'Bhutan',
    'BO' => 'Bolivia',
    'BA' => 'Bosnia-Herzegovina',
    'BW' => 'Botswana',
    'BR' => 'Brazil',
    'VG' => 'British Virgin Islands',
    'BN' => 'Brunei Darussalam',
    'BG' => 'Bulgaria',
    'BF' => 'Burkina Faso',
    'MM' => 'Burma',
    'BI' => 'Burundi',
    'KH' => 'Cambodia',
    'CM' => 'Cameroon',
    'CA' => 'Canada',
    'CV' => 'Cape Verde',
    'KY' => 'Cayman Islands',
    'CF' => 'Central African Republic',
    'TD' => 'Chad',
    'CL' => 'Chile',
    'CN' => 'China',
    'CX' => 'Christmas Island (Australia)',
    'CC' => 'Cocos Island (Australia)',
    'CO' => 'Colombia',
    'KM' => 'Comoros',
    'CG' => 'Congo (Brazzaville),Republic of the',
    'ZR' => 'Congo, Democratic Republic of the',
    'CK' => 'Cook Islands (New Zealand)',
    'CR' => 'Costa Rica',
    'CI' => 'Cote d\'Ivoire (Ivory Coast)',
    'HR' => 'Croatia',
    'CU' => 'Cuba',
    'CY' => 'Cyprus',
    'CZ' => 'Czech Republic',
    'DK' => 'Denmark',
    'DJ' => 'Djibouti',
    'DM' => 'Dominica',
    'DO' => 'Dominican Republic',
    'TP' => 'East Timor (Indonesia)',
    'EC' => 'Ecuador',
    'EG' => 'Egypt',
    'SV' => 'El Salvador',
    'GQ' => 'Equatorial Guinea',
    'ER' => 'Eritrea',
    'EE' => 'Estonia',
    'ET' => 'Ethiopia',
    'FK' => 'Falkland Islands',
    'FO' => 'Faroe Islands',
    'FJ' => 'Fiji',
    'FI' => 'Finland',
    'FR' => 'France',
    'GF' => 'French Guiana',
    'PF' => 'French Polynesia',
    'GA' => 'Gabon',
    'GM' => 'Gambia',
    'GE' => 'Georgia, Republic of',
    'DE' => 'Germany',
    'GH' => 'Ghana',
    'GI' => 'Gibraltar',
    'GB' => 'Great Britain and Northern Ireland',
    'GR' => 'Greece',
    'GL' => 'Greenland',
    'GD' => 'Grenada',
    'GP' => 'Guadeloupe',
    'GT' => 'Guatemala',
    'GN' => 'Guinea',
    'GW' => 'Guinea-Bissau',
    'GY' => 'Guyana',
    'HT' => 'Haiti',
    'HN' => 'Honduras',
    'HK' => 'Hong Kong',
    'HU' => 'Hungary',
    'IS' => 'Iceland',
    'IN' => 'India',
    'ID' => 'Indonesia',
    'IR' => 'Iran',
    'IQ' => 'Iraq',
    'IE' => 'Ireland',
    'IL' => 'Israel',
    'IT' => 'Italy',
    'JM' => 'Jamaica',
    'JP' => 'Japan',
    'JO' => 'Jordan',
    'KZ' => 'Kazakhstan',
    'KE' => 'Kenya',
    'KI' => 'Kiribati',
    'KW' => 'Kuwait',
    'KG' => 'Kyrgyzstan',
    'LA' => 'Laos',
    'LV' => 'Latvia',
    'LB' => 'Lebanon',
    'LS' => 'Lesotho',
    'LR' => 'Liberia',
    'LY' => 'Libya',
    'LI' => 'Liechtenstein',
    'LT' => 'Lithuania',
    'LU' => 'Luxembourg',
    'MO' => 'Macao',
    'MK' => 'Macedonia, Republic of',
    'MG' => 'Madagascar',
    'MW' => 'Malawi',
    'MY' => 'Malaysia',
    'MV' => 'Maldives',
    'ML' => 'Mali',
    'MT' => 'Malta',
    'MQ' => 'Martinique',
    'MR' => 'Mauritania',
    'MU' => 'Mauritius',
    'YT' => 'Mayotte (France)',
    'MX' => 'Mexico',
    'MD' => 'Moldova',
    'MC' => 'Monaco (France)',
    'MN' => 'Mongolia',
    'MS' => 'Montserrat',
    'MA' => 'Morocco',
    'MZ' => 'Mozambique',
    'NA' => 'Namibia',
    'NR' => 'Nauru',
    'NP' => 'Nepal',
    'NL' => 'Netherlands',
    'AN' => 'Netherlands Antilles',
    'NC' => 'New Caledonia',
    'NZ' => 'New Zealand',
    'NI' => 'Nicaragua',
    'NE' => 'Niger',
    'NG' => 'Nigeria',
    'KP' => 'North Korea (Korea, Democratic People\'s Republic of)',
    'NO' => 'Norway',
    'OM' => 'Oman',
    'PK' => 'Pakistan',
    'PA' => 'Panama',
    'PG' => 'Papua New Guinea',
    'PY' => 'Paraguay',
    'PE' => 'Peru',
    'PH' => 'Philippines',
    'PN' => 'Pitcairn Island',
    'PL' => 'Poland',
    'PT' => 'Portugal',
    'QA' => 'Qatar',
    'RE' => 'Reunion',
    'RO' => 'Romania',
    'RU' => 'Russia',
    'RW' => 'Rwanda',
    'SH' => 'Saint Helena',
    'KN' => 'Saint Kitts (St. Christopher and Nevis)',
    'LC' => 'Saint Lucia',
    'PM' => 'Saint Pierre and Miquelon',
    'VC' => 'Saint Vincent and the Grenadines',
    'SM' => 'San Marino',
    'ST' => 'Sao Tome and Principe',
    'SA' => 'Saudi Arabia',
    'SN' => 'Senegal',
    'YU' => 'Serbia-Montenegro',
    'SC' => 'Seychelles',
    'SL' => 'Sierra Leone',
    'SG' => 'Singapore',
    'SK' => 'Slovak Republic',
    'SI' => 'Slovenia',
    'SB' => 'Solomon Islands',
    'SO' => 'Somalia',
    'ZA' => 'South Africa',
    'GS' => 'South Georgia (Falkland Islands)',
    'KR' => 'South Korea (Korea, Republic of)',
    'ES' => 'Spain',
    'LK' => 'Sri Lanka',
    'SD' => 'Sudan',
    'SR' => 'Suriname',
    'SZ' => 'Swaziland',
    'SE' => 'Sweden',
    'CH' => 'Switzerland',
    'SY' => 'Syrian Arab Republic',
    'TW' => 'Taiwan',
    'TJ' => 'Tajikistan',
    'TZ' => 'Tanzania',
    'TH' => 'Thailand',
    'TG' => 'Togo',
    'TK' => 'Tokelau (Union) Group (Western Samoa)',
    'TO' => 'Tonga',
    'TT' => 'Trinidad and Tobago',
    'TN' => 'Tunisia',
    'TR' => 'Turkey',
    'TM' => 'Turkmenistan',
    'TC' => 'Turks and Caicos Islands',
    'TV' => 'Tuvalu',
    'UG' => 'Uganda',
    'UA' => 'Ukraine',
    'AE' => 'United Arab Emirates',
    'UY' => 'Uruguay',
    'UZ' => 'Uzbekistan',
    'VU' => 'Vanuatu',
    'VA' => 'Vatican City',
    'VE' => 'Venezuela',
    'VN' => 'Vietnam',
    'WF' => 'Wallis and Futuna Islands',
    'WS' => 'Western Samoa',
    'YE' => 'Yemen',
    'ZM' => 'Zambia',
    'ZW' => 'Zimbabwe');

    return $list;
  }
}
?>