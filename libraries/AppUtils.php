<?php
/**
 * Created by PhpStorm.
 * User: Jeff
 * library for re-usable utility functions NOT found in /core/libraries/Utils.php
 * All methods should be static, accessed like: AppUtils::method(...);
*/

class AppUtils {

    /* ----------------------------------------------------------------------------------------------
     * Validate an email address.
     *  Provide email address (raw input)
     *  Returns true if the email address has the email format
     *  and the domain exists.
     *
     * Source: http://www.linuxjournal.com/article/9585?page=0,3
     *
     * Other option: user web svc, e.g. http://www.email-validator.net
     * ---------------------------------------------------------------------------------------------- */
    public static function valid_email($email) {
        $isValid = true;
        $atIndex = strrpos($email, "@");
        if (is_bool($atIndex) && !$atIndex) {
            $isValid = false;

        } else {
            $domain = substr($email, $atIndex+1);
            $local = substr($email, 0, $atIndex);
            $localLen = strlen($local);
            $domainLen = strlen($domain);
            if ($localLen < 1 || $localLen > 64) {
                // local part length exceeded
                $isValid = false;
            } else if ($domainLen < 1 || $domainLen > 255) {
                // domain part length exceeded
                $isValid = false;
            } else if ($local[0] == '.' || $local[$localLen-1] == '.') {
                // local part starts or ends with '.'
                $isValid = false;
            } else if (preg_match('/\\.\\./', $local)) {
                // local part has two consecutive dots
                $isValid = false;
            } else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain)) {
                // character not valid in domain part
                $isValid = false;
            } else if (preg_match('/\\.\\./', $domain)) {
                // domain part has two consecutive dots
                $isValid = false;
            } else if (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace("\\\\","",$local))) {
                // character not valid in local part unless
                // local part is quoted
                if (!preg_match('/^"(\\\\"|[^"])+"$/', str_replace("\\\\","",$local))) {
                    $isValid = false;
                }
            }
            if ($isValid && !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A"))) {
                // domain not found in DNS
                $isValid = false;
            }
        }
        return $isValid;

    } # End of valid_email()

    /* ----------------------------------------------------------------------------------------------
     * Little routine to concatenate router passed messages -
     * - concatenates messages in reverse order (pushed to top)
     * ---------------------------------------------------------------------------------------------- */
     public static function push_message($msg, $new_msg) {

         $msg = trim($msg);
         $new_msg = trim($new_msg);
         if (empty($msg)) {
             # must be first item added
             $msg = $new_msg;
         } else {
             $msg = $new_msg . "&" . $msg;
         }

         return $msg;
     }

    /* ----------------------------------------------------------------------------------------------
     * Little routine to unstring router passed messages -
     * - pops messages off front of string
     * ---------------------------------------------------------------------------------------------- */
    public static function pop_message($msg) {

        $msg_stack = Array();

        $msg = trim($msg);

        if (!empty($msg)) {
            $pos = strpos($msg, "&", 0);

            if ($pos == 0) {
                $msg_stack[] = $msg;
            } else {
                $start = 0;

                while ($pos > 0) {
                    $msg_stack[] = substr($msg, $start, ($pos - $start));
                    $start = $pos + 1;
                    $pos = strpos($msg, "&", $start);
                }

                $msg_stack[] = substr($msg, ($start), (strlen($msg) - $start) );
            }
        }

       return $msg_stack;
    }


} #eoc