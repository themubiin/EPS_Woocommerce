
<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="wrap eps-dashboard-wrap">
    <div class="eps-header-card">
        <div class="eps-brand">
            <div class="eps-title-group">
                <h2><?php esc_html_e( 'EPS Gateway Settings', 'eps' ); ?></h2>
                <span class="eps-mode-badge <?php echo esc_attr(($values['mode'] ?? 'sandbox') === 'production' ? 'mode-live' : 'mode-sandbox'); ?>">
                    <span class="dot"></span>
                    <?php echo esc_html(($values['mode'] ?? 'sandbox') === 'production' ? esc_html__( 'Live Mode', 'eps' ) : esc_html__( 'Sandbox Mode', 'eps' )); ?>
                </span>
            </div>
        </div>
    </div>

    <?php if ( isset($this->errors['error_message' ] ) ) { ?>
        <div class="notice notice-error is-dismissible eps-notice">
            <p><strong><?php echo esc_html($this->errors['error_message' ]); ?></strong></p>
        </div>
    <?php } ?>   

    <?php if ( isset($this->successes['success_message' ] ) ) { ?>
        <div class="notice notice-success is-dismissible eps-notice">
            <p><strong><?php echo esc_html($this->successes['success_message' ]); ?></strong></p>
        </div>
    <?php } ?>   

    <div class="eps-card eps-form-card">
        <form action="" method="post" class="eps-form">
            <div class="eps-form-grid">
                <div class="eps-form-group">
                    <label for="module_val"><?php esc_html_e( 'User Name', 'eps' ); ?> <span class="required">*</span></label>
                    <input type="text" name="module_val" required="true" id="module_val" class="eps-input" value="<?php echo esc_attr($values['module_val']); ?>">
                </div>

                <div class="eps-form-group">
                    <label for="password"><?php esc_html_e( 'Password', 'eps' ); ?> <?php echo empty($values['plugin_key']) ? '<span class="required">*</span>' : ''; ?></label>
                    <input type="password" name="password" id="password" class="eps-input" value="" placeholder="<?php echo !empty($values['plugin_key']) ? '••••••••••••••••' : ''; ?>" <?php echo empty($values['plugin_key']) ? 'required="true"' : ''; ?>>
                </div>

                <div class="eps-form-group eps-col-span-2">
                    <label for="merchent_code"><?php esc_html_e( 'Hash Key', 'eps' ); ?> <span class="required">*</span></label>
                    <input type="text" class="eps-input" required="true" name="merchent_code" id="merchent_code" value="<?php echo esc_attr($values['merchent_code']); ?>">
                </div>

                <div class="eps-form-group">
                    <label for="api_base_url"><?php esc_html_e( 'Merchant ID', 'eps' ); ?></label>
                    <input type="text" name="api_base_url" id="api_base_url" class="eps-input" value="<?php echo esc_attr($values['api_base_url']); ?>">
                </div>

                <div class="eps-form-group">
                    <label for="redirect_url"><?php esc_html_e( 'Store ID', 'eps' ); ?></label>
                    <input type="text" name="redirect_url" id="redirect_url" class="eps-input" value="<?php echo esc_attr($values['redirect_url']); ?>">
                </div>

                <div class="eps-form-group eps-col-span-2">
                    <label><?php esc_html_e( 'Environment Mode', 'eps' ); ?></label>
                    <div class="eps-radio-group">
                        <label class="eps-radio-pill <?php echo (esc_attr($values['mode'] ?? 'sandbox') === 'sandbox') ? 'is-active' : ''; ?>">
                            <input type="radio" name="mode" value="sandbox" <?php checked($values['mode'] ?? 'sandbox', 'sandbox'); ?>>
                            <span><?php esc_html_e( 'Sandbox', 'eps' ); ?></span>
                        </label>
                        <label class="eps-radio-pill <?php echo (esc_attr($values['mode'] ?? 'sandbox') === 'production') ? 'is-active' : ''; ?>">
                            <input type="radio" name="mode" value="production" <?php checked($values['mode'] ?? 'sandbox', 'production'); ?>>
                            <span><?php esc_html_e( 'Production (Live)', 'eps' ); ?></span>
                        </label>
                    </div>
                </div>
            </div>

            <?php wp_nonce_field( 's_mc_eps_setting' ); ?>
            <div class="eps-form-footer">
                <button type="submit" name="submit_mc_eps_setting" class="eps-btn-save">
                    <span class="dashicons dashicons-saved"></span> <?php esc_html_e( 'Save Changes', 'eps' ); ?>
                </button>
            </div>
        </form>
    </div>
</div>
