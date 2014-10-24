<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Построение многоугольника по заданным координатам</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<script src="//api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript"></script>
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
	<style>
		html, body {
			width: 100%; height: 100%; padding: 0; margin: 0;
			font-family: Arial;
		}

		#map {
			widht: 100%;
			height: 80%;
		}

		.header {
			padding: 5px;
		}
	</style>

	<script type="text/javascript">
		// Как только будет загружен API и готов DOM, выполняем инициализацию
		ymaps.ready(init);
		function init () {
			myPolygons = [];
			points = []
			var myMap = new ymaps.Map("map", {
					center: [55.73, 37.75],
					zoom: 10,
					controls: ['zoomControl', 'typeSelector']
				});

			loadFromDb(myMap);

		// Создаем экземпляр класса ymaps.control.SearchControl
			mySearchControl = new ymaps.control.SearchControl({
				options: {
					noPlacemark: true
				}
			}),
		// Результаты поиска будем помещать в коллекцию.
			mySearchResults = new ymaps.GeoObjectCollection(null, {
				hintContentLayout: ymaps.templateLayoutFactory.createClass('$[properties.name]')
			});

			myMap.controls.add(mySearchControl);
			myMap.geoObjects.add(mySearchResults);
			
			// При клике по найденному объекту метка становится красной.
			mySearchResults.events.add('click', function (e) {
				e.get('target').options.set('preset', 'islands#redIcon');
				points.length = 0;
				points.push(e.get('target').geometry.getCoordinates());
				searchPoint();
			});
			// Выбранный результат помещаем в коллекцию.
			mySearchControl.events.add('resultselect', function (e) {
				var index = e.get('index');
				mySearchControl.getResult(index).then(function (res) {
				   mySearchResults.add(res);
				});
			}).add('submit', function () {
					mySearchResults.removeAll();
				})
			}

		function loadFromDb(myMap) {
			var url = '<? print_r($this->createUrl('regions/list')); ?>'
			$.ajax({
				type: "POST",
				url: url,
				success: function(res) {
				
					$.each(JSON.parse(res), function( index, value ) {
						myPolygon = new ymaps.Polygon(JSON.parse(value), {}, {
							
						});
						myPolygon.properties.set('name', index)
						myPolygon.properties.set('hintContent', index)
						
						myPolygons.push(myPolygon);
						
						myMap.geoObjects.add(myPolygon);
					});
				
				
				}
			  
			});

		}
		function searchPoint() {
			$.each(myPolygons, function( index, value ) {
				if(value.geometry.contains(points[0])) {
					var url = '<? print_r($this->createUrl('regions/loadPriceFromDb')); ?>'
					$.ajax({
						type: "POST",
						url: url,
						data: ({
							title: value.properties.get('name')
						}),
						success: function(res) {
							if(res){
								$.each(JSON.parse(res), function( index, value ) {
									alert(index + ' - ' + value);
								});
							}
						}
					});
					console.log('попал');
				} else {
					console.log('мимо');

				}
					
			   
			});
			
		}
	</script>
</head>
 
<body>
<h2>Построение многоугольника по заданным координатам</h2>
 
<div id="map" style="width: 800px; height: 400px; margin: auto"></div>
</body>
 
</html>