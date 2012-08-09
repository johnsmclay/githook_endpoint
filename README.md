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

The user definition is in the config.php file and like like this:
```php
# one user in the array of users
array(
        # the name of the user - this is only used in the logging
        'name' => 'gitlab', 
        # the key this user should put in their URL
        'key' => '5aef35982fb2d34e9d9d4502f6ede1072793222d', 
        # the ip addresses this user is authorized to connect from
        'ip_addresses' => array('10.9.8.7','172.6.5.4'), 
),
```

When a user successfully connects and sends the post-recieve data the PHP calls a bash script called "script_runner.sh" and includes some variables like:
* $REPOSITORY -- the name of the repo the recieve was for
* $BRANCH -- the name of the branch the commit was for
* $USER -- the full name of the user that committed
* $MESSAGE -- the commit message
* $COMMITID -- the ID of the commit so you can checkout that specific revision if needed

Within "script_runner.sh" you have access to all these variables, so you can either do something like call another script located in hooks/$REPOSITORY/$BRANCH.sh or do if statements looking for commits to specific repo/branches or whatever you want.