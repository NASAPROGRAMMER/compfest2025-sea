<?php
session_start();
if (!isset($_SESSION["user_id"])) exit("Unauthorized");

$conn = new mysqli("localhost", "root", "", "sea_catering");
if ($conn->connect_error) die("Koneksi gagal.");

$id = intval($_POST["id"]);
$start = $_POST["start_date"];
$end = $_POST["end_date"];

$stmt = $conn->prepare("UPDATE subscriptions SET status='paused', pause_start=?, pause_end=? WHERE id=? AND user_id=?");
$stmt->bind_param("ssii", $start, $end, $id, $_SESSION["user_id"]);
$stmt->execute();
header("Location: user_dashboard.php");
?>
