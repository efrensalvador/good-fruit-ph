<?php
defined('ABSPATH') || exit;
get_header();
while (have_posts()) : the_post();
    if (post_password_required()) { echo get_the_password_form(); continue; }
    global $product;
    $product = wc_get_product(get_the_ID());
    if (!$product) { continue; }
    WC()->structured_data->generate_product_data($product);
?>
<main id="gfei-product" class="gfei">
  <div class="gfei-wrap">
    <nav class="gfei-breadcrumb" aria-label="Breadcrumb"><a href="<?php echo esc_url(home_url('/shop/')); ?>">Shop</a><span aria-hidden="true">/</span><span aria-current="page">3M VFlex 9105</span></nav>
    <?php woocommerce_output_all_notices(); ?>
    <div class="gfei-grid">
      <section class="gfei-visual" aria-label="Product photograph">
        <div class="gfei-image-label"><span>GFE INDUSTRIAL</span><span>RESPIRATORY PROTECTION</span></div>
        <img class="gfei-photo" src="<?php echo esc_url(home_url('/wp-content/uploads/2026/09/3M-N95-9105.webp')); ?>" alt="3M VFlex 9105 N95 particulate respirator, front view" width="1000" height="1047" fetchpriority="high">
        <p class="gfei-caption">3M™ VFlex™ <span>Model 9105</span></p>
      </section>
      <section class="gfei-summary" aria-labelledby="gfei-title">
        <p class="gfei-eyebrow">GFE INDUSTRIAL · PERSONAL PROTECTIVE EQUIPMENT</p>
        <h1 id="gfei-title">3M™ VFlex™ 9105<span>N95 Particulate Respirator</span></h1>
        <p class="gfei-intro">A disposable particulate respirator with a distinctive V-shaped pleated design. Priced per piece, with a minimum order of 3 pieces.</p>
        <div class="gfei-tags"><span>3M™ VFlex™</span><span>Model 9105</span><span>Sold individually</span></div>
        <div class="gfei-purchase">
          <p>Minimum order: <strong>3 pieces</strong></p>
          <div class="gfei-price"><?php echo wp_kses_post($product->get_price_html()); ?><span>per piece</span></div>
          <?php woocommerce_template_single_add_to_cart(); ?>
          <a class="gfei-cart-link" href="<?php echo esc_url(wc_get_cart_url()); ?>">View cart →</a>
        </div>
        <div class="gfei-enquiry"><strong>Ordering for your team?</strong><p>Contact Good Fruit Enterprise for quantity and delivery enquiries.</p><a href="mailto:sales@goodfruitph.com">sales@goodfruitph.com →</a></div>
      </section>
    </div>
    <section class="gfei-details" aria-labelledby="gfei-details-title">
      <div><p class="gfei-eyebrow">KNOW YOUR PRODUCT</p><h2 id="gfei-details-title">Product details</h2><p>Review the product information and manufacturer instructions before use.</p></div>
      <dl><div><dt>Brand</dt><dd>3M™</dd></div><div><dt>Product family</dt><dd>VFlex™</dd></div><div><dt>Model</dt><dd>9105</dd></div><div><dt>Product type</dt><dd>N95 particulate respirator</dd></div><div><dt>Sales unit</dt><dd>1 piece</dd></div></dl>
    </section>
    <section class="gfei-description" aria-label="Additional product information"><?php the_content(); ?></section>
  </div>
</main>
<?php endwhile; get_footer(); ?>
