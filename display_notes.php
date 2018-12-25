<?php
  include 'db_functions.php';
  $result = get_notes();

?>

<!DOCTYPE html>
<html>
<body>

<h1>My First Google Map</h1>

<div id="map" style="width:100%;height:400px;"></div>

<script type="text/javascript" language="javascript">
 var latitude = new Array();
 var longitude = new Array();
 var description = new Array();
  <?php for ($i=0; $i < sizeof($result); $i++) { 
   // $temp = $result["latitude"][$i];

    ?>
    latitude.push('<?php echo $result["latitude"][$i]; ?>');
    longitude.push('<?php echo $result["longitude"][$i]; ?>');
    description.push('<?php echo $result["description"][$i]; ?>');
  <?php } ?>

    
    console.log("hello");
    console.log(latitude);
    console.log(longitude);
    console.log(description);
    console.log("hello");
  
function initialize() {

   var mapCanvas = document.getElementById("map");
  var i;

  var myCenter = new Array();
  var marker = new Array();
  var infowindow = new Array();

for (i = 0; i < latitude.length; i++) { 
  console.log(i);
   myCenter[i] = new google.maps.LatLng(19.231241, 72.851475);

   marker[i] = new google.maps.Marker({position:myCenter,animation:google.maps.Animation.BOUNCE});
  marker[i].setMap(map);

  infowindow[i] = new google.maps.InfoWindow({
      content:"Head office"
    });
  infowindow[i].open(map,marker[i]);
}
    

  

 
 

  var mapProp = {center: myCenter, zoom: 15};

  var map = new google.maps.Map(mapCanvas, mapProp);

    
  
}
     google.maps.event.addDomListener(window, 'load', initialize);

/*
var x = document.getElementById("map");

function getLocation() {
  
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(showPosition);

  } else { 
    x.innerHTML = "Geolocation is not supported by this browser.";
  }
}

function showPosition(position) {
  
  var lati = position.coords.latitude;
  var longi = position.coords.longitude;
   document.getElementById('latitude').value = lati;
   document.getElementById('longitude').value = longi;
   

  console.log(longi);
  console.log(lati);

}*/
</script>

 <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB7RI0dAxn5-0vPv4Log-Nj6eiFF5HAdZU&callback=initialize"></script>
</body>
</html>