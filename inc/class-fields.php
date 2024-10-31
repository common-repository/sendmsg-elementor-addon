<?php
/**
** Including Custom Control 
**/
//namespace ElementorPro\Modules\Forms\Actions;

//require_once('custom-control.php');

use Elementor\Controls_Manager;
use ElementorPro\Modules\Forms\Classes\Action_Base;
use ElementorPro\Modules\Forms\Controls\Fields_Map;
//use MailOptin\Core\AjaxHandler;

class Sendmsg_Action_After_Submit extends \ElementorPro\Modules\Forms\Classes\Action_Base {
    /**
     * Get Name
     *
     * Return the action name
     *
     * @access public
     * @return string
     */
    public function get_name() {
        return 'Sendmsg';
    }

    /**
     * Get Label
     *
     * Returns the action label
     *
     * @access public
     * @return string
     */
    public function get_label() {
        return __( 'Sendmsg', 'text-domain' );
    }

    /**
     * Run
     *
     * Runs the action after submit
     *
     * @access public
     * @param \ElementorPro\Modules\Forms\Classes\Form_Record $record
     * @param \ElementorPro\Modules\Forms\Classes\Ajax_Handler $ajax_handler
     */
    public function run( $record, $ajax_handler ) {
        $settings = $record->get( 'form_settings' );
        $error_flag_pe = false;

        //  Make sure that there is a sendmsg installation url
        if ( empty( $settings['sendmsg_url'] ) ) {
            return;
        }

        // Get sumitetd Form data
        $form_custom_field_mappings = array();

        $fields_map = $record->get_form_settings('mailoptin_fields_map');


       // print_r($fields_map);


        $list_id = $record->get_form_settings('sendmsg_url');

        $sendmsg_remove = $record->get_form_settings('sendmsg_remove');

        $posted_data = $record->get('sent_data');
   
       // print_r($posted_data);

        $final_data = array();
        $ready_to_send = array();

        $remote_labels_indexed = sendmsgs_get_custom_fields();


        if (is_array($fields_map) && !empty($fields_map)) {
            foreach ($fields_map as $mapped_field) {
                $form_custom_field_mappings[$mapped_field['remote_id']] = $mapped_field['local_id'];
            }
        }


        foreach ($form_custom_field_mappings as $key => $value) {
            
            foreach ($posted_data as $postedkey => $postedvalue) {
                
                    if(strtoupper($value) == strtoupper($postedkey)){

                        $final_data[$key] = $postedvalue;

                    }

            }

        }

        $email_phone = array(
            'eMail' => $final_data['eMail'],
            'cellPhone' => $final_data['cellPhone']
        );

        if(!empty($sendmsg_remove)){
           $response_remove = sendmsgs_delete_user_from_lists($final_data['eMail'],$final_data['cellPhone'],$sendmsg_remove);
        }


        if(empty($email_phone['cellPhone']) && empty($email_phone['eMail'])){
            $error_flag_pe = true;
        }
        
        if($error_flag_pe == true){
            $ajax_handler->add_error_message("Error: Phone or Email is required.");
            return;
        }

        unset($final_data['eMail']);
        unset($final_data['cellPhone']);

        foreach ($final_data as $key => $value) {
           $ready_to_send[] = array("Key" => $remote_labels_indexed[$key], 'Value' => $value);
        }

        $response = sendmsgs_submit_data($ready_to_send,$email_phone,$list_id);

        if ($response['error']) {
            $ajax_handler->add_error_message("Error");
            return;
        }

        $ajax_handler->set_success(true);

    }

    /**
     * Register Settings Section
     *
     * Registers the Action controls
     *
     * @access public
     * @param \Elementor\Widget_Base $widget
     */
    public function register_settings_section( $widget ) {

        $sendmsg_api_options = get_option( 'sendmsg_api_option_name' ); 
        $send_msg_token = $sendmsg_api_options['send_msg_token'];

        $widget->start_controls_section(
            'section_sendmsg',
            [
                'label' => __( 'Sendmsg', 'text-domain' ),
                'condition' => [
                    'submit_actions' => $this->get_name(),
                ],
            ]
        );

        if(!empty($send_msg_token)){
            $widget->add_control(
                'sendmsg_url',
                [
                    'label' => __( 'Sendmsg List', 'text-domain' ),
                    'type' => \Elementor\Controls_Manager::SELECT2,
                    'multiple' => true,
                    'options' => sendmsgs_get_lists()
              ]
            );

            $widget->add_control(
                'sendmsg_remove',
                [
                    'label' => __( 'Sendmsg Remove List', 'text-domain' ),
                    'type' => \Elementor\Controls_Manager::SELECT2,
                    'multiple' => true,
                    'options' => sendmsgs_get_lists()
              ]
            );


            $widget->add_control(
              'mailoptin_fields_map',
              [
                'type' => Fields_Map::CONTROL_TYPE,
                'separator' => 'before',
                'fields' => [
                    [
                        'name' => 'remote_id',
                        'type' => Controls_Manager::HIDDEN,
                    ],
                    [
                        'name' => 'local_id',
                        'type' => Controls_Manager::SELECT,
                    ],
                ],
              ]
            );



        }
        else{

               $widget->add_control(
                'sendmsg_empty_token',
                [
                    'label' => __( 'Authentication Required', 'plugin-domain' ),
                    'type' => \Elementor\Controls_Manager::RAW_HTML,
                    'raw' => __( '
                        <style>
                            .elementor-control.elementor-control-sendmsg_empty_token span.elementor-control-title {
                                text-align: center;
                                font-size: 15px;
                                font-weight: 500;
                            }

                            .elementor-control-raw-html.sendmsg-no-auth {
                                text-align: center;
                                line-height: 19px;
                                padding-top: 10px;
                            }

                            a.connect-send-msg {
                                display: block;
                                text-align: center;
                                margin-top: 10px;
                                background: #93003c;
                                color: #fff !important;
                                padding: 9px;
                                border-radius: 4px;
                                max-width: 100px;
                                margin: 16px auto;
                                text-transform: uppercase;
                                margin-bottom: 0;
                                cursor: none;
                            }
                        </style>You need to add Site ID and API Key in the plugin settings, please click here to add the authentication data. <a href="'.site_url().'/wp-admin/options-general.php?page=sendmsg-api" class="connect-send-msg">Connect</a>', 'plugin-name' ),
                    'content_classes' => 'sendmsg-no-auth',
                ]
            );         

        }

        $widget->end_controls_section();

    }

    /**
     * On Export
     *
     * Clears form settings on export
     * @access Public
     * @param array $element
     */
    public function on_export( $element ) {
        unset(
            $element['sendmsg_url'],
            $element['sendmsg_list'],
            $element['sendmsg_name_field'],
            $element['sendmsg_email_field'],
            $element['sendmsg_api_field'],
            $element['sendmsg_custom_fields'],
            $element['sendmsg_gdpr'],
            $element['custom_field_name'],
            $element['custom_field_id']
        );
    }
}
add_action( 'elementor_pro/init', function() {
// Here its safe to include our action class file
    include_once( 'main.php' );

// Instantiate the action class
    $sendmsg_action = new Sendmsg_Action_After_Submit();

// Register the action with form widget
    \ElementorPro\Plugin::instance()->modules_manager->get_modules( 'forms' )->add_form_action( $sendmsg_action->get_name(), $sendmsg_action );
});