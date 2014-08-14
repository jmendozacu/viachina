<?php
Mage::app();
$geoIP = Mage::getSingleton('geoip/country');
$country = $geoIP->getCountry();

if(strcmp($country,'NL') == 0) {
    $mageRunType = 'store';
    $mageRunCode = 'nl';
}
else if(strcmp($country,'GB') == 0) {
    $mageRunType = 'store';
    $mageRunCode = 'en';
}
else if(strcmp($country,'FR') == 0) {
    $mageRunType = 'store';
    $mageRunCode = 'fr';
}
else if(strcmp($country,'DE') == 0) {
    $mageRunType = 'store';
    $mageRunCode = 'de';
}
else
{
    $mageRunType = 'store';
    $mageRunCode = 'eu';
}
Mage::reset();
?>