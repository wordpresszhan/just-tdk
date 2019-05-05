<?php
/*
 * 一些基础的功能
 */

/*
 * 1. 不使用古藤堡编辑器
 */
if ( version_compare( get_bloginfo('version'), '5.0', '>=' ) ) {
	add_filter('use_block_editor_for_post', '__return_false'); // 切换回之前的编辑器
	remove_action( 'wp_enqueue_scripts', 'wp_common_block_scripts_and_styles' ); // 禁止前端加载样式文件
}else{
	// 4.9.8 < WP < 5.0 插件形式集成Gutenberg古腾堡编辑器
	add_filter('gutenberg_can_edit_post_type', '__return_false');
}

/*
 * 2. 禁止使用 emoji
 */
function disable_emojis() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
	add_filter( 'tiny_mce_plugins', 'disable_emojis_tinymce' );
}

function disable_emojis_tinymce( $plugins ) {
	if ( is_array( $plugins ) ) {
		return array_diff( $plugins, array( 'wpemoji' ) );
	} else {
		return array();
	}
}

add_action( 'init', 'disable_emojis' );


/*
 * 
 * <link rel='dns-prefetch' href='//s.w.org' /> 
 */

remove_action( 'wp_head', 'wp_resource_hints', 2 );

/*
 * 禁用 feed
 */

remove_action( 'wp_head','feed_links', 2 );
remove_action( 'wp_head','feed_links_extra', 3 );

/*
 * 3. 精简 wp_heade
 */

remove_action('wp_head', 'feed');//当前文章的索引
remove_action('wp_head', 'index_rel_link');//当前文章的索引
remove_action('wp_head', 'feed_links_extra', 3);// 额外的feed,例如category, tag页
remove_action('wp_head', 'start_post_rel_link', 10, 0);// 开始篇
remove_action('wp_head', 'parent_post_rel_link', 10, 0);// 父篇
remove_action('wp_head', 'adjacent_posts_rel_link', 10, 0); // 上、下篇.
remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );//rel=pre
remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0 );//rel=shortlink
remove_action('wp_head', 'rel_canonical' );
wp_deregister_script('l10n');
remove_action('wp_head','rsd_link');//移除head中的rel="EditURI"
remove_action('wp_head','wlwmanifest_link');//移除head中的rel="wlwmanifest"
remove_action('wp_head','rsd_link');//rsd_link移除XML-RPC
remove_filter('the_content', 'wptexturize');//禁用半角符号自动转换为全角
remove_action('wp_head','wp_generator');

/*
 *
 */

add_filter('rest_enabled', '__return_false');
add_filter('rest_jsonp_enabled', '__return_false');
remove_action('wp_head', 'rest_output_link_wp_head', 10 );
remove_action('template_redirect', 'rest_output_link_header', 11 );


/*
 * 关闭XML-RPC
 */
add_filter('xmlrpc_enabled', '__return_false');
