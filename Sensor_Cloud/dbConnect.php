<?php
   define('DB_SERVER', "cmpe272.c7heilvdo1fe.us-east-1.rds.amazonaws.com");
   define('DB_USERNAME', "root");
   define('DB_PASSWORD', "bsrihari09");
   define('DB_DATABASE', "SENSOR_CLOUD");
  
   $db = mysqli_connect(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE);
?>