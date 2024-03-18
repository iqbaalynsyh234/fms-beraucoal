<?php

include 'config_contact.php';

error_reporting (E_ALL ^ E_NOTICE);

$post = (!empty($_POST)) ? true : false;

if($post)
{
include 'functions.php';

$name = stripslashes($_POST['name']);
$message = stripslashes($_POST['message']);

$error = '';


if(!$name)
{
$error .= 'Mohon Untuk Mengisi Nama Anda.<br />';
}

if(!$message || strlen($message) < 5)
{
$error .= "Masukkan Pesan Anda. Minimal 5 Karakter<br />";
}

if(!$error)
{
$mail = mail(WEBMASTER_EMAIL, 'Feedback', $message,
     "From: ".$email."\r\n"
    ."Reply-To: ".$email."\r\n"
    ."X-Mailer: PHP/" . phpversion());

if($mail)
{
echo 'OK';
}

}
else
{
echo '<div class="notification_error">'.$error.'</div>';
}

}
?>