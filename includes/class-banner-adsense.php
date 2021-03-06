<?php
/**
 * The main plugin class file.
 *
 * @package BannerAdsense
 */

namespace Perfomatix\BannerAdsense;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The singleton plugin class.
 *
 * @since 1.0.0
 */
class Banner_Adsense {

	/**
	 * Static property to hold singleton instance for the class.
	 *
	 * @var self|null $instance
	 */
	private static $instance = null;

	/**
	 * The plugin basename.
	 *
	 * @var string|null $basename
	 */
	private $basename = null;

	/**
	 * The plugin base path.
	 *
	 * @var string|null $path
	 */
	private $path = null;

	/**
	 * The plugin base url.
	 *
	 * @var string|null $url
	 */
	private $url = null;

	/**
	 * The plugin version.
	 *
	 * @var string|null $version
	 */
	private $version = null;

	/**
	 * Get the basename.
	 *
	 * @return string|null
	 */
	public function get_basename() {
		return $this->basename;
	}

	/**
	 * Set the basename.
	 *
	 * @param  string $basename base name.
	 * @return self
	 */
	public function set_basename( $basename ) {
		$this->basename = $basename;
		return $this;
	}

	/**
	 * Get the base path.
	 *
	 * @return string|null
	 */
	public function get_path() {
		return $this->path;
	}

	/**
	 * Set the base path.
	 *
	 * @param  string $path base path.
	 * @return self
	 */
	public function set_path( $path ) {
		$this->path = $path;
		return $this;
	}

	/**
	 * Get the base url.
	 *
	 * @return string|null
	 */
	public function get_url() {
		return $this->url;
	}

	/**
	 * Set the base url.
	 *
	 * @param  string $url base url.
	 * @return self
	 */
	public function set_url( $url ) {
		$this->url = $url;
		return $this;
	}

	/**
	 * Get the version.
	 *
	 * @return string|null
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Set the version.
	 *
	 * @param  string $version version.
	 * @return self
	 */
	public function set_version( $version ) {
		$this->version = $version;
		return $this;
	}

	/**
	 * If an instance exists, returns it. If not, creates one and retuns it.
	 *
	 * @return self
	 */
	final public static function instance() {

		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Initialise all the basic features.
	 *
	 * @return self
	 */
	public function init() {

		// Load textdomain for the plugin.
		$path = dirname( $this->get_basename() ) . '/languages';
		load_plugin_textdomain( 'banner-adsense', false, $path );

		// Initialise widget.
		add_action( 'widgets_init', array( $this, 'widgets_init' ) );

		// Append scripts.
		add_action( 'wp_head', array( $this, 'append_header_script' ) );
		add_action( 'wp_footer', array( $this, 'append_footer_script' ) );

		if ( function_exists( 'wp_body_open' ) ) {
			add_action( 'wp_body_open', array( $this, 'append_body_script' ) );
		}

		return $this;
	}

	/**
	 * Initialise all the admin features.
	 *
	 * @return self
	 */
	public function admin_init() {

		// Initialise admin settings.
		( new Admin_Settings() )->init( $this->get_basename() );

		// Enque scripts and style.
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

		return $this;
	}

	/**
	 * Initialise all the widget features.
	 *
	 * @return self
	 */
	public function widgets_init() {

		// Register the banner widget.
		register_widget( new Widget() );

		return $this;
	}

	/**
	 * Enque admin scripts and style.
	 *
	 * @return void
	 */
	public function admin_enqueue_scripts() {

		wp_enqueue_media();

		wp_register_script(
			'banner-adsense-admin',
			$this->get_url() . 'assets/js/admin.js',
			array( 'jquery' ),
			$this->get_version(),
			true
		);

		wp_localize_script(
			'banner-adsense-admin',
			'metaImage',
			array(
				'title'  => __( 'Choose or Upload PDF', 'banner-adsense' ),
				'button' => __( 'Use this document', 'banner-adsense' ),
			)
		);

		wp_enqueue_script( 'banner-adsense-admin' );
	}

	/**
	 * Append the header scripts.
	 *
	 * @return void
	 */
	public function append_header_script() {
		$this->render_output( 'headscript' );
	}

	/**
	 * Append the body scripts.
	 *
	 * @return void
	 */
	public function append_body_script() {
		$this->render_output( 'bodyscript' );
	}

	/**
	 * Append the footer scripts.
	 *
	 * @return void
	 */
	public function append_footer_script() {
		$this->render_output( 'footerscript' );
	}

	/**
	 * Outputs the given section.
	 *
	 * @param string $section The section name head, body or footer.
	 * @return void
	 */
	private function render_output( $section ) {
		// Ignore admin, feed, robots or trackbacks.
		if ( is_admin() || is_feed() || is_robots() || is_trackback() ) {
			return;
		}

		if ( 'headscript' === $section && apply_filters( 'disable_banner_adsense_head', false ) ) {
			return;
		}

		if ( 'bodyscript' === $section && apply_filters( 'disable_banner_adsense_body', false ) ) {
			return;
		}

		if ( 'footerscript' === $section && apply_filters( 'disable_banner_adsense_footer', false ) ) {
			return;
		}

		// Get the default values.
		$options = get_option( 'banner_adsense_settings' );

		if ( ! isset( $options[ $section ] ) || empty( trim( $options[ $section ] ) ) ) {
			return;
		}

		echo wp_unslash( $options[ $section ] ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Prresource initiate.
	 */
	private function __construct() {}

	/**
	 * Prresource cloning.
	 */
	private function __clone() {}
}
