<?php

require_once('./config.php');

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
	log_message("HTTP_RAW_POST_DATA var: ".print_r($HTTP_RAW_POST_DATA,TRUE));
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

if(!isset($_POST[$post_var_name]) && empty($HTTP_RAW_POST_DATA))
{
	log_message("No data submitted.");
	exit(0);
}

$payload_type = 'github';
if (isset($_POST[$post_var_name])) {
    $json_payload = $_POST[$post_var_name];
}
else {
    $json_payload = $HTTP_RAW_POST_DATA;
    $payload_type = 'gitlab';
}

if(!$authorized)
{
    log_message("Access denied. Stopping");	
    die();
}

$payload = json_decode($json_payload);
log_message("Data Submitted:");
log_message(print_r($payload,TRUE));

$after_commit_id = $payload->after;
$repo_name = $payload->repository->name;
if($payload_type == 'gitlab')
{
	log_message(print_r($payload->total_commits_count,TRUE));
	log_message(print_r($payload->commits[$payload->total_commits_count - 1],TRUE));
	$commiting_user = escapeshellarg($payload->commits[$payload->total_commits_count - 1]->author->name);
	$commit_message = escapeshellarg($payload->commits[$payload->total_commits_count - 1]->message);
}else if($payload_type == 'github'){
	$commiting_user = escapeshellarg($payload->head_commit->author->name);
	$commit_message = escapeshellarg($payload->head_commit->message);
}
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
