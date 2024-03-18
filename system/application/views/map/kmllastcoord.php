<?="<?";?>xml version="1.0" encoding="UTF-8"?>
<kml xmlns="http://www.opengis.net/kml/2.2">
        <Document>
            <name><?=$vehicle->vehicle_no?>.kml</name>
            <description><?=$vehicle->vehicle_no?></description>


            <Style id="n_icon<?=$vehicle->vehicle_id?>">
                <IconStyle>
                    <scale><?=$nscale?></scale>
                    <Icon>
                        <href><?=$car?></href>
                    </Icon>
                </IconStyle>
            </Style>
            <Style id="h_icon<?=$vehicle->vehicle_id?>">
                <IconStyle>
                    <scale><?=$hscale?></scale>
                    <Icon>
                        <href><?=$car?></href>
                    </Icon>
                </IconStyle>
            </Style>

            <StyleMap id="icon<?=$vehicle->vehicle_id?>">
                <Pair>
                    <key>normal</key>
                    <styleUrl>#n_icon<?=$vehicle->vehicle_id?></styleUrl>
                </Pair>
                <Pair>
                    <key>highlight</key>

                    <styleUrl>#h_icon<?=$vehicle->vehicle_id?></styleUrl>
                </Pair>
            </StyleMap>


            <Folder>
                <name><?=$vehicle->vehicle_no?></name>
                <description><?=$vehicle->vehicle_no?></description>


                    <Placemark id="point<?=$vehicle->vehicle_id?>">
                        <name><?=$vehicle->vehicle_no?></name>
                        <description><?=$vehicle->vehicle_no?></description>
                        <Point>
                            <coordinates><?=$lng?>, <?=$lat?></coordinates>
                        </Point>

                        <styleUrl>#icon<?=$vehicle->vehicle_id?></styleUrl>
                    </Placemark>

            </Folder>

        </Document>
        </kml>
