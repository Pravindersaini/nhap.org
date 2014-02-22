<?php

the_post();

/**



 * Default Community Template

 * Created by Community Support Staff 12/12/2013



 * This file is the basic wrapper template for all the views if 'Default Events Template' 



 * is selected in Events -> Settings -> Template -> Events Template.



 * 



 * Override this template in your own theme by creating a file at [your-theme]/tribe-events/default-template.php



 *



 * @package TribeEventsCalendar



 * @since  3.0



 * @author Modern Tribe Inc.



 *



 */







if ( !defined('ABSPATH') ) { die('-1'); }







get_header();



 ?>







	<div class="content">


		<?php the_content(); ?>



	</div><!-- .content -->



	



<aside id="sidebar">



	<?php dynamic_sidebar('The BUZZ'); ?>



</aside>







<?php get_footer(); ?>