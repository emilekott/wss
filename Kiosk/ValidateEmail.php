<?php
require_once 'is_email.php';

// Set error reporting level
ini_set('display_errors', 1);
error_reporting(E_ALL & ~(E_STRICT|E_NOTICE));

// Get the 'email' variable from javascript
$email = $_POST['email'];
$result = is_email($email, true, true);
echo $result;
?>