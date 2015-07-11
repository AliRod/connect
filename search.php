<?php

  // session_start();
  // if (isset($_SESSION['winename'])) {
  //   echo 'the variable is set!';
  // }

  // function xssafe($data, $encoding='UTF-8')
  // {
  //    return htmlspecialchars($data, ENT_QUOTES | ENT_HTML5, $encoding);
  // }

  // function xecho($data)
  // {
  //    echo xssafe($data);
  // }

?>
<!DOCTYPE html>

<!-- ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
RMIT School of Computer Science and Information Technology 
CPT 375 Web Database Applications SP2 2015    
ASSIGNMENT 1   
Alexandra Margaret Rodley s3372356

search.php
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^ -->

<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Wine Store Search</title>

    <link href='http://fonts.googleapis.com/css?family=Lobster' rel='stylesheet' type='text/css'>
    <!-- Bootstrap -->
    <link href="css/bootstrap.css" rel="stylesheet">
    <link href="css/bootstrap-theme.css" rel="stylesheet">
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
            <h1><span class='fancy'>The Wine Store</span></h1>       
          </header>

          <form class='form-horizontal' action='php/answer.php' method='GET'>
          <div class='col-xs-9 col-xs-offset-3'>
            <p class='form-control-static text-uppercase search-instructions'>enter your search terms</p>
          </div>
          <!-- WINE NAME -->
          <div class='form-group'>
            <label class='col-xs-3 control-label' for='winename'>Wine Name</label>
            <div class='col-xs-8'>
              <input class='form-control' type='text' name='winename'>
            </div>
          </div>

          <!-- WINERY NAME -->
          <div class='form-group'>
            <label class='col-xs-3 control-label' for='winery'>Winery Name</label>
            <div class='col-xs-8'>
              <input class='form-control' type='text' name='winery'>
            </div>
          </div>
          <?php
            require_once('php/config.php');
            require_once("php/dbconnect.php");

            /* REGION */
            echo "<div class='form-group'><label class='col-xs-3 control-label' for='region'>Region</label>\n";
            echo "<div class='col-xs-8'><select class='form-control' name='region'>\n";
            $sql_region = "SELECT region_name FROM region ORDER BY region_name";
            foreach ($dbconn->query($sql_region) as $row) {
              echo "<option value=\"$row[region_name]\">$row[region_name]</option>\n";
            }
            echo "</select></div></div>";
            
            /* GRAPE */
            echo "<div class='form-group'><label class='col-xs-3 control-label' for='grape'>Grape Variety</label>\n";
            echo "<div class='col-xs-8'><select class='form-control' name='grape'>\n";
            /* include default value */
            echo "<option value='Any' selected='selected'>Any</option>\n";
            $sql_grape = "SELECT variety FROM grape_variety ORDER BY variety";
            foreach ($dbconn->query($sql_grape) as $row) {
              echo "<option value=$row[variety]>$row[variety]</option>\n";
            }
            echo "</select></div></div>";

            /* YEAR RANGE */
            echo "<div class='form-group'><label class='col-xs-3 control-label' for='minyear'>Years</label>\n";

            echo "<div class='col-xs-1'><p class='form-control-static'>from</p></div>\n";
            echo "<div class='col-xs-3'><select class='form-control input-sm' name='minyear'>\n";
            $sql_year_min = "SELECT DISTINCT year FROM wine ORDER BY year";
            foreach ($dbconn->query($sql_year_min) as $row) {
              echo "<option value=$row[year]>$row[year]</option>\n";
            }
            echo "</select></div><div class='col-xs-1'><p class='form-control-static'>to</p></div>";
            echo "<div class='col-xs-3'><select class='form-control input-sm' name='maxyear'>\n";
            $sql_year_max = "SELECT DISTINCT year FROM wine ORDER BY year DESC";
            foreach ($dbconn->query($sql_year_max) as $row) {
              echo "<option value=$row[year]>$row[year]</option>\n";
            }
            echo "</select></div></div>";

            /* CLOSE DB CONNECTION */
            $dbconn = null;
          ?>
          
          <!-- WINES IN STOCK -->
          <div class='form-group'>
            <label class='col-xs-3 control-label' for='onhand'>Min Stock On Hand</label>
            <div class='col-xs-8'>
              <input class='form-control' type='text' name='onhand'>
            </div>
          </div>

          <!-- WINES ORDERED -->
          <div class='form-group'>
            <label class='col-xs-3 control-label' for='ordered'>Min Wines Ordered</label>
            <div class='col-xs-8'>
              <input class='form-control' type='text' name='ordered'>
            </div>
          </div>

          <!-- MIN and MAX COST -->
          <div class='form-group'>
            <label class='col-xs-3 control-label' for='mincost'>Cost</label>
            <div class='col-xs-1'>
              <p class='form-control-static'>min</p>
            </div>
            <div class='col-xs-3'>
              <input class='form-control' type='text' name='mincost'>
            </div>
            <div class='col-xs-1'>
              <p class='form-control-static'>max</p>
            </div>
            <div class='col-xs-3'>
              <input class='form-control' type='text' name='maxcost'>
            </div>
          </div>

          <button id='clear-form' type='button' class='btn btn-default btn-lg col-xs-2 col-xs-offset-2'>Clear</button>

          <button id='show-wine' type='submit' class='btn btn-primary col-xs-5 col-xs-offset-1 btn-lg'>Show Wines</button>

        </form>
      </div>

    </div>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    
    <script src="../js/bootstrap.js"></script>
    <script src="../js/winestore.js"></script>
  </body>
</html>




