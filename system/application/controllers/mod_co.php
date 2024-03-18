<?php
include "base.php";

class Mod_co extends Base {
	
	function __construct()
	{
		parent::Base();
		$this->load->model("gpsmodel");
		$this->load->model("vehiclemodel");
		$this->load->model("configmodel");
		$this->load->model("historymodel");		
		$this->load->helper('url');
        $this->load->helper('form');
        $this->load->helper('file');
		$this->load->helper('download');
	}
	
	function menu()
	{
		
		if (! isset($this->sess->user_id))
		{
			redirect(base_url());
		}		
		
		$vehicle = $this->get_vehicle();
		$driver = $this->get_driver();
		$this->params['vehicle'] = $vehicle;
		$this->params['driver'] = $driver;
		$this->params['content'] = $this->load->view("mod_co/list", $this->params, true);
		
		$this->load->view("templatesess", $this->params);
	}
	
	function get_vehicle()
	{
		if (!isset($this->sess->user_id))
		{
			redirect(base_url());
		}
		
		$this->db->order_by("vehicle_no","asc");
		$this->db->where("vehicle_user_id",$this->sess->user_id);
		
		if ($this->sess->user_company > 0)
		{
			$this->db->or_where("vehicle_company",$this->sess->user_company);
		}
		
		if ($this->sess->user_group > 0)
		{
			$this->db->or_where("vehicle_group",$this->sess->user_group);
		}
		
		$this->db->where("vehicle_status <>",3);
		$q = $this->db->get("vehicle");
		$rows = $q->result();
		return $rows;
		
	}
	
	function get_driver()
	{
		$this->dbtrans = $this->load->database("transporter",true);
		$this->dbtrans->order_by("driver_name","asc");
		$this->dbtrans->where("driver_company",$this->sess->user_company);
		$this->dbtrans->where("driver_status",1);
		$q = $this->dbtrans->get("driver");
		$rows = $q->result();
		return $rows;
	}
	
	function search_co()
	{
		$field = isset($_POST['field']) ? $_POST['field'] : "";
		$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;	
		$sortby = isset($_POST['sortby']) ? $_POST['sortby'] : "destination_id";
		$orderby = isset($_POST['orderby']) ? $_POST['orderby'] : "desc";		
		$destination_vehicle = isset($_POST['destination_vehicle']) ? $_POST['destination_vehicle'] : "";
		$destination_driver = isset($_POST['destination_driver']) ? $_POST['destination_driver'] : "";
		$startdate = isset($_POST['startdate']) ? $_POST['startdate'] : "";		
		$enddate = isset($_POST['enddate']) ? $_POST['enddate'] : "";

		$startdate_fmt = date("Y-m-d",strtotime($startdate));
		$enddate_fmt = date("Y-m-d",strtotime($enddate));

		$this->dbtransporter = $this->load->database('transporter', true);
		$my_company = $this->sess->user_company;
		
		$vehicle = $this->get_vehicle();
		$driver = $this->get_driver();
			
		$this->dbtransporter->order_by("destination_id","desc");
		
		
		switch($field)
		{
			case "destination_name1":
				$this->dbtransporter->where("destination_name1 like", '%'.$keyword.'%');
			break;
			case "destination_vehicle":
				$this->dbtransporter->where("destination_vehicle",$destination_vehicle);
			break;
			case "destination_driver":
				$this->dbtransporter->where("destination_driver",$destination_driver);
			break;
			case "destination_date":
				$this->dbtransporter->where("destination_date >= ", $startdate_fmt);
				$this->dbtransporter->where("destination_date <= ", $enddate_fmt);
			break;
		}

		$this->dbtransporter->where("destination_company", $my_company);
		$this->dbtransporter->where("destination_status", 1);
		$q = $this->dbtransporter->get("destination_reksa", 50, $offset);
		$rows = $q->result();
		
		$this->dbtransporter->select("count(*) as total");
		
		switch($field)
		{
			case "destination_name1":
				$this->dbtransporter->where("destination_name1 like", '%'.$keyword.'%');
			break;
			case "destination_vehicle":
				$this->dbtransporter->where("destination_vehicle",$destination_vehicle);
			break;
			case "destination_driver":
				$this->dbtransporter->where("destination_driver",$destination_driver);
			break;
			case "destination_date":
				$this->dbtransporter->where("destination_date >= ", $startdate_fmt);
				$this->dbtransporter->where("destination_date <= ", $enddate_fmt);
			break;
		}
		
		$this->dbtransporter->where("destination_company", $my_company);
		$this->dbtransporter->where("destination_status", 1);
		$qt = $this->dbtransporter->get("destination_reksa");
		$rt = $qt->row();
		$total = $rt->total;
		
		$this->load->library("pagination1");
		
		$config['uri_segment'] = 4;
		$config['total_rows'] = $total;
		$config['per_page'] = 50;
		$config['num_links'] = floor($total/50);
		
		$this->pagination1->initialize($config);
		$this->params["paging"] = $this->pagination1->create_links();
		$this->params["driver"] = $driver;
		$this->params["vehicle"] = $vehicle;
		$this->params["title"] = "CO Number";
		$this->params["field"] = $field;
		$this->params["keyword"] = $keyword;
		$this->params["offset"] = $offset;
		$this->params["total"] = $total;
		$this->params["data"] = $rows;
		$this->params["orderby"] = $orderby;
		$this->params["sortby"] = $sortby;
		
		$this->dbtransporter->close();
		
		$callback['html'] = $this->load->view("mod_co/list_result", $this->params, true);
		$callback['total'] = $total;
		
		echo json_encode($callback);
	}
	
	function add_co()
	{
		if (! isset($this->sess->user_company)) 
		{
			redirect(base_url());
		}
		
		$vehicle = $this->get_vehicle();
        $this->params["vehicle"] = $vehicle;
		$this->params["title"] = "Manage No. CO - ADD";		
		$this->params['content'] = $this->load->view("mod_co/add_co", $this->params, true);
		$this->load->view("templatesess", $this->params);
	}
	
	function save_co()
	{
		if (! isset($this->sess->user_id)) 
		{
			redirect(base_url());
		}
		
		$this->dbtrans = $this->load->database("transporter",true);
		$my_company = $this->sess->user_company;
		
		$destination_name1 = isset($_POST['destination_name1']) ? $_POST['destination_name1'] : "";
		$destination_vehicle = isset($_POST['destination_vehicle']) ? $_POST['destination_vehicle'] : "";
		$destination_date = isset($_POST['destination_date']) ? $_POST['destination_date'] : "";
		
		if ($destination_name1 == "")
		{
			$callback['error'] = true;
			$callback['message'] = "Please Input No. CO !";
		
			echo json_encode($callback);
			return;
		}
		
		if ($destination_vehicle == "")
		{
			$callback['error'] = true;
			$callback['message'] = "Please Select Vehicle !";
		
			echo json_encode($callback);
			return;
		}
		
		if ($destination_date == "")
		{
			$callback['error'] = true;
			$callback['message'] = "Please Inpute Date !";
		
			echo json_encode($callback);
			return;
		}
		
		if (isset($destination_date))
		{
			$destination_date = date("Y-m-d",strtotime($destination_date));
		}
		
		if (isset($destination_name1))
		{
			//Cek No CO Sebelumnya
			$this->dbtrans->where("destination_name1",$destination_name1);
			$this->dbtrans->where("destination_status",1);
			$this->dbtrans->where("destination_company",$this->sess->user_company);
			$v = $this->dbtrans->get("destination_reksa");
			$co = $v->row();
			
			if ($v->num_rows()>0)
			{
				$callback['error'] = true;
				$callback['message'] = "Already CO Number !!";
		
				echo json_encode($callback);
				return;
			}
		}
		
		//Select Vehicle
		$this->db->where("vehicle_device",$destination_vehicle);
		$this->db->limit(1);
		$q = $this->db->get("vehicle");
		$vehicle = $q->row();
		
		//Select Driver
		$this->dbtrans->where("driver_vehicle",$vehicle->vehicle_id);
		$q = $this->dbtrans->get("driver");
		$driver = $q->row();
		
		if ($q->num_rows()==0)
		{
			$callback['error'] = true;
			$callback['message'] = "No Driver For This Vehicle Select !! Or Inactive Driver ";
		
			echo json_encode($callback);
			return;
		}
		
		unset($data);
		$data['destination_name1'] = $destination_name1;
		$data['destination_vehicle'] = $destination_vehicle;
		$data['destination_vehicle_no'] = $vehicle->vehicle_no;
		$data['destination_driver'] = $driver->driver_id;
		$data['destination_date'] = $destination_date;
		$data['destination_create_user'] = $this->sess->user_id;
		$data['destination_company'] = $this->sess->user_company;
		$data['destination_create_date'] = date("Y-m-d H:i:s");
		
		$this->dbtrans->insert("destination_reksa",$data);
		$this->dbtrans->close();
		
		$callback["error"] = false;
		$callback["message"] = "Add No.CO Success";
		$callback["redirect"] = base_url()."mod_co/menu";
	
		echo json_encode($callback);
		return;

	}
	
	function edit_co()
	{
		$destination_id = $this->input->post('id');
		$this->dbtransporter = $this->load->database('transporter', true);
		$my_company = $this->sess->user_company;
		
		$this->dbtransporter->where("destination_id", $destination_id);
		$q = $this->dbtransporter->get("destination_reksa");
		$row = $q->row();
		
		$vehicle = $this->get_vehicle();
		
		$params["vehicle"] = $vehicle;
		$params["data"] = $row;
		
		$html = $this->load->view('mod_co/edit_co', $params, true);
		$callback["error"] = false;
		$callback["html"] = $html;
			
		echo json_encode($callback);
	}
	
	function saveedit_co()
	{
		if (! isset($this->sess->user_id)) 
		{
			redirect(base_url());
		}
		
		$this->dbtrans = $this->load->database("transporter",true);
		$my_company = $this->sess->user_company;
		
		$destination_id = isset($_POST['destination_id']) ? $_POST['destination_id'] : "";
		$destination_name1 = isset($_POST['destination_name1']) ? $_POST['destination_name1'] : "";
		$destination_name1_bf = isset($_POST['destination_name1_bf']) ? $_POST['destination_name1_bf'] : "";
		$destination_vehicle = isset($_POST['destination_vehicle']) ? $_POST['destination_vehicle'] : "";
		$destination_date = isset($_POST['destination_date']) ? $_POST['destination_date'] : "";
		
		if ($destination_name1 == "")
		{
			$callback['error'] = true;
			$callback['message'] = "Please Input No. CO !";
		
			echo json_encode($callback);
			return;
		}
		
		if ($destination_vehicle == "")
		{
			$callback['error'] = true;
			$callback['message'] = "Please Select Vehicle !";
		
			echo json_encode($callback);
			return;
		}
		
		if ($destination_date == "")
		{
			$callback['error'] = true;
			$callback['message'] = "Please Inpute Date !";
		
			echo json_encode($callback);
			return;
		}
		
		if (isset($destination_date))
		{
			$destination_date = date("Y-m-d",strtotime($destination_date));
		}
		
		if ($destination_name1 != $destination_name1_bf)
		{
			//Cek No CO Sebelumnya
			$this->dbtrans->where("destination_name1",$destination_name1);
			$this->dbtrans->where("destination_status",1);
			$this->dbtrans->where("destination_company",$this->sess->user_company);
			$v = $this->dbtrans->get("destination_reksa");
			$co = $v->row();
			
			if ($v->num_rows()>0)
			{
				$callback['error'] = true;
				$callback['message'] = "Already CO Number !!";
		
				echo json_encode($callback);
				return;
			}
		}
		
		//Select Vehicle
		$this->db->where("vehicle_device",$destination_vehicle);
		$this->db->limit(1);
		$q = $this->db->get("vehicle");
		$vehicle = $q->row();
		
		//Select Driver
		$this->dbtrans->where("driver_vehicle",$vehicle->vehicle_id);
		$q = $this->dbtrans->get("driver");
		$driver = $q->row();
		
		if ($q->num_rows()==0)
		{
			$callback['error'] = true;
			$callback['message'] = "No Driver For This Vehicle Select !! Or Inactive Driver ";
		
			echo json_encode($callback);
			return;
		}
		
		unset($data);
		$data['destination_name1'] = $destination_name1;
		$data['destination_vehicle'] = $destination_vehicle;
		$data['destination_vehicle_no'] = $vehicle->vehicle_no;
		$data['destination_driver'] = $driver->driver_id;
		$data['destination_date'] = $destination_date;
		$data['destination_create_user'] = $this->sess->user_id;
		$data['destination_company'] = $this->sess->user_company;
		$data['destination_create_date'] = date("Y-m-d H:i:s");
		
		$this->dbtrans->where("destination_id",$destination_id);
		$this->dbtrans->update("destination_reksa",$data);
		$this->dbtrans->close();
		
		$callback["error"] = false;
		$callback["message"] = "Edit No.CO Success";
		$callback["redirect"] = base_url()."mod_co/menu";
	
		echo json_encode($callback);
		return;

	}
	
	function delete_co($id)
	{
		$this->dbtransporter = $this->load->database('transporter', true);
		unset($data);
		$data["destination_status"] = 2;
		
		$this->dbtransporter->where("destination_id", $id);
		if($this->dbtransporter->update("destination_reksa", $data))
		{
			$callback['message'] = "Data has been deleted";
			$callback['error'] = false;	
		}
		else
		{
			$callback['message'] = "Failed delete data";
			$callback['error'] = true;	
		}
		
		echo json_encode($callback);		
	}
	
	function export_co()
	{
		$field = isset($_POST['field']) ? $_POST['field'] : "";
		$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;	
		$sortby = isset($_POST['sortby']) ? $_POST['sortby'] : "destination_id";
		$orderby = isset($_POST['orderby']) ? $_POST['orderby'] : "desc";		
		$destination_vehicle = isset($_POST['destination_vehicle']) ? $_POST['destination_vehicle'] : "";
		$destination_driver = isset($_POST['destination_driver']) ? $_POST['destination_driver'] : "";
		$startdate = isset($_POST['startdate']) ? $_POST['startdate'] : "";		
		$enddate = isset($_POST['enddate']) ? $_POST['enddate'] : "";

		$startdate_fmt = date("Y-m-d",strtotime($startdate));
		$enddate_fmt = date("Y-m-d",strtotime($enddate));

		$this->dbtransporter = $this->load->database('transporter', true);
		$my_company = $this->sess->user_company;
		
		$vehicle = $this->get_vehicle();
		$driver = $this->get_driver();
			
		$this->dbtransporter->order_by("destination_id","desc");
		
		
		switch($field)
		{
			case "destination_name1":
				$this->dbtransporter->where("destination_name1 like", '%'.$keyword.'%');
			break;
			case "destination_vehicle":
				$this->dbtransporter->where("destination_vehicle",$destination_vehicle);
			break;
			case "destination_driver":
				$this->dbtransporter->where("destination_driver",$destination_driver);
			break;
			case "destination_date":
				$this->dbtransporter->where("destination_date >= ", $startdate_fmt);
				$this->dbtransporter->where("destination_date <= ", $enddate_fmt);
			break;
		}

		$this->dbtransporter->where("destination_company", $my_company);
		$this->dbtransporter->where("destination_status", 1);
		$q = $this->dbtransporter->get("destination_reksa", 50, $offset);
		$data = $q->result();
		
		/** PHPExcel */
		include 'class/PHPExcel.php';		
		/** PHPExcel_Writer_Excel2007 */
		include 'class/PHPExcel/Writer/Excel2007.php';		
		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();
		
		// Set properties
		$objPHPExcel->getProperties()->setCreator("lacak-mobil.com");
		$objPHPExcel->getProperties()->setLastModifiedBy("lacak-mobil.com");
		$objPHPExcel->getProperties()->setTitle("Report No. CO");
		$objPHPExcel->getProperties()->setSubject("Report No. CO");
		$objPHPExcel->getProperties()->setDescription("Report No. CO");
		
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
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);			
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
		
		 //Header
		 $objPHPExcel->setActiveSheetIndex(0);
		 $objPHPExcel->getActiveSheet()->mergeCells('A1:E1');
		 $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Report No. CO');
		 $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		 $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		 
		 $objPHPExcel->getActiveSheet()->SetCellValue('A5', 'No');
		 $objPHPExcel->getActiveSheet()->SetCellValue('B5', 'No. CO');
		 $objPHPExcel->getActiveSheet()->SetCellValue('C5', 'Vehicle');
		 $objPHPExcel->getActiveSheet()->SetCellValue('D5', 'Driver');
		 $objPHPExcel->getActiveSheet()->SetCellValue('E5', 'Date');
		 
		 $objPHPExcel->getActiveSheet()->getStyle('A5:E5')->getFont()->setBold(true);
         $objPHPExcel->getActiveSheet()->getStyle('A5:E5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		 
		 $i=1;
		 for($i=0; $i < count($data); $i++)
		 {
			$objPHPExcel->getActiveSheet()->SetCellValue('A'.(6+$i), $i+1);
			$objPHPExcel->getActiveSheet()->SetCellValue('B'.(6+$i), $data[$i]->destination_name1);
			$objPHPExcel->getActiveSheet()->SetCellValue('C'.(6+$i), $data[$i]->destination_vehicle_no);
			if (isset($driver))
			{
				foreach ($driver as $d)
				{
					if ($d->driver_id == $data[$i]->destination_driver)
					{
						$val = $d->driver_name;
					}
				}
			}
			$objPHPExcel->getActiveSheet()->SetCellValue('D'.(6+$i), $val);
			
			$val = date("d-m-Y",strtotime($data[$i]->destination_date));
			$objPHPExcel->getActiveSheet()->SetCellValue('E'.(6+$i), $val);
		 }
		 
		 $styleArray = array(
				  'borders' => array(
				    'allborders' => array(
				      'style' => PHPExcel_Style_Border::BORDER_THIN
				    )
				  )
				);
            
            $objPHPExcel->getActiveSheet()->getStyle('A5:E'.(5+$i))->applyFromArray($styleArray);
            $objPHPExcel->getActiveSheet()->getStyle('A5:E'.(5+$i))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
            $objPHPExcel->getActiveSheet()->getStyle('A5:E'.(5+$i))->getAlignment()->setWrapText(true);
            
            // Rename sheet
            $objPHPExcel->getActiveSheet()->setTitle('Report No. CO');
	
            // Save Excel
            $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
            @mkdir(REPORT_PATH, DIR_WRITE_MODE); //REPORT_PATH -> config->constant
            $filecreatedname = "Report_No_CO".date('YmdHis') . ".xls";
            $objWriter->save(REPORT_PATH.$filecreatedname);	
            $output = '{"success":true,"errMsg":"","filename":"' .base_url() . "assets/media/report/" . $filecreatedname.'"}';
            echo $output;
            return;
	}

	function upload_co()
	{
		$this->dbtrans = $this->load->database('transporter', true);
		
		$vehicle_no = "";
		$co_no = "";
						
		$config['upload_path'] = './temp_upload/conumber';
		$config['allowed_types'] = 'txt';
		$config['max_size']	= '100';
		$config['max_width']  = '1024';
		$config['max_height']  = '768';
		
		$this->load->library('upload', $config);

		if ( ! $this->upload->do_upload())
		{
			$this->params['error'] = $this->upload->display_errors();
			$this->params['content'] = $this->load->view("mod_co/upload_error", $this->params, true);
			$this->load->view("templatesess", $this->params);
		}
		else
		{
			$data = $this->upload->data();
			$file_name = $data["file_name"];
			
			$extensions = array("txt");
		
			$file_path = $config['upload_path'];
			$filenames = get_filenames($file_path,$extensions);
			$totalfile = count($filenames);
			
			if (isset($filenames) && $totalfile > 0)
			{
				$string = $filenames[0];
				$line = file($string);
				$total_line = count($line);
				
				for ($i=1; $i<count($line); $i++)
				{
					$co = trim($line[$i]);
					$co = str_replace(" ","",$line[$i]);
					
					if (isset($line[$i]))
					{
						$xyz = explode(",",$co);
						if (isset($xyz[0]))
						{
							$vehicle_no = $xyz[0];
						}
						if (isset($xyz[1]))
						{
							$co_no = $xyz[1];
						}
					}
					
					//Cek sudah ada datanya belum
					$this->dbtrans->where("destination_name1",$co_no);
					$this->dbtrans->where("destination_date",date("Y-m-d"));
					$this->dbtrans->where("destination_status",1);
					$this->dbtrans->where("destination_company",$this->sess->user_company);
					$q = $this->dbtrans->get("destination_reksa");
					
					if ($q->num_rows == 0)
					{
						//Cari Mobil
						$this->db->where("vehicle_no",$vehicle_no);
						$this->db->where("vehicle_user_id",$this->sess->user_id);
						$this->db->limit(1);
						$q = $this->db->get("vehicle");
						$vehicle = $q->row();
						if ($q->num_rows>0)
						{
							//Cari Driver
							$this->dbtrans->where("driver_vehicle",$vehicle->vehicle_id);
							$this->dbtrans->limit(1);
							$q = $this->dbtrans->get("driver");
							$driver = $q->row();
							if ($q->num_rows>0)
							{
								//Insert To Database
								unset($data);
								$data['destination_name1'] = $co_no;
								$data['destination_vehicle'] = $vehicle->vehicle_device;
								$data['destination_vehicle_no'] = $vehicle->vehicle_no;
								$data['destination_driver'] = $driver->driver_id;
								$data['destination_date'] = date("Y-m-d");
								$data['destination_create_user'] = $this->sess->user_id;
								$data['destination_company'] = $this->sess->user_company;
								$data['destination_create_date'] = date("Y-m-d H:i:s");
		
								$this->dbtrans->insert("destination_reksa",$data);
								$this->dbtrans->close();
							}
						}
					}
				}
				
				//Delete File Upload
				if (unlink($filenames[0]))
				{}
				else
				{}
			}
			
			redirect(base_url()."mod_co/menu");
			
		}

	}
	
}

/* End of file driver.php */
/* Location: ./system/application/controllers/transporter/driver.php */