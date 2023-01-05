<?php
/**
* 2013-2020 2N Technologies
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to contact@2n-tech.com so we can send you a copy immediately.
*
* @author    2N Technologies <contact@2n-tech.com>
* @copyright 2013-2020 2N Technologies
* @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*/

class Apparatus
{
    public function __construct()
    {

    }

    /**
     * Encodes data with MIME base64
     *
     * @param   String  $data   The data to encode.
     *
     * @return  String          The encoded data, as a string.
     */
    public static function b64_encode($data)
    {
        return base64_encode($data);
    }

    /**
     * Decodes data encoded with MIME base64
     *
     * @param   String  $data   The encoded data
     * @param   bool    $strict If the strict parameter is set to TRUE then the base64_decode() function will return FALSE if the input contains character from outside the base64 alphabet. Otherwise invalid characters will be silently discarded.
     *
     * @return  String          The decoded data or FALSE on failure. The returned data may be binary.
     */
    public static function b64_decode($data, $strict = false)
    {
        return base64_decode($data, $strict);
    }

    /**
     * Generate a keyed hash value using the HMAC method
     *
     * @param   String  $algo       Name of selected hashing algorithm (i.e. "md5", "sha256", "haval160,4", etc..) See hash_hmac_algos() for a list of supported algorithms.
     * @param   String  $data       Message to be hashed.
     * @param   String  $key        Shared secret key used for generating the HMAC variant of the message digest.
     * @param   bool    $raw_output When set to TRUE, outputs raw binary data. FALSE outputs lowercase hexits.
     *
     * @return  String          The String decoded
     */
    public static function hash_hm($algo, $data, $key, $raw_output = false)
    {
        return hash_hmac($algo, $data, $key, $raw_output);
    }

    /**
     * Opens a bzip2 compressed file
     *
     * @param   mixed   $file   The name of the file to open, or an existing stream resource.
     * @param   String  $mode   The modes 'r' (read), and 'w' (write) are supported. Everything else will cause bzopen() to return FALSE.
     *
     * @return  resource    If the open fails, bzopen() returns FALSE, otherwise it returns a pointer to the newly opened file.
     */
    public static function bz_open($file, $mode)
    {
        return bzopen($file, $mode);
    }

    /**
     * Close a bzip2 file
     *
     * @param   resource   $bz  The file pointer. It must be valid and must point to a file successfully opened by bzopen().
     *
     * @return  bool    Returns TRUE on success or FALSE on failure.
     */
    public static function bz_close($bz)
    {
        return bzclose($bz);
    }

    /**
     * Binary safe bzip2 file write
     *
     * @param   resource    $bz     The file pointer. It must be valid and must point to a file successfully opened by bzopen().
     * @param   string      $data   The written data.
     * @param   int         $length If supplied, writing will stop after length (uncompressed) bytes have been written or the end of data is reached, whichever comes first.
     *
     * @return  int Returns the number of bytes written, or FALSE on error.
     */
    public static function bz_write($bz, $data, $length = false)
    {
        if ($length) {
            return bzwrite($bz, $data, $length);
        }

        return bzwrite($bz, $data);
    }

    /**
     *  Add two arbitrary precision numbers
     *
     * @param   string  $left_operand
     * @param   string  $right_operand
     * @param   int     $scale
     *
     * @return  String  The sum of the two operands, as a string.
     */
    public static function bc_add($left_operand, $right_operand, $scale = 0)
    {
        return bcadd($left_operand, $right_operand, $scale);
    }

    /**
     *  Subtract one arbitrary precision number from another
     *
     * @param   string  $left_operand
     * @param   string  $right_operand
     * @param   int     $scale
     *
     * @return  String  The result of the subtraction, as a string.
     */
    public static function bc_sub($left_operand, $right_operand, $scale = 0)
    {
        return bcsub($left_operand, $right_operand, $scale);
    }

    /**
     * Get all values from $_POST/$_GET.
     *
     * @return mixed
     */
    public static function getAllValues()
    {
        return $_POST + $_GET;
    }

    /**
     * Decodes a hexadecimally encoded binary string
     *
     * @param   string  $hexa
     *
     * @return binary or false
     */
    public static function hex2bin($hexa) {
        if (!function_exists('hex2bin')) {
            $out = '';
            for ($c = 0; $c < strlen($hexa); $c += 2) {
                $out .= chr(hexdec($hexa[$c].$hexa[$c+1]));
            }
            return (string) $out;
        } else {
            return hex2bin($hexa);
        }
    }

    /**
     * Timing attack safe string comparison
     *
     * @param String $known_string  The string of known length to compare against
     * @param String $user_string   The user-supplied string
     *
     * @return boolean
     */
    public static function hash_equals($known_string, $user_string) {
        if(!function_exists('hash_equals')) {
            if (strlen($known_string) != strlen($user_string)) {
                return false;
            } else {
                $res = $known_string ^ $user_string;
                $ret = 0;
                for($i = strlen($res) - 1; $i >= 0; $i--) $ret |= ord($res[$i]);
                return !$ret;
            }
        } else {
            return hash_equals($known_string, $user_string);
        }
    }

    /**
     * Return part of a string
     *
     * @param string    The input string
     * @param int       Start position
     * @param int       Length characters
     *
     * @return string   Extracted part of string; or FALSE on failure, or an empty string.
     */
    public static function substr($string, $start, $length = 0) {
        if (!$length) {
            return substr($string, $start);
        }

        return substr($string, $start, $length);
    }
}
