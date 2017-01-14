<?php
function to_currency($number, $decimals = 2)
{
	$CI =& get_instance();
	
	$decimals_system_decide = true;
	
	if ($CI->config->item('number_of_decimals') !== NULL && $CI->config->item('number_of_decimals')!= '')
	{
		$decimals = (int)$CI->config->item('number_of_decimals');
		$decimals_system_decide = false;
	}
	
	$thousands_separator = $CI->config->item('thousands_separator') ? $CI->config->item('thousands_separator') : ',';
	$decimal_point = $CI->config->item('decimal_point') ? $CI->config->item('decimal_point') : '.';
	
	$currency_symbol = $CI->config->item('currency_symbol') ? $CI->config->item('currency_symbol') : '$';
	
	if($number >= 0)
	{
		$ret = $currency_symbol.number_format($number, $decimals, $decimal_point, $thousands_separator);
   }
   else
   {
		$ret = '<span style="white-space:nowrap;">-</span>'.$currency_symbol.number_format(abs($number), $decimals, $decimal_point, $thousands_separator);
   }

	 if ($decimals_system_decide && $decimals >=2)
	 {
		 return preg_replace('/(?<=\d{2})0+$/', '', $ret);
	 }
	 else
	 {
		 return $ret;
	 }
	
}

function round_to_nearest_05($amount)
{
	return round($amount * 2, 1) / 2;
}

function to_currency_no_money($number, $decimals = 2)
{
	$CI =& get_instance();
	
	$decimals_system_decide = true;
	
	//Only use override if decimals passed in is less than 5 and we have configured a decimal override
	if ($decimals <=5 && $CI->config->item('number_of_decimals') !== NULL && $CI->config->item('number_of_decimals')!= '')
	{
		$decimals = (int)$CI->config->item('number_of_decimals');
		$decimals_system_decide = false;
	}
	
	 $ret = number_format($number, $decimals, '.', '');
	 
	 if ($decimals_system_decide && $decimals >=2)
	 {
		 return preg_replace('/(?<=\d{2})0+$/', '', $ret);
	 }
	 else
	 {
		 return $ret;
	 }
		 
}

function to_quantity($val, $show_not_set = TRUE)
{
	if ($val !== NULL)
	{
		return $val == (int)$val ? (int)$val : rtrim($val, '0');		
	}
	
	if ($show_not_set)
	{
		return lang('common_not_set');
	}
	
	return '';
	
}
?>