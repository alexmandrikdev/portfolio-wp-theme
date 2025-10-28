<?php

require get_parent_theme_file_path( 'vendor/erusev/parsedown/Parsedown.php' );

require get_parent_theme_file_path( 'inc/helpers.php' );
require get_parent_theme_file_path( 'inc/class-theme-init.php' );
require get_parent_theme_file_path( 'inc/class-asset-helper.php' );
require get_parent_theme_file_path( 'inc/class-markdown-helper.php' );

require get_parent_theme_file_path( 'inc/post-types/class-project-cpt.php' );
require get_parent_theme_file_path( 'inc/taxonomies/class-project-taxonomies.php' );

require get_parent_theme_file_path( 'inc/components/class-project-card.php' );

require get_parent_theme_file_path( 'inc/admin/class-settings-page.php' );
require get_parent_theme_file_path( 'inc/admin/class-settings-api.php' );
require get_parent_theme_file_path( 'inc/admin/class-settings-helper.php' );

AMPortfolioTheme\Theme_Init::init();

AMPortfolioTheme\PostTypes\Project_CPT::init();
AMPortfolioTheme\Taxonomies\Project_Taxonomies::init();

AMPortfolioTheme\Admin\Settings_Page::init();
AMPortfolioTheme\Admin\Settings_API::init();
