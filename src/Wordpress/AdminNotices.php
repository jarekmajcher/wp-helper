<?php
namespace Jm\WpHelper\Wordpress;

if (!defined('WPINC')) {
    die;
}

use Jm\WpHelper;

class AdminNotices extends \Jm\WpHelper\WpHelper {
    public function __construct() {
        parent::__construct();

        add_action('admin_notices', [$this, 'admin_notices_from_transient']);
    }

    /**
     * @return void
     */
    public function admin_notices_from_transient() : void {
        $messages = get_transient('jm_admin_notices_' . get_current_user_id());

        if($messages) {
            foreach($messages as $message) {
                printf( '<div class="notice notice-%1$s is-dismissible"><p>%2$s</p></div>',
                    $message['type'],
                    $message['message'],
                );
            }
            delete_transient('jm_admin_notices_' . get_current_user_id());
        }
    }

    /**
     * @param $type
     * @param $message
     * @return void
     */
    public static function set_admin_notice($type, $message) : void {
        $messages = get_transient('jm_admin_notices_' . get_current_user_id()) ?? [];

        $messages[] = [
            'type' => $type,
            'message' => $message
        ];

        set_transient('jm_admin_notices_' . get_current_user_id(), $messages, 60);
    }

}
