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

class Onedrive extends ObjectModel
{
    /** @var    integer     id_ntbr_config */
    public $id_ntbr_config;

    /** @var    boolean     active */
    public $active;

    /** @var    String      name */
    public $name;

    /** @var    integer     config_nb_backup */
    public $config_nb_backup;

    /** @var    String      directory_key */
    public $directory_key;

    /** @var    String      directory_path */
    public $directory_path;

    /** @var    String      token */
    public $token;

    /** @var    String      date_add */
    public $date_add;

    /** @var    String      date_upd */
    public $date_upd;

/**********************************************************/

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table'             => 'ntbr_onedrive',
        'primary'           => 'id_ntbr_onedrive',
        'multilang'         => false,
        'multilang_shop'    => false,
        'fields'            => array(
            'id_ntbr_config'    =>  array(
                'type'      => self::TYPE_INT,
                'validate'  => 'isUnsignedInt',
            ),
            'active'            =>  array(
                'type'      => self::TYPE_BOOL,
                'validate'  => 'isBool',
                'default'   => '0',
            ),
            'name'              =>  array(
                'type'      => self::TYPE_STRING,
                'validate'  => 'isGenericName',
                'size'      => 255,
                'required'  => true,
                'default'   => 'OneDrive',
            ),
            'config_nb_backup'  =>  array(
                'type'      => self::TYPE_INT,
                'validate'  => 'isUnsignedInt',
                'default'   => '0',
            ),
            'directory_key'     =>  array(
                'type'      => self::TYPE_STRING,
                'validate'  => 'isString',
                'size'      => 255,
                'required'  => true,
                'default'   => '',
            ),
            'directory_path'    =>  array(
                'type'      => self::TYPE_STRING,
                'validate'  => 'isString',
                'size'      => 255,
                'default'   => '',
            ),
            'token'             =>  array(
                'type'      => self::TYPE_STRING,
                'validate'  => 'isString',
                'required'  => true,
                'default'   => '',
            ),
            'date_add'          =>  array(
                'type'      => self::TYPE_DATE,
                'validate'  => 'isDate',
            ),
            'date_upd'          =>  array(
                'type'      => self::TYPE_DATE,
                'validate'  => 'isDate',
            ),
        )
    );

    /**
     * Get the default values
     *
     * @return  array   Default values
     */
    public static function getDefaultValues()
    {
        $default_values = array();

        $default_values[self::$definition['primary']] = 0;
        $default_values['nb_account'] = self::getNbAccounts() + 1;

        foreach (self::$definition['fields'] as $name => $field) {
            if (isset($field['default'])) {
                $default_values[$name] = $field['default'];
            }
        }

        return $default_values;
    }

    /**
     * Get a list of all OneDrive accounts
     *
     * @return  array   List of all OneDrive accounts
     */
    public static function getListOnedriveAccounts($id_ntbr_config)
    {
        $onedrive_accounts = Db::getInstance()->executeS('
            SELECT `id_ntbr_onedrive`, `active`, `name`, `config_nb_backup`, `directory_key`, `directory_path`, `token`
            FROM `'._DB_PREFIX_.'ntbr_onedrive`
            WHERE `id_ntbr_config` = '.(int)$id_ntbr_config.'
            ORDER BY `date_upd` DESC
        ');

        if (!is_array($onedrive_accounts)) {
            return array();
        }

        return $onedrive_accounts;
    }

    /**
     * Get a list of all active OneDrive accounts
     *
     * @return  array   List of all active OneDrive accounts
     */
    public static function getListActiveOnedriveAccounts($id_ntbr_config)
    {
        $onedrive_accounts = Db::getInstance()->executeS('
            SELECT `id_ntbr_onedrive`, `active`, `name`, `config_nb_backup`, `directory_key`, `directory_path`, `token`
            FROM `'._DB_PREFIX_.'ntbr_onedrive`
            WHERE `active` = 1
            AND `id_ntbr_config` = '.(int)$id_ntbr_config.'
            ORDER BY `name`
        ');

        if (!is_array($onedrive_accounts)) {
            return array();
        }

        return $onedrive_accounts;
    }

    /**
     * Get nb OneDrive active accounts
     *
     * @return  integer Nb active accounts
     */
    public static function getNbAccountsActive($id_ntbr_config)
    {
        return (int)Db::getInstance()->getValue('
            SELECT count(`id_ntbr_onedrive`)
            FROM `'._DB_PREFIX_.'ntbr_onedrive`
            WHERE `id_ntbr_config` = '.(int)$id_ntbr_config.'
            AND `active` = 1
        ');
    }

    /**
     * Get OneDrive account data by ID
     *
     * @param   integer     $id_ntbr_onedrive   ID of the OneDrive account
     *
     * @return  array                           Data of the account
     */
    public static function getOnedriveAccountById($id_ntbr_onedrive)
    {
        $onedrive_account = Db::getInstance()->getRow('
            SELECT `id_ntbr_onedrive`, `active`, `name`, `config_nb_backup`, `directory_key`, `directory_path`, `token`
            FROM `'._DB_PREFIX_.'ntbr_onedrive`
            WHERE `id_ntbr_onedrive` = '.(int)$id_ntbr_onedrive.'
        ');

        if (!is_array($onedrive_account)) {
            return array();
        }

        return $onedrive_account;
    }

    /**
     * Get OneDrive account token by ID
     *
     * @param   integer     $id_ntbr_onedrive   ID of the OneDrive account
     *
     * @return  String                          Token of the account
     */
    public static function getOnedriveTokenById($id_ntbr_onedrive)
    {
        return Db::getInstance()->getValue('
            SELECT `token`
            FROM `'._DB_PREFIX_.'ntbr_onedrive`
            WHERE `id_ntbr_onedrive` = '.(int)$id_ntbr_onedrive.'
        ');
    }

    /**
     * Get OneDrive account ID by name
     *
     * @param   integer     $id_ntbr_config     ID of the configuration
     * @param   String      $name               Name of the OneDrive account
     *
     * @return  integer             ID of the account
     */
    public static function getIdByName($id_ntbr_config, $name)
    {
        return (int)Db::getInstance()->getValue('
            SELECT `id_ntbr_onedrive`
            FROM `'._DB_PREFIX_.'ntbr_onedrive`
            WHERE `name` = "'.pSQL($name).'"
            AND `id_ntbr_config` = '.(int)$id_ntbr_config.'
        ');
    }

    /**
     * Get nb OneDrive accounts
     *
     * @return  integer Nb accounts
     */
    public static function getNbAccounts()
    {
        return (int)Db::getInstance()->getValue('
            SELECT count(`id_ntbr_onedrive`)
            FROM `'._DB_PREFIX_.'ntbr_onedrive`
        ');
    }

    /**
     * Deactive all OneDrive accounts
     *
     * @return  boolean     Success or failure of the operation
     */
    public static function deactiveAllOnedrive()
    {
        return Db::getInstance()->execute('
            UPDATE `'._DB_PREFIX_.'ntbr_onedrive`
            SET `active` = 0
        ');
    }
}
