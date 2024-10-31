<?php
use Elementor\Controls_Manager;
use ElementorPro\Modules\Forms\Module;
class Init
{
    public function __construct()
    {
        add_action('elementor_pro/init', [$this, 'load_integration']);
        add_action('elementor/controls/controls_registered', [$this, 'register_custom_control']);
        add_action('elementor/editor/after_enqueue_scripts', [$this, 'enqueue_script']);
        add_action('wp_ajax_mo_elementor_fetch_custom_fields', [$this, 'fetch_custom_fields']);
        add_action('wp_ajax_mo_elementor_fetch_tags', [$this, 'fetch_tags']);
    }

    public function load_integration()
    {
        Module::instance()->add_form_action('Sendmsg', new Sendmsg_Action_After_Submit());
    }

    public function register_custom_control(Controls_Manager $control_manager)
    {
        $control_manager->register_control('moselect', new CustomSelect());
    }

    public function enqueue_script()
    {

        wp_enqueue_script('mailoptin-elementor', plugin_dir_url( __FILE__ ) . '../assets/js/custom-control.js', ['jquery', 'underscore'], SENDMSGS_VERSION, true);

        wp_localize_script('mailoptin-elementor', 'moElementor', [
            'fields'                  => [],
            'ajax_url'                => admin_url('admin-ajax.php'),
            'nonce'                   => wp_create_nonce('mailoptin-elementor')
        ]);


    }

    public function fetch_custom_fields()
    {
      	$custom_fields = sendmsgs_get_custom_fields();

      	$fields = [];

        foreach ($custom_fields as $field_id => $field_label) {
            $fields[] = [
                'remote_id'    => $field_id,
                'remote_label' => $field_label,
                'remote_type'  => 'text'
            ];
        }

        $response = [
            'fields' => $fields
        ];

        wp_send_json_success($response);
    }

    public function fetch_tags()
    {
       	

    }

    public static function is_mailoptin_detach_libsodium()
    {
        return defined('MAILOPTIN_DETACH_LIBSODIUM');
    }

    /**
     * Singleton poop.
     *
     * @return Init|null
     */
    public static function get_instance()
    {
        static $instance = null;

        if (is_null($instance)) {
            $instance = new self();
        }

        return $instance;
    }
}

Init::get_instance();
