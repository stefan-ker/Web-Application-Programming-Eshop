<?php
session_start(); 

$msg = ""; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $host = "localhost";
    $user = "root"; 
    $password = ""; 
    $dbname = "my_databaseshop"; 

    // Σύνδεση με τη βάση δεδομένων
    $conn = new mysqli($host, $user, $password, $dbname);

    if ($conn->connect_error) {
        die("Σφάλμα σύνδεσης: " . $conn->connect_error);
    }

    // Παίρνουμε τα δεδομένα από τη φόρμα
    $username = trim($_POST['username']);
    $password_input = trim($_POST['password']);

    // Έλεγχος στη βάση δεδομένων για τον χρήστη
    $sql = "SELECT id, username, password FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Αν οι κωδικοί ταιριάζουν
        if (password_verify($password_input, $row['password'])) {
            // Σύνδεση του χρήστη
            $_SESSION['username'] = $row['username'];
            $_SESSION['user_id'] = $row['id'];
            header("Location: user.php");
            exit();
        } else {
            // Λάθος κωδικός
            $msg = "Λάθος κωδικός!";
        }
    } else {
        // Αν δεν υπάρχει χρήστης με το όνομα, ελέγχουμε για το 'test' χρήστη
        if ($username == 'test' && $password_input == '1234') {
            // Συνδέουμε τον χρήστη 'test'
            $_SESSION['username'] = 'test';
            $_SESSION['user_id'] = 1;  // Το user_id για τον test χρήστη
            header("Location: user.php");  
            exit();
        } else {
            $msg = "Ο χρήστης δεν βρέθηκε!";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Σύνδεση Χρήστη</title>
    <link rel="stylesheet" type="text/css" href="mycss.css"/>
</head>
<body>

<div id="container">
  <?php require('header.php'); ?>
  <?php require('leftsidebar.php'); ?>

  <?php if ($msg != "") { echo '<p style="color:red;">' . $msg . '</p>'; } ?>

  <div id="main">
    <h2>Σύνδεση Χρήστη</h2>
    <form action="login.php" method="post">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br>
        
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br>
        
        <input type="submit" value="Σύνδεση">
    </form>
  </div>

  <?php require('footer.php'); ?>
</div>

</body>
</html>
