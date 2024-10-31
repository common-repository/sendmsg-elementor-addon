<?php
use Elementor\Base_Data_Control;
class CustomSelect extends Base_Data_Control {

    public function get_type() {
        return 'moselect';
    }

    public function content_template() {
        $control_uid = $this->get_control_uid();
        ?>
        <div class="elementor-control-field">
            <label for="<?php echo esc_attr($control_uid); ?>" class="elementor-control-title">{{{ data.label }}}</label>
            <div class="elementor-control-input-wrapper elementor-custom-select">
                <select id="<?php echo esc_attr($control_uid); ?>" class="elementor-select2" multiple data-setting="{{ data.name }}">
                    <# _.each( data.options, function( value, key ) { #>

                        <# if(_.isObject(value)) { #>
                            <optgroup label="{{ key }}">
                                <# _.each(value, function(value2, key2) { #>
                                    <option value="{{ key2 }}">{{{ value2 }}}</option>
                                 <# }) #>
                            </optgroup>
                        <# } #>

                        <# } ); #>
                </select>
            </div>
        </div>
        <# if ( data.description ) { #>
        <div class="elementor-control-field-description">{{{ data.description }}}</div>
        <# } #>

        <?php
    }
}