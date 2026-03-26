<?php
/**
 * デザインパターン — タクソノミーフッターナビ（共通パーシャル）
 * archive-design_pattern.php / single-design_pattern.php / page-design_pattern_standard.php で使用
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$dp_archive_url = get_post_type_archive_link( 'design_pattern' );

$dp_lp_pages = get_pages([
    'meta_key'   => '_wp_page_template',
    'meta_value' => 'page-design_pattern_standard.php',
    'number'     => 1,
]);
$dp_lp_url = ! empty( $dp_lp_pages ) ? get_permalink( $dp_lp_pages[0] ) : '';

$dp_footer_sections = get_terms([
    'taxonomy'   => 'pattern_section',
    'hide_empty' => false,
]);

$dp_footer_industries = get_terms([
    'taxonomy'   => 'pattern_industry',
    'hide_empty' => false,
]);

$dp_has_sections   = ! is_wp_error( $dp_footer_sections ) && ! empty( $dp_footer_sections );
$dp_has_industries = ! is_wp_error( $dp_footer_industries ) && ! empty( $dp_footer_industries );

if ( $dp_has_sections || $dp_has_industries || $dp_lp_url ) :
?>
<div class="pl-tax-footer">

    <?php if ( $dp_has_sections ) : ?>
    <div class="pl-tax-footer__group">
        <span class="pl-tax-footer__label">パターン名</span>
        <div class="pl-tax-footer__links">
            <?php foreach ( $dp_footer_sections as $term ) : ?>
            <a class="pl-tax-footer__link"
                href="<?php echo esc_url( add_query_arg( 'section', $term->slug, $dp_archive_url ) ); ?>">
                <?php echo esc_html( $term->name ); ?>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if ( $dp_has_industries ) : ?>
    <div class="pl-tax-footer__group">
        <span class="pl-tax-footer__label">ジャンル</span>
        <div class="pl-tax-footer__links">
            <?php foreach ( $dp_footer_industries as $term ) : ?>
            <a class="pl-tax-footer__link"
                href="<?php echo esc_url( add_query_arg( 'industry', $term->slug, $dp_archive_url ) ); ?>">
                <?php echo esc_html( $term->name ); ?>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <?php
    $dp_is_lp_page = is_page_template( 'page-design_pattern_standard.php' );
    if ( $dp_is_lp_page && $dp_archive_url ) : ?>
    <div class="pl-tax-footer__group">
        <span class="pl-tax-footer__label">別の探し方</span>
        <div class="pl-tax-footer__links">
            <a class="pl-tax-footer__link pl-tax-footer__link--cta" href="<?php echo esc_url( $dp_archive_url ); ?>">
                パーツごとに探す（絞り込み） →
            </a>
        </div>
    </div>
    <?php elseif ( ! $dp_is_lp_page && $dp_lp_url ) : ?>
    <div class="pl-tax-footer__group">
        <span class="pl-tax-footer__label">別の探し方</span>
        <div class="pl-tax-footer__links">
            <a class="pl-tax-footer__link pl-tax-footer__link--cta" href="<?php echo esc_url( $dp_lp_url ); ?>">
                よく使われるページ構成（LP構成順）で見る →
            </a>
        </div>
    </div>
    <?php endif; ?>

</div>
<?php endif; ?>