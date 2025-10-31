# WordPress Portfolio Theme Development Guidelines

This document outlines the development guidelines for the custom WordPress "Portfolio" theme. This theme is built with a modern approach, leveraging Gutenberg blocks, custom post types, taxonomies, and potentially the Interactivity API for dynamic frontend experiences.

## 1. General Principles

-   **WordPress Best Practices:** Adhere to WordPress coding standards and best practices for theme development.
-   **Modularity:** Maintain a modular structure, especially for Gutenberg blocks and PHP classes.
-   **Performance:** Prioritize performance by optimizing assets, using Action Scheduler for background tasks, and efficient database queries.
-   **Security:** Implement secure coding practices, including nonce verification and proper data sanitization/validation.
-   **Internationalization:** Ensure all user-facing strings are translatable using the `am-portfolio-theme` text domain.
-   **CSS Styling:** Prefer the BEM (Block, Element, Modifier) methodology for CSS class naming to ensure modularity and reusability.

## 2. Theme Structure

The theme follows a structured approach:

-   **`functions.php`**: Main theme functions file, primarily used for `require` statements to include other PHP files and initial theme setup.
-   **`style.css`**: Basic theme information.
-   **`theme.json`**: Global theme settings, including typography (e.g., 'Inter' font) and layout.
-   **`inc/`**: Contains PHP classes for core theme functionalities:
    -   `inc/admin/`: Admin settings pages (e.g., reCAPTCHA configuration).
    -   `inc/ajax-handlers/`: AJAX request handling (e.g., contact form submissions).
    -   `inc/components/`: Reusable PHP components (e.g., `Project_Card`).
    -   `inc/emails/`: Email notification logic.
    -   `inc/post-types/`: Custom Post Type definitions (e.g., `Project_CPT`, `Contact_Submission_CPT`).
    -   `inc/taxonomies/`: Custom Taxonomy definitions (e.g., `Project_Type_Taxonomy`, `Project_Technology_Taxonomy`).
    -   `inc/helpers.php`: General utility functions.
    -   `inc/class-asset-helper.php`: Manages enqueuing of scripts and styles.
    -   `inc/class-theme-init.php`: Initializes theme functionalities, registers blocks, and enqueues global scripts.
-   **`src/`**: Frontend source files:
    -   `src/admin/settings/`: React-based admin settings interface.
    -   `src/blocks/` : Individual Gutenberg block implementations. Each block typically includes:
        -   `block.json`: Block metadata, attributes, and script/style dependencies.
        -   `edit.js`: React component for block editing in the Gutenberg editor.
        -   `render.php`: PHP template for rendering the block on the frontend.
        -   `style.scss`: SCSS for block-specific styling.
        -   `view.js`: JavaScript for frontend interactivity (especially with Interactivity API).
    -   `src/components/global/`: Global SCSS and JavaScript for theme-wide elements (e.g., dark mode script, scroll animations).
    -   `src/scss/shared/`: Shared SCSS mixins and variables.
-   **`assets/`**: Compiled frontend assets (fonts, JavaScript).
-   **`templates/`**: PHP templates for various parts of the theme, including email templates.

## 3. Key Features and Functionalities

-   **Gutenberg Block-Based Development:** The theme heavily relies on custom Gutenberg blocks for content creation. When adding new features, consider implementing them as blocks. Blocks are automatically registered from the `src/blocks` directory via `inc/class-theme-init.php`, so no manual registration is required for new blocks.
-   **Custom Post Types & Taxonomies:** "Projects" are managed via a custom post type with associated "Project Type" and "Project Technology" taxonomies.
-   **Contact Form with reCAPTCHA:** A contact form is implemented with AJAX submission and reCAPTCHA integration. Ensure reCAPTCHA keys are configured in the admin settings.
-   **Email System:** Automated email notifications are sent for contact form submissions. Action Scheduler is recommended for background processing of these emails.
-   **Dark Mode:** A client-side script provides a dark mode toggle, respecting system preferences.
-   **Webpack for Asset Compilation:** Frontend assets (SCSS, JavaScript) are compiled using Webpack.
-   **WPML Compatibility:** The theme includes `wpml-config.xml` for multilingual support.

## 4. Development Workflow

-   **PHP Dependencies:** Managed with Composer.
-   **JavaScript/CSS Dependencies:** Managed with NPM/Yarn.
-   **Asset Compilation:** Frontend assets are compiled using `@wordpress/scripts` and Webpack.
    -   **`npm run start`**: Starts the development server with live reloading and compiles assets. It also generates `blocks-manifest.php` and supports experimental modules.
    -   **`npm run build`**: Compiles all assets for production, generates `blocks-manifest.php`, and supports experimental modules.
    -   **`npm run clean`**: Removes the `build` directory before `start` or `build` commands.
    -   **`npm run create-block`**: Utility to scaffold new Gutenberg blocks within `src/blocks`.
        -   To scaffold a new block, run the following command from the theme root: `./node_modules/.bin/wp-create-block [slug] --variant dynamic --no-plugin --target-dir ./src/blocks/[slug] --namespace portfolio --title [rövid, leíró title, nézd meg hogy van a többi block elnevezve] --category widgets --textdomain [slug]`
        -   After scaffolding, remove the "icon" value from the generated `block.json` file.
    -   **`webpack.config.js`**: Extends the default `@wordpress/scripts` Webpack configuration to include custom entry points for components (`src/components/**/index.js`) and admin scripts (`src/admin/**/index.js`).
-   **Coding Standards:** Adhere to PHP_CodeSniffer rules defined in `phpcs.xml.dist`.
-   **Linting & Formatting:**
    -   `npm run format`: Formats code using configured tools.
    -   `npm run lint:css`: Lints CSS files.
    -   `npm run lint:js`: Lints JavaScript files.

## 5. Roo Code Interaction Guidelines

When working with this theme, Roo Code should:

-   **Understand Block Structure:** Recognize that features are often encapsulated within Gutenberg blocks. Blocks are automatically registered, so when modifying or adding features, identify the relevant `block.json`, `edit.js`, `render.php`, `style.scss`, and `view.js` files within the `src/blocks` directory. New blocks do not require manual registration.
-   **Respect PHP Class Architecture:** Understand the purpose of classes in the `inc/` directory for managing post types, taxonomies, AJAX, emails, and admin settings.
-   **Utilize Asset Helper:** When enqueuing new scripts or styles, use the `Asset_Helper` class for consistency.
-   **Consider `theme.json`:** For global styling or layout changes, consult and modify `theme.json`. Note that keeping `theme.json` in sync with global styles is not necessary.
-   **Be Aware of Dependencies:** Recognize the use of Composer and NPM/Yarn for dependency management.
-   **Prioritize Security:** Always consider security implications when modifying PHP code, especially with user input or database interactions.
-   **Maintain Internationalization:** Use WordPress internationalization functions for all new strings.
-   **Test Thoroughly:** After making changes, verify functionality, especially for contact forms, email sending, and block rendering.
