vcl 4.1;

# Symfony Backend
backend symfony {
    .host = "host.docker.internal"; # host in docker
    .port = "8083";
}

sub vcl_recv {
    # Permet de passer tout en cacheable
    if (req.method == "PURGE") {
        return (purge);
    }
}

sub vcl_backend_response {
    # Si le backend ne renvoie pas TTL, par défaut 1 seconde
    if (beresp.ttl <= 0s) {
        set beresp.ttl = 1s;
    }
}

sub vcl_deliver {
    # debug header in dev
    set resp.http.X-Dev-Varnish = "yes";
    if (obj.hits > 0) {
        set resp.http.X-Cache = "HIT";
    } else {
        set resp.http.X-Cache = "MISS";
    }
}
