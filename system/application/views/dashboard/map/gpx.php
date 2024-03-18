<?php echo "<?"; ?>xml version="1.0" encoding="UTF-8" standalone="yes" ?>
<gpx version="1.0" creator="Turbo GPS" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://www.topografix.com/GPX/1/0" xsi:schemaLocation="http://www.topografix.com/GPX/1/0 http://www.topografix.com/GPX/1/0/gpx.xsd">
 <trk>
  <name>mo velotour blauenpasse</name>
  <number>1</number>
  <trkseg>
  	<?php for($i=0; $i < count($lon); $i++) { ?>  	
   	<trkpt lat="<?php echo $lat[$i]; ?>" lon="<?php echo $lon[$i]; ?>">
    	<ele>371.000000</ele>
    	<name>#<?php echo $i+1; ?></name>
   	</trkpt>
	<?php } ?>
  </trkseg>
 </trk>
</gpx>