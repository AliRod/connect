<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Winestore Search</title>

    <!-- Bootstrap -->
    <link href="../css/bootstrap.css" rel="stylesheet">
    <link href="../css/bootstrap-theme.css" rel="stylesheet">
    <link href="../css/winestore.css" rel="stylesheet">
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

  </head>

  <body>
    <h1>Welcome to the Winestore!</h1>
    <h2>Please enter your search terms</h2>

    <div class='search-form'>
      <form class='col-sm-offset col-sm-10' action='answer.php' method='GET'>

        <!-- WINE NAME -->
        <div class='form-group'>
          <label for='winename'>Wine name:</label>
          <input type='text' name='winename'><br>
        </div>

        <!-- WINERY NAME -->
        <div class='form-group'>
          <label for='winery'>Winery name:</label>
          <input type='text' name='winery'><br>
        </div>
        <?php
          require_once("php/dbconnect.php");

          /* REGION */
          echo "<div class='form-group'><label for='region'>Region:</label>\n<select name='region'>\n";
          $sql_region = "SELECT region_name FROM region ORDER BY region_name";
          foreach ($dbconn->query($sql_region) as $row) {
            echo "<option value=\"$row[region_name]\">$row[region_name]</option>\n";
          }
          echo "</select></div>";
          
          /* GRAPE */
          echo "<div class='form-group'><label for='grape'>Grape Variety:</label>\n<select name='grape'>\n";
          /* include default value */
          echo "<option value='Any' selected='selected'>Any</option>\n";
          $sql_grape = "SELECT variety FROM grape_variety ORDER BY variety";
          foreach ($dbconn->query($sql_grape) as $row) {
            echo "<option value=$row[variety]>$row[variety]</option>\n";
          }
          echo "</select></div>";

          /* YEAR RANGE */
          echo "<div class='form-group'><label for='minyear'>Years of Production: </label>\n";

          echo "<span>&nbsp;from&nbsp;</span><select name='minyear'>\n";
          $sql_year_min = "SELECT DISTINCT year FROM wine ORDER BY year";
          foreach ($dbconn->query($sql_year_min) as $row) {
            echo "<option value=$row[year]>$row[year]</option>\n";
          }
          echo "</select><span>&nbsp;to&nbsp;</span>";

          echo "<select name='maxyear'>\n";
          $sql_year_max = "SELECT DISTINCT year FROM wine ORDER BY year DESC";
          foreach ($dbconn->query($sql_year_max) as $row) {
            echo "<option value=$row[year]>$row[year]</option>\n";
          }
          echo "</select></div>";

          /* CLOSE DB CONNECTION */
          $dbconn = null;
        ?>
        
        <!-- WINES IN STOCK -->
        <div class='form-group'>
          <label for='onhand'>Minimum Stock On Hand:</label>
          <input type='text' name='onhand'><br>
        </div>

        <!-- WINES ORDERED -->
        <div class='form-group'>
          <label for='ordered'>Minimum Wines Ordered:</label>
          <input type='text' name='ordered'><br>
        </div>

        <!-- ONE OR OTHER OF THESE WILL BE DISABLED WITH JS LATER -->
        <!-- MIN COST -->
        <div class='form-group'>
          <label for='mincost'>Minimum Cost:</label>
          <input type='text' name='mincost'><br>
        </div>
        
        <!-- MAX COST -->
        <div class='form-group'>
          <label for='maxcost'>Maximum Cost:</label>
          <input type='text' name='maxcost'><br>
        </div>

        <br>  
        <button type='submit' class='btn btn-primary'>Show Wines</button>
      </form>

    </div>


    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="../js/bootstrap.js"></script>
  </body>
</html>