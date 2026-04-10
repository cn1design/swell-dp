<?php
/**
 * Archive template for design_pattern
 * JS絞り込み + URLパラメータ初期適用 + ビュー切替
 */

get_header();

$all_sections   = get_terms([ 'taxonomy' => 'pattern_section',  'hide_empty' => true ]);
$all_industries = get_terms([ 'taxonomy' => 'pattern_industry', 'hide_empty' => true ]);

$query = new WP_Query([
    'post_type'      => 'design_pattern',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'orderby'        => 'menu_order date',
    'order'          => 'ASC',
]);

$dp_lp_pages = get_pages([
    'meta_key'   => '_wp_page_template',
    'meta_value' => 'page-design_pattern_standard.php',
    'number'     => 1,
]);
$dp_lp_url = ! empty( $dp_lp_pages ) ? get_permalink( $dp_lp_pages[0] ) : '';
?>

<main id="primary" class="site-main">
    <div class="l-container">

        <header class="archive-header">
            <h1 class="archive-title">
                <?php echo esc_html( post_type_archive_title( '', false ) ); ?>
            </h1>
        </header>

        <?php if ( $dp_lp_url ) : ?>
        <div class="pl-view-toggle">
            <span class="pl-view-toggle__btn is-active">パーツを探す</span>
            <a href="<?php echo esc_url( $dp_lp_url ); ?>" class="pl-view-toggle__btn">LP構成順</a>
        </div>
        <?php endif; ?>

        <div class="pl-wrap" id="dp-archive-wrap">

            <?php if ( ! empty( $all_sections ) || ! empty( $all_industries ) ) : ?>
            <div class="pl-filter-bar">

                <?php if ( ! empty( $all_sections ) ) : ?>
                <div class="pl-filter-group">
                    <span class="pl-filter-label">パターン名</span>
                    <div class="pl-filter-btns" data-filter-type="section">
                        <button class="pl-filter-btn is-active" data-value="all">すべて</button>
                        <?php foreach ( $all_sections as $term ) : ?>
                        <button class="pl-filter-btn" data-value="<?php echo esc_attr( $term->slug ); ?>">
                            <?php echo esc_html( $term->name ); ?>
                            <span class="pl-filter-count"><?php echo esc_html( $term->count ); ?></span>
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

            <?php dp_render_base_css_copy_area(); ?>

            <p class="pl-count"><span class="pl-count-num"><?php echo esc_html( $query->found_posts ); ?></span> 件</p>

            <?php if ( $query->have_posts() ) : ?>
            <div class="pl-grid pl-cols-3">

                <?php while ( $query->have_posts() ) : $query->the_post();
                    $post_id    = get_the_ID();
                    $block_code = get_post_field( 'post_content', $post_id );
                    $gif_data   = get_field( 'pattern_gif', $post_id );
                    $gif_url    = $gif_data ? $gif_data['url'] : '';
                    $gif_alt    = $gif_data ? $gif_data['alt'] : '';
                    $thumb_url  = get_the_post_thumbnail_url( $post_id, 'large' );
                    $excerpt    = get_the_excerpt();
                    $detail_url = get_permalink( $post_id );

                    $sections   = get_the_terms( $post_id, 'pattern_section' );
                    $industries = get_the_terms( $post_id, 'pattern_industry' );

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

                    <div class="pl-card-thumb<?php echo $gif_url ? ' has-gif' : ''; ?>"
                        <?php if ( $gif_url ) : ?>data-gif="<?php echo esc_url( $gif_url ); ?>" <?php endif; ?>>

                        <?php /* LPビルダー 選択チェックボックス（左上 absolute）*/ ?>
                        <label class="pl-card-select" title="LPビルダーに追加">
                            <input type="checkbox" class="pl-card-select__input"
                                data-post-id="<?php echo esc_attr( $post_id ); ?>"
                                data-title="<?php echo esc_attr( get_the_title() ); ?>"
                                data-thumb="<?php echo esc_url( $thumb_url ); ?>">
                            <span class="pl-card-select__mark" aria-hidden="true"></span>
                        </label>

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

                        <?php $like_count = (int) get_post_meta( $post_id, '_dp_like_count', true ); ?>
                        <button type="button" class="pl-like-btn"
                            data-post-id="<?php echo esc_attr( $post_id ); ?>"
                            data-like-count="<?php echo esc_attr( $like_count ); ?>"
                            aria-label="いいね" aria-pressed="false">
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" fill="none" stroke="currentColor" stroke-width="2"/>
                            </svg>
                            <span class="pl-like-count"<?php echo $like_count <= 0 ? ' hidden' : ''; ?>><?php echo $like_count > 0 ? esc_html( $like_count ) : ''; ?></span>
                        </button>
                    </div>

                    <div class="pl-card-body">
                        <div class="pl-card-tags">
                            <?php if ( $sections && ! is_wp_error( $sections ) ) :
                                foreach ( $sections as $t ) : ?>
                            <span class="pl-tag pl-tag--section"><?php echo esc_html( $t->name ); ?></span>
                            <?php endforeach; endif; ?>
                            <?php if ( $industries && ! is_wp_error( $industries ) ) :
                                foreach ( $industries as $t ) : ?>
                            <span class="pl-tag pl-tag--industry"><?php echo esc_html( $t->name ); ?></span>
                            <?php endforeach; endif; ?>
                        </div>

                        <h3 class="pl-card-title"><?php the_title(); ?></h3>

                    </div>

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

            <?php include get_stylesheet_directory() . '/inc/dp-taxonomy-footer.php'; ?>

        </div><!-- /.pl-wrap -->

    </div><!-- /.l-container -->
</main>

<?php /* ========================================================
 * スプラッシュ画面 + ヘルプボタン（チュートリアル）
 * 初回訪問時のみ表示。JS: dp-tutorial.js / CSS: _c-tutorial.scss
 * ======================================================== */ ?>

<div id="dp-splash" class="dp-splash" role="dialog" aria-modal="true" aria-label="使い方ガイド">
    <div class="dp-splash__inner">
        <div class="dp-splash__icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm16-4H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-1 9h-4v4h-2v-4H9V9h4V5h2v4h4v2z"/></svg>
        </div>
        <p class="dp-splash__lead">Welcome</p>
        <h2 class="dp-splash__title">SWELLデザインパターンへようこそ</h2>
        <p class="dp-splash__desc">コピペするだけでプロ品質のLPが作れます。<br>はじめての方に、かんたんな使い方をご案内します（約1分）。</p>
        <button id="dp-splash-start" class="dp-splash__btn">さっそく使ってみる</button>
        <button id="dp-splash-skip" class="dp-splash__skip">スキップして使い始める</button>
    </div>
</div>

<button id="dp-help-btn" class="dp-help-btn" aria-label="使い方を見る">
    <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 17h-2v-2h2v2zm2.07-7.75l-.9.92C13.45 12.9 13 13.5 13 15h-2v-.5c0-1.1.45-2.1 1.17-2.83l1.24-1.26c.37-.36.59-.86.59-1.41 0-1.1-.9-2-2-2s-2 .9-2 2H8c0-2.21 1.79-4 4-4s4 1.79 4 4c0 .88-.36 1.68-.93 2.25z"/></svg>
    使い方
</button>

<?php /* ========================================================
 * LPビルダー UI（カートドロワー + オーバーレイ + トリガー）
 * archive-design_pattern.php 専用。JSは Phase 2 で実装。
 * ======================================================== */ ?>

<div id="dp-cart-drawer" class="dp-cart-drawer" aria-hidden="true" role="dialog" aria-modal="true" aria-label="LPビルダー">
    <div class="dp-cart-drawer__header">
        <div class="dp-cart-drawer__title">
            <svg viewBox="0 0 24 24" aria-hidden="true" fill="currentColor"><path d="M4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm16-4H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-1 9h-4v4h-2v-4H9V9h4V5h2v4h4v2z"/></svg>
            LPビルダー
            <span class="dp-cart-drawer__badge" id="dp-cart-badge">0</span>
        </div>
        <button class="dp-cart-drawer__close" id="dp-cart-close" aria-label="ドロワーを閉じる">
            <svg viewBox="0 0 24 24" aria-hidden="true" fill="currentColor"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
        </button>
    </div>
    <div class="dp-cart-drawer__body">
        <ul class="dp-cart-list" id="dp-cart-list"></ul>
        <p class="dp-cart-empty" id="dp-cart-empty">パターンを選択してください</p>
    </div>
    <div class="dp-cart-drawer__footer">
        <button class="dp-cart-copy-btn" id="dp-cart-copy-btn" disabled>
            <svg viewBox="0 0 24 24" aria-hidden="true" fill="currentColor"><path d="M16 1H4a2 2 0 0 0-2 2v14h2V3h12V1zm3 4H8a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h11a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2zm0 16H8V7h11v14z"/></svg>
            一括コピーしてSWELLに貼る
        </button>
    </div>
</div>

<div id="dp-cart-overlay" class="dp-cart-overlay" role="presentation"></div>

<button id="dp-cart-trigger" class="dp-cart-trigger" aria-label="LPビルダーを開く（選択: 0件）">
    <svg viewBox="0 0 24 24" aria-hidden="true" fill="currentColor"><path d="M4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm16-4H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-1 9h-4v4h-2v-4H9V9h4V5h2v4h4v2z"/></svg>
    <span class="dp-cart-trigger__label">LPビルダー</span>
    <span class="dp-cart-trigger__count" id="dp-cart-trigger-count" data-count="0">0</span>
</button>

<?php
get_footer();