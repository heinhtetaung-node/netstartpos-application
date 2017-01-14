<?php
function is_phppos_update_available()
{
	$url = (!defined("ENVIRONMENT") or ENVIRONMENT == 'development') ? 'http://dev.amztechnology.com/netstartposv2/current_version.php?build_timestamp=1': 'http://apps.amztechnology.com/netstartposv2/current_version.php?build_timestamp=1';
	
   $ch = curl_init($url);
  	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
  	$current_build = curl_exec($ch);
  	curl_close($ch);

	return ($current_build != '' && (BUILD_TIMESTAMP != $current_build));
}

function is_on_phppos_host()
{
	return strpos($_SERVER['HTTP_HOST'],'apps.amztechnology.com/netstartposv2') !== FALSE || strpos($_SERVER['HTTP_HOST'],'dev.amztechnology.com/netstartposv2') !== FALSE;
}
?>