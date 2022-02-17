<?php
/**
 * The plugin settings page file.
 *
 * @package BannerAdsense
 */

namespace Perfomatix\BannerAdsense;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The plugin settings class.
 */
class Admin_Settings {

	/**
	 * Initiate class.
	 *
	 * @return void
	 */
	public function __construct() {}

	/**
	 * Setup class.
	 *
	 * @param string $basename The plugin base name.
	 * @return self
	 */
	public function init( $basename ) {
		// Add settings link.
		add_filter( 'plugin_action_links_' . $basename, array( $this, 'settings_links' ) );

		// Add admin settings sub menu and page.
		add_action( 'admin_menu', array( $this, 'add_admin_pages' ) );

		// Register settings options.
		add_action( 'admin_init', array( $this, 'register_settings_fields' ) );

		// Enque scripts and style.
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

		return $this;
	}

	/**
	 * Enque admin scripts and style.
	 *
	 * @return void
	 */
	public function admin_enqueue_scripts() {

		global $pagenow;

		// Custom styles for the form fields.
		$styles = '.CodeMirror{ border:1px solid #b7b9bd; border-radius:4px; height:250px !important; }';

		wp_add_inline_style( 'code-editor', $styles );

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( ! ( 'options-general.php' === $pagenow && isset( $_GET['page'] ) && 'banner-adsense' === $_GET['page'] ) ) {
			return;
		}

		$editor_args = array( 'type' => 'text/html' );

		if ( ! current_user_can( 'unfiltered_html' ) || ! current_user_can( 'manage_options' ) ) {
			$editor_args['codemirror']['readOnly'] = true;
		}

		// Enqueue code editor and settings for manipulating HTML.
		$settings = wp_enqueue_code_editor( $editor_args );

		// Bail if user disabled CodeMirror.
		if ( false === $settings ) {
			return;
		}

		// CodeMirror script for the fields.
		$scripts  = sprintf( 'jQuery( function() { wp.codeEditor.initialize( "headscript", %s ); } );', wp_json_encode( $settings ) );
		$scripts .= sprintf( 'jQuery( function() { wp.codeEditor.initialize( "bodyscript", %s ); } );', wp_json_encode( $settings ) );
		$scripts .= sprintf( 'jQuery( function() { wp.codeEditor.initialize( "footerscript", %s ); } );', wp_json_encode( $settings ) );

		$scripts .= sprintf(
			'jQuery( function() { wp.codeEditor.initialize( jQuery("code-mirror"), %s ); } );',
			wp_json_encode( $settings )
		);

		wp_add_inline_script( 'code-editor', $scripts );
	}

	/**
	 * Initialise all the basic features.
	 *
	 * @param array $links The default links.
	 * @return array
	 */
	public function settings_links( $links ) {
		$links[] = '<a href="' . admin_url( 'options-general.php?page=banner-adsense' ) . '">' . __( 'Settings', 'banner-adsense' ) . '</a>';
		return $links;
	}

	/**
	 * Add admin pages.
	 *
	 * @return void
	 */
	public function add_admin_pages() {

		add_options_page(
			__( 'Banner Adsense', 'banner-adsense' ),
			__( 'Banner Adsense', 'banner-adsense' ),
			'manage_options',
			'banner-adsense',
			array( $this, 'page_callback' )
		);
	}

	/**
	 * Settings page display callback.
	 *
	 * @return void
	 */
	public function page_callback() {

		echo '<div class="wrap"><h1>' . esc_html( get_admin_page_title() ) . '</h1>';
		echo '<form method="post" enctype="multipart/form-data" action="options.php">';

		settings_fields( 'banner-adsense-settings' );
		do_settings_sections( 'banner-adsense-settings' );
		submit_button();

		echo '</form>';
	}

	/**
	 * Register settings options.
	 *
	 * @return void
	 */
	public function register_settings_fields() {
		// Register the fields.
		register_setting( 'banner-adsense-settings', 'banner_adsense_settings' );

		// Get the default values.
		$options = get_option( 'banner_adsense_settings' );

		// Add the global section.
		add_settings_section(
			'banner-adsense-settings',
			__( 'Global Settings', 'banner-adsense' ),
			array( $this, 'section_description' ),
			'banner-adsense-settings'
		);

		// Add the headscript field.
		add_settings_field(
			'headscript',
			__( 'Head Script', 'banner-adsense' ),
			array( $this, 'render_field' ),
			'banner-adsense-settings',
			'banner-adsense-settings',
			array(
				'type'    => 'textarea',
				'id'      => 'headscript',
				'name'    => 'headscript',
				'class'   => 'large-text',
				'default' => ! empty( $options['headscript'] ) ? $options['headscript'] : '',
				'desc'    => sprintf(
					/* translators: %s: The `<head>` tag */
					esc_html__( 'These scripts will be printed in the %s section.', 'banner-adsense' ),
					'<code>&lt;head&gt;</code>'
				),
			)
		);

		// Add the bodyscript field.
		add_settings_field(
			'bodyscript',
			__( 'Body Script', 'banner-adsense' ),
			array( $this, 'render_field' ),
			'banner-adsense-settings',
			'banner-adsense-settings',
			array(
				'type'    => 'textarea',
				'id'      => 'bodyscript',
				'name'    => 'bodyscript',
				'class'   => 'large-text',
				'default' => ! empty( $options['bodyscript'] ) ? $options['bodyscript'] : '',
				'desc'    => sprintf(
					/* translators: %s: The `<body>` tag */
					esc_html__( 'These scripts will be printed in the %s section.', 'banner-adsense' ),
					'<code>&lt;body&gt;</code>'
				),
			)
		);

		// Add the footerscript field.
		add_settings_field(
			'footerscript',
			__( 'Footer Script', 'banner-adsense' ),
			array( $this, 'render_field' ),
			'banner-adsense-settings',
			'banner-adsense-settings',
			array(
				'type'    => 'textarea',
				'id'      => 'footerscript',
				'name'    => 'footerscript',
				'class'   => 'large-text',
				'default' => ! empty( $options['footerscript'] ) ? $options['footerscript'] : '',
				'desc'    => sprintf(
					/* translators: %s: The `<footer>` tag */
					esc_html__( 'These scripts will be printed in the %s section.', 'banner-adsense' ),
					'<code>&lt;footer&gt;</code>'
				),
			)
		);
	}

	/**
	 * Render section callback.
	 *
	 * @param  array $args The additional params.
	 * @return void
	 */
	public function section_description( $args ) {
		echo __( 'The global settings for the plugin.', 'banner-adsense' ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Render input field callback.
	 *
	 * @param  array $args The additional params.
	 * @return void
	 */
	public function render_field( $args ) {

		$args['name'] = 'banner_adsense_settings[' . $args['name'] . ']';
		$html         = '';
		$checked      = '';

		if ( 'textarea' === $args['type'] ) {
			$html = sprintf(
				'<textarea id="%s" name="%s" rows="3" class="%s">%s</textarea>',
				esc_attr( $args['id'] ),
				esc_attr( $args['name'] ),
				esc_attr( $args['class'] ),
				esc_attr( $args['default'] )
			);
		} elseif ( 'select' === $args['type'] ) {
			$html .= sprintf(
				'<select id="%s" name="%s" class="%s">',
				esc_attr( $args['id'] ),
				esc_attr( $args['name'] ),
				esc_attr( $args['class'] )
			);

			if ( ! empty( $args['options'] ) && is_array( $args['options'] ) ) {
				foreach ( $args['options'] as $key => $value ) {
					$selected = ( (string) $args['default'] === (string) $value['id'] ) ? 'selected' : '';
					$html    .= sprintf(
						'<option %s value="%s">%s</option>',
						$selected,
						esc_attr( $value['id'] ),
						esc_attr( $value['text'] )
					);
				}
			}

			$html .= '</slect>';
		} else {
			if ( 'checkbox' === $args['type'] || 'radio' === $args['type'] ) {
				if ( isset( $args['value'] ) && $args['value'] === $args['default'] ) {
					$checked = 'checked="checked"';
				}
			}
			$html .= sprintf(
				'<input type="%s" id="%s" name="%s" class="%s" value="%s" %s />',
				esc_attr( $args['type'] ),
				esc_attr( $args['id'] ),
				esc_attr( $args['name'] ),
				esc_attr( $args['class'] ),
				esc_attr( $args['default'] ),
				$checked
			);
		}

		if ( $args['desc'] ) {
			$html .= sprintf( '<br><em><span class="description">%s</span></em>', wp_kses( $args['desc'], 'post' ) );
		}

		echo $html; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
