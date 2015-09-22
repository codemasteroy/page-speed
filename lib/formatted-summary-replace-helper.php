<?php

class Formatted_Summary_Replace_Helper {
    private $args;

    public function __construct($args) {
        $this->args = $args;
    }

    public function callback($matches) {
        $return = $matches[0];
        
        switch ($matches[1]) {
            case 'END_LINK':
                $return = '</a>';
                break;
            case 'BEGIN_LINK':
                $return = '<a href="'.$this->get_link().'" target="_blank">';
                break;
            default:
                $return = $this->get_value($matches[1], $return);
        }
        return $return;
    }

    private function get_link() {
        foreach ($this->args as $arg) {
            if ($arg['key'] == 'LINK') {
                return $arg['value'];
            }
        }
        return '';
    }

    private function get_value($key, $default) {
        foreach ($this->args as $arg) {
            if ($arg['key'] == $key) {
                return $arg['value'];
            }
        }
        return $default;
    }
}