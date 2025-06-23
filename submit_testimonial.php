<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST["name"];
    $review = $_POST["review"];
    $rating = $_POST["rating"];

    $conn = new mysqli("localhost", "root", "", "sea_catering");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("INSERT INTO testimonials (name, review, rating) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $review, $rating);

    if ($stmt->execute()) {
        echo "<h2>✅ Testimonial submitted successfully!</h2>";
        echo "<script>
            setTimeout(function() {
              window.location.href = 'testimonial.html';
            }, 5000);
        </script>";
    } else {
        echo "❌ Error inserting testimonial: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "❌ Invalid request.";
}
?>
