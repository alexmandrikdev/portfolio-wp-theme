<?php
namespace AMPortfolioTheme\Taxonomies;

defined( 'ABSPATH' ) || exit;

class Project_Type_Taxonomy {

	public static function init() {
		$self = new self();
		add_action( 'init', array( $self, 'register' ) );
	}

	public function register() {
		$labels = array(
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

		register_taxonomy( 'project_type', array( 'project' ), $args );
	}
}
