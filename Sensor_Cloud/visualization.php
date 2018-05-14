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

  extract($_POST);
  $locationArray=array(array());
    $sql = mysqli_query($db ,"Select * from SENSOR_LIST where user_id='$user_id'");
  
  $i=0;
  if(!empty($sql)){
  while($row = mysqli_fetch_array($sql,MYSQLI_ASSOC)) {
    $locationArray[$i][0]=$row['latitude'];
    $locationArray[$i][1]=$row['longitude'];
    $locationArray[$i][2]=$row['type'];
    $i++;
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

    <title>Sensor Cloud! | Analytics</title>

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

    <link rel="stylesheet" type="text/css" href="css/cssFile.css">
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?v=3.11&sensor=false&key=AIzaSyAe6jtM83BDhNdL49jySjJ3XA1sODx-WmI" type="text/javascript"></script>
	<script type="text/javascript" 
	    src="https://public.tableau.com/javascripts/api/tableau-2.min.js"></script>
   <script type="text/javascript">
   var workbook;
   var activeSheet;
        // check DOM Ready
		function initViz1(cont,urlToTableau) {
            var containerDiv = document.getElementById(cont),
                url = urlToTableau,
                options = {
                    hideTabs: true,
                    onFirstInteractive: function () {
                        console.log("Run this code when the viz has finished loading.");
						workbook = viz.getWorkbook();
						activeSheet = workbook.getActiveSheet();
                    }
                };
            
            var viz = new tableau.Viz(containerDiv, url, options); 
            // Create a viz object and embed it in the container div.
            if(cont=="vizContainer3") 
            filterSingleValue();
        }

        function filterSingleValue() {
      activeSheet.applyFilterAsync(
      "Wmo Id",
      "4801910",
    tableau.FilterUpdateType.REPLACE);
}    

        </script>
  </head>

  <body class="nav-md" >
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
                      <!--<li><a href="virtualization.php">Sensor Virtualization</a></li>-->
					  <li><a href="add-cluster.php">Add Cluster</a></li>
					   <li><a href="visualization.php">Sensor Data Analytics</a></li>
                    </ul>
                  </li>
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
                    <li><a href="javascript:;"> Profile</a></li>
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
                <h3>Sensor Data Analytics</h3>
              </div>
<div class="x_content" id="vizContainer" style="width:800px; height:700px;"></div>
<div class="x_content" id="vizContainer2" style="width:800px; height:700px;"></div>
<div class="x_content" id="vizContainer3" style="width:800px; height:700px;"></div>
<div class="x_content" id="vizContainer4" style="width:800px; height:700px;"></div>
<div class="x_content" id="vizContainer5" style="width:800px; height:700px;"></div>
<div class="x_content" id="vizContainer6" style="width:800px; height:700px;"></div>
<div class="x_content" id="vizContainer7" style="width:800px; height:700px;"></div>

<script>initViz1("vizContainer","https://public.tableau.com/views/281-02/Sheet1?:embed=y&:display_count=yes&publish=yes");</script>
<script>initViz1("vizContainer2","https://public.tableau.com/views/281-01/Sheet1?:embed=y&:display_count=no");</script>
<script>initViz1("vizContainer3","https://public.tableau.com/views/281-05/Sheet1?:embed=y&:display_count=yes&publish=yes");</script>
<script>initViz1("vizContainer4","https://public.tableau.com/views/281-06/Sheet1?:embed=y&:display_count=yes&publish=yes");</script>
<script>initViz1("vizContainer5","https://public.tableau.com/views/281-03/Sheet1?:embed=y&:display_count=yes&publish=yes");</script>
<script>initViz1("vizContainer6","https://public.tableau.com/views/281-04/Sheet1?:embed=y&:display_count=yes&publish=yes");</script>
<script>initViz1("vizContainer7","https://public.tableau.com/views/281-07/Sheet1?:embed=y&:display_count=yes&publish=yes");</script>
       
            </div>
			
            <div class="clearfix"></div>
            <!--<div class="row">
              <div class="col-md-12 col-sm-12 col-xs-12">
                
                <div id="map_canvas"></div>

              </div>
            </div>-->

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

  
   
  </body>
</html>
