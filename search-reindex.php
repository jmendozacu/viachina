<?php
require_once 'app/Mage.php';
umask( 0 );
Mage :: app( "default" );
Mage::log("Started Rebuilding Search Index At: " . date("d/m/y h:i:s"));
$sql = "truncate catalogsearch_fulltext;";
$mysqli = Mage::getSingleton('core/resource')->getConnection('core_write');
$mysqli->query($sql);
$process = Mage::getModel('index/process')->load(7);
$process->reindexAll();
Mage::log("Finished Rebuilding Search Index At: " . date("d/m/y h:i:s")); 
?>