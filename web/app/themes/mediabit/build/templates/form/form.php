<?php
namespace Mediabit\Templates\Form;

class Form {
    public static function label($for, $text, $class = '') {
        $classAttr = $class ? ' class="' . $class . '"' : '';
        return '<label for="' . $for . '"' . $classAttr . '>' . $text . '</label>';
    }
    
    public static function input($type, $name, $value = '', $class = '', $placeholder = '') {
        $classAttr = $class ? ' class="' . $class . '"' : '';
        $placeholderAttr = $placeholder ? ' placeholder="' . $placeholder . '"' : '';
        switch($type) {
            case 'text':
            case 'email':
            case 'password':
            case 'number':
            case 'date':
            case 'time':
            case 'checkbox':
            case 'radio':
            case 'submit':
                return '<input type="' . $type . '" name="' . $name . '" value="' . $value . '"' . $classAttr . $placeholderAttr . '/>';
            case 'textarea':
                return '<textarea name="' . $name . '"' . $classAttr . $placeholderAttr . '>' . $value . '</textarea>';
            default:
                return '';
        }
    }
}