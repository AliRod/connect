<?php


// $bar = 'BAR';
// apc_store('foo', $bar);
// var_dump(apc_fetch('foo'));

$key = $_GET['s'];

print $key;

$searchresult = apc_fetch($key);

fillTemplate($searchresult);

function fillTemplate($results) {

  require_once ("php/MiniTemplator.class.php");

  $numberOfResults = count($results);

  $t = new MiniTemplator;

  $t->readTemplateFromFile ("html/searchresult_template.htm");

  $t->setVariable ("numberOfResults", $numberOfResults);
  $t->addBlock ("numresults");

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