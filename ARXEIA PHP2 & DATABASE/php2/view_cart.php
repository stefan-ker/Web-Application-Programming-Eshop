<?php
session_start();

// Αν ο χρήστης δεν είναι συνδεδεμένος, τον ανακατευθύνουμε στη σελίδα σύνδεσης.
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Σύνδεση με τη βάση δεδομένων
$conn = new mysqli("localhost", "root", "", "my_databaseshop");
if ($conn->connect_error) {
    die("Σφάλμα σύνδεσης: " . $conn->connect_error);
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); 

$user_id = $_SESSION['user_id'];

// Ερώτημα για την ανάκτηση προϊόντων από το καλάθι του χρήστη
$sql = "SELECT c.product_id, c.product_name, c.price, c.quantity
        FROM cart c
        WHERE c.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Έλεγχος αν υπάρχουν προϊόντα στο καλάθι
$cart_empty = false;
if ($result->num_rows == 0) {
    $cart_empty = true;
}

// Αποθήκευση μηνύματος επιτυχίας ή σφάλματος
$message = '';

// Ολοκλήρωση παραγγελίας
if (isset($_POST['checkout']) && !$cart_empty) {
    $full_name = $_POST['full_name'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $postal_code = $_POST['postal_code'];
    $phone = $_POST['phone'];

    // Υπολογισμός του συνολικού ποσού
    $total = 0;
    while ($row = $result->fetch_assoc()) {
        $total += $row['price'] * $row['quantity'];
    }

    // Εισαγωγή παραγγελίας στον πίνακα `orders`
    $order_sql = "INSERT INTO orders (user_id, total, full_name, address, city, postal_code, phone) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
    $order_stmt = $conn->prepare($order_sql);
    $order_stmt->bind_param("idsssss", $user_id, $total, $full_name, $address, $city, $postal_code, $phone);

    if ($order_stmt->execute()) {
        $order_id = $conn->insert_id;  // ID της παραγγελίας που καταχωρήθηκε

        // Εισαγωγή των προϊόντων της παραγγελίας στον πίνακα `order_items`
        $result->data_seek(0); // Επιστροφή στην αρχή του αποτελέσματος
        while ($item = $result->fetch_assoc()) {
            $item_stmt = $conn->prepare("INSERT INTO order_items (order_id, product_name, price, quantity) 
                                         VALUES (?, ?, ?, ?)");
            $item_stmt->bind_param("isdi", $order_id, $item['product_name'], $item['price'], $item['quantity']);
            if (!$item_stmt->execute()) {
                $_SESSION['order_message'] = "Σφάλμα κατά την καταχώρηση προϊόντων στην παραγγελία.";
                header("Location: view_cart.php");
                exit();
            }
            $item_stmt->close();
        }

        // Διαγραφή των προϊόντων από το καλάθι μετά την ολοκλήρωση της παραγγελίας
        $delete_sql = "DELETE FROM cart WHERE user_id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("i", $user_id);
        if ($delete_stmt->execute()) {
            $_SESSION['order_message'] = "Η παραγγελία σας ολοκληρώθηκε με επιτυχία!";
        } else {
            $_SESSION['order_message'] = "Σφάλμα κατά την διαγραφή των προϊόντων.";
        }

        $delete_stmt->close();

        // Ανακατεύθυνση στην ίδια σελίδα για να εμφανιστεί το μήνυμα και να καθαρίσει το καλάθι
        header("Location: view_cart.php");
        exit();
    } else {
        $_SESSION['order_message'] = "Σφάλμα κατά την καταχώρηση της παραγγελίας.";
        header("Location: view_cart.php");
        exit();
    }
    $order_stmt->close();
}

// Ενημέρωση ποσότητας ή αφαίρεση προϊόντος
if (isset($_POST['update_quantity'])) {
    foreach ($_POST['quantity'] as $product_id => $quantity) {
        $quantity = intval($quantity); // Σιγουρευόμαστε ότι η ποσότητα είναι ακέραιος
        if ($quantity > 0) {
            $update_sql = "UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("iii", $quantity, $user_id, $product_id);
            $update_stmt->execute();
            $update_stmt->close();
        }
    }
    header("Location: view_cart.php"); // Επανεκφόρτωση της σελίδας μετά την ενημέρωση
    exit();
}

if (isset($_POST['remove_product'])) {
    foreach ($_POST['remove_product'] as $product_id) {
        $remove_sql = "DELETE FROM cart WHERE user_id = ? AND product_id = ?";
        $remove_stmt = $conn->prepare($remove_sql);
        $remove_stmt->bind_param("ii", $user_id, $product_id);
        $remove_stmt->execute();
        $remove_stmt->close();
    }
    header("Location: view_cart.php"); // Επανεκφόρτωση της σελίδας μετά τη διαγραφή
    exit();
}

?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Καλάθι Αγορών</title>
    <link rel="stylesheet" href="mycss.css">
</head>
<body>
<div id="container">
    <?php require('header.php'); ?>
    <?php require('leftsidebar.php'); ?>

    <div id="main">
        <h2>Το Καλάθι σας</h2>

   
        <?php if (isset($_SESSION['order_message'])): ?>
            <p style="color: green;"><?= $_SESSION['order_message'] ?></p>
            <?php unset($_SESSION['order_message']); ?>
        <?php elseif ($cart_empty): ?>
            <p>Το καλάθι σας είναι άδειο.</p>
        <?php else: ?>
        
            <form action="view_cart.php" method="POST">
                <table border="1" cellpadding="10" cellspacing="0" style="width: 100%;">
                    <tr>
                        <th>Προϊόν</th>
                        <th>Τιμή</th>
                        <th>Ποσότητα</th>
                        <th>Σύνολο</th>
                        <th>Διαχείριση</th>
                    </tr>
                    <?php 
                    $total = 0;
                    while($row = $result->fetch_assoc()): 
                        $subtotal = $row['price'] * $row['quantity'];
                        $total += $subtotal;
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($row['product_name']) ?></td>
                        <td><?= number_format($row['price'], 2) ?> €</td>
                        <td>
                            <input type="number" name="quantity[<?= $row['product_id'] ?>]" value="<?= $row['quantity'] ?>" min="1">
                        </td>
                        <td><?= number_format($subtotal, 2) ?> €</td>
                        <td>
                   
                            <button type="submit" name="update_quantity" value="update">Ανανέωση Ποσότητας</button>
                            <button type="submit" name="remove_product[]" value="<?= $row['product_id'] ?>">Αφαίρεση</button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    <tr>
                        <td colspan="3"><strong>Σύνολο:</strong></td>
                        <td><strong><?= number_format($total, 2) ?> €</strong></td>
                        <td></td>
                    </tr>
                </table>
            </form>

           <form action="view_cart.php" method="POST">
    <h3>Στοιχεία Αποστολής</h3>
    
    <label>Ονοματεπώνυμο:</label><br>
    <input type="text" name="full_name" required><br><br>

    <label>Διεύθυνση:</label><br>
    <input type="text" name="address" required><br><br>

    <label>Πόλη:</label><br>
    <input type="text" name="city" required><br><br>

    <label>Ταχυδρομικός Κώδικας:</label><br>
    <input type="text" name="postal_code" required><br><br>

    <label>Τηλέφωνο:</label><br>
    <input type="text" name="phone" required><br><br>


    <label>Μέθοδος Πληρωμής:</label><br>
    <input type="radio" name="payment_method" value="αντικαταβολή" checked disabled> Αντικαταβολή<br><br>

    <button type="submit" name="checkout">🛒 Ολοκλήρωση Παραγγελίας</button>
</form>

        <?php endif; ?>

    </div>

    <?php require('footer.php'); ?>
</div>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
