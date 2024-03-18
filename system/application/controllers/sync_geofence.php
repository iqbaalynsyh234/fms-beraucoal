<?php
include "base.php";

class Sync_geofence extends Base {
	function __construct()
	{
		parent::__construct();	
		
		$this->load->model("gpsmodel");
		$this->load->model("smsmodel");
		$this->load->model("configmodel");
		$this->load->library('email');
		$this->load->helper('email');
		$this->load->helper('common');
		
	}
	
	function sync_table($source, $target, $source_table, $target_table, $fieldid, $limit, $lastid, $fielduser, $userid)
	{
		ini_set('memory_limit', '5G');
		printf("Finish ----- \n");
		$this->db = $this->load->database($source,true); //webtracking
		$this->dbtarget = $this->load->database($target,true); //DB live 5 trial
		
		printf("Get Data from DB %s ", $source);
		$this->db->order_by($fieldid,"asc");
		$this->db->where($fieldid." >", $lastid);
		$this->db->where($fielduser, $userid);
		$this->db->limit($limit);
		$q = $this->db->get($source_table);
		$data = $q->result();
		$total = $q->num_rows;	
		
		printf(" - Total Data %s \n", $total);
		$datas = $q->result_array();
		
		$lastdata = 0;
		printf("Proses Insert Data ----- \n");
		foreach($datas as $d)
		{
			$lastdata = $d[$fieldid];
			$this->dbtarget->insert($target_table, $d);
		}
		
		printf("Last ID %s ----- \n",$lastdata);
		printf("Finish ----- \n");
	}
	
	function sync_table_desc($source, $target, $source_table, $target_table, $fieldid, $limit, $lastid)
	{
		ini_set('memory_limit', '3G');
		printf("Finish ----- \n");
		$this->db = $this->load->database($source,true); //webtracking
		$this->dbtarget = $this->load->database($target,true); //dblocation_balrich
		
		printf("Get Data from DB %s ", $source);
		$this->db->order_by($fieldid,"desc");
		$this->db->where($fieldid." <", $lastid);
		$this->db->limit($limit);
		$q = $this->db->get($source_table);
		$data = $q->result();
		$total = $q->num_rows;	
		
		printf(" - Total Data %s \n", $total);
		$datas = $q->result_array();
		
		$lastdata = 0;
		printf("Proses Insert Data ----- \n");
		foreach($datas as $d)
		{
			$lastdata = $d[$fieldid];
			$this->dbtarget->insert($target_table, $d);
		}
		
		printf("Last ID %s ----- \n",$lastdata);
		printf("Finish ----- \n");
	}
	
	function sync_table_byid($source, $target, $source_table, $target_table, $fieldid, $id)
	{
		ini_set('memory_limit', '3G');
		printf("Finish ----- \n");
		$this->db = $this->load->database($source,true); //webtracking
		$this->dbtarget = $this->load->database($target,true); //dblocation_balrich
		
		printf("Get Data from DB %s ", $source);
		$this->db->order_by($fieldid,"asc");
		$this->db->where($fieldid, $id);
		$this->db->limit(1);
		$q = $this->db->get($source_table);
		$data = $q->result();
		$total = $q->num_rows;	
		
		printf(" - Total Data %s \n", $total);
		$datas = $q->result_array();
		
		$lastdata = 0;
		printf("Proses Insert Data ----- \n");
		foreach($datas as $d)
		{
			$lastdata = $d[$fieldid];
			$this->dbtarget->insert($target_table, $d);
		}
		
		printf("Last ID %s ----- \n",$lastdata);
		printf("Finish ----- \n");
	}
	
	function telegram_direct($groupid,$message)
    {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        
        $url = "http://lacak-mobil.com/telegram/telegram_directpost";
        
        $data = array("id" => $groupid, "message" => $message);
        $data_string = json_encode($data);
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POST, true);                                                           
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                 
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);                        
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($data_string)));  
        $result = curl_exec($ch);
        
        if ($result === FALSE) {
            die("Curl failed: " . curL_error($ch));
        }
        echo $result;
        echo curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
    }
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
