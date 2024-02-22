<?php
namespace Mediabit\Shortcodes;

use Mediabit\Shortcodes\Wrapper;

class GetInfo {
    public static function init() {
        Wrapper::register('getinfo', [self::class, 'render_shortcode']);
    }

    public static function render_shortcode($atts) {
        $atts = shortcode_atts([
            'field' => '',
            'format' => 'raw',
        ], $atts, 'getinfo');
    
        if (empty($atts['field'])) {
            return '';
        }
    
        $field_value = get_field($atts['field'], 'option');
    
        if ($atts['field'] === 'business_phone') {
            if (is_array($field_value)) {
                $field_value = $field_value['number'] ?? '';
            }
            if ($atts['format'] === 'display') {
                $field_value = self::format_phone($field_value);
            }
        } elseif ($atts['field'] === 'business_email' && $atts['format'] === 'display') {
            $field_value = self::obfuscate_email($field_value);
        }
    
        if ($atts['field'] === 'business_hours') {
            return self::render_business_hours($field_value);
        }
    
        if (is_array($field_value)) {
            $field_value = implode(', ', $field_value);
        }
    
        return $field_value;
    }
    
    
    private static function obfuscate_email($email) {
        $email = str_replace('@', '&#64;', $email);
        $email = str_replace('.', '&#46;', $email);
        return $email;
    }
    private static function format_phone($phone) {
        if(  preg_match( '/^\+\d(\d{3})(\d{3})(\d{4})$/', $phone,  $matches ) )
        {
            $result = $matches[1] . '-' .$matches[2] . '-' . $matches[3];
            return $result;
        } else {
            return $phone;
        }
    }
    private static function render_business_hours($business_hours) {
        if (empty($business_hours) || !is_array($business_hours)) {
            return '';
        }
    
        $output = '<ul class="business-hours">';
        foreach ($business_hours as $hours) {
            $day = isset($hours['day']) ? $hours['day'] : '';
            $hour = isset($hours['hours']) ? $hours['hours'] : '';
            $output .= sprintf('<li><span class="day">%s:</span> <span class="hours">%s</span></li>', $day, $hour);
        }
        $output .= '</ul>';
    
        return $output;
    }
}

GetInfo::init();