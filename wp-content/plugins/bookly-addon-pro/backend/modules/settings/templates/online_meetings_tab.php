<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls\Inputs as ControlsInputs;
use Bookly\Backend\Components\Controls\Buttons;
use Bookly\Backend\Components\Settings\Inputs;
use BooklyPro\Lib;
?>
<div class="tab-pane" id="bookly_settings_online_meetings">
    <form method="post" action="<?php echo esc_url( add_query_arg( 'tab', 'online_meetings' ) ) ?>">
        <div class="card-body">
            <div class="card bookly-collapse">
                <div class="card-header d-flex align-items-center">
                    <a href="#bookly_pmt_locally" class="ml-2" role="button" data-toggle="collapse">
                        Zoom
                    </a>
                    <img class="ml-auto" src="<?php echo plugins_url( 'frontend/resources/images/zoom.png', Lib\Plugin::getMainFile() ) ?>" />
                </div>
                <div id="bookly_pmt_locally" class="collapse show">
                    <div class="card-body">
                        <div class="form-group">
                            <h4><?php esc_html_e( 'Instructions', 'bookly' ) ?></h4>
                            <p><?php esc_html_e( 'To find your API Key and Secret, do the following:', 'bookly' ) ?></p>
                            <ol>
                                <li><?php esc_html_e( 'Sign in to your Zoom account', 'bookly' ) ?></li>
                                <li><?php _e( 'Visit the <a href="https://marketplace.zoom.us/" target="_blank">Zoom App Marketplace</a>', 'bookly' ) ?></li>
                                <li><?php _e( 'Click on the <b>Develop</b> option in the dropdown on the top-right corner and select <b>Build App</b>', 'bookly' ) ?></li>
                                <li><?php _e( 'A page with various app types will be displayed. Select <b>JWT</b> as the app type and click on <b>Create</b>', 'bookly' ) ?></li>
                                <li><?php esc_html_e( 'After creating your app, fill out descriptive and contact information', 'bookly' ) ?></li>
                                <li><?php _e( 'Go to <b>App Credentials</b> tab and look for the <b>API Key</b> and <b>API Secret</b>. Use them in the form below on this page', 'bookly' ) ?></li>
                                <li><?php _e( 'Once you\'ve copied over your API Key and Secret, go to <b>Activation</b> tab and make sure your app is activated', 'bookly' ) ?></li>
                            </ol>
                        </div>
                        <?php Inputs::renderText( 'bookly_zoom_jwt_api_key', __( 'API Key', 'bookly' ), __( 'The API Key obtained from your JWT app', 'bookly' ) ) ?>
                        <?php Inputs::renderText( 'bookly_zoom_jwt_api_secret', __( 'API Secret', 'bookly' ), __( 'The API Secret obtained from your JWT app', 'bookly' ) ) ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer bg-transparent d-flex justify-content-end">
            <?php ControlsInputs::renderCsrf() ?>
            <?php Buttons::renderSubmit() ?>
            <?php Buttons::renderReset( 'bookly-online-meetings-reset', 'ml-2' ) ?>
        </div>
    </form>
</div>