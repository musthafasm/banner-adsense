<?php
/**
 * The widget class file.
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
class Widget extends \WP_Widget {

	/**
	 * Initialize class.
	 */
	public function __construct() {
		parent::__construct(
			'banner-adsense-widget',
			__( 'Banner Adsense', 'banner-adsense' ),
			array( 'description' => __( 'An advanced image widget for placing multiple banner images or ads.', 'banner-adsense' ) )
		);
	}

	/**
	 * The widget form
	 *
	 * @see WP_Widget::widget
	 *
	 * @param array $args     The display arguments.
	 * @param array $instance The instance values.
	 * @return void
	 */
	public function widget( $args, $instance ) {
		$title  = ! empty( $instance['title'] ) ? $instance['title'] : '';
		$script = ! empty( $instance['script'] ) ? $instance['script'] : '';
		$images = ( ! empty( $instance['images'] ) && is_array( $instance['images'] ) ) ? $instance['images'] : array();

		$html = $args['before_widget'];

		if ( ! empty( $instance['title'] ) ) {
			$html .= $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}

		$widget_id = uniqid( 'banner-adsense-' );

		if ( ! empty( $script ) ) {
			wp_enqueue_script( $widget_id, esc_url( $script ), array(), null ); //phpcs:ignore WordPress.WP.EnqueuedResourceParameters
		}

		$html .= '<ul class="banner-adsense-images">';

		foreach ( $images as $key => $image ) {
			if ( ! empty( $image['imgid'] ) ) {
				$imgsrc    = wp_get_attachment_image_src( $image['imgid'], 'full', false );
				$image_url = ( ! empty( $imgsrc[0] ) && '' !== $imgsrc[0] ) ? $imgsrc[0] : '';
				$target    = ! empty( $image['target'] ) ? '_blank' : '';

				if ( ! empty( $image['geocode'] ) ) {
					wp_enqueue_script( $widget_id . '-' . $key, esc_url( $image['geocode'] ), array(), null ); //phpcs:ignore WordPress.WP.EnqueuedResourceParameters
				}

				$html .= '<li>';
				$html .= ! empty( $image['link'] ) ? '<a target="' . $target . '" href="' . esc_url( $image['link'] ) . '">' : '';
				$html .= '<img style="max-width:100%;height:auto;" class="banner-adsense-image-img" alt="" src="' . esc_url( $image_url ) . '"/>';
				$html .= ! empty( $image['link'] ) ? '</a>' : '';
				$html .= '</li>';
			}
		}

		$html .= '</ul>';
		$html .= $args['after_widget'];

		echo $html; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * The widget form
	 *
	 * @see WP_Widget::update
	 *
	 * @param array $new_instance The new instance values.
	 * @param array $old_instance The old instance values.
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		// Sanitize the fields.
		$instance = $old_instance;

		$instance['title']  = wp_strip_all_tags( $new_instance['title'] );
		$instance['script'] = esc_url( $new_instance['script'] );
		$instance['images'] = array();

		if ( ! empty( $new_instance['imgid'] ) ) {

			foreach ( $new_instance['imgid'] as $key => $value ) {
				if ( ! empty( $value ) ) {
					$instance['images'][] = array(
						'imgid'   => $new_instance['imgid'][ $key ],
						'link'    => esc_url( $new_instance['link'][ $key ] ),
						'target'  => $new_instance['target'][ $key ],
						'geocode' => $new_instance['geocode'][ $key ],
					);
				}
			}
		}

		return $instance;
	}

	/**
	 * The widget form
	 *
	 * @see WP_Widget::form
	 *
	 * @param array $instance The instance values.
	 * @return string
	 */
	public function form( $instance ) {

		$title  = ! empty( $instance['title'] ) ? $instance['title'] : '';
		$script = ! empty( $instance['script'] ) ? $instance['script'] : '';
		$images = ( ! empty( $instance['images'] ) && is_array( $instance['images'] ) ) ? $instance['images'] : array();

		$html = '<p>
			<label for="' . esc_attr( $this->get_field_id( 'title' ) ) . '">' . __( 'Title:', 'banner-adsense' ) . '</label>
			<input id="' . esc_attr( $this->get_field_id( 'title' ) ) . '" name="' . esc_attr( $this->get_field_name( 'title' ) ) . '" type="text" value="' . esc_html( $title ) . '" class="widefat" />
		</p>';

		$html .= '<p>
			<label for="' . esc_attr( $this->get_field_id( 'script' ) ) . '">' . __( 'Script Url:', 'banner-adsense' ) . '</label>
			<input id="' . esc_attr( $this->get_field_id( 'script' ) ) . '" name="' . esc_attr( $this->get_field_name( 'script' ) ) . '" type="text" value="' . esc_url( $script ) . '" class="widefat"/>
		</p>';

		$html .= '<hr style="margin:1.6rem 0;">';
		$html .= '<div class="banner-adsense-block">';
		$html .= '<div class="banner-adsense-clone" style="display:none;">' . $this->get_image_field() . '</div>';
		$html .= '<div class="banner-adsense-images">';

		foreach ( $images as $image ) {
			$html .= $this->get_image_field( $image );
		}

		$html .= '</div>';

		$html .= '<p style="margin-top:1rem;text-align:right;"><a style="text-decoration:none;" class="dashicons-before dashicons-plus add-banner-adsense-image" href="javascript:void(0);">' . __( 'Add Image', 'banner-adsense' ) . '</a></p>';

		$html .= '</div>';

		echo $html; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		return '';
	}

	/**
	 * Get the image form field
	 *
	 * @param array $image The image value.
	 * @return string
	 */
	public function get_image_field( $image = array() ) {

		$imgid     = ! empty( $image['imgid'] ) ? $image['imgid'] : '';
		$link      = ! empty( $image['link'] ) ? $image['link'] : '';
		$target    = ! empty( $image['target'] ) ? 'selected' : '';
		$geocode   = ! empty( $image['geocode'] ) ? $image['geocode'] : '';
		$image_url = ( Banner_Adsense::instance() )->get_url() . '/assets/images/blank.gif';
		$html      = array();

		$html[] = '<div class="banner-adsense-image">';

		if ( ! empty( $imgid ) ) {
			$imgsrc    = wp_get_attachment_image_src( $imgid, 'thumbnail', false );
			$image_url = ( ! empty( $imgsrc[0] ) && '' !== $imgsrc[0] ) ? $imgsrc[0] : $image_url;
		}

		$html[] = '<p style="text-align:center;border:1px dashed #ddd;padding:1rem;"><img style="max-width:100%;height:auto;" class="banner-adsense-image-img" alt="" src="' . esc_url( $image_url ) . '"/></p>';
		$html[] = '<input class="banner-adsense-image-id" name="' . $this->get_field_name( 'imgid' ) . '[]" type="hidden" value="' . $imgid . '" />';

		$html[] = '<p><label>' . __( 'Link:', 'banner-adsense' ) . '</label>';
		$html[] = '<input name="' . $this->get_field_name( 'link' ) . '[]" type="text" value="' . esc_url( $link ) . '" class="widefat" /></p>';

		$html[] = '<p><label>' . __( 'Open Link In:', 'banner-adsense' ) . '</label>';
		$html[] = '<select name="' . $this->get_field_name( 'target' ) . '[]" class="widefat">';
		$html[] = '<option value="">' . __( 'Same Window', 'banner-adsense' ) . '</option>';
		$html[] = '<option ' . $target . ' value="_blank">' . __( 'New Window', 'banner-adsense' ) . '</option>';
		$html[] = '</select></p>';

		$html[] = '<p><label>' . __( 'Script Url:', 'banner-adsense' ) . '</label>';
		$html[] = '<input name="' . $this->get_field_name( 'geocode' ) . '[]" type="text" value="' . esc_url( $geocode ) . '" class="widefat"/></p>';

		$html[] = '<p><button type="button" class="button media-select-btn">' . __( 'Add / Edit Image', 'banner-adsense' ) . '</button>&nbsp;<button type="button" class="button remove-banner-adsense-image">' . __( 'Remove Image', 'banner-adsense' ) . '</button></p>';

		$html[] = '<hr style="margin:1.6rem 0;">';

		$html[] = '</div>';

		return implode( '', $html );
	}
}
