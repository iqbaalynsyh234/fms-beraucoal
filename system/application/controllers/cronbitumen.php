<?php
include "base.php";

class Cronbitumen extends Base {

	function Cronbitumen()
	{
		parent::Base();	
		$this->load->model("gpsmodel");
        $this->load->model("vehiclemodel");
        $this->load->model("configmodel");
        $this->load->helper('common_helper');
        $this->load->helper('kopindosat');
        $this->load->model("historymodel");
	}
    
    function mirroring()
    {
		 printf("PROSES MIRRORING DB PTPLM_BMI >> START \r\n");
         $startproses = date("Y-m-d H:i:s");
         
         printf("LOAD DB FROM LACAK MOBIL >> START \r\n");
         $this->db = $this->load->database("GPS_BITUMEN_60010",true);
         
         printf("GET DATA GPS FROM LACAK MOBIL >> START \r\n");
         $this->db->order_by("gps_id","asc");
		 $this->db->limit(1000);
         $this->db->where("gps_sent",0);
         $q = $this->db->get("gps");
         $mygps = $q->result();
         $datagps = $q->result_array();
         
         printf("GET DATA INFO GPS FROM LACAK MOBIL >> START \r\n");
         $this->db->order_by("gps_info_id","asc");
		 $this->db->limit(1000);
         $this->db->where("gps_info_sent",0);
         $q = $this->db->get("gps_info");
         $myinfo = $q->result();
         $datainfo = $q->result_array();
         
         printf("LOAD DB PTPLM_BMI >> START \r\n");
         $historydb = $this->load->database("ptplm_bmi", TRUE);
         
         printf("INSERT DATA GPS TO DB PTPLM_BMI >> START \r\n");
         printf("TOTAL DATA GPS (%s) >> START \r\n",count($mygps));
         foreach($datagps as $gps)
         {
			unset($gps['gps_id']);
			$historydb->insert("gps", $gps);
		 }
		 
		 printf("UPDATE STATUS DATA GPS RECORD DB LACAK >> START \r\n");
		 for($i=0;$i<count($mygps);$i++)
		 {
			unset($dataupdate);
			$dataupdate["gps_sent"] = 1;
			$this->db->where("gps_id",$mygps[$i]->gps_id);
			$this->db->update("gps",$dataupdate);
		 }
		 
		 printf("INSERT DATA INFO TO DB PTPLM_BMI >> START \r\n");
		 printf("TOTAL DATA INFO (%s) >> START \r\n",count($myinfo));
         foreach($datainfo as $gpsinfo)
         {
			unset($gpsinfo['gps_info_id']);
			$historydb->insert("gps_info", $gpsinfo);
		 }
			
		 printf("UPDATE STATUS DATA INFO RECORD DB LACAK >> START \r\n");
		 for($i=0;$i<count($myinfo);$i++)
		 {
			unset($dataupdate);
			$dataupdate["gps_info_sent"] = 1;
			$this->db->where("gps_info_id",$myinfo[$i]->gps_info_id);
			$this->db->update("gps_info",$dataupdate);
		 }
		 
		 printf("PROSES FINISH \r\n");
		 printf(":):):):):):):):):):P \r\n");
	}
   
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
