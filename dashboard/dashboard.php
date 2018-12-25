
<?php
session_start();
include '../db_functions.php';   
if(!isset($_SESSION["user_id"]))
  header("location:../index.php");
   // echo "string";
  //  echo($_SESSION["user_email"]);
   // echo($_SESSION["user_id"]);
    $original_user = $_SESSION["user_id"];
   

 
   $flag=0;
    $result_state = get_states();
error_reporting(E_ERROR | E_PARSE);
//print_r($result);
  $count_state = count($result_state['name']);


//echo $count_tag;
$comment="";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
   if ($_POST["Comment"]) {
        if (empty($_POST["description-comment"])) {
    $comment = "Radius is required";
    $note_id = "";
  } 
  else{
    
    $comment = $_POST["description-comment"];
    $note_id = $_POST["note"];
    $original_user = $_SESSION["user_id"];
    write_comment($comment,$original_user,$note_id);

  }

   }

}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
   if ($_POST["search-submit"]) {
        if (empty($_POST["search"])) {
    
    $search = "";
  } 
  else{
    $flag=1;
    $search = $_POST["search"];
//echo "search ios:";
    echo $search;
    //get_filter_search($original_user,$search)
    //$note_id = $_POST["note"];
    //$original_user = $_SESSION["user_id"];
    //write_comment($comment,$original_user,$note_id);

  }

   }

}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
if ($_POST["state_submit"]) {
        if (empty($_POST["state"])) {
    $state_id = "";
    
  } 
  else{
    
    $state_id = $_POST["state"];
    update_state($original_user,$state_id);

  }

   }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Material Design Bootstrap</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <!-- Material Design Bootstrap -->
    <link href="css/mdb.min.css" rel="stylesheet">
    <!-- Your custom styles (optional) -->
    <link href="css/style.min.css" rel="stylesheet">
</head>

<body class="grey lighten-3">

    <!--Main Navigation-->
    <header>

        <!-- Navbar -->
        <nav class="navbar fixed-top navbar-expand-lg navbar-light white scrolling-navbar">
            <div class="container-fluid">

                <!-- Brand -->
                <a class="navbar-brand waves-effect" href="https://mdbootstrap.com/docs/jquery/" target="_blank">
                    <strong class="blue-text">Oingo</strong>
                </a>

                <!-- Collapse -->
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
                    aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Links -->
                <div class="collapse navbar-collapse" id="navbarSupportedContent">

                    <!-- Left -->
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item active">
                            <a class="nav-link waves-effect" href="#">Home
                                
                                <span class="sr-only">(current)</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link waves-effect" href="https://mdbootstrap.com/docs/jquery/" target="_blank">
   </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link waves-effect" href="https://mdbootstrap.com/docs/jquery/getting-started/download/"
                                target="_blank"></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link waves-effect" href="https://mdbootstrap.com/education/bootstrap/" target="_blank"></a>
                        </li>
                    </ul>

                    <!-- Right -->
                    <ul class="navbar-nav nav-flex-icons">
                        <li class="nav-item">
                            <a href="https://www.facebook.com/mdbootstrap" class="nav-link waves-effect" target="_blank">
                                <i class="fa fa-facebook"></i>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="https://twitter.com/MDBootstrap" class="nav-link waves-effect" target="_blank">
                                <i class="fa fa-twitter"></i>
                            </a>
                        </li>
                        <li class="nav-item">
                            <button onclick="logout()">logout</button>
                            </a>
                        </li>
                    </ul>

                </div>

            </div>
        </nav>
        <!-- Navbar -->

        <!-- Sidebar -->
        <div class="sidebar-fixed position-fixed">

            

            <div class="list-group list-group-flush">
                <a href="#" class="list-group-item active waves-effect">
                    <i class="fa fa-pie-chart mr-3"></i>Dashboard
                </a>
                
            </div>

        </div>
        <!-- Sidebar -->

    </header>
    <!--Main Navigation-->

    <!--Main layout-->
    <main class="pt-5 mx-lg-5">
        <div class="container-fluid mt-5">

            <!-- Heading -->
            <div class="card mb-4 wow fadeIn">

                <!--Card content-->
                <div class="card-body d-sm-flex justify-content-between">

                    <h4 class="mb-2 mb-sm-0 pt-1">
                        <a href="https://mdbootstrap.com/docs/jquery/" target="_blank">Home Page</a>
                        <span>/</span>
                        <span>Dashboard</span>
                    </h4>

                    <form class="d-flex justify-content-center" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                        <!-- Default input -->
                        <input type="search" name="search" placeholder="Type your query" aria-label="Search" class="form-control">
                         <input type="submit" name="search-submit" value="search">  
                            
                        </button>

                    </form>
                     
                                    

                </div>

            </div>
            <!-- Heading -->

            <!--Grid row-->
            <div class="row wow fadeIn">

                <!--Grid column-->
                
                <!--Grid column-->

                <!--Grid column-->
                <div class="col-md-9 mb-4">

                    <!--Card-->
                    <div class="card mb-4">

                        <!-- Card header -->
                        <div class="card-header text-center">
                            Update State
                        </div>

                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">  

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

    
<input type="submit" name="state_submit" value="Update">  
</form>

<button onclick="cust_notes()">See cust maps</button>
<button onclick="Create_notes()">Create Notes</button>
<button onclick="Create_filter()">Create filter</button>
<button onclick="ChangeLoc()">Change Loc</button>
<button onclick="myFunction()">Display all Notes</button>
                        <!--Card content-->
                        <div class="card-body">

                            
                        </div>

                    </div>
<?php
    
 
$ab=$_SESSION["user_id"];
$result = get_friends($ab);

//print_r($result);
$count= count($result['name']);

//echo $count;


?>                     <!--/.Card-->


                    <!--Card-->
                    <div class="card mb-4">

                        <!--Card content-->
                        <div class="card-body">MY FRIENDS

                            <!-- List group links -->
                            <div class="list-group list-group-flush">

                                <?php

                        if($count > 0)
                        {

                        for ($i=0; $i < $count; $i++) { ?>
                                <a class="list-group-item list-group-item-action waves-effect"><?php echo $result["name"][$i] ?>
                                    
                                </a>   

                                <?php 
                    }   
        
                        }

                        else{

                            echo "No Friends";
                        }



                   ?> 

<!--delete from here
                                <a class="list-group-item list-group-item-action waves-effect">Traffic
                                    <span class="badge badge-danger badge-pill pull-right">5%
                                        <i class="fa fa-arrow-down ml-1"></i>
                                    </span>
                                </a>
                                <a class="list-group-item list-group-item-action waves-effect">Orders
                                    <span class="badge badge-primary badge-pill pull-right">14</span>
                                </a>
                                <a class="list-group-item list-group-item-action waves-effect">Issues
                                    <span class="badge badge-primary badge-pill pull-right">123</span>
                                </a>
                                <a class="list-group-item list-group-item-action waves-effect">Messages
                                    <span class="badge badge-primary badge-pill pull-right">8</span>
                                </a>
                            </div>
        till here                -->
                         <!-- List group links -->

                        </div>

                    </div>
                    <!--/.Card-->

                </div>
                <!--Grid column-->

            </div>
            <!--Grid row-->

<?php
//echo "string";
     if(!empty($_POST['add_friends'])){ // Fetching variables of the form which travels in URL
$add_friends_id = $_POST['add_friends'];
//echo $add_friends_id;
add_friends($add_friends_id,$original_user);
$url = 'http://localhost/oingo/dashboard/dashboard.php';
                    echo '<script language="javascript">window.location.href ="'.$url.'"</script>';

}


$result_make = make_friends($ab);

//print_r($result);
if(count($result_make)==0)
    $count_make = 0;
else
    $count_make = count($result_make['name']);


//echo $count_make;


?>   
            <!--Grid row-->
            <div class="row wow fadeIn">

                <!--Grid column-->
                <div class="col-md-6 mb-4">

                    <!--Card-->
                    <div class="card">

                        <!--Card content-->
                        <div class="card-body">

                            <!-- Table  -->
                            <table class="table table-hover">
                                <!-- Table head -->
                                <thead class="blue-grey lighten-4">
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Send request</th>
                                        
                                    </tr>
                                </thead>
                                <!-- Table head -->

                                <!-- Table body -->
                                <tbody>
                                    <?php
                                     if($count_make > 0)
                        {

                        for ($i=0; $i < $count_make; $i++) { ?>


                                    <tr>
                                        <th scope="row"><?php echo $i ?></th>
                                        <td><?php echo $result_make["name"][$i] ?></td>
                                        <td><form name='form' action='' method='post'>
                                            <input class="submit_ultimate" type="submit" value="Send Request" ></td>
                                       <input type="hidden" name="add_friends" value=<?php echo $result_make["user_id"][$i] ?>>
                                        </form>
                                </tr>
                                    <?php
                                }
                            }
                                    ?>
                                   
                                </tbody>
                                <!-- Table body -->
                            </table>
                            <!-- Table  -->

                        </div>

                    </div>
                    <!--/.Card-->

                </div>
                <!--Grid column-->
<?php
    
if(!empty($_POST['accept_friends'])){ // Fetching variables of the form which travels in URL
$accepted_friends_id = $_POST['accept_friends'];
//echo "string";
//echo $accepted_friends_id;
//echo "string";
//add_friends($add_friends_id,$original_user);
update_friends_accept($accepted_friends_id,$original_user);
$url = 'http://localhost/oingo/dashboard/dashboard.php';
                    echo '<script language="javascript">window.location.href ="'.$url.'"</script>';

//header("location:dashboard.php");
}

if(!empty($_POST['block_friends'])){ // Fetching variables of the form which travels in URL
$blocked_friends_id = $_POST['block_friends'];
//echo $blocked_friends_id;
block_friends($blocked_friends_id,$original_user);
$url = 'http://localhost/oingo/dashboard/dashboard.php';
                    echo '<script language="javascript">window.location.href ="'.$url.'"</script>';

}


$result_pending = pending_friends($original_user);
//print_r($result_pending);

//print_r($result);
if(count($result_pending)==0)
    $count_pending = 0;
else
    $count_pending = count($result_pending['name']);

//echo $count_pending;


?>
                <!--Grid column-->
                <div class="col-md-6 mb-4">

                    <!--Card-->
                    <div class="card">

                        <!--Card content-->
                        <div class="card-body">

                            <!-- Table  -->
                            <table class="table table-hover">
                                <!-- Table head -->
                                <thead class="blue lighten-4">
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Accept</th>
                                        <th>Block</th>
                                    </tr>
                                </thead>
                                <!-- Table head -->

                                <!-- Table body -->
                                <tbody>
                                    <?php
                                     if($count_pending > 0)
                        {

                        for ($i=0; $i < $count_pending; $i++) { ?>
                                    <tr>
                                          <th scope="row"><?php echo $i ?></th>
                                        <td><?php echo $result_pending["name"][$i] ?></td>
                                        <td><form name='form' action='' method='post'>
                                            <input class="submit" type="submit" value="Accept" ></td>
                                       <input type="hidden" name="accept_friends" value=<?php echo $result_pending["user_id"][$i] ?>>
                                        </form>
                                        <td><form name='form' action='' method='post'>
                                            <input class="submit" type="submit" value="Block" ></td>
                                       <input type="hidden" name="block_friends" value=<?php echo $result_pending["user_id"][$i] ?>>
                                        </form>
                                    </tr>
                                    <?php
                                }
                            }
                                    ?>
                                </tbody>
                                <!-- Table body -->
                            </table>
                            <!-- Table  -->

                        </div>

                    </div>
                    <!--/.Card-->

                </div>
                <!--Grid column-->

            </div>
            <!--Grid row-->

<?php
    if($flag==1){
?>

            <!--Grid row-->
            <div class="row wow fadeIn">

                <?php
        
                $result_search = get_filter_search($original_user,$search);
        //print_r($result_search);

        if(count($result_search)==0)
            $count_result_search = 0;
        else
            $count_result_search = count($result_search['note_id']);
       // echo $count_result_get_notes;
  
  //echo $result['note_id'][0];
  //echo $result['description'][0];
                ?>
                <!--Grid column-->
                <?php
                    if($count_result_search > 0)
                        {

                        for ($i=0; $i < $count_result_search; $i++) { ?>
                <div class="col-lg-10 col-md-6 mb-4">

                    <!--Card-->
                    <div class="card">

                        <!-- Card header -->
                        <div class="card-header">Note number <?php echo ($i+1)?></div>

                        <!--Card content-->
                        <div class="card-body">

                            <div><?php echo $result_search['description'][$i] ?></div>
                            <div class="card-header"><?php 
                                if($result_search['comment_possible'][$i]==0){
                                    echo "Comments are disabled";
                                }else{
                                    
                                    ?>

                                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                                        <textarea name="description-comment" rows="5" cols="35"></textarea>
                                        <input type="hidden" name="note" value="<?php echo $result_get_notes['note_id'][$i]; ?>" >
                                        <input type="submit" name="Comment" value="Comment">  
                                    </form>
                                    
                                    <br>
                                    <?php

                                    $result_comment = get_comment($result_search['note_id'][$i]);
                                        
                                        if(count($result_comment)==0){

                                            $count_result_comment = 0;

                                            echo "This Note has no comment";   
                                        }
                                        else{
                                            //echo count($result_comment['description']);
                                            //print_r($result_comment);
                                            for ($j=0; $j < count($result_comment['description']); $j++) { 
                                              echo $result_comment['description'][$j];
                                              echo("           --");
                                              echo $result_comment['name'][$j];
                                              ?>
                                                <br> <br>
                                              <?php
                                            }


                                        }
                                        //print_r($result_comment);
                                        /*else
                                            $count_result_comment = sizeof($result_comment['note_id']);
                                    echo $count_result_comment;*/
                                }

                            ?>

                            </div>

                        </div>

                    </div>
                    <!--/.Card-->

                </div>
                <!--Grid column-->
                 <?php
                    }
                }
                else
                {
                    ?>
<!--
                        <div class="col-lg-6 col-md-6 mb-4">
-->
                    <!--Card-->
<!--                    <div class="card">
-->

                        <!-- Card header -->
<!--                        <div class="card-header">Note number <?php //echo ("zdfas")?></div>
-->

                        <!--Card content-->
 <!--                        <div class="card-body">

--> 
<!--                            <div><?php// echo'description'?></div>

                        </div>

                    </div>
      -->              
                    <!--/.Card-->

 <!--               </div>
-->                     <?php


                }
              ?>
                    
                <!--Grid column-->
            </div>
            <!--Grid row-->
<?php
}

?>


<?php
    if($flag==0){
?>

            <!--Grid row-->
            <div class="row wow fadeIn">

                <?php
        
                $result_get_notes = get_filter($original_user);

        if(count($result_get_notes)==0)
            $count_result_get_notes = 0;
        else
            $count_result_get_notes = count($result_get_notes['note_id']);
       // echo $count_result_get_notes;
  
  //echo $result['note_id'][0];
  //echo $result['description'][0];
                ?>
                <!--Grid column-->
                <?php
                    if($count_result_get_notes > 0)
                        {

                        for ($i=0; $i < $count_result_get_notes; $i++) { ?>
                <div class="col-lg-10 col-md-6 mb-4">

                    <!--Card-->
                    <div class="card">

                        <!-- Card header -->
                        <div class="card-header">Note number <?php echo ($i+1)?></div>

                        <!--Card content-->
                        <div class="card-body">

                            <div><?php echo $result_get_notes['description'][$i] ?></div>
                            <div class="card-header"><?php 
                                if($result_get_notes['comment_possible'][$i]==0){
                                    echo "Comments are disabled";
                                }else{
                                    
                                    ?>

                                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                                        <textarea name="description-comment" rows="5" cols="35"></textarea>
                                        <input type="hidden" name="note" value="<?php echo $result_get_notes['note_id'][$i]; ?>" >
                                        <input type="submit" name="Comment" value="Comment">  
                                    </form>
                                    
                                    <br>
                                    <?php

                                    $result_comment = get_comment($result_get_notes['note_id'][$i]);
                                        
                                        if(count($result_comment)==0){

                                            $count_result_comment = 0;

                                            echo "This Note has no comment";   
                                        }
                                        else{
                                            //echo count($result_comment['description']);
                                            //print_r($result_comment);
                                            for ($j=0; $j < count($result_comment['description']); $j++) { 
                                              echo $result_comment['description'][$j];
                                              echo("           --");
                                              echo $result_comment['name'][$j];
                                              ?>
                                                <br> <br>
                                              <?php
                                            }


                                        }
                                        //print_r($result_comment);
                                        /*else
                                            $count_result_comment = sizeof($result_comment['note_id']);
                                    echo $count_result_comment;*/
                                }

                            ?>

                            </div>

                        </div>

                    </div>
                    <!--/.Card-->

                </div>
                <!--Grid column-->
                 <?php
                    }
                }
                else
                {
                    ?>
<!--
                        <div class="col-lg-6 col-md-6 mb-4">
-->
                    <!--Card-->
<!--                    <div class="card">
-->

                        <!-- Card header -->
<!--                        <div class="card-header">Note number <?php //echo ("zdfas")?></div>
-->

                        <!--Card content-->
 <!--                        <div class="card-body">

--> 
<!--                            <div><?php// echo'description'?></div>

                        </div>

                    </div>
      -->              
                    <!--/.Card-->

 <!--               </div>
-->                     <?php


                }
              ?>
                    
                <!--Grid column-->
            </div>
            <!--Grid row-->
<?php
}
$flag=0;
?>
            <!--Grid row-->
          
                                  

        <!--Copyright-->
        <div class="footer-copyright py-3">
            © 2018 Copyright:
            <a href="https://mdbootstrap.com/education/bootstrap/" target="_blank"> Oingo.com </a>
        </div>
        <!--/.Copyright-->

    </footer>
    <!--/.Footer-->

    <!-- SCRIPTS -->
    <!-- JQuery -->
    <script type="text/javascript" src="js/jquery-3.3.1.min.js"></script>
    <!-- Bootstrap tooltips -->
    <script type="text/javascript" src="js/popper.min.js"></script>
    <!-- Bootstrap core JavaScript -->
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    <!-- MDB core JavaScript -->
    <script type="text/javascript" src="js/mdb.min.js"></script>
    <!-- Initializations -->
    <script type="text/javascript">
        // Animations initialization
        new WOW().init();
    </script>

    <!-- Charts -->
    <script>
        // Line
        var ctx = document.getElementById("myChart").getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ["Red", "Blue", "Yellow", "Green", "Purple", "Orange"],
                datasets: [{
                    label: '# of Votes',
                    data: [12, 19, 3, 5, 2, 3],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(255, 159, 64, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255,99,132,1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });

        //pie
        var ctxP = document.getElementById("pieChart").getContext('2d');
        var myPieChart = new Chart(ctxP, {
            type: 'pie',
            data: {
                labels: ["Red", "Green", "Yellow", "Grey", "Dark Grey"],
                datasets: [{
                    data: [300, 50, 100, 40, 120],
                    backgroundColor: ["#F7464A", "#46BFBD", "#FDB45C", "#949FB1", "#4D5360"],
                    hoverBackgroundColor: ["#FF5A5E", "#5AD3D1", "#FFC870", "#A8B3C5", "#616774"]
                }]
            },
            options: {
                responsive: true,
                legend: false
            }
        });


        //line
        var ctxL = document.getElementById("lineChart").getContext('2d');
        var myLineChart = new Chart(ctxL, {
            type: 'line',
            data: {
                labels: ["January", "February", "March", "April", "May", "June", "July"],
                datasets: [{
                        label: "My First dataset",
                        backgroundColor: [
                            'rgba(105, 0, 132, .2)',
                        ],
                        borderColor: [
                            'rgba(200, 99, 132, .7)',
                        ],
                        borderWidth: 2,
                        data: [65, 59, 80, 81, 56, 55, 40]
                    },
                    {
                        label: "My Second dataset",
                        backgroundColor: [
                            'rgba(0, 137, 132, .2)',
                        ],
                        borderColor: [
                            'rgba(0, 10, 130, .7)',
                        ],
                        data: [28, 48, 40, 19, 86, 27, 90]
                    }
                ]
            },
            options: {
                responsive: true
            }
        });


        //radar
        var ctxR = document.getElementById("radarChart").getContext('2d');
        var myRadarChart = new Chart(ctxR, {
            type: 'radar',
            data: {
                labels: ["Eating", "Drinking", "Sleeping", "Designing", "Coding", "Cycling", "Running"],
                datasets: [{
                    label: "My First dataset",
                    data: [65, 59, 90, 81, 56, 55, 40],
                    backgroundColor: [
                        'rgba(105, 0, 132, .2)',
                    ],
                    borderColor: [
                        'rgba(200, 99, 132, .7)',
                    ],
                    borderWidth: 2
                }, {
                    label: "My Second dataset",
                    data: [28, 48, 40, 19, 96, 27, 100],
                    backgroundColor: [
                        'rgba(0, 250, 220, .2)',
                    ],
                    borderColor: [
                        'rgba(0, 213, 132, .7)',
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true
            }
        });

        //doughnut
        var ctxD = document.getElementById("doughnutChart").getContext('2d');
        var myLineChart = new Chart(ctxD, {
            type: 'doughnut',
            data: {
                labels: ["Red", "Green", "Yellow", "Grey", "Dark Grey"],
                datasets: [{
                    data: [300, 50, 100, 40, 120],
                    backgroundColor: ["#F7464A", "#46BFBD", "#FDB45C", "#949FB1", "#4D5360"],
                    hoverBackgroundColor: ["#FF5A5E", "#5AD3D1", "#FFC870", "#A8B3C5", "#616774"]
                }]
            },
            options: {
                responsive: true
            }
        });
    </script>

    <!--Google Maps-->
    <script src="https://maps.google.com/maps/api/js"></script>
    <script>
        // Regular map
        function regular_map() {
            var var_location = new google.maps.LatLng(40.725118, -73.997699);

            var var_mapoptions = {
                center: var_location,
                zoom: 14
            };

            var var_map = new google.maps.Map(document.getElementById("map-container"),
                var_mapoptions);

            var var_marker = new google.maps.Marker({
                position: var_location,
                map: var_map,
                title: "New York"
            });
        }

        // Initialize maps
        google.maps.event.addDomListener(window, 'load', regular_map);

        new Chart(document.getElementById("horizontalBar"), {
            "type": "horizontalBar",
            "data": {
                "labels": ["Red", "Orange", "Yellow", "Green", "Blue", "Purple", "Grey"],
                "datasets": [{
                    "label": "My First Dataset",
                    "data": [22, 33, 55, 12, 86, 23, 14],
                    "fill": false,
                    "backgroundColor": ["rgba(255, 99, 132, 0.2)", "rgba(255, 159, 64, 0.2)",
                        "rgba(255, 205, 86, 0.2)", "rgba(75, 192, 192, 0.2)",
                        "rgba(54, 162, 235, 0.2)",
                        "rgba(153, 102, 255, 0.2)", "rgba(201, 203, 207, 0.2)"
                    ],
                    "borderColor": ["rgb(255, 99, 132)", "rgb(255, 159, 64)", "rgb(255, 205, 86)",
                        "rgb(75, 192, 192)", "rgb(54, 162, 235)", "rgb(153, 102, 255)",
                        "rgb(201, 203, 207)"
                    ],
                    "borderWidth": 1
                }]
            },
            "options": {
                "scales": {
                    "xAxes": [{
                        "ticks": {
                            "beginAtZero": true
                        }
                    }]
                }
            }
        });

function myFunction() {
 // document.getElementById("demo").innerHTML = "Hello World";
  
 location.replace("http://localhost/oingo/location-notes-done.php");

}

function Create_notes() {
 // document.getElementById("demo").innerHTML = "Hello World";
  
 location.replace("http://localhost/oingo/notes.php");

}
function Create_filter() {
 // document.getElementById("demo").innerHTML = "Hello World";
  
 location.replace("http://localhost/oingo/Filter.php");

}
function cust_notes() {
 // document.getElementById("demo").innerHTML = "Hello World";
  
 location.replace("http://localhost/oingo/cust_maps_display.php");

}
function logout() {
 // document.getElementById("demo").innerHTML = "Hello World";
  
 location.replace("http://localhost/oingo/logout.php");

}




function ChangeLoc() {
 // document.getElementById("demo").innerHTML = "Hello World";
  
 location.replace("http://localhost/oingo/cust_maps.php");

}


    </script>
</body>

</html>