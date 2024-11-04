<?php
defined( 'WINDROS_INIT' ) || exit;  



if ( ! class_exists( 'Windros_Admin_Subscription_Detail_View' ) ) {
    class Windros_Admin_Subscription_Detail_View {
        public function __construct() {
            add_action('admin_menu', [$this, 'subscription_details_menu']);
            add_action('admin_enqueue_scripts', [$this, 'enqueue_select2_assets']);
            
        }

        public function subscription_details_menu() {
            add_submenu_page(
                null, 
                'Subscriptions',
                'Subscriptions',
                'manage_woocommerce',
                'windrose-subscription-detail',
                [$this, 'subscription_detail_view'],
            );
        }

        public function subscription_detail_view() {
            echo '<div class="wrap">';
            echo '<h1>'. __('Subscription Details', 'windros-subscription') .'</h1>';
            echo '<br><a href="'. get_admin_url(null, 'admin.php?page=windrose-subscriptions') .'">';
            echo '<input type="button" id="doaction" class="button action" value="Go Back">';
            echo '</a>';
            if ( class_exists( 'Windros_Admin_Subscription_Details_Template' ) ) {
                $subscription_id = $_GET['id'];
                $subscription_detail = new Windros_Admin_Subscription_Details_Template();
                ?>
                <div id="windrose-subscription-data" class="postbox windrose-postbox">
                    <div class="inside">
                        <div class="panel-wrap windrose">
                            <?php $subscription_detail->display_subscription_details($subscription_id); ?>
                            <div class="clear"></div>                            
                        </div>
                        <div class="panel-wrap windrose">
                            <?php $subscription_detail->display_subscription_actions($subscription_id); ?>
                            <div class="clear"></div>                            
                        </div>
                        <div class="panel-wrap windrose">
                            <?php $subscription_detail->display_subscription_upcoming_delivery($subscription_id); ?>
                            <div class="clear"></div>                            
                        </div>
                        <div class="panel-wrap windrose">
                            <?php $subscription_detail->display_subscription_previous_deliveries($subscription_id); ?>
                            <div class="clear"></div>                            
                        </div>
                    </div>
                </div>
                <?php
                                                             
            }

            echo '<br><a href="'. get_admin_url(null, 'admin.php?page=windrose-subscriptions') .'">';
            echo '<input type="button" id="doaction" class="button action" value="Go Back">';
            echo '</a>';
            
            echo '</div>';
        }

        public function enqueue_select2_assets() {
            // Ensure WooCommerce is active to use its assets
            wp_enqueue_style('windrose-admin', WINDROS_URL . '/assets/stylesheets/admin-style.css');
            
        }
    }
}
new Windros_Admin_Subscription_Detail_View();