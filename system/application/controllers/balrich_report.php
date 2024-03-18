	<?php
include "base.php";

class Balrich_report extends Base {
	var $otherdb;
	
	function Balrich_report()
	{
		parent::Base();	
		$this->load->model("gpsmodel");
		$this->load->model("vehiclemodel");
		$this->load->model("configmodel");
		$this->load->helper('common_helper');
	}
	
	function index(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		
		$this->db->order_by("vehicle_name", "asc");
		$this->db->order_by("vehicle_no", "asc");
		$this->db->where("vehicle_user_id", $this->sess->user_id);
		$this->db->or_where("vehicle_company", $this->sess->user_company);
		$this->db->where("vehicle_status <>", 3);
		$q = $this->db->get("vehicle");
		
		if ($q->num_rows() == 0)
		{
			redirect(base_url());
		}
		
		$rows = $q->result();
		$this->params["vehicles"] = $rows;
		
		$this->params["content"] = $this->load->view('report/mn_inout_geofence', $this->params, true);		
		$this->load->view("templatesess", $this->params);
	}
	
	function inout_geofence_report()
	{
		
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;
		$sortby = isset($_POST['sortby']) ? $_POST['sortby'] : "geoalert_time";
		$orderby = isset($_POST['orderby']) ? $_POST['orderby'] : "asc";	
		
		$vehicle = $this->input->post("vehicle");
		$startdate = $this->input->post("startdate");
		$enddate = $this->input->post("enddate");
		$shour = $this->input->post("shour");
		$ehour = $this->input->post("ehour");
		
		$this->db->order_by("vehicle_name", "asc");
		$this->db->order_by("vehicle_no", "asc");
		$this->db->where("vehicle_device", $vehicle);
		$q = $this->db->get("vehicle");
		$rowvehicle = $q->row();

		$sdate = date("Y-m-d H:i:s", strtotime($startdate . " " . $shour . ":00"));
		$edate = date("Y-m-d H:i:s", strtotime($enddate ." " . $ehour . ":59"));
		
		$this->db->select("vehicle_no, vehicle_name, geoalert_direction, geoalert_door, geoalert_engine, geoalert_speed,
						   geoalert_lat, geoalert_lng, geoalert_time, geofence_name, geoalert_vehicle_type");
		$this->db->order_by("geoalert_time", "asc");
		$this->db->where("geoalert_time >=", $sdate);
		$this->db->where("geoalert_time <=", $edate);
		$this->db->where("geoalert_vehicle", $vehicle);
		$this->db->join("geofence", "geofence_id = geoalert_geofence", "left");
		$this->db->join("vehicle", "vehicle_device = geoalert_vehicle", "left");

		$q = $this->db->get("geofence_alert_balrich");
		$rows = $q->result();
		
		for($i=0; $i < count($rows); $i++)
		{
			$rows[$i]->geoalert_time_t = dbmaketime($rows[$i]->geoalert_time);	
		}
			
		$params['data'] = $rows;

		$html = $this->load->view('report/result_inout_geofence', $params, true);
		$callback['error'] = false;
		$callback['html'] = $html;
		echo json_encode($callback);
	}

	function inout_geofence_excel()
	{
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;
		$sortby = isset($_POST['sortby']) ? $_POST['sortby'] : "geoalert_time";
		$orderby = isset($_POST['orderby']) ? $_POST['orderby'] : "asc";	
		
		$vehicle = $this->input->post("vehicle");
		$startdate = $this->input->post("startdate");
		$enddate = $this->input->post("enddate");
		$shour = $this->input->post("shour");
		$ehour = $this->input->post("ehour");
		
		$this->db->order_by("vehicle_name", "asc");
		$this->db->order_by("vehicle_no", "asc");
		$this->db->where("vehicle_device", $vehicle);
		$q = $this->db->get("vehicle");
		$rowvehicle = $q->row();

		$sdate = date("Y-m-d H:i:s", strtotime($startdate . " " . $shour . ":00"));
		$edate = date("Y-m-d H:i:s", strtotime($enddate ." " . $ehour . ":59"));
		
		$this->db->select("vehicle_no, vehicle_name, geoalert_direction, geoalert_door, geoalert_engine, geoalert_speed,
						   geoalert_lat, geoalert_lng, geoalert_time, geofence_name, geoalert_vehicle_type");
		$this->db->order_by("geoalert_time", "asc");
		$this->db->where("geoalert_time >=", $sdate);
		$this->db->where("geoalert_time <=", $edate);
		$this->db->where("geoalert_vehicle", $vehicle);
		$this->db->join("geofence", "geofence_id = geoalert_geofence", "left");
		$this->db->join("vehicle", "vehicle_device = geoalert_vehicle", "left");

		$q = $this->db->get("geofence_alert_balrich");
		$data = $q->result();
		
		for($i=0; $i < count($data); $i++)
		{
			$data[$i]->geoalert_time_t = dbmaketime($data[$i]->geoalert_time);	
		}
		
		/** PHPExcel */
        include 'class/PHPExcel.php';
            
        /** PHPExcel_Writer_Excel2007 */
        include 'class/PHPExcel/Writer/Excel2007.php';
            
        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setTitle("InOut Geofence Report");
        $objPHPExcel->getProperties()->setSubject("InOut Geofence Report");
		$objPHPExcel->getProperties()->setDescription("InOut Geofence Report");
		
		$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(1);
        $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.75);
        $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.75);
        $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(1);
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Times New Roman');
        $objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);  
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(45);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(45);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(35);			
        //$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
		
		//Header
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->mergeCells('A1:D1');
        $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'IN-OUT GEOFENCE REPORT');
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('D3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $objPHPExcel->getActiveSheet()->getStyle('D3')->getFont()->setBold(true);
        
		$objPHPExcel->getActiveSheet()->SetCellValue('A5', 'NO');
        $objPHPExcel->getActiveSheet()->SetCellValue('B5', 'KELUAR');
        $objPHPExcel->getActiveSheet()->SetCellValue('C5', 'MASUK');
        $objPHPExcel->getActiveSheet()->SetCellValue('D5', 'DURATION');
        $objPHPExcel->getActiveSheet()->getStyle('A5:D5')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('A5:D5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$j = 0;
			for($i=0; $i < count($data); $i++) 
			{
                if ($data[$i]->geoalert_direction == 2)
                {
				 if ($data[$i]->geoalert_direction == 2) 
                    {
                        $objPHPExcel->getActiveSheet()->SetCellValue('A'.(6+$j), $j+1);
							if(isset($data[$i]->geofence_name)){
								$geofence_name = $data[$i]->geofence_name;
								if(preg_match("/#/", $geofence_name)) {
									$geofence_rute = explode("#",$geofence_name);
									$geofence_name_print = "RUTE: ".$geofence_rute[1];
								}else{
									$geofence_name_print = $geofence_name;
								}
							}else{
								$geofence_name_print = "-";
							}
							
							if ($data[$i]->geoalert_engine == 1){
								$engine = "ON";
							}else{
								$engine = "OFF";
							}
							
						if($data[$i]->geoalert_vehicle_type == "T5DOOR"){
							if ($geofence_name_print) 
							{
								if ($data[$i]->geoalert_door == 1){
										$door = "OPEN";
									}else{
										$door = "CLOSE";
								}									
									
							}
							$objPHPExcel->getActiveSheet()->SetCellValue('B'.(6+$j), $geofence_name_print . " " . "Start date". " " .date("d/m/Y H:i:s", $data[$i]->geoalert_time_t)." Door : ".$door.", ". "Engine : "." ".$engine.","." ".$data[$i]->geoalert_speed." "."kph");
						}
                        else 
                        {
                            $objPHPExcel->getActiveSheet()->SetCellValue('B'.(6+$j), $geofence_name_print . " " . "Start date". " " .date("d/m/Y H:i:s", $data[$i]->geoalert_time_t). " Engine : "." ".$engine.","." ".$data[$i]->geoalert_speed." "."kph");
                        }
					
                    
                    if ($data[$i]->geoalert_direction == 2) 
                    { 
							if(isset($data[$i+1]->geofence_name)){
								$geofence_name = $data[$i+1]->geofence_name;
								if(preg_match("/#/", $geofence_name)) {
									$geofence_rute = explode("#",$geofence_name);
									$geofence_name_print = "RUTE: ".$geofence_rute[1];
								}else{
									$geofence_name_print = $geofence_name;
								}
							}else{
								$geofence_name_print = "-";
							}
							
						if (isset($data[$i+1]->geoalert_engine) && $data[$i+1]->geoalert_engine == 1){
								$engine = "ON";
							}else{
								$engine = "OFF";
							}
							
						if(isset($data[$i+1]->geoalert_vehicle_type) && $data[$i+1]->geoalert_vehicle_type == "T5DOOR"){
							if ($geofence_name_print) 
							{
								if ($data[$i+1]->geoalert_door == 1){
										$door = "OPEN";
									}else{
										$door = "CLOSE";
								}									
									
							}
							$objPHPExcel->getActiveSheet()->SetCellValue('C'.(6+$j), $geofence_name_print . " " . "Finish". " " .date("d/m/Y H:i:s", $data[$i+1]->geoalert_time_t)." Door : ".$door.", ". "Engine : "." ".$engine.","." ".$data[$i+1]->geoalert_speed." "."kph");
						}
						if(isset($data[$i+1]->geoalert_vehicle_type)){
						
							$objPHPExcel->getActiveSheet()->SetCellValue('C'.(6+$j), $geofence_name_print . " " . "Finish". " " .date("d/m/Y H:i:s", $data[$i+1]->geoalert_time_t). " Engine : "." ".$engine.","." ".$data[$i+1]->geoalert_speed." "."kph");	
						}
                    } 
                    
                    if (isset($data[$i+1]->geofence_name) && $data[$i]->geoalert_direction == 2)
                    {
                        $startdate = new DateTime(date("d-m-Y H:i:s", $data[$i]->geoalert_time_t));
                        $enddate = new DateTime(date("d-m-Y H:i:s", $data[$i+1]->geoalert_time_t));
                        $duration = $startdate->diff($enddate);
                        $d_day = $duration->format('%d');
                        $d_hour = $duration->format('%h');
                        $d_minute = $duration->format('%i');
                        $d_second = $duration->format('%s');
                        if (isset($d_day) && ($d_day > 0))
                        {
                            $objPHPExcel->getActiveSheet()->SetCellValue('D'.(6+$j), $d_day ." ". "Day" ." ".$d_hour ." "."Jam" ." ". $d_minute ." ". "Menit" ." ". $d_second ." ". "Detik" );
                        }
                        else if (isset($d_hour) && ($d_hour > 0))
                        {
                            $objPHPExcel->getActiveSheet()->SetCellValue('D'.(6+$j), $d_hour ." "."Jam" ." ". $d_minute ." ". "Menit" ." ". $d_second ." ". "Detik" );
                        }
                        else
                        {
                            $objPHPExcel->getActiveSheet()->SetCellValue('D'.(6+$j), $d_minute ." ". "Menit" ." ". $d_second ." ". "Detik" );
                        }
                    } 
                    $j = $j + 1;  
                }
			}
		}
            
			$styleArray = array(
            'borders' => array(
            'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
            )
            )
            );
			
            $this->db->cache_delete_all();
            
            $objPHPExcel->getActiveSheet()->getStyle('A5:D'.(5+$j))->applyFromArray($styleArray);
            $objPHPExcel->getActiveSheet()->getStyle('A5:D'.(5+$j))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
            $objPHPExcel->getActiveSheet()->getStyle('A5:D'.(5+$j))->getAlignment()->setWrapText(true);
            $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
            @mkdir(REPORT_PATH, DIR_WRITE_MODE); //REPORT_PATH -> config->constant
            $filecreatedname = "InOutGeofenceReport_" . ".xls";
            
            $objWriter->save(REPORT_PATH.$filecreatedname);
        
            $output = '{"success":true,"errMsg":"","filename":"' .base_url() . "assets/media/report/" . $filecreatedname.'"}';
            echo $output;
            return;
		
	}
	
	function mn_dataoperasional()
	{
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		
		$this->db->order_by("vehicle_name", "asc");
		$this->db->order_by("vehicle_no", "asc");		
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_user_id", $this->sess->user_id);
		$this->db->or_where("vehicle_company", $this->sess->user_company);
		$q = $this->db->get("vehicle");
		
		$rows = $q->result();
		$this->params["vehicles"] = $rows;
		$this->params["content"] = $this->load->view('report/mn_operasional', $this->params, true);		
		$this->load->view("templatesess", $this->params);
	}
	
	function dataoperasional()
	{
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		
		$vehicle = $this->input->post("vehicle");
		$startdate = $this->input->post("startdate");
		$enddate = $this->input->post("enddate");
		$engine = $this->input->post("engine");
		$report = "operasional_";
		
		$sdate = date("Y-m-d H:i:s", strtotime($startdate." "."00:00:00"));
		$edate = date("Y-m-d H:i:s", strtotime($enddate." "."23:59:59"));
		
		$m1 = date("F", strtotime($startdate)); 
		$m2 = date("F", strtotime($enddate)); 
		$year = date("Y", strtotime($startdate));
		$year2 = date("Y", strtotime($enddate));
		$rows = array();
		$rows2 = array();
		$total_q = 0;
		$total_q2 = 0;
		
		switch ($m1)
		{
			case "January":
            $dbtable = $report."januari_".$year;
			break;
			case "February":
            $dbtable = $report."februari_".$year;
			break;
			case "March":
            $dbtable = $report."maret_".$year;
			break;
			case "April":
            $dbtable = $report."april_".$year;
			break;
			case "May":
            $dbtable = $report."mei_".$year;
			break;
			case "June":
            $dbtable = $report."juni_".$year;
			break;
			case "July":
            $dbtable = $report."juli_".$year;
			break;
			case "August":
            $dbtable = $report."agustus_".$year;
			break;
			case "September":
            $dbtable = $report."september_".$year;
			break;
			case "October":
            $dbtable = $report."oktober_".$year;
			break;
			case "November":
            $dbtable = $report."november_".$year;
			break;
			case "December":
            $dbtable = $report."desember_".$year;
			break;
		}
		
		switch ($m2)
		{
			case "January":
            $dbtable2 = $report."januari_".$year;
			break;
			case "February":
            $dbtable2 = $report."februari_".$year;
			break;
			case "March":
            $dbtable2 = $report."maret_".$year;
			break;
			case "April":
            $dbtable2 = $report."april_".$year;
			break;
			case "May":
            $dbtable2 = $report."mei_".$year;
			break;
			case "June":
            $dbtable2 = $report."juni_".$year;
			break;
			case "July":
            $dbtable2 = $report."juli_".$year;
			break;
			case "August":
            $dbtable2 = $report."agustus_".$year;
			break;
			case "September":
            $dbtable2 = $report."september_".$year;
			break;
			case "October":
            $dbtable2 = $report."oktober_".$year;
			break;
			case "November":
            $dbtable2 = $report."november_".$year;
			break;
			case "December":
            $dbtable2 = $report."desember_".$year;
			break;
		}
		
		//get vehicle
		$this->db->select("vehicle_device");
		$this->db->from("vehicle");
		if ($vehicle != 0)
		{
			$this->db->where("vehicle_device",$vehicle);
		}
		else
		{
			$this->db->where("vehicle_user_id",$this->sess->user_id);
			$this->db->or_where("vehicle_company", $this->sess->user_company);
		}
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		$qv = $this->db->get();
		
		if ($qv->num_rows == 0)
		{
			redirect(base_url());
		}
		$rv = $qv->result();
		//end get vehicle
	
		//get data operasional
		for($i=0;$i<count($rv);$i++)
		{
			$this->dbtrip = $this->load->database("balrich_report",true);
			$this->dbtrip->order_by("trip_mileage_vehicle_id","asc");
			$this->dbtrip->order_by("trip_mileage_start_time","asc");
			$this->dbtrip->where("trip_mileage_vehicle_id", $rv[$i]->vehicle_device);
			$this->dbtrip->where("trip_mileage_start_time >=",$sdate);
			$this->dbtrip->where("trip_mileage_end_time <=", $edate);
			if($engine != ""){
				$this->dbtrip->where("trip_mileage_engine", $engine);
			}
			$q = $this->dbtrip->get($dbtable);
		
			if ($q->num_rows>0)
			{
				$rows = array_merge($rows, $q->result());
			}
			if($m1 != $m2)
			{
				$this->dbtrip = $this->load->database("balrich_report",true);
				$this->dbtrip->order_by("trip_mileage_vehicle_id","asc");
				$this->dbtrip->order_by("trip_mileage_start_time","asc");
				$this->dbtrip->where("trip_mileage_vehicle_id", $rv[$i]->vehicle_device);
				$this->dbtrip->where("trip_mileage_start_time >=",$sdate);
				$this->dbtrip->where("trip_mileage_end_time <=", $edate);
				if($engine != ""){
					$this->dbtrip->where("trip_mileage_engine", $engine);
				}
				$q2 = $this->dbtrip->get($dbtable2);
			
				if ($q2->num_rows>0)
				{
					$rows2 = array_merge($rows2, $q2->result());
				}
				
				if($year != $year2)
				{
					switch ($m2)
					{
						case "January":
						$dbtable2 = $report."januari_".$year2;
						break;
						case "February":
						$dbtable2 = $report."februari_".$year2;
						break;
						case "March":
						$dbtable2 = $report."maret_".$year2;
						break;
						case "April":
						$dbtable2 = $report."april_".$year2;
						break;
						case "May":
						$dbtable2 = $report."mei_".$year2;
						break;
						case "June":
						$dbtable2 = $report."juni_".$year2;
						break;
						case "July":
						$dbtable2 = $report."juli_".$year2;
						break;
						case "August":
						$dbtable2 = $report."agustus_".$year2;
						break;
						case "September":
						$dbtable2 = $report."september_".$year2;
						break;
						case "October":
						$dbtable2 = $report."oktober_".$year2;
						break;
						case "November":
						$dbtable2 = $report."november_".$year2;
						break;
						case "December":
						$dbtable2 = $report."desember_".$year2;
						break;
					}
		
					$this->dbtrip = $this->load->database("balrich_report",true);
					$this->dbtrip->order_by("trip_mileage_vehicle_id","asc");
					$this->dbtrip->order_by("trip_mileage_start_time","asc");
					$this->dbtrip->where("trip_mileage_vehicle_id", $rv[$i]->vehicle_device);
					$this->dbtrip->where("trip_mileage_start_time >=",$sdate);
					$this->dbtrip->where("trip_mileage_end_time <=", $edate);
					if($engine != ""){
						$this->dbtrip->where("trip_mileage_engine", $engine);
					}
					$q2 = $this->dbtrip->get($dbtable2);
					if ($q2->num_rows>0)
					{
						$rows2 = array_merge($rows2, $q2->result());
					}

				}
			}
			if($m1 != $m2)
			{
				$rowsall = array_merge($rows, $rows2);
			}
		}

		//totaldur
		//total cumm km 
		$totalcumm = 0;
		$totalcumm_on = 0;
		$totalcumm_off = 0;
		$totaldur = 0;
		if($m1 != $m2)
		{
			for($i=0; $i < count($rowsall); $i++)
			{	
				if($rowsall[$i]->trip_mileage_engine == 1 ){
					$totalcumm_on += $rowsall[$i]->trip_mileage_trip_mileage;
				}
				if($rowsall[$i]->trip_mileage_engine == 0 ){
					$totalcumm_off += $rowsall[$i]->trip_mileage_trip_mileage;
				}
				
				$totalcumm += $rowsall[$i]->trip_mileage_trip_mileage;
				$totaldur += $rowsall[$i]->trip_mileage_duration_sec;			
			}
		}
		else{
			for($i=0; $i < count($rows); $i++)
			{	
				if($rows[$i]->trip_mileage_engine == 1 ){
					$totalcumm_on += $rows[$i]->trip_mileage_trip_mileage;
					$totaldur += $rows[$i]->trip_mileage_duration_sec;	
				}
				if($rows[$i]->trip_mileage_engine == 0 ){
					$totalcumm_off += $rows[$i]->trip_mileage_trip_mileage;
				}
				
				$totalcumm += $rows[$i]->trip_mileage_trip_mileage;
						
			}
		
		}
		
		//print_r($rows);exit();

		$totalcummulative = $totalcumm;
		$totalcummulative_on = $totalcumm_on;
		$totalcummulative_off = $totalcumm_off;
		$totalduration = $totaldur;

		if($m1 != $m2)
		{
			$params['data'] = $rowsall;
		}
		else
		{
			$params['data'] = $rows;
		}
		
		$params['totalduration'] = $totalduration;
		$params['totalcummulative'] = $totalcummulative;
		$params['totalcummulative_on'] = $totalcummulative_on;
		$params['totalcummulative_off'] = $totalcummulative_off;
	
		$html = $this->load->view("report/result_operasional", $params, true);
		$callback['error'] = false;
		$callback['html'] = $html;
		echo json_encode($callback);
		return;
		
	}
	
	function dataoperasional_excel()
	{
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$vehicle = $this->input->post("vehicle");
		$startdate = $this->input->post("startdate");
		$enddate = $this->input->post("enddate");
		$engine = $this->input->post("engine");

		$report = "operasional_";
		$offset = 0;
		
		$sdate = date("Y-m-d H:i:s", strtotime($startdate." "."00:00:00"));
		$edate = date("Y-m-d H:i:s", strtotime($enddate." "."23:59:59"));
		
		$m1 = date("F", strtotime($startdate)); 
		$m2 = date("F", strtotime($enddate)); 
		$year = date("Y", strtotime($startdate));
		$year2 = date("Y", strtotime($enddate));
		$rows = array();
		$rows2 = array();
		$total_q = 0;
		$total_q2 = 0;
		
		switch ($m1)
		{
			case "January":
            $dbtable = $report."januari_".$year;
			break;
			case "February":
            $dbtable = $report."februari_".$year;
			break;
			case "March":
            $dbtable = $report."maret_".$year;
			break;
			case "April":
            $dbtable = $report."april_".$year;
			break;
			case "May":
            $dbtable = $report."mei_".$year;
			break;
			case "June":
            $dbtable = $report."juni_".$year;
			break;
			case "July":
            $dbtable = $report."juli_".$year;
			break;
			case "August":
            $dbtable = $report."agustus_".$year;
			break;
			case "September":
            $dbtable = $report."september_".$year;
			break;
			case "October":
            $dbtable = $report."oktober_".$year;
			break;
			case "November":
            $dbtable = $report."november_".$year;
			break;
			case "December":
            $dbtable = $report."desember_".$year;
			break;
		}
		
		switch ($m2)
		{
			case "January":
            $dbtable2 = $report."januari_".$year;
			break;
			case "February":
            $dbtable2 = $report."februari_".$year;
			break;
			case "March":
            $dbtable2 = $report."maret_".$year;
			break;
			case "April":
            $dbtable2 = $report."april_".$year;
			break;
			case "May":
            $dbtable2 = $report."mei_".$year;
			break;
			case "June":
            $dbtable2 = $report."juni_".$year;
			break;
			case "July":
            $dbtable2 = $report."juli_".$year;
			break;
			case "August":
            $dbtable2 = $report."agustus_".$year;
			break;
			case "September":
            $dbtable2 = $report."september_".$year;
			break;
			case "October":
            $dbtable2 = $report."oktober_".$year;
			break;
			case "November":
            $dbtable2 = $report."november_".$year;
			break;
			case "December":
            $dbtable2 = $report."desember_".$year;
			break;
		}
		
		//get vehicle
		$this->db->select("vehicle_device");
		$this->db->from("vehicle");
		if ($vehicle != 0)
		{
			$this->db->where("vehicle_device",$vehicle);
		}
		else
		{
			$this->db->where("vehicle_user_id",$this->sess->user_id);
			$this->db->or_where("vehicle_company", $this->sess->user_company);
		}
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		$qv = $this->db->get();
		
		$rv = $qv->result();
		//end get vehicle
	
		//get data operasional
		for($i=0;$i<count($rv);$i++)
		{
			$this->dbtrip = $this->load->database("balrich_report",true);
			$this->dbtrip->order_by("trip_mileage_vehicle_id","asc");
			$this->dbtrip->order_by("trip_mileage_start_time","asc");
			$this->dbtrip->where("trip_mileage_vehicle_id", $rv[$i]->vehicle_device);
			$this->dbtrip->where("trip_mileage_start_time >=",$sdate);
			$this->dbtrip->where("trip_mileage_end_time <=", $edate);
			if($engine != ""){
				$this->dbtrip->where("trip_mileage_engine", $engine);
			}
			
			$q = $this->dbtrip->get($dbtable);
		
			if ($q->num_rows>0)
			{
				$rows = array_merge($rows, $q->result());
			}
			if($m1 != $m2)
			{
				$this->dbtrip = $this->load->database("balrich_report",true);
				$this->dbtrip->order_by("trip_mileage_vehicle_id","asc");
				$this->dbtrip->order_by("trip_mileage_start_time","asc");
				$this->dbtrip->where("trip_mileage_vehicle_id", $rv[$i]->vehicle_device);
				$this->dbtrip->where("trip_mileage_start_time >=",$sdate);
				$this->dbtrip->where("trip_mileage_end_time <=", $edate);
		
				if($engine != ""){
					$this->dbtrip->where("trip_mileage_engine", $engine);
				}
				
				$q2 = $this->dbtrip->get($dbtable2);
				
				if ($q2->num_rows>0)
				{
					$rows2 = array_merge($rows2, $q2->result());
				}
				
				if($year != $year2)
				{
					switch ($m2)
					{
						case "January":
						$dbtable2 = $report."januari_".$year2;
						break;
						case "February":
						$dbtable2 = $report."februari_".$year2;
						break;
						case "March":
						$dbtable2 = $report."maret_".$year2;
						break;
						case "April":
						$dbtable2 = $report."april_".$year2;
						break;
						case "May":
						$dbtable2 = $report."mei_".$year2;
						break;
						case "June":
						$dbtable2 = $report."juni_".$year2;
						break;
						case "July":
						$dbtable2 = $report."juli_".$year2;
						break;
						case "August":
						$dbtable2 = $report."agustus_".$year2;
						break;
						case "September":
						$dbtable2 = $report."september_".$year2;
						break;
						case "October":
						$dbtable2 = $report."oktober_".$year2;
						break;
						case "November":
						$dbtable2 = $report."november_".$year2;
						break;
						case "December":
						$dbtable2 = $report."desember_".$year2;
						break;
					}
		
					$this->dbtrip = $this->load->database("balrich_report",true);
					$this->dbtrip->order_by("trip_mileage_vehicle_id","asc");
					$this->dbtrip->order_by("trip_mileage_start_time","asc");
					$this->dbtrip->where("trip_mileage_vehicle_id", $rv[$i]->vehicle_device);
					$this->dbtrip->where("trip_mileage_start_time >=",$sdate);
					$this->dbtrip->where("trip_mileage_end_time <=", $edate);
					if($engine != ""){
						$this->dbtrip->where("trip_mileage_engine", $engine);
					}
					$q2 = $this->dbtrip->get($dbtable2);
					if ($q2->num_rows>0)
					{
						$rows2 = array_merge($rows2, $q2->result());
					}

				}
			}
			if($m1 != $m2)
			{
				$rowsall = array_merge($rows, $rows2);
			}
		}

		//totaldur
		//total cumm km 
		$totalcumm = 0;
		$totalcumm_on = 0;
		$totalcumm_off = 0;
		$totaldur = 0;
		if($m1 != $m2)
		{
			for($i=0; $i < count($rowsall); $i++)
			{	
				if($rowsall[$i]->trip_mileage_engine == 1 ){
					$totalcumm_on += $rowsall[$i]->trip_mileage_trip_mileage;
				}
				if($rowsall[$i]->trip_mileage_engine == 0 ){
					$totalcumm_off += $rowsall[$i]->trip_mileage_trip_mileage;
				}
				
				$totalcumm += $rowsall[$i]->trip_mileage_trip_mileage;
				$totaldur += $rowsall[$i]->trip_mileage_duration_sec;			
			}
		}
		else{
			for($i=0; $i < count($rows); $i++)
			{	
				if($rows[$i]->trip_mileage_engine == 1 ){
					$totalcumm_on += $rows[$i]->trip_mileage_trip_mileage;
					$totaldur += $rows[$i]->trip_mileage_duration_sec;	
				}
				if($rows[$i]->trip_mileage_engine == 0 ){
					$totalcumm_off += $rows[$i]->trip_mileage_trip_mileage;
				}
				
				$totalcumm += $rows[$i]->trip_mileage_trip_mileage;
						
			}
		
		}

		$totalcummulative = $totalcumm;
		$totalcummulative_on = $totalcumm_on;
		$totalcummulative_off = $totalcumm_off;
		$totalduration = $totaldur;
		
		if($m1 != $m2)
		{
			$data = $rowsall;
		}
		else
		{
			$data = $rows;
		}
		
		$total = count($data);
		
		//get vehicle name
		$this->db->order_by("vehicle_id","asc");
		$this->db->select("vehicle_no, vehicle_name");
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_device", $vehicle);
		$this->db->limit(1);
		$q_name = $this->db->get("vehicle");
		$r_name = $q_name->row();
		if ($q_name->num_rows>0){
			$vehicle_name = $r_name->vehicle_name;
			$vehicle_no = $r_name->vehicle_no;
		}else{
			$vehicle_name = "-";
			$vehicle_no = "-";
		}
		
		/** PHPExcel */
		include 'class/PHPExcel.php';
			
		/** PHPExcel_Writer_Excel2007 */
		include 'class/PHPExcel/Writer/Excel2007.php';
			
		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();
			
		// Set properties
		$objPHPExcel->getProperties()->setCreator("lacak-mobil.com");
		$objPHPExcel->getProperties()->setLastModifiedBy("lacak-mobil.com");
		$objPHPExcel->getProperties()->setTitle("Operasional Data Report");
		$objPHPExcel->getProperties()->setSubject("Operational Data Report Lacak-mobil.com");
		$objPHPExcel->getProperties()->setDescription("Operational Data Report Lacak-mobil.com");
			
		//set document
		$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(1);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.50);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.50);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(1);
		$objPHPExcel->getDefaultStyle()->getFont()->setName('Times New Roman');
		$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(12);			
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(35);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(35);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);

		//Header
		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->getActiveSheet()->mergeCells('A1:I1');
		$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'OPERATIONAL DATA REPORT');
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->SetCellValue('B3', 'Date :');
		$objPHPExcel->getActiveSheet()->getStyle('B3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('B3')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->SetCellValue('C3', $startdate." "."-"." ".$enddate);
		$objPHPExcel->getActiveSheet()->getStyle('C3')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->SetCellValue('F3', "Vehicle :");
		$objPHPExcel->getActiveSheet()->getStyle('F3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('F3')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->SetCellValue('G3', $vehicle_name." ".$vehicle_no);
		$objPHPExcel->getActiveSheet()->getStyle('G3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$objPHPExcel->getActiveSheet()->getStyle('G3')->getFont()->setBold(true);
		
		//Top Header
		$objPHPExcel->getActiveSheet()->SetCellValue('A5', 'No');
		$objPHPExcel->getActiveSheet()->SetCellValue('B5', 'Start Time');
		$objPHPExcel->getActiveSheet()->SetCellValue('C5', 'End Time');
		$objPHPExcel->getActiveSheet()->SetCellValue('D5', 'Engine');
		$objPHPExcel->getActiveSheet()->SetCellValue('E5', 'Duration');
		$objPHPExcel->getActiveSheet()->SetCellValue('F5', 'Location Start');
		$objPHPExcel->getActiveSheet()->SetCellValue('G5', 'Location End');
		$objPHPExcel->getActiveSheet()->SetCellValue('H5', 'Coordinate Start');
		$objPHPExcel->getActiveSheet()->SetCellValue('I5', 'Coordinate End');
		$objPHPExcel->getActiveSheet()->SetCellValue('J5', 'Trip Mileage');
		$objPHPExcel->getActiveSheet()->SetCellValue('K5', 'Cumulative Mileage');
		
		$objPHPExcel->getActiveSheet()->getStyle('A5:K5')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A5:K5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		$i=1;
		$k=0;
		for($j=0;$j<count($data);$j++)
		{
			$k = $k + $data[$j]->trip_mileage_trip_mileage;
			$objPHPExcel->getActiveSheet()->SetCellValue('A'.(5+$i), $i);
			$objPHPExcel->getActiveSheet()->getStyle('A'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->SetCellValue('B'.(5+$i), $data[$j]->trip_mileage_start_time);						
			$objPHPExcel->getActiveSheet()->SetCellValue('C'.(5+$i), $data[$j]->trip_mileage_end_time);
			
			if($data[$j]->trip_mileage_engine == 0){
				$engine = "OFF";
			}else{
				$engine = "ON";
			}
			$objPHPExcel->getActiveSheet()->SetCellValue('D'.(5+$i), $engine);
			$objPHPExcel->getActiveSheet()->getStyle('D'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$objPHPExcel->getActiveSheet()->SetCellValue('E'.(5+$i), $data[$j]->trip_mileage_duration);
			$objPHPExcel->getActiveSheet()->getStyle('E'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$geofence_start = strlen($data[$j]->trip_mileage_geofence_start);
			if (strlen($geofence_start == 1)){
				$geofence_start_name = "";
				$objPHPExcel->getActiveSheet()->SetCellValue('F'.(5+$i),$geofence_start_name."  ".$data[$j]->trip_mileage_location_start);
				$objPHPExcel->getActiveSheet()->getStyle('F'.(5+$i))->getFont()->setSize(8);
			}
			if (strlen($geofence_start > 1)){
				$geofence_start_name = $data[$j]->trip_mileage_geofence_start;
				$objPHPExcel->getActiveSheet()->SetCellValue('F'.(5+$i),$geofence_start_name.", ".$data[$j]->trip_mileage_location_start);
				$objPHPExcel->getActiveSheet()->getStyle('F'.(5+$i))->getFont()->setSize(8);
			}
			
			$geofence_end = strlen($data[$j]->trip_mileage_geofence_end);
			
			if (strlen($geofence_end == 1)){
				$geofence_end_name = "";
				$objPHPExcel->getActiveSheet()->SetCellValue('G'.(5+$i),$geofence_end_name."  ".$data[$j]->trip_mileage_location_end);
				$objPHPExcel->getActiveSheet()->getStyle('G'.(5+$i))->getFont()->setSize(8);
			}
			if (strlen($geofence_end > 1)){
				$geofence_end_name = $data[$j]->trip_mileage_geofence_end;
				$objPHPExcel->getActiveSheet()->SetCellValue('G'.(5+$i),$geofence_end_name.",  ".$data[$j]->trip_mileage_location_end);
				$objPHPExcel->getActiveSheet()->getStyle('G'.(5+$i))->getFont()->setSize(8);
			}

			$objPHPExcel->getActiveSheet()->SetCellValue('H'.(5+$i), $data[$j]->trip_mileage_coordinate_start);
			$objPHPExcel->getActiveSheet()->getStyle('H'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->SetCellValue('I'.(5+$i), $data[$j]->trip_mileage_coordinate_end);
			$objPHPExcel->getActiveSheet()->getStyle('I'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

			$objPHPExcel->getActiveSheet()->SetCellValue('J'.(5+$i), $data[$j]->trip_mileage_trip_mileage." "."KM");
			$objPHPExcel->getActiveSheet()->getStyle('J'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->SetCellValue('K'.(5+$i), $k." "."KM");
			$objPHPExcel->getActiveSheet()->getStyle('K'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$i++;
		}

			$objPHPExcel->getActiveSheet()->SetCellValue('B'.(6+$i), 'Total Mileage');
			$objPHPExcel->getActiveSheet()->getStyle('B'.(6+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$objPHPExcel->getActiveSheet()->getStyle('B'.(6+$i))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->mergeCells('I'.(6+$i).':'.'K'.(6+$i));
			//$objPHPExcel->getActiveSheet()->SetCellValue('I'.(6+$i), $totalcummulative_on.' '.'KM');
			if(isset($k) && $k > 0){
				$objPHPExcel->getActiveSheet()->SetCellValue('I'.(6+$i), $k.' '.'KM');
			}else{
				$objPHPExcel->getActiveSheet()->SetCellValue('I'.(6+$i), ' ');
			}
			$objPHPExcel->getActiveSheet()->SetCellValue('I'.(6+$i), $k.' '.'KM');
			
			$objPHPExcel->getActiveSheet()->getStyle('I'.(6+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('I'.(6+$i))->getFont()->setBold(true);
			
			$objPHPExcel->getActiveSheet()->SetCellValue('B'.(7+$i), 'Total Duration');
			$objPHPExcel->getActiveSheet()->getStyle('B'.(7+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$objPHPExcel->getActiveSheet()->getStyle('B'.(7+$i))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->mergeCells('I'.(7+$i).':'.'K'.(7+$i));
			if (isset($totalduration))
									{
										$conval = $totalduration;
										$seconds = $conval;
										
										// extract hours
										$hours = floor($seconds / (60 * 60));
										$h_duration = "";
										// extract minutes
										$divisor_for_minutes = $seconds % (60 * 60);
										$minutes = floor($divisor_for_minutes / 60);
										$m_duration = "";
										// extract the remaining seconds
										$divisor_for_seconds = $divisor_for_minutes % 60;
										$seconds = ceil($divisor_for_seconds);
										
										if(isset($hours) && $hours > 0)
										{
											if($hours > 0 && $hours <= 1)
											{
												$h_duration = $hours." "."Hour"." ";
											}
											if($hours >= 2)
											{
												$h_duration = $hours." "."Hours"." ";
											}
										}
										if(isset($minutes) && $minutes > 0)
										{
											if($minutes > 0 && $minutes <= 1 )
											{
												$m_duration = $minutes." "."Minute"." ";
											}
											if($minutes >= 2)
											{
												$m_duration = $minutes." "."Minutes"." ";
											}
										}
										 /* if(isset($seconds) && $seconds > 0)
										{
											$s_duration =  $seconds." "."Detik"." ";
										} */
									}
		
			$objPHPExcel->getActiveSheet()->SetCellValue('I'.(7+$i), $h_duration." ".$m_duration);
			$objPHPExcel->getActiveSheet()->getStyle('I'.(7+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('I'.(7+$i))->getFont()->setBold(true);
			
			
		$styleArray = array(
				'borders' => array(
				'allborders' => array(
				'style' => PHPExcel_Style_Border::BORDER_THIN
				    )
				  )
				);
			
		$objPHPExcel->getActiveSheet()->getStyle('A5:K'.(7+$i))->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->getStyle('A5:K'.(7+$i))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
		$objPHPExcel->getActiveSheet()->getStyle('A5:K'.(7+$i))->getAlignment()->setWrapText(true);
		// Rename sheet
		$objPHPExcel->getActiveSheet()->setTitle('operational_data');
			
		// Save Excel
		$filedate = date("Ymd",strtotime($startdate))."_".date("Ymd",strtotime($enddate));
		$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
		@mkdir(REPORT_PATH, DIR_WRITE_MODE); //REPORT_PATH -> config->constant
		$filecreatedname = "operational_".$vehicle_no."_".$filedate.".xls";
			
		$objWriter->save(REPORT_PATH.$filecreatedname);
		
		$output = '{"success":true,"errMsg":"","filename":"' .base_url() . "assets/media/report/" . $filecreatedname.'"}';
		echo $output;
		return;
	}
	
	function mn_histdoorstatus()
	{
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		
		$this->db->order_by("vehicle_name", "asc");
		$this->db->order_by("vehicle_no", "asc");
		$this->db->where("vehicle_type", "T5DOOR");		
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_user_id", $this->sess->user_id);
		$this->db->or_where("vehicle_company", $this->sess->user_company);
		$q = $this->db->get("vehicle");
		
		$rows = $q->result();
		$this->params["vehicles"] = $rows;
		$this->params["content"] = $this->load->view('report/mn_histdoorstatus', $this->params, true);		
		$this->load->view("templatesess", $this->params);
	}
	
	function histdoorstatus()
	{
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		
		$vehicle = $this->input->post("vehicle");
		$startdate = $this->input->post("startdate");
		$enddate = $this->input->post("enddate");
		$door = $this->input->post("door");
		$report = "door_";
		
		$sdate = date("Y-m-d H:i:s", strtotime($startdate." "."00:00:00"));
		$edate = date("Y-m-d H:i:s", strtotime($enddate." "."23:59:59"));
		
		$m1 = date("F", strtotime($startdate)); 
		$m2 = date("F", strtotime($enddate)); 
		$year = date("Y", strtotime($startdate));
		$year2 = date("Y", strtotime($enddate));
		$rows = array();
		$rows2 = array();
		$total_q = 0;
		$total_q2 = 0;
		
		switch ($m1)
		{
			case "January":
            $dbtable = $report."januari_".$year;
			break;
			case "February":
            $dbtable = $report."februari_".$year;
			break;
			case "March":
            $dbtable = $report."maret_".$year;
			break;
			case "April":
            $dbtable = $report."april_".$year;
			break;
			case "May":
            $dbtable = $report."mei_".$year;
			break;
			case "June":
            $dbtable = $report."juni_".$year;
			break;
			case "July":
            $dbtable = $report."juli_".$year;
			break;
			case "August":
            $dbtable = $report."agustus_".$year;
			break;
			case "September":
            $dbtable = $report."september_".$year;
			break;
			case "October":
            $dbtable = $report."oktober_".$year;
			break;
			case "November":
            $dbtable = $report."november_".$year;
			break;
			case "December":
            $dbtable = $report."desember_".$year;
			break;
		}
		
		switch ($m2)
		{
			case "January":
            $dbtable2 = $report."januari_".$year;
			break;
			case "February":
            $dbtable2 = $report."februari_".$year;
			break;
			case "March":
            $dbtable2 = $report."maret_".$year;
			break;
			case "April":
            $dbtable2 = $report."april_".$year;
			break;
			case "May":
            $dbtable2 = $report."mei_".$year;
			break;
			case "June":
            $dbtable2 = $report."juni_".$year;
			break;
			case "July":
            $dbtable2 = $report."juli_".$year;
			break;
			case "August":
            $dbtable2 = $report."agustus_".$year;
			break;
			case "September":
            $dbtable2 = $report."september_".$year;
			break;
			case "October":
            $dbtable2 = $report."oktober_".$year;
			break;
			case "November":
            $dbtable2 = $report."november_".$year;
			break;
			case "December":
            $dbtable2 = $report."desember_".$year;
			break;
		}
		
		//get vehicle
		$this->db->select("vehicle_device");
		$this->db->from("vehicle");
		if ($vehicle != 0)
		{
			$this->db->where("vehicle_device",$vehicle);
		}
		else
		{
			$this->db->where("vehicle_user_id",$this->sess->user_id);
			$this->db->or_where("vehicle_company", $this->sess->user_company);
		}
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		$qv = $this->db->get();
		
		if ($qv->num_rows == 0)
		{
			redirect(base_url());
		}
		$rv = $qv->result();
		//end get vehicle
	
		//get data door
		for($i=0;$i<count($rv);$i++)
		{
			$this->dbtrip = $this->load->database("balrich_report",true);
			$this->dbtrip->order_by("door_vehicle_id","asc");
			$this->dbtrip->order_by("door_start_time","asc");
			$this->dbtrip->where("door_vehicle_device", $rv[$i]->vehicle_device);
			$this->dbtrip->where("door_start_time >=",$sdate);
			$this->dbtrip->where("door_end_time <=", $edate);
			if($door != ""){
				$this->dbtrip->where("door_status", $door);
			}
			
			$q = $this->dbtrip->get($dbtable);
		
			if ($q->num_rows>0)
			{
				$rows = array_merge($rows, $q->result());
			}
			if($m1 != $m2)
			{
				$this->dbtrip = $this->load->database("balrich_report",true);
				$this->dbtrip->order_by("door_vehicle_id","asc");
				$this->dbtrip->order_by("door_start_time","asc");
				$this->dbtrip->where("door_vehicle_device", $rv[$i]->vehicle_device);
				$this->dbtrip->where("door_start_time >=",$sdate);
				$this->dbtrip->where("door_end_time <=", $edate);
					if($door != ""){
					$this->dbtrip->where("door_status", $door);
				}
				
				$q2 = $this->dbtrip->get($dbtable2);
			
				if ($q2->num_rows>0)
				{
					$rows2 = array_merge($rows2, $q2->result());
				}
				if($year != $year2)
				{
					switch ($m2)
					{
						case "January":
						$dbtable2 = $report."januari_".$year2;
						break;
						case "February":
						$dbtable2 = $report."februari_".$year2;
						break;
						case "March":
						$dbtable2 = $report."maret_".$year2;
						break;
						case "April":
						$dbtable2 = $report."april_".$year2;
						break;
						case "May":
						$dbtable2 = $report."mei_".$year2;
						break;
						case "June":
						$dbtable2 = $report."juni_".$year2;
						break;
						case "July":
						$dbtable2 = $report."juli_".$year2;
						break;
						case "August":
						$dbtable2 = $report."agustus_".$year2;
						break;
						case "September":
						$dbtable2 = $report."september_".$year2;
						break;
						case "October":
						$dbtable2 = $report."oktober_".$year2;
						break;
						case "November":
						$dbtable2 = $report."november_".$year2;
						break;
						case "December":
						$dbtable2 = $report."desember_".$year2;
						break;
					}
		
					$this->dbtrip = $this->load->database("balrich_report",true);
					$this->dbtrip->order_by("door_vehicle_id","asc");
					$this->dbtrip->order_by("door_start_time","asc");
					$this->dbtrip->where("door_vehicle_device", $rv[$i]->vehicle_device);
					$this->dbtrip->where("door_start_time >=",$sdate);
					$this->dbtrip->where("door_end_time <=", $edate);
						if($door != ""){
						$this->dbtrip->where("door_status", $door);
					}
					$q2 = $this->dbtrip->get($dbtable2);
					
					if ($q2->num_rows>0)
					{
						$rows2 = array_merge($rows2, $q2->result());
					}

				}
			}
			if($m1 != $m2)
			{
				$rowsall = array_merge($rows, $rows2);
			}
		}

		if($m1 != $m2)
		{
			$params['data'] = $rowsall;
		}
		else
		{
			$params['data'] = $rows;
		}
	
		$html = $this->load->view("report/result_histdoorstatus", $params, true);
		$callback['error'] = false;
		$callback['html'] = $html;
		echo json_encode($callback);
		return;
		
	}
	
	function histdoorstatus_excel()
	{
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		
		$vehicle = $this->input->post("vehicle");
		$startdate = $this->input->post("startdate");
		$enddate = $this->input->post("enddate");
		$door = $this->input->post("door");
		$report = "door_";
		
		$sdate = date("Y-m-d H:i:s", strtotime($startdate." "."00:00:00"));
		$edate = date("Y-m-d H:i:s", strtotime($enddate." "."23:59:59"));
		
		$m1 = date("F", strtotime($startdate)); 
		$m2 = date("F", strtotime($enddate)); 
		$year = date("Y", strtotime($startdate));
		$year2 = date("Y", strtotime($enddate));
		$rows = array();
		$rows2 = array();
		$total_q = 0;
		$total_q2 = 0;
		
		switch ($m1)
		{
			case "January":
            $dbtable = $report."januari_".$year;
			break;
			case "February":
            $dbtable = $report."februari_".$year;
			break;
			case "March":
            $dbtable = $report."maret_".$year;
			break;
			case "April":
            $dbtable = $report."april_".$year;
			break;
			case "May":
            $dbtable = $report."mei_".$year;
			break;
			case "June":
            $dbtable = $report."juni_".$year;
			break;
			case "July":
            $dbtable = $report."juli_".$year;
			break;
			case "August":
            $dbtable = $report."agustus_".$year;
			break;
			case "September":
            $dbtable = $report."september_".$year;
			break;
			case "October":
            $dbtable = $report."oktober_".$year;
			break;
			case "November":
            $dbtable = $report."november_".$year;
			break;
			case "December":
            $dbtable = $report."desember_".$year;
			break;
		}
		
		switch ($m2)
		{
			case "January":
            $dbtable2 = $report."januari_".$year;
			break;
			case "February":
            $dbtable2 = $report."februari_".$year;
			break;
			case "March":
            $dbtable2 = $report."maret_".$year;
			break;
			case "April":
            $dbtable2 = $report."april_".$year;
			break;
			case "May":
            $dbtable2 = $report."mei_".$year;
			break;
			case "June":
            $dbtable2 = $report."juni_".$year;
			break;
			case "July":
            $dbtable2 = $report."juli_".$year;
			break;
			case "August":
            $dbtable2 = $report."agustus_".$year;
			break;
			case "September":
            $dbtable2 = $report."september_".$year;
			break;
			case "October":
            $dbtable2 = $report."oktober_".$year;
			break;
			case "November":
            $dbtable2 = $report."november_".$year;
			break;
			case "December":
            $dbtable2 = $report."desember_".$year;
			break;
		}
		
		//get vehicle
		$this->db->select("vehicle_device");
		$this->db->from("vehicle");
		if ($vehicle != 0)
		{
			$this->db->where("vehicle_device",$vehicle);
		}
		else
		{
			$this->db->where("vehicle_user_id",$this->sess->user_id);
			$this->db->or_where("vehicle_company", $this->sess->user_company);
		}
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		$qv = $this->db->get();
		
		if ($qv->num_rows == 0)
		{
			redirect(base_url());
		}
		$rv = $qv->result();
		//end get vehicle
	
		//get data door
		for($i=0;$i<count($rv);$i++)
		{
			$this->dbtrip = $this->load->database("balrich_report",true);
			$this->dbtrip->order_by("door_vehicle_id","asc");
			$this->dbtrip->order_by("door_start_time","asc");
			$this->dbtrip->where("door_vehicle_device", $rv[$i]->vehicle_device);
			$this->dbtrip->where("door_start_time >=",$sdate);
			$this->dbtrip->where("door_end_time <=", $edate);
			if($door != ""){
				$this->dbtrip->where("door_status", $door);
			}
			
			$q = $this->dbtrip->get($dbtable);
		
			if ($q->num_rows>0)
			{
				$rows = array_merge($rows, $q->result());
			}
			if($m1 != $m2)
			{
				$this->dbtrip = $this->load->database("balrich_report",true);
				$this->dbtrip->order_by("door_vehicle_id","asc");
				$this->dbtrip->order_by("door_start_time","asc");
				$this->dbtrip->where("door_vehicle_device", $rv[$i]->vehicle_device);
				$this->dbtrip->where("door_start_time >=",$sdate);
				$this->dbtrip->where("door_end_time <=", $edate);
					if($door != ""){
					$this->dbtrip->where("door_status", $door);
				}
				
				$q2 = $this->dbtrip->get($dbtable2);
			
				if ($q2->num_rows>0)
				{
					$rows2 = array_merge($rows2, $q2->result());
				}
				
				if($year != $year2)
				{
					switch ($m2)
					{
						case "January":
						$dbtable2 = $report."januari_".$year2;
						break;
						case "February":
						$dbtable2 = $report."februari_".$year2;
						break;
						case "March":
						$dbtable2 = $report."maret_".$year2;
						break;
						case "April":
						$dbtable2 = $report."april_".$year2;
						break;
						case "May":
						$dbtable2 = $report."mei_".$year2;
						break;
						case "June":
						$dbtable2 = $report."juni_".$year2;
						break;
						case "July":
						$dbtable2 = $report."juli_".$year2;
						break;
						case "August":
						$dbtable2 = $report."agustus_".$year2;
						break;
						case "September":
						$dbtable2 = $report."september_".$year2;
						break;
						case "October":
						$dbtable2 = $report."oktober_".$year2;
						break;
						case "November":
						$dbtable2 = $report."november_".$year2;
						break;
						case "December":
						$dbtable2 = $report."desember_".$year2;
						break;
					}
		
					$this->dbtrip = $this->load->database("balrich_report",true);
					$this->dbtrip->order_by("door_vehicle_id","asc");
					$this->dbtrip->order_by("door_start_time","asc");
					$this->dbtrip->where("door_vehicle_device", $rv[$i]->vehicle_device);
					$this->dbtrip->where("door_start_time >=",$sdate);
					$this->dbtrip->where("door_end_time <=", $edate);
						if($door != ""){
						$this->dbtrip->where("door_status", $door);
					}
					$q2 = $this->dbtrip->get($dbtable2);
					
					if ($q2->num_rows>0)
					{
						$rows2 = array_merge($rows2, $q2->result());
					}

				}
			}
			if($m1 != $m2)
			{
				$rowsall = array_merge($rows, $rows2);
			}
		}

		if($m1 != $m2)
		{
			$data = $rowsall;
		}
		else
		{
			$data = $rows;
		}
		
		$total = count($data);
		
		//get vehicle name
		$this->db->order_by("vehicle_id","asc");
		$this->db->select("vehicle_no, vehicle_name");
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_device", $vehicle);
		$this->db->limit(1);
		$q_name = $this->db->get("vehicle");
		$r_name = $q_name->row();
		if ($q_name->num_rows>0){
			$vehicle_name = $r_name->vehicle_name;
			$vehicle_no = $r_name->vehicle_no;
		}else{
			$vehicle_name = "-";
			$vehicle_no = "-";
		}
		
		/** PHPExcel */
		include 'class/PHPExcel.php';
			
		/** PHPExcel_Writer_Excel2007 */
		include 'class/PHPExcel/Writer/Excel2007.php';
			
		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();
			
		// Set properties
		$objPHPExcel->getProperties()->setCreator("lacak-mobil.com");
		$objPHPExcel->getProperties()->setLastModifiedBy("lacak-mobil.com");
		$objPHPExcel->getProperties()->setTitle("History Door Status Report");
		$objPHPExcel->getProperties()->setSubject("History Door Status Report Lacak-mobil.com");
		$objPHPExcel->getProperties()->setDescription("History Door Status Report Lacak-mobil.com");
			
		//set document
		$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(1);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.50);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.50);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(1);
		$objPHPExcel->getDefaultStyle()->getFont()->setName('Times New Roman');
		$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(12);			
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(12);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(35);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(35);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);

		//Header
		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->getActiveSheet()->mergeCells('A1:I1');
		$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'DOOR STATUS REPORT');
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->SetCellValue('B3', 'Date :');
		$objPHPExcel->getActiveSheet()->getStyle('B3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('B3')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->SetCellValue('C3', $startdate." "."-"." ".$enddate);
		$objPHPExcel->getActiveSheet()->getStyle('C3')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->SetCellValue('F3', "Vehicle :");
		$objPHPExcel->getActiveSheet()->getStyle('F3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('F3')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->SetCellValue('G3', $vehicle_name." ".$vehicle_no);
		$objPHPExcel->getActiveSheet()->getStyle('G3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$objPHPExcel->getActiveSheet()->getStyle('G3')->getFont()->setBold(true);
		
		//Top Header
		$objPHPExcel->getActiveSheet()->SetCellValue('A5', 'No');
		$objPHPExcel->getActiveSheet()->SetCellValue('B5', 'Start Time');
		$objPHPExcel->getActiveSheet()->SetCellValue('C5', 'End Time');
		$objPHPExcel->getActiveSheet()->SetCellValue('D5', 'Door');
		$objPHPExcel->getActiveSheet()->SetCellValue('E5', 'Duration');
		$objPHPExcel->getActiveSheet()->SetCellValue('F5', 'Location Start');
		$objPHPExcel->getActiveSheet()->SetCellValue('G5', 'Location End');
		$objPHPExcel->getActiveSheet()->SetCellValue('H5', 'Coordinate Start');
		$objPHPExcel->getActiveSheet()->SetCellValue('I5', 'Coordinate End');
		
		$objPHPExcel->getActiveSheet()->getStyle('A5:I5')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A5:I5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		$i=1;
		for($j=0;$j<count($data);$j++)
		{
			$objPHPExcel->getActiveSheet()->SetCellValue('A'.(5+$i), $i);
			$objPHPExcel->getActiveSheet()->getStyle('A'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->SetCellValue('B'.(5+$i), $data[$j]->door_start_time);	
			$objPHPExcel->getActiveSheet()->getStyle('B'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);			
			$objPHPExcel->getActiveSheet()->SetCellValue('C'.(5+$i), $data[$j]->door_end_time);
			$objPHPExcel->getActiveSheet()->getStyle('C'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->SetCellValue('D'.(5+$i), $data[$j]->door_status);
			$objPHPExcel->getActiveSheet()->getStyle('D'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->SetCellValue('E'.(5+$i), $data[$j]->door_duration);
			$objPHPExcel->getActiveSheet()->getStyle('E'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$geofence_start = strlen($data[$j]->door_geofence_start);
			if (strlen($geofence_start == 1)){
				$geofence_start_name = "";
				$objPHPExcel->getActiveSheet()->SetCellValue('F'.(5+$i),$geofence_start_name."  ".$data[$j]->door_location_start);
				$objPHPExcel->getActiveSheet()->getStyle('F'.(5+$i))->getFont()->setSize(8);
			}
			if (strlen($geofence_start > 1)){
				$geofence_start_name = $data[$j]->door_geofence_start;
				$objPHPExcel->getActiveSheet()->SetCellValue('F'.(5+$i),$geofence_start_name.", ".$data[$j]->door_location_start);
				$objPHPExcel->getActiveSheet()->getStyle('F'.(5+$i))->getFont()->setSize(8);
			}
			
			$geofence_end = strlen($data[$j]->door_geofence_end);
			//print_r($geofence_end);exit();
			if (strlen($geofence_end == 1)){
				$geofence_end_name = "";
				$objPHPExcel->getActiveSheet()->SetCellValue('G'.(5+$i),$geofence_end_name."  ".$data[$j]->door_location_end);
				$objPHPExcel->getActiveSheet()->getStyle('G'.(5+$i))->getFont()->setSize(8);
			}
			if (strlen($geofence_end > 1)){
				$geofence_end_name = $data[$j]->door_geofence_end;
				$objPHPExcel->getActiveSheet()->SetCellValue('G'.(5+$i),$geofence_end_name.",  ".$data[$j]->door_location_end);
				$objPHPExcel->getActiveSheet()->getStyle('G'.(5+$i))->getFont()->setSize(8);
			}

			
			$objPHPExcel->getActiveSheet()->SetCellValue('H'.(5+$i), $data[$j]->door_coordinate_start);
			$objPHPExcel->getActiveSheet()->getStyle('H'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->SetCellValue('I'.(5+$i), $data[$j]->door_coordinate_end);
			$objPHPExcel->getActiveSheet()->getStyle('I'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$i++;
		}
			
		$styleArray = array(
				'borders' => array(
				'allborders' => array(
				'style' => PHPExcel_Style_Border::BORDER_THIN
				    )
				  )
				);
			
		$objPHPExcel->getActiveSheet()->getStyle('A5:I'.(4+$i))->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->getStyle('A5:I'.(4+$i))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
		$objPHPExcel->getActiveSheet()->getStyle('A5:I'.(4+$i))->getAlignment()->setWrapText(true);
		// Rename sheet
		$objPHPExcel->getActiveSheet()->setTitle('History Door Status');
			
		// Save Excel
		$filedate = date("Ymd",strtotime($startdate))."_".date("Ymd",strtotime($enddate));
		$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
		@mkdir(REPORT_PATH, DIR_WRITE_MODE); //REPORT_PATH -> config->constant
		$filecreatedname = "doorstatus_".$vehicle_no."_".$filedate.".xls";
			
		$objWriter->save(REPORT_PATH.$filecreatedname);
		
		$output = '{"success":true,"errMsg":"","filename":"' .base_url() . "assets/media/report/" . $filecreatedname.'"}';
		echo $output;
		return;
	}
}