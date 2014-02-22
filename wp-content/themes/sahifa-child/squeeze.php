<?php
/*
Template Name: Squeeze Page
*/
?>

<?php get_header(); ?>

<?php if ( ! have_posts() ) : ?>
	<div id="post-0" class="post not-found post-listing">
		<h1 class="post-title"><?php _e( 'Not Found', 'tie' ); ?></h1>
		<div class="entry">
			<p><?php _e( 'Apologies, but no results were found for the requested archive. Perhaps searching will help find a related post.', 'tie' ); ?></p>
			<?php get_search_form(); ?>
		</div>
	</div>
<?php endif; ?>

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
	<div class="entry">
		<?php the_content(); ?>
	</div><!-- .entry /-->
<?php endwhile; ?>

