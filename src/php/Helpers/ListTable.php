<?php

namespace AzureUploadsTab\Helpers;

use AzureUploadsTab\Helpers\WpListTable;
use AzureUploadsTab\Helpers\Azure;

class ListTable extends WpListTable {

	protected $data;
	protected $openWebViewerUrl = 'http://view.officeapps.live.com/op/view.aspx?src=';

	public function __construct($data) {
		$this->data = $data;

		// Set parent defaults.
		parent::__construct(array(
			'singular' => 'blob', 
			'plural'   => 'blobs',
			'ajax'     => false,  
		));
	}

	public function get_columns() {
		$columns = array(
			'title' => 'Title',
			'url'   => 'Url'
		);
		return $columns;
	}


	protected function get_sortable_columns() {
		$sortable_columns = array(
			'title' => array( 'title', false )
		);

		return $sortable_columns;
	}

	protected function column_default( $item, $column_name ) {
		$url = str_replace(' ', '%20', $this->openWebViewerUrl . $item->getUrl());
		$etag = $item->getProperties()->getETag();

		return sprintf(
			'<div class="aut__td">
				<input class="aut__input" type="text" id="az%2$s" value="%1$s" readonly>
				<button class="button button-primary aut__copy-btn" data-clipboard-target="#az%2$s">Kopiera URL</button>
			</div>',
			$url,
			$etag
		);
	}

	protected function column_title( $item ) {
		$url = str_replace(' ', '%20', $this->openWebViewerUrl . $item->getUrl());
		$name = $item->getName();

		return sprintf( 
			'<a target=_blank href="%1$s">%2$s</a>',  
			$url,
			$name
		);
	}


	function prepare_items() {
		$per_page = 10;

		$columns  = $this->get_columns();
		$hidden   = array();
		$sortable = $this->get_sortable_columns();

		$this->_column_headers = array( $columns, $hidden, $sortable );
		$this->process_bulk_action();

		$data = $this->data;

		usort( $data, array( $this, 'usort_reorder' ) );

		$current_page = $this->get_pagenum();
		$total_items = count( $data );

		/*
		 * The WP_List_Table class does not handle pagination for us, so we need
		 * to ensure that the data is trimmed to only the current page. We can use
		 * array_slice() to do that.
		 */
		$data = array_slice( $data, ( ( $current_page - 1 ) * $per_page ), $per_page );

		/*
		 * REQUIRED. Now we can add our *sorted* data to the items property, where
		 * it can be used by the rest of the class.
		 */
		$this->items = $data;

		/**
		 * REQUIRED. We also have to register our pagination options & calculations.
		 */
		$this->set_pagination_args( array(
			'total_items' => $total_items,                     // WE have to calculate the total number of items.
			'per_page'    => $per_page,                        // WE have to determine how many items to show on a page.
			'total_pages' => ceil( $total_items / $per_page ), // WE have to calculate the total number of pages.
		) );
	}

	/**
	 * Callback to allow sorting of example data.
	 *
	 * @param string $a First value.
	 * @param string $b Second value.
	 *
	 * @return int
	 */
	protected function usort_reorder( $a, $b ) {
		// If no sort, default to title.
		$orderby = !empty( $_REQUEST['orderby'] ) ? wp_unslash( $_REQUEST['orderby'] ) : 'title';

		// If no order, default to asc.
		$order = !empty( $_REQUEST['order'] ) ? wp_unslash( $_REQUEST['order'] ) : 'asc';

		// Determine sort order.
		$result = strcmp( $a->getName(), $b->getName() );

		return ( 'asc' === $order ) ? $result : - $result;
	}
}
