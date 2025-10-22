<?php

namespace AMPortfolioTheme\PostTypes;

defined( 'ABSPATH' ) || exit;

class Project_CPT {

	public static function init() {
		$self = new self();

		add_action( 'init', array( $self, 'register_project_post_type' ) );
	}

	public function register_project_post_type() {
		$labels = array(
			'name'          => _x( 'Projects', 'Post Type General Name', 'am-portfolio-theme' ),
			'singular_name' => _x( 'Project', 'Post Type Singular Name', 'am-portfolio-theme' ),
			'menu_name'     => __( 'Projects', 'am-portfolio-theme' ),
			'all_items'     => __( 'All Projects', 'am-portfolio-theme' ),
			'add_new_item'  => __( 'Add New Project', 'am-portfolio-theme' ),
			'add_new'       => __( 'Add New', 'am-portfolio-theme' ),
			'new_item'      => __( 'New Project', 'am-portfolio-theme' ),
			'edit_item'     => __( 'Edit Project', 'am-portfolio-theme' ),
			'update_item'   => __( 'Update Project', 'am-portfolio-theme' ),
			'view_item'     => __( 'View Project', 'am-portfolio-theme' ),
			'view_items'    => __( 'View Projects', 'am-portfolio-theme' ),
		);

		$args = array(
			'label'               => __( 'Project', 'am-portfolio-theme' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => 20,
			'menu_icon'           => 'dashicons-portfolio',
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => true,
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'show_in_rest'        => true,
			'rest_base'           => 'projects',
		);

		register_post_type( 'project', $args );
	}
}
