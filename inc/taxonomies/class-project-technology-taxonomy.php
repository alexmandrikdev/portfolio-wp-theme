<?php
namespace AMPortfolioTheme\Taxonomies;

defined( 'ABSPATH' ) || exit;

class Project_Technology_Taxonomy {

	public static function init() {
		$self = new self();
		add_action( 'init', array( $self, 'register' ) );
	}

	public function register() {
		$labels = array(
			'name'              => _x( 'Technologies', 'taxonomy general name', 'am-portfolio-theme' ),
			'singular_name'     => _x( 'Technology', 'taxonomy singular name', 'am-portfolio-theme' ),
			'search_items'      => __( 'Search Technologies', 'am-portfolio-theme' ),
			'all_items'         => __( 'All Technologies', 'am-portfolio-theme' ),
			'parent_item'       => __( 'Parent Technology', 'am-portfolio-theme' ),
			'parent_item_colon' => __( 'Parent Technology:', 'am-portfolio-theme' ),
			'edit_item'         => __( 'Edit Technology', 'am-portfolio-theme' ),
			'update_item'       => __( 'Update Technology', 'am-portfolio-theme' ),
			'add_new_item'      => __( 'Add New Technology', 'am-portfolio-theme' ),
			'new_item_name'     => __( 'New Technology Name', 'am-portfolio-theme' ),
			'menu_name'         => __( 'Technologies', 'am-portfolio-theme' ),
		);

		$args = array(
			'hierarchical'      => false,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'show_in_rest'      => true,
			'public'            => false,
			'show_in_nav_menus' => false,
			'rewrite'           => false,
		);

		register_taxonomy( 'project_technology', array( 'project' ), $args );
	}
}
