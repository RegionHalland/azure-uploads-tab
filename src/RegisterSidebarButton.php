<?php

namespace Halland;
use Philo\Blade\Blade;
use WindowsAzure\Common\ServicesBuilder;
use MicrosoftAzure\Storage\Blob\Models\CreateBlobOptions;


class RegisterSidebarButton
{
    public function __construct()
    {
        $this->views = __DIR__ . '/views';
        $this->cache = __DIR__ . '/cache';
        $this->addTab();
    }

    public function addTab()
    {
        add_action('admin_menu', function() {
	        add_menu_page( "page_title", "Region Halland Azure", 'capability', 'menu_slug', array($this, 'blobFileList'));
        });

        add_filter('media_upload_tabs', function ($tabs) {
            unset($tabs["type_url"]);
            unset($tabs['library']);
            $newtab = array('ell_insert_gmap_tab' => __('Styrda Dokument', 'insertgmap'));

            return array_merge($tabs, $newtab);
        });

        add_action('media_upload_ell_insert_gmap_tab', function () {
            return wp_iframe(array($this, 'blobFileList'));
        });
    }

    public function blobFileList()
    {
        $blobs = array();
        $connectionString = "DefaultEndpointsProtocol=https;AccountName=[enternamehere];AccountKey=[enteraccountkeyhere]";
        $blobRestProxy = ServicesBuilder::getInstance()->createBlobService($connectionString);

        try {
            // List all blobs.
            $blob_list = $blobRestProxy->listBlobs("styrdadokument");
            $blobs = $blob_list->getBlobs();
            foreach ($blobs as $blob) {
                array_push($blobs, $blob);
            }
        } catch (ServiceException $e) {
            $code = $e->getCode();
            $error_message = $e->getMessage();
            echo $code.": ".$error_message."<br />";
        }

        $blade = new Blade($this->views, $this->cache);

        echo $blade->view()->make('hello')->with('blobs', $blobs)->render();
    }
}
