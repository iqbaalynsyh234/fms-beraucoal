<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Usession extends CI_Session {

    public $logged_in = FALSE;

    public function  __construct() {
        parent::CI_Session();
        $this->is_logged_in();
    }

    public function is_logged_in()
    {
        $logged = $this->userdata('admin_logged_in');
        $this->logged_in = ($logged) ? TRUE : FALSE;
    }
}
