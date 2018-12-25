<?php
session_start();

if(isset($_SESSION["user_id"]))
  header("location:dashboard/dashboard.php");


if(!empty($_REQUEST))
 {
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
      $pass = md5($_REQUEST['password']);
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
header("location:dashboard/dashboard.php");
}
else
{
echo "your username or password is incorrect";
}

}
     
}

 }


?>
<!DOCTYPE html>
<html lang="en">

<body>

 <form name="loginform" action="" method="get">
<input type="text" name="username" placeholder="enter username" 
value="<?php if(isset($_COOKIE['username'])) echo $_COOKIE['username']; ?>" required>
<input type="password" id="passwordID" name="password" placeholder="enter password" 
value="<?php if(isset($_COOKIE['password'])) echo $_COOKIE['password']; ?>" required>
 <div class="checkbox">
 <input name="remember" id="remember" type="checkbox" 
<?php if(isset($_COOKIE['username'])){echo "checked='checked'"; } ?> value="1">
                                    <label for="remember">
                                        Remember Me
                                    </label>
                                </div>
<input type="submit" value="Login">
<br>
<br>
<br>
<br>
<br>
 
</form>
<button onclick="logout()">Register</button>
<script type="text/javascript">
function logout() {
 // document.getElementById("demo").innerHTML = "Hello World";
  //console.log("dzfads");
 location.replace("http://localhost/oingo/register.php");

}



</script>
</body>
</html>

<?php   


if ( isset( $_POST['submit'] ) ) {

if($_POST["remember_me"]=='1' || $_POST["remember_me"]=='on')
                    {

                    $hour = time() + 3600 * 24 * 30;
                    setcookie('username', $login, $hour);
                         setcookie('password', $password, $hour);
                    }


}

?>

