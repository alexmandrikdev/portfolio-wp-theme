# Project Detail Page Blocks Plan

This document outlines the plan for creating Gutenberg blocks for the project detail page, based on the provided HTML prototype. Each section of the prototype will be a distinct block, including its own template and SCSS.

## Global Styling Integration (Completed)

-   [x] Extract CSS variables from the prototype's `<style>` block and add them to `wp-content/themes/portfolio/src/scss/shared/_variables.scss` or `wp-content/themes/portfolio/src/components/global/_variables.scss`.
-   [x] Integrate base styles (e.g., `body`, `.container`, `section`, `h1`-`h4`, `p`, `.body-large`, `.body-small`) from the prototype into `wp-content/themes/portfolio/src/components/global/_base.scss` and `wp-content/themes/portfolio/src/components/global/_typography.scss`.
-   [x] Integrate button styles (`.btn-primary`, `.btn-secondary`, `.btn-text`) into `wp-content/themes/portfolio/src/components/global/_components.scss`.
-   [x] Integrate animation styles (`@keyframes fadeUp`, `.scroll-fade`) into `wp-content/themes/portfolio/src/components/global/_animations.scss`.
-   [x] Integrate accessibility styles (`button:focus-visible`, `a:focus-visible`) into `wp-content/themes/themes/portfolio/src/components/global/_accessibility.scss`.
-   [x] Update `wp-content/themes/portfolio/theme.json` with any necessary global settings (e.g., custom color palette if not already defined, spacing presets).

## Global Script Integration (Completed)

-   [x] Implement theme toggle and language switcher logic in `wp-content/themes/portfolio/src/blocks/header/view.js`.
-   [x] Implement mobile menu logic in `wp-content/themes/portfolio/src/blocks/header/view.js`.
-   [x] Implement scroll animation logic in `wp-content/themes/portfolio/src/components/global/scroll-animation.js`.

## Block Creation: `portfolio/project-detail-hero` (Pending)

-   [x] Create `wp-content/themes/portfolio/src/blocks/project-detail-hero/block.json` with attributes for meta items and live project link, using BEM class selectors.
-   [x] Create `wp-content/themes/portfolio/src/blocks/project-detail-hero/render.php` to render the HTML structure from the prototype's "Hero szekció", using `the_title()`, `the_excerpt()`, hardcoded link text, and dynamic meta items and live project URL, with BEM class names.
-   [x] Create `wp-content/themes/portfolio/src/blocks/project-detail-hero/style.scss` for block-specific styles, adapting variable names and using BEM class names.
-   [x] Create `wp-content/themes/portfolio/src/blocks/project-detail-hero/edit.js` for Gutenberg editor functionality, using a repeater for meta items.
-   [x] Create `wp-content/themes/portfolio/src/blocks/project-detail-hero/editor.scss` for editor-specific styles.
-   [x] Create `wp-content/themes/portfolio/src/blocks/project-detail-hero/index.js` for block registration.
-   [x] Implement a global setting or mechanism to define the "Back to Portfolio" link URL, replacing the hardcoded value in `portfolio/project-detail-hero` block. (Backend and Frontend settings implemented)
    -   [x] **Update Project Detail Hero Block (`src/blocks/project-detail-hero/render.php`)**
        -   [x] Retrieve the `projects_listing_page_ids` setting using `Settings_Helper::get_current_settings()`.
        -   [x] Determine the current language (using Polylang functions like `pll_current_language()`).
        -   [x] Get the correct Projects Listing page ID for the current language from the settings.
        -   [x] Construct the "Back to Portfolio" link URL using `get_permalink()` with the retrieved page ID.
    -   [x] **Testing**
        -   [x] Verify that the "General" tab appears in the admin settings.
        -   [x] Verify that page selection dropdowns appear for each active language.
        -   [x] Verify that selected pages are saved correctly.
        -   [x] Verify that the "Back to Portfolio" link in the `project-detail-hero` block correctly points to the configured page for the current language.

## Block Creation: `portfolio/project-overview` (Pending)

-   [ ] Create `wp-content/themes/portfolio/src/blocks/project-overview/block.json` with attributes for the two cards (title, bullet list items).
-   [ ] Create `wp-content/themes/portfolio/src/blocks/project-overview/render.php` to render the HTML structure from the prototype's "Gyors áttekintés szekció".
-   [ ] Create `wp-content/themes/portfolio/src/blocks/project-overview/style.scss` for block-specific styles.
-   [ ] Create `wp-content/themes/portfolio/src/blocks/project-overview/edit.js` for Gutenberg editor functionality.

## Block Creation: `portfolio/project-results` (Pending)

-   [ ] Create `wp-content/themes/portfolio/src/blocks/project-results/block.json` with attributes for main screenshot (media item), and a repeatable block for smaller screenshots (media item).
-   [ ] Create `wp-content/themes/portfolio/src/blocks/project-results/render.php` to render the HTML structure from the prototype's "Végeredmény szekció".
-   [ ] Create `wp-content/themes/portfolio/src/blocks/project-results/style.scss` for block-specific styles.
-   [ ] Create `wp-content/themes/portfolio/src/blocks/project-results/edit.js` for Gutenberg editor functionality.

## Block Creation: `portfolio/project-tech-details` (Pending)

-   [ ] Create `wp-content/themes/portfolio/src/blocks/project-tech-details/block.json` with attribute for challenges (repeatable: title, description, solution).
-   [ ] Create `wp-content/themes/portfolio/src/blocks/project-tech-details/render.php` to render the HTML structure from the prototype's "Technikai szekció".
-   [ ] Create `wp-content/themes/portfolio/src/blocks/project-tech-details/style.scss` for block-specific styles.
-   [ ] Create `wp-content/themes/portfolio/src/blocks/project-tech-details/edit.js` for Gutenberg editor functionality.

## Block Creation: `portfolio/project-testimonial` (Pending)

-   [ ] Create `wp-content/themes/portfolio/src/blocks/project-testimonial/block.json` with attributes for section title, quote, author, and role.
-   [ ] Create `wp-content/themes/portfolio/src/blocks/project-testimonial/render.php` to render the HTML structure from the prototype's "Visszajelzés szekció".
-   [ ] Create `wp-content/themes/portfolio/src/blocks/project-testimonial/style.scss` for block-specific styles.
-   [ ] Create `wp-content/themes/portfolio/src/blocks/project-testimonial/edit.js` for Gutenberg editor functionality.
