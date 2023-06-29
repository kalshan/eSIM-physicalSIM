<?php
class Esim_Physicalsim_Woocommerce {
	protected $table = ESIMPHYSICALSIM_TABLE;

	function __construct(){
		global $wpdb;
		add_filter( 'woocommerce_order_actions', [$this,'resend_email_action'], 10, 1 );
		add_filter( 'woocommerce_order_action_esimevoucer_resend_email', [$this,'esimevoucer_resend_email_handler'], 10, 1 );
		
		add_action( 'woocommerce_new_product', [$this,'update_stock'], 10, 1 );
		add_action( 'woocommerce_update_product', [$this,'update_stock'], 10, 1 );
		//add_action('init',[$this,'order_completed']);

	}
	public function update_stock(){
		require_once(ESIM_PHYSICALSIM_DIR.'/admin/includes/esim-physicalsim-main-class.php'); 
		Esim_Physicalsim_MAIN::update_stocks();
	}
	public function resend_email_action($actions){
	   global $theorder;
	    $order_id = $theorder->get_id();
	    $purchase_time = get_post_meta( $order_id, '_esimphysicalsim_purchase_time', true );
	 
	    if ($purchase_time ) {
	        $actions['esimevoucer_resend_email'] = __( 'Resend Esim/Physicalsim Email', 'esim-physicalsim' );
	    }
	 
	    return $actions;
	}
	public function esimevoucer_resend_email_handler( $order ){
		 $order_id = $order->get_id();
    	$order = wc_get_order( $order_id );
		$items = $order->get_items();
		$to = $order->get_billing_email();
		
		require ESIM_PHYSICALSIM_DIR.'/admin/includes/esim-physicalsim-main-class.php'; 
		if($order->get_items())
		foreach ( $order->get_items() as $item ) {

		    // Compatibility for woocommerce 3+
		    $product_id = version_compare( WC_VERSION, '3.0', '<' ) ? $item['product_id'] : $item->get_product_id();

		    // Here you get your data
		    $esim_physicalsim_sku = get_post_meta( $product_id, '_esimphysicalsim_sku', true); 
		    if(empty($esim_physicalsim_sku))
		    	continue;
		    $product = Esim_Physicalsim_MAIN::getItemfromSKU($esim_physicalsim_sku,$isEnabled = 0,$trashed = [0,1]);

		    Esim_Physicalsim_MAIN::sendMail($to,$product);

		}

	}
	public function product_esim_physicalsim_fields(){
		global $woocommerce, $wpdb;
		echo '<div class=" product_esim_physicalsim_field ">';
		   global $post;


		    $value = get_post_meta( $post->ID, '_esimphysicalsim_sku',true );
		    //print_r($value);
		    if( empty( $value ) ) $value = '';


		    $options[''] = __( 'Select a value', 'woocommerce'); // default value
		    $items  = $wpdb->get_results("SELECT `sku`,`name` FROM $this->table WHERE `enabled` = 1", ARRAY_A); 
		    foreach ($items as $option) {
		    	$options[$option['sku']] = $option['name'];
		    }

		    echo '<div class="options_group">';

		    woocommerce_wp_select( array(
		        'id'      => '_esimphysicalsim_sku',
		        'label'   => __( 'ESIM Physical-Sim Product', 'woocommerce' ),
		        'options' =>  $options, //this is where I am having trouble
		        'value'   => $value,
		    ) );

		    echo '</div>';
		echo '</div>';
	}	
	public function product_esim_physicalsim_fields_save($post_id){
		    $woocommerce_esimphysicalsim_sku = sanitize_text_field($_POST['_esimphysicalsim_sku']);
		    if (!empty($woocommerce_esimphysicalsim_sku))
		        update_post_meta($post_id, '_esimphysicalsim_sku', $woocommerce_esimphysicalsim_sku);
	}
	public function order_completed($order_id){
		//$order_id = 29;
		$order = wc_get_order( $order_id );
		$items = $order->get_items();

		require ESIM_PHYSICALSIM_DIR.'/admin/includes/esim-physicalsim-main-class.php'; 
		if($order->get_items())
		foreach ( $order->get_items() as $item ) {

		    // Compatibility for woocommerce 3+
		    $product_id = version_compare( WC_VERSION, '3.0', '<' ) ? $item['product_id'] : $item->get_product_id();

		    // Here you get your data
		    $esim_physicalsim_sku = get_post_meta( $product_id, '_esimphysicalsim_sku', true); 
		    if(empty($esim_physicalsim_sku))
		    	continue;

		    Esim_Physicalsim_MAIN::purchaseSKU($esim_physicalsim_sku,$order_id);
		    Esim_Physicalsim_MAIN::update_stocks();

		}
	}
}