<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST["name"];
    $phone = $_POST["phone"];
    $planValue = $_POST["plan"];
    $planLabel = "";

    // Label rencana
    switch ($planValue) {
        case "30000":
            $planLabel = "Diet Plan";
            break;
        case "40000":
            $planLabel = "Protein Plan";
            break;
        case "60000":
            $planLabel = "Royal Plan";
            break;
        default:
            $planLabel = "Unknown";
    }

    $mealTypes = implode(", ", $_POST["mealType"] ?? []);
    $deliveryDays = implode(", ", $_POST["deliveryDay"] ?? []);
    $allergies = $_POST["allergies"] ?? "";

    $planPrice = intval($planValue);
    $mealCount = count($_POST["mealType"] ?? []);
    $dayCount = count($_POST["deliveryDay"] ?? []);
    $total = $planPrice * $mealCount * $dayCount * 4.3;

    // Koneksi database
    $conn = new mysqli("localhost", "root", "", "sea_catering");
    if ($conn->connect_error) {
        die("❌ Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("INSERT INTO subscriptions (name, phone, plan, meal_types, delivery_days, allergies, total_price) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssi", $name, $phone, $planLabel, $mealTypes, $deliveryDays, $allergies, $total);

    if ($stmt->execute()) {
        echo "<h2>✅ Subscription submitted successfully!</h2>";
        echo "<p>Total Price: Rp" . number_format($total, 0, ',', '.') . ",00</p>";
       if ($stmt->execute()) {
 
    echo "<p>Redirecting back to subscription page in 5 seconds...</p>";
    echo "<script>
        setTimeout(function() {
            window.location.href = 'subscription.html';
        }, 5000);
    </script>";
} else {
    echo "❌ Error inserting: " . $stmt->error;
}

    } else {
        echo "❌ Error inserting: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "❌ Invalid request.";
}
?>
