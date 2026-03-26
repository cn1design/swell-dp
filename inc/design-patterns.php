<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ====================================================================
// * デザインパターン コピーボタン ショートコード
// ====================================================================
// ------------------------------------------------------------
// 1. Clipboard.js 読み込み
// ------------------------------------------------------------
function enqueue_pattern_assets() {
    wp_enqueue_script(
        'clipboard-js',
        'https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.11/clipboard.min.js',
        array(),
        null,
        true
    );
}
add_action( 'wp_enqueue_scripts', 'enqueue_pattern_assets' );


// ------------------------------------------------------------
// 2. カスタム投稿タイプ：design_pattern
// ------------------------------------------------------------
function register_design_pattern_post_type() {
    register_post_type( 'design_pattern', [
        'labels' => [
            'name'               => 'SWELLデザインパターン',
            'singular_name'      => 'SWELLデザインパターン',
            'add_new'            => '新規追加',
            'add_new_item'       => '新規SWELLデザインパターンを追加',
            'edit_item'          => 'SWELLデザインパターンを編集',
            'new_item'           => '新規SWELLデザインパターン',
            'view_item'          => 'SWELLデザインパターンを表示',
            'search_items'       => 'SWELLデザインパターンを検索',
            'not_found'          => 'SWELLデザインパターンが見つかりません',
            'not_found_in_trash' => 'ゴミ箱にSWELLデザインパターンはありません',
            'menu_name'          => 'SWELLデザインパターン',
        ],
        'public'              => true,
        'has_archive'         => true,
        'show_in_rest'        => true,      // ブロックエディタ対応
        'supports'            => [
            'title',        // レイアウト・構図を一言
            'editor',       // ブロックエディタ（本文）
            'excerpt',      // UI/UXの説明（抜粋）
            'thumbnail',    // 静止画サムネイル（アイキャッチ）
            'custom-fields',
            'page-attributes', // 並び順（menu_order）
        ],
        'menu_icon'           => 'dashicons-layout',
        'menu_position'       => 5,
        'rewrite'             => [ 'slug' => 'design-patterns' ],
    ]);
}
add_action( 'init', 'register_design_pattern_post_type' );


// ------------------------------------------------------------
// 3. タクソノミー①：設置箇所（pattern_section）
// ------------------------------------------------------------
function register_pattern_section_taxonomy() {
    register_taxonomy( 'pattern_section', 'design_pattern', [
        'labels' => [
            'name'              => '設置箇所',
            'singular_name'     => '設置箇所',
            'search_items'      => '設置箇所を検索',
            'all_items'         => 'すべての設置箇所',
            'edit_item'         => '設置箇所を編集',
            'update_item'       => '設置箇所を更新',
            'add_new_item'      => '新しい設置箇所を追加',
            'new_item_name'     => '新しい設置箇所名',
            'menu_name'         => '設置箇所',
        ],
        'hierarchical'      => true,    // カテゴリー型
        'show_in_rest'      => true,
        'show_admin_column' => true,
        'rewrite'           => [ 'slug' => 'pattern-section' ],
    ]);
}
add_action( 'init', 'register_pattern_section_taxonomy' );


// ------------------------------------------------------------
// 4. タクソノミー②：ジャンル（pattern_industry）
// ------------------------------------------------------------
function register_pattern_industry_taxonomy() {
    register_taxonomy( 'pattern_industry', 'design_pattern', [
        'labels' => [
            'name'              => 'ジャンル',
            'singular_name'     => 'ジャンル',
            'search_items'      => 'ジャンルを検索',
            'all_items'         => 'すべてのジャンル',
            'edit_item'         => 'ジャンルを編集',
            'update_item'       => 'ジャンルを更新',
            'add_new_item'      => '新しいジャンルを追加',
            'new_item_name'     => '新しいジャンル名',
            'menu_name'         => 'ジャンル',
        ],
        'hierarchical'      => false,   // タグ型
        'show_in_rest'      => true,
        'show_admin_column' => true,
        'rewrite'           => [ 'slug' => 'pattern-industry' ],
    ]);
}
add_action( 'init', 'register_pattern_industry_taxonomy' );


// ------------------------------------------------------------
// 5. ACFフィールド登録（ACFプラグイン必須）
//    ブロックコード用テキストエリア＋gif URL
// ------------------------------------------------------------
add_action( 'acf/init', function() {
    if ( ! function_exists( 'acf_add_local_field_group' ) ) return;

    // --- グループ1: 基本設定（GIFサムネイル）---
    acf_add_local_field_group([
        'key'      => 'group_design_pattern',
        'title'    => 'デザインパターン設定',
        'fields'   => [

            // GIF サムネイル（画像型）
            [
                'key'           => 'field_pattern_gif',
                'label'         => 'GIFサムネイル',
                'name'          => 'pattern_gif',
                'type'          => 'image',
                'instructions'  => 'ホバー（PC）またはタップ（スマホ・タブレット）時に切り替わるGIF画像をアップロードしてください。空欄の場合はアイキャッチのみ表示されます。',
                'return_format' => 'array',
                'preview_size'  => 'medium',
                'mime_types'    => 'gif',
            ],

        ],
        'location' => [
            [
                [
                    'param'    => 'post_type',
                    'operator' => '==',
                    'value'    => 'design_pattern',
                ],
            ],
        ],
        'menu_order'      => 0,
        'position'        => 'normal',
        'style'           => 'default',
        'label_placement' => 'top',
    ]);

    // --- グループ2: カスタマイズTIPS設定（Repeater）---
    acf_add_local_field_group([
        'key'   => 'group_dp_custom_tips',
        'title' => 'カスタマイズTIPS設定',
        'fields' => [
            [
                'key'          => 'field_dp_custom_tips',
                'label'        => 'TIPSリスト',
                'name'         => 'dp_custom_tips',
                'type'         => 'repeater',
                'instructions' => 'このパターン専用のカスタマイズTIPSを最大3件登録してください。',
                'max'          => 3,
                'layout'       => 'block',
                'button_label' => 'TIPSを追加',
                'sub_fields'   => [

                    // 1-A: TIPSの種類（プルダウン）
                    [
                        'key'           => 'field_tip_type',
                        'label'         => 'TIPSの種類',
                        'name'          => 'tip_type',
                        'type'          => 'select',
                        'required'      => 1,
                        'instructions'  => '定型TIPSを選択するか、「独自のTIPS」で自由入力してください。',
                        'choices'       => [
                            'change_column' => 'カラム数（列数）の変え方',
                            'change_image'  => '画像の形・サイズの変え方',
                            'change_color'  => '背景色・ボタンの色の変え方',
                            'add_item'      => 'リストやステップの追加方法',
                            'custom'        => '【自由入力】独自のTIPS',
                        ],
                        'default_value' => '',
                        'allow_null'    => 0,
                        'ui'            => 1,
                    ],

                    // 1-B: 独自TIPSタイトル（custom 選択時のみ表示）
                    [
                        'key'               => 'field_custom_tip_title',
                        'label'             => '独自TIPSタイトル',
                        'name'              => 'custom_tip_title',
                        'type'              => 'text',
                        'instructions'      => '独自TIPSの見出しを入力してください。',
                        'conditional_logic' => [
                            [
                                [
                                    'field'    => 'field_tip_type',
                                    'operator' => '==',
                                    'value'    => 'custom',
                                ],
                            ],
                        ],
                    ],

                    // 1-C: 独自TIPS内容（custom 選択時のみ表示）
                    [
                        'key'               => 'field_custom_tip_content',
                        'label'             => '独自TIPS内容',
                        'name'              => 'custom_tip_content',
                        'type'              => 'textarea',
                        'rows'              => 4,
                        'instructions'      => '独自TIPSの説明文を入力してください。',
                        'conditional_logic' => [
                            [
                                [
                                    'field'    => 'field_tip_type',
                                    'operator' => '==',
                                    'value'    => 'custom',
                                ],
                            ],
                        ],
                    ],

                ],
            ],
        ],
        'location' => [
            [
                [
                    'param'    => 'post_type',
                    'operator' => '==',
                    'value'    => 'design_pattern',
                ],
            ],
        ],
        'menu_order'      => 1,
        'position'        => 'normal',
        'style'           => 'default',
        'label_placement' => 'top',
    ]);
});


// ------------------------------------------------------------
// 6. 一覧ショートコード：[pattern_list]
//    オプション:
//      section="slug"    設置箇所で絞り込み
//      industry="slug"   ジャンルで絞り込み
//      columns="3"       カラム数（デフォルト3）
//      posts_per_page="-1" 表示件数（デフォルト全件）
// ------------------------------------------------------------
function pattern_list_shortcode( $atts ) {
    $atts = shortcode_atts([
        'section'        => '',
        'industry'       => '',
        'columns'        => '3',
        'posts_per_page' => '-1',
    ], $atts );

    // タクソノミークエリ組み立て
    $tax_query = [];
    if ( $atts['section'] ) {
        $tax_query[] = [
            'taxonomy' => 'pattern_section',
            'field'    => 'slug',
            'terms'    => explode( ',', $atts['section'] ),
        ];
    }
    if ( $atts['industry'] ) {
        $tax_query[] = [
            'taxonomy' => 'pattern_industry',
            'field'    => 'slug',
            'terms'    => explode( ',', $atts['industry'] ),
        ];
    }
    if ( count( $tax_query ) > 1 ) {
        $tax_query['relation'] = 'AND';
    }

    $query_args = [
        'post_type'      => 'design_pattern',
        'post_status'    => 'publish',
        'posts_per_page' => intval( $atts['posts_per_page'] ),
        'orderby'        => 'menu_order date',
        'order'          => 'ASC',
    ];
    if ( ! empty( $tax_query ) ) {
        $query_args['tax_query'] = $tax_query;
    }

    $query = new WP_Query( $query_args );

    // フィルター用タクソノミーを全件取得
    $all_sections   = get_terms([ 'taxonomy' => 'pattern_section',  'hide_empty' => true ]);
    $all_industries = get_terms([ 'taxonomy' => 'pattern_industry', 'hide_empty' => true ]);

    $cols = max( 1, min( 4, intval( $atts['columns'] ) ) );

    ob_start();
    ?>
<div class="pl-wrap" id="pl-wrap-<?php echo uniqid(); ?>">

    <?php
        // ---------- フィルターUI ----------
        if ( ! empty( $all_sections ) || ! empty( $all_industries ) ) : ?>
    <div class="pl-filter-bar">

        <?php if ( ! empty( $all_sections ) ) : ?>
        <div class="pl-filter-group">
            <span class="pl-filter-label">パターン名</span>
            <div class="pl-filter-btns" data-filter-type="section">
                <button class="pl-filter-btn is-active" data-value="all">すべて</button>
                <?php foreach ( $all_sections as $term ) : ?>
                <button class="pl-filter-btn" data-value="<?php echo esc_attr( $term->slug ); ?>">
                    <?php echo esc_html( $term->name ); ?>
                    <span class="pl-filter-count"><?php echo $term->count; ?></span>
                </button>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if ( ! empty( $all_industries ) ) : ?>
        <div class="pl-filter-group">
            <span class="pl-filter-label">ジャンル</span>
            <div class="pl-filter-btns" data-filter-type="industry">
                <button class="pl-filter-btn is-active" data-value="all">すべて</button>
                <?php foreach ( $all_industries as $term ) : ?>
                <button class="pl-filter-btn" data-value="<?php echo esc_attr( $term->slug ); ?>">
                    <?php echo esc_html( $term->name ); ?>
                </button>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

    </div>
    <?php endif; ?>

    <!-- 件数表示 -->
    <p class="pl-count"><span class="pl-count-num"><?php echo $query->found_posts; ?></span> 件</p>

    <?php
        // ---------- カードグリッド ----------
        if ( $query->have_posts() ) : ?>
    <div class="pl-grid pl-cols-<?php echo $cols; ?>">

        <?php while ( $query->have_posts() ) : $query->the_post();
                $post_id    = get_the_ID();
                // 変更前： $block_code = get_field( 'pattern_block_code', $post_id );
                $block_code = get_post_field( 'post_content', $post_id );
                $gif_data   = get_field( 'pattern_gif',        $post_id );  // array: url, alt, width, height
                $gif_url    = $gif_data ? $gif_data['url'] : '';
                $gif_alt    = $gif_data ? $gif_data['alt'] : '';
                $thumb_url  = get_the_post_thumbnail_url( $post_id, 'large' );
                $excerpt    = get_the_excerpt();
                $detail_url = get_permalink( $post_id );

                // タクソノミー取得
                $sections   = get_the_terms( $post_id, 'pattern_section' );
                $industries = get_the_terms( $post_id, 'pattern_industry' );

                // フィルター用data属性
                $data_section  = '';
                $data_industry = '';
                if ( $sections && ! is_wp_error( $sections ) ) {
                    $data_section = implode( ' ', wp_list_pluck( $sections, 'slug' ) );
                }
                if ( $industries && ! is_wp_error( $industries ) ) {
                    $data_industry = implode( ' ', wp_list_pluck( $industries, 'slug' ) );
                }
            ?>
        <div class="pl-card" data-section="<?php echo esc_attr( $data_section ); ?>"
            data-industry="<?php echo esc_attr( $data_industry ); ?>">
            <!-- サムネイル -->
            <div class="pl-card-thumb<?php echo $gif_url ? ' has-gif' : ''; ?>"
                <?php if ( $gif_url ) : ?>data-gif="<?php echo esc_url( $gif_url ); ?>" <?php endif; ?>>
                <?php if ( $thumb_url ) : ?>
                <img class="pl-thumb-still" src="<?php echo esc_url( $thumb_url ); ?>"
                    alt="<?php the_title_attribute(); ?>" loading="lazy">
                <?php else : ?>
                <div class="pl-thumb-placeholder">No Image</div>
                <?php endif; ?>

                <?php if ( $gif_url ) : ?>
                <img class="pl-thumb-gif" src="" alt="<?php echo esc_attr( $gif_alt ); ?>" aria-hidden="true">
                <span class="pl-gif-badge">▶ GIF</span>
                <?php endif; ?>
            </div>

            <!-- カード本文 -->
            <div class="pl-card-body">

                <!-- タクソノミーバッジ -->
                <div class="pl-card-tags">
                    <?php if ( $sections && ! is_wp_error( $sections ) ) :
                            foreach ( $sections as $term ) : ?>
                    <span class="pl-tag pl-tag--section"><?php echo esc_html( $term->name ); ?></span>
                    <?php endforeach; endif; ?>
                    <?php if ( $industries && ! is_wp_error( $industries ) ) :
                            foreach ( $industries as $term ) : ?>
                    <span class="pl-tag pl-tag--industry"><?php echo esc_html( $term->name ); ?></span>
                    <?php endforeach; endif; ?>
                </div>

                <!-- タイトル -->
                <h3 class="pl-card-title"><?php the_title(); ?></h3>

            </div>

            <!-- ボタンエリア -->
            <div class="pl-card-actions">

                <?php if ( $block_code ) : ?>
                <button class="pl-btn pl-btn--copy"
                    data-code="<?php echo htmlspecialchars( $block_code, ENT_QUOTES, 'UTF-8' ); ?>"
                    data-label-copied="コピー完了 ✓" data-label-default="コピーする">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path
                            d="M16 1H4a2 2 0 0 0-2 2v14h2V3h12V1zm3 4H8a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h11a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2zm0 16H8V7h11v14z" />
                    </svg>
                    コピーする
                </button>
                <?php else : ?>
                <button class="pl-btn pl-btn--copy is-disabled" disabled>
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path
                            d="M16 1H4a2 2 0 0 0-2 2v14h2V3h12V1zm3 4H8a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h11a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2zm0 16H8V7h11v14z" />
                    </svg>
                    コード未登録
                </button>
                <?php endif; ?>

                <a class="pl-btn pl-btn--detail" href="<?php echo esc_url( $detail_url ); ?>">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path
                            d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8a3 3 0 1 0 0 6 3 3 0 0 0 0-6z" />
                    </svg>
                    詳細を見る
                </a>

            </div>
        </div>
        <?php endwhile; wp_reset_postdata(); ?>

        <div class="pl-no-result" id="pl-no-result" style="display:none;">
            該当するパターンが見つかりませんでした。
        </div>

    </div><!-- /.pl-grid -->

    <?php else : ?>
    <p class="pl-empty">デザインパターンがまだ登録されていません。</p>
    <?php endif; ?>

</div><!-- /.pl-wrap -->

<?php
    // ---------- JS ----------
    static $pl_css_output = false;
    if ( ! $pl_css_output ) :
        $pl_css_output = true;
    ?>
<script>
(function() {
    var isTouch = window.matchMedia('(hover: none)').matches;

    document.querySelectorAll('.pl-card-thumb.has-gif').forEach(function(thumb) {
        var gifImg = thumb.querySelector('.pl-thumb-gif');
        var gifSrc = thumb.dataset.gif;
        var loaded = false;

        function loadGif() {
            if (!loaded && gifSrc) {
                gifImg.src = gifSrc;
                loaded = true;
            }
        }

        if (isTouch) {
            // ---- タッチデバイス：タップでトグル ----
            thumb.addEventListener('click', function(e) {
                // ボタンへのタップは除外
                if (e.target.closest('.pl-btn')) return;
                loadGif();
                thumb.classList.toggle('is-gif-active');
            });
        } else {
            // ---- PC：ホバーで切り替え（遅延ロード）----
            thumb.addEventListener('mouseenter', function() {
                loadGif();
            });
        }
    });

    // コピーボタン
    document.querySelectorAll('.pl-btn--copy:not(.is-disabled)').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var code = this.dataset.code;
            var copied = this.dataset.labelCopied || 'コピー完了 ✓';
            var def = this.dataset.labelDefault || 'コピーする';
            var self = this;
            navigator.clipboard.writeText(code).then(function() {
                self.classList.add('is-copied');
                self.innerHTML =
                    '<svg viewBox="0 0 24 24" style="width:14px;height:14px;fill:currentColor;flex-shrink:0"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>' +
                    copied;
                setTimeout(function() {
                    self.classList.remove('is-copied');
                    self.innerHTML =
                        '<svg viewBox="0 0 24 24" style="width:14px;height:14px;fill:currentColor;flex-shrink:0"><path d="M16 1H4a2 2 0 0 0-2 2v14h2V3h12V1zm3 4H8a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h11a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2zm0 16H8V7h11v14z"/></svg>' +
                        def;
                }, 2500);
            });
        });
    });

    // フィルター
    var activeSection = 'all';
    var activeIndustry = 'all';

    function applyFilter() {
        var cards = document.querySelectorAll('.pl-card');
        var visible = 0;
        cards.forEach(function(card) {
            var secMatch = activeSection === 'all' || card.dataset.section.split(' ').indexOf(
                activeSection) !== -1;
            var indMatch = activeIndustry === 'all' || card.dataset.industry.split(' ').indexOf(
                activeIndustry) !== -1;
            if (secMatch && indMatch) {
                card.classList.remove('is-hidden');
                visible++;
            } else {
                card.classList.add('is-hidden');
            }
        });
        var countNum = document.querySelector('.pl-count-num');
        if (countNum) countNum.textContent = visible;
        var noResult = document.getElementById('pl-no-result');
        if (noResult) noResult.style.display = visible === 0 ? 'block' : 'none';
    }

    document.querySelectorAll('.pl-filter-btns').forEach(function(group) {
        var type = group.dataset.filterType;
        group.querySelectorAll('.pl-filter-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                group.querySelectorAll('.pl-filter-btn').forEach(function(b) {
                    b.classList.remove('is-active');
                });
                this.classList.add('is-active');
                if (type === 'section') activeSection = this.dataset.value;
                if (type === 'industry') activeIndustry = this.dataset.value;
                applyFilter();
            });
        });
    });

    // ----- フィルターグループ アコーディオン -----
    document.querySelectorAll('.pl-filter-group').forEach(function(group) {
        var btnsEl = group.querySelector('.pl-filter-btns');
        if (!btnsEl) return;

        var btns = Array.from(btnsEl.querySelectorAll('.pl-filter-btn'));
        if (btns.length < 2) return;

        // 2行目以降があるか確認（完全にレンダリングされた後の位置で判定）
        var firstTop = btns[0].getBoundingClientRect().top;
        var hasMultiRow = btns.some(function(b, i) {
            return i > 0 && b.getBoundingClientRect().top > firstTop + 2;
        });
        if (!hasMultiRow) return;

        // 1行分の高さ（最初のボタンの高さ）
        var oneRowH = btns[0].offsetHeight;
        var fullH = btnsEl.scrollHeight;

        // 折りたたみ初期化
        btnsEl.style.overflow = 'hidden';
        btnsEl.style.maxHeight = oneRowH + 'px';
        btnsEl.style.transition = 'max-height 0.3s ease';
        group.classList.add('is-collapsible');

        // トグルボタン生成（グループ最右上）
        var toggle = document.createElement('button');
        toggle.className = 'pl-filter-toggle';
        toggle.type = 'button';
        toggle.setAttribute('aria-expanded', 'false');
        toggle.setAttribute('aria-label', '展開');
        toggle.textContent = '▼';
        group.appendChild(toggle);

        toggle.addEventListener('click', function() {
            var isOpen = toggle.classList.contains('is-open');
            if (isOpen) {
                btnsEl.style.maxHeight = oneRowH + 'px';
                toggle.classList.remove('is-open');
                toggle.setAttribute('aria-expanded', 'false');
            } else {
                // 展開前に scrollHeight を再取得（動的変化に対応）
                btnsEl.style.maxHeight = btnsEl.scrollHeight + 'px';
                toggle.classList.add('is-open');
                toggle.setAttribute('aria-expanded', 'true');
            }
        });
    });
})();
</script>

<?php
    endif; // end css/js static output

    return ob_get_clean();
}
add_shortcode( 'pattern_list', 'pattern_list_shortcode' );

// ============================================================