<?php
/**
 * Astra Child Theme — CloudCraft
 * functions.php
 */

// ── 5. Primary navigation menu setup (runs once via admin_init) ──────────────
add_action( 'init', 'cloudcraft_setup_primary_menu' );
function cloudcraft_setup_primary_menu() {
    if ( get_option( 'cloudcraft_menu_v2' ) ) {
        return;
    }

    $menu_name = 'CloudCraft Primary';
    $existing  = wp_get_nav_menu_object( $menu_name );
    $menu_id   = $existing ? $existing->term_id : wp_create_nav_menu( $menu_name );
    if ( is_wp_error( $menu_id ) ) {
        return;
    }

    // Wipe existing items so re-runs stay clean
    $old_items = wp_get_nav_menu_items( $menu_id );
    if ( $old_items ) {
        foreach ( $old_items as $item ) {
            wp_delete_post( $item->ID, true );
        }
    }

    // Home
    wp_update_nav_menu_item( $menu_id, 0, array(
        'menu-item-title'  => __( 'Home', 'astra-child' ),
        'menu-item-type'   => 'custom',
        'menu-item-url'    => home_url( '/' ),
        'menu-item-status' => 'publish',
    ) );

    // Category items with dropdowns for child categories
    $cat_items = array(
        'AI'     => 'AI',
        'Linux'  => 'Linux',
        'DevOps' => 'DevOps',
    );

    foreach ( $cat_items as $search => $label ) {
        $cat = get_term_by( 'name', $search, 'category' );
        if ( ! $cat ) {
            $cats = get_terms( array( 'taxonomy' => 'category', 'search' => $search, 'hide_empty' => false ) );
            $cat  = ! empty( $cats ) ? $cats[0] : null;
        }

        if ( $cat ) {
            $parent_item_id = wp_update_nav_menu_item( $menu_id, 0, array(
                'menu-item-title'     => $label,
                'menu-item-type'      => 'taxonomy',
                'menu-item-object'    => 'category',
                'menu-item-object-id' => $cat->term_id,
                'menu-item-status'    => 'publish',
            ) );

            // Child categories → dropdown
            $children = get_terms( array(
                'taxonomy'   => 'category',
                'parent'     => $cat->term_id,
                'hide_empty' => true,
            ) );
            foreach ( $children as $child ) {
                wp_update_nav_menu_item( $menu_id, 0, array(
                    'menu-item-title'     => $child->name,
                    'menu-item-type'      => 'taxonomy',
                    'menu-item-object'    => 'category',
                    'menu-item-object-id' => $child->term_id,
                    'menu-item-parent-id' => $parent_item_id,
                    'menu-item-status'    => 'publish',
                ) );
            }
        } else {
            // Fallback: custom link
            wp_update_nav_menu_item( $menu_id, 0, array(
                'menu-item-title'  => $label,
                'menu-item-type'   => 'custom',
                'menu-item-url'    => home_url( '/category/' . strtolower( $search ) . '/' ),
                'menu-item-status' => 'publish',
            ) );
        }
    }

    // About us — find the page or fall back to /about/
    $about_query = new WP_Query( array(
        'post_type'      => 'page',
        'post_status'    => 'publish',
        'posts_per_page' => 1,
        'title'          => 'About',
    ) );
    $about_id = $about_query->have_posts() ? $about_query->posts[0]->ID : 0;

    if ( $about_id ) {
        wp_update_nav_menu_item( $menu_id, 0, array(
            'menu-item-title'     => __( 'About us', 'astra-child' ),
            'menu-item-type'      => 'post_type',
            'menu-item-object'    => 'page',
            'menu-item-object-id' => $about_id,
            'menu-item-status'    => 'publish',
        ) );
    } else {
        wp_update_nav_menu_item( $menu_id, 0, array(
            'menu-item-title'  => __( 'About us', 'astra-child' ),
            'menu-item-type'   => 'custom',
            'menu-item-url'    => home_url( '/about/' ),
            'menu-item-status' => 'publish',
        ) );
    }

    // Assign to Astra primary location
    $locations            = get_theme_mod( 'nav_menu_locations', array() );
    $locations['primary'] = $menu_id;
    set_theme_mod( 'nav_menu_locations', $locations );

    update_option( 'cloudcraft_menu_v2', true );
}


// ── 5b. Animated SVG logo ────────────────────────────────────────────────────
function cloudcraft_logo_svg() {
    // Gear upper-left (center 16,16): ombre gradient, transparent hole via evenodd, cloud covers lower-right at rest.
    return '<svg class="cc-logo-svg" viewBox="0 0 68 52" width="52" height="40"
            fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        <defs>
            <linearGradient id="cc-gear-grad" gradientUnits="userSpaceOnUse" x1="2" y1="2" x2="30" y2="30">
                <stop offset="0%"   stop-color="#74A9E6"/>
                <stop offset="100%" stop-color="#C4AEED"/>
            </linearGradient>
        </defs>
        <g class="cc-logo-gear">
            <rect x="14" y="2" width="4" height="5" rx="1.5" fill="url(#cc-gear-grad)" transform="rotate(0   16 16)"/>
            <rect x="14" y="2" width="4" height="5" rx="1.5" fill="url(#cc-gear-grad)" transform="rotate(45  16 16)"/>
            <rect x="14" y="2" width="4" height="5" rx="1.5" fill="url(#cc-gear-grad)" transform="rotate(90  16 16)"/>
            <rect x="14" y="2" width="4" height="5" rx="1.5" fill="url(#cc-gear-grad)" transform="rotate(135 16 16)"/>
            <rect x="14" y="2" width="4" height="5" rx="1.5" fill="url(#cc-gear-grad)" transform="rotate(180 16 16)"/>
            <rect x="14" y="2" width="4" height="5" rx="1.5" fill="url(#cc-gear-grad)" transform="rotate(225 16 16)"/>
            <rect x="14" y="2" width="4" height="5" rx="1.5" fill="url(#cc-gear-grad)" transform="rotate(270 16 16)"/>
            <rect x="14" y="2" width="4" height="5" rx="1.5" fill="url(#cc-gear-grad)" transform="rotate(315 16 16)"/>
            <path fill-rule="evenodd" fill="url(#cc-gear-grad)"
                  d="M 26,16 A 10,10 0 1,0 6,16 A 10,10 0 1,0 26,16 Z
                     M 21,16 A 5,5 0 1,1 11,16 A 5,5 0 1,1 21,16 Z"/>
        </g>
        <g class="cc-logo-cloud">
            <ellipse cx="23" cy="31" rx="10" ry="9"  fill="#74A9E6"/>
            <ellipse cx="41" cy="22" rx="15" ry="13" fill="#74A9E6"/>
            <ellipse cx="57" cy="28" rx="11" ry="9"  fill="#74A9E6"/>
            <rect    x="13"  y="30"  width="55" height="18" rx="7" fill="#74A9E6"/>
        </g>
    </svg>';
}

// ── 5c. Primary navigation bar (sticky, inside header wrapper) ───────────────
add_action( 'astra_below_header', 'cloudcraft_primary_nav' );
function cloudcraft_primary_nav() {
    ?>
    <div class="cc-nav-wrap">
        <div class="cc-nav-inner">
            <a class="cc-logo-wrap" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php esc_attr_e( 'CloudCraft home', 'astra-child' ); ?>">
                <?php echo cloudcraft_logo_svg(); // phpcs:ignore WordPress.Security.EscapeOutput ?>
                <span class="cc-logo-text">CloudCraft</span>
            </a>
            <?php
            wp_nav_menu( array(
                'theme_location' => 'primary',
                'menu_class'     => 'cc-nav-menu',
                'container'      => false,
                'depth'          => 2,
                'fallback_cb'    => false,
            ) );
            ?>
            <div class="cc-nav-right">
                <button class="cc-dm-btn" id="cc-dm-btn" aria-label="<?php esc_attr_e( 'Toggle dark mode', 'astra-child' ); ?>">
                    <span class="cc-dm-knob" id="cc-dm-knob">🌙</span>
                    <span id="cc-dm-label"><?php esc_html_e( 'Dark', 'astra-child' ); ?></span>
                </button>
            </div>
        </div>
    </div>
    <?php
}


// ── 6. Hero banner on blog index ─────────────────────────────────────────────
// Hooks into astra_content_before which fires AFTER </header> and BEFORE
// <div id="content">, giving the hero full viewport width above the content grid.
add_action( 'astra_content_before', 'cloudcraft_hero_banner' );
function cloudcraft_hero_banner() {
    if ( ! is_home() ) {
        return;
    }
    ?>
    <div class="cc-hero">
        <div class="cc-hero-inner">
            <span class="cc-hero-eyebrow"><?php esc_html_e( 'Tech & DevOps Blog', 'astra-child' ); ?></span>
            <h1 class="cc-hero-title"><?php esc_html_e( 'Build, Deploy & Learn with CloudCraft', 'astra-child' ); ?></h1>
            <p class="cc-hero-sub"><?php esc_html_e( 'Practical guides on Linux, DevOps, cloud infrastructure, and modern tools — written for humans.', 'astra-child' ); ?></p>
        </div>
    </div>
    <?php
}


// ── 7. Footer copyright text ─────────────────────────────────────────────────
add_filter( 'option_astra-settings', 'cloudcraft_footer_copyright_text' );
function cloudcraft_footer_copyright_text( $settings ) {
    if ( is_array( $settings ) ) {
        $settings['footer-copyright-editor'] = 'Built with &#9729; and coffee';
    }
    return $settings;
}


// ── 0. Enqueue parent + child stylesheets ────────────────────────────────────
add_action( 'wp_enqueue_scripts', 'cloudcraft_enqueue_styles' );
function cloudcraft_enqueue_styles() {
    wp_enqueue_style( 'astra-parent-style', get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'astra-child-style', get_stylesheet_uri(), array( 'astra-parent-style' ) );
}


// ── 1. Hide empty tag / category sections ────────────────────────────────────
add_filter( 'the_tags', 'cloudcraft_hide_empty_tags', 10, 3 );
function cloudcraft_hide_empty_tags( $tag_list, $before, $sep ) {
    return get_the_tags( get_the_ID() ) ? $tag_list : '';
}

add_filter( 'the_category', 'cloudcraft_hide_empty_categories', 10, 3 );
function cloudcraft_hide_empty_categories( $thelist, $separator, $parents ) {
    $cats = get_the_category();
    if ( empty( $cats ) ) {
        return '';
    }
    // Treat "only Uncategorized" as no real category
    if ( 1 === count( $cats ) && 'uncategorized' === $cats[0]->slug ) {
        return '';
    }
    return $thelist;
}


// ── 2. Fallback featured image ───────────────────────────────────────────────
// Chain: featured image → first content image → category default → site logo → site icon
//
// NOTE: Astra gates get_the_post_thumbnail() behind has_post_thumbnail(), so the
// standard post_thumbnail_html filter never fires for posts without a thumbnail.
// We hook into astra_get_post_thumbnail instead — it fires unconditionally after
// Astra's block, with $output='' when no thumbnail.

function cloudcraft_get_first_content_image( $post_id ) {
    $content = get_post_field( 'post_content', $post_id );
    if ( preg_match( '/<img[^>]+src=[\'"]([^\'"]+)[\'"][^>]*/i', $content, $m ) ) {
        return esc_url( $m[1] );
    }
    return '';
}

function cloudcraft_get_fallback_image_url( $post_id ) {
    // First image in post content
    $url = cloudcraft_get_first_content_image( $post_id );
    if ( $url ) {
        return $url;
    }

    // Category default image
    foreach ( get_the_category( $post_id ) as $cat ) {
        $cat_image_id = get_term_meta( $cat->term_id, 'cloudcraft_cat_image_id', true );
        if ( $cat_image_id ) {
            $src = wp_get_attachment_image_url( $cat_image_id, 'large' );
            if ( $src ) {
                return $src;
            }
        }
    }

    // Site logo
    $logo_id = get_theme_mod( 'custom_logo' );
    if ( $logo_id ) {
        $src = wp_get_attachment_image_url( $logo_id, 'large' );
        if ( $src ) {
            return $src;
        }
    }

    // Site icon
    return get_site_icon_url( 512 ) ?: '';
}

add_filter( 'astra_get_post_thumbnail', 'cloudcraft_astra_fallback_thumbnail', 10, 3 );
function cloudcraft_astra_fallback_thumbnail( $output, $before, $after ) {
    if ( ! empty( $output ) ) {
        return $output;
    }

    $post_id = get_the_ID();
    if ( ! $post_id ) {
        return $output;
    }

    $fallback_url = cloudcraft_get_fallback_image_url( $post_id );
    if ( empty( $fallback_url ) ) {
        return $output;
    }

    $post_title = esc_html( get_the_title( $post_id ) );
    $img = '<img src="' . esc_url( $fallback_url ) . '" alt="' . esc_attr( $post_title ) . '" class="wp-post-image cloudcraft-fallback-thumb" />';

    $inner = '<div class="post-thumb-img-content post-thumb">';
    if ( ! is_singular() ) {
        $inner .= '<a href="' . esc_url( get_permalink( $post_id ) ) . '" aria-label="'
            . esc_attr( sprintf( /* translators: %s: post title */ __( 'Read: %s', 'astra' ), $post_title ) )
            . '">';
    }
    $inner .= $img;
    if ( ! is_singular() ) {
        $inner .= '</a>';
    }
    $inner .= '</div>';

    return $inner;
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
                var img = $('<img>').attr('src', preview).css('max-width', '150px');
                $('#cloudcraft-cat-img-preview').empty().append(img);
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
                $count_label = $show_count ? ' <span class="cat-count">(' . (int) $cat->count . ')</span>' : '';
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


// ── 4. Dark mode toggle ──────────────────────────────────────────────────────
// Early script prevents flash-of-light before CSS loads
if ( ! is_admin() ) {
    add_action( 'wp_head',   'cloudcraft_dark_mode_early_init', 1 );
    add_action( 'wp_footer', 'cloudcraft_dark_mode_script' );
}
function cloudcraft_dark_mode_early_init() {
    ?>
    <script>
    (function(){
        var s = localStorage.getItem('cc-dark');
        var sys = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
        if ( s === '1' || ( s === null && sys ) ) {
            document.documentElement.classList.add('cloudcraft-dark');
        }
    })();
    </script>
    <?php
}

// Toggle script — runs at footer so button element exists
function cloudcraft_dark_mode_script() {
    ?>
    <script>
    (function(){
        var btn   = document.getElementById('cc-dm-btn');
        var knob  = document.getElementById('cc-dm-knob');
        var label = document.getElementById('cc-dm-label');
        var root  = document.documentElement;
        var key   = 'cc-dark';

        function apply( dark ) {
            root.classList.toggle( 'cloudcraft-dark', dark );
            if ( knob )  knob.textContent  = dark ? '☀️' : '🌙';
            if ( label ) label.textContent = dark ? 'Light' : 'Dark';
        }

        // Sync button state to whatever early-init already set
        apply( root.classList.contains('cloudcraft-dark') );

        if ( btn ) {
            btn.addEventListener('click', function(){
                var isDark = root.classList.contains('cloudcraft-dark');
                apply( !isDark );
                localStorage.setItem( key, !isDark ? '1' : '0' );
            });
        }
    })();
    </script>
    <?php
}


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

// ── REST API: Enable Application Passwords ────────────────────────────────────
add_filter( 'wp_is_application_passwords_available', '__return_true' );
