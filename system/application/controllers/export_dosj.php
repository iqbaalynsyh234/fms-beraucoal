<?php
include "base.php";

class Export_dosj extends Base {
	
		var $period1;
		var $period2;
		var $tblhist;
		var $tblinfohist;
		var $otherdb;
	
	function Export_dosj()
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
		
		if (! isset($this->sess->user_company)){
		redirect(base_url());
		}		
		
	}
        
        function dosj()
        {
            /** PHPExcel */
            include 'class/PHPExcel.php';		
            /** PHPExcel_Writer_Excel2007 */
            include 'class/PHPExcel/Writer/Excel2007.php';		
            // Create new PHPExcel object
            $objPHPExcel = new PHPExcel();
            
            $field = isset($_POST['field']) ? $_POST['field'] : "";
            $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
            $offset = isset($_POST['offset']) ? $_POST['offset'] : 0;	
            $customer = isset($_POST['customer']) ? $_POST['customer'] : "";
		
            $this->dbtransporter = $this->load->database('transporter', true);
            $dosj_company = $this->sess->user_company;
            
            if (!$dosj_company)
            {
                redirect(base_url());
            }
            
            switch($field)
            {
		case "dosj_no":
			$this->dbtransporter->where("dosj_no", $keyword);
		break;
		case "customer":
			$this->dbtransporter->where("dosj_customer_id", $customer);
		break;
            }
		
            $this->dbtransporter->where("dosj_company", $dosj_company);
            $this->dbtransporter->order_by("dosj_id","desc");
            $q = $this->dbtransporter->get("dosj");
            $data = $q->result();
		
            switch($field)
            {
		case "do_no":
			$this->dbtransporter->where("dosj_no", $keyword);
		break;
		case "customer":
			$this->dbtransporter->where("dosj_customer_id", $customer);
		break;
            }
		
            $this->dbtransporter->where("dosj_company", $dosj_company);
            $this->dbtransporter->order_by("dosj_id","desc");
            $qtotal = $this->dbtransporter->get("dosj");
            $rowstotal = $qtotal->result();
		
            $total = count($rowstotal);
		
            //Get Customer
            $customer = $this->get_customer();
            
            // Set properties
            $objPHPExcel->getProperties()->setCreator("lacak-mobil.com");
            $objPHPExcel->getProperties()->setLastModifiedBy("lacak-mobil.com");
            $objPHPExcel->getProperties()->setTitle("Data SO");
            $objPHPExcel->getProperties()->setSubject("Data SO");
            $objPHPExcel->getProperties()->setDescription("Data SO");
            
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
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
            
            //Header
            $objPHPExcel->setActiveSheetIndex(0);
            $objPHPExcel->getActiveSheet()->mergeCells('A1:J1');
            $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Data SO');
            $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
            
            $objPHPExcel->getActiveSheet()->SetCellValue('A5', 'No');
            $objPHPExcel->getActiveSheet()->SetCellValue('B5', 'SO Type');
            $objPHPExcel->getActiveSheet()->mergeCells('C4:D4');
            $objPHPExcel->getActiveSheet()->SetCellValue('C4', 'SO Number');
            $objPHPExcel->getActiveSheet()->getStyle('C4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('C4')->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->SetCellValue('C5', 'Block');
            $objPHPExcel->getActiveSheet()->SetCellValue('D5', 'Mortar');
            $objPHPExcel->getActiveSheet()->SetCellValue('E5', 'Customer');
            $objPHPExcel->getActiveSheet()->SetCellValue('F5', 'Item Desc');
            $objPHPExcel->getActiveSheet()->mergeCells('G4:H4');
            $objPHPExcel->getActiveSheet()->SetCellValue('G4', 'Total Quantity');
            $objPHPExcel->getActiveSheet()->getStyle('G4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('G4')->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->SetCellValue('G5', 'Block');
            $objPHPExcel->getActiveSheet()->SetCellValue('H5', 'Mortar');
            $objPHPExcel->getActiveSheet()->mergeCells('I4:J4');
            $objPHPExcel->getActiveSheet()->SetCellValue('I4', 'SJP');
            $objPHPExcel->getActiveSheet()->getStyle('I4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('I4')->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->SetCellValue('I5', 'Block');
            $objPHPExcel->getActiveSheet()->SetCellValue('J5', 'Mortar');
            
            $objPHPExcel->getActiveSheet()->getStyle('A5:J5')->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle('A5:J5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            
            $i=1;
            for($i=0; $i < count($data); $i++)
            {
                $objPHPExcel->getActiveSheet()->SetCellValue('A'.(6+$i), $i+1);
                $objPHPExcel->getActiveSheet()->SetCellValue('B'.(6+$i), $data[$i]->dosj_type);
                $objPHPExcel->getActiveSheet()->SetCellValue('C'.(6+$i), $data[$i]->dosj_no_block);
                $objPHPExcel->getActiveSheet()->SetCellValue('D'.(6+$i), $data[$i]->dosj_no_mortar);
                
                if (isset($customer) && $data[$i]->dosj_customer_tmp == "")
                {
                    if (count($customer)>0)
                    {
                        for ($y=0;$y<count($customer);$y++)
                        {
                            if ($customer[$y]->group_id == $data[$i]->dosj_customer_id)
                            {
                                $a = $customer[$y]->group_name;
                            }
                        }
                    }
                }
                else 
                    {
                        $a = $data[$i]->dosj_customer_tmp;
                    }
                    
                $objPHPExcel->getActiveSheet()->SetCellValue('E'.(6+$i), $a);
                $objPHPExcel->getActiveSheet()->SetCellValue('F'.(6+$i), $data[$i]->dosj_item_desc.",".$data[$i]->dosj_item_size);
                $objPHPExcel->getActiveSheet()->SetCellValue('G'.(6+$i), $data[$i]->dosj_item_quantity);
                $objPHPExcel->getActiveSheet()->SetCellValue('H'.(6+$i), $data[$i]->dosj_item_quantity_mortar);
                $objPHPExcel->getActiveSheet()->SetCellValue('I'.(6+$i), $data[$i]->dosj_block_no);
                $objPHPExcel->getActiveSheet()->SetCellValue('J'.(6+$i), $data[$i]->dosj_mortar_no);
            }
            
            $styleArray = array(
				  'borders' => array(
				    'allborders' => array(
				      'style' => PHPExcel_Style_Border::BORDER_THIN
				    )
				  )
				);
            
            $objPHPExcel->getActiveSheet()->getStyle('A4:J'.(5+$i))->applyFromArray($styleArray);
            $objPHPExcel->getActiveSheet()->getStyle('A4:J'.(5+$i))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
            $objPHPExcel->getActiveSheet()->getStyle('A4:J'.(5+$i))->getAlignment()->setWrapText(true);
            
            // Rename sheet
            $objPHPExcel->getActiveSheet()->setTitle('Data SO');
	
            // Save Excel
            $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
            @mkdir(REPORT_PATH, DIR_WRITE_MODE); //REPORT_PATH -> config->constant
            $filecreatedname = "Data_SO_".date('YmdHis') . ".xls";
            $objWriter->save(REPORT_PATH.$filecreatedname);	
            $output = '{"success":true,"errMsg":"","filename":"' .base_url() . "assets/media/report/" . $filecreatedname.'"}';
            echo $output;
            return;
            
        }
        
        function dosj_history()
        {
            /** PHPExcel */
            include 'class/PHPExcel.php';		
            /** PHPExcel_Writer_Excel2007 */
            include 'class/PHPExcel/Writer/Excel2007.php';		
            // Create new PHPExcel object
            $objPHPExcel = new PHPExcel();
            
            $field = isset($_POST['field']) ? $_POST['field'] : "";
            $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
            $offset = isset($_POST['offset']) ? $_POST['offset'] : 0;
            $sdate = isset($_POST['sdate']) ? $_POST['sdate'] : "";
            $edate = isset($_POST['edate']) ? $_POST['edate'] : "";
		
            $this->dbtransporter = $this->load->database('transporter', true);
            $dosj_company = $this->sess->user_company;
		
            if (!$dosj_company)
            {
                redirect(base_url());
            }
		
            switch($field)
            {
                case "dosj_no":
                    $this->dbtransporter->where("do_delivered_do_number", $keyword);
                    break;
                    }
		
            if ($sdate != "" && $edate != "")
            {
                $fm_sdate = date("Y-m-d", strtotime($sdate));
                $fm_edate = date("Y-m-d", strtotime($edate));
                $this->dbtransporter->where("do_delivered_date >=", $fm_sdate);
                $this->dbtransporter->where("do_delivered_date <=", $fm_edate);
            }
		
            $this->dbtransporter->where("do_delivered_company", $dosj_company);
            $this->dbtransporter->order_by("do_delivered_id","desc");
            $this->dbtransporter->order_by("do_delivered_do_number","asc");
            $this->dbtransporter->join("driver","driver_id = do_delivered_driver", "left");
            $this->dbtransporter->join("cost","cost_id = do_delivered_cost", "left");
            $q = $this->dbtransporter->get("dosj_delivered");
            $data = $q->result();
		
            switch($field)
            {
                case "dosj_no":
                    $this->dbtransporter->where("do_delivered_do_number", $keyword);
                    break;
                    }
		
            if ($sdate != "" && $edate != "")
            {
                $fm_sdate = date("Y-m-d", strtotime($sdate));
                $fm_edate = date("Y-m-d", strtotime($edate));
                $this->dbtransporter->where("do_delivered_date >=", $fm_sdate);
                $this->dbtransporter->where("do_delivered_date <=", $fm_edate);
            }
		
            $this->dbtransporter->where("do_delivered_company", $dosj_company);
            $qtotal = $this->dbtransporter->get("dosj_delivered");
            $rowstotal = $qtotal->result();
		
            $total = count($rowstotal);
		
            $vehicle = $this->get_vehicle();
            
            // Set properties
            $objPHPExcel->getProperties()->setCreator("lacak-mobil.com");
            $objPHPExcel->getProperties()->setLastModifiedBy("lacak-mobil.com");
            $objPHPExcel->getProperties()->setTitle("Data SO History");
            $objPHPExcel->getProperties()->setSubject("Data SO History");
            $objPHPExcel->getProperties()->setDescription("Data SO History");
            
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
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
            
            //Header
            $objPHPExcel->setActiveSheetIndex(0);
            $objPHPExcel->getActiveSheet()->mergeCells('A1:J1');
            $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Data SO History');
            $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
            
            $objPHPExcel->getActiveSheet()->SetCellValue('A5', 'No');
            $objPHPExcel->getActiveSheet()->SetCellValue('B5', 'SO Type');
            $objPHPExcel->getActiveSheet()->mergeCells('C4:D4');
            $objPHPExcel->getActiveSheet()->SetCellValue('C4', 'SO Number');
            $objPHPExcel->getActiveSheet()->getStyle('C4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('C4')->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->SetCellValue('C5', 'Block');
            $objPHPExcel->getActiveSheet()->SetCellValue('D5', 'Mortar');
            $objPHPExcel->getActiveSheet()->SetCellValue('E5', 'Vehicle');
            $objPHPExcel->getActiveSheet()->SetCellValue('F5', 'Driver');
            $objPHPExcel->getActiveSheet()->mergeCells('G4:H4');
            $objPHPExcel->getActiveSheet()->SetCellValue('G4', 'Quantity On Delivery');
            $objPHPExcel->getActiveSheet()->getStyle('G4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('G4')->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->SetCellValue('G5', 'Block');
            $objPHPExcel->getActiveSheet()->SetCellValue('H5', 'Mortar');
            $objPHPExcel->getActiveSheet()->SetCellValue('I5', 'Cost');
            $objPHPExcel->getActiveSheet()->SetCellValue('J5', 'Ship Date');
            
            $objPHPExcel->getActiveSheet()->getStyle('A5:J5')->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle('A5:J5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            
            $i=1;
            for($i=0; $i < count($data); $i++)
            {
                $objPHPExcel->getActiveSheet()->SetCellValue('A'.(6+$i), $i+1);
                $objPHPExcel->getActiveSheet()->SetCellValue('B'.(6+$i), $data[$i]->do_delivered_do_type);
                $objPHPExcel->getActiveSheet()->SetCellValue('C'.(6+$i), $data[$i]->do_delivered_do_block);
                $objPHPExcel->getActiveSheet()->SetCellValue('D'.(6+$i), $data[$i]->do_delivered_do_mortar);
                
                if (isset($vehicle))
                {
                    if (count($vehicle)>0)
                    {
                        for ($y=0;$y<count($vehicle);$y++)
                        {
                            if ($vehicle[$y]->vehicle_device == $data[$i]->do_delivered_vehicle)
                            {
                                $a = $vehicle[$y]->vehicle_name." ".$vehicle[$y]->vehicle_no;
                            }
                        }
                    }
                    else
                    {
                        $a = "-";
                    }
                }
                else
                {
                    $a = "-";
                }
                
                $objPHPExcel->getActiveSheet()->SetCellValue('E'.(6+$i), $a);
                $objPHPExcel->getActiveSheet()->SetCellValue('F'.(6+$i), $data[$i]->driver_name);
                $objPHPExcel->getActiveSheet()->SetCellValue('G'.(6+$i), $data[$i]->do_delivered_quantity);
                $objPHPExcel->getActiveSheet()->SetCellValue('H'.(6+$i), $data[$i]->do_delivered_quantity_mortar);
                $objPHPExcel->getActiveSheet()->SetCellValue('I'.(6+$i), "Rp."." ".number_format($data[$i]->cost));
                $objPHPExcel->getActiveSheet()->SetCellValue('J'.(6+$i), date("d-m-Y", strtotime($data[$i]->do_delivered_date)));
            }
            
            $styleArray = array(
				  'borders' => array(
				    'allborders' => array(
				      'style' => PHPExcel_Style_Border::BORDER_THIN
				    )
				  )
				);
            
            $objPHPExcel->getActiveSheet()->getStyle('A4:J'.(5+$i))->applyFromArray($styleArray);
            $objPHPExcel->getActiveSheet()->getStyle('A4:J'.(5+$i))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
            $objPHPExcel->getActiveSheet()->getStyle('A4:J'.(5+$i))->getAlignment()->setWrapText(true);
            
            // Rename sheet
            $objPHPExcel->getActiveSheet()->setTitle('Data SO History');
	
            // Save Excel
            $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
            @mkdir(REPORT_PATH, DIR_WRITE_MODE); //REPORT_PATH -> config->constant
            $filecreatedname = "Data_SO_History_".date('YmdHis') . ".xls";
            $objWriter->save(REPORT_PATH.$filecreatedname);	
            $output = '{"success":true,"errMsg":"","filename":"' .base_url() . "assets/media/report/" . $filecreatedname.'"}';
            echo $output;
            return;
        }
        
        function driver_history()
        {
            /** PHPExcel */
            include 'class/PHPExcel.php';		
            /** PHPExcel_Writer_Excel2007 */
            include 'class/PHPExcel/Writer/Excel2007.php';		
            // Create new PHPExcel object
            $objPHPExcel = new PHPExcel();
            
            $field = isset($_POST['field']) ? $_POST['field'] : "";
            $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
            $offset = isset($_POST['offset']) ? $_POST['offset'] : 0;		
            $driver = isset($_POST['driver']) ? $_POST['driver'] : "";
            $vehicle = isset($_POST['vehicle']) ? $_POST['vehicle'] : "";
            $sdate = isset($_POST['sdate']) ? $_POST['sdate'] : "";
            $edate = isset($_POST['edate']) ? $_POST['edate'] : "";
		
            $this->dbtransporter = $this->load->database('transporter', true);
            $dosj_company = $this->sess->user_company;
		
            if (!$dosj_company)
            {
                redirect(base_url());
            }
		
            switch($field)
            {
                case "dosj_no":
                    $this->dbtransporter->where("do_delivered_do_number LIKE '%".$keyword."%'", null);
		break;
		case "driver":
                    $this->dbtransporter->where("do_delivered_driver", $driver);
		break;
		case "vehicle":
                    $this->dbtransporter->where("do_delivered_vehicle", $vehicle);
		break;
            }
		
            if ($sdate != "" && $edate != "")
            {
                $fm_sdate = date("Y-m-d", strtotime($sdate));
                $fm_edate = date("Y-m-d", strtotime($edate));
                $this->dbtransporter->where("do_delivered_date >=", $fm_sdate);
                $this->dbtransporter->where("do_delivered_date <=", $fm_edate);
            }
		
            $this->dbtransporter->where("do_delivered_company", $dosj_company);
            $this->dbtransporter->order_by("do_delivered_id","desc");
            $this->dbtransporter->order_by("do_delivered_do_number","asc");
            $this->dbtransporter->join("driver","driver_id = do_delivered_driver", "left");
            $this->dbtransporter->join("dosj","dosj_no = do_delivered_do_number", "left");
            $this->dbtransporter->join("cost","cost_id = do_delivered_cost", "left");
            $q = $this->dbtransporter->get("dosj_delivered");
            $data = $q->result();
		
            switch($field)
            {
                case "dosj_no":
                    $this->dbtransporter->where("do_delivered_do_number LIKE '%".$keyword."%'", null);
		break;
		case "driver":
                    $this->dbtransporter->where("do_delivered_driver", $driver);
		break;
		case "vehicle":
                    $this->dbtransporter->where("do_delivered_vehicle", $vehicle);
		break;
            }
		
            if ($sdate != "" && $edate != "")
            {
                $fm_sdate = date("Y-m-d", strtotime($sdate));
                $fm_edate = date("Y-m-d", strtotime($edate));
                $this->dbtransporter->where("do_delivered_date >=", $fm_sdate);
                $this->dbtransporter->where("do_delivered_date <=", $fm_edate);
            }
		
            $this->dbtransporter->where("do_delivered_company", $dosj_company);
            $qtotal = $this->dbtransporter->get("dosj_delivered");
            $rowstotal = $qtotal->result();
		
            $total = count($rowstotal);
            
            //Get Vehicle
            $vehicle = $this->get_vehicle();
		
            //Get Customer
            $customer = $this->get_customer();
                
            // Set properties
            $objPHPExcel->getProperties()->setCreator("lacak-mobil.com");
            $objPHPExcel->getProperties()->setLastModifiedBy("lacak-mobil.com");
            $objPHPExcel->getProperties()->setTitle("Data Driver History");
            $objPHPExcel->getProperties()->setSubject("Data Driver History");
            $objPHPExcel->getProperties()->setDescription("Data Driver History");
            
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
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
            
            //Header
            $objPHPExcel->setActiveSheetIndex(0);
            $objPHPExcel->getActiveSheet()->mergeCells('A1:J1');
            $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Data Driver History');
            $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
            
            $objPHPExcel->getActiveSheet()->SetCellValue('A5', 'No');
            $objPHPExcel->getActiveSheet()->SetCellValue('B5', 'Driver');
            $objPHPExcel->getActiveSheet()->SetCellValue('C5', 'Vehicle');
            $objPHPExcel->getActiveSheet()->SetCellValue('D5', 'Customer');
            $objPHPExcel->getActiveSheet()->SetCellValue('E5', 'Item');
            $objPHPExcel->getActiveSheet()->SetCellValue('F5', 'SO Type');
            $objPHPExcel->getActiveSheet()->SetCellValue('G5', 'SO');
            $objPHPExcel->getActiveSheet()->SetCellValue('H5', 'Quantity');
            $objPHPExcel->getActiveSheet()->SetCellValue('I5', 'Cost');
            $objPHPExcel->getActiveSheet()->SetCellValue('J5', 'Date');
            
            $objPHPExcel->getActiveSheet()->getStyle('A5:J5')->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle('A5:J5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            
            $i=1;
            for($i=0; $i < count($data); $i++)
            {
                $objPHPExcel->getActiveSheet()->SetCellValue('A'.(6+$i), $i+1);
                $objPHPExcel->getActiveSheet()->SetCellValue('B'.(6+$i), $data[$i]->driver_name);
                if (isset($vehicle))
                {
                    if (count($vehicle)>0)
                    {
                        for ($y=0;$y<count($vehicle);$y++)
                        {
                            if ($vehicle[$y]->vehicle_device == $data[$i]->do_delivered_vehicle)
                            {
                                $a = $vehicle[$y]->vehicle_name." ".$vehicle[$y]->vehicle_no;
                            }
                        }
                    }
                    else
                    {
                        $a = "-";
                    }
                }
                else
                {
                    $a = "-";
                }
                
                $objPHPExcel->getActiveSheet()->SetCellValue('C'.(6+$i), $a);
                
                if (isset($customer) && $data[$i]->dosj_customer_tmp == "")
                {
                    if (count($customer)>0)
                    {
                        for ($y=0;$y<count($customer);$y++)
                        {
                            if ($customer[$y]->group_id == $data[$i]->dosj_customer_id)
                            {
                                $a = $customer[$y]->group_name;
                            }
                        }
                    }
                }
		else 
                {
                    $a = $data[$i]->dosj_customer_tmp;
		}
                $objPHPExcel->getActiveSheet()->SetCellValue('D'.(6+$i), $a);
                
                $objPHPExcel->getActiveSheet()->SetCellValue('E'.(6+$i), $data[$i]->dosj_item_desc." ".$data[$i]->dosj_item_size);
                $objPHPExcel->getActiveSheet()->SetCellValue('F'.(6+$i), $data[$i]->do_delivered_do_type);
                $objPHPExcel->getActiveSheet()->SetCellValue('G'.(6+$i), $data[$i]->do_delivered_do_number);
                $objPHPExcel->getActiveSheet()->SetCellValue('H'.(6+$i), $data[$i]->do_delivered_quantity);
                $objPHPExcel->getActiveSheet()->SetCellValue('I'.(6+$i), "Rp."." ".number_format($data[$i]->cost));
                $objPHPExcel->getActiveSheet()->SetCellValue('J'.(6+$i), date("d-m-Y", strtotime($data[$i]->do_delivered_date)));
            }
            
            $styleArray = array(
				  'borders' => array(
				    'allborders' => array(
				      'style' => PHPExcel_Style_Border::BORDER_THIN
				    )
				  )
				);
            
            $objPHPExcel->getActiveSheet()->getStyle('A5:J'.(5+$i))->applyFromArray($styleArray);
            $objPHPExcel->getActiveSheet()->getStyle('A5:J'.(5+$i))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
            $objPHPExcel->getActiveSheet()->getStyle('A5:J'.(5+$i))->getAlignment()->setWrapText(true);
            
            // Rename sheet
            $objPHPExcel->getActiveSheet()->setTitle('Data Driver History');
	
            // Save Excel
            $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
            @mkdir(REPORT_PATH, DIR_WRITE_MODE); //REPORT_PATH -> config->constant
            $filecreatedname = "Data_Driver_History_".date('YmdHis') . ".xls";
            $objWriter->save(REPORT_PATH.$filecreatedname);	
            $output = '{"success":true,"errMsg":"","filename":"' .base_url() . "assets/media/report/" . $filecreatedname.'"}';
            echo $output;
            return;
        }
        
        function get_customer()
	{
		$driver_company = $this->sess->user_company;
		$nodata = 0;
		
		$this->db->where('group_status', 1);
		$this->db->where('group_company', $driver_company);
		$q_cust = $this->db->get('group');
		$rows_cust = $q_cust->result();
		
		if (count($rows_cust)>0)
		{
			return $rows_cust;
		}
		else
		{
			return $nodata;
		}
	}
	
	function get_vehicle()
	{
            $user_id = $this->sess->user_id;
            $user_company = $this->sess->user_company;
            $user_group = $this->sess->user_group;
		
            $this->db->order_by("vehicle_no", "asc");
            $this->db->where("vehicle_status <>", 3);
            $this->db->where("vehicle_user_id", $user_id);
            if (isset($user_company) || isset($user_group))
            {
                if ($user_company > 0)
		{
                    $this->db->or_where('vehicle_company', $user_company);
                }
		if ($user_group > 0)
                {
                    $this->db->or_where('vehicle_group', $user_group);
                }
            }
            $this->db->where("vehicle_active_date2 >=", date("Ymd"));
            //$this->db->join("user", "vehicle_user_id = user_id", "left outer");
            $qv = $this->db->get("vehicle");
            $rv = $qv->result();
            return $rv;
	}
	
}

/* End of file driver.php */
/* Location: ./system/application/controllers/transporter/driver.php */