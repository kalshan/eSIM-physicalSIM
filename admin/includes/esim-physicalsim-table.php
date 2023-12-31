<?php
class Esim_Physicalsim_List_Table extends WP_List_Table {

	/**
	 * ***********************************************************************
	 * Normally we would be querying data from a database and manipulating that
	 * for use in your list table. For this example, we're going to simplify it
	 * slightly and create a pre-built array. Think of this as the data that might
	 * be returned by $wpdb->query()
	 *
	 * In a real-world scenario, you would run your own custom query inside
	 * the prepare_items() method in this class.
	 *
	 * @var array
	 * ************************************************************************
	 */
	protected $table = '';
	/**
	 * Esim_Physicalsim_List_Table constructor.
	 *
	 * REQUIRED. Set up a constructor that references the parent constructor. We
	 * use the parent reference to set some default configs.
	 */
	public function __construct() {
		global $wpdb;
		// Set parent defaults.
		$this->table = ESIMPHYSICALSIM_TABLE;
		parent::__construct( array(
			'singular' => 'pin',     // Singular name of the listed records.
			'plural'   => 'pins',    // Plural name of the listed records.
			'ajax'     => false,       // Does this table support ajax?
		) );
	}
	public function get_views(){
		   $views = array();
		   $current = ( !empty($_REQUEST['type']) ? $_REQUEST['type'] : 'all');

		   //All link
		   $class = ($current == 'all' ? ' class="current"' :'');
		   $all_url = remove_query_arg('type');
		   $views['all'] = "<a href='{$all_url }' {$class} >All</a>";

		   //Foo link
		   $esim_url = add_query_arg('type','SIM');
		   $class = ($current == 'SIM' ? ' class="current"' :'');
		   $views['SIM'] = "<a href='{$esim_url}' {$class} >ESIM</a>";

		   //Bar link
		   $physicalsim = add_query_arg('type','Physical-Sim');
		   $class = ($current == 'Physical-Sim' ? ' class="current"' :'');
		   $views['Physical-Sim'] = "<a href='{$physicalsim}' {$class} >Physical-Sim</a>";

		   //Bar link
		   $used = add_query_arg('type','used');
		   $class = ($current == 'used' ? ' class="current"' :'');
		   $views['used'] = "<a href='{$used}' {$class} >Used</a>";  

		   $trash = add_query_arg('type','trash');
		   $class = ($current == 'trash' ? ' class="current"' :'');
		   $views['trash'] = "<a href='{$trash}' {$class} >Trash</a>";

		   return $views;
		}

    private function get_table_data( $search = '' ) {
        global $wpdb;

        $where = $args = [];
        if ( isset( $_REQUEST['type'] ) && !empty( $_REQUEST['type'] ) && ( $_REQUEST['type'] != '0' ) ) {
        	$type = sanitize_text_field($_REQUEST['type']);
            $args[] = $_REQUEST['type'];
            if($type == 'used'){
        		 $where[] = 'enabled=0';
        	}elseif($type == 'trash'){
        		$where[] = 'trashed=1'; // Enabled 2 is Trash
        	}
        	else{
          		  $where[] = 'type="'.$type.'" AND trashed = 0';
        	}
        }else{
        	$where[] = 'trashed = 0';
        }
        $where = !empty( $where ) ? ' WHERE ' . implode(' AND ', $where) : '';

        if ( !empty($search) ) {
            return $wpdb->get_results(
                "SELECT * from {$this->table} WHERE name Like '%{$search}%' OR description Like '%{$search}%' OR status Like '%{$search}%'",
                ARRAY_A
            );
        } else {
            return $wpdb->get_results(
                "SELECT {$this->table}.* from {$this->table} $where
                 ",
                ARRAY_A
            );
        }
    }
	/**
	 * Get a list of columns. The format is:
	 * 'internal-name' => 'Title'
	 *
	 * REQUIRED! This method dictates the table's columns and titles. This should
	 * return an array where the key is the column slug (and class) and the value
	 * is the column's title text. If you need a checkbox for bulk actions, refer
	 * to the $columns array below.
	 *
	 * The 'cb' column is treated differently than the rest. If including a checkbox
	 * column in your table you must create a `column_cb()` method. If you don't need
	 * bulk actions or checkboxes, simply leave the 'cb' entry out of your array.
	 *
	 * @see WP_List_Table::::single_row_columns()
	 * @return array An associative array containing column information.
	 */
	public function get_columns() {
		$columns = array(
			'cb'       => '<input type="checkbox" />', // Render a checkbox instead of text.
			'name'    => _x( 'Package', 'Package', 'esim-physicalsim' ),
			'pin_or_confirmation'    => _x( 'Confirmation Code / PIN', 'Column label', 'esim-physicalsim' ),
			'purchase_order'    => _x( 'Purchase Order', 'Column label', 'esim-physicalsim' ),
			'serial_number'   => _x( 'Serial Number', 'Column label', 'esim-physicalsim' ),
			'type' => _x( 'Category', 'Column label', 'esim-physicalsim' ),
			'recepent' => _x( 'Recepent', 'Column label', 'esim-physicalsim' ),
			'notes' => _x( 'Notes', 'Column label', 'esim-physicalsim' ),
			'enabled' => _x( 'Enabled', 'Column label', 'esim-physicalsim' ),
		);

		return $columns;
	}

	/**
	 * Get a list of sortable columns. The format is:
	 * 'internal-name' => 'orderby'
	 * or
	 * 'internal-name' => array( 'orderby', true )
	 *
	 * The second format will make the initial sorting order be descending
	 *
	 * Optional. If you want one or more columns to be sortable (ASC/DESC toggle),
	 * you will need to register it here. This should return an array where the
	 * key is the column that needs to be sortable, and the value is db column to
	 * sort by. Often, the key and value will be the same, but this is not always
	 * the case (as the value is a column name from the database, not the list table).
	 *
	 * This method merely defines which columns should be sortable and makes them
	 * clickable - it does not handle the actual sorting. You still need to detect
	 * the ORDERBY and ORDER querystring variables within `prepare_items()` and sort
	 * your data accordingly (usually by modifying your query).
	 *
	 * @return array An associative array containing all the columns that should be sortable.
	 */
	protected function get_sortable_columns() {
		$sortable_columns = array(
			'code'    => array( 'code', false ),
			'rating'   => array( 'rating', false ),
			'director' => array( 'director', false ),
		);

		return $sortable_columns;
	}

	/**
	 * Get default column value.
	 *
	 * Recommended. This method is called when the parent class can't find a method
	 * specifically build for a given column. Generally, it's recommended to include
	 * one method for each column you want to render, keeping your package class
	 * neat and organized. For example, if the class needs to process a column
	 * named 'title', it would first see if a method named $this->column_title()
	 * exists - if it does, that method will be used. If it doesn't, this one will
	 * be used. Generally, you should try to use custom column methods as much as
	 * possible.
	 *
	 * Since we have defined a column_title() method later on, this method doesn't
	 * need to concern itself with any column with a name of 'title'. Instead, it
	 * needs to handle everything else.
	 *
	 * For more detailed insight into how columns are handled, take a look at
	 * WP_List_Table::single_row_columns()
	 *
	 * @param object $item        A singular item (one full row's worth of data).
	 * @param string $column_name The name/slug of the column to be processed.
	 * @return string Text or HTML to be placed inside the column <td>.
	 */
	protected function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'name':
			case 'type':
			case 'pin_or_confirmation':
				return $item[ $column_name ];
			default:
				//return print_r( $item, true ); // Show the whole array for troubleshooting purposes.
		}
	}	
	protected function column_purchase_order( $item ) {
		if(!empty($item['order_id']))
		return '<a target="_blank" href="post.php?post='.$item['order_id'].'&action=edit">#'.$item['order_id'].'</a> by '.get_post_meta($item['order_id'],'_billing_first_name',true).' '.get_post_meta($item['order_id'],'_billing_last_name',true);
	}	

	protected function column_recepent( $item ) {
		if(!empty($item['order_id'])){
			return get_post_meta($item['order_id'],'_billing_email',true).'<br> Sent on '.get_post_meta($item['order_id'],'_esimphysicalsim_purchase_time',true);
		}
		else{
			return '<span class="esimphysicalsim-info-message">Not yet sent</span>';
		}
	}	
	protected function column_enabled( $item ) {
		return '<input type="checkbox" disabled name="enabled" value="1" ' . checked( 1, $item['enabled'], false ) . '/>'; 
	}

	/**
	 * Get value for checkbox column.
	 *
	 * REQUIRED if displaying checkboxes or using bulk actions! The 'cb' column
	 * is given special treatment when columns are processed. It ALWAYS needs to
	 * have it's own method.
	 *
	 * @param object $item A singular item (one full row's worth of data).
	 * @return string Text to be placed inside the column <td>.
	 */
	protected function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="%1$s[]" value="%2$s" />',
			$this->_args['singular'],  // Let's simply repurpose the table's singular label ("movie").
			$item['ID']                // The value of the checkbox should be the record's ID.
		);
	}

	/**
	 * Get title column value.
	 *
	 * Recommended. This is a custom column method and is responsible for what
	 * is rendered in any column with a name/slug of 'title'. Every time the class
	 * needs to render a column, it first looks for a method named
	 * column_{$column_title} - if it exists, that method is run. If it doesn't
	 * exist, column_default() is called instead.
	 *
	 * This example also illustrates how to implement rollover actions. Actions
	 * should be an associative array formatted as 'slug'=>'link html' - and you
	 * will need to generate the URLs yourself. You could even ensure the links are
	 * secured with wp_nonce_url(), as an expected security measure.
	 *
	 * @param object $item A singular item (one full row's worth of data).
	 * @return string Text to be placed inside the column <td>.
	 */
	protected function column_name( $item ) {
		$page = wp_unslash( $_REQUEST['page'] ); // WPCS: Input var ok.

		// Build edit row action.
		$edit_query_args = array(
			'page'   => $page,
			'action' => 'edit',
			'movie'  => $item['ID'],
		);

		$actions['edit'] = sprintf(
			'<a href="%1$s">%2$s</a>',
			esc_url( wp_nonce_url( add_query_arg( $edit_query_args, 'admin.php' ), 'editmovie_' . $item['ID'] ) ),
			_x( 'Edit', 'List table row action', 'esim-physicalsim' )
		);

		// Build delete row action.
		$delete_query_args = array(
			'page'   => $page,
			'action' => 'delete',
			'pin'  => $item['ID'],
		);
		$trash_query_args = array(
			'page'   => $page,
			'action' => 'trash',
			'pin'  => $item['ID'],
		);
		$restore_query_args = array(
			'page'   => $page,
			'action' => 'restore',
			'pin'  => $item['ID'],
		);

		if(isset($_GET['type']) && $_GET['type'] == 'trash'){
				$actions['delete'] = sprintf(
			'<a href="%1$s">%2$s</a>',
			esc_url( wp_nonce_url( add_query_arg( $delete_query_args, 'admin.php' ), 'deletemovie_' . $item['ID'] ) ),
			_x( 'Delete', 'List table row action', 'esim-physicalsim' )
		);	
				$actions['restore'] = sprintf(
			'<a href="%1$s">%2$s</a>',
			esc_url( wp_nonce_url( add_query_arg( $restore_query_args, 'admin.php' ), 'deletemovie_' . $item['ID'] ) ),
			_x( 'Restore', 'List table row action', 'esim-physicalsim' )
		);
		}else{
				$actions['trash'] = sprintf(
			'<a href="%1$s">%2$s</a>',
			esc_url( wp_nonce_url( add_query_arg( $trash_query_args, 'admin.php' ), 'deletemovie_' . $item['ID'] ) ),
			_x( 'Trash', 'List table row action', 'esim-physicalsim' )
		);
		}
	

		// Return the title contents.
		return sprintf( '%1$s <span style="color:silver;">(id:%2$s)</span>%3$s',
			$item['name'],
			$item['ID'],
			$this->row_actions( $actions )
		);
	}

	protected function column_pin_or_confirmation($item){
		if($item['type'] == "Physical-Sim"){
			return $item['pin_or_confirmation'];
		}else{
			return $item['activation_url'];
		}
	}	
	protected function column_serial_number($item){
			return $item['sim_or_serial_num'];

	}
	/**
	 * Get an associative array ( option_name => option_title ) with the list
	 * of bulk actions available on this table.
	 *
	 * Optional. If you need to include bulk actions in your list table, this is
	 * the place to define them. Bulk actions are an associative array in the format
	 * 'slug'=>'Visible Title'
	 *
	 * If this method returns an empty value, no bulk action will be rendered. If
	 * you specify any bulk actions, the bulk actions box will be rendered with
	 * the table automatically on display().
	 *
	 * Also note that list tables are not automatically wrapped in <form> elements,
	 * so you will need to create those manually in order for bulk actions to function.
	 *
	 * @return array An associative array containing all the bulk actions.
	 */
	protected function get_bulk_actions() {
		$actions = array();

		if(isset($_GET['type']) && $_GET['type']  == 'trash'){
			$actions['delete'] = _x( 'Delete', 'List table bulk action', 'esim-physicalsim' );
			$actions['restore'] = _x( 'Restore', 'List table bulk action', 'esim-physicalsim' );
		}else{
			$actions['trash'] = _x( 'Trash', 'List table bulk action', 'esim-physicalsim' );
		}
		return $actions;
	}

	/**
	 * Handle bulk actions.
	 *
	 * Optional. You can handle your bulk actions anywhere or anyhow you prefer.
	 * For this example package, we will handle it in the class to keep things
	 * clean and organized.
	 *
	 * @see $this->prepare_items()
	 */
	protected function process_bulk_action() {
		global $wpdb;
		// Detect when a bulk action is being triggered.
		if ( 'delete' === $this->current_action() ) {
			if(is_array($_GET['pin'])){
				$ids = implode(', ', $_GET['pin']);
			}
			else
			{
			$ids = (int)$_GET['pin'];
			}

			$delete_error = $wpdb->query("DELETE FROM $this->table WHERE ID IN($ids)");

			$sendback = remove_query_arg( array( 'action', 'action2', '_wpnonce', '_wp_http_referer', 'bulk-item-selection', 'delete_id', 'updated' ), wp_get_referer() );

			if ( false === $delete_error ) {

				$sendback = add_query_arg( 'deleted', 'error', $sendback );

			} else {

				$sendback = add_query_arg( 'deleted', 'success', $sendback );

			}
			require_once ESIM_PHYSICALSIM_DIR.'/admin/includes/esim-physicalsim-main-class.php'; 
			Esim_Physicalsim_MAIN::update_stocks();
			
			wp_redirect( $sendback );
			exit();
		}	
		if ( 'trash' === $this->current_action() || 'restore' === $this->current_action()) {
		    if(is_array($_GET['pin'])){
		        $ids = implode(', ', $_GET['pin']);
		    }
		    else {
		        $ids = (int)$_GET['pin'];
		    }
		    if('trash' === $this->current_action()){
		    	$trash = 1;
		    }else{
		    	$trash = 0;
		    }
		    $update_error = $wpdb->query("UPDATE $this->table SET trashed = $trash WHERE ID IN($ids)");

		    $sendback = remove_query_arg( array( 'action', 'action2', '_wpnonce', '_wp_http_referer', 'bulk-item-selection', 'delete_id', 'updated' ), wp_get_referer() );

		    if ( false === $update_error ) {
		        $sendback = add_query_arg( 'updated', 'error', $sendback );
		    } else {
		        $sendback = add_query_arg( 'updated', 'success', $sendback );
		    }

		    require_once ESIM_PHYSICALSIM_DIR.'/admin/includes/esim-physicalsim-main-class.php'; 
		    Esim_Physicalsim_MAIN::update_stocks();
		    
		    wp_redirect( $sendback );
		    exit();
		}
	}

	/**
	 * Prepares the list of items for displaying.
	 *
	 * REQUIRED! This is where you prepare your data for display. This method will
	 * usually be used to query the database, sort and filter the data, and generally
	 * get it ready to be displayed. At a minimum, we should set $this->items and
	 * $this->set_pagination_args(), although the following properties and methods
	 * are frequently interacted with here.
	 *
	 * @global wpdb $wpdb
	 * @uses $this->_column_headers
	 * @uses $this->items
	 * @uses $this->get_columns()
	 * @uses $this->get_sortable_columns()
	 * @uses $this->get_pagenum()
	 * @uses $this->set_pagination_args()
	 */
	function prepare_items() {
		global $wpdb; //This is used only if making any database queries

		/*
		 * First, lets decide how many records per page to show
		 */
		$per_page = 20;

		/*
		 * REQUIRED. Now we need to define our column headers. This includes a complete
		 * array of columns to be displayed (slugs & titles), a list of columns
		 * to keep hidden, and a list of columns that are sortable. Each of these
		 * can be defined in another method (as we've done here) before being
		 * used to build the value for our _column_headers property.
		 */
		$columns  = $this->get_columns();
		$hidden   = array();
		$sortable = $this->get_sortable_columns();

		/*
		 * REQUIRED. Finally, we build an array to be used by the class for column
		 * headers. The $this->_column_headers property takes an array which contains
		 * three other arrays. One for all columns, one for hidden columns, and one
		 * for sortable columns.
		 */
		$this->_column_headers = array( $columns, $hidden, $sortable );

		/**
		 * Optional. You can handle your bulk actions however you see fit. In this
		 * case, we'll handle them within our package just to keep things clean.
		 */
		$this->process_bulk_action();

		/*
		 * GET THE DATA!
		 * 
		 * Instead of querying a database, we're going to fetch the example data
		 * property we created for use in this plugin. This makes this example
		 * package slightly different than one you might build on your own. In
		 * this example, we'll be using array manipulation to sort and paginate
		 * our dummy data.
		 * 
		 * In a real-world situation, this is probably where you would want to 
		 * make your actual database query. Likewise, you will probably want to
		 * use any posted sort or pagination data to build a custom query instead, 
		 * as you'll then be able to use the returned query data immediately.
		 *
		 * For information on making queries in WordPress, see this Codex entry:
		 * http://codex.wordpress.org/Class_Reference/wpdb
		 */
		$data = $this->get_table_data();

		/*
		 * This checks for sorting input and sorts the data in our array of dummy
		 * data accordingly (using a custom usort_reorder() function). It's for 
		 * example purposes only.
		 *
		 * In a real-world situation involving a database, you would probably want
		 * to handle sorting by passing the 'orderby' and 'order' values directly
		 * to a custom query. The returned data will be pre-sorted, and this array
		 * sorting technique would be unnecessary. In other words: remove this when
		 * you implement your own query.
		 */
		usort( $data, array( $this, 'usort_reorder' ) );

		/*
		 * REQUIRED for pagination. Let's figure out what page the user is currently
		 * looking at. We'll need this later, so you should always include it in
		 * your own package classes.
		 */
		$current_page = $this->get_pagenum();

		/*
		 * REQUIRED for pagination. Let's check how many items are in our data array.
		 * In real-world use, this would be the total number of items in your database,
		 * without filtering. We'll need this later, so you should always include it
		 * in your own package classes.
		 */
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
		$orderby = ! empty( $_REQUEST['orderby'] ) ? wp_unslash( $_REQUEST['orderby'] ) : 'ID'; // WPCS: Input var ok.

		// If no order, default to asc.
		$order = ! empty( $_REQUEST['order'] ) ? wp_unslash( $_REQUEST['order'] ) : 'asc'; // WPCS: Input var ok.

		// Determine sort order.
		$result = strcmp( $a[ $orderby ], $b[ $orderby ] );

		return ( 'asc' === $order ) ? $result : - $result;
	}
}