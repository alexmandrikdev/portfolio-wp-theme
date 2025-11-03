# Project Documentation Rules (Non-Obvious Only)

-   Gutenberg blocks are automatically registered from the `src/blocks` directory via [`inc/class-theme-init.php`](inc/class-theme-init.php); manual registration is not required.
-   The `theme.json` file can be modified for global styles, but it **does not need to be kept in sync with global styles** defined elsewhere.
-   Custom Post Types and Taxonomies: "Projects" are managed via a custom post type (`Project_CPT`) with associated "Project Type" (`Project_Type_Taxonomy`) and "Project Technology" (`Project_Technology_Taxonomy`) taxonomies.
-   Internationalization: All user-facing strings must use the `am-portfolio-theme` text domain.
-   Frontend assets (SCSS, JavaScript) are compiled using `@wordpress/scripts` and Webpack, with custom entry points for `src/components/**/index.js` and `src/admin/**/index.js` defined in `webpack.config.js`.
