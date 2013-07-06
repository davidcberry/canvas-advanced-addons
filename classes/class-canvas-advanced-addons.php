<?php 

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

/**
 * Canvas Advanced Addons Class
 *
 * All functionality pertaining to the dashboard widget feature.
 *
 * @package WordPress
 * @subpackage Canvas_Advanced_Addons
 * @category Plugin
 * @author Stuart Duff
 * @since 1.0.0
 */
class Canvas_Advanced_Addons {
	private $dir;
	private $assets_dir;
	private $assets_url;
	private $token;
	public $version;
	private $file;

	/**
	 * Constructor function.
	 * 
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function __construct( $file ) {
		$this->dir = dirname( $file );
		$this->file = $file;
		$this->assets_dir = trailingslashit( $this->dir ) . 'assets';
		$this->assets_url = esc_url( trailingslashit( plugins_url( '/assets/', $file ) ) );
		$this->token = 'canvas_advanced_addons';

		$this->load_plugin_textdomain();
		add_action( 'init', array( &$this, 'load_localisation' ), 0 );

		$woo_options = get_option( 'woo_options' );

		// Run this on activation.
		register_activation_hook( $this->file, array( &$this, 'activation' ) );

		//add_action( 'admin_print_styles', array( &$this, 'enqueue_admin_styles' ), 5 );

		add_action( 'init', array( &$this, 'woo_canvas_options_add' ) );

		// Add Social Icons To Header
		if ( isset( $woo_options['woo_head_social_icons'] ) && ( 'true' == $woo_options['woo_head_social_icons'] ) ) {
		add_action( 'woo_header_inside', array( &$this, 'header_social_icons_logic' ), 10 );
		}

		// Add Search Box To Header
		if ( isset( $woo_options['woo_head_searchbox'] ) && ( 'true' == $woo_options['woo_head_searchbox'] ) ) {
		add_action( 'woo_header_inside', array( &$this, 'woo_custom_add_searchform' ), 20 );
		}		

		// Enable Business Slider On Homepage
		if ( isset( $woo_options['woo_biz_slider_homepage'] ) && ( 'true' == $woo_options['woo_biz_slider_homepage'] ) ) {
		add_action( 'get_header', array( &$this, 'business_slider_logic' ) );
		}

		// Enable Magazine Slider On Homepage
		if ( isset( $woo_options['woo_magazine_slider_homepage'] ) && ( 'true' == $woo_options['woo_magazine_slider_homepage'] ) ) {
		add_action( 'get_header', array( &$this, 'magazine_slider_logic' ) );
		}

		// Enable Magazine Page Content
		if ( isset( $woo_options['woo_magazine_page_content'] ) && ( 'true' == $woo_options['woo_magazine_page_content'] ) ) {
		add_action( 'init', array( &$this, 'magazine_page_content_logic' ) );
		}

		// WooCommerce Mini Cart Location
		if ( isset( $woo_options['woo_mini_cart_location'] ) && ( 'top-nav' == $woo_options['woo_mini_cart_location'] ) ) {
			add_action( 'init', array( &$this, 'remove_mini_cart_main_nav' ) );
			add_action( 'wp_nav_menu_items', array( &$this, 'move_mini_cart_to_top_nav' ), 10, 2 );
		}

		// Loads Custom Styling
		add_action( 'woo_head', array( &$this, 'canvas_custom_styling' ) );

	} // End __construct()

	/**
	 * Load the plugin's localisation file.
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function load_localisation () {
		load_plugin_textdomain( 'canvas-advanced-addons', false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	} // End load_localisation()

	/**
	 * Load the plugin textdomain from the main WordPress "languages" folder.
	 * @since  1.0.0
	 * @return  void
	 */
	public function load_plugin_textdomain () {
	    $domain = 'canvas-advanced-addons';
	    // The "plugin_locale" filter is also used in load_plugin_textdomain()
	    $locale = apply_filters( 'plugin_locale', get_locale(), $domain );
	 
	    load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
	    load_plugin_textdomain( $domain, FALSE, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	} // End load_plugin_textdomain()

	/**
	 * Enqueue post type admin CSS.
	 * 
	 * @access public
	 * @since   1.0.0
	 * @return   void
	 */
	public function enqueue_admin_styles () {
		wp_register_style( 'canvas-advanced-addons-admin', $this->assets_url . 'css/admin.css', array(), '1.0.0' );
		wp_enqueue_style( 'canvas-advanced-addons-admin' );
	} // End enqueue_admin_styles()


	/**
	 * Run on activation.
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function activation () {
		$this->register_plugin_version();
	} // End activation()

	/**
	 * Register the plugin's version.
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	private function register_plugin_version () {
		if ( $this->version != '' ) {
			update_option( 'canvas-advanced-addons' . '-version', $this->version );
		}
	} // End register_plugin_version()	

	/**
	 * Display Social Icons In The Header.
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */	
	public function header_social_icons_logic() {

	 global $woo_options; 
	 
	  	$settings = array(
							'feed_url' => '',
							'connect_rss' => '',
							'connect_twitter' => '',
							'connect_facebook' => '',
							'connect_youtube' => '',
							'connect_flickr' => '',
							'connect_linkedin' => '',
							'connect_delicious' => '',
							'connect_rss' => '',
							'connect_googleplus' => '',
							'connect_dribbble' => '',
							'connect_instagram' => '',
							'connect_vimeo' => '',
							'connect_pinterest' => ''
							);
			$settings = woo_get_dynamic_values( $settings );
	 
	 ?>
			
				<div class="social">
			   		<?php if ( $settings['connect_rss' ] == "true" ) { ?>
			   		<a href="<?php if ( $settings['feed_url'] ) { echo esc_url( $settings['feed_url'] ); } else { echo get_bloginfo_rss('rss2_url'); } ?>" class="subscribe" title="RSS"></a>
	 
			   		<?php } if ( $settings['connect_twitter' ] != "" ) { ?>
			   		<a target="_blank" href="<?php echo esc_url( $settings['connect_twitter'] ); ?>" class="twitter" title="Twitter"></a>
	 
			   		<?php } if ( $settings['connect_facebook' ] != "" ) { ?>
			   		<a target="_blank" href="<?php echo esc_url( $settings['connect_facebook'] ); ?>" class="facebook" title="Facebook"></a>
	 
			   		<?php } if ( $settings['connect_youtube' ] != "" ) { ?>
			   		<a target="_blank" href="<?php echo esc_url( $settings['connect_youtube'] ); ?>" class="youtube" title="YouTube"></a>
	 
			   		<?php } if ( $settings['connect_flickr' ] != "" ) { ?>
			   		<a target="_blank" href="<?php echo esc_url( $settings['connect_flickr'] ); ?>" class="flickr" title="Flickr"></a>
	 
			   		<?php } if ( $settings['connect_linkedin' ] != "" ) { ?>
			   		<a target="_blank" href="<?php echo esc_url( $settings['connect_linkedin'] ); ?>" class="linkedin" title="LinkedIn"></a>
	 
			   		<?php } if ( $settings['connect_delicious' ] != "" ) { ?>
			   		<a target="_blank" href="<?php echo esc_url( $settings['connect_delicious'] ); ?>" class="delicious" title="Delicious"></a>
	 
			   		<?php } if ( $settings['connect_googleplus' ] != "" ) { ?>
			   		<a target="_blank" href="<?php echo esc_url( $settings['connect_googleplus'] ); ?>" class="googleplus" title="Google+"></a>
	 
					<?php } if ( $settings['connect_dribbble' ] != "" ) { ?>
			   		<a target="_blank" href="<?php echo esc_url( $settings['connect_dribbble'] ); ?>" class="dribbble" title="Dribbble"></a>
	 
					<?php } if ( $settings['connect_instagram' ] != "" ) { ?>
			   		<a target="_blank" href="<?php echo esc_url( $settings['connect_instagram'] ); ?>" class="instagram" title="Instagram"></a>
	 
					<?php } if ( $settings['connect_vimeo' ] != "" ) { ?>
			   		<a target="_blank" href="<?php echo esc_url( $settings['connect_vimeo'] ); ?>" class="vimeo" title="Vimeo"></a>
	 
					<?php } if ( $settings['connect_pinterest' ] != "" ) { ?>
			   		<a target="_blank" href="<?php echo esc_url( $settings['connect_pinterest'] ); ?>" class="pinterest" title="Pinterest"></a>
	 
					<?php } ?>
				</div>
	 
	<?php } // END header_social_icons_logic ()
	
	/**
	 * Display Search Box In The Header.
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */	
	public function woo_custom_add_searchform() {
	    echo '<div id="header-search" class="header-search fr">' . "";
	    get_template_part( 'search', 'form' );
	    echo '</div><!--/#header-search .header-search fr-->' . "";
	} // End woo_custom_add_searchform()	



	/**
	 * Display the "Business" slider above the default WordPress homepage.
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */	
	public function business_slider_logic() {

		if ( is_front_page() && ! is_paged() ) {
		    add_action( 'woo_main_before_home', 'woo_slider_biz', 10 );
		    add_action( 'woo_main_before_home', 'woo_custom_reset_biz_query', 11 );
		    add_action( 'woo_load_slider_js', '__return_true', 10 );
		    add_filter( 'body_class', 'woo_custom_add_business_bodyclass', 10 );
	    }  // End woo_custom_load_biz_slider()
		 
		function woo_custom_add_business_bodyclass ( $classes ) {
		    if ( is_home() ) {
		        $classes[] = 'business';
		    }
		    return $classes;
		} // End woo_custom_add_biz_bodyclass()
		 
		function woo_custom_reset_biz_query () {
		    wp_reset_query();
		} // End woo_custom_reset_biz_query()		

	} // End full_width_footer_logic()	

	/**
	 * Display the "Magazine" slider above the default WordPress homepage.
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */	
	public function magazine_slider_logic() {

		if ( is_front_page() && ! is_paged() ) {
		    add_action( 'woo_loop_before_home', 'woo_slider_magazine', 10 );
			add_action( 'woo_loop_before_home', 'woo_custom_reset_query', 11 );
			add_action( 'woo_load_slider_js', '__return_true', 10 );
			add_filter( 'body_class', 'woo_custom_add_magazine_bodyclass', 10 );
	    }  // End woo_custom_load_magazine_slider()
		 
		function woo_custom_add_magazine_bodyclass ( $classes ) {
		    if ( is_home() ) {
		        $classes[] = 'magazine';
		    }
		    return $classes;
		} // End woo_custom_add_magazine_bodyclass()
		 
		function woo_custom_reset_query () {
		    wp_reset_query();
		} // End woo_custom_reset_query()		

	} // End full_width_footer_logic()


	/**
	 * Display the Page Content below the magazine slider .
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */	
	public function magazine_page_content_logic() {

		add_action( 'get_template_part_loop', 'woo_custom_display_page_content', 10, 2 );

	    function woo_custom_display_page_content ( $slug, $name ) {
	        if ( $name != 'magazine' ) { return; }
	            wp_reset_query();
	            global $post;
	            setup_postdata( $post );
		?>
	    <div <?php post_class( 'post' ); ?>>
	    <?php the_content(); ?>
	    </div><!--/.post-->
		<?php
	    } // End woo_custom_display_page_content()

	} // End magazine_page_content_logic()

	/**
	 * Remove the mini cart from the main navigation
	 * @access public
	 * @since 1.0.1
	 * @return void
	 **/
	public function remove_mini_cart_main_nav() {
		remove_action( 'woo_nav_inside', 'woo_add_nav_cart_link' );
	} // End remove_mini_cart_main_nav

	/**
	 * Move the mini cart to the top navigation
	 * @access public
	 * @since 1.0.1
	 * @param string $items
	 * @param array $args
	 * @return string
	 **/
	public function move_mini_cart_to_top_nav( $items, $args ) {
		global $woocommerce;
		if ( $args->menu_id == 'top-nav' ) {
			$items .= '</ul><ul class="nav cart fr"><li class="menu-item mini-cart-top-nav"><a class="cart-contents" href="'.esc_url( $woocommerce->cart->get_cart_url() ).'" title="'.esc_attr( 'View your shopping cart', 'woothemes' ).'">'.sprintf( _n('%d item', '%d items', $woocommerce->cart->cart_contents_count, 'woothemes' ), $woocommerce->cart->cart_contents_count ).' - '.$woocommerce->cart->get_cart_total().'</a></li>'; 
		}
		return $items;
	} // End move_mini_cart_to_top_nav

	/**
	 * Canvas Custom Styling.
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */	
	public function canvas_custom_styling() {

		global $woo_options;

		$output = '';

		// Add css for the header social icons
		if ( isset( $woo_options['woo_head_social_icons'] ) && ( 'true' == $woo_options['woo_head_social_icons'] ) ) {

			$output .= '#header .social { float:right; }' . "\n";
			$output .= '#header .social a { filter: alpha(opacity=@opacity * 100); -moz-opacity: 0.8; -khtml-opacity: 0.8; opacity: 0.8; -webkit-transition: all ease-in-out 0.2s; -moz-transition: all ease-in-out 0.2s; -o-transition: all ease-in-out 0.2s; transition: all ease-in-out 0.2s; }' . "\n";
			$output .= '#header .social a:hover { filter: alpha(opacity=@opacity * 100); -moz-opacity: 1; -khtml-opacity: 1; opacity: 1; text-decoration: none; }' . "\n";
			$output .= '#header .social a:before { font-family: Social; font-size: 1.1em; line-height: 1; margin: 0 0.2em 0.6em 0; padding: .53em; display: inline-block; -webkit-border-radius: 300px; -moz-border-radius: 300px; border-radius: 300px; color: #fff; text-shadow: -1px -1px 0 rgba(0, 0, 0, 0.1); }' . "\n";
			$output .= '#header .social a.subscribe:before { content: "\e001"; background-color: #FF6600; }' . "\n";
			$output .= '#header .social a.twitter:before { content: "\e002"; background-color: #00aced; }' . "\n";
			$output .= '#header .social a.facebook:before { content: "\e003"; background-color: #3b5998; }' . "\n";
			$output .= '#header .social a.youtube:before { content: "\e004"; background-color: #af2b26; }' . "\n";
			$output .= '#header .social a.flickr:before { content: "\e005"; background-color: #ff0084; }' . "\n";	
			$output .= '#header .social a.linkedin:before { content: "\e006"; background-color: #71c5ef; }' . "\n";
			$output .= '#header .social a.delicious:before { content: "\e007"; background-color: #285da7; }' . "\n";
			$output .= '#header .social a.googleplus:before { content: "\e008"; background-color: #2d2d2d; font-weight: bold; }' . "\n";
			$output .= '#header .social a.dribbble:before { content: "\e009"; background-color: #ea4c89; }' . "\n";
			$output .= '#header .social a.instagram:before { content: "\e010"; background-color: #517fa4; }' . "\n";
			$output .= '#header .social a.vimeo:before { content: "\e011"; background-color: #33454E; }' . "\n";
			$output .= '#header .social a.pinterest:before { content: "\e012"; background-color: #cb2027; }' . "\n";

		}	

		// Add css for aligning the top navigation menu
		if ( isset( $woo_options['woo_top_nav_align'] ) && ( 'false' != $woo_options['woo_top_nav_align'] ) ) {

			$align_primary_nav = $woo_options['woo_top_nav_align'];

			if ( $align_primary_nav == 'centre' ) :
				$output .= '#top {text-align:center;}'. "\n";
		        $output .= '#top .col-full {float:none;display:inline-block;vertical-align:top;}'. "\n";
		        $output .= '#top .col-full li {display:inline;}'. "\n";
			elseif ( $align_primary_nav == 'right' ) : 
		        $output .= 'ul#top-nav {float:right;}'. "\n";
		    endif;    		        	        

		}				

		// Add css for aligning the primary navigation menu
		if ( isset( $woo_options['woo_primary_nav_align'] ) && ( 'false' != $woo_options['woo_primary_nav_align'] ) ) {

			$align_primary_nav = $woo_options['woo_primary_nav_align'];

			if ( $align_primary_nav == 'centre' ) :
				$output .= '#navigation {text-align:center;}'. "\n";
		        $output .= 'ul#main-nav {float:none;display:inline-block;vertical-align:top;}'. "\n";
		        $output .= 'ul#main-nav li {display:inline;}'. "\n";
			elseif ( $align_primary_nav == 'right' ) : 
		        $output .= 'ul#main-nav {float:right;}'. "\n";
		    endif;    		        	        

		}		

		// Add css for top nav WooCommerce mini cart
		if ( isset( $woo_options['woo_mini_cart_location'] ) && ( 'top-nav' == $woo_options['woo_mini_cart_location'] ) ) {
			$output .= '#top .cart-contents::before {font-family: \'FontAwesome\';display: inline-block;font-size: 100%;margin-right: .618em;font-weight: normal;line-height: 1em;width: 1em;content: "\f07a";}' ."\n";
			$output .= '#top .cart{ margin-right:0px !important;}';
		}

		// Output the CSS to the woo_head function
		if ( '' != $output ) {
			echo "\n" . '<!-- Advanced Canvas CSS Styling -->' . "\n";
			echo '<style type="text/css">' . "\n";
			echo $output;
			echo '</style>' . "\n";
		}

	} // End canvas_custom_styling()


	/**
	 * Integrate Setting into WooFramework
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */	

	public function woo_canvas_options_add() {

		function woo_options_add($options) {

		 	$shortname = 'woo';

		    // Full Width Header Options
		    $options[] = array( 'name' => __( 'Advanced Settings', 'canvas-advanced-addons' ),
								'icon' => 'misc',
							    'type' => 'heading');    

			// Canvas Header Options
			$options[] = array( 'name' => __( 'Header Settings', 'canvas-advanced-addons' ),
								'type' => 'subheading');

			$options[] = array( "name" => __( 'Add Social Icons To Header', 'canvas-advanced-addons' ),
								"desc" => __( 'Enabling this setting will add the subscribe and connect social icons to your header.', 'canvas-advanced-addons' ),
								"id" => $shortname."_head_social_icons",
								"std" => "false",
								"type" => "checkbox" );

			$options[] = array( "name" => __( 'Add Searchbox to Header', 'canvas-advanced-addons' ),
								"desc" => __( 'Enabling this setting will add search input box to your header.', 'canvas-advanced-addons' ),
								"id" => $shortname."_head_searchbox",
								"std" => "false",
								"type" => "checkbox" );			

			$options[] = array( "name" => __( 'Adjust Top Navigation Menu Position', 'canvas-advanced-addons' ),
								"desc" => __( 'Use these settings to adjust the alignment of the items within your Top Navigation Menu area.', 'canvas-advanced-addons' ),
								"id" => $shortname."_top_nav_align",							
								"type" => "select2",
								"options" => array( "false" => __( 'Align Left', 'canvas-advanced-addons' ), "centre" => __( 'Align Centre', 'canvas-advanced-addons' ), "right" => __( 'Align Right', 'canvas-advanced-addons' ) ) );									

			$options[] = array( "name" => __( 'Adjust Primary Navigation Menu Position', 'canvas-advanced-addons' ),
								"desc" => __( 'Use these settings to adjust the alignment of the items within your Primary Navigation Menu area.', 'canvas-advanced-addons' ),
								"id" => $shortname."_primary_nav_align",							
								"type" => "select2",
								"options" => array( "false" => __( 'Align Left', 'canvas-advanced-addons' ), "centre" => __( 'Align Centre', 'canvas-advanced-addons' ), "right" => __( 'Align Right', 'canvas-advanced-addons' ) ) );										


			// Canvas Homepage Options
			$options[] = array( 'name' => __( 'Homepage Settings', 'canvas-advanced-addons' ),
								'type' => 'subheading');

			$options[] = array( "name" => __( 'Add Business Slider To The Homepage', 'canvas-advanced-addons' ),
								"desc" => __( 'This setting will add the business slider to the homepage of your canvas theme.', 'canvas-advanced-addons' ),
								"id" => $shortname."_biz_slider_homepage",
								"std" => "false",
								"type" => "checkbox" );

			$options[] = array( "name" => __( 'Add Magazine Slider To The Homepage', 'canvas-advanced-addons' ),
								"desc" => __( 'This setting will add the magazine slider to the homepage of your canvas theme.', 'canvas-advanced-addons' ),
								"id" => $shortname."_magazine_slider_homepage",
								"std" => "false",
								"type" => "checkbox" );
			

			// Canvas Magazine Template Options
			$options[] = array( 'name' => __( 'Magazine Template Settings', 'canvas-advanced-addons' ),
								'type' => 'subheading');	


			$options[] = array( "name" => __( 'Display Page Content Below The Magazine Slider', 'canvas-advanced-addons' ),
								"desc" => __( 'This setting will display the page content below the magazine slider on the magazine page template.', 'canvas-advanced-addons' ),
								"id" => $shortname."_magazine_page_content",
								"std" => "false",
								"type" => "checkbox" );		

			// Check To See If WooCommerce Is Activated Before Showing The Settings
			if ( is_woocommerce_activated() ) {				

			// Canvas WooCommerce Options
			$options[] = array( 'name' => __( 'WooCommerce Settings', 'canvas-advanced-addons' ),
								'type' => 'subheading' );

			$options[] = array( 'name' => __( 'Mini Cart Location', 'canvas-advanced-addons' ),
								'desc' => __( 'Location where the mini cart is displayed, by default this is in the main navigation.', 'canvas-advanced-addons' ),
								'id' => $shortname . '_mini_cart_location',
								'type' => 'select2',
								'options' => array( 'main-nav' => __( 'Main Navigation', 'canvas-advanced-addons' ), 'top-nav' => __( 'Top Navigation', 'canvas-advanced-addons' ) ),
								'std' => 'main-nav' );

			} // END is_woocommerce_activated()
																									
			return $options;
		 
		}	

	}



} // End Class	




