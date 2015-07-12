<!-- ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
RMIT School of Computer Science and Information Technology 
CPT 375 Web Database Applications SP2 2015    
ASSIGNMENT 1   
Alexandra Margaret Rodley s3372356

answer.php
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^ -->

<?php
  require_once('config.php');

  // session_start();

  /* xss mitigation functions 
  https://www.owasp.org/index.php/PHP_Security_Cheat_Sheet#Input_handling */

  // function xssafe($data, $encoding='UTF-8')
  // {
  //    return htmlspecialchars($data, ENT_QUOTES | ENT_HTML5, $encoding);
  // }

  // function xecho($data)
  // {
  //    echo xssafe($data);
  // }

  // $_SESSION['winename'] = $_GET['winename'];

  //htmlspecialchars($_GET['winename'], ENT_QUOTES);

  try {

    /* occasionally clearing cache during testing */
    apc_clear_cache();

    /* for A1 at least the winestore database is never modified, 
    but if code existed that inserted or deleted data from the table, 
    cached searches would need to be cleared. */

    /* generate unique key from query 
    md5 not safe for passwords but ok for use as cache key */
    $cache_key = hash('md5', $_SERVER['QUERY_STRING']);

    /* check if search results already cached */
    if (apc_exists('$cache_key')) {

      /* redirect user to results page */
      header("Location: ../results.php?search=$cache_key");
      exit();

    } else {

      /* perform validation */
      if (validateInput(trimInput($_GET))) {
        /* connect to DB */
        require_once('dbconnect.php');

        /* query database */
        $wines = getWines($dbconn, $_GET);

        /* cache search results */
        apc_store($cache_key, $wines);

        /* close DB connection */
        $dbconn = null;

        /* redirect user to results page */
        header("Location: ../results.php?search=$cache_key");
        exit();       
      }

    }

  } catch (PDOException $e) {
      print "Error!: " . $e->getMessage() . "<br>";

      /* something classier here? */
      die();
  }


function trimInput($params) {
  /* trim all input to remove whitespace */
  foreach($params as $key=>$value) {
    if($params[$key]) {
      trim($params[$key]);
    }
  }
  return $params;
}


function validateInput($params) {
  $errorFlag = False;

  /* validate individual input fields */
  if(($params['winename']) && (strlen($params['winename']) > 50)) {
    $errorFlag = True;
    $errorMsg .= 'Wine Name is too long. Maximum 50 characters.<br>';
  }

  if(($params['winery']) && (strlen($params['winery']) > 100)) {
    $errorFlag = True;
    $errorMsg .= 'Winery Name is too long. Maximum 100 characters.<br>';
  }

  if(($params['onhand']) && (!preg_match('/^[0-9]{1,5}$/', $params['onhand']))) {
    $errorFlag = True;
    $errorMsg .= 'Minimum Stock On Hand is invalid. Please enter a number, maximum 5 digits.<br>';
  }

  /* no digit length specified here since it's looking at the sum of item.qty entries.
     Although the max(sum(item.qty)) is currently 103, this could change if the db was 
     updated. In any case, constraining the length and calling it an error could be 
     misleading since the user might want to know that there are no wines with a certain 
     number of bottles ordered */
  if(($params['ordered']) && (!preg_match('/^[0-9]$/', $params['ordered']))) {
    $errorFlag = True;
    $errorMsg .= 'Minimum Wines Ordered is invalid. Please enter a number.<br>';
  }

  if(($params['mincost']) && (!preg_match('/^[0-9]{1,5}(\.[0-9]{0,2})?$/', $params['mincost']))) {
    $errorFlag = True;
    $errorMsg .= 'Minimum Cost is invalid. Please enter an integer or decimal number.<br>';
  }

  if(($params['maxcost']) && (!preg_match('/^[0-9]{1,5}(\.[0-9]{0,2})?$/', $params['maxcost']))) {
    $errorFlag = True;
    $errorMsg .= 'Maximum Cost is invalid. Please enter an integer or decimal number.<br>';
  }
  if ($errorFlag == True) {

    reportErrors($errorMsg);
    return False;

  } else {

    return True;
  }

}

function getWines($dbconn, $params) {

  /* DEBUG QUERY STRING */
  // foreach ($params as $key => $value) {
  //   print $key."\t".$value."\n";
  // }

  /* TO DO: VALIDATION! */

  /* prepare AND and HAVING clauses */
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


  /* build SQL query */
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
      SELECT wv.wine_id, GROUP_CONCAT(g.variety ORDER BY wv.id SEPARATOR " ") AS grapes
      /* used space as separator instead of comma for cleaner output */
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
  }
  $sql .= $having;

  /* debug finished query */
  //print $sql;

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

function reportErrors($msg) {
  /* use a templated html page, insert error messages */

  /* cache search results - no need to keep this cached very long */
  // apc_store($cache_key, $msg);

  /* redirect user to error page */
  header("Location: ../error.php?msg=$msg");
  exit(); 
}

?>











