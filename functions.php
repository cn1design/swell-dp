<?php

/* 子テーマのfunctions.phpは、親テーマのfunctions.phpより先に読み込まれることに注意してください。 */


/**
 * 親テーマのfunctions.phpのあとで読み込みたいコードはこの中に。
 */
// add_filter('after_setup_theme', function(){
// }, 11);


/**
 * 子テーマでのファイルの読み込み
 */
add_action('wp_enqueue_scripts', function() {
	
	$timestamp = date( 'Ymdgis', filemtime( get_stylesheet_directory() . '/style.css' ) );
	wp_enqueue_style( 'child_style', get_stylesheet_directory_uri() .'/style.css', [], $timestamp );

	$dp_css = get_stylesheet_directory() . '/dp-style.css';
	if ( file_exists( $dp_css ) ) {
		wp_enqueue_style(
			'swell-dp-style',
			get_stylesheet_directory_uri() . '/dp-style.css',
			array('main_style'),
			filemtime( $dp_css )
		);
	}

	/* その他の読み込みファイルはこの下に記述 */
	wp_enqueue_script(
		'child-dp-swipe-slider',
		get_stylesheet_directory_uri() . '/javascript/dp-swipe-slider.js',
		[],
		filemtime( get_stylesheet_directory() . '/javascript/dp-swipe-slider.js' ),
		true
	);

	wp_enqueue_script(
		'child-fade-scroll',
		get_stylesheet_directory_uri() . '/javascript/fade-scroll.js',
		[],
		filemtime( get_stylesheet_directory() . '/javascript/fade-scroll.js' ),
		true
	);

	wp_enqueue_script(
		'child-observer-fadeIn',
		get_stylesheet_directory_uri() . '/javascript/observer-fadeIn.js',
		[],
		filemtime( get_stylesheet_directory() . '/javascript/observer-fadeIn.js' ),
		true
	);

	wp_enqueue_script(
		'child-cpt',
		get_stylesheet_directory_uri() . '/javascript/cpt.js',
		[],
		filemtime( get_stylesheet_directory() . '/javascript/cpt.js' ),
		true
	);

	wp_enqueue_script(
		'child-fade-slide',
		get_stylesheet_directory_uri() . '/javascript/fade-slide.js',
		[],
		filemtime( get_stylesheet_directory() . '/javascript/fade-slide.js' ),
		true
	);

	// デザインパターン一覧アーカイブ専用
	if ( is_post_type_archive( 'design_pattern' ) ) {
		wp_enqueue_script(
			'child-archive-design',
			get_stylesheet_directory_uri() . '/javascript/archive-design.js',
			[],
			filemtime( get_stylesheet_directory() . '/javascript/archive-design.js' ),
			true
		);
	}

	// DP ベースCSSコピーボタン（アーカイブ + 標準構成ページ）
	if ( is_post_type_archive( 'design_pattern' ) || is_page_template( 'page-design_pattern_standard.php' ) ) {
		wp_enqueue_script(
			'child-dp-base-css',
			get_stylesheet_directory_uri() . '/javascript/dp-base-css.js',
			[],
			filemtime( get_stylesheet_directory() . '/javascript/dp-base-css.js' ),
			true
		);
	}

	// コピーボタン サンバーストアニメーション（アーカイブ + 標準構成 + シングル）
	if (
		is_post_type_archive( 'design_pattern' ) ||
		is_page_template( 'page-design_pattern_standard.php' ) ||
		( is_singular( 'design_pattern' ) )
	) {
		wp_enqueue_script(
			'child-dp-burst',
			get_stylesheet_directory_uri() . '/javascript/dp-burst.js',
			[],
			filemtime( get_stylesheet_directory() . '/javascript/dp-burst.js' ),
			true
		);
	}

}, 11);

//WordPressで自動更新メール通知を無効化 / fuunctions.php
add_filter( 'auto_plugin_update_send_email', '__return_false' );

require_once get_stylesheet_directory() . '/inc/design-patterns.php';
require_once get_stylesheet_directory() . '/inc/dp-base-css-data.php';
require_once get_stylesheet_directory() . '/inc/dp-tips-data.php';

/* フッター サブナビ（重要事項説明書・利用契約書・プライバシーポリシー等）*/
add_action('init', function() {
    register_nav_menu('footer_secondary_menu', 'フッター サブナビ');
});

/* ======================================================== */

/**
 * タクソノミーアーカイブ → メインアーカイブへ 301 リダイレクト
 * /pattern-section/hero/   → /design-patterns/?section=hero
 * /pattern-industry/salon/ → /design-patterns/?industry=salon
 */
function dp_redirect_taxonomy_to_archive() {
	$param = '';
	if ( is_tax( 'pattern_section' ) ) {
		$param = 'section';
	} elseif ( is_tax( 'pattern_industry' ) ) {
		$param = 'industry';
	}

	if ( $param ) {
		$term = get_queried_object();
		if ( $term instanceof WP_Term ) {
			$url = add_query_arg( $param, $term->slug, get_post_type_archive_link( 'design_pattern' ) );
			wp_redirect( $url, 301 );
			exit;
		}
	}
}
add_action( 'template_redirect', 'dp_redirect_taxonomy_to_archive' );

/* ======================================================== */

//SVGをアップロード
function add_file_types_to_uploads($file_types)
{

  $new_filetypes = array();
  $new_filetypes['svg'] = 'image/svg+xml';
  $file_types = array_merge($file_types, $new_filetypes);

  return $file_types;
}
add_action('upload_mimes', 'add_file_types_to_uploads');

/* ======================================================== */