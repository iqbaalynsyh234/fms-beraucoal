<?php
include "base.php";

class Autoreport extends Base {

	function Autoreport()
	{
		parent::Base();	
		$this->load->model("gpsmodel");
        $this->load->model("vehiclemodel");
        $this->load->model("configmodel");
        $this->load->helper('common_helper');
        $this->load->helper('kopindosat');
        $this->load->model("historymodel");
	}
    
    function trip_mileage($userid="", $name = "", $host = "", $startdate = "", $enddate = "")
    {
        printf("PROSES AUTO REPORT TRIP MILEAGE >> START \r\n");
        $startproses = date("Y-m-d H:i:s");
        
        include 'class/PHPExcel.php';
        include 'class/PHPExcel/Writer/Excel2007.php';
        
        $report_type = "trip_mileage";
        $process_date = date("Y-m-d H:i:s");
        $domain_server = "http://202.129.190.194/";
		$report = "tripmileage_";
        //$userlist = array(775, 631, 981, 1059, 834, 778, 1222, 1027, 1246, 
        //1332, 1298, 986, 651, 645, 736, 666, 866, 627, 1386, 1110, 1148, 1122,
        //703, 1147, 1168);
        
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
		
		switch ($month)
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
        
        $sdate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($startdate)));
        $edate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($enddate)));
        $z =0;
		
        if ($name != "" && $host != "")
        {
            $dev = $name."@".$host;
            $this->db->where("vehicle_device", $dev);    
        } 
        
		if ($userid != "")
		{
			$this->db->where("user_id",$userid);
		}
       
        $this->db->join("user", "vehicle_user_id = user_id", "left outer");
        
		if ($userid == "")
		{
			$this->db->where("user_company >",0);
		}
		
		$this->db->where("vehicle_status <>", 3);
        $q = $this->db->get("vehicle");
        $rowvehicle = $q->result();
        
        $total_process = count($rowvehicle);
        printf("TOTAL PROSES VEHICLE : %s \r\n",$total_process);
        printf("============================================ \r\n");
        
        for($x=0;$x<count($rowvehicle);$x++)
        {
            printf("PROSES VEHICLE : %s (%d/%d) \r\n",$rowvehicle[$x]->vehicle_device, ++$z, $total_process);
            unset($data_insert);
            //Websocket Only
            if (isset($rowvehicle[$x]->vehicle_info))
            {
                $json = json_decode($rowvehicle[$x]->vehicle_info);
                if (isset($json->vehicle_ws))
                {
					$istbl_history = $this->config->item("dbhistory_default");
					if($this->config->item("is_dbhistory") == 1)
					{
						$istbl_history = $rowvehicle[$x]->vehicle_dbhistory_name;
					}
					$this->dbhist = $this->load->database($istbl_history, TRUE);
                    $this->dbtrans = $this->load->database("transporter",true); 
					$this->dbhist2 = $this->load->database("gpshistory2",true);						
                    
                    
                    //cek apakah sudah pernah ada filenya
                    $this->dbtrans->select("autoreport_vehicle_id");
                    $this->dbtrans->where("autoreport_vehicle_id",$rowvehicle[$x]->vehicle_id);
                    $this->dbtrans->where("autoreport_data_startdate",$startdate);
                    $this->dbtrans->limit(1);
                    $qrpt = $this->dbtrans->get("autoreport_tripmileage");
                    $rrpt = $qrpt->row();
                    
                    if (count($rrpt)>0)
                    {
                        printf("VEHICLE %s SUDAH PERNAH DI PROSES, HAPUS DULU DATA SEBELUMNYA \r\n", $rowvehicle[$x]->vehicle_device);     
                        printf("------------------------------------------------------------- \r\n");
                    }
                    else
                    {
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
                        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(16);
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
                        $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'TRIP MILIAGE REPORT');
                        $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
                        $objPHPExcel->getActiveSheet()->getStyle('B3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                        $objPHPExcel->getActiveSheet()->getStyle('B3')->getFont()->setBold(true);
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
            
                        $vehicle_device = explode("@", $rowvehicle[$x]->vehicle_device);  
                        $vehicle_no = $rowvehicle[$x]->vehicle_no;
						$vehicle_dev = $rowvehicle[$x]->vehicle_device;
						$vehicle_name = $rowvehicle[$x]->vehicle_name;
                
                        $ex_vno = explode("/",$vehicle_no);

                        if ($rowvehicle[$x]->vehicle_type == "T5" || $rowvehicle[$x]->vehicle_type == "T5 PULSE")
                        {
                            $table = $vehicle_device[0]."@t5_gps";
                            $tableinfo = $vehicle_device[0]."@t5_info";    
                        }
                        else
                        {
                            $table = strtolower($vehicle_device[0])."@".strtolower($vehicle_device[1])."_gps";
                            $tableinfo = strtolower($vehicle_device[0])."@".strtolower($vehicle_device[1])."_info";
                        }
                        
                        //$report_path = "/home/lacakmobil/public_html/transporter.lacak-mobil.com/public/assets/media/autoreport/".$report_type."/".$vehicle_device[0]."/";
                        $report_path = "/home/lacakmobil/public_html/transporter.lacak-mobil.com/public/assets/media/autoreport/".$report_type."/".$vehicle_device[0]."/";
						$pub_path = "assets/media/autoreport/".$report_type."/".$vehicle_device[0]."/";
            
                        $this->dbhist->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");    
                        $this->dbhist->where("gps_info_device", $rowvehicle[$x]->vehicle_device);
                        $this->dbhist->where("gps_time >=", $sdate);
                        $this->dbhist->where("gps_time <=", $edate);    
                        $this->dbhist->order_by("gps_time","asc");
                        $this->dbhist->from($table);
                        $q = $this->dbhist->get();
                        $rows1 = $q->result();
                        //print_r($rows);exit;
						
						
						$this->dbhist2->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");    
                        $this->dbhist2->where("gps_info_device", $rowvehicle[$x]->vehicle_device);
                        $this->dbhist2->where("gps_time >=", $sdate);
                        $this->dbhist2->where("gps_time <=", $edate);    
                        $this->dbhist2->order_by("gps_time","asc");
                        $this->dbhist2->from($table);
                        $q2 = $this->dbhist2->get();
                        $rows2 = $q2->result();
						
						$rows = array_merge($rows1, $rows2);
                
                        $data = array();
                        $nopol = "";
                        $on = false;
                        $trows = count($rows);
        
                        printf("TOTAL DATA : %s \r\n",$trows);
            
                        for($i=0;$i<$trows;$i++)
                        {
                            //if($rows[$i]->gps_speed == 0) continue;
                            if($nopol != $rowvehicle[$x]->vehicle_no)
                            { //new vehicle
                                if($on && $i!=0)
                                { 
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i-1]->gps_longitude, $rows[$i-1]->gps_ew, $rows[$i-1]->gps_latitude, $rows[$i-1]->gps_ns);
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i-1]->gps_info_distance;
                                }
                        
                                if(substr($rows[$i]->gps_info_io_port, 4, 1) == 1)
                                {                                
                                    $trip_no = 1;                    
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no++]['start_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_mileage'] = $rows[$i]->gps_info_distance;
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['vehicle_name'] = $rowvehicle[$x]->vehicle_name;
                                    $on = true;
                            
                                    if($i==$trows-1)
                                    {                                                
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
                                    }
                                }
                                else
                                {
                                    $trip_no = 1;
                                    $on = false;
                                }
                            }
                            else
                            { //same vehicle
                                if(substr($rows[$i]->gps_info_io_port, 4, 1) == 1)
                                {
                                    if(!$on)
                                    {    
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no++]['start_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_mileage'] = $rows[$i]->gps_info_distance;
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['vehicle_name'] = $rowvehicle[$x]->vehicle_name;
                                    }
                                    $on = true;    
                                    if($i==$trows-1 && $on)
                                    {                                                
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
                                    }                                    
                                }
                                else
                                {            
                                    if($on)
                                    {
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));    
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
                                    }
                                    $on = false;
                                }
                            }
                            $nopol = $rowvehicle[$x]->vehicle_no;
                        }
                        
                        //Write Data
                        $i=1;
                        $new = "";
                        printf("WRITE DATA EXCEL : ");
                        foreach($data as $vehicle_no=>$val)
                        {
                            if($new != $vehicle_no)
                            {
                                $cumm = 0;
                                $trip_no = 1;
                            }
                    
                            foreach($val as $no=>$report)
                            {
                                $mileage = $report['end_mileage']- $report['start_mileage'];
                                if($mileage != 0)
                                {
                                    $duration = get_time_difference($report['start_time'], $report['end_time']);
                                    $show = "";
                                    if($duration[0]!=0)
                                    {
                                        $show .= $duration[0] ." Day ";
                                    }
                                    if($duration[1]!=0)
                                    {
                                        $show .= $duration[1] ." Hour ";
                                    }
                                    if($duration[2]!=0)
                                    {
                                        $show .= $duration[2] ." Min ";
                                    }
                                    if($show == "")
                                    {
                                        $show .= "0 Min";
                                    }
                                    $tm = round(($mileage/1000),2);
                                    $cumm += $tm;
                                    printf("|%s|",$i);
									
									$notrip = $trip_no++;
                                    
                                    $objPHPExcel->getActiveSheet()->SetCellValue('A'.(5+$i), $i);
                                    $objPHPExcel->getActiveSheet()->getStyle('A'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                    $objPHPExcel->getActiveSheet()->SetCellValue('B'.(5+$i), $vehicle_no);
                                    $objPHPExcel->getActiveSheet()->SetCellValue('C'.(5+$i), $report['vehicle_name']);
                                    $objPHPExcel->getActiveSheet()->SetCellValue('D'.(5+$i), $notrip);
                                    $objPHPExcel->getActiveSheet()->getStyle('D'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                    $objPHPExcel->getActiveSheet()->SetCellValue('E'.(5+$i), $report['start_time']);                        
                                    $objPHPExcel->getActiveSheet()->SetCellValue('F'.(5+$i), $report['end_time']);
                                    $objPHPExcel->getActiveSheet()->SetCellValue('G'.(5+$i), $show);
                                    $objPHPExcel->getActiveSheet()->getStyle('G'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            
                                    //mileage
                                    $xme = $tm;
                                    $xxme = explode(".",$xme);
                                    if (isset($xxme[1]))
                                    {
                                        $xsub = substr($xxme[1],0,2); 
                                        $x_mile = $xxme[0].".".$xsub;     
                                    }
                                    else
                                    {
                                        $x_mile = $xxme[0];
                                    }
                            
                                    //cummulative
                                    $xcum = $cumm;
                                    $xxcum = explode(".",$xcum);
                                    if (isset($xxcum[1]))
                                    {
                                        $xcumsub = substr($xxcum[1],0,2); 
                                        $x_cum = $xxcum[0].".".$xcumsub;     
                                    }
                                    else
                                    {
                                        $x_cum = $xxcum[0];
                                    }
                            
                                    $objPHPExcel->getActiveSheet()->SetCellValue('H'.(5+$i), $x_mile . " km");
                                    $objPHPExcel->getActiveSheet()->getStyle('H'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                                    $objPHPExcel->getActiveSheet()->SetCellValue('I'.(5+$i), $x_cum . " km");
                                    $objPHPExcel->getActiveSheet()->getStyle('I'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                                    $objPHPExcel->getActiveSheet()->SetCellValue('J'.(5+$i), $report['start_position']->display_name);
                                    $objPHPExcel->getActiveSheet()->SetCellValue('K'.(5+$i), $report['end_position']->display_name);
                                    $objPHPExcel->getActiveSheet()->getStyle('J'.(5+$i))->getFont()->setSize(8);
                                    $objPHPExcel->getActiveSheet()->getStyle('K'.(5+$i))->getFont()->setSize(8);
									
									unset($datainsert);
									$datainsert["trip_mileage_vehicle_id"] = $vehicle_dev;
									$datainsert["trip_mileage_vehicle_no"] = $vehicle_no;
									$datainsert["trip_mileage_vehicle_name"] = $report['vehicle_name'];
									$datainsert["trip_mileage_trip_no"] = $notrip;
									$datainsert["trip_mileage_start_time"] = date("Y-m-d H:i:s", strtotime($report['start_time']));
									$datainsert["trip_mileage_end_time"] = date("Y-m-d H:i:s", strtotime($report['end_time']));
									$datainsert["trip_mileage_duration"] = $show;
									$datainsert["trip_mileage_trip_mileage"] = $x_mile;
									$datainsert["trip_mileage_cummulative_mileage"] = $x_cum;
									$datainsert["trip_mileage_location_start"] = $report['start_position']->display_name;
									$datainsert["trip_mileage_location_end"] = $report['end_position']->display_name;
									
									$this->dbtrip = $this->load->database("tripmileage",TRUE);
									$this->dbtrip->insert($dbtable,$datainsert);
									
                                    $i++;
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
                
                        $objPHPExcel->getActiveSheet()->getStyle('A5:K'.(4+$i))->applyFromArray($styleArray);
                        $objPHPExcel->getActiveSheet()->getStyle('A5:K'.(4+$i))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
                        $objPHPExcel->getActiveSheet()->getStyle('A5:K'.(4+$i))->getAlignment()->setWrapText(true);
                
                        // Rename sheet
                        $objPHPExcel->getActiveSheet()->setTitle('trip_mileage');
                        printf("CREATE FILE FOR : %s \r\n",$rowvehicle[$x]->vehicle_device);
            
                        // Save Excel
                        $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
                        @mkdir($report_path, DIR_WRITE_MODE); //REPORT_PATH -> config->constant
                
                        if (isset($ex_vno))
                        {
                            if (isset($ex_vno[1]))
                            {
                                $filecreatedname = "TripMileage_".$ex_vno[0].$ex_vno[1]."_".$datefilename.".xls";    
                            }
                            else
                            {
                                $filecreatedname = "TripMileage_".$ex_vno[0]."_".$datefilename.".xls";       
                            }
                        }
                        else
                        {
                            $filecreatedname = "TripMileage_".$vehicle_no."_".$datefilename.".xls";  
                        }
                        
                        $objWriter->save($report_path.$filecreatedname);
                        printf("CREATE FILE DONE : %s \r\n",$filecreatedname);
                        $public_path = $domain_server.$pub_path.$filecreatedname;
						
                        printf("INSERT TO DATABASE TRANSPORTER \r\n");
                        $data_insert["autoreport_vehicle_id"] = $rowvehicle[$x]->vehicle_id;
                        $data_insert["autoreport_vehicle_device"] = $rowvehicle[$x]->vehicle_device;
                        $data_insert["autoreport_user_id"] = $rowvehicle[$x]->user_id;
                        $data_insert["autoreport_company"] = $rowvehicle[$x]->user_company;
                        $data_insert["autoreport_data_startdate"] = $startdate;
                        $data_insert["autoreport_data_enddate"] = $enddate;
                        $data_insert["autoreport_type"] = $report_type;
                        $data_insert["autoreport_file_path"] = $report_path;
                        $data_insert["autoreport_filename"] = $filecreatedname;
                        $data_insert["autoreport_download_path"] = $report_path.$filecreatedname;
                        $data_insert["autoreport_public_path"] = $public_path;
                        $data_insert["autoreport_process_date"] = $process_date;
                        $data_insert["autoreport_insert_db"] = date("Y-m-d H:i:s");
            
                        $this->dbtrans->insert("autoreport_tripmileage",$data_insert);
                        printf("INSERT OK \r\n");
            
                        printf("DELETE CACHE HISTORY \r\n");
                        $this->dbhist->cache_delete_all();
                        $this->dbtrans->cache_delete_all();
                        unset($data);
            
                        printf("FINISH FOR VEHICLE : %s \r\n",$rowvehicle[$x]->vehicle_device);
                        printf("============================================ \r\n");
            
                        //$output = '{"success":true,"errMsg":"","filename":"' .base_url() . "assets/media/report/" . $filecreatedname.'"}';
                        //echo $output;
                        // return;    
                    }
                }
                else
                {
                    printf("SKIP VEHICLE ( NO VEHICLE WEB SOCKET ) \r\n");
                    printf("-------------------------------------- \r\n");    
                }    
            }
            else
            {
                printf("SKIP VEHICLE ( NO VEHICLE INFO ) \r\n");
                printf("-------------------------------- \r\n");
            }
        }
        
        unset($datalog);
        $this->dbtrans = $this->load->database("transporter",true);
        $datalog["cron_name"] = "Autoreport Trip Mileage";
        $datalog["cron_start_data"] = $startdate;
        $datalog["cron_end_data"] = $enddate;
        $datalog["cron_start_proses"] = $startproses;
        $datalog["cron_end_proses"] = date("Y-m-d H:i:s");
        $this->dbtrans->insert("log_cronjobs",$datalog);
        
		$finishtime = date("d-m-Y H:i:s");
        printf("AUTOREPORT TRIP MILEAGE DONE %s\r\n",$finishtime);
        
    } 
        
	function trip_mileage_port($userid="", $name = "", $host = "", $startdate = "", $enddate = "")
    {
        printf("PROSES AUTO REPORT TRIP MILEAGE KHUSUS PORT >> START \r\n");
        $startproses = date("Y-m-d H:i:s");
        
        include 'class/PHPExcel.php';
        include 'class/PHPExcel/Writer/Excel2007.php';
        
        $report_type = "trip_mileage";
        $process_date = date("Y-m-d H:i:s");
        $domain_server = "http://202.129.190.194/";
		$report = "tripmileage_";
        //$userlist = array(775, 631, 981, 1059, 834, 778, 1222, 1027, 1246, 
        //1332, 1298, 986, 651, 645, 736, 666, 866, 627, 1386, 1110, 1148, 1122,
        //703, 1147, 1168);
        
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
		
		switch ($month)
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
        
        $sdate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($startdate)));
        $edate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($enddate)));
        $z =0;
		
        if ($name != "" && $host != "")
        {
            $dev = $name."@".$host;
            $this->db->where("vehicle_device", $dev);    
        } 
        
		if ($userid != "")
		{
			$this->db->where("user_id",$userid);
		}
       
        $this->db->join("user", "vehicle_user_id = user_id", "left outer");
        
		if ($userid == "")
		{
			$this->db->where("user_company >",0);
		}
		
		$this->db->where("vehicle_status <>", 3);
		if ($userid == "" && $name == "" && $host == "") 
		{
			$this->db->where("vehicle_type","T5");
			$this->db->or_where("vehicle_type","T5SILVER");
			$this->db->or_where("vehicle_type","T5PULSE");
			$this->db->or_where("vehicle_type","T5DOOR");
			$this->db->or_where("vehicle_type","T5FAN");
			$this->db->or_where("vehicle_type","T5PTO");
		}
        $q = $this->db->get("vehicle");
        $rowvehicle = $q->result();
        
        $total_process = count($rowvehicle);
        printf("TOTAL PROSES VEHICLE : %s \r\n",$total_process);
        printf("============================================ \r\n");
        
		$this->dbtrans = $this->load->database("transporter",true); 
        for($x=0;$x<count($rowvehicle);$x++)
        {
            printf("PROSES VEHICLE : %s (%d/%d) \r\n",$rowvehicle[$x]->vehicle_device, ++$z, $total_process);
            unset($data_insert);
            //PORT Only
            if (isset($rowvehicle[$x]->vehicle_info))
            {
                $json = json_decode($rowvehicle[$x]->vehicle_info);
                if (isset($json->vehicle_ip) && isset($json->vehicle_port))
                {
                    
					$databases = $this->config->item('databases');
					if (isset($databases[$json->vehicle_ip][$json->vehicle_port]))
					{
						$database = $databases[$json->vehicle_ip][$json->vehicle_port];
						$table = $this->config->item("external_gpstable");
						$tableinfo = $this->config->item("external_gpsinfotable");				
						$this->dbhist = $this->load->database($database, TRUE);
						
						$istbl_history = $this->config->item("dbhistory_default");
						if($this->config->item("is_dbhistory") == 1)
						{
							$istbl_history = $rowvehicle[$x]->vehicle_dbhistory_name;
						}
						$this->dbhist2 = $this->load->database($istbl_history, TRUE);
					}
					else
					{
						$table = $this->gpsmodel->getGPSTable($rowvehicle[$x]->vehicle_type);
						$tableinfo = $this->gpsmodel->getGPSInfoTable($rowvehicle[$x]->vehicle_type);
						$this->dbhist = $this->load->database("default", TRUE);
						$istbl_history = $this->config->item("dbhistory_default");
						if($this->config->item("is_dbhistory") == 1)
						{
							$istbl_history = $rowvehicle[$x]->vehicle_dbhistory_name;
						}
						$this->dbhist2 = $this->load->database($istbl_history, TRUE);
					}
					
					$vehicle_device = explode("@", $rowvehicle[$x]->vehicle_device);  
                    $vehicle_no = $rowvehicle[$x]->vehicle_no;
					$vehicle_dev = $rowvehicle[$x]->vehicle_device;
					$vehicle_name = $rowvehicle[$x]->vehicle_name;
						
					if ($rowvehicle[$x]->vehicle_type == "T5" || $rowvehicle[$x]->vehicle_type == "T5 PULSE")
                    {
                        $tablehist = $vehicle_device[0]."@t5_gps";
                        $tablehistinfo = $vehicle_device[0]."@t5_info";    
                    }
                    else
                    {
						$tablehist = strtolower($vehicle_device[0])."@".strtolower($vehicle_device[1])."_gps";
						$tablehistinfo = strtolower($vehicle_device[0])."@".strtolower($vehicle_device[1])."_info";
                    }
					
                    //cek apakah sudah pernah ada filenya
                    $this->dbtrans->select("autoreport_vehicle_id");
                    $this->dbtrans->where("autoreport_vehicle_id",$rowvehicle[$x]->vehicle_id);
                    $this->dbtrans->where("autoreport_data_startdate",$startdate);
                    $this->dbtrans->limit(1);
                    $qrpt = $this->dbtrans->get("autoreport_tripmileage");
                    $rrpt = $qrpt->row();
                    
                    if (count($rrpt)>0)
                    {
                        printf("VEHICLE %s SUDAH PERNAH DI PROSES, HAPUS DULU DATA SEBELUMNYA \r\n", $rowvehicle[$x]->vehicle_device);     
                        printf("------------------------------------------------------------- \r\n");
                    }
                    else
                    {
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
                        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(16);
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
                        $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'TRIP MILIAGE REPORT');
                        $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
                        $objPHPExcel->getActiveSheet()->getStyle('B3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                        $objPHPExcel->getActiveSheet()->getStyle('B3')->getFont()->setBold(true);
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
            
                
                        $ex_vno = explode("/",$vehicle_no);

                        
                        //$report_path = "/home/lacakmobil/public_html/transporter.lacak-mobil.com/public/assets/media/autoreport/".$report_type."/".$vehicle_device[0]."/";
                        $report_path = "/home/lacakmobil/public_html/transporter.lacak-mobil.com/public/assets/media/autoreport/".$report_type."/".$vehicle_device[0]."/";
						$pub_path = "assets/media/autoreport/".$report_type."/".$vehicle_device[0]."/";
            
                        $this->dbhist->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");    
                        $this->dbhist->where("gps_info_device", $rowvehicle[$x]->vehicle_device);
                        $this->dbhist->where("gps_time >=", $sdate);
                        $this->dbhist->where("gps_time <=", $edate);    
                        $this->dbhist->order_by("gps_time","asc");
                        $this->dbhist->from($table);
                        $q = $this->dbhist->get();
                        $rows1 = $q->result();
                        //print_r($rows);exit;
						
						
						$this->dbhist2->join($tablehistinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");    
                        $this->dbhist2->where("gps_info_device", $rowvehicle[$x]->vehicle_device);
                        $this->dbhist2->where("gps_time >=", $sdate);
                        $this->dbhist2->where("gps_time <=", $edate);    
                        $this->dbhist2->order_by("gps_time","asc");
                        $this->dbhist2->from($tablehist);
                        $q2 = $this->dbhist2->get();
                        $rows2 = $q2->result();
						
						$rows = array_merge($rows1, $rows2);
                
                        $data = array();
                        $nopol = "";
                        $on = false;
                        $trows = count($rows);
        
                        printf("TOTAL DATA : %s \r\n",$trows);
            
                        for($i=0;$i<$trows;$i++)
                        {
                            //if($rows[$i]->gps_speed == 0) continue;
                            if($nopol != $rowvehicle[$x]->vehicle_no)
                            { //new vehicle
                                if($on && $i!=0)
                                { 
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i-1]->gps_longitude, $rows[$i-1]->gps_ew, $rows[$i-1]->gps_latitude, $rows[$i-1]->gps_ns);
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i-1]->gps_info_distance;
                                }
                        
                                if(substr($rows[$i]->gps_info_io_port, 4, 1) == 1)
                                {                                
                                    $trip_no = 1;                    
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no++]['start_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_mileage'] = $rows[$i]->gps_info_distance;
                                    $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['vehicle_name'] = $rowvehicle[$x]->vehicle_name;
                                    $on = true;
                            
                                    if($i==$trows-1)
                                    {                                                
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
                                    }
                                }
                                else
                                {
                                    $trip_no = 1;
                                    $on = false;
                                }
                            }
                            else
                            { //same vehicle
                                if(substr($rows[$i]->gps_info_io_port, 4, 1) == 1)
                                {
                                    if(!$on)
                                    {    
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no++]['start_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_mileage'] = $rows[$i]->gps_info_distance;
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['vehicle_name'] = $rowvehicle[$x]->vehicle_name;
                                    }
                                    $on = true;    
                                    if($i==$trows-1 && $on)
                                    {                                                
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
                                    }                                    
                                }
                                else
                                {            
                                    if($on)
                                    {
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));    
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                        $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
                                    }
                                    $on = false;
                                }
                            }
                            $nopol = $rowvehicle[$x]->vehicle_no;
                        }
                        
                        //Write Data
                        $i=1;
                        $new = "";
                        printf("WRITE DATA EXCEL : ");
                        foreach($data as $vehicle_no=>$val)
                        {
                            if($new != $vehicle_no)
                            {
                                $cumm = 0;
                                $trip_no = 1;
                            }
                    
                            foreach($val as $no=>$report)
                            {
                                $mileage = $report['end_mileage']- $report['start_mileage'];
                                if($mileage != 0)
                                {
                                    $duration = get_time_difference($report['start_time'], $report['end_time']);
                                    $show = "";
                                    if($duration[0]!=0)
                                    {
                                        $show .= $duration[0] ." Day ";
                                    }
                                    if($duration[1]!=0)
                                    {
                                        $show .= $duration[1] ." Hour ";
                                    }
                                    if($duration[2]!=0)
                                    {
                                        $show .= $duration[2] ." Min ";
                                    }
                                    if($show == "")
                                    {
                                        $show .= "0 Min";
                                    }
                                    $tm = round(($mileage/1000),2);
                                    $cumm += $tm;
                                    printf("|%s|",$i);
									
									$notrip = $trip_no++;
                                    
                                    $objPHPExcel->getActiveSheet()->SetCellValue('A'.(5+$i), $i);
                                    $objPHPExcel->getActiveSheet()->getStyle('A'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                    $objPHPExcel->getActiveSheet()->SetCellValue('B'.(5+$i), $vehicle_no);
                                    $objPHPExcel->getActiveSheet()->SetCellValue('C'.(5+$i), $report['vehicle_name']);
                                    $objPHPExcel->getActiveSheet()->SetCellValue('D'.(5+$i), $notrip);
                                    $objPHPExcel->getActiveSheet()->getStyle('D'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                    $objPHPExcel->getActiveSheet()->SetCellValue('E'.(5+$i), $report['start_time']);                        
                                    $objPHPExcel->getActiveSheet()->SetCellValue('F'.(5+$i), $report['end_time']);
                                    $objPHPExcel->getActiveSheet()->SetCellValue('G'.(5+$i), $show);
                                    $objPHPExcel->getActiveSheet()->getStyle('G'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            
                                    //mileage
                                    $xme = $tm;
                                    $xxme = explode(".",$xme);
                                    if (isset($xxme[1]))
                                    {
                                        $xsub = substr($xxme[1],0,2); 
                                        $x_mile = $xxme[0].".".$xsub;     
                                    }
                                    else
                                    {
                                        $x_mile = $xxme[0];
                                    }
                            
                                    //cummulative
                                    $xcum = $cumm;
                                    $xxcum = explode(".",$xcum);
                                    if (isset($xxcum[1]))
                                    {
                                        $xcumsub = substr($xxcum[1],0,2); 
                                        $x_cum = $xxcum[0].".".$xcumsub;     
                                    }
                                    else
                                    {
                                        $x_cum = $xxcum[0];
                                    }
                            
                                    $objPHPExcel->getActiveSheet()->SetCellValue('H'.(5+$i), $x_mile . " km");
                                    $objPHPExcel->getActiveSheet()->getStyle('H'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                                    $objPHPExcel->getActiveSheet()->SetCellValue('I'.(5+$i), $x_cum . " km");
                                    $objPHPExcel->getActiveSheet()->getStyle('I'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                                    $objPHPExcel->getActiveSheet()->SetCellValue('J'.(5+$i), $report['start_position']->display_name);
                                    $objPHPExcel->getActiveSheet()->SetCellValue('K'.(5+$i), $report['end_position']->display_name);
                                    $objPHPExcel->getActiveSheet()->getStyle('J'.(5+$i))->getFont()->setSize(8);
                                    $objPHPExcel->getActiveSheet()->getStyle('K'.(5+$i))->getFont()->setSize(8);
									
									unset($datainsert);
									$datainsert["trip_mileage_vehicle_id"] = $vehicle_dev;
									$datainsert["trip_mileage_vehicle_no"] = $vehicle_no;
									$datainsert["trip_mileage_vehicle_name"] = $report['vehicle_name'];
									$datainsert["trip_mileage_trip_no"] = $notrip;
									$datainsert["trip_mileage_start_time"] = date("Y-m-d H:i:s", strtotime($report['start_time']));
									$datainsert["trip_mileage_end_time"] = date("Y-m-d H:i:s", strtotime($report['end_time']));
									$datainsert["trip_mileage_duration"] = $show;
									$datainsert["trip_mileage_trip_mileage"] = $x_mile;
									$datainsert["trip_mileage_cummulative_mileage"] = $x_cum;
									$datainsert["trip_mileage_location_start"] = $report['start_position']->display_name;
									$datainsert["trip_mileage_location_end"] = $report['end_position']->display_name;
									
									$this->dbtrip = $this->load->database("tripmileage",TRUE);
									$this->dbtrip->insert($dbtable,$datainsert);
									
                                    $i++;
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
                
                        $objPHPExcel->getActiveSheet()->getStyle('A5:K'.(4+$i))->applyFromArray($styleArray);
                        $objPHPExcel->getActiveSheet()->getStyle('A5:K'.(4+$i))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
                        $objPHPExcel->getActiveSheet()->getStyle('A5:K'.(4+$i))->getAlignment()->setWrapText(true);
                
                        // Rename sheet
                        $objPHPExcel->getActiveSheet()->setTitle('trip_mileage');
                        printf("CREATE FILE FOR : %s \r\n",$rowvehicle[$x]->vehicle_device);
            
                        // Save Excel
                        $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
                        @mkdir($report_path, DIR_WRITE_MODE); //REPORT_PATH -> config->constant
                
                        if (isset($ex_vno))
                        {
                            if (isset($ex_vno[1]))
                            {
                                $filecreatedname = "TripMileage_".$ex_vno[0].$ex_vno[1]."_".$datefilename.".xls";    
                            }
                            else
                            {
                                $filecreatedname = "TripMileage_".$ex_vno[0]."_".$datefilename.".xls";       
                            }
                        }
                        else
                        {
                            $filecreatedname = "TripMileage_".$vehicle_no."_".$datefilename.".xls";  
                        }
                        
                        $objWriter->save($report_path.$filecreatedname);
                        printf("CREATE FILE DONE : %s \r\n",$filecreatedname);
                        $public_path = $domain_server.$pub_path.$filecreatedname;
            
                        printf("INSERT TO DATABASE TRANSPORTER \r\n");
                        $data_insert["autoreport_vehicle_id"] = $rowvehicle[$x]->vehicle_id;
                        $data_insert["autoreport_vehicle_device"] = $rowvehicle[$x]->vehicle_device;
                        $data_insert["autoreport_user_id"] = $rowvehicle[$x]->user_id;
                        $data_insert["autoreport_company"] = $rowvehicle[$x]->user_company;
                        $data_insert["autoreport_data_startdate"] = $startdate;
                        $data_insert["autoreport_data_enddate"] = $enddate;
                        $data_insert["autoreport_type"] = $report_type;
                        $data_insert["autoreport_file_path"] = $report_path;
                        $data_insert["autoreport_filename"] = $filecreatedname;
                        $data_insert["autoreport_download_path"] = $report_path.$filecreatedname;
                        $data_insert["autoreport_public_path"] = $public_path;
                        $data_insert["autoreport_process_date"] = $process_date;
                        $data_insert["autoreport_insert_db"] = date("Y-m-d H:i:s");
            
                        $this->dbtrans->insert("autoreport_tripmileage",$data_insert);
                        printf("INSERT OK \r\n");
            
                        printf("DELETE CACHE HISTORY \r\n");
                        $this->dbhist->cache_delete_all();
                        $this->dbtrans->cache_delete_all();
                        unset($data);
            
                        printf("FINISH FOR VEHICLE : %s \r\n",$rowvehicle[$x]->vehicle_device);
                        printf("============================================ \r\n");
            
                        //$output = '{"success":true,"errMsg":"","filename":"' .base_url() . "assets/media/report/" . $filecreatedname.'"}';
                        //echo $output;
                        // return;    
                    }
                }
                else
                {
                    printf("SKIP VEHICLE ( NO VEHICLE PORT ) \r\n");
                    printf("-------------------------------------- \r\n");    
                }    
            }
            else
            {
                printf("SKIP VEHICLE ( NO VEHICLE INFO ) \r\n");
                printf("-------------------------------- \r\n");
            }
        }
        
        unset($datalog);
        $this->dbtrans = $this->load->database("transporter",true);
        $datalog["cron_name"] = "Autoreport Trip Mileage";
        $datalog["cron_start_data"] = $startdate;
        $datalog["cron_end_data"] = $enddate;
        $datalog["cron_start_proses"] = $startproses;
        $datalog["cron_end_proses"] = date("Y-m-d H:i:s");
        $this->dbtrans->insert("log_cronjobs",$datalog);
        
		$finishtime = date("d-m-Y H:i:s");
        printf("AUTOREPORT TRIP MILEAGE DONE %s\r\n",$finishtime);
        
    } 
        
    function trip_mileage_peruser($userid = "", $startdate = "", $enddate = "")
    {
        printf("PROSES AUTO REPORT TRIP MILEAGE >> START \r\n");
        $startproses = date("Y-m-d H:i:s");
        
        include 'class/PHPExcel.php';
        include 'class/PHPExcel/Writer/Excel2007.php';
        
        $report_type = "trip_mileage";
        $process_date = date("Y-m-d H:i:s");
        $domain_server = "http://202.129.190.194/";
        $z =0;
        //$userlist = array(775, 631, 981, 1059, 834, 778, 1222, 1027, 1246, 
        //1332, 1298, 986, 651, 645, 736, 666, 866, 627, 1386, 1110, 1148, 1122,
        //703, 1147, 1168);
        
        if ($startdate == "") {
            $startdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
            $datefilename = date("Ymd", strtotime("yesterday")); 
        }
        
        if ($startdate != "")
        {
            $datefilename = date("Ymd", strtotime($startdate));     
            $startdate = date("Y-m-d 00:00:00", strtotime($startdate));
        }
        
        if ($enddate != "")
        {
            $enddate = date("Y-m-d 23:59:59", strtotime($enddate));
        }
        
        if ($enddate == "") {
            $enddate = date("Y-m-d 23:59:59", strtotime("yesterday"));
        }
        
        $sdate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($startdate)));
        $edate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($enddate)));
        
        
        if ($userid != "")
        {
            $this->db->where("user_id",$userid);
        }
        
        /*if ($vehicle == 0)         
        {
            $this->db->where_in("vehicle_user_id", $userlist);    
        }*/
        
        $this->db->join("user", "vehicle_user_id = user_id", "left outer");
        $this->db->where("vehicle_status <>", 3);
        $q = $this->db->get("vehicle");
        $rowvehicle = $q->result();
        
        $total_process = count($rowvehicle);
        printf("TOTAL PROSES VEHICLE : %s \r\n",$total_process);
        printf("============================================ \r\n");
        //print_r($rowvehicle);exit;
        
        
        for($x=0;$x<count($rowvehicle);$x++)
        {
			$istbl_history = $this->config->item("dbhistory_default");
			if($this->config->item("is_dbhistory") == 1)
			{
				$istbl_history = $rowvehicle[$x]->vehicle_dbhistory_name;
			}
			$this->dbhist = $this->load->database($istbl_history, TRUE);
			$this->dbhist2 = $this->load->database("gpshistory2",true);
            $this->dbtrans = $this->load->database("transporter",true);
        
            printf("PROSES VEHICLE : %s (%d/%d) \r\n",$rowvehicle[$x]->vehicle_device, ++$z, $total_process);
            unset($data_insert);
            
            //cek apakah sudah pernah ada filenya
            $this->dbtrans->select("autoreport_vehicle_id");
            $this->dbtrans->where("autoreport_vehicle_id",$rowvehicle[$x]->vehicle_id);
            $this->dbtrans->where("autoreport_data_startdate",$startdate);
            $this->dbtrans->limit(1);
            $qrpt = $this->dbtrans->get("autoreport_tripmileage");
            $rrpt = $qrpt->row();
            
            if (count($rrpt)>0)
            {
              printf("VEHICLE %s SUDAH PERNAH DI PROSES, HAPUS DULU DATA SEBELUMNYA \r\n", $rowvehicle[$x]->vehicle_device);     
            }
            else
            {
                $objPHPExcel = new PHPExcel();
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
                $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(16);
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
                $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'TRIP MILEAGE REPORT');
                $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle('B3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $objPHPExcel->getActiveSheet()->getStyle('B3')->getFont()->setBold(true);
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
                
                $vehicle_device = explode("@", $rowvehicle[$x]->vehicle_device);  
                $vehicle_no = $rowvehicle[$x]->vehicle_no;
                
                $ex_vno = explode("/",$vehicle_no);

                if ($rowvehicle[$x]->vehicle_type == "T5" || $rowvehicle[$x]->vehicle_type == "T5 PULSE")
                {
                    $table = $vehicle_device[0]."@t5_gps";
                    $tableinfo = $vehicle_device[0]."@t5_info";    
                }
                else
                {
                    $table = strtolower($vehicle_device[0])."@".strtolower($vehicle_device[1])."_gps";
                    $tableinfo = strtolower($vehicle_device[0])."@".strtolower($vehicle_device[1])."_info";
                }
                
                //$report_path = "/home/lacakmobil/public_html/transporter.lacak-mobil.com/public/assets/media/autoreport/".$report_type."/".$vehicle_device[0]."/";
                $report_path = "/home/lacakmobil/public_html/transporter.lacak-mobil.com/public/assets/media/autoreport/".$report_type."/".$vehicle_device[0]."/";
				$pub_path = "assets/media/autoreport/".$report_type."/".$vehicle_device[0]."/";
            
                $this->dbhist->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");    
                $this->dbhist->where_in("gps_info_device", $rowvehicle[$x]->vehicle_device);
                $this->dbhist->where("gps_time >=", $sdate);
                $this->dbhist->where("gps_time <=", $edate);    
                $this->dbhist->order_by("gps_time","asc");
                $this->dbhist->from($table);
                $q = $this->dbhist->get();
                $rows1 = $q->result();
                
				$this->dbhist2->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");    
                $this->dbhist2->where("gps_info_device", $rowvehicle[$x]->vehicle_device);
				$this->dbhist2->where("gps_time >=", $sdate);
				$this->dbhist2->where("gps_time <=", $edate);    
				$this->dbhist2->order_by("gps_time","asc");
				$this->dbhist2->from($table);
				$q2 = $this->dbhist2->get();
				$rows2 = $q2->result();
				
				$rows = array_merge($rows1, $rows2);
                
                $data = array();
                $nopol = "";
                $on = false;
                $trows = count($rows);
        
                printf("TOTAL DATA : %s \r\n",$trows);
            
                for($i=0;$i<$trows;$i++)
                {
                    //if($rows[$i]->gps_speed == 0) continue;
                    if($nopol != $rowvehicle[$x]->vehicle_no)
                    { //new vehicle
                        if($on && $i!=0)
                        { 
                            $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
                            $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                            $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i-1]->gps_longitude, $rows[$i-1]->gps_ew, $rows[$i-1]->gps_latitude, $rows[$i-1]->gps_ns);
                            $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i-1]->gps_info_distance;
                        }
                        
                        if(substr($rows[$i]->gps_info_io_port, 4, 1) == 1)
                        {                                
                            $trip_no = 1;                    
                            $data[$rowvehicle[$x]->vehicle_no][$trip_no++]['start_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
                            $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                            $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                            $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_mileage'] = $rows[$i]->gps_info_distance;
                            $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['vehicle_name'] = $rowvehicle[$x]->vehicle_name;
                            $on = true;
                            
                            if($i==$trows-1)
                            {                                                
                                $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
                                $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
                            }
                        }
                        else
                        {
                            $trip_no = 1;
                            $on = false;
                        }
                    }
                    else
                    { //same vehicle
                        if(substr($rows[$i]->gps_info_io_port, 4, 1) == 1)
                        {
                            if(!$on)
                            {    
                                $data[$rowvehicle[$x]->vehicle_no][$trip_no++]['start_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
                                $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_mileage'] = $rows[$i]->gps_info_distance;
                                $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['vehicle_name'] = $rowvehicle[$x]->vehicle_name;
                            }
                            $on = true;    
                            if($i==$trows-1 && $on)
                            {                                                
                                $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
                                $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
                            }                                    
                        }
                        else
                        {            
                            if($on)
                            {
                                $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));    
                                $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
                            }
                            $on = false;
                        }
                    }
                    $nopol = $rowvehicle[$x]->vehicle_no;
                }
                
                            
                //Write Data
                $i=1;
                $new = "";
                printf("WRITE DATA EXCEL : ");
                foreach($data as $vehicle_no=>$val)
                {
                    if($new != $vehicle_no)
                    {
                        $cumm = 0;
                        $trip_no = 1;
                    }
                    
                    foreach($val as $no=>$report)
                    {
                        $mileage = $report['end_mileage']- $report['start_mileage'];
                        if($mileage != 0)
                        {
                            
                            $duration = get_time_difference($report['start_time'], $report['end_time']);
                            $show = "";
                            if($duration[0]!=0)
                            {
                                $show .= $duration[0] ." Day ";
                            }
                            if($duration[1]!=0)
                            {
                                $show .= $duration[1] ." Hour ";
                            }
                            if($duration[2]!=0)
                            {
                                $show .= $duration[2] ." Min ";
                            }
                            
                            if($show == "")
                            {
                                $show .= "0 Min";
                            }
                            
                            $tm = round(($mileage/1000),2);
                            $cumm += $tm;
                            
                            printf("|%s|",$i);
                            
                            $objPHPExcel->getActiveSheet()->SetCellValue('A'.(5+$i), $i);
                            $objPHPExcel->getActiveSheet()->getStyle('A'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            $objPHPExcel->getActiveSheet()->SetCellValue('B'.(5+$i), $vehicle_no);
                            $objPHPExcel->getActiveSheet()->SetCellValue('C'.(5+$i), $report['vehicle_name']);
                            $objPHPExcel->getActiveSheet()->SetCellValue('D'.(5+$i), $trip_no++);
                            $objPHPExcel->getActiveSheet()->getStyle('D'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            $objPHPExcel->getActiveSheet()->SetCellValue('E'.(5+$i), $report['start_time']);                        
                            $objPHPExcel->getActiveSheet()->SetCellValue('F'.(5+$i), $report['end_time']);
                            $objPHPExcel->getActiveSheet()->SetCellValue('G'.(5+$i), $show);
                            $objPHPExcel->getActiveSheet()->getStyle('G'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            
                            
                            //mileage
                            $xme = $tm;
                            $xxme = explode(".",$xme);
                            if (isset($xxme[1]))
                            {
                                $xsub = substr($xxme[1],0,2); 
                                $x_mile = $xxme[0].".".$xsub;     
                            }
                            else
                            {
                                $x_mile = $xxme[0];
                            }
                            
                            //cummulative
                            $xcum = $cumm;
                            $xxcum = explode(".",$xcum);
                            if (isset($xxcum[1]))
                            {
                                $xcumsub = substr($xxcum[1],0,2); 
                                $x_cum = $xxcum[0].".".$xcumsub;     
                            }
                            else
                            {
                                $x_cum = $xxcum[0];
                            }
                            
                            
                            $objPHPExcel->getActiveSheet()->SetCellValue('H'.(5+$i), $x_mile . " km");
                            $objPHPExcel->getActiveSheet()->getStyle('H'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                            $objPHPExcel->getActiveSheet()->SetCellValue('I'.(5+$i), $x_cum . " km");
                            $objPHPExcel->getActiveSheet()->getStyle('I'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                            $objPHPExcel->getActiveSheet()->SetCellValue('J'.(5+$i), $report['start_position']->display_name);
                            $objPHPExcel->getActiveSheet()->SetCellValue('K'.(5+$i), $report['end_position']->display_name);
                            $objPHPExcel->getActiveSheet()->getStyle('J'.(5+$i))->getFont()->setSize(8);
                            $objPHPExcel->getActiveSheet()->getStyle('K'.(5+$i))->getFont()->setSize(8);
                            $i++;
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
                
                $objPHPExcel->getActiveSheet()->getStyle('A5:K'.(4+$i))->applyFromArray($styleArray);
                $objPHPExcel->getActiveSheet()->getStyle('A5:K'.(4+$i))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
                $objPHPExcel->getActiveSheet()->getStyle('A5:K'.(4+$i))->getAlignment()->setWrapText(true);
                
                // Rename sheet
                $objPHPExcel->getActiveSheet()->setTitle('trip_mileage');
            
                printf("CREATE FILE FOR : %s \r\n",$rowvehicle[$x]->vehicle_device);
            
                // Save Excel
                $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
                @mkdir($report_path, DIR_WRITE_MODE); //REPORT_PATH -> config->constant
                
                if (isset($ex_vno))
                {
                    if (isset($ex_vno[1]))
                    {
                        $filecreatedname = "TripMileage_".$ex_vno[0].$ex_vno[1]."_".$datefilename.".xls";    
                    }
                    else
                    {
                        $filecreatedname = "TripMileage_".$ex_vno[0]."_".$datefilename.".xls";       
                    }
                }
                else
                {
                    
                    $filecreatedname = "TripMileage_".$vehicle_no."_".$datefilename.".xls";  
                }
                  
                $objWriter->save($report_path.$filecreatedname);
                printf("CREATE FILE DONE : %s \r\n",$filecreatedname);
            
                $public_path = $domain_server.$pub_path.$filecreatedname;
            
                printf("INSERT TO DATABASE TRANSPORTER \r\n");
                $data_insert["autoreport_vehicle_id"] = $rowvehicle[$x]->vehicle_id;
                $data_insert["autoreport_vehicle_device"] = $rowvehicle[$x]->vehicle_device;
                $data_insert["autoreport_user_id"] = $rowvehicle[$x]->user_id;
                $data_insert["autoreport_company"] = $rowvehicle[$x]->user_company;
                $data_insert["autoreport_data_startdate"] = $startdate;
                $data_insert["autoreport_data_enddate"] = $enddate;
                $data_insert["autoreport_type"] = $report_type;
                $data_insert["autoreport_file_path"] = $report_path;
                $data_insert["autoreport_filename"] = $filecreatedname;
                $data_insert["autoreport_download_path"] = $report_path.$filecreatedname;
                $data_insert["autoreport_public_path"] = $public_path;
                $data_insert["autoreport_process_date"] = $process_date;
                $data_insert["autoreport_insert_db"] = date("Y-m-d H:i:s");
            
                $this->dbtrans->insert("autoreport_tripmileage",$data_insert);
                printf("INSERT OK \r\n");
            
                printf("DELETE CACHE HISTORY \r\n");
                $this->dbhist->cache_delete_all();
                $this->dbtrans->cache_delete_all();
                unset($data);
            
                printf("FINISH FOR VEHICLE : %s \r\n",$rowvehicle[$x]->vehicle_device);
                printf("============================================ \r\n");
            
                //$output = '{"success":true,"errMsg":"","filename":"' .base_url() . "assets/media/report/" . $filecreatedname.'"}';
                //echo $output;
                // return;    
            }
        }
        
        unset($datalog);
        $this->dbtrans = $this->load->database("transporter",true);
        $datalog["cron_name"] = "Autoreport Trip Mileage";
        $datalog["cron_start_data"] = $startdate;
        $datalog["cron_end_data"] = $enddate;
        $datalog["cron_start_proses"] = $startproses;
        $datalog["cron_end_proses"] = date("Y-m-d H:i:s");
        $this->dbtrans->insert("log_cronjobs",$datalog);
        
        printf("AUTOREPORT TRIP MILEAGE DONE \r\n");
        
    } 
        
    function trip_mileage_pervehicle($name = "", $host="", $startdate = "", $enddate = "")
    {
        printf("PROSES AUTO REPORT TRIP MILEAGE >> START \r\n");
        $startproses = date("Y-m-d H:i:s");
        
        include 'class/PHPExcel.php';
        include 'class/PHPExcel/Writer/Excel2007.php';
            
        $report_type = "trip_mileage";
        $process_date = date("Y-m-d H:i:s");
        $domain_server = "http://202.129.190.194/";
        $z =0;
        //$userlist = array(775, 631, 981, 1059, 834, 778, 1222, 1027, 1246, 
        //1332, 1298, 986, 651, 645, 736, 666, 866, 627, 1386, 1110, 1148, 1122,
        //703, 1147, 1168);
        
        if ($startdate == "") {
            $startdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
            $datefilename = date("Ymd", strtotime("yesterday")); 
        }
        
        if ($startdate != "")
        {
            $datefilename = date("Ymd", strtotime($startdate));     
            $startdate = date("Y-m-d 00:00:00", strtotime($startdate));
        }
        
        if ($enddate != "")
        {
            $enddate = date("Y-m-d 23:59:59", strtotime($enddate));
        }
        
        if ($enddate == "") {
            $enddate = date("Y-m-d 23:59:59", strtotime("yesterday"));
        }
        
        $sdate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($startdate)));
        $edate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($enddate)));
        
        
        if ($name != "" && $host != "")
        {
            $dev = $name."@".$host;
            $this->db->where("vehicle_device",$dev);
        }
        
        $this->db->join("user", "vehicle_user_id = user_id", "left outer");
        $this->db->where("vehicle_status <>", 3);
        $q = $this->db->get("vehicle");
        $rowvehicle = $q->result();
        
        $total_process = count($rowvehicle);
        printf("TOTAL PROSES VEHICLE : %s \r\n",$total_process);
        printf("============================================ \r\n");
        
        for($x=0;$x<count($rowvehicle);$x++)
        {
			$istbl_history = $this->config->item("dbhistory_default");
			if($this->config->item("is_dbhistory") == 1)
			{
				$istbl_history = $rowvehicle[$x]->vehicle_dbhistory_name;
			}
			$this->dbhist = $this->load->database($istbl_history, TRUE);
			$this->dbhist2 = $this->load->database("gpshistory2",true);
            $this->dbtrans = $this->load->database("transporter",true);
        
            printf("PROSES VEHICLE : %s (%d/%d) \r\n",$rowvehicle[$x]->vehicle_device, ++$z, $total_process);
            unset($data_insert);
            
            //cek apakah sudah pernah ada filenya
            $this->dbtrans->select("autoreport_vehicle_id");
            $this->dbtrans->where("autoreport_vehicle_id",$rowvehicle[$x]->vehicle_id);
            $this->dbtrans->where("autoreport_data_startdate",$startdate);
            $this->dbtrans->limit(1);
            $qrpt = $this->dbtrans->get("autoreport_tripmileage");
            $rrpt = $qrpt->row();
            if (count($rrpt)>0)
            {
              printf("VEHICLE %s SUDAH PERNAH DI PROSES, HAPUS DULU DATA SEBELUMNYA \r\n", $rowvehicle[$x]->vehicle_device);     
            }
            else
            {
            
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
                $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(16);
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
                $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'TRIP MILEAGE REPORT');
                $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle('B3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $objPHPExcel->getActiveSheet()->getStyle('B3')->getFont()->setBold(true);
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

                $vehicle_device = explode("@", $rowvehicle[$x]->vehicle_device);  
                $vehicle_no = $rowvehicle[$x]->vehicle_no;
                
                $ex_vno = explode("/",$vehicle_no);

                if ($rowvehicle[$x]->vehicle_type == "T5" || $rowvehicle[$x]->vehicle_type == "T5 PULSE")
                {
                    $table = $vehicle_device[0]."@t5_gps";
                    $tableinfo = $vehicle_device[0]."@t5_info";    
                }
                else
                {
                    $table = strtolower($vehicle_device[0])."@".strtolower($vehicle_device[1])."_gps";
                    $tableinfo = strtolower($vehicle_device[0])."@".strtolower($vehicle_device[1])."_info";
                }
            
                //$report_path = "/home/lacakmobil/public_html/transporter.lacak-mobil.com/public/assets/media/autoreport/".$report_type."/".$vehicle_device[0]."/";
                $report_path = "/home/lacakmobil/public_html/transporter.lacak-mobil.com/public/assets/media/autoreport/".$report_type."/".$vehicle_device[0]."/";
				$pub_path = "assets/media/autoreport/".$report_type."/".$vehicle_device[0]."/";
            
                $this->dbhist->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");    
                $this->dbhist->where_in("gps_info_device", $rowvehicle[$x]->vehicle_device);
                $this->dbhist->where("gps_time >=", $sdate);
                $this->dbhist->where("gps_time <=", $edate);    
                $this->dbhist->order_by("gps_time","asc");
                $this->dbhist->from($table);
                $q = $this->dbhist->get();
                $rows1 = $q->result();
				
				$this->dbhist2->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");    
                $this->dbhist2->where("gps_info_device", $rowvehicle[$x]->vehicle_device);
				$this->dbhist2->where("gps_time >=", $sdate);
				$this->dbhist2->where("gps_time <=", $edate);    
				$this->dbhist2->order_by("gps_time","asc");
				$this->dbhist2->from($table);
				$q2 = $this->dbhist2->get();
				$rows2 = $q2->result();
				
				$rows = array_merge($rows1, $rows2);
                
                $data = array();
                $nopol = "";
                $on = false;
                $trows = count($rows);
        
                printf("TOTAL DATA : %s \r\n",$trows);
            
                for($i=0;$i<$trows;$i++)
                {
                    //if($rows[$i]->gps_speed == 0) continue;
                    if($nopol != $rowvehicle[$x]->vehicle_no)
                    { //new vehicle
                        if($on && $i!=0)
                        { 
                            $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
                            $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                            $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i-1]->gps_longitude, $rows[$i-1]->gps_ew, $rows[$i-1]->gps_latitude, $rows[$i-1]->gps_ns);
                            $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i-1]->gps_info_distance;
                        }
                        
                        if(substr($rows[$i]->gps_info_io_port, 4, 1) == 1)
                        {                                
                            $trip_no = 1;                    
                            $data[$rowvehicle[$x]->vehicle_no][$trip_no++]['start_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
                            $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                            $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                            $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_mileage'] = $rows[$i]->gps_info_distance;
                            $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['vehicle_name'] = $rowvehicle[$x]->vehicle_name;
                            $on = true;
                            
                            if($i==$trows-1)
                            {                                                
                                $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
                                $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
                            }
                        }
                        else
                        {
                            $trip_no = 1;
                            $on = false;
                        }
                    }
                    else
                    { //same vehicle
                        if(substr($rows[$i]->gps_info_io_port, 4, 1) == 1)
                        {
                            if(!$on)
                            {    
                                $data[$rowvehicle[$x]->vehicle_no][$trip_no++]['start_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
                                $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_mileage'] = $rows[$i]->gps_info_distance;
                                $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['vehicle_name'] = $rowvehicle[$x]->vehicle_name;
                            }
                            $on = true;    
                            if($i==$trows-1 && $on)
                            {                                                
                                $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
                                $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
                            }                                    
                        }
                        else
                        {            
                            if($on)
                            {
                                $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));    
                                $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence_location($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rows[$i]->gps_name);
                                $data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
                            }
                            $on = false;
                        }
                    }
                    $nopol = $rowvehicle[$x]->vehicle_no;
                }
                
                //Write Data
                $i=1;
                $new = "";
                printf("WRITE DATA EXCEL : ");
                foreach($data as $vehicle_no=>$val)
                {
                    if($new != $vehicle_no)
                    {
                        $cumm = 0;
                        $trip_no = 1;
                    }
                    
                    foreach($val as $no=>$report)
                    {
                        $mileage = $report['end_mileage']- $report['start_mileage'];
                        if($mileage != 0)
                        {
                            
                            $duration = get_time_difference($report['start_time'], $report['end_time']);
                            $show = "";
                            if($duration[0]!=0)
                            {
                                $show .= $duration[0] ." Day ";
                            }
                            if($duration[1]!=0)
                            {
                                $show .= $duration[1] ." Hour ";
                            }
                            if($duration[2]!=0)
                            {
                                $show .= $duration[2] ." Min ";
                            }
                            
                            if($show == "")
                            {
                                $show .= "0 Min";
                            }
                            
                            $tm = round(($mileage/1000),2);
                            $cumm += $tm;
                            
                            printf("|%s|",$i);
                            
                            $objPHPExcel->getActiveSheet()->SetCellValue('A'.(5+$i), $i);
                            $objPHPExcel->getActiveSheet()->getStyle('A'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            $objPHPExcel->getActiveSheet()->SetCellValue('B'.(5+$i), $vehicle_no);
                            $objPHPExcel->getActiveSheet()->SetCellValue('C'.(5+$i), $report['vehicle_name']);
                            $objPHPExcel->getActiveSheet()->SetCellValue('D'.(5+$i), $trip_no++);
                            $objPHPExcel->getActiveSheet()->getStyle('D'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            $objPHPExcel->getActiveSheet()->SetCellValue('E'.(5+$i), $report['start_time']);                        
                            $objPHPExcel->getActiveSheet()->SetCellValue('F'.(5+$i), $report['end_time']);
                            $objPHPExcel->getActiveSheet()->SetCellValue('G'.(5+$i), $show);
                            $objPHPExcel->getActiveSheet()->getStyle('G'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            
                            
                            //mileage
                            $xme = $tm;
                            $xxme = explode(".",$xme);
                            if (isset($xxme[1]))
                            {
                                $xsub = substr($xxme[1],0,2); 
                                $x_mile = $xxme[0].".".$xsub;     
                            }
                            else
                            {
                                $x_mile = $xxme[0];
                            }
                            
                            //cummulative
                            $xcum = $cumm;
                            $xxcum = explode(".",$xcum);
                            if (isset($xxcum[1]))
                            {
                                $xcumsub = substr($xxcum[1],0,2); 
                                $x_cum = $xxcum[0].".".$xcumsub;     
                            }
                            else
                            {
                                $x_cum = $xxcum[0];
                            }
                            
                            
                            $objPHPExcel->getActiveSheet()->SetCellValue('H'.(5+$i), $x_mile . " km");
                            $objPHPExcel->getActiveSheet()->getStyle('H'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                            $objPHPExcel->getActiveSheet()->SetCellValue('I'.(5+$i), $x_cum . " km");
                            $objPHPExcel->getActiveSheet()->getStyle('I'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                            $objPHPExcel->getActiveSheet()->SetCellValue('J'.(5+$i), $report['start_position']->display_name);
                            $objPHPExcel->getActiveSheet()->SetCellValue('K'.(5+$i), $report['end_position']->display_name);
                            $objPHPExcel->getActiveSheet()->getStyle('J'.(5+$i))->getFont()->setSize(8);
                            $objPHPExcel->getActiveSheet()->getStyle('K'.(5+$i))->getFont()->setSize(8);
                            $i++;
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
                
                $objPHPExcel->getActiveSheet()->getStyle('A5:K'.(4+$i))->applyFromArray($styleArray);
                $objPHPExcel->getActiveSheet()->getStyle('A5:K'.(4+$i))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
                $objPHPExcel->getActiveSheet()->getStyle('A5:K'.(4+$i))->getAlignment()->setWrapText(true);
                
                // Rename sheet
                $objPHPExcel->getActiveSheet()->setTitle('trip_mileage');
            
                printf("CREATE FILE FOR : %s \r\n",$rowvehicle[$x]->vehicle_device);
            
                // Save Excel
                $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
                @mkdir($report_path, DIR_WRITE_MODE); //REPORT_PATH -> config->constant
                
                if (isset($ex_vno))
                {
                    if (isset($ex_vno[1]))
                    {
                        $filecreatedname = "TripMileage_".$ex_vno[0].$ex_vno[1]."_".$datefilename.".xls";    
                    }
                    else
                    {
                        $filecreatedname = "TripMileage_".$ex_vno[0]."_".$datefilename.".xls";       
                    }
                }
                else
                {
                    
                    $filecreatedname = "TripMileage_".$vehicle_no."_".$datefilename.".xls";  
                }
                  
                $objWriter->save($report_path.$filecreatedname);
                printf("CREATE FILE DONE : %s \r\n",$filecreatedname);
            
                $public_path = $domain_server.$pub_path.$filecreatedname;
            
                printf("INSERT TO DATABASE TRANSPORTER \r\n");
                $data_insert["autoreport_vehicle_id"] = $rowvehicle[$x]->vehicle_id;
                $data_insert["autoreport_vehicle_device"] = $rowvehicle[$x]->vehicle_device;
                $data_insert["autoreport_user_id"] = $rowvehicle[$x]->user_id;
                $data_insert["autoreport_company"] = $rowvehicle[$x]->user_company;
                $data_insert["autoreport_data_startdate"] = $startdate;
                $data_insert["autoreport_data_enddate"] = $enddate;
                $data_insert["autoreport_type"] = $report_type;
                $data_insert["autoreport_file_path"] = $report_path;
                $data_insert["autoreport_filename"] = $filecreatedname;
                $data_insert["autoreport_download_path"] = $report_path.$filecreatedname;
                $data_insert["autoreport_public_path"] = $public_path;
                $data_insert["autoreport_process_date"] = $process_date;
                $data_insert["autoreport_insert_db"] = date("Y-m-d H:i:s");
            
                $this->dbtrans->insert("autoreport_tripmileage",$data_insert);
                printf("INSERT OK \r\n");
            
                printf("DELETE CACHE HISTORY \r\n");
                $this->dbhist->cache_delete_all();
                $this->dbtrans->cache_delete_all();
                unset($data);
            
                printf("FINISH FOR VEHICLE : %s \r\n",$rowvehicle[$x]->vehicle_device);
                printf("============================================ \r\n");
            
                //$output = '{"success":true,"errMsg":"","filename":"' .base_url() . "assets/media/report/" . $filecreatedname.'"}';
                //echo $output;
                // return;    
            }
        }
        
        unset($datalog);
        $this->dbtrans = $this->load->database("transporter",true);
        $datalog["cron_name"] = "Autoreport Trip Mileage";
        $datalog["cron_start_data"] = $startdate;
        $datalog["cron_end_data"] = $enddate;
        $datalog["cron_start_proses"] = $startproses;
        $datalog["cron_end_proses"] = date("Y-m-d H:i:s");
        $this->dbtrans->insert("log_cronjobs",$datalog);
        
        printf("AUTOREPORT TRIP MILEAGE DONE \r\n");
        
    } 
        
    function history($datatype=1, $startdate="", $enddate="", $name="", $host="")
    {
        printf("PROSES AUTO REPORT HISTORY START \r\n");
        $startproses = date("Y-m-d H:i:s");
        
        include 'class/PHPExcel.php';
        include 'class/PHPExcel/Writer/Excel2007.php';
        
        //$userlist = array(775, 631, 981, 1059, 834, 778, 1222, 1027, 1246, 
        //1332, 1298, 986, 651, 645, 736, 666, 866, 627, 1386, 1110, 1148, 1122,
        //703, 1147, 1168, 1212);
        
        $dbname = "gpshistory";
        $order = "desc";
        $limit = "1000";
        $offset = 0;
        $totalodometer = 0;
        $totalodometer1 = 0;
        $process_date = date("Y-m-d H:i:s");
        $this->dbhist = $this->load->database($dbname, TRUE);
		$this->dbhist2 = $this->load->database("gpshistory2", TRUE);
        $this->dbtrans = $this->load->database("transporter",true);
        $z = 0;
        
        $report_type = "history_report";
        
        if ($startdate == "") 
        {
            $startdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
            $datefilename = date("Ymd", strtotime("yesterday")); 
        }
        
        if ($startdate != "")
        {
            $datefilename = date("Ymd", strtotime($startdate));     
            $startdate = date("Y-m-d 00:00:00", strtotime($startdate));
        }
        
        if ($enddate != "")
        {
            $enddate = date("Y-m-d 23:59:59", strtotime($enddate));
        }
        
        if ($enddate == "") 
        {
            $enddate = date("Y-m-d 23:59:59", strtotime("yesterday"));
        }
        
        $month = date("F", strtotime($startdate));
        $year = date("Y", strtotime($startdate));
        
        $sdate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($startdate)));
        $edate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($enddate)));
        
		
		//User Duluan
		$this->db->where("user_id",1488);
		$this->db->or_where("user_id",1434);
		$this->db->or_where("user_id",1486);
        $this->db->join("user", "vehicle_user_id = user_id", "left outer");
        $this->db->where("vehicle_status <>", 3);
        $q = $this->db->get("vehicle");
        $rowvehicle = $q->result();
		
		$total_process = count($rowvehicle);
        printf("TOTAL PROSES VEHICLE DULUAN : %s \r\n",$total_process);
        printf("============================================ \r\n");
		
		for($x=0;$x<count($rowvehicle);$x++)
        {
            printf("PROSES VEHICLE : %s (%d/%d) \r\n",$rowvehicle[$x]->vehicle_device, ++$z, $total_process);
            
            if (isset($rowvehicle[$x]->vehicle_info))
            {
                $json = json_decode($rowvehicle[$x]->vehicle_info);
                if (isset($json->vehicle_ws))
                {
                    $objPHPExcel = new PHPExcel();
                    $objPHPExcel->getProperties()->setTitle("History Report");
                    $objPHPExcel->getProperties()->setSubject("History Report");
                    $objPHPExcel->getProperties()->setDescription("History Report");
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
                    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(55);            
                    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(12);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(12);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(12);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10);

                    //Header
                    $objPHPExcel->setActiveSheetIndex(0);
                    $objPHPExcel->getActiveSheet()->mergeCells('A1:I1');
                    $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'HISTORY REPORT');
                    $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
                    $objPHPExcel->getActiveSheet()->getStyle('B3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    $objPHPExcel->getActiveSheet()->getStyle('B3')->getFont()->setBold(true);
            
                    $objPHPExcel->getActiveSheet()->getStyle('J3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    $objPHPExcel->getActiveSheet()->getStyle('J3')->getFont()->setBold(true);
                    $objPHPExcel->getActiveSheet()->SetCellValue('A5', 'No');
                    $objPHPExcel->getActiveSheet()->SetCellValue('B5', 'Date');
                    $objPHPExcel->getActiveSheet()->SetCellValue('C5', 'Time');
                    $objPHPExcel->getActiveSheet()->SetCellValue('D5', 'Location');
                    $objPHPExcel->getActiveSheet()->SetCellValue('E5', 'Coordinate');
                    $objPHPExcel->getActiveSheet()->SetCellValue('F5', 'Speed (Kph)');
                    $objPHPExcel->getActiveSheet()->SetCellValue('G5', 'Engine');
                    $objPHPExcel->getActiveSheet()->SetCellValue('H5', 'Status');
                    $objPHPExcel->getActiveSheet()->SetCellValue('I5', 'Odometer (km)');
                    $objPHPExcel->getActiveSheet()->getStyle('A5:I5')->getFont()->setBold(true);
                    $objPHPExcel->getActiveSheet()->getStyle('A5:I5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    
                    //cek apakah sudah pernah ada filenya
                    $this->dbtrans->select("autoreport_vehicle_id");
                    $this->dbtrans->where("autoreport_vehicle_id",$rowvehicle[$x]->vehicle_id);
                    $this->dbtrans->where("autoreport_data_startdate",$startdate);
                    $this->dbtrans->limit(1);
                    $qrpt = $this->dbtrans->get("autoreport_history");
                    $rrpt = $qrpt->row();
                    
                    if (count($rrpt)>0)
                    {
                        printf("VEHICLE %s SUDAH PERNAH DI PROSES, HAPUS DULU DATA SEBELUMNYA \r\n", $rowvehicle[$x]->vehicle_device);     
                    }
                    else
                    {
                        $vehicle_device = explode("@", $rowvehicle[$x]->vehicle_device);  
                        $vehicle_no = $rowvehicle[$x]->vehicle_no;
                
                        $ex_vno = explode("/",$vehicle_no);
                        $name = $vehicle_device[0];
                        $host = $vehicle_device[1];
            
                        if ($rowvehicle[$x]->vehicle_type == "T5" || $rowvehicle[$x]->vehicle_type == "T5 PULSE")
                        {
                            $tables = $name."@t5_gps";
                            $tablesinfo = $name."@t5_info";       
                        }
                        else
                        {
                            $tables = strtolower($name)."@".strtolower($host)."_gps";
                            $tablesinfo = strtolower($name)."@".strtolower($host)."_info";
                        }
            
                        //$report_path = "/home/lacakmobil/public_html/transporter.lacak-mobil.com/public/assets/media/autoreport/".
                        $report_path = "/home/lacakmobil/public_html/transporter.lacak-mobil.com/public/assets/media/autoreport/".
						$report_type."/".$year."/".$month."/".$vehicle_device[0]."/";
            
                        printf("GET DATA GPS VEHICLE : %s \r\n",$rowvehicle[$x]->vehicle_no);
						$this->dbhist->select("gps_id, gps_time, gps_speed, gps_longitude, gps_ew, gps_latitude, gps_ns, gps_status");
						$this->dbhist->order_by("gps_time", $order);
						$this->dbhist->where("gps_name", $name);
						$this->dbhist->where("gps_host", $host);
						$this->dbhist->where("gps_time >=", $sdate);
						$this->dbhist->where("gps_time <=", $edate);
						$q = $this->dbhist->get($tables);
						$rows1 = $q->result();
						
						$this->dbhist2->select("gps_id, gps_time, gps_speed, gps_longitude, gps_ew, gps_latitude, gps_ns, gps_status");
						$this->dbhist2->order_by("gps_time", $order);
						$this->dbhist2->where("gps_name", $name);
						$this->dbhist2->where("gps_host", $host);
						$this->dbhist2->where("gps_time >=", $sdate);
						$this->dbhist2->where("gps_time <=", $edate);
						$q = $this->dbhist2->get($tables);
						$rows2 = $q->result();
				
						$rows = array_merge($rows1, $rows2);
            
						printf("GET DATA INFO VEHICLE : %s \r\n",$rowvehicle[$x]->vehicle_no);
						$this->dbhist->order_by("gps_info_time", $order);
						$this->dbhist->select("gps_info_io_port, gps_info_time, gps_info_distance, gps_info_alarm_alert, gps_info_alarm_data, gps_info_ad_input");
						$this->dbhist->where("gps_info_device", $name."@".$host);
						$this->dbhist->where("gps_info_time >=", $sdate);
						$this->dbhist->where("gps_info_time <=", $edate);
						$this->dbhist->limit(1);
						$qinfo = $this->dbhist->get($tablesinfo);
						$rowlastinfos1 = $qinfo->result();
							
						$this->dbhist->order_by("gps_info_time", $order);
						$this->dbhist->select("gps_info_io_port, gps_info_time, gps_info_distance, gps_info_alarm_alert, gps_info_alarm_data, gps_info_ad_input");
						$this->dbhist->where("gps_info_device", $name."@".$host);
						$this->dbhist->where("gps_info_time >=", $sdate);
						$this->dbhist->where("gps_info_time <=", $edate);
						$this->dbhist->limit(1);
						$qinfo = $this->dbhist->get($tablesinfo);
						$rowlastinfos2 = $qinfo->result();
							
						$rowlastinfos = array_merge($rowlastinfos1, $rowlastinfos2);
                        
                        //Summary Saja yang diambil
                        if ($datatype == 0)
                        {
                            for($i=count($rows)-1; $i >= 0; $i--)
                            {
                                if (($i+1) >= count($rows))
                                {
                                    $rowsummary[] = $rows[$i];
                                    continue;
                                }
                                
                                $latbefore = getLatitude($rows[$i+1]->gps_latitude, $rows[$i+1]->gps_ns);
                                $lngbefore = getLongitude($rows[$i+1]->gps_longitude, $rows[$i+1]->gps_ew);
                
                                $latcurrent = getLatitude($rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                $lngcurrent = getLongitude($rows[$i]->gps_longitude, $rows[$i]->gps_ew);
                                
                                if (sprintf("%.4f,%.4f", $latbefore, $lngbefore) != sprintf("%.4f,%.4f", $latcurrent, $lngcurrent))
                                {
                                    $rowsummary[] = $rows[$i];
                                    continue;
                                }

                                if ($rows[$i+1]->gps_speed != $rows[$i]->gps_speed)
                                {
                                    $rowsummary[] = $rows[$i];
                                    continue;
                                }
                            }
                            
                            $rows = array();
                            $total = 0;
                            
                            if (isset($rowsummary))
                            {
                                $rowsummary = array_reverse($rowsummary);            
                                $total = count($rowsummary);
                                $rows = array_splice($rowsummary, $offset, $limit);                            
                            }   
                        }
                        //Finis Summary
            
                        unset($map_params);
                        $ismove = false;
                        $lastcoord = false;
                        
                        printf("PROSES XLS FILE FOR : %s  \r\n",$rowvehicle[$x]->vehicle_no);
                        
                        for($i=0; $i < count($rows); $i++)
                        {
                            printf("|%s|",$i);
                            if ($i == 0)
                            {
                                $this->dbhist->order_by("gps_info_time", $order);
                                $this->dbhist->select("gps_info_io_port, gps_info_time, gps_info_distance, gps_info_alarm_alert, gps_info_alarm_data, gps_info_ad_input");
                                $this->dbhist->where("gps_info_device", $name."@".$host);
                                $this->dbhist->where("gps_info_time >=", $sdate);
                                $this->dbhist->where("gps_info_time <=", $edate);
								$this->dbhist->limit($limit);
                                $qinfos = $this->dbhist->get($tablesinfo);   
                                $rowinfos = $qinfos->result();
                                $this->db->cache_delete_all();
                                
                                for($j=0; $j < count($rowinfos); $j++)
                                {
                                    $infos[dbmaketime($rowinfos[$j]->gps_info_time)] = $rowinfos[$j];
                                }
                            }
                            
                            $rows[$i]->gps_timestamp = dbmaketime($rows[$i]->gps_time);
                            $rows[$i]->gps_longitude_real = getLongitude($rows[$i]->gps_longitude, $rows[$i]->gps_ew);
                            $rows[$i]->gps_latitude_real = getLatitude($rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                            $rows[$i]->gps_longitude_real_fmt = number_format($rows[$i]->gps_longitude_real, 4, ".", "");
                            $rows[$i]->gps_latitude_real_fmt = number_format($rows[$i]->gps_latitude_real, 4, ".", "");        
            
                            if ($i == 0)
                            {
                                $lastcoord = array($rows[$i]->gps_longitude_real_fmt, $rows[$i]->gps_latitude_real_fmt);
                            }
                            else
                            {
                                if (($lastcoord[0] != $rows[$i]->gps_longitude_real_fmt) || ($lastcoord[1] != $rows[$i]->gps_latitude_real_fmt))
                                {
                                    $ismove = true;
                                }
                            }
                
                            $rows[$i]->gps_date_fmt = date("d/m/Y", $rows[$i]->gps_timestamp+7*3600);
                            $rows[$i]->gps_time_fmt = date("H:i:s", $rows[$i]->gps_timestamp+7*3600);
                            $rows[$i]->gps_speed_fmt = number_format($rows[$i]->gps_speed*1.852, 0, "", ".");
                            $rows[$i]->gps_status = ($rows[$i]->gps_status == "A") ? "OK" : "NOT OK";
            
                            if (isset($infos[$rows[$i]->gps_timestamp]))
                            {
                                $ioport = $infos[$rows[$i]->gps_timestamp]->gps_info_io_port;
                                $rows[$i]->status1 = ((strlen($ioport) > 4) && ($ioport[4] == 1)) ? $this->lang->line('lon') : $this->lang->line('loff');
                                $rows[$i]->odometer = number_format(round(($infos[$rows[$i]->gps_timestamp]->gps_info_distance+$rowvehicle[$x]->vehicle_odometer*1000)/1000), 0, "", ","); 
                            }
                            else
                            {
                                $rows[$i]->status1 = "-";
                                $rows[$i]->odometer = "-";
                            }
                
                            $rows[$i]->georeverse = $this->gpsmodel->GeoReverse($rows[$i]->gps_latitude_real_fmt, $rows[$i]->gps_longitude_real_fmt);            
                            $rows[$i]->gpsindex = $i+1;
                            $rows[$i]->gpsdate = date("d/m/Y", $rows[$i]->gps_timestamp+7*3600);
                            $rows[$i]->gpstime = date("H:i:s", $rows[$i]->gps_timestamp+7*3600);
                            $rows[$i]->gpsaddress = $rows[$i]->georeverse->display_name;
                            $rows[$i]->gpscoord = "(".$rows[$i]->gps_longitude_real_fmt." ".$rows[$i]->gps_latitude_real_fmt.")";
                            $rows[$i]->gpstatus = (($rows[$i]->gps_status == "V") ? "NOT OK" : "OK");
                            $map_params[] = array($rows[$i]->gps_longitude_real_fmt, $rows[$i]->gps_latitude_real_fmt);
            
                            $objPHPExcel->getActiveSheet()->SetCellValue('A'.(6+$i), $i+1);
                            $objPHPExcel->getActiveSheet()->SetCellValue('B'.(6+$i), date("d/m/Y", $rows[$i]->gps_timestamp+7*3600));
                            $objPHPExcel->getActiveSheet()->SetCellValue('C'.(6+$i), date("H:i:s", $rows[$i]->gps_timestamp+7*3600));
                            $objPHPExcel->getActiveSheet()->SetCellValue('D'.(6+$i), $rows[$i]->georeverse->display_name);
                            $objPHPExcel->getActiveSheet()->SetCellValue('E'.(6+$i), $rows[$i]->gps_latitude_real_fmt .",".$rows[$i]->gps_longitude_real_fmt);
                            $objPHPExcel->getActiveSheet()->SetCellValue('F'.(6+$i), $rows[$i]->gps_speed_fmt);
                            $objPHPExcel->getActiveSheet()->SetCellValue('G'.(6+$i), $rows[$i]->status1);
                            $objPHPExcel->getActiveSheet()->SetCellValue('H'.(6+$i), ($rows[$i]->gps_status == "V") ? "NOT OK" : "OK"); 
                            $objPHPExcel->getActiveSheet()->SetCellValue('I'.(6+$i), $rows[$i]->odometer);
                
                            $styleArray = array(
                                'borders' => array(
                                'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                            )
                            );
                        
                            $objPHPExcel->getActiveSheet()->getStyle('A5:I'.(5+$i))->applyFromArray($styleArray);
                            $objPHPExcel->getActiveSheet()->getStyle('A5:I'.(5+$i))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
                            $objPHPExcel->getActiveSheet()->getStyle('A5:I'.(5+$i))->getAlignment()->setWrapText(true);
                        } 
            
                        // Save Excel
                        $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
                        @mkdir($report_path, DIR_WRITE_MODE); //REPORT_PATH -> config->constant
                
                        if (isset($ex_vno))
                        {
                            if (isset($ex_vno[1]))
                            {
                                $filecreatedname = "HistoryReport_".$ex_vno[0].$ex_vno[1]."_".$datefilename.".xls";    
                            }
                            else
                            {
                                $filecreatedname = "HistoryReport_".$ex_vno[0]."_".$datefilename.".xls";       
                            }
                        }
                        else
                        {
                            $filecreatedname = "HistoryReport_".$vehicle_no."_".$datefilename.".xls";  
                        }
                
                        printf("SAVE XLS FILE FOR : %s  \r\n",$filecreatedname); 
                        $objWriter->save($report_path.$filecreatedname);
                
                        printf("DELETE CACHE GPS \r\n"); 
                        $this->db->cache_delete_all();
                        $this->dbhist->cache_delete_all();
                
                        printf("INSERT TO DB : %s  \r\n",$rowvehicle[$x]->vehicle_no);
                        unset($datainsert);
                        $datainsert["autoreport_vehicle_id"] = $rowvehicle[$x]->vehicle_id;
                        $datainsert["autoreport_vehicle_device"] = $rowvehicle[$x]->vehicle_device;
                        $datainsert["autoreport_vehicle_name"] = $rowvehicle[$x]->vehicle_name;
                        $datainsert["autoreport_vehicle_no"] = $rowvehicle[$x]->vehicle_no;
                        $datainsert["autoreport_user_id"] = $rowvehicle[$x]->user_id;
                        $datainsert["autoreport_company"] = $rowvehicle[$x]->user_company;
                        $datainsert["autoreport_data_startdate"] = $startdate;
                        $datainsert["autoreport_data_enddate"] = $enddate;
                        $datainsert["autoreport_type"] = $report_type;
                        $datainsert["autoreport_file_path"] = $report_path;
                        $datainsert["autoreport_filename"] = $filecreatedname;
                        $datainsert["autoreport_download_path"] = $report_path.$filecreatedname;
                        $datainsert["autoreport_process_date"] = $process_date; 
                        $datainsert["autoreport_insert_db"] = date("Y-m-d H:i:s");
                        $this->dbtrans->insert("autoreport_history",$datainsert);
                
                        printf("INSERT OK \r\n");
                
                        printf("FINIS PROSES FOR VEHICLE %s  \r\n",$rowvehicle[$x]->vehicle_no); 
                        printf("===========================================================\r\n");       
                    }
    
                }
                else
                {
                    printf("SKIP VEHICLE ( NO VEHICLE WEB SOCKET ) \r\n");
                    printf("============================================ \r\n");       
                }
            }
            else
            {
                printf("SKIP VEHICLE ( NO VEHICLE INFO ) \r\n");
                printf("============================================ \r\n");    
            }
            
        }
        
        if ($name != "" && $host != "")
        {
            $this->db->where("vehicle_device", $name.'@'.$host);    
        }
        $this->db->where("user_company >",0);
        $this->db->join("user", "vehicle_user_id = user_id", "left outer");
        $this->db->where("vehicle_status <>", 3);
        $q = $this->db->get("vehicle");
        $rowvehicle = $q->result();
        
                
        $total_process = count($rowvehicle);
        printf("TOTAL PROSES VEHICLE : %s \r\n",$total_process);
        printf("============================================ \r\n");
        
        for($x=0;$x<count($rowvehicle);$x++)
        {
            printf("PROSES VEHICLE : %s (%d/%d) \r\n",$rowvehicle[$x]->vehicle_device, ++$z, $total_process);
            
            if (isset($rowvehicle[$x]->vehicle_info))
            {
                $json = json_decode($rowvehicle[$x]->vehicle_info);
                if (isset($json->vehicle_ws))
                {
                    $objPHPExcel = new PHPExcel();
                    $objPHPExcel->getProperties()->setTitle("History Report");
                    $objPHPExcel->getProperties()->setSubject("History Report");
                    $objPHPExcel->getProperties()->setDescription("History Report");
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
                    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(55);            
                    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(12);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(12);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(12);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10);

                    //Header
                    $objPHPExcel->setActiveSheetIndex(0);
                    $objPHPExcel->getActiveSheet()->mergeCells('A1:I1');
                    $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'HISTORY REPORT');
                    $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
                    $objPHPExcel->getActiveSheet()->getStyle('B3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    $objPHPExcel->getActiveSheet()->getStyle('B3')->getFont()->setBold(true);
            
                    $objPHPExcel->getActiveSheet()->getStyle('J3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    $objPHPExcel->getActiveSheet()->getStyle('J3')->getFont()->setBold(true);
                    $objPHPExcel->getActiveSheet()->SetCellValue('A5', 'No');
                    $objPHPExcel->getActiveSheet()->SetCellValue('B5', 'Date');
                    $objPHPExcel->getActiveSheet()->SetCellValue('C5', 'Time');
                    $objPHPExcel->getActiveSheet()->SetCellValue('D5', 'Location');
                    $objPHPExcel->getActiveSheet()->SetCellValue('E5', 'Coordinate');
                    $objPHPExcel->getActiveSheet()->SetCellValue('F5', 'Speed (Kph)');
                    $objPHPExcel->getActiveSheet()->SetCellValue('G5', 'Engine');
                    $objPHPExcel->getActiveSheet()->SetCellValue('H5', 'Status');
                    $objPHPExcel->getActiveSheet()->SetCellValue('I5', 'Odometer (km)');
                    $objPHPExcel->getActiveSheet()->getStyle('A5:I5')->getFont()->setBold(true);
                    $objPHPExcel->getActiveSheet()->getStyle('A5:I5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    
                    //cek apakah sudah pernah ada filenya
                    $this->dbtrans->select("autoreport_vehicle_id");
                    $this->dbtrans->where("autoreport_vehicle_id",$rowvehicle[$x]->vehicle_id);
                    $this->dbtrans->where("autoreport_data_startdate",$startdate);
                    $this->dbtrans->limit(1);
                    $qrpt = $this->dbtrans->get("autoreport_history");
                    $rrpt = $qrpt->row();
                    
                    if (count($rrpt)>0)
                    {
                        printf("VEHICLE %s SUDAH PERNAH DI PROSES, HAPUS DULU DATA SEBELUMNYA \r\n", $rowvehicle[$x]->vehicle_device);     
                    }
                    else
                    {
                        $vehicle_device = explode("@", $rowvehicle[$x]->vehicle_device);  
                        $vehicle_no = $rowvehicle[$x]->vehicle_no;
                
                        $ex_vno = explode("/",$vehicle_no);
                        $name = $vehicle_device[0];
                        $host = $vehicle_device[1];
            
                        if ($rowvehicle[$x]->vehicle_type == "T5" || $rowvehicle[$x]->vehicle_type == "T5 PULSE")
                        {
                            $tables = $name."@t5_gps";
                            $tablesinfo = $name."@t5_info";       
                        }
                        else
                        {
                            $tables = strtolower($name)."@".strtolower($host)."_gps";
                            $tablesinfo = strtolower($name)."@".strtolower($host)."_info";
                        }
            
                        //$report_path = "/home/lacakmobil/public_html/transporter.lacak-mobil.com/public/assets/media/autoreport/".
                        $report_path = "/home/lacakmobil/public_html/transporter.lacak-mobil.com/public/assets/media/autoreport/".
						$report_type."/".$year."/".$month."/".$vehicle_device[0]."/";
            
                        printf("GET DATA GPS VEHICLE : %s \r\n",$rowvehicle[$x]->vehicle_no);
						$this->dbhist->select("gps_id, gps_time, gps_speed, gps_longitude, gps_ew, gps_latitude, gps_ns, gps_status");
						$this->dbhist->order_by("gps_time", $order);
						$this->dbhist->where("gps_name", $name);
						$this->dbhist->where("gps_host", $host);
						$this->dbhist->where("gps_time >=", $sdate);
						$this->dbhist->where("gps_time <=", $edate);
						$q = $this->dbhist->get($tables);
						$rows1 = $q->result();
						
						$this->dbhist2->select("gps_id, gps_time, gps_speed, gps_longitude, gps_ew, gps_latitude, gps_ns, gps_status");
						$this->dbhist2->order_by("gps_time", $order);
						$this->dbhist2->where("gps_name", $name);
						$this->dbhist2->where("gps_host", $host);
						$this->dbhist2->where("gps_time >=", $sdate);
						$this->dbhist2->where("gps_time <=", $edate);
						$q = $this->dbhist2->get($tables);
						$rows2 = $q->result();
				
						$rows = array_merge($rows1, $rows2);
            
						printf("GET DATA INFO VEHICLE : %s \r\n",$rowvehicle[$x]->vehicle_no);
						$this->dbhist->order_by("gps_info_time", $order);
						$this->dbhist->select("gps_info_io_port, gps_info_time, gps_info_distance, gps_info_alarm_alert, gps_info_alarm_data, gps_info_ad_input");
						$this->dbhist->where("gps_info_device", $name."@".$host);
						$this->dbhist->where("gps_info_time >=", $sdate);
						$this->dbhist->where("gps_info_time <=", $edate);
						$this->dbhist->limit(1);
						$qinfo = $this->dbhist->get($tablesinfo);
						$rowlastinfos1 = $qinfo->result();
							
						$this->dbhist->order_by("gps_info_time", $order);
						$this->dbhist->select("gps_info_io_port, gps_info_time, gps_info_distance, gps_info_alarm_alert, gps_info_alarm_data, gps_info_ad_input");
						$this->dbhist->where("gps_info_device", $name."@".$host);
						$this->dbhist->where("gps_info_time >=", $sdate);
						$this->dbhist->where("gps_info_time <=", $edate);
						$this->dbhist->limit(1);
						$qinfo = $this->dbhist->get($tablesinfo);
						$rowlastinfos2 = $qinfo->result();
							
						$rowlastinfos = array_merge($rowlastinfos1, $rowlastinfos2);
						
                        //Summary Saja yang diambil
                        if ($datatype == 0)
                        {
                            for($i=count($rows)-1; $i >= 0; $i--)
                            {
                                if (($i+1) >= count($rows))
                                {
                                    $rowsummary[] = $rows[$i];
                                    continue;
                                }
                                
                                $latbefore = getLatitude($rows[$i+1]->gps_latitude, $rows[$i+1]->gps_ns);
                                $lngbefore = getLongitude($rows[$i+1]->gps_longitude, $rows[$i+1]->gps_ew);
                
                                $latcurrent = getLatitude($rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                $lngcurrent = getLongitude($rows[$i]->gps_longitude, $rows[$i]->gps_ew);
                                
                                if (sprintf("%.4f,%.4f", $latbefore, $lngbefore) != sprintf("%.4f,%.4f", $latcurrent, $lngcurrent))
                                {
                                    $rowsummary[] = $rows[$i];
                                    continue;
                                }

                                if ($rows[$i+1]->gps_speed != $rows[$i]->gps_speed)
                                {
                                    $rowsummary[] = $rows[$i];
                                    continue;
                                }
                            }
                            
                            $rows = array();
                            $total = 0;
                            
                            if (isset($rowsummary))
                            {
                                $rowsummary = array_reverse($rowsummary);            
                                $total = count($rowsummary);
                                $rows = array_splice($rowsummary, $offset, $limit);                            
                            }   
                        }
                        //Finis Summary
            
                        unset($map_params);
                        $ismove = false;
                        $lastcoord = false;
                        
                        printf("PROSES XLS FILE FOR : %s  \r\n",$rowvehicle[$x]->vehicle_no);
                        
                        for($i=0; $i < count($rows); $i++)
                        {
                            printf("|%s|",$i);
                            if ($i == 0)
                            {
                                $this->dbhist->order_by("gps_info_time", $order);
                                $this->dbhist->select("gps_info_io_port, gps_info_time, gps_info_distance, gps_info_alarm_alert, gps_info_alarm_data, gps_info_ad_input");
                                $this->dbhist->where("gps_info_device", $name."@".$host);
                                $this->dbhist->where("gps_info_time >=", $sdate);
                                $this->dbhist->where("gps_info_time <=", $edate);
								$this->dbhist->limit($limit);
                                $qinfos = $this->dbhist->get($tablesinfo);   
                                $rowinfos = $qinfos->result();
                                $this->db->cache_delete_all();
                                
                                for($j=0; $j < count($rowinfos); $j++)
                                {
                                    $infos[dbmaketime($rowinfos[$j]->gps_info_time)] = $rowinfos[$j];
                                }
                            }
                            
                            $rows[$i]->gps_timestamp = dbmaketime($rows[$i]->gps_time);
                            $rows[$i]->gps_longitude_real = getLongitude($rows[$i]->gps_longitude, $rows[$i]->gps_ew);
                            $rows[$i]->gps_latitude_real = getLatitude($rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                            $rows[$i]->gps_longitude_real_fmt = number_format($rows[$i]->gps_longitude_real, 4, ".", "");
                            $rows[$i]->gps_latitude_real_fmt = number_format($rows[$i]->gps_latitude_real, 4, ".", "");        
            
                            if ($i == 0)
                            {
                                $lastcoord = array($rows[$i]->gps_longitude_real_fmt, $rows[$i]->gps_latitude_real_fmt);
                            }
                            else
                            {
                                if (($lastcoord[0] != $rows[$i]->gps_longitude_real_fmt) || ($lastcoord[1] != $rows[$i]->gps_latitude_real_fmt))
                                {
                                    $ismove = true;
                                }
                            }
                
                            $rows[$i]->gps_date_fmt = date("d/m/Y", $rows[$i]->gps_timestamp+7*3600);
                            $rows[$i]->gps_time_fmt = date("H:i:s", $rows[$i]->gps_timestamp+7*3600);
                            $rows[$i]->gps_speed_fmt = number_format($rows[$i]->gps_speed*1.852, 0, "", ".");
                            $rows[$i]->gps_status = ($rows[$i]->gps_status == "A") ? "OK" : "NOT OK";
            
                            if (isset($infos[$rows[$i]->gps_timestamp]))
                            {
                                $ioport = $infos[$rows[$i]->gps_timestamp]->gps_info_io_port;
                                $rows[$i]->status1 = ((strlen($ioport) > 4) && ($ioport[4] == 1)) ? $this->lang->line('lon') : $this->lang->line('loff');
                                $rows[$i]->odometer = number_format(round(($infos[$rows[$i]->gps_timestamp]->gps_info_distance+$rowvehicle[$x]->vehicle_odometer*1000)/1000), 0, "", ","); 
                            }
                            else
                            {
                                $rows[$i]->status1 = "-";
                                $rows[$i]->odometer = "-";
                            }
                
                            $rows[$i]->georeverse = $this->gpsmodel->GeoReverse($rows[$i]->gps_latitude_real_fmt, $rows[$i]->gps_longitude_real_fmt);            
                            $rows[$i]->gpsindex = $i+1;
                            $rows[$i]->gpsdate = date("d/m/Y", $rows[$i]->gps_timestamp+7*3600);
                            $rows[$i]->gpstime = date("H:i:s", $rows[$i]->gps_timestamp+7*3600);
                            $rows[$i]->gpsaddress = $rows[$i]->georeverse->display_name;
                            $rows[$i]->gpscoord = "(".$rows[$i]->gps_longitude_real_fmt." ".$rows[$i]->gps_latitude_real_fmt.")";
                            $rows[$i]->gpstatus = (($rows[$i]->gps_status == "V") ? "NOT OK" : "OK");
                            $map_params[] = array($rows[$i]->gps_longitude_real_fmt, $rows[$i]->gps_latitude_real_fmt);
            
                            $objPHPExcel->getActiveSheet()->SetCellValue('A'.(6+$i), $i+1);
                            $objPHPExcel->getActiveSheet()->SetCellValue('B'.(6+$i), date("d/m/Y", $rows[$i]->gps_timestamp+7*3600));
                            $objPHPExcel->getActiveSheet()->SetCellValue('C'.(6+$i), date("H:i:s", $rows[$i]->gps_timestamp+7*3600));
                            $objPHPExcel->getActiveSheet()->SetCellValue('D'.(6+$i), $rows[$i]->georeverse->display_name);
                            $objPHPExcel->getActiveSheet()->SetCellValue('E'.(6+$i), $rows[$i]->gps_latitude_real_fmt .",".$rows[$i]->gps_longitude_real_fmt);
                            $objPHPExcel->getActiveSheet()->SetCellValue('F'.(6+$i), $rows[$i]->gps_speed_fmt);
                            $objPHPExcel->getActiveSheet()->SetCellValue('G'.(6+$i), $rows[$i]->status1);
                            $objPHPExcel->getActiveSheet()->SetCellValue('H'.(6+$i), ($rows[$i]->gps_status == "V") ? "NOT OK" : "OK"); 
                            $objPHPExcel->getActiveSheet()->SetCellValue('I'.(6+$i), $rows[$i]->odometer);
                
                            $styleArray = array(
                                'borders' => array(
                                'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                            )
                            );
                        
                            $objPHPExcel->getActiveSheet()->getStyle('A5:I'.(5+$i))->applyFromArray($styleArray);
                            $objPHPExcel->getActiveSheet()->getStyle('A5:I'.(5+$i))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
                            $objPHPExcel->getActiveSheet()->getStyle('A5:I'.(5+$i))->getAlignment()->setWrapText(true);
                        } 
            
                        // Save Excel
                        $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
                        @mkdir($report_path, DIR_WRITE_MODE); //REPORT_PATH -> config->constant
                
                        if (isset($ex_vno))
                        {
                            if (isset($ex_vno[1]))
                            {
                                $filecreatedname = "HistoryReport_".$ex_vno[0].$ex_vno[1]."_".$datefilename.".xls";    
                            }
                            else
                            {
                                $filecreatedname = "HistoryReport_".$ex_vno[0]."_".$datefilename.".xls";       
                            }
                        }
                        else
                        {
                            $filecreatedname = "HistoryReport_".$vehicle_no."_".$datefilename.".xls";  
                        }
                
                        printf("SAVE XLS FILE FOR : %s  \r\n",$filecreatedname); 
                        $objWriter->save($report_path.$filecreatedname);
                
                        printf("DELETE CACHE GPS \r\n"); 
                        $this->db->cache_delete_all();
                        $this->dbhist->cache_delete_all();
                
                        printf("INSERT TO DB : %s  \r\n",$rowvehicle[$x]->vehicle_no);
                        unset($datainsert);
                        $datainsert["autoreport_vehicle_id"] = $rowvehicle[$x]->vehicle_id;
                        $datainsert["autoreport_vehicle_device"] = $rowvehicle[$x]->vehicle_device;
                        $datainsert["autoreport_vehicle_name"] = $rowvehicle[$x]->vehicle_name;
                        $datainsert["autoreport_vehicle_no"] = $rowvehicle[$x]->vehicle_no;
                        $datainsert["autoreport_user_id"] = $rowvehicle[$x]->user_id;
                        $datainsert["autoreport_company"] = $rowvehicle[$x]->user_company;
                        $datainsert["autoreport_data_startdate"] = $startdate;
                        $datainsert["autoreport_data_enddate"] = $enddate;
                        $datainsert["autoreport_type"] = $report_type;
                        $datainsert["autoreport_file_path"] = $report_path;
                        $datainsert["autoreport_filename"] = $filecreatedname;
                        $datainsert["autoreport_download_path"] = $report_path.$filecreatedname;
                        $datainsert["autoreport_process_date"] = $process_date; 
                        $datainsert["autoreport_insert_db"] = date("Y-m-d H:i:s");
                        $this->dbtrans->insert("autoreport_history",$datainsert);
                
                        printf("INSERT OK \r\n");
                
                        printf("FINIS PROSES FOR VEHICLE %s  \r\n",$rowvehicle[$x]->vehicle_no); 
                        printf("===========================================================\r\n");       
                    }
    
                }
                else
                {
                    printf("SKIP VEHICLE ( NO VEHICLE WEB SOCKET ) \r\n");
                    printf("============================================ \r\n");       
                }
            }
            else
            {
                printf("SKIP VEHICLE ( NO VEHICLE INFO ) \r\n");
                printf("============================================ \r\n");    
            }
            
        }
        
        unset($datalog);
        $datalog["cron_name"] = "History To Excel";
        $datalog["cron_start_data"] = $startdate;
        $datalog["cron_end_data"] = $enddate;
        $datalog["cron_start_proses"] = $startproses;
        $datalog["cron_end_proses"] = date("Y-m-d H:i:s");
        $this->dbtrans->insert("log_cronjobs",$datalog);
        
        $this->db->cache_delete_all();
        $this->dbhist->cache_delete_all();
        $this->dbtrans->cache_delete_all();
        printf("FINISH  \r\n");
        exit;
        
    }
    
    function history_pervehicle($datatype=0, $startdate="", $enddate="", $name="", $host="")
    {
        printf("PROSES AUTO REPORT HISTORY START \r\n");
        $startproses = date("Y-m-d H:i:s");
        
        include 'class/PHPExcel.php';
        include 'class/PHPExcel/Writer/Excel2007.php';

        $dbname = "gpshistory";
        $order = "desc";
        $limit = "700";
        $offset = 0;
        $z = 0;
        
        $totalodometer = 0;
        $totalodometer1 = 0;
        $process_date = date("Y-m-d H:i:s");
        $this->dbhist = $this->load->database($dbname, TRUE);
		$this->dbhist2 = $this->load->database("gpshistory2", TRUE);
        $this->dbtrans = $this->load->database("transporter",true);
        
        $report_type = "history_report";
        
        if ($startdate == "") 
        {
            $startdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
            $datefilename = date("Ymd", strtotime("yesterday")); 
        }
        
        if ($startdate != "")
        {
            $datefilename = date("Ymd", strtotime($startdate));     
            $startdate = date("Y-m-d 00:00:00", strtotime($startdate));
        }
        
        if ($enddate != "")
        {
            $enddate = date("Y-m-d 23:59:59", strtotime($enddate));
        }
        
        if ($enddate == "") 
        {
            $enddate = date("Y-m-d 23:59:59", strtotime("yesterday"));
        }
        
        $month = date("F", strtotime($startdate));
        $year = date("Y", strtotime($startdate));
        
        $sdate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($startdate)));
        $edate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($enddate)));
        
        if ($name != "" && $host != "")
        {
            $this->db->where("vehicle_device", $name.'@'.$host);    
        }                                                                  
        
        $this->db->join("user", "vehicle_user_id = user_id", "left outer");
        $this->db->where("vehicle_status <>", 3);
        $q = $this->db->get("vehicle");
        $rowvehicle = $q->result();
        
        $total_process = count($rowvehicle);
        printf("TOTAL PROSES VEHICLE : %s \r\n",$total_process);
        printf("============================================ \r\n");
        
        for($x=0;$x<count($rowvehicle);$x++)
        {
                    printf("PROSES VEHICLE : %s (%d/%d) \r\n",$rowvehicle[$x]->vehicle_device, ++$z, $total_process);
                    $objPHPExcel = new PHPExcel();
                    $objPHPExcel->getProperties()->setTitle("History Report");
                    $objPHPExcel->getProperties()->setSubject("History Report");
                    $objPHPExcel->getProperties()->setDescription("History Report");
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
                    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(55);            
                    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(12);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(12);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(12);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10);

                    //Header
                    $objPHPExcel->setActiveSheetIndex(0);
                    $objPHPExcel->getActiveSheet()->mergeCells('A1:I1');
                    $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'HISTORY REPORT');
                    $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
                    $objPHPExcel->getActiveSheet()->getStyle('B3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    $objPHPExcel->getActiveSheet()->getStyle('B3')->getFont()->setBold(true);
            
                    $objPHPExcel->getActiveSheet()->getStyle('J3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    $objPHPExcel->getActiveSheet()->getStyle('J3')->getFont()->setBold(true);
                    $objPHPExcel->getActiveSheet()->SetCellValue('A5', 'No');
                    $objPHPExcel->getActiveSheet()->SetCellValue('B5', 'Date');
                    $objPHPExcel->getActiveSheet()->SetCellValue('C5', 'Time');
                    $objPHPExcel->getActiveSheet()->SetCellValue('D5', 'Location');
                    $objPHPExcel->getActiveSheet()->SetCellValue('E5', 'Coordinate');
                    $objPHPExcel->getActiveSheet()->SetCellValue('F5', 'Speed (Kph)');
                    $objPHPExcel->getActiveSheet()->SetCellValue('G5', 'Engine');
                    $objPHPExcel->getActiveSheet()->SetCellValue('H5', 'Status');
                    $objPHPExcel->getActiveSheet()->SetCellValue('I5', 'Odometer (km)');
                    $objPHPExcel->getActiveSheet()->getStyle('A5:I5')->getFont()->setBold(true);
                    $objPHPExcel->getActiveSheet()->getStyle('A5:I5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    
                    //cek apakah sudah pernah ada filenya
                    $this->dbtrans->select("autoreport_vehicle_id");
                    $this->dbtrans->where("autoreport_vehicle_id",$rowvehicle[$x]->vehicle_id);
                    $this->dbtrans->where("autoreport_data_startdate",$startdate);
                    $this->dbtrans->limit(1);
                    $qrpt = $this->dbtrans->get("autoreport_history");
                    $rrpt = $qrpt->row();
                    
                    if (count($rrpt)>0)
                    {
                        printf("VEHICLE %s SUDAH PERNAH DI PROSES, HAPUS DULU DATA SEBELUMNYA \r\n", $rowvehicle[$x]->vehicle_device);     
                    }
                    else
                    {
                        $vehicle_device = explode("@", $rowvehicle[$x]->vehicle_device);  
                        $vehicle_no = $rowvehicle[$x]->vehicle_no;
                
                        $ex_vno = explode("/",$vehicle_no);
                        $name = $vehicle_device[0];
                        $host = $vehicle_device[1];
            
                        if ($rowvehicle[$x]->vehicle_type == "T5" || $rowvehicle[$x]->vehicle_type == "T5 PULSE")
                        {
                            $tables = $name."@t5_gps";
                            $tablesinfo = $name."@t5_info";       
                        }
                        else
                        {
                            $tables = strtolower($name)."@".strtolower($host)."_gps";
                            $tablesinfo = strtolower($name)."@".strtolower($host)."_info";
                        }
            
                        //$report_path = "/home/lacakmobil/public_html/transporter.lacak-mobil.com/public/assets/media/autoreport/".
                        $report_path = "/home/lacakmobil/public_html/transporter.lacak-mobil.com/public/assets/media/autoreport/".
						$report_type."/".$year."/".$month."/".$vehicle_device[0]."/";
            
                        printf("GET DATA GPS VEHICLE : %s \r\n",$rowvehicle[$x]->vehicle_no);
                        $this->dbhist->select("gps_id, gps_time, gps_speed, gps_longitude, gps_ew, gps_latitude, gps_ns, gps_status");
                        $this->dbhist->order_by("gps_time", $order);
                        $this->dbhist->where("gps_name", $name);
                        $this->dbhist->where("gps_host", $host);
                        $this->dbhist->where("gps_time >=", $sdate);
                        $this->dbhist->where("gps_time <=", $edate);
                        $q = $this->dbhist->get($tables);
                        $rows1 = $q->result();
						
						$this->dbhist2->select("gps_id, gps_time, gps_speed, gps_longitude, gps_ew, gps_latitude, gps_ns, gps_status");
                        $this->dbhist2->order_by("gps_time", $order);
                        $this->dbhist2->where("gps_name", $name);
                        $this->dbhist2->where("gps_host", $host);
                        $this->dbhist2->where("gps_time >=", $sdate);
                        $this->dbhist2->where("gps_time <=", $edate);
                        $q = $this->dbhist2->get($tables);
                        $rows2 = $q->result();
						
						$rows = array_merge($rows1, $rows2);
            
                        printf("GET DATA INFO VEHICLE : %s \r\n",$rowvehicle[$x]->vehicle_no);
                        $this->dbhist->order_by("gps_info_time", $order);
                        $this->dbhist->select("gps_info_io_port, gps_info_time, gps_info_distance, gps_info_alarm_alert, gps_info_alarm_data, gps_info_ad_input");
                        $this->dbhist->where("gps_info_device", $name."@".$host);
                        $this->dbhist->where("gps_info_time >=", $sdate);
                        $this->dbhist->where("gps_info_time <=", $edate);
                        $this->dbhist->limit(1);
                        $qinfo = $this->dbhist->get($tablesinfo);
                        $rowlastinfos1 = $qinfo->result();
						
						$this->dbhist->order_by("gps_info_time", $order);
                        $this->dbhist->select("gps_info_io_port, gps_info_time, gps_info_distance, gps_info_alarm_alert, gps_info_alarm_data, gps_info_ad_input");
                        $this->dbhist->where("gps_info_device", $name."@".$host);
                        $this->dbhist->where("gps_info_time >=", $sdate);
                        $this->dbhist->where("gps_info_time <=", $edate);
                        $this->dbhist->limit(1);
                        $qinfo = $this->dbhist->get($tablesinfo);
                        $rowlastinfos2 = $qinfo->result();
						
						$rowlastinfos = array_merge($rowlastinfos1, $rowlastinfos2);
                        
                        //Summary Saja yang diambil
                        if ($datatype == 2)
                        {
                            for($i=count($rows)-1; $i >= 0; $i--)
                            {
                                if (($i+1) >= count($rows))
                                {
                                    $rowsummary[] = $rows[$i];
                                    continue;
                                }
                                
                                $latbefore = getLatitude($rows[$i+1]->gps_latitude, $rows[$i+1]->gps_ns);
                                $lngbefore = getLongitude($rows[$i+1]->gps_longitude, $rows[$i+1]->gps_ew);
                
                                $latcurrent = getLatitude($rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                $lngcurrent = getLongitude($rows[$i]->gps_longitude, $rows[$i]->gps_ew);
                                
                                if (sprintf("%.4f,%.4f", $latbefore, $lngbefore) != sprintf("%.4f,%.4f", $latcurrent, $lngcurrent))
                                {
                                    $rowsummary[] = $rows[$i];
                                    continue;
                                }

                                if ($rows[$i+1]->gps_speed != $rows[$i]->gps_speed)
                                {
                                    $rowsummary[] = $rows[$i];
                                    continue;
                                }
                            }
                            
                            $rows = array();
                            $total = 0;
                            
                            if (isset($rowsummary))
                            {
                                $rowsummary = array_reverse($rowsummary);            
                                $total = count($rowsummary);
                                $rows = array_splice($rowsummary, $offset, $limit);                            
                            }   
                        }
                        //Finis Summary
            
                        unset($map_params);
                        $ismove = false;
                        $lastcoord = false;
                        
                        printf("PROSES XLS FILE FOR : %s  \r\n",$rowvehicle[$x]->vehicle_no);
                        
                        for($i=0; $i < count($rows); $i++)
                        {
                            printf("|%s|",$i);
                            if ($i == 0)
                            {
                                $this->dbhist->order_by("gps_info_time", $order);
                                $this->dbhist->select("gps_info_io_port, gps_info_time, gps_info_distance, gps_info_alarm_alert, gps_info_alarm_data, gps_info_ad_input");
                                $this->dbhist->where("gps_info_device", $name."@".$host);
                                $this->dbhist->where("gps_info_time >=", $sdate);
                                $this->dbhist->where("gps_info_time <=", $edate);
                                $qinfos = $this->dbhist->get($tablesinfo);   
                                $rowinfos = $qinfos->result();
                                $this->db->cache_delete_all();
                                
                                for($j=0; $j < count($rowinfos); $j++)
                                {
                                    $infos[dbmaketime($rowinfos[$j]->gps_info_time)] = $rowinfos[$j];
                                }
                            }
                            
                            $rows[$i]->gps_timestamp = dbmaketime($rows[$i]->gps_time);
                            $rows[$i]->gps_longitude_real = getLongitude($rows[$i]->gps_longitude, $rows[$i]->gps_ew);
                            $rows[$i]->gps_latitude_real = getLatitude($rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                            $rows[$i]->gps_longitude_real_fmt = number_format($rows[$i]->gps_longitude_real, 4, ".", "");
                            $rows[$i]->gps_latitude_real_fmt = number_format($rows[$i]->gps_latitude_real, 4, ".", "");        
            
                            if ($i == 0)
                            {
                                $lastcoord = array($rows[$i]->gps_longitude_real_fmt, $rows[$i]->gps_latitude_real_fmt);
                            }
                            else
                            {
                                if (($lastcoord[0] != $rows[$i]->gps_longitude_real_fmt) || ($lastcoord[1] != $rows[$i]->gps_latitude_real_fmt))
                                {
                                    $ismove = true;
                                }
                            }
                
                            $rows[$i]->gps_date_fmt = date("d/m/Y", $rows[$i]->gps_timestamp+7*3600);
                            $rows[$i]->gps_time_fmt = date("H:i:s", $rows[$i]->gps_timestamp+7*3600);
                            $rows[$i]->gps_speed_fmt = number_format($rows[$i]->gps_speed*1.852, 0, "", ".");
                            $rows[$i]->gps_status = ($rows[$i]->gps_status == "A") ? "OK" : "NOT OK";
            
                            if (isset($infos[$rows[$i]->gps_timestamp]))
                            {
                                $ioport = $infos[$rows[$i]->gps_timestamp]->gps_info_io_port;
                                $rows[$i]->status1 = ((strlen($ioport) > 4) && ($ioport[4] == 1)) ? $this->lang->line('lon') : $this->lang->line('loff');
                                $rows[$i]->odometer = number_format(round(($infos[$rows[$i]->gps_timestamp]->gps_info_distance+$rowvehicle[$x]->vehicle_odometer*1000)/1000), 0, "", ","); 
                            }
                            else
                            {
                                $rows[$i]->status1 = "-";
                                $rows[$i]->odometer = "-";
                            }
                
                            $rows[$i]->georeverse = $this->gpsmodel->GeoReverse($rows[$i]->gps_latitude_real_fmt, $rows[$i]->gps_longitude_real_fmt);            
                            $rows[$i]->gpsindex = $i+1;
                            $rows[$i]->gpsdate = date("d/m/Y", $rows[$i]->gps_timestamp+7*3600);
                            $rows[$i]->gpstime = date("H:i:s", $rows[$i]->gps_timestamp+7*3600);
                            $rows[$i]->gpsaddress = $rows[$i]->georeverse->display_name;
                            $rows[$i]->gpscoord = "(".$rows[$i]->gps_longitude_real_fmt." ".$rows[$i]->gps_latitude_real_fmt.")";
                            $rows[$i]->gpstatus = (($rows[$i]->gps_status == "V") ? "NOT OK" : "OK");
                            $map_params[] = array($rows[$i]->gps_longitude_real_fmt, $rows[$i]->gps_latitude_real_fmt);
            
                            $objPHPExcel->getActiveSheet()->SetCellValue('A'.(6+$i), $i+1);
                            $objPHPExcel->getActiveSheet()->SetCellValue('B'.(6+$i), date("d/m/Y", $rows[$i]->gps_timestamp+7*3600));
                            $objPHPExcel->getActiveSheet()->SetCellValue('C'.(6+$i), date("H:i:s", $rows[$i]->gps_timestamp+7*3600));
                            $objPHPExcel->getActiveSheet()->SetCellValue('D'.(6+$i), $rows[$i]->georeverse->display_name);
                            $objPHPExcel->getActiveSheet()->SetCellValue('E'.(6+$i), $rows[$i]->gps_latitude_real_fmt .",".$rows[$i]->gps_longitude_real_fmt);
                            $objPHPExcel->getActiveSheet()->SetCellValue('F'.(6+$i), $rows[$i]->gps_speed_fmt);
                            $objPHPExcel->getActiveSheet()->SetCellValue('G'.(6+$i), $rows[$i]->status1);
                            $objPHPExcel->getActiveSheet()->SetCellValue('H'.(6+$i), ($rows[$i]->gps_status == "V") ? "NOT OK" : "OK"); 
                            $objPHPExcel->getActiveSheet()->SetCellValue('I'.(6+$i), $rows[$i]->odometer);
                
                            $styleArray = array(
                                'borders' => array(
                                'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                            )
                            );
                        
                            $objPHPExcel->getActiveSheet()->getStyle('A5:I'.(5+$i))->applyFromArray($styleArray);
                            $objPHPExcel->getActiveSheet()->getStyle('A5:I'.(5+$i))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
                            $objPHPExcel->getActiveSheet()->getStyle('A5:I'.(5+$i))->getAlignment()->setWrapText(true);
                        } 
            
                        // Save Excel
                        $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
                        @mkdir($report_path, DIR_WRITE_MODE); //REPORT_PATH -> config->constant
                
                        if (isset($ex_vno))
                        {
                            if (isset($ex_vno[1]))
                            {
                                $filecreatedname = "HistoryReport_".$ex_vno[0].$ex_vno[1]."_".$datefilename.".xls";    
                            }
                            else
                            {
                                $filecreatedname = "HistoryReport_".$ex_vno[0]."_".$datefilename.".xls";       
                            }
                        }
                        else
                        {
                            $filecreatedname = "HistoryReport_".$vehicle_no."_".$datefilename.".xls";  
                        }
                
                        printf("SAVE XLS FILE FOR : %s  \r\n",$filecreatedname); 
                        $objWriter->save($report_path.$filecreatedname);
                
                        printf("DELETE CACHE GPS \r\n"); 
                        $this->db->cache_delete_all();
                        $this->dbhist->cache_delete_all();
                
                        printf("INSERT TO DB : %s  \r\n",$rowvehicle[$x]->vehicle_no);
                        unset($datainsert);
                        $datainsert["autoreport_vehicle_id"] = $rowvehicle[$x]->vehicle_id;
                        $datainsert["autoreport_vehicle_device"] = $rowvehicle[$x]->vehicle_device;
                        $datainsert["autoreport_vehicle_name"] = $rowvehicle[$x]->vehicle_name;
                        $datainsert["autoreport_vehicle_no"] = $rowvehicle[$x]->vehicle_no;
                        $datainsert["autoreport_user_id"] = $rowvehicle[$x]->user_id;
                        $datainsert["autoreport_company"] = $rowvehicle[$x]->user_company;
                        $datainsert["autoreport_data_startdate"] = $startdate;
                        $datainsert["autoreport_data_enddate"] = $enddate;
                        $datainsert["autoreport_type"] = $report_type;
                        $datainsert["autoreport_file_path"] = $report_path;
                        $datainsert["autoreport_filename"] = $filecreatedname;
                        $datainsert["autoreport_download_path"] = $report_path.$filecreatedname;
                        $datainsert["autoreport_process_date"] = $process_date; 
                        $datainsert["autoreport_insert_db"] = date("Y-m-d H:i:s");
                        $this->dbtrans->insert("autoreport_history",$datainsert);
                
                        printf("INSERT OK \r\n");
                
                        printf("FINIS PROSES FOR VEHICLE %s  \r\n",$rowvehicle[$x]->vehicle_no); 
                        printf("===========================================================\r\n");       
                    }
        }
        
        unset($datalog);
        $datalog["cron_name"] = "History To Excel";
        $datalog["cron_start_data"] = $startdate;
        $datalog["cron_end_data"] = $enddate;
        $datalog["cron_start_proses"] = $startproses;
        $datalog["cron_end_proses"] = date("Y-m-d H:i:s");
        $this->dbtrans->insert("log_cronjobs",$datalog);
        
        $this->db->cache_delete_all();
        $this->dbhist->cache_delete_all();
        $this->dbtrans->cache_delete_all();
        printf("FINISH  \r\n");
        exit;
        
    }
    
	function history_pervehicle_test($datatype=0, $startdate="", $enddate="", $name="", $host="")
    {
        printf("PROSES AUTO REPORT HISTORY START \r\n");
        $startproses = date("Y-m-d H:i:s");
        
        include 'class/PHPExcel.php';
        include 'class/PHPExcel/Writer/Excel2007.php';

        $dbname = "GPS_BALRICH_40033";
        $order = "desc";
        $limit = "700";
        $offset = 0;
        $z = 0;
        
        $totalodometer = 0;
        $totalodometer1 = 0;
        $process_date = date("Y-m-d H:i:s");
        $this->dbhist = $this->load->database($dbname, TRUE);
		$this->dbhist2 = $this->load->database("gpshistory", TRUE);
        $this->dbtrans = $this->load->database("transporter",true);
        
        $report_type = "history_report";
        
        if ($startdate == "") 
        {
            $startdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
            $datefilename = date("Ymd", strtotime("yesterday")); 
        }
        
        if ($startdate != "")
        {
            $datefilename = date("Ymd", strtotime($startdate));     
            $startdate = date("Y-m-d 00:00:00", strtotime($startdate));
        }
        
        if ($enddate != "")
        {
            $enddate = date("Y-m-d 23:59:59", strtotime($enddate));
        }
        
        if ($enddate == "") 
        {
            $enddate = date("Y-m-d 23:59:59", strtotime("yesterday"));
        }
        
        $month = date("F", strtotime($startdate));
        $year = date("Y", strtotime($startdate));
        
        $sdate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($startdate)));
        $edate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($enddate)));
        
        if ($name != "" && $host != "")
        {
            $this->db->where("vehicle_device", $name.'@'.$host);    
        }                                                                  
        
        $this->db->join("user", "vehicle_user_id = user_id", "left outer");
        $this->db->where("vehicle_status <>", 3);
        $q = $this->db->get("vehicle");
        $rowvehicle = $q->result();
        
        $total_process = count($rowvehicle);
        printf("TOTAL PROSES VEHICLE : %s \r\n",$total_process);
        printf("============================================ \r\n");
        
        for($x=0;$x<count($rowvehicle);$x++)
        {
                    printf("PROSES VEHICLE : %s (%d/%d) \r\n",$rowvehicle[$x]->vehicle_device, ++$z, $total_process);
                    $objPHPExcel = new PHPExcel();
                    $objPHPExcel->getProperties()->setTitle("History Report");
                    $objPHPExcel->getProperties()->setSubject("History Report");
                    $objPHPExcel->getProperties()->setDescription("History Report");
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
                    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(55);            
                    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(12);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(12);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(12);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10);

                    //Header
                    $objPHPExcel->setActiveSheetIndex(0);
                    $objPHPExcel->getActiveSheet()->mergeCells('A1:I1');
                    $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'HISTORY REPORT');
                    $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
                    $objPHPExcel->getActiveSheet()->getStyle('B3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    $objPHPExcel->getActiveSheet()->getStyle('B3')->getFont()->setBold(true);
            
                    $objPHPExcel->getActiveSheet()->getStyle('J3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    $objPHPExcel->getActiveSheet()->getStyle('J3')->getFont()->setBold(true);
                    $objPHPExcel->getActiveSheet()->SetCellValue('A5', 'No');
                    $objPHPExcel->getActiveSheet()->SetCellValue('B5', 'Date');
                    $objPHPExcel->getActiveSheet()->SetCellValue('C5', 'Time');
                    $objPHPExcel->getActiveSheet()->SetCellValue('D5', 'Location');
                    $objPHPExcel->getActiveSheet()->SetCellValue('E5', 'Coordinate');
                    $objPHPExcel->getActiveSheet()->SetCellValue('F5', 'Speed (Kph)');
                    $objPHPExcel->getActiveSheet()->SetCellValue('G5', 'Engine');
                    $objPHPExcel->getActiveSheet()->SetCellValue('H5', 'Status');
                    $objPHPExcel->getActiveSheet()->SetCellValue('I5', 'Odometer (km)');
                    $objPHPExcel->getActiveSheet()->getStyle('A5:I5')->getFont()->setBold(true);
                    $objPHPExcel->getActiveSheet()->getStyle('A5:I5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    
                    //cek apakah sudah pernah ada filenya
                    $this->dbtrans->select("autoreport_vehicle_id");
                    $this->dbtrans->where("autoreport_vehicle_id",$rowvehicle[$x]->vehicle_id);
                    $this->dbtrans->where("autoreport_data_startdate",$startdate);
                    $this->dbtrans->limit(1);
                    $qrpt = $this->dbtrans->get("autoreport_history");
                    $rrpt = $qrpt->row();
                    
                    if (count($rrpt)==0)
                    {
                        printf("VEHICLE %s SUDAH PERNAH DI PROSES, HAPUS DULU DATA SEBELUMNYA \r\n", $rowvehicle[$x]->vehicle_device);     
                    }
                    else
                    {
                        $vehicle_device = explode("@", $rowvehicle[$x]->vehicle_device);  
                        $vehicle_no = $rowvehicle[$x]->vehicle_no;
                
                        $ex_vno = explode("/",$vehicle_no);
                        $name = $vehicle_device[0];
                        $host = $vehicle_device[1];
            
                        if ($rowvehicle[$x]->vehicle_type == "T5" || $rowvehicle[$x]->vehicle_type == "T5 PULSE")
                        {
                            $tables = $name."@t5_gps";
                            $tablesinfo = $name."@t5_info";       
                        }
                        else
                        {
                            $tables = strtolower($name)."@".strtolower($host)."_gps";
                            $tablesinfo = strtolower($name)."@".strtolower($host)."_info";
                        }
            
                        $report_path = "/home/lacakmobil/public_html/transporter.lacak-mobil.com/public/assets/media/autoreport/".
                        $report_type."/".$year."/".$month."/".$vehicle_device[0]."/";
            
                        printf("GET DATA GPS VEHICLE : %s \r\n",$rowvehicle[$x]->vehicle_no);
                        $this->dbhist->select("gps_id, gps_time, gps_speed, gps_longitude, gps_ew, gps_latitude, gps_ns, gps_status");
                        $this->dbhist->order_by("gps_time", $order);
                        $this->dbhist->where("gps_name", $name);
                        $this->dbhist->where("gps_host", $host);
                        $this->dbhist->where("gps_time >=", $sdate);
                        $this->dbhist->where("gps_time <=", $edate);
                        $q = $this->dbhist->get("gps");
                        $rows1 = $q->result();
						
						$this->dbhist2->select("gps_id, gps_time, gps_speed, gps_longitude, gps_ew, gps_latitude, gps_ns, gps_status");
                        $this->dbhist2->order_by("gps_time", $order);
                        $this->dbhist2->where("gps_name", $name);
                        $this->dbhist2->where("gps_host", $host);
                        $this->dbhist2->where("gps_time >=", $sdate);
                        $this->dbhist2->where("gps_time <=", $edate);
                        $q = $this->dbhist2->get($tables);
                        $rows2 = $q->result();
						
						$rows = array_merge($rows1, $rows2);
            
                        printf("GET DATA INFO VEHICLE : %s \r\n",$rowvehicle[$x]->vehicle_no);
                        $this->dbhist->order_by("gps_info_time", $order);
                        $this->dbhist->select("gps_info_io_port, gps_info_time, gps_info_distance, gps_info_alarm_alert, gps_info_alarm_data, gps_info_ad_input");
                        $this->dbhist->where("gps_info_device", $name."@".$host);
                        $this->dbhist->where("gps_info_time >=", $sdate);
                        $this->dbhist->where("gps_info_time <=", $edate);
                        $this->dbhist->limit(1);
                        $qinfo = $this->dbhist->get("gps_info");
                        $rowlastinfos1 = $qinfo->result();
						
						$this->dbhist->order_by("gps_info_time", $order);
                        $this->dbhist->select("gps_info_io_port, gps_info_time, gps_info_distance, gps_info_alarm_alert, gps_info_alarm_data, gps_info_ad_input");
                        $this->dbhist->where("gps_info_device", $name."@".$host);
                        $this->dbhist->where("gps_info_time >=", $sdate);
                        $this->dbhist->where("gps_info_time <=", $edate);
                        $this->dbhist->limit(1);
                        $qinfo = $this->dbhist->get("gps_info");
                        $rowlastinfos2 = $qinfo->result();
						
						$rowlastinfos = array_merge($rowlastinfos1, $rowlastinfos2);
                        
                        //Summary Saja yang diambil
                        if ($datatype == 2)
                        {
                            for($i=count($rows)-1; $i >= 0; $i--)
                            {
                                if (($i+1) >= count($rows))
                                {
                                    $rowsummary[] = $rows[$i];
                                    continue;
                                }
                                
                                $latbefore = getLatitude($rows[$i+1]->gps_latitude, $rows[$i+1]->gps_ns);
                                $lngbefore = getLongitude($rows[$i+1]->gps_longitude, $rows[$i+1]->gps_ew);
                
                                $latcurrent = getLatitude($rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                $lngcurrent = getLongitude($rows[$i]->gps_longitude, $rows[$i]->gps_ew);
                                
                                if (sprintf("%.4f,%.4f", $latbefore, $lngbefore) != sprintf("%.4f,%.4f", $latcurrent, $lngcurrent))
                                {
                                    $rowsummary[] = $rows[$i];
                                    continue;
                                }

                                if ($rows[$i+1]->gps_speed != $rows[$i]->gps_speed)
                                {
                                    $rowsummary[] = $rows[$i];
                                    continue;
                                }
                            }
                            
                            $rows = array();
                            $total = 0;
                            
                            if (isset($rowsummary))
                            {
                                $rowsummary = array_reverse($rowsummary);            
                                $total = count($rowsummary);
                                $rows = array_splice($rowsummary, $offset, $limit);                            
                            }   
                        }
                        //Finis Summary
            
                        unset($map_params);
                        $ismove = false;
                        $lastcoord = false;
                        
                        printf("PROSES XLS FILE FOR : %s  \r\n",$rowvehicle[$x]->vehicle_no);
                        
                        for($i=0; $i < count($rows); $i++)
                        {
                            printf("|%s|",$i);
                            if ($i == 0)
                            {
                                $this->dbhist->order_by("gps_info_time", $order);
                                $this->dbhist->select("gps_info_io_port, gps_info_time, gps_info_distance, gps_info_alarm_alert, gps_info_alarm_data, gps_info_ad_input");
                                $this->dbhist->where("gps_info_device", $name."@".$host);
                                $this->dbhist->where("gps_info_time >=", $sdate);
                                $this->dbhist->where("gps_info_time <=", $edate);
                                $qinfos = $this->dbhist->get("gps_info");   
                                $rowinfos = $qinfos->result();
                                $this->db->cache_delete_all();
                                
                                for($j=0; $j < count($rowinfos); $j++)
                                {
                                    $infos[dbmaketime($rowinfos[$j]->gps_info_time)] = $rowinfos[$j];
                                }
                            }
                            
                            $rows[$i]->gps_timestamp = dbmaketime($rows[$i]->gps_time);
                            $rows[$i]->gps_longitude_real = getLongitude($rows[$i]->gps_longitude, $rows[$i]->gps_ew);
                            $rows[$i]->gps_latitude_real = getLatitude($rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                            $rows[$i]->gps_longitude_real_fmt = number_format($rows[$i]->gps_longitude_real, 4, ".", "");
                            $rows[$i]->gps_latitude_real_fmt = number_format($rows[$i]->gps_latitude_real, 4, ".", "");        
            
                            if ($i == 0)
                            {
                                $lastcoord = array($rows[$i]->gps_longitude_real_fmt, $rows[$i]->gps_latitude_real_fmt);
                            }
                            else
                            {
                                if (($lastcoord[0] != $rows[$i]->gps_longitude_real_fmt) || ($lastcoord[1] != $rows[$i]->gps_latitude_real_fmt))
                                {
                                    $ismove = true;
                                }
                            }
                
                            $rows[$i]->gps_date_fmt = date("d/m/Y", $rows[$i]->gps_timestamp+7*3600);
                            $rows[$i]->gps_time_fmt = date("H:i:s", $rows[$i]->gps_timestamp+7*3600);
                            $rows[$i]->gps_speed_fmt = number_format($rows[$i]->gps_speed*1.852, 0, "", ".");
                            $rows[$i]->gps_status = ($rows[$i]->gps_status == "A") ? "OK" : "NOT OK";
            
                            if (isset($infos[$rows[$i]->gps_timestamp]))
                            {
                                $ioport = $infos[$rows[$i]->gps_timestamp]->gps_info_io_port;
                                $rows[$i]->status1 = ((strlen($ioport) > 4) && ($ioport[4] == 1)) ? $this->lang->line('lon') : $this->lang->line('loff');
                                $rows[$i]->odometer = number_format(round(($infos[$rows[$i]->gps_timestamp]->gps_info_distance+$rowvehicle[$x]->vehicle_odometer*1000)/1000), 0, "", ","); 
                            }
                            else
                            {
                                $rows[$i]->status1 = "-";
                                $rows[$i]->odometer = "-";
                            }
                
                            $rows[$i]->georeverse = $this->gpsmodel->GeoReverse($rows[$i]->gps_latitude_real_fmt, $rows[$i]->gps_longitude_real_fmt);            
                            $rows[$i]->gpsindex = $i+1;
                            $rows[$i]->gpsdate = date("d/m/Y", $rows[$i]->gps_timestamp+7*3600);
                            $rows[$i]->gpstime = date("H:i:s", $rows[$i]->gps_timestamp+7*3600);
                            $rows[$i]->gpsaddress = $rows[$i]->georeverse->display_name;
                            $rows[$i]->gpscoord = "(".$rows[$i]->gps_longitude_real_fmt." ".$rows[$i]->gps_latitude_real_fmt.")";
                            $rows[$i]->gpstatus = (($rows[$i]->gps_status == "V") ? "NOT OK" : "OK");
                            $map_params[] = array($rows[$i]->gps_longitude_real_fmt, $rows[$i]->gps_latitude_real_fmt);
            
                            $objPHPExcel->getActiveSheet()->SetCellValue('A'.(6+$i), $i+1);
                            $objPHPExcel->getActiveSheet()->SetCellValue('B'.(6+$i), date("d/m/Y", $rows[$i]->gps_timestamp+7*3600));
                            $objPHPExcel->getActiveSheet()->SetCellValue('C'.(6+$i), date("H:i:s", $rows[$i]->gps_timestamp+7*3600));
                            $objPHPExcel->getActiveSheet()->SetCellValue('D'.(6+$i), $rows[$i]->georeverse->display_name);
                            $objPHPExcel->getActiveSheet()->SetCellValue('E'.(6+$i), $rows[$i]->gps_latitude_real_fmt .",".$rows[$i]->gps_longitude_real_fmt);
                            $objPHPExcel->getActiveSheet()->SetCellValue('F'.(6+$i), $rows[$i]->gps_speed_fmt);
                            $objPHPExcel->getActiveSheet()->SetCellValue('G'.(6+$i), $rows[$i]->status1);
                            $objPHPExcel->getActiveSheet()->SetCellValue('H'.(6+$i), ($rows[$i]->gps_status == "V") ? "NOT OK" : "OK"); 
                            $objPHPExcel->getActiveSheet()->SetCellValue('I'.(6+$i), $rows[$i]->odometer);
                
                            $styleArray = array(
                                'borders' => array(
                                'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                            )
                            );
                        
                            $objPHPExcel->getActiveSheet()->getStyle('A5:I'.(5+$i))->applyFromArray($styleArray);
                            $objPHPExcel->getActiveSheet()->getStyle('A5:I'.(5+$i))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
                            $objPHPExcel->getActiveSheet()->getStyle('A5:I'.(5+$i))->getAlignment()->setWrapText(true);
                        } 
            
                        // Save Excel
                        $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
                        @mkdir($report_path, DIR_WRITE_MODE); //REPORT_PATH -> config->constant
                
                        if (isset($ex_vno))
                        {
                            if (isset($ex_vno[1]))
                            {
                                $filecreatedname = "HistoryReport_".$ex_vno[0].$ex_vno[1]."_".$datefilename.".xls";    
                            }
                            else
                            {
                                $filecreatedname = "HistoryReport_".$ex_vno[0]."_".$datefilename.".xls";       
                            }
                        }
                        else
                        {
                            $filecreatedname = "HistoryReport_".$vehicle_no."_".$datefilename.".xls";  
                        }
                
                        printf("SAVE XLS FILE FOR : %s  \r\n",$filecreatedname); 
                        $objWriter->save($report_path.$filecreatedname);
                
                        printf("DELETE CACHE GPS \r\n"); 
                        $this->db->cache_delete_all();
                        $this->dbhist->cache_delete_all();
                
                        printf("INSERT TO DB : %s  \r\n",$rowvehicle[$x]->vehicle_no);
                        unset($datainsert);
                        $datainsert["autoreport_vehicle_id"] = $rowvehicle[$x]->vehicle_id;
                        $datainsert["autoreport_vehicle_device"] = $rowvehicle[$x]->vehicle_device;
                        $datainsert["autoreport_vehicle_name"] = $rowvehicle[$x]->vehicle_name;
                        $datainsert["autoreport_vehicle_no"] = $rowvehicle[$x]->vehicle_no;
                        $datainsert["autoreport_user_id"] = $rowvehicle[$x]->user_id;
                        $datainsert["autoreport_company"] = $rowvehicle[$x]->user_company;
                        $datainsert["autoreport_data_startdate"] = $startdate;
                        $datainsert["autoreport_data_enddate"] = $enddate;
                        $datainsert["autoreport_type"] = $report_type;
                        $datainsert["autoreport_file_path"] = $report_path;
                        $datainsert["autoreport_filename"] = $filecreatedname;
                        $datainsert["autoreport_download_path"] = $report_path.$filecreatedname;
                        $datainsert["autoreport_process_date"] = $process_date; 
                        $datainsert["autoreport_insert_db"] = date("Y-m-d H:i:s");
                        $this->dbtrans->insert("autoreport_history",$datainsert);
                
                        printf("INSERT OK \r\n");
                
                        printf("FINIS PROSES FOR VEHICLE %s  \r\n",$rowvehicle[$x]->vehicle_no); 
                        printf("===========================================================\r\n");       
                    }
        }
        
        unset($datalog);
        $datalog["cron_name"] = "History To Excel";
        $datalog["cron_start_data"] = $startdate;
        $datalog["cron_end_data"] = $enddate;
        $datalog["cron_start_proses"] = $startproses;
        $datalog["cron_end_proses"] = date("Y-m-d H:i:s");
        $this->dbtrans->insert("log_cronjobs",$datalog);
        
        $this->db->cache_delete_all();
        $this->dbhist->cache_delete_all();
        $this->dbtrans->cache_delete_all();
        printf("FINISH  \r\n");
        exit;
        
    }
    
    function history_peruser($datatype=0, $startdate="", $enddate="", $userid = "")
    {
        printf("PROSES AUTO REPORT HISTORY START \r\n");
        $startproses = date("Y-m-d H:i:s");
        
        include 'class/PHPExcel.php';
        include 'class/PHPExcel/Writer/Excel2007.php';
        
        //$userlist = array(775, 631, 981, 1059, 834, 778, 1222, 1027, 1246, 
        //1332, 1298, 986, 651, 645, 736, 666, 866, 627, 1386, 1110, 1148, 1122,
        //703, 1147, 1168, 1212);
        
        $dbname = "gpshistory";
        $order = "desc";
        $limit = "700";
        $offset = 0;
        $z=0;
        $totalodometer = 0;
        $totalodometer1 = 0;
        $process_date = date("Y-m-d H:i:s");
        $this->dbhist = $this->load->database($dbname, TRUE);
		$this->dbhist2 = $this->load->database("gpshistory2", TRUE);
        $this->dbtrans = $this->load->database("transporter",true);
        
        $report_type = "history_report";
        
        if ($startdate == "") 
        {
            $startdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
            $datefilename = date("Ymd", strtotime("yesterday")); 
        }
        
        if ($startdate != "")
        {
            $datefilename = date("Ymd", strtotime($startdate));     
            $startdate = date("Y-m-d 00:00:00", strtotime($startdate));
        }
        
        if ($enddate != "")
        {
            $enddate = date("Y-m-d 23:59:59", strtotime($enddate));
        }
        
        if ($enddate == "") 
        {
            $enddate = date("Y-m-d 23:59:59", strtotime("yesterday"));
        }
        
        $month = date("F", strtotime($startdate));
        $year = date("Y", strtotime($startdate));
        
        $sdate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($startdate)));
        $edate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($enddate)));
        
        if ($userid != "")
        {
            $this->db->where("user_id",$userid);
        }
        
        $this->db->join("user", "vehicle_user_id = user_id", "left outer");
        $this->db->where("vehicle_status <>", 3);
        $q = $this->db->get("vehicle");
        $rowvehicle = $q->result();
        
        $total_process = count($rowvehicle);
        printf("TOTAL PROSES VEHICLE : %s \r\n",$total_process);
        printf("============================================ \r\n");
        
        for($x=0;$x<count($rowvehicle);$x++)
        {
            printf("PROSES VEHICLE : %s (%d/%d) \r\n",$rowvehicle[$x]->vehicle_device, ++$z, $total_process);
            
            $objPHPExcel = new PHPExcel();
            $objPHPExcel->getProperties()->setTitle("History Report");
            $objPHPExcel->getProperties()->setSubject("History Report");
            $objPHPExcel->getProperties()->setDescription("History Report");
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
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(55);            
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(12);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(12);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(12);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10);

            //Header
            $objPHPExcel->setActiveSheetIndex(0);
            $objPHPExcel->getActiveSheet()->mergeCells('A1:I1');
            $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'HISTORY REPORT');
            $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle('B3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $objPHPExcel->getActiveSheet()->getStyle('B3')->getFont()->setBold(true);
            
            $objPHPExcel->getActiveSheet()->getStyle('J3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $objPHPExcel->getActiveSheet()->getStyle('J3')->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->SetCellValue('A5', 'No');
            $objPHPExcel->getActiveSheet()->SetCellValue('B5', 'Date');
            $objPHPExcel->getActiveSheet()->SetCellValue('C5', 'Time');
            $objPHPExcel->getActiveSheet()->SetCellValue('D5', 'Location');
            $objPHPExcel->getActiveSheet()->SetCellValue('E5', 'Coordinate');
            $objPHPExcel->getActiveSheet()->SetCellValue('F5', 'Speed (Kph)');
            $objPHPExcel->getActiveSheet()->SetCellValue('G5', 'Engine');
            $objPHPExcel->getActiveSheet()->SetCellValue('H5', 'Status');
            $objPHPExcel->getActiveSheet()->SetCellValue('I5', 'Odometer (km)');
            $objPHPExcel->getActiveSheet()->getStyle('A5:I5')->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle('A5:I5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            
            //cek apakah sudah pernah ada filenya
            $this->dbtrans->where("autoreport_vehicle_id",$rowvehicle[$x]->vehicle_id);
            $this->dbtrans->where("autoreport_data_startdate",$startdate);
            $this->dbtrans->limit(1);
            $qrpt = $this->dbtrans->get("autoreport_history");
            $rrpt = $qrpt->row();
            if (count($rrpt)>0)
            {
              printf("VEHICLE %s SUDAH PERNAH DI PROSES, HAPUS DULU DATA SEBELUMNYA \r\n", $rowvehicle[$x]->vehicle_device);     
            }
            else
            {
            
                $vehicle_device = explode("@", $rowvehicle[$x]->vehicle_device);  
                $vehicle_no = $rowvehicle[$x]->vehicle_no;
                
                $ex_vno = explode("/",$vehicle_no);
                $name = $vehicle_device[0];
                $host = $vehicle_device[1];
            
                if ($rowvehicle[$x]->vehicle_type == "T5" || $rowvehicle[$x]->vehicle_type == "T5 PULSE")
                {
                    $tables = $name."@t5_gps";
                    $tablesinfo = $name."@t5_info";       
                }
                else
                {
                    $tables = strtolower($name)."@".strtolower($host)."_gps";
                    $tablesinfo = strtolower($name)."@".strtolower($host)."_info";
                }
            
                $report_path = "/home/lacakmobil/public_html/transporter.lacak-mobil.com/public/assets/media/autoreport/".
                            $report_type."/".$year."/".$month."/".$vehicle_device[0]."/";
            
                printf("GET DATA GPS VEHICLE : %s \r\n",$rowvehicle[$x]->vehicle_no);
                $this->dbhist->select("gps_id, gps_time, gps_speed, gps_longitude, gps_ew, gps_latitude, gps_ns, gps_status");
				$this->dbhist->order_by("gps_time", $order);
				$this->dbhist->where("gps_name", $name);
				$this->dbhist->where("gps_host", $host);
				$this->dbhist->where("gps_time >=", $sdate);
				$this->dbhist->where("gps_time <=", $edate);
				$q = $this->dbhist->get($tables);
				$rows1 = $q->result();
						
				$this->dbhist2->select("gps_id, gps_time, gps_speed, gps_longitude, gps_ew, gps_latitude, gps_ns, gps_status");
                $this->dbhist2->order_by("gps_time", $order);
				$this->dbhist2->where("gps_name", $name);
				$this->dbhist2->where("gps_host", $host);
				$this->dbhist2->where("gps_time >=", $sdate);
				$this->dbhist2->where("gps_time <=", $edate);
				$q = $this->dbhist2->get($tables);
				$rows2 = $q->result();
				
				$rows = array_merge($rows1, $rows2);
            
				printf("GET DATA INFO VEHICLE : %s \r\n",$rowvehicle[$x]->vehicle_no);
				$this->dbhist->order_by("gps_info_time", $order);
				$this->dbhist->select("gps_info_io_port, gps_info_time, gps_info_distance, gps_info_alarm_alert, gps_info_alarm_data, gps_info_ad_input");
				$this->dbhist->where("gps_info_device", $name."@".$host);
				$this->dbhist->where("gps_info_time >=", $sdate);
				$this->dbhist->where("gps_info_time <=", $edate);
				$this->dbhist->limit(1);
				$qinfo = $this->dbhist->get($tablesinfo);
				$rowlastinfos1 = $qinfo->result();
						
				$this->dbhist->order_by("gps_info_time", $order);
				$this->dbhist->select("gps_info_io_port, gps_info_time, gps_info_distance, gps_info_alarm_alert, gps_info_alarm_data, gps_info_ad_input");
				$this->dbhist->where("gps_info_device", $name."@".$host);
				$this->dbhist->where("gps_info_time >=", $sdate);
				$this->dbhist->where("gps_info_time <=", $edate);
				$this->dbhist->limit(1);
				$qinfo = $this->dbhist->get($tablesinfo);
				$rowlastinfos2 = $qinfo->result();
						
				$rowlastinfos = array_merge($rowlastinfos1, $rowlastinfos2);
                
                //Summary Saja yang diambil
                if ($datatype == 0)
                {
                    for($i=count($rows)-1; $i >= 0; $i--)
                    {
                        if (($i+1) >= count($rows))
                        {
                            $rowsummary[] = $rows[$i];
                            continue;
                        }
                
                        $latbefore = getLatitude($rows[$i+1]->gps_latitude, $rows[$i+1]->gps_ns);
                        $lngbefore = getLongitude($rows[$i+1]->gps_longitude, $rows[$i+1]->gps_ew);

                        $latcurrent = getLatitude($rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                        $lngcurrent = getLongitude($rows[$i]->gps_longitude, $rows[$i]->gps_ew);
                
                        if (sprintf("%.4f,%.4f", $latbefore, $lngbefore) != sprintf("%.4f,%.4f", $latcurrent, $lngcurrent))
                        {
                            $rowsummary[] = $rows[$i];
                            continue;
                        }

                        if ($rows[$i+1]->gps_speed != $rows[$i]->gps_speed)
                        {
                            $rowsummary[] = $rows[$i];
                            continue;
                        }
                    }
                 
                    $rows = array();
                    $total = 0;
                    if (isset($rowsummary))
                    {
                        $rowsummary = array_reverse($rowsummary);            
                        $total = count($rowsummary);
                        $rows = array_splice($rowsummary, $offset, $limit);                            
                    }   
                }
                //Finis Summary
            
                unset($map_params);
                $ismove = false;
                $lastcoord = false;
            
                printf("PROSES XLS FILE FOR : %s  \r\n",$rowvehicle[$x]->vehicle_no);
            
                for($i=0; $i < count($rows); $i++)
                {
                    printf("|%s|",$i);
                    if ($i == 0)
                    {
                        $this->dbhist->order_by("gps_info_time", $order);
                        $this->dbhist->select("gps_info_io_port, gps_info_time, gps_info_distance, gps_info_alarm_alert, gps_info_alarm_data, gps_info_ad_input");
                        $this->dbhist->where("gps_info_device", $name."@".$host);
                        $this->dbhist->where("gps_info_time >=", $sdate);
                        $this->dbhist->where("gps_info_time <=", $edate);
                        $qinfos = $this->dbhist->get($tablesinfo);   
                        $rowinfos = $qinfos->result();
                        $this->db->cache_delete_all();
                
                        for($j=0; $j < count($rowinfos); $j++)
                        {
                            $infos[dbmaketime($rowinfos[$j]->gps_info_time)] = $rowinfos[$j];
                        }
                    }
            
                    $rows[$i]->gps_timestamp = dbmaketime($rows[$i]->gps_time);
                    $rows[$i]->gps_longitude_real = getLongitude($rows[$i]->gps_longitude, $rows[$i]->gps_ew);
                    $rows[$i]->gps_latitude_real = getLatitude($rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                    $rows[$i]->gps_longitude_real_fmt = number_format($rows[$i]->gps_longitude_real, 4, ".", "");
                    $rows[$i]->gps_latitude_real_fmt = number_format($rows[$i]->gps_latitude_real, 4, ".", "");        
            
                    if ($i == 0)
                    {
                        $lastcoord = array($rows[$i]->gps_longitude_real_fmt, $rows[$i]->gps_latitude_real_fmt);
                    }
                    else
                    {
                        if (($lastcoord[0] != $rows[$i]->gps_longitude_real_fmt) || ($lastcoord[1] != $rows[$i]->gps_latitude_real_fmt))
                        {
                            $ismove = true;
                        }
                    }

                    $rows[$i]->gps_date_fmt = date("d/m/Y", $rows[$i]->gps_timestamp+7*3600);
                    $rows[$i]->gps_time_fmt = date("H:i:s", $rows[$i]->gps_timestamp+7*3600);
                    $rows[$i]->gps_speed_fmt = number_format($rows[$i]->gps_speed*1.852, 0, "", ".");
                    $rows[$i]->gps_status = ($rows[$i]->gps_status == "A") ? "OK" : "NOT OK";
            
                    if (isset($infos[$rows[$i]->gps_timestamp]))
                    {
                        $ioport = $infos[$rows[$i]->gps_timestamp]->gps_info_io_port;
                        $rows[$i]->status1 = ((strlen($ioport) > 4) && ($ioport[4] == 1)) ? $this->lang->line('lon') : $this->lang->line('loff');
                        $rows[$i]->odometer = number_format(round(($infos[$rows[$i]->gps_timestamp]->gps_info_distance+$rowvehicle[$x]->vehicle_odometer*1000)/1000), 0, "", ","); 
                    }
                    else
                    {
                        $rows[$i]->status1 = "-";
                        $rows[$i]->odometer = "-";
                    }
                
                    $rows[$i]->georeverse = $this->gpsmodel->GeoReverse($rows[$i]->gps_latitude_real_fmt, $rows[$i]->gps_longitude_real_fmt);            
                    $rows[$i]->gpsindex = $i+1;
                    $rows[$i]->gpsdate = date("d/m/Y", $rows[$i]->gps_timestamp+7*3600);
                    $rows[$i]->gpstime = date("H:i:s", $rows[$i]->gps_timestamp+7*3600);
                    $rows[$i]->gpsaddress = $rows[$i]->georeverse->display_name;
                    $rows[$i]->gpscoord = "(".$rows[$i]->gps_longitude_real_fmt." ".$rows[$i]->gps_latitude_real_fmt.")";
                    $rows[$i]->gpstatus = (($rows[$i]->gps_status == "V") ? "NOT OK" : "OK");
                    $map_params[] = array($rows[$i]->gps_longitude_real_fmt, $rows[$i]->gps_latitude_real_fmt);
            
                    $objPHPExcel->getActiveSheet()->SetCellValue('A'.(6+$i), $i+1);
                    $objPHPExcel->getActiveSheet()->SetCellValue('B'.(6+$i), date("d/m/Y", $rows[$i]->gps_timestamp+7*3600));
                    $objPHPExcel->getActiveSheet()->SetCellValue('C'.(6+$i), date("H:i:s", $rows[$i]->gps_timestamp+7*3600));
                    $objPHPExcel->getActiveSheet()->SetCellValue('D'.(6+$i), $rows[$i]->georeverse->display_name);
                    $objPHPExcel->getActiveSheet()->SetCellValue('E'.(6+$i), $rows[$i]->gps_latitude_real_fmt .",".$rows[$i]->gps_longitude_real_fmt);
                    $objPHPExcel->getActiveSheet()->SetCellValue('F'.(6+$i), $rows[$i]->gps_speed_fmt);
                    $objPHPExcel->getActiveSheet()->SetCellValue('G'.(6+$i), $rows[$i]->status1);
                    $objPHPExcel->getActiveSheet()->SetCellValue('H'.(6+$i), ($rows[$i]->gps_status == "V") ? "NOT OK" : "OK"); 
                    $objPHPExcel->getActiveSheet()->SetCellValue('I'.(6+$i), $rows[$i]->odometer);
                
                    $styleArray = array(
                          'borders' => array(
                            'allborders' => array(
                              'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                          )
                        );
                        
                    $objPHPExcel->getActiveSheet()->getStyle('A5:I'.(5+$i))->applyFromArray($styleArray);
                    $objPHPExcel->getActiveSheet()->getStyle('A5:I'.(5+$i))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
                    $objPHPExcel->getActiveSheet()->getStyle('A5:I'.(5+$i))->getAlignment()->setWrapText(true);
                } 
            
                // Save Excel
                $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
                @mkdir($report_path, DIR_WRITE_MODE); //REPORT_PATH -> config->constant
                
                if (isset($ex_vno))
                {
                    if (isset($ex_vno[1]))
                    {
                        $filecreatedname = "HistoryReport_".$ex_vno[0].$ex_vno[1]."_".$datefilename.".xls";    
                    }
                    else
                    {
                        $filecreatedname = "HistoryReport_".$ex_vno[0]."_".$datefilename.".xls";       
                    }
                }
                else
                {
                    
                    $filecreatedname = "HistoryReport_".$vehicle_no."_".$datefilename.".xls";  
                }
                
                printf("SAVE XLS FILE FOR : %s  \r\n",$filecreatedname); 
                $objWriter->save($report_path.$filecreatedname);
                
                printf("DELETE CACHE GPS \r\n"); 
                $this->db->cache_delete_all();
                $this->dbhist->cache_delete_all();
                
                printf("INSERT TO DB : %s  \r\n",$rowvehicle[$x]->vehicle_no);
                unset($datainsert);
                $datainsert["autoreport_vehicle_id"] = $rowvehicle[$x]->vehicle_id;
                $datainsert["autoreport_vehicle_device"] = $rowvehicle[$x]->vehicle_device;
                $datainsert["autoreport_vehicle_name"] = $rowvehicle[$x]->vehicle_name;
                $datainsert["autoreport_vehicle_no"] = $rowvehicle[$x]->vehicle_no;
                $datainsert["autoreport_user_id"] = $rowvehicle[$x]->user_id;
                $datainsert["autoreport_company"] = $rowvehicle[$x]->user_company;
                $datainsert["autoreport_data_startdate"] = $startdate;
                $datainsert["autoreport_data_enddate"] = $enddate;
                $datainsert["autoreport_type"] = $report_type;
                $datainsert["autoreport_file_path"] = $report_path;
                $datainsert["autoreport_filename"] = $filecreatedname;
                $datainsert["autoreport_download_path"] = $report_path.$filecreatedname;
                $datainsert["autoreport_process_date"] = $process_date; 
                $datainsert["autoreport_insert_db"] = date("Y-m-d H:i:s");
                $this->dbtrans->insert("autoreport_history",$datainsert);
                
                printf("INSERT OK \r\n");
                
                printf("FINIS PROSES FOR VEHICLE %s  \r\n",$rowvehicle[$x]->vehicle_no); 
                printf("===========================================================\r\n");       
            }
        }
        
        unset($datalog);
        $datalog["cron_name"] = "History To Excel";
        $datalog["cron_start_data"] = $startdate;
        $datalog["cron_end_data"] = $enddate;
        $datalog["cron_start_proses"] = $startproses;
        $datalog["cron_end_proses"] = date("Y-m-d H:i:s");
        $this->dbtrans->insert("log_cronjobs",$datalog);
        
        $this->db->cache_delete_all();
        $this->dbhist->cache_delete_all();
        $this->dbtrans->cache_delete_all();
        printf("FINISH  \r\n");
        exit;
    }
    
    function history_monthly_table($startdate="",$enddate="",$name="",$host="", $maxdata=10000, $offset = 0)
    {
        $startproses = date("Y-m-d H:i:s");
        
        if ($startdate == "") 
        {
            $startdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
        }
        else
        {
            $startdate = date("Y-m-d 00:00:00", strtotime($startdate));    
        }
        
        if ($enddate == "") 
        {
            $enddate = date("Y-m-d 23:59:59", strtotime("yesterday"));
        }
        else
        {
            $enddate = date("Y-m-d 23:59:59", strtotime($enddate));
        }
        
        $sdate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($startdate)));
        $edate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($enddate)));
        
        $month = date("F", strtotime("yesterday"));
        $year = date("Y", strtotime("yesterday"));
        $report_type = "datagps_";
        
        switch ($month)
        {
            case "January":
                $dbname = $report_type."januari_".$year;
            break;
            case "February":
                $dbname = $report_type."februari_".$year;
            break;
			case "March":
                $dbname = $report_type."maret_".$year;
            break;
			case "April":
                $dbname = $report_type."april_".$year;
            break;
			case "May":
                $dbname = $report_type."mei_".$year;
            break;
			case "June":
                $dbname = $report_type."juni_".$year;
            break;
			case "July":
                $dbname = $report_type."juli_".$year;
            break;
			case "August":
                $dbname = $report_type."agustus_".$year;
            break;
			case "September":
                $dbname = $report_type."september_".$year;
            break;
			case "October":
                $dbname = $report_type."oktober_".$year;
            break;
			case "November":
                $dbname = $report_type."november_".$year;
            break;
			case "December":
                $dbname = $report_type."desember_".$year;
            break;
        }
        
        if (strlen($name) > 0)
        {
            $this->db->where("vehicle_device", $name."@".$host);
        }
    
        $this->db->distinct();
        $this->db->order_by("vehicle_id","asc");
        $this->db->where("vehicle_status <>", 3);
        $this->db->select("vehicle_type, vehicle_device, vehicle_info");
        $q = $this->db->get("vehicle");
        if ($q->num_rows() == 0) return;

        $rows = $q->result();
        $totalvehicle = count($rows);
        $i = 0;
    
        foreach($rows as $row)
        {
            if (($i+1) < $offset)
            {
                $i++;
                continue;
            }
            
            printf("history daily for %s (%d/%d)\n", $row->vehicle_device, ++$i, $totalvehicle);
            
            $istbl_history = $this->config->item("dbhistory_default");
            if($this->config->item("is_dbhistory") == 1)
            {
				$istbl_history = $row->vehicle_dbhistory_name;
			}
			$this->db = $this->load->database($istbl_history, TRUE);
            $table = strtolower($row->vehicle_device)."_gps";
            $tableinfo = strtolower($row->vehicle_device)."_info";
            
            $start = mktime();
            
            $devices = explode("@", $row->vehicle_device);
            
            if (isset($devices[0]) && isset($devices[1]))
            {
                $ok = 1;
                $tableinsertgps = strtolower($devices[0]).strtolower($devices[1])."_gps";
                $tableinsertinfo = strtolower($devices[0]).strtolower($devices[1])."_info";        
            }
            
            // gps
            $offset = 0;
        
            if (isset($ok) && $ok == 1)
            {
                $this->dbdata = $this->load->database($dbname, TRUE);
                $this->dbdata->select("gps_id");
                $this->dbdata->where("gps_name",$devices[0]);
                $this->dbdata->where("gps_host",$devices[1]);
                $this->dbdata->where("gps_time >=", $sdate);
                $this->dbdata->where("gps_time <=", $edate);
                $this->dbdata->limit(1);
                $qx = $this->dbdata->get($tableinsertgps);
                $tx = $qx->num_rows();   
            }
                                
            if (isset($tx) && $tx > 0)
            {
                //sudah ada
                printf("=== Data GPS & INFO Sudah Ada \n"); 
            }
            else
            {
                while(1)
                {

                    $this->db->limit($maxdata, $offset);
                    $this->db->where("gps_name", $devices[0]);
                    $this->db->where("gps_host", $devices[1]);
                    $this->db->where("gps_time >=", $sdate);
                    $this->db->where("gps_time <=", $edate);
                    $q = $this->db->get($table);                    
                    $total = $q->num_rows();    
                    
                    printf("=== jumlah data gps: %d, lama: %ds \n", $q->num_rows(), mktime()-$start);
                
                    if ($q->num_rows() == 0) break;
                
                    $historydb = $this->load->database($dbname, TRUE);
                
                    $gpses = $q->result_array();
                    $q->free_result();
                    foreach($gpses as $gps)
                    {
                        unset($gps['gps_id']);
                        if (isset($ok) && $ok == 1)
                        {
                            $historydb->insert($tableinsertgps, $gps);   
                        }   
                    }
                
                    if ($total < $maxdata) break;
                
                    $offset += $maxdata;
                }
                
                 // gps info
                 $offset = 0;
                 while(1)
                 {

                    $this->db->limit($maxdata, $offset);
                    $this->db->where("gps_info_device", $row->vehicle_device);
                    $this->db->where("gps_info_time >=", $sdate);
                    $this->db->where("gps_info_time <=", $edate);
                    $q = $this->db->get($tableinfo);                    
                    $total = $q->num_rows();    
                    
                    printf("=== jumlah data info: %d, lama: %ds \n", $q->num_rows(), mktime()-$start);
                
                    if ($q->num_rows() == 0) break;
                
                    $historydb = $this->load->database($dbname, TRUE);
                
                    $gpses = $q->result_array();
                    $q->free_result();
                    foreach($gpses as $gps)
                    {
                        unset($gps['gps_info_id']);
                        if (isset($ok) && $ok == 1)
                        {
                            $historydb->insert($tableinsertinfo, $gps);   
                        }   
                    }
                
                if ($total < $maxdata) break;
                
                $offset += $maxdata;
                
                }
            
            }
                
            printf("=== selesai\n");            
        }
        
        unset($datalog);
        $this->dbtrans = $this->load->database("transporter",true);
        $datalog["cron_name"] = "History Data Monthly";
        $datalog["cron_start_data"] = $startdate;
        $datalog["cron_end_data"] = $enddate;
        $datalog["cron_start_proses"] = $startproses;
        $datalog["cron_end_proses"] = date("Y-m-d H:i:s");
        $this->dbtrans->insert("log_cronjobs",$datalog);
    }
    
	function playback($userid="", $name = "", $host="", $startdate = "", $enddate = "")
	{
		printf("PROSES AUTO REPORT PLAYBACK >> START \r\n");
        $startproses = date("Y-m-d H:i:s");
        
        include 'class/PHPExcel.php';
        include 'class/PHPExcel/Writer/Excel2007.php';
        
        $report_type = "playback";
        $process_date = date("Y-m-d H:i:s");
        $domain_server = "http://202.129.190.194/";
        $z = 0;
		$report = "playback_";
        
        //$userlist = array(775, 631, 981, 1059, 834, 778, 1222, 1027, 1246, 
        //1332, 1298, 986, 651, 645, 736, 666, 866, 627, 1386, 1110, 1148, 1122,
        //703, 1147, 1168);
        
        if ($startdate == "") {
            $startdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
            $datefilenamestart = date("Ymd", strtotime("yesterday")); 
			$month = date("F", strtotime("yesterday"));
			$year = date("Y", strtotime("yesterday"));
        }
        
        if ($startdate != "")
        {
            $datefilenamestart = date("Ymd", strtotime($startdate));     
            $startdate = date("Y-m-d 00:00:00", strtotime($startdate));
			$month = date("F", strtotime($startdate));
			$year = date("Y", strtotime($startdate));
        }
        
        if ($enddate != "")
        {
            $enddate = date("Y-m-d 23:59:59", strtotime($enddate));
            $datefilenameend = date("Ymd", strtotime($enddate));
        }
        
        if ($enddate == "") {
            $enddate = date("Y-m-d 23:59:59", strtotime("yesterday"));
            $datefilenameend = date("Ymd", strtotime("yesterday"));
        }
        
        $datefilename = $datefilenamestart."_".$datefilenameend;
        
        switch ($month)
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
        
        $sdate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($startdate)));
        $edate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($enddate)));
        
        if ($name != "" && $host != "")
        {
            $dev = $name."@".$host;
            $this->db->where("vehicle_device", $dev);    
        } 
       
		if ($userid != "")
		{
			$this->db->where("user_id",$userid);
		}
		
        $this->db->join("user", "vehicle_user_id = user_id", "left outer");
		
        if ($userid == "")
		{
			$this->db->where("user_company >",0);
		}
        
		$this->db->where("vehicle_status <>", 3);
        $q = $this->db->get("vehicle");
        $rowvehicle = $q->result();
        
        $total_process = count($rowvehicle);
        printf("TOTAL PROSES VEHICLE : %s \r\n",$total_process);
        printf("============================================ \r\n");
		
        for($x=0;$x<count($rowvehicle);$x++) 
        {
            printf("PROSES VEHICLE : %s (%d/%d) \r\n",$rowvehicle[$x]->vehicle_device, ++$z, $total_process);
            
            if (isset($rowvehicle[$x]->vehicle_info))
            {
                $json = json_decode($rowvehicle[$x]->vehicle_info);
                if (isset($json->vehicle_ws))
                {
					$istbl_history = $this->config->item("dbhistory_default");
					if($this->config->item("is_dbhistory") == 1)
					{
						$istbl_history = $rowvehicle[$x]->vehicle_dbhistory_name;
					}
					$this->dbhist = $this->load->database($istbl_history, TRUE);
					$this->dbhist2 = $this->load->database("gpshistory2",true);
                    $this->dbtrans = $this->load->database("transporter",true);
        
                    unset($data_insert);
                    
                    //cek apakah sudah pernah ada filenya
                    $this->dbtrans->where("autoreport_vehicle_id",$rowvehicle[$x]->vehicle_id);
                    $this->dbtrans->where("autoreport_data_startdate",$startdate);
                    $this->dbtrans->limit(1);
                    $qrpt = $this->dbtrans->get("autoreport_playback");
                    $rrpt = $qrpt->row();
                    
                    if (count($rrpt)>0)
                    {
                        printf("VEHICLE %s SUDAH PERNAH DI PROSES, HAPUS DULU DATA SEBELUMNYA \r\n", $rowvehicle[$x]->vehicle_device);     
                    }
                    else
                    {
                        $objPHPExcel = new PHPExcel();
                        $objPHPExcel->getProperties()->setCreator("lacak-mobil.com");
                        $objPHPExcel->getProperties()->setLastModifiedBy("lacak-mobil.com");
                        $objPHPExcel->getProperties()->setTitle("Playback Report");
                        $objPHPExcel->getProperties()->setSubject("Playback Report Lacak-mobil.com");
                        $objPHPExcel->getProperties()->setDescription("Playback Report Lacak-mobil.com");    
                
                        $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(1);
                        $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.50);
                        $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.50);
                        $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(1);
                        $objPHPExcel->getDefaultStyle()->getFont()->setName('Times New Roman');
                        $objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
                        $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
                        $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
                        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
                        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
                        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
                        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);            
                        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
                        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
                        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
                        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
                        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(45);
                        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(45);
                
                        //Header
                        $objPHPExcel->setActiveSheetIndex(0);
                        $objPHPExcel->getActiveSheet()->mergeCells('A1:J1');
                        $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'PLAYBACK REPORT');
                        $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
                        $objPHPExcel->getActiveSheet()->getStyle('B3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                        $objPHPExcel->getActiveSheet()->getStyle('B3')->getFont()->setBold(true);
                
                        //Top Header
                        $objPHPExcel->getActiveSheet()->SetCellValue('A5', 'No');
                        $objPHPExcel->getActiveSheet()->SetCellValue('B5', 'Vehicle');
                        $objPHPExcel->getActiveSheet()->SetCellValue('C5', 'Engine');
                        $objPHPExcel->getActiveSheet()->SetCellValue('D5', 'Start Time');
                        $objPHPExcel->getActiveSheet()->SetCellValue('E5', 'End Time');
                        $objPHPExcel->getActiveSheet()->SetCellValue('F5', 'Duration');
                        $objPHPExcel->getActiveSheet()->SetCellValue('G5', 'Trip Mileage');
                        $objPHPExcel->getActiveSheet()->SetCellValue('H5', 'Cummulative Mileage');
                        $objPHPExcel->getActiveSheet()->SetCellValue('I5', 'Location Start');
                        $objPHPExcel->getActiveSheet()->SetCellValue('J5', 'Location End');
                        $objPHPExcel->getActiveSheet()->getStyle('A5:J5')->getFont()->setBold(true);
                        $objPHPExcel->getActiveSheet()->getStyle('A5:J5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $objPHPExcel->getActiveSheet()->getStyle('A5:J5')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                        $objPHPExcel->getActiveSheet()->getStyle('A5:J5')->getFill()->getStartColor()->setRGB('bfbfbf;');
                
                        $vehicle_device = explode("@", $rowvehicle[$x]->vehicle_device);  
                        $vehicle_no = $rowvehicle[$x]->vehicle_no;
                
                        $ex_vno = explode("/",$vehicle_no);

                        if ($rowvehicle[$x]->vehicle_type == "T5" || $rowvehicle[$x]->vehicle_type == "T5 PULSE")
                        {
                            $table = $vehicle_device[0]."@t5_gps";
                            $tableinfo = $vehicle_device[0]."@t5_info";    
                        }
                        else
                        {
                            $table = strtolower($vehicle_device[0])."@".strtolower($vehicle_device[1])."_gps";
                            $tableinfo = strtolower($vehicle_device[0])."@".strtolower($vehicle_device[1])."_info";
                        }
            
                        $report_path = "/home/lacakmobil/public_html/transporter.lacak-mobil.com/public/assets/media/autoreport/".$report_type."/".
                                $year."/".$month."/".$vehicle_device[0]."/";
                        $pub_path = "assets/media/autoreport/".$report_type."/".$year."/".$month."/".$vehicle_device[0]."/";
            
                        $this->dbhist->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");    
                        $this->dbhist->where_in("gps_info_device", $rowvehicle[$x]->vehicle_device);
                        $this->dbhist->where("gps_time >=", $sdate);
                        $this->dbhist->where("gps_time <=", $edate);    
                        $this->dbhist->order_by("gps_time","asc");
                        $this->dbhist->from($table);
                        $q = $this->dbhist->get();
                        $rows1 = $q->result();
						
						$this->dbhist2->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");    
                        $this->dbhist2->where_in("gps_info_device", $rowvehicle[$x]->vehicle_device);
                        $this->dbhist2->where("gps_time >=", $sdate);
                        $this->dbhist2->where("gps_time <=", $edate);    
                        $this->dbhist2->order_by("gps_time","asc");
                        $this->dbhist2->from($table);
                        $q2 = $this->dbhist2->get();
                        $rows2 = $q2->result();
						
						$rows = array_merge($rows1, $rows2);
                
                        $data = array(); // initialization variable
                        $vehicle_device = "";
                        $engine = "";
                
                        foreach($rows as $obj)
                        {
                            if($vehicle_device != $rowvehicle[$x]->vehicle_device)
                            {
                                $no=0;
                                $no_data = 1;
                            }
                
                            if(substr($obj->gps_info_io_port, 4, 1) == 1)
                            { //engine ON
                                if($engine != "ON") 
                                {
                                    $no++;
                                    $no_data = 1;
                                }
                    
                                if($no == 0) $no++;
                                if($no_data == 1)
                                {
                                    $data[$rowvehicle[$x]->vehicle_no."#".$rowvehicle[$x]->vehicle_name][$no]['ON']['start'] = $obj;
                                }
                                else
                                {
                                    $data[$rowvehicle[$x]->vehicle_no."#".$rowvehicle[$x]->vehicle_name][$no]['ON']['end'] = $obj;
                                }
                                $no_data++;
                                $engine = "ON";
                            }
                
                            if(substr($obj->gps_info_io_port, 4, 1) == 0)
                            { //engine OFF
                                if($engine != "OFF") 
                                {
                                    $no++;
                                    $no_data = 1;
                                }
                    
                                if($no == 0) $no++;
                                if($no_data == 1)
                                {
                                    $data[$rowvehicle[$x]->vehicle_no."#".$rowvehicle[$x]->vehicle_name][$no]['OFF']['start'] = $obj;
                                }
                                else
                                {
                                    $data[$rowvehicle[$x]->vehicle_no."#".$rowvehicle[$x]->vehicle_name][$no]['OFF']['end'] = $obj;
                                }
                    
                                $no_data++;
                                $engine = "OFF";
                            }
                    
                            $vehicle_device = $rowvehicle[$x]->vehicle_device;
                        }//end loop foreach rows
                
                        $i=1;
                        $cummulative = 0; 
                        printf("WRITE DATA EXCEL : ");
                        foreach($data as $vehicles=>$value_vehicles)
                        {
                            foreach($value_vehicles as $number=>$value_number)
                            {
                                foreach($value_number as $engine=>$report)
                                {
                                    if(!isset($report['end']))
                                    {
                                        $report['end'] = $report['start'];
                                    }
									
                                    $data_report[$vehicles][$number][$engine]['start'] = $report['start'];
                                    $data_report[$vehicles][$number][$engine]['end'] = $report['end'];
                                    $duration = get_time_difference($report['start']->gps_time, $report['end']->gps_time);
                       
                                    $show_duration = "";
                                    if($duration[0]!=0)
                                    {
                                        $show_duration .= $duration[0] ." Day ";
                                    }
                        
                                    if($duration[1]!=0)
                                    {
                                        $show_duration .= $duration[1] ." Hour ";
                                    }
                        
                                    if($duration[2]!=0)
                                    {
                                        $show_duration .= $duration[2] ." Min";
                                    }
                        
                                    if($show_duration == "")
                                    {
                                        $show_duration .= "0 Min";
                                    }
									
									unset($datainsert);
                                    $data_report[$vehicles][$number][$engine]['duration'] = $show_duration;
                                    $mileage = round(($report['end']->gps_info_distance - $report['start']->gps_info_distance)/1000, 2);
                                    $data_report[$vehicles][$number][$engine]['mileage'] = round($mileage, 2);
                        
                                    $location_start = $this->getPosition($report['start']->gps_longitude, $report['start']->gps_ew, $report['start']->gps_latitude, $report['start']->gps_ns);
                                    $data_report[$vehicles][$number][$engine]['location_start'] = $location_start;
                        
                                    $geofence_start = $this->getGeofence_location($report['start']->gps_longitude, $report['start']->gps_ew, $report['start']->gps_latitude, $report['start']->gps_ns, $report['start']->gps_name);
                                    $data_report[$vehicles][$number][$engine]['geofence_start'] = $geofence_start;
                        
                                    $location_end = $this->getPosition($report['end']->gps_longitude, $report['end']->gps_ew, $report['end']->gps_latitude, $report['end']->gps_ns);
                                    $data_report[$vehicles][$number][$engine]['location_end'] = $location_end;
                        
                                    $geofence_end = $this->getGeofence_location($report['end']->gps_longitude, $report['end']->gps_ew, $report['end']->gps_latitude, $report['end']->gps_ns, $report['end']->gps_name);
                                    $data_report[$vehicles][$number][$engine]['geofence_end'] = $geofence_end;
                        
                                    $cummulative += $data_report[$vehicles][$number][$engine]['mileage'];
                                    //print_r($data_report[$vehicles][$number][$engine]['location_start']->display_name);exit();
                        
                                    printf("|%s|",$i);
                                    $objPHPExcel->getActiveSheet()->SetCellValue('A'.(5+$i), $i);
                                    $objPHPExcel->getActiveSheet()->getStyle('A'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                    $objPHPExcel->getActiveSheet()->SetCellValue('B'.(5+$i), $rowvehicle[$x]->vehicle_no);
                                    $objPHPExcel->getActiveSheet()->getStyle('B'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                    $objPHPExcel->getActiveSheet()->SetCellValue('C'.(5+$i), $engine);
                                    $objPHPExcel->getActiveSheet()->getStyle('C'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                    $objPHPExcel->getActiveSheet()->SetCellValue('D'.(5+$i), date("d/m/Y H:i:s", strtotime("+7 hour", strtotime($report['start']->gps_time))));
                                    $objPHPExcel->getActiveSheet()->getStyle('D'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                    $objPHPExcel->getActiveSheet()->SetCellValue('E'.(5+$i), date("d/m/Y H:i:s", strtotime("+7 hour", strtotime($report['end']->gps_time))));
                                    $objPHPExcel->getActiveSheet()->getStyle('E'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                    $objPHPExcel->getActiveSheet()->SetCellValue('F'.(5+$i), $data_report[$vehicles][$number][$engine]['duration']);
                                    $objPHPExcel->getActiveSheet()->getStyle('F'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            
                                    //mileage
                                    $xme = $data_report[$vehicles][$number][$engine]['mileage'];
                                    $xxme = explode(".",$xme);
                                    if (isset($xxme[1]))
                                    {
                                        $xsub = substr($xxme[1],0,2); 
                                        $x_mile = $xxme[0].".".$xsub;     
                                    }
                                    else
                                    {
                                        $x_mile = $xxme[0];
                                    }
                            
                                    //cummulative mileage
                                    $xcum = $cummulative;
                                    $xxcum = explode(".",$xcum);
                                    if (isset($xxcum[1]))
                                    {
                                        $xcumsub = substr($xxcum[1],0,2); 
                                        $x_cum = $xxcum[0].".".$xcumsub;     
                                    }
                                    else
                                    {
                                        $x_cum = $xxcum[0];
                                    }
                            
                            
                                    $objPHPExcel->getActiveSheet()->SetCellValue('G'.(5+$i), $x_mile." "."KM");
                                    $objPHPExcel->getActiveSheet()->getStyle('G'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                    $objPHPExcel->getActiveSheet()->SetCellValue('H'.(5+$i), $x_cum." "."KM");
                                    $objPHPExcel->getActiveSheet()->getStyle('H'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        
                                    if (isset($data_report[$vehicles][$number][$engine]['geofence_start']) && ($data_report[$vehicles][$number][$engine]['geofence_start'] != ""))
                                    {
                                        $z = $data_report[$vehicles][$number][$engine]['geofence_start'];
                                        $y = explode("#",$z);
                            
                                        $valexcel = "Geofence : "." ".$y[1]." ".($data_report[$vehicles][$number][$engine]['location_start']->display_name);
                                        $objPHPExcel->getActiveSheet()->SetCellValue('I'.(5+$i), $valexcel);
                                        $objPHPExcel->getActiveSheet()->getStyle('I'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
										$datainsert["playback_location_start"] = $valexcel;
                                    } 
                                    else
                                    {
                                        $objPHPExcel->getActiveSheet()->SetCellValue('I'.(5+$i), ($data_report[$vehicles][$number][$engine]['location_start']->display_name));
                                        $objPHPExcel->getActiveSheet()->getStyle('I'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
										$datainsert["playback_location_start"] = $data_report[$vehicles][$number][$engine]['location_start']->display_name;
                                    }
                        
                                    if (isset($data_report[$vehicles][$number][$engine]['geofence_end']) && ($data_report[$vehicles][$number][$engine]['geofence_end'] != ""))
                                    {
                                        $j = $data_report[$vehicles][$number][$engine]['geofence_end'];
                                        $k = explode("#",$j);
                                        $valexcel = "Geofence : "." ".$k[1]." ".($data_report[$vehicles][$number][$engine]['location_end']->display_name);
                                        $objPHPExcel->getActiveSheet()->SetCellValue('J'.(5+$i), $valexcel);
                                        $objPHPExcel->getActiveSheet()->getStyle('J'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
										$datainsert["playback_location_end"] = $valexcel;
                                    }
                                    else
                                    {
                                        $objPHPExcel->getActiveSheet()->SetCellValue('J'.(5+$i), ($data_report[$vehicles][$number][$engine]['location_end']->display_name));
                                        $objPHPExcel->getActiveSheet()->getStyle('J'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
										$datainsert["playback_location_end"] = $data_report[$vehicles][$number][$engine]['location_end']->display_name;
                                    }
									
									
									$datainsert["playback_vehicle_device"] = $rowvehicle[$x]->vehicle_device;
									$datainsert["playback_vehicle_no"] = $rowvehicle[$x]->vehicle_no;
									$datainsert["playback_vehicle_name"] = $rowvehicle[$x]->vehicle_name;
									$datainsert["playback_engine"] = $engine;
									$datainsert["playback_start_time"] = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($report['start']->gps_time)));
									$datainsert["playback_end_time"] = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($report['end']->gps_time)));
									$datainsert["playback_duration"] = $data_report[$vehicles][$number][$engine]['duration'];
									$datainsert["playback_mileage"] = $x_mile;
									$datainsert["playback_cumm_mileage"] = $x_cum;
									
									$this->dbplayback = $this->load->database("playback",TRUE);
									$this->dbplayback->insert($dbtable,$datainsert);
									
                                    $i++;
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
            
                        $objPHPExcel->getActiveSheet()->getStyle('A5:J'.(4+$i))->applyFromArray($styleArray);
                        $objPHPExcel->getActiveSheet()->getStyle('A5:J'.(4+$i))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
                        $objPHPExcel->getActiveSheet()->getStyle('A5:J'.(4+$i))->getAlignment()->setWrapText(true);
                        // Rename sheet
                        $objPHPExcel->getActiveSheet()->setTitle('Playback_Report');
            
                        printf("CREATE FILE FOR : %s \r\n",$rowvehicle[$x]->vehicle_device);
                        
                        // Save Excel
                        $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
                        @mkdir($report_path, DIR_WRITE_MODE); //REPORT_PATH -> config->constant
                
                        if (isset($ex_vno))
                        {
                            if (isset($ex_vno[1]))
                            {
                                $filecreatedname = "Playback_".$ex_vno[0].$ex_vno[1]."_".$datefilename.".xls";    
                            }
                            else
                            {
                                $filecreatedname = "Playback_".$ex_vno[0]."_".$datefilename.".xls";       
                            }
                        }
                        else
                        {
                            $filecreatedname = "Playback_".$vehicle_no."_".$datefilename.".xls";  
                        }
                  
                        $objWriter->save($report_path.$filecreatedname);
                        printf("CREATE FILE DONE : %s \r\n",$filecreatedname);
            
                        $public_path = $domain_server.$pub_path.$filecreatedname;
            
                        printf("INSERT TO DATABASE TRANSPORTER \r\n");
                        $data_insert["autoreport_vehicle_id"] = $rowvehicle[$x]->vehicle_id;
                        $data_insert["autoreport_vehicle_device"] = $rowvehicle[$x]->vehicle_device;
                        $data_insert["autoreport_user_id"] = $rowvehicle[$x]->user_id;
                        $data_insert["autoreport_company"] = $rowvehicle[$x]->user_company;
                        $data_insert["autoreport_data_startdate"] = $startdate;
                        $data_insert["autoreport_data_enddate"] = $enddate;
                        $data_insert["autoreport_type"] = $report_type;
                        $data_insert["autoreport_file_path"] = $report_path;
                        $data_insert["autoreport_filename"] = $filecreatedname;
                        $data_insert["autoreport_download_path"] = $report_path.$filecreatedname;
                        $data_insert["autoreport_public_path"] = $public_path;
                        $data_insert["autoreport_process_date"] = $process_date;
                        $data_insert["autoreport_insert_db"] = date("Y-m-d H:i:s");
            
                        $this->dbtrans->insert("autoreport_playback",$data_insert);
                        printf("INSERT OK \r\n");
            
                        printf("DELETE CACHE HISTORY \r\n");
                        $this->dbhist->cache_delete_all();
                        $this->dbtrans->cache_delete_all();
                        unset($data);
            
                        printf("FINISH FOR VEHICLE : %s \r\n",$rowvehicle[$x]->vehicle_device);
                        printf("============================================ \r\n");
                    }        
                }
                else
                {
                    printf("SKIP VEHICLE ( NO VEHICLE WEB SOCKET ) \r\n");
                    printf("-------------------------------------- \r\n");
                }    
            }
            else
            {
                printf("SKIP VEHICLE ( NO VEHICLE INFO ) \r\n");
                printf("-------------------------------- \r\n");    
            }
        }//end loop vehicle
		
        unset($datalog);
        $datalog["cron_name"] = "Playback Report";
        $datalog["cron_start_data"] = $startdate;
        $datalog["cron_end_data"] = $enddate;
        $datalog["cron_start_proses"] = $startproses;
        $datalog["cron_end_proses"] = date("Y-m-d H:i:s");
        $this->dbtrans->insert("log_cronjobs",$datalog);
        
		$finishtime = date("d-m-Y H:i:s");
        printf("AUTOREPORT PLAYBACK DONE %s\r\n",$finishtime);
	}
	
    function playback_peruser($userid = "", $startdate = "", $enddate = "")
    {
        printf("PROSES AUTO REPORT PLAYBACK >> START \r\n");
        $startproses = date("Y-m-d H:i:s");
        
        include 'class/PHPExcel.php';
        include 'class/PHPExcel/Writer/Excel2007.php';
        
        $report_type = "playback";
        $process_date = date("Y-m-d H:i:s");
        $domain_server = "http://202.129.190.194/";
        $z = 0;

        if ($startdate == "") {
            $startdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
            $datefilenamestart = date("Ymd", strtotime("yesterday")); 
        }
        
        if ($startdate != "")
        {
            $datefilenamestart = date("Ymd", strtotime($startdate));     
            $startdate = date("Y-m-d 00:00:00", strtotime($startdate));
        }
        
        if ($enddate != "")
        {
            $enddate = date("Y-m-d 23:59:59", strtotime($enddate));
            $datefilenameend = date("Ymd", strtotime($enddate));
        }
        
        if ($enddate == "") {
            $enddate = date("Y-m-d 23:59:59", strtotime("yesterday"));
            $datefilenameend = date("Ymd", strtotime("yesterday"));
        }
        
        $datefilename = $datefilenamestart."_".$datefilenameend;
        
        $month = date("F", strtotime($startdate));
        $year = date("Y", strtotime($startdate));
        
        $sdate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($startdate)));
        $edate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($enddate)));
               
        if ($userid != "")
        {
            $this->db->where("vehicle_user_id", $userid);    
        } 

        $this->db->join("user", "vehicle_user_id = user_id", "left outer");
        $this->db->where("vehicle_status <>", 3);
        $q = $this->db->get("vehicle");
        $rowvehicle = $q->result();
        
        $total_process = count($rowvehicle);
        printf("TOTAL PROSES VEHICLE : %s \r\n",$total_process);
        printf("============================================ \r\n");
        
        for($x=0;$x<count($rowvehicle);$x++) 
        {
            printf("PROSES VEHICLE : %s (%d/%d) \r\n",$rowvehicle[$x]->vehicle_device, ++$z, $total_process);
            
					$istbl_history = $this->config->item("dbhistory_default");
					if($this->config->item("is_dbhistory") == 1)
					{
						$istbl_history = $rowvehicle[$x]->vehicle_dbhistory_name;
					}
					$this->dbhist = $this->load->database($istbl_history, TRUE);
					$this->dbhist2 = $this->load->database("gpshistory2",true);
                    $this->dbtrans = $this->load->database("transporter",true);
        
                    unset($data_insert);
                    
                    //cek apakah sudah pernah ada filenya
                    $this->dbtrans->where("autoreport_vehicle_id",$rowvehicle[$x]->vehicle_id);
                    $this->dbtrans->where("autoreport_data_startdate",$startdate);
                    $this->dbtrans->limit(1);
                    $qrpt = $this->dbtrans->get("autoreport_playback");
                    $rrpt = $qrpt->row();
                    
                    if (count($rrpt)>0)
                    {
                        printf("VEHICLE %s SUDAH PERNAH DI PROSES, HAPUS DULU DATA SEBELUMNYA \r\n", $rowvehicle[$x]->vehicle_device);     
                    }
                    else
                    {
                        $objPHPExcel = new PHPExcel();
                        $objPHPExcel->getProperties()->setCreator("lacak-mobil.com");
                        $objPHPExcel->getProperties()->setLastModifiedBy("lacak-mobil.com");
                        $objPHPExcel->getProperties()->setTitle("Playback Report");
                        $objPHPExcel->getProperties()->setSubject("Playback Report Lacak-mobil.com");
                        $objPHPExcel->getProperties()->setDescription("Playback Report Lacak-mobil.com");    
                
                        $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(1);
                        $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.50);
                        $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.50);
                        $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(1);
                        $objPHPExcel->getDefaultStyle()->getFont()->setName('Times New Roman');
                        $objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
                        $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
                        $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
                        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
                        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
                        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
                        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);            
                        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
                        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
                        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
                        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
                        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(45);
                        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(45);
                
                        //Header
                        $objPHPExcel->setActiveSheetIndex(0);
                        $objPHPExcel->getActiveSheet()->mergeCells('A1:J1');
                        $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'PLAYBACK REPORT');
                        $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
                        $objPHPExcel->getActiveSheet()->getStyle('B3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                        $objPHPExcel->getActiveSheet()->getStyle('B3')->getFont()->setBold(true);
                
                        //Top Header
                        $objPHPExcel->getActiveSheet()->SetCellValue('A5', 'No');
                        $objPHPExcel->getActiveSheet()->SetCellValue('B5', 'Vehicle');
                        $objPHPExcel->getActiveSheet()->SetCellValue('C5', 'Engine');
                        $objPHPExcel->getActiveSheet()->SetCellValue('D5', 'Start Time');
                        $objPHPExcel->getActiveSheet()->SetCellValue('E5', 'End Time');
                        $objPHPExcel->getActiveSheet()->SetCellValue('F5', 'Duration');
                        $objPHPExcel->getActiveSheet()->SetCellValue('G5', 'Trip Mileage');
                        $objPHPExcel->getActiveSheet()->SetCellValue('H5', 'Cummulative Mileage');
                        $objPHPExcel->getActiveSheet()->SetCellValue('I5', 'Location Start');
                        $objPHPExcel->getActiveSheet()->SetCellValue('J5', 'Location End');
                        $objPHPExcel->getActiveSheet()->getStyle('A5:J5')->getFont()->setBold(true);
                        $objPHPExcel->getActiveSheet()->getStyle('A5:J5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $objPHPExcel->getActiveSheet()->getStyle('A5:J5')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                        $objPHPExcel->getActiveSheet()->getStyle('A5:J5')->getFill()->getStartColor()->setRGB('bfbfbf;');
                
                        $vehicle_device = explode("@", $rowvehicle[$x]->vehicle_device);  
                        $vehicle_no = $rowvehicle[$x]->vehicle_no;
                
                        $ex_vno = explode("/",$vehicle_no);

                        if ($rowvehicle[$x]->vehicle_type == "T5" || $rowvehicle[$x]->vehicle_type == "T5 PULSE")
                        {
                            $table = $vehicle_device[0]."@t5_gps";
                            $tableinfo = $vehicle_device[0]."@t5_info";    
                        }
                        else
                        {
                            $table = strtolower($vehicle_device[0])."@".strtolower($vehicle_device[1])."_gps";
                            $tableinfo = strtolower($vehicle_device[0])."@".strtolower($vehicle_device[1])."_info";
                        }
            
                        $report_path = "/home/lacakmobil/public_html/transporter.lacak-mobil.com/public/assets/media/autoreport/".$report_type."/".
                                $year."/".$month."/".$vehicle_device[0]."/";
                        $pub_path = "assets/media/autoreport/".$report_type."/".$year."/".$month."/".$vehicle_device[0]."/";
            
                        $this->dbhist->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");    
                        $this->dbhist->where_in("gps_info_device", $rowvehicle[$x]->vehicle_device);
                        $this->dbhist->where("gps_time >=", $sdate);
                        $this->dbhist->where("gps_time <=", $edate);    
                        $this->dbhist->order_by("gps_time","asc");
                        $this->dbhist->from($table);
                        $q = $this->dbhist->get();
                        $rows1 = $q->result();
						
						$this->dbhist2->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");    
                        $this->dbhist2->where_in("gps_info_device", $rowvehicle[$x]->vehicle_device);
                        $this->dbhist2->where("gps_time >=", $sdate);
                        $this->dbhist2->where("gps_time <=", $edate);    
                        $this->dbhist2->order_by("gps_time","asc");
                        $this->dbhist2->from($table);
                        $q2 = $this->dbhist2->get();
                        $rows2 = $q2->result();
						
						$rows = array_merge($rows1, $rows2);
                
                        $data = array(); // initialization variable
                        $vehicle_device = "";
                        $engine = "";
                
                        foreach($rows as $obj)
                        {
                            if($vehicle_device != $rowvehicle[$x]->vehicle_device)
                            {
                                $no=0;
                                $no_data = 1;
                            }
                
                            if(substr($obj->gps_info_io_port, 4, 1) == 1)
                            { //engine ON
                                if($engine != "ON") 
                                {
                                    $no++;
                                    $no_data = 1;
                                }
                    
                                if($no == 0) $no++;
                                if($no_data == 1)
                                {
                                    $data[$rowvehicle[$x]->vehicle_no."#".$rowvehicle[$x]->vehicle_name][$no]['ON']['start'] = $obj;
                                }
                                else
                                {
                                    $data[$rowvehicle[$x]->vehicle_no."#".$rowvehicle[$x]->vehicle_name][$no]['ON']['end'] = $obj;
                                }
                                $no_data++;
                                $engine = "ON";
                            }
                
                            if(substr($obj->gps_info_io_port, 4, 1) == 0)
                            { //engine OFF
                                if($engine != "OFF") 
                                {
                                    $no++;
                                    $no_data = 1;
                                }
                    
                                if($no == 0) $no++;
                                if($no_data == 1)
                                {
                                    $data[$rowvehicle[$x]->vehicle_no."#".$rowvehicle[$x]->vehicle_name][$no]['OFF']['start'] = $obj;
                                }
                                else
                                {
                                    $data[$rowvehicle[$x]->vehicle_no."#".$rowvehicle[$x]->vehicle_name][$no]['OFF']['end'] = $obj;
                                }
                    
                                $no_data++;
                                $engine = "OFF";
                            }
                    
                            $vehicle_device = $rowvehicle[$x]->vehicle_device;
                        }//end loop foreach rows
                
                        $i=1;
                        $cummulative = 0; 
                        printf("WRITE DATA EXCEL : ");
                        foreach($data as $vehicles=>$value_vehicles)
                        {
                            foreach($value_vehicles as $number=>$value_number)
                            {
                                foreach($value_number as $engine=>$report)
                                {
                                    if(!isset($report['end']))
                                    {
                                        $report['end'] = $report['start'];
                                    }
                            
                                    $data_report[$vehicles][$number][$engine]['start'] = $report['start'];
                                    $data_report[$vehicles][$number][$engine]['end'] = $report['end'];
                                    $duration = get_time_difference($report['start']->gps_time, $report['end']->gps_time);
                       
                                    $show_duration = "";
                                    if($duration[0]!=0)
                                    {
                                        $show_duration .= $duration[0] ." Day ";
                                    }
                        
                                    if($duration[1]!=0)
                                    {
                                        $show_duration .= $duration[1] ." Hour ";
                                    }
                        
                                    if($duration[2]!=0)
                                    {
                                        $show_duration .= $duration[2] ." Min";
                                    }
                        
                                    if($show_duration == "")
                                    {
                                        $show_duration .= "0 Min";
                                    }
                        
                                    $data_report[$vehicles][$number][$engine]['duration'] = $show_duration;
                                    $mileage = round(($report['end']->gps_info_distance - $report['start']->gps_info_distance)/1000, 2);
                                    $data_report[$vehicles][$number][$engine]['mileage'] = round($mileage, 2);
                        
                                    $location_start = $this->getPosition($report['start']->gps_longitude, $report['start']->gps_ew, $report['start']->gps_latitude, $report['start']->gps_ns);
                                    $data_report[$vehicles][$number][$engine]['location_start'] = $location_start;
                        
                                    $geofence_start = $this->getGeofence_location($report['start']->gps_longitude, $report['start']->gps_ew, $report['start']->gps_latitude, $report['start']->gps_ns, $report['start']->gps_name);
                                    $data_report[$vehicles][$number][$engine]['geofence_start'] = $geofence_start;
                        
                                    $location_end = $this->getPosition($report['end']->gps_longitude, $report['end']->gps_ew, $report['end']->gps_latitude, $report['end']->gps_ns);
                                    $data_report[$vehicles][$number][$engine]['location_end'] = $location_end;
                        
                                    $geofence_end = $this->getGeofence_location($report['end']->gps_longitude, $report['end']->gps_ew, $report['end']->gps_latitude, $report['end']->gps_ns, $report['end']->gps_name);
                                    $data_report[$vehicles][$number][$engine]['geofence_end'] = $geofence_end;
                        
                                    $cummulative += $data_report[$vehicles][$number][$engine]['mileage'];
                                    //print_r($data_report[$vehicles][$number][$engine]['location_start']->display_name);exit();
                        
                                    printf("|%s|",$i);
                                    $objPHPExcel->getActiveSheet()->SetCellValue('A'.(5+$i), $i);
                                    $objPHPExcel->getActiveSheet()->getStyle('A'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                    $objPHPExcel->getActiveSheet()->SetCellValue('B'.(5+$i), $rowvehicle[$x]->vehicle_no);
                                    $objPHPExcel->getActiveSheet()->getStyle('B'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                    $objPHPExcel->getActiveSheet()->SetCellValue('C'.(5+$i), $engine);
                                    $objPHPExcel->getActiveSheet()->getStyle('C'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                    $objPHPExcel->getActiveSheet()->SetCellValue('D'.(5+$i), date("d/m/Y H:i:s", strtotime("+7 hour", strtotime($report['start']->gps_time))));
                                    $objPHPExcel->getActiveSheet()->getStyle('D'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                    $objPHPExcel->getActiveSheet()->SetCellValue('E'.(5+$i), date("d/m/Y H:i:s", strtotime("+7 hour", strtotime($report['end']->gps_time))));
                                    $objPHPExcel->getActiveSheet()->getStyle('E'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                    $objPHPExcel->getActiveSheet()->SetCellValue('F'.(5+$i), $data_report[$vehicles][$number][$engine]['duration']);
                                    $objPHPExcel->getActiveSheet()->getStyle('F'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            
                                    //mileage
                                    $xme = $data_report[$vehicles][$number][$engine]['mileage'];
                                    $xxme = explode(".",$xme);
                                    if (isset($xxme[1]))
                                    {
                                        $xsub = substr($xxme[1],0,2); 
                                        $x_mile = $xxme[0].".".$xsub;     
                                    }
                                    else
                                    {
                                        $x_mile = $xxme[0];
                                    }
                            
                                    //cummulative mileage
                                    $xcum = $cummulative;
                                    $xxcum = explode(".",$xcum);
                                    if (isset($xxcum[1]))
                                    {
                                        $xcumsub = substr($xxcum[1],0,2); 
                                        $x_cum = $xxcum[0].".".$xcumsub;     
                                    }
                                    else
                                    {
                                        $x_cum = $xxcum[0];
                                    }
                            
                            
                                    $objPHPExcel->getActiveSheet()->SetCellValue('G'.(5+$i), $x_mile." "."KM");
                                    $objPHPExcel->getActiveSheet()->getStyle('G'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                    $objPHPExcel->getActiveSheet()->SetCellValue('H'.(5+$i), $x_cum." "."KM");
                                    $objPHPExcel->getActiveSheet()->getStyle('H'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        
                                    if (isset($data_report[$vehicles][$number][$engine]['geofence_start']) && ($data_report[$vehicles][$number][$engine]['geofence_start'] != ""))
                                    {
                                        $z = $data_report[$vehicles][$number][$engine]['geofence_start'];
                                        $y = explode("#",$z);
                            
                                        $valexcel = "Geofence : "." ".$y[1]." ".($data_report[$vehicles][$number][$engine]['location_start']->display_name);
                                        $objPHPExcel->getActiveSheet()->SetCellValue('I'.(5+$i), $valexcel);
                                        $objPHPExcel->getActiveSheet()->getStyle('I'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                    } 
                                    else
                                    {
                                        $objPHPExcel->getActiveSheet()->SetCellValue('I'.(5+$i), ($data_report[$vehicles][$number][$engine]['location_start']->display_name));
                                        $objPHPExcel->getActiveSheet()->getStyle('I'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                    }
                        
                                    if (isset($data_report[$vehicles][$number][$engine]['geofence_end']) && ($data_report[$vehicles][$number][$engine]['geofence_end'] != ""))
                                    {
                                        $j = $data_report[$vehicles][$number][$engine]['geofence_end'];
                                        $k = explode("#",$j);
                                        $valexcel = "Geofence : "." ".$k[1]." ".($data_report[$vehicles][$number][$engine]['location_end']->display_name);
                                        $objPHPExcel->getActiveSheet()->SetCellValue('J'.(5+$i), $valexcel);
                                        $objPHPExcel->getActiveSheet()->getStyle('J'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                    }
                                    else
                                    {
                                        $objPHPExcel->getActiveSheet()->SetCellValue('J'.(5+$i), ($data_report[$vehicles][$number][$engine]['location_end']->display_name));
                                        $objPHPExcel->getActiveSheet()->getStyle('J'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                    }
                                    $i++;
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
            
                        $objPHPExcel->getActiveSheet()->getStyle('A5:J'.(4+$i))->applyFromArray($styleArray);
                        $objPHPExcel->getActiveSheet()->getStyle('A5:J'.(4+$i))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
                        $objPHPExcel->getActiveSheet()->getStyle('A5:J'.(4+$i))->getAlignment()->setWrapText(true);
                        // Rename sheet
                        $objPHPExcel->getActiveSheet()->setTitle('Playback_Report');
            
                        printf("CREATE FILE FOR : %s \r\n",$rowvehicle[$x]->vehicle_device);
                        
                        // Save Excel
                        $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
                        @mkdir($report_path, DIR_WRITE_MODE); //REPORT_PATH -> config->constant
                
                        if (isset($ex_vno))
                        {
                            if (isset($ex_vno[1]))
                            {
                                $filecreatedname = "Playback_".$ex_vno[0].$ex_vno[1]."_".$datefilename.".xls";    
                            }
                            else
                            {
                                $filecreatedname = "Playback_".$ex_vno[0]."_".$datefilename.".xls";       
                            }
                        }
                        else
                        {
                            $filecreatedname = "Playback_".$vehicle_no."_".$datefilename.".xls";  
                        }
                  
                        $objWriter->save($report_path.$filecreatedname);
                        printf("CREATE FILE DONE : %s \r\n",$filecreatedname);
            
                        $public_path = $domain_server.$pub_path.$filecreatedname;
            
                        printf("INSERT TO DATABASE TRANSPORTER \r\n");
                        $data_insert["autoreport_vehicle_id"] = $rowvehicle[$x]->vehicle_id;
                        $data_insert["autoreport_vehicle_device"] = $rowvehicle[$x]->vehicle_device;
                        $data_insert["autoreport_user_id"] = $rowvehicle[$x]->user_id;
                        $data_insert["autoreport_company"] = $rowvehicle[$x]->user_company;
                        $data_insert["autoreport_data_startdate"] = $startdate;
                        $data_insert["autoreport_data_enddate"] = $enddate;
                        $data_insert["autoreport_type"] = $report_type;
                        $data_insert["autoreport_file_path"] = $report_path;
                        $data_insert["autoreport_filename"] = $filecreatedname;
                        $data_insert["autoreport_download_path"] = $report_path.$filecreatedname;
                        $data_insert["autoreport_public_path"] = $public_path;
                        $data_insert["autoreport_process_date"] = $process_date;
                        $data_insert["autoreport_insert_db"] = date("Y-m-d H:i:s");
            
                        $this->dbtrans->insert("autoreport_playback",$data_insert);
                        printf("INSERT OK \r\n");
            
                        printf("DELETE CACHE HISTORY \r\n");
                        $this->dbhist->cache_delete_all();
                        $this->dbtrans->cache_delete_all();
                        unset($data);
            
                        printf("FINISH FOR VEHICLE : %s \r\n",$rowvehicle[$x]->vehicle_device);
                        printf("============================================ \r\n");
                    }        
        }//end loop vehicle
        
        unset($datalog);
        $datalog["cron_name"] = "Autoreport Trip Mileage";
        $datalog["cron_start_data"] = $startdate;
        $datalog["cron_end_data"] = $enddate;
        $datalog["cron_start_proses"] = $startproses;
        $datalog["cron_end_proses"] = date("Y-m-d H:i:s");
        $this->dbtrans->insert("log_cronjobs",$datalog);
        
        printf("AUTOREPORT PLAYBACK DONE \r\n");
    }
    
    function playback_pervehicle($name = "", $host = "", $startdate = "", $enddate = "")
    {
        printf("PROSES AUTO REPORT PLAYBACK >> START \r\n");
        $startproses = date("Y-m-d H:i:s");
        
        include 'class/PHPExcel.php';
        include 'class/PHPExcel/Writer/Excel2007.php';
        
        $report_type = "playback";
        $process_date = date("Y-m-d H:i:s");
        $domain_server = "http://202.129.190.194/";
        $z = 0;

        if ($startdate == "") {
            $startdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
            $datefilenamestart = date("Ymd", strtotime("yesterday")); 
        }
        
        if ($startdate != "")
        {
            $datefilenamestart = date("Ymd", strtotime($startdate));     
            $startdate = date("Y-m-d 00:00:00", strtotime($startdate));
        }
        
        if ($enddate != "")
        {
            $enddate = date("Y-m-d 23:59:59", strtotime($enddate));
            $datefilenameend = date("Ymd", strtotime($enddate));
        }
        
        if ($enddate == "") {
            $enddate = date("Y-m-d 23:59:59", strtotime("yesterday"));
            $datefilenameend = date("Ymd", strtotime("yesterday"));
        }
        
        $datefilename = $datefilenamestart."_".$datefilenameend;
        
        $month = date("F", strtotime($startdate));
        $year = date("Y", strtotime($startdate));
        
        $sdate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($startdate)));
        $edate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($enddate)));
        
        
        if ($name != "" && $host != "")
        {
            $this->db->where("vehicle_device", $name."@".$host);    
        } 

        $this->db->join("user", "vehicle_user_id = user_id", "left outer");
        $this->db->where("vehicle_status <>", 3);
        $q = $this->db->get("vehicle");
        $rowvehicle = $q->result();
        
        $total_process = count($rowvehicle);
        printf("TOTAL PROSES VEHICLE : %s \r\n",$total_process);
        printf("============================================ \r\n");
        
        for($x=0;$x<count($rowvehicle);$x++) 
        {
            printf("PROSES VEHICLE : %s (%d/%d) \r\n",$rowvehicle[$x]->vehicle_device, ++$z, $total_process);
            
					$istbl_history = $this->config->item("dbhistory_default");
					if($this->config->item("is_dbhistory") == 1)
					{
						$istbl_history = $rowvehicle[$x]->vehicle_dbhistory_name;
					}
					$this->dbhist = $this->load->database($istbl_history, TRUE);
					$this->dbhist2 = $this->load->database("gpshistory2",true);
                    $this->dbtrans = $this->load->database("transporter",true);
        
                    unset($data_insert);
                    
                    //cek apakah sudah pernah ada filenya
                    $this->dbtrans->where("autoreport_vehicle_id",$rowvehicle[$x]->vehicle_id);
                    $this->dbtrans->where("autoreport_data_startdate",$startdate);
                    $this->dbtrans->limit(1);
                    $qrpt = $this->dbtrans->get("autoreport_playback");
                    $rrpt = $qrpt->row();
                    
                    if (count($rrpt)>0)
                    {
                        printf("VEHICLE %s SUDAH PERNAH DI PROSES, HAPUS DULU DATA SEBELUMNYA \r\n", $rowvehicle[$x]->vehicle_device);     
                    }
                    else
                    {
                        $objPHPExcel = new PHPExcel();
                        $objPHPExcel->getProperties()->setCreator("lacak-mobil.com");
                        $objPHPExcel->getProperties()->setLastModifiedBy("lacak-mobil.com");
                        $objPHPExcel->getProperties()->setTitle("Playback Report");
                        $objPHPExcel->getProperties()->setSubject("Playback Report Lacak-mobil.com");
                        $objPHPExcel->getProperties()->setDescription("Playback Report Lacak-mobil.com");    
                
                        $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(1);
                        $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.50);
                        $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.50);
                        $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(1);
                        $objPHPExcel->getDefaultStyle()->getFont()->setName('Times New Roman');
                        $objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
                        $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
                        $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
                        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
                        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
                        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
                        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);            
                        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
                        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
                        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
                        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
                        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(45);
                        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(45);
                
                        //Header
                        $objPHPExcel->setActiveSheetIndex(0);
                        $objPHPExcel->getActiveSheet()->mergeCells('A1:J1');
                        $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'PLAYBACK REPORT');
                        $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
                        $objPHPExcel->getActiveSheet()->getStyle('B3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                        $objPHPExcel->getActiveSheet()->getStyle('B3')->getFont()->setBold(true);
                
                        //Top Header
                        $objPHPExcel->getActiveSheet()->SetCellValue('A5', 'No');
                        $objPHPExcel->getActiveSheet()->SetCellValue('B5', 'Vehicle');
                        $objPHPExcel->getActiveSheet()->SetCellValue('C5', 'Engine');
                        $objPHPExcel->getActiveSheet()->SetCellValue('D5', 'Start Time');
                        $objPHPExcel->getActiveSheet()->SetCellValue('E5', 'End Time');
                        $objPHPExcel->getActiveSheet()->SetCellValue('F5', 'Duration');
                        $objPHPExcel->getActiveSheet()->SetCellValue('G5', 'Trip Mileage');
                        $objPHPExcel->getActiveSheet()->SetCellValue('H5', 'Cummulative Mileage');
                        $objPHPExcel->getActiveSheet()->SetCellValue('I5', 'Location Start');
                        $objPHPExcel->getActiveSheet()->SetCellValue('J5', 'Location End');
                        $objPHPExcel->getActiveSheet()->getStyle('A5:J5')->getFont()->setBold(true);
                        $objPHPExcel->getActiveSheet()->getStyle('A5:J5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $objPHPExcel->getActiveSheet()->getStyle('A5:J5')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                        $objPHPExcel->getActiveSheet()->getStyle('A5:J5')->getFill()->getStartColor()->setRGB('bfbfbf;');
                
                        $vehicle_device = explode("@", $rowvehicle[$x]->vehicle_device);  
                        $vehicle_no = $rowvehicle[$x]->vehicle_no;
                
                        $ex_vno = explode("/",$vehicle_no);

                        if ($rowvehicle[$x]->vehicle_type == "T5" || $rowvehicle[$x]->vehicle_type == "T5 PULSE")
                        {
                            $table = $vehicle_device[0]."@t5_gps";
                            $tableinfo = $vehicle_device[0]."@t5_info";    
                        }
                        else
                        {
                            $table = strtolower($vehicle_device[0])."@".strtolower($vehicle_device[1])."_gps";
                            $tableinfo = strtolower($vehicle_device[0])."@".strtolower($vehicle_device[1])."_info";
                        }
            
                        $report_path = "/home/lacakmobil/public_html/transporter.lacak-mobil.com/public/assets/media/autoreport/".$report_type."/".
                                $year."/".$month."/".$vehicle_device[0]."/";
                        $pub_path = "assets/media/autoreport/".$report_type."/".$year."/".$month."/".$vehicle_device[0]."/";
            
                        $this->dbhist->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");    
                        $this->dbhist->where_in("gps_info_device", $rowvehicle[$x]->vehicle_device);
                        $this->dbhist->where("gps_time >=", $sdate);
                        $this->dbhist->where("gps_time <=", $edate);    
                        $this->dbhist->order_by("gps_time","asc");
                        $this->dbhist->from($table);
                        $q = $this->dbhist->get();
                        $rows1 = $q->result();
						
						$this->dbhist2->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");    
                        $this->dbhist2->where_in("gps_info_device", $rowvehicle[$x]->vehicle_device);
                        $this->dbhist2->where("gps_time >=", $sdate);
                        $this->dbhist2->where("gps_time <=", $edate);    
                        $this->dbhist2->order_by("gps_time","asc");
                        $this->dbhist2->from($table);
                        $q2 = $this->dbhist2->get();
                        $rows2 = $q2->result();
						
						$rows = array_merge($rows1, $rows2);
                
                        $data = array(); // initialization variable
                        $vehicle_device = "";
                        $engine = "";
                
                        foreach($rows as $obj)
                        {
                            if($vehicle_device != $rowvehicle[$x]->vehicle_device)
                            {
                                $no=0;
                                $no_data = 1;
                            }
                
                            if(substr($obj->gps_info_io_port, 4, 1) == 1)
                            { //engine ON
                                if($engine != "ON") 
                                {
                                    $no++;
                                    $no_data = 1;
                                }
                    
                                if($no == 0) $no++;
                                if($no_data == 1)
                                {
                                    $data[$rowvehicle[$x]->vehicle_no."#".$rowvehicle[$x]->vehicle_name][$no]['ON']['start'] = $obj;
                                }
                                else
                                {
                                    $data[$rowvehicle[$x]->vehicle_no."#".$rowvehicle[$x]->vehicle_name][$no]['ON']['end'] = $obj;
                                }
                                $no_data++;
                                $engine = "ON";
                            }
                
                            if(substr($obj->gps_info_io_port, 4, 1) == 0)
                            { //engine OFF
                                if($engine != "OFF") 
                                {
                                    $no++;
                                    $no_data = 1;
                                }
                    
                                if($no == 0) $no++;
                                if($no_data == 1)
                                {
                                    $data[$rowvehicle[$x]->vehicle_no."#".$rowvehicle[$x]->vehicle_name][$no]['OFF']['start'] = $obj;
                                }
                                else
                                {
                                    $data[$rowvehicle[$x]->vehicle_no."#".$rowvehicle[$x]->vehicle_name][$no]['OFF']['end'] = $obj;
                                }
                    
                                $no_data++;
                                $engine = "OFF";
                            }
                    
                            $vehicle_device = $rowvehicle[$x]->vehicle_device;
                        }//end loop foreach rows
                
                        $i=1;
                        $cummulative = 0; 
                        printf("WRITE DATA EXCEL : ");
                        foreach($data as $vehicles=>$value_vehicles)
                        {
                            foreach($value_vehicles as $number=>$value_number)
                            {
                                foreach($value_number as $engine=>$report)
                                {
                                    if(!isset($report['end']))
                                    {
                                        $report['end'] = $report['start'];
                                    }
                            
                                    $data_report[$vehicles][$number][$engine]['start'] = $report['start'];
                                    $data_report[$vehicles][$number][$engine]['end'] = $report['end'];
                                    $duration = get_time_difference($report['start']->gps_time, $report['end']->gps_time);
                       
                                    $show_duration = "";
                                    if($duration[0]!=0)
                                    {
                                        $show_duration .= $duration[0] ." Day ";
                                    }
                        
                                    if($duration[1]!=0)
                                    {
                                        $show_duration .= $duration[1] ." Hour ";
                                    }
                        
                                    if($duration[2]!=0)
                                    {
                                        $show_duration .= $duration[2] ." Min";
                                    }
                        
                                    if($show_duration == "")
                                    {
                                        $show_duration .= "0 Min";
                                    }
                        
                                    $data_report[$vehicles][$number][$engine]['duration'] = $show_duration;
                                    $mileage = round(($report['end']->gps_info_distance - $report['start']->gps_info_distance)/1000, 2);
                                    $data_report[$vehicles][$number][$engine]['mileage'] = round($mileage, 2);
                        
                                    $location_start = $this->getPosition($report['start']->gps_longitude, $report['start']->gps_ew, $report['start']->gps_latitude, $report['start']->gps_ns);
                                    $data_report[$vehicles][$number][$engine]['location_start'] = $location_start;
                        
                                    $geofence_start = $this->getGeofence_location($report['start']->gps_longitude, $report['start']->gps_ew, $report['start']->gps_latitude, $report['start']->gps_ns, $report['start']->gps_name);
                                    $data_report[$vehicles][$number][$engine]['geofence_start'] = $geofence_start;
                        
                                    $location_end = $this->getPosition($report['end']->gps_longitude, $report['end']->gps_ew, $report['end']->gps_latitude, $report['end']->gps_ns);
                                    $data_report[$vehicles][$number][$engine]['location_end'] = $location_end;
                        
                                    $geofence_end = $this->getGeofence_location($report['end']->gps_longitude, $report['end']->gps_ew, $report['end']->gps_latitude, $report['end']->gps_ns, $report['end']->gps_name);
                                    $data_report[$vehicles][$number][$engine]['geofence_end'] = $geofence_end;
                        
                                    $cummulative += $data_report[$vehicles][$number][$engine]['mileage'];
                                    //print_r($data_report[$vehicles][$number][$engine]['location_start']->display_name);exit();
                        
                                    printf("|%s|",$i);
                                    $objPHPExcel->getActiveSheet()->SetCellValue('A'.(5+$i), $i);
                                    $objPHPExcel->getActiveSheet()->getStyle('A'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                    $objPHPExcel->getActiveSheet()->SetCellValue('B'.(5+$i), $rowvehicle[$x]->vehicle_no);
                                    $objPHPExcel->getActiveSheet()->getStyle('B'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                    $objPHPExcel->getActiveSheet()->SetCellValue('C'.(5+$i), $engine);
                                    $objPHPExcel->getActiveSheet()->getStyle('C'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                    $objPHPExcel->getActiveSheet()->SetCellValue('D'.(5+$i), date("d/m/Y H:i:s", strtotime("+7 hour", strtotime($report['start']->gps_time))));
                                    $objPHPExcel->getActiveSheet()->getStyle('D'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                    $objPHPExcel->getActiveSheet()->SetCellValue('E'.(5+$i), date("d/m/Y H:i:s", strtotime("+7 hour", strtotime($report['end']->gps_time))));
                                    $objPHPExcel->getActiveSheet()->getStyle('E'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                    $objPHPExcel->getActiveSheet()->SetCellValue('F'.(5+$i), $data_report[$vehicles][$number][$engine]['duration']);
                                    $objPHPExcel->getActiveSheet()->getStyle('F'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            
                                    //mileage
                                    $xme = $data_report[$vehicles][$number][$engine]['mileage'];
                                    $xxme = explode(".",$xme);
                                    if (isset($xxme[1]))
                                    {
                                        $xsub = substr($xxme[1],0,2); 
                                        $x_mile = $xxme[0].".".$xsub;     
                                    }
                                    else
                                    {
                                        $x_mile = $xxme[0];
                                    }
                            
                                    //cummulative mileage
                                    $xcum = $cummulative;
                                    $xxcum = explode(".",$xcum);
                                    if (isset($xxcum[1]))
                                    {
                                        $xcumsub = substr($xxcum[1],0,2); 
                                        $x_cum = $xxcum[0].".".$xcumsub;     
                                    }
                                    else
                                    {
                                        $x_cum = $xxcum[0];
                                    }
                            
                            
                                    $objPHPExcel->getActiveSheet()->SetCellValue('G'.(5+$i), $x_mile." "."KM");
                                    $objPHPExcel->getActiveSheet()->getStyle('G'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                    $objPHPExcel->getActiveSheet()->SetCellValue('H'.(5+$i), $x_cum." "."KM");
                                    $objPHPExcel->getActiveSheet()->getStyle('H'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        
                                    if (isset($data_report[$vehicles][$number][$engine]['geofence_start']) && ($data_report[$vehicles][$number][$engine]['geofence_start'] != ""))
                                    {
                                        $z = $data_report[$vehicles][$number][$engine]['geofence_start'];
                                        $y = explode("#",$z);
                            
                                        $valexcel = "Geofence : "." ".$y[1]." ".($data_report[$vehicles][$number][$engine]['location_start']->display_name);
                                        $objPHPExcel->getActiveSheet()->SetCellValue('I'.(5+$i), $valexcel);
                                        $objPHPExcel->getActiveSheet()->getStyle('I'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                    } 
                                    else
                                    {
                                        $objPHPExcel->getActiveSheet()->SetCellValue('I'.(5+$i), ($data_report[$vehicles][$number][$engine]['location_start']->display_name));
                                        $objPHPExcel->getActiveSheet()->getStyle('I'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                    }
                        
                                    if (isset($data_report[$vehicles][$number][$engine]['geofence_end']) && ($data_report[$vehicles][$number][$engine]['geofence_end'] != ""))
                                    {
                                        $j = $data_report[$vehicles][$number][$engine]['geofence_end'];
                                        $k = explode("#",$j);
                                        $valexcel = "Geofence : "." ".$k[1]." ".($data_report[$vehicles][$number][$engine]['location_end']->display_name);
                                        $objPHPExcel->getActiveSheet()->SetCellValue('J'.(5+$i), $valexcel);
                                        $objPHPExcel->getActiveSheet()->getStyle('J'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                    }
                                    else
                                    {
                                        $objPHPExcel->getActiveSheet()->SetCellValue('J'.(5+$i), ($data_report[$vehicles][$number][$engine]['location_end']->display_name));
                                        $objPHPExcel->getActiveSheet()->getStyle('J'.(5+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                    }
                                    $i++;
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
            
                        $objPHPExcel->getActiveSheet()->getStyle('A5:J'.(4+$i))->applyFromArray($styleArray);
                        $objPHPExcel->getActiveSheet()->getStyle('A5:J'.(4+$i))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
                        $objPHPExcel->getActiveSheet()->getStyle('A5:J'.(4+$i))->getAlignment()->setWrapText(true);
                        // Rename sheet
                        $objPHPExcel->getActiveSheet()->setTitle('Playback_Report');
            
                        printf("CREATE FILE FOR : %s \r\n",$rowvehicle[$x]->vehicle_device);
                        
                        // Save Excel
                        $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
                        @mkdir($report_path, DIR_WRITE_MODE); //REPORT_PATH -> config->constant
                
                        if (isset($ex_vno))
                        {
                            if (isset($ex_vno[1]))
                            {
                                $filecreatedname = "Playback_".$ex_vno[0].$ex_vno[1]."_".$datefilename.".xls";    
                            }
                            else
                            {
                                $filecreatedname = "Playback_".$ex_vno[0]."_".$datefilename.".xls";       
                            }
                        }
                        else
                        {
                            $filecreatedname = "Playback_".$vehicle_no."_".$datefilename.".xls";  
                        }
                  
                        $objWriter->save($report_path.$filecreatedname);
                        printf("CREATE FILE DONE : %s \r\n",$filecreatedname);
            
                        $public_path = $domain_server.$pub_path.$filecreatedname;
            
                        printf("INSERT TO DATABASE TRANSPORTER \r\n");
                        $data_insert["autoreport_vehicle_id"] = $rowvehicle[$x]->vehicle_id;
                        $data_insert["autoreport_vehicle_device"] = $rowvehicle[$x]->vehicle_device;
                        $data_insert["autoreport_user_id"] = $rowvehicle[$x]->user_id;
                        $data_insert["autoreport_company"] = $rowvehicle[$x]->user_company;
                        $data_insert["autoreport_data_startdate"] = $startdate;
                        $data_insert["autoreport_data_enddate"] = $enddate;
                        $data_insert["autoreport_type"] = $report_type;
                        $data_insert["autoreport_file_path"] = $report_path;
                        $data_insert["autoreport_filename"] = $filecreatedname;
                        $data_insert["autoreport_download_path"] = $report_path.$filecreatedname;
                        $data_insert["autoreport_public_path"] = $public_path;
                        $data_insert["autoreport_process_date"] = $process_date;
                        $data_insert["autoreport_insert_db"] = date("Y-m-d H:i:s");
            
                        $this->dbtrans->insert("autoreport_playback",$data_insert);
                        printf("INSERT OK \r\n");
            
                        printf("DELETE CACHE HISTORY \r\n");
                        $this->dbhist->cache_delete_all();
                        $this->dbtrans->cache_delete_all();
                        unset($data);
            
                        printf("FINISH FOR VEHICLE : %s \r\n",$rowvehicle[$x]->vehicle_device);
                        printf("============================================ \r\n");
                    }        
        }//end loop vehicle
        
        unset($datalog);
        $datalog["cron_name"] = "Autoreport Trip Mileage";
        $datalog["cron_start_data"] = $startdate;
        $datalog["cron_end_data"] = $enddate;
        $datalog["cron_start_proses"] = $startproses;
        $datalog["cron_end_proses"] = date("Y-m-d H:i:s");
        $this->dbtrans->insert("log_cronjobs",$datalog);
        
        printf("AUTOREPORT PLAYBACK DONE \r\n");
    }
    
    function getGeofence_location($longitude, $ew, $latitude, $ns, $vehicle_device) 
    {
        
        $this->db = $this->load->database("default", true);
        $lng = getLongitude($longitude, $ew);
        $lat = getLatitude($latitude, $ns);
                                                                           
        $sql = sprintf("
                    SELECT     *
                    FROM     %sgeofence 
                    WHERE     TRUE
                            AND CONTAINS(geofence_polygon, GEOMFROMTEXT('POINT(%s %s)')) 
                            AND (geofence_vehicle = '%s' )
                            AND (geofence_status = 1)
                    LIMIT 1 OFFSET 0", $this->db->dbprefix, $lng, $lat, $vehicle_device);

        $q = $this->db->query($sql);

        if ($q->num_rows() > 0)
        {            
            $row = $q->result();
            $total = $q->num_rows();
            for ($i=0;$i<$total;$i++){
            $data = $row[$i]->geofence_name;
            return $data;
            }
            
        }else
        {
            return false;
        }

    }
    
    function getPosition($longitude, $ew, $latitude, $ns){
        $gps_longitude_real = getLongitude($longitude, $ew);
        $gps_latitude_real = getLatitude($latitude, $ns);
                    
        $gps_longitude_real_fmt = number_format($gps_longitude_real, 4, ".", "");
        $gps_latitude_real_fmt = number_format($gps_latitude_real, 4, ".", "");    
                    
        $georeverse = $this->gpsmodel->GeoReverse($gps_latitude_real_fmt, $gps_longitude_real_fmt);
        
        return $georeverse;
    }
	
	function history_pervehicle_csv($datatype=0, $startdate="", $enddate="", $name="", $host="")
    {
        $startproses = date("Y-m-d H:i:s");
        $dbname = "gpshistory";
        $order = "desc";
        $limit = "100000";
        $offset = 0;
        $z = 0;
        
        $totalodometer = 0;
        $totalodometer1 = 0;
        $process_date = date("Y-m-d H:i:s");
		
        $this->dbhist = $this->load->database($dbname, TRUE);

		$this->dbhist2 = $this->load->database("gpshistory2", TRUE);
		
		//$this->dbhist2 = $this->load->database("gpsarchive", TRUE);
		
        $this->dbtrans = $this->load->database("transporter",true);
        
        $report_type = "history_report";
        
        if ($startdate == "") 
        {
            $startdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
            $datefilename = date("Ymd", strtotime("yesterday")); 
        }
        
        if ($startdate != "")
        {
            $datefilename = date("Ymd", strtotime($startdate));     
            $startdate = date("Y-m-d 00:00:00", strtotime($startdate));
        }
        
        if ($enddate != "")
        {
            $enddate = date("Y-m-d 23:59:59", strtotime($enddate));
        }
        
        if ($enddate == "") 
        {
            $enddate = date("Y-m-d 23:59:59", strtotime("yesterday"));
        }
        
        $month = date("F", strtotime($startdate));
        $year = date("Y", strtotime($startdate));
        
        $sdate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($startdate)));
        $edate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($enddate)));
        
        if ($name != "" && $host != "")
        {
            $this->db->where("vehicle_device", $name.'@'.$host);    
        }                                                                  
        
        $this->db->join("user", "vehicle_user_id = user_id", "left outer");
        $this->db->where("vehicle_status <>", 3);
        $q = $this->db->get("vehicle");
        $rowvehicle = $q->result();
        
        $total_process = count($rowvehicle);
       
        for($x=0;$x<count($rowvehicle);$x++)
        {
                        $vehicle_device = explode("@", $rowvehicle[$x]->vehicle_device);  
                        $vehicle_no = $rowvehicle[$x]->vehicle_no;
                
                        $ex_vno = explode("/",$vehicle_no);
                        $name = $vehicle_device[0];
                        $host = $vehicle_device[1];
            
                        if ($rowvehicle[$x]->vehicle_type == "T5" || $rowvehicle[$x]->vehicle_type == "T5 PULSE")
                        {
                            $tables = $name."@t5_gps";
                            $tablesinfo = $name."@t5_info";       
                        }
                        else
                        {
                            $tables = strtolower($name)."@".strtolower($host)."_gps";
                            $tablesinfo = strtolower($name)."@".strtolower($host)."_info";
                        }
            
                        $report_path = "/home/lacakmobil/public_html/transporter.lacak-mobil.com/public/assets/media/autoreport/".
                        $report_type."/".$year."/".$month."/".$vehicle_device[0]."/";
            
                       
                        $this->dbhist->select("gps_id, gps_time, gps_speed, gps_longitude, gps_ew, gps_latitude, gps_ns, gps_status");
                        $this->dbhist->order_by("gps_time", $order);
                        $this->dbhist->where("gps_name", $name);
                        $this->dbhist->where("gps_host", $host);
                        $this->dbhist->where("gps_time >=", $sdate);
                        $this->dbhist->where("gps_time <=", $edate);
                        $q = $this->dbhist->get($tables);
                        $rows1 = $q->result();
						
						$this->dbhist2->select("gps_id, gps_time, gps_speed, gps_longitude, gps_ew, gps_latitude, gps_ns, gps_status");
                        $this->dbhist2->order_by("gps_time", $order);
                        $this->dbhist2->where("gps_name", $name);
                        $this->dbhist2->where("gps_host", $host);
                        $this->dbhist2->where("gps_time >=", $sdate);
                        $this->dbhist2->where("gps_time <=", $edate);
                        $q = $this->dbhist2->get($tables);
                        $rows2 = $q->result();
						
						$rows = array_merge($rows1, $rows2);
            
                       
                        $this->dbhist->order_by("gps_info_time", $order);
                        $this->dbhist->select("gps_info_io_port, gps_info_time, gps_info_distance, gps_info_alarm_alert, gps_info_alarm_data, gps_info_ad_input");
                        $this->dbhist->where("gps_info_device", $name."@".$host);
                        $this->dbhist->where("gps_info_time >=", $sdate);
                        $this->dbhist->where("gps_info_time <=", $edate);
                        $this->dbhist->limit(1);
                        $qinfo = $this->dbhist->get($tablesinfo);
                        $rowlastinfos1 = $qinfo->result();
						
						$this->dbhist2->order_by("gps_info_time", $order);
                        $this->dbhist2->select("gps_info_io_port, gps_info_time, gps_info_distance, gps_info_alarm_alert, gps_info_alarm_data, gps_info_ad_input");
                        $this->dbhist2->where("gps_info_device", $name."@".$host);
                        $this->dbhist2->where("gps_info_time >=", $sdate);
                        $this->dbhist2->where("gps_info_time <=", $edate);
                        $this->dbhist2->limit(1);
                        $qinfo = $this->dbhist2->get($tablesinfo);
                        $rowlastinfos2 = $qinfo->result();
						
						$rowlastinfos = array_merge($rowlastinfos1, $rowlastinfos2);
                        
                        //Summary Saja yang diambil
                        if ($datatype == 0)
                        {
                            for($i=count($rows)-1; $i >= 0; $i--)
                            {
                                if (($i+1) >= count($rows))
                                {
                                    $rowsummary[] = $rows[$i];
                                    continue;
                                }
                                
                                $latbefore = getLatitude($rows[$i+1]->gps_latitude, $rows[$i+1]->gps_ns);
                                $lngbefore = getLongitude($rows[$i+1]->gps_longitude, $rows[$i+1]->gps_ew);
                
                                $latcurrent = getLatitude($rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                $lngcurrent = getLongitude($rows[$i]->gps_longitude, $rows[$i]->gps_ew);
                                
                                if (sprintf("%.4f,%.4f", $latbefore, $lngbefore) != sprintf("%.4f,%.4f", $latcurrent, $lngcurrent))
                                {
                                    $rowsummary[] = $rows[$i];
                                    continue;
                                }

                                if ($rows[$i+1]->gps_speed != $rows[$i]->gps_speed)
                                {
                                    $rowsummary[] = $rows[$i];
                                    continue;
                                }
                            }
                            
                            $rows = array();
                            $total = 0;
                            
                             if (isset($rowsummary))
                            {
                                $rowsummary = array_reverse($rowsummary);            
                                $total = count($rowsummary);
                                $rows = array_splice($rowsummary, $offset, $limit);                            
                            } 
                        }
                        //Finis Summary
            
                        unset($map_params);
                        $ismove = false;
                        $lastcoord = false;
                        
                        printf("NO;DATE;TIME;POSITION;COORDINATE;SPEED;ENGINE;GPS;ODOMETER\n");
                        for($i=0; $i < count($rows); $i++)
                        {
                            if ($i == 0)
                            {
                                $this->dbhist->order_by("gps_info_time", $order);
                                $this->dbhist->select("gps_info_io_port, gps_info_time, gps_info_distance, gps_info_alarm_alert, gps_info_alarm_data, gps_info_ad_input");
                                $this->dbhist->where("gps_info_device", $name."@".$host);
                                $this->dbhist->where("gps_info_time >=", $sdate);
                                $this->dbhist->where("gps_info_time <=", $edate);
                                $qinfos1 = $this->dbhist->get($tablesinfo);   
                                $rowinfos1 = $qinfos1->result();
								
								$this->dbhist2->order_by("gps_info_time", $order);
                                $this->dbhist2->select("gps_info_io_port, gps_info_time, gps_info_distance, gps_info_alarm_alert, gps_info_alarm_data, gps_info_ad_input");
                                $this->dbhist2->where("gps_info_device", $name."@".$host);
                                $this->dbhist2->where("gps_info_time >=", $sdate);
                                $this->dbhist2->where("gps_info_time <=", $edate);
                                $qinfos2 = $this->dbhist2->get($tablesinfo);   
                                $rowinfos2 = $qinfos2->result();
								
								$rowinfos = array_merge($rowinfos1, $rowinfos2);
								
                                $this->db->cache_delete_all();
                                
                                for($j=0; $j < count($rowinfos); $j++)
                                {
                                    $infos[dbmaketime($rowinfos[$j]->gps_info_time)] = $rowinfos[$j];
                                }
                            }
                            
                            $rows[$i]->gps_timestamp = dbmaketime($rows[$i]->gps_time);
                            $rows[$i]->gps_longitude_real = getLongitude($rows[$i]->gps_longitude, $rows[$i]->gps_ew);
                            $rows[$i]->gps_latitude_real = getLatitude($rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                            $rows[$i]->gps_longitude_real_fmt = number_format($rows[$i]->gps_longitude_real, 4, ".", "");
                            $rows[$i]->gps_latitude_real_fmt = number_format($rows[$i]->gps_latitude_real, 4, ".", "");        
            
                            if ($i == 0)
                            {
                                $lastcoord = array($rows[$i]->gps_longitude_real_fmt, $rows[$i]->gps_latitude_real_fmt);
                            }
                            else
                            {
                                if (($lastcoord[0] != $rows[$i]->gps_longitude_real_fmt) || ($lastcoord[1] != $rows[$i]->gps_latitude_real_fmt))
                                {
                                    $ismove = true;
                                }
                            }
                
                            $rows[$i]->gps_date_fmt = date("d/m/Y", $rows[$i]->gps_timestamp+7*3600);
                            $rows[$i]->gps_time_fmt = date("H:i:s", $rows[$i]->gps_timestamp+7*3600);
                            $rows[$i]->gps_speed_fmt = number_format($rows[$i]->gps_speed*1.852, 0, "", ".");
                            $rows[$i]->gps_status = ($rows[$i]->gps_status == "A") ? "OK" : "NOT OK";
            
                            if (isset($infos[$rows[$i]->gps_timestamp]))
                            {
                                $ioport = $infos[$rows[$i]->gps_timestamp]->gps_info_io_port;
                                $rows[$i]->status1 = ((strlen($ioport) > 4) && ($ioport[4] == 1)) ? $this->lang->line('lon') : $this->lang->line('loff');
                                $rows[$i]->odometer = number_format(round(($infos[$rows[$i]->gps_timestamp]->gps_info_distance+$rowvehicle[$x]->vehicle_odometer*1000)/1000), 0, "", ","); 
                            }
                            else
                            {
                                $rows[$i]->status1 = "-";
                                $rows[$i]->odometer = "-";
                            }
                
                            $rows[$i]->georeverse = $this->gpsmodel->GeoReverse($rows[$i]->gps_latitude_real_fmt, $rows[$i]->gps_longitude_real_fmt);            
                            $rows[$i]->gpsindex = $i+1;
                            $rows[$i]->gpsdate = date("d/m/Y", $rows[$i]->gps_timestamp+7*3600);
                            $rows[$i]->gpstime = date("H:i:s", $rows[$i]->gps_timestamp+7*3600);
                            $rows[$i]->gpsaddress = $rows[$i]->georeverse->display_name;
                            $rows[$i]->gpscoord = "(".$rows[$i]->gps_longitude_real_fmt." ".$rows[$i]->gps_latitude_real_fmt.")";
                            $rows[$i]->gpstatus = (($rows[$i]->gps_status == "V") ? "NOT OK" : "OK");
                            $map_params[] = array($rows[$i]->gps_longitude_real_fmt, $rows[$i]->gps_latitude_real_fmt);
            
							printf("%s;%s;%s;%s;%s;%s;%s;%s;%s\n",
							$i+1,
							date("d/m/Y", $rows[$i]->gps_timestamp+7*3600),
							date("H:i:s", $rows[$i]->gps_timestamp+7*3600),
							$rows[$i]->georeverse->display_name,
							$rows[$i]->gps_latitude_real_fmt .",".$rows[$i]->gps_longitude_real_fmt,
							$rows[$i]->gps_speed_fmt,
							$rows[$i]->status1,
							($rows[$i]->gps_status == "V") ? "NOT OK" : "OK",
							$rows[$i]->odometer
							);
                        } 
        }
        exit;
        
    }
	
	function history_pervehicle_archive($datatype=0, $startdate="", $enddate="", $name="", $host="")
    {
        printf("PROSES AUTO REPORT HISTORY START \r\n");
        $startproses = date("Y-m-d H:i:s");
        
        include 'class/PHPExcel.php';
        include 'class/PHPExcel/Writer/Excel2007.php';

        $dbname = "gpsarchive";
        $order = "desc";
        $limit = "700";
        $offset = 0;
        $z = 0;
        
        $totalodometer = 0;
        $totalodometer1 = 0;
        $process_date = date("Y-m-d H:i:s");
        $this->dbhist = $this->load->database($dbname, TRUE);
		$this->dbhist2 = $this->load->database("gpshistory", TRUE);
        $this->dbtrans = $this->load->database("transporter",true);
        
        $report_type = "history_report";
        
        if ($startdate == "") 
        {
            $startdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
            $datefilename = date("Ymd", strtotime("yesterday")); 
        }
        
        if ($startdate != "")
        {
            $datefilename = date("Ymd", strtotime($startdate));     
            $startdate = date("Y-m-d 00:00:00", strtotime($startdate));
        }
        
        if ($enddate != "")
        {
            $enddate = date("Y-m-d 23:59:59", strtotime($enddate));
        }
        
        if ($enddate == "") 
        {
            $enddate = date("Y-m-d 23:59:59", strtotime("yesterday"));
        }
        
        $month = date("F", strtotime($startdate));
        $year = date("Y", strtotime($startdate));
        
        $sdate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($startdate)));
        $edate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($enddate)));
        
        if ($name != "" && $host != "")
        {
            $this->db->where("vehicle_device", $name.'@'.$host);    
        }                                                                  
        
        $this->db->join("user", "vehicle_user_id = user_id", "left outer");
        $this->db->where("vehicle_status <>", 3);
        $q = $this->db->get("vehicle");
        $rowvehicle = $q->result();
        
        $total_process = count($rowvehicle);
        printf("TOTAL PROSES VEHICLE : %s \r\n",$total_process);
        printf("============================================ \r\n");
        
        for($x=0;$x<count($rowvehicle);$x++)
        {
                    printf("PROSES VEHICLE : %s (%d/%d) \r\n",$rowvehicle[$x]->vehicle_device, ++$z, $total_process);
                    $objPHPExcel = new PHPExcel();
                    $objPHPExcel->getProperties()->setTitle("History Report");
                    $objPHPExcel->getProperties()->setSubject("History Report");
                    $objPHPExcel->getProperties()->setDescription("History Report");
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
                    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(55);            
                    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(12);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(12);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(12);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10);

                    //Header
                    $objPHPExcel->setActiveSheetIndex(0);
                    $objPHPExcel->getActiveSheet()->mergeCells('A1:I1');
                    $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'HISTORY REPORT');
                    $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
                    $objPHPExcel->getActiveSheet()->getStyle('B3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    $objPHPExcel->getActiveSheet()->getStyle('B3')->getFont()->setBold(true);
            
                    $objPHPExcel->getActiveSheet()->getStyle('J3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    $objPHPExcel->getActiveSheet()->getStyle('J3')->getFont()->setBold(true);
                    $objPHPExcel->getActiveSheet()->SetCellValue('A5', 'No');
                    $objPHPExcel->getActiveSheet()->SetCellValue('B5', 'Date');
                    $objPHPExcel->getActiveSheet()->SetCellValue('C5', 'Time');
                    $objPHPExcel->getActiveSheet()->SetCellValue('D5', 'Location');
                    $objPHPExcel->getActiveSheet()->SetCellValue('E5', 'Coordinate');
                    $objPHPExcel->getActiveSheet()->SetCellValue('F5', 'Speed (Kph)');
                    $objPHPExcel->getActiveSheet()->SetCellValue('G5', 'Engine');
                    $objPHPExcel->getActiveSheet()->SetCellValue('H5', 'Status');
                    $objPHPExcel->getActiveSheet()->SetCellValue('I5', 'Odometer (km)');
                    $objPHPExcel->getActiveSheet()->getStyle('A5:I5')->getFont()->setBold(true);
                    $objPHPExcel->getActiveSheet()->getStyle('A5:I5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    
                    //cek apakah sudah pernah ada filenya
                    $this->dbtrans->select("autoreport_vehicle_id");
                    $this->dbtrans->where("autoreport_vehicle_id",$rowvehicle[$x]->vehicle_id);
                    $this->dbtrans->where("autoreport_data_startdate",$startdate);
                    $this->dbtrans->limit(1);
                    $qrpt = $this->dbtrans->get("autoreport_history");
                    $rrpt = $qrpt->row();
                    
                    if (count($rrpt)>0)
                    {
                        printf("VEHICLE %s SUDAH PERNAH DI PROSES, HAPUS DULU DATA SEBELUMNYA \r\n", $rowvehicle[$x]->vehicle_device);     
                    }
                    else
                    {
                        $vehicle_device = explode("@", $rowvehicle[$x]->vehicle_device);  
                        $vehicle_no = $rowvehicle[$x]->vehicle_no;
                
                        $ex_vno = explode("/",$vehicle_no);
                        $name = $vehicle_device[0];
                        $host = $vehicle_device[1];
            
                        if ($rowvehicle[$x]->vehicle_type == "T5" || $rowvehicle[$x]->vehicle_type == "T5 PULSE")
                        {
                            $tables = $name."@t5_gps";
                            $tablesinfo = $name."@t5_info";       
                        }
                        else
                        {
                            $tables = strtolower($name)."@".strtolower($host)."_gps";
                            $tablesinfo = strtolower($name)."@".strtolower($host)."_info";
                        }
            
                        $report_path = "/home/lacakmobil/public_html/transporter.lacak-mobil.com/public/assets/media/autoreport/".
                        $report_type."/".$year."/".$month."/".$vehicle_device[0]."/";
            
                        printf("GET DATA GPS VEHICLE : %s \r\n",$rowvehicle[$x]->vehicle_no);
                        $this->dbhist->select("gps_id, gps_time, gps_speed, gps_longitude, gps_ew, gps_latitude, gps_ns, gps_status");
                        $this->dbhist->order_by("gps_time", $order);
                        $this->dbhist->where("gps_name", $name);
                        $this->dbhist->where("gps_host", $host);
                        $this->dbhist->where("gps_time >=", $sdate);
                        $this->dbhist->where("gps_time <=", $edate);
                        $q = $this->dbhist->get($tables);
                        $rows1 = $q->result();
						
						$this->dbhist2->select("gps_id, gps_time, gps_speed, gps_longitude, gps_ew, gps_latitude, gps_ns, gps_status");
                        $this->dbhist2->order_by("gps_time", $order);
                        $this->dbhist2->where("gps_name", $name);
                        $this->dbhist2->where("gps_host", $host);
                        $this->dbhist2->where("gps_time >=", $sdate);
                        $this->dbhist2->where("gps_time <=", $edate);
                        $q = $this->dbhist2->get($tables);
                        $rows2 = $q->result();
						
						$rows = array_merge($rows1, $rows2);
            
                        printf("GET DATA INFO VEHICLE : %s \r\n",$rowvehicle[$x]->vehicle_no);
                        $this->dbhist->order_by("gps_info_time", $order);
                        $this->dbhist->select("gps_info_io_port, gps_info_time, gps_info_distance, gps_info_alarm_alert, gps_info_alarm_data, gps_info_ad_input");
                        $this->dbhist->where("gps_info_device", $name."@".$host);
                        $this->dbhist->where("gps_info_time >=", $sdate);
                        $this->dbhist->where("gps_info_time <=", $edate);
                        $this->dbhist->limit(1);
                        $qinfo = $this->dbhist->get($tablesinfo);
                        $rowlastinfos1 = $qinfo->result();
						
						$this->dbhist2->order_by("gps_info_time", $order);
                        $this->dbhist2->select("gps_info_io_port, gps_info_time, gps_info_distance, gps_info_alarm_alert, gps_info_alarm_data, gps_info_ad_input");
                        $this->dbhist2->where("gps_info_device", $name."@".$host);
                        $this->dbhist2->where("gps_info_time >=", $sdate);
                        $this->dbhist2->where("gps_info_time <=", $edate);
                        $this->dbhist2->limit(1);
                        $qinfo = $this->dbhist2->get($tablesinfo);
                        $rowlastinfos2 = $qinfo->result();
						
						$rowlastinfos = array_merge($rowlastinfos1, $rowlastinfos2);
                        
                        //Summary Saja yang diambil
                        if ($datatype == 0)
                        {
                            for($i=count($rows)-1; $i >= 0; $i--)
                            {
                                if (($i+1) >= count($rows))
                                {
                                    $rowsummary[] = $rows[$i];
                                    continue;
                                }
                                
                                $latbefore = getLatitude($rows[$i+1]->gps_latitude, $rows[$i+1]->gps_ns);
                                $lngbefore = getLongitude($rows[$i+1]->gps_longitude, $rows[$i+1]->gps_ew);
                
                                $latcurrent = getLatitude($rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                                $lngcurrent = getLongitude($rows[$i]->gps_longitude, $rows[$i]->gps_ew);
                                
                                if (sprintf("%.4f,%.4f", $latbefore, $lngbefore) != sprintf("%.4f,%.4f", $latcurrent, $lngcurrent))
                                {
                                    $rowsummary[] = $rows[$i];
                                    continue;
                                }

                                if ($rows[$i+1]->gps_speed != $rows[$i]->gps_speed)
                                {
                                    $rowsummary[] = $rows[$i];
                                    continue;
                                }
                            }
                            
                            $rows = array();
                            $total = 0;
                            
                            if (isset($rowsummary))
                            {
                                $rowsummary = array_reverse($rowsummary);            
                                $total = count($rowsummary);
                                $rows = array_splice($rowsummary, $offset, $limit);                            
                            }   
                        }
                        //Finis Summary
            
                        unset($map_params);
                        $ismove = false;
                        $lastcoord = false;
                        
                        printf("PROSES XLS FILE FOR : %s  \r\n",$rowvehicle[$x]->vehicle_no);
                        
                        for($i=0; $i < count($rows); $i++)
                        {
                            printf("|%s|",$i);
                            if ($i == 0)
                            {
                                $this->dbhist->order_by("gps_info_time", $order);
                                $this->dbhist->select("gps_info_io_port, gps_info_time, gps_info_distance, gps_info_alarm_alert, gps_info_alarm_data, gps_info_ad_input");
                                $this->dbhist->where("gps_info_device", $name."@".$host);
                                $this->dbhist->where("gps_info_time >=", $sdate);
                                $this->dbhist->where("gps_info_time <=", $edate);
                                $qinfos = $this->dbhist->get($tablesinfo);   
                                $rowinfos = $qinfos->result();
                                $this->db->cache_delete_all();
                                
                                for($j=0; $j < count($rowinfos); $j++)
                                {
                                    $infos[dbmaketime($rowinfos[$j]->gps_info_time)] = $rowinfos[$j];
                                }
                            }
                            
                            $rows[$i]->gps_timestamp = dbmaketime($rows[$i]->gps_time);
                            $rows[$i]->gps_longitude_real = getLongitude($rows[$i]->gps_longitude, $rows[$i]->gps_ew);
                            $rows[$i]->gps_latitude_real = getLatitude($rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                            $rows[$i]->gps_longitude_real_fmt = number_format($rows[$i]->gps_longitude_real, 4, ".", "");
                            $rows[$i]->gps_latitude_real_fmt = number_format($rows[$i]->gps_latitude_real, 4, ".", "");        
            
                            if ($i == 0)
                            {
                                $lastcoord = array($rows[$i]->gps_longitude_real_fmt, $rows[$i]->gps_latitude_real_fmt);
                            }
                            else
                            {
                                if (($lastcoord[0] != $rows[$i]->gps_longitude_real_fmt) || ($lastcoord[1] != $rows[$i]->gps_latitude_real_fmt))
                                {
                                    $ismove = true;
                                }
                            }
                
                            $rows[$i]->gps_date_fmt = date("d/m/Y", $rows[$i]->gps_timestamp+7*3600);
                            $rows[$i]->gps_time_fmt = date("H:i:s", $rows[$i]->gps_timestamp+7*3600);
                            $rows[$i]->gps_speed_fmt = number_format($rows[$i]->gps_speed*1.852, 0, "", ".");
                            $rows[$i]->gps_status = ($rows[$i]->gps_status == "A") ? "OK" : "NOT OK";
            
                            if (isset($infos[$rows[$i]->gps_timestamp]))
                            {
                                $ioport = $infos[$rows[$i]->gps_timestamp]->gps_info_io_port;
                                $rows[$i]->status1 = ((strlen($ioport) > 4) && ($ioport[4] == 1)) ? $this->lang->line('lon') : $this->lang->line('loff');
                                $rows[$i]->odometer = number_format(round(($infos[$rows[$i]->gps_timestamp]->gps_info_distance+$rowvehicle[$x]->vehicle_odometer*1000)/1000), 0, "", ","); 
                            }
                            else
                            {
                                $rows[$i]->status1 = "-";
                                $rows[$i]->odometer = "-";
                            }
                
                            $rows[$i]->georeverse = $this->gpsmodel->GeoReverse($rows[$i]->gps_latitude_real_fmt, $rows[$i]->gps_longitude_real_fmt);            
                            $rows[$i]->gpsindex = $i+1;
                            $rows[$i]->gpsdate = date("d/m/Y", $rows[$i]->gps_timestamp+7*3600);
                            $rows[$i]->gpstime = date("H:i:s", $rows[$i]->gps_timestamp+7*3600);
                            $rows[$i]->gpsaddress = $rows[$i]->georeverse->display_name;
                            $rows[$i]->gpscoord = "(".$rows[$i]->gps_longitude_real_fmt." ".$rows[$i]->gps_latitude_real_fmt.")";
                            $rows[$i]->gpstatus = (($rows[$i]->gps_status == "V") ? "NOT OK" : "OK");
                            $map_params[] = array($rows[$i]->gps_longitude_real_fmt, $rows[$i]->gps_latitude_real_fmt);
            
                            $objPHPExcel->getActiveSheet()->SetCellValue('A'.(6+$i), $i+1);
                            $objPHPExcel->getActiveSheet()->SetCellValue('B'.(6+$i), date("d/m/Y", $rows[$i]->gps_timestamp+7*3600));
                            $objPHPExcel->getActiveSheet()->SetCellValue('C'.(6+$i), date("H:i:s", $rows[$i]->gps_timestamp+7*3600));
                            $objPHPExcel->getActiveSheet()->SetCellValue('D'.(6+$i), $rows[$i]->georeverse->display_name);
                            $objPHPExcel->getActiveSheet()->SetCellValue('E'.(6+$i), $rows[$i]->gps_latitude_real_fmt .",".$rows[$i]->gps_longitude_real_fmt);
                            $objPHPExcel->getActiveSheet()->SetCellValue('F'.(6+$i), $rows[$i]->gps_speed_fmt);
                            $objPHPExcel->getActiveSheet()->SetCellValue('G'.(6+$i), $rows[$i]->status1);
                            $objPHPExcel->getActiveSheet()->SetCellValue('H'.(6+$i), ($rows[$i]->gps_status == "V") ? "NOT OK" : "OK"); 
                            $objPHPExcel->getActiveSheet()->SetCellValue('I'.(6+$i), $rows[$i]->odometer);
                
                            $styleArray = array(
                                'borders' => array(
                                'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                            )
                            );
                        
                            $objPHPExcel->getActiveSheet()->getStyle('A5:I'.(5+$i))->applyFromArray($styleArray);
                            $objPHPExcel->getActiveSheet()->getStyle('A5:I'.(5+$i))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
                            $objPHPExcel->getActiveSheet()->getStyle('A5:I'.(5+$i))->getAlignment()->setWrapText(true);
                        } 
            
                        // Save Excel
                        $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
                        @mkdir($report_path, DIR_WRITE_MODE); //REPORT_PATH -> config->constant
                
                        if (isset($ex_vno))
                        {
                            if (isset($ex_vno[1]))
                            {
                                $filecreatedname = "HistoryReport_".$ex_vno[0].$ex_vno[1]."_".$datefilename.".xls";    
                            }
                            else
                            {
                                $filecreatedname = "HistoryReport_".$ex_vno[0]."_".$datefilename.".xls";       
                            }
                        }
                        else
                        {
                            $filecreatedname = "HistoryReport_".$vehicle_no."_".$datefilename.".xls";  
                        }
                
                        printf("SAVE XLS FILE FOR : %s  \r\n",$filecreatedname); 
                        $objWriter->save($report_path.$filecreatedname);
                
                        printf("DELETE CACHE GPS \r\n"); 
                        $this->db->cache_delete_all();
                        $this->dbhist->cache_delete_all();
                
                        printf("INSERT TO DB : %s  \r\n",$rowvehicle[$x]->vehicle_no);
                        unset($datainsert);
                        $datainsert["autoreport_vehicle_id"] = $rowvehicle[$x]->vehicle_id;
                        $datainsert["autoreport_vehicle_device"] = $rowvehicle[$x]->vehicle_device;
                        $datainsert["autoreport_vehicle_name"] = $rowvehicle[$x]->vehicle_name;
                        $datainsert["autoreport_vehicle_no"] = $rowvehicle[$x]->vehicle_no;
                        $datainsert["autoreport_user_id"] = $rowvehicle[$x]->user_id;
                        $datainsert["autoreport_company"] = $rowvehicle[$x]->user_company;
                        $datainsert["autoreport_data_startdate"] = $startdate;
                        $datainsert["autoreport_data_enddate"] = $enddate;
                        $datainsert["autoreport_type"] = $report_type;
                        $datainsert["autoreport_file_path"] = $report_path;
                        $datainsert["autoreport_filename"] = $filecreatedname;
                        $datainsert["autoreport_download_path"] = $report_path.$filecreatedname;
                        $datainsert["autoreport_process_date"] = $process_date; 
                        $datainsert["autoreport_insert_db"] = date("Y-m-d H:i:s");
                        $this->dbtrans->insert("autoreport_history",$datainsert);
                
                        printf("INSERT OK \r\n");
                
                        printf("FINIS PROSES FOR VEHICLE %s  \r\n",$rowvehicle[$x]->vehicle_no); 
                        printf("===========================================================\r\n");       
                    }
        }
        
        unset($datalog);
        $datalog["cron_name"] = "History To Excel";
        $datalog["cron_start_data"] = $startdate;
        $datalog["cron_end_data"] = $enddate;
        $datalog["cron_start_proses"] = $startproses;
        $datalog["cron_end_proses"] = date("Y-m-d H:i:s");
        $this->dbtrans->insert("log_cronjobs",$datalog);
        
        $this->db->cache_delete_all();
        $this->dbhist->cache_delete_all();
        $this->dbtrans->cache_delete_all();
        printf("FINISH  \r\n");
        exit;
        
    }
	
	function history_peruser_lemo($datatype=0, $startdate="", $enddate="", $userid = "")
    {
        printf("PROSES AUTO REPORT HISTORY START \r\n");
        $startproses = date("Y-m-d H:i:s");
        
        include 'class/PHPExcel.php';
        include 'class/PHPExcel/Writer/Excel2007.php';
        
        //$userlist = array(775, 631, 981, 1059, 834, 778, 1222, 1027, 1246, 
        //1332, 1298, 986, 651, 645, 736, 666, 866, 627, 1386, 1110, 1148, 1122,
        //703, 1147, 1168, 1212);
        
        $dbname = "gpshistory";
        $order = "desc";
        $limit = "700";
        $offset = 0;
        $z=0;
        $totalodometer = 0;
        $totalodometer1 = 0;
        $process_date = date("Y-m-d H:i:s");
        $this->dbhist = $this->load->database($dbname, TRUE);
		$this->dbhist2 = $this->load->database("gpshistory2", TRUE);
        $this->dbtrans = $this->load->database("transporter",true);
        
        $report_type = "history_report";
        
        if ($startdate == "") 
        {
            $startdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
            $datefilename = date("Ymd", strtotime("yesterday")); 
        }
        
        if ($startdate != "")
        {
            $datefilename = date("Ymd", strtotime($startdate));     
            $startdate = date("Y-m-d 00:00:00", strtotime($startdate));
        }
        
        if ($enddate != "")
        {
            $enddate = date("Y-m-d 23:59:59", strtotime($enddate));
        }
        
        if ($enddate == "") 
        {
            $enddate = date("Y-m-d 23:59:59", strtotime("yesterday"));
        }
        
        $month = date("F", strtotime($startdate));
        $year = date("Y", strtotime($startdate));
        
        $sdate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($startdate)));
        $edate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($enddate)));
        
        
		
        $this->db->where("user_id", 2110);
        $this->db->join("user", "vehicle_user_id = user_id", "left outer");
        $this->db->where("vehicle_status <>", 3);
        $q = $this->db->get("vehicle");
        $rowvehicle = $q->result();
        
        $total_process = count($rowvehicle);
        printf("TOTAL PROSES VEHICLE : %s \r\n",$total_process);
        printf("============================================ \r\n");
        
        for($x=0;$x<count($rowvehicle);$x++)
        {
            printf("PROSES VEHICLE : %s (%d/%d) \r\n",$rowvehicle[$x]->vehicle_device, ++$z, $total_process);
            
            $objPHPExcel = new PHPExcel();
            $objPHPExcel->getProperties()->setTitle("History Report");
            $objPHPExcel->getProperties()->setSubject("History Report");
            $objPHPExcel->getProperties()->setDescription("History Report");
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
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(55);            
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(12);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(12);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(12);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10);

            //Header
            $objPHPExcel->setActiveSheetIndex(0);
            $objPHPExcel->getActiveSheet()->mergeCells('A1:I1');
            $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'HISTORY REPORT');
            $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle('B3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $objPHPExcel->getActiveSheet()->getStyle('B3')->getFont()->setBold(true);
            
            $objPHPExcel->getActiveSheet()->getStyle('J3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $objPHPExcel->getActiveSheet()->getStyle('J3')->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->SetCellValue('A5', 'No');
            $objPHPExcel->getActiveSheet()->SetCellValue('B5', 'Date');
            $objPHPExcel->getActiveSheet()->SetCellValue('C5', 'Time');
            $objPHPExcel->getActiveSheet()->SetCellValue('D5', 'Location');
            $objPHPExcel->getActiveSheet()->SetCellValue('E5', 'Coordinate');
            $objPHPExcel->getActiveSheet()->SetCellValue('F5', 'Speed (Kph)');
            $objPHPExcel->getActiveSheet()->SetCellValue('G5', 'Engine');
            $objPHPExcel->getActiveSheet()->SetCellValue('H5', 'Status');
            $objPHPExcel->getActiveSheet()->SetCellValue('I5', 'Odometer (km)');
            $objPHPExcel->getActiveSheet()->getStyle('A5:I5')->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle('A5:I5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            
            //cek apakah sudah pernah ada filenya
            $this->dbtrans->where("autoreport_vehicle_id",$rowvehicle[$x]->vehicle_id);
            $this->dbtrans->where("autoreport_data_startdate",$startdate);
            $this->dbtrans->limit(1);
            $qrpt = $this->dbtrans->get("autoreport_history");
            $rrpt = $qrpt->row();
            if (count($rrpt)>0)
            {
              printf("VEHICLE %s SUDAH PERNAH DI PROSES, HAPUS DULU DATA SEBELUMNYA \r\n", $rowvehicle[$x]->vehicle_device);     
            }
            else
            {
            
                $vehicle_device = explode("@", $rowvehicle[$x]->vehicle_device);  
                $vehicle_no = $rowvehicle[$x]->vehicle_no;
                
                $ex_vno = explode("/",$vehicle_no);
                $name = $vehicle_device[0];
                $host = $vehicle_device[1];
            
                if ($rowvehicle[$x]->vehicle_type == "T5" || $rowvehicle[$x]->vehicle_type == "T5 PULSE")
                {
                    $tables = $name."@t5_gps";
                    $tablesinfo = $name."@t5_info";       
                }
                else
                {
                    $tables = strtolower($name)."@".strtolower($host)."_gps";
                    $tablesinfo = strtolower($name)."@".strtolower($host)."_info";
                }
            
                $report_path = "/home/lacakmobil/public_html/transporter.lacak-mobil.com/public/assets/media/autoreport/".
                            $report_type."/".$year."/".$month."/".$vehicle_device[0]."/";
            
                printf("GET DATA GPS VEHICLE : %s \r\n",$rowvehicle[$x]->vehicle_no);
                $this->dbhist->select("gps_id, gps_time, gps_speed, gps_longitude, gps_ew, gps_latitude, gps_ns, gps_status");
				$this->dbhist->order_by("gps_time", $order);
				$this->dbhist->where("gps_name", $name);
				$this->dbhist->where("gps_host", $host);
				$this->dbhist->where("gps_time >=", $sdate);
				$this->dbhist->where("gps_time <=", $edate);
				$q = $this->dbhist->get($tables);
				$rows1 = $q->result();
						
				$this->dbhist2->select("gps_id, gps_time, gps_speed, gps_longitude, gps_ew, gps_latitude, gps_ns, gps_status");
                $this->dbhist2->order_by("gps_time", $order);
				$this->dbhist2->where("gps_name", $name);
				$this->dbhist2->where("gps_host", $host);
				$this->dbhist2->where("gps_time >=", $sdate);
				$this->dbhist2->where("gps_time <=", $edate);
				$q = $this->dbhist2->get($tables);
				$rows2 = $q->result();
				
				$rows = array_merge($rows1, $rows2);
            
				printf("GET DATA INFO VEHICLE : %s \r\n",$rowvehicle[$x]->vehicle_no);
				$this->dbhist->order_by("gps_info_time", $order);
				$this->dbhist->select("gps_info_io_port, gps_info_time, gps_info_distance, gps_info_alarm_alert, gps_info_alarm_data, gps_info_ad_input");
				$this->dbhist->where("gps_info_device", $name."@".$host);
				$this->dbhist->where("gps_info_time >=", $sdate);
				$this->dbhist->where("gps_info_time <=", $edate);
				$this->dbhist->limit(1);
				$qinfo = $this->dbhist->get($tablesinfo);
				$rowlastinfos1 = $qinfo->result();
						
				$this->dbhist->order_by("gps_info_time", $order);
				$this->dbhist->select("gps_info_io_port, gps_info_time, gps_info_distance, gps_info_alarm_alert, gps_info_alarm_data, gps_info_ad_input");
				$this->dbhist->where("gps_info_device", $name."@".$host);
				$this->dbhist->where("gps_info_time >=", $sdate);
				$this->dbhist->where("gps_info_time <=", $edate);
				$this->dbhist->limit(1);
				$qinfo = $this->dbhist->get($tablesinfo);
				$rowlastinfos2 = $qinfo->result();
						
				$rowlastinfos = array_merge($rowlastinfos1, $rowlastinfos2);
                
                //Summary Saja yang diambil
                if ($datatype == 0)
                {
                    for($i=count($rows)-1; $i >= 0; $i--)
                    {
                        if (($i+1) >= count($rows))
                        {
                            $rowsummary[] = $rows[$i];
                            continue;
                        }
                
                        $latbefore = getLatitude($rows[$i+1]->gps_latitude, $rows[$i+1]->gps_ns);
                        $lngbefore = getLongitude($rows[$i+1]->gps_longitude, $rows[$i+1]->gps_ew);

                        $latcurrent = getLatitude($rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                        $lngcurrent = getLongitude($rows[$i]->gps_longitude, $rows[$i]->gps_ew);
                
                        if (sprintf("%.4f,%.4f", $latbefore, $lngbefore) != sprintf("%.4f,%.4f", $latcurrent, $lngcurrent))
                        {
                            $rowsummary[] = $rows[$i];
                            continue;
                        }

                        if ($rows[$i+1]->gps_speed != $rows[$i]->gps_speed)
                        {
                            $rowsummary[] = $rows[$i];
                            continue;
                        }
                    }
                 
                    $rows = array();
                    $total = 0;
                    if (isset($rowsummary))
                    {
                        $rowsummary = array_reverse($rowsummary);            
                        $total = count($rowsummary);
                        $rows = array_splice($rowsummary, $offset, $limit);                            
                    }   
                }
                //Finis Summary
            
                unset($map_params);
                $ismove = false;
                $lastcoord = false;
            
                printf("PROSES XLS FILE FOR : %s  \r\n",$rowvehicle[$x]->vehicle_no);
            
                for($i=0; $i < count($rows); $i++)
                {
                    printf("|%s|",$i);
                    if ($i == 0)
                    {
                        $this->dbhist->order_by("gps_info_time", $order);
                        $this->dbhist->select("gps_info_io_port, gps_info_time, gps_info_distance, gps_info_alarm_alert, gps_info_alarm_data, gps_info_ad_input");
                        $this->dbhist->where("gps_info_device", $name."@".$host);
                        $this->dbhist->where("gps_info_time >=", $sdate);
                        $this->dbhist->where("gps_info_time <=", $edate);
                        $qinfos = $this->dbhist->get($tablesinfo);   
                        $rowinfos = $qinfos->result();
                        $this->db->cache_delete_all();
                
                        for($j=0; $j < count($rowinfos); $j++)
                        {
                            $infos[dbmaketime($rowinfos[$j]->gps_info_time)] = $rowinfos[$j];
                        }
                    }
            
                    $rows[$i]->gps_timestamp = dbmaketime($rows[$i]->gps_time);
                    $rows[$i]->gps_longitude_real = getLongitude($rows[$i]->gps_longitude, $rows[$i]->gps_ew);
                    $rows[$i]->gps_latitude_real = getLatitude($rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                    $rows[$i]->gps_longitude_real_fmt = number_format($rows[$i]->gps_longitude_real, 4, ".", "");
                    $rows[$i]->gps_latitude_real_fmt = number_format($rows[$i]->gps_latitude_real, 4, ".", "");        
            
                    if ($i == 0)
                    {
                        $lastcoord = array($rows[$i]->gps_longitude_real_fmt, $rows[$i]->gps_latitude_real_fmt);
                    }
                    else
                    {
                        if (($lastcoord[0] != $rows[$i]->gps_longitude_real_fmt) || ($lastcoord[1] != $rows[$i]->gps_latitude_real_fmt))
                        {
                            $ismove = true;
                        }
                    }

                    $rows[$i]->gps_date_fmt = date("d/m/Y", $rows[$i]->gps_timestamp+7*3600);
                    $rows[$i]->gps_time_fmt = date("H:i:s", $rows[$i]->gps_timestamp+7*3600);
                    $rows[$i]->gps_speed_fmt = number_format($rows[$i]->gps_speed*1.852, 0, "", ".");
                    $rows[$i]->gps_status = ($rows[$i]->gps_status == "A") ? "OK" : "NOT OK";
            
                    if (isset($infos[$rows[$i]->gps_timestamp]))
                    {
                        $ioport = $infos[$rows[$i]->gps_timestamp]->gps_info_io_port;
                        $rows[$i]->status1 = ((strlen($ioport) > 4) && ($ioport[4] == 1)) ? $this->lang->line('lon') : $this->lang->line('loff');
                        $rows[$i]->odometer = number_format(round(($infos[$rows[$i]->gps_timestamp]->gps_info_distance+$rowvehicle[$x]->vehicle_odometer*1000)/1000), 0, "", ","); 
                    }
                    else
                    {
                        $rows[$i]->status1 = "-";
                        $rows[$i]->odometer = "-";
                    }
                
                    $rows[$i]->georeverse = $this->gpsmodel->GeoReverse($rows[$i]->gps_latitude_real_fmt, $rows[$i]->gps_longitude_real_fmt);            
                    $rows[$i]->gpsindex = $i+1;
                    $rows[$i]->gpsdate = date("d/m/Y", $rows[$i]->gps_timestamp+7*3600);
                    $rows[$i]->gpstime = date("H:i:s", $rows[$i]->gps_timestamp+7*3600);
                    $rows[$i]->gpsaddress = $rows[$i]->georeverse->display_name;
                    $rows[$i]->gpscoord = "(".$rows[$i]->gps_longitude_real_fmt." ".$rows[$i]->gps_latitude_real_fmt.")";
                    $rows[$i]->gpstatus = (($rows[$i]->gps_status == "V") ? "NOT OK" : "OK");
                    $map_params[] = array($rows[$i]->gps_longitude_real_fmt, $rows[$i]->gps_latitude_real_fmt);
            
                    $objPHPExcel->getActiveSheet()->SetCellValue('A'.(6+$i), $i+1);
                    $objPHPExcel->getActiveSheet()->SetCellValue('B'.(6+$i), date("d/m/Y", $rows[$i]->gps_timestamp+7*3600));
                    $objPHPExcel->getActiveSheet()->SetCellValue('C'.(6+$i), date("H:i:s", $rows[$i]->gps_timestamp+7*3600));
                    $objPHPExcel->getActiveSheet()->SetCellValue('D'.(6+$i), $rows[$i]->georeverse->display_name);
                    $objPHPExcel->getActiveSheet()->SetCellValue('E'.(6+$i), $rows[$i]->gps_latitude_real_fmt .",".$rows[$i]->gps_longitude_real_fmt);
                    $objPHPExcel->getActiveSheet()->SetCellValue('F'.(6+$i), $rows[$i]->gps_speed_fmt);
                    $objPHPExcel->getActiveSheet()->SetCellValue('G'.(6+$i), $rows[$i]->status1);
                    $objPHPExcel->getActiveSheet()->SetCellValue('H'.(6+$i), ($rows[$i]->gps_status == "V") ? "NOT OK" : "OK"); 
                    $objPHPExcel->getActiveSheet()->SetCellValue('I'.(6+$i), $rows[$i]->odometer);
                
                    $styleArray = array(
                          'borders' => array(
                            'allborders' => array(
                              'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                          )
                        );
                        
                    $objPHPExcel->getActiveSheet()->getStyle('A5:I'.(5+$i))->applyFromArray($styleArray);
                    $objPHPExcel->getActiveSheet()->getStyle('A5:I'.(5+$i))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
                    $objPHPExcel->getActiveSheet()->getStyle('A5:I'.(5+$i))->getAlignment()->setWrapText(true);
                } 
            
                // Save Excel
                $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
                @mkdir($report_path, DIR_WRITE_MODE); //REPORT_PATH -> config->constant
                
                if (isset($ex_vno))
                {
                    if (isset($ex_vno[1]))
                    {
                        $filecreatedname = "HistoryReport_".$ex_vno[0].$ex_vno[1]."_".$datefilename.".xls";    
                    }
                    else
                    {
                        $filecreatedname = "HistoryReport_".$ex_vno[0]."_".$datefilename.".xls";       
                    }
                }
                else
                {
                    
                    $filecreatedname = "HistoryReport_".$vehicle_no."_".$datefilename.".xls";  
                }
                
                printf("SAVE XLS FILE FOR : %s  \r\n",$filecreatedname); 
                $objWriter->save($report_path.$filecreatedname);
                
                printf("DELETE CACHE GPS \r\n"); 
                $this->db->cache_delete_all();
                $this->dbhist->cache_delete_all();
                
                printf("INSERT TO DB : %s  \r\n",$rowvehicle[$x]->vehicle_no);
                unset($datainsert);
                $datainsert["autoreport_vehicle_id"] = $rowvehicle[$x]->vehicle_id;
                $datainsert["autoreport_vehicle_device"] = $rowvehicle[$x]->vehicle_device;
                $datainsert["autoreport_vehicle_name"] = $rowvehicle[$x]->vehicle_name;
                $datainsert["autoreport_vehicle_no"] = $rowvehicle[$x]->vehicle_no;
                $datainsert["autoreport_user_id"] = $rowvehicle[$x]->user_id;
                $datainsert["autoreport_company"] = $rowvehicle[$x]->user_company;
                $datainsert["autoreport_data_startdate"] = $startdate;
                $datainsert["autoreport_data_enddate"] = $enddate;
                $datainsert["autoreport_type"] = $report_type;
                $datainsert["autoreport_file_path"] = $report_path;
                $datainsert["autoreport_filename"] = $filecreatedname;
                $datainsert["autoreport_download_path"] = $report_path.$filecreatedname;
                $datainsert["autoreport_process_date"] = $process_date; 
                $datainsert["autoreport_insert_db"] = date("Y-m-d H:i:s");
                $this->dbtrans->insert("autoreport_history",$datainsert);
                
                printf("INSERT OK \r\n");
                
                printf("FINIS PROSES FOR VEHICLE %s  \r\n",$rowvehicle[$x]->vehicle_no); 
                printf("===========================================================\r\n");       
            }
        }
        
        unset($datalog);
        $datalog["cron_name"] = "History To Excel";
        $datalog["cron_start_data"] = $startdate;
        $datalog["cron_end_data"] = $enddate;
        $datalog["cron_start_proses"] = $startproses;
        $datalog["cron_end_proses"] = date("Y-m-d H:i:s");
        $this->dbtrans->insert("log_cronjobs",$datalog);
        
        $this->db->cache_delete_all();
        $this->dbhist->cache_delete_all();
        $this->dbtrans->cache_delete_all();
        printf("FINISH  \r\n");
        exit;
    }
	
	function history_peruser_kct($datatype=2, $startdate="", $enddate="", $userid = "")
    {
        printf("PROSES AUTO REPORT HISTORY START \r\n");
        $startproses = date("Y-m-d H:i:s");
        
        include 'class/PHPExcel.php';
        include 'class/PHPExcel/Writer/Excel2007.php';
        
        //$userlist = array(775, 631, 981, 1059, 834, 778, 1222, 1027, 1246, 
        //1332, 1298, 986, 651, 645, 736, 666, 866, 627, 1386, 1110, 1148, 1122,
        //703, 1147, 1168, 1212);
        
        $dbname = "gpshistory";
        $order = "desc";
        $limit = "700";
        $offset = 0;
        $z=0;
        $totalodometer = 0;
        $totalodometer1 = 0;
        $process_date = date("Y-m-d H:i:s");
        $this->dbhist = $this->load->database($dbname, TRUE);
		$this->dbhist2 = $this->load->database("gpshistory2", TRUE);
        $this->dbtrans = $this->load->database("transporter",true);
        
        $report_type = "history_report";
        
        if ($startdate == "") 
        {
            $startdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
            $datefilename = date("Ymd", strtotime("yesterday")); 
        }
        
        if ($startdate != "")
        {
            $datefilename = date("Ymd", strtotime($startdate));     
            $startdate = date("Y-m-d 00:00:00", strtotime($startdate));
        }
        
        if ($enddate != "")
        {
            $enddate = date("Y-m-d 23:59:59", strtotime($enddate));
        }
        
        if ($enddate == "") 
        {
            $enddate = date("Y-m-d 23:59:59", strtotime("yesterday"));
        }
        
        $month = date("F", strtotime($startdate));
        $year = date("Y", strtotime($startdate));
        
        $sdate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($startdate)));
        $edate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($enddate)));
        
		//only user kct
        $this->db->where("user_id", 1643);
        $this->db->join("user", "vehicle_user_id = user_id", "left outer");
        $this->db->where("vehicle_status <>", 3);
        $q = $this->db->get("vehicle");
        $rowvehicle = $q->result();
        
        $total_process = count($rowvehicle);
        printf("TOTAL PROSES VEHICLE : %s \r\n",$total_process);
        printf("============================================ \r\n");
        
        for($x=0;$x<count($rowvehicle);$x++)
        {
            printf("PROSES VEHICLE : %s (%d/%d) \r\n",$rowvehicle[$x]->vehicle_device, ++$z, $total_process);
            
            $objPHPExcel = new PHPExcel();
            $objPHPExcel->getProperties()->setTitle("History Report");
            $objPHPExcel->getProperties()->setSubject("History Report");
            $objPHPExcel->getProperties()->setDescription("History Report");
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
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(55);            
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(12);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(12);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(12);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10);

            //Header
            $objPHPExcel->setActiveSheetIndex(0);
            $objPHPExcel->getActiveSheet()->mergeCells('A1:I1');
            $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'HISTORY REPORT');
            $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle('B3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $objPHPExcel->getActiveSheet()->getStyle('B3')->getFont()->setBold(true);
            
            $objPHPExcel->getActiveSheet()->getStyle('J3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $objPHPExcel->getActiveSheet()->getStyle('J3')->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->SetCellValue('A5', 'No');
            $objPHPExcel->getActiveSheet()->SetCellValue('B5', 'Date');
            $objPHPExcel->getActiveSheet()->SetCellValue('C5', 'Time');
            $objPHPExcel->getActiveSheet()->SetCellValue('D5', 'Location');
            $objPHPExcel->getActiveSheet()->SetCellValue('E5', 'Coordinate');
            $objPHPExcel->getActiveSheet()->SetCellValue('F5', 'Speed (Kph)');
            $objPHPExcel->getActiveSheet()->SetCellValue('G5', 'Engine');
            $objPHPExcel->getActiveSheet()->SetCellValue('H5', 'Status');
            $objPHPExcel->getActiveSheet()->SetCellValue('I5', 'Odometer (km)');
            $objPHPExcel->getActiveSheet()->getStyle('A5:I5')->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle('A5:I5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            
            //cek apakah sudah pernah ada filenya
            $this->dbtrans->where("autoreport_vehicle_id",$rowvehicle[$x]->vehicle_id);
            $this->dbtrans->where("autoreport_data_startdate",$startdate);
            $this->dbtrans->limit(1);
            $qrpt = $this->dbtrans->get("autoreport_history");
            $rrpt = $qrpt->row();
            if (count($rrpt)>0)
            {
              printf("VEHICLE %s SUDAH PERNAH DI PROSES, HAPUS DULU DATA SEBELUMNYA \r\n", $rowvehicle[$x]->vehicle_device);     
            }
            else
            {
            
                $vehicle_device = explode("@", $rowvehicle[$x]->vehicle_device);  
                $vehicle_no = $rowvehicle[$x]->vehicle_no;
                
                $ex_vno = explode("/",$vehicle_no);
                $name = $vehicle_device[0];
                $host = $vehicle_device[1];
            
                if ($rowvehicle[$x]->vehicle_type == "T5" || $rowvehicle[$x]->vehicle_type == "T5 PULSE")
                {
                    $tables = $name."@t5_gps";
                    $tablesinfo = $name."@t5_info";       
                }
                else
                {
                    $tables = strtolower($name)."@".strtolower($host)."_gps";
                    $tablesinfo = strtolower($name)."@".strtolower($host)."_info";
                }
            
                $report_path = "/home/lacakmobil/public_html/transporter.lacak-mobil.com/public/assets/media/autoreport/".
                            $report_type."/".$year."/".$month."/".$vehicle_device[0]."/";
            
                printf("GET DATA GPS VEHICLE : %s \r\n",$rowvehicle[$x]->vehicle_no);
                $this->dbhist->select("gps_id, gps_time, gps_speed, gps_longitude, gps_ew, gps_latitude, gps_ns, gps_status");
				$this->dbhist->order_by("gps_time", $order);
				$this->dbhist->where("gps_name", $name);
				$this->dbhist->where("gps_host", $host);
				$this->dbhist->where("gps_time >=", $sdate);
				$this->dbhist->where("gps_time <=", $edate);
				$q = $this->dbhist->get($tables);
				$rows1 = $q->result();
						
				$this->dbhist2->select("gps_id, gps_time, gps_speed, gps_longitude, gps_ew, gps_latitude, gps_ns, gps_status");
                $this->dbhist2->order_by("gps_time", $order);
				$this->dbhist2->where("gps_name", $name);
				$this->dbhist2->where("gps_host", $host);
				$this->dbhist2->where("gps_time >=", $sdate);
				$this->dbhist2->where("gps_time <=", $edate);
				$q = $this->dbhist2->get($tables);
				$rows2 = $q->result();
				
				$rows = array_merge($rows1, $rows2);
            
				printf("GET DATA INFO VEHICLE : %s \r\n",$rowvehicle[$x]->vehicle_no);
				$this->dbhist->order_by("gps_info_time", $order);
				$this->dbhist->select("gps_info_io_port, gps_info_time, gps_info_distance, gps_info_alarm_alert, gps_info_alarm_data, gps_info_ad_input");
				$this->dbhist->where("gps_info_device", $name."@".$host);
				$this->dbhist->where("gps_info_time >=", $sdate);
				$this->dbhist->where("gps_info_time <=", $edate);
				$this->dbhist->limit(1);
				$qinfo = $this->dbhist->get($tablesinfo);
				$rowlastinfos1 = $qinfo->result();
						
				$this->dbhist->order_by("gps_info_time", $order);
				$this->dbhist->select("gps_info_io_port, gps_info_time, gps_info_distance, gps_info_alarm_alert, gps_info_alarm_data, gps_info_ad_input");
				$this->dbhist->where("gps_info_device", $name."@".$host);
				$this->dbhist->where("gps_info_time >=", $sdate);
				$this->dbhist->where("gps_info_time <=", $edate);
				$this->dbhist->limit(1);
				$qinfo = $this->dbhist->get($tablesinfo);
				$rowlastinfos2 = $qinfo->result();
						
				$rowlastinfos = array_merge($rowlastinfos1, $rowlastinfos2);
                
                //Summary Saja yang diambil
                if ($datatype == 2)
                {
                    for($i=count($rows)-1; $i >= 0; $i--)
                    {
                        if (($i+1) >= count($rows))
                        {
                            $rowsummary[] = $rows[$i];
                            continue;
                        }
                
                        $latbefore = getLatitude($rows[$i+1]->gps_latitude, $rows[$i+1]->gps_ns);
                        $lngbefore = getLongitude($rows[$i+1]->gps_longitude, $rows[$i+1]->gps_ew);

                        $latcurrent = getLatitude($rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                        $lngcurrent = getLongitude($rows[$i]->gps_longitude, $rows[$i]->gps_ew);
                
                        if (sprintf("%.4f,%.4f", $latbefore, $lngbefore) != sprintf("%.4f,%.4f", $latcurrent, $lngcurrent))
                        {
                            $rowsummary[] = $rows[$i];
                            continue;
                        }

                        if ($rows[$i+1]->gps_speed != $rows[$i]->gps_speed)
                        {
                            $rowsummary[] = $rows[$i];
                            continue;
                        }
                    }
                 
                    $rows = array();
                    $total = 0;
                    if (isset($rowsummary))
                    {
                        $rowsummary = array_reverse($rowsummary);            
                        $total = count($rowsummary);
                        $rows = array_splice($rowsummary, $offset, $limit);                            
                    }   
                }
                //Finis Summary
            
                unset($map_params);
                $ismove = false;
                $lastcoord = false;
            
                printf("PROSES XLS FILE FOR : %s  \r\n",$rowvehicle[$x]->vehicle_no);
            
                for($i=0; $i < count($rows); $i++)
                {
                    printf("|%s|",$i);
                    if ($i == 0)
                    {
                        $this->dbhist->order_by("gps_info_time", $order);
                        $this->dbhist->select("gps_info_io_port, gps_info_time, gps_info_distance, gps_info_alarm_alert, gps_info_alarm_data, gps_info_ad_input");
                        $this->dbhist->where("gps_info_device", $name."@".$host);
                        $this->dbhist->where("gps_info_time >=", $sdate);
                        $this->dbhist->where("gps_info_time <=", $edate);
                        $qinfos = $this->dbhist->get($tablesinfo);   
                        $rowinfos = $qinfos->result();
                        $this->db->cache_delete_all();
                
                        for($j=0; $j < count($rowinfos); $j++)
                        {
                            $infos[dbmaketime($rowinfos[$j]->gps_info_time)] = $rowinfos[$j];
                        }
                    }
            
                    $rows[$i]->gps_timestamp = dbmaketime($rows[$i]->gps_time);
                    $rows[$i]->gps_longitude_real = getLongitude($rows[$i]->gps_longitude, $rows[$i]->gps_ew);
                    $rows[$i]->gps_latitude_real = getLatitude($rows[$i]->gps_latitude, $rows[$i]->gps_ns);
                    $rows[$i]->gps_longitude_real_fmt = number_format($rows[$i]->gps_longitude_real, 4, ".", "");
                    $rows[$i]->gps_latitude_real_fmt = number_format($rows[$i]->gps_latitude_real, 4, ".", "");        
            
                    if ($i == 0)
                    {
                        $lastcoord = array($rows[$i]->gps_longitude_real_fmt, $rows[$i]->gps_latitude_real_fmt);
                    }
                    else
                    {
                        if (($lastcoord[0] != $rows[$i]->gps_longitude_real_fmt) || ($lastcoord[1] != $rows[$i]->gps_latitude_real_fmt))
                        {
                            $ismove = true;
                        }
                    }

                    $rows[$i]->gps_date_fmt = date("d/m/Y", $rows[$i]->gps_timestamp+7*3600);
                    $rows[$i]->gps_time_fmt = date("H:i:s", $rows[$i]->gps_timestamp+7*3600);
                    $rows[$i]->gps_speed_fmt = number_format($rows[$i]->gps_speed*1.852, 0, "", ".");
                    $rows[$i]->gps_status = ($rows[$i]->gps_status == "A") ? "OK" : "NOT OK";
            
                    if (isset($infos[$rows[$i]->gps_timestamp]))
                    {
                        $ioport = $infos[$rows[$i]->gps_timestamp]->gps_info_io_port;
                        $rows[$i]->status1 = ((strlen($ioport) > 4) && ($ioport[4] == 1)) ? $this->lang->line('lon') : $this->lang->line('loff');
                        $rows[$i]->odometer = number_format(round(($infos[$rows[$i]->gps_timestamp]->gps_info_distance+$rowvehicle[$x]->vehicle_odometer*1000)/1000), 0, "", ","); 
                    }
                    else
                    {
                        $rows[$i]->status1 = "-";
                        $rows[$i]->odometer = "-";
                    }
                
                    $rows[$i]->georeverse = $this->gpsmodel->GeoReverse($rows[$i]->gps_latitude_real_fmt, $rows[$i]->gps_longitude_real_fmt);            
                    $rows[$i]->gpsindex = $i+1;
                    $rows[$i]->gpsdate = date("d/m/Y", $rows[$i]->gps_timestamp+7*3600);
                    $rows[$i]->gpstime = date("H:i:s", $rows[$i]->gps_timestamp+7*3600);
                    $rows[$i]->gpsaddress = $rows[$i]->georeverse->display_name;
                    $rows[$i]->gpscoord = "(".$rows[$i]->gps_longitude_real_fmt." ".$rows[$i]->gps_latitude_real_fmt.")";
                    $rows[$i]->gpstatus = (($rows[$i]->gps_status == "V") ? "NOT OK" : "OK");
                    $map_params[] = array($rows[$i]->gps_longitude_real_fmt, $rows[$i]->gps_latitude_real_fmt);
            
                    $objPHPExcel->getActiveSheet()->SetCellValue('A'.(6+$i), $i+1);
                    $objPHPExcel->getActiveSheet()->SetCellValue('B'.(6+$i), date("d/m/Y", $rows[$i]->gps_timestamp+7*3600));
                    $objPHPExcel->getActiveSheet()->SetCellValue('C'.(6+$i), date("H:i:s", $rows[$i]->gps_timestamp+7*3600));
                    $objPHPExcel->getActiveSheet()->SetCellValue('D'.(6+$i), $rows[$i]->georeverse->display_name);
                    $objPHPExcel->getActiveSheet()->SetCellValue('E'.(6+$i), $rows[$i]->gps_latitude_real_fmt .",".$rows[$i]->gps_longitude_real_fmt);
                    $objPHPExcel->getActiveSheet()->SetCellValue('F'.(6+$i), $rows[$i]->gps_speed_fmt);
                    $objPHPExcel->getActiveSheet()->SetCellValue('G'.(6+$i), $rows[$i]->status1);
                    $objPHPExcel->getActiveSheet()->SetCellValue('H'.(6+$i), ($rows[$i]->gps_status == "V") ? "NOT OK" : "OK"); 
                    $objPHPExcel->getActiveSheet()->SetCellValue('I'.(6+$i), $rows[$i]->odometer);
                
                    $styleArray = array(
                          'borders' => array(
                            'allborders' => array(
                              'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                          )
                        );
                        
                    $objPHPExcel->getActiveSheet()->getStyle('A5:I'.(5+$i))->applyFromArray($styleArray);
                    $objPHPExcel->getActiveSheet()->getStyle('A5:I'.(5+$i))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
                    $objPHPExcel->getActiveSheet()->getStyle('A5:I'.(5+$i))->getAlignment()->setWrapText(true);
                } 
            
                // Save Excel
                $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
                @mkdir($report_path, DIR_WRITE_MODE); //REPORT_PATH -> config->constant
                
                if (isset($ex_vno))
                {
                    if (isset($ex_vno[1]))
                    {
                        $filecreatedname = "HistoryReport_".$ex_vno[0].$ex_vno[1]."_".$datefilename.".xls";    
                    }
                    else
                    {
                        $filecreatedname = "HistoryReport_".$ex_vno[0]."_".$datefilename.".xls";       
                    }
                }
                else
                {
                    
                    $filecreatedname = "HistoryReport_".$vehicle_no."_".$datefilename.".xls";  
                }
                
                printf("SAVE XLS FILE FOR : %s  \r\n",$filecreatedname); 
                $objWriter->save($report_path.$filecreatedname);
                
                printf("DELETE CACHE GPS \r\n"); 
                $this->db->cache_delete_all();
                $this->dbhist->cache_delete_all();
                
                printf("INSERT TO DB : %s  \r\n",$rowvehicle[$x]->vehicle_no);
                unset($datainsert);
                $datainsert["autoreport_vehicle_id"] = $rowvehicle[$x]->vehicle_id;
                $datainsert["autoreport_vehicle_device"] = $rowvehicle[$x]->vehicle_device;
                $datainsert["autoreport_vehicle_name"] = $rowvehicle[$x]->vehicle_name;
                $datainsert["autoreport_vehicle_no"] = $rowvehicle[$x]->vehicle_no;
                $datainsert["autoreport_user_id"] = $rowvehicle[$x]->user_id;
                $datainsert["autoreport_company"] = $rowvehicle[$x]->user_company;
                $datainsert["autoreport_data_startdate"] = $startdate;
                $datainsert["autoreport_data_enddate"] = $enddate;
                $datainsert["autoreport_type"] = $report_type;
                $datainsert["autoreport_file_path"] = $report_path;
                $datainsert["autoreport_filename"] = $filecreatedname;
                $datainsert["autoreport_download_path"] = $report_path.$filecreatedname;
                $datainsert["autoreport_process_date"] = $process_date; 
                $datainsert["autoreport_insert_db"] = date("Y-m-d H:i:s");
                $this->dbtrans->insert("autoreport_history",$datainsert);
                
                printf("INSERT OK \r\n");
                
                printf("FINIS PROSES FOR VEHICLE %s  \r\n",$rowvehicle[$x]->vehicle_no); 
                printf("===========================================================\r\n");       
            }
        }
        
        unset($datalog);
        $datalog["cron_name"] = "History To Excel";
        $datalog["cron_start_data"] = $startdate;
        $datalog["cron_end_data"] = $enddate;
        $datalog["cron_start_proses"] = $startproses;
        $datalog["cron_end_proses"] = date("Y-m-d H:i:s");
        $this->dbtrans->insert("log_cronjobs",$datalog);
        
        $this->db->cache_delete_all();
        $this->dbhist->cache_delete_all();
        $this->dbtrans->cache_delete_all();
        printf("FINISH  \r\n");
        exit;
    }
    
	function history_xls($userid="", $datatype=0, $orderby="", $startdate= "", $enddate= "")
    {
		ini_set('memory_limit', '3G');
        printf("PROSES AUTO REPORT DATA CSV KHUSUS PORT >> START \r\n");
        $startproses = date("Y-m-d H:i:s");
		$name = "";
		$host = "";
		
		include 'class/PHPExcel.php';
        include 'class/PHPExcel/Writer/Excel2007.php';
	
		$report_type = "csv";
        $process_date = date("Y-m-d H:i:s");
		$start_time = date("Y-m-d H:i:s");
       
		$report = "csv_";
        
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
		
		$dbtable = "transporter_autoreport_csv";
		
        $sdate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($startdate)));
        $edate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($enddate)));
        $z =0;
		
		$this->db->order_by("vehicle_id", $orderby);
		if ($name != "" && $host != "")
        {
            $dev = $name."@".$host;
            $this->db->where("vehicle_device", $dev);    
        } 
        
		if ($userid != "")
		{
			$this->db->where("user_id",$userid);
		}
       
        $this->db->join("user", "vehicle_user_id = user_id", "left outer");
		$this->db->where("vehicle_user_id", $userid);
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_device", "002100000753@T5");
		$this->db->where_not_in("vehicle_type", $this->config->item('vehicle_no_engine'));
		$q = $this->db->get("vehicle");
        $rowvehicle = $q->result();
        
        $total_process = count($rowvehicle);
        printf("TOTAL PROSES VEHICLE : %s \r\n",$total_process);
        printf("============================================ \r\n");
        
		$this->dbtrans = $this->load->database("transporter",true); 
        for($x=0;$x<count($rowvehicle);$x++)
        {
            printf("PROSES VEHICLE : %s %s %s (%d/%d) \r\n",$rowvehicle[$x]->vehicle_device, $rowvehicle[$x]->vehicle_id, $rowvehicle[$x]->user_name, ++$z, $total_process);
			$cron_username = strtoupper($rowvehicle[$x]->user_name);
			$company_username = $rowvehicle[$x]->user_company;
            unset($data_insert);
            //PORT Only
            if (isset($rowvehicle[$x]->vehicle_info))
            {
                $json = json_decode($rowvehicle[$x]->vehicle_info);
                if (isset($json->vehicle_ip) && isset($json->vehicle_port))
                {
                    
					$databases = $this->config->item('databases');
					if (isset($databases[$json->vehicle_ip][$json->vehicle_port]))
					{
						$database = $databases[$json->vehicle_ip][$json->vehicle_port];
						$table = $this->config->item("external_gpstable");
						$tableinfo = $this->config->item("external_gpsinfotable");				
						$this->dbhist = $this->load->database($database, TRUE);
						$this->dbhist2 = $this->load->database("gpshistory",true);		
					}
					else
					{
						$table = $this->gpsmodel->getGPSTable($rowvehicle[$x]->vehicle_type);
						$tableinfo = $this->gpsmodel->getGPSInfoTable($rowvehicle[$x]->vehicle_type);
						$this->dbhist = $this->load->database("default", TRUE);
						$this->dbhist2 = $this->load->database("gpshistory",true);		
					}
					
					$vehicle_device = explode("@", $rowvehicle[$x]->vehicle_device);  
                    $vehicle_no = $rowvehicle[$x]->vehicle_no;
					$vehicle_dev = $rowvehicle[$x]->vehicle_device;
					$vehicle_name = $rowvehicle[$x]->vehicle_name;
					$vehicle_type = $rowvehicle[$x]->vehicle_type;
						
					if ($rowvehicle[$x]->vehicle_type == "T5" || $rowvehicle[$x]->vehicle_type == "T5 PULSE")
                    {
                        $tablehist = $vehicle_device[0]."@t5_gps";
                        $tablehistinfo = $vehicle_device[0]."@t5_info";    
                    }
                    else
                    {
						$tablehist = strtolower($vehicle_device[0])."@".strtolower($vehicle_device[1])."_gps";
						$tablehistinfo = strtolower($vehicle_device[0])."@".strtolower($vehicle_device[1])."_info";
                    }
					
                    //cek apakah sudah pernah ada filenya
                    $this->dbtrans->select("autoreport_vehicle_id");
                    $this->dbtrans->where("autoreport_user_id",$rowvehicle[$x]->vehicle_user_id);
					$this->dbtrans->where("autoreport_vehicle_id",$rowvehicle[$x]->vehicle_id);
                    $this->dbtrans->where("autoreport_data_startdate",$startdate);
					$this->dbtrans->where("autoreport_type",$report_type);
					$this->dbtrans->limit(1);
                    $qrpt = $this->dbtrans->get("autoreport_csv");
                    $rrpt = $qrpt->row();
                    
                    if (count($rrpt) > 0)
                    {
                        printf("VEHICLE %s SUDAH PERNAH DI PROSES, HAPUS DULU DATA SEBELUMNYA \r\n", $rowvehicle[$x]->vehicle_device);     
                        printf("------------------------------------------------------------- \r\n");
                    }
                    else
                    {
						$this->dbhist->select("gps_name,gps_host,gps_time,gps_info_device,gps_longitude_real,gps_latitude_real,gps_speed,
										       gps_info_io_port,gps_info_device,gps_info_distance,gps_status");
                        $this->dbhist->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");    
                        $this->dbhist->where("gps_info_device", $rowvehicle[$x]->vehicle_device);
                        $this->dbhist->where("gps_time >=", $sdate);
                        $this->dbhist->where("gps_time <=", $edate);    
                        $this->dbhist->order_by("gps_time","desc");
						$this->dbhist->group_by("gps_time");
						$this->dbhist->limit(6000);
                        $this->dbhist->from($table);
                        $q = $this->dbhist->get();
                        $rows1 = $q->result();
                  
						$this->dbhist2->select("gps_name,gps_host,gps_time,gps_info_device,gps_longitude_real,gps_latitude_real,gps_speed,
										       gps_info_io_port,gps_info_device,gps_info_distance,gps_status");
						$this->dbhist2->join($tablehistinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");    
                        $this->dbhist2->where("gps_info_device", $rowvehicle[$x]->vehicle_device);
                        $this->dbhist2->where("gps_time >=", $sdate);
                        $this->dbhist2->where("gps_time <=", $edate);    
                        $this->dbhist2->order_by("gps_time","desc");
						$this->dbhist2->group_by("gps_time");
						$this->dbhist2->limit(6000);
                        $this->dbhist2->from($tablehist);
                        $q2 = $this->dbhist2->get();
                        $rows2 = $q2->result();
						
						$rows = array_merge($rows1, $rows2); //limit data rows = 10000
						
						
                        $data = array();
                        $nopol = "";
                        $on = false;
                        $trows = count($rows);
        
                        printf("TOTAL DATA : %s \r\n",$trows);
					if($trows < 10000){
						$objPHPExcel = new PHPExcel();
						printf("WRITE EXCEL :");
                        // Set properties
                        $objPHPExcel->getProperties()->setCreator("lacak-mobil.com");
                        $objPHPExcel->getProperties()->setLastModifiedBy("lacak-mobil.com");
                        $objPHPExcel->getProperties()->setTitle("History Detail Report");
                        $objPHPExcel->getProperties()->setSubject("History Detail Report Lacak-mobil.com");
                        $objPHPExcel->getProperties()->setDescription("History Report Lacak-mobil.com");
                
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
                        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(16);
                        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(45);            
                        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
                        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(7);
                        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(7);
                        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
                        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(12);
                        
                        //Header
                        $objPHPExcel->setActiveSheetIndex(0);
                        $objPHPExcel->getActiveSheet()->mergeCells('A1:I1');
                        $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'HISTORY DETAIL REPORT');
                        $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
                        $objPHPExcel->getActiveSheet()->getStyle('B3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                        $objPHPExcel->getActiveSheet()->getStyle('B3')->getFont()->setBold(true);
                        $objPHPExcel->getActiveSheet()->getStyle('H3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                        $objPHPExcel->getActiveSheet()->getStyle('H3')->getFont()->setBold(true);
						$objPHPExcel->getActiveSheet()->SetCellValue('H3', 'Vehicle');
						
						$objPHPExcel->getActiveSheet()->SetCellValue('I3', $rowvehicle[$x]->vehicle_no);
						$objPHPExcel->getActiveSheet()->getStyle('I3')->getFont()->setBold(true);
						
                        //Top Header
                        $objPHPExcel->getActiveSheet()->SetCellValue('A5', 'No');
                        $objPHPExcel->getActiveSheet()->SetCellValue('B5', 'Date');
                        $objPHPExcel->getActiveSheet()->SetCellValue('C5', 'Time');
                        $objPHPExcel->getActiveSheet()->SetCellValue('D5', 'Position');
                        $objPHPExcel->getActiveSheet()->SetCellValue('E5', 'Coordinate');
                        $objPHPExcel->getActiveSheet()->SetCellValue('F5', 'Status');
                        $objPHPExcel->getActiveSheet()->SetCellValue('G5', 'Speed');
                        $objPHPExcel->getActiveSheet()->SetCellValue('H5', 'Engine');
                        $objPHPExcel->getActiveSheet()->SetCellValue('I5', 'Odometer');
                       
                
                        $objPHPExcel->getActiveSheet()->getStyle('A5:I5')->getFont()->setBold(true);
                        $objPHPExcel->getActiveSheet()->getStyle('A5:I5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						
                        for($i=0; $i < count($rows); $i++)
                        {
							
                            $rows[$i]->gps_timestamp = dbmaketime($rows[$i]->gps_time);
                            $rows[$i]->gps_longitude_real_fmt = number_format($rows[$i]->gps_longitude_real, 4, ".", "");
                            $rows[$i]->gps_latitude_real_fmt = number_format($rows[$i]->gps_latitude_real, 4, ".", "");        
                            $rows[$i]->gps_speed_fmt = number_format($rows[$i]->gps_speed*1.852, 0, "", ".");
                          
            
                            if (isset($rows[$i]->gps_info_io_port))
                            {
                                $ioport = $rows[$i]->gps_info_io_port;
								$infodistance = $rows[$i]->gps_info_distance;
                                $rows[$i]->odometer = number_format(round(($infodistance+$rowvehicle[$x]->vehicle_odometer*1000)/1000), 0, "", ",");
								if($ioport[4] == 1){
									$rows[$i]->gpsengine = "ON";
								}else{
									$rows[$i]->gpsengine = "OFF";
								}
								
								if($rows[$i]->gps_status == "A"){
									$rows[$i]->gpstatus = "OK";
								}else{
									$rows[$i]->gpstatus = "NOT OK";
								}
								 
                            }
                            else
                            {
                                $rows[$i]->odometer = "-";
								$rows[$i]->gpsengine = "-";
								$rows[$i]->gpstatus = "-";
                            }
                
                            $rows[$i]->georeverse = $this->gpsmodel->GeoReverse($rows[$i]->gps_latitude_real_fmt, $rows[$i]->gps_longitude_real_fmt);            
                            $rows[$i]->gpsindex = $i+1;
                            $rows[$i]->gpsdate = date("d/m/Y", $rows[$i]->gps_timestamp+7*3600);
                            $rows[$i]->gpstime = date("H:i:s", $rows[$i]->gps_timestamp+7*3600);
                            $rows[$i]->gpsaddress = $rows[$i]->georeverse->display_name;
                            $rows[$i]->gpscoord = "(".$rows[$i]->gps_latitude_real_fmt." ".$rows[$i]->gps_longitude_real_fmt.")";
                            
							printf(".");
							
							$objPHPExcel->getActiveSheet()->SetCellValue('A'.(6+$i), $i+1);
                            $objPHPExcel->getActiveSheet()->SetCellValue('B'.(6+$i), $rows[$i]->gpsdate);
                            $objPHPExcel->getActiveSheet()->SetCellValue('C'.(6+$i), $rows[$i]->gpstime);
                            $objPHPExcel->getActiveSheet()->SetCellValue('D'.(6+$i), $rows[$i]->gpsaddress);
                            $objPHPExcel->getActiveSheet()->SetCellValue('E'.(6+$i), $rows[$i]->gpscoord);
							$objPHPExcel->getActiveSheet()->SetCellValue('F'.(6+$i), $rows[$i]->gpstatus);
                            $objPHPExcel->getActiveSheet()->SetCellValue('G'.(6+$i), $rows[$i]->gps_speed_fmt);
                            $objPHPExcel->getActiveSheet()->SetCellValue('H'.(6+$i), $rows[$i]->gpsengine);
                            $objPHPExcel->getActiveSheet()->SetCellValue('I'.(6+$i), $rows[$i]->odometer);
							
							 $styleArray = array(
                                'borders' => array(
                                'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                            )
                            );
							
							$objPHPExcel->getActiveSheet()->getStyle('A5:I'.(6+$i))->applyFromArray($styleArray);
                            $objPHPExcel->getActiveSheet()->getStyle('A5:I'.(6+$i))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
                            $objPHPExcel->getActiveSheet()->getStyle('A5:I'.(6+$i))->getAlignment()->setWrapText(true);
							
                        } 
						
							// Save Excel
							$domain_server = "http://transporter.lacak-mobil.com/";
							$vehicleno_text = str_replace(' ', '_', $rowvehicle[$x]->vehicle_no);
							$sdate_text = date("dmY", strtotime($sdate));
							
							$filecreatedname = $vehicleno_text."_".$sdate_text.".xls";
							$pub_path = "assets/media/autoreport/".$report_type."/";
							$report_path = "/home/lacakmobil/public_html/transporter.lacak-mobil.com/public/assets/media/autoreport/".$report_type."/";
							$public_path = $domain_server.$pub_path;
							$download_link = $public_path."".$filecreatedname;
							
							$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
							@mkdir($report_path, DIR_WRITE_MODE); //REPORT_PATH -> config->constant
						
							printf("SAVE XLS FILE FOR : %s  \r\n",$filecreatedname); 
							$objWriter->save($report_path.$filecreatedname);
						
						
						
					}else{
						printf("TO MANY : %s \r\n", $trows);
						printf("LIMIT 10000 DATA SKIP VEHICLE : %s \r\n", $rowvehicle[$x]->vehicle_device);
					}
						printf("PREPARE INSERT TO DB TRANSPORTER \r\n");
                        $data_insert["autoreport_vehicle_id"] = $rowvehicle[$x]->vehicle_id;
                        $data_insert["autoreport_vehicle_device"] = $rowvehicle[$x]->vehicle_device;
                        $data_insert["autoreport_user_id"] = $rowvehicle[$x]->user_id;
                        $data_insert["autoreport_company"] = $rowvehicle[$x]->user_company;
                        $data_insert["autoreport_data_startdate"] = $startdate;
                        $data_insert["autoreport_data_enddate"] = $enddate;
                        $data_insert["autoreport_type"] = $report_type;
                        $data_insert["autoreport_process_date"] = $process_date;
						$data_insert["autoreport_download_path"] = $download_link;
                        $data_insert["autoreport_insert_db"] = date("Y-m-d H:i:s");
						if ($trows > 0 && $trows < 10000){
							$this->dbtrans->insert("autoreport_csv",$data_insert);
							printf("INSERT CONFIG OK \r\n");
						}else{
							printf("SKIP DATA : %s \r\n", $trows);
						}
            
                        printf("DELETE CACHE HISTORY \r\n");
                        $this->dbhist->cache_delete_all();
                        $this->dbtrans->cache_delete_all();
                        unset($data);
                       
                        printf("============================================ \r\n");

                    }
                }
                else
                {
                    printf("SKIP VEHICLE ( NO VEHICLE PORT ) \r\n");
                    printf("-------------------------------------- \r\n");    
                }    
            }
            else
            {
                printf("SKIP VEHICLE ( NO VEHICLE INFO ) \r\n");
                printf("-------------------------------- \r\n");
            }
        }
        
		$finish_time = date("Y-m-d H:i:s");
        printf("AUTOREPORT CSV DONE %s\r\n",$finish_time);
	
	/*if($total_process != 0){
		
		//get telegram group by company
		$this->db = $this->load->database("default",TRUE);
        $this->db->select("company_id,company_telegram_cron");
        $this->db->where("company_id",$company_username);
        $qcompany = $this->db->get("company");
        $rcompany = $qcompany->row();
		if(count($rcompany)>0){
			$telegram_group = $rcompany->company_telegram_cron;
		}else{
			$telegram_group = 0;
		}
		
		$message =  urlencode(
					"".$cron_name." \n".
					"Periode: ".$startdate." to ".$enddate." \n".
					"Total Data: ".$total_data." \n".
					"Start: ".$startproses." \n".
					"Finish: ".$finish_time." \n"
					);
					
		$sendtelegram = $this->telegram_direct($telegram_group,$message);
		printf("===SENT TELEGRAM OK\r\n");
		
	}*/
		
    }
	
	function history_csv($userid="",$orderby="", $startdate="", $enddate="")
    {
		ini_set('memory_limit', '3G');
        printf("PROSES AUTO REPORT DATA CSV KHUSUS PORT >> START \r\n");
        $startproses = date("Y-m-d H:i:s");
		$name = "";
		$host = "";
		
		$report_type = "csv";
        $process_date = date("Y-m-d H:i:s");
		$start_time = date("Y-m-d H:i:s");
       
		$report = "csv_";
		$domain_server = "http://transporter.lacak-mobil.com/";
		$pub_path = "assets/media/autoreport/".$report_type."/";
		$report_path = "/home/lacakmobil/public_html/transporter.lacak-mobil.com/public/assets/media/autoreport/".$report_type."/";
		$public_path = $domain_server.$pub_path;
        
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
		
		$dbtable = "transporter_autoreport_csv";
		$z =0;
		
		$this->db->order_by("vehicle_id", $orderby);
		if ($name != "" && $host != "")
        {
            $dev = $name."@".$host;
            $this->db->where("vehicle_device", $dev);    
        } 
        
		if ($userid != "")
		{
			$this->db->where("user_id",$userid);
		}
       
        $this->db->join("user", "vehicle_user_id = user_id", "left outer");
		$this->db->where("vehicle_user_id", $userid);
		$this->db->where("vehicle_status <>", 3);
		$this->db->where_not_in("vehicle_type", $this->config->item('vehicle_no_engine'));
		$q = $this->db->get("vehicle");
        $rowvehicle = $q->result();
        
        $total_process = count($rowvehicle);
        printf("TOTAL PROSES VEHICLE : %s \r\n",$total_process);
        printf("============================================ \r\n");
        
		$this->dbtrans = $this->load->database("transporter",true); 
        for($x=0;$x<count($rowvehicle);$x++)
        {
            printf("PROSES VEHICLE : %s %s %s (%d/%d) \r\n",$rowvehicle[$x]->vehicle_device, $rowvehicle[$x]->vehicle_id, $rowvehicle[$x]->user_name, ++$z, $total_process);
			$cron_username = strtoupper($rowvehicle[$x]->user_name);
			$company_username = $rowvehicle[$x]->user_company;
            unset($data_insert);
            //PORT Only
            if (isset($rowvehicle[$x]->vehicle_info))
            {
                $json = json_decode($rowvehicle[$x]->vehicle_info);
                if (isset($json->vehicle_ip) && isset($json->vehicle_port))
                {
                    
					$databases = $this->config->item('databases');
					if (isset($databases[$json->vehicle_ip][$json->vehicle_port]))
					{
						$database = $databases[$json->vehicle_ip][$json->vehicle_port];
						$table = $this->config->item("external_gpstable");
						$tableinfo = $this->config->item("external_gpsinfotable");				
						$this->dbhist = $this->load->database($database, TRUE);
						$this->dbhist2 = $this->load->database("gpshistory",true);		
					}
					else
					{
						$table = $this->gpsmodel->getGPSTable($rowvehicle[$x]->vehicle_type);
						$tableinfo = $this->gpsmodel->getGPSInfoTable($rowvehicle[$x]->vehicle_type);
						$this->dbhist = $this->load->database("default", TRUE);
						$this->dbhist2 = $this->load->database("gpshistory",true);		
					}
					
					$vehicle_device = explode("@", $rowvehicle[$x]->vehicle_device);  
                    $vehicle_no = $rowvehicle[$x]->vehicle_no;
					$vehicle_dev = $rowvehicle[$x]->vehicle_device;
					$vehicle_name = $rowvehicle[$x]->vehicle_name;
					$vehicle_type = $rowvehicle[$x]->vehicle_type;
						
					if ($rowvehicle[$x]->vehicle_type == "T5" || $rowvehicle[$x]->vehicle_type == "T5 PULSE")
                    {
                        $tablehist = $vehicle_device[0]."@t5_gps";
                        $tablehistinfo = $vehicle_device[0]."@t5_info";    
                    }
                    else
                    {
						$tablehist = strtolower($vehicle_device[0])."@".strtolower($vehicle_device[1])."_gps";
						$tablehistinfo = strtolower($vehicle_device[0])."@".strtolower($vehicle_device[1])."_info";
                    }
					
                    //cek apakah sudah pernah ada filenya
                    $this->dbtrans->select("autoreport_vehicle_id");
                    $this->dbtrans->where("autoreport_user_id",$rowvehicle[$x]->vehicle_user_id);
					$this->dbtrans->where("autoreport_vehicle_id",$rowvehicle[$x]->vehicle_id);
                    $this->dbtrans->where("autoreport_data_startdate",$startdate);
					$this->dbtrans->where("autoreport_type",$report_type);
					$this->dbtrans->limit(1);
                    $qrpt = $this->dbtrans->get("autoreport_csv");
                    $rrpt = $qrpt->row();
                    
                    if (count($rrpt) > 0)
                    {
                        printf("VEHICLE %s SUDAH PERNAH DI PROSES, HAPUS DULU DATA SEBELUMNYA \r\n", $rowvehicle[$x]->vehicle_device);     
                        printf("------------------------------------------------------------- \r\n");
                    }
                    else
                    {
						if (in_array(strtoupper($rowvehicle[$x]->vehicle_type), $this->config->item("vehicle_others"))){
							$sdate = date("Y-m-d H:i:s", strtotime($startdate));
							$edate = date("Y-m-d H:i:s", strtotime($enddate));
						}
						else
						{
							$sdate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($startdate)));
							$edate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($enddate)));
						}
						
						printf("PERIODE : %s - %s \r\n",$sdate,$edate);
		
						$this->dbhist->select("gps_name,gps_host,gps_time,gps_info_device,gps_longitude_real,gps_latitude_real,gps_speed,
										       gps_info_io_port,gps_info_device,gps_info_distance,gps_status");
                        $this->dbhist->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");    
                        $this->dbhist->where("gps_info_device", $rowvehicle[$x]->vehicle_device);
                        $this->dbhist->where("gps_time >=", $sdate);
                        $this->dbhist->where("gps_time <=", $edate);    
                        $this->dbhist->order_by("gps_time","desc");
						$this->dbhist->group_by("gps_time");
						$this->dbhist->limit(6000);
                        $this->dbhist->from($table);
                        $q = $this->dbhist->get();
                        $rows1 = $q->result();
                  
						$this->dbhist2->select("gps_name,gps_host,gps_time,gps_info_device,gps_longitude_real,gps_latitude_real,gps_speed,
										       gps_info_io_port,gps_info_device,gps_info_distance,gps_status");
						$this->dbhist2->join($tablehistinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");    
                        $this->dbhist2->where("gps_info_device", $rowvehicle[$x]->vehicle_device);
                        $this->dbhist2->where("gps_time >=", $sdate);
                        $this->dbhist2->where("gps_time <=", $edate);    
                        $this->dbhist2->order_by("gps_time","desc");
						$this->dbhist2->group_by("gps_time");
						$this->dbhist2->limit(6000);
                        $this->dbhist2->from($tablehist);
                        $q2 = $this->dbhist2->get();
                        $rows2 = $q2->result();
						
						$rows = array_merge($rows1, $rows2); //limit data rows = 10000
						
                        $data = array();
                        $nopol = "";
                        $on = false;
                        $trows = count($rows);
        
                        printf("TOTAL DATA : %s \r\n",$trows);
					if($trows < 10000){
						printf("CREATE CSV... \r\n");
						$csv = "NO;DATE;TIME;POSITION;COORDINATE;SPEED;ENGINE;GPS;ODOMETER\n";
						for($i=0; $i < count($rows); $i++)
                        {
							
                            $rows[$i]->gps_timestamp = dbmaketime($rows[$i]->gps_time);
                            $rows[$i]->gps_longitude_real_fmt = number_format($rows[$i]->gps_longitude_real, 4, ".", "");
                            $rows[$i]->gps_latitude_real_fmt = number_format($rows[$i]->gps_latitude_real, 4, ".", "");        
                            $rows[$i]->gps_speed_fmt = number_format($rows[$i]->gps_speed*1.852, 0, "", ".");
                          
                            if (isset($rows[$i]->gps_info_io_port))
                            {
                                $ioport = $rows[$i]->gps_info_io_port;
								$infodistance = $rows[$i]->gps_info_distance;
                                $rows[$i]->odometer = number_format(round(($infodistance+$rowvehicle[$x]->vehicle_odometer*1000)/1000), 0, "", ",");
								if($ioport[4] == 1){
									$rows[$i]->gpsengine = "ON";
								}else{
									$rows[$i]->gpsengine = "OFF";
								}
								
								if($rows[$i]->gps_status == "A"){
									$rows[$i]->gpstatus = "OK";
								}else{
									$rows[$i]->gpstatus = "NOT OK";
								}
								 
                            }
                            else
                            {
                                $rows[$i]->odometer = "-";
								$rows[$i]->gpsengine = "-";
								$rows[$i]->gpstatus = "-";
                            }
                
                            $rows[$i]->georeverse = $this->gpsmodel->GeoReverse($rows[$i]->gps_latitude_real_fmt, $rows[$i]->gps_longitude_real_fmt);            
                            $rows[$i]->gpsindex = $i+1;
							
                            if (in_array(strtoupper($rowvehicle[$x]->vehicle_type), $this->config->item("vehicle_others")))
							{
								$rows[$i]->gpsdate = date("d/m/Y", $rows[$i]->gps_timestamp);
								$rows[$i]->gpstime = date("H:i:s", $rows[$i]->gps_timestamp);
							}
							else
							{
								$rows[$i]->gpsdate = date("d/m/Y", $rows[$i]->gps_timestamp+7*3600);
								$rows[$i]->gpstime = date("H:i:s", $rows[$i]->gps_timestamp+7*3600);
							}
							
                            $rows[$i]->gpsaddress = $rows[$i]->georeverse->display_name;
                            $rows[$i]->gpscoord = "(".$rows[$i]->gps_latitude_real_fmt." ".$rows[$i]->gps_longitude_real_fmt.")";
							
							$csv.= $rows[$i]->gpsindex.";".$rows[$i]->gpsdate.";".$rows[$i]->gpstime.";".$rows[$i]->gpsaddress.";".
								   $rows[$i]->gpscoord.";".$rows[$i]->gps_speed_fmt.";".$rows[$i]->gpsengine.";".$rows[$i]->gpstatus.";".
								   $rows[$i]->odometer."\n";
                        } 
						
						$vehicleno_text = str_replace(' ', '_', $rowvehicle[$x]->vehicle_no);
						$sdate_text = date("dmY", strtotime($startdate));
						$filecreatedname = $vehicleno_text."_".$sdate_text.".csv";
						$download_link = $public_path."".$filecreatedname;
						
						$csv_handler = fopen ($report_path.$filecreatedname,'w');
						fwrite ($csv_handler,$csv);
						fclose ($csv_handler);
						
					}else{
						printf("TO MANY : %s \r\n", $trows);
						printf("LIMIT 10000 DATA SKIP VEHICLE : %s \r\n", $rowvehicle[$x]->vehicle_device);
					}
						printf("PREPARE INSERT TO DB TRANSPORTER \r\n");
                        $data_insert["autoreport_vehicle_id"] = $rowvehicle[$x]->vehicle_id;
                        $data_insert["autoreport_vehicle_device"] = $rowvehicle[$x]->vehicle_device;
						$data_insert["autoreport_vehicle_no"] = $rowvehicle[$x]->vehicle_no;
						$data_insert["autoreport_vehicle_name"] = $rowvehicle[$x]->vehicle_name;
						$data_insert["autoreport_user_id"] = $rowvehicle[$x]->user_id;
                        $data_insert["autoreport_company"] = $rowvehicle[$x]->user_company;
                        $data_insert["autoreport_data_startdate"] = $startdate;
                        $data_insert["autoreport_data_enddate"] = $enddate;
                        $data_insert["autoreport_type"] = $report_type;
                        $data_insert["autoreport_process_date"] = $process_date;
						$data_insert["autoreport_public_path"] = $public_path;
						$data_insert["autoreport_filename"] = $filecreatedname;
						$data_insert["autoreport_download_path"] = $download_link;
                        $data_insert["autoreport_insert_db"] = date("Y-m-d H:i:s");
						if ($trows > 0 && $trows < 10000){
							$this->dbtrans->insert("autoreport_csv",$data_insert);
							printf("INSERT CONFIG OK \r\n");
						}else{
							printf("SKIP DATA : %s \r\n", $trows);
						}
            
                        printf("DELETE CACHE HISTORY \r\n");
                        $this->dbhist->cache_delete_all();
                        $this->dbtrans->cache_delete_all();
                        unset($data);
                       
                        printf("============================================ \r\n");

                    }
                }
                else
                {
                    printf("SKIP VEHICLE ( NO VEHICLE PORT ) \r\n");
                    printf("-------------------------------------- \r\n");    
                }    
            }
            else
            {
                printf("SKIP VEHICLE ( NO VEHICLE INFO ) \r\n");
                printf("-------------------------------- \r\n");
            }
        }
        
		$finish_time = date("Y-m-d H:i:s");
        printf("AUTOREPORT CSV DONE %s\r\n",$finish_time);
	
		if($total_process != 0){
			$cron_name = "HISTORY CSV"." - ".$cron_username;
			//get telegram group by company
			$this->db = $this->load->database("default",TRUE);
			$this->db->select("company_id,company_telegram_cron");
			$this->db->where("company_id",$company_username);
			$qcompany = $this->db->get("company");
			$rcompany = $qcompany->row();
			if(count($rcompany)>0){
				$telegram_group = $rcompany->company_telegram_cron;
			}else{
				$telegram_group = 0;
			}
			
			$message =  urlencode(
						"".$cron_name." \n".
						"Periode: ".date('Y-m-d', strtotime($startdate))." to ".date('Y-m-d', strtotime($enddate))." \n".
						"Start: ".$startproses." \n".
						"Finish: ".$finish_time." \n"
						);
						
			$sendtelegram = $this->telegram_direct($telegram_group,$message);
			printf("===SENT TELEGRAM OK\r\n");
			
		}
		
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
