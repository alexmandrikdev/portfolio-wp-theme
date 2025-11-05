# Project Coding Rules (Non-Obvious Only)

-   **Block scaffolding command:** `./node_modules/.bin/wp-create-block [slug] --variant dynamic --no-plugin --target-dir ./src/blocks/[slug] --namespace portfolio --title [title]`. After creation, **remove the "icon" value from the generated `block.json` file**.
-   Gutenberg block attribute names must use `snake_case`.
-   For array-type Gutenberg block attributes, define `type: "array"` and `default: []` in `block.json`. The structure of individual items within the array is managed by the `edit.js` component, typically with a repeater. Do not use `items` or `properties` keys directly within the `block.json` attribute definition for array elements.
-   Gutenberg blocks in `src/blocks` are **automatically registered** by [`inc/class-theme-init.php`](inc/class-theme-init.php). No manual registration is needed.
-   When enqueuing new scripts or styles, use the [`Asset_Helper`](inc/class-asset-helper.php) class for consistency.
-   `webpack.config.js` extends `@wordpress/scripts` to include custom entry points for `src/components/**/index.js` and `src/admin/**/index.js`.
-   CSS/SCSS class names must follow the BEM-like pattern: `^[a-z](?:[a-z0-9-]+)?(?:__[a-z0-9-]+)?(?:--[a-z0-9-]+)?$`.
-   PHP linter **does not enforce** file, class, function, or variable comments.

## Performance Optimization

-   **Prevent n+1 query problems:** Always use bulk loading patterns when working with loops that fetch meta data or related resources.
-   For term meta: Use [`update_termmeta_cache()`](src/blocks/project-tech-details/render.php:25) to pre-load all term meta in a single query before looping through terms.
-   For media data: Use [`Media_Data_Loader::load_media_data_bulk()`](src/blocks/project-tech-details/render.php:50) to load multiple media items in a single query instead of individual calls.
-   Combine multiple bulk loads: When multiple types of media data are needed, combine all media IDs into a single array and use one bulk load call.
-   Avoid individual [`get_term_meta()`](src/blocks/project-tech-details/render.php:31) calls inside loops - pre-load term meta cache first.
