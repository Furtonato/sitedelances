<?

	if(isset($_GET['endereco'])){

		$geocode = file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?address='.urlencode($_GET['endereco']).'&components=country:BR&sensor=true&key=AIzaSyDEZCMiz9g2KkZgS13zYCV2FTJ-Win3Vb8');

		$output= json_decode($geocode);
		$_GET['lat'] = $output->results[0]->geometry->location->lat;
		$_GET['lon'] = $output->results[0]->geometry->location->lng;

	}

	echo
		'<script type="text/javascript" src="https://maps.google.com/maps/api/js?sensor=false&key=AIzaSyDEZCMiz9g2KkZgS13zYCV2FTJ-Win3Vb8"></script> '.
		'<script type="text/javascript"> '.
			'var map; '.
			'function initialize() { '.
				'var myLatlng = new google.maps.LatLng('.$_GET['lat'].', '.$_GET['lon'].'); '.
				'var myOptions = { '.
					'zoom: '.$_GET['zoom'].', '.
					'center: myLatlng, '.
					'mapTypeId: google.maps.MapTypeId.ROADMAP '.
				'}; '.
				'map = new google.maps.Map(document.getElementById("map_canvas"), myOptions); '.
				'google.maps.event.addListener(map, "zoom_changed", function() { '.
					'setTimeout(moveToDarwin, 1500); '.
				'}); '.
				'var marker = new google.maps.Marker({ '.
					'position: myLatlng,  '.
					'map: map,  '.
					'title:"'.$_GET['nome'].'" '.
				'}); '.
				'google.maps.event.addListener(marker, "click", function() { '.
					'map.setZoom(8); '.
				'}); '.
			'} '.
			'function moveToDarwin() { '.
				'var darwin = new google.maps.LatLng('.$_GET['lat'].', '.$_GET['lon'].'); '.
				'map.setCenter(darwin); '.
			'}; '.
		'</script> '.
		'<div id="map_canvas" style="width:100%; height:100%"></div> '.
		'<script>initialize()</script> ';

?>
