<?php
   define('DB_SERVER', "server ip");
   define('DB_USERNAME', "id");
   define('DB_PASSWORD', "pwd");
   define('DB_DATABASE', "dbname");
  
   $db = mysqli_connect(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE);
?>