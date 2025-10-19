var map;
var idInfoBoxAberto;
var infoBox = [];
var markers = [];

function initialize() {	
    var options = {
        zoom: 5,
		center: new google.maps.LatLng(-18.8800397, -47.05878999999999),
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    map = new google.maps.Map(document.getElementById("mapa"), options);
}
initialize();

function abrirInfoBox(id, marker) {
	if (typeof(idInfoBoxAberto) == 'number' && typeof(infoBox[idInfoBoxAberto]) == 'object') {
		infoBox[idInfoBoxAberto].close();
	}
	infoBox[id].open(map, marker);
	idInfoBoxAberto = id;
}

function carregarPontos($latitude, $longitude) {
	
	$.getJSON(DIR+'/app/Ajax/Pin/pontos.php?latitude='+$latitude+'&longitude='+$longitude, function(pontos) {
		
		var latlngbounds = new google.maps.LatLngBounds();

		$('.chaveiros_perto').html('');

		$.each(pontos, function(index, ponto) {
			if(index != 'select_cidades'){

				// Chaveiros na Home
				if(ponto.home && ponto.n <= 10)
					$('.chaveiros .chaveiros_perto').append(ponto.home);

				var marker = new google.maps.Marker({
					position: new google.maps.LatLng(ponto.lat, ponto.lng),
					title: ponto.nome,
					map: map,
					icon: ponto.valido==1 ? '../plugins/Google/Pin/img/localizacao.png' : '',
				});
				
				var myOptions = {
					content: ponto.txt,
					pixelOffset: new google.maps.Size(-150, 0)
	        	};

				infoBox[ponto.id] = new InfoBox(myOptions);
				infoBox[ponto.id].marker = marker;

				infoBox[ponto.id].listener = google.maps.event.addListener(marker, 'click', function (e) {
					abrirInfoBox(ponto.id, marker);
				});

				markers.push(marker);
				latlngbounds.extend(marker.position);

			}			
		});

		//var markerCluster = new MarkerClusterer(map, markers);		
		map.fitBounds(latlngbounds);

		//setTimeout(function(){
		//	map.setZoom(map.getZoom()-1)
		//	map.setZoom(map.getZoom()+1)
		//}, 500);

		$('.chaveiros_carregando').hide();

	});
	
}