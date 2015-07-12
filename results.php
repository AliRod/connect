<?php

  /* ********************************************
  RMIT School of Computer Science and Information Technology 
  CPT 375 Web Database Applications SP2 2015    
  ASSIGNMENT 1   
  Alexandra Margaret Rodley s3372356

  results.php
  ********************************************* */

  require_once('php/config.php');

  /* get search results from cache (APC) */
  $key = $_GET['search'];
  $searchresult = apc_fetch($key);

  fillTemplate($searchresult);

  /* instructions for displaying results in template */
  function fillTemplate($results) {

    require_once ("php/MiniTemplator.class.php");

    $numberOfResults = count($results);

    $t = new MiniTemplator;

    $ok = $t->readTemplateFromFile ("html/searchresult_template.htm");
    if (!$ok) die ("MiniTemplator.readTemplateFromFile failed.");

    $t->setVariable ("numberOfResults", $numberOfResults);
    $t->addBlock ("numresults");

    if (count($results) > 0) {
      $t->addBlock ("tablehead");
      $t->addBlock ("totop");
    }

    foreach ($results as $row) {
      $t->setVariable ("ID", $row['wine_id']);
      $t->setVariable ("Wine", $row['wine_name']);
      $t->setVariable ("Variety", $row['grapes']);
      $t->setVariable ("Year", $row['year']);
      $t->setVariable ("Winery", $row['winery_name']);
      $t->setVariable ("Region", $row['region_name']);
      $t->setVariable ("MinimumCost", $row['mincost']);
      $t->setVariable ("StockOnHand", $row['onhand']);
      $t->setVariable ("BottlesOrdered", $row['ordered']);
      $t->setVariable ("TotalSales", $row['revenue']);
      $t->addBlock ("winerow");
    }

    $t->generateOutput();
  }

?>