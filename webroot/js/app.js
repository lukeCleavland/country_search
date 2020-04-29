window.addEventListener('load',function(){
    countrySearchForm = document.querySelector("#country_search");
    countrySearchForm.addEventListener('submit',function(e){
        e.preventDefault();
        get_countries();
    });
});


function get_countries(){
    
    var val = document.querySelector('input[name=country]').value;
    var type = document.querySelector('input[name=type]:checked').value;
    var params = 'endpoint='+type+'&search='+val;
    var output = '';
    var stats ='';  
    var xhr = new XMLHttpRequest();

    xhr.onload = function () {
    
        if (xhr.status >= 200 && xhr.status < 300) {

            var data = JSON.parse(xhr.responseText);
  
            //set error messages
            if (data.status) {
                if (data.status == 400) {
                    message = 'You did not enter a proper country code, please use a 2 or 3 letter code.';
                }else if(data.status == 404){
                    message = "No data was found. Try using the 'name' parameter for a general search.";
                }else{
                    message = data.message;
                }
                output = '<div class="error">'+message+'</div>';
            }else{
            
            //build output string with json data
                for (i=0;i<data.info.length; i++) {
                    output += country_template(data.info, i);
                }
                
            }
            
            //build stats string
            if (data.stats) {
                stats = "<p><em>results: </em>"+data.stats.count+"<br><em>regions and subregions: </em>"+data.stats.region_list+"<br> <em># of appearances (in displayed fields): </em>"+data.stats.appearances+"</p>";
            }
        
            document.querySelector("#results").innerHTML = output;
            document.querySelector("#details").innerHTML = stats.toString();
            
        } else {
            output = "The request failed. Pleas check your connection.";
        }
    
    };
    
    xhr.open('POST', 'http://localhost:8765/api/index.php');
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.send(params);

}

function country_template(json,index) {
   
    string = flag = name = region = subregion = population = alpha2Code = alpha3Code = languageString = '';
    
    if (index != null) {
        json = json[index];
    }
    
    if (json.flag) {
      flag ='<img src="'+json.flag+'"/>';
    }
    
    if (json.name) {
      name ='<p class="name">'+json.name+'</p>';
    }
    
    if (json.region) {
        region = '<p>Region: '+json.region+'</p>';
    }
    
    if (json.subregion) {
        subregion = '<p>Subregion: '+json.subregion+'</p>';
    }
    
    if (json.population) {
        population = '<p>Population: '+json.population+'</p>';
    }
    
    if (json.alpha2Code) {
        alpha2Code = '<p>Country Code (Alpha-2): '+json.alpha2Code+'</p>';
    }
    
    if (json.alpha3Code) {
        alpha3Code = '<p>Country Code (Alpha-3): '+json.alpha3Code+'</p>';
    }
    
    if (json.languages) {
        for (j=0;j<json.languages.length;j++) {
            if (json.languages[j].name) {
                languageString += '<li>'+json.languages[j].name+'</li>';
            }
        }
        languageString = 'Languages: <ul>'+languageString+'</ul>';
    }
    
    string += '<div class="country">'+name+'<div class="info_wrapper"><div class="flag">'+flag+'</div><div class="details">'+region+subregion+population+alpha2Code+alpha3Code+languageString+'</div></div></div>';

    return string;
}
