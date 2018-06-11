<?php

namespace Halland;

use Halland\Helpers\ListTable;
use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Common\ServiceException;

class AzureUploads
{
	protected $ACCOUNT_NAME;
	protected $ACCOUNT_KEY;
	protected $CONTAINER_NAME;
	
	public function __construct()
	{
		$this->ACCOUNT_NAME = get_option('azure_uploads_account_name');
		$this->ACCOUNT_KEY = get_option('azure_uploads_account_key');
		$this->CONTAINER_NAME = get_option('azure_uploads_container_name');

		add_action( 'admin_menu', array($this, 'addMenuItem') );
		add_filter( 'media_upload_tabs', array($this, 'addUploadsTab') );
		add_action( 'media_upload_ell_insert_gmap_tab', array($this, 'addListToTab') );
	}

	/**
	 * Adds a menu item to the admin panel.
	 * @return void
	 */
	public function addMenuItem() {
		$page = 'azure-uploads-options';
		$sectionId = 'azure_uploads_settings_section';

		add_menu_page(
			"page_title", // Page title.
			"Region Halland Azure",        // Menu title.
			'capability',                                         // Capability.
			'menu_slug',                                             // Menu slug.
			array($this, 'render_list_page')                                       // Callback function.
		);

		add_options_page(
			'Azure Uploads', 
			'Azure Uploads Tab', 
			'manage_options', 
			$page, 
			array($this, 'render_options_page')
		);

		add_settings_section(
			$sectionId,   // ID used to identify this section and with which to register options
			'Azure Uploads Options',                    // Title to be displayed on the administration page
			array($this, 'azure_uploads_general_options_callback'), // Callback used to render the description of the section
			$page                           // Page on which to add this section of options
	    );

	    add_settings_field( 
			'azure_uploads_account_name',                      // ID used to identify the field throughout the theme
			'Account Name',                          // The label to the left of the option interface element
			array($this, 'azure_uploads_account_name_callback'),   // The name of the function responsible for rendering the option interface
			$page,                         // The page on which this option will be displayed
			$sectionId
		);

		add_settings_field( 
			'azure_uploads_account_key',                      // ID used to identify the field throughout the theme
			'Account Key',                          // The label to the left of the option interface element
			array($this, 'azure_uploads_account_key_callback'),   // The name of the function responsible for rendering the option interface
			$page,                         // The page on which this option will be displayed
			$sectionId
		);

		add_settings_field( 
			'azure_uploads_container_name',                      // ID used to identify the field throughout the theme
			'Container',                          // The label to the left of the option interface element
			array($this, 'azure_uploads_container_name_callback'),   // The name of the function responsible for rendering the option interface
			$page,                         // The page on which this option will be displayed
			$sectionId
		);

		register_setting(
			$page,
			'azure_uploads_account_name'
		);
	
		register_setting(
			$page,
			'azure_uploads_account_key'
		);

		register_setting(
			$page,
			'azure_uploads_container_name'
		);  
	}

	public function azure_uploads_general_options_callback()
	{
		echo '<p>Fill out the form with your Azure credentials. All fields are required.</p>';
	}

	public function azure_uploads_account_name_callback($args) 
	{
    	echo '<input type="text" id="azure_uploads_account_name" name="azure_uploads_account_name" value="' . get_option('azure_uploads_account_name') . '"/>';
	}

	public function azure_uploads_account_key_callback($args) 
	{
    	echo '<input type="text" id="azure_uploads_account_key" name="azure_uploads_account_key" value="' . get_option('azure_uploads_account_key') . '"/>';
	}

	public function azure_uploads_container_name_callback($args) 
	{
    	echo '<input type="text" id="azure_uploads_container_name" name="azure_uploads_container_name" value="' . get_option('azure_uploads_container_name') . '"/>';
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
	public function render_list_page()
	{
		// Create an instance of our package class.
		$test_list_table = new ListTable();
	
		// Fetch, prepare, sort, and filter our data.
		$test_list_table->prepare_items();
	
		// Include the view markup.
		include dirname( __FILE__ ) . '/views/page.php';
	}

	public function render_options_page()
	{
		include dirname( __FILE__ ) . '/views/options.php';
	}


	public function addListToTab()
	{
		$blobs = array();
        $connectionString = "DefaultEndpointsProtocol=https;AccountName=" . $this->ACCOUNT_NAME . ";AccountKey=" . $this->ACCOUNT_KEY;
        $blobClient = BlobRestProxy::createBlobService($connectionString);

 		try {
            // List all blobs.
            $blob_list = $blobClient->listBlobs($this->CONTAINER_NAME);
            $blobs = $blob_list->getBlobs();
            foreach ($blobs as $blob) {
                array_push($blobs, $blob);
            }
        } catch (ServiceException $e) {
            $code = $e->getCode();
            $error_message = $e->getMessage();
            echo $code.": ".$error_message."<br />";
        }

        return $blobs;

        $blade = new Blade($this->views, $this->cache);

        return $blade->view()->make('hello')->with('blobs', $blobs)->render();
	}
	

}