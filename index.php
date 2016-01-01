<!DOCTYPE html>
<head>

	<title>WFU | RideTheWake</title>

	<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
	<meta name="description" content="View Wake Forest University shuttles in realtime. 
	Find out shuttle times, stops, and route directions." />
	<meta name="KEYWORDS" content="ridethewake, shuttle, shuttles, bus schedule, schedules, Wake Forest shuttle service,
	Wake Forest University shuttle service, WFU shuttle, shuttle service, Wake Forest apartment shuttles,
	Wake Forest University apartment shuttles, Wake Line, Gray Line, Black Line, downtown shuttle, Gold Line" />

	<link rel="icon" type="image/png" href="favicon.ico">

	<link type="text/css" rel="stylesheet" href="style.css" />

	<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?sensor=false"></script>

	<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>

	<?php

		$shuttleXMLFile = "stops/shuttleInformation.xml";

		$shuttleInformationData = 0;

		if (file_exists($shuttleXMLFile)){

			$shuttleInformationData = simplexml_load_file($shuttleXMLFile);

		}

		$numOfShuttles = count($shuttleInformationData->children());

	?>

	<script type="text/javascript">

		var mapReferenceObject;

		var shuttleMarkerReference;

		var shuttleMarkerInfoWindow;

		var shuttleLocation = [];

		var mapPolyLine;

		var shuttleLines = [];

		var polyLine = [];

		var stopMarkers = [];

		var infoWindows = [];

		var stopTimes = [];

		var refreshFunction;

		<?

			for($i=0; $i<$numOfShuttles; $i++){

				$shuttleID = $shuttleInformationData -> shuttle[$i]['id'];
				$mapViewInitialZoomLevel = $shuttleInformationData -> shuttle[$i]['mapViewInitialZoomLevel'];
				$mapViewCenterCoordinateLat = $shuttleInformationData -> shuttle[$i]['mapViewCenterCoordinateLat'];
				$mapViewCenterCoordinateLon = $shuttleInformationData -> shuttle[$i]['mapViewCenterCoordinateLon'];
				$lineColorR = $shuttleInformationData -> shuttle[$i]['lineColorR'];
				$lineColorG = $shuttleInformationData -> shuttle[$i]['lineColorG'];
				$lineColorB = $shuttleInformationData -> shuttle[$i]['lineColorB'];
				$serverShuttleURL = $shuttleInformationData -> shuttle[$i]['serverShuttleURL'];
				$shuttleName = $shuttleInformationData -> shuttle[$i]['name'];

				$infoData = '"'.$shuttleID.'",'.$mapViewInitialZoomLevel.','.
				$mapViewCenterCoordinateLat.','.$mapViewCenterCoordinateLon.','
				.$lineColorR.','.$lineColorG.','.$lineColorB.',"'.$serverShuttleURL.'","'.$shuttleName.'"';

		?>

		shuttleLines.push([<?=$infoData?>]);
		
		<? } ?>

		function initialize()
		{
	        
	        var mapOptions = { center: {lat:shuttleLines[0][2],lng:shuttleLines[0][3]}, zoom: shuttleLines[0][1]};

	        var map = new google.maps.Map(document.getElementById('mapCanvas'), mapOptions);

	        mapReferenceObject = map;

	        loadLine(0, map);

	    }
	    
	    google.maps.event.addDomListener(window, 'load', initialize);

	    function changeLine(lineID)
		{

			clearInterval(refreshFunction);

			mapReferenceObject.setOptions({center:{lat:shuttleLines[lineID][2], lng: shuttleLines[lineID][3]}, zoom:shuttleLines[lineID][1]});

			clearMarkers();

			loadLine(lineID, mapReferenceObject);

			if(document.getElementById("scheduleContent").innerHTML != "")
			{
				showSchedule();
			}

		}

		function loadLine(lineID, map)
		{

	        loadShuttleSchedule(lineID);

	        loadPolyline(lineID, map);

	        downloadStopsFile(lineID);

			initializeShuttleMarker(lineID, map);

	        autoRefresh(lineID, map);

		}

	    function loadPolyline(lineID, map)
		{

			downloadRouteFile(lineID);

			if (mapPolyLine == null)
			{

				mapPolyLine = new google.maps.Polyline({

					geodesic: false,
					strokeOpacity: 0.7,
					map: map,
					strokeWeight: 5

				});

			}

			mapPolyLine.setOptions({strokeColor:rgbToHex(shuttleLines[lineID][4], shuttleLines[lineID][5], shuttleLines[lineID][6])});

			mapPolyLine.setPath(polyLine);

		}

		function rgbToHex(r, g, b)
		{

    		return "#" + ((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1);

		}

		function downloadRouteFile(lineID)
		{

			$.ajax({
			type: "GET",
			url: "routes/"+shuttleLines[lineID][0]+"Route.csv",
			dataType: "text",
			async: false,
			success: function(data) { processRouteCoordinates(data); }
			});

		}

		function processRouteCoordinates(routeCoordinatesData)
		{

			if(polyLine != [])
			{

				polyLine = [];

			}

		    var coordinateData = routeCoordinatesData.split(/\r\n|\n/);

		    for (var i=0; i<coordinateData.length; i++)
		    {

		        var data = coordinateData[i].split(',');

	            var coordinate = new google.maps.LatLng(data[0],data[1]);

	            polyLine.push(coordinate);

		    }

		}

		function downloadStopsFile(lineID)
		{

			$.ajax({
			type: "GET",
			url: "stops/"+shuttleLines[lineID][0]+"Stops.xml",
			dataType: "text",
			async: false,
			success: function(data) { loadStops(data, lineID); }
			});

		}

		function loadStops(data, lineID)
		{

			$(data).find('stop').each(function()
			{

				var stop = [];
				stop[0] = $(this).attr("name");
				stop[1] = $(this).attr("times");

				stopTimes.push(stop);

			    var stopCoordinates = new google.maps.LatLng($(this).attr("coordinateLat"),$(this).attr("coordinateLon"));

			    var stopMarker = new google.maps.Marker({
				    position: stopCoordinates,
				    map: mapReferenceObject
			    });

				var infowindow = new google.maps.InfoWindow({
			      content: "<div class='infoWindow'><b>"+$(this).attr('name')+"</b><br>"+$(this).attr("times")+"</div>"
			 	});

			 	google.maps.event.addListener(stopMarker, 'click', function() {
			    	infowindow.open(mapReferenceObject,stopMarker);
			 	});

			 	stopMarkers.push(stopMarker);

			 	infoWindows.push(infowindow);

		    });

		    $(data).find('stopInformation').each(function()
		    {
		    	stopTimes.push($(this).attr("info"));
		    });

		}

		function clearMarkers()
		{

			for (var i = 0; i < stopMarkers.length; i++) {
    			stopMarkers[i].setMap(null);
  			}

  			stopMarkers = [];

  			for (var i = 0; i < infoWindows.length; i++) {
    			infoWindows[i].setMap(null);
  			}

  			infoWindows = [];

  			stopTimes = [];

  			shuttleMarkerReference.setMap(null);

		}

		function initializeShuttleMarker(lineID, map)
		{
			
		    var image ='img/shuttleMarkers/'+shuttleLines[lineID][0]+'ShuttleMarker.png';

		    downloadShuttleLocation(lineID);

		    var shuttleCoordinates = new google.maps.LatLng(shuttleLocation[0],shuttleLocation[1]);

		    shuttleMarkerReference = new google.maps.Marker({
			    position: shuttleCoordinates,
			    zIndex: 100,
			    map: map,
			    icon: image
		    });

		   	shuttleMarkerInfoWindow = new google.maps.InfoWindow({
		      content: "<div class='infoWindow'>Updated at "+shuttleLocation[2]+".<br>There are "+shuttleLocation[3]+" passengers.</div>",
		      maxWidth: 200
		 	});

		 	google.maps.event.addListener(shuttleMarkerReference, 'click', function()
		 	{

		    	shuttleMarkerInfoWindow.open(map,shuttleMarkerReference);

		 	});

		}

		function downloadShuttleLocation(lineID)
		{

			$.ajax({
			type: "GET",
			url: ""+shuttleLines[lineID][7]+".xml",
			dataType: "text",
			async: false,
			success: function(data) { setShuttleLocationData(data); }
			});

		}

		function setShuttleLocationData(data)
		{
		
			$(data).find('marker').each(function()
			{

			    shuttleLocation[0] = $(this).attr("lat");
			    shuttleLocation[1] = $(this).attr("lng");
			    var str = $(this).attr("time");
			    var split = str.split(":");
			    var d = new Date(2014, 10, 20, split[0], split[1], split[2], 00);
			    shuttleLocation[2] = formatAMPM(d);
			    shuttleLocation[3] = $(this).attr("passenger");

		    });

		}

		function formatAMPM(date)
		{
			var hours = date.getHours();
			var minutes = date.getMinutes();
			var ampm = hours >= 12 ? 'pm' : 'am';
			hours = hours % 12;
			hours = hours ? hours : 12;
			minutes = minutes < 10 ? '0'+minutes : minutes;
			var strTime = hours + ':' + minutes + ' ' + ampm;
			return strTime;
		}

		function autoRefresh(lineID, mapReference)
		{

			refreshFunction = window.setInterval(function(){changeShuttleLocation(lineID, mapReference)}, 5000);

		}

		function changeShuttleLocation(lineID, mapReference)
		{

			downloadShuttleLocation(lineID);
		    
		    var shuttleCoordinates = new google.maps.LatLng(shuttleLocation[0],shuttleLocation[1]);

		    shuttleMarkerReference.setOptions({position:shuttleCoordinates});

		    shuttleMarkerInfoWindow.setOptions({content:"<div class='infoWindow'>Updated at "+shuttleLocation[2]+".<br>There are "+shuttleLocation[3]+" passengers.</div>"});

		}

		function loadShuttleSchedule(lineID)
		{

			document.getElementById("line").innerHTML = shuttleLines[lineID][8];

		}

		function showSchedule()
		{

			document.getElementById("showSchedule").innerHTML = "<a href='#' onClick='hideSchedule()'>▿ Hide shuttle schedule</a>";

			var HTML = "<table>";

			for(var j=0; j<stopTimes.length-1; j++)
			{
			    HTML += "<tr><th>"+stopTimes[j][0]+"</th></tr><tr><td>"+stopTimes[j][1]+"</td></tr>";
			}

			HTML += "</table><br>"+stopTimes[stopTimes.length-1];

			document.getElementById("scheduleContent").innerHTML = HTML;

		}

		function hideSchedule()
		{

			document.getElementById("showSchedule").innerHTML = "<a href='#' onClick='showSchedule()'>▹ Show shuttle schedule</a>";

			document.getElementById("scheduleContent").innerHTML = "";

		}

	</script>

</head>

<body>

<div id="container">

	<header><div id="logo"><span class="gold">Wake Forest University</span> RideTheWake</div></header>


	<div id="shuttleMap">
		<div id="mapCanvas"></div>
		<div id="statusPanel">
			<b><span id="line">Black Line</span></b>
			<br><span id="showSchedule"><a href="#" onClick="showSchedule()">▹ Show shuttle schedule</a></span>
			<div id="scheduleContent"></div>
		</div>
	</div>

	<div id="sidebar">

		<div class = "title">Shuttle Routes</div>

		<ul>

		<?

			for ($i=0; $i < $numOfShuttles; $i++) { 

				$name = $shuttleInformationData -> shuttle[$i]['name'];

				if ($name == "Gray Line") {

					if (($shuttleInformationData -> shuttle[$i]['category']) == "day")
					{

						$name = "Gray Day Line";

					}

					else
					{

						$name = "Gray Night Line";

					}
				}

				$stops = $shuttleInformationData -> shuttle[$i]['stops'];
				$id = $shuttleInformationData -> shuttle[$i]['id'];

		?>
			<a href="#" onclick="changeLine(<?=$i?>)">
				<li>

				<div class="shuttleIcon"><img src="img/shuttleIcons/<?=$id?>ShuttleIcon.png" width='30px' \></div>
				<div class="shuttleInfo">
					<span class="shuttleName"><?=$name?></span><br>
					<span class="shuttleStops"><?=$stops?></span>
				</div>

				</li>
			</a>
		<? } ?>

		</ul>

		<div id="iphoneAppAd">
			Download the RideTheWake iPhone App
			<div id="appIcon">
				<a href="https://itunes.apple.com/us/app/ride-the-wake/id387439113"><img src="img/iphoneIcon.png" width="120px"></a>
			</div>
		</div>

		<div id="about">
			Developed by: Niclas Ladd '16<br>Faculty Advisor: Daniel A. Cañas<br><br>Department of Computer Science<br>Wake Forest University
		</div>

	</div>

</div>

</body>

</html>