<!DOCTYPE html>
<html>
<head>
	<title>Редактор многоугольника</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<script src="//api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript"></script>
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
	

<script type="text/javascript">
ymaps.ready(init);

function init() {
	var myMap = new ymaps.Map("map", {
		center: [55.73, 37.75],
		zoom: 10,
		controls: ['zoomControl', 'typeSelector']
	});

  
	button = new ymaps.control.Button({ data: { content: 'регион' } }, { selectOnClick: false }),
	buttonChanel = new ymaps.control.Button({ data: { content: 'отмена' } }, { selectOnClick: false }),
		
		button.events.add('click', function() {
	   		button.disable();
	   		$('#insert').show();
		  // Создаем многоугольник без вершин.
		  var myPolygon = new ymaps.Polygon([], {}, {
			  // Курсор в режиме добавления новых вершин.
			  editorDrawingCursor: "crosshair",
			  // Максимально допустимое количество вершин.
			  editorMaxPoints: 10,
			  // Цвет заливки.
			  fillColor: '#00FF00',
			  // Цвет обводки.
			  strokeColor: '#0000FF',
			  // Ширина обводки.
			  strokeWidth: 1,
			  fillOpacity: 0.35
		  });
		  // Добавляем многоугольник на карту.
		  myMap.geoObjects.add(myPolygon);

		  // В режиме добавления новых вершин меняем цвет обводки многоугольника.
		  var stateMonitor = new ymaps.Monitor(myPolygon.editor.state);
		  stateMonitor.add("drawing", function (newValue) {
			  myPolygon.options.set("strokeColor", newValue ? '#FF0000' : '#0000FF');
		  });

		  // Включаем режим редактирования с возможностью добавления новых вершин.
		  myPolygon.editor.startDrawing();

		   $('#stopEditPolyline').click(
				function () {
					myPolygon.editor.stopEditing();
					insertIntoDb(myPolygon.geometry.getCoordinates());
		   			button.enable();
					
				});
	  });
	buttonChanel.events.add('click', function() {
		location.reload();
	});
	myMap.controls.add(button, {top: 5, right: 5});
	myMap.controls.add(buttonChanel, {top: 10, right: 5});
	loadFromDb(myMap);

}

function insertIntoDb(coordinates) {
	
	if(!$('#title').val()) {
		$('#error_title').css('display', 'inline-block')
		return;
	}
	if(coordinates[0].length <= 0) {
		$('.error').show();
		return;
	}

	var url = '<? print_r($this->createUrl('regions/insertIntoDb')); ?>'
	
	$.ajax({
	  type: "POST",
	  url: url,
	  dataType: 'json',
	  data: ({
		coordinates: JSON.stringify(coordinates),
		title: $('#title').val()
	  }),
	});
	
	$('#insert').hide();
	$('#error_title').hide();
	$('.error').hide();
	$('#insert').trigger( 'reset' );
	location.reload();
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
				myMap.geoObjects.add(myPolygon);
				
			});
		}
	});
}

</script>
</head>
<body>
<h2 class='header'>Создание многоугольника</h2>
<p class='error' style="display:none; color:red">создайте регион</p>

<form style="display:none" id="insert">
	<p id='error_title' style="display:none; color:red">введите название</p>
	<input type="text" name="title" id="title" placeholder ="Ведете название"/>
	<input type="button" value="Сохранить" id="stopEditPolyline"/>
</form>

<div id="map" style="width: 800px; height: 400px; margin: auto"></div>
</body>
</html>