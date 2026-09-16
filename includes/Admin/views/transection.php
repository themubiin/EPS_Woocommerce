<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="wrap eps-dashboard-wrap">
    <div class="eps-header-card">
        <div class="eps-brand">
            <div class="eps-title-group">
                <h2><?php esc_html_e( 'EPS Transactions Dashboard', 'eps' ); ?></h2>
                <span class="eps-mode-badge <?php echo esc_attr($eps_current_mode === 'production' ? 'mode-live' : 'mode-sandbox'); ?>">
                    <span class="dot"></span>
                    <?php echo esc_html($eps_current_mode === 'production' ? esc_html__( 'Live Production', 'eps' ) : esc_html__( 'Sandbox Mode', 'eps' )); ?>
                </span>
            </div>
            <p class="eps-subtitle"><?php esc_html_e( 'Monitor all customer transactions, verify gateway reconciliation, and update fulfillment status.', 'eps' ); ?></p>
        </div>

        <div class="eps-header-actions">
            <button id="eps-sync-7" class="button button-secondary eps-btn" data-range="7_days">
                <span class="dashicons dashicons-update"></span> <?php esc_html_e( 'Sync (7 Days)', 'eps' ); ?>
            </button>
            <button id="eps-sync-12" class="button button-primary eps-btn" data-range="12_months">
                <span class="dashicons dashicons-cloud"></span> <?php esc_html_e( 'Full Sync (12 Months)', 'eps' ); ?>
            </button>
        </div>
    </div>

    <div class="eps-card eps-table-card">
        <div class="eps-table-toolbar">
            <div class="eps-filter-box">
                <label for="responseFilter"><?php esc_html_e( 'Filter by Status:', 'eps' ); ?></label>
                <select id="responseFilter" class="eps-select">
                    <option value=""><?php esc_html_e( 'All Statuses', 'eps' ); ?></option>
                    <option value="Success" selected><?php esc_html_e( 'Success', 'eps' ); ?></option>
                    <option value="Initialize"><?php esc_html_e( 'Initialize', 'eps' ); ?></option>
                    <option value="Failure"><?php esc_html_e( 'Failure', 'eps' ); ?></option>
                    <option value="Cancel"><?php esc_html_e( 'Cancelled', 'eps' ); ?></option>
                </select>
            </div>
        </div>

        <table id="eps_transections_show" class="eps-table" width="100%">
            <thead>
                <tr>
                    <th><?php esc_html_e( 'ID', 'eps' ); ?></th>
                    <th><?php esc_html_e( 'EPS / Txn ID', 'eps' ); ?></th>
                    <th><?php esc_html_e( 'Order ID', 'eps' ); ?></th>
                    <th><?php esc_html_e( 'Payment Status', 'eps' ); ?></th>
                    <th><?php esc_html_e( 'Amount', 'eps' ); ?></th>
                    <th><?php esc_html_e( 'Payment Method / Entity', 'eps' ); ?></th>
                    <th><?php esc_html_e( 'Transaction Date', 'eps' ); ?></th>
                    <th><?php esc_html_e( 'Product Status', 'eps' ); ?></th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>