githook_endpoint
================

Endpoint for post-receive requests from GitHub/GitLab which replaces githooks

The security for this is fairly basic, there can be many "users" like a Github accont or repo.
That "user" has a key that both identifies them and authorizes them to hit the endpoint.
The key is put in the URL as a git parameter identified by the letter "k".
An example URL:
https://localhost/githooks/?k=5aef35982fb2d356789d4502f6ede1072793222d

Also, each user has a list of ip addresses that they can access the endpoint from or you can put in a "*" to let them access it from anywhere.
The IP address whitelist combined with SSL use to keep sniffers from seeing the key in the URL should be good enough security for this type of application.  If you have suggestions for better security let me know.