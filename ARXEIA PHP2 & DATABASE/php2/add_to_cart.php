<?php
session_start();

// Έλεγχος αν ο χρήστης είναι συνδεδεμένος
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Σύνδεση με τη βάση δεδομένων
$conn = new mysqli("localhost", "root", "", "my_databaseshop");
if ($conn->connect_error) {
    die("Σφάλμα σύνδεσης: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];

// Λήψη των δεδομένων της φόρμας
$product_id = $_POST['product_id'];
$product_name = $_POST['product_name'];
$price = $_POST['price'];
$quantity = $_POST['quantity'];

// Έλεγχος αν το προϊόν υπάρχει ήδη στο καλάθι του χρήστη
$sql = "SELECT * FROM cart WHERE user_id = ? AND product_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Αν το προϊόν υπάρχει ήδη, αυξάνουμε την ποσότητα
    $row = $result->fetch_assoc();
    $new_quantity = $row['quantity'] + $quantity;

    // Ενημέρωση της ποσότητας του προϊόντος
    $update_sql = "UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("iii", $new_quantity, $user_id, $product_id);
    $update_stmt->execute();
    $update_stmt->close();
} else {
	var_dump($_POST);

    // Αν το προϊόν δεν υπάρχει στο καλάθι, το προσθέτουμε
    $insert_sql = "INSERT INTO cart (user_id, product_id, product_name, price, quantity) VALUES (?, ?, ?, ?, ?)";
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param("iisdi", $user_id, $product_id, $product_name, $price, $quantity);
    $insert_stmt->execute();
    $insert_stmt->close();
}

// Ανακατεύθυνση πίσω στο καλάθι ή σε άλλη σελίδα
header("Location: cart.php");
exit();
?>
