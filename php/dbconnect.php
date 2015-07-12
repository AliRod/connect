<?php

  /* ********************************************
  RMIT School of Computer Science and Information Technology 
  CPT 375 Web Database Applications SP2 2015    
  ASSIGNMENT 1   
  Alexandra Margaret Rodley s3372356

  dbconnect.php
  ********************************************* */

  require_once('db.php');

  /* make safe connection to database with PHP Data Object */
  try {
      $dbconn = new PDO(DB_NAME, DB_USER, DB_PW);
      $dbconn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $dbconn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

  } catch (PDOException $e) {
      print "Connection Error!: " . $e->getMessage() . "<br>";
      die();
  }

?>