<?php 
/*
	Template Name: Blog
*/
get_header(); ?>

		<div id="content-container" class="span-20">
			<section id="content" role="main">

			<?php get_template_part( 'loop', 'index' ); ?>
			<?php rewind_posts(); ?>
			
			<?php
				$temp = $wp_query;
				$wp_query= null;
				$wp_query = new WP_Query();
				$wp_query->query('&paged='.$paged);
				while ($wp_query->have_posts()) : $wp_query->the_post();
			?>
			
			<div class="listing">
				<h3 class="entry-title">
					<a href="<?php the_permalink(); ?>"><?php the_title();  ?></a>
				</h3>
				<div><?php twentyten_posted_on(); ?></div>
				<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( array (100, 100) ); ?></a>
			<?php the_excerpt(); ?>
			</div>

			<?php endwhile; ?>
	
			<?php if (  $wp_query->max_num_pages > 1 ) : ?>
							<?php if(function_exists('wp_paginate')) {
			    wp_paginate();
			} ?>
			
			<?php endif; ?>

			<?php $wp_query = null; $wp_query = $temp;?>
			
			</section><!-- #content -->
		</div><!-- #content-container -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
