<?php
require_once (APPPATH.'libraries/PHPExcel/PHPExcel.php');

function array_to_spreadsheet($arr,$is_report=FALSE)
{	
	$CI =& get_instance();
	PHPExcel_Shared_File::setUseUploadTempDirectory(true);
	$objPHPExcel = new PHPExcel();
	
	//Default all cells to text if NOT report
	if (!$is_report)
	{
		$objPHPExcel->getDefaultStyle()->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
	}
	
	for($k = 0;$k < count($arr);$k++)
	{
		for($j = 0;$j < count($arr[$k]); $j++)
		{
			if (!$is_report)
			{
				$objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow($j, $k+1, $arr[$k][$j]);	
			}
			else
			{
				$currency_symbol = $CI->config->item('currency_symbol') ? $CI->config->item('currency_symbol') : '$';
				$thousands_separator = $CI->config->item('thousands_separator') ? $CI->config->item('thousands_separator') : ',';
				
				//If we have a currency number make it nice for excel
				if (strpos($arr[$k][$j], $currency_symbol) !== false)
				{
					
					$thousands_separator = preg_quote($thousands_separator);
					$currency_symbol = preg_quote($currency_symbol);					
					$arr[$k][$j] = preg_replace("/[${thousands_separator}${currency_symbol}]/", "", $arr[$k][$j]);
				}
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($j, $k+1, $arr[$k][$j]);
			}
		}
	}
	
	if ($CI->config->item('spreadsheet_format') == 'XLSX')
	{
		$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);		
	}
	else
	{
		$objWriter = new PHPExcel_Writer_CSV($objPHPExcel);
	}
	
	ob_start();
	$objWriter->save('php://output');
	$excelOutput = ob_get_clean();
	
	return $excelOutput;
}

function file_to_obj_php_excel($inputFileName)
{
	$CI =& get_instance();
	PHPExcel_Shared_File::setUseUploadTempDirectory(true);
	if ($CI->config->item('spreadsheet_format') == 'XLSX')
	{
		$objReader = new PHPExcel_Reader_Excel2007();
	}
	else
	{
		$objReader = new PHPExcel_Reader_CSV();
		PHPExcel_Cell::setValueBinder(new TextValueBinder());
	}
	
	$objReader->setReadDataOnly(true);
	$objPHPExcel = $objReader->load($inputFileName);
	
	return $objPHPExcel;
}

class TextValueBinder implements PHPExcel_Cell_IValueBinder
{
	public function bindValue(PHPExcel_Cell $cell, $value = null) 
	{
	    $cell->setValueExplicit($value, PHPExcel_Cell_DataType::TYPE_STRING);
	    return true;
	}
}