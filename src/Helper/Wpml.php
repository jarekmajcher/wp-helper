<?php
namespace Jm\WpHelper\Helper;

/**
 */
class Wpml
{
    /**
     * @param $array
     * @param $name
     * @param $translate
     * @return array
     */
    public static function set_config_array($array, $name, $translate = []) : array {
        global $wpdb;

        $sql = "SELECT meta_key FROM $wpdb->postmeta WHERE meta_key LIKE %s OR meta_key LIKE %s GROUP BY meta_key ORDER BY meta_key";
        $sql = $wpdb->prepare($sql, $name . '%', '_' . $name . '%');
        $fields = $wpdb->get_col($sql);

        foreach($fields as $field) {
            $last = substr($field, strrpos($field, '_') + 1);
            if(in_array($last, $translate) && Php::starts_with($field, $name)) {
                $array['wpml-config']['custom-fields']['custom-field'][] = array(
                    'value' => $field,
                    'attr' => array(
                        'action' => 'translate'
                    )
                );
            }
            else {
                $array['wpml-config']['custom-fields']['custom-field'][] = array(
                    'value' => $field,
                    'attr' => array(
                        'action' => 'copy'
                    )
                );
            }
        }
        return $array;
    }
}
