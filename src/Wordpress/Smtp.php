<?php
namespace Jm\WpHelper\Wordpress;

if (!defined('WPINC')) {
    die;
}

use Jm\WpHelper;

class Smtp extends \Jm\WpHelper\WpHelper {

    public function __construct() {
        parent::__construct();

        add_action('phpmailer_init', [$this, 'smtp_settings']);
    }

    /**
     * @param $phpmailer
     * @return void
     */
    public function smtp_settings($phpmailer) : void {
        if(
            defined('JM_SMTP_HOST') &&
            defined('JM_SMTP_PORT') &&
            defined('JM_SMTP_USERNAME') &&
            defined('JM_SMTP_PASSWORD') &&
            defined('JM_SMTP_SECURE') &&
            defined('JM_SMTP_FROM') &&
            defined('JM_SMTP_FROM_NAME')
        ) {
            if(!is_object($phpmailer)) {
                $phpmailer = (object)$phpmailer;
            }

            $phpmailer->isSMTP();
            $phpmailer->SMTPAuth = true;
            $phpmailer->Host = JM_SMTP_HOST;
            $phpmailer->Port = JM_SMTP_PORT;
            $phpmailer->Username = JM_SMTP_USERNAME;
            $phpmailer->Password = JM_SMTP_PASSWORD;
            $phpmailer->SMTPSecure = JM_SMTP_SECURE;
            $phpmailer->From = JM_SMTP_FROM;
            $phpmailer->FromName = JM_SMTP_FROM_NAME;
        }
    }
}
