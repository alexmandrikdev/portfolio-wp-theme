# Project Debug Rules (Non-Obvious Only)

-   Email notifications are processed in the background using Action Scheduler; immediate delivery should not be expected.
-   If the contact form is not functioning, verify reCAPTCHA keys are configured in the admin settings, as this can cause issues. The theme includes checks for missing reCAPTCHA settings and displays notices via [`Theme_Init::recaptcha_missing_notice()`](inc/class-theme-init.php:183).
