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

class Config extends ObjectModel
{
    /** @var    boolean     is_default */
    public $is_default;

    /** @var    String      name */
    public $name;

    /** @var    String      type_backup */
    public $type_backup;

    /** @var    integer     nb_backup */
    public $nb_backup;

    /** @var    boolean     send_email */
    public $send_email;

    /** @var    boolean     email_only_error */
    public $email_only_error;

    /** @var    String      mail_backup */
    public $mail_backup;

    /** @var    boolean     send_restore */
    public $send_restore;

    /** @var    boolean     activate_log */
    public $activate_log;

    /** @var    integer     part_size */
    public $part_size;

    /** @var    integer     max_file_to_backup */
    public $max_file_to_backup;

    /** @var    integer     dump_max_values */
    public $dump_max_values;

    /** @var    integer     dump_lines_limit */
    public $dump_lines_limit;

    /** @var    boolean     disable_refresh */
    public $disable_refresh;

    /** @var    integer     time_between_refresh */
    public $time_between_refresh;

    /** @var    integer     time_pause_between_refresh */
    public $time_pause_between_refresh;

    /** @var    integer     time_between_progress_refresh */
    public $time_between_progress_refresh;

    /** @var    boolean     disable_server_timeout */
    public $disable_server_timeout;

    /** @var    boolean     increase_server_memory */
    public $increase_server_memory;

    /** @var    integer     server_memory_value */
    public $server_memory_value;

    /** @var    boolean     dump_low_interest_tables */
    public $dump_low_interest_tables;

    /** @var    boolean     maintenance */
    public $maintenance;

    /** @var    integer     time_between_backups */
    public $time_between_backups;

    /** @var    boolean     activate_xsendfile */
    public $activate_xsendfile;

    /** @var    integer     ignore_product_image */
    public $ignore_product_image;

    /** @var    boolean     ignore_compression */
    public $ignore_compression;

    /** @var    boolean     delete_local_backup */
    public $delete_local_backup;

    /** @var    boolean     create_on_distant */
    public $create_on_distant;

    /** @var    boolean     js_download */
    public $js_download;

    /** @var    String      backup_dir */
    public $backup_dir;

    /** @var    String      ignore_directories */
    public $ignore_directories;

    /** @var    String      ignore_file_types */
    public $ignore_file_types;

    /** @var    String      ignore_tables */
    public $ignore_tables;

    /** @var    String      date_add */
    public $date_add;

    /** @var    String      date_upd */
    public $date_upd;

/**********************************************************/

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table'             => 'ntbr_config',
        'primary'           => 'id_ntbr_config',
        'multilang'         => false,
        'multilang_shop'    => false,
        'fields'            => array(
            'is_default'                    =>  array(
                'type'      => self::TYPE_BOOL,
                'validate'  => 'isBool',
                'default'   => '0',
            ),
            'name'                          =>  array(
                'type'      => self::TYPE_STRING,
                'validate'  => 'isGenericName',
                'size'      => 255,
                'required'  => true,
            ),
            'type_backup'                   =>  array(
                'type'      => self::TYPE_STRING,
                'validate'  => 'isGenericName',
                'size'      => 255,
                'required'  => true,
            ),
            'nb_backup'                     =>  array(
                'type'      => self::TYPE_INT,
                'validate'  => 'isUnsignedInt',
            ),
            'send_email'                    =>  array(
                'type'      => self::TYPE_BOOL,
                'validate'  => 'isBool',
                'default'   => '0',
            ),
            'email_only_error'              =>  array(
                'type'      => self::TYPE_BOOL,
                'validate'  => 'isBool',
                'default'   => '0',
            ),
            'mail_backup'                   =>  array(
                'type'      => self::TYPE_STRING,
                'validate'  => 'isGenericName',
                'required'  => true,
            ),
            'send_restore'                  =>  array(
                'type'      => self::TYPE_BOOL,
                'validate'  => 'isBool',
                'default'   => '0',
            ),
            'activate_log'                  =>  array(
                'type'      => self::TYPE_BOOL,
                'validate'  => 'isBool',
                'default'   => '0',
            ),
            'part_size'                     =>  array(
                'type'      => self::TYPE_INT,
                'validate'  => 'isUnsignedInt',
                'default'   => '0',
            ),
            'max_file_to_backup'            =>  array(
                'type'      => self::TYPE_INT,
                'validate'  => 'isUnsignedInt',
                'default'   => '0',
            ),
            'dump_max_values'               =>  array(
                'type'      => self::TYPE_INT,
                'validate'  => 'isUnsignedInt',
                'default'   => NtbrCore::DUMP_MAX_VALUES,
            ),
            'dump_lines_limit'              =>  array(
                'type'      => self::TYPE_INT,
                'validate'  => 'isUnsignedInt',
                'default'   => NtbrCore::DUMP_LINES_LIMIT,
            ),
            'disable_refresh'               =>  array(
                'type'      => self::TYPE_BOOL,
                'validate'  => 'isBool',
                'default'   => '0',
            ),
            'time_between_refresh'          =>  array(
                'type'      => self::TYPE_INT,
                'validate'  => 'isUnsignedInt',
                'default'   => NtbrCore::MAX_TIME_BEFORE_REFRESH,
            ),
            'time_pause_between_refresh'    =>  array(
                'type'      => self::TYPE_INT,
                'validate'  => 'isUnsignedInt',
                'default'   => '0',
            ),
            'time_between_progress_refresh' =>  array(
                'type'      => self::TYPE_INT,
                'validate'  => 'isUnsignedInt',
                'default'   => NtbrCore::MAX_TIME_BEFORE_PROGRESS_REFRESH,
            ),
            'disable_server_timeout'        =>  array(
                'type'      => self::TYPE_BOOL,
                'validate'  => 'isBool',
                'default'   => '0',
            ),
            'increase_server_memory'        =>  array(
                'type'      => self::TYPE_BOOL,
                'validate'  => 'isBool',
                'default'   => '0',
            ),
            'server_memory_value'           =>  array(
                'type'      => self::TYPE_INT,
                'validate'  => 'isUnsignedInt',
                'default'   => NtbrCore::SET_MEMORY_LIMIT,
            ),
            'dump_low_interest_tables'      =>  array(
                'type'      => self::TYPE_BOOL,
                'validate'  => 'isBool',
                'default'   => '0',
            ),
            'maintenance'                   =>  array(
                'type'      => self::TYPE_BOOL,
                'validate'  => 'isBool',
                'default'   => '0',
            ),
            'time_between_backups'          =>  array(
                'type'      => self::TYPE_INT,
                'validate'  => 'isUnsignedInt',
                'default'   => NtbrCore::MIN_TIME_NEW_BACKUP,
            ),
            'activate_xsendfile'            =>  array(
                'type'      => self::TYPE_BOOL,
                'validate'  => 'isBool',
                'default'   => '0',
            ),
            'ignore_product_image'          =>  array(
                'type'      => self::TYPE_INT,
                'validate'  => 'isUnsignedInt',
                'default'   => '0',
            ),
            'ignore_compression'            =>  array(
                'type'      => self::TYPE_BOOL,
                'validate'  => 'isBool',
                'default'   => '0',
            ),
            'delete_local_backup'           =>  array(
                'type'      => self::TYPE_BOOL,
                'validate'  => 'isBool',
                'default'   => '0',
            ),
            'create_on_distant'             =>  array(
                'type'      => self::TYPE_BOOL,
                'validate'  => 'isBool',
                'default'   => '0',
            ),
            'js_download'                   =>  array(
                'type'      => self::TYPE_BOOL,
                'validate'  => 'isBool',
                'default'   => '0',
            ),
            'backup_dir'                    =>  array(
                'type'      => self::TYPE_STRING,
                'validate'  => 'isString',
                'required'  => false,
                'default'   => '',
            ),
            'ignore_directories'            =>  array(
                'type'      => self::TYPE_STRING,
                'validate'  => 'isString',
                'required'  => false,
                'default'   => '',
            ),
            'ignore_file_types'             =>  array(
                'type'      => self::TYPE_STRING,
                'validate'  => 'isString',
                'required'  => false,
                'default'   => '',
            ),
            'ignore_tables'                 =>  array(
                'type'      => self::TYPE_STRING,
                'validate'  => 'isString',
                'required'  => false,
                'default'   => '',
            ),
            'date_add'                      =>  array(
                'type'      => self::TYPE_DATE,
                'validate'  => 'isDate',
                    ),
            'date_upd'                      =>  array(
                'type'      => self::TYPE_DATE,
                'validate'  => 'isDate',
            ),
        )
    );

    public function add($auto_date = true, $null_values = false)
    {
        if (!$this->mail_backup) {
            $this->mail_backup = Configuration::get('PS_SHOP_EMAIL');
        } else {
            $this->mail_backup = preg_replace('/\s/', '', $this->mail_backup);
        }

        if (!$this->backup_dir || !is_dir($this->backup_dir)) {
            $this->backup_dir = NtBackupAndRestore::getModuleBackupDirectory();
        }

        if (!self::getIdDefault()) {
            $this->is_default = 1;
        } elseif ($this->is_default) {
            self::cleanDefault($this->id);
        }

        return parent::add($auto_date, $null_values);
    }

    public function update($null_values = false)
    {
        $id_default = self::getIdDefault();

        if (!$this->mail_backup) {
            $this->mail_backup = Configuration::get('PS_SHOP_EMAIL');
        } else {
            $this->mail_backup = preg_replace('/\s/', '', $this->mail_backup);
        }

        if (!$this->backup_dir || !is_dir($this->backup_dir)) {
            $this->backup_dir = NtBackupAndRestore::getModuleBackupDirectory();
        }

        // If the config was the default one and we try to remove the default attribut, prevent it.
        if ($id_default == $this->id && !$this->is_default) {
            $this->is_default = 1;
        } elseif (!$id_default) {
            // If there is no default config. Force this one to be the default one
            $this->is_default = 1;
        } elseif ($this->is_default) {
            self::cleanDefault($this->id);
        }

        return parent::update($null_values);
    }

    public function delete()
    {
        if ($this->is_default) {
            return false;
        }

        $result         = true;
        $list_accounts  = array(
            'ftp',
            'aws',
            'owncloud',
            'webdav',
            'googledrive',
            'hubic',
            'onedrive',
            'dropbox',
            'sugarsync'
        );

        foreach ($list_accounts as $account) {
            if (!Db::getInstance()->delete('ntbr_'.$account, '`id_ntbr_config` = '.(int)$this->id)) {
                $result = false;
            }
        }

        if ($result) {
            return parent::delete();
        }

        return false;
    }

    /**
     * Get a list of all configs
     *
     * @return  array   List of all configs
     */
    public static function getListConfigs()
    {
        $configs = Db::getInstance()->executeS('
            SELECT
                `id_ntbr_config`, `is_default`, `name`, `type_backup`, `nb_backup`, `send_email`, `email_only_error`,
                `mail_backup`, `send_restore`, `activate_log`, `part_size`, `max_file_to_backup`, `dump_max_values`,
                `dump_lines_limit`, `disable_refresh`, `time_between_refresh`, `time_pause_between_refresh`,
                `time_between_progress_refresh`, `disable_server_timeout`, `increase_server_memory`,
                `server_memory_value`, `dump_low_interest_tables`, `maintenance`, `time_between_backups`,
                `activate_xsendfile`, `ignore_product_image`, `ignore_compression`, `delete_local_backup`,
                `create_on_distant`, `js_download`, `backup_dir`, `ignore_directories`, `ignore_file_types`,
                `ignore_tables`
            FROM `'._DB_PREFIX_.'ntbr_config`
            ORDER BY `date_add`
        ');

        if (!is_array($configs)) {
            return array();
        }

        return $configs;
    }

    /**
     * Get a list of all configs backup directories
     *
     * @return  array   List of all configs backup directories
     */
    public static function getListBackupDirectories()
    {
        $backup_dirs = Db::getInstance()->executeS('
            SELECT DISTINCT `backup_dir`
            FROM `'._DB_PREFIX_.'ntbr_config`
            ORDER BY `backup_dir`
        ');

        if (!is_array($backup_dirs)) {
            return array();
        }

        return $backup_dirs;
    }

    /**
     * Get config ID by name
     *
     * @param   String      $name   Name of the config
     *
     * @return  integer             ID of the config
     */
    public static function getIdByName($name)
    {
        return (int)Db::getInstance()->getValue('
            SELECT `id_ntbr_config`
            FROM `'._DB_PREFIX_.'ntbr_config`
            WHERE `name` = "'.pSQL($name).'"
        ');
    }

    /**
     * Get config name by ID
     *
     * @param   integer     $id_ntbr_config   ID of the config
     *
     * @return  String      Name of the config
     */
    public static function getNameById($id_ntbr_config)
    {
        return Db::getInstance()->getValue('
            SELECT `name`
            FROM `'._DB_PREFIX_.'ntbr_config`
            WHERE `id_ntbr_config` = '.(int)$id_ntbr_config.'
        ');
    }

    /**
     * Get config backup directory by ID
     *
     * @param   integer     $id_ntbr_config   ID of the config
     *
     * @return  String      Backup directory of the config
     */
    public static function getBackupDirectoryById($id_ntbr_config)
    {
        return Db::getInstance()->getValue('
            SELECT `backup_dir`
            FROM `'._DB_PREFIX_.'ntbr_config`
            WHERE `id_ntbr_config` = '.(int)$id_ntbr_config.'
        ');
    }

    /**
     * Get default config
     *
     * @return  integer     ID of the config
     */
    public static function getIdDefault()
    {
        return (int)Db::getInstance()->getValue('
            SELECT `id_ntbr_config`
            FROM `'._DB_PREFIX_.'ntbr_config`
            WHERE `is_default` = 1
        ');
    }

    /**
     * Get first config of a type
     *
     * @return  integer     ID of the config
     */
    public static function getIdByType($type_backup)
    {
        return (int)Db::getInstance()->getValue('
            SELECT `id_ntbr_config`
            FROM `'._DB_PREFIX_.'ntbr_config`
            WHERE `type_backup` = "'.pSQL($type_backup).'"
            ORDER BY `id_ntbr_config`
        ');
    }

    /**
     * Get nb config
     *
     * @return  integer     nb of config
     */
    public static function getNbConfig()
    {
        return (int)Db::getInstance()->getValue('
            SELECT COUNT(`id_ntbr_config`)
            FROM `'._DB_PREFIX_.'ntbr_config`
        ');
    }

    public static function cleanDefault($new_id_default)
    {
        Db::getInstance()->execute('
            UPDATE `'._DB_PREFIX_.'ntbr_config`
            SET `is_default` = 0
            WHERE `id_ntbr_config` <> '.(int)$new_id_default.';
        ');
    }

    /**
     * Get config data by ID
     *
     * @param   integer     $id_ntbr_config     ID of the config
     *
     * @return  array                           Data of the config
     */
    public static function getConfigById($id_ntbr_config)
    {
        $config = Db::getInstance()->getRow('
            SELECT
                `id_ntbr_config`, `is_default`, `name`, `type_backup`, `nb_backup`, `send_email`, `email_only_error`,
                `mail_backup`, `send_restore`, `activate_log`, `part_size`, `max_file_to_backup`, `dump_max_values`,
                `disable_refresh`, `time_between_refresh`, `time_pause_between_refresh`, `dump_lines_limit`,
                `time_between_progress_refresh`, `disable_server_timeout`, `increase_server_memory`, `js_download`,
                `server_memory_value`, `dump_low_interest_tables`, `maintenance`, `time_between_backups`, `backup_dir`,
                `activate_xsendfile`, `ignore_product_image`, `ignore_compression`, `delete_local_backup`,
                `ignore_directories`, `ignore_file_types`, `ignore_tables`, `create_on_distant`
            FROM `'._DB_PREFIX_.'ntbr_config`
            WHERE `id_ntbr_config` = '.(int)$id_ntbr_config.'
        ');

        if (!is_array($config)) {
            return array();
        }

        return $config;
    }
}
