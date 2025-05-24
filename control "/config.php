<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "vision_system";

// إنشاء الاتصال
$con = new mysqli($servername, $username, $password, $dbname);

// التحقق من الاتصال
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// تعيين الترميز
$con->set_charset("utf8mb4");
?>