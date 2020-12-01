<?php
namespace LastPageRedirect;

class Checker
{
    public 
        $redirects;

    public function __construct()
    {
        if ( is_admin() ) return false;

        $this->redirects = array();
        $this->get_redirects();

        if ( count( $this->redirects ) === 0 ) return false;
        add_action( 'wp_enqueue_scripts', [ $this, 'set_markup' ] );
    }

    private function get_redirects()
    {
        global $wpdb;
        $results = $wpdb->get_results( "SELECT * FROM `{$wpdb->prefix}last_page_redirect`" );

        for ($i = 0; $i < count( $results ); $i++ ) {
            $this->redirects[] = new Redirect( $results[$i] );
        }
    }

    function set_markup() {
        $markup = <<<EOD
            <script type="text/javascript" id="last-page-redirect-js">
                (function() {
                    var lastPageRedirects = [
EOD;

            for ( $i = 0; $i < count( $this->redirects ); $i++ ) {
                $redirect = $this->redirects[$i];
                if ( $i !== 0 ) $markup .= ', '; 
        $markup .= <<<EOD

                        {
                            "id":           $redirect->id,
                            "referal_url":  "$redirect->referal_url",
                            "operator":     "$redirect->operator"
                        }
EOD;

            }

        $markup .= <<<EOD

                    ];

                    var domain = window.location.protocol + '//' + window.location.hostname;
                    var lastPage__referrer = document.referrer;
                    var lastPage__redirect = readCookie('last-page-redirect');
                    setCookie('last-page-redirect', window.location.toString(), 1);
EOD;

        if ( !is_user_logged_in() ) {
            $markup .= <<<EOD
                    window.location.href = '/wp-login.php?redirect_to=' + window.location.href;
                    return false;
EOD;
        }

        $markup .= <<<EOD

                    if (!lastPage__redirect) return false;
                    if (lastPage__redirect ===  readCookie('last-page-redirect')) return false;

                    for (var i = 0; i < lastPageRedirects.length; i++) {
                        var redirect = lastPageRedirects[i];

                        if (redirect.referal_url !== domain) {
                            switch (redirect.operator) {
                                case 'contains':
                                    if (lastPage__referrer.indexOf(redirect.referal_url) >= 0) {
                                        window.location.href = lastPage__redirect;
                                        return true;
                                    }
                                    break;
                            
                                case 'exact match':
                                    if (lastPage__referrer === redirect.referal_url) {
                                        window.location.href = lastPage__redirect;
                                        return true;
                                    }
                                    break;

                                default:
                                    break;
                            }
                        }
                    }

                    function readCookie(a) {
                        var b = document.cookie.match('(^|[^;]+)\\s*' + a + '\\s*=\\s*([^;]+)');
                        return b ? b.pop() : '';
                    }

                    function setCookie(cname, cvalue, exdays) {
                        var d = new Date();
                        d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
                        var expires = "expires=" + d.toUTCString();
                        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
                    }
                })();
            </script>
EOD;
        echo $markup;
    }
}
