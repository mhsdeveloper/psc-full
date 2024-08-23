<?php
/**
 * Customize API: PSC_1_Customize_Notice_Control class
 *
 * @package WordPress
 * @subpackage PSC_1
 * @since PSC1 1.0
 */

/**
 * Customize Notice Control class.
 *
 * @since PSC1 1.0
 *
 * @see WP_Customize_Control
 */
class PSC_1_Customize_Notice_Control extends WP_Customize_Control {
	/**
	 * The control type.
	 *
	 * @since PSC1 1.0
	 *
	 * @var string
	 */
	public $type = 'twenty-twenty-one-notice';

	/**
	 * Renders the control content.
	 *
	 * This simply prints the notice we need.
	 *
	 * @access public
	 *
	 * @since PSC1 1.0
	 *
	 * @return void
	 */
	public function render_content() {
		?>
		<div class="notice notice-warning">
			<p><?php esc_html_e( 'To access the Dark Mode settings, select a light background color.', 'primarysourceone' ); ?></p>
			<p><a href="<?php echo esc_url( __( 'https://wordpress.org/support/article/twenty-twenty-one/#dark-mode-support', 'primarysourceone' ) ); ?>">
				<?php esc_html_e( 'Learn more about Dark Mode.', 'primarysourceone' ); ?>
			</a></p>
		</div>
		<?php
	}
}
