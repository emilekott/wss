<?php
require_once 'lookup.php';

// Set error reporting level
ini_set('display_errors', 1);
error_reporting(E_ALL & ~(E_STRICT|E_NOTICE));

// Declare the important variables
$referrer2 = '';
$email = '';
$email_to = '';
$name = '';
$telephone = '';
$message = '';

$referrer2 = $_POST['referrer2'];
$email = $_POST['email'];
$name = $_POST['name'];
$telephone = $_POST['telephone'];
$message = $_POST['message'];

if (empty($referrer2)) {
?>
	<script type="text/javascript">
	window.alert("Internal Error - no referring page! (in DoEmail)");
	history.back();
	</script>
<?php
	exit();
}

$email_to = '';
lookupReferrer($referrer2);

if (empty($email_to)) {  // Couldn't find an e-mail address in lookup.php
?>
	<script type="text/javascript">
	window.alert("Internal Error - invalid 'referrer2' (in DoEmail)");
	history.back();
	</script>
<?php
	exit();
}

//$email_from = "info@witteringsurfshop.com";
$email_from = 'andrew@beammicrosystems.com';

// In case any of our lines are longer than 70 characters, use wordwrap()
$message = wordwrap($message, 70, PHP_EOL);

$headers1 = "From: $email";
$message1 = "Wittering Surf Shop Kiosk Enquiry Form
______________________________________

Name: $name
E-mail: $email
Phone: $telephone
Message: $message
";

$headers2 = "From: " . $email_to . "\r\n" . "Reply-To: " . $email_to . "\r\n";

$message2 = "Thank you for using the Wittering Surf Shop kiosk.\n
We will get back to you shortly.
For your reference, the text of your message is included below.\n
Sincerely,\n
http://www.witteringsurfshop.co.uk\n\n
Your original enquiry was:\n
$message\n\n
The Wittering Surf Shop would like to keep in touch so we can tell you about news,
forthcoming events or changes to our website. We always aim to send only relevant
information but if you don\'t want to receive these messages from us you can unsubscribe
at anytime - see http://www.witteringsurfshop.com/privacy.html
";

$headers3 = "From: " . $email_to . "\r\n" . "Reply-To: " . $email . "\r\n";

$message3 = "In an enquiry about: $contactTitle...\n
Name: $name\n
E-mail: $email\n
Phone: $telephone\n
sent the following message to: $email_to\n\n
$message
";

$sub1 = "Wittering Surf Shop Kiosk Enquiry Form";
$sub2 = "Re: Wittering Surf Shop Kiosk Enquiry Form";
$sub3 = "Wittering Surf Shop Kiosk Enquiry Form [shop copy]";

$message1 = stripslashes($message1);
$message2 = stripslashes($message2);
$message3 = stripslashes($message3);
$message1 = strip_tags($message1);
$message2 = strip_tags($message2);
$message3 = strip_tags($message3);

$sent1 = mail($email_to, $sub1, $message1, $headers1);  // Send to contact company
$sent2 = mail($email, $sub2, $message2, $headers2);  // Copy to originator
$sent3 = mail($email_from, $sub3, $message3, $headers3);  // Copy to Nick

if (($sent1 == true) && ($sent2 == true) && ($sent3 == true)) {
	echo "Mail sent";
}
else {
	echo "Mail failed";
}
?>