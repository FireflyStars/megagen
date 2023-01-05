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

class Hubic extends ObjectModel
{
    /** @var    integer     id_ntbr_config */
    public $id_ntbr_config;

    /** @var    boolean     active */
    public $active;

    /** @var    String      name */
    public $name;

    /** @var    integer     config_nb_backup */
    public $config_nb_backup;

    /** @var    String      directory */
    public $directory;

    /** @var    String      token */
    public $token;

    /** @var    String      credential */
    public $credential;

    /** @var    String      date_add */
    public $date_add;

    /** @var    String      date_upd */
    public $date_upd;

/**********************************************************/

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table'             => 'ntbr_hubic',
        'primary'           => 'id_ntbr_hubic',
        'multilang'         => false,
        'multilang_shop'    => false,
        'fields'            => array(
            'id_ntbr_config'    =>  array(
                'type'      => self::TYPE_INT,
                'validate'  => 'isUnsignedInt',
            ),
            'active'                =>  array(
                'type'      => self::TYPE_BOOL,
                'validate'  => 'isBool',
                'default'   => '0',
            ),
            'name'                  =>  array(
                'type'      => self::TYPE_STRING,
                'validate'  => 'isGenericName',
                'size'      => 255,
                'required'  => true,
                'default'   => 'hubiC',
            ),
            'config_nb_backup'      =>  array(
                'type'      => self::TYPE_INT,
                'validate'  => 'isUnsignedInt',
                'default'   => '0',
            ),
            'directory'             =>  array(
                'type'      => self::TYPE_STRING,
                'validate'  => 'isString',
                'size'      => 255,
                'default'   => '',
            ),
            'token'                 =>  array(
                'type'      => self::TYPE_STRING,
                'validate'  => 'isString',
                'required'  => true,
                'default'   => '',
            ),
            'credential'      =>  array(
                'type'      => self::TYPE_STRING,
                'validate'  => 'isString',
                'required'  => true,
                'default'   => '',
            ),
            'date_add'              =>  array(
                'type'      => self::TYPE_DATE,
                'validate'  => 'isDate',
            ),
            'date_upd'              =>  array(
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
     * Get a list of all hubiC accounts
     *
     * @return  array   List of all hubiC accounts
     */
    public static function getListHubicAccounts($id_ntbr_config)
    {
        $hubic_accounts = Db::getInstance()->executeS('
            SELECT `id_ntbr_hubic`, `active`, `name`, `config_nb_backup`, `directory`, `token`, `credential`
            FROM `'._DB_PREFIX_.'ntbr_hubic`
            WHERE `id_ntbr_config` = '.(int)$id_ntbr_config.'
            ORDER BY `date_upd` DESC
        ');

        if (!is_array($hubic_accounts)) {
            return array();
        }

        return $hubic_accounts;
    }

    /**
     * Get a list of all active hubiC accounts
     *
     * @return  array   List of all active hubiC accounts
     */
    public static function getListActiveHubicAccounts($id_ntbr_config)
    {
        $hubic_accounts = Db::getInstance()->executeS('
            SELECT `id_ntbr_hubic`, `active`, `name`, `config_nb_backup`, `directory`, `token`, `credential`
            FROM `'._DB_PREFIX_.'ntbr_hubic`
            WHERE `active` = 1
            AND `id_ntbr_config` = '.(int)$id_ntbr_config.'
            ORDER BY `name`
        ');

        if (!is_array($hubic_accounts)) {
            return array();
        }

        return $hubic_accounts;
    }

    /**
     * Get nb hubiC active accounts
     *
     * @return  integer Nb active accounts
     */
    public static function getNbAccountsActive($id_ntbr_config)
    {
        return (int)Db::getInstance()->getValue('
            SELECT count(`id_ntbr_hubic`)
            FROM `'._DB_PREFIX_.'ntbr_hubic`
            WHERE `id_ntbr_config` = '.(int)$id_ntbr_config.'
            AND `active` = 1
        ');
    }

    /**
     * Get hubiC account data by ID
     *
     * @param   integer     $id_ntbr_hubic  ID of the hubiC account
     *
     * @return  array                       Data of the account
     */
    public static function getHubicAccountById($id_ntbr_hubic)
    {
        $hubic_account = Db::getInstance()->getRow('
            SELECT `id_ntbr_hubic`, `active`, `name`, `config_nb_backup`, `directory`, `token`, `credential`
            FROM `'._DB_PREFIX_.'ntbr_hubic`
            WHERE `id_ntbr_hubic` = '.(int)$id_ntbr_hubic.'
        ');

        if (!is_array($hubic_account)) {
            return array();
        }

        return $hubic_account;
    }

    /**
     * Get hubiC account token by ID
     *
     * @param   integer     $id_ntbr_hubic  ID of the hubiC account
     *
     * @return  String                      Token of the account
     */
    public static function getHubicTokenById($id_ntbr_hubic)
    {
        return Db::getInstance()->getValue('
            SELECT `token`
            FROM `'._DB_PREFIX_.'ntbr_hubic`
            WHERE `id_ntbr_hubic` = '.(int)$id_ntbr_hubic.'
        ');
    }

    /**
     * Get hubiC account credential by ID
     *
     * @param   integer     $id_ntbr_hubic  ID of the hubiC account
     *
     * @return  String                      Credential of the account
     */
    public static function getHubicCredentialById($id_ntbr_hubic)
    {
        return Db::getInstance()->getValue('
            SELECT `credential`
            FROM `'._DB_PREFIX_.'ntbr_hubic`
            WHERE `id_ntbr_hubic` = '.(int)$id_ntbr_hubic.'
        ');
    }

    /**
     * Get hubiC account connection infos (token and credential) by ID
     *
     * @param   integer     $id_ntbr_hubic  ID of the hubiC account
     *
     * @return  array                       Token and credential of the account
     */
    public static function getHubicConnectionInfosById($id_ntbr_hubic)
    {
        return Db::getInstance()->getRow('
            SELECT `token`, `credential`
            FROM `'._DB_PREFIX_.'ntbr_hubic`
            WHERE `id_ntbr_hubic` = '.(int)$id_ntbr_hubic.'
        ');
    }

    /**
     * Get hubiC account ID by name
     *
     * @param   integer     $id_ntbr_config     ID of the configuration
     * @param   String      $name   Name of the hubiC account
     *
     * @return  String          ID of the account
     */
    public static function getIdByName($id_ntbr_config, $name)
    {
        return (int)Db::getInstance()->getValue('
            SELECT `id_ntbr_hubic`
            FROM `'._DB_PREFIX_.'ntbr_hubic`
            WHERE `name` = "'.pSQL($name).'"
            AND `id_ntbr_config` = '.(int)$id_ntbr_config.'
        ');
    }

    /**
     * Get nb hubiC accounts
     *
     * @return  integer Nb accounts
     */
    public static function getNbAccounts()
    {
        return (int)Db::getInstance()->getValue('
            SELECT count(`id_ntbr_hubic`)
            FROM `'._DB_PREFIX_.'ntbr_hubic`
        ');
    }

    /**
     * Deactive all hubiC accounts
     *
     * @return  boolean     Success or failure of the operation
     */
    public static function deactiveAllHubic()
    {
        return Db::getInstance()->execute('
            UPDATE `'._DB_PREFIX_.'ntbr_hubic`
            SET `active` = 0
        ');
    }
}
