<?php
require_once ("Datacapusbprocessor.php");
class Mercuryemvusbprocessor extends Datacapusbprocessor
{
	function __construct($controller)
	{		
		parent::__construct($controller,'EMV_VX805_MERCURY','127.0.0.1');
	}	
}