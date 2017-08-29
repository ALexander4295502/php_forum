<?php
require("DBconfig.php");
// if(isset($_SESSION['SESSION_PARENT'])){
// 	print("in header ==> ".$_SESSION['SESSION_PARENT']);
// }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>News Web Site</title>
</head>

<body>
    <div id="header">
        <h1>Navigation</h1>
    </div>
    <div id="menu">
        <a href="http://ec2-54-159-118-144.compute-1.amazonaws.com/~ctong/spring2017-module3-451989-452091/index.php">Home</a>
    </div>
    <div id="container">
        <div id="siteBar">
            <?php require( "siteBar.php"); ?>
        </div>
        <div id="main">


