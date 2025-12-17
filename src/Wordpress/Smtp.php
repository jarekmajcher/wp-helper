<?php
namespace Jm\WpHelper\Wordpress;

if (!defined('WPINC')) {
    die;
}

use Jm\WpHelper;

class Smtp extends \Jm\WpHelper\WpHelper {

    public function __construct() {
        parent::__construct();

        if(
            defined('JM_SMTP_HOST') &&
            defined('JM_SMTP_PORT') &&
            defined('JM_SMTP_USERNAME') &&
            defined('JM_SMTP_PASSWORD') &&
            defined('JM_SMTP_SECURE') &&
            defined('JM_SMTP_FROM') &&
            defined('JM_SMTP_FROM_NAME')
        ) {
            add_action('phpmailer_init', [$this, 'smtp_settings']);
            add_filter('wp_mail_from', fn() => JM_SMTP_FROM);
            add_filter('wp_mail_from_name', fn() => JM_SMTP_FROM_NAME);
        }
    }

    /**
     * @param $phpmailer
     * @return void
     */
    public function smtp_settings($phpmailer) : void {
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
        $phpmailer->setFrom(JM_SMTP_FROM, JM_SMTP_FROM_NAME, false);
    }
}
