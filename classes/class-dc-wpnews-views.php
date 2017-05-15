<?php
class Dc_wpnews_views {

    public function __construct() {
		add_shortcode('dc-wpnews', array($this, 'create_views'));
		add_action( 'wp_ajax_get_posts_ajax', array( $this, 'get_posts_ajax' ) );
		add_action( 'wp_ajax_nopriv_get_posts_ajax', array( $this, 'get_posts_ajax' ) );
	}

	/**
	 * Called by the main ajax request.
     * prints item html.
	 */
	public function get_posts_ajax () {
		$post_type      = $_POST['post_type'];
        $post_count     = $_POST['post_count'];
        $page_offset    = $_POST['page_offset'];

		$the_query      = $this->get_posts_array($post_type, $post_count, $page_offset);

		if ( $the_query->have_posts() ) :
			while ( $the_query->have_posts() ) : $the_query->the_post();
        ?>
			<div class="small-12 medium-6 columns itembox" data-totalitems="<?php echo $the_query->found_posts; ?>">
				<div>

                    <?php if (get_the_post_thumbnail()): ?>
                        <a href="<?php the_permalink(); ?>" title="Go to <?php the_title(); ?>" class="not-text-link thumbnail"><?php the_post_thumbnail('news-thumb'); ?></a>
                    <?php else: ?>
                        <a href="<?php the_permalink(); ?>" title="Go to <?php the_title(); ?>" class="not-text-link thumbnail"><img src="http://placehold.it/558x310"></a>
                    <?php endif; ?>


					<div class="news-intro">
						<time datetime="<?php echo get_the_date('d.m.Y'); ?>"><?php echo get_the_date('d.m.Y'); ?></time>

						<a href="<?php the_permalink(); ?>" title="Go to <?php the_title(); ?>" class="not-text-link"><h3><?php the_title(); ?></h3></a>
						<?php the_excerpt(); ?>

                        <div class="addthis_toolbox">
                            <div class="custom_images">
                                <a class="addthis_button_facebook not-text-link"><i class="icon-facebook"></i></a>
                                <a class="addthis_button_linkedin not-text-link"><i class="icon-linkedin"></i></a>
                            </div>
                        </div>

					</div>
				</div>
			</div>
		<?php
            endwhile;
			wp_reset_postdata();
        else:
            echo '<p sttyle="text-align:center">No posts available.</p>';
		endif;

		wp_reset_postdata();
		wp_die();
	}

	/**
	 * @param $post_type
	 * @param $post_count
	 * @param $page_offset
	 *
	 * @return WP_Query
	 */
	public function get_posts_array ($post_type, $post_count, $page_offset) {
	    $args = array(
			'posts_per_page'   => $post_count,
			'offset'           => $page_offset,
			'orderby'          => 'date',
			'order'            => 'DESC',
			'post_type'        => $post_type,
			'post_status'      => 'publish',
		    'paged'            => $paged
		);
	    $the_query = new WP_Query($args);
		return $the_query;
	}

	/**
     * create main views for items. shortcode arguments are extracted here.
	 * @param $atts
	 */
	public function create_views ($atts) {

	    extract(shortcode_atts(array(
			'post_type'     => 'post',
			'post_count'    => '6',
            'loadmore_text' => 'Load more'
		), $atts));

	    echo '<div class="row itembox-container ajax-post-container-'.$post_type.'"></div>';

	    echo '<div class="row loadmore-container loadmore-container-'.$post_type.'">
	            <a data-offset="0" href="#" class="button primary loadmore-button" title="Watch video now"><span class="loader"></span>'.$loadmore_text.'</a>
	          </div>';
		?>

        <script>
            var $result_holder      = jQuery('.ajax-post-container-<?php echo $post_type; ?>'),
                $loadmore_container = jQuery('.loadmore-container-<?php echo $post_type; ?>'),
                $loadmore           = $loadmore_container.find('.loadmore-button'),
                $loader             = $loadmore_container.find('.loader');

			jQuery(document).ready(function($) {

                var post_type       = '<?php echo $post_type; ?>';
                var post_count      = '<?php echo $post_count; ?>';
                var page_offset     = 0;

                // showing data when page loads
			    get_news( post_type, post_count, page_offset, $result_holder );

			    // click load more button
                $loadmore.on('click', function (e) {
                    e.preventDefault();
                    page_offset = $(this).attr('data-offset');
                    get_news( post_type, post_count, page_offset, $result_holder );
                });
			});

            /**
             * Fetch data by ajax. Called initially when page loads and also called when clicked loadmore.
             * Appends HTML data in result_holder.
             * @param post_type
             * @param post_count
             * @param page_offset
             * @param $result_holder
             */
			function get_news( post_type, post_count, page_offset, $result_holder ) {

                var mydata = {
                    action: "get_posts_ajax",
                    post_type: post_type,
                    post_count: post_count,
                    page_offset: page_offset
                };

                $.ajax({
                    async: true,
                    type: 'POST',
                    url: 'http://decisivedc.wpengine.com/wp-admin/admin-ajax.php',
                    dataType: 'html',
                    data: mydata,
                    beforeSend: function(){
                        $loader.fadeIn();
                        $loadmore.addClass('showing-loader');
                    },
                    error: function(){
                        $loader.html('Something went wrong!');
                        $loadmore.removeClass('showing-loader');
                    },
                    success: function(result){
                        $loader.fadeOut();
                        $result_holder.append(result);
                        $loadmore.removeClass('showing-loader');

                        var new_offset = <?php echo $post_count; ?> + parseInt(page_offset),
                            total_items = $('.itembox').attr('data-totalitems');
                        console.log(total_items);
                        if (new_offset < total_items)
                            $loadmore.attr('data-offset', new_offset);
                        else
                            $loadmore.hide();

                        addthis.toolbox('.addthis_toolbox');
                    }
                });
			}
		</script>
		<?php
	}
}
?>