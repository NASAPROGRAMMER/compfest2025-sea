<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// Debug session
echo "<!-- Session Data: ";
print_r($_SESSION);
echo " -->";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];
$conn = new mysqli("localhost", "root", "", "sea_catering");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error . " [Error No: " . $conn->connect_errno . "]");
}

// Ambil data user untuk ditampilkan
$user_stmt = $conn->prepare("SELECT full_name FROM users WHERE id = ?");
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user_data = $user_result->fetch_assoc();
$user_stmt->close();

// Ambil data subscription user
$sub_stmt = $conn->prepare("SELECT * FROM subscriptions WHERE id = ?");
$sub_stmt->bind_param("i", $user_id);
$sub_stmt->execute();
$result = $sub_stmt->get_result();
$subscriptions = $result->fetch_all(MYSQLI_ASSOC);
$sub_stmt->close();

// Debug subscriptions
echo "<!-- Subscriptions Data: ";
print_r($subscriptions);
echo " -->";
?>
<!DOCTYPE html>
<html>
<head>
  <title>User Dashboard</title>
  <link rel="stylesheet" href="styles.css" />
  <style>
    .subscription-box { 
      border: 1px solid #ccc; 
      padding: 20px; 
      margin-bottom: 15px; 
      background: #f9f9f9;
      border-radius: 8px;
    }
    button { 
      padding: 8px 15px; 
      margin: 5px; 
      background: #4CAF50;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }
    button:hover {
      background: #45a049;
    }
    .no-subscription {
      padding: 20px;
      background: #f0f0f0;
      border-radius: 8px;
      text-align: center;
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
  <h2>👤 Welcome, <?php echo htmlspecialchars($user_data['full_name'] ?? 'User'); ?></h2>

  <?php if (empty($subscriptions)): ?>
    <div class="no-subscription">
      <p>You don't have any active subscriptions yet.</p>
      <a href="subscription.php"><button>Create New Subscription</button></a>
    </div>
  <?php else: ?>
    <?php foreach ($subscriptions as $sub): ?>
      <div class="subscription-box">
        <p><strong>Plan:</strong> <?= htmlspecialchars($sub['plan']) ?></p>
        <p><strong>Meal Types:</strong> <?= htmlspecialchars($sub['meal_types']) ?></p>
        <p><strong>Delivery Days:</strong> <?= htmlspecialchars($sub['delivery_days']) ?></p>
        <p><strong>Total Price:</strong> Rp<?= number_format($sub['total_price'], 0, ',', '.') ?></p>
        <p><strong>Status:</strong> <?= htmlspecialchars($sub['status']) ?></p>

        <?php if ($sub['status'] === 'active'): ?>
          <form method="POST" action="pause_subscription.php" style="display:inline;">
            <input type="hidden" name="subscription_id" value="<?= $sub['id'] ?>">
            <label>Pause From: <input type="date" name="pause_start" required></label>
            <label>To: <input type="date" name="pause_end" required></label>
            <button type="submit">Pause Subscription</button>
          </form>

          <form method="POST" action="cancel_subscription.php" style="display:inline;">
            <input type="hidden" name="subscription_id" value="<?= $sub['id'] ?>">
            <button type="submit" onclick="return confirm('Are you sure you want to cancel this subscription?')">Cancel Subscription</button>
          </form>
        <?php elseif ($sub['status'] === 'paused'): ?>
          <form method="POST" action="reactivate_subscription.php" style="display:inline;">
            <input type="hidden" name="subscription_id" value="<?= $sub['id'] ?>">
            <button type="submit">Reactivate Subscription</button>
          </form>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>

  <div style="margin-top: 20px;">
    <a href="logout.php"><button>Logout</button></a>
  </div>
</body>
</html>
<?php
$conn->close();
?>
