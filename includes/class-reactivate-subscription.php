<?php
defined( 'WINDROS_INIT' ) || exit;  



if ( ! class_exists( 'Windros_Reactivate_Subscription' ) ) {
    class Windros_Reactivate_Subscription {
        public function __construct() {
            add_action( 'wp_ajax_windrose_reactivate_subscription', [$this, 'reactivate_subscription'] );
            add_action( 'wp_ajax_nopriv_windrose_reactivate_subscription', [$this, 'reactivate_subscription'] );
        }

        public function reactivate_subscription() {
            // Verify the nonce for security
            if (!isset($_POST['reactivate_subscription_nonce']) || !wp_verify_nonce($_POST['reactivate_subscription_nonce'], 'reactivate_subscription_action')) {
                wp_send_json_error(__('Invalid submission.', 'windros-subscription'));
                wp_die();
            }

            // Nonce is valid, process form data            
            $subscription_id = intval($_POST['subscription_id']);
            $current_user_id = get_current_user_id();

            global $wpdb;

            // Define your custom table name (considering WordPress table prefix)
            $subscription_table = $wpdb->prefix . WINDROS_SUBSCRIPTION_MAIN_TABLE;
            $subscription_order_table = $wpdb->prefix . WINDROS_SUBSCRIPTION_ORDER_TABLE;

            // Query to get all rows from the custom table
            // Prepare and execute the query to retrieve a single subscription
            $subscription = $wpdb->get_row( 
                $wpdb->prepare( "SELECT * FROM $subscription_table WHERE id = %d AND user_id = %s", $subscription_id, $current_user_id )
            );


            $response = array();

            if ( $subscription ) {

                $data = array(
                    'total_orders' => intval($subscription->total_orders)+1,
                    'status' => 'active'
                );
    
                $condition = array(
                    'id' => $subscription_id,
                );
    
                $format = array('%d', '%s');  
                $where_format = array('%d'); 

                $updated = $wpdb->update( $subscription_table, $data, $condition, $format, $where_format );

                if($updated){
                    do_action('windrose_subscription_main_order_activated', $subscription_id);

                    wc_add_notice( __('The subscription has been re-activated!', 'windros-subscription'), 'success');
                    
                    $response['status'] = 'success';
                    $response['message'] = __('The subscription has been re-activated!', 'windros-subscription');

                }else{
                    wc_add_notice( __('The subscription is not re-activated!', 'windros-subscription'), 'success');
                    
                    $response['status'] = 'success';
                    $response['message'] = __('The subscription is not re-activated!', 'windros-subscription');
                }                
            }else{
                $response['status'] = 'error';
                $response['message'] = __('Unauthorized Request!', 'windros-subscription');
                wc_add_notice( __('Unauthorized Request!', 'windros-subscription'), 'error');
            }

            // Return the user ID in the AJAX response
            wp_send_json_success($response);

            wp_die(); // Required to end the AJAX request
        }        
    }
}

new Windros_Reactivate_Subscription();