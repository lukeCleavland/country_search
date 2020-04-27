<?php
class Country {
    private $data_fields = array('name', 'flag', 'alpha2Code', 'alpha3Code', 'region', 'subregion', 'population', 'languages');
   protected $api;
   protected $search;
   protected $url = "https://restcountries.eu/rest/v2";
    
    public function __construct($api, $endpoint, $search = NULL){
        $this->api = $api;
        $this->endpoint = $endpoint;
        $this->search = $search;
    }
    
    private function build_endpoint($endpoint_name){
        switch ($endpoint_name) {
            case 'name':
                $endpoint = "/name/$this->search";
                break;
            case 'full_name':
                $endpoint = "/name/$this->search?fullText=true";
                break;
            case 'code':
                $endpoint = "/alpha/$this->search";
                break;
        }
        return $endpoint;
    }
    
    private function filter_sort_data($array){
       
            //give array an index if doesn't have one so functions work on all types of endpoints
            if(!isset($array[0])){
                $array =  array($array);
            }
            
            usort($array, function($a, $b) {        
                return $b['population'] <=> $a['population'];  
            });

        
         foreach($array as  $countries){
            foreach($countries as $key => $value){
                if(in_array($key,$this->data_fields)){
                    $result[$key] = $value;
                }
            }
            $results[] = $result;
         }
   
        return $results;
    }
    
    
    private function search_stats($array){

        $count = 0;
        $appearances = 0;
        $regions = 0;
        $subregions = 0;
        $region_list = array();
        foreach($array as $value){
                 
            foreach($value as $key => $v){
                if(!is_array($v)){
                    if(strpos($v, $this->search) !== false){
                        $appearances++;
                    }
                }
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
    
    public function data(){
        $data = $this->api;
        $endpoint = self::build_endpoint($this->endpoint);
        $data->url = $this->url.$endpoint;

        
        $countries = $data->call_api();
        $countries = json_decode($countries,true);
        if(isset($countries['status'])){
            $results = $countries;
        }else{
            $countries = self::filter_sort_data($countries);
            $stats = NULL;
            if(is_array($countries)){
                $stats = self::search_stats($countries);  
            }
                $results = array('info'=>$countries, 'stats'=>$stats);
        }

        return $results;
    }
}

?>