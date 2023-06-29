<?php
class Esim_Physicalsim_MAIN {

	static public function getItemfromSKU($sku,$isenabled = 1,$trashed = [0]){
		global $wpdb;
		$trashed = implode(',', $trashed);
		return $wpdb->get_row("SELECT * FROM ".ESIMPHYSICALSIM_TABLE." WHERE enabled = '$isenabled' AND `trashed` IN ($trashed) AND `sku` = '$sku' ", ARRAY_A);
	}	

	static public function updateSKU($pinID,$orderID){
		global $wpdb;
		return $wpdb->update(ESIMPHYSICALSIM_TABLE, array( 'enabled' => '0', 'order_id' => $orderID ), array( 'ID' => $pinID ) );
	}
static public function update_stocks() {
    global $wpdb;
    $voucher_table = ESIMPHYSICALSIM_TABLE;
    $product_meta_key = '_esimphysicalsim_sku';

    // Get the counts of enabled vouchers for each product
    $results = $wpdb->get_results("
        SELECT $wpdb->postmeta.post_id as product_id, COUNT(*) as count
        FROM $voucher_table
        JOIN $wpdb->postmeta ON $voucher_table.sku = $wpdb->postmeta.meta_value
        WHERE $voucher_table.enabled = '1' AND $voucher_table.trashed = '0' AND $wpdb->postmeta.meta_key = '$product_meta_key'
        GROUP BY $wpdb->postmeta.post_id
    ");

    // Map the product IDs to their corresponding voucher counts
    $product_counts = [];
    foreach ($results as $result) {
        $product_counts[$result->product_id] = $result->count;
    }

    // Update the stock count and stock status for all products that have the _esimphysicalsim_sku meta key
    $product_ids = $wpdb->get_col("
        SELECT DISTINCT $wpdb->postmeta.post_id
        FROM $wpdb->postmeta
        WHERE $wpdb->postmeta.meta_key = '$product_meta_key'
    ");

    foreach ($product_ids as $product_id) {
        if (isset($product_counts[$product_id])) {
            $count = $product_counts[$product_id];
            update_post_meta($product_id, '_stock', $count);
            update_post_meta($product_id, '_stock_status', $count < 1 ? 'outofstock' : 'instock');
        } else {
            update_post_meta($product_id, '_stock', 0);
            update_post_meta($product_id, '_stock_status', 'outofstock');
        }
    }
}


	static public function purchaseSKU($sku,$orderID){
		//self::update_stocks();
		//die();
		$isPurchased = get_post_meta($orderID,'_esimphysicalsim_purchase_time',true);
		if(isset($isPurchased) && !empty($isPurchased))
				return; // Already Sent to customer

		$product = self::getItemfromSKU($sku);

		$update = self::updateSKU($product['ID'],$orderID);
		if($update){
			$to = get_post_meta($orderID,'_billing_email',true);
			$current_datetime = current_datetime()->format('Y-m-d H:i:s');
			

			update_post_meta($orderID,'_esimphysicalsim_purchase_time',$current_datetime);
			//self::update_stocks();

			if(isset($product['activation_url'])){
				self::sendMail($to,$product); 
			}else{
				self::sendMail($to,$product); 
			}
		}
	}

	public static function sendMail($to,$args){
		 ob_start();
    	if(isset($args['activation_url'])){
    		$template = 'esim';
    	}else{
    		$template = 'physicalsim';
    	}
    	include(ESIM_PHYSICALSIM_DIR.'/admin/partials/email-templates/'.$template.'.php');
   		 $message = ob_get_contents();
   		 ob_end_clean();
   		 $vars = array(
		  '{{activation_url}}'       => $args['activation_url'],
		  '{{activation_code}}'       => $args['sim_or_serial_num'],
		  '{{confirmation_code}}'        => $args['pin_or_confirmation'],
		  '{{simdp_address}}' => $args['sim_or_serial_num']
		);

		$message =  strtr($message, $vars);

		$headers[] = 'Content-type: text/html; charset=utf-8';
		$headers[] = 'From: '.get_bloginfo("name").' <'.get_bloginfo("admin_email").'>' . "\r\n";
		return wp_mail( $to, 'Purchase successful '.get_bloginfo("name"), $message, $headers );
	}
}