<!DOCTYPE html>
<head>

	<title>WFU | RideTheWake</title>

	<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
	<meta name="description" content="Real Time Shuttle Schedule RideTheWake: Wake Forest University shuttle services" />
	<meta name="KEYWORDS" content="ridethewake, shuttle, shuttles, bus schedule, schedules, Wake Forest shuttle service, Wake Forest University shuttle service, WFU shuttle, shuttle service, Wake Forest apartment shuttles, Wake Forest University apartment shuttles, Wake Line, Gray Line, Black Line, downtown shuttle, Gold Line" />

	<link rel="icon" type="image/png" href="favicon.ico">

	<link type="text/css" rel="stylesheet" href="style.css" />

	<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?sensor=false"></script>

	<?php

		$shuttleXMLFile = "shuttleInformation.xml";

		if (file_exists($shuttleXMLFile)){

			$shuttleInformationData = simplexml_load_file($shuttleXMLFile);

			$numOfShuttles = 6;

			$shuttlesArray = new ArrayObject;

			for($i=0; $i<$numOfShuttles; $i++)
			{

				$shuttlesArray[$i] = $shuttleInformationData -> shuttle[$i];

				echo $shuttlesArray[$i];

			}


		}

	?>
<!--<script>

		var shuttleInformation  = [];

		//for (var i = 0; i < shuttleRoutes.length; i++) {
			
			shuttleInformation[0][0] = x[0].getAttribute("name");
			//shuttleInformation[i]["shuttleID"] = shuttleRoutes[i].getAttribute("id");
			//shuttleInformation[i]["shuttleCategory"] = shuttleRoutes[i].getAttribute("cat");
			//shuttleInformation[i]["serverShuttleURL"] = shuttleRoutes[i].getAttribute("serverShuttleURL");
			//shuttleInformation[i]["lineColorR"] = shuttleRoutes[i].getAttribute("lineColorR");
			//shuttleInformation[i]["lineColorG"] = shuttleRoutes[i].getAttribute("lineColorG");
			//shuttleInformation[i]["lineColorB"] = shuttleRoutes[i].getAttribute("lineColorB");
		
		//}

		// Read polyline coordinate files from server 
		// and create an array of polylines for map
		// 
		
		alert(shuttleInformation[0][0]);

		var shuttleRoutesPolyines = [];

//		for (var i = shuttleInformation.length - 1; i >= 0; i--)
//		{

			//var shuttlePolylineCoordinatesURL = "routes/"+shuttleInformation[i]["shuttleID"]+"Route.csv";

			var shuttlePolylineCoordinatesURL = "/routes/blackRoute.csv";

			var gpsCoordinates;

			var gpsCoordinatesFile = new XMLHttpRequest();
											
    		gpsCoordinatesFile.open("GET", shuttlePolylineCoordinatesURL, true);
    
    		gpsCoordinatesFile.onreadystatechange = function ()
    		{
		        if(gpsCoordinatesFile.readyState === 4)
		        {

		            if(gpsCoordinatesFile.status === 200 || gpsCoordinatesFile.status == 0)
		            {
		                
		                gpsCoordinates = gpsCoordinatesFile.responseText;

		            }

		        }
    		}

    		gpsCoordinatesFile.send(null);

			gpsCoordinates.split("\n");

			var polylineArray = [];

			for (var i = gpsCoordinates.length - 1; i >= 0; i--)
			{

				var point = new google.maps.LatLng(gpsCoordinates[i].split(',')[0],gpsCoordinates[i].split(',')[1]);
  				
  				polylineArray.push(point); 

			};

			var polyLine = new google.maps.Polyline({
				
				path: polylineArray,
				geodesic: false,
				strokeColor: '#000000',
				strokeOpacity: 0.7,
				strokeWeight: 7

			});

			polyLine.setMap(shuttleMap);

			shuttleRoutesPolylines.push(polyLine);

//		};
</script>-->
<script>
	
	function initialize()
	{
        
        var mapOptions = {
          center: { lat: 36.15100, lng: -80.27915},
          zoom: 14
        };

        var map = new google.maps.Map(document.getElementById('mapCanvas'),mapOptions);

    }
    
    google.maps.event.addDomListener(window, 'load', initialize);

</script>

</head>

<body>

<div id="container">

	<header><span class="gold">Wake Forest University</span> RideTheWake</header>


	<div id="shuttleMap">
		<div id="offlineWarning">This shuttle is currently not running. Check the schedule to see the operating times.</div>
		<div id="mapCanvas"></div>
	</div>

	<div id="sidebar">

		<div class = "title">Shuttle Routes</div>

		<ul>

		<?


		?>
			<a href="#" onclick="showBlackLine()">
				<li>

				<div class="shuttleIcon"><img src="img/shuttleIcons/blackShuttleIcon.png" width='38px' \></div>
				<div class="shuttleInfo">
					<span class="shuttleName">Black Line</span><br>
					<span class="shuttleStops">Alaris Village, Deacon Ridge, Crowne Oaks, Bus Stop Shelter</span>
				</div>

				</li>
			</a>
		<?  ?>

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