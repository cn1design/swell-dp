<?php
if (!defined('ABSPATH')) exit;
?>


<div class="l-footer__inner">

    <?php SWELL_Theme::get_parts('parts/footer/foot_widget'); ?>

    <div class="l-footer__foot">
        <div class="l-container l-inner">

            <!-- ===== 左カラム: ロゴ・住所 ===== -->
            <div class="footer-address">
                <div class="footer-address__logo-wrap">
                    <!-- ロゴ（ヘッダーロゴ流用: SWELLカスタマイザー > ロゴ設定と共通） -->
                    <div class="footer-logo">
                        <?php echo SWELL_PARTS::head_logo(); ?>
                    </div>
                    <!-- SNS アイコン（LINEなど: SWELLカスタマイザーで設定） -->
                    <?php
                    if (SWELL_Theme::get_setting('show_foot_icon_list')) :
                        $sns_settings = SWELL_Theme::get_sns_settings();
                        if (!empty($sns_settings)) :
                            $list_data = [
                                'list_data' => $sns_settings,
                                'fz_class'  => 'u-fz-14',
                            ];
                            SWELL_Theme::get_parts('parts/icon_list', $list_data);
                        endif;
                    endif;
                    ?>
                </div>
                <!-- 住所・TEL/FAX: カスタマイザー > フッター設定（子テーマ）で設定 -->
                <?php
                $bp_address = (int) get_theme_mod( 'child_bp_footer_address', 0 );
                if ( $bp_address > 0 ) :
                    echo do_shortcode( '[blog_parts id="' . $bp_address . '"]' );
                endif;
                ?>
            </div>

            <!-- ===== 右カラム: ナビゲーション ===== -->
            <div class="footer-nav-wrap">

                <!-- 1段目: メインナビ -->
                <nav class="footer-first-nav">
                    <?php wp_nav_menu([
                        'container'      => false,
                        'fallback_cb'    => '',
                        'theme_location' => 'footer_menu',
                        'items_wrap'     => '<ul class="l-footer__nav">%3$s</ul>',
                        'link_before'    => '',
                        'link_after'     => '',
                    ]); ?>
                </nav>

                <!-- 2段目: サブナビ（重要事項説明書・プライバシーポリシー等） -->
                <!-- WP管理画面 > 外観 > メニュー で「フッター サブナビ」に割り当て -->
                <nav class="footer-secondary-nav">
                    <?php wp_nav_menu([
                        'container'      => false,
                        'fallback_cb'    => '',
                        'theme_location' => 'footer_secondary_menu',
                        'items_wrap'     => '<ul class="l-footer__nav-secondary">%3$s</ul>',
                        'link_before'    => '',
                        'link_after'     => '',
                    ]); ?>
                </nav>

            </div>

        </div>
    </div>

    <div class="copyright">
        <span lang="en">&copy;</span>
        <?= wp_kses(SWELL_Theme::get_setting('copyright'), SWELL_Theme::$allowed_text_html) ?>
    </div>
    <?php do_action('swell_after_copyright'); ?>
</div>