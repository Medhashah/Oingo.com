<?php
session_start();
$original_user = $_SESSION["user_id"];

?>

<!DOCTYPE HTML>  
<html>
<head>
<style>
.error {color: #FF0000;}
</style>
<style type="text/css">
          #map{ width:700px; height: 500px; }
        </style>
        <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB7RI0dAxn5-0vPv4Log-Nj6eiFF5HAdZU&callback=initMap"></script>
 
</head>
<body>  

<?php
// define variables and set to empty values

 include 'db_functions.php'; 
 $link = db_connect();

//echo "string";
$nameErr = $addressErr = $latitude = $longitude =  $locationErr ="";
$name = $email = $address = $contact = $pasword = "";



if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $latitude = $_POST['latitude']; 
 // echo $latitude;
  //echo "longitude:";
 $longitude = $_POST['longitude'];
  //echo $longitude;

  if (empty($_POST["start_date"])) {
    $start_date = "";
   
  } else {
    
    $start_date = $_POST["start_date"];
  }


  if (empty($_POST["start_time"])) {
    $start_time = "";
   
  } else {
    
    $start_time = $_POST["start_time"].":00";
  }
$timee_stamp =  $start_date." ".$start_time;
//echo $timee_stamp;
  
  update_loc($latitude,$longitude,$timee_stamp,$original_user);
  //echo "longitude:";
 $url = 'http://localhost/oingo/index.php';
                    echo '<script language="javascript">window.location.href ="'.$url.'"</script>';



} 


function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}
?>



<p><span class="error">* required field</span></p>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">  

  
  Location:
  <button  type="button" onclick="getLocation()">Locate me</button>
  <div id="map">hello</div>
   <span class="error">* <?php echo $locationErr;?></span>
  <p id="demo">
    <input type="hidden" id="latitude" name="latitude" >
    <input type="hidden" id="longitude" name="longitude" >
  </p>

<br><br>

  
date: <input type="date" name="start_date" >
  time: <input type="time" name="start_time" >

   <br>

  <input type="submit" name="submit" value="Submit">  
</form>

 
<script>
var x = document.getElementById("demo");

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

}

</script>
<script type="text/javascript" src="map.js"></script>
</body>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script>    
$('#Type').on('change',function(){
     var selection = $(this).val();
    switch(selection){
    case "next":
    $("#Daily").show()
    $("#Monthly").hide()
   break;
   case "every":
    $("#Daily").show()
    $("#Monthly").hide()
   break;
   case "monthly":
    $("#Daily").hide()
    $("#Monthly").show()
   break;
    default:
    $("#Daily").hide()
    }
});
</script>


</html>