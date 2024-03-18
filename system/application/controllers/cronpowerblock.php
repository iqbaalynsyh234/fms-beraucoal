<?php
include "base.php";

class Cronpowerblock extends Base {

	function Cronpowerblock()
	{
		parent::Base();	
		$this->load->model("gpsmodel");
        $this->load->model("vehiclemodel");
        $this->load->model("configmodel");
        $this->load->helper('common_helper');
        $this->load->helper('kopindosat');
        $this->load->model("historymodel");
		$this->load->library('email');
		$this->load->helper('email');
	}
    
	function index(){}
	
	
	//mobil
	function read_text_file_mobil(){
		
		$this->load->helper("file");
		$extensions = array("txt");
		$file_path = "/home/powerblock/inbox/mobil/";
		$copy_file_path = "/home/powerblock/recheck/";
		$filenames = get_filenames($file_path,$extensions);
		$totalfile = count($filenames);
		$this->dbtransporter = $this->load->database("transporter",true);
		
		if(isset($filenames) && $totalfile > 0)
		{
			$string = $filenames[0];
			$line = file($string);
			$total_line = count($line);
				
			for ($i=0; $i<count($line); $i++)
			{
			
				$mobil = explode(";", $line[$i]);
				$no_mobil = $mobil[0];
				$driver_name = $mobil[1];
				$brand = $mobil[2];
				$tahun_produksi = $mobil[3];
				$tahun_beli = $mobil[4];
				$tipe_mobil = $mobil[5];
				$gps_id = $mobil[6];
				
				unset($data);
				$data["no_mobil"] = $no_mobil;
				$data["driver_id"] = $driver_name;
				$data["brand"] = $brand;
				$data["production_years"] = $tahun_produksi;
				$data["purchase_years"] = $tahun_beli;
				$data["car_type"] = $tipe_mobil;
				$data["gps_id"] = $gps_id;
					
				$this->db->select("vehicle_device, vehicle_id");
				$this->db->where("vehicle_device", $gps_id);
				$qv = $this->db->get("vehicle");	
				$rowsv = $qv->row();
				$total_rowsv = count($rowsv);	
				
				if($total_rowsv > 0)
				{	
					
					$this->dbtransporter->like("driver_name", $driver_name);
					$this->dbtransporter->where("driver_company", 48);
					$q = $this->dbtransporter->get("driver");
					$rows = $q->row();
					$total_rows = count($rows);
					
					
					if($total_rows > 0){
						unset($data_driver);
						$data_driver["driver_vehicle"] = $rowsv->vehicle_id;
						$this->dbtransporter->where("driver_id", $rows->driver_id);
						$this->dbtransporter->update("driver", $data_driver);
					}
					
				}
				
			
				$this->dbtransporter->where("gps_id", $gps_id);
				$q = $this->dbtransporter->get("powerblock_vehicle");
				$rowsveh = $q->result();
				$total_rowsveh = count($rowsveh);
				if ($total_rowsveh > 0)
				{
					$this->dbtransporter->where("driver_id", $driver_name);
					$this->dbtransporter->update("powerblock_vehicle",$data);	
				}
				else
				{
					$this->dbtransporter->insert("powerblock_vehicle",$data);
				}
			}
				
			if(isset($filenames[0]))
			{
				$ex_filename = explode("/",$filenames[0]);
				if (isset($ex_filename[5]))
				{
					$newfile = $copy_file_path.$ex_filename[5];
					if(!copy($filenames[0],$newfile))
					{
						printf("Copy Failed \r\n");	
					}
					else
					{
						printf("Copy File Success \r\n");	
					}
				}
			}
			else
			{
				printf("Empty Files ! Can't Copy File \r\n");	
			}
				
			if(isset($filenames[0]))
			{
				if (unlink($filenames[0]))
				{
					printf("Delete File OK \r\n");	
				}
				else
				{
					printf("Delete File Failed \r\n");	
				}
			}
			else
			{
				printf("Empty Files \r\n");	
			}
				
			printf("FINISH AT %s\r\n",date("d-m-y H:i:s"));
					
		}	
	
	}
	
	//customer
	function read_text_file_customer(){
	
		$this->load->helper("file");
		$extensions = array("txt");
		$file_path = "/home/powerblock/inbox/customer/";
		$copy_file_path = "/home/powerblock/recheck/";
		$filenames = get_filenames($file_path,$extensions);
		$totalfile = count($filenames);
		$this->dbtransporter = $this->load->database("transporter",true);
		
		if(isset($filenames) && $totalfile > 0)
		{
			$string = $filenames[0];
			$line = file($string);
			$total_line = count($line);
				
			for ($i=0; $i<count($line); $i++)
			{
				$group = explode(";", $line[$i]);
				$group_code = $group[0];
				$group_name = $group[1];
					
				unset($data);
				//$data["group_code"] = $group_code;
				$data["group_name"] = $group_name;
				$data["group_status"] = 1;
				$data["group_company"] = 48;
				$data["group_creator"] = 1147;
					
				$this->db->like("group_name", $group_name);
				$q = $this->db->get("group");
				$rows = $q->row();
				$total_rows = count($rows);
				if ($total_rows > 0)
				{
					
				}
				else
				{
					$data["group_created"] = date("Y-m-d H:i:s");
					$this->db->insert("group",$data);
				}
			}
				
			if(isset($filenames[0]))
			{
				$ex_filename = explode("/",$filenames[0]);
				if (isset($ex_filename[5]))
				{
					$newfile = $copy_file_path.$ex_filename[5];
					if(!copy($filenames[0],$newfile))
					{
						printf("Copy Failed \r\n");	
					}
					else
					{
						printf("Copy File Success \r\n");	
					}
				}
			}
			else
			{
				printf("Empty Files ! Can't Copy File \r\n");	
			}
				
			if(isset($filenames[0]))
			{
				if (unlink($filenames[0]))
				{
					printf("Delete File OK \r\n");	
				}
				else
				{
					printf("Delete File Failed \r\n");	
				}
			}
			else
			{
				printf("Empty Files \r\n");	
			}
				
			printf("FINISH AT %s\r\n",date("d-m-y H:i:s"));
					
		}	
	
	}
	
	//driver
	function read_text_file_driver(){
		
		$this->load->helper("file");
		$extensions = array("txt");
		$file_path = "/home/powerblock/inbox/driver";
		$copy_file_path = "/home/powerblock/recheck/";
		$filenames = get_filenames($file_path,$extensions);
		$totalfile = count($filenames);
		$this->dbtransporter = $this->load->database("transporter",true);
		
		
		if(isset($filenames) && $totalfile > 0)
		{
			$string = $filenames[0];
			$line = file($string);
			$total_line = count($line);
				
			for ($i=0; $i<count($line); $i++)
			{
				$driver = explode(";", $line[$i]);
				$driver_code = $driver[0];
				$driver_name = $driver[1];
				$driver_mobile1 = $driver[2];
				$driver_mobile2 = $driver[3];
				$driver_address = $driver[4];
					
				unset($data);
				$data["driver_code"] = $driver_code;
				$data["driver_name"] = $driver_name;
				$data["driver_mobile"] = $driver_mobile1;
				$data["driver_mobile2"] = $driver_mobile2;
				$data["driver_address"] = $driver_address;
				$data["driver_company"] = 48;
				$data["driver_group"] = 0;
					
				$this->dbtransporter->like("driver_name",$driver_name);
				$this->dbtransporter->where("driver_company", 48);
				$q = $this->dbtransporter->get("driver");
				$rows = $q->row();
				$total_rows = count($rows);
				
				if($total_rows > 0){
					$this->dbtransporter->where("driver_id", $rows->driver_id);
					$this->dbtransporter->where("driver_company", 48);
					$this->dbtransporter->update("driver", $data);	
				}else{
					$this->dbtransporter->insert("driver", $data);
				}
			}
				
			if(isset($filenames[0]))
			{
				$ex_filename = explode("/",$filenames[0]);
				if (isset($ex_filename[5]))
				{
					$newfile = $copy_file_path.$ex_filename[5];
					if(!copy($filenames[0],$newfile))
					{
						printf("Copy Failed \r\n");	
					}
					else
					{
						printf("Copy File Success \r\n");	
					}
				}		
			}else{
				printf("Empty Files ! Can't Copy File \r\n");	
			}
				
				
			if(isset($filenames[0]))
			{
				if(unlink($filenames[0]))
				{
					printf("Delete File OK \r\n");	
				}
				else
				{
					printf("Delete File Failed \r\n");	
				}
			}else{
				printf("Empty Files \r\n");	
			}
				
			printf("FINISH AT %s\r\n",date("d-m-y H:i:s"));
					
		}else{
			printf("Data Driver tidak ada \n");
		}	
			
		
	}
	
	
	//travel expence
	function read_text_file_travel_expense(){
		
		$this->load->helper("file");
		$extensions = array("txt");
		$file_path = "/home/powerblock/inbox/travel_expence/";
		$copy_file_path = "/home/powerblock/recheck/";
		$filenames = get_filenames($file_path,$extensions);
		$totalfile = count($filenames);
		$this->dbtransporter = $this->load->database("transporter",true);
		
		
			if(isset($filenames) && $totalfile > 0)
			{
				$string = $filenames[0];
				$line = file($string);
				$total_line = count($line);
				
				for ($i=0; $i<count($line); $i++)
				{
				
					$trap_ex = explode(";", $line[$i]);
					$area = $trap_ex[0];
					$car_type = $trap_ex[1];
					$amount = $trap_ex[2];
					
					$this->dbtransporter->like("destination_name", $area);
					$this->dbtransporter->where("destination_company", 48);
					$this->dbtransporter->where("destination_status", 1);
					$q_area = $this->dbtransporter->get("destination");
					$rows_area = $q_area->row();
					$total_rows = count($rows_area);
					
					unset($data_area);
					$data_area["destination_company"] = 48;
					$data_area["destination_status"] = 1;
					$data_area["destination_name"] = $area;
						
					if($total_rows > 0)
					{
						$this->dbtransporter->where("destination_id", $rows_area->destination_id);
						$this->dbtransporter->update("destination", $data_area);
					}
					else
					{
						$this->dbtransporter->insert("destination",$data_area);
					}
						
					unset($data);
					$data["cost_vehicle_type"] = $car_type;
					$data["cost"] = $amount;
					$data["cost_company"] = 48;
					$data["cost_status"] = 1;
					
					if($total_rows > 0){
					
						$this->dbtransporter->where("cost_destination", $rows_area->destination_id);
						$q = $this->dbtransporter->get("cost");
						$rows_cost = $q->result();
						$total_rowscost = count($rows_cost);
						
						if($total_rowscost > 0)
						{
							$this->dbtransporter->where("cost_destination", $rows_area->destination_id);
							$this->dbtransporter->update("cost",$data);
							
						}else{
						
							$data["cost_destination"] = $rows_area->destination_id;
							$this->dbtransporter->insert("cost",$data);
						}
						
					}else{
						
						$this->dbtransporter->where("destination_name", $area);
						$q_areain = $this->dbtransporter->get("destination");
						$rows_areain = $q_areain->row();
						$total_rowsin = count($rows_areain);
						
						if($total_rowsin > 0){
							$data["cost_destination"] = $rows_areain->destination_id;
							$this->dbtransporter->insert("cost",$data);
						}
						
					}
					
				}
				
				if (isset($filenames[0]))
				{
					$ex_filename = explode("/",$filenames[0]);
					if (isset($ex_filename[5]))
					{
						$newfile = $copy_file_path.$ex_filename[5];
						if(!copy($filenames[0],$newfile))
						{
							printf("Copy Failed \r\n");	
						}
						else
						{
							printf("Copy File Success \r\n");	
						}
					}
					
				}
				else
				{
					printf("Empty Files ! Can't Copy File \r\n");	
				}
				
				
				if(isset($filenames[0]))
				{
					if (unlink($filenames[0]))
					{
						printf("Delete File OK \r\n");	
					}
					else
					{
						printf("Delete File Failed \r\n");	
					}
				}
				else
				{
					printf("Empty Files \r\n");	
				}
				
				printf("FINISH AT %s\r\n",date("d-m-y H:i:s"));
					
			}else{
				printf("Data Travel Expanece tidak ada \n");
			}		
		
		
			
	
	}
	
	
	
	
    function read_text_file_suratjalan()
	{
		$this->load->helper("file");
		$extensions = array("txt");
		$file_path = "/home/powerblock/inbox/suratjalan/";
		$copy_file_path = "/home/powerblock/recheck/";
		$filenames = get_filenames($file_path,$extensions);
		$totalfile = count($filenames);
		$this->dbtransporter = $this->load->database("transporter",true);
		
		if (isset($filenames) && $totalfile > 0)
		{
			//$string = read_file($filenames[$i]); >> Bawaan Code Igniter
			$string = $filenames[0];
			$line = file($string);
			$total_line = count($line);
			
			for ($i=0; $i<count($line); $i++)
			{
			
				$surat_jalan = explode(";", $line[$i]);
				$sales_order_block = trim($surat_jalan[0]);
				$sales_order_bond = trim($surat_jalan[1]);
				$sales_ship_block = trim($surat_jalan[2]);
				$sales_ship_bond = trim($surat_jalan[3]);
				$sales_ship_date = trim($surat_jalan[4]);
				$cust_no = trim($surat_jalan[5]);
				$ship_name = trim($surat_jalan[6]);
				$ship_address = trim($surat_jalan[7]);
				$ship_address2 = trim($surat_jalan[8]);
				$sales_code = trim($surat_jalan[9]);
				$shipping_agen_code = trim($surat_jalan[10]);
				$travel_expense_amount = trim($surat_jalan[11]);
				$vehicle_no = trim($surat_jalan[12]);
				$vehicle_id = trim($surat_jalan[13]);
				$item_type = trim($surat_jalan[14]);
				$item_no = trim($surat_jalan[15]);
				$item_desc = trim($surat_jalan[16]);
				$item_desc2 = trim($surat_jalan[17]);
				$qty = trim($surat_jalan[18]);
				$uom_code = trim($surat_jalan[19]);
				$amount = trim($surat_jalan[20]);

				$exp = explode("@", $vehicle_id);
				
				
				unset($data);
				$data["suratjalan_sales_order_block"] = $sales_order_block;
				$data["suratjalan_sales_order_bond"] = $sales_order_bond;
				$data["suratjalan_ship_block"] = $sales_ship_block;
				$data["suratjalan_ship_bond"] = $sales_ship_bond;
				$data["suratjalan_ship_date"] = date("Y-m-d", strtotime($sales_ship_date));
				$data["suratjalan_cust_no"] = $cust_no;
				$data["suratjalan_ship_name"] = $ship_name; 
				$data["suratjalan_ship_address"] = $ship_address;
				$data["suratjalan_ship_address2"] = $ship_address2;
			    $data["suratjalan_sales_code"] = $sales_code;
				$data["suratjalan_ship_agen_code"] = $shipping_agen_code;
				$data["suratjalan_travel_expense_amount"] = $travel_expense_amount;
				$data["suratjalan_vehicle_no"] = $vehicle_no;
				$data["suratjalan_vehicle_id"] = $exp[0]."@T5";
				$data["suratjalan_item_type"] = $item_type;
				$data["suratjalan_item_no"] = $item_no;
				$data["suratjalan_desc"] = $item_desc;
				$data["suratjalan_desc2"] = $item_desc2;
				$data["suratjalan_qty"] = $qty;
				$data["suratjalan_uom_code"] = $uom_code;
				$data["suratjalan_amount"] = $amount;
				
				
				//Cek sales order block/bond sudah ada atau belum
				$this->dbtransporter->where("suratjalan_sales_order_block",$sales_order_block);
				$this->dbtransporter->where("suratjalan_sales_order_bond",$sales_order_bond);
				$q = $this->dbtransporter->get("powerblock_suratjalan");
				$rows = $q->result();
				$total_rows = count($rows);
				if ($total_rows > 0)
				{
					//$sendmaildb = $this->sendmail_to_db($id_booking, $dbcode, $dr, $so);
				}
				else
				{
					$this->dbtransporter->insert("powerblock_suratjalan",$data);
					//$sendmaildb = $this->sendmail_to_db($id_booking, $dbcode, $dr, $so);
				}
			}
		}
		
		//Copy File to directory Recheck
		if (isset($filenames[0]))
		{
			$ex_filename = explode("/",$filenames[0]);
			if (isset($ex_filename[5]))
			{
				$newfile = $copy_file_path.$ex_filename[5];
				if(!copy($filenames[0],$newfile))
				{
					printf("Copy Failed \r\n");	
				}
				else
				{
					printf("Copy File Success \r\n");	
				}
			}
			
		}
		else
		{
			printf("Empty Files ! Can't Copy File \r\n");	
		}
		
		//Delete File
		if (isset($filenames[0]))
		{
			if (unlink($filenames[0]))
			{
				printf("Delete File OK \r\n");	
			}
			else
			{
				printf("Delete File Failed \r\n");	
			}
		}
		else
		{	
			printf("Empty Files \r\n");	
		}
		
		printf("FINISH AT %s\r\n",date("d-m-y H:i:s"));
	}
	
	function sendmail_to_db($key, $dbcode, $dr, $so)
	{
		$content = "";$myvehicle ="";$myekspedisi = "";$link="";
		$this->dbtransporter->where("booking_id",$key);
		$this->dbtransporter->where("booking_delivery_status",1);
		$this->dbtransporter->where("booking_status",1);
		$this->dbtransporter->limit(1);
		$q = $this->dbtransporter->get("id_booking");
		$row = $q->row();
		if(count($row)>0)
		{
			//print_r($row);exit;
			if($row->booking_vehicle != "" || $row->booking_vehicle != 0)
			{
				$dbmail = $this->getdbmail($dbcode);
				$myvehicle = $this->getvehicle($row->booking_vehicle);
				$myekspedisi = $this->getekspedisi($row->booking_vehicle);
				if($dbmail != "")
				{
					$exv = explode("@",$row->booking_vehicle);
					$link = "http://tupperware.lacak-mobil.com/mod_db_tupperware/dbtupper/".$exv[0].
							"/".$dr."/".$so."/".$dbcode."/".date("YmdHis")."/".$exv[1];
					$emails = get_valid_emails($dbmail);
					$content = "<html>".
							   "<body>".
							   "Kami telah menerima permintaan Order Anda.<br />"." ".
							   "Order tersebut telah kami proses dan dikirimkan pada:"." ".date("d-m-Y").",<br />".
							   "<br />".
							   "<font size='4px'><b>DELIVERY</b>- Barang pesanan sedang di perjalanan.</font><br />".
							   "<br />".
							   "Berikut ini adalah informasi detail transaksi tsb :<br />".
							   "<table width='100%'>".
							   "<tr>".
							   "<td width='30%'>".
							   "OrderNo [date] ".
							   "</td>".
							   "<td width='3%'>".
							   ":".
							   "</td>".
							   "<td>".
							   $so." "."[".date("d-m-Y")."]".
							   "</td>".
							   "</tr>".
							   "<tr>".
							   "<td>".
							   "Purchase No. ".
							   "</td>".
							   "<td>".
							   ":".
							   "</td>".
							   "<td>".
							   "&nbsp;".
							   "</td>".
							   "<tr>".
							   "<td>".
							   "DRNo ".
							   "</td>".
							   "<td>".
							   ":".
							   "</td>".
							   "<td>".$dr."</td>".
							   "</tr>".
							   "<tr>".
							   "<td>".
							   "Expedisi ".
							   "</td>".
							   "<td>".
							   ":".
							   "</td>".
							   "<td>".$myekspedisi."</td>".
							   "</tr>".
							   "<tr>".
							   "<td>".
							   "No /Vehicle/Truk/Kapal ".
							   "</td>".
							   "<td>".
							   ":".
							   "</td>".
							   "<td>".
							   $myvehicle."</td>".
							   "</tr>".
							   "</table>".
							   "<br />".
							   "Posisi order tersebut dapat dilihat melalui GPS pada link dibawah ini :<br />".
							   "Link :"." ".$link."<br />".
							   "<br />".
							   "Terima Kasih"."<br />".
							   "<br />".
							   "Tupperware".
							   "</body>".
							   "</html>";
					if (is_array($emails) && count($emails))
					{
						foreach($emails as $email)
						{
							 $subject = "DELIVERY REPORT TUPPERWARE";
							 $msgbody = $content;
							 $tomail = $email;
							 $tocc = "agung@lacak-mobil.com";
							 $process = $this->smtpmailer($tomail, 'support@lacak-mobil.com', 'Support Tupperware', $subject, $msgbody, $tocc);
							 if($process == true)
							{
								printf("PROCESS SEND MAIL OK \r\n");
							}
							else
							{
								printf("PROCESS SEND MAIL FAIL! \r\n");	
								printf("ERROR! %s \r\n",$process);	
							}
						}
					}
				}
			}
		}
	}
	function getdbmail($key)
	{
		$v = "";
		$this->dbtransporter->where("dist_code",$key);
		$this->dbtransporter->where("dist_status",1);
		$this->dbtransporter->limit(1);
		$q = $this->dbtransporter->get("dist_tupper");
		$row = $q->row();
		if(count($row)>0)
		{
			$v = $row->dist_email;
		}
		return $v;
	}
	function getvehicle($key)
	{
		$ve = "";
		$this->db->select("vehicle_no");
		$this->db->where("vehicle_device",$key);
		$this->db->limit(1);
		$q = $this->db->get("vehicle");
		$row = $q->row();
		if(count($row)>0)
		{
			$ve = $row->vehicle_no;
		}
		return $ve;
	}
	function getekspedisi($key)
	{
		$ve = "";
		$this->db->select("vehicle_user_id");
		$this->db->where("vehicle_device",$key);
		$this->db->limit(1);
		$q = $this->db->get("vehicle");
		$row = $q->row();
		if(count($row)>0)
		{
			$ve = $row->vehicle_user_id;
			$this->dbtransporter->select("slcars_name");
			$this->dbtransporter->where("slcars_lacak_code",$ve);
			$this->dbtransporter->where("slcars_status",1);
			$this->dbtransporter->limit(1);
			$q = $this->dbtransporter->get("tupper_slcars");
			$row = $q->row();
			if(count($row)>0)
			{
				$ve = $row->slcars_name;
			}
		}
		return $ve;
	}
	function smtpmailer($to, $from, $from_name, $subject, $body, $tocc) 
	{ 
		require_once ('class/phpmailer/class.phpmailer.php');
		require_once ('class/phpmailer/class.smtp.php');
		global $error;
		$mail = new PHPMailer();  // create a new object
		$mail->IsSMTP(); // enable SMTP
		$mail->SMTPDebug = 0;  // debugging: 1 = errors and messages, 2 = messages only
		$mail->SMTPAuth = true;  // authentication enabled
		$mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for GMail
		$mail->Host = 'smtp.gmail.com';
		$mail->Port = 465; 
		$mail->IsHTML(true);
		$mail->Username = "agung@lacak-mobil.com";  
		$mail->Password = "ariestiani";           
		$mail->SetFrom($from, $from_name);
		$mail->Subject = $subject;
		$mail->Body = $body;
		$mail->AddAddress($to);
		$mail->AddCC($tocc);
		/* foreach($tocc as $email => $name)
		{
			$mail->AddCC($email, $name);
		} */
		if(!$mail->Send()) 
		{
			$error = 'Mail error: '.$mail->ErrorInfo; 
			return $error;
		} 
		else 
		{
			$error = 'Message sent!';
			return true;
		}
	}
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
