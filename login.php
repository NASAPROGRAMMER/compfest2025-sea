<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    $conn = new mysqli("localhost", "root", "", "sea_catering");
    $stmt = $conn->prepare("SELECT id, full_name, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $name, $hashedPassword, $role);
        $stmt->fetch();

        if (password_verify($password, $hashedPassword)) {
            $_SESSION["user_id"] = $id;
            $_SESSION["full_name"] = $name;
            $_SESSION["email"] = $email;
            $_SESSION["role"] = $role;

            header("Location: index.html");
            exit;
        } else {
            echo "❌ Invalid credentials.";
        }
    } else {
        echo "❌ No user found.";
    }

    $stmt->close();
    $conn->close();
}
?>
