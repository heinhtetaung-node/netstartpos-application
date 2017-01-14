<?php
require_once ("Datacapusbprocessor.php");
class Heartlandemvusbprocessor extends Datacapusbprocessor
{
	function __construct($controller)
	{		
		parent::__construct($controller,'EMV_IPP320_HEARTLAND','testhost5.dsipscs.com');
	}	
}