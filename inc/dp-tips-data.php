<?php
/**
 * デザインパターン 定型TIPSデータ
 * プルダウン選択（tip_type）に対応する中央管理データ。
 * 動画やテキストの差し替えはここだけで完結する。
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * 共通ガイド用ブログパーツID（swell_bp）
 * ブログパーツで「共通ガイド」を作成したら ID を設定する。
 * 未設定（0）の場合はアコーディオン自体を非表示にする。
 */
define( 'DP_COMMON_GUIDE_BP_ID', 0 );

/**
 * 定型TIPSデータ取得
 *
 * @return array<string, array{title: string, media: string, text: string}>
 */
function dp_get_standard_tips(): array {
    return [
        'change_column' => [
            'title' => 'カラム数（列数）の変え方',
            'media' => '', // 例: '<video src="' . get_stylesheet_directory_uri() . '/assets/tips/column.mp4" autoplay loop muted playsinline></video>'
            'text'  => '外側の「カラムブロック」を選択し、右側パネルの「カラム設定」からカラム数を変更してください。',
        ],
        'change_image'  => [
            'title' => '画像の形・サイズの変え方',
            'media' => '',
            'text'  => '画像ブロックを選択し、右側パネルの「画像設定」からサイズや縦横比を変更できます。',
        ],
        'change_color'  => [
            'title' => '背景色・ボタンの色の変え方',
            'media' => '',
            'text'  => '色を変えたいブロックを選択し、右側パネルの「色設定」から変更してください。SWELLのテーマカラー（カスタマイザー）に連動します。',
        ],
        'add_item'      => [
            'title' => 'リストやステップの追加方法',
            'media' => '',
            'text'  => 'リスト・ステップブロック内の末尾でEnterキーを押すと項目を追加できます。ブロックを選択して「複製」する方法も使えます。',
        ],
    ];
}
