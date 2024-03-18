<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['base_url']	= "http://kopindosat.lacak-mobil.com/";
$config['admin_mail'] = "support@lacak-mobil.com";
$config['license'] = "lacak-mobil.com";
$config['web_title'] = "Fleet Management System (c) Kopindosat";
$config['system_name'] = "FLEET MANAGEMENT SYSTEM";
$config['active_icon'] = "<img src='" .$config['base_url'] . "assets/kopindosat/images/accept-icon.png' alt='active' title='active' width='24' border='0'/>";
$config['deactive_icon'] = "<img src='" .$config['base_url'] . "assets/kopindosat/images/no-icon.png' alt='inactive' title='Inactive' width='24' border='0'/>";
$config['driver_photo_path'] = $_SERVER['DOCUMENT_ROOT'] . "/assets/kopindosat/media/foto_driver/";
$config['employee_import_path'] = $_SERVER['DOCUMENT_ROOT'] . "/assets/kopindosat/media/employee_data/";
$config['template'] = 'kopindosat/';