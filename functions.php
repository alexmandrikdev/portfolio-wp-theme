<?php

require get_parent_theme_file_path( 'inc/helpers.php' );
require get_parent_theme_file_path( 'inc/class-theme-init.php' );

require get_parent_theme_file_path( 'inc/post-types/class-project-cpt.php' );
require get_parent_theme_file_path( 'inc/taxonomies/class-project-taxonomies.php' );

AMPortfolioTheme\Theme_Init::init();

AMPortfolioTheme\PostTypes\Project_CPT::init();
AMPortfolioTheme\Taxonomies\Project_Taxonomies::init();
