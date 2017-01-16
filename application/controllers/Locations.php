<?php
require_once ("Secure_area.php");
require_once ("interfaces/Idata_controller.php");
class Locations extends Secure_area implements Idata_controller
{
	function __construct()
	{
		parent::__construct('locations');
		$this->lang->load('locations');
		$this->lang->load('module');		
		
	}
	
	function index($offset=0)
	{		
		$params = $this->session->userdata('location_search_data') ? $this->session->userdata('location_search_data') : array('offset' => 0, 'order_col' => 'location_id', 'order_dir' => 'asc', 'search' => FALSE);
		if ($offset!=$params['offset'])
		{
		   redirect('locations/index/'.$params['offset']);
		}
		
		$this->check_action_permission('search');
		
		$config['base_url'] = site_url('locations/sorting');
		$config['total_rows'] = $this->Location->count_all();
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		$data['controller_name']=strtolower(get_class());
		$data['per_page'] = $config['per_page'];
		$data['search'] = $params['search'] ? $params['search'] : "";
		if ($data['search'])
		{
			$config['total_rows'] = $this->Location->search_count_all($data['search']);
			$table_data = $this->Location->search($data['search'],$data['per_page'],$params['offset'],$params['order_col'],$params['order_dir']);
		}
		else
		{
			$config['total_rows'] = $this->Location->count_all();
			$table_data = $this->Location->get_all($data['per_page'],$params['offset'],$params['order_col'],$params['order_dir']);
		}
		$this->load->library('pagination');$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();
		$data['order_col'] = $params['order_col'];
		$data['order_dir'] = $params['order_dir'];
		$data['total_rows'] = $config['total_rows'];
		$data['manage_table']=get_locations_manage_table($table_data,$this);
		$this->load->view('locations/manage',$data);
	}
	
	function sorting()
	{
		$this->check_action_permission('search');
		
		$search=$this->input->post('search') ? $this->input->post('search') : "";
		$per_page=$this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;

		$offset = $this->input->post('offset') ? $this->input->post('offset') : 0;
		$order_col = $this->input->post('order_col') ? $this->input->post('order_col') : 'name';
		$order_dir = $this->input->post('order_dir') ? $this->input->post('order_dir'): 'asc';

		$location_search_data = array('offset' => $offset, 'order_col' => $order_col, 'order_dir' => $order_dir, 'search' => $search);
		$this->session->set_userdata("location_search_data",$location_search_data);

		if ($search)
		{
			$config['total_rows'] = $this->Location->search_count_all($search);
			$table_data = $this->Location->search($search,$per_page,$this->input->post('offset') ? $this->input->post('offset') : 0, $this->input->post('order_col') ? $this->input->post('order_col') : 'name' ,$this->input->post('order_dir') ? $this->input->post('order_dir'): 'asc');
		}
		else
		{
			$config['total_rows'] = $this->Location->count_all();
			$table_data = $this->Location->get_all($per_page,$this->input->post('offset') ? $this->input->post('offset') : 0, $this->input->post('order_col') ? $this->input->post('order_col') : 'name' ,$this->input->post('order_dir') ? $this->input->post('order_dir'): 'asc');
		}
		$config['base_url'] = site_url('items/sorting');
		$config['per_page'] = $per_page; 
		$this->load->library('pagination');$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();
		$data['manage_table']=get_locations_manage_table_data_rows($table_data,$this);
		echo json_encode(array('manage_table' => $data['manage_table'], 'pagination' => $data['pagination'],'total_rows' => $config['total_rows']));	
	}

	function search()
	{
		$this->check_action_permission('search');
		
		$search=$this->input->post('search');
		$offset = $this->input->post('offset') ? $this->input->post('offset') : 0;
		$order_col = $this->input->post('order_col') ? $this->input->post('order_col') : 'name';
		$order_dir = $this->input->post('order_dir') ? $this->input->post('order_dir'): 'asc';

		$location_search_data = array('offset' => $offset, 'order_col' => $order_col, 'order_dir' => $order_dir, 'search' => $search);
		$this->session->set_userdata("location_search_data",$location_search_data);
		$per_page=$this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
		$search_data=$this->Location->search($search,$per_page,$this->input->post('offset') ? $this->input->post('offset') : 0, $this->input->post('order_col') ? $this->input->post('order_col') : 'name' ,$this->input->post('order_dir') ? $this->input->post('order_dir'): 'asc');
		$config['base_url'] = site_url('locations/search');
		$config['total_rows'] = $this->Location->search_count_all($search);
		$config['per_page'] = $per_page ;
		$this->load->library('pagination');$this->pagination->initialize($config);				
		$data['pagination'] = $this->pagination->create_links();
		$data['manage_table']=get_locations_manage_table_data_rows($search_data,$this);
		echo json_encode(array('manage_table' => $data['manage_table'], 'pagination' => $data['pagination'],'total_rows' => $config['total_rows']));
	}

	function clear_state()
	{
		$this->session->unset_userdata('location_search_data');
		redirect('locations');
	}

	/*
	Gives search suggestions based on what is being searched for
	*/
	function suggest()
	{
		//allow parallel searchs to improve performance.
		session_write_close();
		$suggestions = $this->Location->get_search_suggestions($this->input->get('term'),100);
		echo json_encode($suggestions);
	}
	

	// edited by Hein Htet Aung @16Jan2017 ({
	function view($location_id=-1,$redirect=false)  
	{
		//$this->check_action_permission('add_update');
		if($location_id!=-1){
		
			$location_info = $this->Location->get_info($location_id);
			$data = array();
			$data['needs_auth'] = FALSE;
			
			$this->load->helper('demo');
			if (!is_on_demo_host())
			{
				if (!$location_info->location_id && !$this->session->flashdata('has_location_auth'))
				{
					$data['needs_auth'] = TRUE;
				}
			}
			if ($this->session->flashdata('purchase_email'))
			{
				$data['purchase_email'] = $this->session->flashdata('purchase_email');
			}
			else
			{
				$data['purchase_email'] = '';
			}
			
			$data['location_info']=$location_info;
			//$data['registers'] = $this->Register->get_all($location_id);
			
			$data['all_timezones'] = $this->_get_timezones();
			$data['redirect']=$redirect;
			
			
					
			if ($this->Location->get_info_for_key('credit_card_processor') == 'mercury' || !$this->Location->get_info_for_key('credit_card_processor'))
			{
				require_once (APPPATH.'libraries/Mercuryemvusbprocessor.php');
				$credit_card_processor = new MercuryEMVUSBProcessor($this);
				$data['emv_param_download_init_params'] = $credit_card_processor->get_emv_param_download_params();
			}
			elseif ($this->Location->get_info_for_key('credit_card_processor') == 'heartland')
			{
				require_once (APPPATH.'libraries/Heartlandemvusbprocessor.php');
				$credit_card_processor = new Heartlandemvusbprocessor($this);
				$data['emv_param_download_init_params'] = $credit_card_processor->get_emv_param_download_params();			
			}
			elseif ($this->Location->get_info_for_key('credit_card_processor') == 'evo')
			{
				require_once (APPPATH.'libraries/Evoemvusbprocessor.php');
				$credit_card_processor = new Evoemvusbprocessor($this);
				$data['emv_param_download_init_params'] = $credit_card_processor->get_emv_param_download_params();			
			}
			else //Default to Mercury just so we have something.... (most likley going to be caught in if statements above)
			{
				require_once (APPPATH.'libraries/Mercuryemvusbprocessor.php');
				$credit_card_processor = new MercuryEMVUSBProcessor($this);
				$data['emv_param_download_init_params'] = $credit_card_processor->get_emv_param_download_params();
			}
		}
		
		$data['employees'] = array();
		foreach ($this->Employee->get_all()->result() as $employee)
		{
			$has_access = $this->Employee->is_employee_authenticated($employee->person_id, $location_id);
			$data['employees'][$employee->person_id] = array('name' => $employee->first_name . ' '. $employee->last_name, 'has_access' => $has_access);
		}

		$this->load->view("locations/form",$data);
	}
	// edited by Hein Htet Aung @16Jan2017 });
	

	//http://stackoverflow.com/questions/1727077/generating-a-drop-down-list-of-timezones-with-php
	function _get_timezones()
	{
		$timezones = DateTimeZone::listIdentifiers();
		$timezone_offsets = array();
		
		foreach($timezones as $timezone)
		{
		    $tz = new DateTimeZone($timezone);
		    $timezone_offsets[$timezone] = $tz->getOffset(new DateTime);
		}

		// sort timezone by offset
		asort($timezone_offsets);

		$timezone_list = array();
		foreach($timezone_offsets as $timezone => $offset)
		{
		    $offset_prefix = $offset < 0 ? '-' : '+';
		    $offset_formatted = gmdate('H:i', abs($offset) );
		    $pretty_offset = "UTC${offset_prefix}${offset_formatted}";
			

			$current_time = '';
			$date = new DateTime();
			$date->setTimezone(new DateTimeZone($timezone));
			if (method_exists($date, 'setTimestamp'))
			{
				$date->setTimestamp(time());
				$current_time = $date->format(get_time_format());
			}
			$timezone_list[$timezone] = "(${pretty_offset}) $timezone $current_time";
		}

		return $timezone_list;
	}
	
	function check_auth()
	{
		$this->form_validation->set_rules('purchase_email', 'lang:locations_purchase_email', 'callback_location_auth_check');
	    $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
		
		if($this->form_validation->run() !== FALSE)
		{
			$this->session->set_flashdata('has_location_auth', TRUE);
			$this->session->set_flashdata('purchase_email', $this->input->post('purchase_email'));
			redirect('locations/view/-1');
		}
		else
		{
			$data  = array();
			$data['location_info']=$this->Location->get_info(-1);
			
			$data['needs_auth'] = TRUE;
			$this->load->view("locations/form", $data);
		}
	}
	
	function location_auth_check($email)
	{
		if (!$this->does_have_valid_number_of_locations_for_an_additional_location($email))
		{
			$this->form_validation->set_message('location_auth_check', lang('locations_invalid_email_or_dont_have_auth'));
			return FALSE;
		}
		
		return TRUE;
	}

	//Does the validation for valid Locations
	//NOTE: If you modify this function you are breaking the terms of license
	function does_have_valid_number_of_locations_for_an_additional_location($email)
	{
		$current_location_count = $this->Location->count_all();
		$auth_url = (!defined("ENVIRONMENT") or ENVIRONMENT == 'development') ? 'http://dev.amztechnology.com/netstartposv2/allowed_stores.php?email='.rawurlencode($email): 'http://apps.amztechnology.com/netstartposv2/allowed_stores.php?email='.rawurlencode($email);
		
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, $auth_url); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        $authenticated_locations_count = (int)curl_exec($ch); 
        curl_close($ch);		
		
		return ($authenticated_locations_count >= ($current_location_count + 1));
	}
	
	function save_emv_data($location_id=false)
	{
		$this->load->helper('demo');
		if (!is_on_demo_host())
		{
			if ($location_id)
			{
				$location_data = array(
				'credit_card_processor' => $this->input->post('credit_card_processor'),
				'enable_credit_card_processing'=>1,
				'emv_merchant_id'=>$this->input->post('emv_merchant_id'),
				'com_port'=>$this->input->post('com_port'),
				'listener_port'=>$this->input->post('listener_port'),
				);
				
				$this->Location->save($location_data,$location_id);
			}
			
			echo json_encode(array('success'=>true));
			return;
		}
		
		echo json_encode(array('success'=>false));
	}

	
	// edited by Hein Htet Aung @16Jan2017 ({
	function save($location_id=-1)		
	{
		$this->check_action_permission('add_update');
		
		$location_data = array(
		'name'=>$this->input->post('name'),
		'color' => $this->input->post('color') ? $this->input->post('color') : NULL,
		'address'=>$this->input->post('address'),
		'phone'=>$this->input->post('phone'),
		'fax'=>$this->input->post('fax'),
		'email'=>$this->input->post('email'),
		'return_policy'=>$this->input->post('return_policy'),
		'receive_stock_alert' => $this->input->post('receive_stock_alert') ? 1 : 0,
		'stock_alert_email'=>$this->input->post('stock_alert_email'),
		'timezone'=>$this->input->post('timezone'),
		'mailchimp_api_key'=>$this->input->post('mailchimp_api_key'),
		'enable_credit_card_processing'=>$this->input->post('enable_credit_card_processing') ? 1 : 0,
		'credit_card_processor' => $this->input->post('credit_card_processor'),		
		'stripe_public' => $this->input->post('stripe_public'),		
		'stripe_private' => $this->input->post('stripe_private'),	
		'braintree_merchant_id' => $this->input->post('braintree_merchant_id'),	
		'braintree_public_key' => $this->input->post('braintree_public_key'),	
		'braintree_private_key' => $this->input->post('braintree_private_key'),	
		'stripe_currency_code' => $this->input->post('stripe_currency_code'),
		'hosted_checkout_merchant_id'=>$this->input->post('hosted_checkout_merchant_id'),
		'hosted_checkout_merchant_password'=>$this->input->request('hosted_checkout_merchant_password'),//Use REQUEST to avoid url encoding that causes issues
		'emv_merchant_id'=>$this->input->post('emv_merchant_id'),
		'com_port'=>$this->input->post('com_port'),
		'listener_port'=>$this->input->post('listener_port'),
		'default_tax_1_rate'=>$this->input->post('default_tax_1_rate') && is_numeric($this->input->post('default_tax_1_rate')) ?  $this->input->post('default_tax_1_rate') : NULL ,		
		'default_tax_1_name'=>$this->input->post('default_tax_1_name'),		
		'default_tax_2_rate'=>$this->input->post('default_tax_2_rate') && is_numeric($this->input->post('default_tax_2_rate')) ?  $this->input->post('default_tax_2_rate') : NULL ,		
		'default_tax_2_name'=>$this->input->post('default_tax_2_name'),
		'default_tax_2_cumulative' => $this->input->post('default_tax_2_cumulative') ? 1 : 0,
		'default_tax_3_rate'=>$this->input->post('default_tax_3_rate') && is_numeric($this->input->post('default_tax_3_rate')) ?  $this->input->post('default_tax_3_rate') : NULL ,		
		'default_tax_3_name'=>$this->input->post('default_tax_3_name'),		
		'default_tax_4_rate'=>$this->input->post('default_tax_4_rate') && is_numeric($this->input->post('default_tax_4_rate')) ?  $this->input->post('default_tax_4_rate') : NULL ,		
		'default_tax_4_name'=>$this->input->post('default_tax_4_name'),		
		'default_tax_5_rate'=>$this->input->post('default_tax_5_rate') && is_numeric($this->input->post('default_tax_5_rate')) ?  $this->input->post('default_tax_5_rate') : NULL ,		
		'default_tax_5_name'=>$this->input->post('default_tax_5_name'),		
		);
		
		//var_dump($location_data); exit;
		
		$redirect = $this->input->post('redirect');
		$employees = $this->input->post('employees') ? $this->input->post('employees') : array();
		
		$this->load->helper('demo');
		if (is_on_demo_host())
		{			
			unset($location_data['enable_credit_card_processing']);
			unset($location_data['hosted_checkout_merchant_id']);
			unset($location_data['hosted_checkout_merchant_password']);
			unset($location_data['stripe_public']);
			unset($location_data['stripe_private']);
			unset($location_data['stripe_currency_code']);
			unset($location_data['braintree_merchant_id']);
			unset($location_data['braintree_public_key']);
			unset($location_data['braintree_private_key']);			
			unset($location_data['emv_merchant_id']);
			unset($location_data['com_port']);
			unset($location_data['listener_port']);
			unset($location_data['mailchimp_api_key']);
			
			if ($location_id == 1)
			{
				unset($location_data['color']);
			}
			
			//Make sure demo admin user is always included
			if(!in_array(1, $employees))
			{
				$employees[] = 1;
			}
		}
		
		//Make sure we always have an employee with access
		if (empty($employees))
		{
			$employees[] = 1;
		}
		
		if ($location_id == -1)
		{
			//If we have a purcahse email, do a an auth check
			$purchase_email = $this->input->post('purchase_email');
		
			$this->load->helper('demo');
			// if (!is_on_demo_host() && (!$purchase_email || !$this->does_have_valid_number_of_locations_for_an_additional_location($purchase_email)))
			// {
				// echo json_encode(array('success'=>false,'message'=>lang('locations_error_adding_updating')));
				// die();
			// }
		}
		
		//var_dump($location_data); exit;
		
		if($this->Location->save($location_data,$location_id) && $this->Location->assign_employees_to_location($location_id != -1 ? $location_id : $location_data['location_id'],$employees))
		{
			$success_message = '';
			
			//New item
			if($location_id==-1)
			{
				$this->save_registers($location_data['location_id'], $this->input->post('registers_to_edit'), $this->input->post('registers_to_add'), $this->input->post('registers_to_delete'));
				$success_message = lang('locations_successful_adding').' '.$location_data['name'];
				echo json_encode(array('success'=>true,'message'=>$success_message,'location_id'=>$location_data['location_id']));
			}
			else //previous item
			{
				$this->save_registers($location_id, $this->input->post('registers_to_edit'), $this->input->post('registers_to_add'), $this->input->post('registers_to_delete'));
				$success_message = lang('locations_successful_updating').' '.$location_data['name'];
				$this->session->set_flashdata('manage_success_message', $success_message);
				echo json_encode(array('success'=>true,'message'=>$success_message,'location_id'=>$location_id,'redirect'=>$redirect));
			}
			
		}
		else//failure
		{
			echo json_encode(array('success'=>false,'message'=>lang('locations_error_adding_updating')));
		}

	}
	// edited by Hein Htet Aung @16Jan2017 });
	
	function save_registers($location_id, $registers_to_edit, $registers_to_add, $registers_to_delete)
	{		
		if ($registers_to_edit)
		{
			foreach($registers_to_edit as $register_id => $name)
			{
				if ($name)
				{
					$register_data = array('name' => $name, 'location_id' => $location_id);
					$this->Register->save($register_data, $register_id);
				}
			}
		}
		
		if ($registers_to_add)
		{
			foreach($registers_to_add as $name)
			{
				if ($name)
				{
					$register_data = array('name' => $name, 'location_id' => $location_id);
					$this->Register->save($register_data);
				}
			}
		}
		
		if ($registers_to_delete)
		{
			foreach($registers_to_delete as $register_id)
			{
				$this->Register->delete($register_id);
			}
		}
		
		//If we aren't editing any registers and aren't adding any, then we need to add a register so we always have done
		if ($registers_to_edit === NULL && $registers_to_add === NULL)
		{
			$register_data = array('name' => lang('locations_default'), 'location_id' => $location_id);
			$this->Register->save($register_data);
		}
		
		return TRUE;
	}
	
	function delete()
	{
		$this->check_action_permission('delete');
		
		$locations_to_delete=$this->input->post('ids');
		
		$this->load->helper('demo');
		//Don't let location 1 to be deleted
		if (is_on_demo_host())
		{
			$default_location_index = array_search(1, $locations_to_delete);
			
			if ($default_location_index !== FALSE)
			{
				unset($locations_to_delete[$default_location_index]);
				$locations_to_delete = array_values($locations_to_delete);
			}
		}
		
		if($this->Location->delete_list($locations_to_delete))
		{
			
			echo json_encode(array('success'=>true,'message'=>lang('locations_successful_deleted').' '.lang('locations_one_or_multiple')));
		}
		else
		{
			echo json_encode(array('success'=>false,'message'=>lang('locations_cannot_be_deleted')));
		}
	}
}
?>