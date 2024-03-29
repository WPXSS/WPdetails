<?php 
function wcds_get_file_version( $file ) 
{
	if ( ! file_exists( $file ) ) {
		return '';
	}
$fp = fopen( $file, 'r' );
$file_data = fread( $fp, 8192 );
fclose( $fp );
$file_data = str_replace( "\r", "\n", $file_data );
	$version   = '';

	if ( preg_match( '/^[ \t\/*#@]*' . preg_quote( '@version', '/' ) . '(.*)$/mi', $file_data, $match ) && $match[1] )
		$version = _cleanup_header_comment( $match[1] );

	return $version ;
	}
function wcds_get_woo_version_number() 
{
	if ( ! function_exists( 'get_plugins' ) )
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	$plugin_folder = get_plugins( '/' . 'woocommerce' );
	$plugin_file = 'woocommerce.php';
if ( isset( $plugin_folder[$plugin_file]['Version'] ) ) {
		return $plugin_folder[$plugin_file]['Version'];

	} else {
	return NULL;
	}
}
$wcds_result = get_option("_".$wcds_id);
//$wcds_notice = !$wcds_result || $wcds_result != md5($_SERVER['SERVER_NAME']);
$wcds_notice = false;
/* if($wcds_notice)
	remove_action( 'plugins_loaded', 'wcds_setup'); */
if(!$wcds_notice)
	wcds_setup();
if( !function_exists('apache_request_headers') ) 
{
    function wcds_apache_request_headers() {
        $arh = array();
        $rx_http = '/\AHTTP_/';

        foreach($_SERVER as $key => $val) {
            if( preg_match($rx_http, $key) ) {
                $arh_key = preg_replace($rx_http, '', $key);
                $rx_matches = array();
           // do some nasty string manipulations to restore the original letter case
           // this should work in most cases
                $rx_matches = explode('_', $arh_key);

                if( count($rx_matches) > 0 and strlen($arh_key) > 2 ) {
                    foreach($rx_matches as $ak_key => $ak_val) {
                        $rx_matches[$ak_key] = ucfirst($ak_val);
                    }

                    $arh_key = implode('-', $rx_matches);
                }

                $arh[$arh_key] = $val;
            }
        }

        return( $arh );
    }
}
?>