<!-- ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
RMIT School of Computer Science and Information Technology 
CPT 375 Web Database Applications SP2 2015    
ASSIGNMENT 1   
Alexandra Margaret Rodley s3372356

error.php
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^ -->

<?php

  require_once('php/config.php');

  fillTemplate($_GET['msg']);

  function fillTemplate($msg) {

    require_once ("php/MiniTemplator.class.php");

    $t = new MiniTemplator;

    $ok = $t->readTemplateFromFile ("html/errorpage_template.htm");
    if (!$ok) die ("MiniTemplator.readTemplateFromFile failed.");

    $t->setVariable ("message", $msg);
    $t->addBlock ("errormessages");

    $t->generateOutput();

  }


?>