<?php
/**
 * Astra Child Theme — CloudCraft
 * functions.php
 */

// ── 0. Enqueue parent + child stylesheets ────────────────────────────────────
add_action( 'wp_enqueue_scripts', 'cloudcraft_enqueue_styles' );
function cloudcraft_enqueue_styles() {
    wp_enqueue_style( 'astra-parent-style', get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'astra-child-style', get_stylesheet_uri(), array( 'astra-parent-style' ) );
}


// ── 1. Hide empty tag / category sections ────────────────────────────────────
add_filter( 'the_tags', 'cloudcraft_hide_empty_tags', 10, 3 );
function cloudcraft_hide_empty_tags( $tag_list, $before, $sep ) {
    return get_the_tags() ? $tag_list : '';
}

add_filter( 'the_category', 'cloudcraft_hide_empty_categories', 10, 3 );
function cloudcraft_hide_empty_categories( $thelist, $separator, $parents ) {
    return get_the_category() ? $thelist : '';
}


// ── 2. Fallback featured image ───────────────────────────────────────────────
// Chain: featured image → first content image → category default → site logo → site icon

function cloudcraft_get_first_content_image( $post_id ) {
    $content = get_post_field( 'post_content', $post_id );
    if ( preg_match( '/<img[^>]+src=[\'"]([^\'"]+)[\'"][^>]*/i', $content, $m ) ) {
        return esc_url( $m[1] );
    }
    return '';
}

add_filter( 'post_thumbnail_html', 'cloudcraft_fallback_featured_image', 10, 5 );
function cloudcraft_fallback_featured_image( $html, $post_id, $post_thumbnail_id, $size, $attr ) {
    if ( ! empty( $html ) ) {
        return $html;
    }

    $fallback_url = '';

    // First image in post content
    $fallback_url = cloudcraft_get_first_content_image( $post_id );

    // Category default image
    if ( empty( $fallback_url ) ) {
        foreach ( get_the_category( $post_id ) as $cat ) {
            $cat_image_id = get_term_meta( $cat->term_id, 'cloudcraft_cat_image_id', true );
            if ( $cat_image_id ) {
                $src = wp_get_attachment_image_url( $cat_image_id, $size );
                if ( $src ) {
                    $fallback_url = $src;
                    break;
                }
            }
        }
    }

    // Site logo
    if ( empty( $fallback_url ) ) {
        $logo_id = get_theme_mod( 'custom_logo' );
        if ( $logo_id ) {
            $src = wp_get_attachment_image_url( $logo_id, $size );
            if ( $src ) {
                $fallback_url = $src;
            }
        }
    }

    // Site icon
    if ( empty( $fallback_url ) ) {
        $fallback_url = get_site_icon_url( 512 ) ?: '';
    }

    if ( empty( $fallback_url ) ) {
        return $html;
    }

    $alt   = isset( $attr['alt'] ) ? esc_attr( $attr['alt'] ) : esc_attr( get_the_title( $post_id ) );
    $class = isset( $attr['class'] )
        ? esc_attr( $attr['class'] )
        : 'attachment-' . esc_attr( $size ) . ' size-' . esc_attr( $size ) . ' wp-post-image';

    return '<img src="' . esc_url( $fallback_url ) . '" alt="' . $alt . '" class="' . $class . ' cloudcraft-fallback-thumb" />';
}


// ── 2b. Admin UI — Category Default Image field ──────────────────────────────
add_action( 'category_add_form_fields',  'cloudcraft_cat_image_field_add' );
add_action( 'category_edit_form_fields', 'cloudcraft_cat_image_field_edit' );
add_action( 'created_category',          'cloudcraft_save_cat_image' );
add_action( 'edited_category',           'cloudcraft_save_cat_image' );

function cloudcraft_cat_image_field_add( $taxonomy ) {
    wp_nonce_field( 'cloudcraft_cat_image', 'cloudcraft_cat_nonce' );
    ?>
    <div class="form-field">
        <label><?php esc_html_e( 'Default Featured Image', 'astra-child' ); ?></label>
        <input type="hidden" id="cloudcraft_cat_image_id" name="cloudcraft_cat_image_id" value="" />
        <div id="cloudcraft-cat-img-preview" style="margin-bottom:8px;"></div>
        <button type="button" class="button" id="cloudcraft-cat-img-btn">
            <?php esc_html_e( 'Upload / Choose Image', 'astra-child' ); ?>
        </button>
        <p class="description"><?php esc_html_e( 'Shown as featured image for posts with no image set.', 'astra-child' ); ?></p>
    </div>
    <?php
    cloudcraft_cat_image_admin_script();
}

function cloudcraft_cat_image_field_edit( $term ) {
    $image_id = get_term_meta( $term->term_id, 'cloudcraft_cat_image_id', true );
    $preview  = $image_id ? wp_get_attachment_image_url( $image_id, 'thumbnail' ) : '';
    wp_nonce_field( 'cloudcraft_cat_image', 'cloudcraft_cat_nonce' );
    ?>
    <tr class="form-field">
        <th scope="row"><label><?php esc_html_e( 'Default Featured Image', 'astra-child' ); ?></label></th>
        <td>
            <input type="hidden" id="cloudcraft_cat_image_id" name="cloudcraft_cat_image_id" value="<?php echo esc_attr( $image_id ); ?>" />
            <div id="cloudcraft-cat-img-preview" style="margin-bottom:8px;">
                <?php if ( $preview ) : ?><img src="<?php echo esc_url( $preview ); ?>" style="max-width:150px;" /><?php endif; ?>
            </div>
            <button type="button" class="button" id="cloudcraft-cat-img-btn">
                <?php esc_html_e( 'Upload / Choose Image', 'astra-child' ); ?>
            </button>
            <p class="description"><?php esc_html_e( 'Shown as featured image for posts with no image set.', 'astra-child' ); ?></p>
        </td>
    </tr>
    <?php
    cloudcraft_cat_image_admin_script();
}

function cloudcraft_save_cat_image( $term_id ) {
    if ( ! isset( $_POST['cloudcraft_cat_nonce'] )
        || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['cloudcraft_cat_nonce'] ) ), 'cloudcraft_cat_image' )
        || ! current_user_can( 'manage_categories' )
    ) {
        return;
    }

    $image_id = isset( $_POST['cloudcraft_cat_image_id'] ) ? absint( $_POST['cloudcraft_cat_image_id'] ) : 0;
    if ( $image_id ) {
        update_term_meta( $term_id, 'cloudcraft_cat_image_id', $image_id );
    } else {
        delete_term_meta( $term_id, 'cloudcraft_cat_image_id' );
    }
}

function cloudcraft_cat_image_admin_script() {
    wp_enqueue_media();
    ?>
    <script>
    (function($){
        $('#cloudcraft-cat-img-btn').on('click', function(e){
            e.preventDefault();
            var frame = wp.media({ title: 'Choose Category Image', button: { text: 'Use this image' }, multiple: false });
            frame.on('select', function(){
                var attachment = frame.state().get('selection').first().toJSON();
                $('#cloudcraft_cat_image_id').val(attachment.id);
                var preview = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;
                $('#cloudcraft-cat-img-preview').html('<img src="'+preview+'" style="max-width:150px;" />');
            });
            frame.open();
        });
    })(jQuery);
    </script>
    <?php
}


// ── 3. Top Categories Widget ─────────────────────────────────────────────────
class CloudCraft_Top_Categories_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'cloudcraft_top_categories',
            __( 'CloudCraft: Top Categories', 'astra-child' ),
            array( 'description' => __( 'Shows categories sorted by post count.', 'astra-child' ) )
        );
    }

    public function widget( $args, $instance ) {
        $title      = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Top Categories', 'astra-child' );
        $number     = ! empty( $instance['number'] ) ? absint( $instance['number'] ) : 10;
        $show_count = ! empty( $instance['show_count'] );

        echo $args['before_widget'];
        echo $args['before_title'] . apply_filters( 'widget_title', $title ) . $args['after_title'];

        $categories = get_categories( array(
            'orderby'    => 'count',
            'order'      => 'DESC',
            'number'     => $number,
            'hide_empty' => true,
        ) );

        if ( ! empty( $categories ) ) {
            echo '<ul class="cloudcraft-top-cats">';
            foreach ( $categories as $cat ) {
                $count_label = $show_count ? ' <span class="cat-count">(' . $cat->count . ')</span>' : '';
                echo '<li><a href="' . esc_url( get_category_link( $cat->term_id ) ) . '">'
                    . esc_html( $cat->name ) . $count_label . '</a></li>';
            }
            echo '</ul>';
        } else {
            echo '<p>' . esc_html__( 'No categories found.', 'astra-child' ) . '</p>';
        }

        echo $args['after_widget'];
    }

    public function form( $instance ) {
        $title      = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Top Categories', 'astra-child' );
        $number     = ! empty( $instance['number'] ) ? absint( $instance['number'] ) : 10;
        $show_count = ! empty( $instance['show_count'] );
        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title:', 'astra-child' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php esc_html_e( 'Number of categories:', 'astra-child' ); ?></label>
            <input class="tiny-text" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="number" step="1" min="1" value="<?php echo esc_attr( $number ); ?>" size="3" />
        </p>
        <p>
            <input class="checkbox" type="checkbox" <?php checked( $show_count ); ?> id="<?php echo $this->get_field_id( 'show_count' ); ?>" name="<?php echo $this->get_field_name( 'show_count' ); ?>" />
            <label for="<?php echo $this->get_field_id( 'show_count' ); ?>"><?php esc_html_e( 'Show post count', 'astra-child' ); ?></label>
        </p>
        <?php
    }

    public function update( $new_instance, $old_instance ) {
        return array(
            'title'      => sanitize_text_field( $new_instance['title'] ),
            'number'     => absint( $new_instance['number'] ),
            'show_count' => ! empty( $new_instance['show_count'] ) ? 1 : 0,
        );
    }
}

add_action( 'widgets_init', function() {
    register_widget( 'CloudCraft_Top_Categories_Widget' );
} );


// ── 3b. Shortcode: [cloudcraft_top_categories number="10" show_count="1"] ────
add_shortcode( 'cloudcraft_top_categories', 'cloudcraft_top_cats_shortcode' );
function cloudcraft_top_cats_shortcode( $atts ) {
    $atts = shortcode_atts( array(
        'number'     => 10,
        'show_count' => 1,
        'title'      => '',
    ), $atts, 'cloudcraft_top_categories' );

    $categories = get_categories( array(
        'orderby'    => 'count',
        'order'      => 'DESC',
        'number'     => absint( $atts['number'] ),
        'hide_empty' => true,
    ) );

    if ( empty( $categories ) ) {
        return '';
    }

    ob_start();
    if ( $atts['title'] ) {
        echo '<h3 class="cloudcraft-top-cats-title">' . esc_html( $atts['title'] ) . '</h3>';
    }
    echo '<ul class="cloudcraft-top-cats">';
    foreach ( $categories as $cat ) {
        $count_label = $atts['show_count'] ? ' <span class="cat-count">(' . $cat->count . ')</span>' : '';
        echo '<li><a href="' . esc_url( get_category_link( $cat->term_id ) ) . '">'
            . esc_html( $cat->name ) . $count_label . '</a></li>';
    }
    echo '</ul>';
    return ob_get_clean();
}
