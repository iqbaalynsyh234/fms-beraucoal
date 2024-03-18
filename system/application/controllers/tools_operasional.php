<?php
include "base.php";

class Tools_operasional extends Base {

	function Tools_operasional(){ 
		parent::Base();
		$this->load->helper("common");
		/*$this->load->model("gpsmodel");
		$this->load->model("smsmodel");
		$this->load->model("agenmodel");*/
		$this->load->model("crud_model_operasional");
	}

	function sinkronisasi_transaksi_barang_masuk()
	{
		$start_time = date("d-m-Y H:i:s");
		
		printf("PROSES SYNC TRANSAKSI BARANG MASUK \r\n"); 
		$this->dbopr = $this->load->database("iserp", true);
		$this->dbopr->order_by("transaksi_barang_masuk_id","desc");
		$q = $this->dbopr->get("transaksi_barang_masuk");
		$rows = $q->result();
		$total = count($rows);
		printf("GET TRANSAKSI BARANG MASUK: %s \r\n", $total); 
		$i = 0;
		foreach($rows as $row)
		{
			$i++;
			printf("PROSES SYNC: %s of %s \r\n", $i, $total); 
			unset($data);
			
			$data["transaksi_barang_keluar_id"] = $row->transaksi_barang_masuk_id;
			$data["transaksi_barang_keluar_po"] = $row->transaksi_barang_masuk_po;
			$data["transaksi_barang_keluar_vendor"] = $row->transaksi_barang_masuk_vendor;
			$data["transaksi_barang_keluar_barang"] = $row->transaksi_barang_masuk_barang;
			$data["transaksi_barang_keluar_barang_category"] = $row->transaksi_barang_masuk_barang_category;
			$data["transaksi_barang_keluar_tipe"] = $row->transaksi_barang_masuk_tipe;
			$data["transaksi_barang_keluar_qty"] = $row->transaksi_barang_masuk_qty;
			$data["transaksi_barang_keluar_qty_backup"] = $row->transaksi_barang_masuk_qty_backup;
			$data["transaksi_barang_keluar_date"] = $row->transaksi_barang_masuk_date;
			$data["transaksi_barang_keluar_pic"] = $row->transaksi_barang_masuk_pic;
			$data["transaksi_barang_keluar_sync"] = 1; //aktif yang dihitung sebagai barang masuk
			$data["transaksi_barang_keluar_modified"] = $row->transaksi_barang_masuk_modified;
			$data["transaksi_barang_keluar_modified_user"] = $row->transaksi_barang_masuk_modified_user;
			$data["transaksi_barang_keluar_status"] = $row->transaksi_barang_masuk_status;
			$data["transaksi_barang_keluar_islock"] = $row->transaksi_barang_masuk_islock;
			
			//TABLE TUJUAN
			$this->dbopr->select("transaksi_barang_keluar_id");
			$this->dbopr->where("transaksi_barang_keluar_id", $row->transaksi_barang_masuk_id);
			$qu = $this->dbopr->get("transaksi_barang_keluar_sync");
			$ru = $qu->row();
			if (count($ru)>0)
			{
				printf("UPDATE TABLE TRANSAKSI BARANG KELUAR SYNC : %s \r\n", $row->transaksi_barang_masuk_id); 
				$this->dbopr->where("transaksi_barang_keluar_id", $row->transaksi_barang_masuk_id);	
				$this->dbopr->update("transaksi_barang_keluar_sync",$data);
			}
			else
			{
				printf("INSERT TABLE TRANSAKSI BARANG KELUAR SYNC : %s \r\n", $row->transaksi_barang_masuk_id); 
				$this->dbopr->insert("transaksi_barang_keluar_sync",$data);
			}
			printf("FINISH SYNC TABLE TRANSAKSI BARANG KELUAR : %s \r\n", $row->transaksi_barang_masuk_id); 
			printf("=============================================== \r\n"); 
			
		}
		$finish_time = date("d-m-Y H:i:s");
		printf("DONE"." ".$finish_time);
		printf("=============================================== \r\n");
		
		//Send Email
		/*$cron_name = "Sync Transaksi Barang Masuk";
		
		unset($mail);
		$mail['subject'] =  "[Operasional]"." ".$cron_name;
		$mail['message'] = 
"
Cron Report Status :

Nama Cron  : ".$cron_name."
Start Cron : ".$start_time."
End Cron   : ".$finish_time."
Status     : Finish

Terima Kasih

";
		$mail['dest'] = "budiyanto@lacak-mobil.com,alfa@lacak-mobil.com";
		$mail['bcc'] = "";
		$mail['sender'] = "noreply-opr@lacak-mobil.com";
		lacakmobilmail($mail);
			
		printf("Send Email OK");*/
		printf("=============================================== \r\n");
		
	}
	
	//sinkronisasi all OPR
	function sync_opr()
	{
		$this->sinkronisasi_transaksi_barang_masuk();
		
	}
	
	//inventory for find stok awal bulan
	function inventory_first_stock($now="")
	{
		$start_time = date("d-m-Y H:i:s");
		if($now == ""){
			$now = date("Y-m-d");
		}
		
		$nowtime = date("Y-m-d", (strtotime($now)));
		
		//bulan lalu
		
		$startdatelalu = date('Y-m-d', strtotime('-1 month', strtotime($nowtime)));
		$bulanlalu = date('m', strtotime($startdatelalu));
		$tahunlalu = date('Y', strtotime($startdatelalu));
		$bulanlalu_view = date('M', strtotime($startdatelalu));
		$periodelalu = $tahunlalu."-".$bulanlalu."-"."01";
		
		printf("===STARTING CREATE STOK AWAL BULAN at %s \r\n", $start_time); 
		printf("===TANGGAL YANG DI PILIH : %s \r\n", $nowtime); 
		printf("===TANGGAL YANG LALU : %s \r\n", $periodelalu); 
		printf("===BULAN YANG DI PILIH : %s \r\n", date('M', strtotime($nowtime))); 
		printf("===BULAN LALU : %s \r\n", $bulanlalu_view); 
		
		$this->dbopr = $this->load->database("iserp", true);
		$this->dbopr->order_by("product_id","asc");
		$this->dbopr->where("product_status",1);
		$q = $this->dbopr->get("product");
		$rows = $q->result();
		$total = count($rows);
		
		printf("===TOTAL PRODUCT: %s \r\n", $total); 
		$i = 0;
		foreach($rows as $row)
		{
			$i++;
			printf("===PROSES CEK STOK %s : %s of %s \r\n", $row->product_name, $i, $total); 
			
			//Get inventory bulan lalu
			$this->dbopr->order_by("inventory_product","asc");
			$this->dbopr->where("inventory_status",1);
			$this->dbopr->where("inventory_month",$bulanlalu);
			$this->dbopr->where("inventory_year",$tahunlalu);
			$this->dbopr->where("inventory_product",$row->product_id);
			$q_inv_old = $this->dbopr->get("inventory");
			$data_inv_old = $q_inv_old->row();
			
			printf("===CEK STOK BULAN : %s  %s \r\n", $bulanlalu_view, $tahunlalu); 
									$totalbarangkeluar = 0;
									$totalbarangmasuk = 0;
									$totalstock = 0;
									
									
										$barangkeluar_table = "transaksi_barang_keluar";
										$barangkeluar_status = "transaksi_barang_keluar_status";
										$barangkeluar_field = "transaksi_barang_keluar_barang";
										$barangkeluar_idbarang	= $data_inv_old->inventory_product;
										$barangkeluar_qty	= "transaksi_barang_keluar_qty";
										$barangkeluar_qtybackup	= "transaksi_barang_keluar_qty_backup";
										
										$barangkeluar_date	= "transaksi_barang_keluar_date";
										$barangkeluar_filterdate_start = $data_inv_old->inventory_lastupdated;
										$barangkeluar_filterdate_end = date("Y-m-t", strtotime($data_inv_old->inventory_lastupdated));
										
										//barang masuk
										$barangmasuk_table = "transaksi_barang_masuk";
										$barangmasuk_status = "transaksi_barang_masuk_status";
										$barangmasuk_field = "transaksi_barang_masuk_barang";
										$barangmasuk_idbarang = $data_inv_old->inventory_product;
										$barangmasuk_qty = "transaksi_barang_masuk_qty";
										$barangmasuk_qtybackup = "transaksi_barang_masuk_qty_backup";
										
										$barangmasuk_date	= "transaksi_barang_masuk_date";
										$barangmasuk_filterdate_start = $data_inv_old->inventory_lastupdated;
										$barangmasuk_filterdate_end = date("Y-m-t", strtotime($data_inv_old->inventory_lastupdated));
										
										$totalbarangkeluar = $this->crud_model_operasional->countdata_bycode_date(
																				$barangkeluar_table,$barangkeluar_status,$barangkeluar_field,$barangkeluar_idbarang,
																				$barangkeluar_qty,$barangkeluar_qtybackup,$barangkeluar_date,
																				$barangkeluar_filterdate_start,$barangkeluar_filterdate_end
																				);
										
										$totalbarangmasuk = $this->crud_model_operasional->countdata_bycode_date(
																				$barangmasuk_table,$barangmasuk_status,$barangmasuk_field,$barangmasuk_idbarang,
																				$barangmasuk_qty,$barangmasuk_qtybackup,$barangmasuk_date,
																				$barangmasuk_filterdate_start,$barangmasuk_filterdate_end
																				);
																				
										$master_stock_lalu = $data_inv_old->inventory_total;
										$totalstock = ($master_stock_lalu + $totalbarangmasuk) - $totalbarangkeluar;
										
									
			printf("===SISA STOK AKHIR BULAN : %s\r\n", $totalstock); 
			
			if($now == ""){
				$bulanini = date('m');
				$tahunini = date('Y');
			}else{
				$bulanini = date('m', strtotime($now));
				$tahunini = date('Y', strtotime($now));
			}
			
			//bulan ini
			$startdate = $tahunini."-".$bulanini."-"."01";
			$periode = date("Y-m-d", strtotime($startdate));
			$bulanini_view = date('M', strtotime($startdate));
			
			//Get inventory bulan ini
			$this->dbopr->order_by("inventory_product","asc");
			$this->dbopr->where("inventory_status",1);
			$this->dbopr->where("inventory_month",$bulanini);
			$this->dbopr->where("inventory_year",$tahunini);
			$this->dbopr->where("inventory_product",$row->product_id);
			$q_inv_new = $this->dbopr->get("inventory");
			$data_inv_new = $q_inv_new->row();
			
			unset($data);
			$data["inventory_product"] = $row->product_id;
			$data["inventory_total"] = $totalstock;
			
			//jika sudah ada data di update.
			if(count($data_inv_new)>0){
				printf("===CREATE STOK AWAL BULAN INI : %s \r\n", $bulanini_view); 
				
				//cek all stok awal, jika ada update
				printf("===ADA DATANYA %s STOK AWAL : %s \r\n", $data_inv_new->inventory_lastupdated, $data_inv_new->inventory_total); 
				if($data_inv_new->inventory_total == $totalstock){
					printf("===STOK AWAL BULAN INI SUDAH SYNC \r\n"); 
					
				}else{
					printf("===NO DATA, PROSES UPDATED STOK AWAL !! \r\n"); 
					$this->dbopr->where("inventory_product", $row->product_id);
					$this->dbopr->where("inventory_month", $bulanini);
					$this->dbopr->where("inventory_year", $tahunini);
					$this->dbopr->where("inventory_status", 1);
					$this->dbopr->update("inventory",$data);
					printf("UPDATE TABLE INVENTORY : %s \r\n", $row->product_id);
				}
			}
				//jika tidak ada data insert
			else{ 
				printf("===NO DATA, PROCESS INSERT \r\n"); 
				
				$data["inventory_month"] = $bulanini;
				$data["inventory_year"] = $tahunini;
				$data["inventory_lastupdated"] = date('Y-m-d', strtotime($periode));
				$data["inventory_modified"] = date('Y-m-d H:i:s');
				$data["inventory_modified_user"] = 0;
				$data["inventory_status"] = 1;
				$this->dbopr->insert("inventory",$data);
				printf("INSERT NEW INVENTORY : %s BULAN %s \r\n", $row->product_id, $bulanini, $tahunini);
			}
			
			printf("=============================================== \r\n");
			
		}
		
		$finish_time = date("d-m-Y H:i:s");
		printf("===FINISH at %s \r\n", $finish_time);
		printf("=============================================== \r\n");
		
		//Send Email
		/*$cron_name = "Sync Transaksi Barang Masuk";
		
		unset($mail);
		$mail['subject'] =  "[Operasional]"." ".$cron_name;
		$mail['message'] = 
"
Cron Report Status :

Nama Cron  : ".$cron_name."
Start Cron : ".$start_time."
End Cron   : ".$finish_time."
Status     : Finish

Terima Kasih

";
		$mail['dest'] = "budiyanto@lacak-mobil.com,alfa@lacak-mobil.com";
		$mail['bcc'] = "";
		$mail['sender'] = "noreply-opr@lacak-mobil.com";
		lacakmobilmail($mail);
			
		printf("Send Email OK");*/
		printf("=============================================== \r\n");
		
	}
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
