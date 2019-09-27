<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

if (!function_exists('profile_pic')) 
{
    function profile_pic($image) 
	{
        $image = asset_url('uploads/propics/' . $image);
        // return $image;
        if(is_array(@getimagesize($image)))  
		{
			return $image;  
		}
		else
		{
			return asset_url('img/unknown.jpg');
		}
    }
}

if (!function_exists('asset_url')) 
{
    function asset_url($uri = '', $group = FALSE) 
	{
        $CI = & get_instance();
        
        if (!$dir = $CI->config->item('assets_path')) 
            $dir = 'assets/';
        
        if ($group) 
            return $CI->config->base_url($dir . $group . '/' . $uri);
		else 
            return $CI->config->base_url($dir . $uri);
    }
}

if (!function_exists('app_assets')) 
{
    function app_assets($group, $app_assets) 
	{
		if (isset($app_assets) && is_array($app_assets))
		{	
			foreach ($app_assets as $asset)
			{
				if ($group == 'js')
					
					echo '<script type="text/javascript" src="' . asset_url($asset.'.js', 'js') . '"></script>'."\n";
		
				else if ($group == 'css')
					
					echo '<link type="text/css" rel="stylesheet" href="'. asset_url($asset.'.css', 'css') . '" />'."\n\t";
			}
		}
    }
}

/*
if (!function_exists('module_assets')) 
{
    function module_assets($group, $module_name) 
	{
		$file_list = get_dir_file_info(APPPATH.'modules/'.$module_name.'/views/assets/'.$group);
		echo "\n";
		foreach ($file_list as $file) include_once $file['server_path'];
    }
}*/

/* End of file assets_helper.php */
/* Location: ./application/helpers/assets_helper.php */
