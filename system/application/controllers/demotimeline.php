<?php
include "base.php";

class Demotimeline extends Base {

	function Demotimeline()
	{
		parent::Base();
		$this->load->helper('common_helper');
		$this->load->helper('email');
		$this->load->library('email');
		$this->load->model("dashboardmodel");
		$this->load->helper('common');

		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
	}


  function index(){
		$this->params['code_view_menu'] = "monitor";

		$data = array(
			"0" => array(
				"vehicle" 							 => "B1621KRM",
				"totalroute"             => "6",
				"geofence" 							 => array(
																		"Lacak Mobil",
																		"Naga Pekayon",
																		"Mega Mall Bekasi",
																		"Bekasi Cyber Park",
																		"Summarecon Mall Bekasi",
																		"Lacak Mobil"
																	),
				"data"  => array(
								"0" => array(
												"geofence" 							 => "Lacak Mobil",
												"status"                 => "1",
												"kmtonextgeofence"       => "12",
												"currentkm"              => "0",
												"currentpositioncoord"   => "-6.26288,106.9850633",
												"currentpositionaddress" => "Mal Pekayon, Jl. Raya Pekayon No.29, RT.001/RW.020, Pekayon Jaya, Kec. Bekasi Sel., Kota Bks, Jawa Barat 17148",
												"persentbar"             => "100%"
											),
								"1" => array(
												"geofence"               => "Naga Pekayon",
												"status"                 => "1",
												"kmtonextgeofence"       => "80",
												"currentkm"              => "0",
												"currentpositioncoord"   => "-6.2494045,106.9901368",
												"currentpositionaddress" => "Jl. Jend. Ahmad Yani, RT.004/RW.001, Marga Jaya, Kec. Bekasi Bar., Kota Bks, Jawa Barat 17141",
												"persentbar"             => "100%"
										),
								"2" => array(
												"geofence"               => "Mega Mall Bekasi",
												"status"                 => "0",
												"kmtonextgeofence"       => "4",
												"currentkm"              => "0",
												"currentpositioncoord"   => "-6.2494045,106.9901368",
												"currentpositionaddress" => "Bks Cyber Park Kayuringin Jaya Kec. Bekasi Sel. Kota Bks Jawa Barat",
												"persentbar"             => "100%"
										),
								"3" => array(
													"geofence"               => "Bekasi Cyber Park",
													"status"                 => "0",
													"kmtonextgeofence"       => "2",
													"currentkm"              => "0",
													"currentpositioncoord"   => "-6.2259813,106.9988616",
													"currentpositionaddress" => "Jl. Bulevar Ahmad Yani, RT.006/RW.002, Marga Mulya, Kec. Bekasi Utara, Kota Bks, Jawa Barat 17142",
													"persentbar"             => "100%"
								),
								"4" => array(
													"geofence"               => "Summarecon Mall Bekasi",
													"status"                 => "0",
													"kmtonextgeofence"       => "2",
													"currentkm"              => "0",
													"currentpositioncoord"   => "-6.26288,106.9850633",
													"currentpositionaddress" => "Mal Pekayon, Jl. Raya Pekayon No.29, RT.001/RW.020, Pekayon Jaya, Kec. Bekasi Sel., Kota Bks, Jawa Barat 17148",
													"persentbar"             => "100%"
								),
								"5" => array(
													"geofence"               => "Lacak Mobil",
													"status"                 => "0",
													"kmtonextgeofence"       => "12",
													"currentkm"              => "0",
													"currentpositioncoord"   => "-6.2915165,106.9638034",
													"currentpositionaddress" => "Jalan Raya Swatantra Kav.4 No.71 Pekayon, RT.004/RW.004, Jatirasa, Kec. Jatiasih, Kota Bks, Jawa Barat 17424",
													"persentbar"             => "100%"
								)
			)
		),
		"1" => array(
			"vehicle" 							 => "B1622KRM",
			"totalroute"             => "5",
			"geofence" 							 => array(
																	"Lacak Mobil",
																	"Naga Pekayon",
																	"Mega Mall Bekasi",
																	"Bekasi Cyber Park",
																	"Lacak Mobil"
																),
			"data"  => array(
							"0" => array(
											"geofence" 							 => "Lacak Mobil",
											"status"                 => "1",
											"kmtonextgeofence"       => "12",
											"currentkm"              => "0",
											"currentpositioncoord"   => "-6.26288,106.9850633",
											"currentpositionaddress" => "Mal Pekayon, Jl. Raya Pekayon No.29, RT.001/RW.020, Pekayon Jaya, Kec. Bekasi Sel., Kota Bks, Jawa Barat 17148",
											"persentbar"             => "100%"
										),
							"1" => array(
											"geofence"               => "Naga Pekayon",
											"status"                 => "1",
											"kmtonextgeofence"       => "80",
											"currentkm"              => "0",
											"currentpositioncoord"   => "-6.2494045,106.9901368",
											"currentpositionaddress" => "Jl. Jend. Ahmad Yani, RT.004/RW.001, Marga Jaya, Kec. Bekasi Bar., Kota Bks, Jawa Barat 17141",
											"persentbar"             => "100%"
									),
							"2" => array(
											"geofence"               => "Mega Mall Bekasi",
											"status"                 => "1",
											"kmtonextgeofence"       => "4",
											"currentkm"              => "0",
											"currentpositioncoord"   => "-6.2494045,106.9901368",
											"currentpositionaddress" => "Bks Cyber Park Kayuringin Jaya Kec. Bekasi Sel. Kota Bks Jawa Barat",
											"persentbar"             => "100%"
									),
							"3" => array(
												"geofence"               => "Bekasi Cyber Park",
												"status"                 => "1",
												"kmtonextgeofence"       => "2",
												"currentkm"              => "0",
												"currentpositioncoord"   => "-6.2259813,106.9988616",
												"currentpositionaddress" => "Jl. Bulevar Ahmad Yani, RT.006/RW.002, Marga Mulya, Kec. Bekasi Utara, Kota Bks, Jawa Barat 17142",
												"persentbar"             => "100%"
							),
							"4" => array(
												"geofence"               => "Lacak Mobil",
												"status"                 => "1",
												"kmtonextgeofence"       => "12",
												"currentkm"              => "0",
												"currentpositioncoord"   => "-6.2915165,106.9638034",
												"currentpositionaddress" => "Jalan Raya Swatantra Kav.4 No.71 Pekayon, RT.004/RW.004, Jatirasa, Kec. Jatiasih, Kota Bks, Jawa Barat 17424",
												"persentbar"             => "100%"
							)
		)
	),
	"2" => array(
		"vehicle" 							 => "B1623KRM",
		"totalroute"             => "4",
		"geofence" 							 => array(
																"Lacak Mobil",
																"Naga Pekayon",
																"Mega Mall Bekasi",
																"Lacak Mobil"
															),
		"data"  => array(
						"0" => array(
										"geofence" 							 => "Lacak Mobil",
										"status"                 => "1",
										"kmtonextgeofence"       => "12",
										"currentkm"              => "0",
										"currentpositioncoord"   => "-6.26288,106.9850633",
										"currentpositionaddress" => "Mal Pekayon, Jl. Raya Pekayon No.29, RT.001/RW.020, Pekayon Jaya, Kec. Bekasi Sel., Kota Bks, Jawa Barat 17148",
										"persentbar"             => "100%"
									),
						"1" => array(
										"geofence"               => "Naga Pekayon",
										"status"                 => "1",
										"kmtonextgeofence"       => "80",
										"currentkm"              => "0",
										"currentpositioncoord"   => "-6.2494045,106.9901368",
										"currentpositionaddress" => "Jl. Jend. Ahmad Yani, RT.004/RW.001, Marga Jaya, Kec. Bekasi Bar., Kota Bks, Jawa Barat 17141",
										"persentbar"             => "100%"
								),
						"2" => array(
										"geofence"               => "Mega Mall Bekasi",
										"status"                 => "1",
										"kmtonextgeofence"       => "4",
										"currentkm"              => "0",
										"currentpositioncoord"   => "-6.2494045,106.9901368",
										"currentpositionaddress" => "Bks Cyber Park Kayuringin Jaya Kec. Bekasi Sel. Kota Bks Jawa Barat",
										"persentbar"             => "100%"
								),
						"3" => array(
											"geofence"               => "Lacak Mobil",
											"status"                 => "1",
											"kmtonextgeofence"       => "12",
											"currentkm"              => "0",
											"currentpositioncoord"   => "-6.2915165,106.9638034",
											"currentpositionaddress" => "Jalan Raya Swatantra Kav.4 No.71 Pekayon, RT.004/RW.004, Jatirasa, Kec. Jatiasih, Kota Bks, Jawa Barat 17424",
											"persentbar"             => "100%"
						)
	)
	),
	"3" => array(
	"vehicle" 							 => "B1624KRM",
	"totalroute"             => "5",
	"geofence" 							 => array(
															"Lacak Mobil",
															"Naga Pekayon",
															"Mega Mall Bekasi",
															"Bekasi Cyber Park",
															"Lacak Mobil"
														),
	"data"  => array(
					"0" => array(
									"geofence" 							 => "Lacak Mobil",
									"status"                 => "1",
									"kmtonextgeofence"       => "12",
									"currentkm"              => "0",
									"currentpositioncoord"   => "-6.26288,106.9850633",
									"currentpositionaddress" => "Mal Pekayon, Jl. Raya Pekayon No.29, RT.001/RW.020, Pekayon Jaya, Kec. Bekasi Sel., Kota Bks, Jawa Barat 17148",
									"persentbar"             => "100%"
								),
					"1" => array(
									"geofence"               => "Naga Pekayon",
									"status"                 => "1",
									"kmtonextgeofence"       => "80",
									"currentkm"              => "0",
									"currentpositioncoord"   => "-6.2494045,106.9901368",
									"currentpositionaddress" => "Jl. Jend. Ahmad Yani, RT.004/RW.001, Marga Jaya, Kec. Bekasi Bar., Kota Bks, Jawa Barat 17141",
									"persentbar"             => "100%"
							),
					"2" => array(
									"geofence"               => "Mega Mall Bekasi",
									"status"                 => "1",
									"kmtonextgeofence"       => "4",
									"currentkm"              => "0",
									"currentpositioncoord"   => "-6.2494045,106.9901368",
									"currentpositionaddress" => "Bks Cyber Park Kayuringin Jaya Kec. Bekasi Sel. Kota Bks Jawa Barat",
									"persentbar"             => "100%"
							),
					"3" => array(
										"geofence"               => "Bekasi Cyber Park",
										"status"                 => "1",
										"kmtonextgeofence"       => "2",
										"currentkm"              => "0",
										"currentpositioncoord"   => "-6.2259813,106.9988616",
										"currentpositionaddress" => "Jl. Bulevar Ahmad Yani, RT.006/RW.002, Marga Mulya, Kec. Bekasi Utara, Kota Bks, Jawa Barat 17142",
										"persentbar"             => "100%"
					),
					"4" => array(
										"geofence"               => "Lacak Mobil",
										"status"                 => "1",
										"kmtonextgeofence"       => "12",
										"currentkm"              => "0",
										"currentpositioncoord"   => "-6.2915165,106.9638034",
										"currentpositionaddress" => "Jalan Raya Swatantra Kav.4 No.71 Pekayon, RT.004/RW.004, Jatirasa, Kec. Jatiasih, Kota Bks, Jawa Barat 17424",
										"persentbar"             => "100%"
					)
	)
	),
	"4" => array(
	"vehicle" 							 => "B1625KRM",
	"totalroute"             => "6",
	"geofence" 							 => array(
															"Lacak Mobil",
															"Naga Pekayon",
															"Mega Mall Bekasi",
															"Bekasi Cyber Park",
															"Summarecon Mall Bekasi",
															"Lacak Mobil"
														),
	"data"  => array(
					"0" => array(
									"geofence" 							 => "Lacak Mobil",
									"status"                 => "1",
									"kmtonextgeofence"       => "12",
									"currentkm"              => "0",
									"currentpositioncoord"   => "-6.26288,106.9850633",
									"currentpositionaddress" => "Mal Pekayon, Jl. Raya Pekayon No.29, RT.001/RW.020, Pekayon Jaya, Kec. Bekasi Sel., Kota Bks, Jawa Barat 17148",
									"persentbar"             => "100%"
								),
					"1" => array(
									"geofence"               => "Naga Pekayon",
									"status"                 => "1",
									"kmtonextgeofence"       => "80",
									"currentkm"              => "0",
									"currentpositioncoord"   => "-6.2494045,106.9901368",
									"currentpositionaddress" => "Jl. Jend. Ahmad Yani, RT.004/RW.001, Marga Jaya, Kec. Bekasi Bar., Kota Bks, Jawa Barat 17141",
									"persentbar"             => "100%"
							),
					"2" => array(
									"geofence"               => "Mega Mall Bekasi",
									"status"                 => "1",
									"kmtonextgeofence"       => "4",
									"currentkm"              => "0",
									"currentpositioncoord"   => "-6.2494045,106.9901368",
									"currentpositionaddress" => "Bks Cyber Park Kayuringin Jaya Kec. Bekasi Sel. Kota Bks Jawa Barat",
									"persentbar"             => "100%"
							),
					"3" => array(
										"geofence"               => "Bekasi Cyber Park",
										"status"                 => "1",
										"kmtonextgeofence"       => "2",
										"currentkm"              => "0",
										"currentpositioncoord"   => "-6.2259813,106.9988616",
										"currentpositionaddress" => "Jl. Bulevar Ahmad Yani, RT.006/RW.002, Marga Mulya, Kec. Bekasi Utara, Kota Bks, Jawa Barat 17142",
										"persentbar"             => "100%"
					),
					"4" => array(
										"geofence"               => "Summarecon Mall Bekasi",
										"status"                 => "1",
										"kmtonextgeofence"       => "2",
										"currentkm"              => "0",
										"currentpositioncoord"   => "-6.26288,106.9850633",
										"currentpositionaddress" => "Mal Pekayon, Jl. Raya Pekayon No.29, RT.001/RW.020, Pekayon Jaya, Kec. Bekasi Sel., Kota Bks, Jawa Barat 17148",
										"persentbar"             => "100%"
					),
					"5" => array(
										"geofence"               => "Lacak Mobil",
										"status"                 => "1",
										"kmtonextgeofence"       => "12",
										"currentkm"              => "0",
										"currentpositioncoord"   => "-6.2915165,106.9638034",
										"currentpositionaddress" => "Jalan Raya Swatantra Kav.4 No.71 Pekayon, RT.004/RW.004, Jatirasa, Kec. Jatiasih, Kota Bks, Jawa Barat 17424",
										"persentbar"             => "100%"
					)
	)
	)
	);


		$datamobil = array();
		for ($i=0; $i < sizeof($data); $i++) {
			array_push($datamobil, array(
				"vehicle" => $data[$i]['vehicle']
			));
		}
		$this->params['vehicleno']      = json_encode($datamobil);;
		$this->params['data']           = $data;
		$this->params['totaldatamobil'] = sizeof($datamobil);

		// echo "<pre>";
		// var_dump($this->params['vehicleno']);die();
		// echo "<pre>";

    $this->params["header"]          = $this->load->view('dashboard/header', $this->params, true);
    $this->params["sidebar"]         = $this->load->view('dashboard/sidebar', $this->params, true);
    $this->params["chatsidebar"]     = $this->load->view('dashboard/chatsidebar', $this->params, true);
    $this->params["content"]         = $this->load->view('dashboard/demotimeline/v_timelinetracking', $this->params, true);
    $this->load->view("dashboard/template_dashboard_report", $this->params);
  }

	function v2(){
    $this->params['code_view_menu'] = "monitor";

		$data = array(
			"0" => array(
				"vehicle" 							 => "B1621KRM",
				"totalroute"             => "6",
				"geofence" 							 => array(
																		"Lacak Mobil",
																		"Naga Pekayon",
																		"Mega Mall Bekasi",
																		"Bekasi Cyber Park",
																		"Summarecon Mall Bekasi",
																		"Lacak Mobil"
																	),
				"data"  => array(
								"0" => array(
												"geofence" 							 => "Lacak Mobil",
									      "status"                 => "1",
									      "kmtonextgeofence"       => "12",
									      "currentkm"              => "0",
									      "currentpositioncoord"   => "-6.26288,106.9850633",
									      "currentpositionaddress" => "Mal Pekayon, Jl. Raya Pekayon No.29, RT.001/RW.020, Pekayon Jaya, Kec. Bekasi Sel., Kota Bks, Jawa Barat 17148",
									      "persentbar"             => "100%"
											),
								"1" => array(
												"geofence"               => "Naga Pekayon",
									      "status"                 => "1",
									      "kmtonextgeofence"       => "80",
									      "currentkm"              => "0",
									      "currentpositioncoord"   => "-6.2494045,106.9901368",
									      "currentpositionaddress" => "Jl. Jend. Ahmad Yani, RT.004/RW.001, Marga Jaya, Kec. Bekasi Bar., Kota Bks, Jawa Barat 17141",
									      "persentbar"             => "100%"
										),
								"2" => array(
												"geofence"               => "Mega Mall Bekasi",
												"status"                 => "0",
												"kmtonextgeofence"       => "4",
												"currentkm"              => "0",
												"currentpositioncoord"   => "-6.2494045,106.9901368",
												"currentpositionaddress" => "Bks Cyber Park Kayuringin Jaya Kec. Bekasi Sel. Kota Bks Jawa Barat",
												"persentbar"             => "100%"
										),
								"3" => array(
													"geofence"               => "Bekasi Cyber Park",
													"status"                 => "0",
													"kmtonextgeofence"       => "2",
													"currentkm"              => "0",
													"currentpositioncoord"   => "-6.2259813,106.9988616",
													"currentpositionaddress" => "Jl. Bulevar Ahmad Yani, RT.006/RW.002, Marga Mulya, Kec. Bekasi Utara, Kota Bks, Jawa Barat 17142",
													"persentbar"             => "100%"
								),
								"4" => array(
													"geofence"               => "Summarecon Mall Bekasi",
													"status"                 => "0",
													"kmtonextgeofence"       => "2",
													"currentkm"              => "0",
													"currentpositioncoord"   => "-6.26288,106.9850633",
													"currentpositionaddress" => "Mal Pekayon, Jl. Raya Pekayon No.29, RT.001/RW.020, Pekayon Jaya, Kec. Bekasi Sel., Kota Bks, Jawa Barat 17148",
													"persentbar"             => "100%"
								),
								"5" => array(
													"geofence"               => "Lacak Mobil",
													"status"                 => "0",
													"kmtonextgeofence"       => "12",
													"currentkm"              => "0",
													"currentpositioncoord"   => "-6.2915165,106.9638034",
													"currentpositionaddress" => "Jalan Raya Swatantra Kav.4 No.71 Pekayon, RT.004/RW.004, Jatirasa, Kec. Jatiasih, Kota Bks, Jawa Barat 17424",
													"persentbar"             => "100%"
								)
			)
		),
		"1" => array(
			"vehicle" 							 => "B1622KRM",
			"totalroute"             => "5",
			"geofence" 							 => array(
																	"Lacak Mobil",
																	"Naga Pekayon",
																	"Mega Mall Bekasi",
																	"Bekasi Cyber Park",
																	"Lacak Mobil"
																),
			"data"  => array(
							"0" => array(
											"geofence" 							 => "Lacak Mobil",
											"status"                 => "1",
											"kmtonextgeofence"       => "12",
											"currentkm"              => "0",
											"currentpositioncoord"   => "-6.26288,106.9850633",
											"currentpositionaddress" => "Mal Pekayon, Jl. Raya Pekayon No.29, RT.001/RW.020, Pekayon Jaya, Kec. Bekasi Sel., Kota Bks, Jawa Barat 17148",
											"persentbar"             => "100%"
										),
							"1" => array(
											"geofence"               => "Naga Pekayon",
											"status"                 => "1",
											"kmtonextgeofence"       => "80",
											"currentkm"              => "0",
											"currentpositioncoord"   => "-6.2494045,106.9901368",
											"currentpositionaddress" => "Jl. Jend. Ahmad Yani, RT.004/RW.001, Marga Jaya, Kec. Bekasi Bar., Kota Bks, Jawa Barat 17141",
											"persentbar"             => "100%"
									),
							"2" => array(
											"geofence"               => "Mega Mall Bekasi",
											"status"                 => "1",
											"kmtonextgeofence"       => "4",
											"currentkm"              => "0",
											"currentpositioncoord"   => "-6.2494045,106.9901368",
											"currentpositionaddress" => "Bks Cyber Park Kayuringin Jaya Kec. Bekasi Sel. Kota Bks Jawa Barat",
											"persentbar"             => "100%"
									),
							"3" => array(
												"geofence"               => "Bekasi Cyber Park",
												"status"                 => "1",
												"kmtonextgeofence"       => "2",
												"currentkm"              => "0",
												"currentpositioncoord"   => "-6.2259813,106.9988616",
												"currentpositionaddress" => "Jl. Bulevar Ahmad Yani, RT.006/RW.002, Marga Mulya, Kec. Bekasi Utara, Kota Bks, Jawa Barat 17142",
												"persentbar"             => "100%"
							),
							"4" => array(
												"geofence"               => "Lacak Mobil",
												"status"                 => "1",
												"kmtonextgeofence"       => "12",
												"currentkm"              => "0",
												"currentpositioncoord"   => "-6.2915165,106.9638034",
												"currentpositionaddress" => "Jalan Raya Swatantra Kav.4 No.71 Pekayon, RT.004/RW.004, Jatirasa, Kec. Jatiasih, Kota Bks, Jawa Barat 17424",
												"persentbar"             => "100%"
							)
		)
	),
	"2" => array(
		"vehicle" 							 => "B1623KRM",
		"totalroute"             => "4",
		"geofence" 							 => array(
																"Lacak Mobil",
																"Naga Pekayon",
																"Mega Mall Bekasi",
																"Lacak Mobil"
															),
		"data"  => array(
						"0" => array(
										"geofence" 							 => "Lacak Mobil",
										"status"                 => "1",
										"kmtonextgeofence"       => "12",
										"currentkm"              => "0",
										"currentpositioncoord"   => "-6.26288,106.9850633",
										"currentpositionaddress" => "Mal Pekayon, Jl. Raya Pekayon No.29, RT.001/RW.020, Pekayon Jaya, Kec. Bekasi Sel., Kota Bks, Jawa Barat 17148",
										"persentbar"             => "100%"
									),
						"1" => array(
										"geofence"               => "Naga Pekayon",
										"status"                 => "1",
										"kmtonextgeofence"       => "80",
										"currentkm"              => "0",
										"currentpositioncoord"   => "-6.2494045,106.9901368",
										"currentpositionaddress" => "Jl. Jend. Ahmad Yani, RT.004/RW.001, Marga Jaya, Kec. Bekasi Bar., Kota Bks, Jawa Barat 17141",
										"persentbar"             => "100%"
								),
						"2" => array(
										"geofence"               => "Mega Mall Bekasi",
										"status"                 => "1",
										"kmtonextgeofence"       => "4",
										"currentkm"              => "0",
										"currentpositioncoord"   => "-6.2494045,106.9901368",
										"currentpositionaddress" => "Bks Cyber Park Kayuringin Jaya Kec. Bekasi Sel. Kota Bks Jawa Barat",
										"persentbar"             => "100%"
								),
						"3" => array(
											"geofence"               => "Lacak Mobil",
											"status"                 => "1",
											"kmtonextgeofence"       => "12",
											"currentkm"              => "0",
											"currentpositioncoord"   => "-6.2915165,106.9638034",
											"currentpositionaddress" => "Jalan Raya Swatantra Kav.4 No.71 Pekayon, RT.004/RW.004, Jatirasa, Kec. Jatiasih, Kota Bks, Jawa Barat 17424",
											"persentbar"             => "100%"
						)
	)
),
"3" => array(
	"vehicle" 							 => "B1624KRM",
	"totalroute"             => "5",
	"geofence" 							 => array(
															"Lacak Mobil",
															"Naga Pekayon",
															"Mega Mall Bekasi",
															"Bekasi Cyber Park",
															"Lacak Mobil"
														),
	"data"  => array(
					"0" => array(
									"geofence" 							 => "Lacak Mobil",
									"status"                 => "1",
									"kmtonextgeofence"       => "12",
									"currentkm"              => "0",
									"currentpositioncoord"   => "-6.26288,106.9850633",
									"currentpositionaddress" => "Mal Pekayon, Jl. Raya Pekayon No.29, RT.001/RW.020, Pekayon Jaya, Kec. Bekasi Sel., Kota Bks, Jawa Barat 17148",
									"persentbar"             => "100%"
								),
					"1" => array(
									"geofence"               => "Naga Pekayon",
									"status"                 => "1",
									"kmtonextgeofence"       => "80",
									"currentkm"              => "0",
									"currentpositioncoord"   => "-6.2494045,106.9901368",
									"currentpositionaddress" => "Jl. Jend. Ahmad Yani, RT.004/RW.001, Marga Jaya, Kec. Bekasi Bar., Kota Bks, Jawa Barat 17141",
									"persentbar"             => "100%"
							),
					"2" => array(
									"geofence"               => "Mega Mall Bekasi",
									"status"                 => "1",
									"kmtonextgeofence"       => "4",
									"currentkm"              => "0",
									"currentpositioncoord"   => "-6.2494045,106.9901368",
									"currentpositionaddress" => "Bks Cyber Park Kayuringin Jaya Kec. Bekasi Sel. Kota Bks Jawa Barat",
									"persentbar"             => "100%"
							),
					"3" => array(
										"geofence"               => "Bekasi Cyber Park",
										"status"                 => "1",
										"kmtonextgeofence"       => "2",
										"currentkm"              => "0",
										"currentpositioncoord"   => "-6.2259813,106.9988616",
										"currentpositionaddress" => "Jl. Bulevar Ahmad Yani, RT.006/RW.002, Marga Mulya, Kec. Bekasi Utara, Kota Bks, Jawa Barat 17142",
										"persentbar"             => "100%"
					),
					"4" => array(
										"geofence"               => "Lacak Mobil",
										"status"                 => "1",
										"kmtonextgeofence"       => "12",
										"currentkm"              => "0",
										"currentpositioncoord"   => "-6.2915165,106.9638034",
										"currentpositionaddress" => "Jalan Raya Swatantra Kav.4 No.71 Pekayon, RT.004/RW.004, Jatirasa, Kec. Jatiasih, Kota Bks, Jawa Barat 17424",
										"persentbar"             => "100%"
					)
)
),
"4" => array(
	"vehicle" 							 => "B1625KRM",
	"totalroute"             => "6",
	"geofence" 							 => array(
															"Lacak Mobil",
															"Naga Pekayon",
															"Mega Mall Bekasi",
															"Bekasi Cyber Park",
															"Summarecon Mall Bekasi",
															"Lacak Mobil"
														),
	"data"  => array(
					"0" => array(
									"geofence" 							 => "Lacak Mobil",
									"status"                 => "1",
									"kmtonextgeofence"       => "12",
									"currentkm"              => "0",
									"currentpositioncoord"   => "-6.26288,106.9850633",
									"currentpositionaddress" => "Mal Pekayon, Jl. Raya Pekayon No.29, RT.001/RW.020, Pekayon Jaya, Kec. Bekasi Sel., Kota Bks, Jawa Barat 17148",
									"persentbar"             => "100%"
								),
					"1" => array(
									"geofence"               => "Naga Pekayon",
									"status"                 => "1",
									"kmtonextgeofence"       => "80",
									"currentkm"              => "0",
									"currentpositioncoord"   => "-6.2494045,106.9901368",
									"currentpositionaddress" => "Jl. Jend. Ahmad Yani, RT.004/RW.001, Marga Jaya, Kec. Bekasi Bar., Kota Bks, Jawa Barat 17141",
									"persentbar"             => "100%"
							),
					"2" => array(
									"geofence"               => "Mega Mall Bekasi",
									"status"                 => "1",
									"kmtonextgeofence"       => "4",
									"currentkm"              => "0",
									"currentpositioncoord"   => "-6.2494045,106.9901368",
									"currentpositionaddress" => "Bks Cyber Park Kayuringin Jaya Kec. Bekasi Sel. Kota Bks Jawa Barat",
									"persentbar"             => "100%"
							),
					"3" => array(
										"geofence"               => "Bekasi Cyber Park",
										"status"                 => "1",
										"kmtonextgeofence"       => "2",
										"currentkm"              => "0",
										"currentpositioncoord"   => "-6.2259813,106.9988616",
										"currentpositionaddress" => "Jl. Bulevar Ahmad Yani, RT.006/RW.002, Marga Mulya, Kec. Bekasi Utara, Kota Bks, Jawa Barat 17142",
										"persentbar"             => "100%"
					),
					"4" => array(
										"geofence"               => "Summarecon Mall Bekasi",
										"status"                 => "1",
										"kmtonextgeofence"       => "2",
										"currentkm"              => "0",
										"currentpositioncoord"   => "-6.26288,106.9850633",
										"currentpositionaddress" => "Mal Pekayon, Jl. Raya Pekayon No.29, RT.001/RW.020, Pekayon Jaya, Kec. Bekasi Sel., Kota Bks, Jawa Barat 17148",
										"persentbar"             => "100%"
					),
					"5" => array(
										"geofence"               => "Lacak Mobil",
										"status"                 => "1",
										"kmtonextgeofence"       => "12",
										"currentkm"              => "0",
										"currentpositioncoord"   => "-6.2915165,106.9638034",
										"currentpositionaddress" => "Jalan Raya Swatantra Kav.4 No.71 Pekayon, RT.004/RW.004, Jatirasa, Kec. Jatiasih, Kota Bks, Jawa Barat 17424",
										"persentbar"             => "100%"
					)
)
)
	);


		$datamobil = array();
		for ($i=0; $i < sizeof($data); $i++) {
			array_push($datamobil, array(
				"vehicle" => $data[$i]['vehicle']
			));
		}
		$this->params['vehicleno']      = json_encode($datamobil);;
		$this->params['data']           = $data;
		$this->params['totaldatamobil'] = sizeof($datamobil);

		// echo "<pre>";
		// var_dump($this->params['vehicleno']);die();
		// echo "<pre>";

    $this->params["header"]          = $this->load->view('dashboard/header', $this->params, true);
    $this->params["sidebar"]         = $this->load->view('dashboard/sidebar', $this->params, true);
    $this->params["chatsidebar"]     = $this->load->view('dashboard/chatsidebar', $this->params, true);
    $this->params["content"]         = $this->load->view('dashboard/demotimeline/v_timelinetrackingv2', $this->params, true);
    $this->load->view("dashboard/template_dashboard_report", $this->params);
  }

	function getdatanya(){
	$this->params['code_view_menu'] = "monitor";

	$data = array(
		"0" => array(
			"vehicle" 							 => "B1621KRM",
			"totalroute"             => "6",
			"geofence" 							 => array(
																	"Lacak Mobil",
																	"Naga Pekayon",
																	"Mega Mall Bekasi",
																	"Bekasi Cyber Park",
																	"Summarecon Mall Bekasi",
																	"Lacak Mobil"
																),
			"data"  => array(
							"0" => array(
											"geofence" 							 => "Lacak Mobil",
											"status"                 => "1",
											"kmtonextgeofence"       => "12",
											"currentkm"              => "0",
											"currentpositioncoord"   => "-6.26288,106.9850633",
											"currentpositionaddress" => "Mal Pekayon, Jl. Raya Pekayon No.29, RT.001/RW.020, Pekayon Jaya, Kec. Bekasi Sel., Kota Bks, Jawa Barat 17148",
											"persentbar"             => "100%"
										),
							"1" => array(
											"geofence"               => "Naga Pekayon",
											"status"                 => "1",
											"kmtonextgeofence"       => "80",
											"currentkm"              => "0",
											"currentpositioncoord"   => "-6.2494045,106.9901368",
											"currentpositionaddress" => "Jl. Jend. Ahmad Yani, RT.004/RW.001, Marga Jaya, Kec. Bekasi Bar., Kota Bks, Jawa Barat 17141",
											"persentbar"             => "100%"
									),
							"2" => array(
											"geofence"               => "Mega Mall Bekasi",
											"status"                 => "1",
											"kmtonextgeofence"       => "4",
											"currentkm"              => "0",
											"currentpositioncoord"   => "-6.2494045,106.9901368",
											"currentpositionaddress" => "Bks Cyber Park Kayuringin Jaya Kec. Bekasi Sel. Kota Bks Jawa Barat",
											"persentbar"             => "100%"
									),
							"3" => array(
												"geofence"               => "Bekasi Cyber Park",
												"status"                 => "1",
												"kmtonextgeofence"       => "2",
												"currentkm"              => "0",
												"currentpositioncoord"   => "-6.2259813,106.9988616",
												"currentpositionaddress" => "Jl. Bulevar Ahmad Yani, RT.006/RW.002, Marga Mulya, Kec. Bekasi Utara, Kota Bks, Jawa Barat 17142",
												"persentbar"             => "100%"
							),
							"4" => array(
												"geofence"               => "Summarecon Mall Bekasi",
												"status"                 => "1",
												"kmtonextgeofence"       => "2",
												"currentkm"              => "0",
												"currentpositioncoord"   => "-6.26288,106.9850633",
												"currentpositionaddress" => "Mal Pekayon, Jl. Raya Pekayon No.29, RT.001/RW.020, Pekayon Jaya, Kec. Bekasi Sel., Kota Bks, Jawa Barat 17148",
												"persentbar"             => "100%"
							),
							"5" => array(
												"geofence"               => "Lacak Mobil",
												"status"                 => "1",
												"kmtonextgeofence"       => "12",
												"currentkm"              => "0",
												"currentpositioncoord"   => "-6.2915165,106.9638034",
												"currentpositionaddress" => "Jalan Raya Swatantra Kav.4 No.71 Pekayon, RT.004/RW.004, Jatirasa, Kec. Jatiasih, Kota Bks, Jawa Barat 17424",
												"persentbar"             => "100%"
							)
		)
	),
	"1" => array(
		"vehicle" 							 => "B1622KRM",
		"totalroute"             => "5",
		"geofence" 							 => array(
																"Lacak Mobil",
																"Naga Pekayon",
																"Mega Mall Bekasi",
																"Bekasi Cyber Park",
																"Lacak Mobil"
															),
		"data"  => array(
						"0" => array(
										"geofence" 							 => "Lacak Mobil",
										"status"                 => "1",
										"kmtonextgeofence"       => "12",
										"currentkm"              => "0",
										"currentpositioncoord"   => "-6.26288,106.9850633",
										"currentpositionaddress" => "Mal Pekayon, Jl. Raya Pekayon No.29, RT.001/RW.020, Pekayon Jaya, Kec. Bekasi Sel., Kota Bks, Jawa Barat 17148",
										"persentbar"             => "100%"
									),
						"1" => array(
										"geofence"               => "Naga Pekayon",
										"status"                 => "1",
										"kmtonextgeofence"       => "80",
										"currentkm"              => "0",
										"currentpositioncoord"   => "-6.2494045,106.9901368",
										"currentpositionaddress" => "Jl. Jend. Ahmad Yani, RT.004/RW.001, Marga Jaya, Kec. Bekasi Bar., Kota Bks, Jawa Barat 17141",
										"persentbar"             => "100%"
								),
						"2" => array(
										"geofence"               => "Mega Mall Bekasi",
										"status"                 => "1",
										"kmtonextgeofence"       => "4",
										"currentkm"              => "0",
										"currentpositioncoord"   => "-6.2494045,106.9901368",
										"currentpositionaddress" => "Bks Cyber Park Kayuringin Jaya Kec. Bekasi Sel. Kota Bks Jawa Barat",
										"persentbar"             => "100%"
								),
						"3" => array(
											"geofence"               => "Bekasi Cyber Park",
											"status"                 => "1",
											"kmtonextgeofence"       => "2",
											"currentkm"              => "0",
											"currentpositioncoord"   => "-6.2259813,106.9988616",
											"currentpositionaddress" => "Jl. Bulevar Ahmad Yani, RT.006/RW.002, Marga Mulya, Kec. Bekasi Utara, Kota Bks, Jawa Barat 17142",
											"persentbar"             => "100%"
						),
						"4" => array(
											"geofence"               => "Lacak Mobil",
											"status"                 => "1",
											"kmtonextgeofence"       => "12",
											"currentkm"              => "0",
											"currentpositioncoord"   => "-6.2915165,106.9638034",
											"currentpositionaddress" => "Jalan Raya Swatantra Kav.4 No.71 Pekayon, RT.004/RW.004, Jatirasa, Kec. Jatiasih, Kota Bks, Jawa Barat 17424",
											"persentbar"             => "100%"
						)
	)
),
"2" => array(
	"vehicle" 							 => "B1623KRM",
	"totalroute"             => "4",
	"geofence" 							 => array(
															"Lacak Mobil",
															"Naga Pekayon",
															"Mega Mall Bekasi",
															"Lacak Mobil"
														),
	"data"  => array(
					"0" => array(
									"geofence" 							 => "Lacak Mobil",
									"status"                 => "1",
									"kmtonextgeofence"       => "12",
									"currentkm"              => "0",
									"currentpositioncoord"   => "-6.26288,106.9850633",
									"currentpositionaddress" => "Mal Pekayon, Jl. Raya Pekayon No.29, RT.001/RW.020, Pekayon Jaya, Kec. Bekasi Sel., Kota Bks, Jawa Barat 17148",
									"persentbar"             => "100%"
								),
					"1" => array(
									"geofence"               => "Naga Pekayon",
									"status"                 => "1",
									"kmtonextgeofence"       => "80",
									"currentkm"              => "0",
									"currentpositioncoord"   => "-6.2494045,106.9901368",
									"currentpositionaddress" => "Jl. Jend. Ahmad Yani, RT.004/RW.001, Marga Jaya, Kec. Bekasi Bar., Kota Bks, Jawa Barat 17141",
									"persentbar"             => "100%"
							),
					"2" => array(
									"geofence"               => "Mega Mall Bekasi",
									"status"                 => "1",
									"kmtonextgeofence"       => "4",
									"currentkm"              => "0",
									"currentpositioncoord"   => "-6.2494045,106.9901368",
									"currentpositionaddress" => "Bks Cyber Park Kayuringin Jaya Kec. Bekasi Sel. Kota Bks Jawa Barat",
									"persentbar"             => "100%"
							),
					"3" => array(
										"geofence"               => "Lacak Mobil",
										"status"                 => "1",
										"kmtonextgeofence"       => "12",
										"currentkm"              => "0",
										"currentpositioncoord"   => "-6.2915165,106.9638034",
										"currentpositionaddress" => "Jalan Raya Swatantra Kav.4 No.71 Pekayon, RT.004/RW.004, Jatirasa, Kec. Jatiasih, Kota Bks, Jawa Barat 17424",
										"persentbar"             => "100%"
					)
)
),
"3" => array(
"vehicle" 							 => "B1623KRM",
"totalroute"             => "5",
"geofence" 							 => array(
														"Lacak Mobil",
														"Naga Pekayon",
														"Mega Mall Bekasi",
														"Bekasi Cyber Park",
														"Lacak Mobil"
													),
"data"  => array(
				"0" => array(
								"geofence" 							 => "Lacak Mobil",
								"status"                 => "1",
								"kmtonextgeofence"       => "12",
								"currentkm"              => "0",
								"currentpositioncoord"   => "-6.26288,106.9850633",
								"currentpositionaddress" => "Mal Pekayon, Jl. Raya Pekayon No.29, RT.001/RW.020, Pekayon Jaya, Kec. Bekasi Sel., Kota Bks, Jawa Barat 17148",
								"persentbar"             => "100%"
							),
				"1" => array(
								"geofence"               => "Naga Pekayon",
								"status"                 => "1",
								"kmtonextgeofence"       => "80",
								"currentkm"              => "0",
								"currentpositioncoord"   => "-6.2494045,106.9901368",
								"currentpositionaddress" => "Jl. Jend. Ahmad Yani, RT.004/RW.001, Marga Jaya, Kec. Bekasi Bar., Kota Bks, Jawa Barat 17141",
								"persentbar"             => "100%"
						),
				"2" => array(
								"geofence"               => "Mega Mall Bekasi",
								"status"                 => "1",
								"kmtonextgeofence"       => "4",
								"currentkm"              => "0",
								"currentpositioncoord"   => "-6.2494045,106.9901368",
								"currentpositionaddress" => "Bks Cyber Park Kayuringin Jaya Kec. Bekasi Sel. Kota Bks Jawa Barat",
								"persentbar"             => "100%"
						),
				"3" => array(
									"geofence"               => "Bekasi Cyber Park",
									"status"                 => "1",
									"kmtonextgeofence"       => "2",
									"currentkm"              => "0",
									"currentpositioncoord"   => "-6.2259813,106.9988616",
									"currentpositionaddress" => "Jl. Bulevar Ahmad Yani, RT.006/RW.002, Marga Mulya, Kec. Bekasi Utara, Kota Bks, Jawa Barat 17142",
									"persentbar"             => "100%"
				),
				"4" => array(
									"geofence"               => "Lacak Mobil",
									"status"                 => "1",
									"kmtonextgeofence"       => "12",
									"currentkm"              => "0",
									"currentpositioncoord"   => "-6.2915165,106.9638034",
									"currentpositionaddress" => "Jalan Raya Swatantra Kav.4 No.71 Pekayon, RT.004/RW.004, Jatirasa, Kec. Jatiasih, Kota Bks, Jawa Barat 17424",
									"persentbar"             => "100%"
				)
)
),
"4" => array(
"vehicle" 							 => "B1624KRM",
"totalroute"             => "6",
"geofence" 							 => array(
														"Lacak Mobil",
														"Naga Pekayon",
														"Mega Mall Bekasi",
														"Bekasi Cyber Park",
														"Summarecon Mall Bekasi",
														"Lacak Mobil"
													),
"data"  => array(
				"0" => array(
								"geofence" 							 => "Lacak Mobil",
								"status"                 => "1",
								"kmtonextgeofence"       => "12",
								"currentkm"              => "0",
								"currentpositioncoord"   => "-6.26288,106.9850633",
								"currentpositionaddress" => "Mal Pekayon, Jl. Raya Pekayon No.29, RT.001/RW.020, Pekayon Jaya, Kec. Bekasi Sel., Kota Bks, Jawa Barat 17148",
								"persentbar"             => "100%"
							),
				"1" => array(
								"geofence"               => "Naga Pekayon",
								"status"                 => "1",
								"kmtonextgeofence"       => "80",
								"currentkm"              => "0",
								"currentpositioncoord"   => "-6.2494045,106.9901368",
								"currentpositionaddress" => "Jl. Jend. Ahmad Yani, RT.004/RW.001, Marga Jaya, Kec. Bekasi Bar., Kota Bks, Jawa Barat 17141",
								"persentbar"             => "100%"
						),
				"2" => array(
								"geofence"               => "Mega Mall Bekasi",
								"status"                 => "1",
								"kmtonextgeofence"       => "4",
								"currentkm"              => "0",
								"currentpositioncoord"   => "-6.2494045,106.9901368",
								"currentpositionaddress" => "Bks Cyber Park Kayuringin Jaya Kec. Bekasi Sel. Kota Bks Jawa Barat",
								"persentbar"             => "100%"
						),
				"3" => array(
									"geofence"               => "Bekasi Cyber Park",
									"status"                 => "1",
									"kmtonextgeofence"       => "2",
									"currentkm"              => "0",
									"currentpositioncoord"   => "-6.2259813,106.9988616",
									"currentpositionaddress" => "Jl. Bulevar Ahmad Yani, RT.006/RW.002, Marga Mulya, Kec. Bekasi Utara, Kota Bks, Jawa Barat 17142",
									"persentbar"             => "100%"
				),
				"4" => array(
									"geofence"               => "Summarecon Mall Bekasi",
									"status"                 => "1",
									"kmtonextgeofence"       => "2",
									"currentkm"              => "0",
									"currentpositioncoord"   => "-6.26288,106.9850633",
									"currentpositionaddress" => "Mal Pekayon, Jl. Raya Pekayon No.29, RT.001/RW.020, Pekayon Jaya, Kec. Bekasi Sel., Kota Bks, Jawa Barat 17148",
									"persentbar"             => "100%"
				),
				"5" => array(
									"geofence"               => "Lacak Mobil",
									"status"                 => "1",
									"kmtonextgeofence"       => "12",
									"currentkm"              => "0",
									"currentpositioncoord"   => "-6.2915165,106.9638034",
									"currentpositionaddress" => "Jalan Raya Swatantra Kav.4 No.71 Pekayon, RT.004/RW.004, Jatirasa, Kec. Jatiasih, Kota Bks, Jawa Barat 17424",
									"persentbar"             => "100%"
				)
)
)
);

	$callback['data']      = $data;

	// echo "<pre>";
	// var_dump($datapercent);die();
	// echo "<pre>";
	echo json_encode($callback);
}

function getdatamobil(){
	$this->params['code_view_menu'] = "monitor";

	$data = array(
		"0" => array(
			"vehicle" 							 => "B1621KRM",
			"totalroute"             => "6",
			"geofence" 							 => array(
																	"Lacak Mobil",
																	"Naga Pekayon",
																	"Mega Mall Bekasi",
																	"Bekasi Cyber Park",
																	"Summarecon Mall Bekasi",
																	"Lacak Mobil"
																),
			"data"  => array(
							"0" => array(
											"geofence" 							 => "Lacak Mobil",
											"status"                 => "1",
											"kmtonextgeofence"       => "12",
											"currentkm"              => "0",
											"currentpositioncoord"   => "-6.26288,106.9850633",
											"currentpositionaddress" => "Mal Pekayon, Jl. Raya Pekayon No.29, RT.001/RW.020, Pekayon Jaya, Kec. Bekasi Sel., Kota Bks, Jawa Barat 17148",
											"persentbar"             => "100%"
										),
							"1" => array(
											"geofence"               => "Naga Pekayon",
											"status"                 => "1",
											"kmtonextgeofence"       => "80",
											"currentkm"              => "0",
											"currentpositioncoord"   => "-6.2494045,106.9901368",
											"currentpositionaddress" => "Jl. Jend. Ahmad Yani, RT.004/RW.001, Marga Jaya, Kec. Bekasi Bar., Kota Bks, Jawa Barat 17141",
											"persentbar"             => "100%"
									),
							"2" => array(
											"geofence"               => "Mega Mall Bekasi",
											"status"                 => "1",
											"kmtonextgeofence"       => "4",
											"currentkm"              => "0",
											"currentpositioncoord"   => "-6.2494045,106.9901368",
											"currentpositionaddress" => "Bks Cyber Park Kayuringin Jaya Kec. Bekasi Sel. Kota Bks Jawa Barat",
											"persentbar"             => "100%"
									),
							"3" => array(
												"geofence"               => "Bekasi Cyber Park",
												"status"                 => "1",
												"kmtonextgeofence"       => "2",
												"currentkm"              => "0",
												"currentpositioncoord"   => "-6.2259813,106.9988616",
												"currentpositionaddress" => "Jl. Bulevar Ahmad Yani, RT.006/RW.002, Marga Mulya, Kec. Bekasi Utara, Kota Bks, Jawa Barat 17142",
												"persentbar"             => "100%"
							),
							"4" => array(
												"geofence"               => "Summarecon Mall Bekasi",
												"status"                 => "1",
												"kmtonextgeofence"       => "2",
												"currentkm"              => "0",
												"currentpositioncoord"   => "-6.26288,106.9850633",
												"currentpositionaddress" => "Mal Pekayon, Jl. Raya Pekayon No.29, RT.001/RW.020, Pekayon Jaya, Kec. Bekasi Sel., Kota Bks, Jawa Barat 17148",
												"persentbar"             => "100%"
							),
							"5" => array(
												"geofence"               => "Lacak Mobil",
												"status"                 => "1",
												"kmtonextgeofence"       => "12",
												"currentkm"              => "0",
												"currentpositioncoord"   => "-6.2915165,106.9638034",
												"currentpositionaddress" => "Jalan Raya Swatantra Kav.4 No.71 Pekayon, RT.004/RW.004, Jatirasa, Kec. Jatiasih, Kota Bks, Jawa Barat 17424",
												"persentbar"             => "100%"
							)
		)
	),
	"1" => array(
		"vehicle" 							 => "B1622KRM",
		"totalroute"             => "5",
		"geofence" 							 => array(
																"Lacak Mobil",
																"Naga Pekayon",
																"Mega Mall Bekasi",
																"Bekasi Cyber Park",
																"Lacak Mobil"
															),
		"data"  => array(
						"0" => array(
										"geofence" 							 => "Lacak Mobil",
										"status"                 => "1",
										"kmtonextgeofence"       => "12",
										"currentkm"              => "0",
										"currentpositioncoord"   => "-6.26288,106.9850633",
										"currentpositionaddress" => "Mal Pekayon, Jl. Raya Pekayon No.29, RT.001/RW.020, Pekayon Jaya, Kec. Bekasi Sel., Kota Bks, Jawa Barat 17148",
										"persentbar"             => "100%"
									),
						"1" => array(
										"geofence"               => "Naga Pekayon",
										"status"                 => "1",
										"kmtonextgeofence"       => "80",
										"currentkm"              => "0",
										"currentpositioncoord"   => "-6.2494045,106.9901368",
										"currentpositionaddress" => "Jl. Jend. Ahmad Yani, RT.004/RW.001, Marga Jaya, Kec. Bekasi Bar., Kota Bks, Jawa Barat 17141",
										"persentbar"             => "100%"
								),
						"2" => array(
										"geofence"               => "Mega Mall Bekasi",
										"status"                 => "1",
										"kmtonextgeofence"       => "4",
										"currentkm"              => "0",
										"currentpositioncoord"   => "-6.2494045,106.9901368",
										"currentpositionaddress" => "Bks Cyber Park Kayuringin Jaya Kec. Bekasi Sel. Kota Bks Jawa Barat",
										"persentbar"             => "100%"
								),
						"3" => array(
											"geofence"               => "Bekasi Cyber Park",
											"status"                 => "1",
											"kmtonextgeofence"       => "2",
											"currentkm"              => "0",
											"currentpositioncoord"   => "-6.2259813,106.9988616",
											"currentpositionaddress" => "Jl. Bulevar Ahmad Yani, RT.006/RW.002, Marga Mulya, Kec. Bekasi Utara, Kota Bks, Jawa Barat 17142",
											"persentbar"             => "100%"
						),
						"4" => array(
											"geofence"               => "Lacak Mobil",
											"status"                 => "1",
											"kmtonextgeofence"       => "12",
											"currentkm"              => "0",
											"currentpositioncoord"   => "-6.2915165,106.9638034",
											"currentpositionaddress" => "Jalan Raya Swatantra Kav.4 No.71 Pekayon, RT.004/RW.004, Jatirasa, Kec. Jatiasih, Kota Bks, Jawa Barat 17424",
											"persentbar"             => "100%"
						)
	)
),
"2" => array(
	"vehicle" 							 => "B1623KRM",
	"totalroute"             => "4",
	"geofence" 							 => array(
															"Lacak Mobil",
															"Naga Pekayon",
															"Mega Mall Bekasi",
															"Lacak Mobil"
														),
	"data"  => array(
					"0" => array(
									"geofence" 							 => "Lacak Mobil",
									"status"                 => "1",
									"kmtonextgeofence"       => "12",
									"currentkm"              => "0",
									"currentpositioncoord"   => "-6.26288,106.9850633",
									"currentpositionaddress" => "Mal Pekayon, Jl. Raya Pekayon No.29, RT.001/RW.020, Pekayon Jaya, Kec. Bekasi Sel., Kota Bks, Jawa Barat 17148",
									"persentbar"             => "100%"
								),
					"1" => array(
									"geofence"               => "Naga Pekayon",
									"status"                 => "1",
									"kmtonextgeofence"       => "80",
									"currentkm"              => "0",
									"currentpositioncoord"   => "-6.2494045,106.9901368",
									"currentpositionaddress" => "Jl. Jend. Ahmad Yani, RT.004/RW.001, Marga Jaya, Kec. Bekasi Bar., Kota Bks, Jawa Barat 17141",
									"persentbar"             => "100%"
							),
					"2" => array(
									"geofence"               => "Mega Mall Bekasi",
									"status"                 => "1",
									"kmtonextgeofence"       => "4",
									"currentkm"              => "0",
									"currentpositioncoord"   => "-6.2494045,106.9901368",
									"currentpositionaddress" => "Bks Cyber Park Kayuringin Jaya Kec. Bekasi Sel. Kota Bks Jawa Barat",
									"persentbar"             => "100%"
							),
					"3" => array(
										"geofence"               => "Lacak Mobil",
										"status"                 => "1",
										"kmtonextgeofence"       => "12",
										"currentkm"              => "0",
										"currentpositioncoord"   => "-6.2915165,106.9638034",
										"currentpositionaddress" => "Jalan Raya Swatantra Kav.4 No.71 Pekayon, RT.004/RW.004, Jatirasa, Kec. Jatiasih, Kota Bks, Jawa Barat 17424",
										"persentbar"             => "100%"
					)
)
),
"3" => array(
"vehicle" 							 => "B1623KRM",
"totalroute"             => "5",
"geofence" 							 => array(
														"Lacak Mobil",
														"Naga Pekayon",
														"Mega Mall Bekasi",
														"Bekasi Cyber Park",
														"Lacak Mobil"
													),
"data"  => array(
				"0" => array(
								"geofence" 							 => "Lacak Mobil",
								"status"                 => "1",
								"kmtonextgeofence"       => "12",
								"currentkm"              => "0",
								"currentpositioncoord"   => "-6.26288,106.9850633",
								"currentpositionaddress" => "Mal Pekayon, Jl. Raya Pekayon No.29, RT.001/RW.020, Pekayon Jaya, Kec. Bekasi Sel., Kota Bks, Jawa Barat 17148",
								"persentbar"             => "100%"
							),
				"1" => array(
								"geofence"               => "Naga Pekayon",
								"status"                 => "1",
								"kmtonextgeofence"       => "80",
								"currentkm"              => "0",
								"currentpositioncoord"   => "-6.2494045,106.9901368",
								"currentpositionaddress" => "Jl. Jend. Ahmad Yani, RT.004/RW.001, Marga Jaya, Kec. Bekasi Bar., Kota Bks, Jawa Barat 17141",
								"persentbar"             => "100%"
						),
				"2" => array(
								"geofence"               => "Mega Mall Bekasi",
								"status"                 => "1",
								"kmtonextgeofence"       => "4",
								"currentkm"              => "0",
								"currentpositioncoord"   => "-6.2494045,106.9901368",
								"currentpositionaddress" => "Bks Cyber Park Kayuringin Jaya Kec. Bekasi Sel. Kota Bks Jawa Barat",
								"persentbar"             => "100%"
						),
				"3" => array(
									"geofence"               => "Bekasi Cyber Park",
									"status"                 => "1",
									"kmtonextgeofence"       => "2",
									"currentkm"              => "0",
									"currentpositioncoord"   => "-6.2259813,106.9988616",
									"currentpositionaddress" => "Jl. Bulevar Ahmad Yani, RT.006/RW.002, Marga Mulya, Kec. Bekasi Utara, Kota Bks, Jawa Barat 17142",
									"persentbar"             => "100%"
				),
				"4" => array(
									"geofence"               => "Lacak Mobil",
									"status"                 => "1",
									"kmtonextgeofence"       => "12",
									"currentkm"              => "0",
									"currentpositioncoord"   => "-6.2915165,106.9638034",
									"currentpositionaddress" => "Jalan Raya Swatantra Kav.4 No.71 Pekayon, RT.004/RW.004, Jatirasa, Kec. Jatiasih, Kota Bks, Jawa Barat 17424",
									"persentbar"             => "100%"
				)
)
),
"4" => array(
"vehicle" 							 => "B1624KRM",
"totalroute"             => "6",
"geofence" 							 => array(
														"Lacak Mobil",
														"Naga Pekayon",
														"Mega Mall Bekasi",
														"Bekasi Cyber Park",
														"Summarecon Mall Bekasi",
														"Lacak Mobil"
													),
"data"  => array(
				"0" => array(
								"geofence" 							 => "Lacak Mobil",
								"status"                 => "1",
								"kmtonextgeofence"       => "12",
								"currentkm"              => "0",
								"currentpositioncoord"   => "-6.26288,106.9850633",
								"currentpositionaddress" => "Mal Pekayon, Jl. Raya Pekayon No.29, RT.001/RW.020, Pekayon Jaya, Kec. Bekasi Sel., Kota Bks, Jawa Barat 17148",
								"persentbar"             => "100%"
							),
				"1" => array(
								"geofence"               => "Naga Pekayon",
								"status"                 => "1",
								"kmtonextgeofence"       => "80",
								"currentkm"              => "0",
								"currentpositioncoord"   => "-6.2494045,106.9901368",
								"currentpositionaddress" => "Jl. Jend. Ahmad Yani, RT.004/RW.001, Marga Jaya, Kec. Bekasi Bar., Kota Bks, Jawa Barat 17141",
								"persentbar"             => "100%"
						),
				"2" => array(
								"geofence"               => "Mega Mall Bekasi",
								"status"                 => "1",
								"kmtonextgeofence"       => "4",
								"currentkm"              => "0",
								"currentpositioncoord"   => "-6.2494045,106.9901368",
								"currentpositionaddress" => "Bks Cyber Park Kayuringin Jaya Kec. Bekasi Sel. Kota Bks Jawa Barat",
								"persentbar"             => "100%"
						),
				"3" => array(
									"geofence"               => "Bekasi Cyber Park",
									"status"                 => "1",
									"kmtonextgeofence"       => "2",
									"currentkm"              => "0",
									"currentpositioncoord"   => "-6.2259813,106.9988616",
									"currentpositionaddress" => "Jl. Bulevar Ahmad Yani, RT.006/RW.002, Marga Mulya, Kec. Bekasi Utara, Kota Bks, Jawa Barat 17142",
									"persentbar"             => "100%"
				),
				"4" => array(
									"geofence"               => "Summarecon Mall Bekasi",
									"status"                 => "1",
									"kmtonextgeofence"       => "2",
									"currentkm"              => "0",
									"currentpositioncoord"   => "-6.26288,106.9850633",
									"currentpositionaddress" => "Mal Pekayon, Jl. Raya Pekayon No.29, RT.001/RW.020, Pekayon Jaya, Kec. Bekasi Sel., Kota Bks, Jawa Barat 17148",
									"persentbar"             => "100%"
				),
				"5" => array(
									"geofence"               => "Lacak Mobil",
									"status"                 => "1",
									"kmtonextgeofence"       => "12",
									"currentkm"              => "0",
									"currentpositioncoord"   => "-6.2915165,106.9638034",
									"currentpositionaddress" => "Jalan Raya Swatantra Kav.4 No.71 Pekayon, RT.004/RW.004, Jatirasa, Kec. Jatiasih, Kota Bks, Jawa Barat 17424",
									"persentbar"             => "100%"
				)
)
)
);

	$datamobil = array();
	for ($i=0; $i < sizeof($data); $i++) {
		array_push($datamobil, array(
			"vehicle" => $data[$i]['vehicle']
		));
	}
	$callback['data']      = $datamobil;

	// echo "<pre>";
	// var_dump($callback['data']);die();
	// echo "<pre>";
	echo json_encode($callback);
}

function getdatabyvehicleno(){
	$this->params['code_view_menu'] = "monitor";
	$vehicleno                      = $this->input->post("vehicleno");

	$data = array(
			"0" => array(
				"vehicle"    => "B1621KRM",
				"totalroute" => "6",
				"geofence"   => array(
													"Lacak Mobil",
													"Naga Pekayon",
													"Mega Mall Bekasi",
													"Bekasi Cyber Park",
													"Summarecon Mall Bekasi",
													"Lacak Mobil"
												),
				"data"  => array(
								"0" => array(
												"geofence"               => "Lacak Mobil",
												"status"                 => "1",
												"kmtonextgeofence"       => "15",
												"currentkm"              => "0",
												"currentpositioncoord"   => "-6.26288,106.9850633",
												"currentpositionaddress" => "Mal Pekayon, Jl. Raya Pekayon No.29, RT.001/RW.020, Pekayon Jaya, Kec. Bekasi Sel., Kota Bks, Jawa Barat 17148",
												"persentbar"             => "100%"
											),
								"1" => array(
												"geofence"               => "Naga Pekayon",
												"status"                 => "1",
												"kmtonextgeofence"       => "25",
												"currentkm"              => "0",
												"currentpositioncoord"   => "-6.2494045,106.9901368",
												"currentpositionaddress" => "Jl. Jend. Ahmad Yani, RT.004/RW.001, Marga Jaya, Kec. Bekasi Bar., Kota Bks, Jawa Barat 17141",
												"persentbar"             => "100%"
										)
		)
	),
	"1" => array(
		"vehicle"    => "B1622KRM",
		"totalroute" => "5",
		"geofence"   => array(
											"Lacak Mobil",
											"Naga Pekayon",
											"Mega Mall Bekasi",
											"Bekasi Cyber Park",
											"Lacak Mobil"
										),
		"data"  => array(
						"0" => array(
										"geofence"               => "Lacak Mobil",
										"status"                 => "1",
										"kmtonextgeofence"       => "20",
										"currentkm"              => "0",
										"currentpositioncoord"   => "-6.26288,106.9850633",
										"currentpositionaddress" => "Mal Pekayon, Jl. Raya Pekayon No.29, RT.001/RW.020, Pekayon Jaya, Kec. Bekasi Sel., Kota Bks, Jawa Barat 17148",
										"persentbar"             => "100%"
									),
						"1" => array(
										"geofence"               => "Naga Pekayon",
										"status"                 => "0",
										"kmtonextgeofence"       => "15",
										"currentkm"              => "10",
										"currentpositioncoord"   => "-6.2494045,106.9901368",
										"currentpositionaddress" => "Jl. Jend. Ahmad Yani, RT.004/RW.001, Marga Jaya, Kec. Bekasi Bar., Kota Bks, Jawa Barat 17141",
										"persentbar"             => "100%"
								)
)
),
"2" => array(
	"vehicle"    => "B1623KRM",
	"totalroute" => "4",
	"geofence"   => array(
										"Lacak Mobil",
										"Naga Pekayon",
										"Mega Mall Bekasi",
										"Lacak Mobil"
									),
	"data"  => array(
					"0" => array(
									"geofence"               => "Lacak Mobil",
									"status"                 => "1",
									"kmtonextgeofence"       => "15",
									"currentkm"              => "0",
									"currentpositioncoord"   => "-6.26288,106.9850633",
									"currentpositionaddress" => "Mal Pekayon, Jl. Raya Pekayon No.29, RT.001/RW.020, Pekayon Jaya, Kec. Bekasi Sel., Kota Bks, Jawa Barat 17148",
									"persentbar"             => "100%"
								),
					"1" => array(
									"geofence"               => "Naga Pekayon",
									"status"                 => "1",
									"kmtonextgeofence"       => "25",
									"currentkm"              => "0",
									"currentpositioncoord"   => "-6.2494045,106.9901368",
									"currentpositionaddress" => "Jl. Jend. Ahmad Yani, RT.004/RW.001, Marga Jaya, Kec. Bekasi Bar., Kota Bks, Jawa Barat 17141",
									"persentbar"             => "100%"
							)
)
),
"3" => array(
	"vehicle"    => "B1624KRM",
	"totalroute" => "5",
	"geofence"   => array(
										"Lacak Mobil",
										"Naga Pekayon",
										"Mega Mall Bekasi",
										"Bekasi Cyber Park",
										"Lacak Mobil"
									),
	"data"  => array(
					"0" => array(
									"geofence"               => "Lacak Mobil",
									"status"                 => "1",
									"kmtonextgeofence"       => "30",
									"currentkm"              => "0",
									"currentpositioncoord"   => "-6.26288,106.9850633",
									"currentpositionaddress" => "Mal Pekayon, Jl. Raya Pekayon No.29, RT.001/RW.020, Pekayon Jaya, Kec. Bekasi Sel., Kota Bks, Jawa Barat 17148",
									"persentbar"             => "100%"
								),
					"1" => array(
									"geofence"               => "Naga Pekayon",
									"status"                 => "1",
									"kmtonextgeofence"       => "30",
									"currentkm"              => "0",
									"currentpositioncoord"   => "-6.2494045,106.9901368",
									"currentpositionaddress" => "Jl. Jend. Ahmad Yani, RT.004/RW.001, Marga Jaya, Kec. Bekasi Bar., Kota Bks, Jawa Barat 17141",
									"persentbar"             => "100%"
							)
)
),
"4" => array(
	"vehicle"    => "B1625KRM",
	"totalroute" => "6",
	"geofence"   => array(
										"Lacak Mobil",
										"Naga Pekayon",
										"Mega Mall Bekasi",
										"Bekasi Cyber Park",
										"Summarecon Mall Bekasi",
										"Lacak Mobil"
									),
	"data"  => array(
					"0" => array(
									"geofence"               => "Lacak Mobil",
									"status"                 => "1",
									"kmtonextgeofence"       => "35",
									"currentkm"              => "0",
									"currentpositioncoord"   => "-6.26288,106.9850633",
									"currentpositionaddress" => "Mal Pekayon, Jl. Raya Pekayon No.29, RT.001/RW.020, Pekayon Jaya, Kec. Bekasi Sel., Kota Bks, Jawa Barat 17148",
									"persentbar"             => "100%"
								),
					"1" => array(
									"geofence"               => "Naga Pekayon",
									"status"                 => "0",
									"kmtonextgeofence"       => "40",
									"currentkm"              => "30",
									"currentpositioncoord"   => "-6.2494045,106.9901368",
									"currentpositionaddress" => "Jl. Jend. Ahmad Yani, RT.004/RW.001, Marga Jaya, Kec. Bekasi Bar., Kota Bks, Jawa Barat 17141",
									"persentbar"             => "100%"
							)
)
)
);

	$datamobilnya = array();
	for ($i=0; $i < sizeof($data); $i++) {
		if ($vehicleno == $data[$i]['vehicle']) {
			array_push($datamobilnya, array(
				"data"             => $data[$i]
			));
		}
	}

	$callback['data'] = $datamobilnya;

	// echo "<pre>";
	// var_dump($datamobilnya);die();
	// echo "<pre>";
	echo json_encode($callback);
}

function getdatabyvehicleno2(){
	$this->params['code_view_menu'] = "monitor";
	$vehicleno                      = $this->input->post("vehicleno");

	$data = array(
			"0" => array(
				"vehicle"    => "B1621KRM",
				"totalroute" => "6",
				"geofence"   => array(
													"Lacak Mobil",
													"Naga Pekayon",
													"Mega Mall Bekasi",
													"Bekasi Cyber Park",
													"Summarecon Mall Bekasi",
													"Lacak Mobil"
												),
				"data"  => array(
								"0" => array(
												"geofence"               => "Lacak Mobil",
												"status"                 => "1",
												"kmtonextgeofence"       => "15",
												"currentkm"              => "0",
												"kmbeforethis" 					 => "20",
												"currentpositioncoord"   => "-6.26288,106.9850633",
												"currentpositionaddress" => "Mal Pekayon, Jl. Raya Pekayon No.29, RT.001/RW.020, Pekayon Jaya, Kec. Bekasi Sel., Kota Bks, Jawa Barat 17148",
												"persentbar"             => "100%"
											),
								"1" => array(
												"geofence"               => "Naga Pekayon",
												"status"                 => "1",
												"kmtonextgeofence"       => "25",
												"currentkm"              => "0",
												"kmbeforethis" 					 => "20",
												"currentpositioncoord"   => "-6.2494045,106.9901368",
												"currentpositionaddress" => "Jl. Jend. Ahmad Yani, RT.004/RW.001, Marga Jaya, Kec. Bekasi Bar., Kota Bks, Jawa Barat 17141",
												"persentbar"             => "100%"
										),
								"2" => array(
												"geofence"               => "Mega Mall Bekasi",
												"status"                 => "1",
												"kmtonextgeofence"       => "25",
												"currentkm"              => "0",
												"kmbeforethis" 					 => "20",
												"currentpositioncoord"   => "-6.2494045,106.9901368",
												"currentpositionaddress" => "Bks Cyber Park Kayuringin Jaya Kec. Bekasi Sel. Kota Bks Jawa Barat",
												"persentbar"             => "100%"
										),
								"3" => array(
												"geofence"               => "Bekasi Cyber Park",
												"status"                 => "0",
												"kmtonextgeofence"       => "20",
												"currentkm"              => "5",
												"kmbeforethis" 					 => "20",
												"currentpositioncoord"   => "-6.2259813,106.9988616",
												"currentpositionaddress" => "Jl. Bulevar Ahmad Yani, RT.006/RW.002, Marga Mulya, Kec. Bekasi Utara, Kota Bks, Jawa Barat 17142",
												"persentbar"             => "100%"
										)
		)
	),
	"1" => array(
		"vehicle"    => "B1622KRM",
		"totalroute" => "5",
		"geofence"   => array(
											"Lacak Mobil",
											"Naga Pekayon",
											"Mega Mall Bekasi",
											"Bekasi Cyber Park",
											"Lacak Mobil"
										),
		"data"  => array(
						"0" => array(
										"geofence"               => "Lacak Mobil",
										"status"                 => "1",
										"kmtonextgeofence"       => "20",
										"currentkm"              => "0",
										"kmbeforethis" 					 => "20",
										"currentpositioncoord"   => "-6.26288,106.9850633",
										"currentpositionaddress" => "Mal Pekayon, Jl. Raya Pekayon No.29, RT.001/RW.020, Pekayon Jaya, Kec. Bekasi Sel., Kota Bks, Jawa Barat 17148",
										"persentbar"             => "100%"
									),
						"1" => array(
										"geofence"               => "Naga Pekayon",
										"status"                 => "0",
										"kmtonextgeofence"       => "15",
										"currentkm"              => "5",
										"kmbeforethis" 					 => "20",
										"currentpositioncoord"   => "-6.2494045,106.9901368",
										"currentpositionaddress" => "Jl. Jend. Ahmad Yani, RT.004/RW.001, Marga Jaya, Kec. Bekasi Bar., Kota Bks, Jawa Barat 17141",
										"persentbar"             => "100%"
								),
						"2" => array(
										"geofence"               => "Mega Mall Bekasi",
										"status"                 => "0",
										"kmtonextgeofence"       => "25",
										"currentkm"              => "10",
										"kmbeforethis" 					 => "20",
										"currentpositioncoord"   => "-6.2494045,106.9901368",
										"currentpositionaddress" => "Bks Cyber Park Kayuringin Jaya Kec. Bekasi Sel. Kota Bks Jawa Barat",
										"persentbar"             => "100%"
								)
)
),
"2" => array(
	"vehicle"    => "B1623KRM",
	"totalroute" => "4",
	"geofence"   => array(
										"Lacak Mobil",
										"Naga Pekayon",
										"Mega Mall Bekasi",
										"Lacak Mobil"
									),
	"data"  => array(
					"0" => array(
									"geofence"               => "Lacak Mobil",
									"status"                 => "1",
									"kmtonextgeofence"       => "15",
									"currentkm"              => "0",
									"kmbeforethis" 					 => "20",
									"currentpositioncoord"   => "-6.26288,106.9850633",
									"currentpositionaddress" => "Mal Pekayon, Jl. Raya Pekayon No.29, RT.001/RW.020, Pekayon Jaya, Kec. Bekasi Sel., Kota Bks, Jawa Barat 17148",
									"persentbar"             => "100%"
								),
					"1" => array(
									"geofence"               => "Naga Pekayon",
									"status"                 => "1",
									"kmtonextgeofence"       => "25",
									"currentkm"              => "0",
									"kmbeforethis" 					 => "20",
									"currentpositioncoord"   => "-6.2494045,106.9901368",
									"currentpositionaddress" => "Jl. Jend. Ahmad Yani, RT.004/RW.001, Marga Jaya, Kec. Bekasi Bar., Kota Bks, Jawa Barat 17141",
									"persentbar"             => "100%"
							)
)
),
"3" => array(
	"vehicle"    => "B1624KRM",
	"totalroute" => "5",
	"geofence"   => array(
										"Lacak Mobil",
										"Naga Pekayon",
										"Mega Mall Bekasi",
										"Bekasi Cyber Park",
										"Lacak Mobil"
									),
	"data"  => array(
					"0" => array(
									"geofence"               => "Lacak Mobil",
									"status"                 => "1",
									"kmtonextgeofence"       => "30",
									"currentkm"              => "0",
									"kmbeforethis" 					 => "20",
									"currentpositioncoord"   => "-6.26288,106.9850633",
									"currentpositionaddress" => "Mal Pekayon, Jl. Raya Pekayon No.29, RT.001/RW.020, Pekayon Jaya, Kec. Bekasi Sel., Kota Bks, Jawa Barat 17148",
									"persentbar"             => "100%"
								),
					"1" => array(
									"geofence"               => "Naga Pekayon",
									"status"                 => "1",
									"kmtonextgeofence"       => "30",
									"currentkm"              => "0",
									"kmbeforethis" 					 => "20",
									"currentpositioncoord"   => "-6.2494045,106.9901368",
									"currentpositionaddress" => "Jl. Jend. Ahmad Yani, RT.004/RW.001, Marga Jaya, Kec. Bekasi Bar., Kota Bks, Jawa Barat 17141",
									"persentbar"             => "100%"
							)
)
),
"4" => array(
	"vehicle"    => "B1625KRM",
	"totalroute" => "6",
	"geofence"   => array(
										"Lacak Mobil",
										"Naga Pekayon",
										"Mega Mall Bekasi",
										"Bekasi Cyber Park",
										"Summarecon Mall Bekasi",
										"Lacak Mobil"
									),
	"data"  => array(
					"0" => array(
									"geofence"               => "Lacak Mobil",
									"status"                 => "1",
									"kmtonextgeofence"       => "35",
									"currentkm"              => "0",
									"kmbeforethis" 					 => "20",
									"currentpositioncoord"   => "-6.26288,106.9850633",
									"currentpositionaddress" => "Mal Pekayon, Jl. Raya Pekayon No.29, RT.001/RW.020, Pekayon Jaya, Kec. Bekasi Sel., Kota Bks, Jawa Barat 17148",
									"persentbar"             => "100%"
								),
					"1" => array(
									"geofence"               => "Naga Pekayon",
									"status"                 => "1",
									"kmtonextgeofence"       => "40",
									"currentkm"              => "0",
									"kmbeforethis" 					 => "20",
									"currentpositioncoord"   => "-6.2494045,106.9901368",
									"currentpositionaddress" => "Jl. Jend. Ahmad Yani, RT.004/RW.001, Marga Jaya, Kec. Bekasi Bar., Kota Bks, Jawa Barat 17141",
									"persentbar"             => "100%"
							)
)
)
);

	$datamobilnya = array();
	for ($i=0; $i < sizeof($data); $i++) {
		if ($vehicleno == $data[$i]['vehicle']) {
			array_push($datamobilnya, array(
				"data"             => $data[$i]
			));
		}
	}

	$callback['data'] = $datamobilnya;

	// echo "<pre>";
	// var_dump($datamobilnya);die();
	// echo "<pre>";
	echo json_encode($callback);
}

function getalldata(){
	$this->params['code_view_menu'] = "monitor";
	$vehicleno                      = $this->input->post("vehicleno");

	$data = array(
			"0" => array(
				"vehicle"    => "B1621KRM",
				"totalroute" => "6",
				"geofence"   => array(
													"Lacak Mobil",
													"Naga Pekayon",
													"Mega Mall Bekasi",
													"Bekasi Cyber Park",
													"Summarecon Mall Bekasi",
													"Lacak Mobil"
												),
				"data"  => array(
								"0" => array(
												"geofence"               => "Lacak Mobil",
												"status"                 => "0",
												"kmtonextgeofence"       => "15",
												"currentkm"              => "5",
												"currentpositioncoord"   => "-6.26288,106.9850633",
												"currentpositionaddress" => "Mal Pekayon, Jl. Raya Pekayon No.29, RT.001/RW.020, Pekayon Jaya, Kec. Bekasi Sel., Kota Bks, Jawa Barat 17148",
												"persentbar"             => "100%"
											)
								// "1" => array(
								// 				"geofence"               => "Naga Pekayon",
								// 				"status"                 => "0",
								// 				"kmtonextgeofence"       => "25",
								// 				"currentkm"              => "15",
								// 				"currentpositioncoord"   => "-6.2494045,106.9901368",
								// 				"currentpositionaddress" => "Jl. Jend. Ahmad Yani, RT.004/RW.001, Marga Jaya, Kec. Bekasi Bar., Kota Bks, Jawa Barat 17141",
								// 				"persentbar"             => "100%"
								// 		)
		)
	),
	"1" => array(
		"vehicle"    => "B1622KRM",
		"totalroute" => "5",
		"geofence"   => array(
											"Lacak Mobil",
											"Naga Pekayon",
											"Mega Mall Bekasi",
											"Bekasi Cyber Park",
											"Lacak Mobil"
										),
		"data"  => array(
						"0" => array(
										"geofence"               => "Lacak Mobil",
										"status"                 => "1",
										"kmtonextgeofence"       => "20",
										"currentkm"              => "0",
										"currentpositioncoord"   => "-6.26288,106.9850633",
										"currentpositionaddress" => "Mal Pekayon, Jl. Raya Pekayon No.29, RT.001/RW.020, Pekayon Jaya, Kec. Bekasi Sel., Kota Bks, Jawa Barat 17148",
										"persentbar"             => "100%"
									),
						"1" => array(
										"geofence"               => "Naga Pekayon",
										"status"                 => "1",
										"kmtonextgeofence"       => "15",
										"currentkm"              => "0",
										"currentpositioncoord"   => "-6.2494045,106.9901368",
										"currentpositionaddress" => "Jl. Jend. Ahmad Yani, RT.004/RW.001, Marga Jaya, Kec. Bekasi Bar., Kota Bks, Jawa Barat 17141",
										"persentbar"             => "100%"
								)
)
),
"2" => array(
	"vehicle"    => "B1623KRM",
	"totalroute" => "4",
	"geofence"   => array(
										"Lacak Mobil",
										"Naga Pekayon",
										"Mega Mall Bekasi",
										"Lacak Mobil"
									),
	"data"  => array(
					"0" => array(
									"geofence"               => "Lacak Mobil",
									"status"                 => "1",
									"kmtonextgeofence"       => "15",
									"currentkm"              => "0",
									"currentpositioncoord"   => "-6.26288,106.9850633",
									"currentpositionaddress" => "Mal Pekayon, Jl. Raya Pekayon No.29, RT.001/RW.020, Pekayon Jaya, Kec. Bekasi Sel., Kota Bks, Jawa Barat 17148",
									"persentbar"             => "100%"
								),
					"1" => array(
									"geofence"               => "Naga Pekayon",
									"status"                 => "1",
									"kmtonextgeofence"       => "25",
									"currentkm"              => "0",
									"currentpositioncoord"   => "-6.2494045,106.9901368",
									"currentpositionaddress" => "Jl. Jend. Ahmad Yani, RT.004/RW.001, Marga Jaya, Kec. Bekasi Bar., Kota Bks, Jawa Barat 17141",
									"persentbar"             => "100%"
							)
)
),
"3" => array(
	"vehicle"    => "B1624KRM",
	"totalroute" => "5",
	"geofence"   => array(
										"Lacak Mobil",
										"Naga Pekayon",
										"Mega Mall Bekasi",
										"Bekasi Cyber Park",
										"Lacak Mobil"
									),
	"data"  => array(
					"0" => array(
									"geofence"               => "Lacak Mobil",
									"status"                 => "1",
									"kmtonextgeofence"       => "30",
									"currentkm"              => "0",
									"currentpositioncoord"   => "-6.26288,106.9850633",
									"currentpositionaddress" => "Mal Pekayon, Jl. Raya Pekayon No.29, RT.001/RW.020, Pekayon Jaya, Kec. Bekasi Sel., Kota Bks, Jawa Barat 17148",
									"persentbar"             => "100%"
								),
					"1" => array(
									"geofence"               => "Naga Pekayon",
									"status"                 => "1",
									"kmtonextgeofence"       => "30",
									"currentkm"              => "0",
									"currentpositioncoord"   => "-6.2494045,106.9901368",
									"currentpositionaddress" => "Jl. Jend. Ahmad Yani, RT.004/RW.001, Marga Jaya, Kec. Bekasi Bar., Kota Bks, Jawa Barat 17141",
									"persentbar"             => "100%"
							)
)
),
"4" => array(
	"vehicle"    => "B1625KRM",
	"totalroute" => "6",
	"geofence"   => array(
										"Lacak Mobil",
										"Naga Pekayon",
										"Mega Mall Bekasi",
										"Bekasi Cyber Park",
										"Summarecon Mall Bekasi",
										"Lacak Mobil"
									),
	"data"  => array(
					"0" => array(
									"geofence"               => "Lacak Mobil",
									"status"                 => "1",
									"kmtonextgeofence"       => "35",
									"currentkm"              => "0",
									"currentpositioncoord"   => "-6.26288,106.9850633",
									"currentpositionaddress" => "Mal Pekayon, Jl. Raya Pekayon No.29, RT.001/RW.020, Pekayon Jaya, Kec. Bekasi Sel., Kota Bks, Jawa Barat 17148",
									"persentbar"             => "100%"
								),
					"1" => array(
									"geofence"               => "Naga Pekayon",
									"status"                 => "1",
									"kmtonextgeofence"       => "40",
									"currentkm"              => "0",
									"currentpositioncoord"   => "-6.2494045,106.9901368",
									"currentpositionaddress" => "Jl. Jend. Ahmad Yani, RT.004/RW.001, Marga Jaya, Kec. Bekasi Bar., Kota Bks, Jawa Barat 17141",
									"persentbar"             => "100%"
							)
)
)
);

	$callback['data'] = $data;

	// echo "<pre>";
	// var_dump($datamobilnya);die();
	// echo "<pre>";
	echo json_encode($callback);
}


}
