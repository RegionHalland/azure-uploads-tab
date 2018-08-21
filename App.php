<?php

namespace AzureUploadsTab;

use Philo\Blade\Blade;
use Guzzle\Guzzle;
use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
use MicrosoftAzure\Storage\Common\ServiceException;
use AzureUploadsTab\Helpers\ListTable;

class App
{
	protected $ACCOUNT_NAME;
	protected $ACCOUNT_KEY;
	protected $CONTAINER_NAME;
	protected $TAB_NAME;
	protected $CONNECTION_STRING;
	
	public function __construct()
	{	
		$this->views = __DIR__ . '/src/php/views';
		$this->cache = wp_upload_dir()['basedir'] . '/cache';

		$this->ACCOUNT_NAME = get_option('azure_uploads_account_name');
		$this->ACCOUNT_KEY = get_option('azure_uploads_account_key');
		$this->CONTAINER_NAME = get_option('azure_uploads_container_name');
		$this->TAB_NAME = get_option('azure_uploads_tab_name');
		$this->CONNECTION_STRING = "DefaultEndpointsProtocol=https;AccountName=" . $this->ACCOUNT_NAME . ";AccountKey=" . $this->ACCOUNT_KEY;

		add_action( 'admin_enqueue_scripts', array($this, 'enqueue') );
		add_action( 'admin_menu', array($this, 'createOptionsPage') );
		add_filter( 'media_upload_tabs', array($this, 'addTabToUploads') );
		add_action( 'media_upload_azure_uploads_tab', array($this, 'iframe') );
	}

	/**
	 * Enqueue scripts and styles.
	 * @return void
	 */
	public function enqueue()
	{
		wp_enqueue_script( 'azure-uploads-tab-js', AUT_PLUGIN_URL . '/dist/index.min.js', false, '', true );
		wp_enqueue_style( 'azure-uploads-tab-css', AUT_PLUGIN_URL . '/dist/index.min.css', false, false, '' );
	}

	/**
	 * Creates an option page and adds it to the settings menu.
	 * @return void
	 */
	public function createOptionsPage() 
	{
		$title = 'Azure Uploads Tab';
		$slug = 'azure-uploads-options';
		$sectionId = 'azure_uploads_settings_section';

		// Add options page
		add_options_page(
			$title, 
			$title, 
			'manage_options', 
			$slug, 
			array($this, 'renderOptionsPage')
		);

		// Add settings section to the created options page
		add_settings_section(
			$sectionId,   
			'Azure Uploads Options',
			function() {
				echo '<p>Fill out the form with your Azure credentials. All fields are required.</p>';
			},
			$slug
		);

		// Add the tab name field
		add_settings_field( 
			'azure_uploads_tab_name',
			'Uploads Tab Name',
			array($this, 'addSettingsFieldCallback'),
			$slug,
			$sectionId,
			array( 'azure_uploads_tab_name' )
		);

		// Add the settings fields
		add_settings_field( 
			'azure_uploads_account_name',
			'Azure Account Name',
			array($this, 'addSettingsFieldCallback'),
			$slug,
			$sectionId,
			array( 'azure_uploads_account_name' )
		);

		add_settings_field( 
			'azure_uploads_account_key',
			'Azure Account Key',
			array($this, 'addSettingsFieldCallback'),
			$slug,
			$sectionId,
			array( 'azure_uploads_account_key' )
		);

		add_settings_field( 
			'azure_uploads_container_name',
			'Azure Container',
			array($this, 'addSettingsFieldCallback'),
			$slug,
			$sectionId,
			array( 'azure_uploads_container_name' )
		);

		// Register the created fields
		register_setting( $slug, 'azure_uploads_tab_name' );
		register_setting( $slug, 'azure_uploads_account_name' );
		register_setting( $slug, 'azure_uploads_account_key' );
		register_setting( $slug, 'azure_uploads_container_name' );  
	}

	/**
	 * Callback to add the settings field
	 * @return void
	 */
	public function addSettingsFieldCallback( $args )
	{
		echo '<input type="text" id="' . $args[0] . '" name="' . $args[0] . '" value="' . get_option($args[0]) . '"/>';
	}

	/**
	 * Render the options page
	 * @return void
	 */
	public function renderOptionsPage()
	{ 
		$blade = new Blade($this->views, $this->cache);
		echo $blade->view()->make('options')->render();
	}

	/**
	 * Adds a a new tab and merges array with other tabs in the media selector
	 * @return array
	 */
	public function addTabToUploads( $tabs )
	{
		$title = isset($this->TAB_NAME) && !empty($this->TAB_NAME) ? $this->TAB_NAME : AUT_PLUGIN_NAME;
		$tab = array('azure_uploads_tab' => $title);
		
		return array_merge($tabs, $tab);
	}

	/**
	 * Echoes the table list view
	 * @return void
	 */
	public function renderListPage()
	{
		// Check for search query
		if (isset($_POST['s'])) {
			// TODO: Search here
			$query = $_POST['s'];
			$blobs = self::getBlobsSearch($query);
		} else {
			$blobs = self::getBlobs();
		}

		$listTable = new ListTable($blobs);
		$listTable->prepare_items();
		
		$blade = new Blade($this->views, $this->cache);
		echo $blade->view()->make('table')->with('listTable', $listTable)->render();
	}

	/**
	 * Adds the iframe to display content
	 * @return void
	 */
	public function iframe()
	{
		return wp_iframe( array($this, 'renderListPage') );
	}

	/**
	 * Gets all blobs from container
	 * @return void
	 */
	private function getBlobs()
	{
		$blobs = array();
		$blobClient = BlobRestProxy::createBlobService($this->CONNECTION_STRING);

		try {
			// Set ListBlob options
			$options = new ListBlobsOptions();
			$options->setIncludeMetadata(true);
			$options->setMaxResults(10);
			
			// List blobs.
			$blob_list = $blobClient->listBlobs($this->CONTAINER_NAME, $options);
			$blobs = $blob_list->getBlobs();
		} catch (ServiceException $e) {
			$code = $e->getCode();
			$error_message = $e->getMessage();
			echo $code.": ".$error_message."<br />";
		}

		return $blobs;
	}

	/**
	 * Gets blobs from container by search query
	 * @return void
	 */
	private function getBlobsSearch($query)
	{
		$searchUri = "https://searchcontrolleddocuments.search.windows.net/indexes/azureblob-index/docs?search=" . $query . "&api-version=2016-09-01";

		$client = new \GuzzleHttp\Client();

		$result = $client->request('GET', $searchUri, [
			'headers' => [
				'api-key' => '1570F8A4B2C7F2D6739EA20327335175',
				'Content-Type' => 'application/json'
			]
		]);

		$body = json_decode($result->getBody()->getContents())->value;

		$blobs = [];

		foreach ($body as $blob) {
			array_push($blobs, base64_decode($blob->metadata_storage_path));
		}

		var_dump($blobs);

		return [];
	}
}