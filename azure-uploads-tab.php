<?php
/**
 * Plugin Name: Azure Uploads Tab
 * Plugin URI: https://github.com/regionhalland/azure-uploads-tab
 * Description: Adds a tab to the media selector with files from azure blob storage
 * Version: 0.0.1
 * Author: Region Halland
 * Author URI: https://github.com/regionhalland
 * License: MIT License
 */

define('AUT_PLUGIN_NAME', 'Azure Uploads Tab');
define('AUT_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('AUT_PLUGIN_CACHE_DIR', trailingslashit(wp_upload_dir()['basedir']) . 'cache');

if (file_exists(AUT_PLUGIN_PATH . 'vendor/autoload.php')) {
	require_once AUT_PLUGIN_PATH . 'vendor/autoload.php';
}

if (file_exists(dirname(ABSPATH) . '/vendor/autoload.php')) {
	require_once dirname(ABSPATH) . '/vendor/autoload.php';
}

require_once AUT_PLUGIN_PATH . 'src/php/Vendor/Psr4ClassLoader.php';

$loader = new AzureUploadsTab\Vendor\Psr4ClassLoader();
$loader->addPrefix('AzureUploadsTab', AUT_PLUGIN_PATH);
$loader->addPrefix('AzureUploadsTab', AUT_PLUGIN_PATH . 'src/php/');
$loader->register();

// Initialize plugin
add_action('plugins_loaded', function () {
	new AzureUploadsTab\App();
}, 20);
