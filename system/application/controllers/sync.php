<?php
include "base.php";

class Sync extends Base {
	function __construct()
	{
		parent::__construct();	
		
		$this->load->model("gpsmodel");
		$this->load->model("smsmodel");
		$this->load->model("configmodel");
		$this->load->library('email');
		$this->load->helper('email');
		$this->load->helper('common');
		
	}
	
	
	function all(){
		$this->vehicle();
		$this->vehicle_autocheck();
		$this->user();
		$this->company();
		$this->group();
		$this->apiuser();
		$this->configport();
		$this->cronport();
		$this->config();
		$this->privilege();
		$this->subcompany();
		$this->subgroup();
		$this->teknisi();
		$this->sales();
		
		
	}
	
	function vehicle()
	{
		ini_set('memory_limit', '1G');
		$offset = 0;
		$i = 0;
		$start_time = date("Y-m-d H:i:s");
	
		
		printf("PROSES SYNC VEHICLE \r\n");
		$this->dbprimary = $this->load->database("webtracking_primary", TRUE);
		$this->dbprimary->order_by("vehicle_id","desc");
		$q = $this->dbprimary->get("vehicle");
		$rows = $q->result();
		$total = count($rows);
		printf("Total DATA : %s \r\n", $total); 
		$j = 0;
		for($i=0;$i<$total; $i++)
		{
			
			printf("PROCESS DATA	 : %s \r\n", ++$j." of ".$total);
			printf("GET DATA  : %s %s %s \r\n", $rows[$i]->vehicle_id, $rows[$i]->vehicle_device, $rows[$i]->vehicle_no);
			
					unset($data);
					$data["vehicle_id"] = $rows[$i]->vehicle_id;
					$data["vehicle_user_id"] = $rows[$i]->vehicle_user_id;
					$data["vehicle_user_parent"] = $rows[$i]->vehicle_user_parent;
					$data["vehicle_device"] = $rows[$i]->vehicle_device;
					$data["vehicle_no"] = $rows[$i]->vehicle_no;
					$data["vehicle_no_bk"] = $rows[$i]->vehicle_no_bk;
					$data["vehicle_note"] = $rows[$i]->vehicle_note;
					$data["vehicle_name"] = $rows[$i]->vehicle_name;
					$data["vehicle_active_date2"] = $rows[$i]->vehicle_active_date2;
					$data["vehicle_card_no"] = $rows[$i]->vehicle_card_no;
					$data["vehicle_operator"] = $rows[$i]->vehicle_operator;
					$data["vehicle_active_date"] = $rows[$i]->vehicle_active_date;
					$data["vehicle_active_date1"] = $rows[$i]->vehicle_active_date1;
					$data["vehicle_status"] = $rows[$i]->vehicle_status;
					$data["vehicle_image"] = $rows[$i]->vehicle_image;
					$data["vehicle_created_date"] = $rows[$i]->vehicle_created_date;
					$data["vehicle_type"] = $rows[$i]->vehicle_type;
					$data["vehicle_autorefill"] = $rows[$i]->vehicle_autorefill;
					$data["vehicle_maxspeed"] = $rows[$i]->vehicle_maxspeed;
					$data["vehicle_maxparking"] = $rows[$i]->vehicle_maxparking;
					$data["vehicle_company"] = $rows[$i]->vehicle_company;
					$data["vehicle_subcompany"] = $rows[$i]->vehicle_subcompany;
					$data["vehicle_group"] = $rows[$i]->vehicle_group;
					$data["vehicle_subgroup"] = $rows[$i]->vehicle_subgroup;
					$data["vehicle_odometer"] = $rows[$i]->vehicle_odometer;
					$data["vehicle_payment_type"] = $rows[$i]->vehicle_payment_type;
					$data["vehicle_payment_amount"] = $rows[$i]->vehicle_payment_amount;
					$data["vehicle_fuel_capacity"] = $rows[$i]->vehicle_fuel_capacity;
					$data["vehicle_fuel_volt"] = $rows[$i]->vehicle_fuel_volt;
					$data["vehicle_info"] = $rows[$i]->vehicle_info;
					$data["vehicle_server"] = $rows[$i]->vehicle_server;
					$data["vehicle_sales"] = $rows[$i]->vehicle_sales;
					$data["vehicle_teknisi_id"] = $rows[$i]->vehicle_teknisi_id;
					$data["vehicle_tanggal_pasang"] = $rows[$i]->vehicle_tanggal_pasang;
					$data["vehicle_imei"] = $rows[$i]->vehicle_imei;
					$data["vehicle_dbhistory"] = $rows[$i]->vehicle_dbhistory;
					$data["vehicle_dbhistory_name"] = $rows[$i]->vehicle_dbhistory_name;
					$data["vehicle_dbname_live"] = $rows[$i]->vehicle_dbname_live;
					$data["vehicle_isred"] = $rows[$i]->vehicle_isred;
					$data["vehicle_gotohistory"] = $rows[$i]->vehicle_gotohistory;
					$data["vehicle_modem"] = $rows[$i]->vehicle_modem;
					$data["vehicle_card_no_status"] = $rows[$i]->vehicle_card_no_status;
					$data["vehicle_autocheck"] = $rows[$i]->vehicle_autocheck;
					$data["vehicle_tms"] = $rows[$i]->vehicle_tms;
					$data["vehicle_mv03"] = $rows[$i]->vehicle_mv03;
					$data["vehicle_cam_type"] = $rows[$i]->vehicle_cam_type;
					$data["vehicle_sensor"] = $rows[$i]->vehicle_sensor;
					$data["vehicle_sos"] = $rows[$i]->vehicle_sos;
					$data["vehicle_mdt"] = $rows[$i]->vehicle_mdt;
					$data["vehicle_nourut"] = $rows[$i]->vehicle_nourut;
					$data["vehicle_is_share"] = $rows[$i]->vehicle_is_share;
					$data["vehicle_id_shareto"] = $rows[$i]->vehicle_id_shareto;
					$data["vehicle_wim_stime"] = $rows[$i]->vehicle_wim_stime;
					$data["vehicle_wim_etime"] = $rows[$i]->vehicle_wim_etime;
					$data["vehicle_portal_rangka"] = $rows[$i]->vehicle_portal_rangka;
					$data["vehicle_portal_mesin"] = $rows[$i]->vehicle_portal_mesin;
					$data["vehicle_portal_rfid_spi"] = $rows[$i]->vehicle_portal_rfid_spi;
					$data["vehicle_portal_rfid_wim"] = $rows[$i]->vehicle_portal_rfid_wim;
					$data["vehicle_portal_tare"] = $rows[$i]->vehicle_portal_tare;
					$data["vehicle_port_time"] = $rows[$i]->vehicle_port_time;
					$data["vehicle_port_name"] = $rows[$i]->vehicle_port_name;
					$data["vehicle_rom_time"] = $rows[$i]->vehicle_rom_time;
					$data["vehicle_rom_name"] = $rows[$i]->vehicle_rom_name;
					$data["vehicle_typeunit"] = $rows[$i]->vehicle_typeunit;
				
				$this->dbsecondary = $this->load->database("webtracking_secondary", true);
				$this->dbsecondary->order_by("vehicle_id", "desc");
				$this->dbsecondary->where("vehicle_id", $rows[$i]->vehicle_id);
				$this->dbsecondary->limit(1);
				$q_exists = $this->dbsecondary->get("vehicle");
				$rows_exists = $q_exists->row();
				
				
				if (count($rows_exists)>0)
				{
					printf("UPDATE VEHICLE IN DB SECONDARY : %s, %s, %s \r\n", $rows[$i]->vehicle_id, $rows[$i]->vehicle_device, $rows[$i]->vehicle_no); 
					
					$this->dbsecondary->where("vehicle_id", $rows[$i]->vehicle_id);
					$this->dbsecondary->limit(1);
					$this->dbsecondary->update("vehicle",$data);
					printf("UPDATE OK \r\n");
					printf("=============================================== \r\n"); 
				}
				//jika tidak ada, insert all field data
				else
				{
					printf("INSERT VEHICLE IN DB SECONDARY : %s, %s, %s \r\n", $rows[$i]->vehicle_id, $rows[$i]->vehicle_device, $rows[$i]->vehicle_no); 
					
					$this->dbsecondary->insert("vehicle", $data);
					
					printf("INSERT OK \r\n");
					printf("=============================================== \r\n"); 
				}
				
				
			
		}
		
		$this->dbprimary->close();
		$this->dbprimary->cache_delete_all();
		
		$this->dbsecondary->close();
		$this->dbsecondary->cache_delete_all();
		
		$finish_time = date("Y-m-d H:i:s");
		printf("FINISH !! %s, \r\n", $finish_time); 
		printf("=============================================== \r\n"); 
		
		$title_name = "SYNC TABLE VEHICLE";
			$message = urlencode(
			"".$title_name." \n".
			"Total Rows: ".$total." \n".
			"Start: ".$start_time." \n".
			"Finish: ".$finish_time." \n"
														
		);
		sleep(2);		
		$sendtelegram = $this->telegram_direct("-664183792",$message); // SYNC TELE
		printf("===SENT TELEGRAM OK\r\n");	
		
		return;   
		
	}
	
	function vehicle_autocheck()
	{
		ini_set('memory_limit', '1G');
		$offset = 0;
		$i = 0;
		$start_time = date("Y-m-d H:i:s");
	
		
		printf("PROSES SYNC VEHICLE AUTOCHECK \r\n");
		$this->dbprimary = $this->load->database("webtracking_primary", TRUE);
		$this->dbprimary->order_by("auto_id","desc");
		$q = $this->dbprimary->get("vehicle_autocheck");
		$rows = $q->result();
		$total = count($rows);
		printf("Total Vehicle Autocheck : %s \r\n", $total); 
		$j = 0;
		for($i=0;$i<$total; $i++)
		{
			
			printf("PROCESS VEHICLE	 : %s \r\n", ++$j." of ".$total);
			printf("GET VEHICLE  : %s %s %s \r\n", $rows[$i]->auto_id, $rows[$i]->auto_vehicle_device, $rows[$i]->auto_vehicle_no);
			
					unset($data);
					$data["auto_id"] = $rows[$i]->auto_id;
					$data["auto_user_id"] = $rows[$i]->auto_user_id;
					$data["auto_user_name"] = $rows[$i]->auto_user_name;
					$data["auto_vehicle_id"] = $rows[$i]->auto_vehicle_id;
					$data["auto_vehicle_no"] = $rows[$i]->auto_vehicle_no;
					$data["auto_vehicle_name"] = $rows[$i]->auto_vehicle_name;
					$data["auto_vehicle_device"] = $rows[$i]->auto_vehicle_device;
					$data["auto_vehicle_type"] = $rows[$i]->auto_vehicle_type;
					$data["auto_vehicle_company"] = $rows[$i]->auto_vehicle_company;
					$data["auto_vehicle_subcompany"] = $rows[$i]->auto_vehicle_subcompany;
					$data["auto_vehicle_group"] = $rows[$i]->auto_vehicle_group;
					$data["auto_vehicle_subgroup"] = $rows[$i]->auto_vehicle_subgroup;
					$data["auto_vehicle_active_date2"] = $rows[$i]->auto_vehicle_active_date2;
					$data["auto_simcard"] = $rows[$i]->auto_simcard;
					$data["auto_status"] = $rows[$i]->auto_status;
					$data["auto_last_update"] = $rows[$i]->auto_last_update;
					$data["auto_last_check"] = $rows[$i]->auto_last_check;
					$data["auto_last_position"] = $rows[$i]->auto_last_position;
					$data["auto_last_lat"] = $rows[$i]->auto_last_lat;
					$data["auto_last_long"] = $rows[$i]->auto_last_long;
					$data["auto_last_engine"] = $rows[$i]->auto_last_engine;
					$data["auto_last_gpsstatus"] = $rows[$i]->auto_last_gpsstatus;
					$data["auto_last_speed"] = $rows[$i]->auto_last_speed;
					$data["auto_last_course"] = $rows[$i]->auto_last_course;
					$data["auto_last_road"] = $rows[$i]->auto_last_road;
					$data["auto_last_hauling"] = $rows[$i]->auto_last_hauling;
					$data["auto_last_rom_name"] = $rows[$i]->auto_last_rom_name;
					$data["auto_last_rom_time"] = $rows[$i]->auto_last_rom_time;
					$data["auto_last_port_name"] = $rows[$i]->auto_last_port_name;
					$data["auto_last_port_time"] = $rows[$i]->auto_last_port_time;
					$data["auto_last_nonbib"] = $rows[$i]->auto_last_nonbib;
					$data["auto_flag"] = $rows[$i]->auto_flag;
					$data["auto_change_engine_status"] = $rows[$i]->auto_change_engine_status;
					$data["auto_change_engine_datetime"] = $rows[$i]->auto_change_engine_datetime;
					$data["auto_change_position"] = $rows[$i]->auto_change_position;
					$data["auto_change_coordinate"] = $rows[$i]->auto_change_coordinate;
					
				
				$this->dbsecondary = $this->load->database("webtracking_secondary", true);
				$this->dbsecondary->order_by("auto_id", "desc");
				$this->dbsecondary->where("auto_id", $rows[$i]->auto_id);
				$this->dbsecondary->limit(1);
				$q_exists = $this->dbsecondary->get("vehicle_autocheck");
				$rows_exists = $q_exists->row();
			
				if (count($rows_exists)>0)
				{
					printf("UPDATE VEHICLE IN DB SECONDARY : %s, %s, %s \r\n", $rows[$i]->auto_id, $rows[$i]->auto_vehicle_device, $rows[$i]->auto_vehicle_no); 
					
					$this->dbsecondary->where("auto_id", $rows[$i]->auto_id);
					$this->dbsecondary->limit(1);
					$this->dbsecondary->update("vehicle_autocheck",$data);
					printf("UPDATE OK \r\n");
					printf("=============================================== \r\n"); 
				}
				//jika tidak ada, insert all field data
				else
				{
					printf("INSERT VEHICLE IN DB SECONDARY : %s, %s, %s \r\n", $rows[$i]->auto_id, $rows[$i]->auto_vehicle_device, $rows[$i]->auto_vehicle_no); 
					
					$this->dbsecondary->insert("vehicle_autocheck", $data);
					
					printf("INSERT OK \r\n");
					printf("=============================================== \r\n"); 
				}
				
				
			
		}
		
		$this->dbprimary->close();
		$this->dbprimary->cache_delete_all();
		
		$this->dbsecondary->close();
		$this->dbsecondary->cache_delete_all();
		
		$finish_time = date("Y-m-d H:i:s");
		printf("FINISH !! %s, \r\n", $finish_time); 
		printf("=============================================== \r\n"); 
		
		$title_name = "SYNC TABLE VEHICLE AUTOCHECK";
			$message = urlencode(
			"".$title_name." \n".
			"Total Rows: ".$total." \n".
			"Start: ".$start_time." \n".
			"Finish: ".$finish_time." \n"
														
		);
		sleep(2);		
		$sendtelegram = $this->telegram_direct("-664183792",$message); // SYNC TELE
		printf("===SENT TELEGRAM OK\r\n");	
		
		return;   
		
	}
	
	function user()
	{
		ini_set('memory_limit', '1G');
		$offset = 0;
		$i = 0;
		$start_time = date("Y-m-d H:i:s");
	
		
		printf("PROSES SYNC USER \r\n");
		$this->dbprimary = $this->load->database("webtracking_primary", TRUE);
		$this->dbprimary->order_by("user_id","desc");
		$q = $this->dbprimary->get("user");
		$rows = $q->result();
		$total = count($rows);
		printf("Total User : %s \r\n", $total); 
		$j = 0;
		for($i=0;$i<$total; $i++)
		{
			
			printf("PROCESS USER	 : %s \r\n", ++$j." of ".$total);
			printf("GET USER  : %s %s %s \r\n", $rows[$i]->user_id, $rows[$i]->user_login, $rows[$i]->user_name);
			
					unset($data);
					$data["user_id"] = $rows[$i]->user_id;
					$data["user_login"] = $rows[$i]->user_login;
					$data["user_pass"] = $rows[$i]->user_pass;
					$data["user_name"] = $rows[$i]->user_name;
					$data["user_license_id"] = $rows[$i]->user_license_id;
					$data["user_license_type"] = $rows[$i]->user_license_type;
					$data["user_sex"] = $rows[$i]->user_sex;
					$data["user_birth_date"] = $rows[$i]->user_birth_date;
					$data["user_province"] = $rows[$i]->user_province;
					$data["user_city"] = $rows[$i]->user_city;
					$data["user_address"] = $rows[$i]->user_address;
					$data["user_mobile"] = $rows[$i]->user_mobile;
					$data["user_phone"] = $rows[$i]->user_phone;
					$data["user_type"] = $rows[$i]->user_type;
					$data["user_status"] = $rows[$i]->user_status;
					$data["user_lastlogin_date"] = $rows[$i]->user_lastlogin_date;
					$data["user_lastlogin_time"] = $rows[$i]->user_lastlogin_time;
					$data["user_photo"] = $rows[$i]->user_photo;
					$data["user_zipcode"] = $rows[$i]->user_zipcode;
					$data["user_create_date"] = $rows[$i]->user_create_date;
					$data["user_agent"] = $rows[$i]->user_agent;
					$data["user_mail"] = $rows[$i]->user_mail;
					$data["user_agent_admin"] = $rows[$i]->user_agent_admin;
					$data["user_alarm"] = $rows[$i]->user_alarm;
					$data["user_engine"] = $rows[$i]->user_engine;
					$data["user_company"] = $rows[$i]->user_company;
					$data["user_subcompany"] = $rows[$i]->user_subcompany;
					$data["user_group"] = $rows[$i]->user_group;
					$data["user_subgroup"] = $rows[$i]->user_subgroup;
					$data["user_manage_password"] = $rows[$i]->user_manage_password;
					$data["user_sms_notifikasi"] = $rows[$i]->user_sms_notifikasi;
					$data["user_change_profile"] = $rows[$i]->user_change_profile;
					$data["user_payment_type"] = $rows[$i]->user_payment_type;
					$data["user_payment_period"] = $rows[$i]->user_payment_period;
					$data["user_payment_amount"] = $rows[$i]->user_payment_amount;
					$data["user_payment_pulsa"] = $rows[$i]->user_payment_pulsa;
					$data["user_alert_geo_sms"] = $rows[$i]->user_alert_geo_sms;
					$data["user_alert_geo_email"] = $rows[$i]->user_alert_geo_email;
					$data["user_alert_speed_sms"] = $rows[$i]->user_alert_speed_sms;
					$data["user_alert_speed_email"] = $rows[$i]->user_alert_speed_email;
					$data["user_alert_parking_sms"] = $rows[$i]->user_alert_parking_sms;
					$data["user_alert_parking_email"] = $rows[$i]->user_alert_parking_email;
					$data["user_trans_tupper"] = $rows[$i]->user_trans_tupper;
					$data["user_istransporter"] = $rows[$i]->user_istransporter;
					$data["user_sales"] = $rows[$i]->user_sales;
					$data["user_telegroup"] = $rows[$i]->user_telegroup;
					$data["user_level"] = $rows[$i]->user_level;
					$data["user_parent"] = $rows[$i]->user_parent;
					$data["user_dblive"] = $rows[$i]->user_dblive;
					$data["user_app"] = $rows[$i]->user_app;
					$data["user_id_role"] = $rows[$i]->user_id_role;
					$data["user_local_login"] = $rows[$i]->user_local_login;
					$data["user_company_alias"] = $rows[$i]->user_company_alias;
					$data["user_password_default"] = $rows[$i]->user_password_default;
					$data["user_excavator"] = $rows[$i]->user_excavator;
					
				
				$this->dbsecondary = $this->load->database("webtracking_secondary", true);
				$this->dbsecondary->order_by("user_id", "desc");
				$this->dbsecondary->where("user_id", $rows[$i]->user_id);
				$this->dbsecondary->limit(1);
				$q_exists = $this->dbsecondary->get("user");
				$rows_exists = $q_exists->row();
				
			
				if (count($rows_exists)>0)
				{
					printf("UPDATE USER IN DB SECONDARY : %s, %s, %s \r\n", $rows[$i]->user_id, $rows[$i]->user_login, $rows[$i]->user_name);
					
					$this->dbsecondary->where("user_id", $rows[$i]->user_id);
					$this->dbsecondary->limit(1);
					$this->dbsecondary->update("user",$data);
					printf("UPDATE OK \r\n");
					printf("=============================================== \r\n"); 
				}
				//jika tidak ada, insert all field data
				else
				{
					printf("INSERT USER IN DB SECONDARY : %s, %s, %s \r\n", $rows[$i]->user_id, $rows[$i]->user_login, $rows[$i]->user_name);
					
					$this->dbsecondary->insert("user", $data);
					
					printf("INSERT OK \r\n");
					printf("=============================================== \r\n"); 
				}
			
				
				
			
		}
		
		$this->dbprimary->close();
		$this->dbprimary->cache_delete_all();
		
		$this->dbsecondary->close();
		$this->dbsecondary->cache_delete_all();
		
		$finish_time = date("Y-m-d H:i:s");
		printf("FINISH !! %s, \r\n", $finish_time); 
		printf("=============================================== \r\n"); 
		
		$title_name = "SYNC TABLE USER";
			$message = urlencode(
			"".$title_name." \n".
			"Total Rows: ".$total." \n".
			"Start: ".$start_time." \n".
			"Finish: ".$finish_time." \n"
														
		);
		sleep(2);		
		$sendtelegram = $this->telegram_direct("-664183792",$message); // SYNC TELE
		printf("===SENT TELEGRAM OK\r\n");	
		
		return;   
		
	}
	
	function company()
	{
		ini_set('memory_limit', '1G');
		$offset = 0;
		$i = 0;
		$start_time = date("Y-m-d H:i:s");
	
		
		printf("PROSES SYNC COMPANY \r\n");
		$this->dbprimary = $this->load->database("webtracking_primary", TRUE);
		$this->dbprimary->order_by("company_id","desc");
		$q = $this->dbprimary->get("company");
		$rows = $q->result();
		$total = count($rows);
		printf("Total Company : %s \r\n", $total); 
		$j = 0;
		for($i=0;$i<$total; $i++)
		{
			
			printf("PROCESS COMPANY	 : %s \r\n", ++$j." of ".$total);
			printf("GET COMPANY  : %s %s \r\n", $rows[$i]->company_id, $rows[$i]->company_name);
			
					unset($data);
					$data["company_id"] = $rows[$i]->company_id;
					$data["company_name"] = $rows[$i]->company_name;
					$data["company_agent"] = $rows[$i]->company_agent;
					$data["company_created"] = $rows[$i]->company_created;
					$data["company_site"] = $rows[$i]->company_site;
					$data["company_user_parent"] = $rows[$i]->company_user_parent;
					$data["company_created_by"] = $rows[$i]->company_created_by;
					$data["company_status"] = $rows[$i]->company_status;
					$data["company_flag"] = $rows[$i]->company_flag;
					$data["company_site_logout"] = $rows[$i]->company_site_logout;
					$data["company_telegram_sos"] = $rows[$i]->company_telegram_sos;
					$data["company_telegram_parkir"] = $rows[$i]->company_telegram_parkir;
					$data["company_telegram_speed"] = $rows[$i]->company_telegram_speed;
					$data["company_telegram_geofence"] = $rows[$i]->company_telegram_geofence;
					$data["company_telegram_cron"] = $rows[$i]->company_telegram_cron;
					$data["company_exca"] = $rows[$i]->company_exca;
					
				$this->dbsecondary = $this->load->database("webtracking_secondary", true);
				$this->dbsecondary->order_by("company_id", "desc");
				$this->dbsecondary->where("company_id", $rows[$i]->company_id);
				$this->dbsecondary->limit(1);
				$q_exists = $this->dbsecondary->get("company");
				$rows_exists = $q_exists->row();
				
			
				if (count($rows_exists)>0)
				{
					printf("UPDATE USER IN DB SECONDARY : %s, %s \r\n", $rows[$i]->company_id, $rows[$i]->company_name);
					
					$this->dbsecondary->where("company_id", $rows[$i]->company_id);
					$this->dbsecondary->limit(1);
					$this->dbsecondary->update("company",$data);
					printf("UPDATE OK \r\n");
					printf("=============================================== \r\n"); 
				}
				//jika tidak ada, insert all field data
				else
				{
					printf("INSERT USER IN DB SECONDARY : %s, %s \r\n", $rows[$i]->company_id, $rows[$i]->company_name);
					
					$this->dbsecondary->insert("company", $data);
					
					printf("INSERT OK \r\n");
					printf("=============================================== \r\n"); 
				}
			
		}
		
		$this->dbprimary->close();
		$this->dbprimary->cache_delete_all();
		
		$this->dbsecondary->close();
		$this->dbsecondary->cache_delete_all();
		
		$finish_time = date("Y-m-d H:i:s");
		printf("FINISH !! %s, \r\n", $finish_time); 
		printf("=============================================== \r\n"); 
		
		$title_name = "SYNC TABLE COMPANY";
			$message = urlencode(
			"".$title_name." \n".
			"Total Rows: ".$total." \n".
			"Start: ".$start_time." \n".
			"Finish: ".$finish_time." \n"
														
		);
		sleep(2);		
		$sendtelegram = $this->telegram_direct("-664183792",$message); // SYNC TELE
		printf("===SENT TELEGRAM OK\r\n");	
		
		return;   
		
	}
	
	function group()
	{
		ini_set('memory_limit', '1G');
		$offset = 0;
		$i = 0;
		$start_time = date("Y-m-d H:i:s");
	
		
		printf("PROSES SYNC GROUP \r\n");
		$this->dbprimary = $this->load->database("webtracking_primary", TRUE);
		$this->dbprimary->order_by("group_id","desc");
		$q = $this->dbprimary->get("group");
		$rows = $q->result();
		$total = count($rows);
		printf("Total Company : %s \r\n", $total); 
		$j = 0;
		for($i=0;$i<$total; $i++)
		{
			
			printf("PROCESS COMPANY	 : %s \r\n", ++$j." of ".$total);
			printf("GET COMPANY  : %s %s \r\n", $rows[$i]->group_id, $rows[$i]->group_name);
			
					unset($data);
					$data["group_id"] = $rows[$i]->group_id;
					$data["group_name"] = $rows[$i]->group_name;
					$data["group_parent"] = $rows[$i]->group_parent;
					$data["group_created"] = $rows[$i]->group_created;
					$data["group_creator"] = $rows[$i]->group_creator;
					$data["group_company"] = $rows[$i]->group_company;
					$data["group_subcompany"] = $rows[$i]->group_subcompany;
					$data["group_status"] = $rows[$i]->group_status;
					$data["group_flag"] = $rows[$i]->group_flag;
					
					
				$this->dbsecondary = $this->load->database("webtracking_secondary", true);
				$this->dbsecondary->order_by("group_id", "desc");
				$this->dbsecondary->where("group_id", $rows[$i]->group_id);
				$this->dbsecondary->limit(1);
				$q_exists = $this->dbsecondary->get("group");
				$rows_exists = $q_exists->row();
				
			
				if (count($rows_exists)>0)
				{
					printf("UPDATE USER IN DB SECONDARY : %s, %s \r\n", $rows[$i]->group_id, $rows[$i]->group_name);
					
					$this->dbsecondary->where("group_id", $rows[$i]->group_id);
					$this->dbsecondary->limit(1);
					$this->dbsecondary->update("group",$data);
					printf("UPDATE OK \r\n");
					printf("=============================================== \r\n"); 
				}
				//jika tidak ada, insert all field data
				else
				{
					printf("INSERT USER IN DB SECONDARY : %s, %s \r\n", $rows[$i]->group_id, $rows[$i]->group_name);
					
					$this->dbsecondary->insert("group", $data);
					
					printf("INSERT OK \r\n");
					printf("=============================================== \r\n"); 
				}
			
		}
		
		$this->dbprimary->close();
		$this->dbprimary->cache_delete_all();
		
		$this->dbsecondary->close();
		$this->dbsecondary->cache_delete_all();
		
		$finish_time = date("Y-m-d H:i:s");
		printf("FINISH !! %s, \r\n", $finish_time); 
		printf("=============================================== \r\n"); 
		
		$title_name = "SYNC TABLE GROUP";
			$message = urlencode(
			"".$title_name." \n".
			"Total Rows: ".$total." \n".
			"Start: ".$start_time." \n".
			"Finish: ".$finish_time." \n"
														
		);
		sleep(2);		
		$sendtelegram = $this->telegram_direct("-664183792",$message); // SYNC TELE
		printf("===SENT TELEGRAM OK\r\n");	
		
		return;   
		
	}
	
	function apiuser()
	{
		ini_set('memory_limit', '1G');
		$offset = 0;
		$i = 0;
		$start_time = date("Y-m-d H:i:s");
	
		
		printf("PROSES SYNC API USER \r\n");
		$this->dbprimary = $this->load->database("webtracking_primary", TRUE);
		$this->dbprimary->order_by("api_id","desc");
		$q = $this->dbprimary->get("api_user");
		$rows = $q->result();
		$total = count($rows);
		printf("Total API USER: %s \r\n", $total); 
		$j = 0;
		for($i=0;$i<$total; $i++)
		{
			
			printf("PROCESS API USER	 : %s \r\n", ++$j." of ".$total);
			printf("GET API USER  : %s %s \r\n", $rows[$i]->api_id, $rows[$i]->api_user);
			
					unset($data);
					$data["api_id"] = $rows[$i]->api_id;
					$data["api_user"] = $rows[$i]->api_user;
					$data["api_token"] = $rows[$i]->api_token;
					$data["api_googlemap"] = $rows[$i]->api_googlemap;
					$data["api_note"] = $rows[$i]->api_note;
					$data["api_status"] = $rows[$i]->api_status;
					$data["api_flag"] = $rows[$i]->api_flag;
					
				$this->dbsecondary = $this->load->database("webtracking_secondary", true);
				$this->dbsecondary->order_by("api_id", "desc");
				$this->dbsecondary->where("api_id", $rows[$i]->api_id);
				$this->dbsecondary->limit(1);
				$q_exists = $this->dbsecondary->get("api_user");
				$rows_exists = $q_exists->row();
				
			
				if (count($rows_exists)>0)
				{
					printf("UPDATE USER IN DB SECONDARY : %s, %s \r\n",  $rows[$i]->api_id, $rows[$i]->api_user);
					
					$this->dbsecondary->where("api_id", $rows[$i]->api_id);
					$this->dbsecondary->limit(1);
					$this->dbsecondary->update("api_user",$data);
					printf("UPDATE OK \r\n");
					printf("=============================================== \r\n"); 
				}
				//jika tidak ada, insert all field data
				else
				{
					printf("INSERT USER IN DB SECONDARY : %s, %s \r\n",  $rows[$i]->api_id, $rows[$i]->api_user);
					
					$this->dbsecondary->insert("api_user", $data);
					
					printf("INSERT OK \r\n");
					printf("=============================================== \r\n"); 
				}
			
		}
		
		$this->dbprimary->close();
		$this->dbprimary->cache_delete_all();
		
		$this->dbsecondary->close();
		$this->dbsecondary->cache_delete_all();
		
		$finish_time = date("Y-m-d H:i:s");
		printf("FINISH !! %s, \r\n", $finish_time); 
		printf("=============================================== \r\n"); 
		
		$title_name = "SYNC TABLE API USER";
			$message = urlencode(
			"".$title_name." \n".
			"Total Rows: ".$total." \n".
			"Start: ".$start_time." \n".
			"Finish: ".$finish_time." \n"
														
		);
		sleep(2);		
		$sendtelegram = $this->telegram_direct("-664183792",$message); // SYNC TELE
		printf("===SENT TELEGRAM OK\r\n");	
		
		return;   
		
	}
	
	function configport()
	{
		ini_set('memory_limit', '1G');
		$offset = 0;
		$i = 0;
		$start_time = date("Y-m-d H:i:s");
	
		
		printf("PROSES SYNC CONFIG PORT \r\n");
		$this->dbprimary = $this->load->database("webtracking_primary", TRUE);
		$this->dbprimary->order_by("config_id","desc");
		$q = $this->dbprimary->get("config_port");
		$rows = $q->result();
		$total = count($rows);
		printf("Total Config : %s \r\n", $total); 
		$j = 0;
		for($i=0;$i<$total; $i++)
		{
			
			printf("PROCESS CONFIG PORT	 : %s \r\n", ++$j." of ".$total);
			printf("GET CONFIG PORT  : %s %s %s \r\n", $rows[$i]->config_id, $rows[$i]->config_port_value, $rows[$i]->config_protocol_type);
			
					unset($data);
					$data["config_id"] = $rows[$i]->config_id;
					$data["config_port_name"] = $rows[$i]->config_port_name;
					$data["config_protocol"] = $rows[$i]->config_protocol;
					$data["config_protocol_type"] = $rows[$i]->config_protocol_type;
					$data["config_server"] = $rows[$i]->config_server;
					$data["config_device_type"] = $rows[$i]->config_device_type;
					$data["config_port_type"] = $rows[$i]->config_port_type;
					$data["config_port_value"] = $rows[$i]->config_port_value;
					$data["config_stop_service"] = $rows[$i]->config_stop_service;
					$data["config_start_service"] = $rows[$i]->config_start_service;
					$data["config_load_database"] = $rows[$i]->config_load_database;
					$data["config_port_ip"] = $rows[$i]->config_port_ip;
					$data["config_port_ip_local"] = $rows[$i]->config_port_ip_local;
					$data["config_port_database"] = $rows[$i]->config_port_database;
					$data["config_group_database"] = $rows[$i]->config_group_database;
					$data["config_port_table_gps"] = $rows[$i]->config_port_table_gps;
					$data["config_port_table_gps_info"] = $rows[$i]->config_port_table_gps_info;
					$data["config_live_status"] = $rows[$i]->config_live_status;
					$data["config_live_ip"] = $rows[$i]->config_live_ip;
					$data["config_live_ip_local"] = $rows[$i]->config_live_ip_local;
					$data["config_live_database"] = $rows[$i]->config_live_database;
					$data["config_live_table_gps"] = $rows[$i]->config_live_table_gps;
					$data["config_live_table_gps_info"] = $rows[$i]->config_live_table_gps_info;
					$data["config_note"] = $rows[$i]->config_note;
					$data["config_active"] = $rows[$i]->config_active;
					$data["config_flag"] = $rows[$i]->config_flag;
					$data["config_sos_alert"] = $rows[$i]->config_sos_alert;
					$data["config_sos_ip"] = $rows[$i]->config_sos_ip;
					$data["config_sos_ip_local"] = $rows[$i]->config_sos_ip_local;
					$data["config_sos_database"] = $rows[$i]->config_sos_database;
					$data["config_sos_table_gps"] = $rows[$i]->config_sos_table_gps;
					$data["config_sos_table_gps_info"] = $rows[$i]->config_sos_table_gps_info;
					$data["config_cutpower_alert"] = $rows[$i]->config_cutpower_alert;
					$data["config_cutpower_ip"] = $rows[$i]->config_cutpower_ip;
					$data["config_cutpower_ip_local"] = $rows[$i]->config_cutpower_ip_local;
					$data["config_cutpower_database"] = $rows[$i]->config_cutpower_database;
					$data["config_cutpower_table_gps"] = $rows[$i]->config_cutpower_table_gps;
					$data["config_cutpower_table_gps_info"] = $rows[$i]->config_cutpower_table_gps_info;
					
				$this->dbsecondary = $this->load->database("webtracking_secondary", true);
				$this->dbsecondary->order_by("config_id", "desc");
				$this->dbsecondary->where("config_id", $rows[$i]->config_id);
				$this->dbsecondary->limit(1);
				$q_exists = $this->dbsecondary->get("config_port");
				$rows_exists = $q_exists->row();
				
				//jika sudah ada, hanya update port (vehicle info)
				if (count($rows_exists)>0)
				{
					printf("UPDATE VEHICLE IN DB SECONDARY : %s, %s, %s \r\n",$rows[$i]->config_id, $rows[$i]->config_port_value, $rows[$i]->config_protocol_type);
					
					$this->dbsecondary->where("config_id", $rows[$i]->config_id);
					$this->dbsecondary->limit(1);
					$this->dbsecondary->update("config_port",$data);
					printf("UPDATE OK \r\n");
					printf("=============================================== \r\n"); 
				}
				//jika tidak ada, insert all field data
				else
				{
					printf("INSERT VEHICLE IN DB SECONDARY : %s, %s, %s \r\n", $rows[$i]->config_id, $rows[$i]->config_port_value, $rows[$i]->config_protocol_type);
					
					$this->dbsecondary->insert("config_port", $data);
					
					printf("INSERT OK \r\n");
					printf("=============================================== \r\n"); 
				}
				
				
			
		}
		
		$this->dbprimary->close();
		$this->dbprimary->cache_delete_all();
		
		$this->dbsecondary->close();
		$this->dbsecondary->cache_delete_all();
		
		$finish_time = date("Y-m-d H:i:s");
		printf("FINISH !! %s, \r\n", $finish_time); 
		printf("=============================================== \r\n"); 
		
		$title_name = "SYNC TABLE CONFIG PORT";
			$message = urlencode(
			"".$title_name." \n".
			"Total Rows: ".$total." \n".
			"Start: ".$start_time." \n".
			"Finish: ".$finish_time." \n"
														
		);
		sleep(2);		
		$sendtelegram = $this->telegram_direct("-664183792",$message); // SYNC TELE
		printf("===SENT TELEGRAM OK\r\n");	
		
		return;   
		
	}
	
	function cronport()
	{
		ini_set('memory_limit', '1G');
		$offset = 0;
		$i = 0;
		$start_time = date("Y-m-d H:i:s");
	
		
		printf("PROSES SYNC CRON PORT \r\n");
		$this->dbprimary = $this->load->database("webtracking_primary", TRUE);
		$this->dbprimary->order_by("port_id","desc");
		$q = $this->dbprimary->get("cron_port");
		$rows = $q->result();
		$total = count($rows);
		printf("Total Cron Port : %s \r\n", $total); 
		$j = 0;
		for($i=0;$i<$total; $i++)
		{
			
			printf("PROCESS CRON PORT	 : %s \r\n", ++$j." of ".$total);
			printf("GET CRON PORT  : %s %s \r\n", $rows[$i]->port_value, $rows[$i]->port_database);
			
					unset($data);
					$data["port_id"] = $rows[$i]->port_id;
					$data["port_value"] = $rows[$i]->port_value;
					$data["port_database"] = $rows[$i]->port_database;
					$data["port_note"] = $rows[$i]->port_note;
					$data["port_config"] = $rows[$i]->port_config;
					$data["port_db_position"] = $rows[$i]->port_db_position;
					$data["port_cron_position"] = $rows[$i]->port_cron_position;
					$data["port_status"] = $rows[$i]->port_status;
					
				$this->dbsecondary = $this->load->database("webtracking_secondary", true);
				$this->dbsecondary->order_by("port_id", "desc");
				$this->dbsecondary->where("port_id", $rows[$i]->port_id);
				$this->dbsecondary->limit(1);
				$q_exists = $this->dbsecondary->get("cron_port");
				$rows_exists = $q_exists->row();
				
			
				if (count($rows_exists)>0)
				{
					printf("UPDATE USER IN DB SECONDARY : %s, %s \r\n", $rows[$i]->port_value, $rows[$i]->port_database);
					
					$this->dbsecondary->where("port_id", $rows[$i]->port_id);
					$this->dbsecondary->limit(1);
					$this->dbsecondary->update("cron_port",$data);
					printf("UPDATE OK \r\n");
					printf("=============================================== \r\n"); 
				}
				//jika tidak ada, insert all field data
				else
				{
					printf("INSERT USER IN DB SECONDARY : %s, %s \r\n", $rows[$i]->port_value, $rows[$i]->port_database);
					
					$this->dbsecondary->insert("cron_port", $data);
					
					printf("INSERT OK \r\n");
					printf("=============================================== \r\n"); 
				}
			
		}
		
		$this->dbprimary->close();
		$this->dbprimary->cache_delete_all();
		
		$this->dbsecondary->close();
		$this->dbsecondary->cache_delete_all();
		
		$finish_time = date("Y-m-d H:i:s");
		printf("FINISH !! %s, \r\n", $finish_time); 
		printf("=============================================== \r\n"); 
		
		$title_name = "SYNC TABLE CRON PORT";
			$message = urlencode(
			"".$title_name." \n".
			"Total Rows: ".$total." \n".
			"Start: ".$start_time." \n".
			"Finish: ".$finish_time." \n"
														
		);
		sleep(2);		
		$sendtelegram = $this->telegram_direct("-664183792",$message); // SYNC TELE
		printf("===SENT TELEGRAM OK\r\n");	
		
		return;   
		
	}
	
	function config()
	{
		ini_set('memory_limit', '1G');
		$offset = 0;
		$i = 0;
		$start_time = date("Y-m-d H:i:s");
	
		
		printf("PROSES SYNC CONFIG \r\n");
		$this->dbprimary = $this->load->database("webtracking_primary", TRUE);
		$this->dbprimary->order_by("config_id","desc");
		$q = $this->dbprimary->get("config");
		$rows = $q->result();
		$total = count($rows);
		printf("Total Rows : %s \r\n", $total); 
		$j = 0;
		for($i=0;$i<$total; $i++)
		{
			
			printf("PROCESS DATA	 : %s \r\n", ++$j." of ".$total);
			printf("GET DATA  : %s %s \r\n", $rows[$i]->config_id, $rows[$i]->config_name);
			
					unset($data);
					$data["config_id"] = $rows[$i]->config_id;
					$data["config_name"] = $rows[$i]->config_name;
					$data["config_value"] = $rows[$i]->config_value;
					$data["config_lastmodified"] = $rows[$i]->config_lastmodified;
					$data["config_lastmodifier"] = $rows[$i]->config_lastmodifier;
					$data["config_user"] = $rows[$i]->config_user;
					$data["config_status"] = $rows[$i]->config_status;
		
					
				$this->dbsecondary = $this->load->database("webtracking_secondary", true);
				$this->dbsecondary->order_by("config_id", "desc");
				$this->dbsecondary->where("config_id", $rows[$i]->config_id);
				$this->dbsecondary->limit(1);
				$q_exists = $this->dbsecondary->get("config");
				$rows_exists = $q_exists->row();
				
			
				if (count($rows_exists)>0)
				{
					printf("UPDATE USER IN DB SECONDARY : %s, %s \r\n", $rows[$i]->config_id, $rows[$i]->config_name);
					
					$this->dbsecondary->where("config_id", $rows[$i]->config_id);
					$this->dbsecondary->limit(1);
					$this->dbsecondary->update("config",$data);
					printf("UPDATE OK \r\n");
					printf("=============================================== \r\n"); 
				}
				//jika tidak ada, insert all field data
				else
				{
					printf("INSERT USER IN DB SECONDARY : %s, %s \r\n", $rows[$i]->config_id, $rows[$i]->config_name);
					
					$this->dbsecondary->insert("config", $data);
					
					printf("INSERT OK \r\n");
					printf("=============================================== \r\n"); 
				}
			
		}
		
		$this->dbprimary->close();
		$this->dbprimary->cache_delete_all();
		
		$this->dbsecondary->close();
		$this->dbsecondary->cache_delete_all();
		
		$finish_time = date("Y-m-d H:i:s");
		printf("FINISH !! %s, \r\n", $finish_time); 
		printf("=============================================== \r\n"); 
		
		$title_name = "SYNC TABLE CONFIG";
			$message = urlencode(
			"".$title_name." \n".
			"Total Rows: ".$total." \n".
			"Start: ".$start_time." \n".
			"Finish: ".$finish_time." \n"
														
		);
		sleep(2);		
		$sendtelegram = $this->telegram_direct("-664183792",$message); // SYNC TELE
		printf("===SENT TELEGRAM OK\r\n");	
		
		return;   
		
	}
	
	function privilege()
	{
		ini_set('memory_limit', '1G');
		$offset = 0;
		$i = 0;
		$start_time = date("Y-m-d H:i:s");
	
		
		printf("PROSES SYNC PRIVILEGE \r\n");
		$this->dbprimary = $this->load->database("webtracking_primary", TRUE);
		$this->dbprimary->order_by("privilege_id","desc");
		$this->dbprimary->where("privilege_id >",0);
		$q = $this->dbprimary->get("masterdata_privilege");
		$rows = $q->result();
		$total = count($rows);
		printf("Total Rows : %s \r\n", $total); 
		$j = 0;
		for($i=0;$i<$total; $i++)
		{
			
			printf("PROCESS DATA	 : %s \r\n", ++$j." of ".$total);
			printf("GET DATA  : %s %s \r\n", $rows[$i]->privilege_id, $rows[$i]->privilege_name);
			
					unset($data);
					$data["privilege_id"] = $rows[$i]->privilege_id;
					$data["privilege_name"] = $rows[$i]->privilege_name;
					
					
				$this->dbsecondary = $this->load->database("webtracking_secondary", true);
				$this->dbsecondary->order_by("privilege_id", "desc");
				$this->dbsecondary->where("privilege_id", $rows[$i]->privilege_id);
				$this->dbsecondary->limit(1);
				$q_exists = $this->dbsecondary->get("masterdata_privilege");
				$rows_exists = $q_exists->row();
				
			
				if (count($rows_exists)>0)
				{
					printf("UPDATE USER IN DB SECONDARY : %s, %s \r\n", $rows[$i]->privilege_id, $rows[$i]->privilege_name);
					
					$this->dbsecondary->where("privilege_id", $rows[$i]->privilege_id);
					$this->dbsecondary->limit(1);
					$this->dbsecondary->update("masterdata_privilege",$data);
					printf("UPDATE OK \r\n");
					printf("=============================================== \r\n"); 
				}
				//jika tidak ada, insert all field data
				else
				{
					printf("INSERT USER IN DB SECONDARY : %s, %s \r\n", $rows[$i]->privilege_id, $rows[$i]->privilege_name);
					
					$this->dbsecondary->insert("masterdata_privilege", $data);
					
					printf("INSERT OK \r\n");
					printf("=============================================== \r\n"); 
				}
			
		}
		
		$this->dbprimary->close();
		$this->dbprimary->cache_delete_all();
		
		$this->dbsecondary->close();
		$this->dbsecondary->cache_delete_all();
		
		$finish_time = date("Y-m-d H:i:s");
		printf("FINISH !! %s, \r\n", $finish_time); 
		printf("=============================================== \r\n"); 
		
		$title_name = "SYNC TABLE MASTER PRIVILEGE";
			$message = urlencode(
			"".$title_name." \n".
			"Total Rows: ".$total." \n".
			"Start: ".$start_time." \n".
			"Finish: ".$finish_time." \n"
														
		);
		sleep(2);		
		$sendtelegram = $this->telegram_direct("-664183792",$message); // SYNC TELE
		printf("===SENT TELEGRAM OK\r\n");	
		
		return;   
		
	}
	
	function subcompany()
	{
		ini_set('memory_limit', '1G');
		$offset = 0;
		$i = 0;
		$start_time = date("Y-m-d H:i:s");
	
		
		printf("PROSES SYNC SUB COMPANY \r\n");
		$this->dbprimary = $this->load->database("webtracking_primary", TRUE);
		$this->dbprimary->order_by("subcompany_id","desc");
		$q = $this->dbprimary->get("subcompany");
		$rows = $q->result();
		$total = count($rows);
		printf("Total Sub Company : %s \r\n", $total); 
		$j = 0;
		for($i=0;$i<$total; $i++)
		{
			
			printf("PROCESS DATA	 : %s \r\n", ++$j." of ".$total);
			printf("GET DATA  : %s %s \r\n", $rows[$i]->subcompany_id, $rows[$i]->subcompany_name);
			
					unset($data);
					$data["subcompany_id"] = $rows[$i]->subcompany_id;
					$data["subcompany_name"] = $rows[$i]->subcompany_name;
					$data["subcompany_created"] = $rows[$i]->subcompany_created;
					$data["subcompany_creator"] = $rows[$i]->subcompany_creator;
					$data["subcompany_parent"] = $rows[$i]->subcompany_parent;
					$data["subcompany_status"] = $rows[$i]->subcompany_status;
					$data["subcompany_flag"] = $rows[$i]->subcompany_flag;
					
				$this->dbsecondary = $this->load->database("webtracking_secondary", true);
				$this->dbsecondary->order_by("subcompany_id", "desc");
				$this->dbsecondary->where("subcompany_id", $rows[$i]->subcompany_id);
				$this->dbsecondary->limit(1);
				$q_exists = $this->dbsecondary->get("subcompany");
				$rows_exists = $q_exists->row();
				
			
				if (count($rows_exists)>0)
				{
					printf("UPDATE USER IN DB SECONDARY : %s, %s \r\n", $rows[$i]->subcompany_id, $rows[$i]->subcompany_name);
					
					$this->dbsecondary->where("subcompany_id", $rows[$i]->subcompany_id);
					$this->dbsecondary->limit(1);
					$this->dbsecondary->update("subcompany",$data);
					printf("UPDATE OK \r\n");
					printf("=============================================== \r\n"); 
				}
				//jika tidak ada, insert all field data
				else
				{
					printf("INSERT USER IN DB SECONDARY : %s, %s \r\n", $rows[$i]->subcompany_id, $rows[$i]->subcompany_name);
					
					$this->dbsecondary->insert("subcompany", $data);
					
					printf("INSERT OK \r\n");
					printf("=============================================== \r\n"); 
				}
			
		}
		
		$this->dbprimary->close();
		$this->dbprimary->cache_delete_all();
		
		$this->dbsecondary->close();
		$this->dbsecondary->cache_delete_all();
		
		$finish_time = date("Y-m-d H:i:s");
		printf("FINISH !! %s, \r\n", $finish_time); 
		printf("=============================================== \r\n"); 
		
		$title_name = "SYNC TABLE SUBCOMPANY";
			$message = urlencode(
			"".$title_name." \n".
			"Total Rows: ".$total." \n".
			"Start: ".$start_time." \n".
			"Finish: ".$finish_time." \n"
														
		);
		sleep(2);		
		$sendtelegram = $this->telegram_direct("-664183792",$message); // SYNC TELE
		printf("===SENT TELEGRAM OK\r\n");	
		
		return;   
		
	}
	
	function subgroup()
	{
		ini_set('memory_limit', '1G');
		$offset = 0;
		$i = 0;
		$start_time = date("Y-m-d H:i:s");
	
		
		printf("PROSES SYNC SUB GROUP \r\n");
		$this->dbprimary = $this->load->database("webtracking_primary", TRUE);
		$this->dbprimary->order_by("subgroup_id","desc");
		$q = $this->dbprimary->get("subgroup");
		$rows = $q->result();
		$total = count($rows);
		printf("Total Data : %s \r\n", $total); 
		$j = 0;
		for($i=0;$i<$total; $i++)
		{
			
			printf("PROCESS DATA	 : %s \r\n", ++$j." of ".$total);
			printf("GET DATA  : %s %s \r\n", $rows[$i]->subgroup_id, $rows[$i]->subgroup_name);
			
					unset($data);
					$data["subgroup_id"] = $rows[$i]->subgroup_id;
					$data["subgroup_name"] = $rows[$i]->subgroup_name;
					$data["subgroup_created"] = $rows[$i]->subgroup_created;
					$data["subgroup_creator"] = $rows[$i]->subgroup_creator;
					$data["subgroup_company"] = $rows[$i]->subgroup_company;
					$data["subgroup_subcompany"] = $rows[$i]->subgroup_subcompany;
					$data["subgroup_customer"] = $rows[$i]->subgroup_customer;
					$data["subgroup_flag"] = $rows[$i]->subgroup_flag;
					$data["subgroup_status"] = $rows[$i]->subgroup_status;
					
				$this->dbsecondary = $this->load->database("webtracking_secondary", true);
				$this->dbsecondary->order_by("subgroup_id", "desc");
				$this->dbsecondary->where("subgroup_id", $rows[$i]->subgroup_id);
				$this->dbsecondary->limit(1);
				$q_exists = $this->dbsecondary->get("subgroup");
				$rows_exists = $q_exists->row();
				
			
				if (count($rows_exists)>0)
				{
					printf("UPDATE USER IN DB SECONDARY : %s, %s \r\n", $rows[$i]->subgroup_id, $rows[$i]->subgroup_name);
					
					$this->dbsecondary->where("subgroup_id", $rows[$i]->subgroup_id);
					$this->dbsecondary->limit(1);
					$this->dbsecondary->update("subgroup",$data);
					printf("UPDATE OK \r\n");
					printf("=============================================== \r\n"); 
				}
				//jika tidak ada, insert all field data
				else
				{
					printf("INSERT USER IN DB SECONDARY : %s, %s \r\n", $rows[$i]->subgroup_id, $rows[$i]->subgroup_name);
					
					$this->dbsecondary->insert("subgroup", $data);
					
					printf("INSERT OK \r\n");
					printf("=============================================== \r\n"); 
				}
			
		}
		
		$this->dbprimary->close();
		$this->dbprimary->cache_delete_all();
		
		$this->dbsecondary->close();
		$this->dbsecondary->cache_delete_all();
		
		$finish_time = date("Y-m-d H:i:s");
		printf("FINISH !! %s, \r\n", $finish_time); 
		printf("=============================================== \r\n"); 
		
		$title_name = "SYNC TABLE SUBGROUP";
			$message = urlencode(
			"".$title_name." \n".
			"Total Rows: ".$total." \n".
			"Start: ".$start_time." \n".
			"Finish: ".$finish_time." \n"
														
		);
		sleep(2);		
		$sendtelegram = $this->telegram_direct("-664183792",$message); // SYNC TELE
		printf("===SENT TELEGRAM OK\r\n");	
		
		return;   
		
	}
	
	function teknisi()
	{
		ini_set('memory_limit', '1G');
		$offset = 0;
		$i = 0;
		$start_time = date("Y-m-d H:i:s");
	
		
		printf("PROSES SYNC TEKNISI \r\n");
		$this->dbprimary = $this->load->database("webtracking_primary", TRUE);
		$this->dbprimary->order_by("teknisi_id","desc");
		$q = $this->dbprimary->get("teknisi");
		$rows = $q->result();
		$total = count($rows);
		printf("Total Data : %s \r\n", $total); 
		$j = 0;
		for($i=0;$i<$total; $i++)
		{
			
			printf("PROCESS DATA	 : %s \r\n", ++$j." of ".$total);
			printf("GET DATA  : %s %s \r\n", $rows[$i]->teknisi_id, $rows[$i]->teknisi_name);
			
					unset($data);
					$data["teknisi_id"] = $rows[$i]->teknisi_id;
					$data["teknisi_name"] = $rows[$i]->teknisi_name;
					$data["teknisi_mobile"] = $rows[$i]->teknisi_mobile;
					$data["teknisi_status"] = $rows[$i]->teknisi_status;
					
				$this->dbsecondary = $this->load->database("webtracking_secondary", true);
				$this->dbsecondary->order_by("teknisi_id", "desc");
				$this->dbsecondary->where("teknisi_id", $rows[$i]->teknisi_id);
				$this->dbsecondary->limit(1);
				$q_exists = $this->dbsecondary->get("teknisi");
				$rows_exists = $q_exists->row();
				
			
				if (count($rows_exists)>0)
				{
					printf("UPDATE USER IN DB SECONDARY : %s, %s \r\n", $rows[$i]->teknisi_id, $rows[$i]->teknisi_name);
					
					$this->dbsecondary->where("teknisi_id", $rows[$i]->teknisi_id);
					$this->dbsecondary->limit(1);
					$this->dbsecondary->update("teknisi",$data);
					printf("UPDATE OK \r\n");
					printf("=============================================== \r\n"); 
				}
				//jika tidak ada, insert all field data
				else
				{
					printf("INSERT USER IN DB SECONDARY : %s, %s \r\n", $rows[$i]->teknisi_id, $rows[$i]->teknisi_name);
					
					$this->dbsecondary->insert("teknisi", $data);
					
					printf("INSERT OK \r\n");
					printf("=============================================== \r\n"); 
				}
			
		}
		
		$this->dbprimary->close();
		$this->dbprimary->cache_delete_all();
		
		$this->dbsecondary->close();
		$this->dbsecondary->cache_delete_all();
		
		$finish_time = date("Y-m-d H:i:s");
		printf("FINISH !! %s, \r\n", $finish_time); 
		printf("=============================================== \r\n"); 
		
		$title_name = "SYNC TABLE TEKNISI";
			$message = urlencode(
			"".$title_name." \n".
			"Total Rows: ".$total." \n".
			"Start: ".$start_time." \n".
			"Finish: ".$finish_time." \n"
														
		);
		sleep(2);		
		$sendtelegram = $this->telegram_direct("-664183792",$message); // SYNC TELE
		printf("===SENT TELEGRAM OK\r\n");	
		
		return;   
		
	}
	
	function sales()
	{
		ini_set('memory_limit', '1G');
		$offset = 0;
		$i = 0;
		$start_time = date("Y-m-d H:i:s");
	
		
		printf("PROSES SYNC SALES \r\n");
		$this->dbprimary = $this->load->database("webtracking_primary", TRUE);
		$this->dbprimary->order_by("sales_id","desc");
		$q = $this->dbprimary->get("sales");
		$rows = $q->result();
		$total = count($rows);
		printf("Total Data : %s \r\n", $total); 
		$j = 0;
		for($i=0;$i<$total; $i++)
		{
			
			printf("PROCESS DATA	 : %s \r\n", ++$j." of ".$total);
			printf("GET DATA  : %s %s \r\n", $rows[$i]->sales_id, $rows[$i]->sales_name);
			
					unset($data);
					$data["sales_id"] = $rows[$i]->sales_id;
					$data["sales_name"] = $rows[$i]->sales_name;
					$data["sales_mobile"] = $rows[$i]->sales_mobile;
					$data["sales_agent"] = $rows[$i]->sales_agent;
					$data["sales_status"] = $rows[$i]->sales_status;
					$data["sales_flag"] = $rows[$i]->sales_flag;
					
				$this->dbsecondary = $this->load->database("webtracking_secondary", true);
				$this->dbsecondary->order_by("sales_id", "desc");
				$this->dbsecondary->where("sales_id", $rows[$i]->sales_id);
				$this->dbsecondary->limit(1);
				$q_exists = $this->dbsecondary->get("sales");
				$rows_exists = $q_exists->row();
				
			
				if (count($rows_exists)>0)
				{
					printf("UPDATE USER IN DB SECONDARY : %s, %s \r\n", $rows[$i]->sales_id, $rows[$i]->sales_name);
					
					$this->dbsecondary->where("sales_id", $rows[$i]->sales_id);
					$this->dbsecondary->limit(1);
					$this->dbsecondary->update("sales",$data);
					printf("UPDATE OK \r\n");
					printf("=============================================== \r\n"); 
				}
				//jika tidak ada, insert all field data
				else
				{
					printf("INSERT USER IN DB SECONDARY : %s, %s \r\n", $rows[$i]->sales_id, $rows[$i]->sales_name);
					
					$this->dbsecondary->insert("sales", $data);
					
					printf("INSERT OK \r\n");
					printf("=============================================== \r\n"); 
				}
			
		}
		
		$this->dbprimary->close();
		$this->dbprimary->cache_delete_all();
		
		$this->dbsecondary->close();
		$this->dbsecondary->cache_delete_all();
		
		$finish_time = date("Y-m-d H:i:s");
		printf("FINISH !! %s, \r\n", $finish_time); 
		printf("=============================================== \r\n"); 
		
		$title_name = "SYNC TABLE SALES";
			$message = urlencode(
			"".$title_name." \n".
			"Total Rows: ".$total." \n".
			"Start: ".$start_time." \n".
			"Finish: ".$finish_time." \n"
														
		);
		sleep(2);		
		$sendtelegram = $this->telegram_direct("-664183792",$message); // SYNC TELE
		printf("===SENT TELEGRAM OK\r\n");	
		
		return;   
		
	}
	
	function telegram_direct($groupid,$message)
    {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        
        //$url = "http://lacak-mobil.com/telegram/telegram_directpost";
		//$url = "http://admintib.buddiyanto.my.id/telegram/telegram_directpost";
		//$url = "http://admintib.pilartech.co.id/telegram/telegram_directpost";
		//$url = "http://admin.abditrack.com/telegram/telegram_directpost";
		$url = $this->config->item('url_send_telegram');
	   
        $data = array("id" => $groupid, "message" => $message);
        $data_string = json_encode($data);
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POST, true);                                                           
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                 
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);   
		curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);	//new
		
		
		/* curl_setopt($ch, CURLOPT_HTTPGET, 1);
		curl_setopt($ch, CURLOPT_URL, 'http://admintib.pilartech.co.id/' );
		curl_setopt($ch, CURLOPT_DNS_USE_GLOBAL_CACHE, false );
		curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 2 ); */

		//var_dump(curl_exec($ch));
		//var_dump(curl_getinfo($ch));
		//var_dump(curl_error($ch));
		
		/* curl_setopt($ch, CURLOPT_HTTPGET, 1);
		curl_setopt($ch, CURLOPT_URL, 'http://admintib.buddiyanto.my.id/' );
		curl_setopt($ch, CURLOPT_DNS_USE_GLOBAL_CACHE, false );
		curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 2 ); */
		
		//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,true);
		//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
		//curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
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
