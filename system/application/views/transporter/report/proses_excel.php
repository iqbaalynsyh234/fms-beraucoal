<?php
		/** PHPExcel */
		include APPPATH.'controllers/class/PHPExcel.php';
			
		/** PHPExcel_Writer_Excel2007 */
		include APPPATH.'controllers/class/PHPExcel/Writer/Excel2007.php';
			
		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();
			
		// Set properties
		$objPHPExcel->getProperties()->setCreator("lacak-mobil.com");
		$objPHPExcel->getProperties()->setLastModifiedBy("lacak-mobil.com");
		$objPHPExcel->getProperties()->setTitle("Trip Mileage Detail Report");
		$objPHPExcel->getProperties()->setSubject("Trip Mileage Detail Report Lacak-mobil.com");
		$objPHPExcel->getProperties()->setDescription("Trip Mileage Detail Repor Lacak-mobil.com");
			
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
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(5);			
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(12);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(12);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(12);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(12);
		$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(12);
		$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(35);
		$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(35);
		//Header
		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->getActiveSheet()->mergeCells('A1:K1');
		$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Trip Mileage Report');
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->SetCellValue('B3', 'Date :');
		$objPHPExcel->getActiveSheet()->getStyle('B3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('B3')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->SetCellValue('C3', $startdate." "."-"." ".$enddate);
		$objPHPExcel->getActiveSheet()->SetCellValue('J3', "Total Record :");
		$objPHPExcel->getActiveSheet()->getStyle('J3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('J3')->getFont()->setBold(true);
		
		//Top Header
		$objPHPExcel->getActiveSheet()->SetCellValue('A5', 'No');
		$objPHPExcel->getActiveSheet()->SetCellValue('B5', 'Vehicle No');
		$objPHPExcel->getActiveSheet()->SetCellValue('C5', 'Vehicle Name');
		$objPHPExcel->getActiveSheet()->SetCellValue('D5', 'Trip No');
		$objPHPExcel->getActiveSheet()->SetCellValue('E5', 'Start Time');
		$objPHPExcel->getActiveSheet()->SetCellValue('F5', 'End Time');
		$objPHPExcel->getActiveSheet()->SetCellValue('G5', 'Duration');
		$objPHPExcel->getActiveSheet()->SetCellValue('H5', 'Trip Mileage');
		$objPHPExcel->getActiveSheet()->SetCellValue('I5', 'Cumulative Mileage');
		$objPHPExcel->getActiveSheet()->SetCellValue('J5', 'Location Start');
		$objPHPExcel->getActiveSheet()->SetCellValue('K5', 'Location End');
		
		$objPHPExcel->getActiveSheet()->getStyle('A5:K5')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A5:K5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		$i=1;
		for($j=0;$j<count($data);$j++)
		{
			$objPHPExcel->getActiveSheet()->SetCellValue('A'.(5+$i), $i);
			$objPHPExcel->getActiveSheet()->getStyle('A'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->SetCellValue('B'.(5+$i), $data[$j]->trip_mileage_vehicle_no);
			$objPHPExcel->getActiveSheet()->SetCellValue('C'.(5+$i), $data[$j]->trip_mileage_vehicle_name);
			$objPHPExcel->getActiveSheet()->SetCellValue('D'.(5+$i), $data[$j]->trip_mileage_trip_no);
			$objPHPExcel->getActiveSheet()->getStyle('D'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->SetCellValue('E'.(5+$i), $data[$j]->trip_mileage_start_time);						
			$objPHPExcel->getActiveSheet()->SetCellValue('F'.(5+$i), $data[$j]->trip_mileage_end_time);
			$objPHPExcel->getActiveSheet()->SetCellValue('G'.(5+$i), $data[$j]->trip_mileage_duration);
			$objPHPExcel->getActiveSheet()->getStyle('G'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->SetCellValue('H'.(5+$i), $data[$j]->trip_mileage_trip_mileage." "."KM");
			$objPHPExcel->getActiveSheet()->getStyle('H'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->SetCellValue('I'.(5+$i), $data[$j]->trip_mileage_cummulative_mileage." "."KM");
			$objPHPExcel->getActiveSheet()->getStyle('I'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->SetCellValue('J'.(5+$i), $data[$j]->trip_mileage_location_start);
			$objPHPExcel->getActiveSheet()->SetCellValue('K'.(5+$i), $data[$j]->trip_mileage_location_end);
			$objPHPExcel->getActiveSheet()->getStyle('J'.(5+$i))->getFont()->setSize(8);
			$objPHPExcel->getActiveSheet()->getStyle('K'.(5+$i))->getFont()->setSize(8);
			$i++;
		}
		
		$objPHPExcel->getActiveSheet()->SetCellValue('K3', $i-1);
		$objPHPExcel->getActiveSheet()->getStyle('K3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			
		$styleArray = array(
				'borders' => array(
				'allborders' => array(
				'style' => PHPExcel_Style_Border::BORDER_THIN
				    )
				  )
				);
			
		$objPHPExcel->getActiveSheet()->getStyle('A5:K'.(4+$i))->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->getStyle('A5:K'.(4+$i))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
		$objPHPExcel->getActiveSheet()->getStyle('A5:K'.(4+$i))->getAlignment()->setWrapText(true);
		// Rename sheet
		$objPHPExcel->getActiveSheet()->setTitle('trip_mileage');
			
		// Save Excel
		$filedate = date("Ymd",strtotime($startdate))."_".date("Ymd",strtotime($enddate));
		$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
		@mkdir(REPORT_PATH, DIR_WRITE_MODE); //REPORT_PATH -> config->constant
		$filecreatedname = "Trip_Mileage_".$filedate . ".xls";
			
		$objWriter->save('php://output');
?>