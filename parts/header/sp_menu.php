<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$spmenu_class    = ( 'center_left' === SWELL_Theme::get_setting( 'header_layout_sp' ) ) ? '-left' : '-right';
$dp_spmenu_style = get_theme_mod( 'dp_spmenu_style', 'none' );
$is_dp_spmenu    = in_array( $dp_spmenu_style, [ 'a', 'b' ], true );
?>
<div id="sp_menu" class="p-spMenu <?=esc_attr( $spmenu_class )?>">
	<div class="p-spMenu__inner">
		<?php if ( $dp_spmenu_style === 'a' ) : ?>
		<div class="dp-spmenu-deco" aria-hidden="true"></div>
		<?php endif; ?>
		<div class="p-spMenu__closeBtn">
			<button class="c-iconBtn -menuBtn c-plainBtn" data-onclick="toggleMenu" aria-label="<?=esc_attr__( 'メニューを閉じる', 'swell' )?>">
				<i class="c-iconBtn__icon icon-close-thin"></i>
			</button>
		</div>
		<div class="p-spMenu__body">
			<div class="c-widget__title -spmenu">
				<?php if ( $is_dp_spmenu ) : ?>
					<?php if ( has_custom_logo() ) : ?>
						<a href="<?=esc_url( home_url( '/' ) )?>" class="dp-spmenu-logo">
							<?=wp_get_attachment_image( get_theme_mod( 'custom_logo' ), 'full' )?>
						</a>
					<?php else : ?>
						<a href="<?=esc_url( home_url( '/' ) )?>" class="dp-spmenu-logo">
							<?=esc_html( get_bloginfo( 'name' ) )?>
						</a>
					<?php endif; ?>
				<?php else : ?>
					<?=wp_kses( SWELL_Theme::get_setting( 'spmenu_main_title' ), SWELL_Theme::$allowed_text_html )?>
				<?php endif; ?>
			</div>
			<div class="p-spMenu__nav">
				<?php
					if ( has_nav_menu( 'nav_sp_menu' ) ) :
						wp_nav_menu([
							'container'      => '',
							'fallback_cb'    => '',
							'theme_location' => 'nav_sp_menu',
							'items_wrap'     => '<ul class="c-spnav c-listMenu">%3$s</ul>',
						]);
					else :
						wp_nav_menu([
							'fallback_cb'    => '__return_false',
							'container'      => '',
							'theme_location' => 'header_menu',
							'items_wrap'     => '<ul class="c-spnav c-listMenu">%3$s</ul>',
						]);
					endif;
				?>
			</div>
			<?php
				\SWELL_Theme::outuput_widgets( 'sp_menu_bottom', [
					'before' => '<div id="sp_menu_bottom" class="p-spMenu__bottom w-spMenuBottom">',
					'after'  => '</div>',
				] );
			?>
		</div>
	</div>
	<div class="p-spMenu__overlay c-overlay" data-onclick="toggleMenu"></div>
</div>
