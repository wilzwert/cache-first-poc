# CacheFirstPoc

For an app I intend to create in a few weeks, I have a few technical requirements :
1. avoid the complexity and overhead of a frontend lib or framework (React, Angular)
2.  be able to change my mind about 1. later ; this probably implies building and consuming a backend API in some way
3. keep things as simple as possible, because the app itself will be quite simpe
4. reduce bandwith usage and computation as much as possible
5. learn new things (of course)

As for the app in itself, it will look like :
- one type of resource displayed (article or something like that)
- a resource in itself should not be updated, or only very rarely
- each resource has stats, which are to be displayed on the public resource's page, and may of course change often  

It seems it's a good use case to make use of caching.

This POC aims to show how to fulfill these requirements by :
1. Using Symfony to provide the backend API as well as the frontend, with twig as the main HTML engine.
That way we keep it simple, everything in one place, and almost plain old HTML / CSS / JS
2. A "resource" is rendered in the twig template
3. Stats are loaded asynchronously with JavaScript (in this case, with Symfony UX / Stimulus)
4. Set cache properties and headers both in API and frontend Controllers, with values allowing long-lived cache
5. Frontend controllers are clients themselves of the API ; as the app will be run in docker containers, the overhead will be minimum.
Plus, the caching strategy aims to minimize data reloading.
6. Both the API and the Frontend will be behind Varnish as a cache and reverse proxy

In the dev docker conf provided, headers are added by Varnish to check hits/misses.

The Symfony app in itself is very basic at the moment, with in memory repos (with arrays).
The benefit of the caching strategy was thus not really obvious at first, so I added some artificial 
and random latency when accessing repositories (i.e. when no cache available, data must be fetched to generate pages) to demonstrate how caching improves things drastically.



