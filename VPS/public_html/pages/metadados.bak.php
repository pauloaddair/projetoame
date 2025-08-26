<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload de Foto e Extração de Metadados</title>
    <style>
        #map {
            height: 400px;
            width: 100%;
        }
    </style>
</head>
<body>
    <h1>Envie sua Foto</h1>
    <form action="/include/upload.php" method="POST" enctype="multipart/form-data">
        <label for="photo">Escolha uma imagem:</label>
        <input type="file" name="photo" id="photo" accept="image/*">
        <br><br>
        <button type="submit">Enviar Foto</button>
    </form>
    <div id="map"></div>

    <script>
        function initMap(lat, lng) {
            var mapOptions = {
                zoom: 15,
                center: {lat: lat, lng: lng}
            };
            var map = new google.maps.Map(document.getElementById('map'), mapOptions);
            new google.maps.Marker({
                position: {lat: lat, lng: lng},
                map: map,
                title: 'Local da Foto'
            });
        }
    </script>
    <!-- Adicione sua API Key do Google Maps aqui -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAYRJcmxdjawHH9_Uljd8v7-7S-SyvTKVU"></script>
</body>
</html>
