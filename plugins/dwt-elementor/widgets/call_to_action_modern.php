<?php

/**
 * Call to Action Modern
 */

namespace ElementorDwt\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

class Call_To_Action_Modern extends Widget_Base {

    public function get_name() {
        return 'call-to-action-modern';
    }

    public function get_title() {
        return __('Call To Action(Modern)', 'dwt-elementor');
    }

    public function get_icon() {
        return 'eicon-call-to-action';
    }

    public function get_categories() {
        return ['dwttheme'];
    }

    public function get_script_depends() {
        return [''];
    }

    /**
     * Register the widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @since 1.0.0
     *
     * @access protected
     */
    protected function _register_controls() {
        /* for Basic tab */
        $this->start_controls_section(
                'call_to_action_modern',
                [
                    'label' => __('Content Area', 'dwt-elementor'),
                    'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
                ]
        );
        $this->add_control(
                'section_title',
                [
                    'label' => __('Section Title', 'dwt-elementor'),
                    'type' => \Elementor\Controls_Manager::TEXT,
                    'default' => __('Business directory theme ', 'dwt-elementor'),
                    'placeholder' => __('Put title here', 'dwt-elementor'),
                    'label_block' => true,
                ]
        );
        $this->add_control(
                'section_description',
                [
                    'label' => __('Section Description', 'dwt-elementor'),
                    'type' => \Elementor\Controls_Manager::TEXTAREA,
                    'placeholder' => __('Put Description here', 'dwt-elementor'),
                    'label_block' => true,
                    'rows' => 5,
                ]
        );
        $this->add_control(
                'btn_title',
                [
                    'label' => __('Button Title', 'dwt-elementor'),
                    'type' => \Elementor\Controls_Manager::TEXT,
                    'placeholder' => __('Button title here', 'dwt-elementor'),
                    'default' => __('Button Link', 'dwt-elementor'),
                    'label_block' => true,
                    'label_block' => true
                ]
        );
        $this->add_control(
                'btn_link',
                [
                    'label' => __('Button Link', 'dwt-elementor'),
                    'type' => \Elementor\Controls_Manager::URL,
                    'placeholder' => __('https://your-link.com', 'dwt-elementor'),
                    'show_external' => true,
                    'default' => [
                        'url' => '#',
                        'is_external' => true,
                        'nofollow' => true,
                    ],
                    'label_block' => true,
                ]
        );
        $this->add_control(
                'show_extra_img',
                [
                    'label' => __('SHow Additional Image', 'plugin-domain'),
                    'type' => \Elementor\Controls_Manager::SWITCHER,
                    'label_on' => __('Show', 'your-plugin'),
                    'label_off' => __('Hide', 'your-plugin'),
                    'return_value' => 'yes',
                    'default' => 'no',
                    'label_block' => true,
                ]
        );
        $this->add_control(
                'extra_image',
                [
                    'label' => __('Background Image', 'dwt-elementor'),
                    'type' => \Elementor\Controls_Manager::MEDIA,
                    'default' => [
                        'url' => \Elementor\Utils::get_placeholder_image_src(),
                        'id' => '',
                    ],
                    'condition' => [
                        'show_extra_img' => 'yes'
                    ],
                    'label_block' => true,
                ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        // // get our input from the widget settings.
        $settings = $this->get_settings_for_display();
        $params['section_title'] = $settings['section_title'] ? $settings['section_title'] : '';
        $params['section_description'] = $settings['section_description'] ? $settings['section_description'] : '';
        $params['btn_title'] = $settings['btn_title'] ? $settings['btn_title'] : '';
        $params['btn_link'] = $settings['btn_link']['url'] ? $settings['btn_link']['url'] : '';
        $params['target_one'] = $settings['btn_link']['is_external'] ? ' target="_blank"' : '';
        $params['nofollow_one'] = $settings['btn_link']['nofollow'] ? ' rel="nofollow"' : '';
        $params['show_extra_img'] = $settings['show_extra_img'] ? $settings['show_extra_img'] : 'no';
        if($params['show_extra_img'] == 'yes'){
        $params['extra_image'] = $settings['extra_image']['id'] ? $settings['extra_image']['id'] : '';
        }
        echo dwt_elementor_call_to_action_three($params);
    }

    /**
     * Render the widget output in the editor.
     *
     * Written as a Backbone JavaScript template and used to generate the live preview.
     *
     * @since 1.0.0
     *
     * @access protected
     */
    protected function _content_template() {
        
    }

}
