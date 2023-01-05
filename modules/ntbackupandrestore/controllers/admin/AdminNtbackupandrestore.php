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

if (file_exists(_PS_ROOT_DIR_.'/modules/ntbackupandrestore/classes/ntbrfull.php')) {
    require_once(dirname(__FILE__).'/../../classes/ntbrfull.php');
} elseif (file_exists(_PS_ROOT_DIR_.'/modules/ntbackupandrestore/classes/ntbrlight.php')) {
    require_once(dirname(__FILE__).'/../../classes/ntbrlight.php');
} else {
    die('Missing override');
}

define('CONFIGURE_NTCRON', 'https://ntcron.2n-tech.com/app/configure.php?');

class AdminNtbackupandrestoreController extends ModuleAdminController
{
    const PAGE = 'adminntbackupandrestore';

    private $id_shop;
    private $id_shop_group;
    private $ntbr;

    public function __construct()
    {
        $this->display              = 'view';
        $this->bootstrap            = true;
        $this->multishop_context    = Shop::CONTEXT_ALL;
        $this->context              = Context::getContext();

        parent::__construct();

        if (version_compare(_PS_VERSION_, '1.6.0.12', '>=') === true) {
            $this->meta_title = array($this->l('2NT Backup and Restore', self::PAGE));
        } else {
            $this->meta_title = $this->l('2NT Backup and Restore', self::PAGE);
        }

        if (!$this->module->active) {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminHome'));
        }

        $this->ntbr = new NtbrChild();
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);

        $module_path = $this->module->getPathUri();

        $version_script = '1.5';
        if (version_compare(_PS_VERSION_, '1.6.0', '>=') === true) {
            $version_script = '1.6';
        }

        $this->addCSS(array(
            $module_path.'views/css/style_'.$version_script.'.css',
        ));

        $this->addCSS(array(
            $module_path.'views/css/style.css',
        ));

        $this->addCSS(array(
            $module_path.'views/css/fontawesome-all.min.css',
        ));

        $this->addJS(array(
            $module_path.'views/js/script_'.$version_script.'.js',
        ));

        $this->addJS(array(
            $module_path.'views/js/script.'.$this->module->version.'.js',
        ));

        if (version_compare(_PS_VERSION_, '1.6.0', '<') === true) {
            $this->addJS(array(
                $module_path.'views/js/moment-with-locales.min.js',
            ));
        }

        return true;
    }

    public function renderView()
    {
        $type_module            = $this->ntbr->getTypeModule();
        $light                  = ($type_module=='')?0:1;
        $context                = Context::getContext();
        $shop                   = $context->shop;
        $physic_path_modules    = realpath(_PS_ROOT_DIR_.'/modules').'/';
        $fct_crypt_exists       = true;
        $os_windows             = false;
        $curl_exists            = true;
        $param_secure_key       = 'secure_key='.$this->ntbr->secure_key;

        if (!$light) {
            if (Tools::isSubmit('display_ftp_account')) {
                $this->displayFtpAccount();
            } elseif (Tools::isSubmit('display_dropbox_account')) {
                $this->displayDropboxAccount();
            } elseif (Tools::isSubmit('display_owncloud_account')) {
                $this->displayOwncloudAccount();
            } elseif (Tools::isSubmit('display_webdav_account')) {
                $this->displayWebdavAccount();
            } elseif (Tools::isSubmit('display_googledrive_account')) {
                $this->displayGoogledriveAccount();
            } elseif (Tools::isSubmit('display_onedrive_account')) {
                $this->displayOnedriveAccount();
            } elseif (Tools::isSubmit('display_sugarsync_account')) {
                $this->displaySugarsyncAccount();
            } elseif (Tools::isSubmit('display_hubic_account')) {
                $this->displayHubicAccount();
            } elseif (Tools::isSubmit('display_aws_account')) {
                $this->displayAwsAccount();
            } elseif (Tools::isSubmit('save_ftp')) {
                $this->saveFtp();
            } elseif (Tools::isSubmit('save_dropbox')) {
                $this->saveDropbox();
            } elseif (Tools::isSubmit('save_owncloud')) {
                $this->saveOwncloud();
            } elseif (Tools::isSubmit('save_webdav')) {
                $this->saveWebdav();
            } elseif (Tools::isSubmit('save_googledrive')) {
                $this->saveGoogledrive();
            } elseif (Tools::isSubmit('save_onedrive')) {
                $this->saveOnedrive();
            } elseif (Tools::isSubmit('save_sugarsync')) {
                $this->saveSugarsync();
            } elseif (Tools::isSubmit('save_hubic')) {
                $this->saveHubic();
            } elseif (Tools::isSubmit('save_aws')) {
                $this->saveAws();
            } elseif (Tools::isSubmit('check_connection_ftp')) {
                $this->checkConnectionFtp();
            } elseif (Tools::isSubmit('check_connection_dropbox')) {
                $this->checkConnectionDropbox();
            } elseif (Tools::isSubmit('check_connection_owncloud')) {
                $this->checkConnectionOwncloud();
            } elseif (Tools::isSubmit('check_connection_webdav')) {
                $this->checkConnectionWebdav();
            } elseif (Tools::isSubmit('check_connection_googledrive')) {
                $this->checkConnectionGoogledrive();
            } elseif (Tools::isSubmit('check_connection_onedrive')) {
                $this->checkConnectionOnedrive();
            } elseif (Tools::isSubmit('check_connection_sugarsync')) {
                $this->checkConnectionSugarsync();
            } elseif (Tools::isSubmit('check_connection_hubic')) {
                $this->checkConnectionHubic();
            } elseif (Tools::isSubmit('check_connection_aws')) {
                $this->checkConnectionAws();
            } elseif (Tools::isSubmit('delete_ftp')) {
                $this->deleteFtp();
            } elseif (Tools::isSubmit('delete_dropbox')) {
                $this->deleteDropbox();
            } elseif (Tools::isSubmit('delete_owncloud')) {
                $this->deleteOwncloud();
            } elseif (Tools::isSubmit('delete_webdav')) {
                $this->deleteWebdav();
            } elseif (Tools::isSubmit('delete_googledrive')) {
                $this->deleteGoogledrive();
            } elseif (Tools::isSubmit('delete_onedrive')) {
                $this->deleteOnedrive();
            } elseif (Tools::isSubmit('delete_sugarsync')) {
                $this->deleteSugarsync();
            } elseif (Tools::isSubmit('delete_hubic')) {
                $this->deleteHubic();
            } elseif (Tools::isSubmit('delete_aws')) {
                $this->deleteAws();
            } elseif (Tools::isSubmit('display_googledrive_tree')) {
                $this->displayGoogledriveTree();
            } elseif (Tools::isSubmit('display_googledrive_tree_child')) {
                $this->displayGoogledriveTreeChild();
            } elseif (Tools::isSubmit('display_onedrive_tree')) {
                $this->displayOnedriveTree();
            } elseif (Tools::isSubmit('display_onedrive_tree_child')) {
                $this->displayOnedriveTreeChild();
            } elseif (Tools::isSubmit('display_sugarsync_tree')) {
                $this->displaySugarsyncTree();
            } elseif (Tools::isSubmit('display_sugarsync_tree_child')) {
                $this->displaySugarsyncTreeChild();
            } elseif (Tools::isSubmit('display_aws_tree')) {
                $this->displayAwsTree();
            } elseif (Tools::isSubmit('display_aws_tree_child')) {
                $this->displayAwsTreeChild();
            } elseif (Tools::isSubmit('send_backup')) {
                $this->sendBackupAway();
            } elseif (Tools::isSubmit('restore_backup')) {
                $this->restoreBackup();
            } elseif (Tools::isSubmit('end_restore_backup')) {
                $this->endRestoreBackup();
            } elseif (Tools::isSubmit('save_config_profile')) {
                $this->saveConfigProfile();
            } elseif (Tools::isSubmit('delete_config')) {
                $this->deleteConfig();
            } elseif (Tools::isSubmit('generate_urls')) {
                $this->generateUrls();
            } elseif (Tools::isSubmit('get_directory_children')) {
                $this->getDirectoryChidren();
            } elseif (Tools::isSubmit('get_dropbox_files')) {
                $this->getDropboxFiles();
            } elseif (Tools::isSubmit('download_dropbox_file')) {
                $this->downloadDropboxFile();
            } elseif (Tools::isSubmit('delete_dropbox_file')) {
                $this->deleteDropboxFile();
            } elseif (Tools::isSubmit('get_googledrive_files')) {
                $this->getGoogledriveFiles();
            } elseif (Tools::isSubmit('download_googledrive_file')) {
                $this->downloadGoogledriveFile();
            } elseif (Tools::isSubmit('delete_googledrive_file')) {
                $this->deleteGoogledriveFile();
            } elseif (Tools::isSubmit('get_onedrive_files')) {
                $this->getOnedriveFiles();
            } elseif (Tools::isSubmit('download_onedrive_file')) {
                $this->downloadOnedriveFile();
            } elseif (Tools::isSubmit('delete_onedrive_file')) {
                $this->deleteOnedriveFile();
            } elseif (Tools::isSubmit('get_owncloud_files')) {
                $this->getOwncloudFiles();
            } elseif (Tools::isSubmit('download_owncloud_file')) {
                $this->downloadOwncloudFile();
            } elseif (Tools::isSubmit('delete_owncloud_file')) {
                $this->deleteOwncloudFile();
            } elseif (Tools::isSubmit('get_webdav_files')) {
                $this->getWebdavFiles();
            } elseif (Tools::isSubmit('download_webdav_file')) {
                $this->downloadWebdavFile();
            } elseif (Tools::isSubmit('delete_webdav_file')) {
                $this->deleteWebdavFile();
            } elseif (Tools::isSubmit('get_ftp_files')) {
                $this->getFtpFiles();
            } elseif (Tools::isSubmit('download_ftp_file')) {
                $this->downloadFtpFile();
            } elseif (Tools::isSubmit('delete_ftp_file')) {
                $this->deleteFtpFile();
            }
        }

        if (Tools::isSubmit('save_infos_backup')) {
            $this->saveInfosBackup();
        } elseif (Tools::isSubmit('save_config')) {
            $this->saveConfig();
        } elseif (Tools::isSubmit('delete_backup')) {
            $this->deleteBackup();
        } elseif (Tools::isSubmit('create_backup')) {
            $this->createBackup();
        } elseif (Tools::isSubmit('refresh_backup')) {
            $this->ntbr->log('backup refresh', true);
            $this->refreshBackup();
        } elseif (Tools::isSubmit('add_backup')) {
            $this->addBackup();
        } elseif (Tools::isSubmit('get_time_between_refresh')) {
            $this->getTimeBetweenRefresh();
        } elseif (Tools::isSubmit('log_msg')) {
            $this->logMsg();
        } elseif (Tools::isSubmit('get_backup_download_data')) {
            $this->getBackupDownloadData();
        } elseif (Tools::isSubmit('download_backup')) {
            $this->downloadBackup();
        } elseif (Tools::isSubmit('get_js_download')) {
            $this->getJsBackup();
        } elseif (Tools::isSubmit('save_automation')) {
            $this->saveAutomation();
        } elseif (Tools::isSubmit('stop_backup')) {
            $this->stopScript();
        }

        /*if (Tools::isSubmit('id_profile')) {
            $default_config = new Config(Tools::getValue('id_profile'));

            // If the configuration does not exist anymore
            if (!$default_config->id) {
                $default_config = new Config(Config::getIdDefault());
            }
        } else {*/
            $default_config = new Config(Config::getIdDefault());
        /*}*/

        $list_config        = Config::getListConfigs();
        $multi_config       = $this->ntbr->getConfig('NTBR_MULTI_CONFIG');
        $authorized_type    = array();

        if ($multi_config) {
            foreach ($list_config as $conf) {
                if (!in_array($conf['type_backup'], $authorized_type)) {
                    $authorized_type[] = $conf['type_backup'];
                }
            }
        } else {
            $authorized_type[] = $default_config->type_backup;
        }

        //Check if a backup is running
        $running_backup  = (int)$this->ntbr->runningBackup();

        if (!$running_backup && file_exists(_PS_MODULE_DIR_.$this->ntbr->name.'/'. NtbrChild::STOP_FILE)) {
            $this->ntbr->fileDelete(_PS_MODULE_DIR_.$this->ntbr->name.'/'.NtbrChild::STOP_FILE);
        }

        if (Tools::isSubmit('hide_big_site')) {
            $this->ntbr->setConfig('NTBR_BIG_WEBSITE_HIDE', 1);
        }

        $http_context = stream_context_create(
            array('http'=>
                array(
                    'timeout' => 1,
                )
            )
        );

        $available_version = Tools::file_get_contents(NtbrCore::URL_VERSION, false, $http_context, 1);

        //version_compare return -1 if first version is smaller than the second,
        //0 if they are equals and 1 if the second is smaller than the first
        // $available_version < $this->ntbr->version
        if (version_compare($this->ntbr->version, $available_version) == 1) {
            $available_version = 0; // Make sur the test in smarty will display the right thing
        }

        //Add IP for maintenance mode
        $this->ntbr->setMaintenanceIP();

        $domain_use     = Tools::getHttpHost();
        $protocol       = Tools::getCurrentUrlProtocolPrefix();
        $shop_domain    = $protocol.$domain_use;
        $base_uri       = $shop->getBaseURI();

        if ($base_uri == '/') {
            $base_uri = '';
        }

        $module_controller_link = $context->link->getAdminLink(NtBackupAndRestore::TAB_MODULE);

        if (Configuration::get('PS_SSL_ENABLED')) {
            $domain_config = ShopUrl::getMainShopDomainSSL();
        } else {
            $domain_config = ShopUrl::getMainShopDomain();
        }

        $current_address = $_SERVER['PHP_SELF'];
        $admin_directory = str_replace($base_uri, '', str_replace('index.php', '', $current_address));

        $this->ntbr->setConfig(
            'NTBR_ADMIN_DIR',
            str_replace('/', '', $admin_directory),
            $shop->id_shop_group,
            $shop->id
        );

        $module_address_use     = $protocol.$domain_use.$base_uri.$admin_directory.$module_controller_link;
        $module_address_config  = $protocol.$domain_config.$base_uri.$admin_directory.$module_controller_link;

        $url_modules            = $shop_domain.__PS_BASE_URI__.'modules/';
        $url_ajax               = $url_modules.$this->ntbr->name.'/ajax';
        $documentation          = $url_modules.$this->ntbr->name.'/readme_en.pdf';
        $changelog              = $url_modules.$this->ntbr->name.'/changelog.txt';
        $ajax_loader            = $url_modules.$this->ntbr->name.'/views/img/ajax-loader.gif';
        $documentation_name     = 'readme_en.pdf';
        $this->id_shop          = (int)Configuration::get('PS_SHOP_DEFAULT');
        $this->id_shop_group    = Shop::getGroupFromShop($this->id_shop);

        clearstatcache();
        $list_module_content = $this->ntbr->listDirectoryContent($physic_path_modules.$this->ntbr->name);

        foreach ($list_module_content as $file) {
            if ($file['perm'] != NtbrCore::PERM_FILE || $file['perm'] != NtbrCore::PERM_DIR) {
                if (is_dir($file['path'])) {
                    if (chmod($file['path'], octdec(NtbrCore::PERM_DIR)) !== true) {
                        $msg = sprintf(
                            $this->ntbr->l('The directory "%s" permission cannot be updated to %d', self::PAGE),
                            $file['path'],
                            NtbrCore::PERM_DIR
                        );
                        $this->ntbr->log($msg, true);
                        $this->ntbr->errors[] = $msg;
                    }
                } else {
                    if (chmod($file['path'], octdec(NtbrCore::PERM_FILE)) !== true) {
                        $msg = sprintf(
                            $this->ntbr->l('The file "%s" permission cannot be updated to %d', self::PAGE),
                            $file['path'],
                            NtbrCore::PERM_FILE
                        );
                        $this->ntbr->log($msg, true);
                        $this->ntbr->errors[] = $msg;
                    }
                }
            }
        }
//p($this->ntbr->errors);
//d($list_module_content);

        if (stripos(PHP_OS, 'win') !== false) {
            $os_windows = true;
        }

        if (!extension_loaded('openssl')) {
            $fct_crypt_exists = false;
            Owncloud::deactiveAllOwncloud();
            Webdav::deactiveAllWebdav();
            FTP::deactiveAllFtpSftp();
            Aws::deactiveAllAws();
            Dropbox::deactiveAllDropbox();
            Googledrive::deactiveAllGoogledrive();
            Hubic::deactiveAllHubic();
            Onedrive::deactiveAllOnedrive();
            Sugarsync::deactiveAllSugarsync();
        }

        if (!extension_loaded('curl')) {
            $curl_exists = false;
        }

        if ($os_windows || !$fct_crypt_exists) {
            // for ftp_ssl_connect() to be available on Windows you must compile your own PHP binaries
            Ftp::removeSSL();
        }

        /*****************************************/
        // FTP
        $ftp_port_default       = '21';
        $ftp_directory_default  = '/';
        $ftp_default            = Ftp::getDefaultValues();

        // Dropbox
        $dropbox_default = Dropbox::getDefaultValues();

        if ($light) {
            $dropbox_authorizeUrl   = '';
        } else {
            $dropbox                = $this->ntbr->connectToDropbox();
            $dropbox_authorizeUrl   = $dropbox->getLogInUrl();
        }

        // OneDrive
        $onedrive_default = Onedrive::getDefaultValues();

        if ($light) {
            $onedrive_authorizeUrl  = '';
        } else {
            $onedrive               = $this->ntbr->connectToOnedrive();
            $onedrive_authorizeUrl  = $this->ntbr->getOnedriveAccessTokenUrl($onedrive);
        }

        // Google Drive
        $googledrive_default = Googledrive::getDefaultValues();

        if ($light) {
            $googledrive_authorizeUrl   = '';
        } else {
            $googledrive                = $this->ntbr->connectToGoogledrive();
            $googledrive_authorizeUrl   = $googledrive->getLogInUrl();
        }

        // SugarSync
        $sugarsync_default  = Sugarsync::getDefaultValues();

        // ownCloud
        $owncloud_default   = Owncloud::getDefaultValues();

        // WebDAV
        $webdav_default     = Webdav::getDefaultValues();

        // hubiC
        $hubic_default      = Hubic::getDefaultValues();

        if ($light) {
            $hubic_authorizeUrl = '';
        } else {
            $hubic              = $this->ntbr->connectToHubic();
            $hubic_authorizeUrl = $hubic->getLogInUrl();
        }

        $default_backup_dir = NtBackupAndRestore::getModuleBackupDirectory();

        // AWS
        $aws_default        = Aws::getDefaultValues();

        foreach ($list_config as &$config) {
            $id_config = $config['id_ntbr_config'];

            // If the directory configured does not exists anymore, replace it by the default one
            if (!is_dir($config['backup_dir'])) {
                $config['backup_dir'] = $default_backup_dir;

                $o_config = new Config($id_config);
                $o_config->backup_dir = $config['backup_dir'];
                $o_config->update();
            }

            // FTP
            $config['ftp_accounts']             = Ftp::getListFtpAccounts($id_config);
            $config['nb_ftp_accounts']          = count($config['ftp_accounts']);
            $config['nb_ftp_active_accounts']   = FTP::getNbAccountsActive($id_config);

            // Dropbox
            $config['dropbox_accounts']             = Dropbox::getListDropboxAccounts($id_config);
            $config['nb_dropbox_accounts']          = count($config['dropbox_accounts']);
            $config['nb_dropbox_active_accounts']   = Dropbox::getNbAccountsActive($id_config);

            // OneDrive
            $config['onedrive_accounts']            = Onedrive::getListOnedriveAccounts($id_config);
            $config['nb_onedrive_accounts']         = count($config['onedrive_accounts']);
            $config['nb_onedrive_active_accounts']  = Onedrive::getNbAccountsActive($id_config);

            // SugarSync
            $config['sugarsync_accounts']           = Sugarsync::getListSugarsyncAccounts($id_config);
            $config['nb_sugarsync_accounts']        = count($config['sugarsync_accounts']);
            $config['nb_sugarsync_active_accounts'] = Sugarsync::getNbAccountsActive($id_config);

            // Google Drive
            $config['googledrive_accounts']             = Googledrive::getListGoogledriveAccounts($id_config);
            $config['nb_googledrive_accounts']          = count($config['googledrive_accounts']);
            $config['nb_googledrive_active_accounts']   = Googledrive::getNbAccountsActive($id_config);

            // ownCloud
            $config['owncloud_accounts']            = Owncloud::getListOwncloudAccounts($id_config);
            $config['nb_owncloud_accounts']         = count($config['owncloud_accounts']);
            $config['nb_owncloud_active_accounts']  = Owncloud::getNbAccountsActive($id_config);

            // WebDAV
            $config['webdav_accounts']              = Webdav::getListWebdavAccounts($id_config);
            $config['nb_webdav_accounts']           = count($config['webdav_accounts']);
            $config['nb_webdav_active_accounts']    = webdav::getNbAccountsActive($id_config);

            // hubiC
            $config['hubic_accounts']           = Hubic::getListHubicAccounts($id_config);
            $config['nb_hubic_accounts']        = count($config['hubic_accounts']);
            $config['nb_hubic_active_accounts'] = Hubic::getNbAccountsActive($id_config);

            // AWS
            $config['aws_accounts']             = Aws::getListAwsAccounts($id_config);
            $config['nb_aws_accounts']          = count($config['aws_accounts']);
            $config['nb_aws_active_accounts']   = Aws::getNbAccountsActive($id_config);
        }

        /*****************************************/

        if (Tools::file_exists_cache(
            $physic_path_modules.$this->ntbr->name.'/readme_'.$this->context->language->iso_code.'.pdf'
        )
        ) {
            $documentation = $url_modules.$this->ntbr->name.'/readme_'.$this->context->language->iso_code.'.pdf';
            $documentation_name = 'readme_'.$this->context->language->iso_code.'.pdf';
        }

        $display_translate_tab  = true;
        $translate_lng          = array();
        $translate_files        = glob($physic_path_modules.$this->ntbr->name.'/translations/*.php');

        foreach ($translate_files as $trslt_file) {
            $translate_lng[] = basename($trslt_file, '.php');
        }

        if (in_array($this->context->language->iso_code, $translate_lng)) {
            $display_translate_tab = false;
        }

        $backup_files_to_upload         = $this->ntbr->findOldBackups(0, false);
        $backup_files                   = $this->ntbr->findOldBackups();
        $restore_backup_files_complete  = array();
        $restore_backup_files_file      = array();
        $restore_backup_files_base      = array();

        foreach ($backup_files as $key => &$b_file) {
            if (!$multi_config && $default_config->id != $b_file['id_config']) {
                $b_file['id_config'] = 0;
            }

            // Check if first (or only) file exist (in case backup_dir changed)
            $first_file = reset($b_file['part']);

            if (!file_exists($b_file['backup_dir'].$first_file['name'])) {
                unset($backup_files[$key]);
            }

            if ($b_file['id_config']) {
                $file_config = new Config($b_file['id_config']);

                if ($file_config->type_backup == $this->ntbr->type_backup_complete
                    && in_array($this->ntbr->type_backup_complete, $authorized_type)
                ) {
                    $restore_backup_files_complete[] = $b_file;
                } elseif ($file_config->type_backup == $this->ntbr->type_backup_file
                    && in_array($this->ntbr->type_backup_file, $authorized_type)
                ) {
                    $restore_backup_files_file[] = $b_file;
                } elseif ($file_config->type_backup == $this->ntbr->type_backup_base
                    && in_array($this->ntbr->type_backup_base, $authorized_type)
                ) {
                    $restore_backup_files_base[] = $b_file;
                }
            }
        }

        foreach ($backup_files_to_upload as $key => $b_to_up) {
            if (stripos($b_to_up['name'], '.'.$this->ntbr->type_backup_complete.'.') !== false
                && !in_array($this->ntbr->type_backup_complete, $authorized_type)
            ) {
                unset($backup_files_to_upload[$key]);
            } elseif (stripos($b_to_up['name'], '.'.$this->ntbr->type_backup_file.'.') !== false
                && !in_array($this->ntbr->type_backup_file, $authorized_type)
            ) {
                unset($backup_files_to_upload[$key]);
            } elseif (stripos($b_to_up['name'], '.'.$this->ntbr->type_backup_base.'.') !== false
                && !in_array($this->ntbr->type_backup_base, $authorized_type)
            ) {
                unset($backup_files_to_upload[$key]);
            }
        }

        $download_files_links = $this->ntbr->generateUrls(true);

        $this->ntbr->writeCronFiles(false);

        $ads_url    = 'https://addons.prestashop.com/';
        $ads_url_fr = $ads_url.'fr/';
        $ads_url_en = $ads_url.'en/';
        $p_id       = '20130';
        $ads_var    = '?id_product='.$p_id;

        if ($this->context->language->iso_code == 'fr') {
            $link_contact       = $ads_url_fr.'ecrire-au-developpeur'.$ads_var;
            $link_full_version  = $ads_url_fr.'migration-donnees-sauvegarde/'.$p_id.'-nt-sauvegarde-et-restaure.html';
        } else {
            $link_contact       = $ads_url_en.'write-to-developper'.$ads_var;
            $link_full_version  = $ads_url_en.'data-migration-backup/'.$p_id.'-nt-backup-and-restore.html';
        }

        $activate_2nt_automation = true;

        $list_infos = Backups::getListBackupsInfos();

        $ip = $domain_use;
        // If the domain is not an IP, find the IP of the domain
        if (!(filter_var($domain_use, FILTER_VALIDATE_IP))) {
            //$ip = gethostbyname($domain_use);

            if (strpos($ip, 'localhost') === false) {
                $ip = filter_var(Tools::file_get_contents(NtbrCore::URL_SERVICE_IP_EXTERNE), FILTER_VALIDATE_IP);
                if ($ip === false) {
                    $ip = false;
                }
            } else {
                $ip = false;
            }
        }

        // The IP of the server running the script
        //$ip = $_SERVER['SERVER_ADDR'];

        $special_ip_range = array(
            '0.0.0.0/8',
            '10.0.0.0/8',
            '100.64.0.0/10',
            '127.0.0.0/8',
            '169.254.0.0/16',
            '172.16.0.0/12',
            '192.0.0.0/24',
            '192.0.2.0/24',
            '192.88.99.0/24',
            '192.168.0.0/16',
            '198.18.0.0/15',
            '198.51.100.0/24',
            '203.0.113.0/24',
            '224.0.0.0/4',
            '240.0.0.0/4',
            '255.255.255.255/32',
            '::/128',
            '::1/128',
            '::ffff:0:0/96',
            '0100::/64',
            '2000::/3',
            '2001::/32',
            '2001:2::/48',
            '2001:10::/28',
            '2001:db8::/32',
            '2002::/16',
            'fc00::/7',
            'fe80::/10',
            'ff00::/8',
        );

        if ($ip) {
            foreach ($special_ip_range as $range) {
                $is_ip_in_range = NtbrCore::ipInRange($ip, $range);
                if ($is_ip_in_range !== false) {
                    $activate_2nt_automation = false;
                    break;
                }
            }
        } else {
            $activate_2nt_automation = false;
        }

        $big_website = 0;

        if (!$this->ntbr->getConfig('NTBR_BIG_WEBSITE_HIDE')) {
            $big_website = (int)$this->ntbr->getConfig('NTBR_BIG_WEBSITE');
        }

        $this->tpl_view_vars = array(
            'light'                             => $light,
            'link_full_version'                 => $link_full_version,
            'create_backup_cron'                => $url_ajax.'/backup_'.$this->ntbr->secure_key.'.php',
            'backup_progress'                   => $url_ajax.'/backup_progress.php?'.$param_secure_key,
            'backup_stop'                       => $url_ajax.'/backup_stop.php?'.$param_secure_key,
            'link_restore_file'                 => $shop_domain.__PS_BASE_URI__.NtbrCore::NEW_RESTORE_NAME,
            'restore_lastlog'                   => $shop_domain.__PS_BASE_URI__.'lastlog.txt',
            'download_file'                     => $download_files_links['link'],
            'backup_files'                      => $backup_files,
            'backup_files_to_upload'            => $backup_files_to_upload,
            'changelog'                         => $changelog,
            'documentation'                     => $documentation,
            'documentation_name'                => $documentation_name,
            'display_translate_tab'             => $display_translate_tab,
            'multi_config'                      => $multi_config,
            'automation_2nt'                    => $this->ntbr->getConfig('NTBR_AUTOMATION_2NT'),
            'automation_2nt_hours'              => $this->ntbr->getConfig('NTBR_AUTOMATION_2NT_HOURS'),
            'automation_2nt_minutes'            => $this->ntbr->getConfig('NTBR_AUTOMATION_2NT_MINUTES'),
            'automation_2nt_ip'                 => $this->ntbr->getConfig('NTBR_AUTOMATION_2NT_IP'),
            'activate_2nt_automation'           => $activate_2nt_automation,
            'ftp_port_default'                  => $ftp_port_default,
            'ftp_directory_default'             => $ftp_directory_default,
            'xsendfile_detected'                => Tools::apacheModExists('xsendfile'),
            'id_shop_group'                     => $this->id_shop_group,
            'id_shop'                           => $this->id_shop,
            'version'                           => $this->ntbr->version.$type_module,
            'available_version'                 => $available_version,
            'dropbox_authorizeUrl'              => $dropbox_authorizeUrl,
            'onedrive_authorizeUrl'             => $onedrive_authorizeUrl,
            'googledrive_authorizeUrl'          => $googledrive_authorizeUrl,
            'hubic_authorizeUrl'                => $hubic_authorizeUrl,
            'ajax_loader'                       => $ajax_loader,
            'link_contact'                      => $link_contact,
            'module_address_use'                => $module_address_use,
            'module_address_config'             => $module_address_config,
            'fct_crypt_exists'                  => $fct_crypt_exists,
            'os_windows'                        => $os_windows,
            'curl_exists'                       => $curl_exists,
            'memory_limit'                      => ini_get('memory_limit'),
            'max_execution_time'                => ini_get('max_execution_time'),
            'min_memory_limit'                  => NtbrCore::SET_MEMORY_LIMIT,
            'min_time_new_backup'               => NtbrCore::MIN_TIME_NEW_BACKUP,
            'max_time_before_refresh'           => NtbrCore::MAX_TIME_BEFORE_REFRESH,
            'max_time_before_progress_refresh'  => NtbrCore::MAX_TIME_BEFORE_PROGRESS_REFRESH,
            'time_before_warning_timeout'       => NtbrCore::TIME_BEFORE_WARNING_TIMEOUT,
            'big_website'                       => $big_website,
            'ftp_default'                       => $ftp_default,
            'dropbox_default'                   => $dropbox_default,
            'googledrive_default'               => $googledrive_default,
            'onedrive_default'                  => $onedrive_default,
            'sugarsync_default'                 => $sugarsync_default,
            'owncloud_default'                  => $owncloud_default,
            'webdav_default'                    => $webdav_default,
            'hubic_default'                     => $hubic_default,
            'aws_default'                       => $aws_default,
            'list_infos'                        => $list_infos,
            'backup_type_complete'              => $this->ntbr->type_backup_complete,
            'backup_type_file'                  => $this->ntbr->type_backup_file,
            'backup_type_base'                  => $this->ntbr->type_backup_base,
            'restore_backup_files_complete'     => $restore_backup_files_complete,
            'restore_backup_files_file'         => $restore_backup_files_file,
            'restore_backup_files_base'         => $restore_backup_files_base,
            'restore_backup_finish'             => Tools::substr($this->ntbr->l('FINISH', self::PAGE), 0, 5),
            'restore_backup_error'              => Tools::substr($this->ntbr->l('Error', self::PAGE), 0, 5),
            'list_config'                       => $list_config,
            'activate_log'                      => $default_config->activate_log,
            'id_current_config'                 => $default_config->id,
            'current_hour'                      => date('H:i:s'),
            'running_backup'                    => $running_backup,
            'fake_mdp'                          => NtbrChild::FAKE_MDP,
            'time_zone'                         => date_default_timezone_get(),
            'max_file_download_size'            => NtbrChild::MAX_FILE_DOWNLOAD_SIZE,
            'default_backup_dir'                => $default_backup_dir,
            'ntbr_dropbox_name'                 => NtbrChild::DROPBOX,
            'ntbr_owncloud_name'                => NtbrChild::OWNCLOUD,
            'ntbr_webdav_name'                  => NtbrChild::WEBDAV,
            'ntbr_googledrive_name'             => NtbrChild::GOOGLEDRIVE,
            'ntbr_onedrive_name'                => NtbrChild::ONEDRIVE,
            'ntbr_ftp_sftp_name'                => NtbrChild::FTP_SFTP,
            'ntbr_ftp_name'                     => NtbrChild::FTP,
            'ntbr_sftp_name'                    => NtbrChild::SFTP,
            'ntbr_aws_name'                     => NtbrChild::AWS,
            'ntbr_hubic_name'                   => NtbrChild::HUBIC,
            'ntbr_sugarsync_name'               => NtbrChild::SUGARSYNC,
        );
//d($list_config);
        return parent::renderView();
    }

    public function displayFtpAccount()
    {
        $id_ntbr_ftp                = (int)Tools::getValue('id_ntbr_ftp');
        $ftp_account                = $this->ntbr->displayFtpAccount($id_ntbr_ftp);
        $ftp_account['password']    = NtbrChild::FAKE_MDP;

        die(Tools::jsonEncode(array('ftp_account' => $ftp_account)));
    }

    public function displayDropboxAccount()
    {
        $id_ntbr_dropbox            = (int)Tools::getValue('id_ntbr_dropbox');
        $dropbox_account            = $this->ntbr->displayDropboxAccount($id_ntbr_dropbox);
        $dropbox_account['token']   = NtbrChild::FAKE_MDP;

        die(Tools::jsonEncode(array('dropbox_account' => $dropbox_account)));
    }

    public function displayOwncloudAccount()
    {
        $id_ntbr_owncloud               = (int)Tools::getValue('id_ntbr_owncloud');
        $owncloud_account               = $this->ntbr->displayOwncloudAccount($id_ntbr_owncloud);
        $owncloud_account['password']   = NtbrChild::FAKE_MDP;

        die(Tools::jsonEncode(array('owncloud_account' => $owncloud_account)));
    }

    public function displayWebdavAccount()
    {
        $id_ntbr_webdav             = (int)Tools::getValue('id_ntbr_webdav');
        $webdav_account             = $this->ntbr->displayWebdavAccount($id_ntbr_webdav);
        $webdav_account['password'] = NtbrChild::FAKE_MDP;

        die(Tools::jsonEncode(array('webdav_account' => $webdav_account)));
    }

    public function displayGoogledriveAccount()
    {
        $id_ntbr_googledrive            = (int)Tools::getValue('id_ntbr_googledrive');
        $googledrive_account            = $this->ntbr->displayGoogledriveAccount($id_ntbr_googledrive);
        $googledrive_account['token']   = NtbrChild::FAKE_MDP;

        die(Tools::jsonEncode(array('googledrive_account' => $googledrive_account)));
    }

    public function displayOnedriveAccount()
    {
        $id_ntbr_onedrive           = (int)Tools::getValue('id_ntbr_onedrive');
        $onedrive_account           = $this->ntbr->displayOnedriveAccount($id_ntbr_onedrive);
        $onedrive_account['token']  = NtbrChild::FAKE_MDP;

        die(Tools::jsonEncode(array('onedrive_account' => $onedrive_account)));
    }

    public function displaySugarsyncAccount()
    {
        $id_ntbr_sugarsync          = (int)Tools::getValue('id_ntbr_sugarsync');
        $sugarsync_account          = $this->ntbr->displaySugarsyncAccount($id_ntbr_sugarsync);
        //$sugarsync_account['token'] = NtbrChild::FAKE_MDP;

        die(Tools::jsonEncode(array('sugarsync_account' => $sugarsync_account)));
    }

    public function displayHubicAccount()
    {
        $id_ntbr_hubic                  = (int)Tools::getValue('id_ntbr_hubic');
        $hubic_account                  = $this->ntbr->displayHubicAccount($id_ntbr_hubic);
        $hubic_account['token']         = NtbrChild::FAKE_MDP;
        $hubic_account['credential']    = NtbrChild::FAKE_MDP;

        die(Tools::jsonEncode(array('hubic_account' => $hubic_account)));
    }

    public function displayAwsAccount()
    {
        $id_ntbr_aws                        = (int)Tools::getValue('id_ntbr_aws');
        $aws_account                        = $this->ntbr->displayAwsAccount($id_ntbr_aws);
        $aws_account['acces_key_id']        = NtbrChild::FAKE_MDP;
        $aws_account['secret_access_key']   = NtbrChild::FAKE_MDP;

        die(Tools::jsonEncode(array('aws_account' => $aws_account)));
    }

    public function generateUrls()
    {
        $urls = $this->ntbr->generateSecureUrls($this->id_shop_group, $this->id_shop);

        die(Tools::jsonEncode(array('urls' => $urls)));
    }

    public function logMsg()
    {
        $msg    = Tools::getValue('msg');

        $this->ntbr->log($msg, true);

        die();
    }

    public function getBackupDownloadData()
    {
        $nb     = Tools::getValue('nb');
        $backup = $this->ntbr->findThisBackup($nb);

        if (count($backup) > 1) {
            $file_name  = $backup[$nb]['name'];
            $file_size  = $backup[$nb]['size_byte'];
            $backup_dir = $backup[$nb]['backup_dir'];
        } else {
            $file_name  = $backup[$nb.'.1']['name'];
            $file_size  = $backup[$nb.'.1']['size_byte'];
            $backup_dir = $backup[$nb.'.1']['backup_dir'];
        }

        if (!$file_size) {
            $file_path = $backup_dir.$file_name;
            $file_size = $this->ntbr->getFileSize($file_path);

            if (!$file_size) {
                $this->ntbr->log($this->ntbr->l('Error, the backup size cannot be found', self::PAGE), true);
                die(Tools::jsonEncode(array('result' => 0)));
            }
        }

        die(Tools::jsonEncode(array(
            'result'        => 1,
            'file_name'     => $file_name,
            'file_size'     => $file_size,
            'backup_dir'    => $backup_dir
        )));
    }

    public function downloadBackup()
    {
        $pos        = Tools::getValue('pos');
        $file_size  = Tools::getValue('file_size');

        $infos      = array(
            'file_name'     => Tools::getValue('file_name'),
            'backup_dir'    => Tools::getValue('backup_dir'),
            'content'       => '',
            'pos'           => $pos,
            'file_size'     => $file_size,
            'finish'        => 0,
            'progress'      => '',
        );

        $file_path = $infos['backup_dir'].$infos['file_name'];

        $size_to_read   = $infos['file_size'] - $infos['pos'];

        if ($size_to_read > NtbrChild::MAX_FILE_DOWNLOAD_SIZE) {
            $size_to_read = NtbrChild::MAX_FILE_DOWNLOAD_SIZE;
        }

        $infos['content'] = $this->ntbr->getContentFromFile($file_path, $infos['pos'], $size_to_read, false);

        if ($infos['content'] === false) {
            die(Tools::jsonEncode(array('result' => 0)));
        }

        $infos['pos'] += $size_to_read;

        $infos['progress'] = $this->ntbr->l('Download backup:', self::PAGE).' '
            .$this->ntbr->readableSize($infos['pos']).'/'.$this->ntbr->readableSize($infos['file_size']);

        if ($infos['pos'] >= $infos['file_size']) {
            $infos['finish'] = 1;
            $this->ntbr->log(
                $this->l('File downloaded:', self::PAGE).' '.$infos['file_name']
                .' ('.$infos['pos'].'/'.$infos['file_size'].')',
                true
            );
        }

        die($infos['content']);
        //die(Tools::jsonEncode(array('result' => 1, 'infos' => $infos)));
    }

    public function saveConfigProfile()
    {
        $is_default = (int)(bool)Tools::getValue('is_default');
        $name       = Tools::getValue('name');
        $type       = Tools::getValue('type');

        $result = $this->ntbr->saveConfigProfile($is_default, $name, $type);

        die(Tools::jsonEncode(array('result' => $result)));
    }

    public function deleteConfig()
    {
        $id_ntbr_config = (int)Tools::getValue('id_ntbr_config');
        $result         = $this->ntbr->deleteConfig($id_ntbr_config);

        die(Tools::jsonEncode(array('result' => $result)));
    }

    public function getTimeBetweenRefresh()
    {
        $id_ntbr_config = (int)Tools::getValue('id_ntbr_config');
        $config         = new Config($id_ntbr_config);

        die(Tools::jsonEncode(array('time_between_refresh' => $config->time_between_refresh)));
    }

    public function saveConfig()
    {
        $type_module    = $this->ntbr->getTypeModule();
        $light          = ($type_module=='')?0:1;

        $result = array(
            'success'   => 1,
            'errors'    => array()
        );

        $values = Apparatus::getAllValues();

        $id_ntbr_config                 = (int)(isset($values['id_ntbr_config'])?$values['id_ntbr_config']:0);
        $time_between_refresh           = (int)(isset($values['time_between_refresh'])?$values['time_between_refresh']:0);
        $time_pause_between_refresh     = (int)(isset($values['time_pause_between_refresh'])?$values['time_pause_between_refresh']:0);
        $time_between_progress_refresh  = (int)(isset($values['time_between_progress_refresh'])?$values['time_between_progress_refresh']:0);
        $is_default                     = (int)(bool)(isset($values['is_default'])?$values['is_default']:false);
        $name_config                    = (isset($values['name'])?$values['name']:'');
        $mail_backup                    = (isset($values['mail_backup'])?$values['mail_backup']:'');

        if ($time_pause_between_refresh) {
            if ($time_pause_between_refresh >= $time_between_refresh) {
                $result['errors'][] = $this->ntbr->l('The duration of the pause between two intermediate renewal must be inferior to the intermedial renewal value.', self::PAGE);
            }
        }

        if (!isset($name_config) || !$name_config) {
            $result['errors'][] = $this->ntbr->l('The name of the configuration is required.', self::PAGE);
        }

        $id_default_config = Config::getIdDefault();

        // If this config is the default one, we need to choose a
        // new default config before removing default for this one
        if ($id_default_config == $id_ntbr_config && !$is_default) {
            $result['errors'][] = $this->ntbr->l('At least one configuration must be the default one', self::PAGE);
        }

        if (count($result['errors'])) {
            $result['success'] = 0;
            die(Tools::jsonEncode(array('result' => $result)));
        }

        if (!$mail_backup) {
            $mail_backup = Configuration::get('PS_SHOP_EMAIL');
        }

        $config                                 = new Config($id_ntbr_config);
        $config->is_default                     = $is_default;
        $config->name                           = $name_config;
        $config->activate_log                   = (int)(bool)(isset($values['activate_log'])?$values['activate_log']:false);
        $config->nb_backup                      = (int)(isset($values['nb_backup'])?$values['nb_backup']:0);
        $config->mail_backup                    = $mail_backup;
        $config->dump_low_interest_tables       = (int)(bool)(isset($values['dump_low_interest_table'])?$values['dump_low_interest_table']:false);
        $config->disable_refresh                = (int)(bool)(isset($values['disable_refresh'])?$values['disable_refresh']:false);
        $config->disable_server_timeout         = (int)(bool)(isset($values['disable_server_timeout'])?$values['disable_server_timeout']:false);
        $config->increase_server_memory         = (int)(bool)(isset($values['increase_server_memory'])?$values['increase_server_memory']:false);
        $config->js_download                    = (int)(bool)(isset($values['js_download'])?$values['js_download']:false);
        $config->send_email                     = (int)(bool)(isset($values['send_email'])?$values['send_email']:false);
        $config->email_only_error               = (int)(bool)(isset($values['email_only_error'])?$values['email_only_error']:false);
        $config->maintenance                    = (int)(bool)(isset($values['maintenance'])?$values['maintenance']:false);
        $config->part_size                      = (int)(isset($values['part_size'])?$values['part_size']:0);
        $config->max_file_to_backup             = (int)(isset($values['max_file_to_backup'])?$values['max_file_to_backup']:0);
        $config->dump_max_values                = (int)(isset($values['dump_max_values'])?$values['dump_max_values']:0);
        $config->dump_lines_limit               = (int)(isset($values['dump_lines_limit'])?$values['dump_lines_limit']:0);
        $config->time_between_backups           = (int)(isset($values['time_between_backups'])?$values['time_between_backups']:0);
        $config->server_memory_value            = (int)(isset($values['server_memory_value'])?$values['server_memory_value']:0);
        $config->time_between_refresh           = (int)$time_between_refresh;
        $config->time_pause_between_refresh     = (int)$time_pause_between_refresh;
        $config->time_between_progress_refresh  = ($time_between_progress_refresh)?$time_between_progress_refresh:1;

        $old_backup_dir = $config->backup_dir;

        if (!$light) {
            $multi_config           = (int)(bool)(isset($values['multi_config'])?$values['multi_config']:false);
            $send_restore           = (int)(bool)(isset($values['send_restore'])?$values['send_restore']:false);
            $activate_xsendfile     = (int)(bool)(isset($values['activate_xsendfile'])?$values['activate_xsendfile']:false);
            $ignore_compression     = (int)(bool)(isset($values['ignore_compression'])?$values['ignore_compression']:false);
            $delete_local_backup    = (int)(bool)(isset($values['delete_local_backup'])?$values['delete_local_backup']:false);
            $create_on_distant      = (int)(bool)(isset($values['create_on_distant'])?$values['create_on_distant']:false);
            $ignore_product_image   = (int)(isset($values['ignore_product_image'])?$values['ignore_product_image']:0);
            $backup_dir             = (isset($values['backup_dir'])?$values['backup_dir']:'');
            $ignore_directories     = (isset($values['ignore_directories'])?$values['ignore_directories']:'');
            $ignore_file_types      = (isset($values['ignore_files_types'])?$values['ignore_files_types']:'');
            $ignore_tables          = (isset($values['ignore_tables'])?$values['ignore_tables']:'');
            $id_shop_group          = $this->id_shop_group;
            $id_shop                = $this->id_shop;

            $config = $this->ntbr->saveConfig(
                $config,
                $send_restore,
                $activate_xsendfile,
                $ignore_product_image,
                $ignore_compression,
                $delete_local_backup,
                $create_on_distant,
                $backup_dir,
                $ignore_directories,
                $ignore_file_types,
                $ignore_tables,
                $multi_config,
                $id_shop_group,
                $id_shop
            );

            if (!$config) {
                $result['success'] = 0;
            }

            if (!is_dir($config->backup_dir)) {
                $result['success'] = 0;
                $result['errors'][] = sprintf(
                    $this->ntbr->l('The backup directory "%s" does not exists', self::PAGE),
                    $config->backup_dir
                );
            }
        }

        if ($result['success']) {
            if (!$config->save()) {
                $result['success'] = 0;
            }
        }

        if (!$result['success']) {
            $result['errors'][] = $this->ntbr->l('Error during the saving of your configuration.', self::PAGE);
        } else {
            if ($old_backup_dir != $config->backup_dir) {
                $old_backups = $this->ntbr->findOldBackups($config->id);

                if (isset($old_backups['parts']) && count($old_backups['parts'])) {
                    foreach ($old_backups['parts'] as $backup_file) {
                        if (!copy($old_backup_dir.$backup_file['name'], $config->backup_dir.$backup_file['name'])) {
                            $result['success'] = 0;
                            $result['errors'][] = sprintf(
                                $this->ntbr->l('The backup %s could not be move to the new directory', self::PAGE),
                                $backup_file['name']
                            );
                        }
                    }
                }
            }
        }

        die(Tools::jsonEncode(array('result' => $result)));
    }

    public function saveAutomation()
    {
        $automation_2nt_ip      = (int)Tools::getValue('automation_2nt_ip');
        $id_shop_group          = $this->id_shop_group;
        $id_shop                = $this->id_shop;
        $result                 = false;
        $errors                 = array();
        $update_maintenance_ip  = false;

        // If not in localhost (so automation can be activated)
        if (Tools::isSubmit('automation_2nt')
            && Tools::isSubmit('automation_2nt_hours')
            && Tools::isSubmit('automation_2nt_minutes')
        ) {
            $automation_2nt         = (int)(bool)Tools::getValue('automation_2nt');
            $automation_2nt_hours   = (int)Tools::getValue('automation_2nt_hours');
            $automation_2nt_minutes = (int)Tools::getValue('automation_2nt_minutes');

            // If something change
            if ($this->ntbr->getConfig('NTBR_AUTOMATION_2NT', $id_shop_group, $id_shop) != $automation_2nt
                || $this->ntbr->getConfig('NTBR_AUTOMATION_2NT_HOURS', $id_shop_group, $id_shop) != $automation_2nt_hours
                || $this->ntbr->getConfig('NTBR_AUTOMATION_2NT_MINUTES', $id_shop_group, $id_shop) != $automation_2nt_minutes
            ) {
                // Call the 2NT cron url
                $shop_domain = Tools::getCurrentUrlProtocolPrefix().Tools::getHttpHost();
                $shop_url = $shop_domain.__PS_BASE_URI__;

                $url = CONFIGURE_NTCRON
                .'site='.urlencode($shop_url)
                .'&enable='.$automation_2nt
                .'&h='.$automation_2nt_hours
                .'&m='.$automation_2nt_minutes
                .'&fuseau_h='.urlencode(date_default_timezone_get())
                .'&securekey='.urlencode($this->ntbr->secure_key);

                $ntcron_result = Tools::file_get_contents($url);

                $result = ($ntcron_result == 'OK');

                if ($result) {
                    // Update with the new values
                    $this->ntbr->setConfig('NTBR_AUTOMATION_2NT', $automation_2nt, $id_shop_group, $id_shop);

                    $this->ntbr->setConfig(
                        'NTBR_AUTOMATION_2NT_HOURS',
                        $automation_2nt_hours,
                        $id_shop_group,
                        $id_shop
                    );

                    $this->ntbr->setConfig(
                        'NTBR_AUTOMATION_2NT_MINUTES',
                        $automation_2nt_minutes,
                        $id_shop_group,
                        $id_shop
                    );

                    if ($this->ntbr->getConfig('NTBR_AUTOMATION_2NT', $id_shop_group, $id_shop) != $automation_2nt) {
                        $result = false;
                    }

                    if ($this->ntbr->getConfig('NTBR_AUTOMATION_2NT_HOURS', $id_shop_group, $id_shop) != $automation_2nt_hours) {
                        $result = false;
                    }

                    if ($this->ntbr->getConfig('NTBR_AUTOMATION_2NT_MINUTES', $id_shop_group, $id_shop) != $automation_2nt_minutes) {
                        $result = false;
                    }

                    if ($automation_2nt) {
                        $update_maintenance_ip = true;
                    }
                }
            }
        } else {
            $result = true;
        }

        if ($this->ntbr->getConfig('NTBR_AUTOMATION_2NT_IP', $id_shop_group, $id_shop) != $automation_2nt_ip) {
            $this->ntbr->setConfig('NTBR_AUTOMATION_2NT_IP', $automation_2nt_ip, $id_shop_group, $id_shop);

            if ($this->ntbr->getConfig('NTBR_AUTOMATION_2NT_IP', $id_shop_group, $id_shop) != $automation_2nt_ip) {
                $result = false;
            }

            $update_maintenance_ip = true;
        }

        if (!$result) {
            $errors[] = $this->ntbr->l('Error during the saving of your automation.', self::PAGE);
        } elseif ($update_maintenance_ip) {
            // Update automation IP in maintenance
            $this->ntbr->setMaintenanceIP();
        }

        die(Tools::jsonEncode(array('result' => $result, 'errors' => $errors)));
    }

    public function saveFtp()
    {
        $id_ntbr_config     = (int)Tools::getValue('id_ntbr_config');
        $id_ntbr_ftp        = (int)Tools::getValue('id_ntbr_ftp');
        $name               = Tools::getValue('name');
        $active             = (int)(bool)Tools::getValue('active');
        $sftp               = (int)(bool)Tools::getValue('sftp');
        $ssl                = (int)(bool)Tools::getValue('ssl');
        $passive_mode       = (int)(bool)Tools::getValue('passive_mode');
        $config_nb_backup   = (int)Tools::getValue('config_nb_backup');
        $server             = Tools::getValue('server');
        $login              = Tools::getValue('login');
        $password           = Tools::getValue('password');
        $port               = (int)Tools::getValue('port');
        $directory          = Tools::getValue('directory');

        $result = $this->ntbr->saveFtp(
            $id_ntbr_config,
            $id_ntbr_ftp,
            $name,
            $active,
            $sftp,
            $ssl,
            $passive_mode,
            $config_nb_backup,
            $server,
            $login,
            $password,
            $port,
            $directory
        );

        die(Tools::jsonEncode(array('result' => $result)));
    }

    public function saveDropbox()
    {
        $id_ntbr_config     = (int)Tools::getValue('id_ntbr_config');
        $id_ntbr_dropbox    = (int)Tools::getValue('id_ntbr_dropbox');
        $name               = Tools::getValue('name');
        $active             = (int)(bool)Tools::getValue('active');
        $config_nb_backup   = (int)Tools::getValue('config_nb_backup');
        $code               = Tools::getValue('code');
        $directory          = Tools::getValue('directory');

        $result = $this->ntbr->saveDropbox(
            $id_ntbr_config,
            $id_ntbr_dropbox,
            $name,
            $active,
            $config_nb_backup,
            $code,
            $directory
        );

        die(Tools::jsonEncode(array('result' => $result)));
    }

    public function saveOwncloud()
    {
        $id_ntbr_config     = (int)Tools::getValue('id_ntbr_config');
        $id_ntbr_owncloud   = (int)Tools::getValue('id_ntbr_owncloud');
        $name               = Tools::getValue('name');
        $active             = (int)(bool)Tools::getValue('active');
        $config_nb_backup   = (int)Tools::getValue('config_nb_backup');
        $login              = Tools::getValue('login');
        $password           = Tools::getValue('password');
        $server             = Tools::getValue('server');
        $directory          = Tools::getValue('directory');

        $result = $this->ntbr->saveOwncloud(
            $id_ntbr_config,
            $id_ntbr_owncloud,
            $name,
            $active,
            $config_nb_backup,
            $login,
            $password,
            $server,
            $directory
        );

        die(Tools::jsonEncode(array('result' => $result)));
    }

    public function saveWebdav()
    {
        $id_ntbr_config     = (int)Tools::getValue('id_ntbr_config');
        $id_ntbr_webdav     = (int)Tools::getValue('id_ntbr_webdav');
        $name               = Tools::getValue('name');
        $active             = (int)(bool)Tools::getValue('active');
        $config_nb_backup   = (int)Tools::getValue('config_nb_backup');
        $login              = Tools::getValue('login');
        $password           = Tools::getValue('password');
        $server             = Tools::getValue('server');
        $directory          = Tools::getValue('directory');

        $result = $this->ntbr->saveWebdav(
            $id_ntbr_config,
            $id_ntbr_webdav,
            $name,
            $active,
            $config_nb_backup,
            $login,
            $password,
            $server,
            $directory
        );

        die(Tools::jsonEncode(array('result' => $result)));
    }

    public function saveGoogledrive()
    {
        $id_ntbr_config         = (int)Tools::getValue('id_ntbr_config');
        $id_ntbr_googledrive    = (int)Tools::getValue('id_ntbr_googledrive');
        $name                   = Tools::getValue('name');
        $active                 = (int)(bool)Tools::getValue('active');
        $config_nb_backup       = (int)Tools::getValue('config_nb_backup');
        $code                   = Tools::getValue('code');
        $directory_path         = Tools::getValue('directory_path');
        $directory_key          = Tools::getValue('directory_key');

        $result = $this->ntbr->saveGoogledrive(
            $id_ntbr_config,
            $id_ntbr_googledrive,
            $name,
            $active,
            $config_nb_backup,
            $code,
            $directory_path,
            $directory_key
        );

        die(Tools::jsonEncode(array('result' => $result)));
    }

    public function saveOnedrive()
    {
        $id_ntbr_config     = (int)Tools::getValue('id_ntbr_config');
        $id_ntbr_onedrive   = (int)Tools::getValue('id_ntbr_onedrive');
        $name               = Tools::getValue('name');
        $active             = (int)(bool)Tools::getValue('active');
        $config_nb_backup   = (int)Tools::getValue('config_nb_backup');
        $code               = Tools::getValue('code');
        $directory_path     = Tools::getValue('directory_path');
        $directory_key      = Tools::getValue('directory_key');

        $result = $this->ntbr->saveOnedrive(
            $id_ntbr_config,
            $id_ntbr_onedrive,
            $name,
            $active,
            $config_nb_backup,
            $code,
            $directory_path,
            $directory_key
        );

        die(Tools::jsonEncode(array('result' => $result)));
    }

    public function saveSugarsync()
    {
        $id_ntbr_config     = (int)Tools::getValue('id_ntbr_config');
        $id_ntbr_sugarsync  = (int)Tools::getValue('id_ntbr_sugarsync');
        $name               = trim(Tools::getValue('name'));
        $active             = (int)(bool)Tools::getValue('active');
        $config_nb_backup   = (int)Tools::getValue('config_nb_backup');
        $login              = trim(Tools::getValue('login'));
        $password           = trim(Tools::getValue('password'));
        $directory_path     = trim(Tools::getValue('directory_path'));
        $directory_key      = trim(Tools::getValue('directory_key'));

        $result = $this->ntbr->saveSugarsync(
            $id_ntbr_config,
            $id_ntbr_sugarsync,
            $name,
            $active,
            $config_nb_backup,
            $login,
            $password,
            $directory_path,
            $directory_key
        );

        die(Tools::jsonEncode(array('result' => $result)));
    }

    public function saveHubic()
    {
        $id_ntbr_config     = (int)Tools::getValue('id_ntbr_config');
        $id_ntbr_hubic      = (int)Tools::getValue('id_ntbr_hubic');
        $name               = Tools::getValue('name');
        $active             = (int)(bool)Tools::getValue('active');
        $config_nb_backup   = (int)Tools::getValue('config_nb_backup');
        $code               = Tools::getValue('code');
        $directory          = Tools::getValue('directory');

        $result = $this->ntbr->saveHubic(
            $id_ntbr_config,
            $id_ntbr_hubic,
            $name,
            $active,
            $config_nb_backup,
            $code,
            $directory
        );

        die(Tools::jsonEncode(array('result' => $result)));
    }

    public function saveAws()
    {
        $id_ntbr_config     = (int)Tools::getValue('id_ntbr_config');
        $id_ntbr_aws        = (int)Tools::getValue('id_ntbr_aws');
        $name               = Tools::getValue('name');
        $active             = (int)(bool)Tools::getValue('active');
        $config_nb_backup   = (int)Tools::getValue('config_nb_backup');
        $access_key_id      = Tools::getValue('access_key_id');
        $secret_access_key  = Tools::getValue('secret_access_key');
        $region             = Tools::getValue('region');
        $bucket             = Tools::getValue('bucket');
        $storage_class      = Tools::getValue('storage_class');
        $directory_key      = Tools::getValue('directory_key');
        $directory_path     = Tools::getValue('directory_path');

        $result = $this->ntbr->saveAws(
            $id_ntbr_config,
            $id_ntbr_aws,
            $name,
            $active,
            $config_nb_backup,
            $access_key_id,
            $secret_access_key,
            $region,
            $bucket,
            $storage_class,
            $directory_key,
            $directory_path
        );

        die(Tools::jsonEncode(array('result' => $result)));
    }

    public function checkConnectionFtp()
    {
        $id_ntbr_ftp    = (int)Tools::getValue('id_ntbr_ftp');
        $success        = $this->ntbr->checkConnectionFtp($id_ntbr_ftp);

        die(Tools::jsonEncode(array('success' => $success)));
    }

    public function checkConnectionDropbox()
    {
        $id_ntbr_dropbox    = (int)Tools::getValue('id_ntbr_dropbox');
        $success            = $this->ntbr->checkConnectionDropbox($id_ntbr_dropbox);

        die(Tools::jsonEncode(array('success' => $success)));
    }

    public function checkConnectionOwncloud()
    {
        $id_ntbr_owncloud   = (int)Tools::getValue('id_ntbr_owncloud');
        $success            = $this->ntbr->checkConnectionOwncloud($id_ntbr_owncloud);

        die(Tools::jsonEncode(array('success' => $success)));
    }

    public function checkConnectionWebdav()
    {
        $id_ntbr_webdav = (int)Tools::getValue('id_ntbr_webdav');
        $success        = $this->ntbr->checkConnectionWebdav($id_ntbr_webdav);

        die(Tools::jsonEncode(array('success' => $success)));
    }

    public function checkConnectionGoogledrive()
    {
        $id_ntbr_googledrive    = (int)Tools::getValue('id_ntbr_googledrive');
        $success                = $this->ntbr->checkConnectionGoogledrive($id_ntbr_googledrive);

        die(Tools::jsonEncode(array('success' => $success)));
    }

    public function checkConnectionOnedrive()
    {
        $id_ntbr_onedrive   = (int)Tools::getValue('id_ntbr_onedrive');
        $success            = $this->ntbr->checkConnectionOnedrive($id_ntbr_onedrive);

        die(Tools::jsonEncode(array('success' => $success)));
    }

    public function checkConnectionSugarsync()
    {
        $id_ntbr_sugarsync  = (int)Tools::getValue('id_ntbr_sugarsync');
        $success            = $this->ntbr->checkConnectionSugarsync($id_ntbr_sugarsync);

        die(Tools::jsonEncode(array('success' => $success)));
    }

    public function checkConnectionHubic()
    {
        $id_ntbr_hubic  = (int)Tools::getValue('id_ntbr_hubic');
        $success        = $this->ntbr->checkConnectionHubic($id_ntbr_hubic);

        die(Tools::jsonEncode(array('success' => $success)));
    }

    public function checkConnectionAws()
    {
        $id_ntbr_aws    = (int)Tools::getValue('id_ntbr_aws');
        $success        = $this->ntbr->checkConnectionAws($id_ntbr_aws);

        die(Tools::jsonEncode(array('success' => $success)));
    }

    public function deleteFtp()
    {
        $id_ntbr_ftp    = (int)Tools::getValue('id_ntbr_ftp');
        $success        = $this->ntbr->deleteFtp($id_ntbr_ftp);

        die(Tools::jsonEncode(array('success' => $success)));
    }

    public function deleteDropbox()
    {
        $id_ntbr_dropbox    = (int)Tools::getValue('id_ntbr_dropbox');
        $success            = $this->ntbr->deleteDropbox($id_ntbr_dropbox);

        die(Tools::jsonEncode(array('success' => $success)));
    }

    public function deleteOwncloud()
    {
        $id_ntbr_owncloud   = (int)Tools::getValue('id_ntbr_owncloud');
        $success            = $this->ntbr->deleteOwncloud($id_ntbr_owncloud);

        die(Tools::jsonEncode(array('success' => $success)));
    }

    public function deleteWebdav()
    {
        $id_ntbr_webdav = (int)Tools::getValue('id_ntbr_webdav');
        $success        = $this->ntbr->deleteWebdav($id_ntbr_webdav);

        die(Tools::jsonEncode(array('success' => $success)));
    }

    public function deleteGoogledrive()
    {
        $id_ntbr_googledrive    = (int)Tools::getValue('id_ntbr_googledrive');
        $success                = $this->ntbr->deleteGoogledrive($id_ntbr_googledrive);

        die(Tools::jsonEncode(array('success' => $success)));
    }

    public function deleteOnedrive()
    {
        $id_ntbr_onedrive   = (int)Tools::getValue('id_ntbr_onedrive');
        $success            = $this->ntbr->deleteOnedrive($id_ntbr_onedrive);

        die(Tools::jsonEncode(array('success' => $success)));
    }

    public function deleteSugarsync()
    {
        $id_ntbr_sugarsync  = (int)Tools::getValue('id_ntbr_sugarsync');
        $success            = $this->ntbr->deleteSugarsync($id_ntbr_sugarsync);

        die(Tools::jsonEncode(array('success' => $success)));
    }

    public function deleteHubic()
    {
        $id_ntbr_hubic  = (int)Tools::getValue('id_ntbr_hubic');
        $success        = $this->ntbr->deleteHubic($id_ntbr_hubic);

        die(Tools::jsonEncode(array('success' => $success)));
    }

    public function deleteAws()
    {
        $id_ntbr_aws    = (int)Tools::getValue('id_ntbr_aws');
        $success        = $this->ntbr->deleteAws($id_ntbr_aws);

        die(Tools::jsonEncode(array('success' => $success)));
    }

    public function deleteBackup()
    {
        $result = false;

        if (Tools::isSubmit('nb')) {
            $result = ($this->ntbr->deleteThisBackup(Tools::getValue('nb')));
        }

        die(Tools::jsonEncode(array('result' => $result)));
    }

    public function createBackup()
    {
        $current_time   = time();
        $id_config      = Config::getIdDefault();

        if (Tools::isSubmit('id_ntbr_config')) {
            $id_config = Tools::getValue('id_ntbr_config');
        }

        $config                 = new Config($id_config);
        $time_between_backups   = $config->time_between_backups;

        if ($time_between_backups <= 0) {
            $time_between_backups = NtbrCore::MIN_TIME_NEW_BACKUP;
        }

        $ntbr_ongoing = $this->ntbr->getConfig('NTBR_ONGOING');

        if ($current_time - $ntbr_ongoing >= $time_between_backups) {
            $this->ntbr->setConfig('NTBR_ONGOING', time());
            $this->ntbr->backup($id_config);
            $update = $this->ntbr->updateBackupList();
            die(Tools::jsonEncode(array('backuplist' => $update, 'warnings' => $this->ntbr->warnings)));
        } else {
            $time_to_wait = $time_between_backups - ($current_time - $ntbr_ongoing);
            $this->ntbr->log(
                'ERR'.sprintf(
                    $this->ntbr->l('For security reason, some time is needed between two backups. Please wait %d seconds', self::PAGE),
                    $time_to_wait
                )
            );
        }

        die();
    }

    public function refreshBackup()
    {
        $result = $this->ntbr->backup(Config::getIdDefault(), true);

        if ($result) {
            $update = $this->ntbr->updateBackupList();
            die(Tools::jsonEncode(array('backuplist' => $update, 'warnings' => $this->ntbr->warnings)));
        }

        die();
    }

    public function stopScript()
    {
        $this->ntbr->stopScript();
        die();
    }

    public function getDirectoryChidren()
    {
        $id_ntbr_config = (int)Tools::getValue('id_ntbr_config');
        $directory      = Tools::getValue('directory');

        $tree = $this->ntbr->getDirectoryTreeChildren($id_ntbr_config, $directory);

        die(Tools::jsonEncode(array('tree' => $tree)));
    }

    public function getDropboxFiles()
    {
        $id_ntbr_dropbox    = (int)Tools::getValue('id_ntbr_dropbox');
        $files              = $this->ntbr->getDropboxFilesList($id_ntbr_dropbox);

        if (is_array($files)) {
            $res = 1;
        } else {
            $res = 0;
        }

        die(Tools::jsonEncode(array('res' => $res,  'files' => $files)));
    }

    public function deleteDropboxFile()
    {
        $id_ntbr_dropbox    = (int)Tools::getValue('id_ntbr_dropbox');
        $nb_part            = (int)Tools::getValue('nb_part');
        $file_name          = Tools::getValue('file_name');

        $result = $this->ntbr->deleteDropboxFile($id_ntbr_dropbox, $file_name, $nb_part);

        die(Tools::jsonEncode(array('result' => (int)$result)));
    }

    public function downloadDropboxFile()
    {
        $id_ntbr_dropbox    = (int)Tools::getValue('id_ntbr_dropbox');
        $id_file            = Tools::getValue('id_file');

        $link = $this->ntbr->downloadDropboxFile($id_ntbr_dropbox, $id_file);

        die(Tools::jsonEncode(array('link' => $link)));
    }

    public function getGoogledriveFiles()
    {
        $id_ntbr_googledrive    = (int)Tools::getValue('id_ntbr_googledrive');
        $files                  = $this->ntbr->getGoogledriveFilesList($id_ntbr_googledrive);

        if (is_array($files)) {
            $res = 1;
        } else {
            $res = 0;
        }

        die(Tools::jsonEncode(array('res' => $res,  'files' => $files)));
    }

    public function downloadGoogledriveFile()
    {
        $id_ntbr_googledrive    = (int)Tools::getValue('id_ntbr_googledrive');
        $id_file                = Tools::getValue('id_file');

        $link = $this->ntbr->downloadGoogledriveFile($id_ntbr_googledrive, $id_file);

        die(Tools::jsonEncode(array('link' => $link)));
    }

    public function deleteGoogledriveFile()
    {
        $id_ntbr_googledrive    = (int)Tools::getValue('id_ntbr_googledrive');
        $nb_part                = (int)Tools::getValue('nb_part');
        $file_name              = Tools::getValue('file_name');

        $result = $this->ntbr->deleteGoogledriveFile($id_ntbr_googledrive, $file_name, $nb_part);

        die(Tools::jsonEncode(array('result' => (int)$result)));
    }

    public function getOnedriveFiles()
    {
        $id_ntbr_onedrive   = (int)Tools::getValue('id_ntbr_onedrive');
        $files              = $this->ntbr->getOnedriveFilesList($id_ntbr_onedrive);

        if (is_array($files)) {
            $res = 1;
        } else {
            $res = 0;
        }

        die(Tools::jsonEncode(array('res' => $res,  'files' => $files)));
    }

    public function downloadOnedriveFile()
    {
        $id_ntbr_onedrive   = (int)Tools::getValue('id_ntbr_onedrive');
        $id_file            = Tools::getValue('id_file');

        $link = $this->ntbr->downloadOnedriveFile($id_ntbr_onedrive, $id_file);

        die(Tools::jsonEncode(array('link' => $link)));
    }

    public function deleteOnedriveFile()
    {
        $id_ntbr_onedrive   = (int)Tools::getValue('id_ntbr_onedrive');
        $nb_part            = (int)Tools::getValue('nb_part');
        $file_name          = Tools::getValue('file_name');

        $result = $this->ntbr->deleteOnedriveFile($id_ntbr_onedrive, $file_name, $nb_part);

        die(Tools::jsonEncode(array('result' => (int)$result)));
    }

    public function getOwncloudFiles()
    {
        $id_ntbr_owncloud   = (int)Tools::getValue('id_ntbr_owncloud');
        $files              = $this->ntbr->getOwncloudFilesList($id_ntbr_owncloud);

        if (is_array($files)) {
            $res = 1;
        } else {
            $res = 0;
        }

        die(Tools::jsonEncode(array('res' => $res,  'files' => $files)));
    }

    public function downloadOwncloudFile()
    {
        $id_ntbr_owncloud   = (int)Tools::getValue('id_ntbr_owncloud');
        $id_file            = Tools::getValue('id_file');
        $file_size          = Tools::getValue('file_size');
        $pos                = Tools::getValue('pos');

        $length   = $file_size - $pos;

        if ($length > NtbrChild::MAX_FILE_DOWNLOAD_SIZE) {
            $length = NtbrChild::MAX_FILE_DOWNLOAD_SIZE;
        }

        $content = $this->ntbr->downloadOwncloudFile($id_ntbr_owncloud, $id_file, $pos, $length, $file_size);

        die($content);
    }

    public function deleteOwncloudFile()
    {
        $id_ntbr_owncloud   = (int)Tools::getValue('id_ntbr_owncloud');
        $nb_part            = (int)Tools::getValue('nb_part');
        $file_name          = Tools::getValue('file_name');

        $result = $this->ntbr->deleteOwncloudFile($id_ntbr_owncloud, $file_name, $nb_part);

        die(Tools::jsonEncode(array('result' => (int)$result)));
    }

    public function getWebdavFiles()
    {
        $id_ntbr_webdav = (int)Tools::getValue('id_ntbr_webdav');
        $files          = $this->ntbr->getWebdavFilesList($id_ntbr_webdav);

        if (is_array($files)) {
            $res = 1;
        } else {
            $res = 0;
        }

        die(Tools::jsonEncode(array('res' => $res,  'files' => $files)));
    }

    public function downloadWebdavFile()
    {
        $id_ntbr_webdav = (int)Tools::getValue('id_ntbr_webdav');
        $id_file        = Tools::getValue('id_file');
        $file_size      = Tools::getValue('file_size');
        $pos            = Tools::getValue('pos');

        $length   = $file_size - $pos;

        if ($length > NtbrChild::MAX_FILE_DOWNLOAD_SIZE) {
            $length = NtbrChild::MAX_FILE_DOWNLOAD_SIZE;
        }

        $content = $this->ntbr->downloadWebdavFile($id_ntbr_webdav, $id_file, $pos, $length, $file_size);

        die($content);
    }

    public function deleteWebdavFile()
    {
        $id_ntbr_webdav = (int)Tools::getValue('id_ntbr_webdav');
        $nb_part        = (int)Tools::getValue('nb_part');
        $file_name      = Tools::getValue('file_name');

        $result = $this->ntbr->deleteWebdavFile($id_ntbr_webdav, $file_name, $nb_part);

        die(Tools::jsonEncode(array('result' => (int)$result)));
    }

    public function getFtpFiles()
    {
        $id_ntbr_ftp    = (int)Tools::getValue('id_ntbr_ftp');
        $files          = $this->ntbr->getFtpFilesList($id_ntbr_ftp);

        if (is_array($files)) {
            $res = 1;
        } else {
            $res = 0;
        }

        die(Tools::jsonEncode(array('res' => $res,  'files' => $files)));
    }

    public function downloadFtpFile()
    {
        $id_ntbr_ftp    = (int)Tools::getValue('id_ntbr_ftp');
        $id_file        = Tools::getValue('id_file');
        $file_size      = Tools::getValue('file_size');
        $pos            = Tools::getValue('pos');

        $length   = $file_size - $pos;

        if ($length > NtbrChild::MAX_FILE_DOWNLOAD_SIZE) {
            $length = NtbrChild::MAX_FILE_DOWNLOAD_SIZE;
        }

        $content = $this->ntbr->downloadFtpFile($id_ntbr_ftp, $id_file, $pos, $length);

        die($content);
    }

    public function deleteFtpFile()
    {
        $id_ntbr_ftp    = (int)Tools::getValue('id_ntbr_ftp');
        $nb_part        = (int)Tools::getValue('nb_part');
        $file_name      = Tools::getValue('file_name');

        $result = $this->ntbr->deleteFtpFile($id_ntbr_ftp, $file_name, $nb_part);

        die(Tools::jsonEncode(array('result' => (int)$result)));
    }

    public function displayGoogledriveTree()
    {
        $id_ntbr_googledrive    = (int)Tools::getValue('id_ntbr_googledrive');
        $tree                   = $this->ntbr->displayGoogledriveTree($id_ntbr_googledrive);

        die(Tools::jsonEncode(array('tree' => $tree)));
    }

    public function displayGoogledriveTreeChild()
    {
        $id_ntbr_googledrive    = (int)Tools::getValue('id_ntbr_googledrive');
        $id_parent              = Tools::getValue('id_parent');
        $googledrive_dir        = Tools::getValue('googledrive_dir');
        $level                  = Tools::getValue('level');
        $path                   = Tools::getValue('path');

        $tree = $this->ntbr->displayGoogledriveTreeChild(
            $id_ntbr_googledrive,
            $id_parent,
            $googledrive_dir,
            $level,
            $path
        );

        die(Tools::jsonEncode(array('tree' => $tree)));
    }

    public function displayOnedriveTree()
    {
        $id_ntbr_onedrive   = (int)Tools::getValue('id_ntbr_onedrive');
        $tree               = $this->ntbr->displayOnedriveTree($id_ntbr_onedrive);

        die(Tools::jsonEncode(array('tree' => $tree)));
    }

    public function displayOnedriveTreeChild()
    {
        $id_ntbr_onedrive   = (int)Tools::getValue('id_ntbr_onedrive');
        $id_parent          = Tools::getValue('id_parent');
        $onedrive_dir       = Tools::getValue('onedrive_dir');
        $level              = Tools::getValue('level');
        $path               = Tools::getValue('path');

        $tree = $this->ntbr->displayOnedriveTreeChild($id_ntbr_onedrive, $id_parent, $onedrive_dir, $level, $path);

        die(Tools::jsonEncode(array('tree' => $tree)));
    }

    public function displaySugarsyncTree()
    {
        $id_ntbr_sugarsync  = (int)Tools::getValue('id_ntbr_sugarsync');
        $tree               = $this->ntbr->displaySugarsyncTree($id_ntbr_sugarsync);

        die(Tools::jsonEncode(array('tree' => $tree)));
    }

    public function displaySugarsyncTreeChild()
    {
        $id_ntbr_sugarsync  = (int)Tools::getValue('id_ntbr_sugarsync');
        $id_parent          = Tools::getValue('id_parent');
        $sugarsync_dir      = Tools::getValue('sugarsync_dir');
        $level              = Tools::getValue('level');
        $path               = Tools::getValue('path');

        $tree = $this->ntbr->displaySugarsyncTreeChild($id_ntbr_sugarsync, $id_parent, $sugarsync_dir, $level, $path);

        die(Tools::jsonEncode(array('tree' => $tree)));
    }

    public function displayAwsTree()
    {
        $id_ntbr_aws    = (int)Tools::getValue('id_ntbr_aws');
        $tree           = $this->ntbr->displayAwsTree($id_ntbr_aws);

        die(Tools::jsonEncode(array('tree' => $tree)));
    }

    public function displayAwsTreeChild()
    {
        $id_ntbr_aws    = (int)Tools::getValue('id_ntbr_aws');
        $directory_key  = Tools::getValue('directory_key');
        $directory_path = Tools::getValue('directory_path');
        $level          = Tools::getValue('level');

        $tree = $this->ntbr->displayAwsTreeChild($id_ntbr_aws, $directory_key, $directory_path, $level);

        die(Tools::jsonEncode(array('tree' => $tree)));
    }

    public function sendBackupAway()
    {
        $nb     = Tools::getValue('nb');
        $result = $this->ntbr->onlySendBackupAway($nb);

        if ($result !== false) {
            die(Tools::jsonEncode($result));
        }

        die();
    }

    public function getJsBackup()
    {
        $nb     = Tools::getValue('nb');
        $backup = $this->ntbr->findThisBackup($nb);

        if (count($backup) > 1) {
            $backup_name = preg_replace('/([0-9]+\.part\.)/', '', $backup[$nb]['name']);
        } else {
            $backup_name = $backup[$nb.'.1']['name'];
        }

        $config = new Config(Backups::getBackupIdConfig($backup_name));

        die(Tools::jsonEncode(array('js_download' => (int)$config->js_download)));
    }

    public function saveInfosBackup()
    {
        $backup_name    = Tools::getValue('backup_name');
        $backup_comment = Tools::getValue('backup_comment');
        $backup_safe    = Tools::getValue('backup_safe');

        if (!$backup_name || $backup_name == '') {
            die(Tools::jsonEncode(array('result' => '0')));
        }

        $infos = Backups::getBackupInfos($backup_name);

        if (isset($infos['id_ntbr_backups'])) {
            $backup = new Backups($infos['id_ntbr_backups']);
        }

        $backup->comment = $backup_comment;
        $backup->safe   = $backup_safe;

        if ($backup->save()) {
            die(Tools::jsonEncode(array('result' => '1')));
        }

        die(Tools::jsonEncode(array('result' => '0')));
    }

    public function restoreBackup()
    {
        $backup_name    = Tools::getValue('backup');
        $type_backup    = Tools::getValue('type_backup');

        $options_restore = $this->ntbr->restoreBackup($backup_name, $type_backup);

        if ($options_restore === false) {
            die(Tools::jsonEncode(array('result' => '0')));
        }

        $backup_infos = Backups::getBackupInfos($backup_name);

        die(Tools::jsonEncode(array('result' => '1', 'options' => $options_restore, 'infos' => $backup_infos)));
    }

    public function endRestoreBackup()
    {
        $backup_name    = Tools::getValue('backup_name');
        $comment        = Tools::getValue('comment');
        $safe           = Tools::getValue('safe');
        $id_ntbr_config = Tools::getValue('id_ntbr_config');

        $res = $this->ntbr->endLocalRestore($backup_name, $comment, $safe, $id_ntbr_config);

        if (!$res) {
            die(Tools::jsonEncode(array('result' => '0')));
        }

        die(Tools::jsonEncode(array('result' => '1')));
    }

    public function addBackup()
    {
        $backup_name    = Tools::getValue('backup');
        $id_config      = Tools::getValue('id_config');

        $backup = new Backups();
        $backup->id_ntbr_config = $id_config;
        $backup->backup_name    = $backup_name;
        $backup->comment        = '';
        $backup->safe           = 0;

        if (!$backup->add()) {
            $this->ntbr->log($this->l('The backup infos were not saved', self::PAGE), true);
            die(Tools::jsonEncode(array('result' => '0')));
        }

        $update = $this->ntbr->updateBackupList();

        die(Tools::jsonEncode(array('backup_list' => $update, 'result' => '1')));
    }

    public function initToolBarTitle()
    {
        $this->toolbar_title = $this->l('2NT Backup and Restore', self::PAGE);
    }

    /**
     * assign default action in page_header_toolbar_btn smarty var, if they are not set.
     * uses override to specifically add, modify or remove items
     *
     */
    public function initPageHeaderToolbar()
    {
        if (version_compare(_PS_VERSION_, '1.6.0', '>=') === true) {
            if ($this->display == 'view') {
                    $this->page_header_toolbar_btn['save'] = array(
                        'href' => '#',
                        'desc' => $this->l('Save', self::PAGE)
                    );
            }
            parent::initPageHeaderToolbar();
        }
    }

    /**
     * assign default action in toolbar_btn smarty var, if they are not set.
     * uses override to specifically add, modify or remove items
     *
     */
    public function initToolbar()
    {
        if (version_compare(_PS_VERSION_, '1.6.0', '>=') !== true) {
            if ($this->display == 'view') {
                // Default save button - action dynamically handled in javascript
                $this->toolbar_btn['save'] = array(
                    'href' => '#',
                    'desc' => $this->l('Save', self::PAGE)
                );
            }
        }
    }
}
