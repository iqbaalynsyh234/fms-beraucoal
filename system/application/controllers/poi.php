<?php
include "base.php";

class POI extends Base {

	function POI()
	{
		parent::Base();
		$this->load->model("gpsmodel");

		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		if (! $this->sess->user_type)
		{
			redirect(base_url());
		}
	}

	function dosuggest()
	{
		$poiname = isset($_POST['poiname']) ? $_POST['poiname'] : "";
		$poicat = isset($_POST['poicat']) ? $_POST['poicat'] : "";

		if (! is_array($poiname))
		{
			$callback['error'] = true;
			$callback['message'] = $this->lang->line('lpoi_suggest_failed');

			echo json_encode($callback);
			return;
		}

		if (! is_array($poicat))
		{
			$callback['error'] = true;
			$callback['message'] = $this->lang->line('lpoi_suggest_failed');

			echo json_encode($callback);
			return;
		}

		$mydb = $this->load->database("master", TRUE);
		for($i=0; $i < count($poiname); $i++)
		{
			if (! $poicat[$i]) continue;

			unset($data);
			$data['poi_category'] = $poicat[$i];

			$mydb->where("poi_name", $poiname[$i]);
			$mydb->update("poi", $data);

			$this->db->cache_delete_all();
		}

		$callback['error'] = false;
		$callback['message'] = $this->lang->line('lpoi_suggest_success');
		$callback['redirect'] = base_url()."poi/suggest/".uniqid();

		echo json_encode($callback);
		return;
	}

	function suggest()
	{
		$this->db->order_by("poi_cat_name", "asc");
		$q = $this->db->get("poi_category");
		$categories = $q->result();

		$this->db->order_by("poi_name", "asc");
		$this->db->distinct();
		$this->db->select("poi_name");
		$this->db->where('poi_category', 0);
		$q = $this->db->get("poi");
		$rows = $q->result();

		for($i=0; $i < count($rows); $i++)
		{
			$found = false;
			for($j=0; $j < count($categories); $j++)
			{
				if (! $categories[$j]->poi_cat_name) continue;

				$pos = strpos(strtoupper($rows[$i]->poi_name), strtoupper($categories[$j]->poi_cat_name));
				if ($pos === FALSE) continue;

				$rows[$i]->poi_category = $categories[$j]->poi_cat_id;
				$rows[$i]->poi_icon = $categories[$j]->poi_cat_icon;

				$found = true;
				break;
			}

			if ($found) continue;
			$rows[$i]->poi_category = 0;
			$rows[$i]->poi_icon = "";
		}

		$this->params['title'] = $this->lang->line('lsuggest_poi');
		$this->params['categories'] = $categories;
		$this->params["initmap"] = $this->load->view('initmap', $this->params, true);
		$this->params["offset"] = 0;
		$this->params["total"] = count($rows);
		$this->params["data"] = $rows;
		$this->params["contentpoi"]  = $this->load->view('poi/suggest', $this->params, true);
		$this->params["content"] = $this->load->view('poi/list', $this->params, true);
		$this->load->view("templatesess", $this->params);
	}

	function index($field='all', $keyword='all', $offset=0)
	{

		$this->db->order_by("poi_cat_name", "asc");
		$this->db->where("poi_cat_status", 1);
		$q = $this->db->get("poi_category");
		$rowcats = $q->result();

		switch($field)
		{
			case "poi_category":
				$this->db->where("poi_category", $keyword);
			break;
			case "poi_name":
				$this->db->where("poi_name LIKE '%".$keyword."%'", null);
			break;
		}

		$this->db->order_by("poi_name", "asc");
		$this->db->join("poi_category", "poi_cat_id = poi_category");
		$this->db->join("location", "location_lat = poi_latitude AND location_lng = poi_longitude", "left outer");
		$q = $this->db->get("poi", $this->config->item("limit_records"), $offset);
		$rows = $q->result();		

		for($i=0; $i < count($rows); $i++)
		{
			if (! $rows[$i]->location_address)
			{
				$lat = sprintf("%.4f", $rows[$i]->poi_latitude);
				$lng = sprintf("%.4f", $rows[$i]->poi_longitude);

				$lokasi = $this->gpsmodel->GeoReverse($lat, $lng);
				$rows[$i]->location_address = $lokasi->display_name;
			}

			$rows[$i]->updated = ($this->sess->user_type != 2) || ($rows[$i]->poi_user_id == $this->sess->user_id);
		}

		switch($field)
		{
			case "poi_category":
				$this->db->where("poi_category", $keyword);
			break;
			case "poi_name":
				$this->db->where("poi_name LIKE '%".$keyword."%'", null);
			break;
		}
		$this->db->join("poi_category", "poi_cat_id = poi_category");
		$total = $this->db->count_all_results("poi");

		$config['uri_segment'] = 5;
		$config['base_url'] = base_url()."poi/index/".$field."/".$keyword;
		$config['total_rows'] = $total;
		$config['per_page'] = $this->config->item("limit_records");

		$this->pagination->initialize($config);

		$this->params['title'] = $this->lang->line('lpoi_list');
		$this->params["field"] = $field;
		$this->params["keyword"] = $keyword;
		$this->params["poicats"] = $rowcats;
		$this->params["initmap"] = $this->load->view('initmap', $this->params, true);
		$this->params["paging"] = $this->pagination->create_links();
		$this->params["offset"] = $offset;
		$this->params["total"] = $total;
		$this->params["data"] = $rows;
		$this->params["contentpoi"]  = $this->load->view('poi/tblpoi', $this->params, true);
		$this->params["content"] = $this->load->view('poi/list', $this->params, true);
		$this->load->view("templatesess", $this->params);
	}

	function import()
	{
		if ($this->sess->user_type == 2)
		{
			redirect(base_url());
		}

		$this->params['title'] = $this->lang->line('lpoi_import');
		$this->params["content"] = $this->load->view('poi/import', $this->params, true);
		$this->load->view("templatesess", $this->params);
	}

	function add($id=0)
	{
		if ($this->sess->user_type == 2)
		{
			//redirect(base_url());
		}

		if ($id)
		{
			$this->db->where("poi_id", $id);
			$q = $this->db->get("poi");

			if ($q->num_rows() == 0)
			{
				redirect(base_url());
				return;
			}

			$row = $q->row();

			if ($this->sess->user_type == 2)
			{
				if ($this->sess->user_id != $row->poi_user_id)
				{
					redirect(base_url()."poi/");
					return;
				}
			}

			$this->params['row'] = $row;
			$this->params['title'] = $this->lang->line('lpoi_update');
		}
		else
		{
			$this->params['title'] = $this->lang->line('lpoi_add');
		}

		$this->db->order_by("poi_cat_name", "asc");
		$this->db->where("poi_cat_status", 1);
		$q = $this->db->get("poi_category");

		$categories = $q->result();

		$this->params["initmap"] = $this->load->view('initmap', $this->params, true);
		$this->params["categories"] = $categories;
		$this->params["contentpoi"] = $this->load->view('poi/form', $this->params, true);
		$this->params["content"] = $this->load->view('poi/list', $this->params, true);
		$this->load->view("templatesess", $this->params);
	}

	function convert($lat, $ns, $lng, $we)
	{
		$callback['lat'] = getLatitude($lat, $ns);
		$callback['lon'] = getLongitude($lng, $we);

		echo json_encode($callback);
	}

	function save()
	{
		$id = isset($_POST['id']) ? trim($_POST['id']) : "";
		$poicat = isset($_POST['poicat']) ? trim($_POST['poicat']) : "";
		$poiname = isset($_POST['poiname']) ? trim($_POST['poiname']) : "";
		$coord = isset($_POST['coord']) ? trim($_POST['coord']) : "";

		if (strlen($poicat) == 0)
		{
			$callback['error'] = true;
			$callback['message'] = $this->lang->line('lempty_poi_category');

			echo json_encode($callback);
			return;
		}

		if (strlen($poiname) == 0)
		{
			$callback['error'] = true;
			$callback['message'] = $this->lang->line('lempty_poi_name');

			echo json_encode($callback);
			return;
		}

		if (strlen($coord) == 0)
		{
			$callback['error'] = true;
			$callback['message'] = $this->lang->line('lempty_poi_coord');

			echo json_encode($callback);
			return;
		}

		$arr = explode(",", $coord);

		if (count($arr) <= 1)
		{
			$callback['error'] = true;
			$callback['message'] = $this->lang->line('linvalid_poi_coord');

			echo json_encode($callback);
			return;
		}

		$lat = $arr[0];
		$lng = $arr[1];

		if ((! is_numeric($lat)) || (! is_numeric($lng)))
		{
			$callback['error'] = true;
			$callback['message'] = $this->lang->line('linvalid_poi_coord');

			echo json_encode($callback);
			return;
		}

		$this->db->where("poi_latitude", $lat);
		$this->db->where("poi_longitude", $lng);
		$q = $this->db->get("poi");

		if ($q->num_rows() > 0)
		{
			$row = $q->row();

			if ($row->poi_id != $id)
			{
				$callback['error'] = true;
				$callback['message'] = $this->lang->line('lexist_coord');

				echo json_encode($callback);
				return;
			}
		}

		unset($data);
		$data['poi_name'] = $poiname;
		$data['poi_latitude'] = sprintf("%.4f", $lat);
		$data['poi_longitude'] = sprintf("%.4f", $lng);
		$data['poi_status'] = 1;
		$data['poi_category'] = $poicat;
		$data['poi_user_id'] = $this->sess->user_id;

		$mydb = $this->load->database("master", TRUE);

		if ($id)
		{
			$mydb->where("poi_id", $id);
			$mydb->update("poi", $data);

			$this->db->cache_delete_all();

			$callback['error'] = false;
			$callback['message'] = $this->lang->line('lpoi_updated');
			$callback['redirect'] = base_url()."poi/add/".$id."/".uniqid();

			echo json_encode($callback);

			return;
		}

		$mydb->insert("poi", $data);
		$this->db->cache_delete_all();

		$lastid = $mydb->insert_id();

		$callback['error'] = false;
		$callback['message'] = $this->lang->line('lpoi_added');
		$callback['redirect'] = base_url()."poi/add/".$lastid."/".uniqid();

		$mail['subject'] = sprintf("add POI: %s", $poiname);
		$mail['message'] = implode("\r\n<br />", $data);
		$mail['dest'] = "jaya@vilanishop.com,jayatriyadi@hotmail.com,prastgtx@gmail.com,owner@adilahsoft.com";

		lacakmobilmail($mail);

		echo json_encode($callback);
	}

	function remove($id)
	{
		$this->db->where("poi_id", $id);
		$q = $this->db->get("poi");

		if ($q->num_rows() == 0)
		{
			redirect(base_url()."poi/");
			return;
		}

		$row = $q->row();

		if ($this->sess->user_type == 2)
		{
			if ($this->sess->user_id != $row->poi_user_id)
			{
				redirect(base_url()."poi/");
				return;
			}
		}

		$mydb = $this->load->database("master", TRUE);

		$mydb->where("poi_id", $id);
		$mydb->delete("poi");

		$this->db->cache_delete_all();

		redirect(base_url()."poi");
	}

	function category($offset=0)
	{
		parse_str($_SERVER['QUERY_STRING'], $_GET);

		if (isset($_GET['keyword']))
		{
			$this->db->where("poi_cat_name LIKE '%".trim($_GET['keyword'])."%'", null);
		}

		$this->db->order_by("poi_cat_name", "asc");
		$this->db->where("poi_cat_status", 1);
		$q = $this->db->get("poi_category", $this->config->item("limit_records"), $offset);
		$rows = $q->result();

		if (isset($_GET['keyword']))
		{
			$this->db->where("poi_cat_name LIKE '%".trim($_GET['keyword'])."%'", null);
		}
		$this->db->where("poi_cat_status", 1);
		$total = $this->db->count_all_results("poi_category");

		$config['base_url'] = base_url()."poi/category/";
		$config['total_rows'] = $total;
		$config['per_page'] = $this->config->item("limit_records");

		$this->load->library("pagination1");
		$this->pagination1->initialize($config);

		$this->params['title'] = $this->lang->line('lpoi_category_list');
		$this->params["paging"] = $this->pagination1->create_links();
		$this->params["offset"] = $offset;
		$this->params["total"] = $total;
		$this->params["data"] = $rows;
		$this->params["content"] = $this->load->view('poicategory/list', $this->params, true);
		$this->load->view("templatesess", $this->params);
	}

	function remove_category($id)
	{
		if ($this->sess->user_type == 2)
		{
			redirect(base_url());
		}

		$mydb = $this->load->database("master", TRUE);
		$mydb->where("poi_cat_id", $id);
		if ($this->sess->user_type != 1)
		{
			$mydb->where("poi_cat_creator", $this->sess->user_id);
		}
		$mydb->delete("poi_category");

		$this->db->cache_delete_all();

		redirect(base_url()."poi/category");
	}

	function add_category($id=0)
	{
		if ($this->sess->user_type == 2)
		{
			redirect(base_url());
		}

		if ($id)
		{
			$this->db->where("poi_cat_id", $id);
			if ($this->sess->user_type != 1)
			{
				$this->db->where("poi_cat_creator", $this->sess->user_id);
			}
			$q = $this->db->get("poi_category");

			if ($q->num_rows() == 0)
			{
				redirect(base_url());
			}

			$row = $q->row();
			$this->params['row'] = $row;
			$this->params['title'] = $this->lang->line('lpoi_category_update');
		}
		else
		{
			$this->params['title'] = $this->lang->line('lpoi_category_add');
		}

		$this->params["content"] = $this->load->view('poicategory/form', $this->params, true);
		$this->load->view("templatesess", $this->params);
	}

	function doimport()
	{

		if (! $_FILES['userfile']['name'])
		{
			echo "<script>parent.showAlert('".$this->lang->line('lchoose_kml_file')."');</script>";
			return;
		}

		$paths = pathinfo($_FILES['userfile']['name']);
		if (strcasecmp($paths['extension'], "kml"))
		{
			echo "<script>parent.showAlert('".$this->lang->line('linvalid_kml_file')."');</script>";
			return;
		}

		$config = $this->config->item("importpoi");
		$filename = $config['upload_path'].$paths['filename']."_".uniqid().".".$paths['extension'];

		move_uploaded_file($_FILES['userfile']['tmp_name'], $filename);

		$data = implode("", file($filename));

		$parser = xml_parser_create();
		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
		xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
		xml_parse_into_struct($parser, $data, $values, $tags);
		xml_parser_free($parser);

		foreach ($tags as $key=>$val)
		{
			if ($key != "Placemark") continue;

			$placemarks = $val;
			for ($i=0; $i < count($placemarks); $i+=2)
			{
                $offset = $placemarks[$i] + 1;
                $len = $placemarks[$i + 1] - $offset;

                $res = array_slice($values, $offset, $len);
                for ($j=0; $j < count($res); $j++)
                {
                	if ($res[$j]['type'] == 'complete')
                	{
                		if (! isset($res[$j]['value'])) continue;

                		$childs[$res[$j]['tag']] = $res[$j]['value'];

                		if (! isset($childs['coordinates'])) continue;

                		$coords = explode(",", $childs['coordinates']);
                		$childs['lat'] = sprintf("%.4f", trim($coords[0]));
                		$childs['lng'] = sprintf("%.4f", trim($coords[1]));
                	}
                }

                $poies[] = $childs;
			}
		}

		if (! isset($poies))
		{
			echo "<script>parent.showAlert('".$this->lang->line('linvalid_kml_file')."');</script>";
			return;
		}

		$this->db->select('poi_latitude, poi_longitude');
		$q = $this->db->get("poi");
		$rows = $q->result();

		for($i=0; $i < count($rows); $i++)
		{
			$latlngs[$rows[$i]->poi_latitude][$rows[$i]->poi_longitude] = 1;
		}

		$inserted = 0;
		$duplicated = 0;
		$i = 0;

		$mydb = $this->load->database("master", TRUE);

		foreach($poies as $poi)
		{
			if (isset($latlngs[$poi['lat']][$poi['lng']]))
			{
				$duplicated++;
				continue;
			}

			unset($data);

			$data['poi_latitude'] = $poi['lat'];
			$data['poi_longitude'] = $poi['lng'];
			$data['poi_name'] = $poi['name'];
			$data['poi_category'] = 0;
			$data['poi_status'] = 0;

			$mydb->insert("poi", $data);
			$inserted++;

			$latlngs[$poi['lat']][$poi['lng']] = 1;
		}

		$this->db->cache_delete_all();

		$msg = sprintf("%s\\r\\nDupicated: %s\\r\\nInserted: %s", $this->lang->line('limport_poi_success'), $duplicated, $inserted);
		echo "<script>parent.showAlertSuccess('".$msg."', '".base_url()."poi/');</script>";
	}

	function savecategory()
	{
		$id = isset($_POST['id']) ? trim($_POST['id']) : "";
		$catname = isset($_POST['catname']) ? trim($_POST['catname']) : "";

		if (strlen($catname) == 0)
		{
			echo "<script>parent.showAlert('".$this->lang->line('lempty_poi_category_name')."');</script>";
			return;
		}

		$this->db->where("poi_cat_name", $catname);
		$q = $this->db->get("poi_category");

		if ($q->num_rows() > 0)
		{
			$row = $q->row();

			if ($row->poi_cat_id != $id)
			{
				echo "<script>parent.showAlert('".$this->lang->line('lexist_poi_category_name')."');</script>";
				return;
			}
		}

		if (isset($_FILES['userfile']) && $_FILES['userfile']['name'])
		{
			$this->load->library('upload', $this->config->item('upload'));
			if (! $this->upload->do_upload())
			{
				$msg = $this->upload->display_errors();
				echo "<script>parent.showAlert('".$this->lang->line('linvalid_poi_category_icon')."\\r\\n".$msg."');</script>";
				return;
			}
		}

		unset($data);
		$data['poi_cat_name'] = $catname;

		if (isset($_FILES['userfile']) && $_FILES['userfile']['name'])
		{
			$data['poi_cat_icon'] = $_FILES['userfile']['name'];
		}

		$mydb = $this->load->database("master", TRUE);

		if ($id)
		{
			$mydb->where("poi_cat_id", $id);
			$mydb->update("poi_category", $data);
			$this->db->cache_delete_all();

			echo "<script>parent.showAlertSuccess('".$this->lang->line('lpoi_category_updated')."', '".base_url()."poi/category/0/".uniqid()."');</script>";
			return;
		}

		$data['poi_cat_creator'] = $this->sess->user_id;
		$data['poi_cat_created'] = date("Y-m-d H:i:s");

		$mydb->insert("poi_category", $data);
		$this->db->cache_delete_all();

		echo "<script>parent.showAlertSuccess('".$this->lang->line('lpoi_category_added')."', '".base_url()."poi/category/0/".uniqid()."');</script>";
	}

}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
