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

require_once dirname(__FILE__).'/classes/Aws.php';
require_once dirname(__FILE__).'/classes/Backups.php';
require_once dirname(__FILE__).'/classes/Config.php';
require_once dirname(__FILE__).'/classes/Dropbox.php';
require_once dirname(__FILE__).'/classes/Ftp.php';
require_once dirname(__FILE__).'/classes/Googledrive.php';
require_once dirname(__FILE__).'/classes/Hubic.php';
require_once dirname(__FILE__).'/classes/Onedrive.php';
require_once dirname(__FILE__).'/classes/Owncloud.php';
require_once dirname(__FILE__).'/classes/Sugarsync.php';
require_once dirname(__FILE__).'/classes/Webdav.php';

require_once dirname(__FILE__).'/lib/apparatus/Apparatus.php';
require_once dirname(__FILE__).'/lib/aws/Aws.php';
require_once dirname(__FILE__).'/lib/dropbox/Dropbox.php';
require_once dirname(__FILE__).'/lib/googledrive/Googledrive.php';
require_once dirname(__FILE__).'/lib/hubic/Hubic.php';
require_once dirname(__FILE__).'/lib/onedrive/OneDrive.php';
require_once dirname(__FILE__).'/lib/openstack/Openstack.php';
require_once dirname(__FILE__).'/lib/owncloud/Owncloud.php';
require_once dirname(__FILE__).'/lib/sugarsync/Sugarsync.php';
require_once dirname(__FILE__).'/lib/webdav/Webdav.php';

class NtBackupAndRestore extends Module
{
    const MODULE_NAME = 'ntbackupandrestore';

    const TAB_2NT       = 'NTModules';
    const NAME_TAB_2NT  = 'NT Modules';
    const TAB_MODULE    = 'AdminNtbackupandrestore';
    const NAME_TAB      = 'NtBackupAndRestore';

    const INSTALL_SQL_FILE      = 'sql/install.sql';
    const UNINSTALL_SQL_FILE    = 'sql/uninstall.sql';
    const SAVE_CONFIG_FILE      = 'backup/save_config.cfg';

    public function __construct()
    {
        $this->name             = 'ntbackupandrestore';
        $this->tab              = 'administration';
        $this->version          = '11.2.6';
        $this->author           = '2N Technologies';
        $this->need_instance    = 0;
        $this->ps_versions_compliancy = array('min' => '1.5', 'max' => '1.7');
        $key_full               = '652f1358e0ab9984c886ef5fceac8675';
        $key_light              = '4271039ddcff6fad89705a81386abdec';
        $this->module_key       = '652f1358e0ab9984c886ef5fceac8675';
        $this->secure_key       = Tools::encrypt($this->name);

        // Delete one of the class if both exists, depending on the module_key (except when in dev environment)
        if (!@file_exists(_PS_ROOT_DIR_.'/../ntbr_mode_dev.txt')) {
            if (file_exists(_PS_ROOT_DIR_.'/modules/ntbackupandrestore/classes/ntbrfull.php')
                && $this->module_key == $key_light
            ) {
                unlink(_PS_ROOT_DIR_.'/modules/ntbackupandrestore/classes/ntbrfull.php');
            } elseif (file_exists(_PS_ROOT_DIR_.'/modules/ntbackupandrestore/classes/ntbrlight.php')
                && $this->module_key == $key_full
            ) {
                unlink(_PS_ROOT_DIR_.'/modules/ntbackupandrestore/classes/ntbrlight.php');
            }
        }

        parent::__construct();

        $this->displayName = $this->l('2N Technologies Backup And Restore');
        $this->description = $this->l('Backup your prestashop site and easily restore it wherever you want');
        //$this->confirmUninstall = $this->l(
        //'Do you really want to uninstall this module?');

        $this->tabs[] = array(
            'parent_class'  =>  self::TAB_2NT,
            'parent_name'   =>  self::NAME_TAB_2NT,
            'tab_class'     =>  self::TAB_MODULE,
            'tab_name'      =>  self::NAME_TAB,
        );

        if (version_compare(_PS_VERSION_, '1.7.0', '>=') === true) {
            $this->tabs[] = array(
                'parent_class'  =>  'AdminAdvancedParameters',
                'parent_name'   =>  self::NAME_TAB_2NT,
                'tab_class'     =>  self::TAB_MODULE.'Tab',
                'tab_name'      =>  self::NAME_TAB,
            );
        } else {
            $this->tabs[] = array(
                'parent_class'  =>  'AdminTools',
                'parent_name'   =>  self::NAME_TAB_2NT,
                'tab_class'     =>  self::TAB_MODULE.'Tab',
                'tab_name'      =>  self::NAME_TAB,
            );
        }
    }

    /**
     * Execute a SQL file
     *
     * @param   String  $file_path  The path of the SQL file
     *
     * @return  boolean             Success or failure of the operation
     */
    public function executeFile($file_path)
    {
        // Check if the file exists
        if (!file_exists($file_path)) {
            return Tools::displayError('Error : no sql file !');
        } elseif (!$sql = Tools::file_get_contents($file_path)) {// Get file content
            return Tools::displayError('Error : there is a problem with your install sql file !');
        }

        $sql_replace = str_replace(array('PREFIX_', 'ENGINE_TYPE'), array(_DB_PREFIX_, _MYSQL_ENGINE_), $sql);
        $sql = preg_split("/;\s*[\r\n]+/", trim($sql_replace));

        foreach ($sql as $query) {
            if (!Db::getInstance()->execute(trim($query))) {
                return Tools::displayError('Error : this query doesn\'t work ! '.$query);
            }
        }

        return true;
    }

    /**
     * @see Module::install()
     */
    public function install()
    {
        require_once(dirname(__FILE__).'/classes/ntbr.php');

        $physic_path_modules = realpath(_PS_ROOT_DIR_.DIRECTORY_SEPARATOR.'modules').DIRECTORY_SEPARATOR;
        $backup_dir =  $physic_path_modules.$this->name.DIRECTORY_SEPARATOR.NtbrCore::BACKUP_FOLDER.DIRECTORY_SEPARATOR;

        //We initialize the configuration for all shops
        $shops = Shop::getShops();

        foreach ($shops as $shop) {
            $id_shop        = $shop['id_shop'];
            $id_shop_group  = $shop['id_shop_group'];
            $rd_hour        = mt_rand(2, 5); // Rand hour between 2 and 5

            if (!Configuration::getIdByName('NTBR_AUTOMATION_2NT_HOURS', $id_shop_group, $id_shop)) {
                if (!Configuration::updateValue(
                    'NTBR_AUTOMATION_2NT_HOURS',
                    $rd_hour,
                    false,
                    $id_shop_group,
                    $id_shop
                )
                ) {
                    $this->_errors[] = $this->l('The configuration cannot be create.');
                    return false;
                }
            }

            if (!Configuration::getIdByName('NTBR_AUTOMATION_2NT_MINUTES', $id_shop_group, $id_shop)) {
                if (!Configuration::updateValue('NTBR_AUTOMATION_2NT_MINUTES', 0, false, $id_shop_group, $id_shop)) {
                    $this->_errors[] = $this->l('The configuration cannot be create.');
                    return false;
                }
            }

            if (!Configuration::getIdByName('NTBR_ONGOING', $id_shop_group, $id_shop)) {
                if (!Configuration::updateValue('NTBR_ONGOING', 0, false, $id_shop_group, $id_shop)) {
                    $this->_errors[] = $this->l('The configuration cannot be create.');
                    return false;
                }
            }

            if (!Configuration::getIdByName('NTBR_ADMIN_DIR', $id_shop_group, $id_shop)) {
                if (!Configuration::updateValue('NTBR_ADMIN_DIR', '', false, $id_shop_group, $id_shop)) {
                    $this->_errors[] = $this->l('The configuration cannot be create.');
                    return false;
                }
            }

            if (!Configuration::getIdByName('NTBR_BIG_WEBSITE_HIDE', $id_shop_group, $id_shop)) {
                if (!Configuration::updateValue('NTBR_BIG_WEBSITE_HIDE', 0, false, $id_shop_group, $id_shop)) {
                    $this->_errors[] = $this->l('The configuration cannot be create.');
                    return false;
                }
            }

            if (!Configuration::getIdByName('NTBR_AUTOMATION_2NT_MINUTES', $id_shop_group, $id_shop)) {
                if (!Configuration::updateValue('NTBR_AUTOMATION_2NT_MINUTES', 0, false, $id_shop_group, $id_shop)) {
                    $this->_errors[] = $this->l('The configuration cannot be create.');
                    return false;
                }
            }

            if (!Configuration::getIdByName('NTBR_AUTOMATION_2NT_IP', $id_shop_group, $id_shop)) {
                if (!Configuration::updateValue('NTBR_AUTOMATION_2NT_IP', 0, false, $id_shop_group, $id_shop)) {
                    $this->_errors[] = $this->l('The configuration cannot be create.');
                    return false;
                }
            }
        }

        $install_on_tab = true;
        /* Install on tab */
        foreach ($this->tabs as $tab) {
            if (!$this->installOnTab($tab['tab_class'], $tab['tab_name'], $tab['parent_class'], $tab['parent_name'])) {
                $install_on_tab = false;
            }
        }

        if (!$install_on_tab) {
            $this->_errors[] = $this->l('The module cannot be install on its tabs.');
            return false;
        }

        // Make sure the database was removed
        $this->executeFile(dirname(__FILE__).'/'.self::UNINSTALL_SQL_FILE);

        // Create new data base table
        $this->executeFile(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE);

        $s_save_config_file = dirname(__FILE__).'/'.self::SAVE_CONFIG_FILE;

        $b_create_default_config = false;

        // Put back config if it exists
        if (file_exists($s_save_config_file)) {
            $b_at_least_one_conf = false;

            if (($handle_save_config_file = fopen($s_save_config_file, 'a+'))) {
                rewind($handle_save_config_file);

                $a_list_configs = unserialize(fgets($handle_save_config_file));

                fclose($handle_save_config_file);

                foreach ($a_list_configs as $a_config) {
                    // Insert config
                    $o_config = new Config($a_config['id_ntbr_config']);

                    $o_config->is_default                       = $a_config['is_default'];
                    $o_config->name                             = $a_config['name'];
                    $o_config->type_backup                      = $a_config['type_backup'];
                    $o_config->nb_backup                        = $a_config['nb_backup'];
                    $o_config->send_email                       = $a_config['send_email'];
                    $o_config->email_only_error                 = $a_config['email_only_error'];
                    $o_config->mail_backup                      = $a_config['mail_backup'];
                    $o_config->send_restore                     = $a_config['send_restore'];
                    $o_config->activate_log                     = $a_config['activate_log'];
                    $o_config->part_size                        = $a_config['part_size'];
                    $o_config->max_file_to_backup               = $a_config['max_file_to_backup'];
                    $o_config->dump_max_values                  = $a_config['dump_max_values'];
                    $o_config->dump_lines_limit                 = $a_config['dump_lines_limit'];
                    $o_config->disable_refresh                  = $a_config['disable_refresh'];
                    $o_config->time_between_refresh             = $a_config['time_between_refresh'];
                    $o_config->time_pause_between_refresh       = $a_config['time_pause_between_refresh'];
                    $o_config->time_between_progress_refresh    = $a_config['time_between_progress_refresh'];
                    $o_config->disable_server_timeout           = $a_config['disable_server_timeout'];
                    $o_config->increase_server_memory           = $a_config['increase_server_memory'];
                    $o_config->server_memory_value              = $a_config['server_memory_value'];
                    $o_config->dump_low_interest_tables         = $a_config['dump_low_interest_tables'];
                    $o_config->maintenance                      = $a_config['maintenance'];
                    $o_config->time_between_backups             = $a_config['time_between_backups'];
                    $o_config->activate_xsendfile               = $a_config['activate_xsendfile'];
                    $o_config->ignore_product_image             = $a_config['ignore_product_image'];
                    $o_config->ignore_compression               = $a_config['ignore_compression'];
                    $o_config->delete_local_backup              = $a_config['delete_local_backup'];
                    $o_config->create_on_distant                = $a_config['create_on_distant'];
                    $o_config->js_download                      = $a_config['js_download'];
                    $o_config->backup_dir                       = $a_config['backup_dir'];
                    $o_config->ignore_directories               = $a_config['ignore_directories'];
                    $o_config->ignore_file_types                = $a_config['ignore_file_types'];
                    $o_config->ignore_tables                    = $a_config['ignore_tables'];

                    if (!$o_config->save()) {
                        self::lg(
                            sprintf(
                                $this->l('The config %s ID %d could not be saved'),
                                $a_config['name'],
                                $a_config['is_default']
                            )
                        );

                        continue;
                    }

                    $b_at_least_one_conf = true;

                    $a_config['aws_accounts'] = Tools::jsonDecode($a_config['aws_accounts'], true);

                    foreach ($a_config['aws_accounts'] as $a_aws_account) {
                        $o_aws = new Aws();

                        $o_aws->id_ntbr_config      = $o_config->id;
                        $o_aws->active              = $a_aws_account['active'];
                        $o_aws->name                = $a_aws_account['name'];
                        $o_aws->config_nb_backup    = $a_aws_account['config_nb_backup'];
                        $o_aws->access_key_id       = $a_aws_account['access_key_id'];
                        $o_aws->secret_access_key   = $a_aws_account['secret_access_key'];
                        $o_aws->region              = $a_aws_account['region'];
                        $o_aws->bucket              = $a_aws_account['bucket'];
                        $o_aws->storage_class       = (isset($a_aws_account['storage_class'])?$a_aws_account['storage_class']:AwsLib::STORAGE_CLASS_STANDARD);
                        $o_aws->directory_key       = $a_aws_account['directory_key'];
                        $o_aws->directory_path      = $a_aws_account['directory_path'];

                        $o_aws->save();
                    }

                    $a_config['dropbox_accounts'] = Tools::jsonDecode($a_config['dropbox_accounts'], true);

                    foreach ($a_config['dropbox_accounts'] as $a_dropbox_account) {
                        $o_dropbox = new Dropbox();

                        $o_dropbox->id_ntbr_config   = $o_config->id;
                        $o_dropbox->active           = $a_dropbox_account['active'];
                        $o_dropbox->name             = $a_dropbox_account['name'];
                        $o_dropbox->config_nb_backup = $a_dropbox_account['config_nb_backup'];
                        $o_dropbox->directory        = $a_dropbox_account['directory'];
                        $o_dropbox->token            = $a_dropbox_account['token'];

                        $o_dropbox->save();
                    }


                    $a_config['googledrive_accounts'] = Tools::jsonDecode($a_config['googledrive_accounts'], true);

                    foreach ($a_config['googledrive_accounts'] as $a_googledrive_account) {
                        $o_googledrive = new Googledrive();

                        $o_googledrive->id_ntbr_config      = $o_config->id;
                        $o_googledrive->active              = $a_googledrive_account['active'];
                        $o_googledrive->name                = $a_googledrive_account['name'];
                        $o_googledrive->config_nb_backup    = $a_googledrive_account['config_nb_backup'];
                        $o_googledrive->directory_key       = $a_googledrive_account['directory_key'];
                        $o_googledrive->directory_path      = $a_googledrive_account['directory_path'];
                        $o_googledrive->token               = $a_googledrive_account['token'];

                        $o_googledrive->save();
                    }

                    $a_config['hubic_accounts'] = Tools::jsonDecode($a_config['hubic_accounts'], true);

                    foreach ($a_config['hubic_accounts'] as $a_hubic_account) {
                        $o_hubic = new Hubic();

                        $o_hubic->id_ntbr_config    = $o_config->id;
                        $o_hubic->active            = $a_hubic_account['active'];
                        $o_hubic->name              = $a_hubic_account['name'];
                        $o_hubic->config_nb_backup  = $a_hubic_account['config_nb_backup'];
                        $o_hubic->directory         = $a_hubic_account['directory'];
                        $o_hubic->token             = $a_hubic_account['token'];
                        $o_hubic->credential        = $a_hubic_account['credential'];

                        $o_hubic->save();
                    }

                    $a_config['onedrive_accounts'] = Tools::jsonDecode($a_config['onedrive_accounts'], true);

                    foreach ($a_config['onedrive_accounts'] as $a_onedrive_account) {
                        $o_onedrive = new Onedrive();

                        $o_onedrive->id_ntbr_config     = $o_config->id;
                        $o_onedrive->active             = $a_onedrive_account['active'];
                        $o_onedrive->name               = $a_onedrive_account['name'];
                        $o_onedrive->config_nb_backup   = $a_onedrive_account['config_nb_backup'];
                        $o_onedrive->directory_key      = $a_onedrive_account['directory_key'];
                        $o_onedrive->directory_path     = $a_onedrive_account['directory_path'];
                        $o_onedrive->token              = $a_onedrive_account['token'];

                        $o_onedrive->save();
                    }

                    $a_config['owncloud_accounts'] = Tools::jsonDecode($a_config['owncloud_accounts'], true);

                    foreach ($a_config['owncloud_accounts'] as $a_owncloud_account) {
                        $o_owncloud = new Owncloud();

                        $o_owncloud->id_ntbr_config     = $o_config->id;
                        $o_owncloud->active             = $a_owncloud_account['active'];
                        $o_owncloud->name               = $a_owncloud_account['name'];
                        $o_owncloud->config_nb_backup   = $a_owncloud_account['config_nb_backup'];
                        $o_owncloud->login              = $a_owncloud_account['login'];
                        $o_owncloud->password           = $a_owncloud_account['password'];
                        $o_owncloud->server             = $a_owncloud_account['server'];
                        $o_owncloud->directory          = $a_owncloud_account['directory'];

                        $o_owncloud->save();
                    }

                    $a_config['sugarsync_accounts'] = Tools::jsonDecode($a_config['sugarsync_accounts'], true);

                    foreach ($a_config['sugarsync_accounts'] as $a_sugarsync_account) {
                        $o_sugarsync = new Sugarsync();

                        $o_sugarsync->id_ntbr_config    = $o_config->id;
                        $o_sugarsync->active            = $a_sugarsync_account['active'];
                        $o_sugarsync->name              = $a_sugarsync_account['name'];
                        $o_sugarsync->config_nb_backup  = $a_sugarsync_account['config_nb_backup'];
                        $o_sugarsync->directory_key     = $a_sugarsync_account['directory_key'];
                        $o_sugarsync->directory_path    = $a_sugarsync_account['directory_path'];
                        $o_sugarsync->token             = $a_sugarsync_account['token'];
                        $o_sugarsync->login             = $a_sugarsync_account['login'];

                        $o_sugarsync->save();
                    }

                    $a_config['webdav_accounts'] = Tools::jsonDecode($a_config['webdav_accounts'], true);

                    foreach ($a_config['webdav_accounts'] as $a_webdav_account) {
                        $o_webdav = new Webdav();

                        $o_webdav->id_ntbr_config   = $o_config->id;
                        $o_webdav->active           = $a_webdav_account['active'];
                        $o_webdav->name             = $a_webdav_account['name'];
                        $o_webdav->config_nb_backup = $a_webdav_account['config_nb_backup'];
                        $o_webdav->login            = $a_webdav_account['login'];
                        $o_webdav->password         = $a_webdav_account['password'];
                        $o_webdav->server           = $a_webdav_account['server'];
                        $o_webdav->directory        = $a_webdav_account['directory'];

                        $o_webdav->save();
                    }

                    $a_config['ftp_accounts'] = Tools::jsonDecode($a_config['ftp_accounts'], true);

                    foreach ($a_config['ftp_accounts'] as $a_ftp_account) {
                        $o_ftp = new Ftp();

                        $o_ftp->id_ntbr_config      = $o_config->id;
                        $o_ftp->active              = $a_ftp_account['active'];
                        $o_ftp->name                = $a_ftp_account['name'];
                        $o_ftp->config_nb_backup    = $a_ftp_account['config_nb_backup'];
                        $o_ftp->sftp                = $a_ftp_account['sftp'];
                        $o_ftp->ssl                 = $a_ftp_account['ssl'];
                        $o_ftp->passive_mode        = $a_ftp_account['passive_mode'];
                        $o_ftp->server              = $a_ftp_account['server'];
                        $o_ftp->login               = $a_ftp_account['login'];
                        $o_ftp->password            = $a_ftp_account['password'];
                        $o_ftp->port                = $a_ftp_account['port'];
                        $o_ftp->directory           = $a_ftp_account['directory'];

                        $o_ftp->save();
                    }

                    $a_config['backups'] = Tools::jsonDecode($a_config['backups'], true);

                    foreach ($a_config['backups'] as $a_backups) {
                        $o_backups = new Backups();

                        $o_backups->id_ntbr_config  = $o_config->id;
                        $o_backups->backup_name     = $a_backups['backup_name'];
                        $o_backups->comment         = $a_backups['comment'];
                        $o_backups->save            = $a_backups['save'];

                        $o_backups->save();
                    }
                }

                if (!$b_at_least_one_conf) {
                    $b_create_default_config = true;
                }
            }

            unlink($s_save_config_file);
        } else {
            $list_config = Config::getListConfigs();

            if (!count($list_config)) {
                $b_create_default_config = true;
            }
        }

        if ($b_create_default_config) {
            // Insert config default data
            $config = new Config();

            $config->is_default                     = 1;
            $config->name                           = $this->l('Complete');
            $config->type_backup                    = 'complete';
            $config->nb_backup                      = 1;
            $config->send_email                     = 0;
            $config->email_only_error               = 0;
            $config->mail_backup                    = Configuration::get('PS_SHOP_EMAIL');
            $config->send_restore                   = 0;
            $config->activate_log                   = 0;
            $config->part_size                      = 0;
            $config->max_file_to_backup             = 0;
            $config->dump_max_values                = NtbrCore::DUMP_MAX_VALUES;
            $config->dump_lines_limit               = NtbrCore::DUMP_LINES_LIMIT;
            $config->disable_refresh                = 0;
            $config->time_between_refresh           = NtbrCore::MAX_TIME_BEFORE_REFRESH;
            $config->time_pause_between_refresh     = 0;
            $config->time_between_progress_refresh  = NtbrCore::MAX_TIME_BEFORE_PROGRESS_REFRESH;
            $config->disable_server_timeout         = 0;
            $config->increase_server_memory         = 0;
            $config->js_download                    = 0;
            $config->server_memory_value            = NtbrCore::SET_MEMORY_LIMIT;
            $config->dump_low_interest_tables       = 0;
            $config->maintenance                    = 0;
            $config->time_between_backups           = NtbrCore::MIN_TIME_NEW_BACKUP;
            $config->activate_xsendfile             = 0;
            $config->ignore_product_image           = 0;
            $config->ignore_compression             = 0;
            $config->delete_local_backup            = 0;
            $config->create_on_distant              = 0;
            $config->backup_dir                     = $backup_dir;
            $config->ignore_directories             = 'upload';
            $config->ignore_file_types              = '';
            $config->ignore_tables                  = '';

            if (!$config->save()) {
                $this->_errors[] = $this->l('The config cannot be created.');
                return false;
            }
        }

        /* Create file with all the varibles need for crons */
        $this->deleteCronFiles();// Delete them first if they already exists
        $this->writeCronFiles(true);

        return parent::install();
    }

    /**
     * @see Module::uninstall()
     */
    public function uninstall()
    {
        /* Save the current configuration */
        $a_list_configs = Config::getListConfigs();

        foreach ($a_list_configs as &$a_config) {
            $a_config['aws_accounts'] = Tools::jsonEncode(Aws::getListAwsAccounts($a_config['id_ntbr_config']));
            $a_config['dropbox_accounts'] = Tools::jsonEncode(
                Dropbox::getListDropboxAccounts($a_config['id_ntbr_config'])
            );
            $a_config['googledrive_accounts'] = Tools::jsonEncode(
                Googledrive::getListGoogledriveAccounts($a_config['id_ntbr_config'])
            );
            $a_config['hubic_accounts'] = Tools::jsonEncode(Hubic::getListHubicAccounts($a_config['id_ntbr_config']));
            $a_config['onedrive_accounts'] = Tools::jsonEncode(
                Onedrive::getListOnedriveAccounts($a_config['id_ntbr_config'])
            );
            $a_config['owncloud_accounts'] = Tools::jsonEncode(
                Owncloud::getListOwncloudAccounts($a_config['id_ntbr_config'])
            );
            $a_config['sugarsync_accounts'] = Tools::jsonEncode(
                Sugarsync::getListSugarsyncAccounts($a_config['id_ntbr_config'])
            );
            $a_config['webdav_accounts'] = Tools::jsonEncode(
                Webdav::getListWebdavAccounts($a_config['id_ntbr_config'])
            );
            $a_config['ftp_accounts'] = Tools::jsonEncode(Ftp::getListFtpAccounts($a_config['id_ntbr_config']));
            $a_config['backups'] = Tools::jsonEncode(Backups::getListBackupsInfosByConfig($a_config['id_ntbr_config']));
        }

        if (($handle_save_config_file = fopen(dirname(__FILE__).'/'.self::SAVE_CONFIG_FILE, 'w+'))) {
            fwrite($handle_save_config_file, serialize($a_list_configs));
            fclose($handle_save_config_file);
        }

        /* Delete Back-office tab */
        foreach ($this->tabs as $tab) {
            $this->uninstallTab($tab['tab_class']);
        }

        /* Delete the database table */
        $this->executeFile(dirname(__FILE__).'/'.self::UNINSTALL_SQL_FILE);

        $this->deleteCronFiles();

        return parent::uninstall();
    }

    public function uninstallTab($tab_class)
    {
        $img_tab_path = _PS_ROOT_DIR_.'/img/t/';
        $module_path = _PS_MODULE_DIR_.'/'.$this->name.'/';
        $id_tab = Tab::getIdFromClassName($tab_class);

        if ($id_tab) {
            $tab = new Tab((int)$id_tab);
            $id_parent = $tab->id_parent;
            $parent_tab = new Tab((int)$id_parent);

            if (file_exists($img_tab_path.$tab->class_name.'.gif')) {
                unlink($img_tab_path.$tab->class_name.'.gif');
            }

            $tab->delete();

            if (Tab::getNbTabs($id_parent) <= 0 && $parent_tab->class_name == self::TAB_2NT) {
                $tab_parent = new Tab((int)$id_parent);
                $img = $tab_parent->class_name.'.gif';

                if (file_exists($img_tab_path.$img)) {
                    unlink($img_tab_path.$img);
                }

                if (version_compare(_PS_VERSION_, '1.6', '<') && file_exists($module_path.$img)) {
                    unlink($module_path.$img);
                }

                $tab_parent->delete();
            }
        }
    }

    /**
    * Install the module in a tab
    *
    * @param string $tab_class Tab class
    * @param string $tab_name Tab name
    * @param string $tab_parent_class Tab parent's class
    * @param string $tab_parent_name Tab parent's name
    * @return bool
    */
    public function installOnTab($tab_class, $tab_name, $tab_parent_class, $tab_parent_name = '')
    {
        $img_tab_path   = _PS_ROOT_DIR_.'/img/t/';
        $module_path    = _PS_MODULE_DIR_.$this->name.'/';

        if (version_compare(_PS_VERSION_, '1.6', '>')) {
            $logo_path = $module_path.'views/img/tab_logo_grey.png';
        } else {
            $logo_path = $module_path.'views/img/tab_logo_color.png';
        }

        $id_tab_parent = Tab::getIdFromClassName($tab_parent_class);

        /* If the parent tab does not exist yet, create it */
        if (!$id_tab_parent) {
            $tab_parent = new Tab();
            $tab_parent->class_name = $tab_parent_class;
            $tab_parent->module = $this->name;
            $tab_parent->id_parent = 0;

            foreach (Language::getLanguages(false) as $lang) {
                $tab_parent->name[(int)$lang['id_lang']] = $tab_parent_name;
            }

            if (!$tab_parent->save()) {
                $this->_errors[] = (sprintf($this->l('Unable to create the "%s" tab'), $tab_parent_class));
                return false;
            }

            $id_tab_parent = $tab_parent->id;
        }

        if (!file_exists($img_tab_path.$tab_parent_class.'.gif')) {
            if (version_compare(_PS_VERSION_, '1.5.5.0', '>=') === true) {
                $copy = Tools::copy($logo_path, $img_tab_path.$tab_parent_class.'.gif');
            } else {
                // Tools::copy does not exists before Prestashop 1.5.5.0
                $copy = copy($logo_path, $img_tab_path.$tab_parent_class.'.gif');
            }

            if (!$copy) {
                $this->_errors[] = (sprintf($this->l('Unable to copy logo.gif in %s'), $img_tab_path));
                return false;
            }
        }

        if (version_compare(_PS_VERSION_, '1.6', '<')) {
            if (!file_exists($module_path.$tab_parent_class.'.gif')) {
                if (version_compare(_PS_VERSION_, '1.5.5.0', '>=') === true) {
                    $copy = Tools::copy($logo_path, $module_path.$tab_parent_class.'.gif');
                } else {
                    // Tools::copy does not exists before Prestashop 1.5.5.0
                    $copy = copy($logo_path, $module_path.$tab_parent_class.'.gif');
                }

                if (!$copy) {
                    $this->_errors[] = (sprintf($this->l('Unable to copy logo.gif in %s'), $module_path));
                    return false;
                }
            }
        }

        /* If the tab does not exist yet, create it */
        if (!Tab::getIdFromClassName($tab_class)) {
            $tab = new Tab();
            $tab->class_name = $tab_class;
            $tab->module = $this->name;
            $tab->id_parent = (int)$id_tab_parent;

            foreach (Language::getLanguages(false) as $lang) {
                $tab->name[(int)$lang['id_lang']] = $tab_name;
            }

            if (!$tab->save()) {
                $this->_errors[] = (sprintf($this->l('Unable to create the "%s" tab'), $tab_class));
                return false;
            }
        }

        if (file_exists($logo_path)) {
            if (!file_exists($img_tab_path.$tab_class.'.gif')) {
                if (version_compare(_PS_VERSION_, '1.5.5.0', '>=') === true) {
                    $copy = Tools::copy($logo_path, $img_tab_path.$tab_class.'.gif');
                } else {
                    // Tools::copy does not exists before Prestashop 1.5.5.0
                    $copy = copy($logo_path, $img_tab_path.$tab_class.'.gif');
                }

                if (!$copy) {
                    $this->_errors[] = (sprintf($this->l('Unable to copy logo.gif in %s'), $img_tab_path));
                    return false;
                }
            }

            if (version_compare(_PS_VERSION_, '1.6', '<')) {
                if (!file_exists($module_path.$tab_class.'.gif')) {
                    if (version_compare(_PS_VERSION_, '1.5.5.0', '>=') === true) {
                        $copy = Tools::copy($logo_path, $module_path.$tab_class.'.gif');
                    } else {
                        // Tools::copy does not exists before Prestashop 1.5.5.0
                        $copy = copy($logo_path, $module_path.$tab_class.'.gif');
                    }

                    if (!$copy) {
                        $this->_errors[] = (sprintf($this->l('Unable to copy logo.gif in %s'), $module_path));
                        return false;
                    }
                }
            }
        }

        return true;
    }

    public function getContent()
    {
        Tools::redirectAdmin($this->context->link->getAdminLink(self::TAB_MODULE));
    }

    public function deleteCronFiles()
    {
        $physic_path_modules    = realpath(_PS_ROOT_DIR_.'/modules').'/';
        $physic_path_ajax       = $physic_path_modules.$this->name.'/ajax';

        $file_path = $physic_path_ajax.'/backup_'.$this->secure_key.'.php';
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }

    public function writeCronFiles($install)
    {
        $physic_path_modules    = realpath(_PS_ROOT_DIR_.'/modules').'/';
        $shop_domain            = Tools::getCurrentUrlProtocolPrefix().Tools::getHttpHost();
        $url_modules            = $shop_domain.__PS_BASE_URI__.'modules/';
        $url_ajax               = $url_modules.$this->name.'/ajax';
        $physic_path_ajax       = $physic_path_modules.$this->name.'/ajax';
        $param_secure_key       = 'secure_key='.$this->secure_key;

        $file_path = $physic_path_ajax.'/backup_'.$this->secure_key.'.php';

        if (file_exists($file_path)) {
            $old_content = Tools::file_get_contents($file_path);

            if (strpos($old_content, $url_ajax) === false) {
                unlink($file_path);
            }
        }

        if (!file_exists($file_path)) {
            $content = '<?php ';
            $content .= '$config=""; ';
            $content .= 'if (isset($_GET["config"])) $config="&config=".$_GET["config"]; ';
            $content .= 'header("Location: '.$url_ajax.'/backup.php?'.$param_secure_key.'".$config); ';
            $content .= 'exit();';

            $file = fopen($file_path, 'w+');
            fwrite($file, $content);
            fclose($file);

            if (chmod($file_path, octdec(NtbrCore::PERM_FILE)) !== true) {
                // The log fonction is this class child's function.
                // It cannot be use when called from this class, only from its child
                if (!$install) {
                    $this->log(
                        sprintf(
                            $this->l('The file "%s" permission cannot be updated to %d'),
                            $file_path,
                            NtbrCore::PERM_FILE
                        ),
                        true
                    );
                }
            }
        }
    }

    /**
     * Get module backup directory
     *
     * @return  String
     */
    public static function getModuleBackupDirectory()
    {
        $physic_path_modules = realpath(_PS_ROOT_DIR_.DIRECTORY_SEPARATOR.'modules').DIRECTORY_SEPARATOR;
        return $physic_path_modules.self::MODULE_NAME.DIRECTORY_SEPARATOR.NtbrCore::BACKUP_FOLDER.DIRECTORY_SEPARATOR;
    }

    public static function lg($message)
    {
        if (!is_string($message)) {
            $message = print_r($message, true);
        } else {
            $message = html_entity_decode($message, ENT_COMPAT, 'UTF-8');
        }

        $module_backup_dir = self::getModuleBackupDirectory();

        $path = $module_backup_dir.'log.txt';

        if (!($file = fopen($path, 'a+'))) {
            return false;
        }

        if (fwrite($file, date(NtbrCore::LOG_DATE_FORMAT).' '.$message."\n") === false) {
            return false;
        }

        if (!fclose($file)) {
            return false;
        }
        return true;
    }
}
