<?php
/*
Plugin Name:        Wenprise Better Emails
Plugin URI:         https://www.wpzhiku.com/wenprise-better-emails/
Description:        使用 HTML 邮件，美化 WordPress 评论审核通知邮件，评论回复通知邮件。
Version:            1.0
Author:             WordPress 智库
Author URI:         https://www.wpzhiku.com/
License:            MIT License
License URI:        http://opensource.org/licenses/MIT
*/

define( 'WPRS_EMAIL_PATH', plugin_dir_path( __FILE__ ) );

require_once( plugin_dir_path( __FILE__ ) . 'vendor/autoload.php' );
require_once( plugin_dir_path( __FILE__ ) . 'src/email.php' );