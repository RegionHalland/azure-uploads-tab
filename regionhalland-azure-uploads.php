<?php
/**
 * Plugin Name: Region Halland Azure Uploads
 * Plugin URI:
 * Description:
 * Version: 0.0.1
 * Author:
 * Author URI:
 * License: MIT License
 */

//class_exists('Roots\Bedrock\URLFixer') || require_once __DIR__.'/vendor/autoload.php';
class_exists('Halland\RegisterSidebarButton') || require_once __DIR__.'/vendor/autoload.php';

use Halland\RegisterSidebarButton;

new RegisterSidebarButton();
