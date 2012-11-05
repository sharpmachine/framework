<?php
	$temp = $wp_query;
	$wp_query = null;
	$wp_query = new WP_Query();
	$wp_query->query('&paged='.$paged);
	while ($wp_query->have_posts()) : $wp_query->the_post();
?>

 <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

		<h2 class="entry-title">
			<a href="<?php the_permalink(); ?>"><?php the_title();  ?></a>
		</h2>
		<div><?php smm_posted_on(); ?></div>
		<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( array (100, 100) ); ?></a>

		<?php the_excerpt(); ?>

		<div class="entry-utility">
		<?php if ( count( get_the_category() ) ) : ?>
			<span class="cat-links">
				<?php printf( __( '<span class="%1$s">Posted in</span> %2$s', 'smm' ), 'entry-utility-prep entry-utility-prep-cat-links', get_the_category_list( ', ' ) ); ?>
			</span>
			<span class="meta-sep">|</span>
		<?php endif; ?>
		<?php
			$tags_list = get_the_tag_list( '', ', ' );
			if ( $tags_list ):
		?>
			<span class="tag-links">
				<?php printf( __( '<span class="%1$s">Tagged</span> %2$s', 'smm' ), 'entry-utility-prep entry-utility-prep-tag-links', $tags_list ); ?>
			</span>
			<span class="meta-sep">|</span>
		<?php endif; ?>
			<span class="comments-link"><?php comments_popup_link( __( 'Leave a comment', 'smm' ), __( '1 Comment', 'smm' ), __( '% Comments', 'smm' ) ); ?></span>
			<?php edit_post_link( __( 'Edit', 'smm' ), '<span class="meta-sep">|</span> <span class="edit-link">', '</span>' ); ?>
		</div><!-- .entry-utility -->
	</article><!-- #post -->

	<?php endwhile; ?>

<?php bootstrap_pagination(); ?>

<?php $wp_query = null; $wp_query = $temp;?>