<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://codeies.com
 * @since      1.0.0
 *
 * @package    Esim_Physicalsim
 * @subpackage Esim_Physicalsim/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Esim_Physicalsim
 * @subpackage Esim_Physicalsim/admin
 * @author     Codeies Pvt Ltd <contact@codeies.com>
 */
class Esim_Physicalsim_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Esim_Physicalsim_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Esim_Physicalsim_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/esim-physicalsim-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Esim_Physicalsim_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Esim_Physicalsim_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/esim-physicalsim-admin.js', array( 'jquery' ), $this->version, false );

	}

	public function register_menupages(){
			add_menu_page(
				__('eSIM & PhysicalSim','esim-physicalsim'), // Page title.
				__('eSIM & PhysicalSim','esim-physicalsim'),        // Menu title.
				'manage_options',                                         // Capability.
				'esim-physicalsim',                                             // Menu slug.
				[$this,'esim_physicalsim_page']                                    // Callback function.
			);

	}
	public function esim_physicalsim_page(){
		require ESIM_PHYSICALSIM_DIR.'/admin/includes/esim-physicalsim-table.php';

		$table = new Esim_Physicalsim_List_Table();
		// Fetch, prepare, sort, and filter our data.
		$table->prepare_items();
		if(isset($_POST['hidden_field'])){
			$this->ajax_upload();
			require ESIM_PHYSICALSIM_DIR.'/admin/includes/esim-physicalsim-main-class.php'; 
			Esim_Physicalsim_MAIN::update_stocks();
		}
		// Include the view markup. ?>
		<div class="wrap">
		    <h1 class="wp-heading-inline"><?php echo esc_html( get_admin_page_title() ); ?></h1>
		    <!-- <a href="http://localhost/wordpress/wp-admin/post-new.php" class="page-title-action">UPLOAD PINS</a> -->
		    <hr class="wp-header-end">
			 <div class="row esim-physicalsim-importform">
	            <div class="panel panel-default">
	                <div class="panel-heading mt-5">
		                    <h3 class="panel-title">Import CSV File Data</h3>
		                </div>
		                <div class="panel-body">
		                    <span id="message"></span>
		                    <form id="esim-physicalsimImport" method="POST" class="form-inline" enctype="multipart/form-data">
		                        <div class="form-group">
		                            <label>Select CSV File</label>
		                            <input type="file" name="csv" id="file" accept=".xlsx, .xls, .csv">
		                        </div>
		                        <div class="form-group" >
		                            <input type="hidden" name="hidden_field" value="1">
		                            <input type="submit" name="import" id="import" class="button" value="Import">
		                        </div>
		                    </form>
		                    <div class="form-group" id="process" style="display: none;">
		                        <div class="progress">
		                            <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
		                                <span id="process_data">0</span> - <span id="total_data">0</span>
		                            </div>
		                        </div>
		                    </div>
		                </div>
		            </div>
		        </div>
		    <hr class="wp-header-end">
			<?php $table->views(); ?>
		    <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
		    <form id="movies-filter" method="get">
		        <!-- For plugins, we also need to ensure that the form posts back to our current page -->
		        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
		        <!-- Now we can render the completed list table -->
		        <?php $table->display() ?>
		    </form>

		</div>
	<?php 	
	}
	public function  do_insert($place_holders, $values) {

		   global $wpdb;
		   $table_name = $wpdb->prefix . "codeies_esim_physicalsim";
		    $query  = "INSERT INTO $table_name (`type`,`sku`,`sim_or_serial_num`,`pin_or_confirmation`,`activation_url`,`name`,`short_description`,`description`,`enabled`
) VALUES ";
		    $query           .= implode( ', ', $place_holders );
		    $sql             = $wpdb->prepare( "$query ", $values );
		    //print_r($values);		 
		       if ( $wpdb->query( $sql ) ) {
		        header("Refresh: 0;"); 
		    } else {
		        echo '<h2>Failed to import</h2>';
		    }

		}

	public function ajax_upload(){
		$csv = array();
		if(isset($_FILES['csv']['tmp_name']) && !empty($_FILES['csv']['tmp_name'])):
			$tmpName = $_FILES['csv']['tmp_name'];
			$records = array();
			$csv_data = array_map('str_getcsv', file($tmpName));
			if(is_array($csv_data)){
				foreach (array_slice($csv_data, 1) as $data) {
					if(empty($data[1]))
						continue;
					/*if(empty($data[5])){
						$data[5] = 'null';
					}*/
					$records[] = array(
						'type'=> $data[1],
						'sku'=> $data[2],
						'sim_or_serial_num'=> $data[3],
						'pin_or_confirmation'=> $data[4],
						'activation_url'=> $data[5],
						'name'=> $data[6],
						'short_description'=> $data[7],
						'description'=> $data[8],
						'order_id'=> '',
						'enabled'=> '1'
					);
				}
			$values = $place_holders = array();
				if(count($records) > 0) {
				    foreach($records as $data) {
				        array_push( $values, 
				        	$data['type'],
							$data['sku'],
							$data['sim_or_serial_num'],
							$data['pin_or_confirmation'],
							$data['activation_url'],
							$data['name'],
							$data['short_description'],
							$data['description'],
							$data['enabled']
				        );
				        $place_holders[] = "( %s,%s,%s,%s,%s, %s, %s, %s, %s)";
				    }

				    $this->do_insert( $place_holders, $values );
				}
			}else{
				// Failed
			}
		endif;
	}	

}
