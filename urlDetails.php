<?php
require(dirname(__FILE__) . '/config/config.php');
require(dirname(__FILE__) . '/siteShorten.php');
require(dirname(__FILE__) . '/siteTraffic.php');

if($_POST['action'] == 'urlShorten'){
$long_url = $_POST['long_url'];
$shortUrl = new SiteShorten($conn,$long_url);
$urlDetails = $shortUrl->urlExistsInDb($long_url);
$siteTraffic = new SiteTraffic($conn,$urlDetails);
$siteDetails = $siteTraffic->siteTrafficCurl();
echo json_encode($siteDetails);
}
