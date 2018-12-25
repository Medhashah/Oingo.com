<?php
session_start();
$original_user = $_SESSION["user_id"];
  include 'db_functions.php';
//echo $original_user; 
  $result = get_notes_cust($original_user);
//print_r($result);
 // echo sizeof($result['note_id']);
?>


<html>
    <head>

<style type="text/css">
  html { height: 100% }
  body { height: 100%; margin: 0; padding: 0 }
  #map_canvas { height: 100% }
</style>
<script type="text/javascript"
  src=
"http://maps.googleapis.com/maps/api/js?key=AIzaSyB7RI0dAxn5-0vPv4Log-Nj6eiFF5HAdZU&sensor=false">
</script>
<script type="text/javascript">



var latitude = new Array();
 var longitude = new Array();
 var description = new Array();
  <?php for ($i=0; $i < sizeof($result['note_id']); $i++) { 
   // $temp = $result["latitude"][$i];

    ?>
    latitude.push('<?php echo $result["latitude"][$i]; ?>');
    longitude.push('<?php echo $result["longitude"][$i]; ?>');
    description.push('<?php echo $result["description"][$i]; ?>');
  <?php } ?>

   /* 
    console.log("hello");
    console.log(latitude);
    console.log(longitude);
    console.log(description);
    console.log("hello");
 
*/
 var locations = new Array(<?php echo sizeof($result['note_id']); ?>);

for (var i = 0; i < locations.length; i++) {
  locations[i] = new Array(6);
}

<?php  $i = 0; ?>

for (var i = 0; i < locations.length; i++) {
 //for (var j = 0; j < locate[0].length; j++) {
  locations[i][0] = 'Note:';
  locations[i][1] = Number(latitude[i]);
  locations[i][2] = Number(longitude[i]);
 locations[i][3] = description[i];
//}
 <?php  $i = 1; ?>
}

console.log(locations);

  

/*
var locations = [
  ['loan 1', 33.890542, 151.274856, 'address 1'],
  ['loan 2', 33.923036, 151.259052, 'address 2'],
  ['loan 3', 34.028249, 151.157507, 'address 3'],
  ['loan 4', 33.80010128657071, 151.28747820854187, 'address 4'],
  ['loan 5', 33.950198, 151.259302, 'address 5']
  ];

  console.log(locations);
*/
  function initialize() {

    var myOptions = {
      center: new google.maps.LatLng(40.62206370, -74.02887160),
      zoom: 8,
      mapTypeId: google.maps.MapTypeId.ROADMAP

    };
    var map = new google.maps.Map(document.getElementById("default"),
        myOptions);

    setMarkers(map,locations)

  }



  function setMarkers(map,locations){

      var marker, i

for (i = 0; i < locations.length; i++)
 {  

 var loan = locations[i][0]
 var lat = locations[i][1]
 var long = locations[i][2]
 var add =  locations[i][3]

 latlngset = new google.maps.LatLng(lat, long);

  var marker = new google.maps.Marker({  
          map: map, title: loan , position: latlngset  
        });
        map.setCenter(marker.getPosition())


        var content = " " + loan +  '</h3>' + "description: " + add  ;   

  var infowindow = new google.maps.InfoWindow()

           infowindow.setContent(content);
           infowindow.open(map,marker);

  }
  }



  </script>
 </head>
 <body onload="initialize()">
  <div id="default" style="width:100%; height:100%"></div>
 </body>
  </html>

  }
  }



  </script>
 </head>
 <body onload="initialize()">
  <div id="default" style="width:100%; height:100%"></div>
 </body>
  </html>