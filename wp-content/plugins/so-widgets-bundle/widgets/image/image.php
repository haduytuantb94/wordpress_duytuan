<?php
/*
Widget Name: Image
Description: A simple image widget with massive power.
Author: SiteOrigin
Author URI: https://siteorigin.com
Documentation: https://siteorigin.com/widgets-bundle/image-widget-documentation/
*/

class SiteOrigin_Widget_Image_Widget extends SiteOrigin_Widget {
	function __construct() {
		parent::__construct(
			'sow-image',
			__('SiteOrigin Image', 'so-widgets-bundle'),
			array(
				'description' => __('A simple image widget with massive power.', 'so-widgets-bundle'),
				'help' => 'https://siteorigin.com/widgets-bundle/image-widget-documentation/'
			),
			array(

			),
			false,
			plugin_dir_path(__FILE__)
		);
	}

	function get_widget_form() {

		return array(
			'image' => array(
				'type' => 'media',
				'label' => __('Image file', 'so-widgets-bundle'),
				'library' => 'image',
				'fallback' => true,
			),

			'size' => array(
				'type' => 'image-size',
				'label' => __('Image size', 'so-widgets-bundle'),
				'custom_size' => true,
			),

			'align' => array(
				'type' => 'select',
				'label' => __('Image alignment', 'so-widgets-bundle'),
				'default' => 'default',
				'options' => array(
					'default' => __('Default', 'so-widgets-bundle'),
					'left' => __('Left', 'so-widgets-bundle'),
					'right' => __('Right', 'so-widgets-bundle'),
					'center' => __('Center', 'so-widgets-bundle'),
				),
			),

			'title_align' => array(
				'type' => 'select',
				'label' => __( 'Title alignment', 'so-widgets-bundle' ),
				'default' => 'default',
				'options' => array(
					'default' => __( 'Default', 'so-widgets-bundle' ),
					'left' =>    __( 'Left', 'so-widgets-bundle' ),
					'right' =>   __( 'Right', 'so-widgets-bundle' ),
					'center' =>  __( 'Center', 'so-widgets-bundle' ),
				),
			),

			'title' => array(
				'type' => 'text',
				'label' => __('Title text', 'so-widgets-bundle'),
			),

			'title_position' => array(
				'type' => 'select',
				'label' => __('Title position', 'so-widgets-bundle'),
				'default' => 'hidden',
				'options' => array(
					'hidden' => __( 'Hidden', 'so-widgets-bundle' ),
					'above' => __( 'Above', 'so-widgets-bundle' ),
					'below' => __( 'Below', 'so-widgets-bundle' ),
				),
			),

			'alt' => array(
				'type' => 'text',
				'label' => __('Alt text', 'so-widgets-bundle'),
			),

			'url' => array(
				'type' => 'link',
				'label' => __('Destination URL', 'so-widgets-bundle'),
			),
			'new_window' => array(
				'type' => 'checkbox',
				'default' => false,
				'label' => __('Open in new window', 'so-widgets-bundle'),
			),

			'bound' => array(
				'type' => 'checkbox',
				'default' => true,
				'label' => __('Bound', 'so-widgets-bundle'),
				'description' => __("Make sure the image doesn't extend beyond its container.", 'so-widgets-bundle'),
			),
			'full_width' => array(
				'type' => 'checkbox',
				'default' => false,
				'label' => __('Full Width', 'so-widgets-bundle'),
				'description' => __("Resize image to fit its container.", 'so-widgets-bundle'),
			),

		);
	}

	function get_style_hash($instance) {
		return substr( md5( serialize( $this->get_less_variables( $instance ) ) ), 0, 12 );
	}

	public function get_template_variables( $instance, $args ) {
		$title = $this->get_image_title( $instance );

		// Add support for custom sizes.
		if (
			$instance['size'] == 'custom_size' &&
			! empty( $instance['size_width'] ) &&
			! empty( $instance['size_height'] )
		) {
			$instance['size'] = array(
				(int) $instance['size_width'],
				(int) $instance['size_height'],
			);
		}

		$src = siteorigin_widgets_get_attachment_image_src(
			$instance['image'],
			$instance['size'],
			! empty( $instance['image_fallback'] ) ? $instance['image_fallback'] : false
		);

		$attr = array();
		if( !empty($src) ) {
			$attr = array( 'src' => $src[0] );

			if ( ! empty( $src[1] ) ) {
				$attr['width'] = $src[1];
			}

			if ( ! empty( $src[2] ) ) {
				$attr['height'] = $src[2];
			}

			if ( function_exists( 'wp_get_attachment_image_srcset' ) ) {
				$attr['srcset'] = wp_get_attachment_image_srcset( $instance['image'], $instance['size'] );
			}
			// Don't add sizes attribute when Jetpack Photon is enabled, as it tends to have unexpected side effects.
			// This was to hotfix an issue. Can remove it when we find a way to make sure output of
			// `wp_get_attachment_image_sizes` is predictable with Photon enabled.
			if ( ! ( class_exists( 'Jetpack_Photon' ) && Jetpack::is_module_active( 'photon' ) ) ) {
				if ( function_exists( 'wp_get_attachment_image_sizes' ) ) {
					$attr['sizes'] = wp_get_attachment_image_sizes( $instance['image'], $instance['size'] );
				}
			}
		}
		$attr = apply_filters( 'siteorigin_widgets_image_attr', $attr, $instance, $this );

		$attr['title'] = $title;

		if ( ! empty( $instance['alt'] ) ) {
			$attr['alt'] = $instance['alt'];
		} else {
			$attr['alt'] = get_post_meta( $instance['image'], '_wp_attachment_image_alt', true );
		}

		if ( function_exists( 'wp_lazy_loading_enabled' ) && wp_lazy_loading_enabled( 'img', 'sow-image' ) ) {
			$attr['loading'] = 'lazy';
		}
		
		$link_atts = array();
		if ( ! empty( $instance['new_window'] ) ) {
			$link_atts['target'] = '_blank';
			$link_atts['rel'] = 'noopener noreferrer';
		}

		return apply_filters( 'siteorigin_widgets_image_args',
			array(
				'title' => $title,
				'title_position' => $instance['title_position'],
				'url' => $instance['url'],
				'new_window' => $instance['new_window'],
				'link_attributes' => $link_atts,
				'attributes' => $attr,
				'classes' => array( 'so-widget-image' ),
			),
			$instance,
			$this
		);
	}

	/**
	 * Try to figure out an image's title for display.
	 *
	 * @param $image
	 *
	 * @return string The title of the image.
	 */
	private function get_image_title( $image ) {
		if ( ! empty( $image['title'] ) ) {
			$title = $image['title'];
		} else if ( apply_filters( 'siteorigin_widgets_auto_title', true, 'sow-image' ) ) {
			$title = wp_get_attachment_caption( $image['image'] );
			if ( empty( $title ) ) {
				// We do not want to use the default image titles as they're based on the file name without the extension
				$file_name = pathinfo( get_post_meta( $image['image'], '_wp_attached_file', true ), PATHINFO_FILENAME );
				$title = get_the_title( $image['image'] );
				if ( $title == $file_name ) {
					$title = '';
				}
			}
		} else {
			$title = '';
		}

		return $title;
	}

	function get_less_variables($instance){
		if ( empty( $instance ) ) {
			return array();
		}
		return array(
			'title_alignment' => ! empty( $instance['title_align'] ) ? $instance['title_align'] : '',
			'image_alignment' => $instance['align'],
			'image_max_width' => ! empty( $instance['bound'] ) ? '100%' : '',
			'image_height' => ! empty( $instance['bound'] ) ? 'auto' : '',
			'image_width' => ! empty( $instance['full_width'] ) ? '100%' : '',
		);
	}

	function get_form_teaser(){
		if( class_exists( 'SiteOrigin_Premium' ) ) return false;

		return sprintf(
			__( 'Add a Lightbox to your images with %sSiteOrigin Premium%s', 'so-widgets-bundle' ),
			'<a href="https://siteorigin.com/downloads/premium/?featured_addon=plugin/lightbox" target="_blank" rel="noopener noreferrer">',
			'</a>'
		);
	}
}

siteorigin_widget_register('sow-image', __FILE__, 'SiteOrigin_Widget_Image_Widget');
