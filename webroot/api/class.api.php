<?php
class Api {
    
    public $url;
    
    public function call_api(){
        $ch = curl_init($this->url);
        
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

       $output = curl_exec($ch);
        if(curl_error($ch)) {
            $output = array('error'=>curl_error($ch));
        }

        curl_close($ch);
        return $output;
 }
}


?>