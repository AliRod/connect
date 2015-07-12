<?php

  /* ********************************************
  RMIT School of Computer Science and Information Technology 
  CPT 375 Web Database Applications SP2 2015    
  ASSIGNMENT 1   
  Alexandra Margaret Rodley s3372356

  search.php
  ********************************************* */
  
  session_start();

  /* when prepopulating input fields after user returns to search page,
  need to check whether it was previously set and echo correct value */
  function fillValue($var) {
    if (isset($_SESSION[$var])) {
      echo $_SESSION[$var];
    } else {
      echo "";
    }
  }

?>
<!DOCTYPE html>

  <!-- basic html page from Twitter Bootstrap -->
  <html lang="en">
    <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
      <title>The Wine Store | Search</title>

      <link href='http://fonts.googleapis.com/css?family=Lobster' rel='stylesheet' type='text/css'>
      <!-- Bootstrap -->
      <link href="css/bootstrap.css" rel="stylesheet">
      <!-- <link href="css/bootstrap-theme.css" rel="stylesheet"> -->
      <link href="css/winestore.css" rel="stylesheet">
      <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
      <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
      <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
      <![endif]-->
    </head>
    <body>
      <div class='container'>
        <div class='centre-panel row col-md-8 col-md-offset-2'>
            <header>
              <h1>The Wine Store</h1>       
            </header>

            <form class='form-horizontal' action='php/answer.php' method='GET'>
              <!-- INSTRUCTIONS -->
              <div class='col-xs-9 col-xs-offset-3'>
                <p class='form-control-static text-uppercase search-instructions'>enter your search terms</p>
              </div>

              <!-- WINE NAME -->
              <div class='form-group'>
                <label class='col-xs-3 control-label' for='winename'>Wine Name</label>
                <div class='col-xs-8'>
                  <input class='form-control' type='text' name='winename' id='winename' value='<?php fillValue('winename'); ?>'>
                </div>
              </div>

              <!-- WINERY NAME -->
              <div class='form-group'>
                <label class='col-xs-3 control-label' for='winery'>Winery Name</label>
                <div class='col-xs-8'>
                  <input class='form-control' type='text' name='winery' id='winery' value='<?php fillValue('winery'); ?>'>
                </div>
              </div>
              <?php
                require_once('php/config.php');
                require_once("php/dbconnect.php");

                /* REGION */
                echo "<div class='form-group'><label class='col-xs-3 control-label' for='region'>Region</label>\n";
                echo "<div class='col-xs-8'><select class='form-control' name='region' id='region'>\n";
                $sql_region = "SELECT region_name FROM region ORDER BY region_name";
                foreach ($dbconn->query($sql_region) as $row) {

                  if ((isset($_SESSION['region'])) && ($row['region_name'] == $_SESSION['region'])) {
                      echo "<option selected='selected' value='$row[region_name]'>$row[region_name]</option>\n";
                    } else {
                      echo "<option value='$row[region_name]'>$row[region_name]</option>\n";
                    }                   
                }
                echo "</select></div></div>";
                
                /* GRAPE */
                echo "<div class='form-group'><label class='col-xs-3 control-label' for='grape'>Grape Variety</label>\n";
                echo "<div class='col-xs-8'><select class='form-control' name='grape' id='grape'>\n";
                /* include default value */
                echo "<option value='Any' selected='selected'>Any</option>\n";
                $sql_grape = "SELECT variety FROM grape_variety ORDER BY variety";
                foreach ($dbconn->query($sql_grape) as $row) {
                  if ((isset($_SESSION['grape'])) && ($row['variety'] == $_SESSION['grape'])) {
                    echo "<option selected='selected' value=$row[variety]>$row[variety]</option>\n";
                  } else {
                    echo "<option value=$row[variety]>$row[variety]</option>\n";
                  }
                }
                echo "</select></div></div>";

                /* YEAR RANGE */
                echo "<div class='form-group'><label class='col-xs-3 control-label' for='minyear'>Years</label>\n";

                echo "<div class='col-xs-1'><p class='form-control-static'>from</p></div>\n";
                echo "<div class='col-xs-3'><select class='form-control input-sm' name='minyear' id='minyear'>\n";
                $sql_year_min = "SELECT DISTINCT year FROM wine ORDER BY year";
                foreach ($dbconn->query($sql_year_min) as $row) {
                  if ((isset($_SESSION['minyear'])) && ($row['year'] == $_SESSION['minyear'])){
                    echo "<option selected='selected' value=$row[year]>$row[year]</option>\n";
                  } else {
                    echo "<option value=$row[year]>$row[year]</option>\n";
                  }
                }
                echo "</select></div><div class='col-xs-1'><p class='form-control-static'>to</p></div>";
                echo "<div class='col-xs-3'><select class='form-control input-sm' name='maxyear'>\n";
                $sql_year_max = "SELECT DISTINCT year FROM wine ORDER BY year DESC";
                foreach ($dbconn->query($sql_year_max) as $row) {
                  if ((isset($_SESSION['maxyear'])) && ($row['year'] == $_SESSION['maxyear'])) {
                    echo "<option selected='selected' value=$row[year]>$row[year]</option>\n";
                  } else {
                    echo "<option value=$row[year]>$row[year]</option>\n";
                  }
                }
                echo "</select></div></div>";

                /* CLOSE DB CONNECTION */
                $dbconn = null;
              ?>
            
              <!-- WINES IN STOCK -->
              <div class='form-group'>
                <label class='col-xs-3 control-label' for='onhand'>Min Stock On Hand</label>
                <div class='col-xs-8'>
                  <input class='form-control' type='text' name='onhand' id='onhand' value='<?php fillValue('onhand'); ?>'>
                </div>
              </div>

              <!-- WINES ORDERED -->
              <div class='form-group'>
                <label class='col-xs-3 control-label' for='ordered'>Min Wines Ordered</label>
                <div class='col-xs-8'>
                  <input class='form-control' type='text' name='ordered' id='ordered' value='<?php fillValue('ordered'); ?>'>
                </div>
              </div>

              <!-- MIN and MAX COST -->
              <div class='form-group'>
                <label class='col-xs-3 control-label' for='mincost'>Cost</label>
                <div class='col-xs-1'>
                  <p class='form-control-static'>min</p>
                </div>
                <div class='col-xs-3'>
                  <input class='form-control' type='text' name='mincost' id='mincost' value='<?php fillValue('mincost'); ?>'>
                </div>
                <div class='col-xs-1'>
                  <p class='form-control-static'>max</p>
                </div>
                <div class='col-xs-3'>
                  <input class='form-control' type='text' name='maxcost' id='maxcost' value='<?php fillValue('maxcost'); ?>'>
                </div>
              </div>

              <!-- ACTION BUTTONS -->
              <a class='button btn btn-default btn-lg col-xs-2 col-xs-offset-2' href='php/clear.php'>Clear</a>
              <button id='show-wine' type='submit' class='btn btn-primary col-xs-5 col-xs-offset-1 btn-lg'>Show Wines</button>

          </form>
        </div>

      </div>
      <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>  
      <!-- <script src="../js/bootstrap.js"></script> -->
      <!-- Validated HTML5 -->
    </body>
  </html>




