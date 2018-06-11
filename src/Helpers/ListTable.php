<?php

namespace Halland\Helpers;

use Halland\Helpers\WpLIstTable;
use Halland\Helpers\Azure;

class ListTable extends WpLIstTable {

	protected $data;

	public function __construct($data) {
		$this->data = $data;

		// Set parent defaults.
		parent::__construct( array(
			'singular' => 'movie',     // Singular name of the listed records.
			'plural'   => 'movies',    // Plural name of the listed records.
			'ajax'     => false,       // Does this table support ajax?
		) );
	}

	public function get_columns() {
		$columns = array(
			'title' => _x( 'Title', 'Column label', 'wp-list-table-example' ),
			'url'   => _x( 'Url', 'Column label', 'wp-list-table-example' ),
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
		return sprintf( '%1$s', $item->getUrl());
	}

	protected function column_title( $item ) {
		return sprintf( '%1$s', $item->getName());
	}

	protected function process_bulk_action() {
		// Detect when a bulk action is being triggered.
		if ( 'delete' === $this->current_action() ) {
			wp_die( 'Items deleted (or they would be if we had items to delete)!' );
		}
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
