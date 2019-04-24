<?php
/*
Plugin Name: JUST TKD
Plugin URI: https://www.wordpresszhan.com/
Description: TKD ( tilte, keywords, description ) , 默认情况 wordpress 没有 keywords, 和 descripton ，那就增加一个。
Author: wpzhan.com 
Version: 0.0.1
Author URI: https://www.wordpresszhan.com 
*/




if ( version_compare( get_bloginfo('version'), '5.0', '>=' ) ) {
	add_filter('use_block_editor_for_post', '__return_false'); // 切换回之前的编辑器
	remove_action( 'wp_enqueue_scripts', 'wp_common_block_scripts_and_styles' ); // 禁止前端加载样式文件
}else{
	// 4.9.8 < WP < 5.0 插件形式集成Gutenberg古腾堡编辑器
	add_filter('gutenberg_can_edit_post_type', '__return_false');
}


//error_reporting(E_ERROR | E_WARNING | E_PARSE);
//error_reporting(E_ALL);

// 增加 TDK 面板
add_action('add_meta_boxes', 'wptdk_add_box');

function wptdk_add_box() {
	$screens = array('post', 'page');

	foreach($screens as $screen) {
		add_meta_box(
			'wptdk', # html 中的 id
			'自定义TDK',
			'wptdk_show_html',
			$screen,
			'advanced', #可以是 normal, advanced, side
			'default', # 显示的优先级别 ('high', 'core', 'default' or 'low')
			'' # $callback_args
		);
	}
}

function wptdk_show_html() {
	global $post;

	wp_nonce_field(plugin_basename(__FILE__), 'wptdk_noncename');

	$value = get_post_meta($post->ID, 'seo_title', true);
	echo '<label for="seo_title">';
	echo '<h3>标题</h3>';
	echo '<input style="width: 100%" type="text" id="seo_title" name="seo_title" value="' . esc_attr($value) . '" size="25" />';
	echo '</label> ';


	$value = get_post_meta($post->ID, 'seo_keywords', true);
	echo '<label for="seo_keywords">';
	echo '<h3>关键词</h3>';
	echo '<input style="width: 100%" type="text" id="seo_keywords" name="seo_keywords" value="' . esc_attr($value) . '" size="25" />';
	echo '</label> ';


	$value = get_post_meta($post->ID, 'seo_description', true);
	echo '<label for="seo_description">';
	echo '<h3>描述</h3>';
	echo '<textarea  style="width: 100%; height: 100px;"  id="seo_description" name="seo_description" >' . esc_attr($value) . '</textarea>';
	echo '</label> ';
}

// 写入meta值
add_action('save_post', 'wptdk_save_tdkdata');

function wptdk_save_tdkdata() {

	if ('page' == $_POST['post_type']) {
		if (!current_user_can('edit_page', $post_id))
			return;
	} else {
		if (!current_user_can('edit_post', $post_id))
			return;
	}

	if (!isset($_POST['wptdk_noncename']) || !wp_verify_nonce($_POST['wptdk_noncename'], plugin_basename(__FILE__)))
		return;

	$post_ID = $_POST['post_ID'];

	$seo_title = sanitize_text_field($_POST['seo_title']);
	$seo_description = sanitize_text_field($_POST['seo_description']);
	$seo_keywords = sanitize_text_field($_POST['seo_keywords']);

	add_post_meta($post_ID, 'seo_title', $seo_title, true);
	add_post_meta($post_ID, 'seo_description', $seo_description, true);
	add_post_meta($post_ID, 'seo_keywords', $seo_keywords, true);

	update_post_meta($post_ID, 'seo_title', $seo_title);
	update_post_meta($post_ID, 'seo_description', $seo_description);
	update_post_meta($post_ID, 'seo_keywords', $seo_keywords);
}
