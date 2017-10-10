<?php
include('config.php');
?>
<!DOCTYPE html>
<html>
<head>
    <style>
        #map {
            height: 400px;
            width: 100%;
        }
    </style>
</head>
<body>
<?php

function getMarkers()
{
    $pdo = connect();
    $sql = 'SELECT * FROM ma_table';
    $query = $pdo->prepare($sql);
    $query->execute();
    $list = $query->fetchAll(PDO::FETCH_ASSOC);

    $locations = array();

    foreach ($list as $row) {
        $company = $row['social'];
        $address = $row['adresse'];
        $cp = $row['cp'];
        $city = $row['ville'];
        $phone = $row['phone'];
        $fax = $row['fax'];
        $tLat = (string)$row['latitude'];
        $tLng = (string)$row['longitude'];

        $locations[] = "["
            .$tLat .", "
            .$tLng.",'"
            .addslashes($company)." '". ", '"
            .addslashes($address). " '". ", '"
            .addslashes($cp)." '". ", '"
            .addslashes($city)." '". ", '"
            .addslashes($phone)." '". ", '"
            .addslashes($fax).
            "']";
    }

    echo sprintf("[%s]",implode(", ", $locations));
}


?>
<div id="map"></div>
<script type="text/javascript">
    var locations = <?php getMarkers(); ?>

    function initMap() {
        var myLatLng = {lat: 48.60222, lng: 2.42245};
        var infowindow = new google.maps.InfoWindow();
        var image = {
            url: 'marker.png',
            size: new google.maps.Size(50, 55),
            origin: new google.maps.Point(0, 0),
            anchor: new google.maps.Point(17, 34),
            scaledSize: new google.maps.Size(50, 55)
        };
        var map = new google.maps.Map(document.getElementById('map'), {
            zoom: 11,
            center: myLatLng
        });

        for (i = 0; i < locations.length; i++) {

            var position = new google.maps.LatLng(locations[i][0], locations[i][1]);
            var contentString = locations[i];

            marker = new google.maps.Marker({
                position: position,
                map: map,
                title: locations[i][2],
                icon: image,
                contentString: contentString
            });

            marker.data = locations[i];

            marker.addListener('click', function() {
                for (i = 0; i < locations.length; i++) {
                    infowindow.setContent('<div id="content">'+
                        '<div id="siteNotice">'+
                        '</div>'+
                        '<h1 id="firstHeading" class="firstHeading">'+ this.data[2] +'</h1>'+
                        '<div id="bodyContent">'+
                        '<p><b>Adresse : </b>'+ this.data[3] +'</p>'+
                        '<p><b>Code postale : </b>'+ this.data[4] +'</p>'+
                        '<p><b>Ville : </b>'+ this.data[5] +'</p>'+
                        '<p><b>Télèphone : </b>'+ this.data[6] +'</p>'+
                        '<p><b>Fax : </b>'+ this.data[7] +'</p>'+
                        '</div>');
                    infowindow.open(map, this);
                    map.setCenter(this.getPosition());
                }
            });
        }
    };
</script>
<script async defer
        src="https://maps.googleapis.com/maps/api/js?key=YOUR_KEY_HERE&callback=initMap"></script>
</body>
</html>
