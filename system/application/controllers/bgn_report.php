<?php
include "base.php";

class Bgn_report extends Base {
	var $otherdb;
	
	function Bgn_report()
	{
		parent::Base();	
		$this->load->model("gpsmodel");
		$this->load->model("vehiclemodel");
		$this->load->model("configmodel");
		$this->load->helper('common_helper');
		$this->load->helper('kopindosat');
	}
	
	//historyparking
	function index(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		
		$this->db->order_by("vehicle_name", "asc");
		$this->db->order_by("vehicle_no", "asc");		
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_user_id", $this->sess->user_id);
		$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		$q = $this->db->get("vehicle");
		
		$rows = $q->result();
		$this->params["vehicles"] = $rows;
		$this->params["content"] = $this->load->view('bangun_trans/report/mn_history_parking', $this->params, true);		
		$this->load->view("templatesess", $this->params);
	}

	function history_parking()
	{
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		
		$vehicle = $this->input->post("vehicle");
		$startdate = $this->input->post("startdate");
		$enddate = $this->input->post("enddate");
		$engine = "OFF";
		//$duration = $this->input->post("duration");
		$report = "parking_";
		
		$sdate = date("Y-m-d H:i:s", strtotime($startdate." "."00:00:00"));
		$edate = date("Y-m-d H:i:s", strtotime($enddate." "."23:59:59"));
		
		$m1 = date("F", strtotime($startdate)); 
		$m2 = date("F", strtotime($enddate)); 
		$year = date("Y", strtotime($startdate));
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
		}
		$this->db->where("vehicle_status <>", 3);
		//$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		$qv = $this->db->get();
		$rv = $qv->result();
		//end get vehicle
		
		//get data parking
		for($i=0;$i<count($rv);$i++)
		{
			$this->dbparking = $this->load->database("bgn_parking",true);
			$this->dbparking->order_by("parking_vehicle_no","asc");
			$this->dbparking->order_by("parking_start_time","asc");
			$this->dbparking->where("parking_vehicle_device", $rv[$i]->vehicle_device);
			$this->dbparking->where("parking_start_time >=",$sdate);
			$this->dbparking->where("parking_end_time <=", $edate);
			$this->dbparking->where("parking_engine", $engine);
			//$this->dbparking->where("parking_duration_sec >=", $limitduration);
			/* if($duration != ""){
				$this->dbparking->where("parking_duration_sec >", $duration);
				$this->dbparking->where("parking_duration_sec <", 99999999);
			} */
			$q = $this->dbparking->get($dbtable);
			//print_r($sdate." ".$edate." ".$engine);exit();
			if ($q->num_rows>0)
			{
				$rows = array_merge($rows, $q->result());
			}
			if($m1 != $m2)
			{
				$this->dbparking = $this->load->database("bgn_parking",true);
				$this->dbparking->order_by("parking_vehicle_no","asc");
				$this->dbparking->order_by("parking_start_time","asc");
				$this->dbparking->where("parking_vehicle_device", $rv[$i]->vehicle_device);
				$this->dbparking->where("parking_start_time >=",$sdate);
				$this->dbparking->where("parking_end_time <=", $edate);
				$this->dbparking->where("parking_engine", $engine);
				//$this->dbparking->where("parking_duration_sec >=", $limitduration);
				/* if($duration != ""){
					$this->dbparking->where("parking_duration_sec >", $duration);
					$this->dbparking->where("parking_duration_sec <", 99999999);
				} */
				$q2 = $this->dbparking->get($dbtable2);
				
				//$total_q2 = count($q2);
				if ($q2->num_rows>0)
				{
					$rows2 = array_merge($rows2, $q2->result());
				}
			}
			if($m1 != $m2)
			{
				$rowsall = array_merge($rows, $rows2);
			}
		}

		//totaldur
		$totaldur = 0;
		if($m1 != $m2)
		{
			for($i=0; $i < count($rowsall); $i++)
			{	
				if($rows[$i]->parking_engine == "OFF" ){
					$totaldur += $rowsall[$i]->parking_duration_sec;			
				}
			}
		}
		else{
			for($i=0; $i < count($rows); $i++)
			{	
				if($rows[$i]->parking_engine == "OFF" ){
					$totaldur += $rows[$i]->parking_duration_sec;
				}				
			}
		}
		
		$totalduration = $totaldur;
		//print_r($rows);exit();
		if($m1 != $m2)
		{
			$params['data'] = $rowsall;
		}
		else
		{
			$params['data'] = $rows;
		}
		
		$params['totalduration'] = $totalduration;
		$html = $this->load->view("bangun_trans/report/result_history_parking", $params, true);
		$callback['error'] = false;
		$callback['html'] = $html;
		echo json_encode($callback);
		return;
		
	}
	
	function history_parking_excel()
	{
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$vehicle = $this->input->post("vehicle");
		$startdate = $this->input->post("startdate");
		$enddate = $this->input->post("enddate");
		$engine = "OFF";
		$report = "parking_";
		$offset = 0;
		
		$sdate = date("Y-m-d H:i:s", strtotime($startdate." "."00:00:00"));
		$edate = date("Y-m-d H:i:s", strtotime($enddate." "."23:59:59"));
		
		$m1 = date("F", strtotime($startdate)); 
		$m2 = date("F", strtotime($enddate)); 
		$year = date("Y", strtotime($startdate));
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
		}
		$this->db->where("vehicle_status <>", 3);
		//$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		$qv = $this->db->get();
		$rv = $qv->result();
		//end get vehicle
		
		//get data parking
		for($i=0;$i<count($rv);$i++)
		{
			$this->dbparking = $this->load->database("bgn_parking",true);
			$this->dbparking->order_by("parking_vehicle_no","asc");
			$this->dbparking->order_by("parking_start_time","asc");
			$this->dbparking->where("parking_vehicle_device", $rv[$i]->vehicle_device);
			$this->dbparking->where("parking_start_time >=",$sdate);
			$this->dbparking->where("parking_end_time <=", $edate);
			$this->dbparking->where("parking_engine", $engine);
			//$this->dbparking->where("parking_duration_sec >=", $limitduration);
			/* if($duration != ""){
				$this->dbparking->where("parking_duration_sec >", $duration);
				$this->dbparking->where("parking_duration_sec <", 99999999);
			} */
			$q = $this->dbparking->get($dbtable);
			//print_r($sdate." ".$edate." ".$engine);exit();
			if ($q->num_rows>0)
			{
				$rows = array_merge($rows, $q->result());
			}
			if($m1 != $m2)
			{
				$this->dbparking = $this->load->database("bgn_parking",true);
				$this->dbparking->order_by("parking_vehicle_no","asc");
				$this->dbparking->order_by("parking_start_time","asc");
				$this->dbparking->where("parking_vehicle_device", $rv[$i]->vehicle_device);
				$this->dbparking->where("parking_start_time >=",$sdate);
				$this->dbparking->where("parking_end_time <=", $edate);
				$this->dbparking->where("parking_engine", $engine);
				//$this->dbparking->where("parking_duration_sec >=", $limitduration);
				/* if($duration != ""){
					$this->dbparking->where("parking_duration_sec >", $duration);
					$this->dbparking->where("parking_duration_sec <", 99999999);
				} */
				$q2 = $this->dbparking->get($dbtable2);
				
				//$total_q2 = count($q2);
				if ($q2->num_rows>0)
				{
					$rows2 = array_merge($rows2, $q2->result());
				}
			}
			if($m1 != $m2)
			{
				$rowsall = array_merge($rows, $rows2);
			}
		}

		//totaldur
		$totaldur = 0;
		if($m1 != $m2)
		{
			for($i=0; $i < count($rowsall); $i++)
			{	
				if($rows[$i]->parking_engine == "OFF" ){
					$totaldur += $rowsall[$i]->parking_duration_sec;			
				}
			}
		}
		else{
			for($i=0; $i < count($rows); $i++)
			{	
				if($rows[$i]->parking_engine == "OFF" ){
					$totaldur += $rows[$i]->parking_duration_sec;
				}				
			}
		}
		
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
		
		
		$this->db->cache_delete_all();
		$this->dbparking->cache_delete_all();
		
		/** PHPExcel */
		include 'class/PHPExcel.php';
			
		/** PHPExcel_Writer_Excel2007 */
		include 'class/PHPExcel/Writer/Excel2007.php';
			
		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();
			
		// Set properties
		$objPHPExcel->getProperties()->setCreator("lacak-mobil.com");
		$objPHPExcel->getProperties()->setLastModifiedBy("lacak-mobil.com");
		$objPHPExcel->getProperties()->setTitle("History ParkingReport");
		$objPHPExcel->getProperties()->setSubject("History Parking Report Lacak-mobil.com");
		$objPHPExcel->getProperties()->setDescription("History Parking Report Lacak-mobil.com");
			
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
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(35);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(35);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);

		//Header
		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->getActiveSheet()->mergeCells('A1:G1');
		$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'History Parking Report');
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
		$objPHPExcel->getActiveSheet()->SetCellValue('E5', 'Location Start');
		$objPHPExcel->getActiveSheet()->SetCellValue('F5', 'Location End');
		$objPHPExcel->getActiveSheet()->SetCellValue('G5', 'Duration');
		
		$objPHPExcel->getActiveSheet()->getStyle('A5:G5')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A5:G5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		$i=1;
		for($j=0;$j<count($data);$j++)
		{
			$objPHPExcel->getActiveSheet()->SetCellValue('A'.(5+$i), $i);
			$objPHPExcel->getActiveSheet()->getStyle('A'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->SetCellValue('B'.(5+$i), $data[$j]->parking_start_time);						
			$objPHPExcel->getActiveSheet()->SetCellValue('C'.(5+$i), $data[$j]->parking_end_time);
			
			if($data[$j]->parking_engine == 0){
				$engine = "OFF";
			}else{
				$engine = "ON";
			}
			$objPHPExcel->getActiveSheet()->SetCellValue('D'.(5+$i), $engine);
			$objPHPExcel->getActiveSheet()->getStyle('D'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$geofence_start = strlen($data[$j]->parking_geofence_start);
			if (strlen($geofence_start == 1)){
				$geofence_start_name = "";
				$objPHPExcel->getActiveSheet()->SetCellValue('E'.(5+$i),strtoupper($geofence_start_name)."  ".$data[$j]->parking_location_start);
				$objPHPExcel->getActiveSheet()->getStyle('E'.(5+$i))->getFont()->setSize(8);
			}
			if (strlen($geofence_start > 1)){
				$geofence_start_name = $data[$j]->parking_geofence_start;
				$objPHPExcel->getActiveSheet()->SetCellValue('E'.(5+$i),strtoupper($geofence_start_name).", ".$data[$j]->parking_location_start);
				$objPHPExcel->getActiveSheet()->getStyle('E'.(5+$i))->getFont()->setSize(8);
			}
			
			$geofence_end = strlen($data[$j]->parking_geofence_end);
			//print_r($geofence_end);exit();
			if (strlen($geofence_end == 1)){
				$geofence_end_name = "";
				$objPHPExcel->getActiveSheet()->SetCellValue('F'.(5+$i),strtoupper($geofence_end_name)."  ".$data[$j]->parking_location_end);
				$objPHPExcel->getActiveSheet()->getStyle('F'.(5+$i))->getFont()->setSize(8);
			}
			if (strlen($geofence_end > 1)){
				$geofence_end_name = $data[$j]->parking_geofence_end;
				$objPHPExcel->getActiveSheet()->SetCellValue('F'.(5+$i),strtoupper($geofence_end_name).",  ".$data[$j]->parking_location_end);
				$objPHPExcel->getActiveSheet()->getStyle('F'.(5+$i))->getFont()->setSize(8);
			}

			$objPHPExcel->getActiveSheet()->SetCellValue('G'.(5+$i), $data[$j]->parking_duration);
			$objPHPExcel->getActiveSheet()->getStyle('G'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$i++;
		}

			$objPHPExcel->getActiveSheet()->SetCellValue('B'.(7+$i), 'Total Duration');
			$objPHPExcel->getActiveSheet()->getStyle('B'.(7+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$objPHPExcel->getActiveSheet()->getStyle('B'.(7+$i))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->mergeCells('E'.(7+$i).':'.'G'.(7+$i));
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
										/*  if(isset($seconds) && $seconds > 0)
										{
											$s_duration =  $seconds." "."Detik"." ";
										} */
									}
		
			$objPHPExcel->getActiveSheet()->SetCellValue('E'.(7+$i), $h_duration." ".$m_duration);
			$objPHPExcel->getActiveSheet()->getStyle('E'.(7+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('E'.(7+$i))->getFont()->setBold(true);
			
			
		$styleArray = array(
				'borders' => array(
				'allborders' => array(
				'style' => PHPExcel_Style_Border::BORDER_THIN
				    )
				  )
				);
			
		$objPHPExcel->getActiveSheet()->getStyle('A5:G'.(7+$i))->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->getStyle('A5:G'.(7+$i))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
		$objPHPExcel->getActiveSheet()->getStyle('A5:G'.(7+$i))->getAlignment()->setWrapText(true);
		// Rename sheet
		$objPHPExcel->getActiveSheet()->setTitle('history_parking');
			
		// Save Excel
		$filedate = date("Ymd",strtotime($startdate))."_".date("Ymd",strtotime($enddate));
		$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
		@mkdir(REPORT_PATH, DIR_WRITE_MODE); //REPORT_PATH -> config->constant
		$filecreatedname = "histparking_".$vehicle_no."_".$filedate.".xls";
			
		$objWriter->save(REPORT_PATH.$filecreatedname);
		
		$output = '{"success":true,"errMsg":"","filename":"' .base_url() . "assets/media/report/" . $filecreatedname.'"}';
		echo $output;
		return;
	}
	
	
}