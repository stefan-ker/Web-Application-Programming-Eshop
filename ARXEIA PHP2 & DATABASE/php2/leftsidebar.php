<div id="leftsidebar">
    <ul class="menu">
        <li><a href="index.php">Αρχική Σελίδα</a></li>
        <li><a href="user.php">Ατομική Σελίδα</a></li>
        <?php if (!isset($_SESSION['username'])) { ?> 
            <li><a href="signup.php">Εγγραφή</a></li>
        <?php } ?>
        <?php if (isset($_SESSION['username'])) { ?>  
            <li><a href="logout.php">Αποσύνδεση</a></li>
        <?php } ?>
    </ul>
</div>
