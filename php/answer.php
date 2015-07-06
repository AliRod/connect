<?php

  /* connect to DB */
  require_once('dbconnect.php');

  try {

    $wines = getWines($dbconn, $_GET);

    /* close DB connection */
    $dbconn = null;

    fillTemplate($wines);

  } catch (PDOException $e) {
      print "Error!: " . $e->getMessage() . "<br>";

      /* something classier here? */
      die();
  }


function getWines($dbconn, $params) {

  /* DEBUG GET VARIABLES */
  foreach ($params as $key => $value) {
    echo $key."\t".$value."\n";
  }

  /* check input and prepare query and variables used with LIKE 
   * must include VALIDATION here later on 
   */

  if($params['winename']) {
    $ands[] = 'w.wine_name LIKE :winename ';
    $winename = '%'.$params['winename'].'%';
  }

  if($params['winery']) {
    $ands[] = 'wir.winery_name LIKE :winery';
    $winery = '%'.$params['winery'].'%';
  }

  if($params['region'] != 'All') $ands[] = 'wir.region_name = :region';

  if($params['onhand']) $ands[] = 'inv.onhand >= :onhand';

  if($params['ordered']) $ands[] = 'it.ordered >= :ordered';

  if($params['mincost']) $ands[] = 'inv.mincost >= :mincost';

  if($params['maxcost']) $ands[] = 'inv.mincost <= :maxcost';

  if($params['grape'] != 'Any') $having = ' HAVING FIND_IN_SET(:grape, wvg.grapes)>0 ';


  /* build sql statement */
  $sql = 'SELECT 
      w.wine_id, 
      w.wine_name,
      wvg.grapes,
      w.year,
      wir.winery_name, 
      wir.region_name, 
      inv.mincost, 
      inv.onhand,
      it.ordered,
      it.revenue
    FROM wine w
    LEFT JOIN
    (
      SELECT wi.winery_id, wi.winery_name, r.region_name
      FROM winery wi
      LEFT JOIN region r
      ON wi.region_id = r.region_id
    ) 
    AS wir
    ON w.winery_id=wir.winery_id

    LEFT JOIN
    (
      SELECT wv.wine_id, GROUP_CONCAT(g.variety ORDER BY wv.id) AS grapes
      FROM wine_variety wv
      LEFT JOIN grape_variety g
      ON wv.variety_id=g.variety_id
      GROUP BY wv.wine_id
    ) 
    AS wvg
    ON w.wine_id=wvg.wine_id

    LEFT JOIN
    (
      SELECT inven.wine_id, MIN(inven.cost) AS mincost, SUM(inven.on_hand) AS onhand
      FROM inventory inven
      GROUP BY inven.wine_id
    )
    AS inv
    ON w.wine_id=inv.wine_id

    LEFT JOIN
    (
      SELECT items.wine_id, SUM(items.qty) AS ordered, SUM(items.price) AS revenue
      FROM items
      GROUP BY items.wine_id
    )
    AS it
    ON w.wine_id=it.wine_id WHERE w.year BETWEEN :minyear AND :maxyear ';

  /* concatenate AND and HAVING clauses as required by user */
  if (count($ands) > 0) {
    $sql .= 'AND ';
    $sql .= implode(' AND ', $ands);
    $sql .= $having;
  }


  /* debug */
  print $sql;

  /* PDO prepare statement */
  $pst = $dbconn->prepare($sql);

  /* bind parameters where they exist */
  if($params['winename']) {
    $pst->bindParam(':winename', $winename, PDO::PARAM_STR);
  }
  if($params['winery']) {
    $pst->bindParam(':winery', $winery, PDO::PARAM_STR);
  }  
  if($params['region'] != 'All') {
    $pst->bindParam(':region', $params['region'], PDO::PARAM_STR);
  }
  if($params['grape'] != 'Any') {
    $pst->bindParam(':grape', $params['grape'], PDO::PARAM_STR);
  }
  if($params['onhand']) {
    $pst->bindParam(':onhand', $params['onhand'], PDO::PARAM_INT);
  } 
  if($params['ordered']) {
    $pst->bindParam(':ordered', $params['ordered'], PDO::PARAM_INT);
  } 
  if($params['mincost']) {
    $pst->bindParam(':mincost', $params['mincost'], PDO::PARAM_INT);
  }
  if($params['maxcost']) {
    $pst->bindParam(':maxcost', $params['maxcost'], PDO::PARAM_INT);
  }

  /* bind year parameters with defaults if untouched by user */
  $pst->bindParam(':minyear', $params['minyear'], PDO::PARAM_INT);
  $pst->bindParam(':maxyear', $params['maxyear'], PDO::PARAM_INT);
  
  /* make call & get results */
  $pst->execute();
  $results = $pst->fetchAll();

  return $results;

}


function fillTemplate($results) {

  //   /* test html printout */
  // print "<table>";
  // print "<tr><th>Wine ID</th><th>Wine Name</th></tr>";
  // foreach ($results as $row) {
  //   print "<tr>";
  //   print "<td>".$row['wine_id']."</td>";
  //   print "<td>".$row['wine_name']."</td>";
  //   print "<td>".$row['grapes']."</td>";
  //   print "<td>".$row['year']."</td>";
  //   print "<td>".$row['winery_name']."</td>";
  //   print "<td>".$row['region_name']."</td>";
  //   print "<td>".$row['mincost']."</td>";
  //   print "<td>".$row['onhand']."</td>";
  //   print "<td>".$row['ordered']."</td>";
  //   print "<td>".$row['revenue']."</td>";
  //   print "</tr>";
  // }
  // print "</table>";

  require_once ("MiniTemplator.class.php");

  $numberOfResults = count($results);

  $t = new MiniTemplator;

  $t->readTemplateFromFile ("searchresult_template.htm");

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











