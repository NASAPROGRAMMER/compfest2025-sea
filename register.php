<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Koneksi ke database
$conn = new mysqli("localhost", "root", "", "sea_catering");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil data dari POST
$name = $_POST["full_name"];
$email = $_POST["email"];
$password = $_POST["password"];

// Validasi sederhana
if (empty($name) || empty($email) || empty($password)) {
    die("Semua field wajib diisi.");
}

// Hash password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Simpan ke database
$stmt = $conn->prepare("INSERT INTO users (full_name, email, password) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $name, $email, $hashedPassword);

if ($stmt->execute()) {
    echo "✅ Registrasi berhasil!";
    
        echo "<p>Redirecting back to subscription page in 5 seconds...</p>";
    echo "<script>
        setTimeout(function() {
            window.location.href = 'register.html';
        }, 5000);
    </script>";
} else {
    echo "❌ Gagal: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
