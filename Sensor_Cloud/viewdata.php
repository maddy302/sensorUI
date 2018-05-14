<?php
   include('dbConnect.php');
   session_start();

   $user_check = $_SESSION['login_user'];

   $ses_sql = mysqli_query($db,"select id, name, type, email from users where email = '$user_check' ");

   $row = mysqli_fetch_array($ses_sql,MYSQLI_ASSOC);

   $login_session = $row['email'];
   $user_name = $row['name'];
   $user_id = $row['id'];

   if(!isset($_SESSION['login_user'])){
      header("location:login.php");
   }
	$cluster_query = "select id, name from clusters where user_id='$user_id'";
	$cluster_list = mysqli_query($db,$cluster_query);
   if($_SERVER["REQUEST_METHOD"] == "POST") {
      // username and password sent from form

      $sensorid = mysqli_real_escape_string($db,$_POST['sensorid']);
      $from = mysqli_real_escape_string($db,$_POST['from']);
      $to = mysqli_real_escape_string($db,$_POST['to']);
      $type = mysqli_real_escape_string($db,$_POST['type']);

      date_default_timezone_set("America/Los_Angeles");
      $currentDate = date("Y-m-d H:i:s");

	     $data_q = "select * from SENSOR_DATA WHERE SENSOR_ID='$sensorid' and
       time_recorded between '$from' and '$to' LIMIT 100 ";


      //$sql = "INSERT INTO `SENSOR_LIST` ( `OWNER`, `SENSOR_ID`, `TYPE`, `LATITUDE`, `LONGITUDE`, `STATUS`,`CLUSTER_ID`) VALUES ( '$user_id','$name', '$type', '$latitude', '$longitude', 'Active','$cluster_for_db')";
      $result_data = mysqli_query($db,$data_q);

      // If result matched $myusername and $mypassword, table row must be 1 row


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

    <title>Sensor Cloud | View Data</title>

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

    <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false&key=AIzaSyAe6jtM83BDhNdL49jySjJ3XA1sODx-WmI"></script>

    <link rel="stylesheet" type="text/css" href="css/cssFile.css">

    <script src="js/script.js?update=444"></script>

  </head>

  <body class="nav-md">
    <div class="container body">
      <div class="main_container">
        <div class="col-md-3 left_col">
          <div class="left_col scroll-view">
            <div class="navbar nav_title" style="border: 0;">
              <a href="index.php" class="site_title"><span>Sensor Cloud</span></a>
            </div>

            <div class="clearfix"></div>

            <!-- menu profile quick info -->
            <div class="profile">
              <div class="profile_pic">
                <img src="images/user.png" alt="..." class="img-circle profile_img">
              </div>
              <div class="profile_info">
                <span>Hello,</span>
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
                      <li><a href="add-cluster.php">Add Cluster</a></li>
					  <li><a href="visualization.php">Sensor Visualization</a></li>
                    </ul>
                  </li>
                  </ul>
                  </div>

            </div>
            <!-- /sidebar menu -->

            <!-- /menu footer buttons -->
            <div class="sidebar-footer hidden-small">
              <a data-toggle="tooltip" data-placement="top" title="Settings">
                <span class="glyphicon glyphicon-cog" aria-hidden="true"></span>
              </a>
              <a data-toggle="tooltip" data-placement="top" title="FullScreen">
                <span class="glyphicon glyphicon-fullscreen" aria-hidden="true"></span>
              </a>
              <a data-toggle="tooltip" data-placement="top" title="Lock">
                <span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span>
              </a>
              <a data-toggle="tooltip" data-placement="top" title="Logout">
                <span class="glyphicon glyphicon-off" aria-hidden="true"></span>
              </a>
            </div>
            <!-- /menu footer buttons -->
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
                    <li><a href="javascript:;"> Profile</a></li>
                    <li>
                      <a href="javascript:;">
                        <span class="badge bg-red pull-right">50%</span>
                        <span>Settings</span>
                      </a>
                    </li>
                    <li><a href="javascript:;">Help</a></li>
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
            <div class="page-title">
              <div class="title_left">
                <h3>View Sensor Data</h3>
              </div>

              <div class="title_right">
                <div class="col-md-5 col-sm-5 col-xs-12 form-group pull-right top_search">
                  <div class="input-group">
                    <input type="text" class="form-control" placeholder="Search for...">
                    <span class="input-group-btn">
                      <button class="btn btn-default" type="button">Go!</button>
                    </span>
                  </div>
                </div>
              </div>
            </div>
            <div class="clearfix"></div>
            <div class="row">
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>View Sensor Data</h2>

                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <br />
                    <form id="demo-form2" data-parsley-validate class="form-horizontal form-label-left" action = "" method = "post">

                    <div id='mapOuter'>
                        <div id="map"></div>
                      </div>

                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first">Sensor ID <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input type="text" id="sensorid" name="sensorid" required="required" class="form-control col-md-7 col-xs-12">
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="latitude">From <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input type="date" id="from" name="from" required="required" class="form-control col-md-7 col-xs-12">
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="to">To <span class="required">*</span>
                        </label>

                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input type="date" id="to" name="to" required="required" class="form-control col-md-7 col-xs-12">

                        </div>
                      </div>

                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Type <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <select class="form-control" name="type">
                            <option>Choose type</option>
                            <option value="TEMPERATURE_C">Temperature</option>
                            <option value="BUOY">Sea Levels</option>
                            <option value="PRESSURE">Pressure</option>
                            <option value="SALINITY">Salinity</option>
                            <option value="CONDUCTIVITY">Conductivity</option>
                          </select>
                        </div>
                      </div>

                      <div class="ln_solid"></div>
                      <div class="form-group">
                        <div class="col-md-12 ">
                          <input type="submit" class="btn btn-success" value="View Data" /><br><br>
                          <div class="row">
                             <div class="col-md-12 col-sm-12 col-xs-12">
                              <div class="x_panel">
                                
                                <div class="x_content">
                                  <p class="text-muted font-13 m-b-30">

                                  </p>
                                  <table id="datatable1" class="table table-striped table-bordered">
                                    <thead>
                                       <tr>
                                        <th>Serial No.</th>
                                        <th>Sensor ID</th>
                                        <th>Type</th>
                                        <th>Value</th>
                                        <th>Time</th>

                                      </tr>
                                      </thead>
                                      <?php
                                      $i=0;
                                      if(isset($result_data)){
                                        if(!empty($result_data)){
                                      while($row_data = $result_data->fetch_assoc()){
                                        $i++;
                                        echo '<tr>';
                                        echo '<td>'.$i.'</td>';
                                        echo '<td>'.$row_data['SENSOR_ID'].'</td>';
                                        echo '<td>'.$type.'</td>';
                                        echo '<td>'.$row_data[$type].'</td>';
                                        echo '<td>'.$row_data['TIME_RECORDED'].'</td>';
                                        echo '</tr>';
}
                                      }}

                                      ?>
                                    </table>


                        </div>
                      </div>

                    </form>

                  </div>
                </div>
              </div>
            </div>
                </div>


              </div>
            </div>
          </div>
        </div>
        <!-- /page content -->

        <!-- footer content -->

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
    <!-- bootstrap-progressbar -->
    <script src="vendors/bootstrap-progressbar/bootstrap-progressbar.min.js"></script>
    <!-- iCheck -->
    <script src="vendors/iCheck/icheck.min.js"></script>
    <!-- bootstrap-daterangepicker -->
    <script src="vendors/moment/min/moment.min.js"></script>
    <script src="vendors/bootstrap-daterangepicker/daterangepicker.js"></script>
    <!-- bootstrap-wysiwyg -->
    <script src="vendors/bootstrap-wysiwyg/js/bootstrap-wysiwyg.min.js"></script>
    <script src="vendors/jquery.hotkeys/jquery.hotkeys.js"></script>
    <script src="vendors/google-code-prettify/src/prettify.js"></script>
    <!-- jQuery Tags Input -->
    <script src="vendors/jquery.tagsinput/src/jquery.tagsinput.js"></script>
    <!-- Switchery -->
    <script src="vendors/switchery/dist/switchery.min.js"></script>
    <!-- Select2 -->
    <script src="vendors/select2/dist/js/select2.full.min.js"></script>
    <!-- Parsley -->
    <script src="vendors/parsleyjs/dist/parsley.min.js"></script>
    <!-- Autosize -->
    <script src="vendors/autosize/dist/autosize.min.js"></script>
    <!-- jQuery autocomplete -->
    <script src="vendors/devbridge-autocomplete/dist/jquery.autocomplete.min.js"></script>
    <!-- starrr -->
    <script src="vendors/starrr/dist/starrr.js"></script>

    <!-- Custom Theme Scripts -->
    <script src="build/js/custom.min.js"></script>



  </body>
</html>
