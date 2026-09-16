(function($) {
    'use strict';

    $(document).ready(function() {
        // Create redirection overlay if not present
        if ($('#eps-payment-loading-overlay').length === 0) {
            var logoUrl = (window.eps_checkout_params && window.eps_checkout_params.logo_url) 
                ? window.eps_checkout_params.logo_url 
                : 'https://eps.com.bd/images/logo.png';

            var overlayHtml = 
                '<div id="eps-payment-loading-overlay" class="eps-loading-overlay" style="display:none;">' +
                    '<div class="eps-loading-modal">' +
                        '<div class="eps-logo-container">' +
                            '<img src="' + logoUrl + '" alt="EPS" class="eps-pulse-logo" />' +
                        '</div>' +
                        '<div class="eps-spinner-circle"></div>' +
                        '<h3 class="eps-loading-title">Connecting to EPS Gateway</h3>' +
                        '<p class="eps-loading-desc">Please wait while we safely redirect you to complete your payment...</p>' +
                        '<div class="eps-secure-badge">' +
                            '<span class="dashicons dashicons-shield"></span> 256-Bit SSL Encrypted & Secured' +
                        '</div>' +
                    '</div>' +
                '</div>';

            $('body').append(overlayHtml);
        }

        function showOverlay() {
            var isEps = $('input[name="payment_method"]:checked').val() === 'eps';
            if (isEps) {
                $('#eps-payment-loading-overlay').stop(true, true).fadeIn(250);
            }
        }

        function hideOverlay() {
            $('#eps-payment-loading-overlay').stop(true, true).fadeOut(200);
        }

        // Classic WooCommerce Checkout submit
        $('form.checkout').on('checkout_place_order', function() {
            var selectedMethod = $('input[name="payment_method"]:checked').val();
            if (selectedMethod === 'eps') {
                showOverlay();
            }
        });

        // Pay for order page (order-pay)
        $('#order_review').on('submit', function() {
            var selectedMethod = $('input[name="payment_method"]:checked').val();
            if (selectedMethod === 'eps') {
                showOverlay();
            }
        });

        // Hide overlay if checkout error occurs
        $(document.body).on('checkout_error', function() {
            hideOverlay();
        });

        // Listen for browser navigation / backward
        window.addEventListener('pageshow', function(event) {
            hideOverlay();
        });
    });
})(jQuery);
