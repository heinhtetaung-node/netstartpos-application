<?php
function is_on_demo_host()
{
	return strpos($_SERVER['HTTP_HOST'], 'demo') === 2;
}
?>