<?php

include 'config_contact.php';

error_reporting (E_ALL ^ E_NOTICE);

$post = (!empty($_POST)) ? true : false;

if($post)
{
include 'functions.php';

$name = stripslashes($_POST['name']);
$perusahaan = stripslashes($_POST['perusahaan']);
$jabatan = stripslashes($_POST['jabatan']);
$email = trim($_POST['email']);
$telp = trim($_POST['telp']);
$hp = trim($_POST['hp']);
$alamat = stripslashes($_POST['alamat']);
$subject = stripslashes($_POST['subject']);
$message = stripslashes($_POST['message']);

$error = '';

if(!$name)
{
$error .= 'Mohon Untuk Mengisi Nama Anda.<br />';
}

if(!$email)
{
$error .= 'Mohon Masukkan Alamat Email Anda.<br />';
}

if($email && !ValidateEmail($email))
{
$error .= 'Mohon Masukkan Alamat Email Anda Dengan Benar.<br />';
}

if(!$message || strlen($message) < 5)
{
$error .= "Masukkan Pesan Anda. Minimal 5 Karakter.<br />";
}


if(!$error)
{

$mess = "You recieved an email!!";
$mess = "Nama :" .$name . "\n";
$mess .= "Perusahaan: " . $perusahaan . "\n";
$mess .= "Jabatan: " . $jabatan . "\n";
$mess .= "Email: " . $email . "\n";
$mess .= "Telp: " . $telp . "\n";
$mess .= "HP: " . $hp . "\n";
$mess .= "Alamat: " . $alamat . "\n";
$mess .= "Subject: " . $subject . "\n";
$mess .= "Pesan: " . $message . "\n";

$mail = mail(WEBMASTER_EMAIL, $subject, $mess, "From: $email");

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