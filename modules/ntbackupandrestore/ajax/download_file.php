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

require_once(dirname(__FILE__).'/../autoload.php');

$ntbr = new NtbrChild();
$page = 'download_file';

if (!Module::isInstalled($ntbr->name)) {
    echo 'Your module is not installed';
    return false;
}

if (Tools::isSubmit('secure_key') || Tools::getValue('secure_key') != $ntbr->secure_key) {
    $secure_key = Tools::getValue('secure_key');
    $id_shop_group = Tools::getValue('id_shop_group');
    $id_shop = Tools::getValue('id_shop');

    $secure_key_test = hash(
        'sha512',
        $secure_key.$ntbr->secure_key.$ntbr->getConfig('NTBR_SEL', $id_shop_group, $id_shop)
    );
    $secure_key_test_temp = hash(
        'sha512',
        $secure_key.$ntbr->secure_key.$ntbr->getConfig('NTBR_SEL_TEMP', $id_shop_group, $id_shop)
    );

    if ($secure_key_test_temp != $ntbr->getConfig('NTBR_HASH_TEMP', $id_shop_group, $id_shop)
        && $secure_key_test != $ntbr->getConfig('NTBR_HASH', $id_shop_group, $id_shop)
    ) {
        sleep(5); /*Limit brute force*/
        echo $ntbr->l('Forbidden', $page);
        return false;
    }
} else {
    echo $ntbr->l('Forbidden', $page);
    return false;
}

if (Tools::isSubmit('backup')) {
    if (!Tools::isSubmit('nb')) {
        echo $ntbr->l('Error', $page);
        return false;
    }
    $old_backups    = $ntbr->findOldBackups();
    $nb_file        = Tools::getValue('nb');
    $nb_detail      = explode('.', $nb_file);
    $backup         = '';

    if (!isset($nb_detail[0])) {
        $ntbr->log('ERR'.$ntbr->l('Error, the number of the backup is unvalid', $page));
        echo $ntbr->l('Error', $page);
        return false;
    }

    if (!isset($old_backups[$nb_detail[0]])) {
        $ntbr->log('ERR'.$ntbr->l('Error, the backup asked was not found', $page));
        echo $ntbr->l('Error', $page);
        return false;
    }

    if (!isset($old_backups[$nb_detail[0]]['backup_dir']) || !is_dir($old_backups[$nb_detail[0]]['backup_dir'])) {
        $ntbr->log('ERR'.$ntbr->l('Error, the backup directory is unvalid', $page));
        echo $ntbr->l('Error', $page);
        return false;
    }

    $backup_dir = $old_backups[$nb_detail[0]]['backup_dir'];

    // If file is only a part of the backup
    if (isset($nb_detail[1])) {
        if (!isset($old_backups[$nb_detail[0]]['part'][$nb_file]['name'])) {
            $ntbr->log('ERR'.$ntbr->l('Error, the backup part is unvalid', $page));
            echo $ntbr->l('Error', $page);
            return false;
        }

        $backup = $old_backups[$nb_detail[0]]['part'][$nb_file]['name'];
    } else {
        if (!isset($old_backups[$nb_detail[0]]['name'])) {
            $ntbr->log('ERR'.$ntbr->l('Error, the backup is unvalid', $page));
            echo $ntbr->l('Error', $page);
            return false;
        }

        $backup = $old_backups[$nb_detail[0]]['name'];
    }

    $ntbr->downloadFile($backup_dir.$backup, 'application/x-tar');
} elseif (Tools::isSubmit('log')) {
    $log_file = NtBackupAndRestore::getModuleBackupDirectory().'log.txt';
    if (file_exists($log_file)) {
        $ntbr->downloadFile($log_file, 'application/octet-stream');// ou text/plain
    } else {
        echo $ntbr->l('No log file available', $page);
        return false;
    }
} elseif (Tools::isSubmit('restore')) {
    $ntbr->downloadFile(_PS_ROOT_DIR_.'/modules/'.$ntbr->name.'/restore.txt', 'application/octet-stream', 'restore.php');// ou application/octet-stream
} else {
    echo $ntbr->l('Error', $page);
    return false;
}
