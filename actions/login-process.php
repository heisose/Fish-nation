<?php
session_start();
include('../config/db.php');

$user = $_POST['username'];
$pass = $_POST['password'];

$res = $conn->query("SELECT * FROM admins WHERE username='$user' AND password='$pass'");

if($res->num_rows > 0){
    $_SESSION['admin'] = $user;
    header("Location: ../admin/dashboard.php");
    exit();
} else {
    echo "Login Failed";
}
?>