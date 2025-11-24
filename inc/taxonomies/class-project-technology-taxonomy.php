<?php
namespace AMPortfolioTheme\Taxonomies;

defined( 'ABSPATH' ) || exit;

class Project_Technology_Taxonomy {

	public static function init() {
		$self = new self();
		add_action( 'init', array( $self, 'register' ) );
		add_action( 'project_technology_add_form_fields', array( $self, 'add_icon_field' ) );
		add_action( 'project_technology_add_form_fields', array( $self, 'add_order_field' ) );
		add_action( 'project_technology_edit_form_fields', array( $self, 'edit_icon_field' ) );
		add_action( 'project_technology_edit_form_fields', array( $self, 'edit_order_field' ) );
		add_action( 'created_project_technology', array( $self, 'save_icon_field' ) );
		add_action( 'created_project_technology', array( $self, 'save_order_field' ) );
		add_action( 'edited_project_technology', array( $self, 'save_icon_field' ) );
		add_action( 'edited_project_technology', array( $self, 'save_order_field' ) );
		add_action( 'admin_enqueue_scripts', array( $self, 'enqueue_media' ) );

		add_filter( 'manage_edit-project_technology_columns', array( $self, 'add_icon_column' ) );
		add_filter( 'manage_edit-project_technology_columns', array( $self, 'add_order_column' ) );
		add_filter( 'manage_project_technology_custom_column', array( $self, 'display_icon_column' ), 10, 3 );
		add_filter( 'manage_project_technology_custom_column', array( $self, 'display_order_column' ), 10, 3 );
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

	public function enqueue_media() {
		$screen = get_current_screen();
		if ( $screen && ( 'edit-project_technology' === $screen->id || 'project_technology' === $screen->taxonomy ) ) {
			wp_enqueue_media();
			wp_enqueue_script(
				'am-taxonomy-icon-uploader',
				get_template_directory_uri() . '/assets/js/taxonomy-icon-uploader.js',
				array( 'jquery' ),
				'1.0.0',
				true
			);

			wp_add_inline_style(
				'wp-admin',
				'
				.column-icon { width: 60px; text-align: center; }
				.column-icon img { width: 40px; height: 40px; object-fit: contain; border-radius: 4px; }
			'
			);
		}
	}

	public function add_icon_field() {
		?>
		<div class="form-field term-icon-wrap">
			<label for="technology-icon"><?php esc_html_e( 'Icon', 'am-portfolio-theme' ); ?></label>
			<div class="technology-icon-preview" style="margin-bottom: 10px;">
				<img src="" style="max-width: 100px; height: auto; display: none;" />
			</div>
			<input type="hidden" name="technology_icon" id="technology-icon" value="" />
			<button type="button" class="button technology-icon-upload">
				<?php esc_html_e( 'Upload Icon', 'am-portfolio-theme' ); ?>
			</button>
			<button type="button" class="button technology-icon-remove" style="display: none;">
				<?php esc_html_e( 'Remove Icon', 'am-portfolio-theme' ); ?>
			</button>
		</div>
		<?php
	}

	public function edit_icon_field( $term ) {
		$icon_id  = get_term_meta( $term->term_id, 'technology_icon', true );
		$icon_url = $icon_id ? wp_get_attachment_image_url( $icon_id, 'thumbnail' ) : '';
		?>
		<tr class="form-field term-icon-wrap">
			<th scope="row">
				<label for="technology-icon"><?php esc_html_e( 'Icon', 'am-portfolio-theme' ); ?></label>
			</th>
			<td>
				<div class="technology-icon-preview" style="margin-bottom: 10px;">
					<img src="<?php echo esc_url( $icon_url ); ?>" style="max-width: 100px; height: auto; <?php echo $icon_url ? '' : 'display: none;'; ?>" />
				</div>
				<input type="hidden" name="technology_icon" id="technology-icon" value="<?php echo esc_attr( $icon_id ); ?>" />
				<button type="button" class="button technology-icon-upload">
					<?php esc_html_e( 'Upload Icon', 'am-portfolio-theme' ); ?>
				</button>
				<button type="button" class="button technology-icon-remove" style="<?php echo $icon_url ? '' : 'display: none;'; ?>">
					<?php esc_html_e( 'Remove Icon', 'am-portfolio-theme' ); ?>
				</button>
			</td>
		</tr>
		<?php
	}

	public function save_icon_field( $term_id ) {
		$is_update_nonce_valid = isset( $_POST['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'update-tag_' . $term_id );
		$is_add_nonce_valid    = isset( $_POST['_wpnonce_add-tag'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce_add-tag'] ) ), 'add-tag' );

		if ( ! $is_update_nonce_valid && ! $is_add_nonce_valid ) {
			return;
		}

		if ( isset( $_POST['technology_icon'] ) ) {
			$icon_id = intval( $_POST['technology_icon'] );
			if ( 0 < $icon_id ) {
				update_term_meta( $term_id, 'technology_icon', $icon_id );
			} else {
				delete_term_meta( $term_id, 'technology_icon' );
			}
		}
	}

	/**
	 * Add order field to the add term form.
	 */
	public function add_order_field() {
		?>
		<div class="form-field term-order-wrap">
			<label for="technology-order"><?php esc_html_e( 'Order', 'am-portfolio-theme' ); ?></label>
			<input type="number" name="technology_order" id="technology-order" value="0" min="0" step="1" />
			<p class="description"><?php esc_html_e( 'Set the display order for this technology. Lower numbers appear first.', 'am-portfolio-theme' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Add order field to the edit term form.
	 *
	 * @param WP_Term $term The term being edited.
	 */
	public function edit_order_field( $term ) {
		$order = get_term_meta( $term->term_id, 'technology_order', true );
		$order = $order ? intval( $order ) : 0;
		?>
		<tr class="form-field term-order-wrap">
			<th scope="row">
				<label for="technology-order"><?php esc_html_e( 'Order', 'am-portfolio-theme' ); ?></label>
			</th>
			<td>
				<input type="number" name="technology_order" id="technology-order" value="<?php echo esc_attr( $order ); ?>" min="0" step="1" />
				<p class="description"><?php esc_html_e( 'Set the display order for this technology. Lower numbers appear first.', 'am-portfolio-theme' ); ?></p>
			</td>
		</tr>
		<?php
	}

	/**
	 * Save order field data.
	 *
	 * @param int $term_id The term ID.
	 */
	public function save_order_field( $term_id ) {
		$is_update_nonce_valid = isset( $_POST['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'update-tag_' . $term_id );
		$is_add_nonce_valid    = isset( $_POST['_wpnonce_add-tag'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce_add-tag'] ) ), 'add-tag' );

		if ( ! $is_update_nonce_valid && ! $is_add_nonce_valid ) {
			return;
		}

		if ( isset( $_POST['technology_order'] ) ) {
			$order = intval( $_POST['technology_order'] );
			update_term_meta( $term_id, 'technology_order', $order );
		}
	}

	/**
	 * Add order column to taxonomy listing.
	 *
	 * @param array $columns The existing columns.
	 * @return array The modified columns.
	 */
	public function add_order_column( $columns ) {
		$new_columns = array();
		foreach ( $columns as $key => $value ) {
			$new_columns[ $key ] = $value;
			if ( 'name' === $key ) {
				$new_columns['order'] = __( 'Order', 'am-portfolio-theme' );
			}
		}
		return $new_columns;
	}

	/**
	 * Display order values in the order column.
	 *
	 * @param string $content     The column content.
	 * @param string $column_name The column name.
	 * @param int    $term_id     The term ID.
	 * @return string The modified content.
	 */
	public function display_order_column( $content, $column_name, $term_id ) {
		if ( 'order' === $column_name ) {
			$order = get_term_meta( $term_id, 'technology_order', true );
			return $order ? intval( $order ) : 0;
		}
		return $content;
	}

	public function add_icon_column( $columns ) {
		$new_columns = array();
		foreach ( $columns as $key => $value ) {
			$new_columns[ $key ] = $value;
			if ( 'cb' === $key ) {
				$new_columns['icon'] = __( 'Icon', 'am-portfolio-theme' );
			}
		}
		return $new_columns;
	}

	public function display_icon_column( $content, $column_name, $term_id ) {
		if ( 'icon' === $column_name ) {
			$icon_id = get_term_meta( $term_id, 'technology_icon', true );
			if ( $icon_id ) {
				$icon_url = wp_get_attachment_image_url( $icon_id, 'thumbnail' );
				if ( $icon_url ) {
					return '<img src="' . esc_url( $icon_url ) . '" alt="" />';
				}
			}
			return '<span style="color: #ddd;">—</span>';
		}
		return $content;
	}
}