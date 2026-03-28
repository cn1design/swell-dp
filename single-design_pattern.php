<?php
/**
 * Template Name: デザインパターン シングルページ
 * 対象ファイル: single-design_pattern.php
 * 設置場所: 子テーマルートに配置
 */

get_header();

while ( have_posts() ) : the_post();
    $post_id    = get_the_ID();
    // 生の投稿コンテンツ（ブロックエディタの最新データ）を取得する
    $raw_block_code = $post->post_content;
    $gif_data   = get_field( 'pattern_gif',$post_id );
    $gif_url    = $gif_data ? $gif_data['url'] : '';
    $gif_alt    = $gif_data ? $gif_data['alt'] : '';
    $thumb_url  = get_the_post_thumbnail_url( $post_id, 'full' );
    $sections   = get_the_terms( $post_id, 'pattern_section' );
    $industries = get_the_terms( $post_id, 'pattern_industry' );
?>

<main id="main" class="l-main single-design_pattern">
    <div class="l-article__body">

        <article id="post-<?php the_ID(); ?>" <?php post_class( 'dp-single' ); ?>>

            <!-- ===== ヘッダー ===== -->
            <header class="dp-single__header">
                <div class="dp-single__header-inner">

                    <!-- タクソノミーバッジ -->
                    <div class="dp-tags">
                        <?php if ( $sections && ! is_wp_error( $sections ) ) :
                        foreach ( $sections as $term ) : ?>
                        <a href="<?php echo esc_url( get_term_link( $term ) ); ?>" class="dp-tag pl-tag--section">
                            <?php echo esc_html( $term->name ); ?>
                        </a>
                        <?php endforeach; endif; ?>
                        <?php if ( $industries && ! is_wp_error( $industries ) ) :
                        foreach ( $industries as $term ) : ?>
                        <a href="<?php echo esc_url( get_term_link( $term ) ); ?>" class="dp-tag pl-tag--industry">
                            <?php echo esc_html( $term->name ); ?>
                        </a>
                        <?php endforeach; endif; ?>
                    </div>

                    <!-- タイトル -->
                    <h1 class="dp-single__title"><?php the_title(); ?></h1>

                    <!-- 抜粋（UI/UX説明） -->
                    <?php $excerpt = get_the_excerpt(); if ( $excerpt ) : ?>
                    <p class="dp-single__excerpt"><?php echo esc_html( $excerpt ); ?></p>
                    <?php endif; ?>

                </div>
            </header>

            <!-- ===== 本文（ブロックエディタ） ===== -->
            <?php $content = get_the_content(); if ( $content ) : ?>
            <div class="dp-single__content entry-content">
                <?php echo apply_filters( 'the_content', $content ); ?>
            </div>
            <?php endif; ?>

            <!-- ===== コピーボタン + スマホいいねボタン ===== -->
            <?php
            $like_count = (int) get_post_meta( $post_id, '_dp_like_count', true );
            ?>
            <?php if ( $raw_block_code ) : ?>
            <p class="dp-copy-hint">＼ご自身のSWELLテーマの編集画面にコピペ／</p>
            <div class="dp-copy-actions">
                <button type="button" class="dp-like-inline"
                    data-post-id="<?php echo esc_attr( $post_id ); ?>"
                    data-like-count="<?php echo esc_attr( $like_count ); ?>"
                    aria-label="いいね" aria-pressed="false">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" fill="none" stroke="currentColor" stroke-width="2"/>
                    </svg>
                </button>
                <button type="button" class="dp-single__copy-area dp-copy-btn"
                    data-code="<?php echo htmlspecialchars( $raw_block_code, ENT_QUOTES, 'UTF-8' ); ?>">
                    <svg class="dp-copy-icon" viewBox="0 0 24 24" aria-hidden="true">
                        <path
                            d="M16 1H4a2 2 0 0 0-2 2v14h2V3h12V1zm3 4H8a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h11a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2zm0 16H8V7h11v14z" />
                    </svg>
                    <span class="dp-copy-text">ブロックコードをコピーする</span>
                </button>
            </div>
            <?php endif; ?>

            <!-- PC固定いいねボタン（.dp-like-fixed は960px以上のみ表示） -->
            <div class="dp-like-fixed">
                <button type="button" class="dp-like-fixed__btn"
                    data-post-id="<?php echo esc_attr( $post_id ); ?>"
                    data-like-count="<?php echo esc_attr( $like_count ); ?>"
                    aria-label="いいね" aria-pressed="false">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" fill="none" stroke="currentColor" stroke-width="2"/>
                    </svg>
                    <span class="dp-like-fixed__count"<?php echo $like_count <= 0 ? ' hidden' : ''; ?>><?php echo $like_count > 0 ? esc_html( $like_count ) : ''; ?></span>
                </button>
            </div>

            <!-- ===== ヘルプエリア ===== -->
            <?php
            $custom_tips   = get_field( 'dp_custom_tips', $post_id );
            $standard_tips = dp_get_standard_tips();
            $has_tips      = ! empty( $custom_tips );
            $has_common    = defined( 'DP_COMMON_GUIDE_BP_ID' ) && DP_COMMON_GUIDE_BP_ID > 0;

            if ( $has_tips || $has_common ) :
            ?>
            <div class="dp-help-area">

                <?php if ( $has_tips ) : ?>
                <div class="dp-help-specific">
                    <h3 class="dp-help-title">このパターンのカスタマイズ方法</h3>
                    <ul class="dp-help-list">
                        <?php foreach ( array_slice( $custom_tips, 0, 3 ) as $tip ) :
                            $type = $tip['tip_type'];
                            if ( 'custom' !== $type && isset( $standard_tips[ $type ] ) ) {
                                $title = $standard_tips[ $type ]['title'];
                                $media = $standard_tips[ $type ]['media'];
                                $text  = esc_html( $standard_tips[ $type ]['text'] );
                            } else {
                                $title = esc_html( $tip['custom_tip_title'] );
                                $media = '';
                                $text  = nl2br( esc_html( $tip['custom_tip_content'] ) );
                            }
                        ?>
                        <li class="dp-help-item">
                            <?php if ( $media ) : ?>
                            <div class="dp-help-item__media">
                                <?php echo $media; // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
                            <?php endif; ?>
                            <div class="dp-help-item__content">
                                <h4><?php echo $title; ?></h4>
                                <p><?php echo $text; ?></p>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <?php if ( $has_common ) : ?>
                <div class="dp-help-common swell-block-accordion">
                    <div class="swell-block-accordion__head">
                        <span>その他の使い方・トラブルシューティングを見る</span>
                    </div>
                    <div class="swell-block-accordion__body">
                        <?php echo do_shortcode( '[swell_bp id="' . DP_COMMON_GUIDE_BP_ID . '"]' ); ?>
                    </div>
                </div>
                <?php endif; ?>

            </div><!-- /.dp-help-area -->
            <?php endif; ?>

        </article>

        <!-- ===== ナビゲーション ===== -->
        <nav class="dp-single__nav">
            <?php
        $prev = get_previous_post();
        $next = get_next_post();
        ?>
            <div class="dp-nav-inner">
                <?php if ( $prev ) : ?>
                <a href="<?php echo esc_url( get_permalink( $prev->ID ) ); ?>" class="dp-nav-btn dp-nav-btn--prev">
                    <span class="dp-nav-dir">← 前のパターン</span>
                    <span class="dp-nav-title"><?php echo esc_html( get_the_title( $prev->ID ) ); ?></span>
                </a>
                <?php endif; ?>
                <a href="<?php echo esc_url( home_url( '/design-patterns-standard/' ) ); ?>"
                    class="dp-nav-btn dp-nav-btn--archive">
                    一覧に戻る
                </a>
                <?php if ( $next ) : ?>
                <a href="<?php echo esc_url( get_permalink( $next->ID ) ); ?>" class="dp-nav-btn dp-nav-btn--next">
                    <span class="dp-nav-dir">次のパターン →</span>
                    <span class="dp-nav-title"><?php echo esc_html( get_the_title( $next->ID ) ); ?></span>
                </a>
                <?php endif; ?>
            </div>
        </nav>

        <!-- ===== タクソノミーフッターナビ ===== -->
        <div class="pl-wrap">
            <?php include get_stylesheet_directory() . '/inc/dp-taxonomy-footer.php'; ?>
        </div>

    </div><!-- /.l-article__body -->
</main>

<?php
endwhile;
get_sidebar();
get_footer();
?>