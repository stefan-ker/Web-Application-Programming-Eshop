<?php
session_start();

// Έλεγχος για την προσθήκη προϊόντος στο καλάθι
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $price = $_POST['price'];

    // Αν το καλάθι δεν υπάρχει ακόμα, το δημιουργούμε
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
    }

    // Προσθήκη του προϊόντος στο καλάθι
    $product = array(
        'product_id' => $product_id,
        'product_name' => $product_name,
        'price' => $price
    );

    // Αν το προϊόν είναι ήδη στο καλάθι, το αυξάνουμε την ποσότητα του
    $found = false;
    foreach ($_SESSION['cart'] as $index => $item) {
        if ($item['product_id'] == $product_id) {
            $_SESSION['cart'][$index]['quantity'] += 1;
            $found = true;
            break;
        }
    }

    // Αν δεν βρεθεί το προϊόν στο καλάθι, το προσθέτουμε
    if (!$found) {
        $product['quantity'] = 1;
        $_SESSION['cart'][] = $product;
    }
}

// Προβολή των προϊόντων στο καλάθι
?>

<!DOCTYPE html>
<html lang="el">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Καλάθι - eShop</title>
  <link rel="stylesheet" href="mycss.css"/>
  <style>
    .cart-table {
      width: 100%;
      border-collapse: collapse;
    }
    .cart-table th, .cart-table td {
      padding: 10px;
      border: 1px solid #ddd;
    }
    .cart-table th {
      background-color: #f2f2f2;
    }
    .cart-table td {
      text-align: center;
    }
    .total-price {
      font-size: 20px;
      font-weight: bold;
    }
  </style>
</head>
<body>

<div id="container">
  <?php require('header.php'); ?>
  <?php require('leftsidebar.php'); ?>

  <div id="main">
    <h2>Το Καλάθι Σας</h2>
    <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
      <table class="cart-table">
        <tr>
          <th>Προϊόν</th>
          <th>Τιμή</th>
          <th>Ποσότητα</th>
          <th>Σύνολο</th>
        </tr>
        <?php
        $total_price = 0;
        foreach ($_SESSION['cart'] as $item):
          $subtotal = $item['price'] * $item['quantity'];
          $total_price += $subtotal;
        ?>
          <tr>
            <td><?php echo $item['product_name']; ?></td>
            <td><?php echo $item['price']; ?>€</td>
            <td><?php echo $item['quantity']; ?></td>
            <td><?php echo $subtotal; ?>€</td>
          </tr>
        <?php endforeach; ?>
      </table>
      <p class="total-price">Συνολική Τιμή: <?php echo $total_price; ?>€</p>
      <form action="checkout.php" method="get">
        <button type="submit">Μετάβαση στην Αγορά</button>
      </form>
    <?php else: ?>
      <p>Το καλάθι σας είναι άδειο.</p>
    <?php endif; ?>
  </div>

  <?php require('footer.php'); ?>
</div>

</body>
</html>
