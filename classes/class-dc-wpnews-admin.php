<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Dc_wpnews_Admin Class
 *
 * @class Dc_wpnews_Admin
 * @version	1.0.0
 * @since 1.0.0
 * @package	Dc_wpnews
 * @author Jeffikus
 */
final class Dc_wpnews_Admin {
	/**
	 * Dc_wpnews_Admin The single instance of Dc_wpnews_Admin.
	 * @var 	object
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * The string containing the dynamically generated hook token.
	 * @var     string
	 * @access  private
	 * @since   1.0.0
	 */
	private $_hook;

	/**
	 * Constructor function.
	 * @access  public
	 * @since   1.0.0
	 */
	public function __construct () {
		// Register the settings with WordPress.
		add_action( 'admin_init', array( $this, 'register_settings' ) );

		// Register the settings screen within WordPress.
		add_action( 'admin_menu', array( $this, 'register_settings_screen' ) );
	} // End __construct()

	/**
	 * Main Dc_wpnews_Admin Instance
	 *
	 * Ensures only one instance of Dc_wpnews_Admin is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @return Main Dc_wpnews_Admin instance
	 */
	public static function instance () {
		if ( is_null( self::$_instance ) )
			self::$_instance = new self();
		return self::$_instance;
	} // End instance()

	/**
	 * Register the admin screen.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function register_settings_screen () {
		//$this->_hook = add_submenu_page( 'options-general.php', __( 'DC WPNews Plugin Settings', 'dc-wpnews' ), __( 'DC WPNews Plugin', 'dc-wpnews' ), 'manage_options', 'dc-wpnews', array( $this, 'settings_screen' ) );
	} // End register_settings_screen()

	/**
	 * Output the markup for the settings screen.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function settings_screen () {
		global $title;
		$sections = Dc_wpnews()->settings->get_settings_sections();
		$tab = $this->_get_current_tab( $sections );
		?>
		<div class="wrap dc-wpnews-wrap">
			<?php
				echo $this->get_admin_header_html( $sections, $title );
			?>
			<form action="options.php" method="post">
				<?php
					settings_fields( 'dc-wpnews-settings-' . $tab );
					do_settings_sections( 'dc-wpnews-' . $tab );
					submit_button( __( 'Save Changes', 'dc-wpnews' ) );
				?>
			</form>
		</div><!--/.wrap-->
		<?php
	} // End settings_screen()

	/**
	 * Register the settings within the Settings API.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function register_settings () {
		$sections = Dc_wpnews()->settings->get_settings_sections();
		if ( 0 < count( $sections ) ) {
			foreach ( $sections as $k => $v ) {
				register_setting( 'dc-wpnews-settings-' . sanitize_title_with_dashes( $k ), 'dc-wpnews-' . $k, array( $this, 'validate_settings' ) );
				add_settings_section( sanitize_title_with_dashes( $k ), $v, array( $this, 'render_settings' ), 'dc-wpnews-' . $k, $k, $k );
			}
		}
	} // End register_settings()

	/**
	 * Render the settings.
	 * @access  public
	 * @param  array $args arguments.
	 * @since   1.0.0
	 * @return  void
	 */
	public function render_settings ( $args ) {
		$token = $args['id'];
		$fields = Dc_wpnews()->settings->get_settings_fields( $token );

		if ( 0 < count( $fields ) ) {
			foreach ( $fields as $k => $v ) {
				$args 		= $v;
				$args['id'] = $k;

				add_settings_field( $k, $v['name'], array( Dc_wpnews()->settings, 'render_field' ), 'dc-wpnews-' . $token , $v['section'], $args );
			}
		}
	} // End render_settings()

	/**
	 * Validate the settings.
	 * @access  public
	 * @since   1.0.0
	 * @param   array $input Inputted data.
	 * @return  array        Validated data.
	 */
	public function validate_settings ( $input ) {
		$sections = Dc_wpnews()->settings->get_settings_sections();
		$tab = $this->_get_current_tab( $sections );
		return Dc_wpnews()->settings->validate_settings( $input, $tab );
	} // End validate_settings()

	/**
	 * Return marked up HTML for the header tag on the settings screen.
	 * @access  public
	 * @since   1.0.0
	 * @param   array  $sections Sections to scan through.
	 * @param   string $title    Title to use, if only one section is present.
	 * @return  string 			 The current tab key.
	 */
	public function get_admin_header_html ( $sections, $title ) {
		$defaults = array(
							'tag' => 'h2',
							'atts' => array( 'class' => 'dc-wpnews-wrapper' ),
							'content' => $title
						);

		$args = $this->_get_admin_header_data( $sections, $title );

		$args = wp_parse_args( $args, $defaults );

		$atts = '';
		if ( 0 < count ( $args['atts'] ) ) {
			foreach ( $args['atts'] as $k => $v ) {
				$atts .= ' ' . esc_attr( $k ) . '="' . esc_attr( $v ) . '"';
			}
		}

		$response = '<' . esc_attr( $args['tag'] ) . $atts . '>' . $args['content'] . '</' . esc_attr( $args['tag'] ) . '>' . "\n";

		return $response;
	} // End get_admin_header_html()

	/**
	 * Return the current tab key.
	 * @access  private
	 * @since   1.0.0
	 * @param   array  $sections Sections to scan through for a section key.
	 * @return  string 			 The current tab key.
	 */
	private function _get_current_tab ( $sections = array() ) {
		if ( isset ( $_GET['tab'] ) ) {
			$response = sanitize_title_with_dashes( $_GET['tab'] );
		} else {
			if ( is_array( $sections ) && ! empty( $sections ) ) {
				list( $first_section ) = array_keys( $sections );
				$response = $first_section;
			} else {
				$response = '';
			}
		}

		return $response;
	} // End _get_current_tab()

	/**
	 * Return an array of data, used to construct the header tag.
	 * @access  private
	 * @since   1.0.0
	 * @param   array  $sections Sections to scan through.
	 * @param   string $title    Title to use, if only one section is present.
	 * @return  array 			 An array of data with which to mark up the header HTML.
	 */
	private function _get_admin_header_data ( $sections, $title ) {
		$response = array( 'tag' => 'h2', 'atts' => array( 'class' => 'dc-wpnews-wrapper' ), 'content' => $title );

		if ( is_array( $sections ) && 1 < count( $sections ) ) {
			$response['content'] = '';
			$response['atts']['class'] = 'nav-tab-wrapper';

			$tab = $this->_get_current_tab( $sections );

			foreach ( $sections as $key => $value ) {
				$class = 'nav-tab';
				if ( $tab == $key ) {
					$class .= ' nav-tab-active';
				}

				$response['content'] .= '<a href="' . admin_url( 'options-general.php?page=dc-wpnews&tab=' . sanitize_title_with_dashes( $key ) ) . '" class="' . esc_attr( $class ) . '">' . esc_html( $value ) . '</a>';
			}
		}

		return (array)apply_filters( 'dc-wpnews-get-admin-header-data', $response );
	} // End _get_admin_header_data()
} // End Class
