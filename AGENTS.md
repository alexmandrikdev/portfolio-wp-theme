# AGENTS.md

This file provides guidance to agents when working with code in this repository.

## Build/Lint/Test Commands

-   **Block scaffolding command:** `./node_modules/.bin/wp-create-block [slug] --variant dynamic --no-plugin --target-dir ./src/blocks/[slug] --namespace portfolio --title [title]`. After creation, **remove the "icon" value from the generated `block.json` file**.
-   `npm run start` and `npm run build` use `--blocks-manifest --experimental-modules`.
-   PHP linting/formatting: `composer lint` / `composer format`. These use `phpcs` and `phpcbf` respectively, with specific exclusions for commenting rules (File, Class, Function, Variable comments are not strictly enforced by the linter).
-   **DDEV Environment:** All WordPress and PHP CLI commands must be prefixed with `ddev`. For example: `ddev wp`, `ddev php`.

## Code Style

-   Gutenberg block attribute names must use `snake_case`.
-   For array-type Gutenberg block attributes, define `type: "array"` and `default: []` in `block.json`. The structure of individual items within the array is managed by the `edit.js` component, typically with a repeater. Do not use `items` or `properties` keys directly within the `block.json` attribute definition for array elements.
-   CSS/SCSS: Follows `@wordpress/stylelint-config/scss` with a custom BEM-like class pattern: `^[a-z](?:[a-z0-9-]+)?(?:__[a-z0-9-]+)?(?:--[a-z0-9-]+)?$`.
-   PHP: Adheres to WordPress coding standards, but the linter **does not enforce** file, class, function, or variable comments.
-   CSS/SCSS: When copying CSS from prototypes, convert variable names to match the conventions used in [`src/components/global/_variables.scss`](src/components/global/_variables.scss).
-   CSS/SCSS: When copying responsive media queries from prototypes, convert them to use the `breakpoint-up`, `breakpoint-down`, or `breakpoint-only` mixins from [`src/scss/shared/_mixins.scss`](src/scss/shared/_mixins.scss) with the breakpoints defined in [`src/scss/shared/_variables.scss`](src/scss/shared/_variables.scss).

## Custom Utilities/Patterns

-   Gutenberg blocks in `src/blocks` are **automatically registered** by `inc/class-theme-init.php`. No manual registration is needed.
-   Use `Asset_Helper` class for enqueuing new scripts or styles.
-   `theme.json` can be modified for global styles, but **does not need to be kept in sync with global styles**.
-   Internationalization: Use `am-portfolio-theme` text domain.
-   `webpack.config.js` extends `@wordpress/scripts` to include custom entry points for `src/components/**/index.js` and `src/admin/**/index.js`.

## Performance Optimization

-   **Prevent n+1 query problems:** Always use bulk loading patterns when working with loops that fetch meta data or related resources.
-   For term meta: Use [`update_termmeta_cache()`](src/blocks/project-tech-details/render.php:25) to pre-load all term meta in a single query before looping through terms.
-   For media data: Use [`Media_Data_Loader::load_media_data_bulk()`](src/blocks/project-tech-details/render.php:50) to load multiple media items in a single query instead of individual calls.
-   Combine multiple bulk loads: When multiple types of media data are needed, combine all media IDs into a single array and use one bulk load call.
-   Avoid individual [`get_term_meta()`](src/blocks/project-tech-details/render.php:31) calls inside loops - pre-load term meta cache first.
