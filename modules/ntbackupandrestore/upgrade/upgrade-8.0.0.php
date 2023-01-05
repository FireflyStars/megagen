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

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once(dirname(__FILE__).'/../autoload.php');

function upgrade_module_8_0_0($module)
{
    $shops = Shop::getShops();
    $cle_cryptage = 'D_T+rW*H`0b84ra.YIen(X|>_Ot&|va;9odG:Gkk3meU=y5kBf3}Yuim';

    // Update string crypt with mcrypt by string crypt by openssl
    foreach ($shops as $shop) {
        if (!Configuration::updateValue('OWNCLOUD_PASS_UPDATE', 0, false, $shop['id_shop_group'], $shop['id_shop'])) {
            return false;
        }

        $old_send_owncloud_value = Configuration::get('SEND_OWNCLOUD', null, $shop['id_shop_group'], $shop['id_shop']);

        if (!Configuration::updateValue('SEND_OWNCLOUD', 0, false, $shop['id_shop_group'], $shop['id_shop'])) {
            return false;
        }

        if (extension_loaded('mcrypt') && extension_loaded('openssl') && function_exists('hash_equals')) {
            $owncloud_old_crypt_pass = Configuration::get(
                'OWNCLOUD_PASS',
                null,
                $shop['id_shop_group'],
                $shop['id_shop']
            );
            $iv_size = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB);
            $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
            $decrypted_string = mcrypt_decrypt(
                MCRYPT_BLOWFISH,
                $cle_cryptage,
                Apparatus::b64_decode($owncloud_old_crypt_pass),
                MCRYPT_MODE_ECB,
                $iv
            );
            $owncloud_old_pass = rtrim($decrypted_string, "\0");
            $owncloud_new_pass = '';

            if (extension_loaded('openssl') && function_exists('hash_equals')) {
                $iv_size    = openssl_cipher_iv_length(NtbrChild::CIPHER_CRYPTAGE);
                $iv         = openssl_random_pseudo_bytes($iv_size);

                $encrypted_string = openssl_encrypt(
                    $owncloud_old_pass,
                    NtbrChild::CIPHER_CRYPTAGE,
                    NtbrChild::CLE_CRYPTAGE,
                    OPENSSL_RAW_DATA,
                    $iv
                );

                if ($encrypted_string !== false) {
                    $hmac = Apparatus::hash_hm('sha256', $encrypted_string, NtbrChild::CLE_CRYPTAGE, true);
                    $owncloud_new_pass = Apparatus::b64_encode($iv.$hmac.$encrypted_string);
                }
            }

            if (Configuration::updateValue(
                'OWNCLOUD_PASS',
                $owncloud_new_pass,
                false,
                $shop['id_shop_group'],
                $shop['id_shop']
            )
            ) {
                if (!Configuration::updateValue(
                    'OWNCLOUD_PASS_UPDATE',
                    1,
                    false,
                    $shop['id_shop_group'],
                    $shop['id_shop']
                )
                ) {
                    return false;
                }
                if (!Configuration::updateValue(
                    'SEND_OWNCLOUD',
                    $old_send_owncloud_value,
                    false,
                    $shop['id_shop_group'],
                    $shop['id_shop']
                )
                ) {
                    return false;
                }
            }
        }

        if (!Configuration::updateValue('NTBR_ADMIN_DIR', '', false, $shop['id_shop_group'], $shop['id_shop'])) {
            return false;
        }

        if (!Configuration::updateValue('NTBR_BIG_WEBSITE', 0, false, $shop['id_shop_group'], $shop['id_shop'])) {
            return false;
        }

        $ftp_pass = Configuration::get('FTP_PASS', null, $shop['id_shop_group'], $shop['id_shop']);
        $ftp_new_pass = '';

        if (extension_loaded('openssl') && function_exists('hash_equals')) {
            $iv_size    = openssl_cipher_iv_length(NtbrChild::CIPHER_CRYPTAGE);
            $iv         = openssl_random_pseudo_bytes($iv_size);

            $encrypted_string = openssl_encrypt(
                $ftp_pass,
                NtbrChild::CIPHER_CRYPTAGE,
                NtbrChild::CLE_CRYPTAGE,
                OPENSSL_RAW_DATA,
                $iv
            );

            if ($encrypted_string !== false) {
                $hmac = Apparatus::hash_hm('sha256', $encrypted_string, NtbrChild::CLE_CRYPTAGE, true);
                $ftp_new_pass = Apparatus::b64_encode($iv.$hmac.$encrypted_string);
            }
        }

        if (!Configuration::updateValue(
            'FTP_PASS',
            $ftp_new_pass,
            false,
            $shop['id_shop_group'],
            $shop['id_shop']
        )
        ) {
            return false;
        }

        if (!Configuration::updateValue('NTBR_BIG_WEBSITE_HIDE', 0, false, $shop['id_shop_group'], $shop['id_shop'])) {
            return false;
        }

        if (!Configuration::updateValue('SEND_RESTORE', 0, false, $shop['id_shop_group'], $shop['id_shop'])) {
            return false;
        }

        if (!Configuration::updateValue('FTP_SSL', 0, false, $shop['id_shop_group'], $shop['id_shop'])) {
            return false;
        }

        if (!Configuration::updateValue('FTP_PASV', 0, false, $shop['id_shop_group'], $shop['id_shop'])) {
            return false;
        }
    }

    return $module;
}
