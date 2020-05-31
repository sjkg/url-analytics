<?php
require_once(dirname(__FILE__) . '/config/contants.php');

class SiteTraffic {
    protected $domain;
    protected $conn;
    public $urlDetails;
    function __construct($conn,$urlDetails) {
      $this->conn = $conn;
      $this->domain = $urlDetails['domain'];
      $this->urlDetails = $urlDetails;  
    }

    // Domain name to submit
   // $domain = "google.com";

    // Register for an API key here https://app.sitetrafficapi.com/register/
    // $site_traffic_result = siteTrafficCurl($domain);
    // siteTrafficCurlResult($site_traffic_result,$domain);
    
    public function siteTrafficCurl(){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, _SITE_TRAFFIC_URL.trim(_WEB_API_KEY)."&host=".$this->domain);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15); 
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $result = curl_exec($ch);
        curl_close($ch);
        $json_site_traffic_result = json_decode($result, true);
        if(!isset($json_site_traffic_result['error'])){
            $site_trafic = $this->siteTrafficCurlResult($json_site_traffic_result,$this->domain);
            $siteDetails = array_merge($this->urlDetails, $site_trafic);
            return $siteDetails;
        }else{
            $site_trafic['traffic'] = "";
            $site_trafic['api_error'] = "API Error: ".htmlspecialchars($json_site_traffic_result['error'])."<br />";
            $siteDetails = array_merge($this->urlDetails, $site_trafic);
            return $siteDetails;
        }
    }
    public function siteTrafficCurlResult($result,$domain){
        $site_traffic_result['traffic'] =  "Daily Pageviews: ".htmlspecialchars($result['data']['estimations']['pageviews']['daily'])."<br />"
            ."Daily Unique Visitors: ".htmlspecialchars($result['data']['estimations']['visitors']['daily'])."<br />"
            ."Monthly Pageviews: ".htmlspecialchars($result['data']['estimations']['pageviews']['monthly'])."<br />"
            ."Monthly Unique Visitors: ".htmlspecialchars($result['data']['estimations']['visitors']['monthly'])."<br />"
            ."Yearly Pageviews: ".htmlspecialchars($result['data']['estimations']['pageviews']['yearly'])."<br />"
            ."Yearly Unique Visitors: ".htmlspecialchars($result['data']['estimations']['visitors']['yearly'])."<br />";
        $site_traffic_result['api_error'] = "";
            // echo  "<code>".json_encode($site_traffic_result)."</code>";
        return $site_traffic_result;
    }
}
?>