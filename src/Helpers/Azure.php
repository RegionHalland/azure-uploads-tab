<?php

namespace Halland;

class Azure
{
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
}