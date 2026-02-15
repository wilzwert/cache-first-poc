vcl 4.1;

# Symfony Backend
backend symfony {
    .host = "host.docker.internal"; # host in docker
    .port = "8083";
}

sub vcl_recv {
    unset req.http.x-cache;

    if (req.method == "PURGE") {
        return (purge);
    }

    if (req.method != "GET" && req.method != "HEAD") {
        return (pass);
    }

    # mandatory with ETag
    unset req.http.If-None-Match;
    unset req.http.If-Modified-Since;

    return (hash);
}

sub vcl_hit {
	set req.http.x-cache = "hit";
}

sub vcl_miss {
	set req.http.x-cache = "miss";
}

sub vcl_pass {
	set req.http.x-cache = "pass";
}

sub vcl_backend_response {
    # default ttl if not send
    if (beresp.ttl <= 0s) {
        set beresp.ttl = 1s;
    }
}

sub vcl_deliver {
    if (obj.uncacheable) {
        set req.http.x-cache = req.http.x-cache + " uncacheable" ;
    } else {
        set req.http.x-cache = req.http.x-cache + " cached" ;
    }
    # to show the information in the response
    set resp.http.x-cache = req.http.x-cache;
    set resp.http.x-varnish-ttl = obj.ttl;
    set resp.http.x-varnish-age = obj.age;
}
