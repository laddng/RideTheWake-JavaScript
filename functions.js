function changeLine(lineID, map)
{

	updateMap(lineID, map);

	loadLine(lineID, map);

}

function updateMap(lineID, map)
{

	map.setZoom(17);

}

function initializeShuttleMarker(lineID, map)
{

    var image ='img/shuttleMarkers/'+lineID+'ShuttleMarker.png';

    var shuttleCoordinates = new google.maps.LatLng(36.15100,-80.27915);

    var shuttleMarker = new google.maps.Marker({
	    position: shuttleCoordinates,
	    map: map,
	    icon: image
    });

   	var infowindow = new google.maps.InfoWindow({
      content: "Updated at 0:00PM.<br>There are # passengers."
 	});

 	google.maps.event.addListener(shuttleMarker, 'click', function() {
    	infowindow.open(mapReference,shuttleMarker);
 	});

 	return shuttleMarker;

}

function loadStops(lineID, map)
{

    var stopCoordinates = new google.maps.LatLng(36.13290,-80.26672);

    var stopMarker = new google.maps.Marker({
	    position: stopCoordinates,
	    map: map,
	    strokeColor: "blue",
	    title:"Crowne Park"
    });

	var infowindow = new google.maps.InfoWindow({
      content: "<b>Crowne Park</b><br>xx:25 xx:55"
 	});

 	google.maps.event.addListener(stopMarker, 'click', function() {
    	infowindow.open(mapReference,stopMarker);
 	});


}

function loadShuttleLocation(shuttleMarkerReference, lineID, mapReference)
{

}

function startAutorefresh(shuttleMarkerReference, lineID, mapReference)
{

	window.setInterval(loadShuttleLocation(shuttleMarkerReference, lineID, mapReference), 5000);

}