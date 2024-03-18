<?php
include "base.php";

class Report_droppoint extends Base {

	function __construct()
	{
		parent::Base();	
		$this->load->helper('common_helper');
	}
	
	function index(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		
		$bulanini = date('m');
		$tahunini = date('Y');
	
		//Get company
		$row_company = $this->get_company_bylogin();
		$this->params['rcompany'] = $row_company;
		
		//Get date
		$row_month = $this->get_month();
		$this->params["month"] = $row_month;
		
		//Get year
		$row_year = $this->get_year();
		$this->params["year"] = $row_year;
		
		//Get this month
		$row_this_month = $this->get_this_month($bulanini);
		$this->params["thismonth"] = $row_this_month;
		
		//get this year
		$row_this_year = $this->get_this_year($tahunini);
		$this->params["thisyear"] = $row_this_year;
		
		$this->params["content"] = $this->load->view('transporter/report/mn_droppoint', $this->params, true);		
		$this->load->view("templatesess", $this->params);
	}
	
	function search(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
        if (!isset($this->sess->user_id)){redirect(base_url());}
		$company = isset($_POST["company"]) ? $_POST["company"] : "";
		$parent = isset($_POST["parent"]) ? $_POST["parent"] : "";
		$distrep = isset($_POST["distrep"]) ? $_POST["distrep"] : "";
		
		$sdate = isset($_POST["sdate"]) ? $_POST["sdate"] : "";
		$edate = isset($_POST["edate"]) ? $_POST["edate"] : "";
		$bank = isset($_POST["bank"]) ? $_POST["bank"] : "";
		$month = isset($_POST["month"]) ? $_POST["month"] : "";
		$thismonth = isset($_POST["thismonth"]) ? $_POST["thismonth"] : "";
		$year = isset($_POST["year"]) ? $_POST["year"] : "";
		$thisyear = isset($_POST["thisyear"]) ? $_POST["thisyear"] : "";
		$date_option = isset($_POST["date_option"]) ? $_POST["date_option"] : "";
		
		if(isset($date_option) && ($date_option == "ini")){
			$startdate = $thisyear."-".$thismonth."-"."01";
		}
		if(isset($date_option) && ($date_option == "semua")){
			$startdate = $year."-".$month."-"."01";
		}
		
		$sdate = date("Y-m-d", strtotime($startdate));
		$edate = date("Y-m-t", strtotime($sdate));
		$this->params["sdate"] = $sdate;
		$this->params["edate"] = $edate;
		
		if($company == "")
		{
			$callback['error'] = true;
            $callback['message'] = "Please Select Area !";
            echo json_encode($callback);
            return;
		}
		
		if($parent == "")
		{
			$callback['error'] = true;
            $callback['message'] = "Please Select Group !";
            echo json_encode($callback);
            return;
		}
		
		if($distrep == "")
		{
			$callback['error'] = true;
            $callback['message'] = "Please Select Distrep !";
            echo json_encode($callback);
            return;
		}
		
		if($sdate == "")
		{
			$callback['error'] = true;
            $callback['message'] = "Plese Select Periode !";
            echo json_encode($callback);
            return;
		}
		
		//get company name
		$row_company_name = $this->get_company_name($company);
		$this->params["company_name"] = $row_company_name;
		
		//get parent name 
		$row_parent_name = $this->get_parent_name($parent);
		$this->params["parent_name"] = $row_parent_name;
		
		//get distrep name 
		$row_distrep_name = $this->get_distrep_name($distrep);
		$this->params["distrep_name"] = $row_distrep_name;
		
		//get droppoint
		$row_droppoint = $this->get_droppoint_bydistrep($distrep);
		$this->params["droppoint"] = $row_droppoint;
		
		// get data monthly report
		$row_monthly_report = $this->get_monthly_report($sdate,$edate);
		$this->params["data"] = $row_monthly_report;
		
		/*$this->dbtransporter = $this->load->database("transporter", TRUE);
		$this->dbtransporter->order_by("monthly_date","asc");
		$this->dbtransporter->where("monthly_date >=", $sdate);
		$this->dbtransporter->where("monthly_date <=", $edate);
		$q = $this->dbtransporter->get("config_monthly_report");
		$rows = $q->result();
		$this->params["data"] = $rows;*/
		
		$html = $this->load->view('transporter/report/list_result_droppoint', $this->params, true);
        
        $callback['html'] = $html;
       
		echo json_encode($callback);
	}
	
	//for dokar payu
	function export()
	{
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
        $offset = isset($_POST['offset']) ? $_POST['offset'] : 0;
        $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
		$unit = isset($_POST['unit']) ? $_POST['unit'] : "";
		$direktorat = isset($_POST['direktorat']) ? $_POST['direktorat'] : "";
		$startdate = isset($_POST['startdate']) ? $_POST['startdate'] : "";
		$enddate = isset($_POST['enddate']) ? $_POST['enddate'] : "";
        $sortby = isset($_POST['sortby']) ? $_POST['sortby'] : "req_start_date";
        $orderby = isset($_POST['orderby']) ? $_POST['orderby'] : "desc";
			
		$this->DB2->order_by("direktorat_name", "asc");
		$this->DB2->select("direktorat_id,direktorat_code,direktorat_working_unit,direktorat_name,unit_code");
		$this->DB2->where("direktorat_working_unit", $unit);
		$this->DB2->where("direktorat_flag", 0);
		$this->DB2->join("tbl_working_unit", "unit_id = direktorat_working_unit", "left");
		$qdir = $this->DB2->get("tbl_direktorat");
		$rows = $qdir->result();
		
		/** PHPExcel */
		include 'class/PHPExcel.php';
		/** PHPExcel_Writer_Excel2007 */
		include 'class/PHPExcel/Writer/Excel2007.php';
		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();
		
		// Set properties
		$objPHPExcel->getProperties()->setCreator("lacak-mobil.com");
		$objPHPExcel->getProperties()->setLastModifiedBy("lacak-mobil.com");
		$objPHPExcel->getProperties()->setTitle("Request Report Dokarisat PAYU");
		$objPHPExcel->getProperties()->setSubject("Request Report Dokarisat PAYU");
		$objPHPExcel->getProperties()->setDescription("Request Report Dokarisat PAYU");
		
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
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);		
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(25);		
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(25);
		$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(25);
		$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(25);
		$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(25);
		$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(25);
		$objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(25);
		$objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(25);
		
		
		//Header
		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->getActiveSheet()->mergeCells('A1:R1');
		$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'DOKARISAT PAYU - SC CATALOG REPORT');
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		
		if($startdate != "" && $enddate != "" ) { 
		
			$objPHPExcel->getActiveSheet()->SetCellValue('A3', 'Schedule Date :');
			$objPHPExcel->getActiveSheet()->mergeCells('A3:B3');
			$objPHPExcel->getActiveSheet()->getStyle('A3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$objPHPExcel->getActiveSheet()->getStyle('A3')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->SetCellValue('C3', date("d/m/Y",strtotime($startdate)).' s/d '.date("d/m/Y",strtotime($enddate)));
			$objPHPExcel->getActiveSheet()->getStyle('C3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$objPHPExcel->getActiveSheet()->getStyle('C3')->getFont()->setBold(true);
			
		}
		
		$objPHPExcel->getActiveSheet()->SetCellValue('A5', 'PENGGUNAAN DALAM KOTA');
		$objPHPExcel->getActiveSheet()->mergeCells('A5:C5');
		$objPHPExcel->getActiveSheet()->getStyle('A5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$objPHPExcel->getActiveSheet()->getStyle('A5')->getFont()->setBold(true);
		
		
		//Top Header
		$objPHPExcel->getActiveSheet()->SetCellValue('A7', 'No');
		$objPHPExcel->getActiveSheet()->mergeCells('A7:A9');
		$objPHPExcel->getActiveSheet()->SetCellValue('B7', 'Area');
		$objPHPExcel->getActiveSheet()->mergeCells('B7:B9');
		$objPHPExcel->getActiveSheet()->SetCellValue('C7', 'Direktorat');
		$objPHPExcel->getActiveSheet()->mergeCells('C7:C9');
		$objPHPExcel->getActiveSheet()->SetCellValue('D7', 'Harga Per Menit dari Paket 1 Jam');
		$objPHPExcel->getActiveSheet()->mergeCells('D7:E7');
		$objPHPExcel->getActiveSheet()->SetCellValue('D8', $this->config->item("tarif_dalam_kota_permenit"));
		$objPHPExcel->getActiveSheet()->mergeCells('D8:E8');
		$objPHPExcel->getActiveSheet()->SetCellValue('D9', 'Jumlah');
		$objPHPExcel->getActiveSheet()->SetCellValue('E9', 'Nilai');
		
		$objPHPExcel->getActiveSheet()->SetCellValue('F7', 'Menit Dibawah 1 Jam');
		$objPHPExcel->getActiveSheet()->mergeCells('F7:F9');
		$objPHPExcel->getActiveSheet()->SetCellValue('G7', 'Pembulatan');
		$objPHPExcel->getActiveSheet()->mergeCells('G7:G9');
		$objPHPExcel->getActiveSheet()->SetCellValue('H7', 'Total Menit');
		$objPHPExcel->getActiveSheet()->mergeCells('H7:H9');
		
		$objPHPExcel->getActiveSheet()->SetCellValue('I7', 'Harga Paket 1 Jam');
		$objPHPExcel->getActiveSheet()->mergeCells('I7:J7');
		$objPHPExcel->getActiveSheet()->SetCellValue('K8', $this->config->item("tarif_dalam_kota_perjam"));
		$objPHPExcel->getActiveSheet()->mergeCells('I8:J8');
		$objPHPExcel->getActiveSheet()->SetCellValue('I9', 'Jumlah');
		$objPHPExcel->getActiveSheet()->SetCellValue('J9', 'Nilai');
		
		$objPHPExcel->getActiveSheet()->SetCellValue('K7', 'Harga Paket 4 Jam');
		$objPHPExcel->getActiveSheet()->mergeCells('K7:L7');
		$objPHPExcel->getActiveSheet()->SetCellValue('K8', $this->config->item("tarif_dalam_kota_4jam"));
		$objPHPExcel->getActiveSheet()->mergeCells('K8:L8');
		$objPHPExcel->getActiveSheet()->SetCellValue('K9', 'Jumlah');
		$objPHPExcel->getActiveSheet()->SetCellValue('L9', 'Nilai');
		
		$objPHPExcel->getActiveSheet()->SetCellValue('M7', 'Harga Paket 8 Jam');
		$objPHPExcel->getActiveSheet()->mergeCells('M7:N7');
		$objPHPExcel->getActiveSheet()->SetCellValue('M8', $this->config->item("tarif_dalam_kota_8jam"));
		$objPHPExcel->getActiveSheet()->mergeCells('M8:N8');
		$objPHPExcel->getActiveSheet()->SetCellValue('M9', 'Jumlah');
		$objPHPExcel->getActiveSheet()->SetCellValue('N9', 'Nilai');
		
		$objPHPExcel->getActiveSheet()->SetCellValue('O7', 'Harga Paket 12 Jam');
		$objPHPExcel->getActiveSheet()->mergeCells('O7:P7');
		$objPHPExcel->getActiveSheet()->SetCellValue('O8', $this->config->item("tarif_dalam_kota_12jam"));
		$objPHPExcel->getActiveSheet()->mergeCells('O8:P8');
		$objPHPExcel->getActiveSheet()->SetCellValue('O9', 'Jumlah');
		$objPHPExcel->getActiveSheet()->SetCellValue('P9', 'Nilai');
		
		$objPHPExcel->getActiveSheet()->SetCellValue('Q7', 'Harga Paket 24 Jam');
		$objPHPExcel->getActiveSheet()->mergeCells('Q7:R7');
		$objPHPExcel->getActiveSheet()->SetCellValue('Q8', $this->config->item("tarif_dalam_kota_24jam"));
		$objPHPExcel->getActiveSheet()->mergeCells('Q8:R8');
		$objPHPExcel->getActiveSheet()->SetCellValue('Q9', 'Jumlah');
		$objPHPExcel->getActiveSheet()->SetCellValue('R9', 'Nilai');
		
		$objPHPExcel->getActiveSheet()->SetCellValue('S7', 'Cancellation');
		$objPHPExcel->getActiveSheet()->mergeCells('S7:T7');
		$objPHPExcel->getActiveSheet()->SetCellValue('S8', $this->config->item("cancel_fee_inner"));
		$objPHPExcel->getActiveSheet()->mergeCells('S8:T8');
		$objPHPExcel->getActiveSheet()->SetCellValue('S9', 'Jumlah');
		$objPHPExcel->getActiveSheet()->SetCellValue('T9', 'Nilai');
		
		
		$objPHPExcel->getActiveSheet()->SetCellValue('U7', 'Total');
		$objPHPExcel->getActiveSheet()->mergeCells('U7:U9');
		
		$objPHPExcel->getActiveSheet()->getStyle('A7:U7')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A8:U8')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A9:U9')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A7:U7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A8:U8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A9:U9')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		//penggunaan luar kota
		//Top Header
		$objPHPExcel->getActiveSheet()->SetCellValue('A26', 'No');
		$objPHPExcel->getActiveSheet()->mergeCells('A26:A28');
		$objPHPExcel->getActiveSheet()->SetCellValue('B26', 'Area');
		$objPHPExcel->getActiveSheet()->mergeCells('B26:B28');
		$objPHPExcel->getActiveSheet()->SetCellValue('C26', 'Direktorat');
		$objPHPExcel->getActiveSheet()->mergeCells('C26:C28');
		$objPHPExcel->getActiveSheet()->SetCellValue('D26', 'Harga Per Menit dari Paket 1 Jam');
		$objPHPExcel->getActiveSheet()->mergeCells('D26:E26');
		$objPHPExcel->getActiveSheet()->SetCellValue('D27', $this->config->item("tarif_luar_kota_permenit"));
		$objPHPExcel->getActiveSheet()->mergeCells('D27:E27');
		$objPHPExcel->getActiveSheet()->SetCellValue('D28', 'Jumlah');
		$objPHPExcel->getActiveSheet()->SetCellValue('E28', 'Nilai');
		
		$objPHPExcel->getActiveSheet()->SetCellValue('F26', 'Menit Dibawah 1 Jam');
		$objPHPExcel->getActiveSheet()->mergeCells('F26:F28');
		$objPHPExcel->getActiveSheet()->SetCellValue('G26', 'Pembulatan');
		$objPHPExcel->getActiveSheet()->mergeCells('G26:G28');
		$objPHPExcel->getActiveSheet()->SetCellValue('H26', 'Total Menit');
		$objPHPExcel->getActiveSheet()->mergeCells('H26:H28');
		
		$objPHPExcel->getActiveSheet()->SetCellValue('I26', 'Harga Paket 1 Jam');
		$objPHPExcel->getActiveSheet()->mergeCells('I26:J26');
		$objPHPExcel->getActiveSheet()->SetCellValue('I27', $this->config->item("tarif_luar_kota_perjam"));
		$objPHPExcel->getActiveSheet()->mergeCells('I27:J27');
		$objPHPExcel->getActiveSheet()->SetCellValue('I28', 'Jumlah');
		$objPHPExcel->getActiveSheet()->SetCellValue('J28', 'Nilai');
		
		$objPHPExcel->getActiveSheet()->SetCellValue('K26', 'Harga Paket 6 Jam');
		$objPHPExcel->getActiveSheet()->mergeCells('K26:L26');
		$objPHPExcel->getActiveSheet()->SetCellValue('K27', $this->config->item("tarif_luar_kota_6jam"));
		$objPHPExcel->getActiveSheet()->mergeCells('K27:L27');
		$objPHPExcel->getActiveSheet()->SetCellValue('K28', 'Jumlah');
		$objPHPExcel->getActiveSheet()->SetCellValue('L28', 'Nilai');
		
		$objPHPExcel->getActiveSheet()->SetCellValue('M26', 'Harga Paket 8 Jam');
		$objPHPExcel->getActiveSheet()->mergeCells('M26:N26');
		$objPHPExcel->getActiveSheet()->SetCellValue('M27', $this->config->item("tarif_luar_kota_8jam"));
		$objPHPExcel->getActiveSheet()->mergeCells('M27:N27');
		$objPHPExcel->getActiveSheet()->SetCellValue('M28', 'Jumlah');
		$objPHPExcel->getActiveSheet()->SetCellValue('N28', 'Nilai');
		
		$objPHPExcel->getActiveSheet()->SetCellValue('O26', 'Harga Paket 12 Jam');
		$objPHPExcel->getActiveSheet()->mergeCells('O26:P26');
		$objPHPExcel->getActiveSheet()->SetCellValue('O27', $this->config->item("tarif_luar_kota_12jam"));
		$objPHPExcel->getActiveSheet()->mergeCells('O27:P27');
		$objPHPExcel->getActiveSheet()->SetCellValue('O28', 'Jumlah');
		$objPHPExcel->getActiveSheet()->SetCellValue('P28', 'Nilai');
		
		$objPHPExcel->getActiveSheet()->SetCellValue('Q26', 'Harga Paket 24 Jam');
		$objPHPExcel->getActiveSheet()->mergeCells('Q26:R26');
		$objPHPExcel->getActiveSheet()->SetCellValue('Q27', $this->config->item("tarif_luar_kota_24jam"));
		$objPHPExcel->getActiveSheet()->mergeCells('Q27:R27');
		$objPHPExcel->getActiveSheet()->SetCellValue('Q28', 'Jumlah');
		$objPHPExcel->getActiveSheet()->SetCellValue('R28', 'Nilai');
		
		$objPHPExcel->getActiveSheet()->SetCellValue('S26', 'Cancellation');
		$objPHPExcel->getActiveSheet()->mergeCells('S26:T26');
		$objPHPExcel->getActiveSheet()->SetCellValue('S27', $this->config->item("cancel_fee_outter"));
		$objPHPExcel->getActiveSheet()->mergeCells('S27:S27');
		$objPHPExcel->getActiveSheet()->SetCellValue('S28', 'Jumlah');
		$objPHPExcel->getActiveSheet()->SetCellValue('T28', 'Nilai');
		
		
		$objPHPExcel->getActiveSheet()->SetCellValue('U26', 'Total');
		$objPHPExcel->getActiveSheet()->mergeCells('U26:U28');
		
		$objPHPExcel->getActiveSheet()->getStyle('A26:U26')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A27:U27')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A28:U28')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A26:U26')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A27:U27')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A28:U28')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		
		$i = 1;
		for ($j=0;$j<count($rows);$j++)
		{
			//penggunaan luar kota
					//Hitung paket dalam kota
					$totalpaketmenit = 0;
					$totalpaket1jam = 0;
					$totalpaket4jam = 0;
					$totalpaket8jam = 0;
					$totalpaket12jam = 0;
					$totalpaket24jam = 0;
					$totalcancel = 0;
					$totalall = 0;
					$totalpaketbulat = 0;
					$totalpaketmenitmin = 0;
					$totalpaket1jammin = 0;
					$totalmenitbulat = 0;
					
					$hargatotalpaketmenit = 0;
					$hargatotalpaket1jam = 0;
					$hargatotalpaket4jam = 0;
					$hargatotalpaket8jam = 0;
					$hargatotalpaket12jam = 0;
					$hargatotalpaket24jam = 0;
					$hargatotalcancel = 0;
					$hargatotalall = 0;
					
					//select diatas 1 jam
					$this->DB2->group_by("cost_req");
					$this->DB2->select("cost_paketmenit,cost_paket1,cost_paket4,cost_paket6,cost_paket8,cost_paket12,cost_paket24,cost_paketbulat,
										req_out_off_town,cost_working_unit,cost_trip_duration
					");
					$this->DB2->where("req_start_date >=",date("Y-m-d H:i:s",strtotime($startdate." "."00:00:00")));
					$this->DB2->where("req_start_date <=",date("Y-m-d H:i:s",strtotime($enddate." "."23:59:59")));
					$this->DB2->where("cost_working_unit",$rows[$j]->direktorat_working_unit);
					$this->DB2->where("direktorat_id",$rows[$j]->direktorat_id);
					$this->DB2->where("req_out_off_town",0);
					$this->DB2->where("req_status",3);
					$this->DB2->where("cost_trip_duration >=",3600);
					$this->DB2->join("tbl_request", "req_id = cost_req", "left");
					$this->DB2->join("tbl_direktorat", "direktorat_id = req_direktorat", "left");
					$qpaket = $this->DB2->get("tbl_trip_cost");
					
					if($qpaket->num_rows>0)
					{
					  $datapaket = $qpaket->result();
					  for($m=0;$m<$qpaket->num_rows;$m++)
					  {
						 $totalpaketmenit = $totalpaketmenit + $datapaket[$m]->cost_paketmenit;
						 $totalpaket1jam = $totalpaket1jam + $datapaket[$m]->cost_paket1;
						 $totalpaket4jam = $totalpaket4jam + $datapaket[$m]->cost_paket4;
						 $totalpaket8jam = $totalpaket8jam + $datapaket[$m]->cost_paket8;
						 $totalpaket12jam = $totalpaket12jam + $datapaket[$m]->cost_paket12;
						 $totalpaket24jam = $totalpaket24jam + $datapaket[$m]->cost_paket24;
					  }
					 
					}
					
					//select dibawah 1jam
					$this->DB2->group_by("cost_req");
					$this->DB2->select("cost_paketmenit,cost_paket1,cost_paketbulat,
										req_out_off_town,cost_working_unit,cost_trip_duration
					");
					$this->DB2->where("req_start_date >=",date("Y-m-d H:i:s",strtotime($startdate." "."00:00:00")));
					$this->DB2->where("req_start_date <=",date("Y-m-d H:i:s",strtotime($enddate." "."23:59:59")));
					$this->DB2->where("cost_working_unit",$rows[$j]->direktorat_working_unit);
					$this->DB2->where("direktorat_id",$rows[$j]->direktorat_id);
					$this->DB2->where("req_out_off_town",0);
					$this->DB2->where("req_status",3);
					$this->DB2->where("cost_trip_duration <=",3599);
					$this->DB2->join("tbl_request", "req_id = cost_req", "left");
					$this->DB2->join("tbl_direktorat", "direktorat_id = req_direktorat", "left");
					$qpaketmin = $this->DB2->get("tbl_trip_cost");
					
					if($qpaketmin->num_rows>0)
					{
					  $datapaketmin = $qpaketmin->result();
					  for($m1=0;$m1<$qpaketmin->num_rows;$m1++)
					  {
						 $totalpaketmenitmin = $totalpaketmenitmin + $datapaketmin[$m1]->cost_paketmenit;
						 $totalpaketbulat = $totalpaketbulat + $datapaketmin[$m1]->cost_paketbulat;
						 $totalpaket1jammin = $totalpaket1jammin + $datapaketmin[$m1]->cost_paket1;
					  }
					 
					}
					
					//hitung cancel
					$this->DB2->group_by("cost_req");
					$this->DB2->select("cost_note,req_out_off_town,cost_working_unit
										");
					$this->DB2->where("req_start_date >=",date("Y-m-d H:i:s",strtotime($startdate." "."00:00:00")));
					$this->DB2->where("req_start_date <=",date("Y-m-d H:i:s",strtotime($enddate." "."23:59:59")));
					$this->DB2->where("cost_working_unit",$rows[$j]->direktorat_working_unit);
					$this->DB2->where("direktorat_id",$rows[$j]->direktorat_id);
					$this->DB2->where("req_out_off_town",0);
					$this->DB2->where("req_status",14);
					$this->DB2->join("tbl_request", "req_id = cost_req", "left");
					$this->DB2->join("tbl_direktorat", "direktorat_id = req_direktorat", "left");
					$qpaketcancel = $this->DB2->get("tbl_trip_cost");
					
					if($qpaketcancel->num_rows>0)
					{
					  $datapaketcancel = $qpaketcancel->result();
					
						$totalcancel = count($datapaketcancel);
						
			
					}
					
					$hargatotalpaketmenit = $totalpaketmenit * ($this->config->item("tarif_dalam_kota_permenit"));
					$hargatotalpaket1jam = ($totalpaket1jammin + $totalpaket1jam) * ($this->config->item("tarif_dalam_kota_perjam"));
					$hargatotalpaket4jam = ( ($totalpaket4jam/4) * ($this->config->item("tarif_dalam_kota_4jam")) );
					$hargatotalpaket8jam = ( ($totalpaket8jam/8) * ($this->config->item("tarif_dalam_kota_8jam")) );
					$hargatotalpaket12jam = ( ($totalpaket12jam/12) * ($this->config->item("tarif_dalam_kota_12jam")) );
					$hargatotalpaket24jam = ( ($totalpaket24jam/24) * ($this->config->item("tarif_dalam_kota_24jam")) );
					$hargatotalcancel = $totalcancel * ($this->config->item("cancel_fee_inner"));
					$hargatotalall = $hargatotalpaketmenit + $hargatotalpaket1jam + $hargatotalpaket4jam +
									 $hargatotalpaket8jam + $hargatotalpaket12jam + $hargatotalpaket24jam + $hargatotalcancel;
									 
					$totalmenitbulat = $totalpaketbulat + $totalpaketmenitmin;

			$objPHPExcel->getActiveSheet()->SetCellValue('A'.(8+1+$i), $i);
			$objPHPExcel->getActiveSheet()->getStyle('A'.(8+1+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->SetCellValue('B'.(8+1+$i), $rows[$j]->unit_code);
			$objPHPExcel->getActiveSheet()->getStyle('B'.(8+1+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->SetCellValue('C'.(8+1+$i), $rows[$j]->direktorat_name." - ".$rows[$j]->direktorat_code);
			$objPHPExcel->getActiveSheet()->getStyle('C'.(8+1+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			
			$objPHPExcel->getActiveSheet()->SetCellValue('D'.(8+1+$i), $totalpaketmenit);	
			$objPHPExcel->getActiveSheet()->getStyle('D'.(8+1+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->SetCellValue('E'.(8+1+$i), $hargatotalpaketmenit);
			$objPHPExcel->getActiveSheet()->getStyle('E'.(8+1+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$objPHPExcel->getActiveSheet()->SetCellValue('F'.(8+1+$i), $totalpaketmenitmin);
			$objPHPExcel->getActiveSheet()->getStyle('F'.(8+1+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->SetCellValue('G'.(8+1+$i), $totalpaketbulat);
			$objPHPExcel->getActiveSheet()->getStyle('G'.(8+1+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->SetCellValue('H'.(8+1+$i), $totalmenitbulat);
			$objPHPExcel->getActiveSheet()->getStyle('H'.(8+1+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$objPHPExcel->getActiveSheet()->SetCellValue('I'.(8+1+$i), $totalpaket1jam+$totalpaket1jammin);
			$objPHPExcel->getActiveSheet()->getStyle('I'.(8+1+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->SetCellValue('J'.(8+1+$i), $hargatotalpaket1jam);
			$objPHPExcel->getActiveSheet()->getStyle('J'.(8+1+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$objPHPExcel->getActiveSheet()->SetCellValue('K'.(8+1+$i), $totalpaket4jam/4);
			$objPHPExcel->getActiveSheet()->getStyle('K'.(8+1+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->SetCellValue('L'.(8+1+$i), $hargatotalpaket4jam);
			$objPHPExcel->getActiveSheet()->getStyle('L'.(8+1+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$objPHPExcel->getActiveSheet()->SetCellValue('M'.(8+1+$i), $totalpaket8jam/8);
			$objPHPExcel->getActiveSheet()->getStyle('M'.(8+1+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->SetCellValue('N'.(8+1+$i), $hargatotalpaket8jam);
			$objPHPExcel->getActiveSheet()->getStyle('N'.(8+1+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$objPHPExcel->getActiveSheet()->SetCellValue('O'.(8+1+$i), $totalpaket12jam/12);
			$objPHPExcel->getActiveSheet()->getStyle('O'.(8+1+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->SetCellValue('P'.(8+1+$i), $hargatotalpaket12jam);
			$objPHPExcel->getActiveSheet()->getStyle('P'.(8+1+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$objPHPExcel->getActiveSheet()->SetCellValue('Q'.(8+1+$i), $totalpaket24jam/24);
			$objPHPExcel->getActiveSheet()->getStyle('Q'.(8+1+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->SetCellValue('R'.(8+1+$i), $hargatotalpaket24jam);
			$objPHPExcel->getActiveSheet()->getStyle('R'.(8+1+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$objPHPExcel->getActiveSheet()->SetCellValue('S'.(8+1+$i), $totalcancel);
			$objPHPExcel->getActiveSheet()->getStyle('S'.(8+1+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->SetCellValue('T'.(8+1+$i), $hargatotalcancel);
			$objPHPExcel->getActiveSheet()->getStyle('T'.(8+1+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$objPHPExcel->getActiveSheet()->SetCellValue('U'.(8+1+$i),$hargatotalall);
			$objPHPExcel->getActiveSheet()->getStyle('U'.(8+1+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			
			//penggunaan luar kota
			$objPHPExcel->getActiveSheet()->SetCellValue('A24', 'PENGGUNAAN LUAR KOTA');
			$objPHPExcel->getActiveSheet()->mergeCells('A24:C24');
			$objPHPExcel->getActiveSheet()->getStyle('A24')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$objPHPExcel->getActiveSheet()->getStyle('A24')->getFont()->setBold(true);
		
					//Hitung paket dalam kota
					$totalpaketmenit_lk = 0;
					$totalpaket1jam_lk = 0;
					$totalpaket6jam_lk = 0;
					$totalpaket8jam_lk = 0;
					$totalpaket12jam_lk = 0;
					$totalpaket24jam_lk = 0;
					$totalcancel_lk = 0;
					$totalall_lk = 0;
					$totalpaketmenitmin_lk = 0;
					$totalpaketbulat_lk = 0;
					$totalmenitbulat_lk = 0;
					$totalpaket1jammin_lk = 0;
					
					
					$hargatotalpaketmenit_lk = 0;
					$hargatotalpaket1jam_lk = 0;
					$hargatotalpaket6jam_lk = 0;
					$hargatotalpaket8jam_lk = 0;
					$hargatotalpaket12jam_lk = 0;
					$hargatotalpaket24jam_lk = 0;
					$hargatotalcancel_lk = 0;
					$hargatotalall_lk = 0;
					
					//select lebih dari 1 jam LK
					$this->DB2->group_by("cost_req");
					$this->DB2->select("cost_paketmenit,cost_paket1,cost_paket4,cost_paket6,cost_paket8,cost_paket12,cost_paket24,
										req_out_off_town,cost_working_unit,cost_trip_duration
					");
					$this->DB2->where("req_start_date >=",date("Y-m-d H:i:s",strtotime($startdate." "."00:00:00")));
					$this->DB2->where("req_start_date <=",date("Y-m-d H:i:s",strtotime($enddate." "."23:59:59")));
					$this->DB2->where("cost_working_unit",$rows[$j]->direktorat_working_unit);
					$this->DB2->where("direktorat_id",$rows[$j]->direktorat_id);
					$this->DB2->where("req_out_off_town",1);
					$this->DB2->where("req_status",3);
					$this->DB2->where("cost_trip_duration >=",3600);
					$this->DB2->join("tbl_request", "req_id = cost_req", "left");
					$this->DB2->join("tbl_direktorat", "direktorat_id = req_direktorat", "left");
					$qpaket_lk = $this->DB2->get("tbl_trip_cost");
					
					if($qpaket_lk->num_rows>0)
					{
					  $datapaket_lk = $qpaket_lk->result();
					  for($n=0;$n<$qpaket_lk->num_rows;$n++)
					  {
						 $totalpaketmenit_lk = $totalpaketmenit_lk + $datapaket_lk[$n]->cost_paketmenit;
						 $totalpaket1jam_lk = $totalpaket1jam_lk + $datapaket_lk[$n]->cost_paket1;
						 $totalpaket6jam_lk = $totalpaket6jam_lk + $datapaket_lk[$n]->cost_paket6;
						 $totalpaket8jam_lk = $totalpaket8jam_lk + $datapaket_lk[$n]->cost_paket8;
						 $totalpaket12jam_lk = $totalpaket12jam_lk + $datapaket_lk[$n]->cost_paket12;
						 $totalpaket24jam_lk = $totalpaket24jam_lk + $datapaket_lk[$n]->cost_paket24;
					  }
					 
					}
					
					//select dibawah 1jam LK
					$this->DB2->group_by("cost_req");
					$this->DB2->select("cost_paketmenit,cost_paket1,cost_paketbulat,
										req_out_off_town,cost_working_unit,cost_trip_duration
					");
					$this->DB2->where("req_start_date >=",date("Y-m-d H:i:s",strtotime($startdate." "."00:00:00")));
					$this->DB2->where("req_start_date <=",date("Y-m-d H:i:s",strtotime($enddate." "."23:59:59")));
					$this->DB2->where("cost_working_unit",$rows[$j]->direktorat_working_unit);
					$this->DB2->where("direktorat_id",$rows[$j]->direktorat_id);
					$this->DB2->where("req_out_off_town",1);
					$this->DB2->where("req_status",3);
					$this->DB2->where("cost_trip_duration <=",3599);
					$this->DB2->join("tbl_request", "req_id = cost_req", "left");
					$this->DB2->join("tbl_direktorat", "direktorat_id = req_direktorat", "left");
					$qpaketmin_lk = $this->DB2->get("tbl_trip_cost");
					
					if($qpaketmin_lk->num_rows>0)
					{
					  $datapaketmin_lk = $qpaketmin_lk->result();
					  for($n1=0;$n1<$qpaketmin_lk->num_rows;$n1++)
					  {
						 $totalpaketmenitmin_lk = $totalpaketmenitmin_lk + $datapaketmin_lk[$n1]->cost_paketmenit;
						 $totalpaketbulat_lk = $totalpaketbulat_lk + $datapaketmin_lk[$n1]->cost_paketbulat;
						 $totalpaket1jammin_lk = $totalpaket1jammin_lk + $datapaketmin_lk[$n1]->cost_paket1;
						 
					  }
					 
					}
					
					//hitung cancel
					$this->DB2->group_by("cost_req");
					$this->DB2->select("cost_note,req_out_off_town,cost_working_unit
										");
					$this->DB2->where("req_start_date >=",date("Y-m-d H:i:s",strtotime($startdate." "."00:00:00")));
					$this->DB2->where("req_start_date <=",date("Y-m-d H:i:s",strtotime($enddate." "."23:59:59")));
					$this->DB2->where("cost_working_unit",$rows[$j]->direktorat_working_unit);
					$this->DB2->where("direktorat_id",$rows[$j]->direktorat_id);
					$this->DB2->where("req_out_off_town",1);
					$this->DB2->where("req_status",14);
					$this->DB2->join("tbl_request", "req_id = cost_req", "left");
					$this->DB2->join("tbl_direktorat", "direktorat_id = req_direktorat", "left");
					$qpaketcancel_lk = $this->DB2->get("tbl_trip_cost");
					
					if($qpaketcancel_lk->num_rows>0)
					{
					  $datapaketcancel_lk = $qpaketcancel_lk->result();
					
						$totalcancel_lk = count($datapaketcancel_lk);
						
			
					}
					
					$hargatotalpaketmenit_lk = $totalpaketmenit_lk * ($this->config->item("tarif_luar_kota_permenit"));
					$hargatotalpaket1jam_lk = ($totalpaket1jam_lk+$totalpaket1jammin_lk) * ($this->config->item("tarif_luar_kota_perjam"));
					$hargatotalpaket6jam_lk = ( ($totalpaket6jam_lk/6) * ($this->config->item("tarif_luar_kota_6jam")) );
					$hargatotalpaket8jam_lk = ( ($totalpaket8jam_lk/8) * ($this->config->item("tarif_luar_kota_8jam")) );
					$hargatotalpaket12jam_lk = ( ($totalpaket12jam_lk/12) * ($this->config->item("tarif_luar_kota_12jam")) );
					$hargatotalpaket24jam_lk = ( ($totalpaket24jam_lk/24) * ($this->config->item("tarif_luar_kota_24jam")) );
					$hargatotalcancel_lk = $totalcancel_lk * ($this->config->item("cancel_fee_outter"));
					$hargatotalall_lk = $hargatotalpaketmenit_lk + $hargatotalpaket1jam_lk + $hargatotalpaket6jam_lk +
									 $hargatotalpaket8jam_lk + $hargatotalpaket12jam_lk + $hargatotalpaket24jam_lk + $hargatotalcancel_lk;
					
					$totalmenitbulat_lk = $totalpaketbulat_lk + $totalpaketmenitmin_lk;
					
			$objPHPExcel->getActiveSheet()->SetCellValue('A'.(27+1+$i), $i);
			$objPHPExcel->getActiveSheet()->getStyle('A'.(27+1+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->SetCellValue('B'.(27+1+$i), $rows[$j]->unit_code);
			$objPHPExcel->getActiveSheet()->getStyle('B'.(27+1+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->SetCellValue('C'.(27+1+$i), $rows[$j]->direktorat_name." - ".$rows[$j]->direktorat_code);
			$objPHPExcel->getActiveSheet()->getStyle('C'.(27+1+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			
			$objPHPExcel->getActiveSheet()->SetCellValue('D'.(27+1+$i), $totalpaketmenit_lk);	
			$objPHPExcel->getActiveSheet()->getStyle('D'.(27+1+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->SetCellValue('E'.(27+1+$i), $hargatotalpaketmenit_lk);
			$objPHPExcel->getActiveSheet()->getStyle('E'.(27+1+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$objPHPExcel->getActiveSheet()->SetCellValue('F'.(27+1+$i), $totalpaketmenitmin_lk);
			$objPHPExcel->getActiveSheet()->getStyle('F'.(27+1+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->SetCellValue('G'.(27+1+$i), $totalpaketbulat_lk);
			$objPHPExcel->getActiveSheet()->getStyle('G'.(27+1+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->SetCellValue('H'.(27+1+$i), $totalmenitbulat_lk);
			$objPHPExcel->getActiveSheet()->getStyle('H'.(27+1+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			
			$objPHPExcel->getActiveSheet()->SetCellValue('I'.(27+1+$i), $totalpaket1jam_lk+$totalpaket1jammin_lk);
			$objPHPExcel->getActiveSheet()->getStyle('I'.(27+1+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->SetCellValue('J'.(27+1+$i), $hargatotalpaket1jam_lk);
			$objPHPExcel->getActiveSheet()->getStyle('J'.(27+1+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$objPHPExcel->getActiveSheet()->SetCellValue('K'.(27+1+$i), $totalpaket6jam_lk/6);
			$objPHPExcel->getActiveSheet()->getStyle('K'.(27+1+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->SetCellValue('L'.(27+1+$i), $hargatotalpaket6jam_lk);
			$objPHPExcel->getActiveSheet()->getStyle('L'.(27+1+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$objPHPExcel->getActiveSheet()->SetCellValue('M'.(27+1+$i), $totalpaket8jam_lk/8);
			$objPHPExcel->getActiveSheet()->getStyle('M'.(27+1+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->SetCellValue('N'.(27+1+$i), $hargatotalpaket8jam_lk);
			$objPHPExcel->getActiveSheet()->getStyle('N'.(27+1+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$objPHPExcel->getActiveSheet()->SetCellValue('O'.(27+1+$i), $totalpaket12jam_lk/12);
			$objPHPExcel->getActiveSheet()->getStyle('O'.(27+1+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->SetCellValue('P'.(27+1+$i), $hargatotalpaket12jam_lk);
			$objPHPExcel->getActiveSheet()->getStyle('P'.(27+1+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$objPHPExcel->getActiveSheet()->SetCellValue('Q'.(27+1+$i), $totalpaket24jam_lk/24);
			$objPHPExcel->getActiveSheet()->getStyle('Q'.(27+1+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->SetCellValue('R'.(27+1+$i), $hargatotalpaket24jam_lk);
			$objPHPExcel->getActiveSheet()->getStyle('R'.(27+1+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$objPHPExcel->getActiveSheet()->SetCellValue('S'.(27+1+$i), $totalcancel_lk);
			$objPHPExcel->getActiveSheet()->getStyle('S'.(27+1+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->SetCellValue('T'.(27+1+$i), $hargatotalcancel_lk);
			$objPHPExcel->getActiveSheet()->getStyle('T'.(27+1+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$objPHPExcel->getActiveSheet()->SetCellValue('U'.(27+1+$i), $hargatotalall_lk);
			$objPHPExcel->getActiveSheet()->getStyle('U'.(27+1+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					
			$i++;
		}
		
		$styleArray = array(
				  'borders' => array(
				    'allborders' => array(
				      'style' => PHPExcel_Style_Border::BORDER_THIN
				    )
				  )
				);
			
			$objPHPExcel->getActiveSheet()->getStyle('A7:U'.(20+1))->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getStyle('A7:U'.(20+1))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
			$objPHPExcel->getActiveSheet()->getStyle('A7:U'.(20+1))->getAlignment()->setWrapText(true);
			
			$objPHPExcel->getActiveSheet()->getStyle('A26:U'.(39+1))->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getStyle('A26:U'.(39+1))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
			$objPHPExcel->getActiveSheet()->getStyle('A26:U'.(39+1))->getAlignment()->setWrapText(true);
			
			// Rename sheet
			$objPHPExcel->getActiveSheet()->setTitle('SC CATALOG REPORT');
			$now = date("Ymd_His");
			// Save Excel
			$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
			@mkdir(REPORT_PATH, DIR_WRITE_MODE);
			$filecreatedname = "DokarPAYU_SCCatalog_(".$now.")".".xls";
			
			//$objWriter->save(REPORT_PATH.$filecreatedname);
			$objWriter->save("assets/media/report/".$filecreatedname);
		
			$output = '{"success":true,"errMsg":"","filename":"' .base_url() . "assets/media/report/" . $filecreatedname.'"}';
			echo $output;
			return;
		
	}
	
	function get_parent_by_company($company){
		
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		$this->dbtransporter->order_by("parent_name","asc");
		$this->dbtransporter->where("parent_flag", 0);
		$this->dbtransporter->where("parent_company", $company);
		$qd = $this->dbtransporter->get("droppoint_parent");
		$rd = $qd->result();
			
		if($qd->num_rows() > 0){
			$options = "<option value='' selected='selected'>--Select Group--</option>";
			foreach($rd as $obj){
				$options .= "<option value='". $obj->parent_id . "'>". $obj->parent_name ."</option>";
			}
			
			echo $options;
			return;
		}
	}
	
	function get_distrep_by_parent($parent){
		
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		$this->dbtransporter->order_by("distrep_name","asc");
		$this->dbtransporter->where("distrep_flag", 0);
		$this->dbtransporter->where("distrep_parent", $parent);
		$qd = $this->dbtransporter->get("droppoint_distrep");
		$rd = $qd->result();
			
		if($qd->num_rows() > 0){
			$options = "<option value='' selected='selected'>--Select Distrep--</option>";
			foreach($rd as $obj){
				$options .= "<option value='". $obj->distrep_id . "'>". $obj->distrep_name ."</option>";
			}
			
			echo $options;
			return;
		}
	}
	
	function get_month(){
		
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		$this->dbtransporter->order_by("month_value","asc");
		$this->dbtransporter->where("month_status", 1);
		$qd = $this->dbtransporter->get("config_month");
		$rd = $qd->result();
		return $rd;
	}
	
	function get_year(){
		
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		$this->dbtransporter->order_by("year_value","asc");
		$this->dbtransporter->where("year_status", 1);
		$qd = $this->dbtransporter->get("config_year");
		$rd = $qd->result();
		return $rd;
	}
	
	function get_this_year($tahunini){
		
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		$this->dbtransporter->order_by("year_value","asc");
		$this->dbtransporter->where("year_status", 1);
		$this->dbtransporter->where("year_value", $tahunini);
		$qd = $this->dbtransporter->get("config_year");
		$rd = $qd->result();
		return $rd;
	}
	
	function get_this_month($bulanini){
		
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		$this->dbtransporter->order_by("month_value","asc");
		$this->dbtransporter->where("month_status", 1);
		$this->dbtransporter->where("month_value", $bulanini);
		$qd = $this->dbtransporter->get("config_month");
		$rd = $qd->result();
		return $rd;
	}
	
	function get_company_name($company){
		
		$this->db->order_by("company_name","asc");
		$this->db->where("company_id", $company);
		$qd = $this->db->get("company");
		$rd = $qd->row();
		
		return $rd;
	}
	
	function get_parent_name($parent){
		
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		$this->dbtransporter->where("parent_id", $parent);
		$qd = $this->dbtransporter->get("droppoint_parent");
		$rd = $qd->row();
		
		return $rd;
	}
	
	function get_distrep_name($distrep){
		
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		$this->dbtransporter->where("distrep_id", $distrep);
		$qd = $this->dbtransporter->get("droppoint_distrep");
		$rd = $qd->row();
		
		return $rd;
	}
	
	function get_monthly_report($sdate,$edate){
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		$this->dbtransporter->order_by("monthly_date","asc");
		$this->dbtransporter->where("monthly_date >=", $sdate);
		$this->dbtransporter->where("monthly_date <=", $edate);
		$qd = $this->dbtransporter->get("config_monthly_report");
		$rd = $qd->result();
		
		return $rd;
	}
	
	function get_parent_bycreator(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		$this->dbtransporter->order_by("parent_name","asc");
		$this->dbtransporter->where("parent_flag", 0);
		$this->dbtransporter->where("parent_creator", $this->sess->user_id);
		$qd = $this->dbtransporter->get("droppoint_parent");
		$rd = $qd->result();
		return $rd;
	}
	
	function get_distrep_bycreator(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		$this->dbtransporter->order_by("distrep_name","asc");
		$this->dbtransporter->where("distrep_flag", 0);
		$qd = $this->dbtransporter->get("droppoint_distrep");
		$rd = $qd->result();
		return $rd;
	}
	
	function get_geofence_bylogin(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		
		$this->db->select("geofence_id,geofence_user,geofence_name,geofence_type");
		$this->db->order_by("geofence_name","asc");
		$this->db->group_by("geofence_name");
		$this->db->where("geofence_status", 1);
		$this->db->where("geofence_user", $this->sess->user_id);
		$qd = $this->db->get("geofence");
		$rd = $qd->result();
		return $rd;
	}
	
	function get_company_bylogin(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		$this->db->order_by("company_name","asc");
		$this->db->where("company_flag", 0);
		$this->db->where("company_created_by", $this->sess->user_id);
		$qd = $this->db->get("company");
		$rd = $qd->result();
		
		return $rd;
	}
	
	function get_droppoint_bydistrep($distrep){
		
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		$this->dbtransporter->order_by("droppoint_name","asc");
		$this->dbtransporter->where("droppoint_flag",0);
		$this->dbtransporter->where("droppoint_distrep", $distrep);
		$qd = $this->dbtransporter->get("droppoint");
		$rd = $qd->result();
		
		return $rd;
	}
}
	
	