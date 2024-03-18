 <style>
#map {
	width:100%;
	height:100%;
}
</style>

 <script src="http://maps.google.com/maps?file=api&amp;v=2&amp;sensor=true&amp;key=<?php echo $this->config->item("GOOGLE_MAP_API_KEY"); ?>" type="text/javascript"></script>
 <script type="text/javascript">
  var ipos = 0;
	var m_dpos = 0;
	var y = new Array();
	  <?php 
			$j=count($data);
			$i=$j;?>
	  <?php foreach($data as $lat=>$val) { 
		  foreach($val as $lng=>$val1) {  ?>
	  y[<?= $i ?>] = "<?php echo $lat, "," ,$lng ?>"; 
	  <?php $i--; ?>
	  <?php } 
	   } ?>
  var p_point = new Array()
 
  GPolygon.prototype.Contains = function(point) {
  var j=0;
  var oddNodes = false;
  var x = point.lng();
  var y = point.lat();
  for (var i=0; i < this.getVertexCount(); i++) {
    j++;
    if (j == this.getVertexCount()) {j = 0;}
    if (((this.getVertex(i).lat() < y) && (this.getVertex(j).lat() >= y))
    || ((this.getVertex(j).lat() < y) && (this.getVertex(i).lat() >= y))) {
      if ( this.getVertex(i).lng() + (y - this.getVertex(i).lat())
      /  (this.getVertex(j).lat()-this.getVertex(i).lat())
      *  (this.getVertex(j).lng() - this.getVertex(i).lng())<x ) {
        oddNodes = !oddNodes
      }
    }
  }
  return oddNodes;
 }

GPolygon.prototype.Area = function() {
  var a = 0;
  var j = 0;
  var b = this.Bounds();
  var x0 = b.getSouthWest().lng();
  var y0 = b.getSouthWest().lat();
  for (var i=0; i < this.getVertexCount(); i++) {
    j++;
    if (j == this.getVertexCount()) {j = 0;}
    var x1 = this.getVertex(i).distanceFrom(new GLatLng(this.getVertex(i).lat(),x0));
    var x2 = this.getVertex(j).distanceFrom(new GLatLng(this.getVertex(j).lat(),x0));
    var y1 = this.getVertex(i).distanceFrom(new GLatLng(y0,this.getVertex(i).lng()));
    var y2 = this.getVertex(j).distanceFrom(new GLatLng(y0,this.getVertex(j).lng()));
    a += x1*y2 - x2*y1;
  }
  return Math.abs(a * 0.5);
}

GPolygon.prototype.Distance = function() {
  var dist = 0;
  for (var i=1; i < this.getVertexCount(); i++) {
    dist += this.getVertex(i).distanceFrom(this.getVertex(i-1));
  }
  return dist;
}

GPolygon.prototype.Bounds = function() {
  var bounds = new GLatLngBounds();
  for (var i=0; i < this.getVertexCount(); i++) {
    bounds.extend(this.getVertex(i));
  }
  return bounds;
}

GPolygon.prototype.GetPointAtDistance = function(metres) {

  if (metres == 0) return this.getVertex(0);
  if (metres < 0) return null;
  var dist=0;
  var olddist=0;
  for (var i=1; (i < this.getVertexCount() && dist < metres); i++) {
    olddist = dist;
    dist += this.getVertex(i).distanceFrom(this.getVertex(i-1));
  }
  if (dist < metres) {return null;}
  var p1= this.getVertex(i-2);
  var p2= this.getVertex(i-1);
  var m = (metres-olddist)/(dist-olddist);
  return new GLatLng( p1.lat() + (p2.lat()-p1.lat())*m, p1.lng() + (p2.lng()-p1.lng())*m);
}

GPolygon.prototype.GetPointsAtDistance = function(metres) {
  var next = metres;
  var points = [];

  if (metres <= 0) return points;
  var dist=0;
  var olddist=0;
  for (var i=1; (i < this.getVertexCount()); i++) {
    olddist = dist;
    dist += this.getVertex(i).distanceFrom(this.getVertex(i-1));
    while (dist > next) {
      var p1= this.getVertex(i-1);
      var p2= this.getVertex(i);
      var m = (next-olddist)/(dist-olddist);
      points.push(new GLatLng( p1.lat() + (p2.lat()-p1.lat())*m, p1.lng() + (p2.lng()-p1.lng())*m));
      next += metres;    
    }
  }
  return points;
}

GPolygon.prototype.GetIndexAtDistance = function(metres) {

  if (metres == 0) return this.getVertex(0);
  if (metres < 0) return null;
  var dist=0;
  var olddist=0;
  for (var i=1; (i < this.getVertexCount() && dist < metres); i++) {
    olddist = dist;
    dist += this.getVertex(i).distanceFrom(this.getVertex(i-1));
  }
  if (dist < metres) {return null;}
  return i;
}

GPolygon.prototype.Bearing = function(v1,v2) {
  if (v1 == null) {
    v1 = 0;
    v2 = this.getVertexCount()-1;
  } else if (v2 ==  null) {
    v2 = v1+1;
  }
  if ((v1 < 0) || (v1 >= this.getVertexCount()) || (v2 < 0) || (v2 >= this.getVertexCount())) {
    return;
  }
  var from = this.getVertex(v1);
  var to = this.getVertex(v2);
  if (from.equals(to)) {
    return 0;
  }
  var lat1 = from.latRadians();
  var lon1 = from.lngRadians();
  var lat2 = to.latRadians();
  var lon2 = to.lngRadians();
  var angle = - Math.atan2( Math.sin( lon1 - lon2 ) * Math.cos( lat2 ), Math.cos( lat1 ) * Math.sin( lat2 ) - Math.sin( lat1 ) * Math.cos( lat2 ) * Math.cos( lon1 - lon2 ) );
  if ( angle < 0.0 ) angle  += Math.PI * 2.0;
  angle = angle * 180.0 / Math.PI;
  return parseFloat(angle.toFixed(1));
}

GPolyline.prototype.Contains             = GPolygon.prototype.Contains;
GPolyline.prototype.Area                 = GPolygon.prototype.Area;
GPolyline.prototype.Distance             = GPolygon.prototype.Distance;
GPolyline.prototype.Bounds               = GPolygon.prototype.Bounds;
GPolyline.prototype.GetPointAtDistance   = GPolygon.prototype.GetPointAtDistance;
GPolyline.prototype.GetPointsAtDistance  = GPolygon.prototype.GetPointsAtDistance;
GPolyline.prototype.GetIndexAtDistance   = GPolygon.prototype.GetIndexAtDistance;
GPolyline.prototype.Bearing              = GPolygon.prototype.Bearing;
	</script>

   
    <body onload="play(0, 0)"> 
     <div style="margin: 0; padding: 0; z-index: 1000; width: 100%;"> 
 		<?=$navigation;?>
     <div id="map" style="position: absolute;"></div>
     <div>
	    <div id="controls" style="position:absolute;align=right;"> 
        <form> 
	       <input type="button" class="button" value=" Info " onclick="javascript: infoanimate();" />
        </form> 
        </div> 
	    <br /><br />
        <div id="next1" style="position:absolute; visibility:hidden;">
            <input type="button" value=" Next " />
        </div>
        <br />
        <div id="step" style="position:absolute;" >&nbsp;</div>
        <br /><br />
        <div id="info_first_end" style="position:absolute;" ></div>
        <br />
        <div id="distance" style="position:absolute;" >KM: 0.00</div> 
	</div>
	
    <div id="infoanimate" style="position:absolute; display: none;">
		<h2><?php echo $vehicle->vehicle_name; ?> <?php echo $vehicle->vehicle_no; ?></h2>
		<?=$this->lang->line("ldatetime"); ?>: <?php echo date("d/m/Y H:i:s", $starttime); ?> -  <?php echo date("d/m/Y H:i:s", $endtime); ?>
	</div>
	
	<div id="error_1" style="position:absolute;display: none;">
		<h2>Can't Create Direction!</h2><br />
		<b>Tidak Dapat Menciptakan Direction Karena Tidak Terjadi Banyak Pergerakan pada Kendaraan.</b> 
	</div>

     </div>
        
	  
	 
		
 
    <script type="text/javascript"> 
    //<![CDATA[
    if (GBrowserIsCompatible()) {
 
      var map = new GMap2(document.getElementById("map"));
	  var trafficInfo;
	  var trafficOptions = {incidents:true};
	  trafficInfo = new GTrafficOverlay(trafficOptions);
      map.addControl(new GMapTypeControl());
      map.setCenter(new GLatLng(0,0),2);
      map.addOverlay(trafficInfo);
	  var dirn = new GDirections();
      var step = 5; // metres
      var tick = 100; // milliseconds
      var poly;
      var poly2;
      var lastVertex = 0;
      var eol;
      var car = new GIcon();
          car.image="<?php echo base_url(); ?>assets/images/car/car4earth.png"
          car.iconSize=new GSize(32,18);
          car.iconAnchor=new GPoint(16,9);
      var marker;
      var k=0;
      var stepnum=0;
      var speed = "";   
      var car_point = new Array() ;
	  
      function updatePoly(d) {
        // Spawn a new polyline every 20 vertices, because updating a 100-vertex poly is too slow
        
        if (poly2.getVertexCount() > 20) {
          poly2=new GPolyline([poly.getVertex(lastVertex-1)]);
          map.addOverlay(poly2)
        }
 
        if (poly.GetIndexAtDistance(d) < lastVertex+2) {
           if (poly2.getVertexCount()>1) {
             poly2.deleteVertex(poly2.getVertexCount()-1)
           }
           poly2.insertVertex(poly2.getVertexCount(),poly.GetPointAtDistance(d));
        } else {
          poly2.insertVertex(poly2.getVertexCount(),poly.getVertex(lastVertex++));
        }
      }
 
      function animate(d) {
        if (d>eol) {			
          ipos = ipos + 23;
          play(ipos, d);
          return;
        }
        var p = poly.GetPointAtDistance(d);
        if (k++>=180/step) {
          map.panTo(p);
          k=0;
        }

        marker.setPoint(p);
        document.getElementById("distance").innerHTML =  "KM: "+((d+ m_dpos)/1000).toFixed(2)+speed;

        if (stepnum+1 < dirn.getRoute(0).getNumSteps()) {			
          if (dirn.getRoute(0).getStep(stepnum).getPolylineIndex() < poly.GetIndexAtDistance(d)) 
          {
            stepnum++;
            var steptext = dirn.getRoute(0).getStep(stepnum).getDescriptionHtml();
            document.getElementById("step").innerHTML = "<b>Next:<\/b> "+steptext;
            var stepdist = dirn.getRoute(0).getStep(stepnum-1).getDistance().meters;
            var steptime = dirn.getRoute(0).getStep(stepnum-1).getDuration().seconds;
            var stepspeed = ((stepdist/steptime) * 2.24).toFixed(0);
            step = stepspeed/2.5;
            speed = "<br>Current speed: " + stepspeed +" mph";
          }
        } else {
          if (dirn.getRoute(0).getStep(stepnum).getPolylineIndex() < poly.GetIndexAtDistance(d)) {
            document.getElementById("step").innerHTML = "<b>Next: Arrive at your destination<\/b>";	
          }
        }
        updatePoly(d);                
        setTimeout("animate("+(d+step)+")", tick);
      }
 
      GEvent.addListener(dirn,"load", function() {		  
        document.getElementById("controls").style.visibility="display";
        poly=dirn.getPolyline();
        eol=poly.Distance();
        map.setCenter(poly.getVertex(0),17);
        map.addOverlay(new GMarker(poly.getVertex(0),G_START_ICON));
        map.addOverlay(new GMarker(poly.getVertex(poly.getVertexCount()-1),G_END_ICON));
        marker = new GMarker(poly.getVertex(0),{icon:car});
        map.addOverlay(marker);
        var steptext = dirn.getRoute(0).getStep(stepnum).getDescriptionHtml();
        document.getElementById("step").innerHTML = steptext;
        poly2 = new GPolyline([poly.getVertex(0)]);
        map.addOverlay(poly2);
        setTimeout("animate(0)",2000);  // Allow time for the initial map display
      });
 
      GEvent.addListener(dirn,"error", function() {
        alert("Location(s) not recognised. Code: "+dirn.getStatus().code);
      });
 
      function play(pos, d) 
      {
		  m_dpos += d;
		  
		  if (pos >= y.length)
		  {			   
			document.getElementById("step").innerHTML = "<b>Trip completed<\/b>";
			document.getElementById("distance").innerHTML =  "KM: "+( m_dpos/1000).toFixed(2);
			return;
		  }
		  
		  p_point = new Array();
		 for(var i=1; i < y.length-pos; i++)
		 {
			 if (i >= 24) break;
			 p_point.push(y[i]);
			 setTimeout(10000);

		 }
		 
		 jQuery("#info_first_end").html("Start: " + y[1] + " End: " + y[p_point.length]);		
	  	dirn.loadFromWaypoints(p_point,{getPolyline:true, getSteps:true});
		}
	}
    //]]>
    </script> 
	<script type="text/javascript">
	function infoanimate()
		{
			showdialog(jQuery("#infoanimate").html(), "Information", 400, 50);
		}
		
    function error_1()
	{
	showdialog(jQuery("#error_1").html(), "Information", 400, 50);
	}
	
	</script>
  </body> 
</html> 
 
 
 
 
