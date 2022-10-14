<?php
/**
 * Plugin Name: WPS Filter Catalog Categories
 * Plugin URI: https://www.netpad.gr
 * Description: Filters categories when we show categories and products in the catalog
 * Version: 1.0.0
 * Author: gtsiokos
 * Author URI: https://www.netpad.gr
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'woocommerce_product_subcategories_args', 'test_woocommerce_product_subcategories_args' );
function test_woocommerce_product_subcategories_args( $args ) {
	if ( method_exists( 'WooCommerce_Product_Search_Service', 'get_term_ids_for_request' ) ) {
		if ( isset( $_REQUEST['ixwpst'] ) ) {
			$term_ids_for_request = WooCommerce_Product_Search_Service::get_term_ids_for_request( $args, array( 'product_cat' ) );
			foreach ( $term_ids_for_request as $term_id ) {
				$args['parent'] = $term_id;
			}
		}
	}
	return $args;
}