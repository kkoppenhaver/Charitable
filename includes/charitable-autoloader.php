<?php
/**
 * Dynamically loads the class attempting to be instantiated elsewhere in the
 * plugin.
 *
 * @package Charitable\Includes
 */

spl_autoload_register( 'charitable_autoloader' );

/**
 * Dynamically loads the class attempting to be instantiated elsewhere in the
 * plugin by looking at the $class_name parameter being passed as an argument.
 *
 * The argument should be in the form: TutsPlus_Namespace_Demo\Namespace. The
 * function will then break the fully-qualified class name into its pieces and
 * will then build a file to the path based on the namespace.
 *
 * The namespaces in this plugin map to the paths in the directory structure.
 *
 * @param string $class_name The fully-qualified name of the class to load.
 */
function charitable_autoloader( $class_name ) {
 
    // If the specified $class_name already exists, bail.
    if ( class_exists( $class_name ) ) {
        return;
    }

    // If the specified $class_name does not include our namespace, duck out.
    if ( false === strpos( $class_name, 'Charitable_' ) ) {
        return;
    }

    // Map class to location.
    $mapping = array(
        /* Interfaces */      
        'Charitable_Admin_Actions_Interface'        => 'interfaces/interface-charitable-admin-actions.php',
        'Charitable_Donation_Form_Interface'        => 'interfaces/interface-charitable-donation-form.php',
        'Charitable_Email_Fields_Interface'         =>  'interfaces/interface-charitable-email-fields.php',
        'Charitable_Email_Interface'                =>  'interfaces/interface-charitable-email.php',
        'Charitable_Endpoint_Interface'             =>  'interfaces/interface-charitable-endpoint.php',
        'Charitable_Field_Registry_Interface'       =>  'interfaces/interface-charitable-field-registry.php',
        'Charitable_Field_Interface'                =>  'interfaces/interface-charitable-field.php',
        'Charitable_Fields_Interface'               =>  'interfaces/interface-charitable-fields.php',
        'Charitable_Form_View_Interface'            =>  'interfaces/interface-charitable-form-view.php',
        'Charitable_Gateway_Interface'              =>  'interfaces/interface-charitable-gateway.php',
 
        /* Abstracts */
        'Charitable_Admin_Actions'                  => 'abstracts/abstract-class-charitable-admin-actions.php',
        'Charitable_DB'                             =>  'abstracts/abstract-class-charitable-db.php',
        'Charitable_Abstract_Donation'              =>  'abstracts/abstract-class-charitable-donation.php',
        'Charitable_Email'                          =>  'abstracts/abstract-class-charitable-email.php',
        'Charitable_Endpoint'                       =>  'abstracts/abstract-class-charitable-endpoint.php',
        'Charitable_Field'                          =>  'abstracts/abstract-class-charitable-field.php',
        'Charitable_Form'                           =>  'abstracts/abstract-class-charitable-form.php',
        'Charitable_Gateway'                        =>  'abstracts/abstract-class-charitable-gateway.php',
        'Charitable_Query'                          =>  'abstracts/abstract-class-charitable-query.php',

        /* Functions & Core Classes */          
        'Charitable_Locations'                      =>  'class-charitable-locations.php',
        'Charitable_Notices'                        =>  'class-charitable-notices.php',
        'Charitable_Post_Types'                     =>  'class-charitable-post-types.php',
        'Charitable_Request'                        =>  'class-charitable-request.php',
        'Charitable_Cron'                           =>  'class-charitable-cron.php',
        'Charitable_i18n'                           =>  'class-charitable-i18n.php',

        /* Addons */
        'Charitable_Addons'                         =>  'addons/class-charitable-addons.php',

        /* Admin */
        'Charitable_Admin'                          => 'admin/class-charitable-admin.php',

            /* Core */
            'Charitable_Metabox_Helper'              => 'admin/class-charitable-meta-box-helper.php',
            'Charitable_Admin_Pages'                 => 'admin/class-charitable-admin-pages.php',
            'Charitable_Admin_Notices'               => 'admin/class-charitable-admin-notices.php',

            /* Admin Actions */         
            'Charitable_Donation_Admin_Actions'      => 'admin/actions/class-charitable-donation-admin-actions.php',

            /* Campaigns */
            'Charitable_Campaign_Meta_Boxes'         => 'admin/campaigns/class-charitable-campaign-meta-boxes.php',
            'Charitable_Campaign_List_Table'         => 'admin/campaigns/class-charitable-campaign-list-table.php',
            'Charitable_Admin_Campaign_Hooks'        => 'admin/campaigns/charitable-admin-campaign-hooks.php',//

            /* Customizer */
            'Charitable_Customizer'                  =>  'admin/customizer/class-charitable-customizer.php',

            /* Dashboard widgets */
            'Charitable_Donations_Dashboard_Widget'  => 'admin/dashboard-widgets/class-charitable-donations-dashboard-widget.php',

            /* Donations */                     
            'Charitable_Donation_Metaboxes'          => 'admin/donations/class-charitable-donation-metaboxes.php',
            'Charitable_Donation_List_Table'         => 'admin/donations/class-charitable-donation-list-table.php',

            /* Forms */
            'Charitable_Admin_Form_View'             => 'admin/forms/views/class-charitable-admin-form-view.php',
            'Charitable_Admin_Form'                  => 'admin/forms/class-charitable-admin-form.php',
            'Charitable_Admin_Donation_Form'         => 'admin/forms/class-charitable-admin-donation-form.php',

            /* Settings */
            'Charitable_Settings'                    => 'admin/settings/class-charitable-settings.php',
            'Charitable_General_Settings'            => 'admin/settings/class-charitable-general-settings.php',
            'Charitable_Email_Settings'              => 'admin/settings/class-charitable-email-settings.php',
            'Charitable_Gateway_Settings'            => 'admin/settings/class-charitable-gateway-settings.php',
            'Charitable_Licenses_Settings'           => 'admin/settings/class-charitable-licenses-settings.php',
            'Charitable_Advanced_Settings'           => 'admin/settings/class-charitable-advanced-settings.php',

            /* Upgrades */
            'Charitable_Upgrade'                     => 'admin/upgrades/class-charitable-upgrade.php',
            'Charitable_Upgrade_Page'                => 'admin/upgrades/class-charitable-upgrade-page.php',

        /* Campaigns */
        'Charitable_Campaign'                       =>  'campaigns/class-charitable-campaign.php',
        'Charitable_Campaigns'                      =>  'campaigns/class-charitable-campaigns.php',

        /* Currency */
        'Charitable_Currency'                       =>  'currency/class-charitable-currency.php',

         /* Database */
        'Charitable_Campaign_Donations_DB'          =>  'db/class-charitable-campaign-donations-db.php',
        'Charitable_Donors_DB'                      =>  'db/class-charitable-donors-db.php',

        /* Donations */    
        'Charitable_Donation_Processor'             =>  'donations/class-charitable-donation-processor.php',
        'Charitable_Donation'                       =>  'donations/class-charitable-donation.php',
        'Charitable_Donation_Factory'               =>  'donations/class-charitable-donation-factory.php',
        'Charitable_Donations'                      =>  'donations/class-charitable-donations.php',            
        
        /* Emails */            
        'Charitable_Emails'                         =>  'emails/class-charitable-emails.php',
        'Charitable_Email_Campaign_End'             =>  'emails/class-charitable-email-campaign-end.php',
        'Charitable_Email_Donation_Receipt'         =>  'emails/class-charitable-email-donation-receipt.php',
        'Charitable_Email_Email_Verification'       =>  'emails/class-charitable-email-email-verification.php',
        'Charitable_Email_New_Donation'             =>  'emails/class-charitable-email-new-donation.php',
        'Charitable_Email_Offline_Donation_Notification'   =>  'emails/class-charitable-email-offline-donation-notification.php',
        'Charitable_Email_Offline_Donation_Receipt'         =>  'emails/class-charitable-email-offline-donation-receipt.php',
        'Charitable_Email_Password_Reset'                   =>  'emails/class-charitable-email-password-reset.php',

        /* Email Fields */
        'Charitable_Email_Fields'                   =>  'emails/fields/class-charitable-email-fields.php',
        'Charitable_Email_Fields_Donation'          =>  'emails/fields/class-charitable-email-fields-donation.php',
        'Charitable_Email_Fields_Campaign'          =>  'emails/fields/class-charitable-email-fields-campaign.php',
        'Charitable_Email_Fields_User'              =>  'emails/fields/class-charitable-email-fields-user.php',            

        /* Endpoints */
        'Charitable_Campaign_Endpoint'              =>  'endpoints/class-charitable-campaign-endpoint.php',
        'Charitable_Campaign_Donation_Endpoint'     =>  'endpoints/class-charitable-campaign-donation-endpoint.php',
        'Charitable_Campaign_Widget_Endpoint'       =>  'endpoints/class-charitable-campaign-widget-endpoint.php',
        'Charitable_Donation_Cancellation_Endpoint' =>  'endpoints/class-charitable-donation-cancellation-endpoint.php',
        'Charitable_Donation_Processing_Endpoint'   =>  'endpoints/class-charitable-donation-processing-endpoint.php',
        'Charitable_Donation_Receipt_Endpoint'      =>  'endpoints/class-charitable-donation-receipt-endpoint.php',
        'Charitable_Email_Preview_Endpoint'         =>  'endpoints/class-charitable-email-preview-endpoint.php',
        'Charitable_Email_Verification_Endpoint'    =>  'endpoints/class-charitable-email-verification-endpoint.php',
        'Charitable_Forgot_Password_Endpoint'       =>  'endpoints/class-charitable-forgot-password-endpoint.php',
        'Charitable_Login_Endpoint'                 =>  'endpoints/class-charitable-login-endpoint.php',
        'Charitable_Profile_Endpoint'               =>  'endpoints/class-charitable-profile-endpoint.php',
        'Charitable_Registration_Endpoint'          =>  'endpoints/class-charitable-registration-endpoint.php',
        'Charitable_Reset_Password_Endpoint'        =>  'endpoints/class-charitable-reset-password-endpoint.php',
        'Charitable_Endpoints'                      =>  'endpoints/class-charitable-endpoints.php',

        /* Fields */
        'Charitable_Donation_Field_Registry'        =>  'fields/class-charitable-donation-field-registry.php',
        'Charitable_Donation_Field'                 =>  'fields/class-charitable-donation-field.php',
        'Charitable_Donation_Fields'                =>  'fields/class-charitable-donation-fields.php',

        /* Forms */
        'Charitable_Donation_Form'                  =>  'forms/class-charitable-donation-form.php',
        'Charitable_Donation_Amount_Form'           =>  'forms/class-charitable-donation-amount-form.php',
        'Charitable_Password_Form'                  =>  'forms/class-charitable-forgot-password-form.php',
        'Charitable_Profile_Form'                   =>  'forms/class-charitable-profile-form.php',
        'Charitable_Registration_Form'              =>  'forms/class-charitable-registration-form.php',
        'Charitable_Reset_Password_Form'            =>  'forms/class-charitable-reset-password-form.php',
        'Charitable_Public_Form_View'               =>  'forms/views/class-charitable-public-form-view.php',           

        /* Gateways */
        'Charitable_Gateways'                       =>  'gateways/class-charitable-gateways.php',
        'Charitable_Gateway_Offline'                =>  'gateways/class-charitable-gateway-offline.php',
        'Charitable_Gateway_Paypal'                 =>  'gateways/class-charitable-gateway-paypal.php',

        /* Licensing */
        'Charitable_Licenses'                       =>  'licensing/class-charitable-licenses.php',
        'Charitable_Plugin_Updater'                 =>  'licensing/class-charitable-plugin-updater.php',

        /* Queries */
        'Charitable_Donations_Query'                =>  'queries/class-charitable-donations-query.php',
        'Charitable_Donor_Query'                    =>  'queries/class-charitable-donor-query.php',

        /* Users */
        'Charitable_User'                           =>  'users/class-charitable-user.php',
        'Charitable_Roles'                          =>  'users/class-charitable-roles.php',
        'Charitable_Donor'                          =>  'users/class-charitable-donor.php',

        /* Public */
        'Charitable_Session'                        =>  'public/class-charitable-session.php',
        'Charitable_Template_Helper'                =>  'public/class-charitable-table-helper.php',
        'Charitable_Template'                       =>  'public/class-charitable-template.php',
        'Charitable_Template_Part'                  =>  'public/class-charitable-template-part.php',
        'Charitable_Ghost_Page'                     =>  'public/class-charitable-ghost-page.php',
        'Charitable_User_Dashboard'                 =>  'public/class-charitable-user-dashboard.php',          

        /* Shortcodes */
        'Charitable_Campaigns_Shortcode'             =>  'shortcodes/class-charitable-campaigns-shortcode.php',
        'Charitable_Donors_Shortcode'                =>  'shortcodes/class-charitable-donors-shortcode.php',
        'Charitable_My_Donations_Shortcode'          =>  'shortcodes/class-charitable-my-donations-shortcode.php',
        'Charitable_Donation_Form_Shortcode'         =>  'shortcodes/class-charitable-donation-form-shortcode.php',
        'Charitable_Donation_Receipt_Shortcode'      =>  'shortcodes/class-charitable-donation-receipt-shortcode.php',
        'Charitable_Login_Shortcode'                 =>  'shortcodes/class-charitable-login-shortcode.php',
        'Charitable_Registration_Shortcode'          =>  'shortcodes/class-charitable-registration-shortcode.php',
        'Charitable_Profile_Shortcode'               =>  'shortcodes/class-charitable-profile-shortcode.php',   
        'Charitable_Email_Shortcode'                 =>  'shortcodes/class-charitable-email-shortcode.php',


        /* Widgets */
        'Charitable_Widgets'                         =>  'widgets/class-charitable-widgets.php',
        'Charitable_Campaign_Terms_Widget'           =>  'widgets/class-charitable-campaign-terms-widget.php',
        'Charitable_Campaigns_Widget'                =>  'widgets/class-charitable-campaigns-widget.php',
        'Charitable_Donors_Widget'                   =>  'widgets/class-charitable-donors-widget.php',
        'Charitable_Donate_Widget'                   =>  'widgets/class-charitable-donate-widget.php',
        'Charitable_Donation_Stats_Widget'           =>  'widgets/class-charitable-donation-stats-widget.php',

        /* User Management */
        'Charitable_User_Management'                 =>  'user-management/class-charitable-user-management.php'

    );

    $file_path = isset( $mapping[$class_name] ) ? trailingslashit( __DIR__ ) . $mapping[$class_name] : false;

    if ( $file_path && file_exists( $file_path ) && is_file( $file_path ) ) {
        require_once( $file_path );
    }

    return;
}


