<?php
/*______________Contact Form 7 Spam Protection_________________
Spam messages are a prevalent issue in Contact Form 7. They flood your inbox with irrelevant content, compromise security, and potentially damage the credibility of your business. One of the simplest ways to combat this is to add a PHP validation code. 

The code snippet below effectively blocks any form submission that includes the defined "spam" words in the "your-message" text area field. If you need to add or remove words from the spam list, you can simply update the $spam_words array. */

// Add action hook
add_action('wpcf7_validate_textarea', 'custom_textarea_validation_filter', 10, 2);

function custom_textarea_validation_filter($result, $tag) {
    // Get the form input
    $form_input = $_POST[$tag->name];

    // Define spam words
    $spam_words = array('spam', 'viagra', 'cialis', 'xanax', 'loan', 'credit', 'claim', 'won', 'selected', 'congratulations');

    // Check if any spam word exists in the input
    foreach($spam_words as $spam_word) {
        if(stripos($form_input, $spam_word) !== false) {
            // Add validation error
            $result->invalidate($tag, 'Please remove inappropriate words from your message.');
            break;
        }
    }
    return $result;
}
?>

<?php
/* __________________Success Page Redirects_______________________ 
Sometimes, you might want to redirect users to another page after they submit a form, like a thank you page.

The code snippet below, used with Contact Form 7, does exactly that. 

It's easy to implement and ensures your website feels more responsive and thoughtful. It uses JavaScript to redirect the user to a specified URL after submitting a Contact Form 7 form.*/
function cf7_redirect() {
    ?>
    <script type="text/javascript">
    document.addEventListener( 'wpcf7mailsent', function( event ) {
        location = 'https://your-redirection-url.com'; //replace with your URL
    }, false );
    </script>
    <?php
}
add_action('wp_footer', 'cf7_redirect');
?>

<?php

/*_________________________Add a PayPal Donation Button________________________
This code snippet is perfect for add donation. Just add the [paypal_donation] shortcode at the desired location within your content and you've got a PayPal donation button ready to go. 

The PHP code below defines a WordPress shortcode [paypal_donation] which outputs a PayPal donation form when used in the post editor or any text widget. The shortcode accepts three attributes: business, amount, and currency_code. Make sure to customize these attributes according to your preferences
*/
function paypal_donation_shortcode($atts) {
    // Extract the attributes
    $atts = shortcode_atts(
        array(
            'business' => 'your-paypal-email@example.com',
            'amount' => '0',
            'currency_code' => 'USD',
        ),
        $atts,
        'paypal_donation'
    );
    // Build the HTML form string
    $output = '<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
        <input type="hidden" name="cmd" value="_donations" />
        <input type="hidden" name="business" value="' . esc_html($atts['business']) . '" />
        <input type="hidden" name="amount" value="' . esc_html($atts['amount']) . '" />
        <input type="hidden" name="currency_code" value="' . esc_html($atts['currency_code']) . '" />
        <input type="submit" value="Donate" />
    </form>';
    // Return the HTML form
    return $output;
}
add_shortcode('paypal_donation', 'paypal_donation_shortcode');
?>

<?php
/*_________________Add the Current Year in Blog Posts______________ 
Simply add the code at the bottom of your 'functions.php' file. Then, insert the [current_year] shortcode within your posts to dynamically display the current year. With this small change, you can easily keep your blog fresh and trustworthy.  
*/
function current_year_shortcode() {
    return date('Y');
}
add_shortcode('current_year', 'current_year_shortcode');
?>

<?php
/*_____________________Declare WooCommerce support in third party theme________________*/
function mytheme_add_woocommerce_support() {
	add_theme_support( 'woocommerce' );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );
} 
add_action( 'after_setup_theme', 'mytheme_add_woocommerce_support' );
?>

<?php
/*___________Adding Custom Currency to WooCommerce__________________ 
WooCommerce by default To add a custom currency in WooCommerce 2.0+, copy and paste this code in your theme functions.php file and swap out the currency code and symbol with your own. After saving changes, it should be available from your WooCommerce settings.
*/
add_filter( 'woocommerce_currencies', 'add_my_currency' ); 
function add_my_currency( $currencies ) { 
 $currencies['ABC'] = __( 'Currency name', 'woocommerce' ); 
 return $currencies;
}
add_filter('woocommerce_currency_symbol', 'add_my_currency_symbol', 10, 2); 
function add_my_currency_symbol( $currency_symbol, $currency ) 
{ 
 switch( $currency ) { 
 case 'ABC': 
 $currency_symbol = '$'; 
 break; 
 } 
 return $currency_symbol;
}
?>

<?php
/*_____________________Show custom billing checkout fields by product id________________________ */

add_action( 'woocommerce_checkout_fields', 'hqhowdotcom_cutom_checkout_field_conditional_logic' );
function hqhowdotcom_cutom_checkout_field_conditional_logic( $fields ) {
foreach( WC()->cart->get_cart() as $cart_item ){
     $product_id = $cart_item['product_id'];
//change 2020 to your product id
   if( $product_id == 2020 ) {
    $fields['billing']['billing_field_' . $product_id] = array(
     'label'     => __('Custom Field on Checkout for ' . $product_id, 'woocommerce'),
     'placeholder'   => _x('Custom Field on Checkout for ' . $product_id, 'placeholder', 'woocommerce'),
     'required'  => false,
     'class'     => array('form-row-wide'),
     'clear'     => true
    );
   }
}
// Return checkout fields.
 return $fields;
}
?>

<?php
/* ______________________Hide all shipping method but free shipping_______________________
In the user experience, you should automatically apply the free shipping method whenever possible, which helps customers feel more comfortable with your purchase. */
function only_show_free_shipping_when_available( $rates, $package ) {
    $new_rates = array();
    foreach ( $rates as $rate_id => $rate ) {
     // Only modify rates if free_shipping is present.
     if ( 'free_shipping' === $rate->method_id ) {
      $new_rates[ $rate_id ] = $rate;
      break;
     }
    }
   if ( ! empty( $new_rates ) ) {
     //Save local pickup if it's present.
     foreach ( $rates as $rate_id => $rate ) {
      if ('local_pickup' === $rate->method_id ) {
       $new_rates[ $rate_id ] = $rate;
       break;
      }
     }
     return $new_rates;
    }
   return $rates;
   }
   add_filter( 'woocommerce_package_rates', 'only_show_free_shipping_when_available', 10, 2 );
?>

