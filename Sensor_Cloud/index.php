<?php
   include('dbConnect.php');
   session_start();
   
   $user_check = $_SESSION['login_user'];
   
   $ses_sql = mysqli_query($db,"select id, name, type, support_plan, email from users where email = '$user_check' ");

   //$row = mysqli_fetch_array($ses_sql,MYSQLI_ASSOC);
   $row = $ses_sql->fetch_assoc();
   
   $login_session = $row['email'];
   $user_type = $row['type'];
   $user_name = $row['name'];
   $user_id = $row['id'];
   $user_plan = $row['support_plan'];

   $ses_sql = mysqli_query($db,"select * from billing_model where plan = '$user_plan' and state = 'Active'");
   if(!empty($ses_sql)){
   //$row = mysqli_fetch_array($ses_sql,MYSQLI_ASSOC);
   $row = $ses_sql->fetch_assoc();
   $running = $row['price'];
   }

   $ses_sql = mysqli_query($db,"select * from billing_model where plan = '$user_plan' and state = 'Paused'");
   if(!empty($ses_sql)){
   //$row = mysqli_fetch_array($ses_sql,MYSQLI_ASSOC);
   $row = $ses_sql->fetch_assoc();
   $paused = $row['price'];
   }

   $ses_sql = mysqli_query($db,"select usage_details.used_hours, usage_details.paused_hours from SENSOR_LIST inner join usage_details on SENSOR_LIST.ID = usage_details.sensor_id where SENSOR_LIST.owner='$user_id'");

    $subtotal = 0;
    
    //while($row = mysqli_fetch_array($ses_sql,MYSQLI_ASSOC)){
		if(!empty($ses_sql)){
	while($row = mysqli_fetch_array($ses_sql,MYSQLI_ASSOC)){
        $subtotal = $subtotal + (($row["used_hours"]*$running) + ($row["paused_hours"]*$paused));
    }
		}

   function getCount($table, $parameter, $value) {
    global $user_id;
	if("SENSOR_LIST"==$table){
		if ($parameter == null) {
        $sql = mysqli_query($GLOBALS['db'],"select count(*) as count from $table where OWNER='$user_id'");
      } else {
        $sql = mysqli_query($GLOBALS['db'],"select count(*) as count from $table where $parameter='$value' AND owner='$user_id'");
      }
	}else if("clusters"==$table){
		if ($parameter == null) {
        $sql = mysqli_query($GLOBALS['db'],"select count(distinct id) as count from $table where user_id='$user_id' group by  id");
      } else {
        $sql = mysqli_query($GLOBALS['db'],"select count(*) as count from $table where $parameter='$value' AND user_id='$user_id'");
      }
	}
	else{
      if ($parameter == null) {
        $sql = mysqli_query($GLOBALS['db'],"select count(*) as count from $table where user_id='$user_id'");
      } else {
        $sql = mysqli_query($GLOBALS['db'],"select count(*) as count from $table where $parameter='$value' AND user_id='$user_id'");
      }
	}
	  if(!empty($sql)){
      $row = mysqli_fetch_array($sql,MYSQLI_ASSOC);
      return $row['count'];
	  }
	
   }

   if(getCount("SENSOR_LIST", null, null) == 0) {
      $sensors = false;
   } else {
      $sensors = true;
   }

   if(!isset($_SESSION['login_user'])){
      header("location:login.php");
   }
   if($user_type == "admin") {
      header("location:index-admin.php");
   }

   /*if($_SERVER["REQUEST_METHOD"] == "POST") {

       if (isset($_POST['action']) && isset($_POST['id'])) {
          $id = $_POST['id'];

          if ($_POST['action'] == 'Start') {
            $sql = mysqli_query($db,"update sensors set status = 'Active' where id = $id");
            $sql_usage = mysqli_query($db,"update usage_details set update_time = NOW() where sensor_id = $id");
          }

          if ($_POST['action'] == 'Pause') {

            $sql = mysqli_query($db,"update sensors set status = 'Paused' where id = $id");
            
            date_default_timezone_set('America/Los_Angeles');
                         
            $ses_sql = mysqli_query($db,"select * from usage_details where user_id='$user_id' AND sensor_id = $id");
              
            $row = mysqli_fetch_array($ses_sql,MYSQLI_ASSOC);
            
            $startTime = new DateTime($row['update_time']);
            $dt = date("Y-m-d H:i:s");
            $currentTime = new DateTime($dt);
            $diff = $currentTime->diff($startTime);
            $hours = $diff->h;
            $mins = $diff->i;
            $hours = $hours + ($diff->days*24);
                            
            $sql_usage = mysqli_query($db,"update usage_details set total_hours = total_hours + '$hours' , total_mins = total_mins + '$mins' , update_time = NOW() where sensor_id = $id");
          }

          if ($_POST['action'] == 'Stop') {
            $sql = mysqli_query($db,"update sensors set status = 'Terminated' where id = $id");
            
            date_default_timezone_set('America/Los_Angeles');
                         
            $ses_sql = mysqli_query($db,"select * from usage_details inner join sensors on usage_details.sensor_id = sensors.id where usage_details.user_id='$user_id' AND sensor_id = $id");
              

            $row = mysqli_fetch_array($ses_sql,MYSQLI_ASSOC);
            
            if ($row['status'] != "Paused") {
              $startTime = new DateTime($row['update_time']);
              $dt = date("Y-m-d H:i:s");
              $currentTime = new DateTime($dt);
              $diff = $currentTime->diff($startTime);
              $hours = $diff->h;
              $mins = $diff->i;
              $hours = $hours + ($diff->days*24);
                            
              $sql_usage = mysqli_query($db,"update usage_details set total_hours = total_hours + '$hours' , total_mins = total_mins + '$mins' , update_time = NOW() where sensor_id = $id");
            }
            
          }
      }

      if (isset($_POST['clusterAction']) && isset($_POST['id'])) {
          $id = $_POST['id'];

          if ($_POST['clusterAction'] == 'Start') {
            $sql = mysqli_query($db,"update clusters set status = 'Active' where id = $id");
            $sql_usage = mysqli_query($db,"update usage_details set update_time = NOW() where cluster_id = $id");
          }

          if ($_POST['clusterAction'] == 'Pause') {

            $sql = mysqli_query($db,"update clusters set status = 'Paused' where id = $id");
            
            date_default_timezone_set('America/Los_Angeles');
                         
            $ses_sql = mysqli_query($db,"select * from usage_details where user_id='$user_id' AND cluster_id = $id");
              
            $row = mysqli_fetch_array($ses_sql,MYSQLI_ASSOC);
            
            $startTime = new DateTime($row['update_time']);
            $dt = date("Y-m-d H:i:s");
            $currentTime = new DateTime($dt);
            $diff = $currentTime->diff($startTime);
            $hours = $diff->h;
            $mins = $diff->i;
            $hours = $hours + ($diff->days*24);
                            
            $sql_usage = mysqli_query($db,"update usage_details set total_hours = total_hours + '$hours' , total_mins = total_mins + '$mins' , update_time = NOW() where cluster_id = $id");
          }

          if ($_POST['clusterAction'] == 'Stop') {
            $sql = mysqli_query($db,"update clusters set status = 'Terminated' where id = $id");
            
            date_default_timezone_set('America/Los_Angeles');
                         
            $ses_sql = mysqli_query($db,"select * from usage_details inner join clusters on usage_details.cluster_id = clusters.id where usage_details.user_id='$user_id' AND cluster_id = $id");
              

            $row = mysqli_fetch_array($ses_sql,MYSQLI_ASSOC);
            
            if ($row['status'] != "Paused") {
              $startTime = new DateTime($row['update_time']);
              $dt = date("Y-m-d H:i:s");
              $currentTime = new DateTime($dt);
              $diff = $currentTime->diff($startTime);
              $hours = $diff->h;
              $mins = $diff->i;
              $hours = $hours + ($diff->days*24);
                            
              $sql_usage = mysqli_query($db,"update usage_details set total_hours = total_hours + '$hours' , total_mins = total_mins + '$mins' , update_time = NOW() where cluster_id = $id");
            }
            
        }
      }
    }*/

    if($_SERVER["REQUEST_METHOD"] == "POST") {

       if (isset($_POST['action']) && isset($_POST['id'])) {
          $id = $_POST['id'];

          if ($_POST['action'] == 'Pause') {
    
              date_default_timezone_set("America/Los_Angeles");
              $currentDate= date("Y-m-d H:i:s");
              $datetime1 = strtotime($currentDate);

              $result = mysqli_query($db,"Select * from usage_details where sensor_id = $id");
              while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)) {
                $StartPauseTime=$row['update_time'];
                $UsedHours=$row['used_hours'];
              }
              $datetime2 = strtotime($StartPauseTime);

              $diffSeconds= $datetime1-$datetime2;
              $UsedHours=$UsedHours+($diffSeconds/3600);
              $UsedHours = round($UsedHours, 2);
              
              
              $sql = mysqli_query($db,"update SENSOR_LIST set status = 'Paused' where id = $id");
              $sql_usage = mysqli_query($db,"update usage_details set update_time = '$currentDate', used_hours = '$UsedHours' where sensor_id = $id");
            
              if(! $sql_usage )
              {
                die('Could not update data: ' . mysql_error());
              }
          }

          if ($_POST['action'] == 'Start') {

                date_default_timezone_set("America/Los_Angeles");
                $currentDate= date("Y-m-d H:i:s");
                $datetime1 = strtotime($currentDate);

                $result = mysqli_query($db,"Select * from usage_details where sensor_id = $id");
                while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)) {
                  $StartPauseTime=$row['update_time'];
                  $PausedHours=$row['paused_hours'];
                }
                
                $datetime2 = strtotime($StartPauseTime);

                $diffSeconds = $datetime1-$datetime2;
                $PausedHours = $PausedHours+($diffSeconds/3600);
                $PausedHours = round($PausedHours, 2);
                  
                $sql = mysqli_query($db,"update SENSOR_LIST set status = 'Active' where id = $id");
                $sql_usage = mysqli_query($db,"update usage_details set update_time = '$currentDate', paused_hours = '$PausedHours' where sensor_id = $id");
              
                if(! $sql_usage )
                {
                  die('Could not update data: ' . mysql_error());
                }
          }

          if ($_POST['action'] == 'Stop') {
    
                  date_default_timezone_set("America/Los_Angeles");
                  $currentDate= date("Y-m-d H:i:s");
                  $datetime1 = strtotime($currentDate);

                  $result = mysqli_query($db,"Select * from usage_details inner join SENSOR_LIST on SENSOR_LIST.id = usage_details.sensor_id where sensor_id = $id");
                  while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)) {
                    $SensorStatus=$row['status'];
                    $StartPauseTime=$row['update_time'];
                    $PausedHours=$row['paused_hours'];
                    $UsedHours=$row['used_hours'];
                  }

                  $datetime2 = strtotime($StartPauseTime);
                  $diffSeconds= $datetime1-$datetime2;
                  
                  if($SensorStatus =="Paused") {
                    $PausedHours=$PausedHours+($diffSeconds/3600);
                    $PausedHours = round($PausedHours, 2);
                    $q="paused_hours='$PausedHours'";
                  }
                  else {
                    $UsedHours=$UsedHours+($diffSeconds/3600);
                    $UsedHours = round($UsedHours, 2);
                    $q="used_hours='$UsedHours'";
                  }
                    
                  $sql = mysqli_query($db,"update SENSOR_LIST set status = 'Terminated' where id = $id");
                  $sql_usage = mysqli_query($db,"update usage_details set update_time = '$currentDate', $q where sensor_id = $id");
                
                  if(! $sql_usage )
                  {
                    die('Could not update data: ' . mysql_error());
                  }
          }
      }

      if (isset($_POST['clusterAction']) && isset($_POST['id'])) {
          $id = $_POST['id'];
		if ($_POST['clusterAction'] == 'Pause') {
		    
		              date_default_timezone_set("America/Los_Angeles");
		              $currentDate= date("Y-m-d H:i:s");
		              $datetime1 = strtotime($currentDate);

		              $result = mysqli_query($db,"Select * from usage_details where cluster_id = $id");
		              while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)) {
		                $StartPauseTime=$row['update_time'];
		                $UsedHours=$row['used_hours'];
		              }
		              $datetime2 = strtotime($StartPauseTime);

		              $diffSeconds= $datetime1-$datetime2;
		              $UsedHours=$UsedHours+($diffSeconds/3600);
		              $UsedHours = round($UsedHours, 2);
		              
		              
		              $sql = mysqli_query($db,"update clusters set status = 'Paused' where id = $id");
		              $sql_usage = mysqli_query($db,"update usage_details set update_time = '$currentDate', used_hours = '$UsedHours' where cluster_id = $id");
		            
		              if(! $sql_usage )
		              {
		                die('Could not update data: ' . mysql_error());
		              }
		          }

		          if ($_POST['clusterAction'] == 'Start') {

		                date_default_timezone_set("America/Los_Angeles");
		                $currentDate= date("Y-m-d H:i:s");
		                $datetime1 = strtotime($currentDate);

		                $result = mysqli_query($db,"Select * from usage_details where cluster_id = $id");
		                while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)) {
		                  $StartPauseTime=$row['update_time'];
		                  $PausedHours=$row['paused_hours'];
		                }
		                
		                $datetime2 = strtotime($StartPauseTime);

		                $diffSeconds = $datetime1-$datetime2;
		                $PausedHours = $PausedHours+($diffSeconds/3600);
		                $PausedHours = round($PausedHours, 2);
		                  
		                $sql = mysqli_query($db,"update clusters set status = 'Active' where id = $id");
		                $sql_usage = mysqli_query($db,"update usage_details set update_time = '$currentDate', paused_hours = '$PausedHours' where cluster_id = $id");
		              
		                if(! $sql_usage )
		                {
		                  die('Could not update data: ' . mysql_error());
		                }
		          }

		          if ($_POST['clusterAction'] == 'Stop') {
		    
		                  date_default_timezone_set("America/Los_Angeles");
		                  $currentDate= date("Y-m-d H:i:s");
		                  $datetime1 = strtotime($currentDate);

		                  $result = mysqli_query($db,"Select * from usage_details inner join clusters on clusters.id = usage_details.cluster_id where cluster_id = $id");
		                  while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)) {
		                    $SensorStatus=$row['status'];
		                    $StartPauseTime=$row['update_time'];
		                    $PausedHours=$row['paused_hours'];
		                    $UsedHours=$row['used_hours'];
		                  }

		                  $datetime2 = strtotime($StartPauseTime);
		                  $diffSeconds= $datetime1-$datetime2;
		                  
		                  if($SensorStatus =="Paused") {
		                    $PausedHours=$PausedHours+($diffSeconds/3600);
		                    $PausedHours = round($PausedHours, 2);
		                    $q="paused_hours='$PausedHours'";
		                  }
		                  else {
		                    $UsedHours=$UsedHours+($diffSeconds/3600);
		                    $UsedHours = round($UsedHours, 2);
		                    $q="used_hours='$UsedHours'";
		                  }
		                    
		                  $sql = mysqli_query($db,"update clusters set status = 'Terminated' where id = $id");
		                  $sql_usage = mysqli_query($db,"update usage_details set update_time = '$currentDate', $q where cluster_id = $id");
		                
		                  if(! $sql_usage )
		                  {
		                    die('Could not update data: ' . mysql_error());
		                  }
		          }
		      }
    }

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Sensor Cloud! | Dashboard</title>

    <!-- Bootstrap -->
    <link href="vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <!-- NProgress -->
    <link href="vendors/nprogress/nprogress.css" rel="stylesheet">
    <!-- iCheck -->
    <link href="vendors/iCheck/skins/flat/green.css" rel="stylesheet">
    <!-- bootstrap-progressbar -->
    <link href="vendors/bootstrap-progressbar/css/bootstrap-progressbar-3.3.4.min.css" rel="stylesheet">
    <!-- JQVMap -->
    <link href="vendors/jqvmap/dist/jqvmap.min.css" rel="stylesheet"/>
    <!-- bootstrap-daterangepicker -->
    <link href="vendors/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">

    <!-- Custom Theme Style -->
    <link href="build/css/custom.min.css" rel="stylesheet">
  </head>

  <body class="nav-md">
    <div class="container body">
      <div class="main_container">
        <div class="col-md-3 left_col">
          <div class="left_col scroll-view">
            <div class="navbar nav_title" style="border: 0;">
              <a href="index.php" class="site_title"><i class="fa fa-cloud"></i> <span>Sensor Cloud!</span></a>
            </div>

            <div class="clearfix"></div>

            <!-- menu profile quick info -->
            <div class="profile">
              <div class="profile_pic">
                <img src="images/user.png" alt="..." class="img-circle profile_img">
              </div>
              <div class="profile_info">
                <span>Welcome,</span>
                <h2><?php echo $user_name; ?></h2>
              </div>
            </div>
            <!-- /menu profile quick info -->

            <br />

            <!-- sidebar menu -->
            <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
              <div class="menu_section">
                <h3>General</h3>
                <ul class="nav side-menu">
                  <li><a href="index.php"><i class="fa fa-home"></i> Home <span class="fa fa-chevron"></span></a>
                    
                  </li>
                  
                  <li><a><i class="fa fa-desktop"></i> Manage Sensors <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="add-sensor.php">Add Sensors</a></li>
                      <li><a href="manage-sensor.php">Manage Sensors</a></li>
                    <!--  <li><a href="virtualization.php">Sensor Virtualization</a></li>-->
					<li><a href="add-cluster.php">Add Cluster</a></li>
					   <li><a href="visualization.php">Sensor Data Analytics</a></li>
                    </ul>
                  </li>
                  <!--<li><a><i class="fa fa-table"></i> Manage Clusters <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="">Add Clusters</a></li>
                      <li><a href="">Manage Clusters</a></li>
                    </ul>
                  </li>-->
                  </ul>
                  </div>

            </div>
            <!-- /sidebar menu -->

           
          </div>
        </div>

        <!-- top navigation -->
        <div class="top_nav">
          <div class="nav_menu">
            <nav>
              <div class="nav toggle">
                <a id="menu_toggle"><i class="fa fa-bars"></i></a>
              </div>

              <ul class="nav navbar-nav navbar-right">
                <li class="">
                  <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                    <img src="images/user.png" alt=""><?php echo $user_name; ?>
                    <span class=" fa fa-angle-down"></span>
                  </a>
                  <ul class="dropdown-menu dropdown-usermenu pull-right">
                    
                    <li>
                      <a href="invoice.php">
                        <span class="badge bg-red pull-right">$<?php echo $subtotal + round($subtotal*0.093, 2); ?></span>
                        <span>Billing</span>
                      </a>
                    </li>
                    <li><a href="logout.php"><i class="fa fa-sign-out pull-right"></i> Log Out</a></li>
                  </ul>
                </li>

                
                  </ul>
                </li>
              </ul>
            </nav>
          </div>
        </div>
        <!-- /top navigation -->

        <!-- page content -->
        <div class="right_col" role="main">
          <div class="">
            <div class="row top_tiles">
              
              <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <div class="tile-stats">
                  <div class="icon"><i class="fa fa-feed"></i></div>
                  <div class="count"><?php echo getCount("SENSOR_LIST", null, null); ?></div>
                  <h3>Total Sensors</h3>
				  
                  <p>Number of sensors subscribed.</p>
                </div>
              </div>
              <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <div class="tile-stats">
                  <div class="icon"><i class="fa fa-crosshairs"></i></div>
                  <div class="count"><?php echo getCount("clusters", null, null); ?></div>
                  <h3>Total Clusters</h3>
                  <p>Number of clusters subscribed.</p>
                </div>
              </div>
              <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <div class="tile-stats">
                  <div class="icon"><i class="fa fa-money"></i></div>
                  <div class="count">$<?php echo $subtotal + round($subtotal*0.093, 2); ?></div>
                  <h3>Due Bill</h3>
                  <p>Bill amount due till date.</p>
                </div>
              </div>
              <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <div class="tile-stats">
                  <div class="icon"><i class="fa fa-archive"></i></div>
                  <div class="count">$0.00</div>
                  <h3>Paid Bill</h3>
                  <p>Bill amount paid till date.</p>
                </div>
              </div>
            </div>
          <!-- /top tiles -->


          <div class="row">

            <div class="col-md-4 col-sm-4 col-xs-12">
              <div class="x_panel tile fixed_height_320 overflow_hidden">
                <div class="x_title">
                  <h2>Sensor Types</h2>
                  
                  <div class="clearfix"></div>
                </div>
                <div class="x_content">
                  <table class="" style="width:100%">
                    <tr>
                      <th style="width:37%;">
                        <p>All types</p>
                      </th>
                      <th>
                        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-7">
                          <p class="">Sensor</p>
                        </div>
                        <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5">
                          <p class="">Progress</p>
                        </div>
                      </th>
                    </tr>
                    <tr>
                      <td>
                        <canvas id="canvas1" height="140" width="140" style="margin: 15px 10px 10px 0"></canvas>
                      </td>
                      <td>
                        <table class="tile_info">
                          <tr>
                            <td>
                              <p><i class="fa fa-square blue"></i>Temperature </p>
                            </td>
                            <td>
                            <?php 
                            if($sensors) {
                              echo round(getCount("SENSOR_LIST", "type", "Temperature")/getCount("SENSOR_LIST", null, null)*100); 
                              } 
                              else {
                                echo 0;
                              }
                            ?>%</td>
                          </tr>
                          <tr>
                            <td>
                              <p><i class="fa fa-square green"></i>Sea Levels </p>
                            </td>
                            <td><?php 
                            if($sensors) {
                              echo round(getCount("SENSOR_LIST", "type", "Sea Levels")/getCount("SENSOR_LIST", null, null)*100); 
                              } 
                              else {
                                echo 0;
                              }
                            ?>%</td>
                          </tr>
                          <tr>
                            <td>
                              <p><i class="fa fa-square purple"></i>Pressure </p>
                            </td>
                            <td><?php 
                            if($sensors) {
                              echo round(getCount("SENSOR_LIST", "type", "Pressure")/getCount("SENSOR_LIST", null, null)*100); 
                              } 
                              else {
                                echo 0;
                              }
                            ?>%</td>
                          </tr>
                          <tr>
                            <td>
                              <p><i class="fa fa-square aero"></i>Salinity </p>
                            </td>
                            <td><?php 
                            if($sensors) {
                              echo round(getCount("SENSOR_LIST", "type", "Salinity")/getCount("SENSOR_LIST", null, null)*100); 
                              } 
                              else {
                                echo 0;
                              }
                            ?>%</td>
                          </tr>
                          <tr>
                            <td>
                              <p><i class="fa fa-square red"></i>Conductivity </p>
                            </td>
                            <td><?php 
                            if($sensors) {
                              echo round(getCount("SENSOR_LIST", "type", "conductivity")/getCount("SENSOR_LIST", null, null)*100); 
                              } 
                              else {
                                echo 0;
                              }
                            ?>%</td>
                          </tr>
                        </table>
                      </td>
                    </tr>
                  </table>
                </div>
              </div>
            </div>

<div class="col-md-4 col-sm-4 col-xs-12">
              <div class="x_panel tile fixed_height_320">
                <div class="x_title">
                  <h2>Sensor Status</h2>
                  
                  <div class="clearfix"></div>
                </div>
                <div class="x_content">
                  <h4>Sensors classified by status</h4>
                  
                  <div class="widget_summary" style="margin: 10px 0 0 0">
                    <div class="w_left w_25">
                      <span>Active</span>
                    </div>
                    <div class="w_center w_55">
                      <div class="progress">
                        <div class="progress-bar bg-green" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo getCount("SENSOR_LIST", "status", "active")/getCount("SENSOR_LIST", null, null)*100; ?>%;">
                          <span class="sr-only">60% Complete</span>
                        </div>
                      </div>
                    </div>
                    <div class="w_right w_20">
                      <span><?php echo getCount("SENSOR_LIST", "status", "active") ?></span>
                    </div>
                    <div class="clearfix"></div>
                  </div>

                  <div class="widget_summary">
                    <div class="w_left w_25">
                      <span>Paused</span>
                    </div>
                    <div class="w_center w_55">
                      <div class="progress">
                        <div class="progress-bar bg-blue" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo getCount("SENSOR_LIST", "status", "paused")/getCount("SENSOR_LIST", null, null)*100; ?>%;">
                          <span class="sr-only">60% Complete</span>
                        </div>
                      </div>
                    </div>
                    <div class="w_right w_20">
                      <span><?php echo getCount("SENSOR_LIST", "status", "paused") ?></span>
                    </div>
                    <div class="clearfix"></div>
                  </div>
                  <div class="widget_summary">
                    <div class="w_left w_25">
                      <span>Terminated</span>
                    </div>
                    <div class="w_center w_55">
                      <div class="progress">
                        <div class="progress-bar bg-red" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo getCount("SENSOR_LIST", "status", "terminated")/getCount("SENSOR_LIST", null, null)*100; ?>%;">
                          <span class="sr-only">60% Complete</span>
                        </div>
                      </div>
                    </div>
                    <div class="w_right w_20">
                      <span><?php echo getCount("SENSOR_LIST", "status", "terminated") ?></span>
                    </div>
                    <div class="clearfix"></div>
                  </div>
                  

                </div>
              </div>
            </div>
            <div class="col-md-4 col-sm-4 col-xs-12">
              <div class="x_panel tile fixed_height_320">
                <div class="x_title">
                  <h2>Cluster Status</h2>
                  
                  <div class="clearfix"></div>
                </div>
                <div class="x_content">
                  <h4>Clusters classified by status</h4>
                  <div class="widget_summary" style="margin: 10px 0 0 0">
                    <div class="w_left w_25">
                      <span>Active</span>
                    </div>
                    <div class="w_center w_55">
                      <div class="progress">
                        <div class="progress-bar bg-green" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo getCount("clusters", "status", "active")/getCount("clusters", null, null)*100; ?>%;">
                          <span class="sr-only">60% Complete</span>
                        </div>
                      </div>
                    </div>
                    <div class="w_right w_20">
                      <span><?php echo getCount("clusters", "status", "active") ?></span>
                    </div>
                    <div class="clearfix"></div>
                  </div>

                  <div class="widget_summary">
                    <div class="w_left w_25">
                      <span>Paused</span>
                    </div>
                    <div class="w_center w_55">
                      <div class="progress">
                        <div class="progress-bar bg-blue" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo getCount("clusters", "status", "paused")/getCount("clusters", null, null)*100; ?>%;">
                          <span class="sr-only">60% Complete</span>
                        </div>
                      </div>
                    </div>
                    <div class="w_right w_20">
                      <span><?php echo getCount("clusters", "status", "paused") ?></span>
                    </div>
                    <div class="clearfix"></div>
                  </div>
                  <div class="widget_summary">
                    <div class="w_left w_25">
                      <span>Terminated</span>
                    </div>
                    <div class="w_center w_55">
                      <div class="progress">
                        <div class="progress-bar bg-red" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo getCount("clusters", "status", "terminated")/getCount("clusters", null, null)*100; ?>%;">
                          <span class="sr-only">60% Complete</span>
                        </div>
                      </div>
                    </div>
                    <div class="w_right w_20">
                      <span><?php echo getCount("clusters", "status", "terminated") ?></span>
                    </div>
                    <div class="clearfix"></div>
                  </div>
                  

                </div>
              </div>
            </div>
            </div>



            <div class="row">
               <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Sensors</h2>
                    
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <p class="text-muted font-13 m-b-30">
                      
                    </p>
                    <table id="datatable1" class="table table-striped table-bordered">
                      <thead>
                         <tr>
                          <th>Name</th>
                          <th>Type</th>
                          <th>Location</th>
                          <th>Status</th>
                          <th>Start date</th>
                          <th>Used Time</th>
                          <th>Paused Time</th>
                          <th>Change Status</th>
                        </tr>
                      </thead>
                      <tbody>
                      <?php
                         
                         date_default_timezone_set('America/Los_Angeles');
                         
                         $ses_sql = mysqli_query($db,"select SENSOR_LIST.latitude, SENSOR_LIST.longitude, SENSOR_LIST.id, SENSOR_LIST.sensor_id, SENSOR_LIST.type, SENSOR_LIST.ip_address, SENSOR_LIST.status, SENSOR_LIST.time_created, usage_details.used_hours, usage_details.paused_hours, usage_details.update_time from SENSOR_LIST inner join usage_details on SENSOR_LIST.id = usage_details.sensor_id where SENSOR_LIST.owner='$user_id'");
						if(!empty($ses_sql)){
                         while($row = mysqli_fetch_array($ses_sql,MYSQLI_ASSOC)){
                            
                            echo '<tr>
                            <td>'.$row["sensor_id"].'</td>
                            <td>'.$row["type"].'</td>
                            <td>Lat: '.$row["latitude"].' Lon: '.$row["longitude"].'</td>
                            <td>'.$row["status"].'</td>
                            <td>'.date_format(new DateTime($row["time_created"]), 'd M Y').'</td>
                            <td>'.$row["used_hours"].' hours</td>
                            <td>'.$row["paused_hours"].' hours</td>';
                            
                            echo '<td>';
                            if ($row['status'] != "Terminated") {
                              echo '<form method="post" action="">';
                            if ($row["status"] == "Active") {
                              echo '<input type="submit" name="action" class="btn btn-default btn-xs btn-success" value="Start" disabled/ >
                              <input type="submit" name="action" class="btn btn-default btn-xs btn-primary" value="Pause" / >
                              <input type="submit" name="action" class="btn btn-default btn-xs btn-danger" value="Stop" / >
                              <input type="hidden" name="id" value="'.$row["id"].'" />';
                            }
                            if ($row["status"] == "Paused") {
                              echo '<input type="submit" name="action" class="btn btn-default btn-xs btn-success" value="Start" / >
                              <input type="submit" name="action" class="btn btn-default btn-xs btn-primary" value="Pause" disabled/ >
                              <input type="submit" name="action" class="btn btn-default btn-xs btn-danger" value="Stop" / >
                              <input type="hidden" name="id" value="'.$row["id"].'" />';
                            }
                            if ($row["status"] == "Terminated") {
                              echo '<input type="submit" name="action" class="btn btn-default btn-xs btn-success" value="Start" / >
                              <input type="submit" name="action" class="btn btn-default btn-xs btn-primary" value="Pause" / >
                              <input type="submit" name="action" class="btn btn-default btn-xs btn-danger" value="Stop" disabled/ >
                              <input type="hidden" name="id" value="'.$row["id"].'" />';
                            }
                            echo '  
                            </form>';
                            }
                            echo '
                            </td>
                            </tr>';
                            
                         }
						}
                        
                        ?>
                    
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
              </div>

              <div class="row">
               <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Clusters</h2>
                    
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content" id="cluster_details">
                    <p class="text-muted font-13 m-b-30">
                      
                    </p>
                    <table id="datatable2" class="table table-striped table-bordered">
                      <thead>
                        <tr>
                          <th>Name</th>
                          <th>No. of Sensors</th>
                          <th>Status</th>
                          <th>Start date</th>
                          <th>Used Time</th>
                          <th>Paused Time</th>
                          <th>Change Status</th>
                        </tr>
                      </thead>

                      <tbody>
                        <?php
                         
                         
                         //$ses_sql = mysqli_query($db,"select cls.id, sl.sensor_id, cls.name,  cls.status, cls.time_created, ud.used_hours, ud.paused_hours, ud.update_time from clusters cls, SENSOR_LIST sl, usage_details ud where cls.id = sl.CLUSTER_ID and ud.sensor_id = sl.ID and cls.user_id = $user_id group by cls.id, sl.sensor_id");
						 $ses_sql = mysqli_query($db,"select cls.id,count(sensor_usage.sensor_id) as sensor_count, sum(sensor_usage.used_hours) as used_hours,sum(sensor_usage.paused_hours) as paused_hours,max(sensor_usage.update_time) as update_time
								,cls.name,cls.status,cls.time_created
									from clusters cls,
														(select sl.id, sl.sensor_id,ud.used_hours,ud.paused_hours,sl.cluster_id,ud.update_time from SENSOR_LIST sl, usage_details ud where 
									ud.sensor_id = sl.id and ud.user_id='$user_id' ) as sensor_usage 
									where cls.id=sensor_usage.cluster_id and cls.user_id='$user_id'");
						if(!empty($ses_sql)){
                         while($row = mysqli_fetch_array($ses_sql,MYSQLI_ASSOC)){
                            
                            echo '<tr>
                            <td>'.$row["name"].'</td>
                            <td>'.$row["sensor_count"].'</td>
                            <td>'.$row["status"].'</td>
                            <td>'.date_format(new DateTime($row["time_created"]), 'd M Y').'</td>
                            <td>'.$row["used_hours"].'</td>
                            <td>'.$row["paused_hours"].'</td>';
                            echo '<td>';
                            if ($row['status'] != "Terminated") {
                              echo '<form method="post" action="">';
                            if ($row["status"] == "Active") {
                              echo '<input type="submit" name="clusterAction" class="btn btn-default btn-xs btn-success" value="Start" disabled/ >
                              <input type="submit" name="clusterAction" class="btn btn-default btn-xs btn-primary" value="Pause" / >
                              <input type="submit" name="clusterAction" class="btn btn-default btn-xs btn-danger" value="Stop" / >
                              <input type="hidden" name="id" value="'.$row["id"].'" />';
                            }
                            if ($row["status"] == "Paused") {
                              echo '<input type="submit" name="clusterAction" class="btn btn-default btn-xs btn-success" value="Start" / >
                              <input type="submit" name="clusterAction" class="btn btn-default btn-xs btn-primary" value="Pause" disabled/ >
                              <input type="submit" name="clusterAction" class="btn btn-default btn-xs btn-danger" value="Stop" / >
                              <input type="hidden" name="id" value="'.$row["id"].'" />';
                            }
                            if ($row["status"] == "Terminated") {
                              echo '<input type="submit" name="clusterAction" class="btn btn-default btn-xs btn-success" value="Start" / >
                              <input type="submit" name="clusterAction" class="btn btn-default btn-xs btn-primary" value="Pause" / >
                              <input type="submit" name="clusterAction" class="btn btn-default btn-xs btn-danger" value="Stop" disabled/ >
                              <input type="hidden" name="id" value="'.$row["id"].'" />';
                            }
                            echo '  
                            </form>';
                            }
                            echo '
                            </td>
                            </tr>';
                            
                         }
						}
                        
                        ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- /page content -->

        <!-- footer content -->
        <footer>
          
          <div class="clearfix"></div>
        </footer>
        <!-- /footer content -->
      </div>
    </div>

    <!-- jQuery -->
    <script src="vendors/jquery/dist/jquery.min.js"></script>
    <!-- Bootstrap -->
    <script src="vendors/bootstrap/dist/js/bootstrap.min.js"></script>
    <!-- FastClick -->
    <script src="vendors/fastclick/lib/fastclick.js"></script>
    <!-- NProgress -->
    <script src="vendors/nprogress/nprogress.js"></script>
    <!-- Chart.js -->
    <script src="vendors/Chart.js/dist/Chart.min.js"></script>
    <!-- gauge.js -->
    <script src="vendors/gauge.js/dist/gauge.min.js"></script>
    <!-- bootstrap-progressbar -->
    <script src="vendors/bootstrap-progressbar/bootstrap-progressbar.min.js"></script>
    <!-- iCheck -->
    <script src="vendors/iCheck/icheck.min.js"></script>
    <!-- Skycons -->
    <script src="vendors/skycons/skycons.js"></script>
    <!-- Flot -->
    <script src="vendors/Flot/jquery.flot.js"></script>
    <script src="vendors/Flot/jquery.flot.pie.js"></script>
    <script src="vendors/Flot/jquery.flot.time.js"></script>
    <script src="vendors/Flot/jquery.flot.stack.js"></script>
    <script src="vendors/Flot/jquery.flot.resize.js"></script>
    <!-- Flot plugins -->
    <script src="vendors/flot.orderbars/js/jquery.flot.orderBars.js"></script>
    <script src="vendors/flot-spline/js/jquery.flot.spline.min.js"></script>
    <script src="vendors/flot.curvedlines/curvedLines.js"></script>
    <!-- DateJS -->
    <script src="vendors/DateJS/build/date.js"></script>
    <!-- JQVMap -->
    <script src="vendors/jqvmap/dist/jquery.vmap.js"></script>
    <script src="vendors/jqvmap/dist/maps/jquery.vmap.world.js"></script>
    <script src="vendors/jqvmap/examples/js/jquery.vmap.sampledata.js"></script>
    <!-- bootstrap-daterangepicker -->
    <script src="vendors/moment/min/moment.min.js"></script>
    <script src="vendors/bootstrap-daterangepicker/daterangepicker.js"></script>

    <!-- Datatables -->
    <script src="vendors/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="vendors/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
    <script src="vendors/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
    <script src="vendors/datatables.net-buttons-bs/js/buttons.bootstrap.min.js"></script>
    <script src="vendors/datatables.net-buttons/js/buttons.flash.min.js"></script>
    <script src="vendors/datatables.net-buttons/js/buttons.html5.min.js"></script>
    <script src="vendors/datatables.net-buttons/js/buttons.print.min.js"></script>
    <script src="vendors/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js"></script>
    <script src="vendors/datatables.net-keytable/js/dataTables.keyTable.min.js"></script>
    <script src="vendors/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
    <script src="vendors/datatables.net-responsive-bs/js/responsive.bootstrap.js"></script>
    <script src="vendors/datatables.net-scroller/js/datatables.scroller.min.js"></script>
    <script src="vendors/jszip/dist/jszip.min.js"></script>
    <script src="vendors/pdfmake/build/pdfmake.min.js"></script>
    <script src="vendors/pdfmake/build/vfs_fonts.js"></script>

    <!-- Custom Theme Scripts -->
    <script src="build/js/custom.min.js"></script>

    

   

    <!-- Doughnut Chart -->
    <script>
      $(document).ready(function(){
        var options = {
          legend: false,
          responsive: false
        };

        new Chart(document.getElementById("canvas1"), {
          type: 'pie',
          tooltipFillColor: "rgba(51, 51, 51, 0.55)",
          data: {
            labels: [
              "Temperature",
              "Sea Levels",
              "Pressure",
              "Salinity",
              "Conductivity"
            ],
            datasets: [{
              data: [<?php echo round(getCount("SENSOR_LIST", "type", "Temperature")/getCount("SENSOR_LIST", null, null)*100) ?>, <?php echo round(getCount("SENSOR_LIST", "type", "Sea Levels")/getCount("SENSOR_LIST", null, null)*100) ?>, <?php echo round(getCount("SENSOR_LIST", "type", "Pressure")/getCount("SENSOR_LIST", null, null)*100) ?>, <?php echo round(getCount("SENSOR_LIST", "type", "Salinity")/getCount("SENSOR_LIST", null, null)*100) ?>, <?php echo round(getCount("SENSOR_LIST", "type", "Conductivity")/getCount("SENSOR_LIST", null, null)*100) ?>],
               backgroundColor: [
                "#3498DB",
                "#26B99A",
				"#9B59B6",
				"#BDC3C7",
				"#E74C3C"
                
              ],
              hoverBackgroundColor: [
                "#49A9EA",
				"#36CAAB",
				"#B370CF",
				"#CFD4D8",
				"#E95E4F"
                
              ]
            }]
          },
          options: options
        });
      });
    </script>
    <!-- /Doughnut Chart -->
    <!-- Datatables -->
    <script>
      $(document).ready(function() {
        var handleDataTableButtons = function() {
          if ($("#datatable-buttons").length) {
            $("#datatable-buttons").DataTable({
              dom: "Bfrtip",
              buttons: [
                {
                  extend: "copy",
                  className: "btn-sm"
                },
                {
                  extend: "csv",
                  className: "btn-sm"
                },
                {
                  extend: "excel",
                  className: "btn-sm"
                },
                {
                  extend: "pdfHtml5",
                  className: "btn-sm"
                },
                {
                  extend: "print",
                  className: "btn-sm"
                },
              ],
              responsive: true
            });
          }
        };

        TableManageButtons = function() {
          "use strict";
          return {
            init: function() {
              handleDataTableButtons();
            }
          };
        }();

        $('#datatable1').dataTable();
        $('#datatable2').dataTable();

        $('#datatable-keytable').DataTable({
          keys: true
        });

        $('#datatable-responsive').DataTable();

        $('#datatable-scroller').DataTable({
          ajax: "js/datatables/json/scroller-demo.json",
          deferRender: true,
          scrollY: 380,
          scrollCollapse: true,
          scroller: true
        });

        $('#datatable-fixed-header').DataTable({
          fixedHeader: true
        });

        var $datatable = $('#datatable-checkbox');

        $datatable.dataTable({
          'order': [[ 1, 'asc' ]],
          'columnDefs': [
            { orderable: false, targets: [0] }
          ]
        });
        $datatable.on('draw.dt', function() {
          $('input').iCheck({
            checkboxClass: 'icheckbox_flat-green'
          });
        });

        TableManageButtons.init();
      });
    </script>
    <!-- /Datatables -->
   

    
  </body>
</html>
