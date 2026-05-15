<?php
/**
 * Astra Child Theme — CloudCraft
 * functions.php
 *
 * Features included:
 *   1. Hide post tags when the post has none (PHP approach — reliable)
 *   2. Fallback featured image: category default → site logo
 *   3. Top Categories by post count widget
 */

// ─────────────────────────────────────────────────────────────────────────────
// 0. Enqueue parent + child stylesheets
// ─────────────────────────────────────────────────────────────────────────────
add_action( 'wp_enqueue_scripts', 'cloudcraft_enqueue_styles' );
function cloudcraft_enqueue_styles() {
    wp_enqueue_style(
        'astra-parent-style',
        get_template_directory_uri() . '/style.css'
    );
    wp_enqueue_style(
        'astra-child-style',
        get_stylesheet_uri(),
        array( 'astra-parent-style' )
    );
}


// ─────────────────────────────────────────────────────────────────────────────
// 1. Hide tags section when a post has no tags
//
//    Astra renders tags via the_tags(). We filter 'the_tags' so that when
//    a post has no tags the entire tag HTML (including label/wrapper) returns
//    an empty string instead of a bare label like "Tags:".
// ─────────────────────────────────────────────────────────────────────────────
add_filter( 'the_tags', 'cloudcraft_hide_empty_tags', 10, 3 );
function cloudcraft_hide_empty_tags( $tag_list, $before, $sep ) {
    // get_the_tags() returns false when the post has no tags
    if ( ! get_the_tags() ) {
        return '';
    }
    return $tag_list;
}


// ─────────────────────────────────────────────────────────────────────────────
// 2. Fallback featured image
//
//    Priority order:
//      a) Post's own featured image  →  shown as-is (WordPress default)
//      b) Category default image     →  set via Appearance → Customize
//         (stored as theme_mod 'cloudcraft_cat_{term_id}_image')
//      c) Site logo                  →  get_custom_logo() / site icon
//
//    We hook into 'post_thumbnail_html'. When it is empty (no featured image)
//    we build the fallback <img> tag ourselves.
// ─────────────────────────────────────────────────────────────────────────────
add_filter( 'post_thumbnail_html', 'cloudcraft_fallback_featured_image', 10, 5 );
function cloudcraft_fallback_featured_image( $html, $post_id, $post_thumbnail_id, $size, $attr ) {

    // If the post already has a featured image, do nothing.
    if ( ! empty( $html ) ) {
        return $html;
    }

    $fallback_url = '';

    // ── (b) Check for a category-specific default image ──────────────────────
    $categories = get_the_category( $post_id );
    if ( ! empty( $categories ) ) {
        foreach ( $categories as $cat ) {
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

    // ── (c) Fall back to the site logo ───────────────────────────────────────
    if ( empty( $fallback_url ) ) {
        $logo_id = get_theme_mod( 'custom_logo' );
        if ( $logo_id ) {
            $src = wp_get_attachment_image_url( $logo_id, $size );
            if ( $src ) {
                $fallback_url = $src;
            }
        }
    }

    // ── (c-alt) Try site icon (favicon) if logo is also missing ──────────────
    if ( empty( $fallback_url ) ) {
        $icon_url = get_site_icon_url( 512 );
        if ( $icon_url ) {
            $fallback_url = $icon_url;
        }
    }

    // If we have a fallback URL, build the <img> tag
    if ( ! empty( $fallback_url ) ) {
        $alt  = isset( $attr['alt'] ) ? esc_attr( $attr['alt'] ) : esc_attr( get_the_title( $post_id ) );
        $class = isset( $attr['class'] ) ? esc_attr( $attr['class'] ) : 'attachment-' . esc_attr( $size ) . ' size-' . esc_attr( $size ) . ' wp-post-image';
        return '<img src="' . esc_url( $fallback_url ) . '" alt="' . $alt . '" class="' . $class . ' cloudcraft-fallback-thumb" />';
    }

    return $html;
}


// ─────────────────────────────────────────────────────────────────────────────
// 2b. (Optional) Admin UI — add "Category Default Image" field to
//     Edit Category screen so editors can set per-category fallback images
//     without touching code.
// ─────────────────────────────────────────────────────────────────────────────
add_action( 'category_add_form_fields',  'cloudcraft_cat_image_field_add' );
add_action( 'category_edit_form_fields', 'cloudcraft_cat_image_field_edit' );
add_action( 'created_category',          'cloudcraft_save_cat_image' );
add_action( 'edited_category',           'cloudcraft_save_cat_image' );

function cloudcraft_cat_image_field_add( $taxonomy ) {
    ?>
    <div class="form-field">
        <label><?php esc_html_e( 'Default Featured Image', 'astra-child' ); ?></label>
        <input type="hidden" id="cloudcraft_cat_image_id" name="cloudcraft_cat_image_id" value="" />
        <div id="cloudcraft-cat-img-preview" style="margin-bottom:8px;"></div>
        <button type="button" class="button" id="cloudcraft-cat-img-btn">
            <?php esc_html_e( 'Upload / Choose Image', 'astra-child' ); ?>
        </button>
        <p class="description"><?php esc_html_e( 'Shown as featured image for posts in this category that have no image set.', 'astra-child' ); ?></p>
    </div>
    <?php
    cloudcraft_cat_image_admin_script();
}

function cloudcraft_cat_image_field_edit( $term ) {
    $image_id = get_term_meta( $term->term_id, 'cloudcraft_cat_image_id', true );
    $preview  = $image_id ? wp_get_attachment_image_url( $image_id, 'thumbnail' ) : '';
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
            <p class="description"><?php esc_html_e( 'Shown as featured image for posts in this category that have no image set.', 'astra-child' ); ?></p>
        </td>
    </tr>
    <?php
    cloudcraft_cat_image_admin_script();
}

function cloudcraft_save_cat_image( $term_id ) {
    if ( isset( $_POST['cloudcraft_cat_image_id'] ) ) {
        update_term_meta( $term_id, 'cloudcraft_cat_image_id', absint( $_POST['cloudcraft_cat_image_id'] ) );
    }
}

function cloudcraft_cat_image_admin_script() {
    // Enqueue WP media uploader
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


// ─────────────────────────────────────────────────────────────────────────────
// 3. Top Categories Widget — shows categories sorted by post count
//
//    Register a widget so you can drag it into any sidebar via
//    Appearance → Widgets.
//
//    Alternatively use the shortcode: [cloudcraft_top_categories]
// ─────────────────────────────────────────────────────────────────────────────

class CloudCraft_Top_Categories_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'cloudcraft_top_categories',
            __( 'CloudCraft: Top Categories', 'astra-child' ),
            array( 'description' => __( 'Shows categories sorted by post count.', 'astra-child' ) )
        );
    }

    /** Front-end display */
    public function widget( $args, $instance ) {
        $title  = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Top Categories', 'astra-child' );
        $number = ! empty( $instance['number'] ) ? absint( $instance['number'] ) : 10;
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

    /** Back-end widget form */
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
            <label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php esc_html_e( 'Number of categories to show:', 'astra-child' ); ?></label>
            <input class="tiny-text" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="number" step="1" min="1" value="<?php echo esc_attr( $number ); ?>" size="3" />
        </p>
        <p>
            <input class="checkbox" type="checkbox" <?php checked( $show_count ); ?> id="<?php echo $this->get_field_id( 'show_count' ); ?>" name="<?php echo $this->get_field_name( 'show_count' ); ?>" />
            <label for="<?php echo $this->get_field_id( 'show_count' ); ?>"><?php esc_html_e( 'Show post count', 'astra-child' ); ?></label>
        </p>
        <?php
    }

    /** Sanitize widget form values on save */
    public function update( $new_instance, $old_instance ) {
        $instance               = array();
        $instance['title']      = sanitize_text_field( $new_instance['title'] );
        $instance['number']     = absint( $new_instance['number'] );
        $instance['show_count'] = ! empty( $new_instance['show_count'] ) ? 1 : 0;
        return $instance;
    }
}

add_action( 'widgets_init', function() {
    register_widget( 'CloudCraft_Top_Categories_Widget' );
} );


// ─────────────────────────────────────────────────────────────────────────────
// 3b. Shortcode version: [cloudcraft_top_categories number="10" show_count="1"]
//     Use this inside any page/post via the block editor or classic editor.
// ─────────────────────────────────────────────────────────────────────────────
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
