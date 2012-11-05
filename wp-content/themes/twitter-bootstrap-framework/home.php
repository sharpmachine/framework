<?php get_header(); ?>

				<section id="page" class="span8">
			
					<h1>Bootstrap starter theme</h1>
					<p>Use this theme as a way to quick start any new project.<br> All you get is this message and a barebones HTML document.</p>
			
					<?php query_posts('showposts=3'); ?>
					<?php get_template_part( 'loop', 'home' ); ?>

				</section><!-- #page -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
