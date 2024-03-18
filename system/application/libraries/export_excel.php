<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH."/third_party/Classes/PHPExcel.php";
require_once APPPATH."/third_party/Classes/PHPExcel/IOFactory.php";

class Export_excel extends PHPExcel {
    public function __construct() {
        parent::__construct();
    }
}
?>
