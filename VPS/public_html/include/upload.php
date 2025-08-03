    <!-- Adicione sua API Key do Google Maps aqui -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAYRJcmxdjawHH9_Uljd8v7-7S-SyvTKVU"></script>
<?php
include_once('funcoes.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png'];
        $fileInfo = pathinfo($_FILES['photo']['name']);
        $fileExtension = strtolower($fileInfo['extension']);
        
        if (in_array($fileExtension, $allowed)) {
			$dir = before("/include",__DIR__);
            $filePath = $dir.'/docs/' . basename($_FILES['photo']['name']);
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
                    echo "<script>initMap($lat, $lng);</script>";
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
