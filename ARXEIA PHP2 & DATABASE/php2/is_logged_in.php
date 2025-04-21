<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php?msg=Απαιτείται σύνδεση για πρόσβαση σε αυτήν τη σελίδα.");
    exit();
}
?>
