<?php
/**
 * PhpUnit bootstrap file
 *
 * @package BannerAdsense
 * @subpackage UnitTest
 */

// First we need to load the composer autoloader so we can use WP Mock.
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../vendor/php-stubs/wordpress-stubs/wordpress-stubs.php';

// Now call the bootstrap method of WP Mock.
\WP_Mock::bootstrap();

/**
 * Now we include any plugin files that we need to be able to run the tests. This
 * should be files that define the functions and classes you're going to test.
 */
require_once __DIR__ . '/../banner-adsense.php';
