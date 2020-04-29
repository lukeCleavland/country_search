<?php
class Api {
    
    /**
    * Used to specifiy endpoint for api
    * @var string
    */
    public $url;
    
    /**
   * For basic connection to an API
   * 
   * @return string $outpout
   * 
   */
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