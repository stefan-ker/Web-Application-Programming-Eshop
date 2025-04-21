<?php session_start(); ?>

<!DOCTYPE html>
<html lang="el">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Κονσόλες - eShop</title>
  <link rel="stylesheet" href="mycss.css"/>
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
    
   <div class="logo">
      <img src="logo/logosite.png" alt="Λογότυπο eShop">
    </div>

    <h2>Καλώς ήρθατε στο eShop μας!</h2>
    <p>Ανακαλύψτε τις καλύτερες κονσόλες παιχνιδιών στις καλύτερες τιμές!</p>

    <h2>Τα Προϊόντα μας</h2>
    
    <div class="product">
      <img src="images/playstation4.jpg" alt="PlayStation 4">
      <h3>PlayStation 4</h3>
      <p>Τιμή: 299€</p>
      <?php if (isset($_SESSION['username'])): ?>
        <form action="user.php" method="get">
          <input type="hidden" name="product_id" value="1">
          <button type="submit">Μετάβαση στην σελίδα προϊόντος</button>
        </form>
      <?php else: ?>
        <p><a href="login.php">Συνδεθείτε</a> για να αγοράσετε</p>
      <?php endif; ?>
    </div>

    <div class="product">
      <img src="images/playstation5.jpg" alt="PlayStation 5">
      <h3>PlayStation 5</h3>
      <p>Τιμή: 499€</p>
      <?php if (isset($_SESSION['username'])): ?>
        <form action="user.php" method="get">
          <input type="hidden" name="product_id" value="2">
          <button type="submit">Μετάβαση στην σελίδα προϊόντος</button>
        </form>
      <?php else: ?>
        <p><a href="login.php">Συνδεθείτε</a> για να αγοράσετε</p>
      <?php endif; ?>
    </div>

    <div class="product">
      <img src="images/xboxseriesx.jpg" alt="Xbox Series X">
      <h3>Xbox Series X</h3>
      <p>Τιμή: 499€</p>
      <?php if (isset($_SESSION['username'])): ?>
        <form action="user.php" method="get">
          <input type="hidden" name="product_id" value="3">
          <button type="submit">Μετάβαση στην σελίδα προϊόντος</button>
        </form>
      <?php else: ?>
        <p><a href="login.php">Συνδεθείτε</a> για να αγοράσετε</p>
      <?php endif; ?>
    </div>

    <div class="product">
      <img src="images/nintendoswitch.jpg" alt="Nintendo Switch">
      <h3>Nintendo Switch</h3>
      <p>Τιμή: 329€</p>
      <?php if (isset($_SESSION['username'])): ?>
        <form action="user.php" method="get">
          <input type="hidden" name="product_id" value="4">
          <button type="submit">Μετάβαση στην σελίδα προϊόντος</button>
        </form>
      <?php else: ?>
        <p><a href="login.php">Συνδεθείτε</a> για να αγοράσετε</p>
      <?php endif; ?>
    </div>

  </div>

  <?php require('footer.php'); ?>
  
</div>

</body>
</html>
