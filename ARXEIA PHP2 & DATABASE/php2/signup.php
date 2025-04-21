<?php
session_start();

$error_message = ""; // Για να αποθηκεύσουμε τα μηνύματα σφάλματος

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = new mysqli("localhost", "root", "", "my_databaseshop");

    if ($conn->connect_error) {
        die("Σφάλμα σύνδεσης: " . $conn->connect_error);
    }

    // Λήψη των δεδομένων από τη φόρμα
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Έλεγχος αν το username υπάρχει ήδη στη βάση
    $check_username_sql = "SELECT id FROM users WHERE username = ?";
    $check_username_stmt = $conn->prepare($check_username_sql);
    $check_username_stmt->bind_param("s", $username);
    $check_username_stmt->execute();
    $check_username_stmt->store_result();

    if ($check_username_stmt->num_rows > 0) {
        $error_message = "❌ Το όνομα χρήστη χρησιμοποιείται ήδη!";
    }

    // Έλεγχος αν το email υπάρχει ήδη στη βάση
    $check_email_sql = "SELECT id FROM users WHERE email = ?";
    $check_email_stmt = $conn->prepare($check_email_sql);
    $check_email_stmt->bind_param("s", $email);
    $check_email_stmt->execute();
    $check_email_stmt->store_result();

    if ($check_email_stmt->num_rows > 0) {
        $error_message = "❌ Το email χρησιμοποιείται ήδη!";
    }

    // Αν δεν υπήρξαν σφάλματα, κάνουμε την εγγραφή
    if (empty($error_message)) {
        // Εισαγωγή του χρήστη στη βάση δεδομένων
        $insert_sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param("sss", $username, $email, $password);

        if ($stmt->execute()) {
            $_SESSION['username'] = $username;
            $_SESSION['user_id'] = $conn->insert_id;
            header("Location: user_dashboard.php");
            exit();
        } else {
            $error_message = "Σφάλμα κατά την εγγραφή: " . $stmt->error;
        }

        $stmt->close();
    }

    $check_username_stmt->close();
    $check_email_stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="el">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Εγγραφή Νέου Χρήστη</title>
  <link rel="stylesheet" href="mycss.css">
  <style>
    .error {
        color: red;
        font-weight: bold;
        margin-bottom: 10px;
    }
  </style>
</head>
<body>
<div id="container">
  <?php require('header.php'); ?>
  <?php require('leftsidebar.php'); ?>

  <div id="main">
    <h2>Εγγραφή Νέου Χρήστη</h2>


    <?php if (!empty($error_message)): ?>
      <div class="error"><?= $error_message ?></div>
    <?php endif; ?>

    <form action="signup.php" method="POST">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br>

        <input type="submit" value="Εγγραφή">
    </form>
  </div>

  <?php require('footer.php'); ?>
</div>
</body>
</html>
