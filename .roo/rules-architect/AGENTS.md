# Project Architecture Rules (Non-Obvious Only)

-   New features should primarily be implemented as custom Gutenberg blocks, as the theme heavily relies on this structure.
-   Gutenberg blocks are automatically registered by [`inc/class-theme-init.php`](inc/class-theme-init.php), so manual registration is not required.
-   The core data model for "Projects" is implemented using a custom post type (`Project_CPT`) with "Project Type" and "Project Technology" taxonomies.
-   Automated email notifications for contact form submissions are designed for asynchronous processing using Action Scheduler.
-   The theme includes `wpml-config.xml` for WPML compatibility, indicating a design for multilingual support.
