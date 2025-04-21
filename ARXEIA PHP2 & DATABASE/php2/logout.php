<?php

// υλοποιεί το logout και ανακατευθύνει στη home με μήνυμα

session_start();    // connect to the session...
session_destroy();  // ...and destroy it
header("Location: index.php?msg=Επιτυχής Αποσύνδεση!");
exit();


?>
