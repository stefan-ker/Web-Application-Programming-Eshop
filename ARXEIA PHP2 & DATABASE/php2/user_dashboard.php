<?php
session_start();  

if (!isset($_SESSION['username'])) {
    header("Location: login.php");  
    exit();
}

// Προϊόντα (προέρχονται από βάση δεδομένων)
$products = [
    ["id" => 1, "name" => "PlayStation 4", "price" => 299.99, "image" => "PlayStation4.jpg"],
    ["id" => 2, "name" => "Xbox One", "price" => 279.99, "image" => "xboxseriesx.jpg"],
    ["id" => 3, "name" => "Nintendo Switch", "price" => 349.99, "image" => "nintendoswitch.jpg"],
    ["id" => 4, "name" => "PlayStation 5", "price" => 499.99, "image" => "PlayStation5.jpg"]
];

// Αν ο χρήστης προσθέσει προϊόν στο καλάθι
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];
    
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    if (!isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] = 1;
    } else {
        $_SESSION['cart'][$product_id]++;
    }
    
    header("Location: user.php?msg=Το προϊόν προστέθηκε στο καλάθι!");
    exit();
}
?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Προσωπική Σελίδα Χρήστη</title>
    <link rel="stylesheet" type="text/css" href="mycss.css"/>
	 <style>
    .logo {
      text-align: center;
      margin-bottom: 20px;
    }
    .logo img {
      max-width: 200px;
    }
    .product {
      border: 1px solid #ddd;
      border-radius: 10px;
      padding: 15px;
      text-align: center;
      margin: 15px;
      display: inline-block;
      width: 250px;
    }
    .product img {
      width: 100%;
      height: auto;
      border-radius: 10px;
    }
    .product h3 {
      font-size: 18px;
      margin: 10px 0;
    }
    .product p {
      color: #555;
    }
    .product button {
      background-color: #28a745;
      color: white;
      padding: 10px;
      border: none;
      cursor: pointer;
      border-radius: 5px;
    }
    .product button:hover {
      background-color: #218838;
    }
  </style>
</head>
<body>

<div id="container">
  <?php require('header.php'); ?>
  <?php require('leftsidebar.php'); ?>

  <div id="main">
    <h2>Καλωσορίσατε, <?php echo $_SESSION['username']; ?>!</h2>
    <p>Αυτή είναι η προσωπική σας σελίδα.</p>
    
    <?php if (isset($_GET['msg'])) { echo '<p style="color: green;">' . $_GET['msg'] . '</p>'; } ?>
    
    <h3>Διαθέσιμα Προϊόντα</h3>
    <div class="products">
        <?php foreach ($products as $product): ?>
            <div class="product">
                <img src="images/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" width="150">
                <h4><?php echo $product['name']; ?></h4>
                <p>Τιμή: €<?php echo number_format($product['price'], 2); ?></p>
                <form method="POST" action="user.php">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <button type="submit">Προσθήκη στο Καλάθι</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
    
    <a href="cart.php">Δείτε το καλάθι σας</a>
  </div>

  <?php require('footer.php'); ?>
</div>

</body>
</html>