<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Log_model extends Model {

	function Log_model ()
	{
		parent::Model();
	}

	function insertlog($user, $name, $action, $type)
    {
		$this->dbts = $this->load->database("webtracking_ts", true);
        unset($insertlog);
		$insertlog["log_name"] = $name;
        $insertlog["log_action"] = $action;
        $insertlog["log_user"] = $user;
		$insertlog["log_type"] = $type;
        $insertlog["log_ip"] = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : "";
        $insertlog["log_target"] = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : "";
        $insertlog["log_created"] = date("Y-m-d H:i:s");
        $insertlog["log_modified"] = date("Y-m-d H:i:s");
        $this->dbts->insert("ts_apps_log",$insertlog);
        return true;
    }
	function insertlog_login($user, $type, $action)
    {
		$this->dbts = $this->load->database("webtracking_ts", true);
        unset($insertlog);
        $insertlog["log_name"] = $type;
        $insertlog["log_action"] = $action;
        $insertlog["log_user"] = $user;
		$insertlog["log_type"] = "FUNCTIONAL";
        $insertlog["log_ip"] = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : "";
        $insertlog["log_target"] = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : "";
        $insertlog["log_created"] = date("Y-m-d H:i:s");
        $insertlog["log_modified"] = date("Y-m-d H:i:s");
        $this->dbts->insert("ts_apps_log",$insertlog);
        return true;
    }













}
