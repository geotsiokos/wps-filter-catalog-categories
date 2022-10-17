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
		add_filter( 'woocommerce_product_subcategories_args', array( __CLASS__, 'woocommerce_product_subcategories_args' ), 1 );
	}

	public static function woocommerce_product_subcategories_args( $args ) {
		
		$ixwpst = array();
		if ( isset( $_REQUEST['ixwpst'] ) ) {
			if ( isset( $_REQUEST['ixwpst']['product_cat'] ) ) {
				$filter_product_cat = $_REQUEST['ixwpst']['product_cat'];
				if ( count( $filter_product_cat ) > 0 ) {
					foreach ( $filter_product_cat as $term_id ) {
						if ( !in_array( $term_id, $ixwpst ) ) {
							$ixwpst[] = $term_id;
						}
					}
				}
			}
		}
		
		// This would include any child or parent term ID for the ones requested.
		// For example, if we select Men > Jackets, so that the Men category is still included.
		// In that case, only the check for parents would be needed.
		// Leaving both cases implemented as a reference if any is later required.
		//
		if ( false ) {
			foreach ( $ixwpst as $term_id ) {
				$term_children = get_term_children( $term_id, 'product_cat' );
				if ( !empty( $term_children ) && !( $term_children instanceof WP_Error ) ) {
					foreach ( $term_children as $child_term_id ) {
						if ( !in_array( $child_term_id, $ixwpst ) ) {
							$ixwpst[] = $child_term_id;
						}
					}
				}
				$parents = get_ancestors( $term_id, 'product_cat', 'taxonomy' );
				if ( !empty( $parents ) ) {
					foreach ( $parents as $parent_term_id ) {
						if ( !in_array( $parent_term_id, $ixwpst ) ) {
							$ixwpst[] = $parent_term_id;
						}
					}
				}
			}
		}
		
		$product_categories = get_categories( $args );
		foreach ( $product_categories as $product_category ) {
			$product_category_term_ids[] = $product_category->term_id;
		}
		
		$has_some = false;
		$term_counts = WooCommerce_Product_Search_Service::get_term_counts( 'product_cat' );
		
		$include = array();
		foreach ( $product_category_term_ids as $term_id ) {
			if ( isset( $term_counts[$term_id] ) && $term_counts[$term_id] > 0 ) {
				$include[] = $term_id;
				$has_some = true;
			} else if ( false ) {
				// Leaving for reference in case needed later on, children in term counts would cause the parent term to be included:
				$term_children = get_term_children( $term_id, 'product_cat' );
				if ( !empty( $term_children ) && !( $term_children instanceof WP_Error ) ) {
					foreach ( $term_children as $child_term_id ) {
						if ( isset( $term_counts[$child_term_id ] ) && $term_counts[$child_term_id] > 0 ) {
							$include[] = $term_id;
							$has_some = true;
							break;
						}
					}
				}
			}
		}
		
		if ( count( $ixwpst ) > 0 ) {
			$include = array_intersect( $ixwpst, $include );
			if ( count( $include ) === 0 ) {
				$include = array( -1 );
			}
		}
		
		if ( !$has_some ) {
			$include = array( -1 );
		}
		
		$args['include'] = $include;
		$args['count'] = false;
		
		return $args;
	}
} WPS_Filter_Catalog_Categories::init();