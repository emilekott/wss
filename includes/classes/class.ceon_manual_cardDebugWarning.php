<?php

// {{{ class ceon_manual_cardDebugWarning

/**
 * Simply checks if the Ceon Manual Card module is installed and in debug mode so that the user can
 * be alerted!
 */
class ceon_manual_cardDebugWarning extends base {
	
	function ceon_manual_cardDebugWarning()
	{
		global $messageStack;
		
		if (defined('MODULE_PAYMENT_CEON_MANUAL_CARD_DEBUGGING_ENABLED') &&  MODULE_PAYMENT_CEON_MANUAL_CARD_DEBUGGING_ENABLED == 'True') {
			$messageStack->add('header', 'CEON MANUAL CARD IS IN TESTING MODE', 'warning');
		}
	}
}

// }}}
 
?>