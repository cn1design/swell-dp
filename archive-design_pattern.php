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

<?php
get_footer();