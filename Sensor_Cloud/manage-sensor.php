<?php
   include('dbConnect.php');
   session_start();
   
   $user_check = $_SESSION['login_user'];
   
   $ses_sql = mysqli_query($db,"select id, name, type, email from users where email = '$user_check' ");

   $row = mysqli_fetch_array($ses_sql,MYSQLI_ASSOC);
   
   $login_session = $row['email'];
   $user_type = $row['type'];
   $user_name = $row['name'];
   $user_id = $row['id'];

   if(!isset($_SESSION['login_user'])){
      header("location:login.php");
   }
   if($user_type == "admin") {
      header("location:index-admin.php");
   }

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
              
              
              $sql = mysqli_query($db,"update SENSOR_LIST set STATUS = 'Paused' where ID = $id");
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
                  
                $sql = mysqli_query($db,"update SENSOR_LIST set STATUS = 'Active' where ID = $id");
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

                  $result = mysqli_query($db,"Select * from usage_details inner join SENSOR_LIST on SENSOR_LIST.ID = usage_details.sensor_id where sensor_id = $id");
                  while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)) {
                    $SensorStatus=$row['STATUS'];
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
                    
                  $sql = mysqli_query($db,"update SENSOR_LIST set STATUS = 'Terminated' where ID = $id");
                  $sql_usage = mysqli_query($db,"update usage_details set update_time = '$currentDate', $q where sensor_id = $id");
                
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

    <title>Sensor Cloud! | Manage Sensors</title>

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
              <a href="index.html" class="site_title"><i class="fa fa-cloud"></i> <span>Sensor Cloud!</span></a>
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
                      <li><a href="virtualization.php">Sensor Virtualization</a></li>
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
                    <li>
                      <a href="invoice.php">
                        <span class="badge bg-red pull-right">$<?php $subtotal =0 ;echo $subtotal + round($subtotal*0.093, 2); ?></span>
                        <span>Billing</span>
                      </a>
                    </li>
                    <li><a href=""><i class="fa fa-sign-out pull-right"></i> Log Out</a></li>
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
                <h3>Manage Sensors</h3>
              </div>

              
            </div>
            <div class="clearfix"></div>

            <div class="row">
              <div class="col-md-12 col-sm-12 col-xs-12">
                
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
                         
                         $ses_sql = mysqli_query($db,"select SENSOR_LIST.LATITUDE, SENSOR_LIST.LONGITUDE,SENSOR_LIST.ID, SENSOR_LIST.SENSOR_ID, SENSOR_LIST.TYPE, SENSOR_LIST.IP_ADDRESS, SENSOR_LIST.STATUS, SENSOR_LIST.TIME_CREATED, usage_details.used_hours, usage_details.paused_hours, usage_details.update_time from SENSOR_LIST inner join usage_details on SENSOR_LIST.ID = usage_details.sensor_id where SENSOR_LIST.OWNER='$user_id'");

						 if(!empty($ses_sql)){
                         while($row = mysqli_fetch_array($ses_sql,MYSQLI_ASSOC)){

                            
                            
                            echo '<tr>
                            <td>'.$row["SENSOR_ID"].'</td>
                            <td>'.$row["TYPE"].'</td>
                            <td>Lat: '.$row["LATITUDE"].' Lon: '.$row["LONGITUDE"].'</td>
                            <td>'.$row["STATUS"].'</td>
                            <td>'.date_format(new DateTime($row["TIME_CREATED"]), 'd M Y').'</td>
                            <td>'.$row["used_hours"].' hours</td>
                            <td>'.$row["paused_hours"].' hours</td>';
                            
                            echo '<td>';
                            if ($row['STATUS'] != "Terminated") {
                              echo '<form method="post" action="">';
                            if ($row["STATUS"] == "Active") {
                              echo '<input type="submit" name="action" class="btn btn-round btn-xs btn-success" value="Start" disabled/ >
                              <input type="submit" name="action" class="btn btn-round btn-xs btn-primary" value="Pause" / >
                              <input type="submit" name="action" class="btn btn-round btn-xs btn-danger" value="Stop" / >
                              <input type="hidden" name="id" value="'.$row["ID"].'" />';
                            }
                            if ($row["STATUS"] == "Paused") {
                              echo '<input type="submit" name="action" class="btn btn-round btn-xs btn-success" value="Start" / >
                              <input type="submit" name="action" class="btn btn-round btn-xs btn-primary" value="Pause" disabled/ >
                              <input type="submit" name="action" class="btn btn-round btn-xs btn-danger" value="Stop" / >
                              <input type="hidden" name="id" value="'.$row["ID"].'" />';
                            }
                            if ($row["STATUS"] == "Terminated") {
                              echo '<input type="submit" name="action" class="btn btn-round btn-xs btn-success" value="Start" / >
                              <input type="submit" name="action" class="btn btn-round btn-xs btn-primary" value="Pause" / >
                              <input type="submit" name="action" class="btn btn-round btn-xs btn-danger" value="Stop" disabled/ >
                              <input type="hidden" name="id" value="'.$row["ID"].'" />';
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
                </div>

                <div class="row">
                <?php 
                  $ses_sql = mysqli_query($db,"select SENSOR_LIST.SENSOR_ID,TYPE from SENSOR_LIST where SENSOR_LIST.OWNER='$user_id' ORDER BY id DESC");
                  $i = 0;
				  if(!empty($ses_sql)){
                  while($row = mysqli_fetch_array($ses_sql,MYSQLI_ASSOC)){
                      echo '<!-- graph line -->
                            <div class="col-md-12 col-sm-12 col-xs-12">
                              <div class="x_panel">
                                <div class="x_title">
                                  <h2>'.$row["SENSOR_ID"].''." ".''.$row["TYPE"].' Data</h2>
                                  
                                  <div class="clearfix"></div>
                                </div>
                                <div class="x_content2">
                                  <div id="graph_line_'.$i.'" style="width:100%; height:300px;"></div>
                                </div>
                              </div>
                            </div>
                            <!-- /graph line -->';

                            $i++;
                  }
				  }
                    
                ?>
           
              </div>


              </div>
            </div>
          </div>
        </div>
        <!-- /page content -->
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
    <!-- Custom Theme Scripts -->
    <script src="build/js/custom.min.js"></script>

    <!-- morris.js -->
    <script src="vendors/raphael/raphael.min.js"></script>
    <script src="vendors/morris.js/morris.min.js"></script>

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

    <script>
      $(document).ready(function() {

        <?php 
          date_default_timezone_set("America/Los_Angeles");
              
            $ses_sql = mysqli_query($db,"select SENSOR_LIST.SENSOR_ID, TYPE, TIME_CREATED from SENSOR_LIST where SENSOR_LIST.OWNER='$user_id' ORDER BY ID DESC");
            $i = 0;
            while($row = mysqli_fetch_array($ses_sql,MYSQLI_ASSOC)){
                echo "Morris.Line({
                          element: 'graph_line_".$i."',
                          xkey: 'date',
                          ykeys: ['value'],
                          labels: ['Value'],
                          hideHover: 'auto',
                          lineColors: ['#26B99A', '#34495E', '#ACADAC', '#3498DB'],
                          data: [";
                          for($j = 1 ; $j < 30 ; $j = $j = $j + 1) {
                            
                              if (date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s").' -'.$j.' minutes')) > date("Y-m-d H:i:s", strtotime($row["TIME_CREATED"]))) {
                                echo "{date: '".date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s").' -'.$j.' minutes'))."', value: ".rand(0,99)."},";
                              }
                              
                          }
                            
                        echo "],
                          resize: true
                        });";

                        $i++;
            }
              
          ?>
        

        $MENU_TOGGLE.on('click', function() {
          $(window).resize();
        });
      });
    </script>
  
   
  </body>
</html>
