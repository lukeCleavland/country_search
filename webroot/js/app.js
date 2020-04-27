function get_countries() {
    $val = $('input[name=country]').val();
    $type = $('input[name=type]:checked').val();
    $.ajax({
    method: "POST",
        url: "http://localhost:8765/api/index.php",
        dataType: 'JSON',
        data: { endpoint: $type, search: $val }
    })
    .done(function( data ) {
        var $output = '';
            if (data.status) {
               $output = data.message;
            }else{
               if (!data.info[0]) {
                   $output = country_template(data.info, null);
               }else{
           
                   for (i=0;i<data.info.length; i++) {
                       $output += country_template(data.info, i);
                   }
               }
            }
            
        var $stats ='';  
            if (data.stats) {
                $stats = 'Results:'+data.stats.count+'<br>Regions and Subregions: '+data.stats.region_list+'<br> # of Appearances: '+data.stats.appearances+' (includes appearances in fields not shown)';
            }
        
    $("#results").html($output);
    $("#details").html($stats);
  });
}

function country_template(json,index) {
   
    string = flag = name = region = subregion = population = alpha2Code = alpha3Code = languageString = '';
    
    if (index != null) {
        json = json[index];
    }
    
    if (json.flag) {
      flag ='<img src="'+json.flag+'" width="300px"/>';
    }
    
    if (json.name) {
      name ='<p>'+json.name+'</p>';
    }
    
    if (json.region) {
        region = '<p>'+json.region+'</p>';
    }
    
    if (json.subregion) {
        subregion = '<p>'+json.subregion+'</p>';
    }
    
    if (json.population) {
        population = '<p>'+json.population+'</p>';
    }
    
    if (json.alpha2Code) {
        alpha2Code = '<p>'+json.alpha2Code+'</p>';
    }
    
    if (json.alpha3Code) {
        alpha3Code = '<p>'+json.alpha3Code+'</p>';
    }
    
    if (json.languages) {
        for (j=0;j<json.languages.length;j++) {
            if (json.languages[j].name) {
                languageString += '<p>'+json.languages[j].name+'</p>';
            }
        }
    }
    
    string += flag+name+region+subregion+population+alpha2Code+alpha3Code+languageString;

    return string;
}

window.addEventListener('load',function(){
    countrySearchForm = document.querySelector("#country_search");
    countrySearchForm.addEventListener('submit',function(e){
        e.preventDefault();
        get_countries();
    });
});
