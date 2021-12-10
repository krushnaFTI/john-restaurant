<?php get_header();

$product_id = get_the_ID();
global $post;
$page_slug = $post->post_name; ?>

    <main id="site-content" role="main">
        <article class="post-<?php echo $product_id; ?> page type-page status-publish hentry" id="post-<?php echo $product_id; ?>">
            <div class="post-inner thin">
                <div class="entry-content">
                    <div class="inner-banner" style=" background-image: url(<?php echo get_the_post_thumbnail_url(); ?>)">
                        <div class="container">
                            <?php echo "<h1>".get_the_title()."</h1>"; ?>
                            <?php $subtitle_value = get_post_meta( $product_id, '_product_banner_subtitle_text');
                            if($subtitle_value[0]){ ?>
                                <div class="sub-title"><?php echo $subtitle_value[0]; ?></div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </article>
    </main>

    <div class="main-content">
        <div class="container">

            <div class="left-content">
                <div class="product-details-top">

                    <div class="product-details-top-left">
                        <div class="product-details-main-img">
                            <img src="<?php echo get_the_post_thumbnail_url(); ?>">
                        </div>
                        <div class="product-thumbnail-img">
                            <ul>
                                <?php $product_gallery = get_post_meta($product_id, 'mp_menu_gallery');
                                $product_gallery = preg_split ("/\,/", $product_gallery[0]);
                                $total_image = count($product_gallery);
                                for($i=0; $i<$total_image; $i++){
                                    $img_src = wp_get_attachment_image_src($product_gallery[$i]); ?>
                                    <li><a href="#"><img src="<?php echo $img_src[0]; ?>"></a></li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>

                    <?php $product_price = get_post_meta($product_id, 'price'); ?>
                    <div class="product-details-top-right">
                        <div class="product-details-name"><?php echo get_the_title(); ?></div>
                        <div class="product-details-des"><?php echo get_the_content(); ?></div>
                        <div class="product-details-price">Price: <?php echo "$".$product_price[0]; ?></div>
                        <div class="mprm_menu_item_buy_button mprm-plugin-styles" style="">
                            <div class="mprm-notice mprm-hidden">
                                <div class="mprm-success">
                                    <span class="mprm-text mprm-notice-text"><span class="mprm-notice-title">“<?php echo get_the_title(); ?>”</span>
                                        <span class="mprm-notice-text">has been added to your cart.</span></span>
                                    <span class="mprm-notice-actions"><a href="<?php echo site_url(); ?>/checkout/" class="">View cart</a></span>
                                </div>
                                <div class="mprm-error">
                                    <span class="mprm-notice-text">An error occurred. Please try again later.</span>
                                </div>
                            </div>	
                            <div class="mprm-add-menu-item mprm-display-inline" style="position: relative;">
                                <div class="mprm-container-preloader">
                                    <div class="mprm-floating-circles mprm-floating-circle-wrapper small-preloader mprm-hidden">
                                        <div class="mprm-floating-circle" id="mprm-floating-circle-rotate-1"></div>
                                        <div class="mprm-floating-circle" id="mprm-floating-circle-rotate-2"></div>
                                        <div class="mprm-floating-circle" id="mprm-floating-circle-rotate-3"></div>
                                        <div class="mprm-floating-circle" id="mprm-floating-circle-rotate-4"></div>
                                        <div class="mprm-floating-circle" id="mprm-floating-circle-rotate-5"></div>
                                        <div class="mprm-floating-circle" id="mprm-floating-circle-rotate-6"></div>
                                        <div class="mprm-floating-circle" id="mprm-floating-circle-rotate-7"></div>
                                        <div class="mprm-floating-circle" id="mprm-floating-circle-rotate-8"></div>
                                    </div>
                                </div>
                                <form id="mprm_purchase_<?php echo $product_id; ?>" class="mprm_purchase_form mprm_purchase_<?php echo $product_id; ?>" data-id="<?php echo $product_id; ?>" method="post">
                                    
                                    <a href="#" class="mprm-add-to-cart mprm-has-js button  mprm-submit mprm-inherit mprm-display-inline" data-action="mprm_add_to_cart" data-menu-item-id="<?php echo $product_id; ?>" data-variable-price="no" data-price-mode="single" data-price="<?php echo $product_price[0]; ?>">
                                        <span class="mprm-add-to-cart-label">Add to Cart</span>
                                    </a>
                                    
                                    <a href="<?php echo site_url(); ?>/checkout/" style="display: none" class="mprm_go_to_checkout button  mprm-submit mprm-inherit mprm-display-inline">Checkout</a>
                                                                
                                    <input type="hidden" name="menu_item_id" value="<?php echo $product_id; ?>">
                                    <input type="hidden" name="controller" value="cart">

                                    <input type="hidden" name="_wp_http_referer" value="<?php echo site_url(); ?>/menu/<?php echo $page_slug; ?>/">
                                    <input type="hidden" name="mprm_action" class="mprm_action_input" value="add_to_cart">
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="product-tabs">
                        <div id="parentHorizontalTab" class="parentHorizontalTab">
                            <ul class="resp-tabs-list hor_1">
                                <li>Portion Size</li>
                                <li>Nutritional</li>
                            </ul>
                            <div class="resp-tabs-container hor_1">
                                <div>
                                    <?php $attributes = get_post_meta($product_id, 'attributes');
                                    $attributes = $attributes[0]; ?>
                                    <ul>
                                        <?php if($attributes['weight']['val'] != "" || $attributes['bulk']['val'] != "" || $attributes['size']['val'] != ""){ ?>
                                            <?php if($attributes['weight']['val']){ echo "<li>Weight: ".$attributes['weight']['val']."</li>"; } ?>
                                            <?php if($attributes['bulk']['val']){ echo "<li>Bulk: ".$attributes['bulk']['val']."</li>"; } ?>
                                            <?php if($attributes['size']['val']){ echo "<li>Size: ".$attributes['size']['val']."</li>"; } ?>
                                        <?php }else{ 
                                           echo "<li>Data not available.</li>";
                                        } ?>
                                    </ul>
                                </div>
                                <div>
                                    <?php $nutritional = get_post_meta($product_id, 'nutritional');
                                    $nutritional = $nutritional[0]; ?>
                                    <ul>
                                        <?php if($nutritional['calories']['val'] != "" || $nutritional['cholesterol']['val'] != "" || $nutritional['fiber']['val'] != "" || $nutritional['sodium']['val'] != "" || $nutritional['carbohydrates']['val'] != "" || $nutritional['fat']['val'] != "" || $nutritional['protein']['val'] != "") { ?>
                                            <?php if($nutritional['calories']['val']){ echo "<li>Calories: ".$nutritional['calories']['val']."</li>"; } ?>
                                            <?php if($nutritional['cholesterol']['val']){ echo "<li>Cholesterol: ".$nutritional['cholesterol']['val']."</li>"; } ?>
                                            <?php if($nutritional['fiber']['val']){ echo "<li>Fiber: ".$nutritional['fiber']['val']."</li>"; } ?>
                                            <?php if($nutritional['sodium']['val']){ echo "<li>Sodium: ".$nutritional['sodium']['val']."</li>"; } ?>
                                            <?php if($nutritional['carbohydrates']['val']){ echo "<li>Carbohydrates: ".$nutritional['carbohydrates']['val']."</li>"; } ?>
                                            <?php if($nutritional['fat']['val']){ echo "<li>Fat: ".$nutritional['fat']['val']."</li>"; } ?>
                                            <?php if($nutritional['protein']['val']){ echo "<li>Protein: ".$nutritional['protein']['val']."</li>"; } ?>
                                        <?php }else{
                                            echo "<li>Data not available.</li>";   
                                        }?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="related-products">
                        <h3>You might also like</h3>
                        <div class="row">
                            <?php
                            $args = array(  
                                'post_type' => 'mp_menu_item',
                                'post_status' => 'publish',
                                'posts_per_page' => -1, 
                                'orderby' => 'date', 
                                'order' => 'DESC', 
                            );
                            $loop = new WP_Query( $args ); 
                            if ( $loop->have_posts() ) {
                                while ( $loop->have_posts() ) : $loop->the_post();
                                    $product_item_id = get_the_ID();
                                    $product_price = get_post_meta($product_item_id, 'price');
                                    $product_in_stock = get_post_meta($product_item_id, 'stock_manage_field_custom');
                                    if($product_in_stock[0] == "InStock"){ ?>
                                        <div class="col-6 item">
                                            <div class="related-products-img"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/product-img.jpg" alt=""></div>
                                            <div class="related-products-content">
                                                <div class="related-products-title">
                                                    <span class="name"><?php echo get_the_title(); ?></span><span class="price">$<?php echo $product_price[0]; ?></span>
                                                </div>
                                                <div class="related-products-des">
                                                    <?php $short_desc = get_the_excerpt();
                                                    echo substr($short_desc, 0, 80); ?>...
                                                </div>
                                            </div>
                                        </div>
                                    <?php }
                                endwhile;
                            } ?>
                        </div>
                    </div>

                </div>
            </div>

            <div class="right-content">
                <div class="sidebar-box">
                    <h3>Cart</h3>
                    <div class="sidebar-content">
                        <div class="cart-items">
                            <?php
                            $session_cart = unserialize($_SESSION['mprm']['mprm_cart']);
                            if(!empty($_SESSION) && !empty($session_cart) ){
                                $cart_data = unserialize($_SESSION['mprm']['mprm_cart']); 
                                $total_cart_items = count($cart_data);
                                for($i=0; $i<$total_cart_items; $i++){ 
                                    $item_id = $cart_data[$i]['id'];
                                    $item_title = get_the_title($cart_data[$i]['id']);
                                    $item_price = get_post_meta($item_id, 'price');
                                    $item_quantity = $cart_data[$i]['quantity'];
                                    $item_total_price = $item_price[0] * $item_quantity; ?>
                                    <div class="cart-item">
                                        <span class="cart-name"><?php echo $item_title; ?></span>
                                        <span class="cart-qty">- <?php echo $item_quantity; ?></span>
                                        <span class="cart-delete"><a href="<?php echo site_url(); ?>/checkout/?cart_item=<?php echo $i; ?>&mprm_action=remove&controller=cart"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/delete-icon.png" alt="" /></a></span>
                                        <span class="cart-price">$<? echo $item_total_price; ?></span>
                                    </div>
                                <?php } ?>
                                <div class="cart-sub-total">
                                    Subtotal <span class="cart-subtotal"><?php echo mprm_currency_filter(mprm_format_amount(mprm_get_cart_subtotal())); ?></span>
                                </div>
                                <div class="cart-button"><a href="<?php echo site_url(); ?>/checkout/" class="button">Checkout</a></div>
                            <?php }else{ ?>
                                <p>Your cart is empty.</p>
                            <?php } ?>
                        </div>
                        
                    </div>
                </div>
                <div class="sidebar-box">
                    <h3>Recommended by Our Chef</h3>
                    <div class="sidebar-content">
                        <div class="recommended-items">
                            <?php
                            $args = array(  
                                'post_type' => 'mp_menu_item',
                                'post_status' => 'publish',
                                'posts_per_page' => 4, 
                                'orderby' => 'date', 
                                'order' => 'DESC', 
                            );
                            $loop = new WP_Query( $args ); 
                            if ( $loop->have_posts() ) {
                                while ( $loop->have_posts() ) : $loop->the_post();
                                    $product_item_id = get_the_ID();
                                    $product_price = get_post_meta($product_item_id, 'price');
                                    $product_in_stock = get_post_meta($product_item_id, 'stock_manage_field_custom');
                                    if($product_in_stock[0] == "InStock"){ ?>
                                    
                                        <div class="recommended-item">
                                            <div class="recommended-item-img"><img src="<?php echo get_the_post_thumbnail_url(); ?>" alt="" /></div>
                                            <div class="recommended-item-right">
                                                <h4><?php echo get_the_title(); ?></h4>
                                                <?php $get_excerpt = get_the_excerpt(); ?>
                                                <p><?php echo substr($get_excerpt, 0, 40); ?>...</p>
                                            </div>
                                        </div>
                                    
                                    <?php }
                                endwhile;
                            } ?>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>

<?php get_footer(); ?>