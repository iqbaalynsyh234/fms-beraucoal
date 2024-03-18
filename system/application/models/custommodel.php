<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Custommodel extends Model {
	
	function Custommodel(){
		parent::Model();
		$this->load->library('email');
		$this->load->helper('email');
	}
	
	function sendemail($to,$cc,$noreply,$subject,$msg)
    {
        $headers = "";
        $headers .= 'MIME-Version: 1.0' . "\r\n";
        //$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n"; 
		$headers .= "X-Priority: 1\r\n"; 
        $headers .= 'Bcc: '.$cc. "\r\n";
		$headers .=  'From: '.$noreply. "\r\n" . 'X-Mailer: PHP/' . phpversion();
		
        mail($to, $subject, $msg, $headers);
        return true;
    }
	
	
}
