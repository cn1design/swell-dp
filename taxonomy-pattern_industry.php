<?php
/**
 * Taxonomy archive template: pattern_industry
 */

get_header();

$term = get_queried_object();
$slug = ( $term instanceof WP_Term ) ? $term->slug : '';
?>

<main id="primary" class="site-main">
	<div class="l-container">
		<header class="archive-header">
			<h1 class="archive-title">
				<?php single_term_title(); ?>
			</h1>
			<?php the_archive_description( '<div class="archive-description">', '</div>' ); ?>
		</header>

		<?php
		echo do_shortcode(
			$slug
				? sprintf( '[pattern_list industry="%s"]', esc_attr( $slug ) )
				: '[pattern_list]'
		);
		?>
	</div>
</main>

<?php
get_footer();

