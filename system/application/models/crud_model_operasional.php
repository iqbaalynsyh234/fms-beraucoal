<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Crud_model_operasional extends Model {
    function Crud_model_operasional () { parent::Model(); }	
    function checklogin($u, $p)
    {
        $this->dbopr = $this->load->database("default",true);
        $this->dbopr->where("user_login",$u);
        $this->dbopr->where("((user_password = PASSWORD('".mysql_real_escape_string($p)."')))", NULL, FALSE);
        $this->dbopr->where("user_status",$this->config->item("active_status"));
		$this->dbopr->where("user_shop",0);
        $q = $this->dbopr->get("user");
        $row = $q->row();
        return $row;
    }
    
    function selectdata($table,$fieldstatus,$sdate="",$edate="")    
    {
        switch ($table)
        {
            case "user":
                
            break;
        }
        $this->dbopr->where($fieldstatus,$this->config->item("active_status"));
        $q = $this->dbopr->get($table);
        $rows = $q->result();
        return $rows;
    }
	
	function selectdata_byorder($table,$fieldstatus,$fieldorderby,$orderby,$sdate="",$edate="")
    {
        switch ($table)
        {
            case "user":
                
            break;
        }
		$this->dbopr->order_by($fieldorderby,$orderby);
        $this->dbopr->where($fieldstatus,$this->config->item("active_status"));
        $q = $this->dbopr->get($table);
        $rows = $q->result();
        return $rows;
    }

	function selectdata_bycode($table,$fieldstatus,$fieldtype,$usertype,$sdate="",$edate="")    
    {
		switch ($table)
        {
            case "user":
                
            break;
        }
		$this->dbopr->where($fieldtype,$usertype);
        $this->dbopr->where($fieldstatus,$this->config->item("active_status"));
        $q = $this->dbopr->get($table);
        $rows = $q->result();
        return $rows;
    }
	
	function selectdata_bycode_order($table,$fieldstatus,$fieldtype,$usertype,$fieldorder,$orderby,$sdate="",$edate="")    
    {
		switch ($table)
        {
            case "user":
                
            break;
        }
		$this->dbopr->order_by($fieldorder,$orderby);
		$this->dbopr->where($fieldtype,$usertype);
        $this->dbopr->where($fieldstatus,$this->config->item("active_status"));
        $q = $this->dbopr->get($table);
        $rows = $q->result();
        return $rows;
    }
    
    //hitung jumlah barang
	//hitung jumlah barang
    function countdata($table,$fieldstatus,$sdate="",$edate="")
    {
    	switch ($table)
    	{
    		case "user":
    
    			break;
    	}
    
    	$totaldata = 0;
  
    	$this->dbopr->where($fieldstatus,$this->config->item("active_status"));
    	$q = $this->dbopr->get($table);
    	$rows = $q->result();
    	$totaldata = count($rows);
		
    	return $totaldata;
    
    }
	
    function countdata_bycode($table,$fieldstatus,$fieldcode,$productcode,$fieldqty,$fieldqtybackup,$sdate="",$edate="")
    {
    	switch ($table)
    	{
    		case "user":
    
    			break;
    	}
    	
    	$grandtotal = 0;
    	$total = 0;
    	$totalbackup = 0;
    	 
    	$this->dbopr->where($fieldcode,$productcode);
    	$this->dbopr->where($fieldstatus,$this->config->item("active_status"));
    	$q = $this->dbopr->get($table);
    	$rows = $q->result();
    	
    	if(isset($rows)&&count($rows)>0)
    	{
    		
    		for($i=0;$i<count($rows);$i++) {
    			$total = $total + $rows[$i]->$fieldqty;
    			$totalbackup = $totalbackup + $rows[$i]->$fieldqtybackup;

    		}
    		
    		$grandtotal = $total + $totalbackup;
    		
    	}
    	
    	return $grandtotal;
    	
    }
	//hanya ini yang dipakai
	function countdata_bycode_date($table,$fieldstatus,$fieldcode,$productcode,$fieldqty,$fieldqtybackup,$fielddate,$sdate,$edate)
    {
		$this->dbopr = $this->load->database("iserp", true);
    	switch ($table)
    	{
    		case "user":
    
    			break;
    	}
    	
    	$grandtotal = 0;
    	$total = 0;
    	$totalbackup = 0;
    	 
    	$this->dbopr->where($fieldcode,$productcode);
    	$this->dbopr->where($fieldstatus,1);
		$this->dbopr->where($fielddate." ".">=",$sdate);
		$this->dbopr->where($fielddate." "."<=",$edate);
    	$q = $this->dbopr->get($table);
    	$rows = $q->result();
    	
    	if(isset($rows)&&count($rows)>0)
    	{
    		
    		for($i=0;$i<count($rows);$i++) {
    			$total = $total + $rows[$i]->$fieldqty;
    			$totalbackup = $totalbackup + $rows[$i]->$fieldqtybackup;

    		}
    		
    		$grandtotal = $total + $totalbackup;
    		
    	}
    	
    	return $grandtotal;
    	
    }
    function selectdata_bydate($table,$fieldstatus,$fieldstartdate,$fieldenddate,$sdate,$edate)    
    {
        switch ($table)
        {
            case "user":
                
            break;
        }
		$this->dbopr->where($fieldstartdate." ".">=",$sdate);
		$this->dbopr->where($fieldenddate." "."<=",$edate);
        $this->dbopr->where($fieldstatus,$this->config->item("active_status"));
        $q = $this->dbopr->get($table);
        $rows = $q->result();
        return $rows;
    }
	
    function selectdata_byid($table,$fid,$fieldkey)    
    {
        switch ($table)
        {
            case "user":
            
            break;
        }
        
        $this->dbopr->where($fieldkey,$fid);
        $q = $this->dbopr->get($table);
        $row = $q->row();
        return $row;
		
    }
	
	 function selectdata_byid_active($table,$fid,$fieldkey,$fieldstatus)    
    {
        switch ($table)
        {
            case "user":
            
            break;
        }
        
        $this->dbopr->where($fieldkey,$fid);
		$this->dbopr->where($fieldstatus,$this->config->item('active_status'));
        $q = $this->dbopr->get($table);
        $row = $q->row();
        return $row;
		
    }
	
	function selectdata_byid_transaksi($table,$fid,$fieldkey,$fieldstatus)    
    {
        switch ($table)
        {
            case "user":
            
            break;
        }
        
        $this->dbopr->where($fieldkey,$fid);
		$this->dbopr->where($fieldstatus,$this->config->item("active_status"));
        $q = $this->dbopr->get($table);
        $row = $q->row();
        return $row;
		
    }
    
    function selectdata_byid_po($table,$fieldstatus,$fieldkey,$fid)
    {
    	switch ($table)
    	{
    		case "user":
    
    			break;
    	}
    
    	$this->dbopr->where($fieldkey,$fid);
    	$this->dbopr->where($fieldstatus,$this->config->item("active_status"));
    	$q = $this->dbopr->get($table);
    	$row = $q->row();
    	return $row;
    
    }
	
	//khusus Select PO
	function selectdatapo_byapprove($table,$fieldstatus,$fieldapprove,$approvesstatus,$sdate="",$edate="")    
    {
        switch ($table)
        {
            case "user":
                
            break;
        }
		//khusus select PO berdasarkan yg sudah di approve
		$this->dbopr->select("transaksi_barang_keluar_id,transaksi_barang_keluar_po,transaksi_barang_keluar_harga_jual,transaksi_barang_keluar_qty,customer_name");
		$this->dbopr->order_by("transaksi_barang_keluar_po","asc");
		$this->dbopr->group_by("transaksi_barang_keluar_po");
		$this->dbopr->where($fieldapprove,$approvesstatus);
        $this->dbopr->where($fieldstatus,$this->config->item("active_status"));
		$this->dbopr->join("customer", "customer_id = transaksi_barang_keluar_customer", "left");
        $q = $this->dbopr->get($table);
        $rows = $q->result();
        return $rows;
    }
	
    function updatedata($table,$data,$id,$fieldid)
    {
		$status = false;
		$this->dbopr->where($fieldid,$id);
		if($this->dbopr->update($table,$data))
		{
			$status = true;
		}
		return $status;
	}
	
	function updatedata_byactive($table,$data,$id,$fieldid,$fieldstatus)
    {
		$status = false;
		$this->dbopr->where($fieldid,$id);
		$this->dbopr->where($fieldstatus,$this->config->item('active_status'));
		if($this->dbopr->update($table,$data))
		{
			$status = true;
		}
		return $status;
	}
	
	function deletedata($table,$fid,$id,$data)
	{
		$status = false;
		$this->dbopr->where($fid,$id);
		if($this->dbopr->update($table,$data))
		{
			$status = true;
		}
		return $status;
	}
	
	function deletedata_byactive($table,$fid,$id,$data,$fieldstatus)
	{
		$status = false;
		$this->dbopr->where($fid,$id);
		$this->dbopr->where($fieldstatus,$this->config->item('active_status'));
		if($this->dbopr->update($table,$data))
		{
			$status = true;
		}
		return $status;
	}
    
    function check_duplicate($table,$fieldvalue,$value,$fieldstatus)
    {
        $status = false;
        
        $this->dbopr->where($fieldvalue,$value);
        $this->dbopr->where($fieldstatus,$this->config->item('active_status'));
        $q = $this->dbopr->get($table);
        $row = $q->row();
        $total = count($row);
        if($total > 0)
        {
            $status = true;
        }
        return $status;
    }
	
	function check_duplicate2($table,$fieldvalue,$value,$fieldvalue2,$value2,$fieldstatus)
    {
        $status = false;
        
        $this->dbopr->where($fieldvalue,$value);
		$this->dbopr->where($fieldvalue2,$value2);
        $this->dbopr->where($fieldstatus,$this->config->item('active_status'));
        $q = $this->dbopr->get($table);
        $row = $q->row();
        $total = count($row);
        if($total > 0)
        {
            $status = true;
        }
        return $status;
    }
    
    function check_duplicate_barangkeluar($table,$fieldvalue,$value,$fieldstatus,$fieldcustomer,$customervalue)
    {
    	$status = false;
    
    	$this->dbopr->where($fieldvalue,$value);
    	$this->dbopr->where($fieldcustomer,$customervalue);
    	$this->dbopr->where($fieldstatus,$this->config->item('active_status'));
    	$q = $this->dbopr->get($table);
    	$row = $q->row();
    	$total = count($row);
    	if($total == 0)
    	{
    		$status = true;
    	}
    	return $status;
    }
    
    function insertdata($table,$data)
    {
        $status = false;
        if($this->dbopr->insert($table,$data))
        {
            if($table == "user")
            {
                $userid = $this->dbopr->insert_id();
				$sql = "UPDATE"." ".$this->dbopr->dboprprefix."user SET user_password = PASSWORD('".mysql_real_escape_string($data['user_password'])."') WHERE user_id = '".$userid."'";
				$this->dbopr->query($sql);
				$this->dbopr->cache_delete_all();
            }
            $status = true;
        }
        return $status;
    }
  	
  	function sendsms($data)
  	{
		$this->dboprsms = $this->load->database("smscolo", true);
		$this->dboprsms->insert('inbox', $data);
	}
	//==crud update barang keluar==//
	function insertbarang_keluar($tableinventory,$transaksi_barang_keluar_barang,$transaksi_barang_keluar_qty,$transaksi_barang_keluar_qty_backup,$fieldinventoryproduct,$fieldinventorystatus)
	{
	
        $status = false;
		$jumlahbarang_keluar_bf = 0;
		$jumlahbarang_stock = 0;
		$jumlahbarang_keluar = 0;
		$totalstock = 0;
		$barang_inventory = 0;
		
		$transaksi_barang_keluar_qty = str_replace(".","",$transaksi_barang_keluar_qty);
		
		$this->dbopr->where($fieldinventoryproduct,$transaksi_barang_keluar_barang);
        $this->dbopr->where($fieldinventorystatus,$this->config->item("active_status"));
        $q = $this->dbopr->get($tableinventory);
        $row = $q->row();
		if(count($row) > 0){
			$barang_inventory = $row->inventory_product;	//id product
			$totalstock = $row->inventory_total;			//stok product
			
			$jumlahbarang_stock = $totalstock;
			$jumlahbarang_keluar = $transaksi_barang_keluar_qty + $transaksi_barang_keluar_qty_backup ;
			$updatebarang_stock = $jumlahbarang_stock - $jumlahbarang_keluar;
			
			if(isset($updatebarang_stock) && ($updatebarang_stock >= 0)){
				unset($datainventory);
				$datainventory["inventory_total"] = $updatebarang_stock;
				$this->dbopr->where("inventory_product",$barang_inventory);
				$this->dbopr->where($fieldinventorystatus,$this->config->item("active_status"));
				if($this->dbopr->update($tableinventory,$datainventory))
				{
					$status = true;
				}
			}else{
				$status = false;
			}
		}else{
			$status = false;
		}
		
        return $status;
    }
	
	function updatebarang_keluar($table,$transaksi_barang_keluar_id,$transaksi_barang_keluar_barang,$transaksi_barang_keluar_qty,$transaksi_barang_keluar_qty_backup,$fieldid,$fieldstatus,
							    $tableinventory,$fieldinventoryproduct,$fieldinventorystatus)
	{
        $status = false;
		$jumlahbarang_keluar_bf = 0;
		$jumlahbarang_stock = 0;
		$jumlahbarang_keluar = 0;
		$totalstock = 0;
		$barang_inventory = 0;
		
		$transaksi_barang_keluar_qty = str_replace(".","",$transaksi_barang_keluar_qty);
		$transaksi_barang_keluar_qty_backup = str_replace(".","",$transaksi_barang_keluar_qty_backup);
		
		$this->dbopr->where($fieldid,$transaksi_barang_keluar_id);
        $this->dbopr->where($fieldstatus,$this->config->item("active_status"));
        $q_bf = $this->dbopr->get($table);
        $row_bf = $q_bf->row();
		$jumlahbarang_keluar_bf = ($row_bf->transaksi_barang_keluar_qty+$row_bf->transaksi_barang_keluar_qty_backup);
		
		$this->dbopr->where($fieldinventoryproduct,$transaksi_barang_keluar_barang);
        $this->dbopr->where($fieldinventorystatus,$this->config->item("active_status"));
        $q = $this->dbopr->get($tableinventory);
        $row = $q->row();
		if(count($row) > 0){
			$barang_inventory = $row->inventory_product;	//id product
			$totalstock = $row->inventory_total;			//stok product
			
			//sebelum dikurang, jumlah stock ditambahkan dengan jumlah barang keluar sebelumnya (restore stock)
			$jumlahbarang_stock = $totalstock + $jumlahbarang_keluar_bf;
			
			$jumlahbarang_keluar = $transaksi_barang_keluar_qty + $transaksi_barang_keluar_qty_backup;
			
			//jumlah stock yg sudah di restore di kurang stock edit
			$updatebarang_stock = $jumlahbarang_stock - $jumlahbarang_keluar;
			//print_r($updatebarang_stock." ".$jumlahbarang_stock." - ".$jumlahbarang_keluar);exit();
			if(isset($updatebarang_stock) && ($updatebarang_stock >= 0)){
				unset($datainventory);
				$datainventory["inventory_total"] = $updatebarang_stock;
				$this->dbopr->where("inventory_product",$barang_inventory);
				$this->dbopr->where($fieldinventorystatus,$this->config->item("active_status"));
				if($this->dbopr->update($tableinventory,$datainventory))
				{
					$status = true;
				}
			}else{
				$status = false;
			}
		}else{
			$status = false;
		}
		
        return $status;
    }
	
	function deletebarang_keluar($tableinventory,$table,$fieldid,$fieldinventory,$fieldstock,$id)
	{
		$status = false;
		$jumlahbarang_keluar_bf = 0;
		$jumlahbarang_stock = 0;
		$jumlahbarang_keluar = 0;
		$totalstock = 0;
		$barang_inventory = 0;
		
		//select barang keluar
		$this->dbopr->where($fieldid,$id);
        $q = $this->dbopr->get($table);
        $row = $q->row();
		$jumlahbarang_keluar = $row->transaksi_barang_keluar_qty+$row->transaksi_barang_keluar_qty_backup;
		$barang_id = $row->transaksi_barang_keluar_barang;
		$barang_keluar_status = $row->transaksi_barang_keluar_approve;
		
		//select product in inventory
		$this->dbopr->where($fieldinventory,$barang_id);
        $qinv = $this->dbopr->get($tableinventory);
        $rowinv = $qinv->row();
		if(count($rowinv) > 0){
		
			$jumlahbarang_stock = $rowinv->inventory_total;
			$barang_inventory = $rowinv->inventory_product;
			
			//restore stock jika statusnya approve
			if(isset($barang_keluar_status) && ($barang_keluar_status == 1) ){
				unset($data);
				$data[$fieldstock] = $jumlahbarang_stock + $jumlahbarang_keluar;
				$this->dbopr->where($fieldinventory,$barang_inventory);
				if($this->dbopr->update($tableinventory,$data))
				{
					$status = true;
				}
			}else{
				$status = false;
			}
		}else{
			$status = false;
		}
		
		return $status;
	}
	
	//== crud update barang masuk ==//
	function insertbarang_masuk($tableinventory,$transaksi_barang_keluar_barang,$transaksi_barang_keluar_qty,$fieldinventoryproduct,$fieldinventorystatus)
	{
        $status = false;
		$jumlahbarang_keluar_bf = 0;
		$jumlahbarang_stock = 0;
		$jumlahbarang_keluar = 0;
		$totalstock = 0;
		$barang_inventory = 0;
		
		$transaksi_barang_keluar_qty = str_replace(".","",$transaksi_barang_keluar_qty);
		
		$this->dbopr->where($fieldinventoryproduct,$transaksi_barang_keluar_barang);
        $this->dbopr->where($fieldinventorystatus,$this->config->item("active_status"));
        $q = $this->dbopr->get($tableinventory);
        $row = $q->row();
		if(count($row) > 0){
			$barang_inventory = $row->inventory_product;	//id product
			$totalstock = $row->inventory_total;			//stok product
			
			$jumlahbarang_stock = $totalstock;
			$jumlahbarang_keluar = $transaksi_barang_keluar_qty;
			$updatebarang_stock = $jumlahbarang_stock + $jumlahbarang_keluar;
			
			unset($datainventory);
			$datainventory["inventory_total"] = $updatebarang_stock;
			$this->dbopr->where("inventory_product",$barang_inventory);
			$this->dbopr->where($fieldinventorystatus,$this->config->item("active_status"));
			if($this->dbopr->update($tableinventory,$datainventory))
			{
				$status = true;
			}
		}else{
			$status = false;
		}
		
        return $status;
    }
	function updatebarang_masuk($table,$transaksi_barang_masuk_id,$transaksi_barang_masuk_barang,$transaksi_barang_masuk_qty,$fieldid,$fieldstatus,
							    $tableinventory,$fieldinventoryproduct,$fieldinventorystatus)
	{
        $status = false;
		$jumlahbarang_keluar_bf = 0;
		$jumlahbarang_stock = 0;
		$jumlahbarang_keluar = 0;
		$totalstock = 0;
		$barang_inventory = 0;
		
		$transaksi_barang_masuk_qty = str_replace(".","",$transaksi_barang_masuk_qty);
		
		$this->dbopr->where($fieldid,$transaksi_barang_masuk_id);
        $this->dbopr->where($fieldstatus,$this->config->item("active_status"));
        $q_bf = $this->dbopr->get($table);
        $row_bf = $q_bf->row();
		$jumlahbarang_masuk_bf = $row_bf->transaksi_barang_masuk_qty;
		
		$this->dbopr->where($fieldinventoryproduct,$transaksi_barang_masuk_barang);
        $this->dbopr->where($fieldinventorystatus,$this->config->item("active_status"));
        $q = $this->dbopr->get($tableinventory);
        $row = $q->row();
		if(count($row) > 0){
		
			$barang_inventory = $row->inventory_product;	//id product
			$totalstock = $row->inventory_total;			//stok product
			
			//ex: stock 100, masuk 100 , stock=200, edit=50 , restore:200-100=  stockawal=100 + stok edit = 150
			//restore stock sebelumnya
			$jumlahbarang_stock = $totalstock - $jumlahbarang_masuk_bf;
			$jumlahbarang_masuk = $transaksi_barang_masuk_qty;
			
			//jumlah stock yg sudah di restore di tambah stock edit
			$updatebarang_stock = $jumlahbarang_stock + $jumlahbarang_masuk;
			
			if(isset($updatebarang_stock) && ($updatebarang_stock >= 0)){
				unset($datainventory);
				$datainventory["inventory_total"] = $updatebarang_stock;
				$this->dbopr->where("inventory_product",$barang_inventory);
				$this->dbopr->where($fieldinventorystatus,$this->config->item("active_status"));
				if($this->dbopr->update($tableinventory,$datainventory))
				{
					$status = true;
				}
			}else{
				$status = false;
			}
		}else{
			$status = false;
		}
		
        return $status;
    }
	function deletebarang_masuk($tableinventory,$table,$fieldid,$fieldinventory,$fieldstock,$id)
	{
		$status = false;
		$jumlahbarang_keluar_bf = 0;
		$jumlahbarang_stock = 0;
		$jumlahbarang_keluar = 0;
		$totalstock = 0;
		$barang_inventory = 0;
		
		//select barang masuk
		$this->dbopr->where($fieldid,$id);
        $q = $this->dbopr->get($table);
        $row = $q->row();
		$jumlahbarang_masuk = $row->transaksi_barang_masuk_qty;
		$barang_id = $row->transaksi_barang_masuk_barang;
		
		//select product in inventory
		$this->dbopr->where($fieldinventory,$barang_id);
        $qinv = $this->dbopr->get($tableinventory);
        $rowinv = $qinv->row();
		if(count($rowinv) > 0){
		
			$jumlahbarang_stock = $rowinv->inventory_total;
			$barang_inventory = $rowinv->inventory_product;
			
			//restore stock
			unset($data);
			$data[$fieldstock] = $jumlahbarang_stock - $jumlahbarang_masuk;
			$this->dbopr->where($fieldinventory,$barang_inventory);
			if($this->dbopr->update($tableinventory,$data))
			{
				$status = true;
			}
		}else{
			$status = false;
		}
		return $status;
	}
	
	function selectdata_biayakirim($table,$fid,$fieldkey,$fieldstatus,$fieldorder,$orderby,$groupby)    
    {
        switch ($table)
        {
            case "user":
            
            break;
        }
		$this->dbopr->order_by($fieldorder,$orderby);
        $this->dbopr->limit(1);
		$this->dbopr->where($fieldkey,$fid);
		$this->dbopr->where($fieldstatus,$this->config->item('active_status'));
        $q = $this->dbopr->get($table);
        $row = $q->row();
        return $row;
		
    }
	
	function count_totalprice($table,$fid,$fieldkey,$fieldstatus,$fieldshop)    
    {
        switch ($table)
        {
            case "user":
            
            break;
        }
		
		//TOTAL HARGA PRODUK
		$this->dbopr->order_by("transaksi_barang_keluar_date","asc");
		$this->dbopr->select("transaksi_barang_keluar_id,transaksi_barang_keluar_po,transaksi_barang_keluar_harga_jual,transaksi_barang_keluar_qty");
		$this->dbopr->where($fieldkey,$fid);
		$this->dbopr->where($fieldstatus,$this->config->item('active_status'));
		$this->dbopr->where($fieldshop,$this->config->item('active_status')); //khusus barang keluar dari store
		$q_po = $this->dbopr->get($table);
		$datatotalharga_product = $q_po->result();
		
		$totalhargaproduct = 0;
		if(isset($datatotalharga_product) && count($datatotalharga_product)>0)
		{
		
			for($i=0;$i<count($datatotalharga_product);$i++) { 
				
				$totalhargaproduct = $totalhargaproduct + ($datatotalharga_product[$i]->transaksi_barang_keluar_harga_jual*$datatotalharga_product[$i]->transaksi_barang_keluar_qty);
			}
		}
		
		//CARI BIAYA KIRIM berdasarkan PO (group by)
		$this->dbopr->order_by("transaksi_barang_keluar_date","asc");
		$this->dbopr->group_by("transaksi_barang_keluar_po");
		$this->dbopr->select("transaksi_barang_keluar_id,transaksi_barang_keluar_po");
		$this->dbopr->where($fieldkey,$fid);
		$this->dbopr->where($fieldstatus,$this->config->item('active_status'));
		$this->dbopr->where($fieldshop,$this->config->item('active_status')); //khusus barang keluar dari store
		$q_group_po = $this->dbopr->get($table);
		$data_group_po = $q_group_po->result();
		
		$biayakirim = 0;
		$totalbiayakirim = 0;
		if(isset($data_group_po) && count($data_group_po)>0)
		{
		
			for($i=0;$i<count($data_group_po);$i++) { 
				$data_biayakirim = $this->crud_model->selectdata_biayakirim("transaksi_barang_keluar_biaya_kirim",$data_group_po[$i]->transaksi_barang_keluar_po,"biaya_kirim_po","biaya_kirim_status","biaya_kirim_id","desc","biaya_kirim_po");
				if (count($data_biayakirim)>0){
					$biayakirim = $data_biayakirim->biaya_kirim_harga;
				}else{
					$biayakirim = 0;
				}
				
				$totalbiayakirim = $totalbiayakirim + $biayakirim;
				
			}
		}
		
		//harga total + biaya kirim 
		$totalprice = $totalhargaproduct + $totalbiayakirim;
		
		$data = array();
		$data['totalprice'] = $totalprice;
		$data['totalhargaproduct'] = $totalhargaproduct;
		$data['totalbiayakirim'] = $totalbiayakirim;
		
        return $data;
		
		
    }
	
	function getdetail_product_bypo($table,$fid,$fieldkey,$fieldstatus,$fieldshop)    
    {
        switch ($table)
        {
            case "user":
            
            break;
        }
		
		//total produk
		$this->dbopr->order_by("transaksi_barang_keluar_id","desc");
		$this->dbopr->select("transaksi_barang_keluar_id,transaksi_barang_keluar_po,transaksi_barang_keluar_barang,transaksi_barang_keluar_date,
						   transaksi_barang_keluar_qty,transaksi_barang_keluar_qty_backup,transaksi_barang_keluar_harga_jual,transaksi_barang_keluar_barang,
						   transaksi_barang_keluar_shop
						   
						   ");
		$this->dbopr->where($fieldkey,$fid);
		$this->dbopr->where($fieldstatus,$this->config->item('active_status'));
		$this->dbopr->where($fieldshop,$this->config->item('active_status')); //khusus barang keluar dari store
		$q = $this->dbopr->get($table);
		$data = $q->result();
		
        return $data;
		
    }
	//for vilanishop
	function sendemail($to,$subject,$msg)
    {
        $headers = "";
        $headers .= 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";        
        $headers .= 'Bcc: '.$this->config->item("mailcc"). "\r\n";
		$headers .=  'From: '.$this->config->item("mailnoreply"). "\r\n" . 'X-Mailer: PHP/' . phpversion();
		
        mail($to, $subject, $msg, $headers);
        return true;
    }
	//for vilanishop
	function selectdata_bycode2($table,$fieldstatus,$fieldtype,$usertype,$fieldtype2,$usertype2,$sdate="",$edate="")    
    {
		switch ($table)
        {
            case "user":
                
            break;
        }
		$this->dbopr->where($fieldtype,$usertype);
		$this->dbopr->where($fieldtype2,$usertype2);
        $this->dbopr->where($fieldstatus,$this->config->item("active_status"));
        $q = $this->dbopr->get($table);
        $rows = $q->result();
        return $rows;
    }
	
	//count per product (report list stok) //tidak dipakai
	function countdata_product_bydate_order2($table_in,$table_out,
											$fieldqty_in,$fieldqty_out,$fieldqtybackup,
											$fieldstatus_in,$fieldstatus_out,
											$fieldproduct_in,$fieldproduct_out,$productvalue,
											$fielddate_in,$fielddate_out,$datevalue,
											$fieldorder_in,$ordervalue_in,$fieldorder_out,$ordervalue_out)
    {
    	$total_in = 0;
    	$total_out = 0;
    	$total_backup = 0;
		
		$ket_in = "-";
		$ket_out = "-";
		$trans_in = 0;
		$trans_out = 0;
		$qty_in = "-";
		$qty_out = "-";
		$qty_backup = "-";
    	$qty_sisa = 0;
		$customer = "-";
		$creator = "-";
		$teknisi = "-";
		
		//barang masuk
		$this->dbopr->order_by($fieldorder_in,$ordervalue_in);		
    	$this->dbopr->where($fieldproduct_in,$productvalue);
    	$this->dbopr->where($fieldstatus_in,$this->config->item("active_status"));
		$this->dbopr->where($fielddate_in,$datevalue);
		$q_in = $this->dbopr->get($table_in);
    	$rows_in = $q_in->result();
    	
    	if(isset($rows_in)&&count($rows_in)>0)
    	{
    		for($i=0;$i<count($rows_in);$i++) {
    			$total_in = $total_in + $rows_in[$i]->$fieldqty_in;
			}
		}
		
		//barang keluar
		$this->dbopr->order_by($fieldorder_out,$ordervalue_out);		
    	$this->dbopr->where($fieldproduct_out,$productvalue);
    	$this->dbopr->where($fieldstatus_out,$this->config->item("active_status"));
		$this->dbopr->where($fielddate_out,$datevalue);
		$q_out = $this->dbopr->get($table_out);
    	$rows_out = $q_out->result();
    	
    	if(isset($rows_out)&&count($rows_out)>0)
    	{
    		for($i=0;$i<count($rows_out);$i++) {
    			$total_out = $total_out + $rows_out[$i]->$fieldqty_out;
				$total_backup = $total_backup + $rows_out[$i]->$fieldqtybackup;
				
			}
		}
		
		$data = array();
		$data['rows_in'] = $rows_in;
		$data['rows_out'] = $rows_out;
		$data['total_in'] = $total_in;
		$data['total_out'] = $total_out;
		$data['total_backup'] = $total_backup;
		
		/*if(isset($rows_in)){
			$ket_in = $rows_in->transaksi_barang_masuk_po;
			$trans_in = $rows_in->transaksi_barang_masuk_id;
			$qty_in = $rows_in->transaksi_barang_masuk_qty;
		}
		
		if(isset($rows_out)){
			$ket_out = $rows_out->transaksi_barang_keluar_po;
			$trans_out = $rows_out->transaksi_barang_keluar_id;
			$qty_out = $rows_out->transaksi_barang_keluar_qty;
			$qty_backup = $rows_out->transaksi_barang_keluar_qty_backup;
			$customer = $rows_out->transaksi_barang_keluar_qty_backup;
			$creator = $rows_out->transaksi_barang_keluar_sales;
			$teknisi = $rows_out->transaksi_barang_keluar_teknisi;
		}*/
		
		
        return $data;
    	
    }
	
	//count per product (report list stok)
	function selectdata_product_bydate_order($table_in,$table_out,$fieldqty,$fieldqtybackup,$fieldstatus,
											$fieldproduct,$productvalue,$fielddate,$datevalue,
											$fieldorder,$ordervalue,$sync_status)
    {
		//barang masuk (table transaksi_barang_keluar_sync) 
		$this->dbopr->order_by($fieldorder,$ordervalue);		
    	$this->dbopr->where($fieldproduct,$productvalue);
    	$this->dbopr->where($fieldstatus,$this->config->item("active_status"));
		$this->dbopr->where($sync_status,$this->config->item("active_status"));
		$this->dbopr->where($fielddate,$datevalue);
		$q_in = $this->dbopr->get($table_in);
    	$rows_in = $q_in->result();
    	
		//barang keluar
		$this->dbopr->order_by($fieldorder,$ordervalue);		
    	$this->dbopr->where($fieldproduct,$productvalue);
    	$this->dbopr->where($fieldstatus,$this->config->item("active_status"));
		$this->dbopr->where($fielddate,$datevalue);
		$q_out = $this->dbopr->get($table_out);
    	$rows_out = $q_out->result();
    	
		$rows = array_merge($rows_in, $rows_out);
		$data = array();
		$data['rows'] = $rows;

        return $data;
    	
    }
	
	function selectdata_bydate_order($table,$fieldstatus,$fieldcontent,$contentvalue,$fieldorder,$orderby,$fielddate,$datevalue)     //tidak dipakai
    {
		switch ($table)
        {
            case "user":
                
            break;
        }
		$this->dbopr->order_by($fieldorder,$orderby);
		$this->dbopr->where($fieldcontent,$contentvalue);
		$this->dbopr->where($fielddate,$datevalue);
        $this->dbopr->where($fieldstatus,$this->config->item("active_status"));
        $q = $this->dbopr->get($table);
        $rows = $q->result();
        return $rows;
    }
}
