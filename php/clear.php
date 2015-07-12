<?php

	/* ********************************************
	RMIT School of Computer Science and Information Technology 
	CPT 375 Web Database Applications SP2 2015    
	ASSIGNMENT 1   
	Alexandra Margaret Rodley s3372356

	clear.php
	********************************************* */

	/* simple script to destroy current session and 
	reload the search page with default values */
	
	session_start();

	session_destroy();

	/* redirect user to search page */
	header("Location: ../search.php");
	exit(); 

?>