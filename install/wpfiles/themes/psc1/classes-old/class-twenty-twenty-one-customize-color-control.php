<?php
/**
 * Customize API: WP_Customize_Color_Control class
 *
 * @package WordPress
 * @subpackage PSC_1
 * @since PSC1 1.0
 */

/**
 * Customize Color Control class.
 *
 * @since PSC1 1.0
 *
 * @see WP_Customize_Control
 */
class PSC_1_Customize_Color_Control extends WP_Customize_Color_Control {
	/**
	 * The control type.
	 *
	 * @since PSC1 1.0
	 *
	 * @var string
	 */
	public $type = 'twenty-twenty-one-color';

	/**
	 * Colorpicker palette
	 *
	 * @access public
	 *
	 * @since PSC1 1.0
	 *
	 * @var array
	 */
	public $palette;

	/**
	 * Enqueue control related scripts/styles.
	 *
	 * @access public
	 *
	 * @since PSC1 1.0
	 *
	 * @return void
	 */
	public function enqueue() {
		parent::enqueue();

		// Enqueue the script.
		wp_enqueue_script(
			'primarysourceone-control-color',
			get_theme_file_uri( 'assets/js/palette-colorpicker.js' ),
			array( 'customize-controls', 'jquery', 'customize-base', 'wp-color-picker' ),
			(string) filemtime( get_theme_file_path( 'assets/js/palette-colorpicker.js' ) ),
			false
		);
	}

	/**
	 * Refresh the parameters passed to the JavaScript via JSON.
	 *
	 * @access public
	 *
	 * @since PSC1 1.0
	 *
	 * @uses WP_Customize_Control::to_json()
	 *
	 * @return void
	 */
	public function to_json() {
		parent::to_json();
		$this->json['palette'] = $this->palette;
	}
}
