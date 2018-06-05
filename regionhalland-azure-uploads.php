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



class_exists('Halland\RegisterSidebarButton') || require_once __DIR__.'/vendor/autoload.php';

use Halland\Helpers\ListTable;

function tt_add_menu_items() {
	add_menu_page(
		"page_title", // Page title.
		"Region Halland Azure",        // Menu title.
		'capability',                                         // Capability.
		'menu_slug',                                             // Menu slug.
		'tt_render_list_page'                                       // Callback function.
	);
}

function tt_render_list_page() {
	// Create an instance of our package class.
	$test_list_table = new ListTable();

	// Fetch, prepare, sort, and filter our data.
	$test_list_table->prepare_items();

	// Include the view markup.
	include dirname( __FILE__ ) . '/views/page.php';
}

add_action( 'admin_menu', 'tt_add_menu_items' );