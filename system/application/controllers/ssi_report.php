<?php
include "base.php";

class Ssi_report extends Base {
	var $otherdb;
	
	function Ssi_report()
	{
		parent::Base();	
		$this->load->model("gpsmodel");
		$this->load->model("vehiclemodel");
		$this->load->model("configmodel");
		$this->load->helper('common_helper');
		$this->load->helper('kopindosat');
	}
	
	function index(){
		$this->db->order_by("vehicle_no","asc");
		//$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		if ($this->sess->user_group != 0){
			$this->db->where("vehicle_group", $this->sess->user_group);
		}if($this->sess->user_id == 2232 || $this->sess->user_id == 2239){
			$this->db->where("vehicle_company", $this->sess->user_company);
		}if($this->sess->user_id == 1933){
			$this->db->where("vehicle_user_id", $this->sess->user_id);
		}if($this->sess->user_id == 2288 && $this->sess->user_type == 5){
			$this->db->where("vehicle_user_id", 1933);
		}
		
		$this->db->where("vehicle_status <>",3);
		$qv = $this->db->get("vehicle");
		$rv = $qv->result();
		
		$params["myvehicles"] = $rv;
		
		$html = $this->load->view('trackers/vehicleonline', $params, true);
		$callback["error"] = false;
		$callback["html"] = $html;
			
		echo json_encode($callback);
	}
	
	function vehicleonline()
	{
		$date = isset($_POST['date']) ? $_POST['date'] : 0;
		$hour = isset($_POST['hour']) ? $_POST['hour'] : 0;
		$vehicle = isset($_POST['vehicle']) ? $_POST['vehicle'] : 0;
		$type = isset($_POST['type']) ? $_POST['type'] : 0;
			
		$now = date("Y-m-d");
		$fdate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($date ." " . $hour . ":59")));
		$cek_date = date("Y-m-d", strtotime("-7 hour", strtotime($date ." " . $hour . ":59")));
		
		if($type == 1){ //online
			if( $vehicle == 0 ) 
			{
				//all vehicle		
				$this->db->order_by("vehicle_no","asc");
				$this->db->where("vehicle_active_date2 >=", date("Ymd"));
				if ($this->sess->user_group != 0){
					$this->db->where("vehicle_group", $this->sess->user_group);
				}if($this->sess->user_id == 2232 || $this->sess->user_id == 2239){
					$this->db->where("vehicle_company", $this->sess->user_company);
				}if($this->sess->user_id == 1933){
					$this->db->where("vehicle_user_id", $this->sess->user_id);
					//$this->db->or_where("vehicle_user_id", 3153); //ssi umum
				}if($this->sess->user_id == 2288 && $this->sess->user_type == 5){
					$this->db->where("vehicle_user_id", 1933);
				}
				$this->db->where("vehicle_status <>",3);
				$this->db->where("vehicle_isred <>",1);
				$this->db->join("group", "vehicle_group = group_id", "left outer");
				$qm = $this->db->get("vehicle");
				$rm = $qm->result();
				
				foreach($rm as $v)
				{
					$this->db->order_by("vehicle_device", "asc");
					$this->db->where("vehicle_device", $v->vehicle_device);
					$this->db->limit(1);
					$this->db->join("group", "vehicle_group = group_id", "left outer");
					$qv = $this->db->get("vehicle");
					$rowvehicle = $qv->row();
					$rowv[] = $qv->row();
				
				}
			} //end if allvehicle
			
			//per vehicle atau lebih (online)
			else
			{
				$this->db->order_by("vehicle_name", "asc");
				$this->db->order_by("vehicle_no", "asc");		
				$this->db->where("vehicle_status <>", 3);
				$this->db->where("vehicle_isred <>",1);
				$this->db->where("vehicle_device", $vehicle);
				$this->db->limit(1);
				$this->db->join("group", "vehicle_group = group_id", "left outer");
				$qm = $this->db->get("vehicle");
				$rm = $qm->row();
				$rowv[] = $qm->row();
				
			}
		
		}else {//offline
			if( $vehicle == 0 )
			{
				//all vehicle	 (offline)	
				$this->db->order_by("vehicle_no","asc");
				$this->db->where("vehicle_active_date2 >=", date("Ymd"));
				if ($this->sess->user_group != 0){
					$this->db->where("vehicle_group", $this->sess->user_group);
				}if($this->sess->user_id == 2232 || $this->sess->user_id == 2239){
					$this->db->where("vehicle_company", $this->sess->user_company);
				}if($this->sess->user_id == 1933){
					$this->db->where("vehicle_user_id", $this->sess->user_id);
					//$this->db->or_where("vehicle_user_id", 3153); //ssi umum
				}if($this->sess->user_id == 2288 && $this->sess->user_type == 5){
					$this->db->where("vehicle_user_id", 1933);
				}
				$this->db->where("vehicle_status <>",3);
				$this->db->where("vehicle_isred",1);
				$this->db->join("group", "vehicle_group = group_id", "left outer");
				$qm = $this->db->get("vehicle");
				$rm = $qm->result();
				
				foreach($rm as $v)
				{
					$this->db->order_by("vehicle_device", "asc");
					$this->db->where("vehicle_device", $v->vehicle_device);
					$this->db->limit(1);
					$this->db->join("group", "vehicle_group = group_id", "left outer");
					$qv = $this->db->get("vehicle");
					$rowvehicle = $qv->row();
					$rowv[] = $qv->row();
					
				}
			} //end if allvehicle
			
			//per vehicle atau lebih (offline)
			else
			{
				$this->db->order_by("vehicle_name", "asc");
				$this->db->order_by("vehicle_no", "asc");		
				$this->db->where("vehicle_status <>", 3);
				$this->db->where("vehicle_isred",1);
				$this->db->where("vehicle_device", $vehicle);
				$this->db->limit(1);
				$this->db->join("group", "vehicle_group = group_id", "left outer");
				$qm = $this->db->get("vehicle");
				$rm = $qm->row();
				$rowv[] = $qm->row();
			
			}
		}
		$trows = count($rm);
		
			/** PHPExcel */
			include 'class/PHPExcel.php';
			
			/** PHPExcel_Writer_Excel2007 */
			include 'class/PHPExcel/Writer/Excel2007.php';
			
			// Create new PHPExcel object
			$objPHPExcel = new PHPExcel();
			
			// Set properties
			$objPHPExcel->getProperties()->setCreator("lacak-mobil.com");
			$objPHPExcel->getProperties()->setLastModifiedBy("lacak-mobil.com");
			$objPHPExcel->getProperties()->setTitle("Vehicle Online Report");
			$objPHPExcel->getProperties()->setSubject("Vehicle Online Detail Report Lacak-mobil.com");
			$objPHPExcel->getProperties()->setDescription("Vehicle Online Detail Repor Lacak-mobil.com");
			
			//set document
			$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(1);
			$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.75);
			$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.75);
			$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(1);
			$objPHPExcel->getDefaultStyle()->getFont()->setName('Times New Roman');
			$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
			$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
			$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);		
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
			
			if($type == 1){
				$typename = "Online";
			}else{
				$typename = "Offline";
			}
			//Header
			$objPHPExcel->setActiveSheetIndex(0);
			$objPHPExcel->getActiveSheet()->mergeCells('A1:F1');
			$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Vehicle '.$typename.' Report');
			$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->SetCellValue('E3', 'Total Vehicle :');
			$objPHPExcel->getActiveSheet()->getStyle('E3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('E3')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->SetCellValue('F3', count($rm));
			$objPHPExcel->getActiveSheet()->getStyle('F3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			
			//Top Header
			$objPHPExcel->getActiveSheet()->SetCellValue('A5', 'No');
			$objPHPExcel->getActiveSheet()->SetCellValue('B5', 'Vehicle No');
			$objPHPExcel->getActiveSheet()->SetCellValue('C5', 'Vehicle Name');
			$objPHPExcel->getActiveSheet()->SetCellValue('D5', 'Sentra');
			$objPHPExcel->getActiveSheet()->SetCellValue('E5', 'Sim Card');
			$objPHPExcel->getActiveSheet()->SetCellValue('F5', 'GPS Status');
			$objPHPExcel->getActiveSheet()->getStyle('A5:F5')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A5:F5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			//Write Data
			$i = 0;
			for($j=0;$j<$trows;$j++){
				$objPHPExcel->getActiveSheet()->SetCellValue('A'.(6+$i), $i+1);
				
							$objPHPExcel->getActiveSheet()->SetCellValue('B'.(6+$i), $rm[$j]->vehicle_no);
							$objPHPExcel->getActiveSheet()->SetCellValue('C'.(6+$i), $rm[$j]->vehicle_name);
							$objPHPExcel->getActiveSheet()->SetCellValue('D'.(6+$i), $rm[$j]->group_name);
							$objPHPExcel->getActiveSheet()->SetCellValue('E'.(6+$i), $rm[$j]->vehicle_card_no . " (" . $rm[$j]->vehicle_operator . ")");
							if($rm[$j]->vehicle_isred == 1){
								$isred = "Tidak Update";
							}else{
								$isred = "Update";
							}
							$objPHPExcel->getActiveSheet()->SetCellValue('F'.(6+$i), $isred);
				$i++;
			}
			
			$styleArray = array(
				  'borders' => array(
				    'allborders' => array(
				      'style' => PHPExcel_Style_Border::BORDER_THIN
				    )
				  )
				);
			$objPHPExcel->getActiveSheet()->getStyle('A5:F'.(5+$trows))->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getStyle('A5:F'.(5+$trows))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
			$objPHPExcel->getActiveSheet()->getStyle('A5:F'.(5+$trows))->getAlignment()->setWrapText(true);
			// Rename sheet
			$objPHPExcel->getActiveSheet()->setTitle('vehicle_online_report');
			
			// Save Excel
			$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
			@mkdir(REPORT_PATH, DIR_WRITE_MODE); //REPORT_PATH -> config->constant
			if($type == 1){
				$title_name = "online";
			}else{
				$title_name = "offline";
			}
			$filecreatedname = "vehicle_".$title_name."_".date("dmY"). ".xls";
			
			$objWriter->save(REPORT_PATH.$filecreatedname);
		
			$output = '{"success":true,"errMsg":"","filename":"' .base_url() . "assets/media/report/" . $filecreatedname.'"}';
			echo $output;
			return;
	}
	
	function vehicleonline2()
	{
		$date = isset($_POST['date']) ? $_POST['date'] : 0;
		$hour = isset($_POST['hour']) ? $_POST['hour'] : 0;
		$vehicle = isset($_POST['vehicle']) ? $_POST['vehicle'] : 0;
			
		$now = date("Y-m-d");
		$fdate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($date ." " . $hour . ":59")));
		$cek_date = date("Y-m-d", strtotime("-7 hour", strtotime($date ." " . $hour . ":59")));
	
			//all vehicle		
			$this->db->order_by("vehicle_no","asc");
			//$this->db->where("vehicle_active_date2 >=", date("Ymd"));
			$this->db->where("vehicle_user_id", $this->sess->user_id);
			$this->db->where("vehicle_status <>",3);
			$this->db->join("group", "vehicle_group = group_id", "left outer");
			$qm = $this->db->get("vehicle");
			$rows = $qm->result();
		
			$trows = count($rows);
			//print_r($trows);exit();
			/** PHPExcel */
			include 'class/PHPExcel.php';
			
			/** PHPExcel_Writer_Excel2007 */
			include 'class/PHPExcel/Writer/Excel2007.php';
			
			// Create new PHPExcel object
			$objPHPExcel = new PHPExcel();
			
			// Set properties
			$objPHPExcel->getProperties()->setCreator("lacak-mobil.com");
			$objPHPExcel->getProperties()->setLastModifiedBy("lacak-mobil.com");
			$objPHPExcel->getProperties()->setTitle("Vehicle Online Report");
			$objPHPExcel->getProperties()->setSubject("Vehicle Online Detail Report Lacak-mobil.com");
			$objPHPExcel->getProperties()->setDescription("Vehicle Online Detail Repor Lacak-mobil.com");
			
			//set document
			$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(1);
			$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.75);
			$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.75);
			$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(1);
			$objPHPExcel->getDefaultStyle()->getFont()->setName('Times New Roman');
			$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
			$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
			$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);		
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);;
			$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(12);
			/* $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10); */
		
			//Header
			$objPHPExcel->setActiveSheetIndex(0);
			$objPHPExcel->getActiveSheet()->mergeCells('A1:G1');
			$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Vehicle Online Report2');
			$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
			/*$objPHPExcel->getActiveSheet()->SetCellValue('B3', 'Date :');
			$objPHPExcel->getActiveSheet()->getStyle('B3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('B3')->getFont()->setBold(true);
			$filter_date = kopindosatformatdatetime($date ." " . $hour . ":59");
			$objPHPExcel->getActiveSheet()->SetCellValue('C3', $filter_date);*/
			$objPHPExcel->getActiveSheet()->SetCellValue('F3', 'Total Record :');
			$objPHPExcel->getActiveSheet()->getStyle('F3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('F3')->getFont()->setBold(true);
			
			
			//Top Header
			$objPHPExcel->getActiveSheet()->SetCellValue('A7', 'No');
			$objPHPExcel->getActiveSheet()->SetCellValue('B7', 'Vehicle No');
			$objPHPExcel->getActiveSheet()->SetCellValue('C7', 'Vehicle Name');
			$objPHPExcel->getActiveSheet()->SetCellValue('D7', 'Sentra');
			$objPHPExcel->getActiveSheet()->SetCellValue('E7', 'Sim Card');
			$objPHPExcel->getActiveSheet()->SetCellValue('F7', 'Operator');
			$objPHPExcel->getActiveSheet()->SetCellValue('G7', 'Tanggal Pasang');
			$objPHPExcel->getActiveSheet()->getStyle('A7:G7')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A7:G7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			//Write Data
			$i = 1;
			for ($j=0;$j<count($rows);$j++)
			{
				$objPHPExcel->getActiveSheet()->SetCellValue('A'.(7+$i), $i);
				$objPHPExcel->getActiveSheet()->getStyle('A'.(7+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				
				/* $vehicle_front = substr($rows[$j]->vehicle_no,0,1);
				$vehicle_mid = substr($rows[$j]->vehicle_no,1,4);
				$vehicle_end = substr($rows[$j]->vehicle_no,5,3); */
				
				$objPHPExcel->getActiveSheet()->SetCellValue('B'.(7+$i), $rows[$j]->vehicle_no);
				//$objPHPExcel->getActiveSheet()->SetCellValue('B'.(7+$i), $vehicle_front." ".$vehicle_mid." ".$vehicle_end);
				$objPHPExcel->getActiveSheet()->getStyle('B'.(7+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				
				
				$objPHPExcel->getActiveSheet()->SetCellValue('C'.(7+$i), $rows[$j]->vehicle_name);
				$objPHPExcel->getActiveSheet()->getStyle('C'.(7+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->SetCellValue('D'.(7+$i), $rows[$j]->group_name);
				$objPHPExcel->getActiveSheet()->getStyle('D'.(7+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->SetCellValue('E'.(7+$i), $rows[$j]->vehicle_card_no);
				$objPHPExcel->getActiveSheet()->getStyle('E'.(7+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->SetCellValue('F'.(7+$i), $rows[$j]->vehicle_operator);
				$objPHPExcel->getActiveSheet()->getStyle('F'.(7+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->SetCellValue('G'.(7+$i), $rows[$j]->vehicle_tanggal_pasang);
				$objPHPExcel->getActiveSheet()->getStyle('G'.(7+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				
				$i++;
			}
		
			$styleArray = array(
				  'borders' => array(
				    'allborders' => array(
				      'style' => PHPExcel_Style_Border::BORDER_THIN
				    )
				  )
				);
		
			$objPHPExcel->getActiveSheet()->getStyle('A7:G'.(6+$i))->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getStyle('A7:G'.(6+$i))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
			$objPHPExcel->getActiveSheet()->getStyle('A7:G'.(6+$i))->getAlignment()->setWrapText(true);
			// Rename sheet
			$objPHPExcel->getActiveSheet()->setTitle('vehicle_online_report2');
			
			// Save Excel
			$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
			@mkdir(REPORT_PATH, DIR_WRITE_MODE); //REPORT_PATH -> config->constant
			$filecreatedname = "vehicle_online_".date("dmY", strtotime($date)). ".xls";
			
			$objWriter->save(REPORT_PATH.$filecreatedname);
		
			$output = '{"success":true,"errMsg":"","filename":"' .base_url() . "assets/media/report/" . $filecreatedname.'"}';
			echo $output;
			return;
	}
	
	function vehicleonline3() //for tag
	{
		$date = isset($_POST['date']) ? $_POST['date'] : 0;
		$hour = isset($_POST['hour']) ? $_POST['hour'] : 0;
		$vehicle = isset($_POST['vehicle']) ? $_POST['vehicle'] : 0;
			
		$now = date("Y-m-d");
		$fdate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($date ." " . $hour . ":59")));
		$cek_date = date("Y-m-d", strtotime("-7 hour", strtotime($date ." " . $hour . ":59")));
	
			//all vehicle		
			$this->db->order_by("vehicle_no","asc");
			//$this->db->where("vehicle_active_date2 >=", date("Ymd"));
			$this->db->where("vehicle_user_id",3212);
			$this->db->where("vehicle_status <>",3);
			$this->db->join("company", "vehicle_company = company_id", "left outer");
			$qm = $this->db->get("vehicle");
			$rows = $qm->result();
		
			$trows = count($rows);
			//print_r($trows);exit();
			/** PHPExcel */
			include 'class/PHPExcel.php';
			
			/** PHPExcel_Writer_Excel2007 */
			include 'class/PHPExcel/Writer/Excel2007.php';
			
			// Create new PHPExcel object
			$objPHPExcel = new PHPExcel();
			
			// Set properties
			$objPHPExcel->getProperties()->setCreator("lacak-mobil.com");
			$objPHPExcel->getProperties()->setLastModifiedBy("lacak-mobil.com");
			$objPHPExcel->getProperties()->setTitle("Vehicle Online Report");
			$objPHPExcel->getProperties()->setSubject("Vehicle Online Detail Report Lacak-mobil.com");
			$objPHPExcel->getProperties()->setDescription("Vehicle Online Detail Repor Lacak-mobil.com");
			
			//set document
			$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(1);
			$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.75);
			$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.75);
			$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(1);
			$objPHPExcel->getDefaultStyle()->getFont()->setName('Times New Roman');
			$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
			$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
			$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);		
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);;
			$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(12);
			/* $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10); */
		
			//Header
			$objPHPExcel->setActiveSheetIndex(0);
			$objPHPExcel->getActiveSheet()->mergeCells('A1:G1');
			$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Vehicle Online Report2');
			$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
			/*$objPHPExcel->getActiveSheet()->SetCellValue('B3', 'Date :');
			$objPHPExcel->getActiveSheet()->getStyle('B3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('B3')->getFont()->setBold(true);
			$filter_date = kopindosatformatdatetime($date ." " . $hour . ":59");
			$objPHPExcel->getActiveSheet()->SetCellValue('C3', $filter_date);*/
			$objPHPExcel->getActiveSheet()->SetCellValue('F3', 'Total Record :');
			$objPHPExcel->getActiveSheet()->getStyle('F3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('F3')->getFont()->setBold(true);
			
			
			//Top Header
			$objPHPExcel->getActiveSheet()->SetCellValue('A7', 'No');
			$objPHPExcel->getActiveSheet()->SetCellValue('B7', 'Vehicle No');
			$objPHPExcel->getActiveSheet()->SetCellValue('C7', 'Vehicle Name');
			$objPHPExcel->getActiveSheet()->SetCellValue('D7', 'Area');
			$objPHPExcel->getActiveSheet()->SetCellValue('E7', 'Sim Card');
			$objPHPExcel->getActiveSheet()->SetCellValue('F7', 'Operator');
			$objPHPExcel->getActiveSheet()->SetCellValue('G7', 'Tanggal Pasang');
			$objPHPExcel->getActiveSheet()->getStyle('A7:G7')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A7:G7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			//Write Data
			$i = 1;
			for ($j=0;$j<count($rows);$j++)
			{
				$objPHPExcel->getActiveSheet()->SetCellValue('A'.(7+$i), $i);
				$objPHPExcel->getActiveSheet()->getStyle('A'.(7+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				
				/* $vehicle_front = substr($rows[$j]->vehicle_no,0,1);
				$vehicle_mid = substr($rows[$j]->vehicle_no,1,4);
				$vehicle_end = substr($rows[$j]->vehicle_no,5,3); */
				
				$objPHPExcel->getActiveSheet()->SetCellValue('B'.(7+$i), $rows[$j]->vehicle_no);
				//$objPHPExcel->getActiveSheet()->SetCellValue('B'.(7+$i), $vehicle_front." ".$vehicle_mid." ".$vehicle_end);
				$objPHPExcel->getActiveSheet()->getStyle('B'.(7+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				
				
				$objPHPExcel->getActiveSheet()->SetCellValue('C'.(7+$i), $rows[$j]->vehicle_name);
				$objPHPExcel->getActiveSheet()->getStyle('C'.(7+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->SetCellValue('D'.(7+$i), $rows[$j]->company_name);
				$objPHPExcel->getActiveSheet()->getStyle('D'.(7+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->SetCellValue('E'.(7+$i), $rows[$j]->vehicle_card_no);
				$objPHPExcel->getActiveSheet()->getStyle('E'.(7+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->SetCellValue('F'.(7+$i), $rows[$j]->vehicle_operator);
				$objPHPExcel->getActiveSheet()->getStyle('F'.(7+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->SetCellValue('G'.(7+$i), $rows[$j]->vehicle_tanggal_pasang);
				$objPHPExcel->getActiveSheet()->getStyle('G'.(7+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				
				$i++;
			}
		
			$styleArray = array(
				  'borders' => array(
				    'allborders' => array(
				      'style' => PHPExcel_Style_Border::BORDER_THIN
				    )
				  )
				);
		
			$objPHPExcel->getActiveSheet()->getStyle('A7:G'.(6+$i))->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getStyle('A7:G'.(6+$i))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
			$objPHPExcel->getActiveSheet()->getStyle('A7:G'.(6+$i))->getAlignment()->setWrapText(true);
			// Rename sheet
			$objPHPExcel->getActiveSheet()->setTitle('vehicle_online_report2');
			
			// Save Excel
			$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
			@mkdir(REPORT_PATH, DIR_WRITE_MODE); //REPORT_PATH -> config->constant
			$filecreatedname = "vehicle_online_".date("dmY", strtotime($date)). ".xls";
			
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
		if ($this->sess->user_group == 0)  {
			$this->db->where("vehicle_user_id", $this->sess->user_id);
		}
		if ($this->sess->user_group != 0)  {
			$this->db->where("vehicle_group", $this->sess->user_group);
		}
		
		$q = $this->db->get("vehicle");
		
		$rows = $q->result();
		$this->params["vehicles"] = $rows;
		$this->params["content"] = $this->load->view('transporter/report/mn_operasional', $this->params, true);		
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
		//$duration = $this->input->post("duration");
		$report = "operasional_";
		
		$sdate = date("Y-m-d H:i:s", strtotime($startdate." "."00:00:00"));
		$edate = date("Y-m-d H:i:s", strtotime($enddate." "."23:59:59"));
		
		$m1 = date("F", strtotime($startdate)); 
		$m2 = date("F", strtotime($enddate)); 
		$year = date("Y", strtotime($startdate));
		$rows = array();
		$rows2 = array();
		$total_q = 0;
		$total_q2 = 0;
		$limitduration = 300; //5menit
		
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
			$this->dbtrip = $this->load->database("ssi_operasional",true);
			$this->dbtrip->order_by("trip_mileage_vehicle_id","asc");
			$this->dbtrip->order_by("trip_mileage_start_time","asc");
			$this->dbtrip->where("trip_mileage_vehicle_id", $rv[$i]->vehicle_device);
			$this->dbtrip->where("trip_mileage_start_time >=",$sdate);
			$this->dbtrip->where("trip_mileage_end_time <=", $edate);
			//$this->dbtrip->where("trip_mileage_duration_sec >=", $limitduration);
			if($engine != ""){
				$this->dbtrip->where("trip_mileage_engine", $engine);
			}
			/* if($duration != ""){
				$this->dbtrip->where("trip_mileage_duration_sec >", $duration);
				$this->dbtrip->where("trip_mileage_duration_sec <", 99999999);
			} */
			$q = $this->dbtrip->get($dbtable);
		
			if ($q->num_rows>0)
			{
				$rows = array_merge($rows, $q->result());
			}
			if($m1 != $m2)
			{
				$this->dbtrip = $this->load->database("ssi_operasional",true);
				$this->dbtrip->order_by("trip_mileage_vehicle_id","asc");
				$this->dbtrip->order_by("trip_mileage_start_time","asc");
				$this->dbtrip->where("trip_mileage_vehicle_id", $rv[$i]->vehicle_device);
				$this->dbtrip->where("trip_mileage_start_time >=",$sdate);
				$this->dbtrip->where("trip_mileage_end_time <=", $edate);
				//$this->dbtrip->where("trip_mileage_duration_sec >=", $limitduration);
				if($engine != ""){
					$this->dbtrip->where("trip_mileage_engine", $engine);
				}
				/* if($duration != ""){
					$this->dbtrip->where("trip_mileage_duration_sec >", $duration);
					$this->dbtrip->where("trip_mileage_duration_sec <", 99999999);
				} */
				$q2 = $this->dbtrip->get($dbtable2);
				
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

		//get data team
		$this->dbtransporter = $this->load->database("transporter", true);
		$this->dbtransporter->order_by("team_date", "desc");
		$this->dbtransporter->order_by("team_time", "desc");
		$this->dbtransporter->order_by("team_vehicle_no", "asc");
		$this->dbtransporter->where("team_flag", 0);
		$this->dbtransporter->where("team_date >=", $startdate);
		$this->dbtransporter->where("team_date <=", $enddate);
		if($vehicle != 0){
			$this->dbtransporter->where("team_vehicle_device", $vehicle);
		}
		$qr = $this->dbtransporter->get("ssi_team");
		$rows_r = $qr->result();
		
		
		if($m1 != $m2)
		{
			$params['data'] = $rowsall;
		}
		else
		{
			$params['data'] = $rows;
		}
		
		$params['data_r'] = $rows_r;
		$params['totalduration'] = $totalduration;
		$params['totalcummulative'] = $totalcummulative;
		$params['totalcummulative_on'] = $totalcummulative_on;
		$params['totalcummulative_off'] = $totalcummulative_off;
	
		$html = $this->load->view("transporter/report/result_operasional", $params, true);
		$callback['error'] = false;
		$callback['html'] = $html;
		echo json_encode($callback);
		return;
		
	}
	
	function mn_dailydataoperasional()
	{
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		
		$this->db->order_by("vehicle_name", "asc");
		$this->db->order_by("vehicle_no", "asc");		
		$this->db->where("vehicle_status <>", 3);
		if ($this->sess->user_group == 0)  {
			$this->db->where("vehicle_user_id", $this->sess->user_id);
		}
		if ($this->sess->user_group != 0)  {
			$this->db->where("vehicle_group", $this->sess->user_group);
		}
		//$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		
		$q = $this->db->get("vehicle");
		
		/* if ($q->num_rows() == 0)
		{
			redirect(base_url());
		} */
		
		$rows = $q->result();
		$this->params["vehicles"] = $rows;
		$this->params["content"] = $this->load->view('transporter/report/mn_operasional_daily', $this->params, true);		
		$this->load->view("templatesess", $this->params);
	}
	
	function daily_dataoperasional()
	{
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		
		$vehicle = $this->input->post("vehicle");
		$startdate = $this->input->post("startdate");
		$enddate = $this->input->post("enddate");
		$starttime = $this->input->post("starttime");
		$endtime = $this->input->post("endtime");
		$engine = $this->input->post("engine");
		$shift = $this->input->post("shift");
		
		$sdate = date("Y-m-d H:i:s", strtotime($startdate . " " . $starttime));
		$edate = date("Y-m-d H:i:s", strtotime($enddate . " " . $endtime));
		
		$report = "operasional_";
		
		/* if($shift == 1){
			$starttime = "07:00:00";
			$sdate = date("Y-m-d H:i:s", strtotime($startdate . " " . $starttime));
			$dateplus = date_create($sdate);
			date_add($dateplus, date_interval_create_from_date_string('9 hours'));
				
			$endtime = date_format($dateplus, 'H:i:s');
			$enddate = date_format($dateplus, 'Y-m-d');
			$edate = date("Y-m-d H:i:s", strtotime($enddate . " " . $endtime));
			
		}else if($shift == 2){
			$starttime = "15:00:00";
			$sdate = date("Y-m-d H:i:s", strtotime($startdate . " " . $starttime));
			$dateplus = date_create($sdate);
			date_add($dateplus, date_interval_create_from_date_string('8 hours'));
				
			$endtime = date_format($dateplus, 'H:i:s');
			$enddate = date_format($dateplus, 'Y-m-d');
			$edate = date("Y-m-d H:i:s", strtotime($enddate . " " . $endtime));
		}else if($shift == 3){
			$starttime = "22:00:00";
			$sdate = date("Y-m-d H:i:s", strtotime($startdate . " " . $starttime));
			$dateplus = date_create($sdate);
			date_add($dateplus, date_interval_create_from_date_string('8 hours'));
				
			$endtime = date_format($dateplus, 'H:i:s');
			$enddate = date_format($dateplus, 'Y-m-d');
			$edate = date("Y-m-d H:i:s", strtotime($enddate . " " . $endtime));
		}else{
			$sdate = date("Y-m-d H:i:s", strtotime($startdate." "."00:00:00"));
			$edate = date("Y-m-d H:i:s", strtotime($enddate." "."23:59:59"));
		} */
		//print_r($sdate." ".$edate);exit();
		
		$m1 = date("F", strtotime($startdate)); 
		$m2 = date("F", strtotime($enddate)); 
		$year = date("Y", strtotime($startdate));
		$rows = array();
		$rows2 = array();
		$total_q = 0;
		$total_q2 = 0;
		$limitduration = 300; //5menit
		
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
			$this->dbtrip = $this->load->database("ssi_operasional",true);
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
				$this->dbtrip = $this->load->database("ssi_operasional",true);
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

		//get data team
		$this->dbtransporter = $this->load->database("transporter", true);
		$this->dbtransporter->order_by("team_date", "desc");
		$this->dbtransporter->order_by("team_time", "desc");
		$this->dbtransporter->order_by("team_vehicle_no", "asc");
		$this->dbtransporter->where("team_flag", 0);
		$this->dbtransporter->where("team_sch_start >=", $sdate);
		$this->dbtransporter->where("team_sch_end <=", $edate);
		//$this->dbtransporter->where("team_shift", $shift);
		if($vehicle != 0){
			$this->dbtransporter->where("team_vehicle_device", $vehicle);
		}
		if($shift != ""){
			$this->dbtransporter->where("team_shift", $shift);
		}
		$qr = $this->dbtransporter->get("ssi_team");
		$rows_r = $qr->result();
		
		
		if($m1 != $m2)
		{
			$params['data'] = $rowsall;
		}
		else
		{
			$params['data'] = $rows;
		}
		
		$params['data_r'] = $rows_r;
		$params['totalduration'] = $totalduration;
		$params['totalcummulative'] = $totalcummulative;
		$params['totalcummulative_on'] = $totalcummulative_on;
		$params['totalcummulative_off'] = $totalcummulative_off;
	
		$html = $this->load->view("transporter/report/result_operasional_daily", $params, true);
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
		//print_r('disini');exit();
		$vehicle = $this->input->post("vehicle");
		$startdate = $this->input->post("startdate");
		$enddate = $this->input->post("enddate");
		$engine = $this->input->post("engine");
		//$duration = $this->input->post("duration");
		$report = "operasional_";
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
		$limitduration = 300; //5menit
		
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
		
		/* if ($qv->num_rows == 0)
		{
			redirect(base_url());
		} */
		$rv = $qv->result();
		//end get vehicle
	
		//get data operasional
		for($i=0;$i<count($rv);$i++)
		{
			$this->dbtrip = $this->load->database("ssi_operasional",true);
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
				$this->dbtrip = $this->load->database("ssi_operasional",true);
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
		
		
		/* $this->db->cache_delete_all();
		$this->dbtrip->cache_delete_all(); */
		
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
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(35);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(35);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(12);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(12);
		$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(12);

		//Header
		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->getActiveSheet()->mergeCells('A1:I1');
		$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Daily Operational Data Report');
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
		$objPHPExcel->getActiveSheet()->SetCellValue('H5', 'Trip Mileage');
		$objPHPExcel->getActiveSheet()->SetCellValue('I5', 'Cumulative Mileage');
		
		$objPHPExcel->getActiveSheet()->getStyle('A5:I5')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A5:I5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		$i=1;
		for($j=0;$j<count($data);$j++)
		{
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
			
			$geofence_start = strlen($data[$j]->trip_mileage_geofence_start);
			if (strlen($geofence_start == 1)){
				$geofence_start_name = "";
				$objPHPExcel->getActiveSheet()->SetCellValue('E'.(5+$i),$geofence_start_name."  ".$data[$j]->trip_mileage_location_start);
				$objPHPExcel->getActiveSheet()->getStyle('E'.(5+$i))->getFont()->setSize(8);
			}
			if (strlen($geofence_start > 1)){
				$geofence_start_name = $data[$j]->trip_mileage_geofence_start;
				$objPHPExcel->getActiveSheet()->SetCellValue('E'.(5+$i),$geofence_start_name.", ".$data[$j]->trip_mileage_location_start);
				$objPHPExcel->getActiveSheet()->getStyle('E'.(5+$i))->getFont()->setSize(8);
			}
			
			$geofence_end = strlen($data[$j]->trip_mileage_geofence_end);
			//print_r($geofence_end);exit();
			if (strlen($geofence_end == 1)){
				$geofence_end_name = "";
				$objPHPExcel->getActiveSheet()->SetCellValue('F'.(5+$i),$geofence_end_name."  ".$data[$j]->trip_mileage_location_end);
				$objPHPExcel->getActiveSheet()->getStyle('F'.(5+$i))->getFont()->setSize(8);
			}
			if (strlen($geofence_end > 1)){
				$geofence_end_name = $data[$j]->trip_mileage_geofence_end;
				$objPHPExcel->getActiveSheet()->SetCellValue('F'.(5+$i),$geofence_end_name.",  ".$data[$j]->trip_mileage_location_end);
				$objPHPExcel->getActiveSheet()->getStyle('F'.(5+$i))->getFont()->setSize(8);
			}

			$objPHPExcel->getActiveSheet()->SetCellValue('G'.(5+$i), $data[$j]->trip_mileage_duration);
			$objPHPExcel->getActiveSheet()->getStyle('G'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

			$objPHPExcel->getActiveSheet()->SetCellValue('H'.(5+$i), $data[$j]->trip_mileage_trip_mileage." "."KM");
			$objPHPExcel->getActiveSheet()->getStyle('H'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->SetCellValue('I'.(5+$i), $data[$j]->trip_mileage_cummulative_mileage." "."KM");
			$objPHPExcel->getActiveSheet()->getStyle('I'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$i++;
		}

			$objPHPExcel->getActiveSheet()->SetCellValue('B'.(6+$i), 'Total Mileage');
			$objPHPExcel->getActiveSheet()->getStyle('B'.(6+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$objPHPExcel->getActiveSheet()->getStyle('B'.(6+$i))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->mergeCells('G'.(6+$i).':'.'I'.(6+$i));
			$objPHPExcel->getActiveSheet()->SetCellValue('G'.(6+$i), $totalcummulative_on.' '.'KM');
			$objPHPExcel->getActiveSheet()->getStyle('G'.(6+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('G'.(6+$i))->getFont()->setBold(true);
			
			$objPHPExcel->getActiveSheet()->SetCellValue('B'.(7+$i), 'Total Duration');
			$objPHPExcel->getActiveSheet()->getStyle('B'.(7+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$objPHPExcel->getActiveSheet()->getStyle('B'.(7+$i))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->mergeCells('G'.(7+$i).':'.'I'.(7+$i));
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
										 if(isset($seconds) && $seconds > 0)
										{
											$s_duration =  $seconds." "."Detik"." ";
										}
									}
		
			$objPHPExcel->getActiveSheet()->SetCellValue('G'.(7+$i), $h_duration." ".$m_duration);
			$objPHPExcel->getActiveSheet()->getStyle('G'.(7+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('G'.(7+$i))->getFont()->setBold(true);
			
			
		$styleArray = array(
				'borders' => array(
				'allborders' => array(
				'style' => PHPExcel_Style_Border::BORDER_THIN
				    )
				  )
				);
			
		$objPHPExcel->getActiveSheet()->getStyle('A5:I'.(7+$i))->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->getStyle('A5:I'.(7+$i))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
		$objPHPExcel->getActiveSheet()->getStyle('A5:I'.(7+$i))->getAlignment()->setWrapText(true);
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
	
	function daily_dataoperasional_excel()
	{
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		
		$vehicle = $this->input->post("vehicle");
		$startdate = $this->input->post("startdate");
		$enddate = $this->input->post("enddate");
		$starttime = $this->input->post("starttime");
		$endtime = $this->input->post("endtime");
		$engine = $this->input->post("engine");
		$shift = $this->input->post("shift");
		
		$sdate = date("Y-m-d H:i:s", strtotime($startdate . " " . $starttime));
		$edate = date("Y-m-d H:i:s", strtotime($enddate . " " . $endtime));
		
		$report = "operasional_";
		
		/* if($shift == 1){
			$starttime = "07:00:00";
			$sdate = date("Y-m-d H:i:s", strtotime($startdate . " " . $starttime));
			$dateplus = date_create($sdate);
			date_add($dateplus, date_interval_create_from_date_string('9 hours'));
				
			$endtime = date_format($dateplus, 'H:i:s');
			$enddate = date_format($dateplus, 'Y-m-d');
			$edate = date("Y-m-d H:i:s", strtotime($enddate . " " . $endtime));
			
		}else if($shift == 2){
			$starttime = "15:00:00";
			$sdate = date("Y-m-d H:i:s", strtotime($startdate . " " . $starttime));
			$dateplus = date_create($sdate);
			date_add($dateplus, date_interval_create_from_date_string('8 hours'));
				
			$endtime = date_format($dateplus, 'H:i:s');
			$enddate = date_format($dateplus, 'Y-m-d');
			$edate = date("Y-m-d H:i:s", strtotime($enddate . " " . $endtime));
		}else if($shift == 3){
			$starttime = "22:00:00";
			$sdate = date("Y-m-d H:i:s", strtotime($startdate . " " . $starttime));
			$dateplus = date_create($sdate);
			date_add($dateplus, date_interval_create_from_date_string('8 hours'));
				
			$endtime = date_format($dateplus, 'H:i:s');
			$enddate = date_format($dateplus, 'Y-m-d');
			$edate = date("Y-m-d H:i:s", strtotime($enddate . " " . $endtime));
		}else{
			$sdate = date("Y-m-d H:i:s", strtotime($startdate." "."00:00:00"));
			$edate = date("Y-m-d H:i:s", strtotime($enddate." "."23:59:59"));
		} */
		
		$m1 = date("F", strtotime($startdate)); 
		$m2 = date("F", strtotime($enddate)); 
		$year = date("Y", strtotime($startdate));
		$rows = array();
		$rows2 = array();
		$total_q = 0;
		$total_q2 = 0;
		$limitduration = 300; //5menit
		
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
			$this->dbtrip = $this->load->database("ssi_operasional",true);
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
				$this->dbtrip = $this->load->database("ssi_operasional",true);
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

		//get data team
		$this->dbtransporter = $this->load->database("transporter", true);
		$this->dbtransporter->order_by("team_date", "asc");
		$this->dbtransporter->order_by("team_time", "asc");
		$this->dbtransporter->order_by("team_vehicle_no", "asc");
		$this->dbtransporter->where("team_flag", 0);
		$this->dbtransporter->where("team_sch_start >=", $sdate);
		$this->dbtransporter->where("team_sch_end <=", $edate);
		//$this->dbtransporter->where("team_shift", $shift);
		if($vehicle != 0){
			$this->dbtransporter->where("team_vehicle_device", $vehicle);
		}
		if($shift != ""){
			$this->dbtransporter->where("team_shift", $shift);
		}
		$qr = $this->dbtransporter->get("ssi_team");
		//$rows_r = $qr->result();
		$rows_r = $qr->row();
		
		//new
		/* if(count($rows_r) > 0){
			
			$data_r1 = $rows_r1;
		} */
		
		$data_r = $rows_r;
		
		
		if($m1 != $m2)
		{
			$data = $rowsall;
		}
		else
		{
			$data = $rows;
		}
		
		$total = count($data);
		
		$this->db->cache_delete_all();
		$this->dbtrip->cache_delete_all();
		
		/** PHPExcel */
		include 'class/PHPExcel.php';
			
		/** PHPExcel_Writer_Excel2007 */
		include 'class/PHPExcel/Writer/Excel2007.php';
			
		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();
			
		// Set properties
		$objPHPExcel->getProperties()->setCreator("lacak-mobil.com");
		$objPHPExcel->getProperties()->setLastModifiedBy("lacak-mobil.com");
		$objPHPExcel->getProperties()->setTitle("Laporan Operasional Harian");
		$objPHPExcel->getProperties()->setSubject("Laporan Operasional Harian");
		$objPHPExcel->getProperties()->setDescription("Laporan Operasional Harian");
			
		//set document
		$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(1);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.50);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.50);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(1);
		$objPHPExcel->getDefaultStyle()->getFont()->setName('Times New Roman');
		$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);			
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(35);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(35);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
		
		//Header
		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->getActiveSheet()->mergeCells('A1:I1');
		$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'LAPORAN OPERASIONAL HARIAN');
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);

		//table team
		if(count($data_r) > 0){
			$objPHPExcel->getActiveSheet()->SetCellValue('A4', "1");
			$objPHPExcel->getActiveSheet()->getStyle('A4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->SetCellValue('B4','STAFF REPLENISHMENT');
			$objPHPExcel->getActiveSheet()->getStyle('B4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$objPHPExcel->getActiveSheet()->SetCellValue('C4', strtoupper($data_r->team_staff));
			$objPHPExcel->getActiveSheet()->getStyle('C4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$objPHPExcel->getActiveSheet()->getStyle('C4')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->SetCellValue('D4','NPP');
			$objPHPExcel->getActiveSheet()->getStyle('D4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$objPHPExcel->getActiveSheet()->SetCellValue('E4', strtoupper($data_r->team_staff_npp));
			$objPHPExcel->getActiveSheet()->getStyle('E4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$objPHPExcel->getActiveSheet()->getStyle('E4')->getFont()->setBold(true);

			
			$objPHPExcel->getActiveSheet()->SetCellValue('A5', "2");
			$objPHPExcel->getActiveSheet()->getStyle('A5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->SetCellValue('B5','PENGEMUDI');
			$objPHPExcel->getActiveSheet()->getStyle('B5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$objPHPExcel->getActiveSheet()->SetCellValue('C5', strtoupper($data_r->team_driver));
			$objPHPExcel->getActiveSheet()->getStyle('C5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$objPHPExcel->getActiveSheet()->getStyle('C5')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->SetCellValue('D5','NPP');
			$objPHPExcel->getActiveSheet()->getStyle('D5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$objPHPExcel->getActiveSheet()->SetCellValue('E5', strtoupper($data_r->team_driver_npp));
			$objPHPExcel->getActiveSheet()->getStyle('E5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$objPHPExcel->getActiveSheet()->getStyle('E5')->getFont()->setBold(true);
			
			$objPHPExcel->getActiveSheet()->SetCellValue('A6', "3");
			$objPHPExcel->getActiveSheet()->getStyle('A6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->SetCellValue('B6','PENGAMAN');
			$objPHPExcel->getActiveSheet()->getStyle('B6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$objPHPExcel->getActiveSheet()->SetCellValue('C6', strtoupper($data_r->team_pengaman1));
			$objPHPExcel->getActiveSheet()->getStyle('C6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$objPHPExcel->getActiveSheet()->getStyle('C6')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->SetCellValue('D6','NRP');
			$objPHPExcel->getActiveSheet()->getStyle('D6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$objPHPExcel->getActiveSheet()->SetCellValue('E6', strtoupper($data_r->team_pengaman1_nrp));
			$objPHPExcel->getActiveSheet()->getStyle('E6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$objPHPExcel->getActiveSheet()->getStyle('E6')->getFont()->setBold(true);
			
			$objPHPExcel->getActiveSheet()->SetCellValue('A7', "4");
			$objPHPExcel->getActiveSheet()->getStyle('A7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->SetCellValue('B7','PENGAMAN');
			$objPHPExcel->getActiveSheet()->getStyle('B7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$objPHPExcel->getActiveSheet()->SetCellValue('C7', strtoupper($data_r->team_pengaman2));
			$objPHPExcel->getActiveSheet()->getStyle('C7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$objPHPExcel->getActiveSheet()->getStyle('C7')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->SetCellValue('D7','NRP');
			$objPHPExcel->getActiveSheet()->getStyle('D7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$objPHPExcel->getActiveSheet()->SetCellValue('E7',strtoupper($data_r->team_pengaman2_nrp));
			$objPHPExcel->getActiveSheet()->getStyle('E7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$objPHPExcel->getActiveSheet()->getStyle('E7')->getFont()->setBold(true);
		}
			
		if(count($data_r) > 0){
			
			$objPHPExcel->getActiveSheet()->SetCellValue('B10','Kendaraan');
			$objPHPExcel->getActiveSheet()->getStyle('B10')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$objPHPExcel->getActiveSheet()->SetCellValue('C10', strtoupper($data_r->team_vehicle_name));
			$objPHPExcel->getActiveSheet()->getStyle('C10')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$objPHPExcel->getActiveSheet()->getStyle('C10')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->SetCellValue('D10','No Polisi');
			$objPHPExcel->getActiveSheet()->getStyle('D10')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$objPHPExcel->getActiveSheet()->SetCellValue('E10', strtoupper($data_r->team_vehicle_no));
			$objPHPExcel->getActiveSheet()->getStyle('E10')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$objPHPExcel->getActiveSheet()->getStyle('E10')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->SetCellValue('F10', 'Periode');
			$objPHPExcel->getActiveSheet()->getStyle('F10')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$objPHPExcel->getActiveSheet()->mergeCells('G10:H10');
			$objPHPExcel->getActiveSheet()->SetCellValue('G10', date("d-m-Y H:i",strtotime($sdate))." s/d ".date("d-m-Y H:i",strtotime($edate)));
			$objPHPExcel->getActiveSheet()->getStyle('G10')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$objPHPExcel->getActiveSheet()->getStyle('G10')->getFont()->setBold(true);
		}

		//table trip
		//Top Header
		$objPHPExcel->getActiveSheet()->SetCellValue('A12', 'NO');
		$objPHPExcel->getActiveSheet()->SetCellValue('B12', 'WAKTU MULAI');
		$objPHPExcel->getActiveSheet()->SetCellValue('C12', 'WAKTU BERAKHIR');
		$objPHPExcel->getActiveSheet()->SetCellValue('D12', 'STATUS MESIN');
		$objPHPExcel->getActiveSheet()->SetCellValue('E12', 'LOKASI MULAI');
		$objPHPExcel->getActiveSheet()->SetCellValue('F12', 'LOKASI BERAKHIR');
		$objPHPExcel->getActiveSheet()->SetCellValue('G12', 'DURASI');
		$objPHPExcel->getActiveSheet()->SetCellValue('H12', 'JARAK TEMPUH');
		$objPHPExcel->getActiveSheet()->SetCellValue('I12', 'AKUMULASI JARAK TEMPUH');
		
		$objPHPExcel->getActiveSheet()->getStyle('A12:I12')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A12:I12')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		$i=1;
		$k=0;
		for($j=0;$j<count($data);$j++)
		{
			$k = $k + $data[$j]->trip_mileage_trip_mileage;
			$objPHPExcel->getActiveSheet()->SetCellValue('A'.(12+$i), $i);
			$objPHPExcel->getActiveSheet()->getStyle('A'.(12+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->SetCellValue('B'.(12+$i), $data[$j]->trip_mileage_start_time);						
			$objPHPExcel->getActiveSheet()->SetCellValue('C'.(12+$i), $data[$j]->trip_mileage_end_time);
			
			if($data[$j]->trip_mileage_engine == 0){
				$engine = "MATI";
			}else{
				$engine = "HIDUP";
			}
			$objPHPExcel->getActiveSheet()->SetCellValue('D'.(12+$i), $engine);
			$objPHPExcel->getActiveSheet()->getStyle('D'.(12+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$geofence_start = strlen($data[$j]->trip_mileage_geofence_start);
			if (strlen($geofence_start == 1)){
				$geofence_start_name = "";
				$objPHPExcel->getActiveSheet()->SetCellValue('E'.(12+$i),$geofence_start_name."  ".$data[$j]->trip_mileage_location_start);
				$objPHPExcel->getActiveSheet()->getStyle('E'.(12+$i))->getFont()->setSize(8);
			}
			if (strlen($geofence_start > 1)){
				$geofence_start_name = $data[$j]->trip_mileage_geofence_start;
				$objPHPExcel->getActiveSheet()->SetCellValue('E'.(12+$i),$geofence_start_name.", ".$data[$j]->trip_mileage_location_start);
				$objPHPExcel->getActiveSheet()->getStyle('E'.(12+$i))->getFont()->setSize(8);
			}
			
			$geofence_end = strlen($data[$j]->trip_mileage_geofence_end);
			//print_r($geofence_end);exit();
			if (strlen($geofence_end == 1)){
				$geofence_end_name = "";
				$objPHPExcel->getActiveSheet()->SetCellValue('F'.(12+$i),$geofence_end_name."  ".$data[$j]->trip_mileage_location_end);
				$objPHPExcel->getActiveSheet()->getStyle('F'.(12+$i))->getFont()->setSize(8);
			}
			if (strlen($geofence_end > 1)){
				$geofence_end_name = $data[$j]->trip_mileage_geofence_end;
				$objPHPExcel->getActiveSheet()->SetCellValue('F'.(12+$i),$geofence_end_name.",  ".$data[$j]->trip_mileage_location_end);
				$objPHPExcel->getActiveSheet()->getStyle('F'.(12+$i))->getFont()->setSize(8);
			}

			$objPHPExcel->getActiveSheet()->SetCellValue('G'.(12+$i), $data[$j]->trip_mileage_duration);
			$objPHPExcel->getActiveSheet()->getStyle('G'.(12+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

			$objPHPExcel->getActiveSheet()->SetCellValue('H'.(12+$i), $data[$j]->trip_mileage_trip_mileage." "."KM");
			$objPHPExcel->getActiveSheet()->getStyle('H'.(12+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->SetCellValue('I'.(12+$i), $k." "."KM");
			$objPHPExcel->getActiveSheet()->getStyle('I'.(12+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$i++;
		}

			$objPHPExcel->getActiveSheet()->SetCellValue('B'.(12+$i), 'TOTAL JARAK TEMPUH');
			$objPHPExcel->getActiveSheet()->getStyle('B'.(12+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$objPHPExcel->getActiveSheet()->getStyle('B'.(12+$i))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->mergeCells('G'.(12+$i).':'.'I'.(12+$i));
			if(isset($k) && $k > 0){
				$objPHPExcel->getActiveSheet()->SetCellValue('G'.(12+$i), $k.' '.'KM');
			}else{
				$objPHPExcel->getActiveSheet()->SetCellValue('G'.(12+$i), ' ');
			}
			$objPHPExcel->getActiveSheet()->SetCellValue('G'.(12+$i), $k.' '.'KM');
			$objPHPExcel->getActiveSheet()->getStyle('G'.(12+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('G'.(12+$i))->getFont()->setBold(true);
			
			$objPHPExcel->getActiveSheet()->SetCellValue('B'.(13+$i), 'TOTAL DURASI MESIN HIDUP');
			$objPHPExcel->getActiveSheet()->getStyle('B'.(13+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$objPHPExcel->getActiveSheet()->getStyle('B'.(13+$i))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->mergeCells('G'.(13+$i).':'.'I'.(13+$i));
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
												$h_duration = $hours." "."Jam"." ";
											}
											if($hours >= 2)
											{
												$h_duration = $hours." "."Jam"." ";
											}
										}
										if(isset($minutes) && $minutes > 0)
										{
											if($minutes > 0 && $minutes <= 1 )
											{
												$m_duration = $minutes." "."Menit"." ";
											}
											if($minutes >= 2)
											{
												$m_duration = $minutes." "."Menit"." ";
											}
										}
										 if(isset($seconds) && $seconds > 0)
										{
											$s_duration =  $seconds." "."Detik"." ";
										}
									}
		
			$objPHPExcel->getActiveSheet()->SetCellValue('G'.(13+$i), $h_duration." ".$m_duration);
			$objPHPExcel->getActiveSheet()->getStyle('G'.(13+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('G'.(13+$i))->getFont()->setBold(true);
			
			
		$styleArray = array(
				'borders' => array(
				'allborders' => array(
				'style' => PHPExcel_Style_Border::BORDER_THIN
				    )
				  )
				);
			
		$objPHPExcel->getActiveSheet()->getStyle('A12:I'.(13+$i))->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->getStyle('A12:I'.(13+$i))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
		$objPHPExcel->getActiveSheet()->getStyle('A12:I'.(13+$i))->getAlignment()->setWrapText(true);
		// Rename sheet
		$objPHPExcel->getActiveSheet()->setTitle('operational_data');
			
		// Save Excel
		$filedate = date("Ymd",strtotime($startdate))."_".date("Ymd",strtotime($enddate));
		$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
		@mkdir(REPORT_PATH, DIR_WRITE_MODE); //REPORT_PATH -> config->constant
		$filecreatedname = "operasional_".$vehicle."_".$filedate.".xls";
			
		$objWriter->save(REPORT_PATH.$filecreatedname);
		
		$output = '{"success":true,"errMsg":"","filename":"' .base_url() . "assets/media/report/" . $filecreatedname.'"}';
		echo $output;
		return;
		
		
	}
	
	function mn_ritase_report()
	{
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		} 
        
		$this->db->order_by("vehicle_name", "asc");
		$this->db->order_by("vehicle_no", "asc");		
		$this->db->where("vehicle_status <>", 3);
		if ($this->sess->user_group == 0)  {
			$this->db->where("vehicle_user_id", $this->sess->user_id);
		}
		if ($this->sess->user_group != 0)  {
			$this->db->where("vehicle_group", $this->sess->user_group);
		}
		
		$q_vehicle = $this->db->get("vehicle");
		$row_vehicle = $q_vehicle->result();
        //print_r($row_vehicle);exit;
		
		$this->db->cache_delete_all();
		
		$this->dbtransporter = $this->load->database("transporter", true);
		
		//get data ritase
		$this->dbtransporter->order_by("ritase_geofence_name", "asc");
		$this->dbtransporter->where("ritase_company", "356"); //company PT_SSI
		$this->dbtransporter->where("ritase_status", "1");
		$q_ritase = $this->dbtransporter->get("ritase");
		$row_ritase = $q_ritase->result();
		
		$this->dbtransporter->cache_delete_all();
		
		$this->params["vehicle"] = $row_vehicle;
		$this->params["ritase"] = $row_ritase;
		$this->params["content"] = $this->load->view('transporter/report/mn_ritase_report', $this->params, true);		
		$this->load->view("templatesess", $this->params);
	}
	
	function ritase_report()
	{
		$vehicle_device = $this->input->post("vehicle");
		
		$startdate = $this->input->post("date");
		$sdate = date("Y-m-d H:i:s", strtotime($startdate." "."00:00:00"));
		
		$enddate = $this->input->post("enddate");
		$edate = date("Y-m-d H:i:s", strtotime($enddate." "."23:59:59"));
		//print($startdate." ".$enddate);exit();
		$ritase = $this->input->post("ritase");
		$exRitase = explode(",",$ritase);
		$ritase_id = $exRitase[0];
		$ritase_name = $exRitase[1];
		
		$this->db->order_by("geoalert_time", "asc");
		$this->db->where("geoalert_vehicle", $vehicle_device);
		$this->db->where("geoalert_time >=", $sdate);
        $this->db->where("geoalert_time <=", $edate);  
		$this->db->join("geofence", "geofence_id = geoalert_geofence", "leftouter");
		$this->db->where("geofence_name", $ritase_name);
		$q = $this->db->get("geofence_alert");
		$rows = $q->result();
		
		//get team
		$this->dbtransporter = $this->load->database("transporter", true);
		$this->dbtransporter->order_by("team_date", "asc");
		$this->dbtransporter->order_by("team_time", "asc");
		$this->dbtransporter->order_by("team_vehicle_no", "asc");
		$this->dbtransporter->where("team_vehicle_device", $vehicle_device);
		$this->dbtransporter->where("team_flag", 0);
		$this->dbtransporter->where("team_date >=", $startdate);
		$this->dbtransporter->where("team_date <=", $enddate);
		/* $this->dbtransporter->where("team_sch_start >=", $sdate);
		$this->dbtransporter->where("team_sch_end <=", $edate); */
		$q_team = $this->dbtransporter->get("ssi_team");
		$rows_team = $q_team->result();
	    
        //print_r($rows_team);exit;
        
		$this->db->cache_delete_all();
		$this->dbtransporter->cache_delete_all();
		
		for ($i=0;$i<count($rows);$i++)
		{
			$rows[$i]->geoalert_time_t = dbmaketime($rows[$i]->geoalert_time);
		}
		
		$params["data"]=$rows;
		$params["data_r"]=$rows_team;
		$params["start_date"]=$startdate;
		$params["end_date"]=$enddate;
		
		$html = $this->load->view("transporter/report/result_ritase", $params, true);
		
		$callback["error"] = false;
		$callback["html"] = $html;
		
		echo json_encode($callback);
	}
	
	function ritase_report_excel()
	{
		$vehicle_device = $this->input->post("vehicle");
		
		$startdate = $this->input->post("date");
        $sdate = date("Y-m-d H:i:s", strtotime($startdate." "."00:00:00"));
        
        $enddate = $this->input->post("enddate");
        $edate = date("Y-m-d H:i:s", strtotime($enddate." "."23:59:59"));
		
		$ritase = $this->input->post("ritase");
		$exRitase = explode(",",$ritase);
		$ritase_id = $exRitase[0];
		$ritase_name = $exRitase[1];
		
		$this->db->order_by("geoalert_time", "asc");
        $this->db->where("geoalert_vehicle", $vehicle_device);
        $this->db->where("geoalert_time >=", $sdate);
        $this->db->where("geoalert_time <=", $edate);  
        $this->db->join("geofence", "geofence_id = geoalert_geofence", "leftouter");
        $this->db->where("geofence_name", $ritase_name);
        $q = $this->db->get("geofence_alert");
        $data = $q->result();
		
		$this->db->cache_delete_all();
		
		for ($i=0;$i<count($data);$i++)
		{
			$data[$i]->geoalert_time_t = dbmaketime($data[$i]->geoalert_time);
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
			$objPHPExcel->getProperties()->setTitle("Ritase Report");
			$objPHPExcel->getProperties()->setSubject("Ritase Report Lacak-mobil.com");
			$objPHPExcel->getProperties()->setDescription("Ritase Report  Lacak-mobil.com");
		
		$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(1);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.75);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.75);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(1);
		$objPHPExcel->getDefaultStyle()->getFont()->setName('Times New Roman');
		$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(35);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(35);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(55);	
		
		//Header
		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->getActiveSheet()->mergeCells('A1:D1');
		$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'RITASE REPORT');
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('D3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('D3')->getFont()->setBold(true);
			
		$objPHPExcel->getActiveSheet()->SetCellValue('A5', '*');
		$objPHPExcel->getActiveSheet()->SetCellValue('B5', 'KELUAR');
		$objPHPExcel->getActiveSheet()->SetCellValue('C5', 'MASUK');
		$objPHPExcel->getActiveSheet()->SetCellValue('D5', 'DURATION');
		$objPHPExcel->getActiveSheet()->getStyle('A5:D5')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A5:D5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
		$totalritase = 0;
		for($i=0; $i < count($data); $i++) 
		{
			if ($data[$i]->geoalert_direction == 2 && isset($data[$i+1]->geofence_name)) 
			{
				$objPHPExcel->getActiveSheet()->SetCellValue('A'.(6+$i), "*");
				if ($data[$i]->geofence_name) 
				{
					$objPHPExcel->getActiveSheet()->SetCellValue('B'.(6+$i), $data[$i]->geofence_name . " " . "Start date". " " .date("d/m/Y H:i:s", $data[$i]->geoalert_time_t));
				}
				else 
				{
					$objPHPExcel->getActiveSheet()->SetCellValue('B'.(6+$i), $data[$i]->geofence_coordinate . " " . "Start date". " " .date("d/m/Y H:i:s", $data[$i]->geoalert_time_t));
				}
			
				if ($data[$i]->geoalert_direction == 2) 
				{ 
					if (isset($data[$i+1]->geofence_name))
					{
						if ($data[$i+1]->geofence_name) 
						{
							$objPHPExcel->getActiveSheet()->SetCellValue('C'.(6+$i), $data[$i+1]->geofence_name." "."Finish ". " ". date("d/m/Y H:i:s", $data[$i+1]->geoalert_time_t));
						}
						else 
						{
							$objPHPExcel->getActiveSheet()->SetCellValue('C'.(6+$i), $data[$i+1]->geofence_coordinate." "."Finish ". " ". date("d/m/Y H:i:s", $data[$i+1]->geoalert_time_t));
						} 
					} 
					else
					{
						$objPHPExcel->getActiveSheet()->SetCellValue('C'.(6+$i), "-");
					}
				}
				if (isset($data[$i+1]->geofence_name))
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
						$objPHPExcel->getActiveSheet()->SetCellValue('D'.(6+$i), $d_day ." ". "Day" ." ".$d_hour ." "."Jam" ." ". $d_minute ." ". "Menit" ." ". $d_second ." ". "Detik" );
					}
					else if (isset($d_hour) && ($d_hour > 0))
					{
						$objPHPExcel->getActiveSheet()->SetCellValue('D'.(6+$i), $d_hour ." "."Jam" ." ". $d_minute ." ". "Menit" ." ". $d_second ." ". "Detik" );
					}
					else
					{
						$objPHPExcel->getActiveSheet()->SetCellValue('D'.(6+$i), $d_minute ." ". "Menit" ." ". $d_second ." ". "Detik" );
					}
				}
				else
				{
					$objPHPExcel->getActiveSheet()->SetCellValue('D'.(6+$i), "-");
				}
				$totalritase += 1;
				$j = $i+1;
			}
		}
		$objPHPExcel->getActiveSheet()->SetCellValue('D'.(7+$j), "TOTAL RITASE : "." ".$totalritase);
		
		$styleArray = array(
			'borders' => array(
            'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
            )
            )
            );
			
		$objPHPExcel->getActiveSheet()->getStyle('A5:D'.(5+$i))->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->getStyle('A5:D'.(5+$i))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
		$objPHPExcel->getActiveSheet()->getStyle('A5:D'.(5+$i))->getAlignment()->setWrapText(true);
		// Save Excel
			$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
			@mkdir(REPORT_PATH, DIR_WRITE_MODE); //REPORT_PATH -> config->constant
			$filecreatedname = "Ritase_Report_".date('YmdHis') . ".xls";
			
			$objWriter->save(REPORT_PATH.$filecreatedname);
		
			$output = '{"success":true,"errMsg":"","filename":"' .base_url() . "assets/media/report/" . $filecreatedname.'"}';
			echo $output;
		return;
	}

}