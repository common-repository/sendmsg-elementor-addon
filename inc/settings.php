<?php
/**
 * Setting page for the admin panel
 */
class SendmsgAPI {
	private $sendmsg_api_options;

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'sendmsg_api_add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'sendmsg_api_page_init' ) );
	}

	public function sendmsg_api_add_plugin_page() {
		add_options_page(
			'Sendmsg API', // page_title
			'Sendmsg API', // menu_title
			'manage_options', // capability
			'sendmsg-api', // menu_slug
			array( $this, 'sendmsg_api_create_admin_page' ) // function
		);
	}

	public function sendmsg_api_create_admin_page() {
		$this->sendmsg_api_options = get_option( 'sendmsg_api_option_name' ); 
		$sendmsg_api_options = get_option( 'sendmsg_api_option_name' ); 
		$send_msg_token = $sendmsg_api_options['send_msg_token'];
		?>
		<div class="wrap">
			<div class="send_msg_wrap">
				<div class="send_msg_inr">
					<h2 class="sendmsg-head">Sendmsg API</h2>
					<form method="post" action="options.php" class="send-msg-form">
						<div class="sendmsg-notification">
						</div>
						<?php
							settings_fields( 'sendmsg_api_option_group' );
							do_settings_sections( 'sendmsg-api-admin' );
							submit_button();
						?>
							<a href="javascript:void(0);" class="connect-now">Connect Now</a>
					</form>
					<div class="loading-icon">
						<div class="loading-icon-inner">
							<img src="<?php echo esc_url(plugin_dir_url( __FILE__ ).'../assets/img/spinner.gif'); ?>" />
						</div>
					</div>

					<?php 
						if(!empty($send_msg_token)){
							?>
							<div class="connected-already">
								Connected
							</div>
							<?php
						}
					?>


				</div>
			</div>
		</div>
	<?php }

	public function sendmsg_api_page_init() {
		register_setting(
			'sendmsg_api_option_group', // option_group
			'sendmsg_api_option_name', // option_name
			array( $this, 'sendmsg_api_sanitize' ) // sanitize_callback
		);

		add_settings_section(
			'sendmsg_api_setting_section', // id
			'Settings', // title
			array( $this, 'sendmsg_api_section_info' ), // callback
			'sendmsg-api-admin' // page
		);

		add_settings_field(
			'site_id_0', // id
			'Site ID', // title
			array( $this, 'site_id_0_callback' ), // callback
			'sendmsg-api-admin', // page
			'sendmsg_api_setting_section' // section
		);

		add_settings_field(
			'api_key_1', // id
			'API Key', // title
			array( $this, 'api_key_1_callback' ), // callback
			'sendmsg-api-admin', // page
			'sendmsg_api_setting_section' // section
		);

		add_settings_field(
			'send_msg_token', // id
			'Token', // title
			array( $this, 'token_1_callback' ), // callback
			'sendmsg-api-admin', // page
			'sendmsg_api_setting_section' // section
		);

	}

	public function sendmsg_api_sanitize($input) {
		$sanitary_values = array();
		if ( isset( $input['site_id_0'] ) ) {
			$sanitary_values['site_id_0'] = sanitize_text_field( $input['site_id_0'] );
		}

		if ( isset( $input['api_key_1'] ) ) {
			$sanitary_values['api_key_1'] = sanitize_text_field( $input['api_key_1'] );
		}

		if ( isset( $input['send_msg_token'] ) ) {
			$sanitary_values['send_msg_token'] = sanitize_text_field( $input['send_msg_token'] );
		}


		return $sanitary_values;
	}

	public function sendmsg_api_section_info() {
		
	}

	public function site_id_0_callback() {
		printf(
			'<input class="regular-text" type="text" name="sendmsg_api_option_name[site_id_0]" id="site_id_0" value="%s">',
			isset( $this->sendmsg_api_options['site_id_0'] ) ? esc_attr( $this->sendmsg_api_options['site_id_0']) : ''
		);
	}

	public function api_key_1_callback() {
		printf(
			'<input class="regular-text" type="password" name="sendmsg_api_option_name[api_key_1]" id="api_key_1" value="%s">',
			isset( $this->sendmsg_api_options['api_key_1'] ) ? esc_attr( $this->sendmsg_api_options['api_key_1']) : ''
		);
	}

	public function token_1_callback() {
		printf(
			'<input class="regular-text" type="text" name="sendmsg_api_option_name[send_msg_token]" id="send_msg_token" value="%s">',
			isset( $this->sendmsg_api_options['send_msg_token'] ) ? esc_attr( $this->sendmsg_api_options['send_msg_token']) : ''
		);
	}

}
if ( is_admin() )
	$sendmsg_api = new SendmsgAPI();

/* 
 * Retrieve this value with:
 * $sendmsg_api_options = get_option( 'sendmsg_api_option_name' ); // Array of All Options
 * $site_id_0 = $sendmsg_api_options['site_id_0']; // Site ID
 * $api_key_1 = $sendmsg_api_options['api_key_1']; // API Key
 */
