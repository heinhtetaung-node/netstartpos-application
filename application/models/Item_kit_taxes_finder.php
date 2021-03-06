<?php

class Item_kit_taxes_finder extends CI_Model
{
	/*
	Gets tax info for a particular item kit
	*/
	function get_info($item_kit_id, $transaction_type = 'sale') //Can be sale or receiving)
	{
		if($transaction_type == 'sale')
		{
			$this->load->library('sale_lib');
			$this->load->model('Customer');
			
			if ($this->sale_lib->get_customer() != -1)
			{
				
				$customer_id = $this->sale_lib->get_customer();
				$customer_info = $this->Customer->get_info($customer_id);
				if($customer_info->override_default_tax)
				{
					$this->load->model('Customer_taxes');
					
					return $this->Customer_taxes->get_info($customer_id);
				}
			}
		}
		
		
		$item_kit_location_info = $this->Item_kit_location->get_info($item_kit_id, false, true);
		if($item_kit_location_info->override_default_tax)
		{
			return $this->Item_kit_location_taxes->get_info($item_kit_id);
		}
		
		$item_kit_info = $this->Item_kit->get_info($item_kit_id);

		$this->load->model('Item_kit_taxes');

		if($item_kit_info->override_default_tax)
		{
			return $this->Item_kit_taxes->get_info($item_kit_id);
		}
		
		//Location Config
		$default_tax_1_rate = $this->Location->get_info_for_key('default_tax_1_rate');
		$default_tax_1_name = $this->Location->get_info_for_key('default_tax_1_name');
				
		$default_tax_2_rate = $this->Location->get_info_for_key('default_tax_2_rate');
		$default_tax_2_name = $this->Location->get_info_for_key('default_tax_2_name');
		$default_tax_2_cumulative = $this->Location->get_info_for_key('default_tax_2_cumulative') ? $this->Location->get_info_for_key('default_tax_2_cumulative') : 0;

		$default_tax_3_rate = $this->Location->get_info_for_key('default_tax_3_rate');
		$default_tax_3_name = $this->Location->get_info_for_key('default_tax_3_name');

		$default_tax_4_rate = $this->Location->get_info_for_key('default_tax_4_rate');
		$default_tax_4_name = $this->Location->get_info_for_key('default_tax_4_name');

		$default_tax_5_rate = $this->Location->get_info_for_key('default_tax_5_rate');
		$default_tax_5_name = $this->Location->get_info_for_key('default_tax_5_name');
		
		if ($default_tax_1_rate && is_numeric($default_tax_1_rate))
		{
			$return[] = array(
				'id' => -1,
				'item_kit_id' => $item_kit_id,
				'name' => $default_tax_1_name,
				'percent' => $default_tax_1_rate,
				'cumulative' => 0
			);
		}
		
		if ($default_tax_2_rate && is_numeric($default_tax_2_rate))
		{
			$return[] = array(
				'id' => -1,
				'item_kit_id' => $item_kit_id,
				'name' => $default_tax_2_name,
				'percent' => $default_tax_2_rate,
				'cumulative' => $default_tax_2_cumulative
			);
		}
				
		if ($default_tax_3_rate && is_numeric($default_tax_3_rate))
		{
			$return[] = array(
				'id' => -1,
				'item_kit_id' => $item_kit_id,
				'name' => $default_tax_3_name,
				'percent' => $default_tax_3_rate,
				'cumulative' => 0
			);
		}
		
		
		if ($default_tax_4_rate && is_numeric($default_tax_4_rate))
		{
			$return[] = array(
				'id' => -1,
				'item_kit_id' => $item_kit_id,
				'name' => $default_tax_4_name,
				'percent' => $default_tax_4_rate,
				'cumulative' => 0
			);
		}
		
		if ($default_tax_5_rate && is_numeric($default_tax_5_rate))
		{
			$return[] = array(
				'id' => -1,
				'item_kit_id' => $item_kit_id,
				'name' => $default_tax_5_name,
				'percent' => $default_tax_5_rate,
				'cumulative' => 0
			);
		}
		
		
		if (!empty($return))
		{
			return $return;
		}
		
		
		//Global Store Config
		
		$default_tax_1_rate = $this->config->item('default_tax_1_rate');
		$default_tax_1_name = $this->config->item('default_tax_1_name');
				
		$default_tax_2_rate = $this->config->item('default_tax_2_rate');
		$default_tax_2_name = $this->config->item('default_tax_2_name');
		$default_tax_2_cumulative = $this->config->item('default_tax_2_cumulative') ? $this->config->item('default_tax_2_cumulative') : 0;

		$default_tax_3_rate = $this->config->item('default_tax_3_rate');
		$default_tax_3_name = $this->config->item('default_tax_3_name');

		$default_tax_4_rate = $this->config->item('default_tax_4_rate');
		$default_tax_4_name = $this->config->item('default_tax_4_name');

		$default_tax_5_rate = $this->config->item('default_tax_5_rate');
		$default_tax_5_name = $this->config->item('default_tax_5_name');
		
		$return = array();
		
		if ($default_tax_1_rate && is_numeric($default_tax_1_rate))
		{
			$return[] = array(
				'id' => -1,
				'item_kit_id' => $item_kit_id,
				'name' => $default_tax_1_name,
				'percent' => $default_tax_1_rate,
				'cumulative' => 0
			);
		}
		
		if ($default_tax_2_rate && is_numeric($default_tax_2_rate))
		{
			$return[] = array(
				'id' => -1,
				'item_kit_id' => $item_kit_id,
				'name' => $default_tax_2_name,
				'percent' => $default_tax_2_rate,
				'cumulative' => $default_tax_2_cumulative
			);
		}

		if ($default_tax_3_rate && is_numeric($default_tax_3_rate))
		{
			$return[] = array(
				'id' => -1,
				'item_kit_id' => $item_kit_id,
				'name' => $default_tax_3_name,
				'percent' => $default_tax_3_rate,
				'cumulative' => 0
			);
		}

		if ($default_tax_4_rate && is_numeric($default_tax_4_rate))
		{
			$return[] = array(
				'id' => -1,
				'item_kit_id' => $item_kit_id,
				'name' => $default_tax_4_name,
				'percent' => $default_tax_4_rate,
				'cumulative' => 0
			);
		}

		if ($default_tax_5_rate && is_numeric($default_tax_5_rate))
		{
			$return[] = array(
				'id' => -1,
				'item_kit_id' => $item_kit_id,
				'name' => $default_tax_5_name,
				'percent' => $default_tax_5_rate,
				'cumulative' => 0
			);
		}
		
				
		return $return;
	}
}
?>
