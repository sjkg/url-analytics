<?php

class SiteShorten {
    protected $conn;
    protected $timestamp;

    function __construct($conn,$long_url) {
      $this->conn = $conn;
      $this->timestamp = date('Y-m-d H:i:s');
      $this->urlToShortCode($long_url);      
    }
    public function urlToShortCode($url) {
        if (empty($url)) {
            throw new Exception("No URL was supplied.");
        }
        
        if ($this->validateUrlFormat($url) == false) {
            throw new Exception(
                "URL does not have a valid format.");
        }
        $check_url_existance = $this->urlExistsInDb($url);
        $shortCode = $check_url_existance['short_code'];
        $id = $check_url_existance['id'];
        if ($shortCode == false) {
            $shortCode = $this->createShortCode($url);
        }else{
            $map_url = $this->shortUrlMapping($id);
        }

    }
    
    protected function validateUrlFormat($url) {
        $pattern = "/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i";
        if ( preg_match($pattern, $url) ) {
            return true;
        } else {
            echo false;
        }
    }
    public function urlExistsInDb($url) {
        $long_url = base64_encode($url);
        $query = "SELECT * FROM short_urls WHERE long_url = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $long_url);
        $stmt->execute();
        $result = $stmt->get_result();
        if($row_count = $result->num_rows >= 1){
            $result_array = $result->fetch_assoc();
            $result_array['decode_long_url'] = $url;
            return $result_array;
        }else{
            return false;
        }
    }

    protected function createShortCode($url) {
        $id = $this->insertUrlInDb($url);
        $shortCode = $this->convertIntToShortCode($id);
        $this->insertShortCodeInDb($id, $shortCode);
        return $shortCode;
    }

    protected function insertUrlInDb($url) {
        $domain = $this->domainFromUrl($url);
        $long_url = base64_encode($url);
        $query = "INSERT INTO short_urls (domain,long_url, date_created) VALUES (?, ?, ?)";
        $stmnt = $this->conn->prepare($query);
        $stmnt->bind_param("sss",
                                $domain,
                                $long_url,
                                $this->timestamp
                            );
        if ($stmnt->execute()) {
            return $this->conn->insert_id;
        }else{
            $shortCode = $this->urlExistsInDb($url);
            if ($shortCode == false) {
                $shortCode = $this->createShortCode($url);
            }else{
                return $shortCode;
            }            
        }
    }
    public function domainFromUrl($url) {
        $domain = str_ireplace('www.', '', parse_url($url));
        $parseUrl = parse_url(trim($url)); 
        if(isset($parseUrl['host']))
        {
            $host = $parseUrl['host'];
            $domain = str_ireplace('www.', '', $host);
        }
        else
        {
                $path = explode('/', $parseUrl['path']);
                $host = $path[0];
                $domain = str_ireplace('www.', '', trim($host));
        }
        return $domain;
    }
    protected function convertIntToShortCode($id) {
        $id = intval($id);
        if ($id < 1) {
            throw new Exception(
                "The ID is not a valid integer");
        }
        $token = substr(base64_encode(uniqid(rand(), true)),0,6); // creates a 6 digit unique short id
        
        return $token;
    }

    protected function insertShortCodeInDb($id, $code) {
        if ($id == null || $code == null) {
            throw new Exception("Input parameter(s) invalid.");
        }
        $query = "UPDATE short_urls SET short_code = ? WHERE id = ?";
        $stmnt = $this->conn->prepare($query);
        $stmnt->bind_param(
            "ss",
            $code,
            $id
        );
        $stmnt->execute();        
        if ($stmnt->affected_rows < 1) {
            $shortCode = $this->convertIntToShortCode($id);
            $this->insertShortCodeInDb($id, $shortCode);
        }else{
            $stmnt->close();
            $map_url = $this->shortUrlMapping($id);
        }
        
    }
    public function shortUrlMapping($id){
        $query = "SELECT * FROM short_urls WHERE id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if($row_count = $result->num_rows >= 1){
            $result_array = $result->fetch_assoc();
            $long_url = base64_decode($result_array['long_url']);
            $result_array['decode_long_url'] = $long_url;
           // echo "<code>". json_encode($result_array). "</code>";
            return $result_array;
        }else{
            return "Some problem occured.";
        }
    }
  }
