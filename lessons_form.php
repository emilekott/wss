<?php
// contact form
    if(isset($_POST['formgo'])):

        //to address
        $toaddr = 'info@witteringsurfshop.com';

        //subject line
        $subject = '[Wittering Surf Shop] New Course Application';

        //sanitise the input
        $c_name = filter_var($_POST['name'],FILTER_SANITIZE_STRING);
        $c_email = filter_var($_POST['email'],FILTER_SANITIZE_EMAIL);
        $c_tel = filter_var($_POST['tel'],FILTER_SANITIZE_STRING);
        $c_course = filter_var($_POST['course'],FILTER_SANITIZE_STRING);

        //send the mail
        if(filter_var($c_email,FILTER_VALIDATE_EMAIL)||strlen($c_tel)>0):
                $c_message = "A new message has been sent via the contact form on the Lessons page.\n\nFrom: ".$c_name."\nEmail: ".$c_email."\nTel: ".$c_tel."\nCourse: ".$c_course;
                @mail($toaddr,$subject,$c_message,'From: '.$c_name.'<'.$c_email.'>');
                ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en">
<head>
<title>Message Sent</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<script>
    alert("Thanks, we have received your message and will be in touch shortly.");
    window.location='/page.html?id=18';
</script>
</head>
    <body>
    </body>
</html>

                <?php
        else:
            header('Location: /page.html?id=18');
        endif;

    endif;
    //end contact form
?>
