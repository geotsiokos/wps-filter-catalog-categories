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

class WPS_Filter_Catalog_Categories {

	public static function init() {
		add_filter( 'woocommerce_product_subcategories_args', array( __CLASS__, 'woocommerce_product_subcategories_args' ) );
	}

	public static function woocommerce_product_subcategories_args( $args ) {
		if ( method_exists( 'WooCommerce_Product_Search_Service', 'get_term_ids_for_request' ) ) {
			if ( isset( $_REQUEST['ixwpst'] ) ) {
				if ( isset( $_REQUEST['ixwpst']['product_cat'] ) ) {
					$filter_product_cat = $_REQUEST['ixwpst']['product_cat'];
					if ( count( $filter_product_cat ) > 0 ) {
						foreach ( $filter_product_cat as $term_id ) {
							$args['parent'] = $term_id;
						}
					}
				}
			}
		}

		return $args;
	}
} WPS_Filter_Catalog_Categories::init();