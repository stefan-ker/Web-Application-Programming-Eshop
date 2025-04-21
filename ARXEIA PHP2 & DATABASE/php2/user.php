<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'my_databaseshop';

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Σφάλμα σύνδεσης με τη βάση δεδομένων: " . $conn->connect_error);
}

// Προϊόντα
$products = [
    ["id" => 1, "name" => "PlayStation 4", "price" => 299.99, "image" => "PlayStation4.jpg"],
    ["id" => 2, "name" => "Xbox One", "price" => 279.99, "image" => "xboxseriesx.jpg"],
    ["id" => 3, "name" => "Nintendo Switch", "price" => 349.99, "image" => "nintendoswitch.jpg"],
    ["id" => 4, "name" => "PlayStation 5", "price" => 499.99, "image" => "PlayStation5.jpg"]
];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];
    $user_id = $_SESSION['user_id'];

    $sql = "SELECT * FROM cart WHERE user_id = ? AND product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $sql_update = "UPDATE cart SET quantity = quantity + 1 WHERE user_id = ? AND product_id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("ii", $user_id, $product_id);
        $stmt_update->execute();
    } else {
        $sql_insert = "INSERT INTO cart (user_id, product_id, product_name, price, quantity) VALUES (?, ?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("issis", $user_id, $product_id, $product_name, $product_price, $quantity);

        foreach ($products as $product) {
            if ($product['id'] == $product_id) {
                $product_name = $product['name'];
                $product_price = $product['price'];
                $quantity = 1;
                break;
            }
        }

        $stmt_insert->execute();
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
            transition: transform 0.3s ease, box-shadow 0.3s ease;
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
        .product.hovered {
            transform: scale(1.07);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.3);
            background-color: #f5f5f5;
        }

        .popup-success {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background-color: #28a745;
            color: white;
            padding: 15px 25px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            z-index: 9999;
            font-weight: bold;
            opacity: 1;
            transition: opacity 0.5s ease;
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

        <h3>Διαθέσιμα Προϊόντα</h3>
        <div class="products">
            <?php foreach ($products as $product): ?>
                <div class="product">
                    <img src="images/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
                    <h4><?php echo $product['name']; ?></h4>
                    <p>Τιμή: €<?php echo number_format($product['price'], 2); ?></p>
                    <form method="POST" action="user.php">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        <button type="submit">Προσθήκη στο Καλάθι</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <?php require('footer.php'); ?>
</div>


<div id="popup-msg" class="popup-success" style="display:none;">
    <p id="popup-text"></p>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Hover effect
        const products = document.querySelectorAll(".product");
        products.forEach(product => {
            product.addEventListener("mouseenter", () => {
                product.classList.add("hovered");
            });
            product.addEventListener("mouseleave", () => {
                product.classList.remove("hovered");
            });
        });

        // Success popup
        const params = new URLSearchParams(window.location.search);
        const msg = params.get("msg");
        if (msg) {
            const popup = document.getElementById("popup-msg");
            const text = document.getElementById("popup-text");
            text.textContent = decodeURIComponent(msg);
            popup.style.display = "block";
            setTimeout(() => {
                popup.style.opacity = '0';
                setTimeout(() => popup.style.display = 'none', 500);
            }, 3000);
        }
    });
</script>

</body>
</html>
