<?php
class Country {
    
    /**
     * Used to declare desired fields retrieved from rest countries api
     * @var array
     */
    private $data_fields = array('name', 'flag', 'alpha2Code', 'alpha3Code', 'region', 'subregion', 'population', 'languages');
    
      /**
     * Used to connect the Api class to Country
     * @var class
     */
    private $api;
    
    /**
     *the query term submitted by user
     * @var string
     */
    private $search;
    
    private $url = "https://restcountries.eu/rest/v2";
    
    public function __construct($api, $endpoint, $search = NULL){
        $this->api = $api;
        $this->endpoint = $endpoint;
        $this->search = $search;
    }
    
    
    /**
     * Create different endpoint path for customer select search parameters
     * 
     * @param string $endpoint_name
     * @return string $endpoint
     * 
     */
    private function build_endpoint($endpoint_name){
        //create string from data_fields array and add to url 
        $field_string = implode(';',$this->data_fields);
        $filter = '?fields='.$field_string;
        switch ($endpoint_name) {
            case 'name':
                $endpoint = "/name/$this->search$filter";
                break;
            case 'full_name':
                $endpoint = "/name/$this->search$filter&fullText=true";
                break;
            case 'code':
                $endpoint = "/alpha/$this->search$filter";
                break;
        }
        return $endpoint;
    }
    
    /**
     * 
     * @param array $array should be decoded from json
     * @return array
     * 
     */   
    private function prep_sort_data($array){
       
            //give array an index if doesn't have one so functions work on all types of endpoints
            if(!isset($array[0])){
                $array =  array($array);
            }
            
             //sort results by population, descending
            usort($array, function($a, $b) {        
                return $b['population'] <=> $a['population'];  
            });

        return $array;
    }
    
    
    /**
     * Generate counts for results and query term instances
     * 
     * @param array $array should be decoded from json
     * @return array $array multi array with different stats
     * 
     */
    private function search_stats($array){

        $count = 0;
        $appearances = 0;
        $region_list = array();
        
        foreach($array as $value){
                 
            foreach($value as $key => $v){
                
                //compare query term with value to see if value contains it.
                if(!is_array($v)){
                    if(strpos(strtolower($v), strtolower($this->search)) !== false){
                        $appearances++;
                    }
                }
                
                //generate array of regions and subregions
                if($key == 'region' || $key == 'subregion'){
                    if(!in_array($v, $region_list)){
                        $region_list[] = $v;
                    }
                }
            }
            $count++;
        }
        $region_string = implode(", ", $region_list);
        return array('count'=>$count, 'appearances'=>$appearances, 'region_list'=>$region_list);
    }
    
    /**
     * Call api and prep data for json retrieval
     * 
     * @return array $results
     * 
     */
    public function data(){
        $data = $this->api;
        $endpoint = self::build_endpoint($this->endpoint);
        $data->url = $this->url.$endpoint;
        $countries = $data->call_api();
        $stats = NULL;
        $results = NULL;
        //convert data to array to perform checks
        $countries = json_decode($countries,true);
        
        //if status then there was a problem so no need to build array
        if(isset($countries['status'])){
            $results = $countries;
        }else{
            $countries = self::prep_sort_data($countries);

            if(is_array($countries)){
                $stats = self::search_stats($countries);  
            }
                $results = array('info'=>$countries, 'stats'=>$stats);
        }

        return $results;
    }
}

?>