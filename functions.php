<?php

require get_parent_theme_file_path( 'vendor/erusev/parsedown/Parsedown.php' );

require get_parent_theme_file_path( 'inc/helpers.php' );
require get_parent_theme_file_path( 'inc/class-theme-init.php' );
require get_parent_theme_file_path( 'inc/class-asset-helper.php' );
require get_parent_theme_file_path( 'inc/class-markdown-helper.php' );

require get_parent_theme_file_path( 'inc/post-types/class-project-cpt.php' );
require get_parent_theme_file_path( 'inc/taxonomies/class-project-type-taxonomy.php' );
require get_parent_theme_file_path( 'inc/taxonomies/class-project-technology-taxonomy.php' );

require get_parent_theme_file_path( 'inc/post-types/class-contact-submission-cpt.php' );
require get_parent_theme_file_path( 'inc/ajax-handlers/class-contact-form-handler.php' );

require get_parent_theme_file_path( 'inc/components/class-project-card-data-loader.php' );
require get_parent_theme_file_path( 'inc/components/class-project-card-data.php' );
require get_parent_theme_file_path( 'inc/components/class-project-card.php' );

require get_theme_file_path( 'inc/emails/class-admin-contact-notification.php' );
require get_theme_file_path( 'inc/emails/class-sender-confirmation-email.php' );

require get_parent_theme_file_path( 'inc/admin/class-settings-page.php' );
require get_parent_theme_file_path( 'inc/admin/class-settings-api.php' );
require get_parent_theme_file_path( 'inc/admin/class-settings-helper.php' );

require get_parent_theme_file_path( 'inc/class-polylang-string-registration.php' );

AMPortfolioTheme\Theme_Init::init();

AMPortfolioTheme\PostTypes\Project_CPT::init();
AMPortfolioTheme\Taxonomies\Project_Type_Taxonomy::init();
AMPortfolioTheme\Taxonomies\Project_Technology_Taxonomy::init();

AMPortfolioTheme\PostTypes\Contact_Submission_CPT::init();
AMPortfolioTheme\AjaxHandlers\Contact_Form_Handler::init();

AMPortfolioTheme\Admin\Settings_Page::init();
AMPortfolioTheme\Admin\Settings_API::init();

AMPortfolioTheme\Polylang_String_Registration::init();
