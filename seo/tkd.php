<?php

/*
 * TKD -- title, keywords, description
 * 
 *
 */


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
