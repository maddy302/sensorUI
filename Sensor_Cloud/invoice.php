<?php
   include('dbConnect.php');
   session_start();
   
   $user_check = $_SESSION['login_user'];
   
   $ses_sql = mysqli_query($db,"select id, name, type, address, state, city, support_plan, postal_code, phone_number, email from users where email = '$user_check' ");

   $row = mysqli_fetch_array($ses_sql,MYSQLI_ASSOC);
   
   $login_session = $row['email'];
   $user_type = $row['type'];
   $user_name = $row['name'];
   $user_id = $row['id'];
   $user_city = $row['city'];
   $user_address = $row['address'];
   $user_plan = $row['support_plan'];
   $user_state = $row['state'];
   $user_postal = $row['postal_code'];
   $user_phone = $row['phone_number'];

   $ses_sql = mysqli_query($db,"select * from billing_model where plan = '$user_plan' and state = 'Active'");
   $row = mysqli_fetch_array($ses_sql,MYSQLI_ASSOC);
   $running = $row['price'];

   $ses_sql = mysqli_query($db,"select * from billing_model where plan = '$user_plan' and state = 'Paused'");
   $row = mysqli_fetch_array($ses_sql,MYSQLI_ASSOC);
   $paused = $row['price'];

   if(!isset($_SESSION['login_user'])){
      header("location:login.php");
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

    <title>Sensor Cloud! | Invoice</title>

    <!-- Bootstrap -->
    <link href="vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <!-- NProgress -->
    <link href="vendors/nprogress/nprogress.css" rel="stylesheet">
    
    <!-- Custom styling plus plugins -->
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
                      <li><a href="virtualization.php">Sensor Virtualization</a></li>
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
                
              </div>

              
            </div>

            <div class="clearfix"></div>

            <div class="row">
              <div class="col-md-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Billing Details</h2>
                    
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">

                    <section class="content invoice">
                      <!-- title row -->
                      <div class="row">
                        <div class="col-xs-12 invoice-header">
                          <h1>
                                          <i class="fa fa-globe"></i> Invoice.
                                          <small class="pull-right">Date: <?php $d = new DateTime(); 
                                                            echo $d->format( 'd/m/Y' );?></small>
                                      </h1>
                        </div>
                        <!-- /.col -->
                      </div>
                      <!-- info row -->
                      <div class="row invoice-info">
                        <div class="col-sm-4 invoice-col">
                          From
                          <address>
                                          <strong>Sensor Cloud, Inc.</strong>
                                          <br>77 N Ave, #699
                                          <br>New York, CA 94107
                                          <br>Phone: +1 (111) 123-9876
                                          <br>Email: billing@sensorcloud.com
                                      </address>
                        </div>
                        <!-- /.col -->
                        <div class="col-sm-4 invoice-col">
                          To
                          <address>
                                          <strong><?php echo $user_name; ?></strong>
                                          <br><?php echo $user_address; ?>
                                          <br><?php echo $user_city; ?>, <?php echo $user_state; ?> <?php echo $user_postal; ?>
                                          <br>Phone: <?php echo $user_phone; ?>
                                          <br>Email: <?php echo $login_session; ?>
                                      </address>
                        </div>
                        <!-- /.col -->
                        <div class="col-sm-4 invoice-col">
                          <b>Invoice #007612</b>
                          <br>
                          
                          <br>
                          <b>Payment Due:</b> <?php $d = new DateTime(); 
                                                            echo $d->format( 't/m/Y' );?>
                          <br>
                          <b>Account:</b> 968-34567
                        </div>
                        <!-- /.col -->
                      </div>
                      <!-- /.row -->

                      <!-- Table row -->
                      <div class="row">
                        <div class="col-xs-12 table">
                          <table class="table table-striped">
                            <thead>
                              <tr>
                                <th>Sr. No.</th>
                                <th>Service</th>
                                <th>Serial #</th>
                                <th style="width: 59%">Billing Details</th>
                                <th>Subtotal</th>
                              </tr>
                            </thead>
                            <tbody>
                            <?php
                            $ses_sql = mysqli_query($db,"select SENSOR_LIST.latitude, SENSOR_LIST.longitude, SENSOR_LIST.id, SENSOR_LIST.sensor_id, SENSOR_LIST.type, usage_details.used_hours, usage_details.paused_hours from SENSOR_LIST inner join usage_details on SENSOR_LIST.id = usage_details.sensor_id where SENSOR_LIST.owner='$user_id'");

                              $i = 1;
                              $subtotal = 0;
                               while($row = mysqli_fetch_array($ses_sql,MYSQLI_ASSOC)){
                                  
                                  echo '<tr>
                                          <td>'.$i.'</td>
                                          <td>'.$row["sensor_id"].'</td>
                                          <td>281-011-'.$row["id"].'</td>
                                          <td>'.$row["type"].' Sensor in Lat: '.$row["latitude"].' and Lon: '.$row["longitude"].'.</td>
                                          <td>$'.(($row["used_hours"]*$running) + ($row["paused_hours"]*$paused)).'</td>
                                        </tr>';
                                  $i++;

                                  $subtotal = $subtotal + (($row["used_hours"]*$running) + ($row["paused_hours"]*$paused));
                              }

                            ?>
                            </tbody>
                          </table>
                        </div>
                        <!-- /.col -->
                      </div>
                      <!-- /.row -->

                      <div class="row">
                        <!-- accepted payments column -->
                        <div class="col-xs-6">
                          <p class="lead">Payment Methods:</p>
                          <img src="images/visa.png" alt="Visa">
                          <img src="images/mastercard.png" alt="Mastercard">
                          <img src="images/american-express.png" alt="American Express">
                          <img src="images/paypal.png" alt="Paypal">
                          <p class="text-muted well well-sm no-shadow" style="margin-top: 10px;">
                            We accept the above payment methods. For more information on billing mail us at billing@sensorcloud.com.
                          </p>
                        </div>
                        <!-- /.col -->
                        <div class="col-xs-6">
                          <p class="lead">Amount Due <?php $d = new DateTime(); 
                                                            echo $d->format( 't/m/Y' );?></p>
                          <div class="table-responsive">
                            <table class="table">
                              <tbody>
                                <tr>
                                  <th style="width:50%">Subtotal:</th>
                                  <td>$<?php echo $subtotal; ?></td>
                                </tr>
                                <tr>
                                  <th>Tax (9.3%)</th>
                                  <td>$<?php echo round($subtotal*0.093, 2); ?></td>
                                </tr>
                                <tr>
                                  <th>Total:</th>
                                  <td>$<?php echo $subtotal + round($subtotal*0.093, 2); ?></td>
                                </tr>
                              </tbody>
                            </table>
                          </div>
                        </div>
                        <!-- /.col -->
                      </div>
                      <!-- /.row -->

                      <!-- this row will not appear when printing -->
                      <div class="row no-print">
                        <div class="col-xs-12">
                          <button class="btn btn-default" onclick="window.print();"><i class="fa fa-print"></i> Print</button>
                          <button class="btn btn-success pull-right"><i class="fa fa-credit-card"></i> Submit Payment</button>
                          <button class="btn btn-primary pull-right" style="margin-right: 5px;"><i class="fa fa-download"></i> Generate PDF</button>
                        </div>
                      </div>
                    </section>
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

    <!-- Custom Theme Scripts -->
    <script src="build/js/custom.min.js"></script>
  </body>
</html>