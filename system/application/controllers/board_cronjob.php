<?php
include "base.php";

class Board_cronjob extends Base {
	function __construct()
	{
		parent::__construct();	
		
		$this->load->model("gpsmodel");
		$this->load->model("configmodel");
		$this->load->library('email');
		$this->load->helper('email');
		$this->load->helper('common');
		
	}
	
	

	//overspeed by Street
	function overspeed($userid="", $orderby="", $startdate = "", $enddate = ""){
		
		ini_set('memory_limit', '2G');
        printf("BOARD by OVERSPEED >> START \r\n");
        $startproses = date("Y-m-d H:i:s");
		$name = "";
		$host = "";
		
        $report_type = "overspeed";
        $process_date = date("Y-m-d H:i:s");
		$start_time = date("Y-m-d H:i:s");
		$report = "overspeed_";
		$report_new = "board_";
		$model = "street";
		
		//$model_array = array("vehicle","jalur","street","geofence");
		
        if ($startdate == "") {
            $startdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
            $datefilename = date("Ymd", strtotime("yesterday"));
			$month = date("F", strtotime("yesterday"));
			$year = date("Y", strtotime("yesterday"));
        }
        
        if ($startdate != "")
        {
            $datefilename = date("Ymd", strtotime($startdate));     
            $startdate = date("Y-m-d 00:00:00", strtotime($startdate));
			$month = date("F", strtotime($startdate));
			$year = date("Y", strtotime($startdate));
        }
        
        if ($enddate != "")
        {
            $enddate = date("Y-m-d 23:59:59", strtotime($enddate));
        }
        
        if ($enddate == "") {
            $enddate = date("Y-m-d 23:59:59", strtotime("yesterday"));
        }
		
		if ($orderby == "") {
            $orderby = "asc";
        }
		
		switch ($month)
		{
			case "January":
            $dbtable = $report."januari_".$year;
			$dbtable_new = $report_new."januari_".$year;
			break;
			case "February":
            $dbtable = $report."februari_".$year;
			$dbtable_new = $report_new."februari_".$year;
			break;
			case "March":
            $dbtable = $report."maret_".$year;
			$dbtable_new = $report_new."maret_".$year;
			break;
			case "April":
            $dbtable = $report."april_".$year;
			$dbtable_new = $report_new."april_".$year;
			break;
			case "May":
            $dbtable = $report."mei_".$year;
			$dbtable_new = $report_new."mei_".$year;
			break;
			case "June":
            $dbtable = $report."juni_".$year;
			$dbtable_new = $report_new."juni_".$year;
			break;
			case "July":
            $dbtable = $report."juli_".$year;
			$dbtable_new = $report_new."juli_".$year;
			break;
			case "August":
            $dbtable = $report."agustus_".$year;
			$dbtable_new = $report_new."agustus_".$year;
			break;
			case "September":
            $dbtable = $report."september_".$year;
			$dbtable_new = $report_new."september_".$year;
			break;
			case "October":
            $dbtable = $report."oktober_".$year;
			$dbtable_new = $report_new."oktober_".$year;
			break;
			case "November":
            $dbtable = $report."november_".$year;
			$dbtable_new = $report_new."november_".$year;
			break;
			case "December":
            $dbtable = $report."desember_".$year;
			$dbtable_new = $report_new."desember_".$year;
			break;
		}
        
        $sdate = date("Y-m-d H:i:s", strtotime($startdate)); //wita
        $edate = date("Y-m-d H:i:s", strtotime($enddate));  //wita
        $z =0;
		printf("START DATE - END DATE : %s %s \r\n", $sdate, $edate);
		
		$this->db->select("user_id,user_dblive");
		$this->db->where("user_id", $userid);
		$q = $this->db->get("user");
        $rowuser = $q->row();
		
		if(count($rowuser)>0){ 
			$dblive = $rowuser->user_dblive;
		}else{
			printf("NO DATA USER DB LIVE : %s \r\n",$userid);
			return;
		}
		
		$rowstreet = $this->getstreet_bycreator($userid);
		$rowscompany = $this->getcompany_bycreator($userid);
		//print_r($rowstreet);exit();
		//print_r($rowscompany);exit();
        $total_process = count($rowstreet);
		$total_company = count($rowscompany);
        printf("TOTAL PROSES STREET : %s \r\n",$total_process);
        printf("============================================ \r\n");
	
		for($i=0;$i<$total_company;$i++){
				$company_name = $rowscompany[$i]->company_name;
				printf("==PROCESS COMPANY %s %s of %s \r\n",$company_name, $i+1, $total_company);
			for($x=0;$x<count($rowstreet);$x++){
				$street_name = str_replace(",", "", $rowstreet[$x]->street_name);
			
				printf("==PROCESS KOSONGAN %s %s %s of %s \r\n",$company_name, $street_name, $x+1, $total_process);
				
				//cari total overspeed by vehicle
				$this->dbreport = $this->load->database("tensor_report",true); 
				$this->dbreport->order_by("overspeed_report_gps_time","asc");
				$this->dbreport->select("overspeed_report_id");
				$this->dbreport->where("overspeed_report_gps_time >=",$sdate);
				$this->dbreport->where("overspeed_report_gps_time <=", $edate);
				$this->dbreport->where("overspeed_report_speed_status", 1); //valid data
				$this->dbreport->where("overspeed_report_location", $street_name);
				$this->dbreport->where("overspeed_report_jalur", "kosongan");
				$this->dbreport->where("overspeed_report_vehicle_company", $rowscompany[$i]->company_id);
				$this->dbreport->where("overspeed_report_type", 0); //data fix (default) = 0
				$q = $this->dbreport->get($dbtable);

				if ($q->num_rows>0)
				{
					$rows = $q->result(); 
					$total_overspeed = count($rows);
					$overspeed_level_1 = 0;
					$overspeed_level_2 = 0;
					$overspeed_level_3 = 0;
					$overspeed_level_4 = 0;
					$delta_speed = 0;
					
					//hitung 
					/*for($y=0;$y<$total_overspeed;$y++){
						printf("==JALUR %s \r\n", $rows[$y]->overspeed_report_jalur);
						$delta_speed = $rows[$y]->overspeed_report_speed - $rows[$y]->overspeed_report_geofence_limit;
						
						if($delta_speed >= 1 && $delta_speed <= 3){
							$overspeed_level_1 = + $overspeed_level_1 + 1;
							$report_level = 1;
						}else if($delta_speed >= 6 && $delta_speed <= 10){
							$overspeed_level_2 = + $overspeed_level_2 + 1;
							$report_level = 2;
						}else if($delta_speed >= 11 && $delta_speed <= 20){
							$overspeed_level_3 = + $overspeed_level_3 + 1;
							$report_level = 3;
						}else if($delta_speed >= 21 && $delta_speed <= 9999){
							$overspeed_level_4 = + $overspeed_level_4 + 1;
							$report_level = 4;
						}else{
							printf("==INVALID OVERSPEED %s \r\n", $delta_speed);
						}
						printf("==LEVEL OVERSPEED %s : %s \r\n", $report_level,$delta_speed);
					}*/
					
					//$street_coordinate = $rows[0]->overspeed_report_coordinate;
					
					printf("==TOTAL %s \r\n", $total_overspeed);
				}
				else
				{
					$total_overspeed = 0;
					//$street_coordinate = "";
					$report_level = 0;
					printf("==TOTAL %s \r\n", $total_overspeed);
				}
				//end query
				
				$nowtime = date("Y-m-d H:i:s");
					
					unset($datainsert);
					$datainsert["board_report_user_id"] = $userid;
					$datainsert["board_report_company"] = $rowscompany[$i]->company_id;
					$datainsert["board_report_company_name"] = $rowscompany[$i]->company_name;
					$datainsert["board_report_location"] = $street_name;
					//$datainsert["board_report_coordinate"] = $street_coordinate;
					$datainsert["board_report_type"] = $report_type;
					$datainsert["board_report_name"] = $report_type;
					//$datainsert["board_report_level"] = $report_level;
					$datainsert["board_report_model"] = $model;
					$datainsert["board_report_jalur"] = "kosongan";
					$datainsert["board_report_date"] = date("Y-m-d", strtotime($sdate));
					$datainsert["board_report_total"] = $total_overspeed;
					$datainsert["board_report_updated"] = date("Y-m-d H:i:s", strtotime("+1 hour", strtotime($nowtime)));
					
					//get last data
					$this->dbreport->where("board_report_company", $rowscompany[$i]->company_id);
					$this->dbreport->where("board_report_location", $street_name);
					$this->dbreport->where("board_report_date",date("Y-m-d", strtotime($sdate)));
					$this->dbreport->where("board_report_type",$report_type);
					$this->dbreport->where("board_report_model",$model);
					//$this->dbreport->where("board_report_level",$report_level);
					$this->dbreport->where("board_report_jalur","kosongan");
					$q_last = $this->dbreport->get($dbtable_new);
					$row_last = $q_last->row();
					$total_last = count($row_last);
					if($total_last>0){
						
						$this->dbreport->where("board_report_company", $rowscompany[$i]->company_id);
						$this->dbreport->where("board_report_location", $street_name);
						$this->dbreport->where("board_report_date",date("Y-m-d", strtotime($sdate)));
						$this->dbreport->where("board_report_type",$report_type);
						$this->dbreport->where("board_report_model",$model);
						//$this->dbreport->where("board_report_level",$report_level);
						$this->dbreport->where("board_report_jalur","kosongan");
						$this->dbreport->update($dbtable_new,$datainsert);
						printf("==UPDATE OK \r\n");
					}else{
						
						$this->dbreport->insert($dbtable_new,$datainsert);
						printf("==INSERT OK \r\n");
					}
					
			}
			
			//Muatan
			/*
			for($x=0;$x<count($rowstreet);$x++){
				$street_name = str_replace(",", "", $rowstreet[$x]->street_name);
				
				printf("==PROCESS MUATAN %s %s of %s \r\n",$street_name, $x+1, $total_process);
				//cari total overspeed by vehicle
				$this->dbreport = $this->load->database("tensor_report",true); 
				$this->dbreport->order_by("overspeed_report_gps_time","asc");
				$this->dbreport->select("overspeed_report_id");
				$this->dbreport->where("overspeed_report_gps_time >=",$sdate);
				$this->dbreport->where("overspeed_report_gps_time <=", $edate);
				$this->dbreport->where("overspeed_report_speed_status", 1); //valid data
				$this->dbreport->where("overspeed_report_location", $street_name);
				$this->dbreport->where("overspeed_report_jalur", "muatan");
				$this->dbreport->where("overspeed_report_vehicle_company", $rowscompany[$i]->company_id);
				$this->dbreport->where("overspeed_report_type", $typereport); //data fix (default) = 0
				$q = $this->dbreport->get($dbtable);

				if ($q->num_rows>0)
				{
					$rows = $q->result();
					$total_overspeed = count($rows);
					printf("==OVERSPEED %s \r\n", $total_overspeed);
				}else{
					$total_overspeed = 0;
					printf("==OVERSPEED %s \r\n", $total_overspeed);
				}
				//end query
				
					unset($datainsert);
					$datainsert["overspeed_board_vehicle_user_id"] = $userid;
					$datainsert["overspeed_board_street"] = $street_name;
					$datainsert["overspeed_board_jalur"] = "muatan";
					$datainsert["overspeed_board_date"] = date("Y-m-d", strtotime($sdate));
					$datainsert["overspeed_board_type"] = $typereport;
					$datainsert["overspeed_board_model"] = $model;
					$datainsert["overspeed_board_total"] = $total_overspeed;
					$datainsert["overspeed_board_updated"] = date("Y-m-d H:i:s");
					
				
				//get last data
				$this->dbreport->where("overspeed_board_street", $street_name);
				$this->dbreport->where("overspeed_board_date",date("Y-m-d", strtotime($sdate)));
				$this->dbreport->where("overspeed_board_type",$typereport);
				$this->dbreport->where("overspeed_board_model",$model);
				$this->dbreport->where("overspeed_board_jalur","muatan");
				$q_last = $this->dbreport->get($dbtable_new);
				$row_last = $q_last->row();
				$total_last = count($row_last);
				if($total_last>0){
					
					$this->dbreport->where("overspeed_board_street", $street_name);
					$this->dbreport->where("overspeed_board_date", date("Y-m-d", strtotime($sdate)));
					$this->dbreport->where("overspeed_board_type",$typereport);
					$this->dbreport->where("overspeed_board_model",$model);
					$this->dbreport->where("overspeed_board_jalur","muatan");
					$this->dbreport->update($dbtable_new,$datainsert);
					printf("==UPDATE OK \r\n ");
				}else{
					
					$this->dbreport->insert($dbtable_new,$datainsert);
					printf("==INSERT OK \r\n");
				}
				
				
				
			}
			*/
		
		
		}
		
		$this->db->close();
		$this->db->cache_delete_all();
		$this->dbreport->close();
		$this->dbreport->cache_delete_all();
		
	}
	
	function getcompany_bycreator($userid){
		
		$this->db->select("company_id,company_name");	
		$this->db->order_by("company_name", "asc");
		$this->db->where("company_flag ", 0);
		$this->db->where("company_created_by", $userid);
		$q = $this->db->get("company");
		$rows = $q->result();
		//$total_rows = count($rows);
		
		return $rows;
	}
	function getstreet_bycreator($userid){
		$this->db = $this->load->database("default",true); 
		$this->db->group_by("street_name");
		$this->db->select("street_id,street_name");
		$this->db->order_by("street_name", "asc");
		$this->db->where("street_creator", $userid);
		//$this->db->like("street_name", "KM");
		$this->db->where("street_type", 1);
		$q = $this->db->get("street");
        $rows = $q->result();
		
		return $rows;
		
	}
}
	

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
