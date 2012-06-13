<?php
// The POST variable in which Github/Gitlab stores the JSON data
$post_var_name = 'payload';

// The folder that contains the scripts that will be run
$hooks_script_root = './hooks';

// All information will be logged here
$log_file = './githooks.log';

// Configuration for users that will be allowed
$authorized_users = array(
        array(
                'name' => 'gitlab',
                'key' => '5aef35982fb2d34e9d9d4502f6ede1072793222d',
                'ip_addresses' => array('*'),
        ),
);

// Enable for more verbose logging and client site logging
$debug =TRUE;

?>
