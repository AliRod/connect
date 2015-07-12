<?php
session_start();

session_destroy();

/* redirect user to search page */
header("Location: ../search.php");
exit(); 

?>