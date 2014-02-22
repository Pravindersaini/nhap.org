<?php get_header(); ?>
	<div class="content">
			
		<div class="post-listing post error404">
			<div class="post-inner">
				<h2 class="post-title"><?php _e( 'Not Found', 'tie' ); ?></h2>
				<div class="clear"></div>
				<div class="entry">
					<p><?php _e( 'Apologies, but the page you requested could not be found. Perhaps the search box at the top of the page could help.', 'tie' ); ?></p>

					<div id="sitemap">
							
						<div class="sitemap-col">
							<h2><?php _e('Categories','tie'); ?></h2>
							<ul id="sitemap-categories"><?php wp_list_categories('exclude=1,50&title_li='); ?></ul>
						</div> <!-- end .sitemap-col -->
																				
					</div> <!-- end #sitemap -->
						
				</div><!-- .entry /-->	
			
			</div><!-- .post-inner -->
		</div><!-- .post-listing -->
	</div>
<?php get_footer(); ?>