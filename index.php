<?php

require_once('./config.php');

/*
#####################
## EXAMPLE PAYLOAD ##
#####################
stdClass Object
(
    [before] => 5aef35982fb2d34e9d9d4502f6ede1072793222d
    [user_id] => 4
    [user_name] => John Smith
    [repository] => stdClass Object
        (
            [url] => http://github.com/defunkt/github
            [name] => github
            [description] => You're lookin' at it.
            [watchers] => 5
            [forks] => 2
            [private] => 1
            [owner] => stdClass Object
                (
                    [email] => chris@ozmm.org
                    [name] => defunkt
                )

        )

    [commits] => Array
        (
            [0] => stdClass Object
                (
                    [id] => 41a212ee83ca127e3c8cf465891ab7216a705f59
                    [url] => http://github.com/defunkt/github/commit/41a212ee83ca127e3c8cf465891ab7216a705f59
                    [author] => stdClass Object
                        (
                            [email] => chris@ozmm.org
                            [name] => Chris Wanstrath
                        )

                    [message] => okay i give in
                    [timestamp] => 2008-02-15T14:57:17-08:00
                    [added] => Array
                        (
                            [0] => filepath.rb
                        )

                )

            [1] => stdClass Object
                (
                    [id] => de8251ff97ee194a289832576287d6f8ad74e3d0
                    [url] => http://github.com/defunkt/github/commit/de8251ff97ee194a289832576287d6f8ad74e3d0
                    [author] => stdClass Object
                        (
                            [email] => chris@ozmm.org
                            [name] => Chris Wanstrath
                        )

                    [message] => update pricing a tad
                    [timestamp] => 2008-02-15T14:36:34-08:00
                )

        )

    [after] => de8251ff97ee194a289832576287d6f8ad74e3d0
    [ref] => refs/heads/master
)

*/

$request_identifier = rand(1000000,9999999);
function log_message($message='')
{
	global $log_file, $request_identifier, $debug;
	
	// add date and newline
	$message = $request_identifier.' '.$_SERVER['REMOTE_ADDR'].' @ '.date("Y-m-d H:i:s").' : '.$message;
	
	if($debug)
	{
		// Tell requestor
        	echo $message.'<br />'."\n";
	}

	// Tell Logs
	file_put_contents($log_file, $message."\n", FILE_APPEND);
}

if($debug)
{
	log_message("_POST obj: ".print_r($_POST,TRUE));
	log_message("_GET obj: ".print_r($_GET,TRUE));
}

if(!isset($_GET['k']))
{
        log_message('No key specified');
	exit(0);
}

$authorized = FALSE;
foreach($authorized_users as $user)
{
	if($user['key'] == $_GET['k'])
	{
		log_message("Connection received from ".$user['name']);
		foreach($user['ip_addresses'] as $allowed_address)
		{
			if($allowed_address == '*' || $allowed_address == $_SERVER['REMOTE_ADDR'])
			{
				log_message("Connection accepted from ".$user['name'].' on '.$_SERVER['REMOTE_ADDR']);
				$authorized = TRUE;
			}
		}
	}
}

if(!isset($_POST[$post_var_name]) || empty($HTTP_RAW_POST_DATA))
{
	log_message("No data submitted.");
	exit(0);
}

if (isset($_POST[$post_var_name])) {
    $json_payload = $_POST[$post_var_name];
}
else {
    $json_payload = $HTTP_RAW_POST_DATA;
}

if(!$authorized)
{
    log_message("Access denied. Stopping");	
    die();
}

$json_payload = $_POST[$post_var_name];
$payload = json_decode($json_payload);
log_message("Data Submitted:");
log_message(print_r($payload,TRUE));

$after_commit_id = $payload->after;
$repo_name = $payload->repository->name;
$commiting_user = escapeshellarg($payload->head_commit->author->name);
$commit_message = escapeshellarg($payload->head_commit->message);
$ref = $payload->ref;
$branch = end(explode('/',$ref));


log_message("After commit $after_commit_id on $repo_name");
#$script_file = $hooks_script_root.'/'.$repo_name.'.sh';
$script_params = "-c '$after_commit_id' -r '$repo_name' -b '$branch' -u $commiting_user -m $commit_message";

if(file_exists($script_file))
{
	log_message('Running '.$script_file.' '.$script_params);
	log_message(shell_exec($script_file.' '.$script_params));
}else{
	log_message($script_file . " not found, aborting.");
}

?>
