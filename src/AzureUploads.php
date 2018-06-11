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

		add_action( 'admin_menu', array($this, 'addOptionsPage') );
		add_filter( 'media_upload_tabs', array($this, 'addUploadsTab') );
		add_action( 'media_upload_ell_insert_gmap_tab', array($this, 'render_list_page') );
	}

	/**
	 * Adds an option page to the settings menu.
	 * @return void
	 */
	public function addOptionsPage() {
		$page = 'azure-uploads-options';
		$sectionId = 'azure_uploads_settings_section';

		add_menu_page(
			'Azure Uploads',
			'Azure Uploads',
			'capability',
			'menu_slug',
			array($this, 'render_list_page')
		);

		// Add options page
		add_options_page(
			'Azure Uploads', 
			'Azure Uploads Tab', 
			'manage_options', 
			$page, 
			array($this, 'render_options_page')
		);

		// Add settings section to the created options page
		add_settings_section(
			$sectionId,   
			'Azure Uploads Options',
			function() {
				echo '<p>Fill out the form with your Azure credentials. All fields are required.</p>';
			},
			$page
	    );

		// Add the settings fields
	    add_settings_field( 
			'azure_uploads_account_name',
			'Account Name',
			function() {
				echo '<input type="text" id="azure_uploads_account_name" name="azure_uploads_account_name" value="' . get_option('azure_uploads_account_name') . '"/>';
			},
			$page,
			$sectionId
		);

		add_settings_field( 
			'azure_uploads_account_key',
			'Account Key',
			function() {
				echo '<input type="text" id="azure_uploads_account_key" name="azure_uploads_account_key" value="' . get_option('azure_uploads_account_key') . '"/>';
			},
			$page,
			$sectionId
		);

		add_settings_field( 
			'azure_uploads_container_name',
			'Container',
			function() {
				echo '<input type="text" id="azure_uploads_container_name" name="azure_uploads_container_name" value="' . get_option('azure_uploads_container_name') . '"/>';
			},
			$page,
			$sectionId
		);

		// Register the created fields
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
		$blobs = self::getBlobs();

		$table = new ListTable($blobs);
		$table->prepare_items();

        // $blade = new Blade($this->views, $this->cache);
        // return $blade->view()->make('hello')->with('blobs', $blobs)->render();
		include dirname( __FILE__ ) . '/views/page.php';
	}

	public function render_options_page()
	{
		include dirname( __FILE__ ) . '/views/options.php';
	}


	private function getBlobs()
	{
		$blobs = array();
        $connectionString = "DefaultEndpointsProtocol=https;AccountName=" . $this->ACCOUNT_NAME . ";AccountKey=" . $this->ACCOUNT_KEY;
        $blobClient = BlobRestProxy::createBlobService($connectionString);

 		try {
            // List all blobs.
            $blob_list = $blobClient->listBlobs($this->CONTAINER_NAME);
            $blobs = $blob_list->getBlobs();
        } catch (ServiceException $e) {
            $code = $e->getCode();
            $error_message = $e->getMessage();
            echo $code.": ".$error_message."<br />";
        }

        return $blobs;
	}
	

}