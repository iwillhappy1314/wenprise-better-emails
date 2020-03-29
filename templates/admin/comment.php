<?php

$blog_url = wp_specialchars_decode(home_url(), ENT_QUOTES);
$blog_name = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

$comment = get_comment($comment_id);
$post = get_post($comment->comment_post_ID);

$comment_author_domain = @gethostbyaddr($comment->comment_author_IP);
$comments_waiting = $wpdb->get_var("SELECT count(comment_ID) FROM $wpdb->comments WHERE comment_approved = '0'");

$comment_content = wp_specialchars_decode( $comment->comment_content );

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta name="viewport" content="width=device-width" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title><?= $blog_name ?>有新的邮件需要审核。</title>
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
                            <meta itemprop="name" content="Confirm Email"/>
                            <table width="100%" cellpadding="0" cellspacing="0">



                                <tr>
                                    <td class="content-block">
                                        在文章「<a href="<?= get_permalink($post); ?>"><?= $post->post_title; ?></a>」中，有一条评论需要您审核。
                                    </td>
                                </tr>
                                <tr>
                                    <td class="content-block">
                                        评论内容为：「<?= $comment_content ?>」。
                                    </td>
                                </tr>
                                <tr>
                                    <td class="content-block" itemprop="handler" itemscope itemtype="http://schema.org/HttpActionHandler">
                                        <a href="<?= admin_url( "comment.php?action=approve&c={$comment_id}#wpbody-content" ); ?>" class="btn-primary" itemprop="url">批准评论</a>
                                        <a href="<?= admin_url( "comment.php?action=trash&c={$comment_id}#wpbody-content" ); ?>" class="btn-outline" itemprop="url">移至回收站</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="content-block" itemprop="handler" itemscope itemtype="http://schema.org/HttpActionHandler">
                                        您还可以：
                                        <a href="<?= admin_url( "comment.php?action=delete&c={$comment_id}#wpbody-content" ); ?>" itemprop="url">永久删除评论 </a>
                                        <a href="<?= admin_url( "comment.php?action=spam&c={$comment_id}#wpbody-content" ); ?>" itemprop="url"> 标记为垃圾评论</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="content-block">
			                            <?php printf( _n('Currently %s comment is waiting for approval. Please visit the moderation panel:',
				                            'Currently %s comments are waiting for approval. Please visit the moderation panel:', $comments_waiting), number_format_i18n($comments_waiting) ); ?>
			                            <?= admin_url('edit-comments.php?comment_status=moderated#wpbody-content'); ?>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
                <div class="footer">
                    <table width="100%">
                        <tr>
                            <td class="aligncenter content-block">此邮件由 <a href="<?= $blog_url ?>"><?= $blog_name ?></a> 发出</td>
                        </tr>
                    </table>
                </div></div>
        </td>
        <td></td>
    </tr>
</table>

</body>
</html>
