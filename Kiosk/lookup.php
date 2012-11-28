<?php

function lookupReferrer($lookup) {
 global $contactTitle;
 global $email_to;

	switch ($lookup) {
		case 'WaveBandits-Kitesurf':
			$contactTitle = 'WaveBandits kitesurfing';
			$email_to = 'andrew@beammicrosystems.com';
			//$email_to = 'peter@wavebandits.co.uk';
			break;

		case 'X-Train-Surf':
			$contactTitle = 'X-Train surfing';
			$email_to = 'andrew@beammicrosystems.com';
			//$email_to = 'info@x-train.co.uk';
			break;

		case 'X-Train-SUP':
			$contactTitle = 'X-Train Stand Up Paddling';
			$email_to = 'andrew@beammicrosystems.com';
			//$email_to = 'info@x-train.co.uk';
			break;

		case 'X-Train-Windsurf':
			$contactTitle = 'X-Train windsurfing';
			$email_to = 'andrew@beammicrosystems.com';
			//$email_to = 'info@x-train.co.uk';
			break;

		case 'X-Train-Kitesurf':
			$contactTitle = 'X-Train kitesurfing';
			$email_to = 'andrew@beammicrosystems.com';
			//$email_to = 'info@x-train.co.uk';
			break;

		case 'canal':
			$contactTitle = 'Chichester Ship Canal';
			$email_to = 'andrew@beammicrosystems.com';
			//$email_to = 'chichestercanal@gmail.com';
			break;

		case 'butterflies':
			$contactTitle = 'Earnley Butterflies';
			$email_to = 'andrew@beammicrosystems.com';
			//$email_to = 'earnleybutterflies@hotmail.co.uk';
			break;

		case 'wealdanddownland':
			$contactTitle = 'The Weald and Downland Museum';
			$email_to = 'andrew@beammicrosystems.com';
			//$email_to = 'office@wealddown.co.uk';
			break;

		case 'romanpalace':
			$contactTitle = 'Fishbourne Roman Palace';
			$email_to = 'andrew@beammicrosystems.com';
			//$email_to = 'info@sussexpast.co.uk';  // *** No email contact known!!
			break;

		case 'cathedral':
			$contactTitle = 'Chichester Cathedral';
			$email_to = 'andrew@beammicrosystems.com';
			//$email_to = 'enquiry@chichestercathedral.org.uk';
			break;

		case 'harbourconservancy':
			$contactTitle = 'Chichester Harbour Conservancy';
			$email_to = 'andrew@beammicrosystems.com';
			//$email_to = 'info@conservancy.co.uk';
			break;

		default:
			$contactTitle = '** unknown recipient **';
			$email_to = '';
	}
}

?>
