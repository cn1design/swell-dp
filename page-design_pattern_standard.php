<?php
/**
 * Template Name: デザインパターン（LP構成順）
 * カテゴリーセクション分割 + ページ内アンカーナビ
 */

get_header();

// LP ストーリー順でセクションを定義（WPのデフォルト順に依存しない）
$section_order = [
    'hero'        => 'メインビジュアル',
    'problem'     => '悩み・問題',
    'solution'    => '解決できること',
    'strength'    => '選ばれる理由',
    'works'       => '実績',
    'voice'       => 'お客様の声',
    'compare'     => '比較表',
    'pricing'     => '料金プラン',
    'step'        => 'ご利用の流れ',
    'faq'         => 'よくある質問',
    'news'        => 'お知らせ',
    'company'     => '会社概要',
    'contact'     => 'お問い合わせ',
    'cta'         => 'CTAボタン',
];

$existing_terms = get_terms([
    'taxonomy'   => 'pattern_section',
    'hide_empty' => true,
]);

$term_map = [];
if ( ! is_wp_error( $existing_terms ) && ! empty( $existing_terms ) ) {
    foreach ( $existing_terms as $t ) {
        $term_map[ $t->slug ] = $t;
    }
}

$active_sections = [];
foreach ( $section_order as $slug => $name ) {
    if ( isset( $term_map[ $slug ] ) ) {
        $active_sections[ $slug ] = $name;
    }
}

$dp_archive_url = get_post_type_archive_link( 'design_pattern' );
?>

<main id="primary" class="site-main">
    <div class="dp-standard-wrapper">
        <div class="l-container">
            <header class="archive-header">
                <h1 class="archive-title">
                    <?php echo esc_html( get_the_title() ); ?>
                </h1>
            </header>

            <?php if ( $dp_archive_url ) : ?>
            <div class="pl-view-toggle">
                <a href="<?php echo esc_url( $dp_archive_url ); ?>" class="pl-view-toggle__btn">パーツを探す</a>
                <span class="pl-view-toggle__btn is-active">LP構成順</span>
            </div>
            <?php endif; ?>

            <?php if ( ! empty( $active_sections ) ) : ?>
            <div class="pl-wrap">
                <?php dp_render_base_css_copy_area(); ?>

                <!-- ===== カテゴリーセクション ===== -->
                <?php foreach ( $active_sections as $slug => $name ) :
                    $section_query = new WP_Query([
                        'post_type'      => 'design_pattern',
                        'post_status'    => 'publish',
                        'posts_per_page' => -1,
                        'orderby'        => 'menu_order date',
                        'order'          => 'ASC',
                        'tax_query'      => [[
                            'taxonomy' => 'pattern_section',
                            'field'    => 'slug',
                            'terms'    => $slug,
                        ]],
                    ]);

                    if ( ! $section_query->have_posts() ) :
                        wp_reset_postdata();
                        continue;
                    endif;
                ?>
                <section class="pl-section" id="pl-section-<?php echo esc_attr( $slug ); ?>">
                    <h2 class="pl-section-title"><?php echo esc_html( $name ); ?></h2>
                    <div class="pl-section-grid">

                        <?php while ( $section_query->have_posts() ) : $section_query->the_post();
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
                        ?>
                        <div class="pl-card">
                            <!-- サムネイル -->
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
                                <img class="pl-thumb-gif" src="" alt="<?php echo esc_attr( $gif_alt ); ?>"
                                    aria-hidden="true">
                                <span class="pl-gif-badge">▶ GIF</span>
                                <?php endif; ?>

                                <?php $like_count = (int) get_post_meta( $post_id, '_dp_like_count', true ); ?>
                                <button type="button" class="pl-like-btn"
                                    data-post-id="<?php echo esc_attr( $post_id ); ?>"
                                    data-like-count="<?php echo esc_attr( $like_count ); ?>" aria-label="いいね"
                                    aria-pressed="false">
                                    <svg viewBox="0 0 24 24" aria-hidden="true">
                                        <path
                                            d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"
                                            fill="none" stroke="currentColor" stroke-width="2" />
                                    </svg>
                                    <span class="pl-like-count"
                                        <?php echo $like_count <= 0 ? ' hidden' : ''; ?>><?php echo $like_count > 0 ? esc_html( $like_count ) : ''; ?></span>
                                </button>
                            </div>

                            <!-- カード本文 -->
                            <div class="pl-card-body">
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

                    </div>
                </section>
                <?php endforeach; ?>

                <!-- ===== タクソノミーフッターナビ ===== -->
                <?php include get_stylesheet_directory() . '/inc/dp-taxonomy-footer.php'; ?>

            </div><!-- /.pl-wrap -->
            <?php else : ?>
            <p class="pl-empty">デザインパターンがまだ登録されていません。</p>
            <?php endif; ?>

        </div><!-- /.l-container -->
    </div><!-- /.dp-standard-wrapper -->
</main>

<script>
(() => {
    const wrapper = document.querySelector('.dp-standard-wrapper');
    if (!wrapper) return;

    const isTouch = window.matchMedia('(hover: none)').matches;

    /* =========================================================
     * 1. GIF トグル（Event Delegation）
     * ========================================================= */
    if (isTouch) {
        wrapper.addEventListener('click', (e) => {
            const thumb = e.target.closest('.pl-card-thumb.has-gif');
            if (!thumb || e.target.closest('.pl-btn')) return;

            const gifImg = thumb.querySelector('.pl-thumb-gif');
            if (gifImg && thumb.dataset.gif && !gifImg.src) {
                gifImg.src = thumb.dataset.gif;
            }
            thumb.classList.toggle('is-gif-active');
        });
    } else {
        wrapper.addEventListener('mouseover', (e) => {
            const thumb = e.target.closest('.pl-card-thumb.has-gif');
            if (!thumb) return;

            const gifImg = thumb.querySelector('.pl-thumb-gif');
            if (gifImg && thumb.dataset.gif && !gifImg.src) {
                gifImg.src = thumb.dataset.gif;
            }
        });
    }

    /* =========================================================
     * 2. スクロールヒント（960px以上・4枚以上のグリッド）
     * ========================================================= */
    if (window.innerWidth >= 960) {
        wrapper.querySelectorAll('.pl-section-grid').forEach(grid => {
            if (grid.querySelectorAll('.pl-card').length < 4) return;
            if (grid.scrollWidth <= grid.clientWidth + 1) return;

            const section = grid.parentElement;
            if (getComputedStyle(section).position === 'static') {
                section.style.position = 'relative';
            }

            const topPx = grid.offsetTop + 'px';
            const heightPx = (grid.offsetHeight - 24) + 'px';

            // 右矢印
            const hintRight = document.createElement('div');
            hintRight.className = 'pl-scroll-hint pl-scroll-hint--right';
            hintRight.innerHTML =
                '<button class="pl-scroll-hint__arrow" aria-label="右にスクロール" tabindex="-1">' +
                '<svg viewBox="0 0 24 24" aria-hidden="true"><polyline points="9 18 15 12 9 6"></polyline></svg>' +
                '</button>';
            hintRight.style.top = topPx;
            hintRight.style.height = heightPx;

            // 左矢印（初期は非表示）
            const hintLeft = document.createElement('div');
            hintLeft.className = 'pl-scroll-hint pl-scroll-hint--left is-hidden';
            hintLeft.innerHTML =
                '<button class="pl-scroll-hint__arrow" aria-label="左にスクロール" tabindex="-1">' +
                '<svg viewBox="0 0 24 24" aria-hidden="true"><polyline points="15 18 9 12 15 6"></polyline></svg>' +
                '</button>';
            hintLeft.style.top = topPx;
            hintLeft.style.height = heightPx;

            section.appendChild(hintRight);
            section.appendChild(hintLeft);

            const cardWidth = () => grid.querySelector('.pl-card')?.offsetWidth ?? 280;

            hintRight.querySelector('.pl-scroll-hint__arrow').addEventListener('click', () => {
                grid.scrollBy({
                    left: cardWidth() + 24,
                    behavior: 'smooth'
                });
            });
            hintLeft.querySelector('.pl-scroll-hint__arrow').addEventListener('click', () => {
                grid.scrollBy({
                    left: -(cardWidth() + 24),
                    behavior: 'smooth'
                });
            });

            // スクロール位置に応じて矢印を表示/非表示
            const updateArrows = () => {
                const atStart = grid.scrollLeft <= 0;
                const atEnd = grid.scrollLeft + grid.clientWidth >= grid.scrollWidth - 1;
                hintLeft.classList.toggle('is-hidden', atStart);
                hintRight.classList.toggle('is-hidden', atEnd);
            };
            grid.addEventListener('scroll', updateArrows);
            updateArrows();
        });
    }

    /* =========================================================
     * 3. コピーボタン（Event Delegation）
     * ========================================================= */
    wrapper.addEventListener('click', (e) => {
        const btn = e.target.closest('.pl-btn--copy:not(.is-disabled)');
        if (!btn) return;

        const code = btn.dataset.code;
        const copied = btn.dataset.labelCopied || 'コピー完了 ✓';
        const def = btn.dataset.labelDefault || 'コピーする';

        navigator.clipboard.writeText(code).then(() => {
            btn.classList.add('is-copied');
            btn.innerHTML =
                '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>' +
                copied;
            setTimeout(() => {
                btn.classList.remove('is-copied');
                btn.innerHTML =
                    '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M16 1H4a2 2 0 0 0-2 2v14h2V3h12V1zm3 4H8a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h11a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2zm0 16H8V7h11v14z"/></svg>' +
                    def;
            }, 2500);
        });
    });
})();
</script>

<?php /* ========================================================
 * LPビルダー UI（カートドロワー + オーバーレイ + トリガー）
 * page-design_pattern_standard.php 専用。JSは Phase 2 で実装。
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