
<?php

$to = 'adex@novix.in';
$subject = 'the subject';
$message = 'new file';
$headers = 'From: webmaster@example.com' . "\r\n" .
        'Reply-To: webmaster@example.com' . "\r\n" .
        'X-Mailer: PHP/' . phpversion();

if (mail($to, $subject, $message, $headers)) {
    echo "mail sent";
} else {
    echo "mail sending failed";
}
?>

