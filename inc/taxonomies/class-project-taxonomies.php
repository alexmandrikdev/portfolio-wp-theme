<?php

namespace AMPortfolioTheme\Taxonomies;

defined( 'ABSPATH' ) || exit;

class Project_Taxonomies {

	public static function init() {
		$self = new self();

		add_action( 'init', array( $self, 'register_project_taxonomies' ) );
	}

	public function register_project_taxonomies() {
		$technology_labels = array(
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

		$technology_args = array(
			'hierarchical'      => false,
			'labels'            => $technology_labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'show_in_rest'      => true,
			// No public archive pages.
			'public'            => false,
			'show_in_nav_menus' => false,
			'rewrite'           => false,
		);

		register_taxonomy( 'project_technology', array( 'project' ), $technology_args );

		$type_labels = array(
			'name'                       => _x( 'Types', 'taxonomy general name', 'am-portfolio-theme' ),
			'singular_name'              => _x( 'Type', 'taxonomy singular name', 'am-portfolio-theme' ),
			'search_items'               => __( 'Search Types', 'am-portfolio-theme' ),
			'all_items'                  => __( 'All Types', 'am-portfolio-theme' ),
			'edit_item'                  => __( 'Edit Type', 'am-portfolio-theme' ),
			'update_item'                => __( 'Update Type', 'am-portfolio-theme' ),
			'add_new_item'               => __( 'Add New Type', 'am-portfolio-theme' ),
			'new_item_name'              => __( 'New Type Name', 'am-portfolio-theme' ),
			'menu_name'                  => __( 'Types', 'am-portfolio-theme' ),
			'popular_items'              => __( 'Popular Types', 'am-portfolio-theme' ),
			'separate_items_with_commas' => __( 'Separate types with commas', 'am-portfolio-theme' ),
			'add_or_remove_items'        => __( 'Add or remove types', 'am-portfolio-theme' ),
			'choose_from_most_used'      => __( 'Choose from the most used types', 'am-portfolio-theme' ),
		);

		$type_args = array(
			'hierarchical'      => false,
			'labels'            => $type_labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'show_in_rest'      => true,
			// No public archive pages.
			'public'            => false,
			'show_in_nav_menus' => false,
			'rewrite'           => false,
		);

		register_taxonomy( 'project_type', array( 'project' ), $type_args );
	}
}
