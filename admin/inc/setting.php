<?php
ob_start();
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

date_default_timezone_set("Asia/Colombo");

require "../class/Database.php";
require "../class/User.php";
// require "../class/Profile.php";
// require "../class/Packages.php";
require "../class/Category.php";
// require "../class/subCategory.php";
// require "../class/Rating.php";
// require "../class/Budget.php";
// require "../class/BudgetList.php";
// require "../class/Models.php";
// require "../class/MailerAPI.php";

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
}

if ($_SERVER['REQUEST_URI'] == "/esanecom/admin/login.php" || $_SERVER['REQUEST_URI'] == "/esanecom/admin/register.php") {
    if (isset($_SESSION['userID'])) {
        header("Location: index.php");
        exit();
    }
} else {
    if (!isset($_SESSION['userID'])) {
        header("Location: login.php");
        exit();
    }
}


if (isset($_GET['url']) && $_GET['url'] != "") {
    $_SESSION['targetURL'] = $_GET['url'];
}

$currentURL = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

if (!isset($_SESSION['compare'])) {
    $_SESSION['compare'] = array();
}


function getLastWord($inputString)
{
    $inputString = trim($inputString);

    $words = explode(' ', $inputString);

    if (count($words) === 1) {
        return $words[0]; // Return the single word
    } else {
        return end($words); // Return the last word
    }
}
?>