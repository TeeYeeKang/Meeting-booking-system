<?php

// This file is used to connect the website to our local host database
// It is just a programming habit to place this file inside a folder called
// "includes". Because the file in this folder is something just working
// on background not something will appear in our website.

// Name file contains .inc is also a programming habit

// for our case it run on local host
// for online the parameter will be base on your online database
$dbServername = "localhost";  
$dbUsername = "root";
$dbPassword = ""; // no password unless online and you set yourself
$dbName = "meetme";

$conn = mysqli_connect($dbServername, $dbUsername, $dbPassword, $dbName);


