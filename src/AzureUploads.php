<?php

namespace Halland;

use Halland\Helpers\ListTable;

class AzureUploads
{
	public function __construct()
	{
		add_action( 'admin_menu', array($this, 'addMenuItem') );
		add_filter( 'media_upload_tabs', array($this, 'addUploadsTab') );
		add_action( 'media_upload_ell_insert_gmap_tab', array($this, 'addListToTab') );
	}

	/**
	 * Adds a menu item to the admin panel.
	 * @return void
	 */
	public function addMenuItem() {
		add_menu_page(
			"page_title", // Page title.
			"Region Halland Azure",        // Menu title.
			'capability',                                         // Capability.
			'menu_slug',                                             // Menu slug.
			array($this, 'render_list_page')                                       // Callback function.
		);
	}

	/**
	 * Adds a a new tab and merges array with other tabs in the media selector
	 * @return array
	 */
	public function addUploadsTab($tabs)
	{
		unset($tabs["type_url"]);
		unset($tabs['library']);
		$newtab = array('ell_insert_gmap_tab' => __('Styrda Dokument', 'insertgmap'));
		return array_merge($tabs, $newtab);
	}

	/**
	 * Returns the table view
	 * @return object
	 */
	public function addListToTab()
	{
		$blobs = array();
        $connectionString = "DefaultEndpointsProtocol=https;AccountName=" . $this->accountName . ";AccountKey=" . $this->accountKey;
        $blobRestProxy = ServicesBuilder::getInstance()->createBlobService($connectionString);

        try {
            // List all blobs.
            $blob_list = $blobRestProxy->listBlobs($this->containerName);
            $blobs = $blob_list->getBlobs();
        } catch (ServiceException $e) {
            $code = $e->getCode();
            $error_message = $e->getMessage();
            echo $code.": ".$error_message."<br />";
        }

        $blade = new Blade($this->views, $this->cache);

        return $blade->view()->make('hello')->with('blobs', $blobs)->render();
	}
	
	public function render_list_page() {
		// Create an instance of our package class.
		$test_list_table = new ListTable();
	
		// Fetch, prepare, sort, and filter our data.
		$test_list_table->prepare_items();
	
		// Include the view markup.
		include dirname( __FILE__ ) . '/views/page.php';
	}
}