<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('pr')) 
{
	function pr($data) 
	{
		echo '<pre>';
		print_r($data);
		echo '</pre>';
	}
}

if (!function_exists('readable_date')) 
{
	function readable_date($date) 
	{
		if ($date && ($date != '0000-00-00'))
		{
			$date = new DateTime($date);
			return $date->format("D d M, Y");
		}
		else
		{
			return FALSE;
		}
	}
}

// if (!function_exists('site_config')) 
// {
// 	function site_config($config_name = FALSE) 
// 	{
// 		$CI =& get_instance();
// 		$CI->load->model('settings/settings_model');

// 		if ($config_name)
// 		{
// 			$conf = $CI->settings_model->find_by('config_name', $config_name);
// 			return $conf->config_value;
// 		}
// 		else
// 		{	
// 			$configs = $CI->settings_model->find_all();
			
// 			$conf = array();
// 			foreach ($configs as $cnf)
// 			{
// 				$conf[$cnf->config_name] = $cnf->config_value;
// 			}

// 			return $conf;
// 		}
// 	}
// }

// if (!function_exists('convert_amount')) 
// {
// 	function convert_amount($from_amount, $from_currency, $to_currency, $from_config = FALSE) 
// 	{
// 		$CI =& get_instance();
// 		$CI->load->model('accounting/exchange_rates_model');

// 		$exchange_rate_base = site_config('exchange_rate_base');
// 		// $default_transaction_currency = site_config('default_transaction_currency');

// 		// convert to base rate
// 		$xrate = $CI->exchange_rates_model->find_by(array('exchange_rate_deleted !=' => 1, 'exchange_rate_currency' => $from_currency));
// 		$to_amount = $from_amount / $xrate->exchange_rate_factor;

// 		// convert to new currency
// 		$xrate = $CI->exchange_rates_model->find_by(array('exchange_rate_deleted !=' => 1, 'exchange_rate_currency' => $to_currency));
// 		$to_amount = $to_amount * $xrate->exchange_rate_factor;

// 		return $to_amount;
// 	}
// }

// if (!function_exists('currency_sign')) 
// {
// 	function currency_sign($currency) 
// 	{
// 		$sign = '&#36;';
// 		switch($currency)
// 		{
// 			case 'PHP': $sign = '&#8369;'; break;
// 			case 'USD': $sign = '&#36;'; break;
// 			default: $sign = $currency; break;
// 		}
// 		return $sign;
// 	}
// }

if (!function_exists('debug')) 
{
	function debug($data) 
	{
		log_message('debug', print_r($data, true));
	}
}

if (!function_exists('is_serial')) 
{
	function is_serial($string) {
		return (@unserialize($string) !== false || $string == 'b:0;');
	}
}

if (!function_exists('module_list')) 
{
    function module_list() 
	{
		$modules = get_dir_file_info(APPPATH.'modules/');
		return $modules;
    }
}

if (!function_exists('controller_list')) 
{
    function controller_list() 
	{
		$modules = module_list();
		$controllers = array();
		foreach ($modules as $module)
		{
			$controllers[$module['name']] = get_dir_file_info(APPPATH.'modules/'.$module['name'].'/controllers/');
		}
		return $controllers;
    }
}

if (!function_exists('mainnav')) 
{
	function mainnav() 
	{
		$modules = module_list();
		foreach ($modules as $module)
		{
			$file = APPPATH.'modules/'.$module['name'].'/views/'.$module['name'].'_nav.php';
			if (file_exists($file)) include_once $file;
		}
    }
}

if (!function_exists('in_array_search')) {
	function in_array_search($string, $array = array ())
	{       
	    foreach ($array as $key => $value) 
		{
	        unset ($array[$key]);
	        if (strpos($value, $string) !== false) {
	            $array[$key] = $value;
	        }
	    }       
	    return $array;
	}
}

if (!function_exists('array_values_by_key')) 
{
	function array_values_by_key($array, $key = FALSE, $value)
	{
		if (is_array($array))
		{
			foreach ($array as $row) 
			{
				if ($key)
				{
					$vals[$row->$key] = $row->$value; 
				}
				else
				{
					$vals[] = $row->$value; 
				}
			}

			return $vals;
		}
		else
		{
			return array();
		}
				
	}
}

if (!function_exists('multi_array_object_search_sibling')) 
{
	function multi_array_object_search_sibling($array_items, $needle_key, $needle_value, $return_value)
	{
		foreach($array_items as $item)
		{
			if ($item->$needle_key === $needle_value )
			return $item->$return_value;
		}
		return false;
	}
}

if( ! function_exists('relative_time'))
{
    function relative_time($datetime)
    {
        $CI =& get_instance();
        $CI->lang->load('date');

        if(!is_numeric($datetime))
        {
            $val = explode(" ",$datetime);
            $date = explode("-",$val[0]);
            $time = explode(":",$val[1]);
            $datetime = mktime($time[0],$time[1],$time[2],$date[1],$date[2],$date[0]);
        }

        $difference = time() - $datetime;
        $periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
        $lengths = array("60","60","24","7","4.35","12","10");

        if ($difference > 0) 
        { 
            $ending = 'ago';
        } 
        else 
        { 
            $difference = -$difference;
            $ending = 'to go';
        }
        for($j = 0; $difference >= $lengths[$j]; $j++)
        {
            $difference /= $lengths[$j];
        } 
        $difference = round($difference);

        if($difference != 1) 
        { 
            $period = strtolower($CI->lang->line('date_'.$periods[$j].'s'));
        } else {
            $period = strtolower($CI->lang->line('date_'.$periods[$j]));
        }

        return "$difference $period $ending";
    }

	function get_age($birth_date)
	{
		if ($birth_date == '0000-00-00') return 0;
		
		// Put the year, month and day in separate variables
		list($Year, $Month, $Day) = explode("-", $birth_date);

		$YearDiff = date("Y") - $Year;

		// If the birthday hasn't arrived yet this year, the person is one year younger
		if(date("m") < $Month || (date("m") == $Month && date("d") < $DayDiff))
		{
			$YearDiff--;
		}
		return $YearDiff;
	}
	
	function url_get_contents($Url) {
	    if (!function_exists('curl_init')){ 
	        die('CURL is not installed!');
	    }
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $Url);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    $output = curl_exec($ch);
	    curl_close($ch);
	    return $output;
	}

} 


/* End of file common_helper.php */
/* Location: ./application/helpers/common_helper.php */
