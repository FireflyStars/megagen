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

function upgrade_module_11_2_4($module)
{
    $update_aws = Db::getInstance()->execute('
        ALTER TABLE `'._DB_PREFIX_.'ntbr_aws` ADD `storage_class` TEXT NOT NULL AFTER `bucket`;
    ');

    if (!$update_aws) {
        return false;
    }

    return $module;
}
