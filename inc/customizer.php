<?php
/**
 * 子テーマ カスタマイザー設定
 *
 * SWELLカスタマイザーに子テーマ専用の設定欄を追加する。
 * functions.php の定数（BP_FOOTER_ADDRESS 等）はフォールバック値として残す。
 * 将来的にプラグイン化する際はこのファイルごと移行する。
 */

if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'customize_register', function ( WP_Customize_Manager $wp_customize ) {

    // =========================================================
    // セクション: 子テーマ フッター設定
    // =========================================================
    $wp_customize->add_section( 'swell_child_footer', [
        'title'       => 'フッター設定（子テーマ）',
        'description' => 'ブログパーツIDは 管理画面 › ブログパーツ の各記事URLで確認できます（例: post=387）',
        'priority'    => 155, // SWELLフッターセクションの直後
    ] );

    // ----- フッターロゴ下 ブログパーツID -----
    $wp_customize->add_setting( 'child_bp_footer_address', [
        'default'           => 0,
        'sanitize_callback' => 'absint',
        'transport'         => 'refresh',
    ] );

    $wp_customize->add_control( 'child_bp_footer_address', [
        'label'       => 'フッターロゴ下 ブログパーツID',
        'description' => '住所・TEL/FAX などを出力するブログパーツのID（0で非表示）',
        'section'     => 'swell_child_footer',
        'type'        => 'number',
        'input_attrs' => [ 'min' => 0, 'step' => 1, 'placeholder' => '例: 387' ],
    ] );

} );


/**
 * 子テーマ カスタマイザー値取得ヘルパー
 *
 * get_theme_mod() の値を優先し、未設定(0)の場合は定数フォールバックを返す。
 *
 * @param  string $key      カスタマイザー設定キー
 * @param  int    $fallback functions.php の定数フォールバック値
 * @return int    ブログパーツID（0 = 非表示）
 */
function child_get_bp_id( string $key, int $fallback = 0 ): int {
    $val = (int) get_theme_mod( $key, $fallback );
    return $val > 0 ? $val : $fallback;
}
