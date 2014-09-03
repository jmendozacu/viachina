function setLocation(url){
     if (window.location.protocol == "https:") {
                
        url=url.replace("http://","https://");  
    }

    window.location.href = url;
}