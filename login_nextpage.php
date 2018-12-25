<?php
session_start();
$usernameVal=$_REQUEST["username"];
//$passwordVAl=$_REQUEST["password"];

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "oingo";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 
else
{ $pass = $_REQUEST['password'];

     $escapedPW = mysqli_real_escape_string($conn,$pass);

     //save this user and pass as cookie if remeber checked start
 if (isset($_REQUEST['remember']))
   $escapedRemember = mysqli_real_escape_string($conn,$_REQUEST['remember']);

 $cookie_time = 60 * 60 * 24 * 30; // 30 days
  $cookie_time_Onset=$cookie_time+ time();
  if (isset($escapedRemember)) {
    /*
     * Set Cookie from here for one hour
     * */
    setcookie("username", $usernameVal, $cookie_time_Onset);
    setcookie("password", $escapedPW, $cookie_time_Onset);  

  } else {

      $cookie_time_fromOffset=time() -$cookie_time;
setcookie("username", '',$cookie_time_fromOffset );
    setcookie("password", '', $cookie_time_fromOffset);  

  }
  //save this user and pass as cookie if remember checked end
     
//now check user and pass verification
 $query = "select * from user where email= '$usernameVal';";
 
     $resultSet = mysqli_query($conn,$query);

                           if(@mysqli_num_rows($resultSet) > 0){
                           //check noraml user salt and pass
                           //echo "noraml";
                            

 $query = "select * from user where email = '$usernameVal' 
and password = '$pass' ";
                        
                            $resultSet = mysqli_query($conn,$query);

                           if(@mysqli_num_rows($resultSet) > 0){
                               $row = mysqli_fetch_assoc($resultSet);
                               echo "your username and  password is corrent";
                               
                               $_SESSION["user_id"]=$row["user_id"];
                               $_SESSION["user_email"]=$row["email"];
                               echo $row["user_id"];
                               echo $row["email"];
                               echo $_SESSION["user_id"];
//header("location:dashboard/dashboard.php");
}
else
{
echo "your username or password is incorrect";
}

}
     
}
?>