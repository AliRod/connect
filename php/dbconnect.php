  <?php

  require_once('db.php');

  try {
      $dbconn = new PDO(DB_NAME, DB_USER, DB_PW);
      $dbconn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      /* DO THIS OR NOT? I'M CONFUSED :( */
      $dbconn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

  } catch (PDOException $e) {
      print "Connection Error!: " . $e->getMessage() . "<br>";
      die();
  }

  ?>