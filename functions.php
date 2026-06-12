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

	// dp-style.css と デザインJS はプラグイン（swell-dp）が管理。
	// プラグイン未使用時（ローカル開発・テスト）のみ子テーマから読み込む。
	if ( ! defined( 'SWELL_DP_VERSION' ) ) {
		$dp_css = get_stylesheet_directory() . '/dp-style.css';
		if ( file_exists( $dp_css ) ) {
			wp_enqueue_style(
				'swell-dp-style',
				get_stylesheet_directory_uri() . '/dp-style.css',
				[ 'main_style' ],
				filemtime( $dp_css )
			);
		}
		$fallback_js = [
			'child-dp-swipe-slider'  => 'dp-swipe-slider.js',
			'child-dp-works-slider'  => 'dp-works-slider.js',
			'child-fade-scroll'      => 'fade-scroll.js',
			'child-observer-fadeIn'  => 'observer-fadeIn.js',
			'child-fade-slide'       => 'fade-slide.js',
			'child-fade-in'          => 'fade-in.js',
			'child-dp-vertical-cta'  => 'dp-vertical-cta.js',
			'child-loading'          => 'loading.js',
		];
		foreach ( $fallback_js as $handle => $file ) {
			$path = get_stylesheet_directory() . '/javascript/' . $file;
			if ( file_exists( $path ) ) {
				wp_enqueue_script( $handle, get_stylesheet_directory_uri() . '/javascript/' . $file, [], filemtime( $path ), true );
			}
		}
	}

	// DP ライブラリ専用スクリプト（プラグイン化の対象外）

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

	// コピーボタン（シングルページ）
	if ( is_singular( 'design_pattern' ) ) {
		wp_enqueue_script(
			'child-dp-cpt',
			get_stylesheet_directory_uri() . '/javascript/cpt.js',
			[],
			filemtime( get_stylesheet_directory() . '/javascript/cpt.js' ),
			true
		);
		wp_localize_script( 'child-dp-cpt', 'dpConfig', [
			'isLoggedIn' => function_exists( 'swell_dp_is_pro_member' ) ? swell_dp_is_pro_member() : is_user_logged_in(),
			'isAdmin'    => current_user_can( 'administrator' ),
		] );
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

	// いいねボタン（アーカイブ + 標準構成 + シングル）
	if (
		is_post_type_archive( 'design_pattern' ) ||
		is_page_template( 'page-design_pattern_standard.php' ) ||
		( is_singular( 'design_pattern' ) )
	) {
		wp_enqueue_script(
			'child-dp-like',
			get_stylesheet_directory_uri() . '/javascript/dp-like.js',
			[],
			filemtime( get_stylesheet_directory() . '/javascript/dp-like.js' ),
			true
		);
		wp_localize_script( 'child-dp-like', 'dpLikeSettings', [
			'apiUrl' => rest_url( 'dp/v1/like' ),
		] );
	}

}, 11);

// 管理者向けデバッグトグル（design_pattern 関連ページのフッターに出力）
add_action( 'wp_footer', function () {
	$is_admin    = current_user_can( 'administrator' );
	$is_pro      = function_exists( 'swell_dp_is_pro_member' ) && swell_dp_is_pro_member();
	if ( ! $is_admin && ! $is_pro ) return;
	if ( ! (
		is_post_type_archive( 'design_pattern' ) ||
		is_page_template( 'page-design_pattern_standard.php' ) ||
		is_singular( 'design_pattern' )
	) ) return;
	?>
<style>
#dp-admin-toggle {
    position: fixed;
    bottom: 40px;
    left: 32px;
    z-index: 9999;
    background: #1e1e2e;
    color: #cdd6f4;
    padding: 10px 16px;
    border-radius: 8px;
    font-size: 13px;
    font-family: sans-serif;
    display: flex;
    align-items: center;
    gap: 10px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, .4);
}

@media screen and (max-width: 959px) {
    #dp-admin-toggle {
        top: 10px;
        right: 56px;
        bottom: auto;
        left: auto;
        height: calc(var(--dp-sp-header-h, 60px) - 20px);
        padding: 0 12px;
        border-radius: 4px;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.2s ease;
    }

    #dp-admin-toggle.is-floating {
        opacity: 1;
        pointer-events: auto;
        font-size: 12px;
    }
}
</style>
<div id="dp-admin-toggle">
    <span>インラインCSS</span>
    <label style="display:flex;align-items:center;gap:6px;cursor:pointer;">
        <input type="checkbox" id="dp-inline-toggle" style="width:16px;height:16px;">
        <span id="dp-inline-label">OFF</span>
    </label>
</div>
<script>
(function() {
    var toggle = document.getElementById('dp-inline-toggle');
    var label = document.getElementById('dp-inline-label');
    var stored = localStorage.getItem('dp_inline_mode');
    // 初期値: ON（管理者はデフォルトでインラインあり＝無料ユーザー目線でテスト）
    var isOn = stored !== null ? stored === 'on' : true;
    toggle.checked = isOn;
    label.textContent = isOn ? 'ON' : 'OFF';
    toggle.addEventListener('change', function() {
        isOn = toggle.checked;
        localStorage.setItem('dp_inline_mode', isOn ? 'on' : 'off');
        label.textContent = isOn ? 'ON' : 'OFF';
    });
})();

// SP: c-infoBar がスクロールで隠れたタイミングでフローティング表示
(function() {
    if (window.innerWidth > 959) return;
    var el = document.getElementById('dp-admin-toggle');
    if (!el) return;

    // ヘッダー高さをCSS変数にセット（ロゴ位置に合わせた天地中央配置用）
    var header = document.querySelector('.l-header');
    if (header) {
        document.documentElement.style.setProperty('--dp-sp-header-h', header.offsetHeight + 'px');
    }

    var infoBar = document.querySelector('.c-infoBar__link');
    if (!infoBar) {
        el.classList.add('is-floating');
        return;
    }

    var observer = new IntersectionObserver(function(entries) {
        el.classList.toggle('is-floating', !entries[0].isIntersecting);
    }, {
        threshold: 0
    });
    observer.observe(infoBar);
})();
</script>
<?php
} );

//WordPressで自動更新メール通知を無効化 / fuunctions.php
add_filter( 'auto_plugin_update_send_email', '__return_false' );

require_once get_stylesheet_directory() . '/inc/design-patterns.php';
require_once get_stylesheet_directory() . '/inc/dp-base-css-data.php';
require_once get_stylesheet_directory() . '/inc/dp-tips-data.php';
require_once get_stylesheet_directory() . '/inc/customizer.php';

// フッター サブナビ・右カスタムウィジェット・フッター設定は
// swell-dp プラグイン（includes/menus.php, footer-widget.php, customizer.php）で管理

/* =====================================================
 * いいね REST API（design_pattern 専用・認証不要）
 * ===================================================== */
add_action( 'rest_api_init', function () {
    register_rest_route( 'dp/v1', '/like', [
        'methods'             => 'POST',
        'callback'            => 'dp_handle_like',
        'permission_callback' => '__return_true',
        'args'                => [
            'post_id' => [
                'required'          => true,
                'validate_callback' => function ( $v ) { return is_numeric( $v ) && $v > 0; },
                'sanitize_callback' => 'absint',
            ],
            'action' => [
                'required'          => true,
                'validate_callback' => function ( $v ) { return in_array( $v, [ 'like', 'unlike' ], true ); },
            ],
        ],
    ] );
} );

function dp_handle_like( WP_REST_Request $request ) {
    $post_id = $request->get_param( 'post_id' );
    $action  = $request->get_param( 'action' );

    if ( get_post_type( $post_id ) !== 'design_pattern' ) {
        return new WP_Error( 'invalid_post', 'Invalid post type', [ 'status' => 400 ] );
    }

    $count = (int) get_post_meta( $post_id, '_dp_like_count', true );
    $count = ( $action === 'like' ) ? $count + 1 : max( 0, $count - 1 );
    update_post_meta( $post_id, '_dp_like_count', $count );

    return rest_ensure_response( [ 'count' => $count ] );
}

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

/**
 * エディタ専用スタイルを登録
 * SWELL と同じ enqueue_block_editor_assets + wp_enqueue_style() 方式で注入する
 */
add_action( 'enqueue_block_editor_assets', function () {
    $ver = filemtime( get_stylesheet_directory() . '/editor-style.css' );
    wp_enqueue_style(
        'swell-child-editor-style',
        get_stylesheet_directory_uri() . '/editor-style.css',
        [],
        $ver
    );
} );

/* ======================================================== */

/**
 * クラシックブロック（core/freeform）をエディタから非表示にする
 */
add_action( 'enqueue_block_editor_assets', function () {
    wp_add_inline_script( 'wp-blocks',
        "wp.domReady(function() { if (wp.blocks.getBlockType('core/freeform')) { wp.blocks.unregisterBlockType('core/freeform'); } });"
    );
} );

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

add_filter( 'sbi_settings_pages_capability', 'custom_sbi_access_capability' );
function custom_sbi_access_capability( $cap ) {
    // デフォルトの 'manage_options' (管理者) から 'read' (購読者以上) に条件を緩める
    return 'read'; 
}