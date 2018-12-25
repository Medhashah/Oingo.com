<?php
// define variables and set to empty values

session_start();
if(!isset($_SESSION["user_id"]))
  header("location:index.php");
else
  $original_user = $_SESSION["user_id"];

 include 'db_functions.php'; 
 $link = db_connect();

//echo "string";
$radiusErr = $commentErr = $locationErr = $latitude = $longitude =$tags =$visibilityErr = $day = "";
$radius = $comment = $location = $Description = $visibility = $start_date = $end_date = $start_time = $end_time = "";



if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (empty($_POST["radius"])) {
    $radiusErr = "Radius is required";
  } 
  else{
    $radius = $_POST["radius"];
  }
  //echo "Radius:";
  //echo $radius;
  //echo "latitude:";
  $latitude = $_POST['latitude']; 
  //echo $latitude;
  //echo "longitude:";
 $longitude = $_POST['longitude'];
  //echo $longitude;  
 //echo "state";

$state = $_POST["state"];
if($state == "Choose state" )
  $state="";
  //echo $state;
   
    //echo "Tags";

$tags = $_POST["tags"];
if($tags == "Choose Tag" )
  $tags="";
  //echo $tags;
   
    $visibility = $_POST["visibility"];
  //echo "visibility:";
  //echo $visibility;

  $repeat = $_POST["repeat"];
  //echo "Repeaatet:";
  //echo $repeat;

if (empty($_POST["start_date"])) {
    $start_date = "";
   
  } else {
    
    $start_date = $_POST["start_date"];
  }

  //echo "start_date:";
  //echo $start_date;

  if (empty($_POST["end_date"])) {
    $end_date = "";
   
  } else {
    
    $end_date = $_POST["end_date"];
  }

  //echo "end_date:";
  //echo $end_date;

  if (empty($_POST["start_time"])) {
    $start_time = "";
   
  } else {
    
    $start_time = $_POST["start_time"].":00";
  }


  //echo "start_time:";
  //echo $start_time;

  if (empty($_POST["end_time"])) {
    $end_time = "";
   
  } else {
    
    $end_time = $_POST["end_time"].":00";
  }

  //echo "end_time:";
  //echo $end_time;


  if (!empty($_POST["DailyType"])) {
    //echo sizeof($_POST['select2']);
    //echo "string";
    $day_arr = $_POST['DailyType'];
    
     $day = $day_arr[0];
   for ($i=1; $i < sizeof($day_arr); $i++) {
     $day.=",".$day_arr[$i];
  }
} //echo "Day:";
    //echo $day;

    if (!empty($_POST["MonthlyType"])) {
    //echo sizeof($_POST['select2']);
    //echo "string";
    $day_arr = $_POST['MonthlyType'];
    
     $day = $day_arr[0];
   for ($i=1; $i < sizeof($day_arr); $i++) {
     $day.=",".$day_arr[$i];
  }
} //echo "Day:";
    //echo $day;

$type = $_POST["Type"];
  //echo "Type:";
  //echo $type;
  insert_filter($original_user,$type,$repeat,$day,$start_time,$end_time,$start_date,$end_date,$latitude,$longitude,$visibility,$radius,$tags,$state);
  
} 

function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}
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

<h2>Create Filters</h2>

<p><span class="error">* required field</span></p>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">  

  Tags: 
  <?php
  
    $result_tag = get_tags();

//print_r($result);
  $count_tag = count($result_tag['name']);


//echo $count_tag;

?>
<select name="tags" id="tags">
   <option>Choose Tag</option>
           <?php

                        if($count_tag > 0)
                        {

                        for ($i=0; $i < $count_tag ; $i++) { ?>
                                 <option value=<?php echo $result_tag["tag_id"][$i]?>><?php echo $result_tag["name"][$i]?></option>
                            <?php           
                         } 
                         } 
                                ?> 
                            

    </select>

<br><br>
  Location:
  <button  type="button" onclick="getLocation()">Locate me</button>
  <div id="map">hello</div>
   <span class="error">* <?php echo $locationErr;?></span>
  <p id="demo">
    <input type="hidden" id="latitude" name="latitude" >
    <input type="hidden" id="longitude" name="longitude" >
  </p>
  
  
  
  Radius: <input type="text" name="radius" value="<?php echo $radius;?>">
  <span class="error">* <?php echo $radiusErr;?></span>
  <br><br>

  Visibility: <select  name="visibility" id="visibility" value="2"> 
  
  <option value="2">Everyone</option>
  <option value="1">Friends</option>
  <option value="0">Self</option>
  
  </select>
  
    <br><br>
   Start date: <input type="date" name="start_date" >

   <br><br>
   End date: <input type="date" name="end_date" >

   <br><br>
   Start time: <input type="time" name="start_time" >

   <br><br>
   End time: <input type="time" name="end_time" >


  <br><br>

  <label for="db">Choose type</label>
<select name="Type" id="Type"   value="">
 <option>Select the option</option>
  <option value="next">Next</option>
   <option value="every">Every</option>
   <option value="monthly">Monthly</option>
</select>

<div id="Daily" style="display:none;">
  <label for="db">Choose the days</label>
<select name="DailyType[]" id="DailyType" multiple="multiple">
   <option value="1">Monday</option>
   <option value="2">Tuesday</option>
   <option value="3">Wednesday</option>
   <option value="4">Thursday</option>
   <option value="5">Friday</option>
   <option value="6">Satday</option>
   <option value="7">Sunday</option>
</select>
</div>

<div id="Monthly" style="display:none;">
  <label for="db">Choose the Month day</label>
<select name="MonthlyType[]" id="MonthlyType" multiple="multiple">
   <option value="1">1</option>
   <option value="2">2</option>
   <option value="3">3</option>
   <option value="4">4</option>
   <option value="5">5</option>
   <option value="6">6</option>
   <option value="7">7</option>
   <option value="8">8</option>
   <option value="9">9</option>
   <option value="10">10</option>
   <option value="11">11</option>
   <option value="12">12</option>
   <option value="13">13</option>
   <option value="14">14</option>
   <option value="15">15</option>
   <option value="16">16</option>
   <option value="17">17</option>
   <option value="18">18</option>
   <option value="19">19</option>
   <option value="20">20</option>
   <option value="21">21</option>
   <option value="22">22</option>
   <option value="23">23</option>
   <option value="24">24</option>
   <option value="25">25</option>
   <option value="26">26</option>
   <option value="27">27</option>
   <option value="28">28</option>
   <option value="29">29</option>
   <option value="30">30</option>
   <option value="31">31</option>
   </select>
</div>

  <br><br>

  Repeat: <select  name="repeat" id="repeat" value="0"> 
  <option value="0">Repeat</option>
  <option value="1">No Repeat</option>
  
</select>
<br><br>
 state: 
  <?php
  
    $result_state = get_states();

//print_r($result);
  $count_state = count($result_state['name']);


//echo $count_tag;

?>
<select name="state" id="state">
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