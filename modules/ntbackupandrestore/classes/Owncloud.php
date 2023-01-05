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

class Owncloud extends ObjectModel
{
    /** @var    integer     id_ntbr_config */
    public $id_ntbr_config;

    /** @var    boolean     active */
    public $active;

    /** @var    String      name */
    public $name;

    /** @var    integer     config_nb_backup */
    public $config_nb_backup;

    /** @var    String      login */
    public $login;

    /** @var    String      password */
    public $password;

    /** @var    String      server */
    public $server;

    /** @var    String      directory */
    public $directory;

    /** @var    String      date_add */
    public $date_add;

    /** @var    String      date_upd */
    public $date_upd;

/**********************************************************/

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table'             => 'ntbr_owncloud',
        'primary'           => 'id_ntbr_owncloud',
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
                'default'   => 'ownCloud',
            ),
            'config_nb_backup'  =>  array(
                'type'      => self::TYPE_INT,
                'validate'  => 'isUnsignedInt',
                'default'   => '0',
            ),
            'login'             =>  array(
                'type'      => self::TYPE_STRING,
                'validate'  => 'isString',
                'size'      => 255,
                'required'  => true,
                'default'   => '',
            ),
            'password'          =>  array(
                'type'      => self::TYPE_STRING,
                'validate'  => 'isString',
                'size'      => 255,
                'required'  => true,
                'default'   => '',
            ),
            'server'            =>  array(
                'type'      => self::TYPE_STRING,
                'validate'  => 'isString',
                'size'      => 255,
                'required'  => true,
                'default'   => '',
            ),
            'directory'         =>  array(
                'type'      => self::TYPE_STRING,
                'validate'  => 'isString',
                'size'      => 255,
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

    public function save($null_values = false, $autodate = true)
    {
        $this->directory = str_replace(' ', '_', $this->directory);

        return parent::save($null_values, $autodate);
    }

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
     * Get a list of all ownCloud accounts
     *
     * @return  array   List of all ownCloud accounts
     */
    public static function getListOwncloudAccounts($id_ntbr_config)
    {
        $owncloud_accounts = Db::getInstance()->executeS('
            SELECT `id_ntbr_owncloud`, `active`, `name`, `config_nb_backup`, `login`, `password`, `server`, `directory`
            FROM `'._DB_PREFIX_.'ntbr_owncloud`
            WHERE `id_ntbr_config` = '.(int)$id_ntbr_config.'
            ORDER BY `date_upd` DESC
        ');

        if (!is_array($owncloud_accounts)) {
            return array();
        }

        return $owncloud_accounts;
    }

    /**
     * Get a list of all active ownCloud accounts
     *
     * @return  array   List of all active ownCloud accounts
     */
    public static function getListActiveOwncloudAccounts($id_ntbr_config)
    {
        $owncloud_accounts = Db::getInstance()->executeS('
            SELECT `id_ntbr_owncloud`, `active`, `name`, `config_nb_backup`, `login`, `password`, `server`, `directory`
            FROM `'._DB_PREFIX_.'ntbr_owncloud`
            WHERE `active` = 1
            AND `id_ntbr_config` = '.(int)$id_ntbr_config.'
            ORDER BY `name`
        ');

        if (!is_array($owncloud_accounts)) {
            return array();
        }

        return $owncloud_accounts;
    }

    /**
     * Get nb ownCloud active accounts
     *
     * @return  integer Nb active accounts
     */
    public static function getNbAccountsActive($id_ntbr_config)
    {
        return (int)Db::getInstance()->getValue('
            SELECT count(`id_ntbr_owncloud`)
            FROM `'._DB_PREFIX_.'ntbr_owncloud`
            WHERE `id_ntbr_config` = '.(int)$id_ntbr_config.'
            AND `active` = 1
        ');
    }

    /**
     * Get ownCloud account data by ID
     *
     * @param   integer     $id_ntbr_owncloud   ID of the ownCloud account
     *
     * @return  array                           Data of the account
     */
    public static function getOwncloudAccountById($id_ntbr_owncloud)
    {
        $owncloud_account = Db::getInstance()->getRow('
            SELECT `id_ntbr_owncloud`, `active`, `name`, `config_nb_backup`, `login`, `password`, `server`, `directory`
            FROM `'._DB_PREFIX_.'ntbr_owncloud`
            WHERE `id_ntbr_owncloud` = '.(int)$id_ntbr_owncloud.'
        ');

        if (!is_array($owncloud_account)) {
            return array();
        }

        return $owncloud_account;
    }

    /**
     * Get ownCloud account ID by name
     *
     * @param   integer     $id_ntbr_config     ID of the configuration
     * @param   String      $name               Name of the ownCloud account
     *
     * @return  integer             ID of the account
     */
    public static function getIdByName($id_ntbr_config, $name)
    {
        return (int)Db::getInstance()->getValue('
            SELECT `id_ntbr_owncloud`
            FROM `'._DB_PREFIX_.'ntbr_owncloud`
            WHERE `name` = "'.pSQL($name).'"
            AND `id_ntbr_config` = '.(int)$id_ntbr_config.'
        ');
    }

    /**
     * Get nb ownCloud accounts
     *
     * @return  integer Nb accounts
     */
    public static function getNbAccounts()
    {
        return (int)Db::getInstance()->getValue('
            SELECT count(`id_ntbr_owncloud`)
            FROM `'._DB_PREFIX_.'ntbr_owncloud`
        ');
    }

    /**
     * Deactive all ownCloud accounts
     *
     * @return  boolean     Success or failure of the operation
     */
    public static function deactiveAllOwncloud()
    {
        return Db::getInstance()->execute('
            UPDATE `'._DB_PREFIX_.'ntbr_owncloud`
            SET `active` = 0
        ');
    }
}
