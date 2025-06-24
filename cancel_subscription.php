<?php
session_start();
if (!isset($_SESSION["user_id"])) exit("Unauthorized");

$conn = new mysqli("localhost", "root", "", "sea_catering");
$id = intval($_POST["id"]);

$stmt = $conn->prepare("UPDATE subscriptions SET status='cancelled' WHERE id=? AND user_id=?");
$stmt->bind_param("ii", $id, $_SESSION["user_id"]);
$stmt->execute();
header("Location: user_dashboard.php");
?>
