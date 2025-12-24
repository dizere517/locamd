<?php
session_start();
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION["user_id"]   = $id;
            $_SESSION["username"]  = $username;
            echo "success";
        } else {
            echo "Mot de passe incorrect !";
        }
    } else {
        echo "Utilisateur inconnu !";
    }
    $stmt->close();
    $conn->close();
}
?>