

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
  if (empty($_POST["name"])) {
    $nameErr = "name is required";
  } 
  else{
    $name = $_POST["name"];
  }
  //echo "Radius:";
  //echo $radius;
  //echo "latitude:";
  $latitude = $_POST['latitude']; 
  //echo $latitude;
  //echo "longitude:";
 $longitude = $_POST['longitude'];
  echo $latitude;
  //echo "longitude:";
 $contact = $_POST['contact'];
  //echo $longitude;  
 //echo "state";

$state_id = $_POST["state_id"];
if($state_id == "Choose state" )
  $state_id="";
  //echo $state;
   
    //echo "Tags";

$email = $_POST["email"];

  //echo $tags;
   
    $password = $_POST["password"];
  //echo "visibility:";
  //echo $visibility;

  $address = $_POST["address"];
  //echo "Repeaatet:";
  //echo $repeat;


  //echo "Type:";
  //echo $type;
    insert_user($name,$address,$contact,$email,$password,$state_id,$latitude,$longitude);
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

<h2>Register</h2>

<p><span class="error">* required field</span></p>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">  

  
  
  
  
  Name: <input type="text" name="name" value="<?php echo $name;?>">
  <span class="error">* <?php echo $nameErr;?></span>
  <br><br>

  Address: <input type="text" name="address" value="<?php echo $address;?>">
  <span class="error">* <?php echo $addressErr;?></span>
  
  <br><br>
  ContactNo: <input type="tel" name="contact" value="<?php echo $contact;?>">
  <span class="error">* <?php echo $addressErr;?></span>
  <br><br>
  
  email: <input type="email" name="email" value="<?php echo $email;?>">
  <span class="error">* <?php echo $nameErr;?></span>
  <br><br>
  
  password: <input type="password" name="password" value="<?php echo $password;?>">
  <span class="error">* <?php echo $nameErr;?></span>
  <br><br>
<br><br>
  Location:
  <button  type="button" onclick="getLocation()">Locate me</button>
  <div id="map">hello</div>
   <span class="error">* <?php echo $locationErr;?></span>
  <p id="demo">
    <input type="hidden" id="latitude" name="latitude" >
    <input type="hidden" id="longitude" name="longitude" >
  </p>
  <br><br>
   state: 
  <?php
  
    $result_state = get_states();

//print_r($result);
  $count_state = count($result_state['name']);


//echo $count_tag;

?>
<select name="state_id" id="state_id">
   <option>Choose state</option>
           <?php

                        if($count_state > 0)
                        {

                        for ($i=0; $i < $count_state ; $i++) { ?>
                                 <option value=<?php echo $result_state["state_id"][$i]?>><?php echo $result_state["name"][$i]?></option>
                            <?php           
                         } 
                         } 
                                ?> 
                            

    </select>

    
<br><br>
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