<?php
session_start();

// Admin-only page: pastikan role admin
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.html");
    exit;
}

// Koneksi ke database
$conn = new mysqli("localhost", "root", "", "sea_catering");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Ambil rentang tanggal dari input atau default bulan ini
$start = $_GET["start"] ?? date("Y-m-01");
$end = $_GET["end"] ?? date("Y-m-t");

// Total subscriptions baru
$new_subs_query = $conn->prepare("SELECT COUNT(*) FROM subscriptions WHERE created_at BETWEEN ? AND ?");
$new_subs_query->bind_param("ss", $start, $end);
$new_subs_query->execute();
$new_subs_query->bind_result($new_subs);
$new_subs_query->fetch();
$new_subs_query->close();

// MRR: total pendapatan dari subscription aktif
$mrr_query = $conn->prepare("SELECT SUM(total_price) FROM subscriptions WHERE status = 'active' AND created_at BETWEEN ? AND ?");
$mrr_query->bind_param("ss", $start, $end);
$mrr_query->execute();
$mrr_query->bind_result($mrr);
$mrr_query->fetch();
$mrr_query->close();
$mrr = $mrr ?? 0;

// Reactivations: langganan yg dipause lalu aktif lagi dalam periode tsb
$react_query = $conn->prepare("SELECT COUNT(*) FROM subscriptions WHERE status = 'active' AND pause_end BETWEEN ? AND ?");
$react_query->bind_param("ss", $start, $end);
$react_query->execute();
$react_query->bind_result($reactivations);
$react_query->fetch();
$react_query->close();

// Total aktif sekarang
$active_query = $conn->query("SELECT COUNT(*) AS total FROM subscriptions WHERE status = 'active'");
$active_total = $active_query->fetch_assoc()["total"];

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8" />
  <title>Admin Dashboard - SEA Catering</title>
  <link rel="stylesheet" href="styles.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      padding: 20px;
      background: #f2f2f2;
    }
    h2 {
      color: #333;
    }
    .dashboard-box {
      background: #fff;
      padding: 20px;
      margin-bottom: 20px;
      border-left: 5px solid #4caf50;
      box-shadow: 0 0 5px rgba(0,0,0,0.1);
    }
    .dashboard-box h3 {
      margin: 0 0 10px;
      color: #4caf50;
    }
    form {
      margin-bottom: 20px;
    }
    label, input {
      font-size: 16px;
      margin-right: 10px;
    }
    input[type="date"] {
      padding: 5px;
    }
    button {
      padding: 5px 15px;
      background: #4caf50;
      color: white;
      border: none;
      cursor: pointer;
    }
  </style>
</head>
<body>
    <div class="navbar">
      <div class="logo">Sea <span>Catering</span></div>
      <nav>
        <div class="menu-toggle" id="mobile-menu">&#9776;</div>
        <ul class="nav-links" id="nav-links">
          <li class="nav-item active"><a href="index.html">Home</a></li>
          <li><a href="mealplans.html">Menu Plans</a></li>
          <li class="nav-item"><a href="index.html#contact">About Us</a></li>
          <li><a href="subscription.php">Subscription</a></li>
           <li><a href="testimonial.php">Testimonial</a></li>
        </ul>
      </nav>
      </div>
      </div>
  <h2>Admin Dashboard - SEA Catering</h2>

  <form method="GET">
    <label>Start:</label>
    <input type="date" name="start" value="<?= $start ?>" required />
    <label>End:</label>
    <input type="date" name="end" value="<?= $end ?>" required />
    <button type="submit">Filter</button>
  </form>

  <div class="dashboard-box">
    <h3>New Subscriptions</h3>
    <p><?= $new_subs ?> new subscriptions from <?= $start ?> to <?= $end ?></p>
  </div>

  <div class="dashboard-box">
    <h3>Monthly Recurring Revenue (MRR)</h3>
    <p>Rp<?= number_format($mrr, 0, ',', '.') ?>,00 from active subscriptions</p>
  </div>

  <div class="dashboard-box">
    <h3>Reactivations</h3>
    <p><?= $reactivations ?> subscriptions reactivated</p>
  </div>

  <div class="dashboard-box">
    <h3>Total Active Subscriptions</h3>
    <p><?= $active_total ?> currently active subscriptions</p>
  </div>
  
   <div style="margin-top: 20px;">
    <a href="logout.php"><button>Logout</button></a>
    </div>
</body>
</html>
