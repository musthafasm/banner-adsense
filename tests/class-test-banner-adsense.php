<?php
/**
 * Class SampleTest
 *
 * @package Banner_Adsense
 */

namespace Perfomatix\BannerAdsense\Tests;

use \Perfomatix\BannerAdsense\Banner_Adsense;

/**
 * Test class for the core plugin class.
 */
class Test_Banner_Adsense extends \WP_Mock\Tools\TestCase {

	/**
	 * Setup all parent.
	 *
	 * @return void
	 */
	public function setUp() {
		\WP_Mock::setUp();
	}

	/**
	 * Teardown all parent.
	 *
	 * @return void
	 */
	public function tearDown() {
		\WP_Mock::tearDown();
	}

	/**
	 * Test all the private properties.
	 *
	 * @return void
	 */
	public function test_properties() {

		$banner_adsense = Banner_Adsense::instance();

		$basename = 'BaseName';
		static::assertInstanceOf( Banner_Adsense::class, $banner_adsense->set_basename( $basename ) );
		static::assertEquals( $basename, $banner_adsense->get_basename() );

		$path = 'BasePath';
		static::assertInstanceOf( Banner_Adsense::class, $banner_adsense->set_path( $path ) );
		static::assertEquals( $path, $banner_adsense->get_path() );

		$url = 'BaseUrl';
		static::assertInstanceOf( Banner_Adsense::class, $banner_adsense->set_url( $url ) );
		static::assertEquals( $url, $banner_adsense->get_url() );

		$version = '1.0.0';
		static::assertInstanceOf( Banner_Adsense::class, $banner_adsense->set_version( $version ) );
		static::assertEquals( $version, $banner_adsense->get_version() );
	}

	/**
	 * Test instance method.
	 *
	 * @return void
	 */
	public function test_instance() {
		static::assertInstanceOf( Banner_Adsense::class, Banner_Adsense::instance() );
	}

	/**
	 * Test init method.
	 *
	 * @return void
	 */
	public function test_init() {

		\WP_Mock::userFunction( 'load_plugin_textdomain' )
			->with( 'banner-adsense', false, 'banner-adsense/languages' )
			->andReturnTrue();

		$banner_adsense = ( Banner_Adsense::instance() )->set_basename( 'banner-adsense/banner-adsense.php' );

		static::assertInstanceOf( Banner_Adsense::class, $banner_adsense->init() );
	}
}
