<?php
/**
 * @package admin
 * @copyright Copyright 2003-2006 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: customers.php 4280 2006-08-26 03:32:55Z drbyte $
 */
 
/**
 * @add customers module
 * @2007/01/17 aerodynamic_hippo 
 */

  require('includes/application_top.php');

  require(DIR_WS_CLASSES . 'currencies.php');
  $currencies = new currencies();

  $action = (isset($_GET['action']) ? $_GET['action'] : '');
  $error = false;
  $processed = false;
  
  
  if(zen_not_null($action))
  {
  //form has been posted
  //starting with code from customers.php file 
		$customers_password = zen_db_prepare_input($_POST['customers_password']);
		$customers_password_confirm = zen_db_prepare_input($_POST['customers_password_confirm']);

  		$customers_firstname = zen_db_prepare_input($_POST['customers_firstname']);
        $customers_lastname = zen_db_prepare_input($_POST['customers_lastname']);
        $customers_email_address = zen_db_prepare_input($_POST['customers_email_address']);
        $customers_telephone = zen_db_prepare_input($_POST['customers_telephone']);
        $customers_fax = zen_db_prepare_input($_POST['customers_fax']);
        $customers_newsletter = zen_db_prepare_input($_POST['customers_newsletter']);
        $customers_group_pricing = (int)zen_db_prepare_input($_POST['customers_group_pricing']);
        $customers_email_format = zen_db_prepare_input($_POST['customers_email_format']);
        $customers_gender = zen_db_prepare_input($_POST['customers_gender']);
        $customers_dob = (empty($_POST['customers_dob']) ? zen_db_prepare_input('0001-01-01 00:00:00') : zen_db_prepare_input($_POST['customers_dob']));
	

        $customers_authorization = zen_db_prepare_input($_POST['customers_authorization']);
        $customers_referral= zen_db_prepare_input($_POST['customers_referral']);
		
		$send_welcome = zen_db_prepare_input($_POST['send_welcome']);

        if (CUSTOMERS_APPROVAL_AUTHORIZATION == 2 and $customers_authorization == 1) {
          $customers_authorization = 2;
          $messageStack->add_session(ERROR_CUSTOMER_APPROVAL_CORRECTION2, 'caution');
        }

        if (CUSTOMERS_APPROVAL_AUTHORIZATION == 1 and $customers_authorization == 2) {
          $customers_authorization = 1;
          $messageStack->add_session(ERROR_CUSTOMER_APPROVAL_CORRECTION1, 'caution');
        }

        $default_address_id = zen_db_prepare_input($_POST['default_address_id']);
        $entry_street_address = zen_db_prepare_input($_POST['entry_street_address']);
        $entry_suburb = zen_db_prepare_input($_POST['entry_suburb']);
        $entry_postcode = zen_db_prepare_input($_POST['entry_postcode']);
        $entry_city = zen_db_prepare_input($_POST['entry_city']);
        $entry_country_id = zen_db_prepare_input($_POST['entry_country_id']);

        $entry_company = zen_db_prepare_input($_POST['entry_company']);
        $entry_state = zen_db_prepare_input($_POST['entry_state']);
        if (isset($_POST['entry_zone_id'])) $entry_zone_id = zen_db_prepare_input($_POST['entry_zone_id']);

		
		if($customers_password != $customers_password_confirm)
		{
			$error = true;
			$entry_password_confirm_error = true;
		}
		else
		{
			$entry_password_confirm_error = false;
		}

        if (strlen($customers_firstname) < ENTRY_FIRST_NAME_MIN_LENGTH) {
          $error = true;
          $entry_firstname_error = true;
        } else {
          $entry_firstname_error = false;
        }


        if (strlen($customers_lastname) < ENTRY_LAST_NAME_MIN_LENGTH) {
          $error = true;
          $entry_lastname_error = true;
        } else {
          $entry_lastname_error = false;
        }
		
		

        if (ACCOUNT_DOB == 'true') {
          if (ENTRY_DOB_MIN_LENGTH >0) {
            if (checkdate(substr(zen_date_raw($customers_dob), 4, 2), substr(zen_date_raw($customers_dob), 6, 2), substr(zen_date_raw($customers_dob), 0, 4))) {
              $entry_date_of_birth_error = false;
            } else {
              $error = true;
              $entry_date_of_birth_error = true;
            }
          } else {
            $customers_dob = '0001-01-01 00:00:00';
          }
        }



        if (strlen($customers_email_address) < ENTRY_EMAIL_ADDRESS_MIN_LENGTH) {
          $error = true;
          $entry_email_address_error = true;
        } else {
          $entry_email_address_error = false;
        }
		
		

        if (!zen_validate_email($customers_email_address)) {
          $error = true;
          $entry_email_address_check_error = true;
        } else {
          $entry_email_address_check_error = false;
        }
		
		

        if (strlen($entry_street_address) < ENTRY_STREET_ADDRESS_MIN_LENGTH) {
          $error = true;
          $entry_street_address_error = true;
        } else {
          $entry_street_address_error = false;
        }




        if (strlen($entry_postcode) < ENTRY_POSTCODE_MIN_LENGTH) {
          $error = true;
          $entry_post_code_error = true;
        } else {
          $entry_post_code_error = false;
        }
		
		

        if (strlen($entry_city) < ENTRY_CITY_MIN_LENGTH) {
          $error = true;
          $entry_city_error = true;
        } else {
          $entry_city_error = false;
        }
		

        if ($entry_country_id == false) {
          $error = true;
          $entry_country_error = true;
        } else {
          $entry_country_error = false;
        }

        if (ACCOUNT_STATE == 'true') {
          if ($entry_country_error == true) {
            $entry_state_error = true;
			
          } else {
            $zone_id = 0;
            $entry_state_error = false;
            $check_value = $db->Execute("select count(*) as total
                                         from " . TABLE_ZONES . "
                                         where zone_country_id = '" . (int)$entry_country_id . "'");

            $entry_state_has_zones = ($check_value->fields['total'] > 0);
            if ($entry_state_has_zones == true) {
              $zone_query = $db->Execute("select zone_id
                                          from " . TABLE_ZONES . "
                                          where zone_country_id = '" . (int)$entry_country_id . "'
                                          and zone_name = '" . zen_db_input($entry_state) . "'");

              if ($zone_query->RecordCount() > 0) {
                $entry_zone_id = $zone_query->fields['zone_id'];
				
              } else {
                $error = true;
                $entry_state_error = true;
              }
            } else {
              if ($entry_state == false) {
                $error = true;
                $entry_state_error = true;
              }
            }
         }
      }
	  
	  

      if (strlen($customers_telephone) < ENTRY_TELEPHONE_MIN_LENGTH) {
        $error = true;
        $entry_telephone_error = true;
      } else {
        $entry_telephone_error = false;
      }

      $check_email = $db->Execute("select customers_email_address
                                   from " . TABLE_CUSTOMERS . "
                                   where customers_email_address = '" . zen_db_input($customers_email_address) . "'
                                   and customers_id != '" . (int)$customers_id . "'");

      if ($check_email->RecordCount() > 0) {
        $error = true;
        $entry_email_address_exists = true;
      } else {
        $entry_email_address_exists = false;
      }
	 

      if ($error == false) {

        $sql_data_array = array('customers_firstname' => $customers_firstname,
                                'customers_lastname' => $customers_lastname,
                                'customers_email_address' => $customers_email_address,
                                'customers_telephone' => $customers_telephone,
                                'customers_fax' => $customers_fax,
                                'customers_group_pricing' => $customers_group_pricing,
                                'customers_newsletter' => $customers_newsletter,
                                'customers_email_format' => $customers_email_format,
                                'customers_authorization' => $customers_authorization,
                                'customers_referral' => $customers_referral,
								'customers_password' => zen_encrypt_password($customers_password),
                                );

        if (ACCOUNT_GENDER == 'true') $sql_data_array['customers_gender'] = $customers_gender;
        if (ACCOUNT_DOB == 'true') $sql_data_array['customers_dob'] = ($customers_dob == '0001-01-01 00:00:00' ? '0001-01-01 00:00:00' : zen_date_raw($customers_dob));

        zen_db_perform(TABLE_CUSTOMERS, $sql_data_array);
		$customer_id = $db->Insert_ID();
        
        if ($entry_zone_id > 0) $entry_state = '';

         $sql_data_array = array('customers_id' => $customer_id,
                            'entry_firstname' => $customers_firstname,
                            'entry_lastname' => $customers_lastname,
                            'entry_street_address' => $entry_street_address,
                            'entry_postcode' => $entry_postcode,
                            'entry_city' => $entry_city,
                            'entry_country_id' => $entry_country_id);

    if (ACCOUNT_GENDER == 'true') $sql_data_array['entry_gender'] = $entry_gender;
    if (ACCOUNT_COMPANY == 'true') $sql_data_array['entry_company'] = $entry_company;
    if (ACCOUNT_SUBURB == 'true') $sql_data_array['entry_suburb'] = $entry_suburb;
    if (ACCOUNT_STATE == 'true') {
      if ($entry_zone_id > 0) {
        $sql_data_array['entry_zone_id'] = $entry_zone_id;
        $sql_data_array['entry_state'] = '';
      } else {
        $sql_data_array['entry_zone_id'] = '0';
        $sql_data_array['entry_state'] = $entry_state;
      }
    }

    zen_db_perform(TABLE_ADDRESS_BOOK, $sql_data_array);

    $address_id = $db->Insert_ID();

	$sql = "update " . TABLE_CUSTOMERS . "
              set customers_default_address_id = '" . (int)$address_id . "'
              where customers_id = '" . (int)$customer_id . "'";

    $db->Execute($sql);

    $sql = "insert into " . TABLE_CUSTOMERS_INFO . "
                          (customers_info_id, customers_info_number_of_logons,
                           customers_info_date_account_created)
              values ('" . (int)$customer_id . "', '0', now())";

    $db->Execute($sql);



		// build the message content
	if($send_welcome == 'send')
	{
		
    $name = $customers_firstname . ' ' . $customers_lastname;

    if (ACCOUNT_GENDER == 'true') {
      if ($customers_gender == 'm') {
        $email_text = sprintf(EMAIL_GREET_MR, $customers_lastname);
      } else {
        $email_text = sprintf(EMAIL_GREET_MS, $customers_lastname);
      }
    } else {
      $email_text = sprintf(EMAIL_GREET_NONE, $customers_firstname);
    }
  	$html_msg['EMAIL_GREETING'] = str_replace('\n','',$email_text);
    $html_msg['EMAIL_FIRST_NAME'] = $customers_firstname;
    $html_msg['EMAIL_LAST_NAME']  = $customers_lastname;

    // initial welcome
    $email_text .=  EMAIL_WELCOME;
	$html_msg['EMAIL_WELCOME'] = str_replace('\n','',EMAIL_WELCOME);
   
    if (NEW_SIGNUP_DISCOUNT_COUPON != '' and NEW_SIGNUP_DISCOUNT_COUPON != '0') {
      $coupon_id = NEW_SIGNUP_DISCOUNT_COUPON;
      $coupon = $db->Execute("select * from " . TABLE_COUPONS . " where coupon_id = '" . $coupon_id . "'");
      $coupon_desc = $db->Execute("select coupon_description from " . TABLE_COUPONS_DESCRIPTION . " where coupon_id = '" . $coupon_id . "' and language_id = '" . $_SESSION['languages_id'] . "'");
      $db->Execute("insert into " . TABLE_COUPON_EMAIL_TRACK . " (coupon_id, customer_id_sent, sent_firstname, emailed_to, date_sent) values ('" . $coupon_id ."', '0', 'Admin', '" . $email_address . "', now() )");

      // if on, add in Discount Coupon explanation
      //        $email_text .= EMAIL_COUPON_INCENTIVE_HEADER .
      $email_text .= "\n" . EMAIL_COUPON_INCENTIVE_HEADER .
      (!empty($coupon_desc->fields['coupon_description']) ? $coupon_desc->fields['coupon_description'] . "\n\n" : '') .
      strip_tags(sprintf(EMAIL_COUPON_REDEEM, ' ' . $coupon->fields['coupon_code'])) . EMAIL_SEPARATOR;
	   $html_msg['COUPON_TEXT_VOUCHER_IS'] = EMAIL_COUPON_INCENTIVE_HEADER ;
      $html_msg['COUPON_DESCRIPTION']     = (!empty($coupon_desc->fields['coupon_description']) ? '<strong>' . $coupon_desc->fields['coupon_description'] . '</strong>' : '');
      $html_msg['COUPON_TEXT_TO_REDEEM']  = str_replace("\n", '', sprintf(EMAIL_COUPON_REDEEM, ''));
      $html_msg['COUPON_CODE']  = $coupon->fields['coupon_code'];
    } //endif coupon

    if (NEW_SIGNUP_GIFT_VOUCHER_AMOUNT > 0) {
      $coupon_code = zen_create_coupon_code();
      $insert_query = $db->Execute("insert into " . TABLE_COUPONS . " (coupon_code, coupon_type, coupon_amount, date_created) values ('" . $coupon_code . "', 'G', '" . NEW_SIGNUP_GIFT_VOUCHER_AMOUNT . "', now())");
      $insert_id = $db->Insert_ID();
      $db->Execute("insert into " . TABLE_COUPON_EMAIL_TRACK . " (coupon_id, customer_id_sent, sent_firstname, emailed_to, date_sent) values ('" . $insert_id ."', '0', 'Admin', '" . $email_address . "', now() )");

      // if on, add in GV explanation
      $email_text .= "\n\n" . sprintf(EMAIL_GV_INCENTIVE_HEADER, $currencies->format(NEW_SIGNUP_GIFT_VOUCHER_AMOUNT)) .
      sprintf(EMAIL_GV_REDEEM, $coupon_code) .
      EMAIL_GV_LINK . zen_href_link(FILENAME_GV_REDEEM, 'gv_no=' . $coupon_code, 'NONSSL', false) . "\n\n" .
      EMAIL_GV_LINK_OTHER . EMAIL_SEPARATOR;
	  $html_msg['GV_WORTH'] = str_replace('\n','',sprintf(EMAIL_GV_INCENTIVE_HEADER, $currencies->format(NEW_SIGNUP_GIFT_VOUCHER_AMOUNT)) );
      $html_msg['GV_REDEEM'] = str_replace('\n','',str_replace('\n\n','<br />',sprintf(EMAIL_GV_REDEEM, '<strong>' . $coupon_code . '</strong>')));
      $html_msg['GV_CODE_NUM'] = $coupon_code;
      $html_msg['GV_CODE_URL'] = str_replace('\n','',EMAIL_GV_LINK . '<a href="' . zen_href_link(FILENAME_GV_REDEEM, 'gv_no=' . $coupon_code, 'NONSSL', false) . '">' . TEXT_GV_NAME . ': ' . $coupon_code . '</a>');
      $html_msg['GV_LINK_OTHER'] = EMAIL_GV_LINK_OTHER;
    } // endif voucher

    // add in regular email welcome text
    $email_text .= "\n\n" . sprintf(EMAIL_TEXT,$customers_password) . EMAIL_CONTACT . EMAIL_GV_CLOSURE;

   
      $html_msg['EMAIL_MESSAGE_HTML']  = str_replace('\n','',sprintf(EMAIL_TEXT,$customers_password));
    $html_msg['EMAIL_CONTACT_OWNER'] = str_replace('\n','',EMAIL_CONTACT);
    $html_msg['EMAIL_CLOSURE']       = nl2br(EMAIL_GV_CLOSURE);

    // include create-account-specific disclaimer
    $email_text .= "\n\n" . sprintf(EMAIL_DISCLAIMER_NEW_CUSTOMER, STORE_OWNER_EMAIL_ADDRESS). "\n\n";
    $html_msg['EMAIL_DISCLAIMER'] = sprintf(EMAIL_DISCLAIMER_NEW_CUSTOMER, '<a href="mailto:' . STORE_OWNER_EMAIL_ADDRESS . '">'. STORE_OWNER_EMAIL_ADDRESS .' </a>');

    // send welcome email
    zen_mail($name, $customers_email_address, EMAIL_SUBJECT, $email_text, STORE_NAME, EMAIL_FROM, $html_msg, 'welcome');

    // send additional emails
    if (SEND_EXTRA_CREATE_ACCOUNT_EMAILS_TO_STATUS == '1' and SEND_EXTRA_CREATE_ACCOUNT_EMAILS_TO !='') {
      

      $extra_info=email_collect_extra_info($name,$email_address, $customers_firstname . ' ' . $customers_lastname , $customers_email_address );
      $html_msg['EXTRA_INFO'] = $extra_info['HTML'];
      zen_mail('', SEND_EXTRA_CREATE_ACCOUNT_EMAILS_TO, SEND_EXTRA_CREATE_ACCOUNT_EMAILS_TO_SUBJECT . ' ' . EMAIL_SUBJECT,$email_text . $extra_info['TEXT'], STORE_NAME, EMAIL_FROM, $html_msg, 'welcome_extra');
    } //endif send extra emails
	}
        zen_redirect(zen_href_link(FILENAME_CUSTOMERS, '', 'NONSSL'));

        } else if ($error == true) {
          $cInfo = new objectInfo($_POST);
          $processed = true;
        }
 
  
  }
  else
  {
  //form has not been posted
  
  
  }

  
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<link rel="stylesheet" type="text/css" href="includes/cssjsmenuhover.css" media="all" id="hoverJS">
<script language="javascript" src="includes/menu.js"></script>
<script language="javascript" src="includes/general.js"></script>
<?php
  if ($action == 'edit' || $action == 'update' || $action == 'add') {
?>
<script language="javascript"><!--

function check_form() {
  var error = 0;
  var error_message = "<?php echo JS_ERROR; ?>";

  var customers_firstname = document.customers.customers_firstname.value;
  var customers_lastname = document.customers.customers_lastname.value;
<?php if (ACCOUNT_COMPANY == 'true') echo 'var entry_company = document.customers.entry_company.value;' . "\n"; ?>
<?php if (ACCOUNT_DOB == 'true') echo 'var customers_dob = document.customers.customers_dob.value;' . "\n"; ?>
  var customers_email_address = document.customers.customers_email_address.value;
  var entry_street_address = document.customers.entry_street_address.value;
  var entry_postcode = document.customers.entry_postcode.value;
  var entry_city = document.customers.entry_city.value;
  var customers_telephone = document.customers.customers_telephone.value;

<?php if (ACCOUNT_GENDER == 'true') { ?>
  if (document.customers.customers_gender[0].checked || document.customers.customers_gender[1].checked) {
  } else {
    error_message = error_message + "<?php echo JS_GENDER; ?>";
    error = 1;
  }
<?php } ?>

  if (customers_firstname == "" || customers_firstname.length < <?php echo ENTRY_FIRST_NAME_MIN_LENGTH; ?>) {
    error_message = error_message + "<?php echo JS_FIRST_NAME; ?>";
    error = 1;
  }

  if (customers_lastname == "" || customers_lastname.length < <?php echo ENTRY_LAST_NAME_MIN_LENGTH; ?>) {
    error_message = error_message + "<?php echo JS_LAST_NAME; ?>";
    error = 1;
  }

<?php if (ACCOUNT_DOB == 'true' && ENTRY_DOB_MIN_LENGTH !='') { ?>
  if (customers_dob == "" || customers_dob.length < <?php echo ENTRY_DOB_MIN_LENGTH; ?>) {
    error_message = error_message + "<?php echo JS_DOB; ?>";
    error = 1;
  }
<?php } ?>

  if (customers_email_address == "" || customers_email_address.length < <?php echo ENTRY_EMAIL_ADDRESS_MIN_LENGTH; ?>) {
    error_message = error_message + "<?php echo JS_EMAIL_ADDRESS; ?>";
    error = 1;
  }

  if (entry_street_address == "" || entry_street_address.length < <?php echo ENTRY_STREET_ADDRESS_MIN_LENGTH; ?>) {
    error_message = error_message + "<?php echo JS_ADDRESS; ?>";
    error = 1;
  }

  if (entry_postcode == "" || entry_postcode.length < <?php echo ENTRY_POSTCODE_MIN_LENGTH; ?>) {
    error_message = error_message + "<?php echo JS_POST_CODE; ?>";
    error = 1;
  }

  if (entry_city == "" || entry_city.length < <?php echo ENTRY_CITY_MIN_LENGTH; ?>) {
    error_message = error_message + "<?php echo JS_CITY; ?>";
    error = 1;
  }

<?php
  if (ACCOUNT_STATE == 'true') {
?>
  if (document.customers.elements['entry_state'].type != "hidden") {
    if (document.customers.entry_state.value == '' || document.customers.entry_state.value.length < <?php echo ENTRY_STATE_MIN_LENGTH; ?> ) {
       error_message = error_message + "<?php echo JS_STATE; ?>";
       error = 1;
    }
  }
<?php
  }
?>

  if (document.customers.elements['entry_country_id'].type != "hidden") {
    if (document.customers.entry_country_id.value == 0) {
      error_message = error_message + "<?php echo JS_COUNTRY; ?>";
      error = 1;
    }
  }

  if (customers_telephone == "" || customers_telephone.length < <?php echo ENTRY_TELEPHONE_MIN_LENGTH; ?>) {
    error_message = error_message + "<?php echo JS_TELEPHONE; ?>";
    error = 1;
  }

  if (error == 1) {
    alert(error_message);
    return false;
  } else {
    return true;
  }
}
//--></script>
<?php
  }
?>
<script type="text/javascript">
  <!--
  function init()
  {
    cssjsmenu('navbar');
    if (document.getElementById)
    {
      var kill = document.getElementById('hoverJS');
      kill.disabled = true;
    }
  }
  // -->
</script>
</head>
<body onLoad="init()">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
<!-- body_text //-->
    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php
    $newsletter_array = array(array('id' => '1', 'text' => ENTRY_NEWSLETTER_YES),
                              array('id' => '0', 'text' => ENTRY_NEWSLETTER_NO));
?>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
            <td class="pageHeading" align="right"><?php echo zen_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo zen_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
	 
      <tr>
	  
	
		 <?php echo zen_draw_form('customers', FILENAME_ADD_CUSTOMERS, zen_get_all_get_params(array('action')) . 'action=add_complete', 'post', 'onsubmit="return check_form(customers);"', true);
           echo zen_hide_session_id(); ?>
        <td class="formAreaTitle"><?php echo CATEGORY_PERSONAL; ?></td>
      </tr>
      <tr>
        <td class="formArea"><table border="0" cellspacing="2" cellpadding="2">
<?php
    if (ACCOUNT_GENDER == 'true') {
?>
          <tr>
            <td class="main"><?php echo ENTRY_GENDER; ?></td>
            <td class="main">
<?php
    if ($error == true && $entry_gender_error == true) {
      echo zen_draw_radio_field('customers_gender', 'm', false, $cInfo->customers_gender) . '&nbsp;&nbsp;' . MALE . '&nbsp;&nbsp;' . zen_draw_radio_field('customers_gender', 'f', false, $cInfo->customers_gender) . '&nbsp;&nbsp;' . FEMALE . '&nbsp;' . ENTRY_GENDER_ERROR;
    } else {
      echo zen_draw_radio_field('customers_gender', 'm', false, $cInfo->customers_gender) . '&nbsp;&nbsp;' . MALE . '&nbsp;&nbsp;' . zen_draw_radio_field('customers_gender', 'f', false, $cInfo->customers_gender) . '&nbsp;&nbsp;' . FEMALE;
    }
?></td>
          </tr>
<?php
    }
?>

<?php
  $customers_authorization_array = array(array('id' => '0', 'text' => CUSTOMERS_AUTHORIZATION_0),
                                array('id' => '1', 'text' => CUSTOMERS_AUTHORIZATION_1),
                                array('id' => '2', 'text' => CUSTOMERS_AUTHORIZATION_2),
                                array('id' => '3', 'text' => CUSTOMERS_AUTHORIZATION_3)
                                );
//                                 array('id' => '3', 'text' => CUSTOMERS_AUTHORIZATION_3)

?>
          <tr>
            <td class="main"><?php echo CUSTOMERS_AUTHORIZATION; ?></td>
            <td class="main">
              <?php echo zen_draw_pull_down_menu('customers_authorization', $customers_authorization_array, $cInfo->customers_authorization); ?>
            </td>
          </tr>

          <tr>
            <td class="main"><?php echo ENTRY_FIRST_NAME; ?></td>
            <td class="main">
<?php
  if ($error == true) {
    if ($entry_firstname_error == true) {
      echo zen_draw_input_field('customers_firstname', $cInfo->customers_firstname, zen_set_field_length(TABLE_CUSTOMERS, 'customers_firstname', 50)) . '&nbsp;' . ENTRY_FIRST_NAME_ERROR;
    } else {
      echo $cInfo->customers_firstname . zen_draw_hidden_field('customers_firstname');
    }
  } else {
    echo zen_draw_input_field('customers_firstname', $cInfo->customers_firstname, zen_set_field_length(TABLE_CUSTOMERS, 'customers_firstname', 50), true);
  }
?></td>
          </tr>
          <tr>
            <td class="main"><?php echo ENTRY_LAST_NAME; ?></td>
            <td class="main">
<?php
  if ($error == true) {
    if ($entry_lastname_error == true) {
      echo zen_draw_input_field('customers_lastname', $cInfo->customers_lastname, zen_set_field_length(TABLE_CUSTOMERS, 'customers_lastname', 50)) . '&nbsp;' . ENTRY_LAST_NAME_ERROR;
    } else {
      echo $cInfo->customers_lastname . zen_draw_hidden_field('customers_lastname');
    }
  } else {
    echo zen_draw_input_field('customers_lastname', $cInfo->customers_lastname, zen_set_field_length(TABLE_CUSTOMERS, 'customers_lastname', 50), true);
  }
?></td>
          </tr>
<?php
    if (ACCOUNT_DOB == 'true') {
?>
          <tr>
            <td class="main"><?php echo ENTRY_DATE_OF_BIRTH; ?></td>
            <td class="main">

<?php
    if ($error == true) {
      if ($entry_date_of_birth_error == true) {
        echo zen_draw_input_field('customers_dob', ($cInfo->customers_dob == '0001-01-01 00:00:00' ? '' : zen_date_short($cInfo->customers_dob)), 'maxlength="10"') . '&nbsp;' . ENTRY_DATE_OF_BIRTH_ERROR;
      } else {
        echo $cInfo->customers_dob . ($customers_dob == '0001-01-01 00:00:00' ? 'N/A' : zen_draw_hidden_field('customers_dob'));
      }
    } else {
      echo zen_draw_input_field('customers_dob', ($customers_dob == '0001-01-01 00:00:00' ? '' : zen_date_short($cInfo->customers_dob)), 'maxlength="10"', true);
    }
?></td>
          </tr>
<?php
    }
?>
          <tr>
            <td class="main"><?php echo ENTRY_EMAIL_ADDRESS; ?></td>
            <td class="main">
<?php
  if ($error == true) {
    if ($entry_email_address_error == true) {
      echo zen_draw_input_field('customers_email_address', $cInfo->customers_email_address, zen_set_field_length(TABLE_CUSTOMERS, 'customers_email_address', 50)) . '&nbsp;' . ENTRY_EMAIL_ADDRESS_ERROR;
    } elseif ($entry_email_address_check_error == true) {
      echo zen_draw_input_field('customers_email_address', $cInfo->customers_email_address, zen_set_field_length(TABLE_CUSTOMERS, 'customers_email_address', 50)) . '&nbsp;' . ENTRY_EMAIL_ADDRESS_CHECK_ERROR;
    } elseif ($entry_email_address_exists == true) {
      echo zen_draw_input_field('customers_email_address', $cInfo->customers_email_address, zen_set_field_length(TABLE_CUSTOMERS, 'customers_email_address', 50)) . '&nbsp;' . ENTRY_EMAIL_ADDRESS_ERROR_EXISTS;
    } else {
      echo $customers_email_address . zen_draw_hidden_field('customers_email_address');
    }
  } else {
    echo zen_draw_input_field('customers_email_address', $cInfo->customers_email_address, zen_set_field_length(TABLE_CUSTOMERS, 'customers_email_address', 50), true);
  }
?></td>
          </tr>
        </table></td>
      </tr>
	  
	   <tr>
        <td><?php echo zen_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr>
        <td class="formAreaTitle"><?php echo CATEGORY_PASSWORD; ?></td>
      </tr>
      <tr>
        <td class="formArea"><table border="0" cellspacing="2" cellpadding="2">
          <tr>
            <td class="main"><?php echo ENTRY_PASSWORD; ?></td>
            <td class="main">
<?php
    if ($error == true) {
      if ($entry_password_error == true) {
        echo zen_draw_password_field('customers_password', $cInfo->customers_password, zen_set_field_length(TABLE_ADDRESS_BOOK, 'customers_password', 50)) . '&nbsp;' . ENTRY_PASSWORD_ERROR;
      } else {
        echo $cInfo->customers_password . zen_draw_hidden_field('customers_password');
      }
    } else {
      echo zen_draw_password_field('customers_password', $cInfo->customers_password, zen_set_field_length(TABLE_ADDRESS_BOOK, 'customers_password', 50));
    }
?></td>
          </tr>
		   <tr>
            <td class="main"><?php echo ENTRY_CONFIRM_PASSWORD; ?></td>
            <td class="main">
<?php
    if ($error == true) {
      if ($entry_password_confirm_error == true) {
        echo zen_draw_password_field('customers_password_confirm', $cInfo->customers_password_confirm, zen_set_field_length(TABLE_ADDRESS_BOOK, 'customers_password_confirm', 50)) . '&nbsp;' . ENTRY_PASSWORD_CONFIRM_ERROR;
      } else {
        echo $cInfo->customers_password_confirm . zen_draw_hidden_field('customers_password_confirm');
      }
    } else {
      echo zen_draw_password_field('customers_password_confirm', $cInfo->customers_password_confirm, zen_set_field_length(TABLE_ADDRESS_BOOK, 'customers_password_confirm', 50));
    }
?></td>
          </tr>
        </table></td>
      </tr>
	  
<?php
    if (ACCOUNT_COMPANY == 'true') {
?>
      <tr>
        <td><?php echo zen_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr>
        <td class="formAreaTitle"><?php echo CATEGORY_COMPANY; ?></td>
      </tr>
      <tr>
        <td class="formArea"><table border="0" cellspacing="2" cellpadding="2">
          <tr>
            <td class="main"><?php echo ENTRY_COMPANY; ?></td>
            <td class="main">
<?php
    if ($error == true) {
      if ($entry_company_error == true) {
        echo zen_draw_input_field('entry_company', $cInfo->entry_company, zen_set_field_length(TABLE_ADDRESS_BOOK, 'entry_company', 50)) . '&nbsp;' . ENTRY_COMPANY_ERROR;
      } else {
        echo $cInfo->entry_company . zen_draw_hidden_field('entry_company');
      }
    } else {
      echo zen_draw_input_field('entry_company', $cInfo->entry_company, zen_set_field_length(TABLE_ADDRESS_BOOK, 'entry_company', 50));
    }
?></td>
          </tr>
        </table></td>
      </tr>
<?php
    }
?>
      <tr>
        <td><?php echo zen_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr>
        <td class="formAreaTitle"><?php echo CATEGORY_ADDRESS; ?></td>
      </tr>
      <tr>
        <td class="formArea"><table border="0" cellspacing="2" cellpadding="2">
          <tr>
            <td class="main"><?php echo ENTRY_STREET_ADDRESS; ?></td>
            <td class="main">
<?php
  if ($error == true) {
    if ($entry_street_address_error == true) {
      echo zen_draw_input_field('entry_street_address', $cInfo->entry_street_address, zen_set_field_length(TABLE_ADDRESS_BOOK, 'entry_street_address', 50)) . '&nbsp;' . ENTRY_STREET_ADDRESS_ERROR;
    } else {
      echo $cInfo->entry_street_address . zen_draw_hidden_field('entry_street_address');
    }
  } else {
    echo zen_draw_input_field('entry_street_address', $cInfo->entry_street_address, zen_set_field_length(TABLE_ADDRESS_BOOK, 'entry_street_address', 50), true);
  }
?></td>
          </tr>
<?php
    if (ACCOUNT_SUBURB == 'true') {
?>
          <tr>
            <td class="main"><?php echo ENTRY_SUBURB; ?></td>
            <td class="main">
<?php
    if ($error == true) {
      if ($entry_suburb_error == true) {
        echo zen_draw_input_field('suburb', $cInfo->entry_suburb, zen_set_field_length(TABLE_ADDRESS_BOOK, 'entry_suburb', 50)) . '&nbsp;' . ENTRY_SUBURB_ERROR;
      } else {
        echo $cInfo->entry_suburb . zen_draw_hidden_field('entry_suburb');
      }
    } else {
      echo zen_draw_input_field('entry_suburb', $cInfo->entry_suburb, zen_set_field_length(TABLE_ADDRESS_BOOK, 'entry_suburb', 50));
    }
?></td>
          </tr>
<?php
    }
?>
          <tr>
            <td class="main"><?php echo ENTRY_POST_CODE; ?></td>
            <td class="main">
<?php
  if ($error == true) {
    if ($entry_post_code_error == true) {
      echo zen_draw_input_field('entry_postcode', $cInfo->entry_postcode, zen_set_field_length(TABLE_ADDRESS_BOOK, 'entry_postcode', 10)) . '&nbsp;' . ENTRY_POST_CODE_ERROR;
    } else {
      echo $cInfo->entry_postcode . zen_draw_hidden_field('entry_postcode');
    }
  } else {
    echo zen_draw_input_field('entry_postcode', $cInfo->entry_postcode, zen_set_field_length(TABLE_ADDRESS_BOOK, 'entry_postcode', 10), true);
  }
?></td>
          </tr>
          <tr>
            <td class="main"><?php echo ENTRY_CITY; ?></td>
            <td class="main">
<?php
  if ($error == true) {
    if ($entry_city_error == true) {
      echo zen_draw_input_field('entry_city', $cInfo->entry_city, zen_set_field_length(TABLE_ADDRESS_BOOK, 'entry_city', 50)) . '&nbsp;' . ENTRY_CITY_ERROR;
    } else {
      echo $cInfo->entry_city . zen_draw_hidden_field('entry_city');
    }
  } else {
    echo zen_draw_input_field('entry_city', $cInfo->entry_city, zen_set_field_length(TABLE_ADDRESS_BOOK, 'entry_city', 50), true);
  }
?></td>
          </tr>
<?php
    if (ACCOUNT_STATE == 'true') {
?>
          <tr>
            <td class="main"><?php echo ENTRY_STATE; ?></td>
            <td class="main">
<?php
    $entry_state = zen_get_zone_name($cInfo->entry_country_id, $cInfo->entry_zone_id, $cInfo->entry_state);
    if ($error == true) {
      if ($entry_state_error == true) {
        if ($entry_state_has_zones == true) {
          $zones_array = array();
          $zones_values = $db->Execute("select zone_name
                                        from " . TABLE_ZONES . "
                                        where zone_country_id = '" . zen_db_input($cInfo->entry_country_id) . "'
                                        order by zone_name");

          while (!$zones_values->EOF) {
            $zones_array[] = array('id' => $zones_values->fields['zone_name'], 'text' => $zones_values->fields['zone_name']);
            $zones_values->MoveNext();
          }
          echo zen_draw_pull_down_menu('entry_state', $zones_array) . '&nbsp;' . ENTRY_STATE_ERROR;
        } else {
          echo zen_draw_input_field('entry_state', zen_get_zone_name($cInfo->entry_country_id, $cInfo->entry_zone_id, $cInfo->entry_state)) . '&nbsp;' . ENTRY_STATE_ERROR;
        }
      } else {
        echo $entry_state . zen_draw_hidden_field('entry_zone_id') . zen_draw_hidden_field('entry_state');
      }
    } else {
      echo zen_draw_input_field('entry_state', zen_get_zone_name($cInfo->entry_country_id, $cInfo->entry_zone_id, $cInfo->entry_state)).'(Full Name)';
    }

?></td>
         </tr>
<?php
    }
?>
          <tr>
            <td class="main"><?php echo ENTRY_COUNTRY; ?></td>
            <td class="main">
<?php
  if ($error == true) {
    if ($entry_country_error == true) {
      echo zen_draw_pull_down_menu('entry_country_id', zen_get_countries(), $cInfo->entry_country_id) . '&nbsp;' . ENTRY_COUNTRY_ERROR;
    } else {
      echo zen_get_country_name($cInfo->entry_country_id) . zen_draw_hidden_field('entry_country_id');
    }
  } else {
    echo zen_draw_pull_down_menu('entry_country_id', zen_get_countries(), $cInfo->entry_country_id);
  }
?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo zen_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr>
        <td class="formAreaTitle"><?php echo CATEGORY_CONTACT; ?></td>
      </tr>
      <tr>
        <td class="formArea"><table border="0" cellspacing="2" cellpadding="2">
          <tr>
            <td class="main"><?php echo ENTRY_TELEPHONE_NUMBER; ?></td>
            <td class="main">
<?php
  if ($error == true) {
    if ($entry_telephone_error == true) {
      echo zen_draw_input_field('customers_telephone', $cInfo->customers_telephone, zen_set_field_length(TABLE_CUSTOMERS, 'customers_telephone', 15)) . '&nbsp;' . ENTRY_TELEPHONE_NUMBER_ERROR;
    } else {
      echo $cInfo->customers_telephone . zen_draw_hidden_field('customers_telephone');
    }
  } else {
    echo zen_draw_input_field('customers_telephone', $cInfo->customers_telephone, zen_set_field_length(TABLE_CUSTOMERS, 'customers_telephone', 15), true);
  }
?></td>
          </tr>
          <tr>
            <td class="main"><?php echo ENTRY_FAX_NUMBER; ?></td>
            <td class="main">
<?php
  if ($processed == true) {
    echo $cInfo->customers_fax . zen_draw_hidden_field('customers_fax');
  } else {
    echo zen_draw_input_field('customers_fax', $cInfo->customers_fax, zen_set_field_length(TABLE_CUSTOMERS, 'customers_fax', 15));
  }
?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo zen_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr>
        <td class="formAreaTitle"><?php echo CATEGORY_OPTIONS; ?></td>
      </tr>
      <tr>
        <td class="formArea"><table border="0" cellspacing="2" cellpadding="2">

      <tr>
        <td class="main"><?php echo ENTRY_EMAIL_PREFERENCE; ?></td>
        <td class="main">
<?php
if ($processed == true) {
  if ($cInfo->customers_email_format) {
    echo $customers_email_format . zen_draw_hidden_field('customers_email_format');
  }
} else {
    $email_pref_text = ($cInfo->customers_email_format == 'TEXT') ? true : false;
  $email_pref_html = !$email_pref_text;
  echo zen_draw_radio_field('customers_email_format', 'HTML', $email_pref_html) . '&nbsp;' . ENTRY_EMAIL_HTML_DISPLAY . '&nbsp;&nbsp;&nbsp;' . zen_draw_radio_field('customers_email_format', 'TEXT', $email_pref_text) . '&nbsp;' . ENTRY_EMAIL_TEXT_DISPLAY ;
}
?></td>
      </tr>
          <tr>
            <td class="main"><?php echo ENTRY_NEWSLETTER; ?></td>
            <td class="main">
<?php
  if ($processed == true) {
    if ($cInfo->customers_newsletter == '1') {
      echo ENTRY_NEWSLETTER_YES;
    } else {
      echo ENTRY_NEWSLETTER_NO;
    }
    echo zen_draw_hidden_field('customers_newsletter');
  } else {
    echo zen_draw_pull_down_menu('customers_newsletter', $newsletter_array, (($cInfo->customers_newsletter == '1') ? '1' : '0'));
  }
?></td>
          </tr>
          <tr>
            <td class="main"><?php echo ENTRY_PRICING_GROUP; ?></td>
            <td class="main">
<?php
  if ($processed == true) {
    if ($cInfo->customers_group_pricing) {
      $group_query = $db->Execute("select group_name, group_percentage from " . TABLE_GROUP_PRICING . " where group_id = '" . $cInfo->customers_group_pricing . "'");
      echo $group_query->fields['group_name'].'&nbsp;'.$group_query->fields['group_percentage'].'%';
    } else {
      echo ENTRY_NONE;
    }
    echo zen_draw_hidden_field('customers_group_pricing', $cInfo->customers_group_pricing);
  } else {
    $group_array_query = $db->execute("select group_id, group_name, group_percentage from " . TABLE_GROUP_PRICING);
    $group_array[] = array('id'=>0, 'text'=>TEXT_NONE);
    while (!$group_array_query->EOF) {
      $group_array[] = array('id'=>$group_array_query->fields['group_id'], 'text'=>$group_array_query->fields['group_name'].'&nbsp;'.$group_array_query->fields['group_percentage'].'%');
      $group_array_query->MoveNext();
    }
    echo zen_draw_pull_down_menu('customers_group_pricing', $group_array, $cInfo->customers_group_pricing);
  }
?></td>
          </tr>

          <tr>
            <td class="main"><?php echo CUSTOMERS_REFERRAL; ?></td>
            <td class="main">
              <?php echo zen_draw_input_field('customers_referral', $cInfo->customers_referral, zen_set_field_length(TABLE_CUSTOMERS, 'customers_referral', 15)); ?>
            </td>
          </tr>
		  
		  
		  
        </table></td>
      </tr>
	   <tr>
        <td><?php echo zen_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
	   <tr>
        <td class="formAreaTitle"><?php echo CATEGORY_EMAIL; ?></td>
      </tr>
      <tr>
        <td class="formArea"><table border="0" cellspacing="2" cellpadding="2">
          <tr>
            <td class="main"><?php echo ENTRY_EMAIL; ?></td>
            <td class="main">
			<? if(isset($_POST['send_welcome']) || $action=='') { ?>
			<input type="checkbox" id="send_welcome" name="send_welcome" value="send" checked />
			<? } else { ?>
			<input type="checkbox" id="send_welcome" value="send" name="send_welcome" />
			<? } ?>
			</td>
          </tr>
		</table>
		</td>
		</tr>
      <tr>
        <td><?php echo zen_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr>
        <td align="right" class="main"><?php echo zen_image_submit('button_insert.gif', IMAGE_UPDATE) . ' <a href="' . zen_href_link(FILENAME_CUSTOMERS, zen_get_all_get_params(array('action')), 'NONSSL') .'">' . zen_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>'; ?></td>
      </tr></form>

    </table></td>
<!-- body_text_eof //-->
  </tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
