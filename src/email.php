<?php
/**
 * 发送 HTML 格式的 Email 通知邮件
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package enter
 */

use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;

add_action('wp_set_comment_status', 'wprs_comment_status_update', 99, 2);
add_action('wp_insert_comment', 'wprs_comment_notification', 99, 2);


/**
 * 发送邮件给被评论的文章作者
 */
add_filter('comment_notification_headers', function ($message_headers, $comment_ID)
{
    return "Content-Type: text/html; charset=\"" . get_option('blog_charset') . "\"\n";
}, 10, 3);


/**
 * 使用 HTML 模版替代默认的通知文章作者的邮件
 */
add_filter('comment_notification_text', function ($notify_message, $comment_ID)
{
    $html = wprs_render_template('author/comment', '', [
        'notify_message' => $notify_message,
        'comment_id'     => $comment_ID,
    ], WPRS_EMAIL_PATH . 'templates', false);

    $message = wprs_render_email($html);

    return $message;
}, 10, 3);


/**
 * 发送邮件给管理员
 *
 * wp_notify_moderator
 */
add_filter('comment_moderation_headers', function ($message_headers, $comment_ID)
{
    return "Content-Type: text/html; charset=\"" . get_option('blog_charset') . "\"\n";
}, 10, 3);


/**
 * 使用 HTML 模版替代默认的通知管理员的邮件
 */
add_filter('comment_moderation_text', function ($notify_message, $comment_ID)
{

    $html = wprs_render_template('admin/comment', '', [
        'notify_message' => $notify_message,
        'comment_id'     => $comment_ID,
    ], WPRS_EMAIL_PATH . 'templates', false);

    $message = wprs_render_email($html);

    return $message;
}, 10, 3);


/**
 * 发送评论回复通知
 *
 * @param $comment_id
 * @param $comment
 */
function wprs_comment_notification($comment_id, $comment)
{

    if ($comment->comment_approved == 1 && $comment->comment_parent > 0) {

        $comment_parent_author_email = get_comment_author_email($comment->comment_parent);

        $html = wprs_render_template('user/comment', '', [
            'comment_id' => $comment_id,
            'comment_id' => $comment_id,
        ], WPRS_EMAIL_PATH . 'templates', false);

        $headers = "Content-Type: text/html; charset=\"" . get_option('blog_charset') . "\"\n";

        $subject = '你在 [' . get_option('blogname') . '] 上的评论有了新回复。';

        $message = wprs_render_email($html);

        wp_mail($comment_parent_author_email, $subject, $message, $headers);
    }
}


/**
 * 渲染 Html 邮件
 *
 * @param      $html
 * @param null $css string CSS 规则
 *
 * @return string
 */
function wprs_render_email($html, $css = null)
{

    $cssToInlineStyles = new CssToInlineStyles();

    if ( ! $css) {
        $css = file_get_contents(WPRS_EMAIL_PATH . 'templates/css/styles.css');
    }

    $message = $cssToInlineStyles->convert(
        $html,
        $css
    );

    return $message;
}


/**
 * 评论审核通过后，发送通知
 *
 * @param $comment_id
 * @param $comment_status
 */
function wprs_comment_status_update($comment_id, $comment_status)
{
    $comment = get_comment($comment_id);

    if ($comment_status == 'approve') {
        wprs_comment_notification($comment->comment_ID, $comment);
    }
}


/**
 * 游客订阅选框
 */
add_filter('comment_form_default_fields', function ($fields)
{
    $label   = apply_filters('wprs_comment_checkbox_label', __('通过邮件订阅评论', 'wprs'));
    $checked = 'checked';

    $checkbox = '<p class="comment-form-comment-subscribe">';
    $checkbox .= '<label for="wprs_subscribe_to_comment">';
    $checkbox .= '<input id="wprs_subscribe_to_comment" name="wprs_subscribe_to_comment" type="checkbox" value="on"  ' . $checked . '>' . $label;
    $checkbox .= '</label>';
    $checkbox .= '</p>';

    $fields[ 'wprs_subscribe_to_comment' ] = $checkbox;

    return $fields;
}
);


/**
 * 注册用户订阅选框
 */
add_filter('comment_form_submit_field', function ($submitField)
{
    $checkbox = '';

    if (is_user_logged_in()) {
        $label   = apply_filters('wprs_comment_checkbox_label', __('通过邮件订阅评论', 'wprs'));
        $checked = 'checked';

        $checkbox = '<p class="comment-form-comment-subscribe">';
        $checkbox .= '<label for="wprs_subscribe_to_comment">';
        $checkbox .= '<input id="wprs_subscribe_to_comment" name="wprs_subscribe_to_comment" type="checkbox" value="on"  ' . $checked . '>' . $label;
        $checkbox .= '</label>';
        $checkbox .= '</p>';

    }

    return $checkbox . $submitField;
});


/**
 * 保存订阅选项
 */
add_action('comment_post', function ($comment_id)
{
    $value = (isset($_POST[ 'wprs_subscribe_to_comment' ]) && $_POST[ 'wprs_subscribe_to_comment' ] == 'on') ? 'on' : 'off';

    return add_comment_meta($comment_id, 'wprs_subscribe_to_comment', $value, true);
});


/**
 * 获取密钥
 *
 * @param $comment_id
 *
 * @return false|string
 */
function wprs_secret_key($comment_id)
{
    return hash_hmac('sha512', $comment_id, wp_salt(), false);
}


/**
 * 获取取消订阅链接
 *
 * @param $comment
 *
 * @return string
 */
function wprs_get_unsubscribe_link($comment)
{
    $key = wprs_secret_key($comment->comment_ID);

    $params = [
        'comment_id' => $comment->comment_ID,
        'key'        => $key,
    ];

    $uri = site_url('/wprs-better-email/unsubscribe?' . http_build_query($params));

    return $uri;
}


/**
 * 取消订阅
 */
add_action('init', function ()
{
    $request_uri = $_SERVER[ 'REQUEST_URI' ];

    if (preg_match('/wprs-better-email\/unsubscribe/', $request_uri)) {
        $comment_id = filter_input(INPUT_GET, 'comment_id', FILTER_SANITIZE_NUMBER_INT);
        $comment    = get_comment($comment_id);

        $user_key = filter_input(INPUT_GET, 'key', FILTER_SANITIZE_STRING);
        $real_key = wprs_secret_key($comment_id);

        if ( ! $comment || $user_key != $real_key) {
            echo 'Invalid request.';
            exit;
        }

        $uri = get_permalink($comment->comment_post_ID);

        update_comment_meta($comment_id, 'wprs_subscribe_to_comment', 'off');

        echo '<!doctype html><html><head><meta charset="utf-8"><title>' . get_bloginfo('name') . '</title></head><body>';
        echo '<p>' . __('您的订阅已取消', 'wprs') . '</p>';
        echo '<script type="text/javascript">setTimeout(function() { window.location.href="' . $uri . '"; }, 3000);</script>';
        echo '</body></html>';
        exit;
    }
});
