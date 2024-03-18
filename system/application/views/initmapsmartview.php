        
        function init() 
        {
        	
            var options = {
                units: 'm',
                projection: new OpenLayers.Projection("EPSG:900913"),
                displayProjection: new OpenLayers.Projection("EPSG:4326"),
                maxResolution: 156543.0339,
                maxExtent: new OpenLayers.Bounds(-20037508.34, -20037508.34, 20037508.34, 20037508.34),
                numZoomLevels: 18,
                controls: [
                    new OpenLayers.Control.Navigation(),
                    new OpenLayers.Control.PanZoomBar(),
                    new OpenLayers.Control.LayerSwitcher({'div':OpenLayers.Util.getElement('layerswitcher')}),
                    new OpenLayers.Control.ScaleLine(),
                    new OpenLayers.Control.MousePosition()
                ],
                eventListeners: {
                	"zoomend": onZoomEnd
                }
            };
            
            map = new OpenLayers.Map('map', options);
 
            var openlayersWms = new OpenLayers.Layer.WMS(
                "OpenLayers WMS",
                "http://labs.metacarta.com/wms/vmap0",
                {layers:'basic'}
            );
 
            //var mapnik = new OpenLayers.Layer.OSM.Mapnik("OSM Mapnik");
            //var osma = new OpenLayers.Layer.OSM.Osmarender("OSM Osmarender");
 
            var gphy = new OpenLayers.Layer.Google(
                "Google Physical",
                {type: google.maps.MapTypeId.TERRAIN}
            );
 
            var gmap = new OpenLayers.Layer.Google(
                "Google Streets",
                {numZoomLevels: 20}
            );
 
            var ghyb = new OpenLayers.Layer.Google(
                "Google Hybrid",
                {type: google.maps.MapTypeId.HYBRID, numZoomLevels: 20}
            );
 
            var gsat = new OpenLayers.Layer.Google(
                "Google Satellite",
                {type: google.maps.MapTypeId.SATELLITE, numZoomLevels: 22}
            );
 
            var gwc = new OpenLayers.Layer.WMS(
                "Peta Lokal",
                "http://119.235.20.251:8080/geowebcache/service/wms",
                {
                    layers: 'indonesia_roads_20110413',
                    format: 'image/png',
                    transparent: false
                },
                {
                    isBaseLayer: true
                }
            );      
            
            var MY_MAPTYPE_ID = 'usroadatlas';
            var roadAtlasStyles = [{
            featureType: "road.highway",
            elementType: "geometry",
            stylers: [{
                        hue: "#ff0022"
                        }, {
                        saturation: 60
                        }, {
                        lightness: -20
                        }]
                        }, {
                        featureType: "road.arterial",
                        elementType: "all",
                        stylers: [{
                        hue: "#2200ff"
                        }, {
                        lightness: -40
                        }, {
                        visibility: "simplified"
                        }, {
                        saturation: 30
                        }]
            }, {
            featureType: "road.local",
            elementType: "all",
            stylers: [{
                        hue: "#f6ff00"
            }, {
                        saturation: 50
            }, {
                        gamma: 0.7
            }, {
                        visibility: "simplified"
            }]
            }, {
            featureType: "water",
            elementType: "geometry",
            stylers: [{
                        saturation: 40
            }, {
            lightness: 40
            }]
            }, {
                        featureType: "road.highway",
                        elementType: "labels",
            stylers: [{
            visibility: "on"
            }, {
                        saturation: 98
            }]
            }, {
                        featureType: "administrative.locality",
                        elementType: "labels",
            stylers: [{
                        hue: "#0022ff"
            }, {
                        saturation: 50
            }, {
                        lightness: -10
            }, {
                        gamma: 0.9
            }]
            }, {
                        featureType: "transit.line",
                        elementType: "geometry",
            stylers: [{
                        hue: "#ff0000"
            }, {
                        visibility: "on"
            }, {
                        lightness: -70
            }]
            }];

            var usRoadMapType = new google.maps.StyledMapType(roadAtlasStyles);
            var roadatlas = new OpenLayers.Layer.Google("Gv3", {
            type: 'usroadatlas'
            });
            roadatlas.id = 'a';
            
            var gmapv3 = new OpenLayers.Layer.Google("Google Roads");
            var trafficLayer = new google.maps.TrafficLayer();
            var fusionlayer = new google.maps.FusionTablesLayer(139529);
			var	tiles_bgrnd = "http://navigasi.net/tiles1/";
			var	tile_bgrnd = new OpenLayers.Layer.OSM("Blank", tiles_bgrnd + "${z}/${x}/${y}.png");
			tile_bgrnd.setVisibility(true);
			tile_bgrnd.setOpacity(1);
			tile_bgrnd.displayInLayerSwitcher = false;

			var	tiles_navnet = "http://navigasi.net/tiles1/";
			var	tile_navnet = new OpenLayers.Layer.OSM("Peta Lokal", tiles_navnet + "${z}/${x}/${y}.png");
			tile_navnet.setVisibility(true);
			tile_navnet.setOpacity(1);                  
 			
 			<?php if ($this->config->item("maps")) { ?>
 			map.addLayers(<?php echo $this->config->item("maps"); ?>);
 			<?php } else { ?>
 			map.addLayers([tile_bgrnd, tile_navnet, gmap, gphy, ghyb, roadatlas, gmap]);
            fusionlayer.setMap(map.getLayer('a').mapObject);
            trafficLayer.setMap(map.getLayer('a').mapObject);
            map.getLayer('a').mapObject.mapTypeControlOptions = {
            mapTypeIds: [google.maps.MapTypeId.ROADMAP, MY_MAPTYPE_ID]
            };
            map.getLayer('a').mapObject.mapTypes.set(MY_MAPTYPE_ID, usRoadMapType);
 			map.setBaseLayer(gmap);
            
 			<?php } ?> 			
                        
            map.setCenter(new OpenLayers.LonLat(118.112640381, -2.06358572).transform(
                    new OpenLayers.Projection("EPSG:4326"),
                    map.getProjectionObject()
                ), <?php echo (isset($ishistory) && $ishistory) ? $this->config->item("zoom_poi_history") : 5; ?>);                                    
                        
                        
            return map;        	

        }       
        
		function onZoomEnd()
		{						
        	var zoom = map.getZoom();
        	
        	if (zoom >= <?=$this->config->item('zoom_poi')?>)
        	{        		
        		showPOI();        		
        		return;
        	}
			if (zoom < <?=$this->config->item('zoom_poi')?>)
			{
				hidePOI();        		
        		return;
			}
			
			hidePOI();      	

		}	
		
		function hidePOI()
		{
        	if (poilayer == null) return;
        	if (! isshowpoi) return;        	        	
        	
        	isshowpoi = false;        	  	
        	isfirstpoilayer = true;	
        	
        	map.removeLayer(poilayer);
        	poilayer = null;
		}	

		function showPOI()
		{				
			if (isshowpoi) 
			{
				return;
			}
			
			poilayer = new OpenLayers.Layer.Vector("POIs", 
				{
			    		strategies: [new OpenLayers.Strategy.BBOX({resFactor: 1.1})],
			    		projection: new OpenLayers.Projection("EPSG:4326"),
			    		protocol: new OpenLayers.Protocol.HTTP(
			    				{
			        				url: '<?=base_url()?>map/poi?dummy1=on&dummy2=on',
			        				format: new OpenLayers.Format.Text(),
									params: {
                    							m: 'r',
                    							srs: 'EPSG:4326'
                    						}
			    				}
			    		)
				}
			);	
			poilayer.displayInLayerSwitcher = false;															

			map.addLayer(poilayer);					
						
			if (isfirstpoilayer)
			{
				poilayer.refresh();
				isfirstpoilayer = false;
			}
			
			poiSelectControl = new OpenLayers.Control.SelectFeature(poilayer);
			map.addControl(poiSelectControl);
			poiSelectControl.activate();
            poilayer.events.on({
                'featureselected': poiSelectControl_onFeatureSelect,
                'featureunselected': poiSelectControl_onFeatureUnselect
            });		
            
            function poiSelectControl_onPopupClose(evt) {
                // 'this' is the popup.
                poiSelectControl.unselect(this.feature);
            }
            
            function poiSelectControl_onFeatureSelect(evt) {            
                feature = evt.feature;
                
                var title = feature.attributes.title;
                if (title.substring(0, 6) == "cctv__")
                {
                	var cctvid = title.substring(6);

					jQuery.post('<?=base_url()?>cctv/grab/'+cctvid, {},
						function(r)
						{
							if (r.isempty)
							{
								return;
							}		
							
			                popup = new OpenLayers.Popup("featurePopup",
			                                         feature.geometry.getBounds().getCenterLonLat(),
			                                         new OpenLayers.Size(400,400),
			                                         "<font size='-1'>"+r.cctv.cctv_desc+"</font><br /><br /><img src='"+r.image+"' border='0' />" ,
			                                         true, poiSelectControl_onPopupClose);
			                feature.popup = popup;
			                popup.feature = feature;
			                map.addPopup(popup);							
						}
						, "json"
					);                	
                
                	return;
                }                
                
                popup = new OpenLayers.Popup("featurePopup",
                                         feature.geometry.getBounds().getCenterLonLat(),
                                         new OpenLayers.Size(100,100),
                                         "<font style='font-size: 12px;'><b>"+feature.attributes.title + "</b></font>" ,
                                         true, poiSelectControl_onPopupClose);
                popup.autoSize = true;
                feature.popup = popup;
                popup.feature = feature;
                map.addPopup(popup);
            }
            function poiSelectControl_onFeatureUnselect(evt) {
                feature = evt.feature;
                if (feature.popup) {
                    popup.feature = null;
                    map.removePopup(feature.popup);
                    feature.popup.destroy();
                    feature.popup = null;
                }
            }    
            
            isshowpoi = true;        	
		}
		
		var isfirstpoilayer = true;
		var isshowpoi = false;
		var poilayer = null;
		var poiSelectControl = null;
