<?php
	  include_once('./include/funcoes.php');
?>
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
    <form action="" method="POST" enctype="multipart/form-data">
        <label for="photo">Escolha uma imagem:</label>
        <input type="file" name="photo" id="photo" accept="image/*">
        <br><br>
        <button type="submit">Enviar Foto</button>
    </form>

    <div id="map"></div>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
            $allowed = ['jpg', 'jpeg', 'png'];
            $fileInfo = pathinfo($_FILES['photo']['name']);
            $fileExtension = strtolower($fileInfo['extension']);
            if (in_array($fileExtension, $allowed)) {
			$dir = before("/pages",__DIR__);
            $filePath = $dir.'/docs/' . basename($_FILES['photo']['name']);
//                $filePath = 'uploads/' . $_FILES['photo']['name'];
 			echo $dir. "</br>".$_FILES['photo']['tmp_name'] ."<br>".$filePath."<br>";
                if (move_uploaded_file($_FILES['photo']['tmp_name'], $filePath)) {
                    // Função para extrair os metadados EXIF
                    $exif = exif_read_data($filePath, 0, true);

                    if (isset($exif['GPS'])) {
                        $gps = $exif['GPS'];
                        $lat = getGPS($gps['GPSLatitude'], $gps['GPSLatitudeRef']);
                        $lng = getGPS($gps['GPSLongitude'], $gps['GPSLongitudeRef']);
                        echo "<h2>Foto enviada com sucesso!</h2>";
                        echo "<h3>Localização da Foto: Latitude: $lat, Longitude: $lng</h3>";
                        echo "<script>document.addEventListener('DOMContentLoaded', function() { initMap($lat, $lng); });</script>";
                    } else {
                        echo "Os metadados GPS não foram encontrados na imagem.";
                    }
                } else {
                    echo "Erro ao salvar a foto.";
                }
            } else {
                echo "Formato de arquivo não suportado.";
            }
        } else {
            echo "Nenhuma imagem foi enviada ou houve um erro no upload.";
        }
    }

    // Função para converter coordenadas GPS para decimal
    function getGPS($exifCoord, $hemi) {
        $degrees = count($exifCoord) > 0 ? gps2Num($exifCoord[0]) : 0;
        $minutes = count($exifCoord) > 1 ? gps2Num($exifCoord[1]) : 0;
        $seconds = count($exifCoord) > 2 ? gps2Num($exifCoord[2]) : 0;

        $flip = ($hemi == 'W' || $hemi == 'S') ? -1 : 1;

        return $flip * ($degrees + ($minutes / 60) + ($seconds / 3600));
    }

    function gps2Num($coordPart) {
        $parts = explode('/', $coordPart);
        if (count($parts) <= 0) return 0;
        if (count($parts) == 1) return $parts[0];
        return floatval($parts[0]) / floatval($parts[1]);
    }
    ?>

    <!-- Adicione sua API Key do Google Maps aqui -->
    <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAYRJcmxdjawHH9_Uljd8v7-7S-SyvTKVU&loading=async&libraries=places&callback=initMap&v=weekly&libraries=marker"></script>
    <script>
        function initMap(lat, lng) {
            var mapOptions = {
                zoom: 15,
                center: {lat: lat, lng: lng}
            };
            var map = new google.maps.Map(document.getElementById('map'), mapOptions);
            // Criar um ícone personalizado com a miniatura da imagem
            var image = {
                url: <?php echo $filePath ?>, // Caminho da imagem
                scaledSize: new google.maps.Size(50, 50), // Tamanho da miniatura
                origin: new google.maps.Point(0, 0), // Origem
                anchor: new google.maps.Point(25, 25) // Ponto de ancoragem no centro
            };

            // Adicionar o marcador com a miniatura da imagem
            new google.maps.Marker({
                position: {lat: lat, lng: lng},
                map: map,
                icon: image, // Ícone personalizado com a imagem
                title: 'Local da Foto'
            });
        }
    </script>
</body>
</html>
