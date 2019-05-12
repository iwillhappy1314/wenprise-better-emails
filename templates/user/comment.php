<?php

// 站点信息
$blog_url  = wp_specialchars_decode(home_url(), ENT_QUOTES);
$blog_name = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

// 回复邮件信息
$comment             = get_comment($comment_id);
$comment_author_name = $comment->comment_author;

// 被回复邮件信息
$comment_parent             = get_comment($comment->comment_parent);
$comment_post_id            = ($comment_parent->comment_post_ID);
$comment_parent_author_name = $comment_parent->comment_author;

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta name="viewport" content="width=device-width" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>你在 [ <?= $blog_name; ?> 上的评论有了新回复。</title>
</head>

<body itemscope itemtype="http://schema.org/EmailMessage">

<table class="body-wrap">
    <tr>
        <td></td>
        <td class="container" width="600">
            <div class="content">
                <table class="main" width="100%" cellpadding="0" cellspacing="0" itemprop="action" itemscope itemtype="http://schema.org/ConfirmAction">
                    <tr>
                        <td class="content-wrap">
                            <meta itemprop="name" content="Confirm Email" />
                            <table width="100%" cellpadding="0" cellspacing="0">

                                <tr>
                                    <td class="content-block">
                                        你好，<?= $comment_parent_author_name ?>，<?= $comment_author_name ?>回复了你在「<a
                                                href="<?= get_permalink($comment_post_id); ?>"><?= get_the_title($comment_post_id); ?></a>」文章上的评论。
                                    </td>
                                </tr>
                                <tr>
                                    <td class="content-block">
                                        您的评论为：「<?= $comment_parent->comment_content ?>」。
                                    </td>
                                </tr>
                                <tr>
                                    <td class="content-block">
                                        <?= $comment_author_name ?>的回复为：「<?= $comment->comment_content ?>」。
                                    </td>
                                </tr>
                                <tr>
                                    <td class="content-block" itemprop="handler" itemscope itemtype="http://schema.org/HttpActionHandler">
                                        <a href="<?= get_comment_link($comment_parent->comment_ID); ?>" class="btn-primary" itemprop="url">点击回复他</a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
                <div class="footer">
                    <table width="100%">
                        <tr>
                            <td class="aligncenter content-block">
                                此邮件由 <a href="<?= $blog_url ?>"><?= $blog_name ?></a> 发出，
                                <a href="<?= wprs_get_unsubscribe_link($comment_parent) ?>"><?= __('点击取消订阅', 'wprs') ?></a>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </td>
        <td></td>
    </tr>
</table>

</body>
</html>
