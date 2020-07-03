#A PHP Proxy to bypass CORS

This is a simple PHP proxy that forwards incoming request to different server and returns
the response. The proxy is powered by `GuzzleHttp` library. The main use of this library
is to bypass any CORS policy restrictions you may encounter while developing JavaScript
application.

This proxy is tested for `GET`, `POST` and `PUT` requests. However you can't do any file
uploads using this proxy because I haven't implemented it. I could but I didn't.

###What is CORS?

CORS stands for Cross-Origin-Resource-Sharing meaning that it is a mechanism that allows
restricted resources on a web page to be requested from another domain outside the domain
from which the first resource was served.

For example: any request going from site A to site B may not be executed by browser itself
when B does not have `Access-Control-Allow-Origin` header.

###Installation

1. Download or clone the repository.
2. Run `./composer.phar install` to install required packages.

###Usage

To initiate a cross domain request you have to provide destination URL in `X-PHP-Url` 
header on your request to the this proxy. Proxy forwards your request without modifying
anything to  destination address and returns the response back to you.

#####Using jQuery

    const destination_url = "https://www.google.com/";
    
    $.ajax({
        url: 'http://www.yourdomain.com/proxy/index.php',
        headers: {
            "X-PHP-Url": destination_url
            // ..
        },
        success: function() { } 
        error: function() { }
    });
    
#####Using Angular $http

    const destination_url = "https://www.google.com/";
    
    $http({
        url: 'http://www.yourdomain.com/proxy/index.php',
        headers: {
            "X-PHP-Url": destination_url
            // ..
        }
    }).then(successCallback, errorCallback)


#####curl

    curl -v -H "X-PHP-Url: http://www.google.com" http://www.yourdomain.com/proxy/index.php
