<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login.html");
    exit;
}

// Buat CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Subscription - SEA Catering</title>
  <link rel="stylesheet" href="styles.css" />
</head>
<body>
  <header>
    <div class="navbar">
      <div class="logo">Sea <span>Catering</span></div>
      <nav>
        <ul class="nav-links">
          <li class="nav-item"><a href="index.html">Home</a></li>
          <li class="nav-item"><a href="mealplans.html">Menu Plans</a></li>
          <li class="nav-item"><a href="index.html#contact">About Us</a></li>
          <li class="nav-item active"><a href="subscription.php">Subscription</a></li>
          <li class="nav-item"><a href="testimonial.php">Testimonial</a></li>
        </ul>
      </nav>
      <button class="login-btn">Log in</button>
    </div>
  </header>

  <section class="subscription-section">
    <h2>Subscribe to Our Meal Plan</h2>
    <form class="subscription-form" action="submit_subscription.php" method="POST">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>" />

      <label>*Full Name:</label>
      <input type="text" name="name" required />

      <label>*Active Phone Number:</label>
      <input type="tel" name="phone" required pattern="[0-9]{8,15}" title="Enter a valid phone number (8-15 digits)" />

      <label>*Select Plan:</label>
      <select name="plan" required>
        <option value="">-- Select a Plan --</option>
        <option value="30000">Diet Plan – Rp30.000,00</option>
        <option value="40000">Protein Plan – Rp40.000,00</option>
        <option value="60000">Royal Plan – Rp60.000,00</option>
      </select>

      <label>*Meal Types:</label>
      <div class="checkbox-group">
        <label><input type="checkbox" name="mealType[]" value="1" /> Breakfast</label>
        <label><input type="checkbox" name="mealType[]" value="1" /> Lunch</label>
        <label><input type="checkbox" name="mealType[]" value="1" /> Dinner</label>
      </div>

      <label>*Delivery Days:</label>
      <div class="checkbox-group days">
        <label><input type="checkbox" name="deliveryDay[]" value="1" /> Monday</label>
        <label><input type="checkbox" name="deliveryDay[]" value="1" /> Tuesday</label>
        <label><input type="checkbox" name="deliveryDay[]" value="1" /> Wednesday</label>
        <label><input type="checkbox" name="deliveryDay[]" value="1" /> Thursday</label>
        <label><input type="checkbox" name="deliveryDay[]" value="1" /> Friday</label>
        <label><input type="checkbox" name="deliveryDay[]" value="1" /> Saturday</label>
        <label><input type="checkbox" name="deliveryDay[]" value="1" /> Sunday</label>
      </div>

      <label>Allergies / Dietary Restrictions:</label>
      <textarea name="allergies" rows="3"></textarea>

      <button type="submit">Submit Subscription</button>
    </form>
  </section>
</body>
</html>
