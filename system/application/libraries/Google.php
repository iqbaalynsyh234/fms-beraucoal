<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
 
set_include_path(APPPATH . 'third_party/vendor/google/apiclient/src/Google/' . PATH_SEPARATOR . get_include_path());
require_once APPPATH . '/third_party/vendor/google/apiclient/src/Google/Client.php';
 
class Google extends Google_Client
{
 
function __construct($params = array())
{
parent::__construct();
}
 
}
?>