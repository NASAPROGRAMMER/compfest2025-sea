<?php
session_start();

// Generate CSRF token jika belum ada
if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

// Ambil testimonial dari DB
$conn = new mysqli("localhost", "root", "", "sea_catering");
if ($conn->connect_error) die("Koneksi gagal: " . $conn->connect_error);

$sql = "SELECT name, review, rating FROM testimonials ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Testimonials - SEA Catering</title>
  <link rel="stylesheet" href="styles.css" />
</head>
<body>
  <header>
    <div class="navbar">
      <div class="logo">Sea <span>Catering</span></div>
      <nav>
        <div class="menu-toggle" id="mobile-menu">&#9776;</div>
        <ul class="nav-links" id="nav-links">
          <li class="nav-item"><a href="index.html">Home</a></li>
          <li class="nav-item"><a href="mealplans.html">Menu Plans</a></li>
          <li class="nav-item"><a href="index.html#contact">About Us</a></li>
          <li class="nav-item"><a href="subscription.php">Subscription</a></li>
          <li class="nav-item active"><a href="testimonial.php">Testimonial</a></li>
        </ul>
      </nav>
    </div>
  </header>

  <section class="testimonial-section">
    <h2>What Our Customers Say</h2>
    <div class="testimonial-carousel">
      <?php if ($result && $result->num_rows > 0): ?>
        <?php $first = true; while($row = $result->fetch_assoc()): ?>
          <div class="testimonial-slide <?= $first ? 'active' : '' ?>">
            <p>"<?= htmlspecialchars($row['review']) ?>"</p>
            <h4>- <?= htmlspecialchars($row['name']) ?></h4>
            <span><?= htmlspecialchars($row['rating']) ?></span>
          </div>
        <?php $first = false; endwhile; ?>
      <?php else: ?>
        <p>No testimonials yet.</p>
      <?php endif; ?>
    </div>

    <div class="testimonial-controls">
      <button id="prev">&#8592;</button>
      <button id="next">&#8594;</button>
    </div>
  </section>

  <section class="testimonial-form-section">
    <h3>Leave Your Testimonial</h3>
    <form class="testimonial-form" action="submit_testimonial.php" method="POST">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION["csrf_token"]) ?>">
      <input type="text" name="name" placeholder="Your Name" required />
      <textarea name="review" placeholder="Your Review" required></textarea>
      <select name="rating" required>
        <option value="">Rating</option>
        <option value="★★★★★">★★★★★</option>
        <option value="★★★★☆">★★★★☆</option>
        <option value="★★★☆☆">★★★☆☆</option>
        <option value="★★☆☆☆">★★☆☆☆</option>
        <option value="★☆☆☆☆">★☆☆☆☆</option>
      </select>
      <button type="submit">Submit Review</button>
    </form>
  </section>

  <script>
    const slides = document.querySelectorAll(".testimonial-slide");
    let currentSlide = 0;

    document.getElementById("next")?.addEventListener("click", () => {
      slides[currentSlide]?.classList.remove("active");
      currentSlide = (currentSlide + 1) % slides.length;
      slides[currentSlide]?.classList.add("active");
    });

    document.getElementById("prev")?.addEventListener("click", () => {
      slides[currentSlide]?.classList.remove("active");
      currentSlide = (currentSlide - 1 + slides.length) % slides.length;
      slides[currentSlide]?.classList.add("active");
    });

    document.getElementById("mobile-menu")?.addEventListener("click", () => {
      document.getElementById("nav-links")?.classList.toggle("active");
    });
  </script>
</body>
</html>

<?php $conn->close(); ?>
