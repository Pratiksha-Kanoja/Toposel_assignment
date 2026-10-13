<?php
/**
 * Page-level settings: brand, top bar, sidebar and section labels.
 */

defined( 'ABSPATH' ) || exit;

final class TPA_Settings {

	public const OPTION = 'tpa_settings';
	private const GROUP = 'tpa_settings_group';

	/** @var array|null */
	private static $cache = null;

	public static function hooks(): void {
		add_action( 'admin_init', array( self::class, 'register' ) );
		add_action( 'update_option_' . self::OPTION, array( self::class, 'flush_cache' ) );
		add_action( 'add_option_' . self::OPTION, array( self::class, 'flush_cache' ) );
	}

	public static function register(): void {
		register_setting(
			self::GROUP,
			self::OPTION,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( self::class, 'sanitize' ),
				'default'           => self::defaults(),
			)
		);
	}

	public static function defaults(): array {
		return array(
			'brand_name'          => 'BuyMore',
			'display_mode'        => 'canvas',
			'currency'            => '$',
			'orders_count'        => 37,
			'orders_label'        => __( 'Orders', 'toposel-assignment' ),
			'orders_period'       => __( 'Last 7 days', 'toposel-assignment' ),
			'cart_label'          => __( 'Cart', 'toposel-assignment' ),
			'cart_url'            => '',
			'user_name'           => '',
			'user_avatar_id'      => 0,
			'team_avatar_ids'     => '',
			'team_extra'          => '',
			'nav_items'           => array(
				array( 'label' => __( 'Popular Products', 'toposel-assignment' ), 'url' => '', 'icon' => 'bolt', 'active' => '' ),
				array( 'label' => __( 'Explore New', 'toposel-assignment' ), 'url' => '', 'icon' => 'compass', 'active' => '1' ),
				array( 'label' => __( 'Clothing and Shoes', 'toposel-assignment' ), 'url' => '', 'icon' => 'cart', 'active' => '' ),
				array( 'label' => __( 'Gifts and Living', 'toposel-assignment' ), 'url' => '', 'icon' => 'archive', 'active' => '' ),
				array( 'label' => __( 'Inspiration', 'toposel-assignment' ), 'url' => '', 'icon' => 'bulb', 'active' => '' ),
			),
			'quick_actions_title' => __( 'Quick actions', 'toposel-assignment' ),
			'quick_actions'       => array(
				array( 'label' => __( 'Request for product', 'toposel-assignment' ), 'url' => '' ),
				array( 'label' => __( 'Add member', 'toposel-assignment' ), 'url' => '' ),
			),
			'orders_title'        => __( 'Last orders', 'toposel-assignment' ),
			'recent_orders'       => array(),
			'recent_orders_url'   => '',
			'explore_title'       => __( 'Explore', 'toposel-assignment' ),
			'favourites_title'    => __( 'Favourites', 'toposel-assignment' ),
			'favourites_cta'      => __( 'See All', 'toposel-assignment' ),
			'favourites_url'      => '',
		);
	}

	/**
	 * Field schema grouped into the cards shown on the settings screen.
	 */
	public static function sections(): array {
		return array(
			'general' => array(
				'title'  => __( 'General', 'toposel-assignment' ),
				'fields' => array(
					'brand_name'   => array( 'type' => 'text', 'label' => __( 'Brand name', 'toposel-assignment' ), 'description' => __( 'The second capitalised word gets the accent underline, e.g. Buy|More.', 'toposel-assignment' ) ),
					'display_mode' => array(
						'type'    => 'select',
						'label'   => __( 'Page layout', 'toposel-assignment' ),
						'choices' => array(
							'canvas' => __( 'Full page (hide theme header and footer)', 'toposel-assignment' ),
							'theme'  => __( 'Inside the theme layout', 'toposel-assignment' ),
						),
					),
					'currency'     => array( 'type' => 'text', 'label' => __( 'Currency symbol', 'toposel-assignment' ) ),
				),
			),
			'topbar'  => array(
				'title'  => __( 'Top bar', 'toposel-assignment' ),
				'fields' => array(
					'orders_count'    => array( 'type' => 'number', 'label' => __( 'Orders count', 'toposel-assignment' ) ),
					'orders_label'    => array( 'type' => 'text', 'label' => __( 'Orders label', 'toposel-assignment' ) ),
					'orders_period'   => array( 'type' => 'text', 'label' => __( 'Orders period', 'toposel-assignment' ) ),
					'cart_label'      => array( 'type' => 'text', 'label' => __( 'Cart button label', 'toposel-assignment' ) ),
					'cart_url'        => array( 'type' => 'url', 'label' => __( 'Cart URL', 'toposel-assignment' ) ),
					'user_name'       => array( 'type' => 'text', 'label' => __( 'User name', 'toposel-assignment' ), 'description' => __( 'Leave empty to show the logged-in user.', 'toposel-assignment' ) ),
					'user_avatar_id'  => array( 'type' => 'image', 'label' => __( 'User avatar', 'toposel-assignment' ), 'description' => __( 'Falls back to the logged-in user\'s Gravatar.', 'toposel-assignment' ) ),
					'team_extra'      => array( 'type' => 'text', 'label' => __( 'Team counter', 'toposel-assignment' ), 'placeholder' => '+8' ),
					'team_avatar_ids' => array( 'type' => 'gallery', 'label' => __( 'Team avatars (first three are shown)', 'toposel-assignment' ) ),
				),
			),
			'sidebar' => array(
				'title'  => __( 'Sidebar', 'toposel-assignment' ),
				'fields' => array(
					'nav_items'           => array(
						'type'      => 'repeater',
						'label'     => __( 'Navigation', 'toposel-assignment' ),
						'add_label' => __( 'Add menu item', 'toposel-assignment' ),
						'fields'    => array(
							'label'  => array( 'type' => 'text', 'label' => __( 'Label', 'toposel-assignment' ) ),
							'url'    => array( 'type' => 'url', 'label' => __( 'Link', 'toposel-assignment' ) ),
							'icon'   => array( 'type' => 'icon', 'label' => __( 'Icon', 'toposel-assignment' ) ),
							'active' => array( 'type' => 'checkbox', 'label' => __( 'Highlight', 'toposel-assignment' ) ),
						),
					),
					'quick_actions_title' => array( 'type' => 'text', 'label' => __( 'Quick actions heading', 'toposel-assignment' ) ),
					'quick_actions'       => array(
						'type'      => 'repeater',
						'label'     => __( 'Quick actions', 'toposel-assignment' ),
						'add_label' => __( 'Add action', 'toposel-assignment' ),
						'fields'    => array(
							'label' => array( 'type' => 'text', 'label' => __( 'Label', 'toposel-assignment' ) ),
							'url'   => array( 'type' => 'url', 'label' => __( 'Link', 'toposel-assignment' ) ),
						),
					),
					'orders_title'        => array( 'type' => 'text', 'label' => __( 'Recent orders heading', 'toposel-assignment' ) ),
					'recent_orders_url'   => array( 'type' => 'url', 'label' => __( '"See all" orders link', 'toposel-assignment' ) ),
					'recent_orders'       => array(
						'type'      => 'repeater',
						'label'     => __( 'Recent orders', 'toposel-assignment' ),
						'add_label' => __( 'Add order', 'toposel-assignment' ),
						'fields'    => array(
							'title'    => array( 'type' => 'text', 'label' => __( 'Product', 'toposel-assignment' ) ),
							'url'      => array( 'type' => 'url', 'label' => __( 'Order link', 'toposel-assignment' ) ),
							'image_id' => array( 'type' => 'image', 'label' => __( 'Thumbnail', 'toposel-assignment' ) ),
						),
					),
				),
			),
			'explore' => array(
				'title'  => __( 'Explore section', 'toposel-assignment' ),
				'fields' => array(
					'explore_title'    => array( 'type' => 'text', 'label' => __( 'Section title', 'toposel-assignment' ) ),
					'favourites_title' => array( 'type' => 'text', 'label' => __( 'Favourites title', 'toposel-assignment' ) ),
					'favourites_cta'   => array( 'type' => 'text', 'label' => __( 'Favourites button label', 'toposel-assignment' ) ),
					'favourites_url'   => array( 'type' => 'url', 'label' => __( 'Favourites button link', 'toposel-assignment' ) ),
				),
			),
		);
	}

	public static function all(): array {
		if ( null === self::$cache ) {
			$saved       = get_option( self::OPTION, array() );
			self::$cache = array_merge( self::defaults(), is_array( $saved ) ? $saved : array() );
		}
		return self::$cache;
	}

	/**
	 * @return mixed
	 */
	public static function get( string $key ) {
		$all = self::all();
		return $all[ $key ] ?? null;
	}

	public static function flush_cache(): void {
		self::$cache = null;
	}

	public static function sanitize( $input ): array {
		$input = is_array( $input ) ? $input : array();
		$clean = array();
		foreach ( self::sections() as $section ) {
			foreach ( $section['fields'] as $key => $field ) {
				$clean[ $key ] = TPA_Fields::sanitize( $field, $input[ $key ] ?? '' );
			}
		}
		return $clean;
	}

	public static function render_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$values  = self::all();
		$page_id = (int) get_option( TPA_Installer::PAGE_OPTION );
		?>
		<div class="wrap tpa-admin">
			<h1><?php esc_html_e( 'BuyMore Explore – Page Settings', 'toposel-assignment' ); ?></h1>
			<p class="tpa-admin__intro">
				<?php
				printf(
					/* translators: %s: shortcode */
					esc_html__( 'Products and banners are managed under BuyMore → Products and Promotions. Place the page anywhere with the shortcode %s.', 'toposel-assignment' ),
					'<code>[' . esc_html( TPA_Shortcode::TAG ) . ']</code>'
				);
				if ( $page_id && get_post_status( $page_id ) === 'publish' ) {
					printf( ' <a href="%1$s" target="_blank" rel="noopener">%2$s</a>', esc_url( get_permalink( $page_id ) ), esc_html__( 'View page →', 'toposel-assignment' ) );
				}
				?>
			</p>
			<?php settings_errors(); ?>
			<form method="post" action="options.php">
				<?php settings_fields( self::GROUP ); ?>
				<?php foreach ( self::sections() as $section ) : ?>
					<div class="tpa-admin__card">
						<h2><?php echo esc_html( $section['title'] ); ?></h2>
						<div class="tpa-fields">
							<?php
							foreach ( $section['fields'] as $key => $field ) {
								TPA_Fields::row( $field, self::OPTION . '[' . $key . ']', $values[ $key ] ?? '' );
							}
							?>
						</div>
					</div>
				<?php endforeach; ?>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}
}
