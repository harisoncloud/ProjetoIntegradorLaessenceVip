<?php

if (defined('HTTP_SERVER_TEMP')) {
    if (defined('JPATH_MIJOSHOP_OC')) {
	$path = HTTP_SERVER_TEMP.'components/com_mijoshop/opencart/admin/';
    } else {
	$path = HTTP_SERVER_TEMP.'components/com_aceshop/opencart/admin/';
    }
} else {
    if (defined('HTTPS_SERVER')) {
        $path = HTTPS_SERVER;
    } else if (defined('HTTP_SERVER')) {
        $path = HTTP_SERVER;
    } else {
        $path = '';
    }
}

if (version_compare(VERSION,'2.3','>=')) {  //newer than 2.2.x
    $modulesPath = 'extension/module';
} else {
    $modulesPath = 'module';
}

$path = preg_replace('/https?:/ims','',$path);

// Heading
$_['heading_title']    = '<img style="height:25px;vertical-align:-6px;" border="0px" src="'.$path.'controller/'.$modulesPath.'/magic360-opencart-module/magic360.svg"><b>&nbsp;Magic 360&trade;</b>';
$_['heading_title_big']    = '<img style="height:50px;vertical-align:-12px;" border="0px" src="'.$path.'controller/'.$modulesPath.'/magic360-opencart-module/magic360.svg"><b>&nbsp;Magic 360&trade;</b>';

$_['title']    	       = 'Magic 360';

// Text
$_['text_module']      = 'Modules';
$_['text_success']     = 'Success: You have modified module Magic 360!';
$_['entry_status']     = 'Module status';
$_['button_clear']     = 'Clear';

// Error
$_['error_permission'] = 'Warning: You do not have permission to modify module Magic 360!';
?>