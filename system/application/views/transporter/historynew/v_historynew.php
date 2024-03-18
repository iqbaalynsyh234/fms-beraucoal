
  <script src="<?php echo base_url()?>assets/js/assetsbaru/jquery.min.js"></script>
  <!-- <link rel="stylesheet" href="<?php echo base_url()?>assets/js/assetsbaru/jquery-ui.css"> -->
  <script src="<?php echo base_url()?>assets/js/assetsbaru/jquery-ui.min.js"></script>
  <script src="<?php echo base_url()?>assets/js/assetsbaru/chosen.jquery.min.js"></script>
  <link rel="stylesheet" href="<?php echo base_url()?>assets/js/assetsbaru/chosen.min.css">
  <script src="<?php echo base_url();?>assets/js/jsblong/jquery.table2excel.js"></script>

	<!-- <script src="<?php echo base_url();?>assets/js/v3_epoly.js" type="text/javascript"></script> -->
<style>
form select{
	position : relative;
}
/* #map_canvas { position:absolute; top:420px; width:95%; height:80%; } */
#history_result { position:absolute; top:412px; width: 97%;}
#info { position:absolute; top:412px; }
.buttonanimation {
    background-color: #4CAF50; /* Green */
    border: none;
    color: white;
    padding: 15px 15px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 10px;
}
</style>

<style media="screen">
	.ui-datepicker{
		z-index: 9999 !important;
	}

</style>

<script>
	var geocoder;
	var map;
	var lat_lng = [];
	var lat_lng_animated = [];
	var source_destination = [];
	var route_time = [];
	var t=0; var t2 = 0;
	var isPaused = false;

	var directionDisplay;
	var directionsService;
	var stepDisplay;
	var markerArray = [];
	var position;
	var marker = null;
	var polyline = null;
	var poly2 = null;
	var speed = 0.000005, wait = 1;
	var infowindow = null;

	var myPano;
	var panoClient;
	var nextPanoId;
	var timerHandle = null;
	var currentDistance;
	var waypoints = [];

	var xx = 0;
	var htmlreport;

	var step = 10; // 1; // metres
	var tick = 100; // milliseconds
	var eol;
	var k=0;
	var stepnum=0;
	var speed = "";
	var lastVertex = 1;

	var initok = false;
  var isgeofenceclicked = 0;


	jQuery.maxZIndex = jQuery.fn.maxZIndex = function(opt) {
	    var def = { inc: 10, group: "*" };
	    jQuery.extend(def, opt);
	    var zmax = 0;
	    jQuery(def.group).each(function() {
	        var cur = parseInt(jQuery(this).css('z-index'));
	        zmax = cur > zmax ? cur : zmax;
	    });
	    if (!this.jquery)
	        return zmax;

	    return this.each(function() {
	        zmax += def.inc;
	        jQuery(this).css("z-index", zmax);
	    });
	}

	jQuery(document).ready(
		function()
		{
				jQuery("#sdate").datepicker(
				{
							dateFormat: 'yy/mm/dd'
						, 	showOn: 'button'
						, 	buttonImage: '<?=base_url()?>assets/images/calendar.gif'
						, 	buttonImageOnly: true
						,	beforeShow:
								function()
								{
									jQuery('#ui-datepicker-div').maxZIndex();
								}
				}
				);

        jQuery("#edate").datepicker(
				{
							dateFormat: 'yy/mm/dd'
						, 	showOn: 'button'
						, 	buttonImage: '<?=base_url()?>assets/images/calendar.gif'
						, 	buttonImageOnly: true
						,	beforeShow:
								function()
								{
									jQuery('#ui-datepicker-div').maxZIndex();
								}
				}
				);

				showclock();


    			// jQuery("#export_xcel").click(function()
    			// {
    			// 	window.open('data:application/vnd.ms-excel,' + encodeURIComponent(jQuery('#isexport_xcel').html()));
    			// });
				// jQuery("#map_canvas").show();
		}
	);

  function fnExcelReport(){
     var tab_text="<table border='2px'><tr bgcolor='#87AFC6'>";
     var textRange; var j=0;
        tab = document.getElementById('stprint'); // id of table


        for(j = 0 ; j < tab.rows.length ; j++)
        {
              tab_text=tab_text+tab.rows[j].innerHTML+"</tr>";
              //tab_text=tab_text+"</tr>";
        }

        tab_text=tab_text+"</table>";
        tab_text= tab_text.replace(/<A[^>]*>|<\/A>/g, "");//remove if u want links in your table
        tab_text= tab_text.replace(/<img[^>]*>/gi,""); // remove if u want images in your table
                    tab_text= tab_text.replace(/<input[^>]*>|<\/input>/gi, ""); // reomves input params

       var ua = window.navigator.userAgent;
       var msie = ua.indexOf("MSIE ");

         if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./))      // If Internet Explorer
            {
                   txtArea1.document.open("txt/html","replace");
                   txtArea1.document.write(tab_text);
                   txtArea1.document.close();
                   txtArea1.focus();
                    sa=txtArea1.document.execCommand("SaveAs",true,"Say Thanks to Sumit.xls");
                  }
          else                 //other browser not tested on IE 11
              sa = window.open('data:application/vnd.ms-excel,' + encodeURIComponent(tab_text));
  }

	function frmhistory_onsubmit()
	{
		jQuery("#loader").show();
		jQuery("#history_result").html("");
		jQuery("#history_result").show();
		jQuery("#info").hide();

		step = 10; // 1; // metres
		tick = 100; // milliseconds
		k=0;
		stepnum=0;
		speed = "";
		lastVertex = 1;

		var vehicle = jQuery("#vehicles").val();
		if(vehicle == "")
		{
			alert("Silahkan pilih vehicle terlebih dahulu!");
			jQuery("#loader").hide();
			return false;
		}
		var sdate = jQuery("#sdate").val();
		if(sdate == "")
		{
			alert("Silahkan pilih Date terlebih dahulu!");
			jQuery("#loader").hide();
			return false;
		}
    var edate = jQuery("#edate").val();
		if(edate == "")
		{
			alert("Silahkan pilih Date terlebih dahulu!");
			jQuery("#loader").hide();
			return false;
		}
		var stime = jQuery("#stime").val();
		if(stime == "")
		{
			alert("Silahkan pilih Start Time terlebih dahulu!");
			jQuery("#loader").hide();
			return false;
		}
		var etime = jQuery("#etime").val();
		if(etime == "")
		{
			alert("Silahkan pilih End Time terlebih dahulu!");
			jQuery("#loader").hide();
			return false;
		}
		var limit = jQuery("#limit").val();
		var typehistory;
		var radioValue = $("input[name='typehistory']:checked").val();
		if(radioValue){
		 typehistory = radioValue;
		}

    if($("input[name='typehistory']:checked").val()) {

    }else{
      if (confirm("Silahkan pilih tipe history terlebih dahulu")) {
        window.location = '<?php echo base_url() ?>historynew';
      }
    }

    var detailorsummary;
    var radioValue2 = $("input[name='detail_summary']:checked").val();
    if(radioValue2){
     detailorsummary = radioValue2;
    }

    // JIKA PILIH TABLE DETAIL TANGGAL MAKSIMAL 3 HARI KEBELAKANG
    var nowsdate           = new Date(sdate);
    var now                = new Date();

    var tgl_dikirim        = nowsdate.setDate(nowsdate.getDate());
    var year               = now.getFullYear();
    var Month              = ("0" + (now.getMonth() + 1)).slice(-2);
    var Day                = now.getDate();
    var tgl_dikirim2       = year + "/" + Month + "/" + Day;

    var tgl_tigahariyglalu = now.setDate(now.getDate() - 3);

    console.log("tgl_dikirim : ", tgl_dikirim);
    console.log("sdate : ", sdate);
    console.log("tgl_dikirim2 : ", tgl_dikirim2);
    console.log("tgl_tigahariyglalu : ", tgl_tigahariyglalu);
    console.log("typehistory : ", typehistory);

    if (typehistory == 0) {
      if (tgl_dikirim >= tgl_tigahariyglalu) {
        var htmlreport = "";
    		htmlreport += "<div id='main' style='margin: 20px;width:100%;'>";
    		htmlreport += "<div class='block-border'>";
    		// htmlreport += "<center><br /><br /><input type='button' id='btnresume' onclick='javascript:resumeme();' class='buttonanimation' value='RESUME'>&nbsp;&nbsp;<input type='button' id='btnpause' onclick='javascript:pauseme();' class='buttonanimation' value='PAUSE'>&nbsp;&nbsp;";
    		// htmlreport += "<input type='button' id='btnanimation' onclick='javascript:animation();' class='buttonanimation' value='ANIMATION'></center>"
    		jQuery.post("historynew/searchhistory", {id:vehicle,sdate:sdate,edate:edate,stime:stime,etime:etime,limit:limit, typehistory:typehistory, detailorsummary:detailorsummary},
    		function(r)
    		{
    			jQuery("#loader").hide();
    			console.log("responya : ", r);
    			if (r.m)
    			{
    				alert(r.m);
    				return;
    			}

    			if (r.typehistory == 0) {

    				var datalength = r.data.length;
    				if(datalength > 0)
    				{
              // htmlreport += "<a class='button' href='javascript:void(0);' id='export_xcel'>Export to Excel</a>";
              htmlreport += '<br><a class="button" href="javascript:fnExcelReport();" id="export_xcel">Export to Excel</a>';
              htmlreport += "<div id='isexport_xcel'>";
    					htmlreport += "<table id='stprint' width='100%' cellpadding='3' cellpadding='0' class='table sortable no-margin' style='margin: 3px;'>";
    					htmlreport += "<tr>";
    						htmlreport += "<th>No</th>";
    						htmlreport += "<th>Date</th>";
    						htmlreport += "<th>Position</th>";
    						htmlreport += "<th>Coordinate</th>";
    						htmlreport += "<th>Status</th>";
    						htmlreport += "<th>Speed</th>";
    						htmlreport += "<th>Engine</th>";
    					htmlreport += "<tr>";
    					for(var i=0; i < datalength; i++)
    					{
    						// lat_lng.push(new google.maps.LatLng(r.data[i].gps_latitude_real_fmt, r.data[i].gps_longitude_real_fmt));
    						// lat_lng_animated.push(new google.maps.LatLng(r.data[i].gps_latitude_real_fmt, r.data[i].gps_longitude_real_fmt));
    						// source_destination.push(r.data[i].georeverse.display_name);
    						// route_time.push(r.data[i].gps_date_fmt+" "+r.data[i].gps_time_fmt);
    	    				var k = i + 1;
    							htmlreport += "<tr>";
    								htmlreport += "<td>"+k +"</td>";
    								htmlreport += "<td>"+r.data[i].gps_date_fmt+" "+ r.data[i].gps_time_fmt + "</td>";
    								htmlreport += "<td>"+ r.data[i].georeverse.display_name +"</td>";
    								htmlreport += "<td>"+r.data[i].gps_latitude_real_fmt+","+r.data[i].gps_longitude_real_fmt +"</td>";
    								htmlreport += "<td>"+r.data[i].gpstatus +"</td>";
    								htmlreport += "<td>"+r.data[i].gps_speed_fmt+" "+"KpH"+"</td>";
    								htmlreport += "<td>"+r.data[i].status1 +"</td>";
    							htmlreport += "<tr>";

    						// htmlreport += "<tr><td>";
    						// htmlreport += "<b>"+k +".</b>"+ " "+"Date :"+" "+r.data[i].gps_date_fmt+" "+r.data[i].gps_time_fmt+"<br />";
    						// htmlreport += r.data[i].georeverse.display_name+"<br />";
    						// htmlreport += r.data[i].gps_latitude_real_fmt+","+r.data[i].gps_longitude_real_fmt+"<br />";
    						// htmlreport += "Speed : "+" "+r.data[i].gps_speed_fmt+" "+"KpH"+" ";
    						// htmlreport += "Engine : "+" "+r.data[i].status1+" ";
    						// htmlreport += "GPS : "+" "+r.data[i].gpstatus+"<br /><br />";
    						// htmlreport += "</td></tr>";
    					}

    					htmlreport += "</table>";
    					htmlreport += "</div>";

    					// window.localStorage.removeItem("lat_lng");
    					// window.localStorage.removeItem("source_destination");
    					// window.localStorage.removeItem("route_time");
    					// window.localStorage.setItem("lat_lng",JSON.stringify(lat_lng_animated));
    					// window.localStorage.setItem("source_destination",JSON.stringify(source_destination));
    					// window.localStorage.setItem("route_time",JSON.stringify(route_time));

    					jQuery("#history_result").css({"position":"absolute","top":"412px"});
    				}
    				else
    				{
    					htmlreport = "";
    					htmlreport += "<div id='main' style='margin: 20px;width:100%;'>";
    					htmlreport += "<div class='block-border'>";
    					htmlreport += "<center><br /><br />";
    					htmlreport += "<table width='100%' id='notavailable_info'>";
    					htmlreport += "<tr><td>";
    					htmlreport += "DATA NOT AVAILABLE !";
    					htmlreport += "</td></tr>";
    					htmlreport += "</table>";
              jQuery("#history_result").css({});
    					jQuery("#history_result").css({"position":"absolute","top":"400px"});
    				}
    				jQuery("#history_result").html(htmlreport);
    			}else {
    					jQuery("#history_result").css({"position":"absolute","top":"412px"});
    					jQuery("#history_result").html(r.html);
    			}


    		}, "json");
    		return false;
      }else {
        alert("Tanggal Maksimal untuk tipe tabel adalah tiga hari yang lalu");
        jQuery("#loader").hide();
      }
    }else {
      if (sdate == edate) {
        var htmlreport="";
    		htmlreport += "<div id='main' style='margin: 20px;width:100%;'>";
    		htmlreport += "<div class='block-border'>";
    		// htmlreport += "<center><br /><br /><input type='button' id='btnresume' onclick='javascript:resumeme();' class='buttonanimation' value='RESUME'>&nbsp;&nbsp;<input type='button' id='btnpause' onclick='javascript:pauseme();' class='buttonanimation' value='PAUSE'>&nbsp;&nbsp;";
    		// htmlreport += "<input type='button' id='btnanimation' onclick='javascript:animation();' class='buttonanimation' value='ANIMATION'></center>"
    		jQuery.post("historynew/searchhistory", {id:vehicle,sdate:sdate,edate:edate,stime:stime,etime:etime,limit:limit, typehistory:typehistory, detailorsummary:detailorsummary},
    		function(r)
    		{
    			jQuery("#loader").hide();
    			console.log("responya : ", r);
    			if (r.m)
    			{
    				alert(r.m);
    				return;
    			}

    			if (r.typehistory == 0) {

    				var datalength = r.data.length;
    				if(datalength > 0)
    				{
              // htmlreport += "<a class='button' href='javascript:void(0);' id='export_xcel'>Export to Excel</a>";
              htmlreport += '<a class="button" href="javascript:fnExcelReport();" id="export_xcel">Export to Excel</a>';
              htmlreport += "<div id='isexport_xcel'>";
    					htmlreport += "<table id='stprint' width='100%' cellpadding='3' cellpadding='0' class='table sortable no-margin' style='margin: 3px;'>";
    					htmlreport += "<tr>";
    						htmlreport += "<th>No</th>";
    						htmlreport += "<th>Date</th>";
    						htmlreport += "<th>Position</th>";
    						htmlreport += "<th>Coordinate</th>";
    						htmlreport += "<th>Status</th>";
    						htmlreport += "<th>Speed</th>";
    						htmlreport += "<th>Engine</th>";
    					htmlreport += "<tr>";
    					for(var i=0; i < datalength; i++)
    					{
    						// lat_lng.push(new google.maps.LatLng(r.data[i].gps_latitude_real_fmt, r.data[i].gps_longitude_real_fmt));
    						// lat_lng_animated.push(new google.maps.LatLng(r.data[i].gps_latitude_real_fmt, r.data[i].gps_longitude_real_fmt));
    						// source_destination.push(r.data[i].georeverse.display_name);
    						// route_time.push(r.data[i].gps_date_fmt+" "+r.data[i].gps_time_fmt);
    	    				var k = i + 1;
    							htmlreport += "<tr>";
    								htmlreport += "<td>"+k +"</td>";
    								htmlreport += "<td>"+r.data[i].gps_date_fmt+" "+ r.data[i].gps_time_fmt + "</td>";
    								htmlreport += "<td>"+ r.data[i].georeverse.display_name +"</td>";
    								htmlreport += "<td>"+r.data[i].gps_latitude_real_fmt+","+r.data[i].gps_longitude_real_fmt +"</td>";
    								htmlreport += "<td>"+r.data[i].gpstatus +"</td>";
    								htmlreport += "<td>"+r.data[i].gps_speed_fmt+" "+"KpH"+"</td>";
    								htmlreport += "<td>"+r.data[i].status1 +"</td>";
    							htmlreport += "<tr>";

    						// htmlreport += "<tr><td>";
    						// htmlreport += "<b>"+k +".</b>"+ " "+"Date :"+" "+r.data[i].gps_date_fmt+" "+r.data[i].gps_time_fmt+"<br />";
    						// htmlreport += r.data[i].georeverse.display_name+"<br />";
    						// htmlreport += r.data[i].gps_latitude_real_fmt+","+r.data[i].gps_longitude_real_fmt+"<br />";
    						// htmlreport += "Speed : "+" "+r.data[i].gps_speed_fmt+" "+"KpH"+" ";
    						// htmlreport += "Engine : "+" "+r.data[i].status1+" ";
    						// htmlreport += "GPS : "+" "+r.data[i].gpstatus+"<br /><br />";
    						// htmlreport += "</td></tr>";
    					}

    					htmlreport += "</table>";
    					htmlreport += "</div>";

    					// window.localStorage.removeItem("lat_lng");
    					// window.localStorage.removeItem("source_destination");
    					// window.localStorage.removeItem("route_time");
    					// window.localStorage.setItem("lat_lng",JSON.stringify(lat_lng_animated));
    					// window.localStorage.setItem("source_destination",JSON.stringify(source_destination));
    					// window.localStorage.setItem("route_time",JSON.stringify(route_time));

    					jQuery("#history_result").css({"position":"absolute","top":"412px"});
    				}
    				else
    				{
    					htmlreport = "";
    					htmlreport += "<div id='main' style='margin: 20px;width:100%;'>";
    					htmlreport += "<div class='block-border'>";
    					htmlreport += "<center><br /><br />";
    					htmlreport += "<table width='100%' id='notavailable_info'>";
    					htmlreport += "<tr><td>";
    					htmlreport += "DATA NOT AVAILABLE !";
    					htmlreport += "</td></tr>";
    					htmlreport += "</table>";
              jQuery("#history_result").css({});
    					jQuery("#history_result").css({"position":"absolute","top":"400px"});
    				}
    				jQuery("#history_result").html(htmlreport);
    			}else {
    					jQuery("#history_result").css({"position":"absolute","top":"412px"});
    					jQuery("#history_result").html(r.html);
    			}


    		}, "json");
    		return false;
      }else {
        alert("Maps hanya bisa ditampilkan dihari yang sama");
        jQuery("#loader").hide();
      }
    }



    // JIKA PILIH MAPS TANGGAL MAKSIMAL HARI YANG SAMA


	}

 function option_type_history(v) {
   switch (v) {
     case "no":
     isgeofenceclicked = 0;
     jQuery("#detailsummaryforhide").hide();
       break;
     case "yes":
     isgeofenceclicked = 1;
     jQuery("#detailsummaryforhide").show();
       break;
   }
 }

</script>
<div style="position: absolute; margin: 0; padding: 0; z-index: 1000; width: 100%;">
<?=$navigation;?>
	<div id="main" style="margin: 20px;">
		<div class="block-border">
			<form class="block-content form" id="frmhistory">
            <h1><?php echo "History"; ?></h1>
			<table class="tablelist">
				<tr>
					<td style="padding: 8px;">Select Vehicle</td>
					<td>
						<select name="vehicles" id="vehicles" class="chosen">
							<option value="">Select Vehicle</option>
							<?php
								if(isset($vehicles))
								{
								  foreach($vehicles as $v)
								  {
							?>
							<option value="<?php echo $v->vehicle_id?>"><?php echo $v->vehicle_no." - ".$v->vehicle_name; ?></option>
							<?php
								  }
								}
							?>
						</select>
						<font color="red">*</font>
					</td>
				</tr>

        <tr>
          <td style="padding: 8px;">History Type</td>
          <td>
            <input type="radio" name="typehistory" id="typehistory0" value="0" onClick="option_type_history('yes')"> Table
            <input type="radio" name="typehistory" id="typehistory1" value="1" onClick="option_type_history('no')"> Maps
          </td>
        </tr>

        <tr id="detailsummaryforhide" style="display: none;">
          <td style="padding: 8px;">Data Type</td>
          <td>
            <input type="radio" name="detail_summary" id="detail_summary0" value="1" checked> Detail
            <input type="radio" name="detail_summary" id="detail_summary1" value="2"> Summary
          </td>
        </tr>

        <tr id="filterdatestartend">
					<td style="padding: 8px;">Date</td>
					<td>
						<input type='text' readonly name="sdate" id="sdate" class="date-pick" value="<?=date('Y/m/d')?>"  maxlength='10'>
						<select class="textgray" style="font-size: 11px; width: 65px;" id="stime" name="stime">
						                    <option value="00:00">00:00</option>
						                    <option value="01:00">01:00</option>
						                    <option value="02:00">02:00</option>
						                    <option value="03:00">03:00</option>
						                    <option value="04:00">04:00</option>
						                    <option value="05:00">05:00</option>
						                    <option value="06:00">06:00</option>
						                    <option value="07:00">07:00</option>
						                    <option value="08:00">08:00</option>
						                    <option value="09:00">09:00</option>
						                    <option value="10:00">10:00</option>
						                    <option value="11:00">11:00</option>
						                    <option value="12:00">12:00</option>
						                    <option value="13:00">13:00</option>
						                    <option value="14:00">14:00</option>
						                    <option value="15:00">15:00</option>
						                    <option value="16:00">16:00</option>
						                    <option value="17:00">17:00</option>
						                    <option value="18:00">18:00</option>
						                    <option value="19:00">19:00</option>
						                    <option value="20:00">20:00</option>
						                    <option value="21:00">21:00</option>
						                    <option value="22:00">22:00</option>
						                    <option value="23:00">23:00</option>

						             </select>

						~ <input type='text' readonly name="edate" id="edate"  class="date-pick" value="<?=date('Y/m/d')?>"  maxlength='10'>
						<select class="textgray" style="font-size: 11px; width: 65px;" id="etime" name="etime">

						                    <option value="00:59">00:59</option>
						                    <option value="01:59">01:59</option>
						                    <option value="02:59">02:59</option>
						                    <option value="03:59">03:59</option>
						                    <option value="04:59">04:59</option>
						                    <option value="05:59">05:59</option>
						                    <option value="06:59">06:59</option>
						                    <option value="07:59">07:59</option>
						                    <option value="08:59">08:59</option>
						                    <option value="09:59">09:59</option>
						                    <option value="10:59">10:59</option>
						                    <option value="11:59">11:59</option>
						                    <option value="12:59">12:59</option>
						                    <option value="13:59">13:59</option>
						                    <option value="14:59">14:59</option>
						                    <option value="15:59">15:59</option>
						                    <option value="16:59">16:59</option>
						                    <option value="17:59">17:59</option>
						                    <option value="18:59">18:59</option>
						                    <option value="19:59">19:59</option>
						                    <option value="20:59">20:59</option>
						                    <option value="21:59">21:59</option>
						                    <option value="22:59">22:59</option>
						                    <option selected="" value="23:59">23:59</option>
						                </select>
					</td>
        </tr>
				<!-- <tr style="border: 0px;">
					<td width="100" style="border: 0px;">Limit</td>
					<td style="border: 0px;">
						<select name="limit" id="limit">
							<option value="10">10</option>
							<option value="30">30</option>
							<option value="50">50</option>
						</select>
						<font color="red">*</font>
					</td>
				</tr> -->
        <tr>
          <td></td>
					<td  style="padding: 8px;">
						<input type="button" name="btnsave" id="btnsave" onclick="javascript:frmhistory_onsubmit();" value=" Search " />
						<img id="loader" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;">
					</td>
				</tr>
			</table>
			</form>
		</div>
	</div>
</div>
<div style="position: absolute; margin: 0; padding: 0; z-index: 1000; width: 100%;">
	<div id="history_result"></div>
	<div id="info" style="width:97%;"></div>
</div>


<script type="text/javascript">
	$(".chosen").chosen();
</script>
