<?php
    ob_start();
    //anything beyond this till ob_end() will not be sent to server
    //ob_end() ends the output buffer 
    //lines beyond this not shown in website

    session_start();
    include("db.php");
    include("functions.php");
    /*if($con){//connection is cool in db.php so its going to work
        echo '$con'. " as var is connected from db.php ";
    }*/
    //echo "INIT IS WORKING !!!--updated in functions/init.php";
?>