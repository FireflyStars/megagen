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

require_once(dirname(__FILE__).'/ntbr.php');

class NtbrChild extends NtbrCore
{
    const PAGE = 'ntbrfull';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get the module type (empty if full)
     *
     * @return  String  Light or empty
     */
    public function getTypeModule()
    {
        return '';
    }

    /**
     * Start local restore
     *
     * @param   string  $backup_name    Name of the backup
     * @param   string  $type_backup    Type of the backup
     *
     * @return  String|bool             Options of the restoration or false if failure
     */
    public function startLocalRestore($backup_name, $type_backup)
    {
        $old_restore            = $this->restore_file;
        $new_restore            = _PS_ROOT_DIR_.'/'.self::NEW_RESTORE_NAME;
        $list_old_new_backup    = array();
        $restore_backup_files   = array();
        $options_restore        = '';
        $backup_files           = $this->findOldBackups();
        $id_config              = $this->config->id;

        foreach ($backup_files as $b_file) {
            if (strpos($b_file['name'], $backup_name) !== false) {
                $restore_backup_files = $b_file['part'];
                $id_config = $b_file['id_config'];
            }
        }

        if (!file_exists($old_restore)) {
            return false;
        }

        $config     = new Config($id_config);
        $backup_dir = $config->backup_dir;

        foreach ($restore_backup_files as $old_backup_files) {
            if (!file_exists($backup_dir.$old_backup_files['name'])) {
                return false;
            }

            $list_old_new_backup[] = array(
                'old' => $backup_dir.$old_backup_files['name'],
                'new' => _PS_ROOT_DIR_.'/'.$old_backup_files['name']
            );
        }

        // Move restore and backup file to the root of the website
        if (!copy($old_restore, $new_restore)) {
            return false;
        }

        foreach ($list_old_new_backup as $old_new_backup) {
            if (!rename($old_new_backup['old'], $old_new_backup['new'])) {
                return false;
            }
        }

        $options_restore .= 'from_module=true';
        $options_restore .= '&db_server='.urlencode(_DB_SERVER_);
        $options_restore .= '&db_name='.urlencode(_DB_NAME_);
        $options_restore .= '&db_user='.urlencode(_DB_USER_);
        $options_restore .= '&db_passwd='.urlencode(_DB_PASSWD_);
        $options_restore .= '&l='.urlencode($this->context->language->iso_code);

        if ($type_backup == $this->type_backup_base) {
            $options_restore .= '&do_not_restore_files=true';
        } elseif ($type_backup == $this->type_backup_file) {
            $options_restore .= '&do_not_restore_database=true';
        }

        if (!$config->disable_refresh) {
            $options_restore .= '&activate_refresh=true&refresh_time='.urlencode($config->time_between_refresh);
        }

        if (!$config->disable_server_timeout) {
            $options_restore .= '&disable_time_limit=true';
        }

        return $options_restore;
    }

    /**
     * End local restore
     *
     * @param   string  $backup_name    Name of the backup
     * @param   string  $comment        Comment of the backup
     * @param   bool    $safe           If the backup is considered safe
     * @param   int     $id_ntbr_config Id of the config
     *
     * @return  String|bool             Options of the restoration or false if failure
     */
    public function endLocalRestore($backup_name, $comment, $safe, $id_ntbr_config)
    {
        $backup_files   = array();

        $config     = new Config($id_ntbr_config);
        $backup_dir = $config->backup_dir;

        if (file_exists(_PS_ROOT_DIR_.'/'. self::NEW_RESTORE_NAME)) {
            unlink(_PS_ROOT_DIR_.'/'. self::NEW_RESTORE_NAME);
        }

        if (Tools::strtolower(Tools::substr($backup_name, -3)) === 'tar') {
            $base_name = Tools::substr($backup_name, 0, -3);
        } else {
            $base_name = Tools::substr($backup_name, 0, -7);
        }

        if (($dir = opendir(_PS_ROOT_DIR_)) !== false) {
            while (($file = readdir($dir)) !== false) {
                if ($file == '.' || $file == '..' || is_dir(_PS_ROOT_DIR_.$file)) {
                    continue;
                }

                if (strpos($file, $base_name) !== false) {
                    $backup_files[] = array(
                        'old' => _PS_ROOT_DIR_.'/'.$file,
                        'new' => $backup_dir.$file,
                    );
                }
            }
        }

        // Move backup file to the module backup directory
        foreach ($backup_files as $old_new_backup) {
            if (!rename($old_new_backup['old'], $old_new_backup['new'])) {
                $this->log($this->l('The backup file was not put back in the module', self::PAGE), true);
                return false;
            }
        }

        if (!Backups::backupExist($backup_name)) {
            $backup = new Backups();
            $backup->id_ntbr_config = $id_ntbr_config;
            $backup->backup_name    = $backup_name;
            $backup->comment        = $comment;
            $backup->safe           = $safe;

            if (!$backup->add()) {
                $this->log($this->l('The backup infos were not saved', self::PAGE), true);
                return false;
            }
        }

        return true;
    }

    /**
     * Download a file
     *
     * @param   String  $path       Path of the file to download
     * @param   String  $mime       Type/mime of the file to download
     * @param   String  $filename   New name of the file to download (optional)
     */
    public function downloadFile($path, $mime, $filename = '')
    {
        //check if file exists
        if (is_dir($path) || !file_exists($path)) {
            header('HTTP/1.0 404 Not Found');
            die('404 Not Found');
        }

        if ($filename == '') {
            $filename = basename($path);
        }

        $xsendfile = Tools::apacheModExists('xsendfile') && $this->config->activate_xsendfile;

        if ($xsendfile) {//Use XSendFile module
            header('X-Sendfile: '.$path); //Apache
            header('X-Accel-Redirect: '.$path); //Nginx
            header('Content-Type: '.$mime);
            header('Content-Disposition: attachment; filename="'.$filename.'"');
        } else {//Using php
            parent::downloadFile($path, $mime, $filename);
        }
    }

    /**
     * Ignore product image in the backup
     *
     * @param   String  $current_normalized_file    Path of the file to check
     *
     * @return  bool                                If the file must be ignore or not
     */
    public function ignoreProductImage($current_normalized_file)
    {
        $ignore_this_file = false;

        //Check if it is a product image
        if (strpos($current_normalized_file, 'img/p/') !== false) {
            if ($this->config->ignore_product_image == 1
                && strpos($current_normalized_file, 'img/p/index.php') === false
            ) {
                $ignore_this_file = true;
            }
        } else {
            if ($this->config->ignore_product_image == 2) {
                $ignore_this_file = true;
            }
        }

        return $ignore_this_file;
    }

    /**
     * Get backup directory
     *
     * @return  String
     */
    public function getBackupDirectory()
    {
        if ($this->config->backup_dir) {
            if (file_exists($this->config->backup_dir)) {
                return $this->config->backup_dir;
            }
        }

        return NtBackupAndRestore::getModuleBackupDirectory();
    }

    /**
     * Get list of directories to ignore
     *
     * @return  array   List of directories to ignore
     */
    public function getDirectoriesToIgnore()
    {
        return $this->config->ignore_directories;
    }

    /**
     * getChildrenDirectories()
     *
     * Get children directories of a given directory
     *
     * @param   String  $dir        Parent directory
     * @param   int     $id_config  ID of the configuration
     *
     * @return  array   List of directories
     *
     */
    public function getChildrenDirectories($dir = '', $id_config = 0)
    {
        $list   = array();
        $root   = rtrim($this->normalizePath(_PS_ROOT_DIR_), '/').'/';

        if ($dir != '') {
            $dir    = rtrim($this->normalizePath($dir), '/').'/';
        }

        if (!$id_config) {
            $id_config = Config::getIdDefault();
        }

        $o_config = new Config($id_config);

        $directories_to_ignore  = explode(',', $o_config->ignore_directories);
        $directories            = glob($root.$dir.'{,.}**', GLOB_ONLYDIR|GLOB_BRACE);

        foreach ($directories_to_ignore as &$d) {
            $d = trim($d);
        }

        foreach ($directories as $directory) {
            $path   = str_replace($root, '', $directory);
            $name   = basename($path);

            if ($name == '.' || $name == '..') {
                continue;
            }

            $ignore         = (in_array($path, $directories_to_ignore))?1:0;
            $always_ignore  = ($this->shouldNeverBeBackuped($directory.'/'))?1:0;

            $list[]  = array(
                'path'          => $path,
                'name'          => $name,
                'ignore'        => $ignore,
                'always_ignore' => $always_ignore,
            );
        }

        return $list;
    }

    /**
     * Get list of file types to ignore
     *
     * @return  array   List of file types to ignore
     */
    public function getFileTypesToIgnore()
    {
        return $this->config->ignore_file_types;
    }

    /**
     * Get list of tables to ignore
     *
     * @return  array   List of tables to ignore
     */
    public function getTablesToIgnore()
    {
        return $this->config->ignore_tables;
    }

    /**
     * Get backup total size
     *
     * @return  float   Total size of the backup
     */
    public function getBackupTotalSize()
    {
        if (is_array($this->part_list) && count($this->part_list) > 0) {
            $total_size = 0;

            foreach ($this->part_list as $part) {
                $total_size += $this->getFileSize($part);
            }

            return $total_size;
        } else {
            if ($this->config->ignore_compression) {
                return $this->getFileSize($this->tar_file);
            } else {
                return $this->getFileSize($this->compressed_file);
            }
        }
    }

    /**
     * Get futur tar total size
     *
     * @param int $total_size Found total size at the moment
     *
     * @return  float   Total size of the futur tar
     */
    public function getFuturTarTotalSize()
    {
        if ($this->next_step == self::STEP_GET_FUTUR_TAR_SIZE) {
            $this->total_size               = 0;
            $this->position_file_list_file  = 0;
            $this->tar_files_size           = array();
            $this->part_number              = 1;
            $this->next_step                = self::STEP_GET_FUTUR_TAR_SIZE_CONTINUE;
        }

        if ($this->getFileSize($this->file_list_file) <= 0) {
            return false;
        }

        if (!is_resource($this->handle_file_list_file)) {
            return false;
        }

        fseek($this->handle_file_list_file, $this->position_file_list_file);

        $end_size = self::TAR_END_SIZE;

        while (!feof($this->handle_file_list_file)) {
            $this->checkStopScript();

            $line = rtrim(fgets($this->handle_file_list_file));

            if (!$line) {
                continue;
            }

            $pos_cut = strrpos($line, ':');

            if ($pos_cut === false) {
                continue;
            }

            $file = self::binaryToString(Tools::substr($line, 0, $pos_cut));

            if (!$file) {
                continue;
            }

            $start_size     = 0;
            $content_size   = 0;

            if ($file != '') {
                if ($file == $this->dump_file && $this->config->type_backup == $this->type_backup_complete) {
                    $path_module_backup = 'modules/'.$this->name.'/'.self::BACKUP_FOLDER.'/';
                    $path_in_tar = str_replace($this->config_backup_dir, $path_module_backup, $file);
                    $filename = ltrim($path_in_tar, '/');
                } else {
                    //Normalize path
                    $current_normalized_file = $this->normalizePath($file);
                    $filename = ltrim(self::getPart($current_normalized_file, $this->base_length), '/');
                }

                $filename_length    = self::getLength($filename);
                $file_size          = Tools::substr($line, ($pos_cut+1));

                if (!isset($this->tar_files_size[$this->part_number])) {
                    $this->tar_files_size[$this->part_number] = 0;
                }

                // Start file size
                $start_size += self::TAR_BLOCK_SIZE;

                if ($filename_length > 100) {
                    $start_size += (1 + floor($filename_length/self::TAR_BLOCK_SIZE) + (($filename_length%self::TAR_BLOCK_SIZE > 0)?1:0)) * self::TAR_BLOCK_SIZE;
                }

                // Content file size
                $content_size += floor(($file_size/self::TAR_BLOCK_SIZE) + (($file_size%self::TAR_BLOCK_SIZE > 0)?1:0)) * self::TAR_BLOCK_SIZE;

                $total_file_tar = $start_size + $content_size + $end_size;

                //Check if future tar file size bigger than authorized
                if ($this->part_size > 0) {
                    //Tar file should not be bigger than part_size
                    if (($this->tar_files_size[$this->part_number] + $total_file_tar) > $this->part_size) {
                        if ($this->part_number == 1) {
                            // Change the name of the first (and only) file, currently listed
                            $this->part_list = array($this->part_file.'.'.$this->part_number.'.part.tar');
                        }

                        // End tar files
                        $this->tar_files_size[$this->part_number] += $end_size;
                        $this->total_size += $end_size;

                        //The tar file will be too big, we need to have a new part
                        $this->part_number++;

                        if (!isset($this->tar_files_size[$this->part_number])) {
                            $this->tar_files_size[$this->part_number] = 0;
                        }

                        $this->part_list[]  = $this->part_file.'.'.$this->part_number.'.part.tar';
                    }
                }

                $this->tar_files_size[$this->part_number] += $start_size + $content_size;
                $this->total_size += $start_size + $content_size;
            }

            $this->position_file_list_file = ftell($this->handle_file_list_file);

            //refresh
            $this->refreshBackup(true);
        }

        // End tar files
        $this->tar_files_size[$this->part_number] += $end_size;
        $this->total_size += $end_size;

        rewind($this->handle_file_list_file);
        $this->position_file_list_file = 0;

        return true;
    }

    /**
     * deleteOldTar()
     *
     * Delete old tar files
     *
     * @return boolean
     *
     */
    public function deleteOldTar()
    {
        if ($this->config->ignore_compression) {
            return true;
        } else {
            return parent::deleteOldTar();
        }
    }

    /**
     * compressBackup()
     *
     * Compress backup
     *
     * @return  boolean     Success or failure of the operation
     *
     */
    protected function compressBackup()
    {
        if ($this->config->ignore_compression) {
            $this->total_size = $this->getFileSize($this->tar_file);
            return true;
        }

        return parent::compressBackup();
    }

    /**
     * Delete local backup if backup is sent away
     *
     * @return  boolean     Success or failure of the operation
     *
     */
    protected function deleteLocalBackup()
    {
        if ($this->send_away_success && $this->config->delete_local_backup) {
            $list_active_ftp_accounts           = FTP::getListActiveFtpAccounts($this->config->id);
            $list_active_dropbox_accounts       = Dropbox::getListActiveDropboxAccounts($this->config->id);
            $list_active_owncloud_accounts      = Owncloud::getListActiveOwncloudAccounts($this->config->id);
            $list_active_webdav_accounts        = Webdav::getListActiveWebdavAccounts($this->config->id);
            $list_active_googledrive_accounts   = Googledrive::getListActiveGoogledriveAccounts($this->config->id);
            $list_active_onedrive_accounts      = Onedrive::getListActiveOnedriveAccounts($this->config->id);
            $list_active_hubic_accounts         = Hubic::getListActiveHubicAccounts($this->config->id);
            $list_active_aws_accounts           = Aws::getListActiveAwsAccounts($this->config->id);
            $list_active_sugarsync_accounts     = Sugarsync::getListActiveSugarsyncAccounts($this->config->id);

            if (count($list_active_ftp_accounts)
                || count($list_active_dropbox_accounts)
                || count($list_active_owncloud_accounts)
                || count($list_active_webdav_accounts)
                || count($list_active_onedrive_accounts)
                || count($list_active_googledrive_accounts)
                || count($list_active_hubic_accounts)
                || count($list_active_aws_accounts)
                || count($list_active_sugarsync_accounts)
            ) {
                foreach ($this->part_list as $part) {
                    if (file_exists($part)) {
                        if (!$this->fileDelete($part)) {
                            $this->log($this->l('Delete local backup file failed:', self::PAGE).' '.$part);
                            return false;
                        }
                    }
                }
            }
        }

        return true;
    }

    /**
     * Initialize SFTP
     */
    public function initForSFTP()
    {
        if (!extension_loaded('openssl')) {
            return false;
        }

        $phpseclib_path = dirname(__FILE__).'/../lib/phpseclib/vendor/';
        set_include_path(get_include_path().PATH_SEPARATOR.$phpseclib_path);
        require_once($phpseclib_path.'autoload.php');
    }

    /**
     * Connect to Dropbox
     *
     * @param   String  $access_token   Dropbox token (optional)
     *
     * @return  object                  new DropboxLib
     */
    public function connectToDropbox($access_token = '')
    {
        $sdk_uri = $this->module_path.'lib/dropbox/';
        $physic_sdk_uri = $this->module_path_physic.'lib/dropbox/';

        if (!empty($access_token)) {
            return new DropboxLib($this, $sdk_uri, $physic_sdk_uri, $access_token);
        } else {
            return new DropboxLib($this, $sdk_uri, $physic_sdk_uri);
        }
    }

    /**
     * Connect to ownCloud/Nextcloud
     *
     * @param   String  $server     ownCloud server
     * @param   String  $user       ownCloud user
     * @param   String  $pass       ownCloud password
     *
     * @return  object                  new OwncloudLib
     */
    public function connectToOwncloud($server, $user, $pass)
    {
        $sdk_uri = $this->module_path.'lib/owncloud/';
        $physic_sdk_uri = $this->module_path_physic.'lib/owncloud/';

        return new OwncloudLib($this, $server, $user, $pass, $sdk_uri, $physic_sdk_uri);
    }

    /**
     * Connect to WebDAV
     *
     * @param   String  $url    WebDAV url
     * @param   String  $user   WebDAV user
     * @param   String  $pass   WebDAV password
     *
     * @return  object          new WebdavLib
     */
    public function connectToWebdav($url, $user, $pass)
    {
        $sdk_uri = $this->module_path.'lib/webdav/';
        $physic_sdk_uri = $this->module_path_physic.'lib/webdav/';

        return new WebdavLib($this, $url, $user, $pass, $sdk_uri, $physic_sdk_uri);
    }

    /**
     * Connect to AWS
     *
     * @param   String  $aws_id_key     AWS ID key
     * @param   String  $aws_key        AWS secret key
     * @param   String  $aws_region     AWS region
     * @param   String  $aws_bucket     AWS bucket
     *
     * @return  object                  new AwsLib
     */
    public function connectToAws($aws_id_key, $aws_key, $aws_region, $aws_bucket)
    {
        $sdk_uri = $this->module_path.'lib/aws/';
        $physic_sdk_uri = $this->module_path_physic.'lib/aws/';

        return new AwsLib($this, $aws_id_key, $aws_key, $aws_region, $aws_bucket, $sdk_uri, $physic_sdk_uri);
    }

    /**
     * Connect to Openstack
     *
     * @param   String  $access_token   Openstack token
     * @param   String  $end_point      Openstack end point
     * @param   String  $account_type   Openstack account type
     *
     * @return  object                  new OpenstackLib
     */
    public function connectToOpenstack($access_token, $end_point, $account_type)
    {
        $sdk_uri = $this->module_path.'lib/openstack/';
        $physic_sdk_uri = $this->module_path_physic.'lib/openstack/';

        return new OpenstackLib($this, $sdk_uri, $physic_sdk_uri, $access_token, $end_point, $account_type);
    }

    /**
     * Connect to Google Drive
     *
     * @param   String  $access_token   Google Drive token (optional)
     *
     * @return  object                  new GoogledriveLib
     */
    public function connectToGoogledrive($access_token = '')
    {
        $access_right   = GoogledriveLib::DRIVE;
        $sdk_uri        = $this->module_path.'lib/googledrive/';
        $physic_sdk_uri = $this->module_path_physic.'lib/googledrive/';

        if (!empty($access_token)) {
            $decode_token = Tools::jsonDecode($access_token, true);

            // If token expire in 30 minutes
            $expired = ($decode_token['created'] + ($decode_token['expires_in'] - 1800)) < time();

            if ($expired) {
                $refresh_token  = $this->decrypt($decode_token['refresh_token']);
                $decode_token   = $this->getGoogledriveRefreshToken($refresh_token);
            } else {
                $decode_token['access_token'] = $this->decrypt($decode_token['access_token']);
            }

            return new GoogledriveLib($this, $access_right, $sdk_uri, $physic_sdk_uri, $decode_token['access_token']);
        } else {
            return new GoogledriveLib($this, $access_right, $sdk_uri, $physic_sdk_uri);
        }
    }

    /**
     * Connect to OneDrive
     *
     * @param   String      $access_token       OneDrive token (optional)
     * @param   integer     $id_ntbr_onedrive   ID OneDrive account (optional)
     *
     * @return  object                          new OnedriveLib
     */
    public function connectToOnedrive($access_token = '', $id_ntbr_onedrive = 0)
    {
        $sdk_uri        = $this->module_path.'lib/onedrive/';
        $physic_sdk_uri = $this->module_path_physic.'lib/onedrive/';

        if (!empty($access_token)) {
            $decode_token = Tools::jsonDecode($access_token, true);

            // If token expire in 30 minutes
            $expired = ($decode_token['created'] + ($decode_token['expires_in'] - 1800)) < time();

            if ($expired) {
                $refresh_token = $this->decrypt($decode_token['refresh_token']);
                $access_token = $this->getOnedriveRefreshToken($refresh_token);
                $decode_token = $access_token;

                $access_token['access_token']   = $this->encrypt($access_token['access_token']);
                $access_token['refresh_token']  = $this->encrypt($access_token['refresh_token']);

                if ($id_ntbr_onedrive) {
                    $onedrive           = new Onedrive($id_ntbr_onedrive);
                    $onedrive->token    = Tools::jsonEncode($access_token);
                    $onedrive->update();
                }
            } else {
                $decode_token['access_token'] = $this->decrypt($decode_token['access_token']);
            }

            return new OnedriveLib($this, $sdk_uri, $physic_sdk_uri, $decode_token['access_token']);
        } else {
            return new OnedriveLib($this, $sdk_uri, $physic_sdk_uri);
        }
    }

    /**
     * Connect to hubiC
     *
     * @param   integer     $id_hubic_account   ID hubiC account (optional)
     *
     * @return  object                          new HubicLib
     */
    public function connectToHubic($id_hubic_account = '0')
    {
        $sdk_uri        = $this->module_path.'lib/hubic/';
        $physic_sdk_uri = $this->module_path_physic.'lib/hubic/';
        $access_token   = '';
        $credential     = '';

        if ($id_hubic_account) {
            $hubic          = new Hubic($id_hubic_account);
            $access_token   = $hubic->token;
            $credential     = $hubic->credential;
        }

        if (!empty($access_token)) {
            $decode_token = Tools::jsonDecode($access_token, true);

            // If token expire in 30 minutes
            if (($decode_token['created'] + ($decode_token['expires_in'] - 1800)) < time()) {
                $refresh_token = $this->decrypt($decode_token['refresh_token']);
                $connect_infos = $this->getHubicRefreshToken($refresh_token);

                if (!is_array($connect_infos)
                    || !isset($connect_infos['token'])
                    || !isset($connect_infos['credential'])
                ) {
                    $this->log(
                        sprintf($this->l('Error while getting %s refresh token', self::PAGE), self::HUBIC)
                    );
                    return false;
                }

                $decode_token       = $connect_infos['token'];
                $decode_credential  = $connect_infos['credential'];

                $connect_infos['token']['access_token']     = $this->encrypt($connect_infos['token']['access_token']);
                $connect_infos['token']['refresh_token']    = $this->encrypt($connect_infos['token']['refresh_token']);

                $connect_infos['credential']['token']       = $this->encrypt($connect_infos['credential']['token']);

                $hubic->token       = Tools::jsonEncode($connect_infos['token']);
                $hubic->credential  = Tools::jsonEncode($connect_infos['credential']);

                if (!$hubic->update()) {
                    $this->log(
                        sprintf($this->l('Error while updating %s token and credentials', self::PAGE), self::HUBIC)
                    );
                    return false;
                }
            } else {
                $decode_credential              = Tools::jsonDecode($credential, true);

                $decode_token['access_token']   = $this->decrypt($decode_token['access_token']);
                $decode_credential['token']     = $this->decrypt($decode_credential['token']);
            }

            $hubic_lib = new HubicLib($this, $sdk_uri, $physic_sdk_uri, $decode_token['access_token']);

            // If credential expired or no credential
            if (!isset($decode_credential['expires']) || strtotime($decode_credential['expires']) <= time()) {
                $new_credential             = $hubic_lib->getCredential();
                $new_credential['token']    = $this->encrypt($new_credential['token']);

                $hubic->credential  = Tools::jsonEncode($new_credential);

                if (!$hubic->update()) {
                    $this->log(
                        sprintf($this->l('Error while updating %s credentials', self::PAGE), self::HUBIC)
                    );
                    return false;
                }
            } else {
                $hubic_lib->setCredential($decode_credential['token'], $decode_credential['endpoint']);
            }

            return $hubic_lib;
        } else {
            return new HubicLib($this, $sdk_uri, $physic_sdk_uri);
        }

        return false;
    }

    /**
     * Connect to SugarSync
     *
     * @param   String  $access_token       SugarSync token (optional)
     * @param   integer $id_ntbr_sugarsync  ID SugarSync account (optional)
     *
     * @return  object                  new SugarsyncLib
     */
    public function connectToSugarsync($access_token = '', $id_ntbr_sugarsync = 0)
    {
        $sdk_uri = $this->module_path.'lib/sugarsync/';
        $physic_sdk_uri = $this->module_path_physic.'lib/sugarsync/';

        if (!empty($access_token)) {
            $decode_token = Tools::jsonDecode($access_token, true);

            // If token expire in 30 minutes
            $expired = (strtotime($decode_token['expire_in']) - 1800) < time();

            if ($expired) {
                $refresh_token  = $this->decrypt($decode_token['refresh_token']);
                $decode_token   = $this->getSugarsyncAccessToken($refresh_token);

                //$this->log($refresh_token, true);

                if ($id_ntbr_sugarsync) {
                    $sugarsync          = new Sugarsync($id_ntbr_sugarsync);
                    $sugarsync->token   = Tools::jsonEncode($decode_token);
                    $sugarsync->update();
                }
            }

            $decode_token['access_token'] = $this->decrypt($decode_token['access_token']);

            return new SugarsyncLib(
                $this,
                $sdk_uri,
                $physic_sdk_uri,
                $decode_token['access_token'],
                $decode_token['user']
            );
        } else {
            return new SugarsyncLib($this, $sdk_uri, $physic_sdk_uri);
        }
    }

    /**
     * Test connection to Dropbox
     *
     * @param   String      $token  Dropbox token
     *
     * @return  boolean             The success or failure of the connection
     */
    public function testDropboxConnection($token)
    {
        $dropbox_lib = $this->connectToDropbox($token);
        return (bool)$dropbox_lib->testConnection();
    }

    /**
     * Test connection to ownCloud/Nextcloud
     *
     * @param   String      $server     ownCloud server
     * @param   String      $user       ownCloud user
     * @param   String      $pass       ownCloud password
     *
     * @return  boolean                 The success or failure of the connection
     */
    public function testOwncloudConnection($server, $user, $pass)
    {
        $owncloud_lib = $this->connectToOwncloud($server, $user, $pass);
        return (bool)$owncloud_lib->testConnection();
    }

    /**
     * Test connection to WebDAV
     *
     * @param   String      $url    WebDAV url
     * @param   String      $user   WebDAV user
     * @param   String      $pass   WebDAV password
     *
     * @return  boolean             The success or failure of the connection
     */
    public function testWebdavConnection($url, $user, $pass)
    {
        $webdav_lib = $this->connectToWebdav($url, $user, $pass);
        return (bool)$webdav_lib->testConnection();
    }

    /**
     * Test connection to Google Drive
     *
     * @param   String      $token  Google Drive token
     *
     * @return  boolean             The success or failure of the connection
     */
    public function testGoogledriveConnection($token)
    {
        $googledrive_lib = $this->connectToGoogledrive($token);
        return (bool)$googledrive_lib->testConnection();
    }

    /**
     * Test connection to OneDrive
     *
     * @param   String      $token              OneDrive token
     * @param   integer     $id_ntbr_onedrive   ID OneDrive account
     *
     * @return  boolean                         The success or failure of the connection
     */
    public function testOnedriveConnection($token, $id_ntbr_onedrive)
    {
        $onedrive_lib = $this->connectToOnedrive($token, $id_ntbr_onedrive);
        return (bool)$onedrive_lib->testConnection();
    }

    /**
     * Test connection to hubiC
     *
     * @param   integer     $id_hubic_account   ID hubiC account
     *
     * @return  boolean                         The success or failure of the connection
     */
    public function testHubicConnection($id_hubic_account)
    {
        $hubic_lib = $this->connectToHubic($id_hubic_account);

        return (bool)$hubic_lib->testConnection();
    }

    /**
     * Test connection to AWS
     *
     * @param   String     $aws_id_key      AWS ID key
     * @param   String     $aws_key         AWS secret key
     * @param   String     $aws_region      AWS region
     * @param   String     $aws_bucket      AWS bucket
     *
     * @return  boolean                     The success or failure of the connection
     */
    public function testAwsConnection($aws_id_key, $aws_key, $aws_region, $aws_bucket)
    {
        $aws_lib = $this->connectToAws($aws_id_key, $aws_key, $aws_region, $aws_bucket);
        return (bool)$aws_lib->testConnection();
    }

    /**
     * Test connection to FTP
     *
     * @param   String      $ftp_server     FTP server
     * @param   String      $ftp_login      FTP login
     * @param   String      $ftp_pass       FTP password
     * @param   integer     $ftp_port       FTP port
     * @param   boolean     $ssl            FTP ssl (enable or disable)
     * @param   boolean     $pasv           FTP passive mode (enable or disable)
     *
     * @return  boolean                     The success or failure of the connection
     */
    public function testFTP($ftp_server, $ftp_login, $ftp_pass, $ftp_port, $ssl = false, $pasv = false)
    {
        if ($ssl) {
            $connection = ftp_ssl_connect($ftp_server, (int)$ftp_port, self::FTP_TIMEOUT);
        } else {
            // Beware of the warning from php if failure
            $connection = @ftp_connect($ftp_server, (int)$ftp_port, self::FTP_TIMEOUT);
        }

        if (!$connection) {
            $this->log(
                sprintf(
                    $this->l('Impossible to connect to the %s. Please check your credential.', self::PAGE),
                    self::FTP
                )
            );

            return false;
        }

        // Beware of the warning from php if failure
        $return = @ftp_login($connection, $ftp_login, $ftp_pass);

        if ($return) {
            $return = ftp_pasv($connection, $pasv);
        }

        ftp_close($connection);

        if (!$return) {
            $this->log(
                sprintf(
                    $this->l('Impossible to connect to the %s. Please check your credential.', self::PAGE),
                    self::FTP
                )
            );
        }

        return $return;
    }

    /**
     * Test connection to SFTP
     *
     * @param   String      $ftp_server     SFTP server
     * @param   String      $ftp_login      SFTP login
     * @param   String      $ftp_pass       SFTP password
     * @param   integer     $ftp_port       SFTP port
     *
     * @return  boolean                     The success or failure of the connection
     */
    public function testSFTP($ftp_server, $ftp_login, $ftp_pass, $ftp_port)
    {
        $return = true;

        $this->initForSFTP();

        $sftp_lib = new \phpseclib\Net\SFTP($ftp_server, $ftp_port);

        // Beware of the warning from php if failure
        if (!@$sftp_lib->login($ftp_login, $ftp_pass)) {
            $return = false;
        }

        // The closing of the ftp can sometime bug so we can't test it
        if (!$this->closeSFTP($sftp_lib)) {
            //$return = false;
        }

        return $return;
    }

    /**
     * Test connection to SugarSync
     *
     * @param   String      $token              SugarSync token
     * @param   integer     $id_ntbr_sugarsync  ID SugarSync account
     *
     * @return  boolean                         The success or failure of the connection
     */
    public function testSugarsyncConnection($token, $id_ntbr_sugarsync)
    {
        $sugarsync_lib = $this->connectToSugarsync($token, $id_ntbr_sugarsync);
        return (bool)$sugarsync_lib->testConnection();
    }

    /**
     * Send a file on a Dropbox account
     *
     * @return  boolean     The success or failure of the operation
     */
    protected function sendFileToDropbox()
    {
        $dropbox        = new Dropbox($this->dropbox_account_id);
        $access_token   = $this->decrypt($dropbox->token);

        if ($this->next_step == $this->step_send['dropbox']
            && (!isset($this->dropbox_nb_part)
            || $this->dropbox_nb_part == 1)
        ) {
            $this->log(sprintf($this->l('Connect to your %s account...', self::PAGE), self::DROPBOX));
        }

        $dropbox_lib = $this->connectToDropbox($access_token);

        if (!$dropbox_lib->testConnection()) {
            $this->log(
                'WAR'.sprintf($this->l('Connection to your %s account impossible', self::PAGE), self::DROPBOX)
            );
            return false;
        }

        if ($this->next_step == $this->step_send['dropbox']
            && (!isset($this->dropbox_nb_part)
            || $this->dropbox_nb_part == 1)
        ) {
            $this->dropbox_dir = $dropbox->directory;

            //Dropbox dir should start with a "/" except for root
            if ($this->dropbox_dir != '' && $this->dropbox_dir[0] !== '/') {
                $this->dropbox_dir = '/'.$this->dropbox_dir;
            }

            $temp_directory = $this->dropbox_dir;

            //Dropbox dir should end with a "/" except when testing if exist
            if (Tools::substr($this->dropbox_dir, -1) != '/') {
                $temp_directory .= '/';
            }

            // If file already on Dropbox
            foreach ($this->part_list as $part) {
                $file_path          = $part;
                $file_destination   = $temp_directory.basename($part);

                $this->log(
                    $this->l('Check if there is a previous version of the file:', self::PAGE).' '.$file_destination
                );

                if ($dropbox_lib->checkExists($file_destination)) {
                    $this->log($this->l('Delete previous version of the file:', self::PAGE).' '.$file_destination);

                    if ($dropbox_lib->deleteFile($file_destination) === false) {
                        $this->log($this->l('Error while deleting the file:', self::PAGE).' '.$file_destination);
                    }
                }
            }

            // Get old backups list
            $old_backups        = $this->getDropboxFiles($dropbox_lib, $temp_directory);
            $nb_backup_to_keep  = $dropbox->config_nb_backup;
            $nb_files           = count($old_backups);
            $size_old_backups   = 0;

            // Get old backup (to delete) size
            if ($nb_backup_to_keep > 0) {
                while ($nb_files >= $nb_backup_to_keep) {
                    if (isset($old_backups[$nb_files])) {
                        $size_old_backups += $old_backups[$nb_files]['size_byte'];

                        $nb_files--;
                    }
                }
            }

            // Get available size
            $available_space = $dropbox_lib->getAvailableQuota() + $size_old_backups;

            // Get size to upload
            $to_send_size = $this->total_size;

            if ($this->config->send_restore) {
                $total_file_size = $this->getFileSize($this->restore_file);
                $to_send_size += $total_file_size;
            }

            // Check if we will have enough size after deleting old backup, too add new backup
            if ($available_space < $to_send_size) {
                $this->log(
                    'WAR'.sprintf($this->l('Sending backup to %s account:', self::PAGE), self::DROPBOX).' '
                    .$this->l('Error there will not be enough space available, even if we delete the old backup', self::PAGE)
                );
                return false;
            }

            // Delete old backup
            if (!$this->deleteDropboxOldBackup($access_token, $old_backups)) {
                $this->log(
                    'WAR'.sprintf($this->l('Sending backup to %s account:', self::PAGE), self::DROPBOX).' '
                    .$this->l('Error while deleting old backup', self::PAGE)
                );
                return false;
            }

            // Check available space
            $new_available_space = $dropbox_lib->getAvailableQuota();

            if ($this->config->send_restore) {
                $total_file_size = $this->getFileSize($this->restore_file);
                $new_available_space -= $total_file_size;
            }

            if ($new_available_space <= $this->total_size) {
                $this->log(
                    'WAR'.sprintf($this->l('Sending backup to %s account:', self::PAGE), self::DROPBOX).' '
                    .$this->l('Not enough space available', self::PAGE)
                );
                return false;
            }

            $this->log(
                sprintf($this->l('Sending backup to %s account:', self::PAGE), self::DROPBOX).' '
                .$this->l('Check the path of your backup', self::PAGE)
            );

            // Check if the folder we want to use exists. If not we create it.
            if ($dropbox_lib->checkExists($this->dropbox_dir) === false) {
                $this->log(
                    sprintf($this->l('Sending backup to %s account:', self::PAGE), self::DROPBOX)
                    .' '.$this->l('Create the directory', self::PAGE).' "'.$this->dropbox_dir.'"'
                );

                if ($dropbox_lib->createFolder($this->dropbox_dir) === false) {
                    $this->log(
                        'WAR'.sprintf($this->l('Sending backup to %s account:', self::PAGE), self::DROPBOX)
                        .' '.$this->l('Error while creating the directory', self::PAGE).' "'.$this->dropbox_dir.'"'
                    );
                    return false;
                }
            }

            //Dropbox dir should end with a "/" except when testing if exist
            $this->dropbox_dir = $temp_directory;

            $this->log(sprintf($this->l('Sending backup to %s account...', self::PAGE), self::DROPBOX));

            $this->dropbox_nb_part = 1;
            $this->dropbox_position = 0;
        }

        $nb_part    = 1;

        foreach ($this->part_list as $part) {
            if ($nb_part == $this->dropbox_nb_part) {
                $file_path          = $part;
                $file_destination   = $this->dropbox_dir.basename($part);

                // Upload the file
                if ($this->next_step == $this->step_send['dropbox']) {
                    $this->next_step = $this->step_send['dropbox_resume'];

                    $upload_file = $dropbox_lib->uploadFile(
                        $file_path,
                        $file_destination,
                        $this->dropbox_nb_part,
                        $this->total_nb_part
                    );

                    if ($upload_file === false) {
                        return false;
                    }
                } else { // Resume the upload
                    $resume_upload_file = $dropbox_lib->resumeUploadFile(
                        $file_path,
                        $file_destination,
                        $this->dropbox_upload_id,
                        $this->dropbox_position,
                        $this->dropbox_nb_part,
                        $this->total_nb_part
                    );

                    if ($resume_upload_file === false) {
                        return false;
                    }
                }

                $this->dropbox_nb_part++;
                // New part, so back to init values
                $this->next_step = $this->step_send['dropbox'];
                $this->dropbox_position = 0;
            }
            $nb_part++;
        }

        $this->next_step = $this->step_send['dropbox_resume'];

        if ($this->config->send_restore) {
            $this->log(
                sprintf($this->l('Sending restore file to %s account...', self::PAGE), self::DROPBOX)
            );

            // Upload the file
            if ($dropbox_lib->uploadFile($this->restore_file, $this->dropbox_dir.self::NEW_RESTORE_NAME) === false) {
                return false;
            }
        }

        return true;
    }

    /**
     * Create a tar file on a Dropbox account
     *
     * @return  boolean     The success or failure of the operation
     */
    protected function createTarOnDropbox()
    {
        $dropbox        = new Dropbox($this->dropbox_account_id);
        $access_token   = $this->decrypt($dropbox->token);

        $this->checkStopScript();

        if ($this->next_step == $this->step_send['dropbox']
            && $this->secondary_next_step  == self::SECONDARY_STEP_TAR_FILE
            && (!isset($this->dropbox_nb_part) || $this->dropbox_nb_part == 1)
        ) {
            $this->log(sprintf($this->l('Connect to your %s account...', self::PAGE), self::DROPBOX));

            $this->secondary_next_step      = self::SECONDARY_STEP_TAR_FILE;
            $this->files_done               = 0;
            $this->position_file_list_file  = 0;
            $this->part_number              = 1;
            $this->old_percent              = 0;
            $tar_name                       = basename($this->part_list[$this->part_number - 1]);

            if (count($this->part_list) != count($this->tar_files_size)) {
                $this->log(
                    'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::DROPBOX).' '
                    .$this->l('The backup cannot be created', self::PAGE)
                );

                return false;
            }

            if (!isset($this->tar_files_size[$this->part_number]) || !$this->tar_files_size[$this->part_number]) {
                $this->log(
                    'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::DROPBOX).' '
                    .sprintf($this->l('The calculated size of the file %s is not valid', self::PAGE), $tar_name)
                );

                return false;
            }
        }

        $this->checkStopScript();

        $dropbox_lib = $this->connectToDropbox($access_token);

        if (!$dropbox_lib->testConnection()) {
            $this->log(
                'WAR'.sprintf($this->l('Connection to your %s account impossible', self::PAGE), self::DROPBOX)
            );
            return false;
        }

        $this->checkStopScript();

        if ($this->next_step == $this->step_send['dropbox']
            && $this->secondary_next_step  == self::SECONDARY_STEP_TAR_FILE
            && (!isset($this->dropbox_nb_part) || $this->dropbox_nb_part == 1)
        ) {
            $this->dropbox_dir = $dropbox->directory;

            //Dropbox dir should start with a "/" except for root
            if ($this->dropbox_dir != '' && $this->dropbox_dir[0] !== '/') {
                $this->dropbox_dir = '/'.$this->dropbox_dir;
            }

            $temp_directory = $this->dropbox_dir;

            //Dropbox dir should end with a "/" except when testing if exist
            if (Tools::substr($this->dropbox_dir, -1) != '/') {
                $temp_directory .= '/';
            }

            // Get old backups list
            $old_backups        = $this->getDropboxFiles($dropbox_lib, $temp_directory);
            $nb_backup_to_keep  = $dropbox->config_nb_backup;
            $nb_files           = count($old_backups);
            $size_old_backups   = 0;

            $this->checkStopScript();

            // Get old backup (to delete) size
            if ($nb_backup_to_keep > 0) {
                while ($nb_files >= $nb_backup_to_keep) {
                    $this->checkStopScript();

                    if (isset($old_backups[$nb_files])) {
                        $size_old_backups += $old_backups[$nb_files]['size_byte'];

                        $nb_files--;
                    }
                }
            }

            $this->checkStopScript();

            // Get available size
            $available_space = $dropbox_lib->getAvailableQuota() + $size_old_backups;

            $this->checkStopScript();

            // Get size to upload
            $to_send_size = $this->total_size;

            if ($this->config->send_restore) {
                $total_file_size = $this->getFileSize($this->restore_file);

                $this->checkStopScript();

                $to_send_size += $total_file_size;
            }

            // Check if we will have enough size after deleting old backup, too add new backup
            if ($available_space < $to_send_size) {
                $this->log(
                    'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::DROPBOX).' '
                    .$this->l('Error there will not be enough space available, even if we delete the old backup', self::PAGE)
                );
                return false;
            }

            $this->checkStopScript();

            // Delete old backup
            if (!$this->deleteDropboxOldBackup($access_token, $old_backups)) {
                $this->log(
                    'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::DROPBOX).' '
                    .$this->l('Error while deleting old backup', self::PAGE)
                );
                return false;
            }

            $this->checkStopScript();

            // Check available space
            $new_available_space = $dropbox_lib->getAvailableQuota();

            $this->checkStopScript();

            if ($new_available_space < $to_send_size) {
                $this->log(
                    'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::DROPBOX).' '
                    .$this->l('Not enough space available', self::PAGE)
                );
                return false;
            }

            $this->log(
                sprintf($this->l('Creating backup on %s account:', self::PAGE), self::DROPBOX).' '
                .$this->l('Check the path of your backup', self::PAGE)
            );

            $this->checkStopScript();

            // Check if the folder we want to use exists. If not we create it.
            if ($dropbox_lib->checkExists($this->dropbox_dir) === false) {
                $this->log(
                    sprintf($this->l('Creating backup on %s account:', self::PAGE), self::DROPBOX)
                    .' '.$this->l('Create the directory', self::PAGE).' "'.$this->dropbox_dir.'"'
                );

                $this->checkStopScript();

                if ($dropbox_lib->createFolder($this->dropbox_dir) === false) {
                    $this->log(
                        'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::DROPBOX)
                        .' '.$this->l('Error while creating the directory', self::PAGE).' "'.$this->dropbox_dir.'"'
                    );
                    return false;
                }
            }

            $this->checkStopScript();

            //Dropbox dir should end with a "/" except when testing if exist
            $this->dropbox_dir = $temp_directory;

            $this->log(sprintf($this->l('Creating backup on %s account...', self::PAGE), self::DROPBOX));

            $this->dropbox_nb_part          = 1;
            $this->dropbox_position         = 0;
            $this->distant_tar_content      = '';
            $this->distant_tar_content_size = 0;
        }

        $this->checkStopScript();

        if ($this->getFileSize($this->file_list_file) <= 0) {
            $this->log(
                'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::DROPBOX)
                .' '.$this->l('No file to backup', self::PAGE)
            );
            return false;
        }

        $this->checkStopScript();

        if (!is_resource($this->handle_file_list_file)) {
            $this->log(
                'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::DROPBOX)
                .' '.$this->l('Error while getting the files to backup', self::PAGE)
            );
            return false;
        }

        $this->handle_file_list_file = $this->goToPositionInFile(
            $this->handle_file_list_file,
            $this->position_file_list_file,
            false
        );

        $this->checkStopScript();

        while (!feof($this->handle_file_list_file)) {
            $this->checkStopScript();

            $line = rtrim(fgets($this->handle_file_list_file));

            if (!$line) {
                continue;
            }

            $pos_cut = strrpos($line, ':');

            if ($pos_cut === false) {
                continue;
            }

            $current_file = self::binaryToString(Tools::substr($line, 0, $pos_cut));

            if (!$current_file) {
                continue;
            }

            $this->checkStopScript();

            $found_size     = Tools::substr($line, ($pos_cut+1));
            $tar_name       = basename($this->part_list[$this->part_number - 1]);
            $path_in_tar    = '';

            //Normalize path
            $current_normalized_file = $this->normalizePath($current_file);

            //Find relative filename
            if (!$path_in_tar) {
                $filename = ltrim(self::getPart($current_normalized_file, $this->base_length), '/');
            } else {
                $filename = ltrim($path_in_tar, '/');
            }

            //File information
            $info       = $this->tarFileInfo($current_file);
            $diff_size  = 0;

            if ($info['size'] != $found_size) {
                if ($this->secondary_next_step == self::SECONDARY_STEP_TAR_FILE) {
                    $this->log(
                        'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::DROPBOX)
                        .' '.sprintf(
                            $this->l('The file %s has changed of size from %s to %s', self::PAGE),
                            $current_file,
                            $this->readableSize($found_size).' ('.$found_size.')',
                            $this->readableSize($info['size']).' ('.$info['size'].')'
                        )
                    );
                }

                $diff_size      = $info['size'] - $found_size;
                $info['size']   = $found_size;
            }

            if ($current_file == $this->dump_file && $this->config->type_backup == $this->type_backup_complete) {
                $path_module_backup = 'modules/'.$this->name.'/'.self::BACKUP_FOLDER.'/';
                $path_in_tar = str_replace($this->config_backup_dir, $path_module_backup, $current_file);
            }

            $this->checkStopScript();

            // Create file header
            if ($this->secondary_next_step == self::SECONDARY_STEP_TAR_FILE) {
                // Start to add a new file to the tar
                $header = $this->createTarHeader($filename, $info);

                $this->distant_tar_content_size += self::getLength($header);
                $this->distant_tar_content .= $header;

                $this->files_done++;

                $this->pos_file_to_tar      = 0;
                $this->secondary_next_step  = self::SECONDARY_STEP_TAR_FILE_CONTINUE;
            }

            if ($info['size'] > 0) {
                //Open the file
                if (($file_read = @fopen($current_file, 'rb')) === false) {
                    // If it is the first opening of the file
                    if ($this->pos_file_to_tar == 0) {
                        $this->log(
                            'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::DROPBOX)
                            .' '.$this->l('File', self::PAGE).' '.$current_file.' '
                            .$this->l('will be empty because the module can not open it, please check its rights and user owner', self::PAGE)
                        );
                    }
                }

                $this->checkStopScript();

                if (is_resource($file_read) && $this->secondary_next_step == self::SECONDARY_STEP_TAR_FILE_CONTINUE) {
                    $file_read = $this->goToPositionInFile($file_read, $this->pos_file_to_tar);

                    if ($file_read === false) {
                        return false;
                    }
                }

                //Data of the file
                $leftsize   = $info['size'] - $this->pos_file_to_tar;
                $blocksize  = self::TAR_BLOCK_SIZE;

                while ($leftsize > 0) {
                    $this->checkStopScript();

                    if ($this->next_step == $this->step_send['dropbox']) {
                        // Create new resumable session
                        $create_session = $dropbox_lib->createSession();

                        if ($create_session === false) {
                            $this->log(
                                'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::DROPBOX)
                                .' '.$this->l('File', self::PAGE).' '.$tar_name
                                .' '.$this->l('cannot be created', self::PAGE)
                            );

                            return false;
                        }

                        $this->dropbox_position = 0;
                        $this->next_step        = $this->step_send['dropbox_resume'];
                    }

                    // If max size for Dropbox has been reach, we send the content or the size of the tar was reach
                    if ($this->distant_tar_content_size >= DropboxLib::MAX_FILE_UPLOAD_SIZE
                        || ($this->distant_tar_content_size + self::TAR_END_SIZE) >= $this->tar_files_size[$this->part_number]
                    ) {
                        $end_of_current_part = 0;

                        if (($this->distant_tar_content_size + self::TAR_END_SIZE) >= $this->tar_files_size[$this->part_number]) {
                            $this->distant_tar_content_size +=  self::TAR_END_SIZE;
                            $this->distant_tar_content .= self::pad('', self::TAR_END_SIZE);
                            $end_of_current_part = 1;
                        }

                        if ($this->distant_tar_content_size > $this->tar_files_size[$this->part_number]) {
                            $this->log(
                                $this->l('The total size should not be less that the size we are now at', self::PAGE)
                                .' ('.$this->distant_tar_content_size.'/'.$this->tar_files_size[$this->part_number]
                            );
                        }

                        // Send content using the current session
                        $resume_upload_content = $dropbox_lib->resumeUploadContent(
                            $this->distant_tar_content,
                            $this->distant_tar_content_size,
                            $this->dropbox_dir.$tar_name,
                            $this->tar_files_size[$this->part_number],
                            $this->dropbox_upload_id,
                            $this->dropbox_position
                        );

                        if ($resume_upload_content === false) {
                            $this->log(
                                'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::DROPBOX)
                                .' '.$this->l('File', self::PAGE).' '.$tar_name
                                .' '.$this->l('cannot be completed', self::PAGE)
                            );

                            return false;
                        } else {
                            $this->dropbox_position += $this->distant_tar_content_size;

                            $this->distant_tar_content = '';
                            $this->distant_tar_content_size = 0;
                        }

                        if ($end_of_current_part) {
                            // New part, so back to init values
                            $this->dropbox_nb_part++;
                            $this->part_number++;

                            $this->next_step            = $this->step_send['dropbox'];
                            $this->dropbox_position     = 0;
                            $this->old_percent          = 0;
                            $this->secondary_next_step  = self::SECONDARY_STEP_TAR_FILE;
                        }
                    } else {
                        //Read data
                        $leftsize -= $blocksize;

                        if ($leftsize < 0) {
                            $blocksize += $leftsize;
                        }

                        $temp_content = '';

                        if (is_resource($file_read)) {
                            $temp_content = $this->createTarContent($file_read, $blocksize);
                        }

                        // Get where we are in the file
                        $this->pos_file_to_tar += $blocksize;

                        // If there is no content because the file size has changed (smaller file) we need to add \0
                        if ($diff_size < 0 && !$temp_content) {
                            //self::TAR_BLOCK_SIZE since createTarContent
                            //return self::TAR_BLOCK_SIZE size block (using self::pad)
                            $temp_content = self::pad('', self::TAR_BLOCK_SIZE);
                            $diff_size += self::TAR_BLOCK_SIZE;
                        } elseif ($temp_content === false) {
                            $this->log(
                                'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::DROPBOX)
                                .' '.$this->l('The module was unable to read the file', self::PAGE)
                                .' '.$current_file.', '.$this->l('please check its rights and user owner', self::PAGE)
                            );

                            if (is_resource($file_read)) {
                                fclose($file_read);
                            }

                            return false;
                        }

                        $this->distant_tar_content .= $temp_content;
                        $this->distant_tar_content_size += self::TAR_BLOCK_SIZE;

                        $percent = (($this->distant_tar_content_size + $this->dropbox_position)/$this->tar_files_size[$this->part_number]) * 100;

                        if ($percent >= $this->old_percent + 1) {
                            $this->old_percent = round($percent, 0);

                            if ($this->total_nb_part > 1) {
                                $this->log(
                                    sprintf($this->l('Creating backup on %s account:', self::PAGE), self::DROPBOX)
                                    .' '.$this->part_number.'/'.$this->total_nb_part.$this->l(':', self::PAGE)
                                    .' '.(int)$percent.'%'
                                );
                            } else {
                                $this->log(
                                    sprintf($this->l('Creating backup on %s account:', self::PAGE), self::DROPBOX)
                                    .' '.(int)$percent.'%'
                                );
                            }
                        }
                    }

                    //refresh
                    $this->refreshBackup(true);
                }

                //Close file
                if (is_resource($file_read)) {
                    fclose($file_read);
                }
            }

            $this->position_file_list_file = ftell($this->handle_file_list_file);

            $this->checkStopScript();

            //Check if tar file size has reach its predicted size
            if (isset($this->tar_files_size[$this->part_number])
                && ($this->dropbox_position + $this->distant_tar_content_size + self::TAR_END_SIZE) >= $this->tar_files_size[$this->part_number]
                && $this->dropbox_position < $this->tar_files_size[$this->part_number]
            ) {
                //The tar file will be too big, we need to close it and use a new one
                $this->distant_tar_content .= self::pad('', self::TAR_END_SIZE);
                $this->distant_tar_content_size +=  self::TAR_END_SIZE;

                if ($this->distant_tar_content_size > $this->tar_files_size[$this->part_number]) {
                    $this->log(
                        $this->l('The total size should not be less that the size we are now at', self::PAGE)
                        .' ('.$this->distant_tar_content_size.'/'.$this->tar_files_size[$this->part_number]
                    );
                }

                // Finish sending current tar
                $resume_upload_content = $dropbox_lib->resumeUploadContent(
                    $this->distant_tar_content,
                    $this->distant_tar_content_size,
                    $this->dropbox_dir.$tar_name,
                    $this->tar_files_size[$this->part_number],
                    $this->dropbox_upload_id,
                    $this->dropbox_position
                );

                if ($resume_upload_content === false) {
                    $this->log(
                        'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::DROPBOX)
                        .' '.$this->l('File', self::PAGE).' '.$tar_name
                        .' '.$this->l('cannot be finished', self::PAGE)
                    );

                    return false;
                }

                // New part, so back to init values
                $this->dropbox_nb_part++;
                $this->part_number++;

                $this->next_step                = $this->step_send['dropbox'];
                $this->dropbox_position         = 0;
                $this->old_percent              = 0;
                $this->secondary_next_step      = self::SECONDARY_STEP_TAR_FILE;
                $this->distant_tar_content      = '';
                $this->distant_tar_content_size = 0;

                //refresh
                $this->refreshBackup(true);
            }
            $this->secondary_next_step = self::SECONDARY_STEP_TAR_FILE;
        }

        // Check that there is no missing file
        if ($this->files_done < $this->total_files) {
            $this->log(
                'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::DROPBOX)
                .$this->l('Be careful! Not all of your files have been added to the backup. Number of files backuped:', self::PAGE)
                .' '.$this->files_done.'/'.$this->total_files
            );

            return false;
        }

        $this->files_done = 0;
        $this->pos_file_to_tar = 0;

        $this->next_step = $this->step_send['dropbox_resume'];

        if ($this->config->send_restore) {
            $this->log(
                sprintf($this->l('Creating restore file on %s account...', self::PAGE), self::DROPBOX)
            );

            // Upload the file
            if ($dropbox_lib->uploadFile($this->restore_file, $this->dropbox_dir.self::NEW_RESTORE_NAME) === false) {
                return false;
            }
        }

        return true;
    }

    /**
     * Connect to a FTP server
     *
     * @param   String      $ftp_server     SFTP server
     * @param   String      $ftp_login      SFTP login
     * @param   String      $ftp_pass       SFTP password
     * @param   integer     $ftp_port       SFTP port
     * @param   boolean     $ftp_ssl        SFTP SSL
     * @param   boolean     $ftp_pasv       SFTP passive mode
     * @param   String      $ftp_dir        SFTP directory
     *
     * @return  ressource   The FTP connexion
     */
    public function connectFtp($ftp_server, $ftp_login, $ftp_pass, $ftp_port, $ftp_ssl, $ftp_pasv, $ftp_dir = '')
    {
        if ($ftp_ssl) {
            $connection = ftp_ssl_connect($ftp_server, (int)$ftp_port, self::FTP_TIMEOUT);
        } else {
            // Beware of the warning from php if failure
            $connection = @ftp_connect($ftp_server, $ftp_port, self::FTP_TIMEOUT);
        }

        if (!$connection) {
            $this->log(
                'WAR'
                .sprintf($this->l('Unable to connect to the %s server, please verify your data', self::PAGE), self::FTP)
            );
            return false;
        }

        // Beware of the warning from php if failure
        $login = @ftp_login($connection, $ftp_login, $ftp_pass);

        if (!$login) {
            ftp_close($connection);
            $this->log(
                'WAR'
                .sprintf(
                    $this->l('Unable to log in the %s server, please verify your credentials', self::PAGE),
                    self::FTP
                )
            );
            return false;
        }

        ftp_pasv($connection, $ftp_pasv);

        if ($ftp_dir != '') {
            ftp_chdir($connection, $ftp_dir);
        }

        return $connection;
    }

    /**
     * Send a file on a FTP account
     *
     * @return  boolean     The success or failure of the operation
     */
    protected function sendFileToFTP()
    {
        $ftp        = new Ftp($this->ftp_account_id);
        $ftp_server = $ftp->server;
        $ftp_login  = $ftp->login;
        $ftp_pass   = $this->decrypt($ftp->password);
        $ftp_port   = $ftp->port;
        $ftp_ssl    = $ftp->ssl;
        $ftp_pasv   = $ftp->passive_mode;

        if ($this->next_step == $this->step_send['ftp'] && (!isset($this->ftp_nb_part) || $this->ftp_nb_part == 1)) {
            $this->ftp_dir      = $ftp->directory;
            $this->ftp_nb_part  = 1;

            //FTP dir should start and end with a /
            $this->ftp_dir = rtrim($this->normalizePath($this->ftp_dir), '/').'/';
            if ($this->ftp_dir[0] !== '/') {
                $this->ftp_dir = '/'.$this->ftp_dir;
            }

            $this->log(sprintf($this->l('Connect to your %s account...', self::PAGE), self::FTP));
        }

        $connection = $this->connectFtp($ftp_server, $ftp_login, $ftp_pass, (int)$ftp_port, $ftp_ssl, $ftp_pasv);

        if (!$connection) {
            $this->log(
                'WAR'.sprintf($this->l('Connection to your %s account impossible', self::PAGE), self::FTP)
            );
            return false;
        }

        if ($this->next_step == $this->step_send['ftp']) {
            $ftp_current_directory = ftp_pwd($connection);

            if (!isset($this->ftp_nb_part) || $this->ftp_nb_part == 1) {
                if ($ftp_current_directory != '/') {
                    $this->ftp_dir = $ftp_current_directory.$this->ftp_dir;
                }
            }
        }

        ftp_chdir($connection, $this->ftp_dir);

        if ($this->next_step == $this->step_send['ftp'] && (!isset($this->ftp_nb_part) || $this->ftp_nb_part == 1)) {
            // If file already on FTP
            foreach ($this->part_list as $part) {
                $file_destination   = $this->ftp_dir.basename($part);

                $this->log(
                    $this->l('Check if there is a previous version of the file:', self::PAGE).' '.$file_destination
                );
                // -1 == error. If file is bigger than 2GB we can have negative number, so be carefull
                if (ftp_size($connection, basename($part)) != -1) {
                    $this->log($this->l('Delete previous version of the file:', self::PAGE).' '.$file_destination);
                    if (ftp_delete($connection, basename($part)) === false) {
                        $this->log($this->l('Error while deleting the file:', self::PAGE).' '.$file_destination);
                    }
                }
            }

            if (!$this->deleteFTPOldBackup($connection)) {
                ftp_close($connection);
                return false;
            }

            $this->ftp_position = 0;
        }

        $nb_part = 1;
        foreach ($this->part_list as $part) {
            if ($this->ftp_nb_part == $nb_part) {
                //Restart connection for each file to avoid server disconnection problems
                ftp_close($connection);
                $connection = $this->connectFtp(
                    $ftp_server,
                    $ftp_login,
                    $ftp_pass,
                    (int)$ftp_port,
                    $ftp_ssl,
                    $ftp_pasv,
                    $this->ftp_dir
                );

                if (!$connection) {
                    $this->log('WAR'.sprintf($this->l('An error occured while uploading your backup to the %s server, connection to ftp server was shutdown', self::PAGE), self::FTP));
                    return false;
                }

                $total_file_size = $this->getFileSize($part);
                $ftp_file_path = basename($part);
                $last_percent = 0;

                $file = fopen($part, "r+");

                if ($file === false) {
                    $this->log('WAR'.sprintf($this->l('Unable to access backup file in order to send it by %s, please check file rights', self::PAGE), self::FTP));
                    return false;
                }

                if ($this->next_step == $this->step_send['ftp_resume']) {
                    // Go to the last position in the file
                    $file = $this->goToPositionInFile($file, $this->ftp_position, false);

                    if ($file === false) {
                        return false;
                    }
                } else {
                    $this->next_step = $this->step_send['ftp_resume'];
                }

                $byte_offset = $this->ftp_position;

                //Send the file
                $ftp_upload = ftp_nb_fput($connection, $ftp_file_path, $file, FTP_BINARY, $this->ftp_position);

                while ($ftp_upload == FTP_MOREDATA) {
                    $this->checkStopScript();
                    $byte_offset = ftell($file);

                    $percent = (int)(($byte_offset/$total_file_size) * 100);
                    if ($percent >= ($last_percent + 1)) {
                        if ($this->total_nb_part > 1) {
                            $this->log(
                                sprintf($this->l('Sending backup to %s account:', self::PAGE), self::FTP)
                                .' '.$this->ftp_nb_part.'/'.$this->total_nb_part
                                .$this->l(':', self::PAGE).' '.(int)$percent.'%'
                            );
                        } else {
                            $this->log(
                                sprintf($this->l('Sending backup to %s account:', self::PAGE), self::FTP)
                                .' '.$percent.'%'
                            );
                        }

                        $last_percent = $percent;
                    }
                    $ftp_upload = ftp_nb_continue($connection);

                    if ($this->validRefresh(true)) {
                        /*$this->log(
                            sprintf($this->l(
                                'Close connection to %s', self::PAGE), self::FTP)
                        );*/

                        ftp_close($connection);

                        if ($ftp_ssl) {
                            $connection = ftp_ssl_connect($ftp_server, (int) $ftp_port, self::FTP_TIMEOUT);
                        } else {
                            // Beware of the warning from php if failure
                            $connection = @ftp_connect($ftp_server, $ftp_port, self::FTP_TIMEOUT);
                        }

                        if (!$connection) {
                            $this->log(
                                'WAR'
                                .sprintf(
                                    $this->l('Unable to connect to the %s server, please verify your data', self::PAGE),
                                    self::FTP
                                )
                            );
                            return false;
                        }

                        // Beware of the warning from php if failure
                        $login = @ftp_login($connection, $ftp_login, $ftp_pass);
                        if (!$login) {
                            ftp_close($connection);
                            $this->log('WAR'.sprintf($this->l('Unable to log in the %s server, please verify your credentials', self::PAGE), self::FTP));
                            return false;
                        }

                        ftp_raw($connection, 'TYPE I');
                        $response =  ftp_raw($connection, 'SIZE '.$this->ftp_dir.$ftp_file_path);
                        $response_code = Tools::substr($response[0], 0, 3);
                        $response_msg = Tools::substr($response[0], 4);

                        if ($response_code == '213') {
                            $this->ftp_position = $response_msg;
                        } else {
                            $this->log('WAR'.$response_msg);
                            return false;
                        }

                        //refresh
                        $this->refreshBackup(true);
                    }
                }

                if ($ftp_upload != FTP_FINISHED) {
                    $byte_offset = ftell($file);

                    if ($byte_offset != $total_file_size) {
                        ftp_close($connection);
                        $this->log(
                            'WAR'
                            .sprintf(
                                $this->l('An error occured while uploading your backup to the %s server, please check your %s server log and retry', self::PAGE),
                                self::FTP,
                                self::FTP
                            )
                        );

                        if (!$ftp_pasv) {
                            $this->log('WAR'.$this->l('Check if passive mode is needed', self::PAGE));
                        }

                        return false;
                    }
                }

                $this->ftp_nb_part++;
                // New part, so back to init values
                $this->next_step = $this->step_send['ftp'];
                $this->ftp_position = 0;
            }

            $nb_part++;
        }

        $this->next_step = $this->step_send['ftp_resume'];

        // Send restore file if needed
        if ($this->config->send_restore) {
            // Test if the connexion is still open
            if (ftp_pwd($connection) === false) {
                $this->log(
                    sprintf($this->l('Try to connect to the %s server. The connexion was lost.', self::PAGE), self::FTP)
                );

                if ($ftp_ssl) {
                    $connection = ftp_ssl_connect($ftp_server, (int) $ftp_port, self::FTP_TIMEOUT);
                } else {
                    // Beware of the warning from php if failure
                    $connection = @ftp_connect($ftp_server, $ftp_port, self::FTP_TIMEOUT);
                }

                if (!$connection) {
                    $this->log(
                        'WAR'
                        .sprintf(
                            $this->l('Unable to connect to the %s server, please verify your data', self::PAGE),
                            self::FTP
                        )
                    );
                    return false;
                }

                // Beware of the warning from php if failure
                $login = @ftp_login($connection, $ftp_login, $ftp_pass);
                if (!$login) {
                    ftp_close($connection);
                    $this->log(
                        'WAR'
                        .sprintf(
                            $this->l('Unable to log in the %s server, please verify your credentials', self::PAGE),
                            self::FTP
                        )
                    );
                    return false;
                }

                ftp_pasv($connection, $ftp_pasv);

                ftp_chdir($connection, $this->ftp_dir);
            }

            $file_destination   = $this->ftp_dir.basename($this->restore_file);
            $this->log($this->l('Check if there is a previous version of the file:', self::PAGE).' '.$file_destination);
            // -1 == error. If file is bigger than 2GB we can have negative number, so be carefull
            if (ftp_size($connection, basename($this->restore_file)) != -1) {
                $this->log($this->l('Delete previous version of the file:', self::PAGE).' '.$file_destination);
                if (ftp_delete($connection, basename($this->restore_file)) === false) {
                    $this->log($this->l('Error while deleting the file:', self::PAGE).' '.$file_destination);
                }
            }

            $this->log(sprintf($this->l('Sending restore file to %s account...', self::PAGE), self::FTP));

            $total_file_size    = $this->getFileSize($this->restore_file);
            $ftp_file_path      = self::NEW_RESTORE_NAME;
            $last_percent       = 0;

            $file = fopen($this->restore_file, "r+");

            if ($file === false) {
                $this->log('WAR'.sprintf($this->l('Unable to access restoration script in order to send it by %s, please check file rights', self::PAGE), self::FTP));
            } else {
                //Send the file
                $upload = ftp_nb_fput($connection, $ftp_file_path, $file, FTP_BINARY);

                while ($upload == FTP_MOREDATA) {
                    $this->checkStopScript();
                    $byte_offset = ftell($file);
                    $percent = (int)(($byte_offset/$total_file_size) * 100);
                    if ($percent >= ($last_percent + 1)) {
                        $this->log(
                            sprintf($this->l('Sending restore file to %s account:', self::PAGE), self::FTP)
                            .' '.$percent.'%'
                        );

                        $last_percent = $percent;
                    }
                    $upload = ftp_nb_continue($connection);
                }

                if ($upload != FTP_FINISHED) {
                    ftp_close($connection);
                    $this->log(
                        'WAR'
                        .sprintf(
                            $this->l('An error occured while uploading your restore file to the %s server, please check your %s server log and retry', self::PAGE),
                            self::FTP,
                            self::FTP
                        )
                    );
                    return false;
                }
            }
        }

        ftp_close($connection);
        return true;
    }

    /**
     * Send a file on a SFTP account
     *
     * @return  boolean     The success or failure of the operation
     */
    protected function sendFileToSFTP()
    {
        $ftp        = new Ftp($this->ftp_account_id);
        $ftp_server = $ftp->server;
        $ftp_login  = $ftp->login;
        $ftp_pass   = $this->decrypt($ftp->password);
        $ftp_port   = $ftp->port;

        if ($this->next_step == $this->step_send['ftp'] && (!isset($this->ftp_nb_part) || $this->ftp_nb_part == 1)) {
            $this->ftp_dir    = $ftp->directory;

            //SFTP dir should start and end with a /
            $this->ftp_dir = rtrim($this->normalizePath($this->ftp_dir), '/').'/';
            if ($this->ftp_dir[0] !== '/') {
                $this->ftp_dir = '/'.$this->ftp_dir;
            }
        }

        $this->initForSFTP();

        $sftp_lib = new \phpseclib\Net\SFTP($ftp_server, $ftp_port);

        // Beware of the warning from php if failure
        if (!@$sftp_lib->login($ftp_login, $ftp_pass)) {
            $this->log(
                'WAR'
                .sprintf(
                    $this->l('Unable to connect to the %s server, please verify your credentials', self::PAGE),
                    self::SFTP
                )
            );
            return false;
        }

        if ($this->next_step == $this->step_send['ftp'] && (!isset($this->ftp_nb_part) || $this->ftp_nb_part == 1)) {
            if ($this->ftp_dir[0] !== '/') {
                $this->ftp_dir = '/'.$this->ftp_dir;
            }

            $this->ftp_dir = $sftp_lib->pwd().$this->ftp_dir;

            // If file already on SFTP
            foreach ($this->part_list as $part) {
                $file_destination = $this->ftp_dir.basename($part);

                $this->log(
                    $this->l('Check if there is a previous version of the file:', self::PAGE).' '.$file_destination
                );
                if ($sftp_lib->file_exists($file_destination)) {
                    $this->log($this->l('Delete previous version of the file:', self::PAGE).' '.$file_destination);
                    if ($sftp_lib->delete($file_destination) === false) {
                        $this->log($this->l('Error while deleting the file:', self::PAGE).' '.$file_destination);
                    }
                }
            }

            if (!$this->deleteSFTPOldBackup($sftp_lib, $this->ftp_dir)) {
                $this->closeSFTP($sftp_lib);
                return false;
            }

            $this->ftp_nb_part = 1;
            $this->ftp_position = 0;
        }

        $sftp_lib->chdir($this->ftp_dir);

        $nb_part = 1;
        foreach ($this->part_list as $part) {
            if ($this->ftp_nb_part == $nb_part) {
                $total_file_size = $this->getFileSize($part);
                $byte_offset = $this->ftp_position;
                $last_percent = 0;
                $file = fopen($part, "r+");

                if ($this->next_step == $this->step_send['ftp_resume'] && $this->ftp_position > 0) {
                    // Go to the last position in the file
                    $file = $this->goToPositionInFile($file, $this->ftp_position, false);

                    if ($file === false) {
                        return false;
                    }
                }

                $this->next_step = $this->step_send['ftp_resume'];

                while (!feof($file)) {
                    $this->checkStopScript();
                    $part_file = fread($file, self::MAX_FILE_UPLOAD_SIZE);
                    $byte_offset += self::MAX_FILE_UPLOAD_SIZE;
                    $this->ftp_position = $byte_offset;

                    if ($total_file_size == 0) {
                        $this->log(
                            'WAR'.$this->l('Your file seems to have an issue. Please check it.', self::PAGE).' '.$part
                        );
                        return false;
                    }

                    $percent = (int)(($byte_offset/$total_file_size) * 100);

                    // if self::MAX_FILE_UPLOAD_SIZE > than what is left to upload
                    if ($percent > 100) {
                        $percent = 100;
                    }

                    if ($percent >= ($last_percent + 1)) {
                        if ($this->total_nb_part > 1) {
                            $this->log(
                                sprintf($this->l('Sending backup to %s account:', self::PAGE), self::SFTP).' '
                                .$nb_part.'/'.$this->total_nb_part.$this->l(':', self::PAGE).' '.(int)$percent.'%'
                            );
                        } else {
                            $this->log(
                                sprintf($this->l('Sending backup to %s account:', self::PAGE), self::SFTP)
                                .' '.$percent.'%'
                            );
                        }

                        $last_percent = $percent;
                    }

                    if (!$sftp_lib->put(basename($part), $part_file, \phpseclib\Net\SFTP::RESUME)) {
                        $this->log(
                            'WAR'
                            .sprintf(
                                $this->l('An error occured while uploading your backup to the %s server, please check your %s server log and retry', self::PAGE),
                                self::SFTP,
                                self::SFTP
                            )
                        );
                        return false;
                    }

                    //refresh
                    $this->refreshBackup(true);
                }

                $this->ftp_nb_part++;
                // New part, so back to init values
                $this->next_step = $this->step_send['ftp'];
                $this->ftp_position = 0;
            }

            $nb_part++;
        }

        $this->next_step = $this->step_send['ftp_resume'];

        if ($this->config->send_restore) {
            $this->log(
                sprintf($this->l('Sending restore file to %s account...', self::PAGE), self::SFTP)
            );

            $total_file_size = $this->getFileSize($this->restore_file);
            $byte_offset = 0;
            $last_percent = 0;
            $file = fopen($this->restore_file, "r+");

            while (!feof($file)) {
                $this->checkStopScript();
                $part_file = fread($file, self::MAX_FILE_UPLOAD_SIZE);
                $byte_offset += self::MAX_FILE_UPLOAD_SIZE;

                if ($total_file_size == 0) {
                    $this->log(
                        'WAR'
                        .$this->l('Your file seems to have an issue. Please check it.', self::PAGE)
                        .' '.$this->restore_file
                    );
                    return false;
                }

                $percent = (int)(($byte_offset/$total_file_size) * 100);

                // if self::MAX_FILE_UPLOAD_SIZE > than what is left to upload
                if ($percent > 100) {
                    $percent = 100;
                }

                if ($percent >= ($last_percent + 1)) {
                    $this->log(
                        sprintf($this->l('Sending restore file to %s account:', self::PAGE), self::SFTP)
                        .' '.$percent.'%'
                    );

                    $last_percent = $percent;
                }

                if (!$sftp_lib->put(self::NEW_RESTORE_NAME, $part_file, \phpseclib\Net\SFTP::RESUME)) {
                    $this->log(
                        'WAR'
                        .sprintf(
                            $this->l('An error occured while uploading your restore file to the %s server, please check your %s server log and retry', self::PAGE),
                            self::SFTP,
                            self::SFTP
                        )
                    );
                    return false;
                }
            }
        }

        $this->closeSFTP($sftp_lib);

        return true;
    }

    /**
     * Create a tar file on a SFTP account
     *
     * @return  boolean     The success or failure of the operation
     */
    protected function createTarOnSFTP()
    {
        $ftp        = new Ftp($this->ftp_account_id);
        $ftp_server = $ftp->server;
        $ftp_login  = $ftp->login;
        $ftp_pass   = $this->decrypt($ftp->password);
        $ftp_port   = $ftp->port;

        $this->checkStopScript();

        if ($this->next_step == $this->step_send['ftp']
            && $this->secondary_next_step  == self::SECONDARY_STEP_TAR_FILE
            && (!isset($this->ftp_nb_part) || $this->ftp_nb_part == 1)
        ) {
            $this->ftp_dir    = $ftp->directory;

            //SFTP dir should start and end with a /
            $this->ftp_dir = rtrim($this->normalizePath($this->ftp_dir), '/').'/';
            if ($this->ftp_dir[0] !== '/') {
                $this->ftp_dir = '/'.$this->ftp_dir;
            }

            $this->secondary_next_step      = self::SECONDARY_STEP_TAR_FILE;
            $this->files_done               = 0;
            $this->position_file_list_file  = 0;
            $this->part_number              = 1;
            $this->old_percent              = 0;
            $tar_name                       = basename($this->part_list[$this->part_number - 1]);

            if (count($this->part_list) != count($this->tar_files_size)) {
                $this->log(
                    'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::SFTP)
                    .' '.$this->l('The backup cannot be created', self::PAGE)
                );

                return false;
            }

            if (!isset($this->tar_files_size[$this->part_number]) || !$this->tar_files_size[$this->part_number]) {
                $this->log(
                    'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::SFTP).' '
                    .sprintf($this->l('The calculated size of the file %s is not valid', self::PAGE), $tar_name)
                );

                return false;
            }
        }

        $this->checkStopScript();

        $this->initForSFTP();

        $sftp_lib = new \phpseclib\Net\SFTP($ftp_server, $ftp_port);

        // Beware of the warning from php if failure
        if (!@$sftp_lib->login($ftp_login, $ftp_pass)) {
            $this->log(
                'WAR'
                .sprintf(
                    $this->l('Unable to connect to the %s server, please verify your credentials', self::PAGE),
                    self::SFTP
                )
            );
            return false;
        }

        $this->checkStopScript();

        if ($this->next_step == $this->step_send['ftp']
            && $this->secondary_next_step  == self::SECONDARY_STEP_TAR_FILE
            && (!isset($this->ftp_nb_part) || $this->ftp_nb_part == 1)
        ) {
            if ($this->ftp_dir[0] !== '/') {
                $this->ftp_dir = '/'.$this->ftp_dir;
            }

            $this->ftp_dir = $sftp_lib->pwd().$this->ftp_dir;

            if (!$this->deleteSFTPOldBackup($sftp_lib, $this->ftp_dir)) {
                $this->closeSFTP($sftp_lib);
                return false;
            }

            $this->ftp_nb_part              = 1;
            $this->ftp_position             = 0;
            $this->distant_tar_content      = '';
            $this->distant_tar_content_size = 0;
        }

        $this->checkStopScript();

        $sftp_lib->chdir($this->ftp_dir);

        if ($this->getFileSize($this->file_list_file) <= 0) {
            $this->log(
                'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::SFTP)
                .' '.$this->l('No file to backup', self::PAGE)
            );
            return false;
        }

        if (!is_resource($this->handle_file_list_file)) {
            $this->log(
                'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::SFTP)
                .' '.$this->l('Error while getting the files to backup', self::PAGE)
            );
            return false;
        }

        $this->handle_file_list_file = $this->goToPositionInFile(
            $this->handle_file_list_file,
            $this->position_file_list_file,
            false
        );

        $this->checkStopScript();

        while (!feof($this->handle_file_list_file)) {
            $this->checkStopScript();

            $line = rtrim(fgets($this->handle_file_list_file));

            if (!$line) {
                continue;
            }

            $pos_cut = strrpos($line, ':');

            if ($pos_cut === false) {
                continue;
            }

            $current_file = self::binaryToString(Tools::substr($line, 0, $pos_cut));

            if (!$current_file) {
                continue;
            }

            $this->checkStopScript();

            $found_size     = Tools::substr($line, ($pos_cut+1));
            $tar_name       = basename($this->part_list[$this->part_number - 1]);
            $path_in_tar    = '';

            //Normalize path
            $current_normalized_file = $this->normalizePath($current_file);

            //Find relative filename
            if (!$path_in_tar) {
                $filename = ltrim(self::getPart($current_normalized_file, $this->base_length), '/');
            } else {
                $filename = ltrim($path_in_tar, '/');
            }

            //File information
            $info       = $this->tarFileInfo($current_file);
            $diff_size  = 0;

            if ($info['size'] != $found_size) {
                if ($this->secondary_next_step == self::SECONDARY_STEP_TAR_FILE) {
                    $this->log(
                        'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::SFTP)
                        .' '.sprintf(
                            $this->l('The file %s has changed of size from %s to %s', self::PAGE),
                            $current_file,
                            $this->readableSize($found_size).' ('.$found_size.')',
                            $this->readableSize($info['size']).' ('.$info['size'].')'
                        )
                    );
                }

                $diff_size      = $info['size'] - $found_size;
                $info['size']   = $found_size;
            }

            if ($current_file == $this->dump_file && $this->config->type_backup == $this->type_backup_complete) {
                $path_module_backup = 'modules/'.$this->name.'/'.self::BACKUP_FOLDER.'/';
                $path_in_tar        = str_replace($this->config_backup_dir, $path_module_backup, $current_file);
            }

            $this->checkStopScript();

            // Create file header
            if ($this->secondary_next_step == self::SECONDARY_STEP_TAR_FILE) {
                // Start to add a new file to the tar
                $header = $this->createTarHeader($filename, $info);

                $this->distant_tar_content_size += self::getLength($header);
                $this->distant_tar_content .= $header;

                $this->files_done++;

                $this->pos_file_to_tar      = 0;
                $this->secondary_next_step  = self::SECONDARY_STEP_TAR_FILE_CONTINUE;
            }

            if ($info['size'] > 0) {
                //Open the file
                if (($file_read = @fopen($current_file, 'rb')) === false) {
                    // If it is the first opening of the file
                    if ($this->pos_file_to_tar == 0) {
                        $this->log(
                            'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::SFTP).' '
                            .$this->l('File', self::PAGE).' '.$current_file.' '
                            .$this->l('will be empty because the module can not open it, please check its rights and user owner', self::PAGE)
                        );
                    }
                }

                $this->checkStopScript();

                if (is_resource($file_read) && $this->secondary_next_step == self::SECONDARY_STEP_TAR_FILE_CONTINUE) {
                    $file_read = $this->goToPositionInFile($file_read, $this->pos_file_to_tar);

                    if ($file_read === false) {
                        return false;
                    }
                }

                //Data of the file
                $leftsize   = $info['size'] - $this->pos_file_to_tar;
                $blocksize  = self::TAR_BLOCK_SIZE;

                while ($leftsize > 0) {
                    $this->checkStopScript();

                    if ($this->next_step == $this->step_send['ftp']) {
                        $this->ftp_position = 0;
                        $this->next_step    = $this->step_send['ftp_resume'];
                    }

                    // If max size for SFTP has been reach, we send the content or the size of the tar was reach
                    if ($this->distant_tar_content_size >= self::MAX_FILE_UPLOAD_SIZE
                        || ($this->distant_tar_content_size + self::TAR_END_SIZE) >= $this->tar_files_size[$this->part_number]
                    ) {
                        $end_of_current_part = 0;

                        if (($this->distant_tar_content_size + self::TAR_END_SIZE) >= $this->tar_files_size[$this->part_number]) {
                            $this->distant_tar_content_size +=  self::TAR_END_SIZE;
                            $this->distant_tar_content .= self::pad('', self::TAR_END_SIZE);
                            $end_of_current_part = 1;
                        }

                        if ($this->distant_tar_content_size > $this->tar_files_size[$this->part_number]) {
                            $this->log(
                                $this->l('The total size should not be less that the size we are now at', self::PAGE)
                                .' ('.$this->distant_tar_content_size.'/'.$this->tar_files_size[$this->part_number]
                            );
                        }

                        // Send content
                        $upload_content = $sftp_lib->put(
                            $tar_name,
                            $this->distant_tar_content,
                            \phpseclib\Net\SFTP::RESUME
                        );

                        if ($upload_content === false) {
                            $this->log(
                                'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::SFTP)
                                .' '.$this->l('File', self::PAGE).' '.$tar_name
                                .' '.$this->l('cannot be completed', self::PAGE)
                            );

                            return false;
                        } else {
                            $this->ftp_position += $this->distant_tar_content_size;

                            $this->distant_tar_content      = '';
                            $this->distant_tar_content_size = 0;
                        }

                        if ($end_of_current_part) {
                            // New part, so back to init values
                            $this->ftp_nb_part++;
                            $this->part_number++;

                            $this->next_step            = $this->step_send['ftp'];
                            $this->ftp_position         = 0;
                            $this->old_percent          = 0;
                            $this->secondary_next_step  = self::SECONDARY_STEP_TAR_FILE;
                        }
                    } else {
                        //Read data
                        $leftsize -= $blocksize;

                        if ($leftsize < 0) {
                            $blocksize += $leftsize;
                        }

                        $temp_content = '';

                        if (is_resource($file_read)) {
                            $temp_content = $this->createTarContent($file_read, $blocksize);
                        }

                        // Get where we are in the file
                        $this->pos_file_to_tar += $blocksize;

                        if ($diff_size < 0 && !$temp_content) {
                            //self::TAR_BLOCK_SIZE since createTarContent
                            //return self::TAR_BLOCK_SIZE size block (using self::pad)
                            $temp_content = self::pad('', self::TAR_BLOCK_SIZE);
                            $diff_size += self::TAR_BLOCK_SIZE;
                        } elseif ($temp_content === false) {
                            $this->log(
                                'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::SFTP)
                                .' '.$this->l('The module was unable to read the file', self::PAGE)
                                .' '.$current_file.', '.$this->l('please check its rights and user owner', self::PAGE)
                            );

                            if (is_resource($file_read)) {
                                fclose($file_read);
                            }

                            return false;
                        }

                        $this->distant_tar_content .= $temp_content;
                        $this->distant_tar_content_size += self::TAR_BLOCK_SIZE;

                        $percent = (($this->distant_tar_content_size + $this->ftp_position)/$this->tar_files_size[$this->part_number]) * 100;

                        if ($percent >= $this->old_percent + 1) {
                            $this->old_percent = round($percent, 0);

                            if ($this->total_nb_part > 1) {
                                $this->log(
                                    sprintf($this->l('Creating backup on %s account:', self::PAGE), self::SFTP)
                                    .' '.$this->part_number.'/'.$this->total_nb_part.$this->l(':', self::PAGE)
                                    .' '.(int)$percent.'%'
                                );
                            } else {
                                $this->log(
                                    sprintf($this->l('Creating backup on %s account:', self::PAGE), self::SFTP)
                                    .' '.(int)$percent.'%'
                                );
                            }
                        }
                    }

                    //refresh
                    $this->refreshBackup(true);
                }

                //Close file
                if (is_resource($file_read)) {
                    fclose($file_read);
                }
            }

            $this->position_file_list_file = ftell($this->handle_file_list_file);

            $this->checkStopScript();

            //Check if tar file size has reach its predicted size
            if (isset($this->tar_files_size[$this->part_number])
                && ($this->ftp_position + $this->distant_tar_content_size + self::TAR_END_SIZE) >= $this->tar_files_size[$this->part_number]
                && $this->ftp_position < $this->tar_files_size[$this->part_number]
            ) {
                //The tar file will be too big, we need to close it and use a new one
                $this->distant_tar_content .= self::pad('', self::TAR_END_SIZE);
                $this->distant_tar_content_size +=  self::TAR_END_SIZE;

                if ($this->distant_tar_content_size > $this->tar_files_size[$this->part_number]) {
                    $this->log(
                        $this->l('The total size should not be less that the size we are now at', self::PAGE)
                        .' ('.$this->distant_tar_content_size.'/'.$this->tar_files_size[$this->part_number]
                    );
                }

                // Finish sending current tar
                $upload_content = $sftp_lib->put(
                    $tar_name,
                    $this->distant_tar_content,
                    \phpseclib\Net\SFTP::RESUME
                );

                if ($upload_content === false) {
                    $this->log(
                        'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::SFTP)
                        .' '.$this->l('File', self::PAGE).' '.$tar_name
                        .' '.$this->l('cannot be finished', self::PAGE)
                    );

                    return false;
                }

                // New part, so back to init values
                $this->ftp_nb_part++;
                $this->part_number++;

                $this->next_step                = $this->step_send['ftp'];
                $this->ftp_position             = 0;
                $this->old_percent              = 0;
                $this->secondary_next_step      = self::SECONDARY_STEP_TAR_FILE;
                $this->distant_tar_content      = '';
                $this->distant_tar_content_size = 0;

                //refresh
                $this->refreshBackup(true);
            }

            $this->secondary_next_step = self::SECONDARY_STEP_TAR_FILE;
        }

        // Check that there is no missing file
        if ($this->files_done < $this->total_files) {
            $this->log(
                'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::SFTP)
                .$this->l('Be careful! Not all of your files have been added to the backup. Number of files backuped:', self::PAGE)
                .' '.$this->files_done.'/'.$this->total_files
            );

            return false;
        }

        $this->files_done       = 0;
        $this->pos_file_to_tar  = 0;
        $this->next_step        = $this->step_send['ftp_resume'];

        if ($this->config->send_restore) {
            $this->log(
                sprintf($this->l('Creating restore file on %s account...', self::PAGE), self::SFTP)
            );

            $total_file_size = $this->getFileSize($this->restore_file);
            $byte_offset = 0;
            $last_percent = 0;
            $file = fopen($this->restore_file, "r+");

            while (!feof($file)) {
                $this->checkStopScript();
                $part_file = fread($file, self::MAX_FILE_UPLOAD_SIZE);
                $byte_offset += self::MAX_FILE_UPLOAD_SIZE;

                if ($total_file_size == 0) {
                    $this->log(
                        'WAR'
                        .$this->l('Your file seems to have an issue. Please check it.', self::PAGE)
                        .' '.$this->restore_file
                    );
                    return false;
                }

                $percent = (int)(($byte_offset/$total_file_size) * 100);

                // if self::MAX_FILE_UPLOAD_SIZE > than what is left to upload
                if ($percent > 100) {
                    $percent = 100;
                }

                if ($percent >= ($last_percent + 1)) {
                    $this->log(
                        sprintf($this->l('Creating restore file on %s account:', self::PAGE), self::SFTP)
                        .' '.$percent.'%'
                    );

                    $last_percent = $percent;
                }

                if (!$sftp_lib->put(self::NEW_RESTORE_NAME, $part_file, \phpseclib\Net\SFTP::RESUME)) {
                    $this->log(
                        'WAR'
                        .sprintf(
                            $this->l('An error occured while uploading your restore file to the %s server, please check your %s server log and retry', self::PAGE),
                            self::SFTP,
                            self::SFTP
                        )
                    );
                    return false;
                }
            }
        }

        $this->closeSFTP($sftp_lib);

        return true;
    }

    /**
     * Send a file on a OneDrive account
     *
     * @return  boolean     The success or failure of the operation
     */
    protected function sendFileToOnedrive()
    {
        $onedrive       = new Onedrive($this->onedrive_account_id);
        $access_token   = $onedrive->token;
        $onedrive_dir   = $onedrive->directory_key;

        if ($access_token !== false) {
            if ($this->next_step == $this->step_send['onedrive']
                && (!isset($this->onedrive_nb_part) || $this->onedrive_nb_part == 1)
            ) {
                $this->log(sprintf($this->l('Connect to your %s account...', self::PAGE), self::ONEDRIVE));
            }

            $onedrive_lib = $this->connectToOnedrive($access_token, $onedrive->id);

            if (!$onedrive_lib->testConnection()) {
                $this->log(
                    'WAR'.sprintf($this->l('Connection to your %s account impossible', self::PAGE), self::ONEDRIVE)
                );
                return false;
            }

            if ($this->next_step == $this->step_send['onedrive']
                && (!isset($this->onedrive_nb_part) || $this->onedrive_nb_part == 1)
            ) {
                if (Tools::substr($onedrive->directory_path, -1) != '/') {
                    $onedrive->directory_path .= '/';
                }

                // If file already on OneDrive
                foreach ($this->part_list as $part) {
                    $file_destination = $onedrive->directory_path.basename($part);

                    $this->log(
                        $this->l('Check if there is a previous version of the file:', self::PAGE).' '.$file_destination
                    );
                    $id_file = $onedrive_lib->checkExists(basename($part), $onedrive_dir);

                    if ($id_file !== false) {
                        $this->log($this->l('Delete previous version of the file:', self::PAGE).' '.$file_destination);
                        if ($onedrive_lib->deleteItem($id_file) === false) {
                            $this->log($this->l('Error while deleting the file:', self::PAGE).' '.$file_destination);
                        }
                    }
                }

                // Get old backups list
                $old_backups        = $this->getOnedriveFiles($onedrive_lib, $onedrive->directory_key);
                $nb_backup_to_keep  = $onedrive->config_nb_backup;
                $nb_files           = count($old_backups);
                $size_old_backups   = 0;

                // Get old backup (to delete) size
                if ($nb_backup_to_keep > 0) {
                    while ($nb_files >= $nb_backup_to_keep) {
                        if (isset($old_backups[$nb_files])) {
                            $size_old_backups += $old_backups[$nb_files]['size_byte'];

                            $nb_files--;
                        }
                    }
                }

                // Get available size
                $available_space = $onedrive_lib->fetchQuota() + $size_old_backups;

                // Get size to upload
                $to_send_size = $this->total_size;

                if ($this->config->send_restore) {
                    $total_file_size = $this->getFileSize($this->restore_file);
                    $to_send_size += $total_file_size;
                }

                // Check if we will have enough size after deleting old backup, too add new backup
                if ($available_space < $to_send_size) {
                    $this->log(
                        'WAR'.sprintf($this->l('Sending backup to %s account:', self::PAGE), self::ONEDRIVE).' '
                        .$this->l('Error there will not be enough space available, even if we delete the old backup', self::PAGE)
                    );
                    return false;
                }

                // Delete old backup
                if (!$this->deleteOnedriveOldBackup($onedrive_lib, $old_backups)) {
                    $this->log(
                        'WAR'
                        .sprintf($this->l('Sending backup to %s account:', self::PAGE), self::ONEDRIVE).' '
                        .$this->l('Error while deleting old backup', self::PAGE)
                    );
                    return false;
                }

                // Check available space
                $new_available_space = $onedrive_lib->fetchQuota();

                if ($this->config->send_restore) {
                    $total_file_size = $this->getFileSize($this->restore_file);
                    $new_available_space -= $total_file_size;
                }

                if ($new_available_space <= $this->total_size) {
                    $this->log(
                        'WAR'.sprintf($this->l('Sending backup to %s account:', self::PAGE), self::ONEDRIVE).' '
                        .$this->l('Not enough space available', self::PAGE)
                    );
                    return false;
                }

                $this->log(sprintf($this->l('Sending backup to %s account...', self::PAGE), self::ONEDRIVE));
                $this->onedrive_nb_part = 1;
                $this->onedrive_position = 0;
            }

            $nb_part    = 1;

            // Upload the file
            foreach ($this->part_list as $part) {
                if ($nb_part == $this->onedrive_nb_part) {
                    $file_name = basename($part);
                    $file_path = $part;

                    if ($this->next_step == $this->step_send['onedrive']) {
                        $this->next_step = $this->step_send['onedrive_resume'];
                        // Upload the file
                        $create_file = $onedrive_lib->createFile(
                            $file_name,
                            $file_path,
                            $onedrive_dir,
                            $this->onedrive_nb_part,
                            $this->total_nb_part
                        );

                        if ($create_file === false) {
                            return false;
                        }
                    } else {
                        // Resume upload of the file
                        $resume_create_file = $onedrive_lib->resumeCreateFile(
                            $file_path,
                            $this->onedrive_session,
                            $this->onedrive_position,
                            $this->onedrive_nb_part,
                            $this->total_nb_part
                        );

                        if ($resume_create_file === false) {
                            return false;
                        }
                    }

                    $this->onedrive_nb_part++;
                    // New part, so back to init values
                    $this->next_step = $this->step_send['onedrive'];
                    $this->onedrive_position = 0;
                }
                $nb_part++;
            }

            $this->next_step = $this->step_send['onedrive_resume'];

            if ($this->config->send_restore) {
                $this->log(
                    sprintf($this->l('Sending restore file to %s account...', self::PAGE), self::ONEDRIVE)
                );

                // Upload the file
                if ($onedrive_lib->createFile(self::NEW_RESTORE_NAME, $this->restore_file, $onedrive_dir) === false) {
                    return false;
                }
            }

            return true;
        }

        return false;
    }

    /**
     * Create a tar file on a OneDrive account
     *
     * @return  boolean     The success or failure of the operation
     */
    protected function createTarOnOnedrive()
    {
        $onedrive       = new Onedrive($this->onedrive_account_id);
        $access_token   = $onedrive->token;
        $onedrive_dir   = $onedrive->directory_key;

        $this->checkStopScript();

        if ($access_token === false) {
            $this->log(
                'WAR'
                .sprintf($this->l('Access to your %s account impossible', self::PAGE), self::ONEDRIVE)
            );

            return false;
        }

        if ($this->next_step == $this->step_send['onedrive']
            && $this->secondary_next_step  == self::SECONDARY_STEP_TAR_FILE
            && (!isset($this->onedrive_nb_part) || $this->onedrive_nb_part == 1)
        ) {
            $this->log(sprintf($this->l('Connect to your %s account...', self::PAGE), self::ONEDRIVE));

            $this->secondary_next_step      = self::SECONDARY_STEP_TAR_FILE;
            $this->files_done               = 0;
            $this->position_file_list_file  = 0;
            $this->part_number              = 1;
            $this->old_percent              = 0;
            $tar_name                       = basename($this->part_list[$this->part_number - 1]);

            if (count($this->part_list) != count($this->tar_files_size)) {
                $this->log(
                    'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::ONEDRIVE).' '
                    .$this->l('The backup cannot be created', self::PAGE)
                );

                return false;
            }

            if (!isset($this->tar_files_size[$this->part_number]) || !$this->tar_files_size[$this->part_number]) {
                $this->log(
                    'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::ONEDRIVE).' '
                    .sprintf($this->l('The calculated size of the file %s is not valid', self::PAGE), $tar_name)
                );

                return false;
            }
        }

        $this->checkStopScript();

        $onedrive_lib = $this->connectToOnedrive($access_token, $onedrive->id);

        if (!$onedrive_lib->testConnection()) {
            $this->log(
                'WAR'.sprintf($this->l('Connection to your %s account impossible', self::PAGE), self::ONEDRIVE)
            );
            return false;
        }

        $this->checkStopScript();

        if ($this->next_step == $this->step_send['onedrive']
            && $this->secondary_next_step  == self::SECONDARY_STEP_TAR_FILE
            && (!isset($this->onedrive_nb_part) || $this->onedrive_nb_part == 1)
        ) {
            if (Tools::substr($onedrive->directory_path, -1) != '/') {
                $onedrive->directory_path .= '/';
            }

            // Get old backups list
            $old_backups        = $this->getOnedriveFiles($onedrive_lib, $onedrive->directory_key);
            $nb_backup_to_keep  = $onedrive->config_nb_backup;
            $nb_files           = count($old_backups);
            $size_old_backups   = 0;

            // Get old backup (to delete) size
            if ($nb_backup_to_keep > 0) {
                while ($nb_files >= $nb_backup_to_keep) {
                    $this->checkStopScript();

                    if (isset($old_backups[$nb_files])) {
                        $size_old_backups += $old_backups[$nb_files]['size_byte'];

                        $nb_files--;
                    }
                }
            }

            $this->checkStopScript();

            // Get available size
            $available_space = $onedrive_lib->fetchQuota() + $size_old_backups;

            $this->checkStopScript();

            // Get size to upload
            $to_send_size = $this->total_size;

            if ($this->config->send_restore) {
                $total_file_size = $this->getFileSize($this->restore_file);

                $this->checkStopScript();

                $to_send_size += $total_file_size;
            }

            // Check if we will have enough size after deleting old backup, too add new backup
            if ($available_space < $to_send_size) {
                $this->log(
                    'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::ONEDRIVE).' '
                    .$this->l('Error there will not be enough space available, even if we delete the old backup', self::PAGE)
                );
                return false;
            }

            $this->checkStopScript();

            // Delete old backup
            if (!$this->deleteOnedriveOldBackup($onedrive_lib, $old_backups)) {
                $this->log(
                    'WAR'
                    .sprintf($this->l('Creating backup on %s account:', self::PAGE), self::ONEDRIVE).' '
                    .$this->l('Error while deleting old backup', self::PAGE)
                );
                return false;
            }

            $this->checkStopScript();

            // Check available space
            $new_available_space = $onedrive_lib->fetchQuota();

            $this->checkStopScript();

            if ($this->config->send_restore) {
                $total_file_size = $this->getFileSize($this->restore_file);

                $this->checkStopScript();

                $new_available_space -= $total_file_size;
            }

            if ($new_available_space <= $this->total_size) {
                $this->log(
                    'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::ONEDRIVE).' '
                    .$this->l('Not enough space available', self::PAGE)
                );
                return false;
            }

            $this->log(sprintf($this->l('Creating backup on %s account...', self::PAGE), self::ONEDRIVE));

            $this->onedrive_nb_part         = 1;
            $this->onedrive_position        = 0;
            $this->distant_tar_content      = '';
            $this->distant_tar_content_size = 0;
        }

        $this->checkStopScript();

        if ($this->getFileSize($this->file_list_file) <= 0) {
            $this->log(
                'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::ONEDRIVE)
                .' '.$this->l('No file to backup', self::PAGE)
            );
            return false;
        }

        $this->checkStopScript();

        if (!is_resource($this->handle_file_list_file)) {
            $this->log(
                'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::ONEDRIVE)
                .' '.$this->l('Error while getting the files to backup', self::PAGE)
            );
            return false;
        }

        $this->handle_file_list_file = $this->goToPositionInFile(
            $this->handle_file_list_file,
            $this->position_file_list_file,
            false
        );

        $this->checkStopScript();

        while (!feof($this->handle_file_list_file)) {
            $this->checkStopScript();

            $line = rtrim(fgets($this->handle_file_list_file));

            if (!$line) {
                continue;
            }

            $pos_cut = strrpos($line, ':');

            if ($pos_cut === false) {
                continue;
            }

            $current_file = self::binaryToString(Tools::substr($line, 0, $pos_cut));

            if ($current_file == '') {
                continue;
            }

            $found_size     = Tools::substr($line, ($pos_cut+1));
            $tar_name       = basename($this->part_list[$this->part_number - 1]);
            $path_in_tar    = '';

            //Normalize path
            $current_normalized_file = $this->normalizePath($current_file);

            //Find relative filename
            if (!$path_in_tar) {
                $filename = ltrim(self::getPart($current_normalized_file, $this->base_length), '/');
            } else {
                $filename = ltrim($path_in_tar, '/');
            }

            //File information
            $info       = $this->tarFileInfo($current_file);
            $diff_size  = 0;

            if ($info['size'] != $found_size) {
                if ($this->secondary_next_step == self::SECONDARY_STEP_TAR_FILE) {
                    $this->log(
                        'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::ONEDRIVE)
                        .' '.sprintf(
                            $this->l('The file %s has changed of size from %s to %s', self::PAGE),
                            $current_file,
                            $this->readableSize($found_size).' ('.$found_size.')',
                            $this->readableSize($info['size']).' ('.$info['size'].')'
                        )
                    );
                }

                $diff_size      = $info['size'] - $found_size;
                $info['size']   = $found_size;
            }

            if ($current_file == $this->dump_file && $this->config->type_backup == $this->type_backup_complete) {
                $path_module_backup = 'modules/'.$this->name.'/'.self::BACKUP_FOLDER.'/';
                $path_in_tar = str_replace($this->config_backup_dir, $path_module_backup, $current_file);
            }

            $this->checkStopScript();

            // Create file header
            if ($this->secondary_next_step == self::SECONDARY_STEP_TAR_FILE) {
                // Start to add a new file to the tar
                $header = $this->createTarHeader($filename, $info);

                $this->distant_tar_content_size += self::getLength($header);
                $this->distant_tar_content .= $header;

                $this->files_done++;

                $this->pos_file_to_tar      = 0;
                $this->secondary_next_step  = self::SECONDARY_STEP_TAR_FILE_CONTINUE;
            }

            if ($info['size'] > 0) {
                //Open the file
                if (($file_read = @fopen($current_file, 'rb')) === false) {
                    // If it is the first opening of the file
                    if ($this->pos_file_to_tar == 0) {
                        $this->log(
                            'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::ONEDRIVE)
                            .' '.$this->l('File', self::PAGE).' '.$current_file.' '
                            .$this->l('will be empty because the module can not open it, please check its rights and user owner', self::PAGE)
                        );
                    }
                }

                $this->checkStopScript();

                if (is_resource($file_read) && $this->secondary_next_step == self::SECONDARY_STEP_TAR_FILE_CONTINUE) {
                    $file_read = $this->goToPositionInFile($file_read, $this->pos_file_to_tar);

                    if ($file_read === false) {
                        return false;
                    }
                }

                //Data of the file
                $leftsize   = $info['size'] - $this->pos_file_to_tar;
                $blocksize  = self::TAR_BLOCK_SIZE;

                while ($leftsize > 0) {
                    $this->checkStopScript();

                    if ($this->next_step == $this->step_send['onedrive']) {
                        // Create new resumable session
                        $create_session = $onedrive_lib->createSession($tar_name, $onedrive_dir);

                        if ($create_session === false) {
                            $this->log(
                                'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::ONEDRIVE)
                                .' '.$this->l('File', self::PAGE).' '.$tar_name
                                .' '.$this->l('cannot be created', self::PAGE)
                            );

                            return false;
                        }

                        $this->onedrive_position    = 0;
                        $this->next_step            = $this->step_send['onedrive_resume'];
                    }

                    // If max size for OneDrive has been reach, we send the content or the size of the tar was reach
                    if ($this->distant_tar_content_size >= OnedriveLib::MAX_FILE_UPLOAD_SIZE
                        || ($this->distant_tar_content_size + self::TAR_END_SIZE) >= $this->tar_files_size[$this->part_number]
                    ) {
                        $end_of_current_part = 0;

                        if (($this->distant_tar_content_size + self::TAR_END_SIZE) >= $this->tar_files_size[$this->part_number]) {
                            $this->distant_tar_content_size +=  self::TAR_END_SIZE;
                            $this->distant_tar_content .= self::pad('', self::TAR_END_SIZE);
                            $end_of_current_part = 1;
                        }

                        if ($this->distant_tar_content_size > $this->tar_files_size[$this->part_number]) {
                            $this->log(
                                $this->l('The total size should not be less that the size we are now at', self::PAGE)
                                .' ('.$this->distant_tar_content_size.'/'.$this->tar_files_size[$this->part_number]
                            );
                        }

                        // Send content using the current session
                        $resume_upload_content = $onedrive_lib->resumeUploadContent(
                            $this->distant_tar_content,
                            $this->distant_tar_content_size,
                            $this->tar_files_size[$this->part_number],
                            $this->onedrive_session,
                            $this->onedrive_position
                        );

                        if ($resume_upload_content === false) {
                            $this->log(
                                'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::ONEDRIVE)
                                .' '.$this->l('File', self::PAGE).' '.$tar_name
                                .' '.$this->l('cannot be completed', self::PAGE)
                            );

                            return false;
                        } else {
                            $this->onedrive_position += $this->distant_tar_content_size;

                            $this->distant_tar_content      = '';
                            $this->distant_tar_content_size = 0;
                        }

                        if ($end_of_current_part) {
                            // New part, so back to init values
                            $this->onedrive_nb_part++;
                            $this->part_number++;

                            $this->next_step            = $this->step_send['onedrive'];
                            $this->onedrive_position    = 0;
                            $this->old_percent          = 0;
                            $this->secondary_next_step  = self::SECONDARY_STEP_TAR_FILE;
                        }
                    } else {
                        //Read data
                        $leftsize -= $blocksize;

                        if ($leftsize < 0) {
                            $blocksize += $leftsize;
                        }

                        $temp_content = '';

                        if (is_resource($file_read)) {
                            $temp_content = $this->createTarContent($file_read, $blocksize);
                        }

                        // Get where we are in the file
                        $this->pos_file_to_tar += $blocksize;

                        // If there is no content because the file size has changed (smaller file) we need to add \0
                        if ($diff_size < 0 && !$temp_content) {
                            //self::TAR_BLOCK_SIZE since createTarContent
                            //return self::TAR_BLOCK_SIZE size block (using self::pad)
                            $temp_content = self::pad('', self::TAR_BLOCK_SIZE);
                            $diff_size += self::TAR_BLOCK_SIZE;
                        } elseif ($temp_content === false) {
                            $this->log(
                                'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::ONEDRIVE)
                                .' '.$this->l('The module was unable to read the file', self::PAGE)
                                .' '.$current_file.', '.$this->l('please check its rights and user owner', self::PAGE)
                            );

                            if (is_resource($file_read)) {
                                fclose($file_read);
                            }

                            return false;
                        }

                        $this->distant_tar_content .= $temp_content;
                        $this->distant_tar_content_size += self::TAR_BLOCK_SIZE;

                        $percent = (($this->distant_tar_content_size + $this->onedrive_position)/$this->tar_files_size[$this->part_number]) * 100;

                        if ($percent >= $this->old_percent + 1) {
                            $this->old_percent = round($percent, 0);

                            if ($this->total_nb_part > 1) {
                                $this->log(
                                    sprintf($this->l('Creating backup on %s account:', self::PAGE), self::ONEDRIVE)
                                    .' '.$this->part_number.'/'.$this->total_nb_part.$this->l(':', self::PAGE)
                                    .' '.(int)$percent.'%'
                                );
                            } else {
                                $this->log(
                                    sprintf($this->l('Creating backup on %s account:', self::PAGE), self::ONEDRIVE)
                                    .' '.(int)$percent.'%'
                                );
                            }
                        }
                    }

                    //refresh
                    $this->refreshBackup(true);
                }

                //Close file
                if (is_resource($file_read)) {
                    fclose($file_read);
                }
            }

            $this->position_file_list_file = ftell($this->handle_file_list_file);

            $this->checkStopScript();

            //Check if tar file size has reach its predicted size
            if (isset($this->tar_files_size[$this->part_number])
                && ($this->onedrive_position + $this->distant_tar_content_size + self::TAR_END_SIZE) >= $this->tar_files_size[$this->part_number]
                && $this->onedrive_position < $this->tar_files_size[$this->part_number]
            ) {
                //The tar file will be too big, we need to close it and use a new one
                $this->distant_tar_content .= self::pad('', self::TAR_END_SIZE);
                $this->distant_tar_content_size +=  self::TAR_END_SIZE;

                if ($this->distant_tar_content_size > $this->tar_files_size[$this->part_number]) {
                    $this->log(
                        $this->l('The total size should not be less that the size we are now at', self::PAGE)
                        .' ('.$this->distant_tar_content_size.'/'.$this->tar_files_size[$this->part_number]
                    );
                }

                // Finish sending current tar
                $resume_upload_content = $onedrive_lib->resumeUploadContent(
                    $this->distant_tar_content,
                    $this->distant_tar_content_size,
                    $this->tar_files_size[$this->part_number],
                    $this->onedrive_session,
                    $this->onedrive_position
                );

                if ($resume_upload_content === false) {
                    $this->log(
                        'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::ONEDRIVE)
                        .' '.$this->l('File', self::PAGE).' '.$tar_name
                        .' '.$this->l('cannot be completed', self::PAGE)
                    );

                    return false;
                }

                // New part, so back to init values
                $this->onedrive_nb_part++;
                $this->part_number++;

                $this->next_step                = $this->step_send['onedrive'];
                $this->onedrive_position        = 0;
                $this->old_percent              = 0;
                $this->secondary_next_step      = self::SECONDARY_STEP_TAR_FILE;
                $this->distant_tar_content      = '';
                $this->distant_tar_content_size = 0;

                //refresh
                $this->refreshBackup(true);
            }
            $this->secondary_next_step = self::SECONDARY_STEP_TAR_FILE;
        }

        // Check that there is no missing file
        if ($this->files_done < $this->total_files) {
            $this->log(
                'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::ONEDRIVE)
                .$this->l('Be careful! Not all of your files have been added to the backup. Number of files backuped:', self::PAGE)
                .' '.$this->files_done.'/'.$this->total_files
            );

            return false;
        }

        $this->files_done       = 0;
        $this->pos_file_to_tar  = 0;

        $this->next_step = $this->step_send['onedrive_resume'];

        if ($this->config->send_restore) {
            $this->log(
                sprintf($this->l('Creating restore file on %s account...', self::PAGE), self::ONEDRIVE)
            );

            // Upload the file
            if ($onedrive_lib->createFile(self::NEW_RESTORE_NAME, $this->restore_file, $onedrive_dir) === false) {
                return false;
            }
        }

        return true;
    }

    /**
     * Send a file on a SugarSync account
     *
     * @return  boolean     The success or failure of the operation
     */
    protected function sendFileToSugarsync()
    {
        $sugarsync      = new Sugarsync($this->sugarsync_account_id);
        $token          = $sugarsync->token;
        $sugarsync_dir  = $sugarsync->directory_key;

        if ($token !== false) {
            if ($this->next_step == $this->step_send['sugarsync']
                && (!isset($this->sugarsync_nb_part) || $this->sugarsync_nb_part == 1)
            ) {
                $this->log(sprintf($this->l('Connect to your %s account...', self::PAGE), self::SUGARSYNC));
            }

            $sugarsync_lib = $this->connectToSugarsync($token, $sugarsync->id);

            if (!$sugarsync_lib->testConnection()) {
                $this->log(
                    'WAR'.sprintf($this->l('Connection to your %s account impossible', self::PAGE), self::SUGARSYNC)
                );
                return false;
            }

            if ($this->next_step == $this->step_send['sugarsync']
                && (!isset($this->sugarsync_nb_part) || $this->sugarsync_nb_part == 1)
            ) {
                if (Tools::substr($sugarsync->directory_path, -1) != '/') {
                    $sugarsync->directory_path .= '/';
                }

                // If file already on SugarSync
                foreach ($this->part_list as $part) {
                    $file_destination = $sugarsync->directory_path.basename($part);

                    $this->log(
                        $this->l('Check if there is a previous version of the file:', self::PAGE).' '.$file_destination
                    );

                    $id_file = $sugarsync_lib->checkExists($sugarsync_dir, basename($part));

                    if ($id_file !== false) {
                        $this->log($this->l('Delete previous version of the file:', self::PAGE).' '.$file_destination);
                        if (!$sugarsync_lib->deleteFile($id_file)) {
                            $this->log($this->l('Error while deleting the file:', self::PAGE).' '.$file_destination);
                        }
                    }
                }

                // Delete old backup
                if (!$this->deleteSugarsyncOldBackup($sugarsync_lib, $sugarsync_dir)) {
                    $this->log(
                        'WAR'
                        .sprintf($this->l('Sending backup to %s account:', self::PAGE), self::SUGARSYNC).' '
                        .$this->l('Error while deleting old backup', self::PAGE)
                    );
                    return false;
                }

                // Check available space
                $available_space = $sugarsync_lib->getAvailableQuota();

                if ($this->config->send_restore) {
                    $total_file_size = $this->getFileSize($this->restore_file);
                    $available_space -= $total_file_size;
                }

                if ($available_space <= $this->total_size) {
                    $this->log(
                        'WAR'.sprintf($this->l('Sending backup to %s account:', self::PAGE), self::SUGARSYNC).' '
                        .$this->l('Not enough space available', self::PAGE)
                    );
                    return false;
                }

                $this->log(sprintf($this->l('Sending backup to %s account...', self::PAGE), self::SUGARSYNC));
                $this->sugarsync_nb_part = 1;
                $this->sugarsync_position = 0;
            }

            $nb_part    = 1;

            // Upload the file
            foreach ($this->part_list as $part) {
                if ($nb_part == $this->sugarsync_nb_part) {
                    $file_name = basename($part);
                    $file_path = $part;

                    if ($this->next_step == $this->step_send['sugarsync']) {
                        $this->next_step = $this->step_send['sugarsync_resume'];
                        // Upload the file
                        $upload_file = $sugarsync_lib->uploadFile(
                            $file_name,
                            $file_path,
                            $sugarsync_dir,
                            $this->sugarsync_nb_part,
                            $this->total_nb_part
                        );

                        if ($upload_file === false) {
                            return false;
                        }
                    } else {
                        // Resume upload of the file
                        $resume_upload_file = $sugarsync_lib->resumeUploadFile(
                            $file_path,
                            $this->sugarsync_nb_part,
                            $this->total_nb_part
                        );

                        if ($resume_upload_file === false) {
                            return false;
                        }
                    }
                    $this->sugarsync_nb_part++;
                    // New part, so back to init values
                    $this->next_step = $this->step_send['sugarsync'];
                    $this->sugarsync_position = 0;
                }

                $nb_part++;
            }

            $this->next_step = $this->step_send['sugarsync_resume'];

            if ($this->config->send_restore) {
                $this->log(
                    sprintf($this->l('Sending restore file to %s account...', self::PAGE), self::SUGARSYNC)
                );

                $file_destination = $sugarsync->directory_path.self::NEW_RESTORE_NAME;

                $this->log(
                    $this->l('Check if there is a previous version of the file:', self::PAGE).' '.$file_destination
                );

                $id_file = $sugarsync_lib->checkExists($sugarsync_dir, self::NEW_RESTORE_NAME);

                if ($id_file !== false) {
                    $this->log($this->l('Delete previous version of the file:', self::PAGE).' '.$file_destination);
                    if (!$sugarsync_lib->deleteFile($id_file)) {
                        $this->log($this->l('Error while deleting the file:', self::PAGE).' '.$file_destination);
                    }
                }

                // Upload the file
                if ($sugarsync_lib->uploadFile(self::NEW_RESTORE_NAME, $this->restore_file, $sugarsync_dir) === false) {
                    return false;
                }
            }

            return true;
        } else {
            $this->log(
                'WAR'.sprintf($this->l('Sending backup to %s account:', self::PAGE), self::SUGARSYNC).' '
                .$this->l('Connection imposible', self::PAGE)
            );

            return false;
        }

        return false;
    }

    /**
     * Send a file on a ownCloud/Nextcloud account
     *
     * @return  boolean     The success or failure of the operation
     */
    protected function sendFileToOwncloud()
    {
        $owncloud           = new Owncloud($this->owncloud_account_id);
        $owncloud_server    = $owncloud->server;
        $owncloud_user      = $owncloud->login;
        $owncloud_pass      = $this->decrypt($owncloud->password);
        $owncloud_dir       = $owncloud->directory;

        if ($this->next_step == $this->step_send['owncloud']
            && (!isset($this->owncloud_nb_part) || $this->owncloud_nb_part == 1)
        ) {
            $this->log(sprintf($this->l('Connect to your %s account...', self::PAGE), self::OWNCLOUD));
        }

        $owncloud_lib = $this->connectToOwncloud($owncloud_server, $owncloud_user, $owncloud_pass);

        if (!$owncloud_lib->testConnection()) {
            $this->log(
                'WAR'.sprintf($this->l('Connection to your %s account impossible', self::PAGE), self::OWNCLOUD)
            );
            return false;
        }

        //ownCloud dir should end with a "/" except when testing if exist
        if (Tools::substr($owncloud_dir, -1) != '/') {
            $owncloud_dir .= '/';
        }

        if ($this->next_step == $this->step_send['owncloud']
            && (!isset($this->owncloud_nb_part) || $this->owncloud_nb_part == 1)
        ) {
            // If file already on ownCloud
            foreach ($this->part_list as $part) {
                $file_destination   = $owncloud_dir.basename($part);

                $this->log(
                    $this->l('Check if there is a previous version of the file:', self::PAGE).' '.$file_destination
                );

                if ($owncloud_lib->fileExists($file_destination)) {
                    $this->log($this->l('Delete previous version of the file:', self::PAGE).' '.$file_destination);
                    if ($owncloud_lib->deleteFile($file_destination) === false) {
                        $this->log($this->l('Error while deleting the file:', self::PAGE).' '.$part['name']);
                    }
                }
            }

            // Get old backups list
            $old_backups        = $this->getOwncloudFiles($owncloud_lib, $owncloud->directory);
            $nb_backup_to_keep  = $owncloud->config_nb_backup;
            $nb_files           = count($old_backups);
            $size_old_backups   = 0;

            // Get old backup (to delete) size
            if ($nb_backup_to_keep > 0) {
                while ($nb_files >= $nb_backup_to_keep) {
                    if (isset($old_backups[$nb_files])) {
                        $size_old_backups += $old_backups[$nb_files]['size_byte'];

                        $nb_files--;
                    }
                }
            }

            // Get available size
            $available_space = $owncloud_lib->getAvailableQuota();

            if ($available_space >= 0) { // Negative value probably mean unlimitted space
                $available_space += $size_old_backups;

                // Get size to upload
                $to_send_size = $this->total_size;

                if ($this->config->send_restore) {
                    $total_file_size = $this->getFileSize($this->restore_file);
                    $to_send_size += $total_file_size;
                }

                // Check if we will have enough size after deleting old backup, too add new backup
                if ($available_space < $to_send_size) {
                    $this->log(
                        'WAR'.sprintf($this->l('Sending backup to %s account:', self::PAGE), self::OWNCLOUD).' '
                        .$this->l('Error there will not be enough space available, even if we delete the old backup', self::PAGE)
                    );
                    return false;
                }
            }

            // Delete old backup
            if (!$this->deleteOwncloudOldBackup($owncloud_lib, $old_backups)) {
                $this->log(
                    'WAR'.sprintf($this->l('Sending backup to %s account:', self::PAGE), self::OWNCLOUD)
                    .' '.$this->l('Error while deleting old backup', self::PAGE)
                );
                return false;
            }

            // Check available space
            $new_available_space = $owncloud_lib->getAvailableQuota();

            if ($new_available_space >= 0) { // Negative value probably mean unlimitted space
                if ($this->config->send_restore) {
                    $total_file_size = $this->getFileSize($this->restore_file);
                    $new_available_space -= $total_file_size;
                }

                if ($new_available_space <= $this->total_size) {
                    $this->log(
                        'WAR'.sprintf($this->l('Sending backup to %s account:', self::PAGE), self::OWNCLOUD)
                        .' '.$this->l('Not enough space available', self::PAGE)
                    );
                    return false;
                }
            }

            $this->log(
                sprintf($this->l('Sending backup to %s account:', self::PAGE), self::OWNCLOUD).' '
                .$this->l('Check the path of your backup', self::PAGE)
            );

            // Check if the folder we want to use exists. If not we create it.
            if ($owncloud_lib->folderExists($owncloud->directory) === false) {
                $this->log(
                    sprintf($this->l('Sending backup to %s account:', self::PAGE), self::OWNCLOUD)
                    .' '.$this->l('Create the directory', self::PAGE).' "'.$owncloud->directory.'"'
                );

                if ($owncloud_lib->createFolder($owncloud->directory) === false) {
                    $this->log(
                        'WAR'.sprintf($this->l('Sending backup to %s account:', self::PAGE), self::OWNCLOUD).' '
                        .$this->l('Error while creating the directory', self::PAGE).' "'.$owncloud->directory.'"'
                    );
                    return false;
                }
            }

            $this->log(sprintf($this->l('Sending backup to %s account...', self::PAGE), self::OWNCLOUD));

            $this->owncloud_session     = rand();
            $this->owncloud_nb_part     = 1;
            $this->owncloud_nb_chunk    = 0;
            $this->owncloud_position    = 0;
        }

        $nb_part = 1;

        // Upload the file
        foreach ($this->part_list as $part) {
            if ($nb_part == $this->owncloud_nb_part) {
                $file_name = basename($part);
                $file_path = $part;

                if ($this->next_step == $this->step_send['owncloud']) {
                    $this->next_step = $this->step_send['owncloud_resume'];
                }

                // Upload the file
                $upload_file = $owncloud_lib->uploadFile(
                    $file_path,
                    $owncloud_dir,
                    $file_name,
                    $this->owncloud_position,
                    $this->owncloud_nb_part,
                    $this->total_nb_part
                );

                if ($upload_file === false) {
                    return false;
                }

                $this->owncloud_nb_part++;
                // New part, so back to init values
                $this->next_step            = $this->step_send['owncloud'];
                $this->owncloud_position    = 0;
                $this->owncloud_nb_chunk    = 0;
                $this->owncloud_session     = rand();
            }
            $nb_part++;
        }

        $this->next_step = $this->step_send['owncloud_resume'];

        if ($this->config->send_restore) {
            $this->log(
                sprintf($this->l('Sending restore file to %s account...', self::PAGE), self::OWNCLOUD)
            );

            // Upload the file
            if ($owncloud_lib->uploadFile($this->restore_file, $owncloud_dir, self::NEW_RESTORE_NAME) === false) {
                return false;
            }
        }

        return true;
    }

    /**
     * Create a tar file on a ownCloud/Nextcloud account
     *
     * @return  boolean     The success or failure of the operation
     */
    protected function createTarOnOwncloud()
    {
        $owncloud           = new Owncloud($this->owncloud_account_id);
        $owncloud_server    = $owncloud->server;
        $owncloud_user      = $owncloud->login;
        $owncloud_pass      = $this->decrypt($owncloud->password);
        $owncloud_dir       = $owncloud->directory;

        $this->checkStopScript();

        if ($this->next_step == $this->step_send['owncloud']
            && $this->secondary_next_step  == self::SECONDARY_STEP_TAR_FILE
            && (!isset($this->owncloud_nb_part) || $this->owncloud_nb_part == 1)
        ) {
            $this->log(sprintf($this->l('Connect to your %s account...', self::PAGE), self::OWNCLOUD));

            $this->secondary_next_step      = self::SECONDARY_STEP_TAR_FILE;
            $this->files_done               = 0;
            $this->position_file_list_file  = 0;
            $this->part_number              = 1;
            $this->old_percent              = 0;
            $tar_name                       = basename($this->part_list[$this->part_number - 1]);

            if (count($this->part_list) != count($this->tar_files_size)) {
                $this->log(
                    'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::OWNCLOUD).' '
                    .$this->l('The backup cannot be created', self::PAGE)
                );

                return false;
            }

            if (!isset($this->tar_files_size[$this->part_number]) || !$this->tar_files_size[$this->part_number]) {
                $this->log(
                    'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::OWNCLOUD).' '
                    .sprintf($this->l('The calculated size of the file %s is not valid', self::PAGE), $tar_name)
                );

                return false;
            }
        }

        $this->checkStopScript();

        $owncloud_lib = $this->connectToOwncloud($owncloud_server, $owncloud_user, $owncloud_pass);

        if (!$owncloud_lib->testConnection()) {
            $this->log(
                'WAR'.sprintf($this->l('Connection to your %s account impossible', self::PAGE), self::OWNCLOUD)
            );
            return false;
        }

        $this->checkStopScript();

        //ownCloud dir should end with a "/" except when testing if exist
        if (Tools::substr($owncloud_dir, -1) != '/') {
            $owncloud_dir .= '/';
        }

        if ($this->next_step == $this->step_send['owncloud']
            && $this->secondary_next_step  == self::SECONDARY_STEP_TAR_FILE
            && (!isset($this->owncloud_nb_part) || $this->owncloud_nb_part == 1)
        ) {
            // Get old backups list
            $old_backups        = $this->getOwncloudFiles($owncloud_lib, $owncloud->directory);
            $nb_backup_to_keep  = $owncloud->config_nb_backup;
            $nb_files           = count($old_backups);
            $size_old_backups   = 0;

            $this->checkStopScript();

            // Get old backup (to delete) size
            if ($nb_backup_to_keep > 0) {
                while ($nb_files >= $nb_backup_to_keep) {
                    $this->checkStopScript();

                    if (isset($old_backups[$nb_files])) {
                        $size_old_backups += $old_backups[$nb_files]['size_byte'];

                        $nb_files--;
                    }
                }
            }

            $this->checkStopScript();

            // Get available size
            $available_space = $owncloud_lib->getAvailableQuota();

            $this->checkStopScript();

            if ($available_space >= 0) { // Negative value probably mean unlimitted space
                $available_space += $size_old_backups;

                // Get size to upload
                $to_send_size = $this->total_size;

                if ($this->config->send_restore) {
                    $total_file_size = $this->getFileSize($this->restore_file);

                    $this->checkStopScript();

                    $to_send_size += $total_file_size;
                }

                // Check if we will have enough size after deleting old backup, too add new backup
                if ($available_space < $to_send_size) {
                    $this->log(
                        'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::OWNCLOUD).' '
                        .$this->l('Error there will not be enough space available, even if we delete the old backup', self::PAGE)
                    );
                    return false;
                }
            }

            $this->checkStopScript();

            // Delete old backup
            if (!$this->deleteOwncloudOldBackup($owncloud_lib, $old_backups)) {
                $this->log(
                    'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::OWNCLOUD)
                    .' '.$this->l('Error while deleting old backup', self::PAGE)
                );
                return false;
            }

            $this->checkStopScript();

            // Check available space
            $new_available_space = $owncloud_lib->getAvailableQuota();

            $this->checkStopScript();

            if ($new_available_space >= 0) { // Negative value probably mean unlimitted space
                if ($this->config->send_restore) {
                    $total_file_size = $this->getFileSize($this->restore_file);

                    $this->checkStopScript();

                    $new_available_space -= $total_file_size;
                }

                if ($new_available_space <= $this->total_size) {
                    $this->log(
                        'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::OWNCLOUD)
                        .' '.$this->l('Not enough space available', self::PAGE)
                    );
                    return false;
                }
            }

            $this->log(
                sprintf($this->l('Creating backup on %s account:', self::PAGE), self::OWNCLOUD).' '
                .$this->l('Check the path of your backup', self::PAGE)
            );

            // Check if the folder we want to use exists. If not we create it.
            if ($owncloud_lib->folderExists($owncloud->directory) === false) {
                $this->log(
                    sprintf($this->l('Creating backup on %s account:', self::PAGE), self::OWNCLOUD)
                    .' '.$this->l('Create the directory', self::PAGE).' "'.$owncloud->directory.'"'
                );

                $this->checkStopScript();

                if ($owncloud_lib->createFolder($owncloud->directory) === false) {
                    $this->log(
                        'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::OWNCLOUD).' '
                        .$this->l('Error while creating the directory', self::PAGE).' "'.$owncloud->directory.'"'
                    );
                    return false;
                }
            }

            $this->checkStopScript();

            $this->log(sprintf($this->l('Creating backup on %s account...', self::PAGE), self::OWNCLOUD));

            $this->owncloud_nb_part         = 1;
            $this->owncloud_position        = 0;
            $this->distant_tar_content      = '';
            $this->distant_tar_content_size = 0;
        }

        $this->checkStopScript();

        if ($this->getFileSize($this->file_list_file) <= 0) {
            $this->log(
                'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::OWNCLOUD)
                .' '.$this->l('No file to backup', self::PAGE)
            );
            return false;
        }

        $this->checkStopScript();

        if (!is_resource($this->handle_file_list_file)) {
            $this->log(
                'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::OWNCLOUD)
                .' '.$this->l('Error while getting the files to backup', self::PAGE)
            );
            return false;
        }

        $this->handle_file_list_file = $this->goToPositionInFile(
            $this->handle_file_list_file,
            $this->position_file_list_file,
            false
        );

        $this->checkStopScript();

        while (!feof($this->handle_file_list_file)) {
            $this->checkStopScript();

            $line = rtrim(fgets($this->handle_file_list_file));

            if (!$line) {
                continue;
            }

            $pos_cut = strrpos($line, ':');

            if ($pos_cut === false) {
                continue;
            }

            $current_file = self::binaryToString(Tools::substr($line, 0, $pos_cut));

            if ($current_file == '') {
                continue;
            }

            $this->checkStopScript();

            $found_size     = Tools::substr($line, ($pos_cut+1));
            $tar_name       = basename($this->part_list[$this->part_number - 1]);
            $path_in_tar    = '';

            //Normalize path
            $current_normalized_file = $this->normalizePath($current_file);

            //Find relative filename
            if (!$path_in_tar) {
                $filename = ltrim(self::getPart($current_normalized_file, $this->base_length), '/');
            } else {
                $filename = ltrim($path_in_tar, '/');
            }

            //File information
            $info       = $this->tarFileInfo($current_file);
            $diff_size  = 0;

            if ($info['size'] != $found_size) {
                if ($this->secondary_next_step == self::SECONDARY_STEP_TAR_FILE) {
                    $this->log(
                        'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::OWNCLOUD)
                        .' '.sprintf(
                            $this->l('The file %s has changed of size from %s to %s', self::PAGE),
                            $current_file,
                            $this->readableSize($found_size).' ('.$found_size.')',
                            $this->readableSize($info['size']).' ('.$info['size'].')'
                        )
                    );
                }

                $diff_size      = $info['size'] - $found_size;
                $info['size']   = $found_size;
            }

            if ($current_file == $this->dump_file && $this->config->type_backup == $this->type_backup_complete) {
                $path_module_backup = 'modules/'.$this->name.'/'.self::BACKUP_FOLDER.'/';
                $path_in_tar = str_replace($this->config_backup_dir, $path_module_backup, $current_file);
            }

            $this->checkStopScript();

            // Create file header
            if ($this->secondary_next_step == self::SECONDARY_STEP_TAR_FILE) {
                // Start to add a new file to the tar
                $header = $this->createTarHeader($filename, $info);

                $this->distant_tar_content_size += self::getLength($header);
                $this->distant_tar_content .= $header;

                $this->files_done++;

                $this->pos_file_to_tar      = 0;
                $this->secondary_next_step  = self::SECONDARY_STEP_TAR_FILE_CONTINUE;
            }

            if ($info['size'] > 0) {
                //Open the file
                if (($file_read = @fopen($current_file, 'rb')) === false) {
                    // If it is the first opening of the file
                    if ($this->pos_file_to_tar == 0) {
                        $this->log(
                            'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::OWNCLOUD)
                            .' '.$this->l('File', self::PAGE).' '.$current_file.' '
                            .$this->l('will be empty because the module can not open it, please check its rights and user owner', self::PAGE)
                        );
                    }
                }

                $this->checkStopScript();

                if (is_resource($file_read) && $this->secondary_next_step == self::SECONDARY_STEP_TAR_FILE_CONTINUE) {
                    $file_read = $this->goToPositionInFile($file_read, $this->pos_file_to_tar);

                    if ($file_read === false) {
                        return false;
                    }
                }

                //Data of the file
                $leftsize   = $info['size'] - $this->pos_file_to_tar;
                $blocksize  = self::TAR_BLOCK_SIZE;

                while ($leftsize > 0) {
                    $this->checkStopScript();

                    if ($this->next_step == $this->step_send['owncloud']) {
                        // Create new resumable session
                        $this->owncloud_session     = rand();
                        $this->owncloud_nb_chunk    = 0;
                        $this->owncloud_position    = 0;
                        $this->next_step            = $this->step_send['owncloud_resume'];
                    }

                    // If max size for ownCloud/Nextcloud has been reach,
                    // we send the content or the size of the tar was reach
                    if ($this->distant_tar_content_size >= OwncloudLib::MAX_CONTENT_UPLOAD_SIZE
                        || ($this->distant_tar_content_size + self::TAR_END_SIZE) >= $this->tar_files_size[$this->part_number]
                    ) {
                        $end_of_current_part = 0;

                        if (($this->distant_tar_content_size + self::TAR_END_SIZE) >= $this->tar_files_size[$this->part_number]) {
                            $this->distant_tar_content_size +=  self::TAR_END_SIZE;
                            $this->distant_tar_content .= self::pad('', self::TAR_END_SIZE);
                            $end_of_current_part = 1;
                        }

                        if ($this->distant_tar_content_size > $this->tar_files_size[$this->part_number]) {
                            $this->log(
                                $this->l('The total size should not be less that the size we are now at', self::PAGE)
                                .' ('.$this->distant_tar_content_size.'/'.$this->tar_files_size[$this->part_number]
                            );
                        }

                        // Send content using the current session
                        $resume_upload_content = $owncloud_lib->uploadContent(
                            $this->distant_tar_content,
                            $this->distant_tar_content_size,
                            $this->tar_files_size[$this->part_number],
                            $tar_name,
                            $owncloud_dir
                        );

                        if ($resume_upload_content === false) {
                            $this->log(
                                'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::OWNCLOUD)
                                .' '.$this->l('File', self::PAGE).' '.$tar_name
                                .' '.$this->l('cannot be completed', self::PAGE)
                            );

                            return false;
                        } else {
                            $this->owncloud_position += $this->distant_tar_content_size;

                            $this->distant_tar_content = '';
                            $this->distant_tar_content_size = 0;
                        }

                        if ($end_of_current_part) {
                            // New part, so back to init values
                            $this->owncloud_nb_part++;
                            $this->part_number++;

                            $this->next_step            = $this->step_send['owncloud'];
                            $this->owncloud_position    = 0;
                            $this->old_percent          = 0;
                            $this->secondary_next_step  = self::SECONDARY_STEP_TAR_FILE;
                        }
                    } else {
                        //Read data
                        $leftsize -= $blocksize;

                        if ($leftsize < 0) {
                            $blocksize += $leftsize;
                        }

                        $temp_content = '';

                        if (is_resource($file_read)) {
                            $temp_content = $this->createTarContent($file_read, $blocksize);
                        }

                        // Get where we are in the file
                        $this->pos_file_to_tar += $blocksize;

                        // If there is no content because the file size has changed (smaller file) we need to add \0
                        if ($diff_size < 0 && !$temp_content) {
                            //self::TAR_BLOCK_SIZE since createTarContent
                            //return self::TAR_BLOCK_SIZE size block (using self::pad)
                            $temp_content = self::pad('', self::TAR_BLOCK_SIZE);
                            $diff_size += self::TAR_BLOCK_SIZE;
                        } elseif ($temp_content === false) {
                            $this->log(
                                'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::OWNCLOUD)
                                .' '.$this->l('The module was unable to read the file', self::PAGE)
                                .' '.$current_file.', '.$this->l('please check its rights and user owner', self::PAGE)
                            );

                            if (is_resource($file_read)) {
                                fclose($file_read);
                            }

                            return false;
                        }

                        $this->distant_tar_content .= $temp_content;
                        $this->distant_tar_content_size += self::TAR_BLOCK_SIZE;

                        $percent = (($this->distant_tar_content_size + $this->owncloud_position)/$this->tar_files_size[$this->part_number]) * 100;

                        if ($percent >= $this->old_percent + 1) {
                            $this->old_percent = round($percent, 0);

                            if ($this->total_nb_part > 1) {
                                $this->log(
                                    sprintf($this->l('Creating backup on %s account:', self::PAGE), self::OWNCLOUD)
                                    .' '.$this->part_number.'/'.$this->total_nb_part.$this->l(':', self::PAGE)
                                    .' '.(int)$percent.'%'
                                );
                            } else {
                                $this->log(
                                    sprintf($this->l('Creating backup on %s account:', self::PAGE), self::OWNCLOUD)
                                    .' '.(int)$percent.'%'
                                );
                            }
                        }
                    }

                    //refresh
                    $this->refreshBackup(true);
                }

                //Close file
                if (is_resource($file_read)) {
                    fclose($file_read);
                }
            }

            $this->position_file_list_file = ftell($this->handle_file_list_file);

            $this->checkStopScript();

            //Check if tar file size has reach its predicted size
            if (isset($this->tar_files_size[$this->part_number])
                && ($this->owncloud_position + $this->distant_tar_content_size + self::TAR_END_SIZE) >= $this->tar_files_size[$this->part_number]
                && $this->owncloud_position < $this->tar_files_size[$this->part_number]
            ) {
                //The tar file will be too big, we need to close it and use a new one
                $this->distant_tar_content .= self::pad('', self::TAR_END_SIZE);
                $this->distant_tar_content_size +=  self::TAR_END_SIZE;

                if ($this->distant_tar_content_size > $this->tar_files_size[$this->part_number]) {
                    $this->log(
                        $this->l('The total size should not be less that the size we are now at', self::PAGE)
                        .' ('.$this->distant_tar_content_size.'/'.$this->tar_files_size[$this->part_number]
                    );
                }

                // Finish sending current tar
                $resume_upload_content = $owncloud_lib->uploadContent(
                    $this->distant_tar_content,
                    $this->distant_tar_content_size,
                    $this->tar_files_size[$this->part_number],
                    $tar_name,
                    $owncloud_dir
                );

                if ($resume_upload_content === false) {
                    $this->log(
                        'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::OWNCLOUD)
                        .' '.$this->l('File', self::PAGE).' '.$tar_name
                        .' '.$this->l('cannot be finished', self::PAGE)
                    );

                    return false;
                }

                // New part, so back to init values
                $this->owncloud_nb_part++;
                $this->part_number++;

                $this->next_step                = $this->step_send['owncloud'];
                $this->owncloud_position        = 0;
                $this->old_percent              = 0;
                $this->secondary_next_step      = self::SECONDARY_STEP_TAR_FILE;
                $this->distant_tar_content      = '';
                $this->distant_tar_content_size = 0;

                //refresh
                $this->refreshBackup(true);
            }
            $this->secondary_next_step = self::SECONDARY_STEP_TAR_FILE;
        }

        // Check that there is no missing file
        if ($this->files_done < $this->total_files) {
            $this->log(
                'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::OWNCLOUD)
                .$this->l('Be careful! Not all of your files have been added to the backup. Number of files backuped:', self::PAGE)
                .' '.$this->files_done.'/'.$this->total_files
            );

            return false;
        }

        $this->files_done = 0;
        $this->pos_file_to_tar = 0;

        $this->next_step = $this->step_send['owncloud_resume'];

        if ($this->config->send_restore) {
            $this->log(
                sprintf($this->l('Creating restore file on %s account...', self::PAGE), self::OWNCLOUD)
            );
            // Upload the file
            if ($owncloud_lib->uploadFile($this->restore_file, $owncloud_dir, self::NEW_RESTORE_NAME) === false) {
                return false;
            }
        }

        return true;
    }

    /**
     * Send a file on a WebDAV account
     *
     * @return  boolean     The success or failure of the operation
     */
    protected function sendFileToWebdav()
    {
        $webdav         = new Webdav($this->webdav_account_id);
        $webdav_server  = $webdav->server;
        $webdav_user    = $webdav->login;
        $webdav_pass    = $this->decrypt($webdav->password);
        $webdav_dir     = $webdav->directory;

        if ($this->next_step == $this->step_send['webdav']
            && (!isset($this->webdav_nb_part) || $this->webdav_nb_part == 1)
        ) {
            $this->log(sprintf($this->l('Connect to your %s account...', self::PAGE), self::WEBDAV));
        }

        $webdav_lib = $this->connectToWebdav($webdav_server, $webdav_user, $webdav_pass);

        if (!$webdav_lib->testConnection()) {
            $this->log(
                'WAR'.sprintf($this->l('Connection to your %s account impossible', self::PAGE), self::WEBDAV)
            );
            return false;
        }

        //WebDAV dir should end with a "/" except when testing if exist
        if (Tools::substr($webdav_dir, -1) != '/') {
            $webdav_dir .= '/';
        }

        if ($this->next_step == $this->step_send['webdav']
            && (!isset($this->webdav_nb_part) || $this->webdav_nb_part == 1)
        ) {
            // If file already on WebDAV
            foreach ($this->part_list as $part) {
                $file_destination   = $webdav_dir.basename($part);

                $this->log(
                    $this->l('Check if there is a previous version of the file:', self::PAGE).' '.$file_destination
                );
                if ($webdav_lib->fileExists($file_destination)) {
                    $this->log($this->l('Delete previous version of the file:', self::PAGE).' '.$file_destination);
                    if ($webdav_lib->deleteFile($file_destination) === false) {
                        $this->log($this->l('Error while deleting the file:', self::PAGE).' '.$part);
                    }
                }
            }

            // Get old backups list
            $old_backups        = $this->getWebdavFiles($webdav_lib, $webdav->directory);
            $nb_backup_to_keep  = $webdav->config_nb_backup;
            $nb_files           = count($old_backups);
            $size_old_backups   = 0;

            // Get old backup (to delete) size
            if ($nb_backup_to_keep > 0) {
                while ($nb_files >= $nb_backup_to_keep) {
                    if (isset($old_backups[$nb_files])) {
                        $size_old_backups += $old_backups[$nb_files]['size_byte'];

                        $nb_files--;
                    }
                }
            }

            // Get available size
            $available_space = $webdav_lib->getAvailableQuota();


            if ($available_space != -1) {
                $available_space += $size_old_backups;

                // Get size to upload
                $to_send_size = $this->total_size;

                if ($this->config->send_restore) {
                    $total_file_size = $this->getFileSize($this->restore_file);
                    $to_send_size += $total_file_size;
                }

                // Check if we will have enough size after deleting old backup, too add new backup
                if ($available_space < $to_send_size) {
                    $this->log(
                        'WAR'.sprintf($this->l('Sending backup to %s account:', self::PAGE), self::WEBDAV).' '
                        .$this->l('Error there will not be enough space available, even if we delete the old backup', self::PAGE)
                    );
                    return false;
                }
            }

            // Delete old backup
            if (!$this->deleteWebdavOldBackup($webdav_lib, $old_backups)) {
                $this->log(
                    'WAR'.sprintf($this->l('Sending backup to %s account:', self::PAGE), self::WEBDAV).' '
                    .$this->l('Error while deleting old backup', self::PAGE)
                );
                return false;
            }

            // Check available space
            $new_available_space = $webdav_lib->getAvailableQuota();

            if ($new_available_space != -1) {
                if ($this->config->send_restore) {
                    $total_file_size = $this->getFileSize($this->restore_file);
                    $new_available_space -= $total_file_size;
                }

                if ($new_available_space <= $this->total_size) {
                    $this->log(
                        'WAR'.sprintf($this->l('Sending backup to %s account:', self::PAGE), self::WEBDAV).' '
                        .$this->l('Not enough space available', self::PAGE)
                    );
                    return false;
                }
            }

            $this->log(
                sprintf($this->l('Sending backup to %s account:', self::PAGE), self::WEBDAV).' '
                .$this->l('Check the path of your backup', self::PAGE)
            );

            // Check if the folder we want to use exists. If not we create it.
            if ($webdav_lib->folderExists($webdav_dir) === false) {
                $this->log(
                    sprintf($this->l('Sending backup to %s account:', self::PAGE), self::WEBDAV)
                    .' '.$this->l('Create the directory', self::PAGE).' "'.$webdav->directory.'"'
                );

                if ($webdav_lib->createFolder($webdav_dir) === false) {
                    $this->log(
                        'WAR'.sprintf($this->l('Sending backup to %s account:', self::PAGE), self::WEBDAV)
                        .' '.$this->l('Error while creating the directory', self::PAGE).' "'.$webdav->directory.'"'
                    );
                    return false;
                }
            }

            $this->log(sprintf($this->l('Sending backup to %s account...', self::PAGE), self::WEBDAV));

            $this->webdav_session     = rand();
            $this->webdav_nb_part     = 1;
            $this->webdav_nb_chunk    = 0;
            $this->webdav_position    = 0;
        }

        $nb_part = 1;

        // Upload the file
        foreach ($this->part_list as $part) {
            if ($nb_part == $this->webdav_nb_part) {
                $file_name = basename($part);
                $file_path = $part;

                if ($this->next_step == $this->step_send['webdav']) {
                    $this->next_step = $this->step_send['webdav_resume'];
                }

                // Upload the file
                $upload_file = $webdav_lib->uploadFile(
                    $file_path,
                    $webdav_dir,
                    $file_name,
                    $this->webdav_position,
                    $this->webdav_nb_part,
                    $this->total_nb_part
                );

                if ($upload_file === false) {
                    return false;
                }

                $this->webdav_nb_part++;
                // New part, so back to init values
                $this->next_step            = $this->step_send['webdav'];
                $this->webdav_position    = 0;
                $this->webdav_nb_chunk    = 0;
                $this->webdav_session     = rand();
            }
            $nb_part++;
        }

        $this->next_step = $this->step_send['webdav_resume'];

        if ($this->config->send_restore) {
            $this->log(
                sprintf($this->l('Sending restore file to %s account...', self::PAGE), self::WEBDAV)
            );

            // Upload the file
            if ($webdav_lib->uploadFile($this->restore_file, $webdav_dir, self::NEW_RESTORE_NAME) === false) {
                return false;
            }
        }

        return true;
    }

    /**
     * Create a tar file on a WebDAV account
     *
     * @return  boolean     The success or failure of the operation
     */
    protected function createTarOnWebdav()
    {
        $webdav         = new Webdav($this->webdav_account_id);
        $webdav_server  = $webdav->server;
        $webdav_user    = $webdav->login;
        $webdav_pass    = $this->decrypt($webdav->password);
        $webdav_dir     = $webdav->directory;

        $this->checkStopScript();

        if ($this->next_step == $this->step_send['webdav']
            && $this->secondary_next_step  == self::SECONDARY_STEP_TAR_FILE
            && (!isset($this->webdav_nb_part) || $this->webdav_nb_part == 1)
        ) {
            $this->log(sprintf($this->l('Connect to your %s account...', self::PAGE), self::WEBDAV));

            $this->secondary_next_step      = self::SECONDARY_STEP_TAR_FILE;
            $this->files_done               = 0;
            $this->position_file_list_file  = 0;
            $this->part_number              = 1;
            $this->old_percent              = 0;
            $tar_name                       = basename($this->part_list[$this->part_number - 1]);

            if (count($this->part_list) != count($this->tar_files_size)) {
                $this->log(
                    'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::WEBDAV).' '
                    .$this->l('The backup cannot be created', self::PAGE)
                );

                return false;
            }

            if (!isset($this->tar_files_size[$this->part_number]) || !$this->tar_files_size[$this->part_number]) {
                $this->log(
                    'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::WEBDAV).' '
                    .sprintf($this->l('The calculated size of the file %s is not valid', self::PAGE), $tar_name)
                );

                return false;
            }
        }

        $this->checkStopScript();

        $webdav_lib = $this->connectToWebdav($webdav_server, $webdav_user, $webdav_pass);

        if (!$webdav_lib->testConnection()) {
            $this->log(
                'WAR'.sprintf($this->l('Connection to your %s account impossible', self::PAGE), self::WEBDAV)
            );
            return false;
        }

        $this->checkStopScript();

        //WebDAV dir should end with a "/" except when testing if exist
        if (Tools::substr($webdav_dir, -1) != '/') {
            $webdav_dir .= '/';
        }

        if ($this->next_step == $this->step_send['webdav']
            && $this->secondary_next_step  == self::SECONDARY_STEP_TAR_FILE
            && (!isset($this->webdav_nb_part) || $this->webdav_nb_part == 1)
        ) {
            // Get old backups list
            $old_backups        = $this->getWebdavFiles($webdav_lib, $webdav->directory);
            $nb_backup_to_keep  = $webdav->config_nb_backup;
            $nb_files           = count($old_backups);
            $size_old_backups   = 0;

            $this->checkStopScript();

            // Get old backup (to delete) size
            if ($nb_backup_to_keep > 0) {
                while ($nb_files >= $nb_backup_to_keep) {
                    $this->checkStopScript();

                    if (isset($old_backups[$nb_files])) {
                        $size_old_backups += $old_backups[$nb_files]['size_byte'];

                        $nb_files--;
                    }
                }
            }

            $this->checkStopScript();

            // Get available size
            $available_space = $webdav_lib->getAvailableQuota();

            $this->checkStopScript();

            if ($available_space != -1) {
                $available_space += $size_old_backups;

                // Get size to upload
                $to_send_size = $this->total_size;

                if ($this->config->send_restore) {
                    $total_file_size = $this->getFileSize($this->restore_file);

                    $this->checkStopScript();

                    $to_send_size += $total_file_size;
                }

                // Check if we will have enough size after deleting old backup, too add new backup
                if ($available_space < $to_send_size) {
                    $this->log(
                        'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::WEBDAV).' '
                        .$this->l('Error there will not be enough space available, even if we delete the old backup', self::PAGE)
                    );
                    return false;
                }
            }

            $this->checkStopScript();

            // Delete old backup
            if (!$this->deleteWebdavOldBackup($webdav_lib, $old_backups)) {
                $this->log(
                    'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::WEBDAV).' '
                    .$this->l('Error while deleting old backup', self::PAGE)
                );
                return false;
            }

            $this->checkStopScript();

            // Check available space
            $new_available_space = $webdav_lib->getAvailableQuota();

            $this->checkStopScript();

            if ($new_available_space != -1) {
                if ($this->config->send_restore) {
                    $total_file_size = $this->getFileSize($this->restore_file);

                    $this->checkStopScript();

                    $new_available_space -= $total_file_size;
                }

                if ($new_available_space <= $this->total_size) {
                    $this->log(
                        'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::WEBDAV).' '
                        .$this->l('Not enough space available', self::PAGE)
                    );
                    return false;
                }
            }

            $this->log(
                sprintf($this->l('Creating backup on %s account:', self::PAGE), self::WEBDAV).' '
                .$this->l('Check the path of your backup', self::PAGE)
            );

            // Check if the folder we want to use exists. If not we create it.
            if ($webdav_lib->folderExists($webdav_dir) === false) {
                $this->log(
                    sprintf($this->l('Creating backup on %s account:', self::PAGE), self::WEBDAV)
                    .' '.$this->l('Create the directory', self::PAGE).' "'.$webdav->directory.'"'
                );

                $this->checkStopScript();

                if ($webdav_lib->createFolder($webdav_dir) === false) {
                    $this->log(
                        'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::WEBDAV)
                        .' '.$this->l('Error while creating the directory', self::PAGE).' "'.$webdav->directory.'"'
                    );
                    return false;
                }
            }

            $this->checkStopScript();

            $this->log(sprintf($this->l('Creating backup on %s account...', self::PAGE), self::WEBDAV));

            $this->webdav_nb_part           = 1;
            $this->webdav_position          = 0;
            $this->distant_tar_content      = '';
            $this->distant_tar_content_size = 0;
        }

        $this->checkStopScript();

        if ($this->getFileSize($this->file_list_file) <= 0) {
            $this->log(
                'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::WEBDAV)
                .' '.$this->l('No file to backup', self::PAGE)
            );
            return false;
        }

        $this->checkStopScript();

        if (!is_resource($this->handle_file_list_file)) {
            $this->log(
                'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::WEBDAV)
                .' '.$this->l('Error while getting the files to backup', self::PAGE)
            );
            return false;
        }

        $this->handle_file_list_file = $this->goToPositionInFile(
            $this->handle_file_list_file,
            $this->position_file_list_file,
            false
        );

        $this->checkStopScript();

        while (!feof($this->handle_file_list_file)) {
            $this->checkStopScript();

            $line = rtrim(fgets($this->handle_file_list_file));

            if (!$line) {
                continue;
            }

            $pos_cut = strrpos($line, ':');

            if ($pos_cut === false) {
                continue;
            }

            $current_file = self::binaryToString(Tools::substr($line, 0, $pos_cut));

            if (!$current_file) {
                continue;
            }

            $this->checkStopScript();

            $found_size     = Tools::substr($line, ($pos_cut+1));
            $tar_name       = basename($this->part_list[$this->part_number - 1]);
            $path_in_tar    = '';

            //Normalize path
            $current_normalized_file = $this->normalizePath($current_file);

            //Find relative filename
            if (!$path_in_tar) {
                $filename = ltrim(self::getPart($current_normalized_file, $this->base_length), '/');
            } else {
                $filename = ltrim($path_in_tar, '/');
            }

            //File information
            $info       = $this->tarFileInfo($current_file);
            $diff_size  = 0;

            if ($info['size'] != $found_size) {
                if ($this->secondary_next_step == self::SECONDARY_STEP_TAR_FILE) {
                    $this->log(
                        'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::WEBDAV)
                        .' '.sprintf(
                            $this->l('The file %s has changed of size from %s to %s', self::PAGE),
                            $current_file,
                            $this->readableSize($found_size).' ('.$found_size.')',
                            $this->readableSize($info['size']).' ('.$info['size'].')'
                        )
                    );
                }

                $diff_size      = $info['size'] - $found_size;
                $info['size']   = $found_size;
            }

            if ($current_file == $this->dump_file && $this->config->type_backup == $this->type_backup_complete) {
                $path_module_backup = 'modules/'.$this->name.'/'.self::BACKUP_FOLDER.'/';
                $path_in_tar = str_replace($this->config_backup_dir, $path_module_backup, $current_file);
            }

            $this->checkStopScript();

            // Create file header
            if ($this->secondary_next_step == self::SECONDARY_STEP_TAR_FILE) {
                // Start to add a new file to the tar
                $header = $this->createTarHeader($filename, $info);

                $this->distant_tar_content_size += self::getLength($header);
                $this->distant_tar_content .= $header;

                $this->files_done++;

                $this->pos_file_to_tar      = 0;
                $this->secondary_next_step  = self::SECONDARY_STEP_TAR_FILE_CONTINUE;
            }

            if ($info['size'] > 0) {
                //Open the file
                if (($file_read = @fopen($current_file, 'rb')) === false) {
                    // If it is the first opening of the file
                    if ($this->pos_file_to_tar == 0) {
                        $this->log(
                            'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::WEBDAV)
                            .' '.$this->l('File', self::PAGE).' '.$current_file.' '
                            .$this->l('will be empty because the module can not open it, please check its rights and user owner', self::PAGE)
                        );
                    }
                }

                $this->checkStopScript();

                if (is_resource($file_read) && $this->secondary_next_step == self::SECONDARY_STEP_TAR_FILE_CONTINUE) {
                    $file_read = $this->goToPositionInFile($file_read, $this->pos_file_to_tar);

                    if ($file_read === false) {
                        return false;
                    }
                }

                //Data of the file
                $leftsize   = $info['size'] - $this->pos_file_to_tar;
                $blocksize  = self::TAR_BLOCK_SIZE;

                while ($leftsize > 0) {
                    $this->checkStopScript();

                    if ($this->next_step == $this->step_send['webdav']) {
                        // Create new resumable session
                        $this->webdav_session   = rand();
                        $this->webdav_nb_chunk  = 0;
                        $this->webdav_position  = 0;
                        $this->next_step        = $this->step_send['webdav_resume'];
                    }

                    // If max size for WebDAV has been reach, we send the content or the size of the tar was reach
                    if ($this->distant_tar_content_size >= WebdavLib::MAX_CONTENT_UPLOAD_SIZE
                        || ($this->distant_tar_content_size + self::TAR_END_SIZE) >= $this->tar_files_size[$this->part_number]
                    ) {
                        $end_of_current_part = 0;

                        if (($this->distant_tar_content_size + self::TAR_END_SIZE) >= $this->tar_files_size[$this->part_number]) {
                            $this->distant_tar_content_size +=  self::TAR_END_SIZE;
                            $this->distant_tar_content .= self::pad('', self::TAR_END_SIZE);
                            $end_of_current_part = 1;
                        }

                        if ($this->distant_tar_content_size > $this->tar_files_size[$this->part_number]) {
                            $this->log(
                                $this->l('The total size should not be less that the size we are now at', self::PAGE)
                                .' ('.$this->distant_tar_content_size.'/'.$this->tar_files_size[$this->part_number]
                            );
                        }

                        // Send content using the current session
                        $resume_upload_content = $webdav_lib->uploadContent(
                            $this->distant_tar_content,
                            $this->distant_tar_content_size,
                            $this->tar_files_size[$this->part_number],
                            $tar_name,
                            $webdav_dir
                        );

                        if ($resume_upload_content === false) {
                            $this->log(
                                'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::WEBDAV)
                                .' '.$this->l('File', self::PAGE).' '.$tar_name
                                .' '.$this->l('cannot be completed', self::PAGE)
                            );

                            return false;
                        } else {
                            $this->webdav_position += $this->distant_tar_content_size;

                            $this->distant_tar_content = '';
                            $this->distant_tar_content_size = 0;
                        }

                        if ($end_of_current_part) {
                            // New part, so back to init values
                            $this->webdav_nb_part++;
                            $this->part_number++;

                            $this->next_step            = $this->step_send['webdav'];
                            $this->webdav_position      = 0;
                            $this->old_percent          = 0;
                            $this->secondary_next_step  = self::SECONDARY_STEP_TAR_FILE;
                        }
                    } else {
                        //Read data
                        $leftsize -= $blocksize;

                        if ($leftsize < 0) {
                            $blocksize += $leftsize;
                        }

                        $temp_content = '';

                        if (is_resource($file_read)) {
                            $temp_content = $this->createTarContent($file_read, $blocksize);
                        }

                        // Get where we are in the file
                        $this->pos_file_to_tar += $blocksize;

                        // If there is no content because the file size has changed (smaller file) we need to add \0
                        if ($diff_size < 0 && !$temp_content) {
                            //self::TAR_BLOCK_SIZE since createTarContent
                            //return self::TAR_BLOCK_SIZE size block (using self::pad)
                            $temp_content = self::pad('', self::TAR_BLOCK_SIZE);
                            $diff_size += self::TAR_BLOCK_SIZE;
                        } elseif ($temp_content === false) {
                            $this->log(
                                'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::WEBDAV)
                                .' '.$this->l('The module was unable to read the file', self::PAGE)
                                .' '.$current_file.', '.$this->l('please check its rights and user owner', self::PAGE)
                            );

                            if (is_resource($file_read)) {
                                fclose($file_read);
                            }

                            return false;
                        }

                        $this->distant_tar_content .= $temp_content;
                        $this->distant_tar_content_size += self::TAR_BLOCK_SIZE;

                        $percent = (($this->distant_tar_content_size + $this->webdav_position)/$this->tar_files_size[$this->part_number]) * 100;

                        if ($percent >= $this->old_percent + 1) {
                            $this->old_percent = round($percent, 0);

                            if ($this->total_nb_part > 1) {
                                $this->log(
                                    sprintf($this->l('Creating backup on %s account:', self::PAGE), self::WEBDAV)
                                    .' '.$this->part_number.'/'.$this->total_nb_part.$this->l(':', self::PAGE)
                                    .' '.(int)$percent.'%'
                                );
                            } else {
                                $this->log(
                                    sprintf($this->l('Creating backup on %s account:', self::PAGE), self::WEBDAV)
                                    .' '.(int)$percent.'%'
                                );
                            }
                        }
                    }

                    //refresh
                    $this->refreshBackup(true);
                }

                //Close file
                if (is_resource($file_read)) {
                    fclose($file_read);
                }
            }

            $this->position_file_list_file = ftell($this->handle_file_list_file);

            $this->checkStopScript();

            //Check if tar file size has reach its predicted size
            if (isset($this->tar_files_size[$this->part_number])
                && ($this->webdav_position + $this->distant_tar_content_size + self::TAR_END_SIZE) >= $this->tar_files_size[$this->part_number]
                && $this->webdav_position < $this->tar_files_size[$this->part_number]
            ) {
                //The tar file will be too big, we need to close it and use a new one
                $this->distant_tar_content .= self::pad('', self::TAR_END_SIZE);
                $this->distant_tar_content_size +=  self::TAR_END_SIZE;

                if ($this->distant_tar_content_size > $this->tar_files_size[$this->part_number]) {
                    $this->log(
                        $this->l('The total size should not be less that the size we are now at', self::PAGE)
                        .' ('.$this->distant_tar_content_size.'/'.$this->tar_files_size[$this->part_number]
                    );
                }

                // Finish sending current tar
                $resume_upload_content = $webdav_lib->uploadContent(
                    $this->distant_tar_content,
                    $this->distant_tar_content_size,
                    $this->tar_files_size[$this->part_number],
                    $tar_name,
                    $webdav_dir
                );

                if ($resume_upload_content === false) {
                    $this->log(
                        'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::WEBDAV)
                        .' '.$this->l('File', self::PAGE).' '.$tar_name
                        .' '.$this->l('cannot be finished', self::PAGE)
                    );

                    return false;
                }

                // New part, so back to init values
                $this->webdav_nb_part++;
                $this->part_number++;

                $this->next_step                = $this->step_send['webdav'];
                $this->webdav_position          = 0;
                $this->old_percent              = 0;
                $this->secondary_next_step      = self::SECONDARY_STEP_TAR_FILE;
                $this->distant_tar_content      = '';
                $this->distant_tar_content_size = 0;

                //refresh
                $this->refreshBackup(true);
            }

            $this->secondary_next_step = self::SECONDARY_STEP_TAR_FILE;
        }

        // Check that there is no missing file
        if ($this->files_done < $this->total_files) {
            $this->log(
                'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::WEBDAV)
                .$this->l('Be careful! Not all of your files have been added to the backup. Number of files backuped:', self::PAGE)
                .' '.$this->files_done.'/'.$this->total_files
            );

            return false;
        }

        $this->files_done = 0;
        $this->pos_file_to_tar = 0;

        $this->next_step = $this->step_send['webdav_resume'];

        if ($this->config->send_restore) {
            $this->log(
                sprintf($this->l('Creating restore file on %s account...', self::PAGE), self::WEBDAV)
            );

            // Upload the file
            if ($webdav_lib->uploadFile($this->restore_file, $webdav_dir, self::NEW_RESTORE_NAME) === false) {
                return false;
            }
        }

        return true;
    }

    /**
     * Send a file on a Google Drive account
     *
     * @return  boolean     The success or failure of the operation
     */
    protected function sendFileToGoogledrive()
    {
        $googledrive        = new Googledrive($this->googledrive_account_id);
        $access_token       = $googledrive->token;
        $googledrive_dir    = $googledrive->directory_key;

        if ($this->next_step == $this->step_send['googledrive']
            && (!isset($this->googledrive_nb_part) || $this->googledrive_nb_part == 1)
        ) {
            $this->log(sprintf($this->l('Connect to your %s account...', self::PAGE), self::GOOGLEDRIVE));
        }

        $googledrive_lib = $this->connectToGoogledrive($access_token);

        if (!$googledrive_lib->testConnection()) {
            $this->log(
                'WAR'.sprintf($this->l('Connection to your %s account impossible', self::PAGE), self::GOOGLEDRIVE)
            );
            return false;
        }

        if ($this->next_step == $this->step_send['googledrive']
            && (!isset($this->googledrive_nb_part) || $this->googledrive_nb_part == 1)
        ) {
            if (Tools::substr($googledrive->directory_path, -1) != '/') {
                $googledrive->directory_path .= '/';
            }

            // If file already on Google Drive
            foreach ($this->part_list as $part) {
                $file_destination = $googledrive->directory_path.basename($part);

                $this->log(
                    $this->l('Check if there is a previous version of the file:', self::PAGE).' '.$file_destination
                );
                $id_file = $googledrive_lib->checkExists(basename($part), $googledrive_dir);

                if ($id_file !== false) {
                    $this->log($this->l('Delete previous version of the file:', self::PAGE).' '.$file_destination);
                    if ($googledrive_lib->deleteFile($id_file) === false) {
                        $this->log($this->l('Error while deleting the file:', self::PAGE).' '.$file_destination);
                    }
                }
            }

            // Get old backups list
            $old_backups        = $this->getGoogledriveFiles($googledrive_lib, $googledrive_dir);
            $nb_backup_to_keep  = $googledrive->config_nb_backup;
            $nb_files           = count($old_backups);
            $size_old_backups   = 0;

            // Get old backup (to delete) size
            if ($nb_backup_to_keep > 0) {
                while ($nb_files >= $nb_backup_to_keep) {
                    if (isset($old_backups[$nb_files])) {
                        $size_old_backups += $old_backups[$nb_files]['size_byte'];

                        $nb_files--;
                    }
                }
            }

            // Get available size
            $available_space = $googledrive_lib->getAvailableQuota();

            if ($available_space != '-1') {
                $available_space += $size_old_backups;

                // Get size to upload
                $to_send_size = $this->total_size;

                if ($this->config->send_restore) {
                    $total_file_size = $this->getFileSize($this->restore_file);
                    $to_send_size += $total_file_size;
                }

                // Check if we will have enough size after deleting old backup, too add new backup
                if ($available_space < $to_send_size) {
                    $this->log(
                        'WAR'.sprintf($this->l('Sending backup to %s account:', self::PAGE), self::GOOGLEDRIVE).' '
                        .$this->l('Error there will not be enough space available, even if we delete the old backup', self::PAGE)
                    );
                    return false;
                }
            }

            // Delete old backup
            if (!$this->deleteGoogledriveOldBackup($googledrive_lib, $old_backups)) {
                $this->log(
                    'WAR'.sprintf($this->l('Sending backup to %s account:', self::PAGE), self::GOOGLEDRIVE)
                    .' '.$this->l('Error while deleting old backup', self::PAGE)
                );
                return false;
            }

            // Check available space
            $new_available_space = $googledrive_lib->getAvailableQuota();

            if ($new_available_space != '-1') {
                if ($this->config->send_restore) {
                    $total_file_size = $this->getFileSize($this->restore_file);
                    $new_available_space -= $total_file_size;
                }

                if ($new_available_space <= $this->total_size) {
                    $this->log(
                        'WAR'.sprintf($this->l('Sending backup to %s account:', self::PAGE), self::GOOGLEDRIVE)
                        .' '.$this->l('Not enough space available', self::PAGE)
                    );
                    return false;
                }
            }

            $this->log(sprintf($this->l('Sending backup to %s account...', self::PAGE), self::GOOGLEDRIVE));
            $this->googledrive_nb_part = 1;
            $this->googledrive_position = 0;
        }

        $nb_part = 1;
        // Upload the file
        foreach ($this->part_list as $part) {
            if ($nb_part == $this->googledrive_nb_part) {
                if ($this->next_step == $this->step_send['googledrive']) {
                    $this->next_step = $this->step_send['googledrive_resume'];

                    // Upload the file
                    $upload_file = $googledrive_lib->uploadFile(
                        $part,
                        $googledrive_dir,
                        '',
                        $this->googledrive_nb_part,
                        $this->total_nb_part
                    );

                    if ($upload_file === false) {
                        return false;
                    }
                } else {
                    // Resume upload of the file
                    $resume_upload_file = $googledrive_lib->resumeUploadFile(
                        $part,
                        $this->googledrive_nb_part,
                        $this->total_nb_part
                    );

                    if ($resume_upload_file === false) {
                        return false;
                    }
                }
                $this->googledrive_nb_part++;
                // New part, so back to init values
                $this->next_step = $this->step_send['googledrive'];
                $this->googledrive_position = 0;
            }

            $nb_part++;
        }

        $this->next_step = $this->step_send['googledrive_resume'];

        if ($this->config->send_restore) {
            if (Tools::substr($googledrive->directory_path, -1) != '/') {
                $googledrive->directory_path .= '/';
            }

            $file_destination = $googledrive->directory_path.self::NEW_RESTORE_NAME;
            $id_file = $googledrive_lib->checkExists(self::NEW_RESTORE_NAME, $googledrive_dir);

            if ($id_file !== false) {
                $this->log($this->l('Delete previous version of the file:', self::PAGE).' '.$file_destination);
                if ($googledrive_lib->deleteFile($id_file) === false) {
                    $this->log($this->l('Error while deleting the file:', self::PAGE).' '.$file_destination);
                }
            }

            $this->log(
                sprintf($this->l('Sending restore file to %s account...', self::PAGE), self::GOOGLEDRIVE)
            );

            // Upload the file
            if ($googledrive_lib->uploadFile($this->restore_file, $googledrive_dir, self::NEW_RESTORE_NAME) === false) {
                return false;
            }
        }

        return true;
    }

    /**
     * Create a tar file on a Google Drive account
     *
     * @return  boolean     The success or failure of the operation
     */
    protected function createTarOnGoogledrive()
    {
        $googledrive        = new Googledrive($this->googledrive_account_id);
        $access_token       = $googledrive->token;
        $googledrive_dir    = $googledrive->directory_key;

        $this->checkStopScript();

        if ($this->next_step == $this->step_send['googledrive']
            && $this->secondary_next_step  == self::SECONDARY_STEP_TAR_FILE
            && (!isset($this->googledrive_nb_part) || $this->googledrive_nb_part == 1)
        ) {
            $this->log(sprintf($this->l('Connect to your %s account...', self::PAGE), self::GOOGLEDRIVE));

            $this->secondary_next_step      = self::SECONDARY_STEP_TAR_FILE;
            $this->files_done               = 0;
            $this->position_file_list_file  = 0;
            $this->part_number              = 1;
            $this->old_percent              = 0;
            $tar_name                       = basename($this->part_list[$this->part_number - 1]);

            if (count($this->part_list) != count($this->tar_files_size)) {
                $this->log(
                    'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::GOOGLEDRIVE)
                    .' '.$this->l('The backup cannot be created', self::PAGE)
                );

                return false;
            }

            if (!isset($this->tar_files_size[$this->part_number]) || !$this->tar_files_size[$this->part_number]) {
                $this->log(
                    'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::GOOGLEDRIVE).' '
                    .sprintf($this->l('The calculated size of the file %s is not valid', self::PAGE), $tar_name)
                );

                return false;
            }
        }

        $this->checkStopScript();

        $googledrive_lib = $this->connectToGoogledrive($access_token);

        if (!$googledrive_lib->testConnection()) {
            $this->log(
                'WAR'.sprintf($this->l('Connection to your %s account impossible', self::PAGE), self::GOOGLEDRIVE)
            );
            return false;
        }

        $this->checkStopScript();

        if ($this->next_step == $this->step_send['googledrive']
            && $this->secondary_next_step  == self::SECONDARY_STEP_TAR_FILE
            && (!isset($this->googledrive_nb_part) || $this->googledrive_nb_part == 1)
        ) {
            if (Tools::substr($googledrive->directory_path, -1) != '/') {
                $googledrive->directory_path .= '/';
            }

            // Get old backups list
            $old_backups        = $this->getGoogledriveFiles($googledrive_lib, $googledrive_dir);
            $nb_backup_to_keep  = $googledrive->config_nb_backup;
            $nb_files           = count($old_backups);
            $size_old_backups   = 0;

            $this->checkStopScript();

            // Get old backup (to delete) size
            if ($nb_backup_to_keep > 0) {
                while ($nb_files >= $nb_backup_to_keep) {
                    $this->checkStopScript();

                    if (isset($old_backups[$nb_files])) {
                        $size_old_backups += $old_backups[$nb_files]['size_byte'];

                        $nb_files--;
                    }
                }
            }

            $this->checkStopScript();

            // Get available size
            $available_space = $googledrive_lib->getAvailableQuota();

            $this->checkStopScript();

            if ($available_space != '-1') {
                $available_space += $size_old_backups;

                // Get size to upload
                $to_send_size = $this->total_size;

                if ($this->config->send_restore) {
                    $total_file_size = $this->getFileSize($this->restore_file);

                    $this->checkStopScript();

                    $to_send_size += $total_file_size;
                }

                // Check if we will have enough size after deleting old backup, too add new backup
                if ($available_space < $to_send_size) {
                    $this->log(
                        'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::GOOGLEDRIVE).' '
                        .$this->l('Error there will not be enough space available, even if we delete the old backup', self::PAGE)
                    );
                    return false;
                }
            }

            $this->checkStopScript();

            // Delete old backup
            if (!$this->deleteGoogledriveOldBackup($googledrive_lib, $old_backups)) {
                $this->log(
                    'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::GOOGLEDRIVE)
                    .' '.$this->l('Error while deleting old backup', self::PAGE)
                );
                return false;
            }

            $this->checkStopScript();

            // Check available space
            $new_available_space = $googledrive_lib->getAvailableQuota();

            $this->checkStopScript();

            if ($new_available_space != '-1') {
                if ($this->config->send_restore) {
                    $total_file_size = $this->getFileSize($this->restore_file);

                    $this->checkStopScript();

                    $new_available_space -= $total_file_size;
                }

                if ($new_available_space <= $this->total_size) {
                    $this->log(
                        'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::GOOGLEDRIVE)
                        .' '.$this->l('Not enough space available', self::PAGE)
                    );
                    return false;
                }
            }

            $this->log(sprintf($this->l('Creating backup on %s account...', self::PAGE), self::GOOGLEDRIVE));

            $this->googledrive_nb_part      = 1;
            $this->googledrive_position     = 0;
            $this->distant_tar_content      = '';
            $this->distant_tar_content_size = 0;
        }

        $this->checkStopScript();

        if ($this->getFileSize($this->file_list_file) <= 0) {
            $this->log(
                'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::GOOGLEDRIVE)
                .' '.$this->l('No file to backup', self::PAGE)
            );
            return false;
        }

        $this->checkStopScript();

        if (!is_resource($this->handle_file_list_file)) {
            $this->log(
                'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::GOOGLEDRIVE)
                .' '.$this->l('Error while getting the files to backup', self::PAGE)
            );
            return false;
        }

        $this->handle_file_list_file = $this->goToPositionInFile(
            $this->handle_file_list_file,
            $this->position_file_list_file,
            false
        );

        $this->checkStopScript();

        while (!feof($this->handle_file_list_file)) {
            $this->checkStopScript();

            $line = rtrim(fgets($this->handle_file_list_file));

            if (!$line) {
                continue;
            }

            $pos_cut = strrpos($line, ':');

            if ($pos_cut === false) {
                continue;
            }

            $current_file = self::binaryToString(Tools::substr($line, 0, $pos_cut));

            if (!$current_file) {
                continue;
            }

            //$this->log($current_file, true);

            $this->checkStopScript();

            $found_size     = Tools::substr($line, ($pos_cut+1));
            $tar_name       = basename($this->part_list[$this->part_number - 1]);
            $path_in_tar    = '';

            //Normalize path
            $current_normalized_file = $this->normalizePath($current_file);

            //Find relative filename
            if (!$path_in_tar) {
                $filename = ltrim(self::getPart($current_normalized_file, $this->base_length), '/');
            } else {
                $filename = ltrim($path_in_tar, '/');
            }

            //File information
            $info       = $this->tarFileInfo($current_file);
            $diff_size  = 0;

            if ($info['size'] != $found_size) {
                if ($this->secondary_next_step == self::SECONDARY_STEP_TAR_FILE) {
                    $this->log(
                        'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::GOOGLEDRIVE)
                        .' '.sprintf(
                            $this->l('The file %s has changed of size from %s to %s', self::PAGE),
                            $current_file,
                            $this->readableSize($found_size).' ('.$found_size.')',
                            $this->readableSize($info['size']).' ('.$info['size'].')'
                        )
                    );
                }

                $diff_size      = $info['size'] - $found_size;
                $info['size']   = $found_size;
            }

            if ($current_file == $this->dump_file && $this->config->type_backup == $this->type_backup_complete) {
                $path_module_backup = 'modules/'.$this->name.'/'.self::BACKUP_FOLDER.'/';
                $path_in_tar        = str_replace($this->config_backup_dir, $path_module_backup, $current_file);
            }

            $this->checkStopScript();

            // Create file header
            if ($this->secondary_next_step == self::SECONDARY_STEP_TAR_FILE) {
                // Start to add a new file to the tar
                $header = $this->createTarHeader($filename, $info);

                $this->distant_tar_content_size += self::getLength($header);
                $this->distant_tar_content .= $header;

                $this->files_done++;

                $this->pos_file_to_tar      = 0;
                $this->secondary_next_step  = self::SECONDARY_STEP_TAR_FILE_CONTINUE;
            }

            if ($info['size'] > 0) {
                //Open the file
                if (($file_read = @fopen($current_file, 'rb')) === false) {
                    // If it is the first opening of the file
                    if ($this->pos_file_to_tar == 0) {
                        $this->log(
                            'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::GOOGLEDRIVE)
                            .' '.$this->l('File', self::PAGE).' '.$current_file.' '
                            .$this->l('will be empty because the module can not open it, please check its rights and user owner', self::PAGE)
                        );
                    }
                }

                $this->checkStopScript();

                if (is_resource($file_read) && $this->secondary_next_step == self::SECONDARY_STEP_TAR_FILE_CONTINUE) {
                    $file_read = $this->goToPositionInFile($file_read, $this->pos_file_to_tar);

                    if ($file_read === false) {
                        return false;
                    }
                }

                //Data of the file
                $leftsize   = $info['size'] - $this->pos_file_to_tar;
                $blocksize  = self::TAR_BLOCK_SIZE;

                while ($leftsize > 0) {
                    $this->checkStopScript();

                    if ($this->next_step == $this->step_send['googledrive']) {
                        // Create new resumable session
                        $create_session = $googledrive_lib->createSession(
                            $googledrive_dir,
                            $tar_name,
                            'application/x-tar',
                            $this->tar_files_size[$this->part_number]
                        );

                        if ($create_session === false) {
                            $this->log(
                                'WAR'
                                .sprintf($this->l('Creating backup on %s account:', self::PAGE), self::GOOGLEDRIVE)
                                .' '.$this->l('File', self::PAGE).' '.$tar_name
                                .' '.$this->l('cannot be created', self::PAGE)
                            );

                            return false;
                        }

                        $this->googledrive_position = 0;
                        $this->next_step            = $this->step_send['googledrive_resume'];
                    }

                    // If max size for Google Drive has been reach, we send the content or the size of the tar was reach
                    if ($this->distant_tar_content_size >= GoogledriveLib::MAX_FILE_UPLOAD_SIZE
                        || ($this->distant_tar_content_size + self::TAR_END_SIZE) >= $this->tar_files_size[$this->part_number]
                    ) {
                        $end_of_current_part = 0;

                        if (($this->distant_tar_content_size + self::TAR_END_SIZE) >= $this->tar_files_size[$this->part_number]) {
                            $this->distant_tar_content_size += self::TAR_END_SIZE;
                            $this->distant_tar_content .= self::pad('', self::TAR_END_SIZE);
                            $end_of_current_part = 1;
                        }

                        if ($this->distant_tar_content_size > $this->tar_files_size[$this->part_number]) {
                            $this->log(
                                $this->l('The total size should not be less that the size we are now at', self::PAGE)
                                .' ('.$this->distant_tar_content_size.'/'.$this->tar_files_size[$this->part_number]
                            );
                        }

                        // Send content using the current session
                        $position_after_upload = $googledrive_lib->resumeUploadContent(
                            $this->distant_tar_content,
                            $this->distant_tar_content_size,
                            $this->tar_files_size[$this->part_number]
                        );

                        if ($position_after_upload === -1) {
                            $this->log(
                                'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::GOOGLEDRIVE)
                                .' '.$this->l('File', self::PAGE).' '.$tar_name
                                .' '.$this->l('cannot be completed', self::PAGE)
                            );

                            return false;
                        } else {
                            // Calcul what should be the new position
                            $new_position = $this->googledrive_position + $this->distant_tar_content_size;
                            $this->googledrive_position = $position_after_upload;

                            // If Google Drive did not upload everything,
                            // we need to put back what is left in content and content size
                            if ($new_position != $position_after_upload) {
                                $diff = $new_position - $position_after_upload;
                                $end_of_current_part = 0;

                                $this->distant_tar_content_size = $diff;

                                // Use Apparatus::substr instead of Tools::substr
                                // so than it will cut using octets instead of characters
                                $this->distant_tar_content      = Apparatus::substr(
                                    $this->distant_tar_content,
                                    ($this->distant_tar_content_size * -1)
                                );
                            } else {
                                $this->distant_tar_content      = '';
                                $this->distant_tar_content_size = 0;
                            }
                        }

                        if ($end_of_current_part) {
                            // New part, so back to init values
                            $this->googledrive_nb_part++;
                            $this->part_number++;

                            $this->next_step            = $this->step_send['googledrive'];
                            $this->googledrive_position = 0;
                            $this->old_percent          = 0;
                            $this->secondary_next_step  = self::SECONDARY_STEP_TAR_FILE;
                        }
                    } else {
                        //Read data
                        $leftsize -= $blocksize;

                        if ($leftsize < 0) {
                            $blocksize += $leftsize;
                        }

                        $temp_content = '';

                        if (is_resource($file_read)) {
                            $temp_content = $this->createTarContent($file_read, $blocksize);
                        }

                        // Get where we are in the file
                        $this->pos_file_to_tar += $blocksize;

                        // If there is no content because the file size has changed (smaller file) we need to add \0
                        if ($diff_size < 0 && !$temp_content) {
                            //self::TAR_BLOCK_SIZE since createTarContent
                            //return self::TAR_BLOCK_SIZE size block (using self::pad)
                            $temp_content = self::pad('', self::TAR_BLOCK_SIZE);
                            $diff_size += self::TAR_BLOCK_SIZE;
                        } elseif ($temp_content === false) {
                            $this->log(
                                'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::GOOGLEDRIVE)
                                .' '.$this->l('The module was unable to read the file', self::PAGE)
                                .' '.$current_file.', '.$this->l('please check its rights and user owner', self::PAGE)
                            );

                            if (is_resource($file_read)) {
                                fclose($file_read);
                            }

                            return false;
                        }

                        $this->distant_tar_content .= $temp_content;
                        $this->distant_tar_content_size += self::TAR_BLOCK_SIZE;

                        $percent = (($this->distant_tar_content_size + $this->googledrive_position)/$this->tar_files_size[$this->part_number]) * 100;

                        if ($percent >= $this->old_percent + 1) {
                            $this->old_percent = round($percent, 0);

                            if ($this->total_nb_part > 1) {
                                $this->log(
                                    sprintf($this->l('Creating backup on %s account:', self::PAGE), self::GOOGLEDRIVE)
                                    .' '.$this->part_number.'/'.$this->total_nb_part.$this->l(':', self::PAGE)
                                    .' '.(int)$percent.'%'
                                );
                            } else {
                                $this->log(
                                    sprintf($this->l('Creating backup on %s account:', self::PAGE), self::GOOGLEDRIVE)
                                    .' '.(int)$percent.'%'
                                );
                            }
                        }
                    }

                    //refresh
                    $this->refreshBackup(true);
                }

                //Close file
                if (is_resource($file_read)) {
                    fclose($file_read);
                }
            }

            $this->position_file_list_file = ftell($this->handle_file_list_file);

            $this->checkStopScript();

            //Check if tar file size has reach its predicted size
            if (isset($this->tar_files_size[$this->part_number])
                && ($this->googledrive_position + $this->distant_tar_content_size + self::TAR_END_SIZE) >= $this->tar_files_size[$this->part_number]
                && $this->googledrive_position < $this->tar_files_size[$this->part_number]
            ) {
                //The tar file will be too big, we need to close it and use a new one
                $this->distant_tar_content .= self::pad('', self::TAR_END_SIZE);
                $this->distant_tar_content_size +=  self::TAR_END_SIZE;

                if ($this->distant_tar_content_size > $this->tar_files_size[$this->part_number]) {
                    $this->log(
                        $this->l('The total size should not be less that the size we are now at', self::PAGE)
                        .' ('.$this->distant_tar_content_size.'/'.$this->tar_files_size[$this->part_number]
                    );
                }

                // Finish sending current tar
                $position_after_upload = $googledrive_lib->resumeUploadContent(
                    $this->distant_tar_content,
                    $this->distant_tar_content_size,
                    $this->tar_files_size[$this->part_number]
                );

                if ($position_after_upload === -1) {
                    $this->log(
                        'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::GOOGLEDRIVE)
                        .' '.$this->l('File', self::PAGE).' '.$tar_name
                        .' '.$this->l('cannot be finished', self::PAGE)
                    );

                    return false;
                }

                // New part, so back to init values
                $this->googledrive_nb_part++;
                $this->part_number++;

                $this->next_step                = $this->step_send['googledrive'];
                $this->googledrive_position     = 0;
                $this->old_percent              = 0;
                $this->secondary_next_step      = self::SECONDARY_STEP_TAR_FILE;
                $this->distant_tar_content      = '';
                $this->distant_tar_content_size = 0;

                //refresh
                $this->refreshBackup(true);
            }

            $this->secondary_next_step = self::SECONDARY_STEP_TAR_FILE;
        }

        // Check that there is no missing file
        if ($this->files_done < $this->total_files) {
            $this->log(
                'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::GOOGLEDRIVE)
                .$this->l('Be careful! Not all of your files have been added to the backup. Number of files backuped:', self::PAGE)
                .' '.$this->files_done.'/'.$this->total_files
            );

            return false;
        }

        $this->files_done = 0;
        $this->pos_file_to_tar = 0;

        $this->next_step = $this->step_send['googledrive_resume'];

        if ($this->config->send_restore) {
            if (Tools::substr($googledrive->directory_path, -1) != '/') {
                $googledrive->directory_path .= '/';
            }

            $file_destination = $googledrive->directory_path.self::NEW_RESTORE_NAME;
            $id_file = $googledrive_lib->checkExists(self::NEW_RESTORE_NAME, $googledrive_dir);

            if ($id_file !== false) {
                $this->log($this->l('Delete previous version of the file:', self::PAGE).' '.$file_destination);

                if ($googledrive_lib->deleteFile($id_file) === false) {
                    $this->log($this->l('Error while deleting the file:', self::PAGE).' '.$file_destination);
                }
            }

            $this->log(
                sprintf($this->l('Creating restore file on %s account...', self::PAGE), self::GOOGLEDRIVE)
            );

            // Upload the file
            if ($googledrive_lib->uploadFile($this->restore_file, $googledrive_dir, self::NEW_RESTORE_NAME) === false) {
                return false;
            }
        }

        return true;
    }

    /**
     * Send a file on a hubiC account
     *
     * @return  boolean     The success or failure of the operation
     */
    protected function sendFileToHubic()
    {
        $hubic  = new Hubic($this->hubic_account_id);

        if ($this->next_step == $this->step_send['hubic']
            && (!isset($this->hubic_nb_part) || $this->hubic_nb_part == 1)
        ) {
            $this->log(sprintf($this->l('Connect to your %s account...', self::PAGE), self::HUBIC));
        }

        $hubic_lib = $this->connectToHubic($this->hubic_account_id);

        if (!$hubic_lib->testConnection()) {
            $this->log(
                'WAR'.sprintf($this->l('Connection to your %s account impossible', self::PAGE), self::HUBIC)
            );
            return false;
        }

        if ($this->next_step == $this->step_send['hubic']
            && (!isset($this->hubic_nb_part) || $this->hubic_nb_part == 1)
        ) {
            // hubiC dir should end with a "/" if not empty
            if (Tools::substr($hubic->directory, -1) != '/' && $hubic->directory != '') {
                $this->hubic_dir = $hubic->directory.'/';
            }

            // If file already on hubiC
            foreach ($this->part_list as $part) {
                $file_destination   = $this->hubic_dir.basename($part);

                $this->log(
                    $this->l('Check if there is a previous version of the file:', self::PAGE).' '.$file_destination
                );
                if ($hubic_lib->checkExists($file_destination)) {
                    $this->log($this->l('Delete previous version of the file:', self::PAGE).' '.$file_destination);
                    if ($hubic_lib->deleteFile($file_destination) === false) {
                        $this->log($this->l('Error while deleting the file:', self::PAGE).' '.$part);
                    }
                }
            }

            // Delete old backup
            if (!$this->deleteHubicOldBackup($hubic_lib)) {
                $this->log(
                    'WAR'.sprintf($this->l('Sending backup to %s account:', self::PAGE), self::HUBIC).' '
                    .$this->l('Error while deleting old backup', self::PAGE)
                );
                return false;
            }

            // Check available space
            $available_space = $hubic_lib->fetchQuota();

            if ($this->config->send_restore) {
                $total_file_size = $this->getFileSize($this->restore_file);
                $available_space -= $total_file_size;
            }

            if ($available_space <= $this->total_size) {
                $this->log(
                    'WAR'.sprintf($this->l('Sending backup to %s account:', self::PAGE), self::HUBIC).' '
                    .$this->l('Not enough space available', self::PAGE)
                );
                return false;
            }

            $this->log(sprintf($this->l('Sending backup to %s account...', self::PAGE), self::HUBIC));
            $this->hubic_nb_part    = 1;
            $this->hubic_nb_chunk   = 1;
            $this->hubic_position   = 0;
        }

        $nb_part = 1;
        // Upload the file
        foreach ($this->part_list as $part) {
            if ($nb_part == $this->hubic_nb_part) {
                if ($this->next_step == $this->step_send['hubic'] || $this->hubic_nb_chunk == 1) {
                    $this->next_step = $this->step_send['hubic_resume'];

                    // Upload the file
                    $create_file = $hubic_lib->createFile(
                        $part,
                        $this->hubic_dir.basename($part),
                        $this->hubic_nb_part,
                        $this->total_nb_part
                    );

                    if ($create_file === false) {
                        return false;
                    }
                } else {
                    // Resume upload of the file
                    $resume_create_file = $hubic_lib->resumeCreateFile(
                        $part,
                        $this->hubic_dir.basename($part),
                        $this->hubic_nb_part,
                        $this->total_nb_part,
                        $this->hubic_position,
                        $this->hubic_nb_chunk
                    );

                    if ($resume_create_file === false) {
                        return false;
                    }
                }
                $this->hubic_nb_part++;
                // New part, so back to init values
                $this->next_step = $this->step_send['hubic'];
                $this->hubic_position   = 0;
                $this->hubic_nb_chunk   = 1;
            }

            $nb_part++;
        }

        $this->next_step = $this->step_send['hubic_resume'];

        if ($this->config->send_restore) {
            $this->log(
                sprintf($this->l('Sending restore file to %s account...', self::PAGE), self::HUBIC)
            );

            // Upload the file
            if ($hubic_lib->createFile($this->restore_file, $this->hubic_dir.basename($this->restore_file)) === false) {
                return false;
            }
        }

        return true;
    }

    /**
     * Create a tar file on a hubiC account
     *
     * @return  boolean     The success or failure of the operation
     */
    protected function createTarOnHubic()
    {
        $hubic  = new Hubic($this->hubic_account_id);

        $this->checkStopScript();

        if ($this->next_step == $this->step_send['hubic']
            && $this->secondary_next_step  == self::SECONDARY_STEP_TAR_FILE
            && (!isset($this->hubic_nb_part) || $this->hubic_nb_part == 1)
        ) {
            $this->log(sprintf($this->l('Connect to your %s account...', self::PAGE), self::HUBIC));

            $this->secondary_next_step      = self::SECONDARY_STEP_TAR_FILE;
            $this->files_done               = 0;
            $this->position_file_list_file  = 0;
            $this->part_number              = 1;
            $this->old_percent              = 0;
            $tar_name                       = basename($this->part_list[$this->part_number - 1]);

            if (count($this->part_list) != count($this->tar_files_size)) {
                $this->log(
                    'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::HUBIC)
                    .' '.$this->l('The backup cannot be created', self::PAGE)
                );

                return false;
            }

            if (!isset($this->tar_files_size[$this->part_number]) || !$this->tar_files_size[$this->part_number]) {
                $this->log(
                    'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::HUBIC).' '
                    .sprintf($this->l('The calculated size of the file %s is not valid', self::PAGE), $tar_name)
                );

                return false;
            }
        }

        $this->checkStopScript();

        $hubic_lib = $this->connectToHubic($this->hubic_account_id);

        if (!$hubic_lib->testConnection()) {
            $this->log(
                'WAR'.sprintf($this->l('Connection to your %s account impossible', self::PAGE), self::HUBIC)
            );
            return false;
        }

        $this->checkStopScript();

        if ($this->next_step == $this->step_send['hubic']
            && $this->secondary_next_step  == self::SECONDARY_STEP_TAR_FILE
            && (!isset($this->hubic_nb_part) || $this->hubic_nb_part == 1)
        ) {
            // hubiC dir should end with a "/" if not empty
            if (Tools::substr($hubic->directory, -1) != '/' && $hubic->directory != '') {
                $this->hubic_dir = $hubic->directory.'/';
            }

            // Delete old backup
            if (!$this->deleteHubicOldBackup($hubic_lib)) {
                $this->log(
                    'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::HUBIC).' '
                    .$this->l('Error while deleting old backup', self::PAGE)
                );
                return false;
            }

            $this->checkStopScript();

            // Check available space
            $available_space = $hubic_lib->fetchQuota();

            $this->checkStopScript();

            if ($this->config->send_restore) {
                $total_file_size = $this->getFileSize($this->restore_file);

                $this->checkStopScript();

                $available_space -= $total_file_size;
            }

            if ($available_space <= $this->total_size) {
                $this->log(
                    'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::HUBIC).' '
                    .$this->l('Not enough space available', self::PAGE)
                );
                return false;
            }

            $this->log(sprintf($this->l('Creating backup on %s account...', self::PAGE), self::HUBIC));

            $this->hubic_nb_part            = 1;
            $this->hubic_nb_chunk           = 1;
            $this->hubic_position           = 0;
            $this->distant_tar_content      = '';
            $this->distant_tar_content_size = 0;
        }

        $this->checkStopScript();

        if ($this->getFileSize($this->file_list_file) <= 0) {
            $this->log(
                'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::HUBIC)
                .' '.$this->l('No file to backup', self::PAGE)
            );
            return false;
        }

        $this->checkStopScript();

        if (!is_resource($this->handle_file_list_file)) {
            $this->log(
                'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::HUBIC)
                .' '.$this->l('Error while getting the files to backup', self::PAGE)
            );
            return false;
        }

        $this->handle_file_list_file = $this->goToPositionInFile(
            $this->handle_file_list_file,
            $this->position_file_list_file,
            false
        );

        $this->checkStopScript();

        while (!feof($this->handle_file_list_file)) {
            $this->checkStopScript();

            $line = rtrim(fgets($this->handle_file_list_file));

            if (!$line) {
                continue;
            }

            $pos_cut = strrpos($line, ':');

            if ($pos_cut === false) {
                continue;
            }

            $current_file = self::binaryToString(Tools::substr($line, 0, $pos_cut));

            if (!$current_file) {
                continue;
            }

            $this->checkStopScript();

            $found_size     = Tools::substr($line, ($pos_cut+1));
            $tar_name       = basename($this->part_list[$this->part_number - 1]);
            $path_in_tar    = '';

            //Normalize path
            $current_normalized_file = $this->normalizePath($current_file);

            //Find relative filename
            if (!$path_in_tar) {
                $filename = ltrim(self::getPart($current_normalized_file, $this->base_length), '/');
            } else {
                $filename = ltrim($path_in_tar, '/');
            }

            //File information
            $info       = $this->tarFileInfo($current_file);
            $diff_size  = 0;

            if ($info['size'] != $found_size) {
                if ($this->secondary_next_step == self::SECONDARY_STEP_TAR_FILE) {
                    $this->log(
                        'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::HUBIC)
                        .' '.sprintf(
                            $this->l('The file %s has changed of size from %s to %s', self::PAGE),
                            $current_file,
                            $this->readableSize($found_size).' ('.$found_size.')',
                            $this->readableSize($info['size']).' ('.$info['size'].')'
                        )
                    );
                }

                $diff_size      = $info['size'] - $found_size;
                $info['size']   = $found_size;
            }

            if ($current_file == $this->dump_file && $this->config->type_backup == $this->type_backup_complete) {
                $path_module_backup = 'modules/'.$this->name.'/'.self::BACKUP_FOLDER.'/';
                $path_in_tar        = str_replace($this->config_backup_dir, $path_module_backup, $current_file);
            }

            $this->checkStopScript();

            // Create file header
            if ($this->secondary_next_step == self::SECONDARY_STEP_TAR_FILE) {
                // Start to add a new file to the tar
                $header = $this->createTarHeader($filename, $info);

                $this->distant_tar_content_size += self::getLength($header);
                $this->distant_tar_content .= $header;

                $this->files_done++;

                $this->pos_file_to_tar      = 0;
                $this->secondary_next_step  = self::SECONDARY_STEP_TAR_FILE_CONTINUE;
            }

            if ($info['size'] > 0) {
                //Open the file
                if (($file_read = @fopen($current_file, 'rb')) === false) {
                    // If it is the first opening of the file
                    if ($this->pos_file_to_tar == 0) {
                        $this->log(
                            'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::HUBIC)
                            .' '.$this->l('File', self::PAGE).' '.$current_file.' '
                            .$this->l('will be empty because the module can not open it, please check its rights and user owner', self::PAGE)
                        );
                    }
                }

                $this->checkStopScript();

                if (is_resource($file_read) && $this->secondary_next_step == self::SECONDARY_STEP_TAR_FILE_CONTINUE) {
                    $file_read = $this->goToPositionInFile($file_read, $this->pos_file_to_tar);

                    if ($file_read === false) {
                        return false;
                    }
                }

                //Data of the file
                $leftsize   = $info['size'] - $this->pos_file_to_tar;
                $blocksize  = self::TAR_BLOCK_SIZE;

                while ($leftsize > 0) {
                    $this->checkStopScript();

                    if ($this->next_step == $this->step_send['hubic']) {
                        // New tar
                        $this->hubic_position   = 0;
                        $this->hubic_nb_chunk   = 1;
                        $this->next_step        = $this->step_send['hubic_resume'];
                    }

                    // If max size for hubiC has been reach, we send the content or the size of the tar was reach
                    if ($this->distant_tar_content_size >= HubicLib::MAX_FILE_UPLOAD_SIZE
                        || ($this->distant_tar_content_size + self::TAR_END_SIZE) >= $this->tar_files_size[$this->part_number]
                    ) {
                        $end_of_current_part = 0;

                        if (($this->distant_tar_content_size + self::TAR_END_SIZE) >= $this->tar_files_size[$this->part_number]) {
                            $this->distant_tar_content_size +=  self::TAR_END_SIZE;
                            $this->distant_tar_content .= self::pad('', self::TAR_END_SIZE);
                            $end_of_current_part = 1;
                        }

                        if ($this->distant_tar_content_size > $this->tar_files_size[$this->part_number]) {
                            $this->log(
                                $this->l('The total size should not be less that the size we are now at', self::PAGE)
                                .' ('.$this->distant_tar_content_size.'/'.$this->tar_files_size[$this->part_number]
                            );
                        }

                        // Send content using the current session
                        $upload_content = $hubic_lib->uploadContent(
                            $this->distant_tar_content,
                            $this->distant_tar_content_size,
                            $this->tar_files_size[$this->part_number],
                            $this->hubic_dir.$tar_name,
                            $this->hubic_position,
                            $this->hubic_nb_chunk
                        );

                        if ($upload_content === false) {
                            $this->log(
                                'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::HUBIC)
                                .' '.$this->l('File', self::PAGE).' '.$tar_name
                                .' '.$this->l('cannot be completed', self::PAGE)
                            );

                            return false;
                        } else {
                            $this->hubic_position += $this->distant_tar_content_size;

                            $this->distant_tar_content      = '';
                            $this->distant_tar_content_size = 0;
                        }

                        if ($end_of_current_part) {
                            // New part, so back to init values
                            $this->hubic_nb_part++;
                            $this->part_number++;

                            $this->next_step            = $this->step_send['hubic'];
                            $this->hubic_position       = 0;
                            $this->old_percent          = 0;
                            $this->secondary_next_step  = self::SECONDARY_STEP_TAR_FILE;
                        }
                    } else {
                        //Read data
                        $leftsize -= $blocksize;

                        if ($leftsize < 0) {
                            $blocksize += $leftsize;
                        }

                        $temp_content = '';

                        if (is_resource($file_read)) {
                            $temp_content = $this->createTarContent($file_read, $blocksize);
                        }

                        // Get where we are in the file
                        $this->pos_file_to_tar += $blocksize;

                        // If there is no content because the file size has changed (smaller file) we need to add \0
                        if ($diff_size < 0 && !$temp_content) {
                            //self::TAR_BLOCK_SIZE since createTarContent
                            //return self::TAR_BLOCK_SIZE size block (using self::pad)
                            $temp_content = self::pad('', self::TAR_BLOCK_SIZE);
                            $diff_size += self::TAR_BLOCK_SIZE;
                        } elseif ($temp_content === false) {
                            $this->log(
                                'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::HUBIC)
                                .' '.$this->l('The module was unable to read the file', self::PAGE)
                                .' '.$current_file.', '.$this->l('please check its rights and user owner', self::PAGE)
                            );

                            if (is_resource($file_read)) {
                                fclose($file_read);
                            }

                            return false;
                        }

                        $this->distant_tar_content .= $temp_content;
                        $this->distant_tar_content_size += self::TAR_BLOCK_SIZE;

                        $percent = (($this->distant_tar_content_size + $this->hubic_position)/$this->tar_files_size[$this->part_number]) * 100;

                        if ($percent >= $this->old_percent + 1) {
                            $this->old_percent = round($percent, 0);

                            if ($this->total_nb_part > 1) {
                                $this->log(
                                    sprintf($this->l('Creating backup on %s account:', self::PAGE), self::HUBIC)
                                    .' '.$this->part_number.'/'.$this->total_nb_part.$this->l(':', self::PAGE)
                                    .' '.(int)$percent.'%'
                                );
                            } else {
                                $this->log(
                                    sprintf($this->l('Creating backup on %s account:', self::PAGE), self::HUBIC)
                                    .' '.(int)$percent.'%'
                                );
                            }
                        }
                    }

                    //refresh
                    $this->refreshBackup(true);
                }

                //Close file
                if (is_resource($file_read)) {
                    fclose($file_read);
                }
            }

            $this->position_file_list_file = ftell($this->handle_file_list_file);

            $this->checkStopScript();

            //Check if tar file size has reach its predicted size
            if (isset($this->tar_files_size[$this->part_number])
                && ($this->hubic_position + $this->distant_tar_content_size + self::TAR_END_SIZE) >= $this->tar_files_size[$this->part_number]
                && $this->hubic_position < $this->tar_files_size[$this->part_number]
            ) {
                //The tar file will be too big, we need to close it and use a new one
                $this->distant_tar_content .= self::pad('', self::TAR_END_SIZE);
                $this->distant_tar_content_size +=  self::TAR_END_SIZE;

                if ($this->distant_tar_content_size > $this->tar_files_size[$this->part_number]) {
                    $this->log(
                        $this->l('The total size should not be less that the size we are now at', self::PAGE)
                        .' ('.$this->distant_tar_content_size.'/'.$this->tar_files_size[$this->part_number]
                    );
                }

                // Finish sending current tar
                $upload_content = $hubic_lib->uploadContent(
                    $this->distant_tar_content,
                    $this->distant_tar_content_size,
                    $this->tar_files_size[$this->part_number],
                    $this->hubic_dir.$tar_name,
                    $this->hubic_position,
                    $this->hubic_nb_chunk
                );

                if ($upload_content === false) {
                    $this->log(
                        'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::HUBIC)
                        .' '.$this->l('File', self::PAGE).' '.$tar_name
                        .' '.$this->l('cannot be finished', self::PAGE)
                    );

                    return false;
                }

                // New part, so back to init values
                $this->hubic_nb_part++;
                $this->part_number++;

                $this->next_step                = $this->step_send['hubic'];
                $this->hubic_position           = 0;
                $this->old_percent              = 0;
                $this->secondary_next_step      = self::SECONDARY_STEP_TAR_FILE;
                $this->distant_tar_content      = '';
                $this->distant_tar_content_size = 0;

                //refresh
                $this->refreshBackup(true);
            }

            $this->secondary_next_step = self::SECONDARY_STEP_TAR_FILE;
        }

        // Check that there is no missing file
        if ($this->files_done < $this->total_files) {
            $this->log(
                'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::HUBIC)
                .$this->l('Be careful! Not all of your files have been added to the backup. Number of files backuped:', self::PAGE)
                .' '.$this->files_done.'/'.$this->total_files
            );

            return false;
        }

        $this->files_done = 0;
        $this->pos_file_to_tar = 0;

        $this->next_step = $this->step_send['hubic_resume'];

        if ($this->config->send_restore) {
            $this->log(
                sprintf($this->l('Creating restore file on %s account...', self::PAGE), self::HUBIC)
            );

            // Upload the file
            if ($hubic_lib->createFile($this->restore_file, $this->hubic_dir.basename($this->restore_file)) === false) {
                return false;
            }
        }

        return true;
    }

    /**
     * Send a file on a AWS account
     *
     * @return  boolean     The success or failure of the operation
     */
    protected function sendFileToAws()
    {
        $aws                = new Aws($this->aws_account_id);
        $aws_directory_key  = $aws->directory_key;
        $prefix             = $this->correctFileName($this->getConfig('PS_SHOP_NAME'));

        if (false === $aws->bucket || '' == $aws->bucket) {
            $this->log('WAR'.$this->l('No bucket configured', self::PAGE));
            return false;
        }

        if (false === $aws->storage_class
            || '' == $aws->storage_class
            || !in_array($aws->storage_class, array(
                AwsLib::STORAGE_CLASS_STANDARD,
                AwsLib::STORAGE_CLASS_REDUCED_REDUNDANCY,
                AwsLib::STORAGE_CLASS_STANDARD_IA,
                AwsLib::STORAGE_CLASS_ONEZONE_IA,
                AwsLib::STORAGE_CLASS_INTELLIGENT_TIERING,
                AwsLib::STORAGE_CLASS_GLACIER,
                AwsLib::STORAGE_CLASS_DEEP_ARCHIVE,
            ))
        ) {
            $aws->storage_class = AwsLib::STORAGE_CLASS_STANDARD;
        }

        if ($this->next_step == $this->step_send['aws'] && (!isset($this->aws_nb_part) || $this->aws_nb_part == 1)) {
            $this->log(sprintf($this->l('Connect to your %s account...', self::PAGE), self::AWS));
        }

        $aws_lib = $this->connectToAws(
            $this->decrypt($aws->access_key_id),
            $this->decrypt($aws->secret_access_key),
            $aws->region,
            $aws->bucket
        );

        if (!$aws_lib->testConnection()) {
            $this->log(
                'WAR'.sprintf($this->l('Connection to your %s account impossible', self::PAGE), self::AWS)
            );
            return false;
        }

        if ($this->next_step == $this->step_send['aws'] && (!isset($this->aws_nb_part) || $this->aws_nb_part == 1)) {
            if (Tools::substr($aws_directory_key, -1) != '/') {
                $aws_directory_key .= '/';
            }

            // Check if directory exists
            if ($aws->directory_key != $aws->bucket) {
                $dir_exists = $aws_lib->checkDirectoryExists($aws->directory_key);

                if ($dir_exists === false) {
                    $this->log(
                        'WAR'.$this->l('Error while creating your file: directory unknow', self::PAGE)
                        .' ('.$aws->directory_path.')'
                    );
                    return false;
                }
            }

            // If file already on Aws
            foreach ($this->part_list as $part) {
                $file_destination = $aws_directory_key.basename($part);

                $this->log(
                    $this->l('Check if there is a previous version of the file:', self::PAGE).' '.$file_destination
                );
                $file_exists = $aws_lib->checkFileExists(basename($part), $aws_directory_key);

                if ($file_exists !== false) {
                    $this->log($this->l('Delete previous version of the file:', self::PAGE).' '.$file_destination);
                    if ($aws_lib->deleteFile($file_destination) === false) {
                        $this->log($this->l('Error while deleting the file:', self::PAGE).' '.$file_destination);
                    }
                }
            }

            // Delete old backup
            if (!$this->deleteAwsOldBackup($aws_lib)) {
                $this->log(
                    'WAR'.sprintf($this->l('Sending backup to %s account:', self::PAGE), self::AWS).' '
                    .$this->l('Error while deleting old backup', self::PAGE)
                );
                return false;
            }

            $this->log(sprintf($this->l('Sending backup to %s account...', self::PAGE), self::AWS));
            $this->aws_nb_part      = 1;
            $this->aws_upload_part  = 1;
            $this->aws_position     = 0;
            $this->aws_etag         = array();
        } else {
            if (!$this->aws_upload_id || $this->aws_upload_id == '') {
                $this->log(
                    'WAR'.sprintf($this->l('Sending backup to %s account:', self::PAGE), self::AWS).' '
                    .$this->l('Invalid upload ID', self::PAGE)
                );
                return false;
            }
        }

        $nb_part = 1;

        // Upload the file
        foreach ($this->part_list as $part) {
            if ($nb_part == $this->aws_nb_part) {
                $file_name = basename($part);
                $file_path = $part;

                if ($this->next_step == $this->step_send['aws']) {
                    $this->next_step = $this->step_send['aws_resume'];

                    // Upload the file
                    $upload_file = $aws_lib->uploadFile(
                        $file_name,
                        $file_path,
                        $aws->directory_key,
                        $aws->storage_class,
                        $this->aws_upload_part,
                        $this->aws_nb_part,
                        $this->total_nb_part,
                        $prefix
                    );

                    if ($upload_file === false) {
                        $this->log(
                            'WAR'.sprintf($this->l('Sending backup to %s account:', self::PAGE), self::AWS).' '
                            .$this->l('Upload failed', self::PAGE)
                        );
                        return false;
                    }
                } else {
                    // Resume upload of the file
                    $resume_upload_file = $aws_lib->resumeUploadFile(
                        $this->aws_upload_id,
                        $file_name,
                        $aws->directory_key,
                        $file_path,
                        $this->aws_upload_part,
                        $this->aws_nb_part,
                        $this->total_nb_part,
                        $this->aws_position
                    );

                    if ($resume_upload_file === false) {
                        $this->log(
                            'WAR'.sprintf($this->l('Sending backup to %s account:', self::PAGE), self::AWS)
                            .' '.$this->l('Upload failed', self::PAGE)
                        );
                        return false;
                    }
                }
                $this->aws_nb_part++;
                // New part, so back to init values
                $this->next_step        = $this->step_send['aws'];
                $this->aws_upload_part  = 1;
                $this->aws_position     = 0;
                $this->aws_etag         = array();
            }
            $nb_part++;
        }

        $this->next_step = $this->step_send['aws_resume'];

        if ($this->config->send_restore) {
            $this->aws_nb_part      = 1;
            $nb_part_total          = 1;
            $this->aws_upload_part  = 1;
            $this->aws_position     = 0;
            $this->aws_etag         = array();

            $this->log(
                sprintf($this->l('Sending restore file to %s account...', self::PAGE), self::AWS)
            );

            // Upload the file
            $upload_file = $aws_lib->uploadFile(
                self::NEW_RESTORE_NAME,
                $this->restore_file,
                $aws->directory_key,
                $aws->storage_class,
                $this->aws_upload_part,
                $this->aws_nb_part,
                $nb_part_total,
                self::NEW_RESTORE_NAME
            );

            if ($upload_file === false) {
                return false;
            }
        }

        return true;
    }

    /**
     * Create a tar file on a AWS account
     *
     * @return  boolean     The success or failure of the operation
     */
    protected function createTarOnAws()
    {
        $aws                = new Aws($this->aws_account_id);
        $aws_directory_key  = $aws->directory_key;
        $prefix             = $this->correctFileName($this->getConfig('PS_SHOP_NAME'));

        if (false === $aws->bucket || '' == $aws->bucket) {
            $this->log('WAR'.$this->l('No bucket configured', self::PAGE));
            return false;
        }

        if (false === $aws->storage_class
            || '' == $aws->storage_class
            || !in_array($aws->storage_class, array(
                AwsLib::STORAGE_CLASS_STANDARD,
                AwsLib::STORAGE_CLASS_REDUCED_REDUNDANCY,
                AwsLib::STORAGE_CLASS_STANDARD_IA,
                AwsLib::STORAGE_CLASS_ONEZONE_IA,
                AwsLib::STORAGE_CLASS_INTELLIGENT_TIERING,
                AwsLib::STORAGE_CLASS_GLACIER,
                AwsLib::STORAGE_CLASS_DEEP_ARCHIVE,
            ))
        ) {
            $aws->storage_class = AwsLib::STORAGE_CLASS_STANDARD;
        }

        $this->checkStopScript();

        if ($this->next_step == $this->step_send['aws']
            && $this->secondary_next_step  == self::SECONDARY_STEP_TAR_FILE
            && (!isset($this->aws_nb_part) || $this->aws_nb_part == 1)
        ) {
            $this->log(sprintf($this->l('Connect to your %s account...', self::PAGE), self::AWS));

            $this->secondary_next_step      = self::SECONDARY_STEP_TAR_FILE;
            $this->files_done               = 0;
            $this->position_file_list_file  = 0;
            $this->part_number              = 1;
            $this->old_percent              = 0;
            $tar_name                       = basename($this->part_list[$this->part_number - 1]);

            if (count($this->part_list) != count($this->tar_files_size)) {
                $this->log(
                    'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::AWS)
                    .' '.$this->l('The backup cannot be created', self::PAGE)
                );

                return false;
            }

            if (!isset($this->tar_files_size[$this->part_number]) || !$this->tar_files_size[$this->part_number]) {
                $this->log(
                    'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::AWS).' '
                    .sprintf($this->l('The calculated size of the file %s is not valid', self::PAGE), $tar_name)
                );

                return false;
            }
        }

        $this->checkStopScript();

        $aws_lib = $this->connectToAws(
            $this->decrypt($aws->access_key_id),
            $this->decrypt($aws->secret_access_key),
            $aws->region,
            $aws->bucket
        );

        if (!$aws_lib->testConnection()) {
            $this->log(
                'WAR'.sprintf($this->l('Connection to your %s account impossible', self::PAGE), self::AWS)
            );
            return false;
        }

        $this->checkStopScript();

        if ($this->next_step == $this->step_send['aws']
            && $this->secondary_next_step  == self::SECONDARY_STEP_TAR_FILE
            && (!isset($this->aws_nb_part) || $this->aws_nb_part == 1)
        ) {
            if (Tools::substr($aws_directory_key, -1) != '/') {
                $aws_directory_key .= '/';
            }

            // Check if directory exists
            if ($aws->directory_key != $aws->bucket) {
                $dir_exists = $aws_lib->checkDirectoryExists($aws->directory_key);

                if ($dir_exists === false) {
                    $this->log(
                        'WAR'.$this->l('Error while creating your file: directory unknow', self::PAGE)
                        .' ('.$aws->directory_path.')'
                    );
                    return false;
                }
            }

            // Delete old backup
            if (!$this->deleteAwsOldBackup($aws_lib)) {
                $this->log(
                    'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::AWS).' '
                    .$this->l('Error while deleting old backup', self::PAGE)
                );
                return false;
            }

            $this->checkStopScript();

            $this->log(sprintf($this->l('Creating backup on %s account...', self::PAGE), self::AWS));

            $this->aws_nb_part              = 1;
            $this->aws_position             = 0;
            $this->distant_tar_content      = '';
            $this->distant_tar_content_size = 0;
            $this->aws_upload_part          = 1;
            $this->aws_etag                 = array();
        } elseif ($this->next_step == $this->step_send['aws_resume']) {
            if (!$this->aws_upload_id || $this->aws_upload_id == '') {
                $this->log(
                    'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::AWS).' '
                    .$this->l('Invalid upload ID', self::PAGE)
                );
                return false;
            }
        }

        $this->checkStopScript();

        if ($this->getFileSize($this->file_list_file) <= 0) {
            $this->log(
                'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::AWS)
                .' '.$this->l('No file to backup', self::PAGE)
            );
            return false;
        }

        $this->checkStopScript();

        if (!is_resource($this->handle_file_list_file)) {
            $this->log(
                'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::AWS)
                .' '.$this->l('Error while getting the files to backup', self::PAGE)
            );
            return false;
        }

        $this->handle_file_list_file = $this->goToPositionInFile(
            $this->handle_file_list_file,
            $this->position_file_list_file,
            false
        );

        $this->checkStopScript();

        while (!feof($this->handle_file_list_file)) {
            $this->checkStopScript();

            $line = rtrim(fgets($this->handle_file_list_file));

            if (!$line) {
                continue;
            }

            $pos_cut = strrpos($line, ':');

            if ($pos_cut === false) {
                continue;
            }

            $current_file = self::binaryToString(Tools::substr($line, 0, $pos_cut));

            if (!$current_file) {
                continue;
            }

            $this->checkStopScript();

            $found_size     = Tools::substr($line, ($pos_cut+1));
            $tar_name       = basename($this->part_list[$this->part_number - 1]);
            $path_in_tar    = '';

            //Normalize path
            $current_normalized_file = $this->normalizePath($current_file);

            //Find relative filename
            if (!$path_in_tar) {
                $filename = ltrim(self::getPart($current_normalized_file, $this->base_length), '/');
            } else {
                $filename = ltrim($path_in_tar, '/');
            }

            //File information
            $info = $this->tarFileInfo($current_file);
            $diff_size  = 0;

            if ($info['size'] != $found_size) {
                if ($this->secondary_next_step == self::SECONDARY_STEP_TAR_FILE) {
                    $this->log(
                        'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::AWS)
                        .' '.sprintf(
                            $this->l('The file %s has changed of size from %s to %s', self::PAGE),
                            $current_file,
                            $this->readableSize($found_size).' ('.$found_size.')',
                            $this->readableSize($info['size']).' ('.$info['size'].')'
                        )
                    );
                }

                $diff_size      = $info['size'] - $found_size;
                $info['size']   = $found_size;
            }

            if ($current_file == $this->dump_file && $this->config->type_backup == $this->type_backup_complete) {
                $path_module_backup = 'modules/'.$this->name.'/'.self::BACKUP_FOLDER.'/';
                $path_in_tar        = str_replace($this->config_backup_dir, $path_module_backup, $current_file);
            }

            $this->checkStopScript();

            // Create file header
            if ($this->secondary_next_step == self::SECONDARY_STEP_TAR_FILE) {
                // Start to add a new file to the tar
                $header = $this->createTarHeader($filename, $info);

                $this->distant_tar_content_size += self::getLength($header);
                $this->distant_tar_content .= $header;

                $this->files_done++;

                $this->pos_file_to_tar      = 0;
                $this->secondary_next_step  = self::SECONDARY_STEP_TAR_FILE_CONTINUE;
            }

            if ($info['size'] > 0) {
                //Open the file
                if (($file_read = @fopen($current_file, 'rb')) === false) {
                    // If it is the first opening of the file
                    if ($this->pos_file_to_tar == 0) {
                        $this->log(
                            'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::AWS)
                            .' '.$this->l('File', self::PAGE).' '.$current_file.' '
                            .$this->l('will be empty because the module can not open it, please check its rights and user owner', self::PAGE)
                        );
                    }
                }

                $this->checkStopScript();

                if (is_resource($file_read) && $this->secondary_next_step == self::SECONDARY_STEP_TAR_FILE_CONTINUE) {
                    $file_read = $this->goToPositionInFile($file_read, $this->pos_file_to_tar);

                    if ($file_read === false) {
                        return false;
                    }
                }

                //Data of the file
                $leftsize   = $info['size'] - $this->pos_file_to_tar;
                $blocksize  = self::TAR_BLOCK_SIZE;

                while ($leftsize > 0) {
                    $this->checkStopScript();

                    if ($this->next_step == $this->step_send['aws']) {
                        // Create new resumable session
                        $create_session = $aws_lib->createSession($tar_name, $aws->directory_key, $aws->storage_class, $prefix);

                        if ($create_session === false) {
                            $this->log(
                                'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::AWS)
                                .' '.$this->l('File', self::PAGE).' '.$tar_name
                                .' '.$this->l('cannot be created', self::PAGE)
                            );

                            return false;
                        }

                        $this->aws_position = 0;
                        $this->next_step    = $this->step_send['aws_resume'];
                    }

                    // If max size for AWS has been reach, we send the content or the size of the tar was reach
                    if ($this->distant_tar_content_size >= AwsLib::MAX_FILE_UPLOAD_SIZE
                        || ($this->distant_tar_content_size + self::TAR_END_SIZE) >= $this->tar_files_size[$this->part_number]
                    ) {
                        $end_of_current_part = 0;

                        if (($this->distant_tar_content_size + self::TAR_END_SIZE) >= $this->tar_files_size[$this->part_number]) {
                            $this->distant_tar_content_size +=  self::TAR_END_SIZE;
                            $this->distant_tar_content .= self::pad('', self::TAR_END_SIZE);
                            $end_of_current_part = 1;
                        }

                        if ($this->distant_tar_content_size > $this->tar_files_size[$this->part_number]) {
                            $this->log(
                                $this->l('The total size should not be less that the size we are now at', self::PAGE)
                                .' ('.$this->distant_tar_content_size.'/'.$this->tar_files_size[$this->part_number]
                            );
                        }

                        // Send content using the current session
                        $resume_upload_content = $aws_lib->resumeUploadContent(
                            $this->distant_tar_content,
                            $this->distant_tar_content_size,
                            $this->tar_files_size[$this->part_number],
                            $tar_name,
                            $aws->directory_key,
                            $this->aws_upload_part
                        );

                        if ($resume_upload_content === false) {
                            $this->log(
                                'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::AWS)
                                .' '.$this->l('File', self::PAGE).' '.$tar_name
                                .' '.$this->l('cannot be completed', self::PAGE)
                            );

                            return false;
                        } else {
                            $this->aws_position += $this->distant_tar_content_size;

                            $this->distant_tar_content      = '';
                            $this->distant_tar_content_size = 0;
                        }

                        if ($end_of_current_part) {
                            // New part, so back to init values
                            $this->aws_nb_part++;
                            $this->part_number++;

                            $this->next_step            = $this->step_send['aws'];
                            $this->aws_position         = 0;
                            $this->old_percent          = 0;
                            $this->secondary_next_step  = self::SECONDARY_STEP_TAR_FILE;
                            $this->aws_upload_part      = 1;
                            $this->aws_etag             = array();
                        }
                    } else {
                        //Read data
                        $leftsize -= $blocksize;

                        if ($leftsize < 0) {
                            $blocksize += $leftsize;
                        }

                        $temp_content = '';

                        if (is_resource($file_read)) {
                            $temp_content = $this->createTarContent($file_read, $blocksize);
                        }

                        // Get where we are in the file
                        $this->pos_file_to_tar += $blocksize;

                        // If there is no content because the file size has changed (smaller file) we need to add \0
                        if ($diff_size < 0 && !$temp_content) {
                            //self::TAR_BLOCK_SIZE since createTarContent
                            //return self::TAR_BLOCK_SIZE size block (using self::pad)
                            $temp_content = self::pad('', self::TAR_BLOCK_SIZE);
                            $diff_size += self::TAR_BLOCK_SIZE;
                        } elseif ($temp_content === false) {
                            $this->log(
                                'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::AWS)
                                .' '.$this->l('The module was unable to read the file', self::PAGE)
                                .' '.$current_file.', '.$this->l('please check its rights and user owner', self::PAGE)
                            );

                            if (is_resource($file_read)) {
                                fclose($file_read);
                            }

                            return false;
                        }

                        $this->distant_tar_content .= $temp_content;
                        $this->distant_tar_content_size += self::TAR_BLOCK_SIZE;

                        $percent = (($this->distant_tar_content_size + $this->aws_position)/$this->tar_files_size[$this->part_number]) * 100;

                        if ($percent >= $this->old_percent + 1) {
                            $this->old_percent = round($percent, 0);

                            if ($this->total_nb_part > 1) {
                                $this->log(
                                    sprintf($this->l('Creating backup on %s account:', self::PAGE), self::AWS)
                                    .' '.$this->part_number.'/'.$this->total_nb_part.$this->l(':', self::PAGE)
                                    .' '.(int)$percent.'%'
                                );
                            } else {
                                $this->log(
                                    sprintf($this->l('Creating backup on %s account:', self::PAGE), self::AWS)
                                    .' '.(int)$percent.'%'
                                );
                            }
                        }
                    }

                    //refresh
                    $this->refreshBackup(true);
                }
                //Close file
                if (is_resource($file_read)) {
                    fclose($file_read);
                }
            }

            $this->position_file_list_file = ftell($this->handle_file_list_file);

            $this->checkStopScript();

            //Check if tar file size has reach its predicted size
            if (isset($this->tar_files_size[$this->part_number])
                && ($this->aws_position + $this->distant_tar_content_size + self::TAR_END_SIZE) >= $this->tar_files_size[$this->part_number]
                && $this->aws_position < $this->tar_files_size[$this->part_number]
            ) {
                //The tar file will be too big, we need to close it and use a new one
                $this->distant_tar_content .= self::pad('', self::TAR_END_SIZE);
                $this->distant_tar_content_size +=  self::TAR_END_SIZE;

                if ($this->distant_tar_content_size > $this->tar_files_size[$this->part_number]) {
                    $this->log(
                        $this->l('The total size should not be less that the size we are now at', self::PAGE)
                        .' ('.$this->distant_tar_content_size.'/'.$this->tar_files_size[$this->part_number]
                    );
                }

                // Finish sending current tar
                $resume_upload_content = $aws_lib->resumeUploadContent(
                    $this->distant_tar_content,
                    $this->distant_tar_content_size,
                    $this->tar_files_size[$this->part_number],
                    $tar_name,
                    $aws->directory_key,
                    $this->aws_upload_part
                );

                if ($resume_upload_content === false) {
                    $this->log(
                        'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::AWS)
                        .' '.$this->l('File', self::PAGE).' '.$tar_name
                        .' '.$this->l('cannot be finished', self::PAGE)
                    );

                    return false;
                }

                // New part, so back to init values
                $this->aws_nb_part++;
                $this->part_number++;

                $this->next_step                = $this->step_send['aws'];
                $this->aws_position             = 0;
                $this->old_percent              = 0;
                $this->secondary_next_step      = self::SECONDARY_STEP_TAR_FILE;
                $this->distant_tar_content      = '';
                $this->distant_tar_content_size = 0;
                $this->aws_upload_part          = 1;
                $this->aws_etag                 = array();

                //refresh
                $this->refreshBackup(true);
            }

            $this->secondary_next_step = self::SECONDARY_STEP_TAR_FILE;
        }

        // Check that there is no missing file
        if ($this->files_done < $this->total_files) {
            $this->log(
                'WAR'.sprintf($this->l('Creating backup on %s account:', self::PAGE), self::AWS)
                .$this->l('Be careful! Not all of your files have been added to the backup. Number of files backuped:', self::PAGE)
                .' '.$this->files_done.'/'.$this->total_files
            );

            return false;
        }

        $this->files_done       = 0;
        $this->pos_file_to_tar  = 0;
        $this->next_step        = $this->step_send['aws_resume'];

        if ($this->config->send_restore) {
            $this->aws_nb_part      = 1;
            $nb_part_total          = 1;
            $this->aws_upload_part  = 1;
            $this->aws_position     = 0;
            $this->aws_etag         = array();

            $this->log(
                sprintf($this->l('Creating restore file on %s account...', self::PAGE), self::AWS)
            );

            // Upload the file
            $upload_file = $aws_lib->uploadFile(
                self::NEW_RESTORE_NAME,
                $this->restore_file,
                $aws->directory_key,
                $aws->storage_class,
                $this->aws_upload_part,
                $this->aws_nb_part,
                $nb_part_total,
                self::NEW_RESTORE_NAME
            );

            if ($upload_file === false) {
                return false;
            }
        }

        return true;
    }

    /**
     * Delete old backup files on a Dropbox account
     *
     * @param   String      $access_token   Dropbox token
     * @param   array       $old_backups    Dropbox old backups
     *
     * @return  boolean                     The success or failure of the connection
     */
    protected function deleteDropboxOldBackup($access_token, $old_backups)
    {
        $dropbox            = new Dropbox($this->dropbox_account_id);
        $nb_backup_to_keep  = $dropbox->config_nb_backup;

        //Do we need to delete old backups
        if ($nb_backup_to_keep == 0) {
            $this->log(
                sprintf($this->l('Sending backup to %s account:', self::PAGE), self::DROPBOX).' '
                .$this->l('Keep all old backup', self::PAGE)
            );
            return true;
        }

        $this->log(
            sprintf($this->l('Sending backup to %s account:', self::PAGE), self::DROPBOX)
            .' '.$this->l('Deleting old backup', self::PAGE)
        );

        $nb_files = count($old_backups);

        //Do we really need to delete old backups
        if ($nb_files < $nb_backup_to_keep) {
            return true;
        }

        $dropbox_lib    = $this->connectToDropbox($access_token);
        $success        = true;
        $temp_directory = $this->dropbox_dir;

        //Dropbox dir should end with a "/" except when testing if exist
        if (Tools::substr($this->dropbox_dir, -1) != '/') {
            $temp_directory .= '/';
        }

        //Yes we have to delete old backups
        if ($nb_backup_to_keep > 0) {
            while ($nb_files >= $nb_backup_to_keep) {
                foreach ($old_backups[$nb_files]['part'] as $part) {
                    if ($dropbox_lib->deleteFile($temp_directory.basename($part['name'])) === false) {
                        $this->log($this->l('Error while deleting the file:', self::PAGE).' '.$part['name']);
                        $success = false;
                    }
                }

                $nb_files--;
            }
        }

        return $success;
    }

    /**
     * Delete old backup files on a ownCloud/Nextcloud account
     *
     * @param   object      $owncloud_lib   ownCloud to use
     * @param   array       $old_backups    ownCloud old backups
     *
     * @return  boolean                     The success or failure of the connection
     */
    protected function deleteOwncloudOldBackup($owncloud_lib, $old_backups)
    {
        $owncloud           = new Owncloud($this->owncloud_account_id);
        $nb_backup_to_keep  = $owncloud->config_nb_backup;

        //Do we need to delete old backups
        if ($nb_backup_to_keep == 0) {
            $this->log(
                sprintf($this->l('Sending backup to %s account:', self::PAGE), self::OWNCLOUD)
                .' '.$this->l('Keep all old backup', self::PAGE)
            );
            return true;
        }

        $this->log(
            sprintf($this->l('Sending backup to %s account:', self::PAGE), self::OWNCLOUD)
            .' '.$this->l('Deleting old backup', self::PAGE)
        );

        $nb_files = count($old_backups);

        //Do we really need to delete old backups
        if ($nb_files < $nb_backup_to_keep) {
            return true;
        }

        //Yes we have to delete old backups
        if ($nb_backup_to_keep > 0) {
            while ($nb_files >= $nb_backup_to_keep) {
                foreach ($old_backups[$nb_files]['part'] as $part) {
                    if ($owncloud_lib->deleteFile($part['file_id']) === false) {
                        $this->log($this->l('Error while deleting the file:', self::PAGE).' '.$part['name']);
                    }
                }

                $nb_files--;
            }
        }

        return true;
    }

    /**
     * Delete old backup files on a WebDAV account
     *
     * @param   object      $webdav_lib     WebDAV to use
     * @param   array       $old_backups    WebDAV old backups
     *
     * @return  boolean                     The success or failure of the connection
     */
    protected function deleteWebdavOldBackup($webdav_lib, $old_backups)
    {
        $webdav             = new Webdav($this->webdav_account_id);
        $nb_backup_to_keep  = $webdav->config_nb_backup;

        //Do we need to delete old backups
        if ($nb_backup_to_keep == 0) {
            $this->log(
                sprintf($this->l('Sending backup to %s account:', self::PAGE), self::WEBDAV).' '
                .$this->l('Keep all old backup', self::PAGE)
            );
            return true;
        }

        $this->log(
            sprintf($this->l('Sending backup to %s account:', self::PAGE), self::WEBDAV)
            .' '.$this->l('Deleting old backup', self::PAGE)
        );

        $nb_files = count($old_backups);

        //Do we really need to delete old backups
        if ($nb_files < $nb_backup_to_keep) {
            return true;
        }

        //Yes we have to delete old backups
        if ($nb_backup_to_keep > 0) {
            while ($nb_files >= $nb_backup_to_keep) {
                foreach ($old_backups[$nb_files]['part'] as $part) {
                    if ($webdav_lib->deleteFile($part['file_id']) === false) {
                        $this->log($this->l('Error while deleting the file:', self::PAGE).' '.$part['name']);
                    }
                }

                $nb_files--;
            }
        }

        return true;
    }

    /**
     * Delete old backup files on a Google Drive account
     *
     * @param   object  $googledrive_lib    Google Drive to use
     * @param   array   $old_backups        Google Drive old backups
     *
     * @return  boolean                     The success or failure of the connection
     */
    protected function deleteGoogledriveOldBackup($googledrive_lib, $old_backups)
    {
        $googledrive        = new Googledrive($this->googledrive_account_id);
        $nb_backup_to_keep  = $googledrive->config_nb_backup;

        //Do we need to delete old backups
        if ($nb_backup_to_keep == 0) {
            $this->log(
                sprintf($this->l('Sending backup to %s account:', self::PAGE), self::GOOGLEDRIVE)
                .' '.$this->l('Keep all old backup', self::PAGE)
            );
            return true;
        }

        $this->log(
            sprintf($this->l('Sending backup to %s account:', self::PAGE), self::GOOGLEDRIVE)
            .' '.$this->l('Deleting old backup', self::PAGE)
        );

        $nb_files = count($old_backups);

        //Do we really need to delete old backups
        if ($nb_files < $nb_backup_to_keep) {
            return true;
        }

        //Yes we have to delete old backups
        if ($nb_backup_to_keep > 0) {
            while ($nb_files >= $nb_backup_to_keep) {
                foreach ($old_backups[$nb_files]['part'] as $part) {
                    // We delete the file by the ID link to the its name
                    if ($googledrive_lib->deleteFile($part['file_id']) === false) {
                        $this->log($this->l('Error while deleting the file:', self::PAGE).' '.$part['name']);
                    }
                }
                $nb_files--;
            }
        }

        return true;
    }

    /**
     * Delete old backup files on a hubiC account
     *
     * @param   object      $hubic_lib      hubiC to use
     *
     * @return  boolean                     The success or failure of the connection
     */
    protected function deleteHubicOldBackup($hubic_lib)
    {
        $hubic              = new Hubic($this->hubic_account_id);
        $nb_backup_to_keep  = $hubic->config_nb_backup;

        //Do we need to delete old backups
        if ($nb_backup_to_keep == 0) {
            $this->log(
                sprintf($this->l('Sending backup to %s account:', self::PAGE), self::HUBIC)
                .' '.$this->l('Keep all old backup', self::PAGE)
            );
            return true;
        }

        $this->log(
            sprintf($this->l('Sending backup to %s account:', self::PAGE), self::HUBIC)
            .' '.$this->l('Deleting old backup', self::PAGE)
        );

        //Find all old backups
        $children       = array_merge($hubic_lib->listFiles($hubic->directory));
        $old_backups    = array();

        foreach ($children as $child) {
            $infos_file = pathinfo($child['name']);

            if (!isset($infos_file['extension']) || strpos($child['content_type'], 'hubic/') !== false) {
                continue;
            }

            if ($infos_file['extension'] == 'tar'
                || strpos($child['name'], '.tar.') !== false
                || (
                    $this->config->send_restore
                    && strpos($child['name'], self::NEW_RESTORE_NAME) !== false
                    )
            ) {
                if (strpos($child['name'], '.'.$this->config->type_backup) !== false) {
                    $old_backups[] = $child['name'];
                }
            }
        }

        $clean_list_old_backups = $this->cleanListBackup($old_backups);
        $nb_files = count($clean_list_old_backups);

        //Do we really need to delete old backups
        if ($nb_files < $nb_backup_to_keep) {
            return true;
        }

        //Yes we have to delete old backups
        if ($nb_backup_to_keep > 0) {
            while ($nb_files >= $nb_backup_to_keep) {
                foreach ($clean_list_old_backups[$nb_files]['part'] as $part) {
                    // We can now delete the manifest (or the file if it was small enough)
                    if ($hubic_lib->deleteFile($part['name']) === false) {
                        $this->log($this->l('Error while deleting the file:', self::PAGE).' '.$part['name']);
                    }
                }

                $nb_files--;
            }
        }

        return true;
    }

    /**
     * Delete old backup files on a OneDrive account
     *
     * @param   object  $onedrive_lib   OneDrive to use
     * @param   array   $old_backups    OneDrive old backups
     *
     * @return  boolean                     The success or failure of the connection
     */
    protected function deleteOnedriveOldBackup($onedrive_lib, $old_backups)
    {
        $onedrive           = new Onedrive($this->onedrive_account_id);
        $nb_backup_to_keep  = $onedrive->config_nb_backup;
        //$onedrive->directory_key

        //Do we need to delete old backups
        if ($nb_backup_to_keep == 0) {
            $this->log(
                sprintf($this->l('Sending backup to %s account:', self::PAGE), self::ONEDRIVE)
                .' '.$this->l('Keep all old backup', self::PAGE)
            );
            return true;
        }

        $this->log(
            sprintf($this->l('Sending backup to %s account:', self::PAGE), self::ONEDRIVE)
            .' '.$this->l('Deleting old backup', self::PAGE)
        );

        $nb_files = count($old_backups);

        //Do we really need to delete old backups
        if ($nb_files < $nb_backup_to_keep) {
            return true;
        }

        //Yes we have to delete old backups
        if ($nb_backup_to_keep > 0) {
            while ($nb_files >= $nb_backup_to_keep) {
                foreach ($old_backups[$nb_files]['part'] as $part) {
                    if (!$onedrive_lib->deleteItem($part['file_id'])) {
                        $this->log($this->l('Error while deleting the file:', self::PAGE).' '.$part['name']);
                    }
                }
                $nb_files--;
            }
        }

        return true;
    }

    /**
     * Delete old backup files on a SugarSync account
     *
     * @param   object      $sugarsync_lib  SugarSync to use
     * @param   String      $id_directory   SugarSync directory ID
     *
     * @return  boolean                     The success or failure of the connection
     */
    protected function deleteSugarsyncOldBackup($sugarsync_lib, $id_directory)
    {
        $sugarsync          = new Sugarsync($this->sugarsync_account_id);
        $nb_backup_to_keep  = $sugarsync->config_nb_backup;

        //Do we need to delete old backups
        if ($nb_backup_to_keep == 0) {
            $this->log(
                sprintf($this->l('Sending backup to %s account:', self::PAGE), self::SUGARSYNC)
                .' '.$this->l('Keep all old backup', self::PAGE)
            );
            return true;
        }

        $this->log(
            sprintf($this->l('Sending backup to %s account:', self::PAGE), self::SUGARSYNC)
            .' '.$this->l('Deleting old backup', self::PAGE)
        );

        $files = $sugarsync_lib->getListFiles($id_directory);

        if (!is_array($files) || !count($files)) {
            return true; // No file to delete
        }

        $old_backups        = array();
        $old_backups_sort   = array();

        foreach ($files as $file) {
            if (strpos($file['displayName'], '.tar.') !== false
                || (
                    $this->config->send_restore
                    && strpos($file['displayName'], self::NEW_RESTORE_NAME) !== false
                )
            ) {
                if (strpos($file['displayName'], '.'.$this->config->type_backup) !== false) {
                    $old_backups[basename($file['ref'])] = $file['displayName'];
                }
            }
        }

        $clean_list_old_backups = $this->cleanListBackup($old_backups);

        foreach ($old_backups as $id_file => $name_file) {
            $old_backups_sort[$name_file] = $id_file;
        }

        $nb_files = count($clean_list_old_backups);

        //Do we really need to delete old backups
        if ($nb_files < $nb_backup_to_keep) {
            return true;
        }

        //Yes we have to delete old backups
        if ($nb_backup_to_keep > 0) {
            while ($nb_files >= $nb_backup_to_keep) {
                foreach ($clean_list_old_backups[$nb_files]['part'] as $part) {
                    if (!isset($old_backups_sort[$part['name']])) {
                        $this->log($this->l('Error unknown file:', self::PAGE).' '.$part['name']);
                    } else {
                        if (!$sugarsync_lib->deleteFile($old_backups_sort[$part['name']])) {
                            $this->log($this->l('Error while deleting the file:', self::PAGE).' '.$part['name']);
                        }
                    }
                }
                $nb_files--;
            }
        }

        return true;
    }

    /**
     * Delete old backup files on a AWS account
     *
     * @param   object      $aws_lib    AWS to use
     *
     * @return  boolean                 The success or failure of the connection
     */
    public function deleteAwsOldBackup($aws_lib)
    {
        $aws                = new Aws($this->aws_account_id);
        $nb_backup_to_keep  = $aws->config_nb_backup;
        $aws_directory_key  = $aws->directory_key;

        //Do we need to delete old backups
        if ($nb_backup_to_keep == 0) {
            $this->log(
                sprintf($this->l('Sending backup to %s account:', self::PAGE), self::AWS)
                .' '.$this->l('Keep all old backup', self::PAGE)
            );
            return true;
        }

        $this->log(
            sprintf($this->l('Sending backup to %s account:', self::PAGE), self::AWS)
            .' '.$this->l('Deleting old backup', self::PAGE)
        );

        if (Tools::substr($aws_directory_key, -1) != '/') {
            $aws_directory_key .= '/';
        }

        if ($aws_directory_key != $aws->bucket) {
            $children = $aws_lib->getListFiles($aws_directory_key);
        } else {
            $children = $aws_lib->getListFiles();
        }

        if ($children === false) {
            return true; // No child to delete
        }

        $old_backups    = array();

        foreach ($children as $child) {
            $infos_file = pathinfo($child['name']);

            if ((isset($infos_file['extension']) && $infos_file['extension'] == 'tar')
                || strpos($child['name'], '.tar.') !== false
                || (
                    $this->config->send_restore
                    && strpos($child['name'], self::NEW_RESTORE_NAME) !== false
                    )
            ) {
                if (strpos($child['name'], '.'.$this->config->type_backup) !== false) {
                    $old_backups[] = $child['name'];
                }
            }
        }

        $clean_list_old_backups = $this->cleanListBackup($old_backups);
        $nb_files = count($clean_list_old_backups);

        //Do we really need to delete old backups
        if ($nb_files < $nb_backup_to_keep) {
            return true;
        }

        //Yes we have to delete old backups
        if ($nb_backup_to_keep > 0) {
            while ($nb_files >= $nb_backup_to_keep) {
                foreach ($clean_list_old_backups[$nb_files]['part'] as $part) {
                    // We can now delete the file
                    if ($aws_lib->deleteFile($aws_directory_key.$part['name']) === false) {
                        $this->log(
                            $this->l('Error while deleting the file:', self::PAGE).' '.$aws_directory_key.$part['name']
                        );
                    }
                }

                $nb_files--;
            }
        }

        return true;
    }

    /**
     * Delete old backup files on a FTP account
     *
     * @param   ressource   $connection     FTP connection
     *
     * @return  boolean                     The success or failure of the connection
     */
    protected function deleteFTPOldBackup($connection)
    {
        $ftp = new Ftp($this->ftp_account_id);
        $nb_backup_to_keep  = $ftp->config_nb_backup;

        //Do we need to delete old backups
        if ($nb_backup_to_keep == 0) {
            $this->log(
                sprintf($this->l('Sending backup to %s account:', self::PAGE), self::FTP)
                .' '.$this->l('Keep all old backup', self::PAGE)
            );
            return true;
        }

        $this->log(
            sprintf($this->l('Sending backup to %s account:', self::PAGE), self::FTP)
            .' '.$this->l('Deleting old backup', self::PAGE)
        );

        //Find all old backups
        $files = ftp_nlist($connection, '');

        if ($files === false) {
            $files = ftp_rawlist($connection, ''); // get the files of the directory using ftp_rawlist instead

            if (is_array($files)) {
                foreach ($files as &$item) {
                    $item = Tools::substr($item, (strrpos($item, ' ') + 1));
                }
            }
        }

        if ($files === false) {
            $this->log(
                sprintf($this->l('Error while listing old %s files', self::PAGE), self::FTP)
            );
            return false;
        }

        $old_backups    = array();

        foreach ($files as $file) {
            $infos_file = pathinfo($file);

            if ((isset($infos_file['extension']) && $infos_file['extension'] == 'tar')
                || strpos($file, '.tar.') !== false
                || (
                    $this->config->send_restore
                    && strpos($file, self::NEW_RESTORE_NAME) !== false
                    )
            ) {
                if (strpos($file, '.'.$this->config->type_backup) !== false) {
                    $old_backups[] = $file;
                }
            }
        }

        $clean_list_old_backups = $this->cleanListBackup($old_backups);
        $nb_files = count($clean_list_old_backups);

        //Do we really need to delete old backups
        if ($nb_files < $nb_backup_to_keep) {
            return true;
        }

        //Yes we have to delete old backups
        if ($nb_backup_to_keep > 0) {
            while ($nb_files >= $nb_backup_to_keep) {
                foreach ($clean_list_old_backups[$nb_files]['part'] as $part) {
                    if (!ftp_delete($connection, basename($part['name']))) {
                        $this->log($this->l('Delete old backup file failed:', self::PAGE).basename($part['name']));
                        return false;
                    }
                }
                $nb_files--;
            }
        }

        return true;
    }

    /**
     * Delete old backup files on a SFTP account
     *
     * @param   object      $sftp_lib   SFTP to use
     * @param   String      $ftp_dir    SFTP directory
     *
     * @return  boolean                 The success or failure of the connection
     */
    protected function deleteSFTPOldBackup($sftp_lib, $ftp_dir)
    {
        $ftp                = new Ftp($this->ftp_account_id);
        $nb_backup_to_keep  = $ftp->config_nb_backup;

        //Do we need to delete old backups
        if ($nb_backup_to_keep == 0) {
            $this->log(
                sprintf($this->l('Sending backup to %s account:', self::PAGE), self::SFTP)
                .' '.$this->l('Keep all old backup', self::PAGE)
            );
            return true;
        }

        $this->log(
            sprintf($this->l('Sending backup to %s account:', self::PAGE), self::SFTP)
            .' '.$this->l('Deleting old backup', self::PAGE)
        );

        //Find all old backups
        $files          = $sftp_lib->nlist($ftp_dir);
        $old_backups    = array();

        if (is_array($files)) {
            foreach ($files as $file) {
                $infos_file = pathinfo($file);

                if ((isset($infos_file['extension']) && $infos_file['extension'] == 'tar')
                    || strpos($file, '.tar.') !== false
                    || (
                        $this->config->send_restore
                        && strpos($file, self::NEW_RESTORE_NAME) !== false
                        )
                ) {
                    if (strpos($file, '.'.$this->config->type_backup) !== false) {
                        $old_backups[] = $file;
                    }
                }
            }
        }

        $clean_list_old_backups = $this->cleanListBackup($old_backups);

        $nb_files = count($clean_list_old_backups);

        //Do we really need to delete old backups
        if ($nb_files < $nb_backup_to_keep) {
            return true;
        }

        //Yes we have to delete old backups
        if ($nb_backup_to_keep > 0) {
            while ($nb_files >= $nb_backup_to_keep) {
                foreach ($clean_list_old_backups[$nb_files]['part'] as $part) {
                    if (!$sftp_lib->delete($ftp_dir.basename($part['name']))) {
                        $this->log($this->l('Delete old backup file failed:', self::PAGE).basename($part['name']));
                        return false;
                    }
                }
                $nb_files--;
            }
        }

        return true;
    }

    /**
     * Get Dropbox access token
     *
     * @param   String  $dropbox_code   Code that will give the token
     *
     * @return  String                  The token
     */
    public function getDropboxAccessToken($dropbox_code)
    {
        $dropbox_lib = $this->connectToDropbox();
        $access_token = $dropbox_lib->getToken($dropbox_code);

        return $access_token;
    }

    /**
     * Get Google Drive access token
     *
     * @param   String  $googledrive_code   Code that will give the token
     *
     * @return  String                      The token
     */
    public function getGoogledriveAccessToken($googledrive_code)
    {
        $googledrive_lib = $this->connectToGoogledrive();
        $access_token = $googledrive_lib->getToken($googledrive_code);

        if (empty($access_token) || !$access_token) {
            return false;
        }

        return $access_token;
    }

    /**
     * Get OneDrive access token
     *
     * @param   String  $onedrive_code      Code that will give the token
     *
     * @return  String                      The token
     */
    public function getOnedriveAccessToken($onedrive_code)
    {
        $onedrive_lib = $this->connectToOnedrive();
        $access_token = $onedrive_lib->getToken($onedrive_code);

        if (empty($access_token) || !$access_token) {
            return false;
        }

        return $access_token;
    }

    /**
     * Get SugarSync refresh token
     *
     * @param   String  $login      Login of the user account
     * @param   String  $password   Password of the user account
     *
     * @return  String                      The token
     */
    public function getSugarsyncRefreshToken($login, $password)
    {
        $sugarsync_lib  = $this->connectToSugarsync();
        $refresh_token   = $sugarsync_lib->getRefreshToken($login, $password);

        if (empty($refresh_token) || !$refresh_token) {
            return false;
        }

        return $refresh_token;
    }

    /**
     * Get hubiC access token
     *
     * @param   String  $hubic_code     Code that will give the token
     *
     * @return  String                  The token
     */
    public function getHubicAccessToken($hubic_code)
    {
        $hubic_lib      = $this->connectToHubic();
        $access_token   = $hubic_lib->getToken($hubic_code);

        if (empty($access_token) || !$access_token) {
            return false;
        }

        $credential = $hubic_lib->getCredential();

        if (!is_array($credential)) {
            return false;
        }

        return array(
            'token'         => $access_token,
            'credential'    => $credential,
        );
    }

    /**
     * Get Google Drive refresh token
     *
     * @param   String  $refresh_token  Code that will give the refresh token
     *
     * @return  String                  The token
     */
    public function getGoogledriveRefreshToken($refresh_token)
    {
        $googledrive_lib = $this->connectToGoogledrive();

        return $googledrive_lib->getRefreshToken($refresh_token);
    }

    /**
     * Get OneDrive refresh token
     *
     * @param   String  $refresh_token  Code that will give the refresh token
     *
     * @return  String                  The token
     */
    public function getOnedriveRefreshToken($refresh_token)
    {
        $onedrive_lib = $this->connectToOnedrive();
        return $onedrive_lib->getRefreshToken($refresh_token);
    }

    /**
     * Get hubiC refresh token
     *
     * @param   String  $refresh_token  Code that will give the refresh token
     *
     * @return  String                  The token
     */
    public function getHubicRefreshToken($refresh_token)
    {
        $hubic_lib = $this->connectToHubic();

        $access_token = $hubic_lib->getRefreshToken($refresh_token);

        if (empty($access_token) || !$access_token) {
            return false;
        }

        $access_token['refresh_token'] = $refresh_token;
        $credential = $hubic_lib->getCredential();

        if (!is_array($credential)) {
            return false;
        }

        return array(
            'token'         => $access_token,
            'credential'    => $credential,
        );
    }

    /**
     * Get SugarSync access token
     *
     * @param   String  $refresh_token  Code that will give the refresh token
     *
     * @return  String                  The token
     */
    public function getSugarsyncAccessToken($refresh_token)
    {
        $sugarsync_lib = $this->connectToSugarsync();

        $access_token = $sugarsync_lib->getAccessToken($refresh_token);

        $decode_token = array();
        $decode_token['refresh_token']  = $this->encrypt($refresh_token);
        $decode_token['access_token']   = $this->encrypt($access_token['access_token']);
        $decode_token['expire_in']      = $access_token['expire_in'];
        $decode_token['user']           = $access_token['user'];

        return $decode_token;
    }

    /**
     * Get OneDrive access token url
     *
     * @param   object  $onedrive_lib   OneDrive that we need the url from
     *
     * @return  String                  The url that will give the access token
     */
    public function getOnedriveAccessTokenUrl($onedrive_lib)
    {
        $url = $onedrive_lib->getLogInUrl();

        if ($url === false) {
            return false;
        }

        return $url;
    }

    /**
     * Close SFTP
     *
     * @param   object  $sftp_lib   SFTP to close
     *
     * @return  boolean             The success or failure of the operation
     */
    public function closeSFTP($sftp_lib)
    {
        if ($sftp_lib->exec('exit;') === false) {
            return false;
        }

        return true;
    }

    /**
     * Get a list of all backup files of the chosen directory in Dropbox account
     *
     * @param   Object  $dropbox_lib    Dropbox to use
     * @param   String  $dropbox_dir    Dropbox directory to search in
     *
     * @return  array|String            The files of the Dropbox directory or an error message
     */
    public function getDropboxFiles($dropbox_lib, $dropbox_dir)
    {
        // Get informations on the directory and his children
        $folder_children    = $dropbox_lib->listFolderChildren($dropbox_dir);
        $children           = $folder_children['entries']; // get contents of the directory
        $files_name         = array();
        $files              = array();
        $infos              = array();
        $total_size         = 0;

        if (is_array($children)) {
            foreach ($children as $child) {
                $child      = (array)$child;
                $infos_file = pathinfo($child['path_lower']);

                if ($child['.tag'] == 'file') {
                    if ((isset($infos_file['extension']) && $infos_file['extension'] == 'tar')
                        || strpos($child['path_lower'], '.tar.') !== false
                    ) {
                        if (strpos($child['path_lower'], '.'.$this->config->type_backup) !== false) {
                            $files_name[] = $child['name'];
                            $infos[$child['name']] = $child;
                        }
                    }
                }
            }
            $files = $this->cleanListBackup($files_name);

            foreach ($files as &$file) {
                $total_size = 0;

                if (is_array($file['part'])) {
                    foreach ($file['part'] as &$part) {
                        if (isset($infos[$part['name']])) {
                            $total_size += $infos[$part['name']]['size'];
                            $part['size_byte']  = $infos[$part['name']]['size'];
                            $part['size']       = $this->readableSize($infos[$part['name']]['size']);
                            $part['file_id']    = $infos[$part['name']]['id'];
                        }
                    }
                } else {
                    $total_size = $infos[$file['name']]['size'];
                }

                if (isset($infos[$file['name']])) {
                    $file['file_id']    = $infos[$file['name']]['id'];
                }

                $file['size_byte']  = $total_size;
                $file['size']       = $this->readableSize($total_size);
            }
        }

        //d($files);
        return $files;
    }

    /**
     * Get a list of all backup files of the chosen directory in Google Drive account
     *
     * @param   Object  $googledrive_lib    Google Drive to use
     * @param   String  $googledrive_dir    Google Drive directory to search in
     *
     * @return  array|String            The files of the Google Drive directory or an error message
     */
    public function getGoogledriveFiles($googledrive_lib, $googledrive_dir)
    {
        // List the directory children
        $children   = $googledrive_lib->getChildrenFiles($googledrive_dir);
        $files_name = array();
        $infos      = array();
        $total_size = 0;

        foreach ($children as $child) {
            $infos_file = pathinfo($child['name']);

            if ((isset($infos_file['extension']) && $infos_file['extension'] == 'tar')
                || strpos($child['name'], '.tar.') !== false
            ) {
                if (strpos($child['name'], '.'.$this->config->type_backup) !== false) {
                    $files_name[$child['name']] = $child['name'];
                    $infos[$child['name']] = $child;
                }
            }
        }

        $files = $this->cleanListBackup($files_name);

        foreach ($files as &$file) {
            $total_size = 0;

            if (is_array($file['part'])) {
                foreach ($file['part'] as &$part) {
                    if (isset($infos[$part['name']])) {
                        $total_size += $infos[$part['name']]['size'];
                        $part['size_byte']  = $infos[$part['name']]['size'];
                        $part['size']       = $this->readableSize($infos[$part['name']]['size']);
                        $part['file_id']    = $infos[$part['name']]['id'];
                    }
                }
            } else {
                $total_size = $infos[$file['name']]['size'];
            }

            if (isset($infos[$file['name']])) {
                $file['file_id']    = $infos[$file['name']]['id'];
            }

            $file['size_byte']  = $total_size;
            $file['size']       = $this->readableSize($total_size);
        }

        //d($files);
        return $files;
    }

    /**
     * Get a list of all backup files of the chosen directory in Onedrive account
     *
     * @param   Object  $onedrive_lib    Onedrive to use
     * @param   String  $onedrive_dir    Onedrive directory to search in
     *
     * @return  array|String            The files of the Onedrive directory or an error message
     */
    public function getOnedriveFiles($onedrive_lib, $onedrive_dir)
    {
        // List the directory children
        $children   = $onedrive_lib->getListChildren($onedrive_dir);
        $files_name = array();
        $infos      = array();
        $total_size = 0;

        foreach ($children as $child) {
            $infos_file = pathinfo($child['name']);

            if (!$child['is_folder']) {
                if ((isset($infos_file['extension']) && $infos_file['extension'] == 'tar')
                    || strpos($child['name'], '.tar.') !== false
                ) {
                    if (strpos($child['name'], '.'.$this->config->type_backup) !== false) {
                        $files_name[$child['id']] = $child['name'];
                        $infos[$child['name']] = $child;
                    }
                }
            }
        }

        $files = $this->cleanListBackup($files_name);

        foreach ($files as &$file) {
            $total_size = 0;

            if (is_array($file['part'])) {
                foreach ($file['part'] as &$part) {
                    if (isset($infos[$part['name']])) {
                        $total_size += $infos[$part['name']]['size'];
                        $part['size_byte']  = $infos[$part['name']]['size'];
                        $part['size']       = $this->readableSize($infos[$part['name']]['size']);
                        $part['file_id']    = $infos[$part['name']]['id'];
                    }
                }
            } else {
                $total_size         = $infos[$file['name']]['size'];
            }

            if (isset($infos[$file['name']])) {
                $file['file_id']    = $infos[$file['name']]['id'];
            }

            $file['size_byte']  = $total_size;
            $file['size']       = $this->readableSize($total_size);
        }
        //d($files);

        return $files;
    }

    /**
     * Get a list of all backup files of the chosen directory in ownCloud/Nextcloud account
     *
     * @param   Object  $owncloud_lib    ownCloud/Nextcloud to use
     * @param   String  $owncloud_dir    ownCloud/Nextcloud directory to search in
     *
     * @return  array|String            The files of the ownCloud/Nextcloud directory or an error message
     */
    public function getOwncloudFiles($owncloud_lib, $owncloud_dir)
    {
        // List the directory children
        $children   = $owncloud_lib->getFileChildren($owncloud_dir); // get the files of the directory

        $files_name = array();
        $infos      = array();
        $total_size = 0;

        foreach ($children as $child) {
            $infos_file = pathinfo($child['name']);

            if ((isset($infos_file['extension']) && $infos_file['extension'] == 'tar')
                || strpos($child['name'], '.tar.') !== false
            ) {
                if (strpos($child['name'], '.'.$this->config->type_backup) !== false) {
                    $child['name'] = basename($child['name']);

                    if ($owncloud_dir == '') {
                        $child['path'] = str_replace('/remote.php/webdav/', '', $child['name']);
                    } else {
                        if (Tools::substr($owncloud_dir, -1) != '/') {
                            $owncloud_dir .= '/';
                        }
                        $child['path'] = $owncloud_dir.$child['name'];
                    }

                    $files_name[] = $child['name'];
                    $infos[$child['name']] = $child;
                }
            }
        }

        $files = $this->cleanListBackup($files_name);

        foreach ($files as &$file) {
            $total_size = 0;

            if (is_array($file['part'])) {
                foreach ($file['part'] as &$part) {
                    if (isset($infos[$part['name']])) {
                        $total_size += $infos[$part['name']]['size'];
                        $part['size_byte']  = $infos[$part['name']]['size'];
                        $part['size']       = $this->readableSize($infos[$part['name']]['size']);
                        $part['file_id']    = $infos[$part['name']]['path'];
                    }
                }
            } else {
                $total_size         = $infos[$file['name']]['size'];
            }

            if (isset($infos[$file['name']])) {
                $file['file_id']    = $infos[$file['name']]['path'];
            }

            $file['size_byte']  = $total_size;
            $file['size']       = $this->readableSize($total_size);
        }

        //d($files);
        return $files;
    }

    /**
     * Get a list of all backup files of the chosen directory in WebDAV account
     *
     * @param   Object  $webdav_lib    WebDAV to use
     * @param   String  $webdav_dir    WebDAV directory to search in
     *
     * @return  array|String            The files of the WebDAV directory or an error message
     */
    public function getWebdavFiles($webdav_lib, $webdav_dir)
    {
        // List the directory children
        $children   = $webdav_lib->getFileChildren($webdav_dir); // get the files of the directory

        $files_name = array();
        $infos      = array();
        $total_size = 0;

        foreach ($children as $child) {
            $infos_file = pathinfo($child['name']);

            if ((isset($infos_file['extension']) && $infos_file['extension'] == 'tar')
                || strpos($child['name'], '.tar.') !== false
            ) {
                if (strpos($child['name'], '.'.$this->config->type_backup) !== false) {
                    $child['name'] = basename($child['name']);

                    if ($webdav_dir == '') {
                        $child['path'] = str_replace('/remote.php/webdav/', '', $child['name']);
                    } else {
                        if (Tools::substr($webdav_dir, -1) != '/') {
                            $webdav_dir .= '/';
                        }
                        $child['path'] = $webdav_dir.$child['name'];
                    }
                    $files_name[] = $child['name'];
                    $infos[$child['name']] = $child;
                }
            }
        }

        $files = $this->cleanListBackup($files_name);

        foreach ($files as &$file) {
            $total_size = 0;

            if (is_array($file['part'])) {
                foreach ($file['part'] as &$part) {
                    if (isset($infos[$part['name']])) {
                        $total_size += $infos[$part['name']]['size'];
                        $part['size_byte']  = $infos[$part['name']]['size'];
                        $part['size']       = $this->readableSize($infos[$part['name']]['size']);
                        $part['file_id']    = $infos[$part['name']]['path'];
                    }
                }
            } else {
                $total_size         = $infos[$file['name']]['size'];
            }

            if (isset($infos[$file['name']])) {
                $file['file_id']    = $infos[$file['name']]['path'];
            }

            $file['size_byte']  = $total_size;
            $file['size']       = $this->readableSize($total_size);
        }

        //d($files);
        return $files;
    }

    /**
     * Get a list of all backup files of the chosen directory in FTP account
     *
     * @param   Object  $connection  FTP to use
     *
     * @return  array|String    The files of the FTP directory or an error message
     */
    public function getFtpFiles($connection)
    {
        // List the directory children
        $children = ftp_nlist($connection, ''); // get the files of the directory

        if ($children === false) {
            $children = ftp_rawlist($connection, ''); // get the files of the directory using ftp_rawlist instead

            if (is_array($children)) {
                foreach ($children as &$item) {
                    $item = Tools::substr($item, (strrpos($item, ' ') + 1));
                }
            }
        }

        $files_name = array();
        $infos      = array();
        $total_size = 0;

        if (is_array($children)) {
            foreach ($children as $child) {
                $infos_file = pathinfo($child);

                if ((isset($infos_file['extension']) && $infos_file['extension'] == 'tar')
                    || strpos($child, '.tar.') !== false
                ) {
                    if (strpos($child, '.'.$this->config->type_backup) !== false) {
                        $files_name[]   = $child;
                        $infos[$child]  = array(
                            'name'  => $child,
                            'size'  => ftp_size($connection, $child),
                        );
                    }
                }
            }
        }

        $files = $this->cleanListBackup($files_name);

        foreach ($files as &$file) {
            $total_size = 0;

            if (is_array($file['part'])) {
                foreach ($file['part'] as &$part) {
                    if (isset($infos[$part['name']])) {
                        $total_size += $infos[$part['name']]['size'];
                        $part['size_byte']  = $infos[$part['name']]['size'];
                        $part['size']       = $this->readableSize($infos[$part['name']]['size']);
                        $part['file_id']    = $part['name'];
                    }
                }
            } else {
                $total_size         = $infos[$file['name']]['size'];
            }

            if (isset($infos[$file['name']])) {
                $file['file_id']    = $file['name'];
            }

            $file['size_byte']  = $total_size;
            $file['size']       = $this->readableSize($total_size);
        }

        //d($files);
        return $files;
    }

    /**
     * Get a list of all backup files of the chosen directory in SFTP account
     *
     * @param   Object  $sftp_lib       SFTP to use
     * @param   String  $sftp_directory SFTP directory
     *
     * @return  array|String    The files of the SFTP directory or an error message
     */
    public function getSftpFiles($sftp_lib, $sftp_directory)
    {
        // List the directory children
        $children   = $sftp_lib->nlist($sftp_directory); // get the files of the directory

        $files_name = array();
        $infos      = array();
        $total_size = 0;

        if (is_array($children)) {
            foreach ($children as $child) {
                $infos_file = pathinfo($child);

                if ((isset($infos_file['extension']) && $infos_file['extension'] == 'tar')
                    || strpos($child, '.tar.') !== false
                ) {
                    if (strpos($child, '.'.$this->config->type_backup) !== false) {
                        $path = $sftp_directory.$child;

                        $files_name[]   = $child;

                        $infos[$child]  = array(
                            'name'  => $child,
                            'size'  => $sftp_lib->size($path),
                        );
                    }
                }
            }
        }

        $files = $this->cleanListBackup($files_name);

        foreach ($files as &$file) {
            $total_size = 0;

            if (is_array($file['part'])) {
                foreach ($file['part'] as &$part) {
                    if (isset($infos[$part['name']])) {
                        $total_size += $infos[$part['name']]['size'];
                        $part['size_byte']  = $infos[$part['name']]['size'];
                        $part['size']       = $this->readableSize($infos[$part['name']]['size']);
                        $part['file_id']    = $part['name'];
                    }
                }
            } else {
                $total_size         = $infos[$file['name']]['size'];
            }

            if (isset($infos[$file['name']])) {
                $file['file_id']    = $file['name'];
            }

            $file['size_byte']  = $total_size;
            $file['size']       = $this->readableSize($total_size);
        }

        //d($files);
        return $files;
    }

    /**
     * Get a list of all directories of the Google Drive account
     *
     * @param   String  $googledrive_dir        Google Drive directory
     * @param   int     $id_ntbr_googledrive    Google Drive ID
     *
     * @return  String                      The tree of Google Drive directories
     */
    public function getGoogledriveTree($googledrive_dir, $id_ntbr_googledrive)
    {
        $googledrive    = new Googledrive($id_ntbr_googledrive);
        $parent_name    = $this->l('Home', self::PAGE);

        $this->smarty->assign(array(
            'config_id'         => $googledrive->id_ntbr_config,
            'level'             => 1,
            'id_parent'         => self::GOOGLEDRIVE_ROOT_ID,
            'path'              => $parent_name,
            'parent_name'       => $parent_name,
            'googledrive_dir'   => $googledrive_dir,
        ));

        return $this->display(
            _PS_MODULE_DIR_.$this->name.'/'.$this->name.'.php',
            $this->template_path.'googledrive_tree.tpl'
        );
    }

    /**
     * Get a list of all children directories of the parent directory of the Google Drive account
     *
     * @param   String  $access_token           Google Drive token
     * @param   String  $id_parent              Google Drive ID parent directory
     * @param   String  $googledrive_dir        Google Drive ID selected directory
     * @param   integer $level                  Google Drive level of the directories
     * @param   String  $parent_path            Google Drive path parent directory
     * @param   integer $id_ntbr_config         Config ID
     *
     * @return  String                      The children directories tree of the given parent directory
     */
    public function getGoogledriveTreeChildren(
        $access_token,
        $id_parent,
        $googledrive_dir,
        $level,
        $parent_path,
        $id_ntbr_config
    ) {
        $googledrive_lib    = $this->connectToGoogledrive($access_token);
        $level++;

        $this->smarty->assign(array(
            'children'          => $googledrive_lib->getChildrenTree($id_parent),
            'level'             => $level,
            'config_id'         => $id_ntbr_config,
            'googledrive_dir'   => $googledrive_dir,
            'parent_path'       => $parent_path,
        ));

        return $this->display(
            _PS_MODULE_DIR_.$this->name.'/'.$this->name.'.php',
            $this->template_path.'googledrive_tree_children.tpl'
        );
    }

    /**
     * Get a list of all directories of the OneDrive account
     *
     * @param   String      $access_token       OneDrive token
     * @param   String      $onedrive_dir       OneDrive ID selected directory
     * @param   integer     $id_ntbr_onedrive   OneDrive account ID
     *
     * @return  String                          The tree of OneDrive directories
     */
    public function getOnedriveTree($access_token, $onedrive_dir, $id_ntbr_onedrive)
    {
        $onedrive_lib   = $this->connectToOnedrive($access_token, $id_ntbr_onedrive);
        $onedrive       = new Onedrive($id_ntbr_onedrive);
        $parent_name    = $this->l('Home', self::PAGE);

        $this->smarty->assign(array(
            'level'         => 1,
            'config_id'     => $onedrive->id_ntbr_config,
            'id_parent'     => $onedrive_lib->getRootID(),
            'path'          => $parent_name,
            'parent_name'   => $parent_name,
            'onedrive_dir'  => $onedrive_dir,
        ));

        return $this->display(
            _PS_MODULE_DIR_.$this->name.'/'.$this->name.'.php',
            $this->template_path.'onedrive_tree.tpl'
        );
    }

    /**
     * Get a list of all children directories of the parent directory of the OneDrive account
     *
     * @param   String      $access_token       OneDrive token
     * @param   String      $onedrive_dir       OneDrive ID selected directory
     * @param   String      $id_parent          OneDrive ID parent directory
     * @param   integer     $level              OneDrive level of the directories
     * @param   String      $parent_path        OneDrive path parent directory
     * @param   integer     $id_ntbr_onedrive   OneDrive account ID
     *
     * @return  String                          The children directories tree of the given parent directory
     */
    public function getOnedriveTreeChildren(
        $access_token,
        $onedrive_dir,
        $id_parent,
        $level,
        $parent_path,
        $id_ntbr_onedrive
    ) {
        $display_tree   = false; // We add the list only if there is at least one folder
        $onedrive_lib   = $this->connectToOnedrive($access_token, $id_ntbr_onedrive);
        $onedrive       = new Onedrive($id_ntbr_onedrive);
        $children       = $onedrive_lib->getListChildren($id_parent);
        $level++;


        if ($children === false) {
            return '';
        }

        foreach ($children as $child) {
            if ($child['is_folder']) {
                $display_tree = true;
            }
        }

        if (!$display_tree) {
            return '';
        }

        $this->smarty->assign(array(
            'children'      => $children,
            'level'         => $level,
            'config_id'     => $onedrive->id_ntbr_config,
            'onedrive_dir'  => $onedrive_dir,
            'parent_path'   => $parent_path,
        ));

        return $this->display(
            _PS_MODULE_DIR_.$this->name.'/'.$this->name.'.php',
            $this->template_path.'onedrive_tree_children.tpl'
        );
    }

    /**
     * Get a list of all directories of the SugarSync account
     *
     * @param   String      $access_token       SugarSync token
     * @param   String      $sugarsync_dir      SugarSync ID selected directory
     * @param   integer     $id_ntbr_sugarsync  SugarSync account ID
     *
     * @return  String                          The tree of SugarSync directories
     */
    public function getSugarsyncTree($access_token, $sugarsync_dir, $id_ntbr_sugarsync)
    {
        $sugarsync_lib  = $this->connectToSugarsync($access_token, $id_ntbr_sugarsync);
        $sugarsync      = new Sugarsync($id_ntbr_sugarsync);
        $root           = $sugarsync_lib->getRoot();
        $parent_name    = $root['displayName'];

        $this->smarty->assign(array(
            'level'         => 1,
            'config_id'     => $sugarsync->id_ntbr_config,
            'id_parent'     => basename($root['ref']),
            'path'          => $parent_name,
            'parent_name'   => $parent_name,
            'sugarsync_dir' => $sugarsync_dir,
        ));

        return $this->display(
            _PS_MODULE_DIR_.$this->name.'/'.$this->name.'.php',
            $this->template_path.'sugarsync_tree.tpl'
        );
    }

    /**
     * Get a list of all children directories of the parent directory of the SugarSync account
     *
     * @param   String      $access_token       SugarSync token
     * @param   String      $sugarsync_dir      SugarSync ID selected directory
     * @param   String      $id_parent          SugarSync ID parent directory
     * @param   integer     $level              SugarSync level of the directories
     * @param   String      $parent_path        SugarSync path parent directory
     * @param   integer     $id_ntbr_sugarsync  SugarSync account ID
     *
     * @return  String                          The children directories tree of the given parent directory
     */
    public function getSugarsyncTreeChildren(
        $access_token,
        $sugarsync_dir,
        $id_parent,
        $level,
        $parent_path,
        $id_ntbr_sugarsync
    ) {
        $sugarsync_lib  = $this->connectToSugarsync($access_token, $id_ntbr_sugarsync);
        $sugarsync      = new Sugarsync($id_ntbr_sugarsync);
        $children       = $sugarsync_lib->getDirectoryChildren($id_parent);
        $level++;

        foreach ($children as &$child) {
            $child['id']    = basename($child['ref']);
        }

        $this->smarty->assign(array(
            'children'      => $children,
            'level'         => $level,
            'config_id'     => $sugarsync->id_ntbr_config,
            'parent_path'   => $parent_path,
            'sugarsync_dir' => $sugarsync_dir,
        ));

        return $this->display(
            _PS_MODULE_DIR_.$this->name.'/'.$this->name.'.php',
            $this->template_path.'sugarsync_tree_children.tpl'
        );
    }

    /**
     * Get a list of all directories of the AWS account
     *
     * @param   integer     $id_ntbr_aws    AWS account ID
     *
     * @return  String                      The tree of AWS directories
     */
    public function getAwsTree($id_ntbr_aws)
    {
        // Root level (bucket)
        $aws    = new Aws($id_ntbr_aws);

        $this->smarty->assign(array(
            'level'         => 1,
            'config_id'     => $aws->id_ntbr_config,
            'aws'           => $aws,
        ));

        return $this->display(
            _PS_MODULE_DIR_.$this->name.'/'.$this->name.'.php',
            $this->template_path.'aws_tree.tpl'
        );
    }

    /**
     * Get a list of all children directories of the parent directory of the AWS account
     *
     * @param   String      $id_parent      AWS name parent directory
     * @param   integer     $level          AWS level of the directories
     * @param   String      $parent_path    AWS path parent directory
     * @param   integer     $id_ntbr_aws    AWS account ID
     *
     * @return  String                      The children directories tree of the given parent directory
     */
    public function getAwsTreeChildren($id_parent, $level, $parent_path, $id_ntbr_aws)
    {
        $aws            = new Aws($id_ntbr_aws);
        $aws_lib        = $this->connectToAws(
            $this->decrypt($aws->access_key_id),
            $this->decrypt($aws->secret_access_key),
            $aws->region,
            $aws->bucket
        );

        $level++;

        if ($id_parent == $aws->bucket) {
            // Only directory must be use as parent. No need if it is the bucket
            $children = $aws_lib->getListDirectories();
        } else {
            $children = $aws_lib->getListDirectories($id_parent);
        }

        if ($children === false || !count($children)) {
            return '';
        }

        $this->smarty->assign(array(
            'children'      => $children,
            'level'         => $level,
            'config_id'     => $aws->id_ntbr_config,
            'parent_path'   => $parent_path,
            'aws'           => $aws,
        ));

        return $this->display(
            _PS_MODULE_DIR_.$this->name.'/'.$this->name.'.php',
            $this->template_path.'aws_tree_children.tpl'
        );
    }

    /**
     * Send backup away
     */
    protected function sendBackupAway()
    {
        if ($this->config->create_on_distant
            && ($this->next_step == self::STEP_GET_FUTUR_TAR_SIZE
            || $this->next_step == self::STEP_GET_FUTUR_TAR_SIZE_CONTINUE)
        ) {
            if (!$this->getFuturTarTotalSize()) {
                $this->log(
                    'WAR'
                    .$this->l('Unable to send backup file to distant account. The tar size cannot be found', self::PAGE)
                );

                $this->next_step    = self::STEP_FINISH;
            } else {
                $this->next_step    = self::STEP_SEND_AWAY;
            }

            $this->secondary_next_step      = self::SECONDARY_STEP_TAR_FILE;
            $this->files_done               = 0;
            $this->position_file_list_file  = 0;
            $this->part_number              = 1;
            $this->old_percent              = 0;
            $this->distant_tar_content      = '';
            $this->distant_tar_content_size = 0;
        }

        if ($this->next_step == self::STEP_SEND_AWAY) {
            $this->next_step = $this->step_send['ftp'];
        }

        $this->total_nb_part = count($this->part_list);

        //d($this->total_size);

        if ($this->next_step == $this->step_send['ftp'] || $this->next_step == $this->step_send['ftp_resume']) {
            // Get all ftp accounts
            $ftp_accounts = Ftp::getListActiveFtpAccounts($this->config->id);

            if (count($ftp_accounts)) {
                foreach ($ftp_accounts as $ftp_account) {
                    $this->checkStopScript();

                    if (!$this->ftp_account_id) {
                        // If we have no account id save we save the current ftp account id
                        $this->ftp_account_id = $ftp_account['id_ntbr_ftp'];
                    } elseif ($this->ftp_account_id != $ftp_account['id_ntbr_ftp']) {
                        // If we have an id save we need to find the right account to pursue the sending to that account
                        continue;
                    }

                    if ($ftp_account['sftp']) {
                        //Send backup to SFTP account
                        if ($this->next_step == $this->step_send['ftp']
                            && (!isset($this->ftp_nb_part) || $this->ftp_nb_part == 1)
                        ) {
                            $this->log(
                                sprintf($this->l('Sending backup to %s account', self::PAGE), self::SFTP)
                                .' '.$ftp_account['name'].'...'
                            );
                        }

                        if ($this->config->create_on_distant) {
                            $sftp_success = $this->createTarOnSFTP();
                        } else {
                            $sftp_success = $this->sendFileToSFTP();
                        }

                        $this->secondary_next_step      = self::SECONDARY_STEP_TAR_FILE;
                        $this->files_done               = 0;
                        $this->position_file_list_file  = 0;
                        $this->part_number              = 1;
                        $this->old_percent              = 0;
                        $this->distant_tar_content      = '';
                        $this->distant_tar_content_size = 0;

                        if ($sftp_success) {
                            $this->log(
                                sprintf($this->l('Sending to %s account: Finish', self::PAGE), self::SFTP)
                            );
                            $this->send_away_success = 1;
                        } else {
                            $this->log(
                                'WAR'
                                .sprintf($this->l('Unable to send backup file to %s account', self::PAGE), self::SFTP)
                                .' '.$ftp_account['name']
                            );
                        }
                    } else {
                        //Send backup to FTP account
                        if ($this->next_step == $this->step_send['ftp']
                            && (!isset($this->ftp_nb_part) || $this->ftp_nb_part == 1)
                        ) {
                            $this->log(
                                sprintf($this->l('Sending backup to %s account', self::PAGE), self::FTP)
                                .' '.$ftp_account['name'].'...'
                            );
                        }

                        if ($this->config->create_on_distant) {
                            $this->log('WAR'.sprintf($this->l('The option to create a backup file directly in distant account is not available with %s account', self::PAGE), self::FTP));
                        } else {
                            $ftp_success = $this->sendFileToFTP();
                        }

                        $this->secondary_next_step      = self::SECONDARY_STEP_TAR_FILE;
                        $this->files_done               = 0;
                        $this->position_file_list_file  = 0;
                        $this->part_number              = 1;
                        $this->old_percent              = 0;
                        $this->distant_tar_content      = '';
                        $this->distant_tar_content_size = 0;

                        if ($ftp_success) {
                            $this->log(
                                sprintf($this->l('Sending to %s account: Finish', self::PAGE), self::FTP)
                            );
                            $this->send_away_success = 1;
                        } else {
                            $this->log(
                                'WAR'
                                .sprintf($this->l('Unable to send backup file to %s account', self::PAGE), self::FTP)
                                .' '.$ftp_account['name']
                            );
                        }
                    }

                    // We reset the id so that we will save the id in the next iteration of the loop
                    $this->ftp_account_id = 0;
                    // Next step is to send to a new ftp (if there is still one)
                    $this->next_step = $this->step_send['ftp'];
                }

                // If we send to all ftp account, then we can go to the next step
                $this->next_step = $this->step_send['dropbox'];

                //refresh
                $this->refreshBackup();
            } else {
                $this->next_step = $this->step_send['dropbox'];
            }
        }

        if ($this->next_step == $this->step_send['dropbox'] || $this->next_step == $this->step_send['dropbox_resume']) {
            // Get all dropbox accounts
            $dropbox_accounts = Dropbox::getListActiveDropboxAccounts($this->config->id);

            if (count($dropbox_accounts)) {
                foreach ($dropbox_accounts as $dropbox_account) {
                    $this->checkStopScript();

                    if (!$this->dropbox_account_id) {
                        // If we have no account id save we save the current dropbox account id
                        $this->dropbox_account_id = $dropbox_account['id_ntbr_dropbox'];
                    } elseif ($this->dropbox_account_id != $dropbox_account['id_ntbr_dropbox']) {
                        // If we have an id save we need to find the right account to pursue the sending to that account
                        continue;
                    }

                    //Send backup to Dropbox account
                    if ($this->next_step == $this->step_send['dropbox']
                        && (!isset($this->dropbox_nb_part) || $this->dropbox_nb_part == 1)
                    ) {
                        $this->log(
                            sprintf($this->l('Sending backup to %s account', self::PAGE), self::DROPBOX)
                            .' '.$dropbox_account['name'].'...'
                        );
                    }

                    if ($this->config->create_on_distant) {
                        $dropbox_success = $this->createTarOnDropbox();
                    } else {
                        $dropbox_success = $this->sendFileToDropbox();
                    }

                    $this->secondary_next_step      = self::SECONDARY_STEP_TAR_FILE;
                    $this->files_done               = 0;
                    $this->position_file_list_file  = 0;
                    $this->part_number              = 1;
                    $this->old_percent              = 0;
                    $this->distant_tar_content      = '';
                    $this->distant_tar_content_size = 0;

                    if ($dropbox_success) {
                        $this->log(
                            sprintf($this->l('Sending to %s account: Finish', self::PAGE), self::DROPBOX)
                        );
                        $this->send_away_success = 1;
                    } else {
                        $this->log(
                            'WAR'
                            .sprintf($this->l('Unable to send backup file to %s account', self::PAGE), self::DROPBOX)
                            .' '.$dropbox_account['name']
                        );
                    }

                    // We reset the id so that we will save the id in the next iteration of the loop
                    $this->dropbox_account_id = 0;

                    // Next step is to send to a new dropbox (if there is still one)
                    $this->next_step = $this->step_send['dropbox'];
                }

                // If we send to all dropbox account, then we can go to the next step
                $this->next_step = $this->step_send['owncloud'];
                //refresh
                $this->refreshBackup();
            } else {
                $this->next_step = $this->step_send['owncloud'];
            }
        }

        if ($this->next_step == $this->step_send['owncloud']
            || $this->next_step == $this->step_send['owncloud_resume']
        ) {
            // Get all ownCloud accounts
            $owncloud_accounts = Owncloud::getListActiveOwncloudAccounts($this->config->id);

            if (count($owncloud_accounts)) {
                foreach ($owncloud_accounts as $owncloud_account) {
                    $this->checkStopScript();

                    if (!$this->owncloud_account_id) {
                        // If we have no account id save we save the current ownCloud account id
                        $this->owncloud_account_id = $owncloud_account['id_ntbr_owncloud'];
                    } elseif ($this->owncloud_account_id != $owncloud_account['id_ntbr_owncloud']) {
                        // If we have an id save we need to find the right account to pursue the sending to that account
                        continue;
                    }

                    //Send backup to ownCloud account
                    if ($this->next_step == $this->step_send['owncloud']
                        && (!isset($this->owncloud_nb_part) || $this->owncloud_nb_part == 1)
                    ) {
                        $this->log(
                            sprintf($this->l('Sending backup to %s account', self::PAGE), self::OWNCLOUD)
                            .' '.$owncloud_account['name'].'...'
                        );
                    }

                    if ($this->config->create_on_distant) {
                        $owncloud_success = $this->createTarOnOwncloud();
                    } else {
                        $owncloud_success = $this->sendFileToOwncloud();
                    }

                    $this->secondary_next_step      = self::SECONDARY_STEP_TAR_FILE;
                    $this->files_done               = 0;
                    $this->position_file_list_file  = 0;
                    $this->part_number              = 1;
                    $this->old_percent              = 0;
                    $this->distant_tar_content      = '';
                    $this->distant_tar_content_size = 0;

                    if ($owncloud_success) {
                        $this->log(
                            sprintf($this->l('Sending to %s account: Finish', self::PAGE), self::OWNCLOUD)
                        );
                        $this->send_away_success = 1;
                    } else {
                        $this->log(
                            'WAR'
                            .sprintf(
                                $this->l('Unable to send backup file to %s account', self::PAGE),
                                self::OWNCLOUD
                            )
                            .' '.$owncloud_account['name']
                        );
                    }

                    // We reset the id so that we will save the id in the next iteration of the loop
                    $this->owncloud_account_id = 0;
                    // Next step is to send to a new ownCloud (if there is still one)
                    $this->next_step = $this->step_send['owncloud'];
                }

                // If we send to all ownCloud account, then we can go to the next step
                $this->next_step = $this->step_send['webdav'];

                //refresh
                $this->refreshBackup();
            } else {
                $this->next_step = $this->step_send['webdav'];
            }
        }

        if ($this->next_step == $this->step_send['webdav'] || $this->next_step == $this->step_send['webdav_resume']) {
            // Get all WebDAV accounts
            $webdav_accounts = Webdav::getListActiveWebdavAccounts($this->config->id);

            if (count($webdav_accounts)) {
                foreach ($webdav_accounts as $webdav_account) {
                    $this->checkStopScript();

                    if (!$this->webdav_account_id) {
                        // If we have no account id save we save the current WebDAV account id
                        $this->webdav_account_id = $webdav_account['id_ntbr_webdav'];
                    } elseif ($this->webdav_account_id != $webdav_account['id_ntbr_webdav']) {
                        // If we have an id save we need to find the right account to pursue the sending to that account
                        continue;
                    }

                    //Send backup to WebDAV account
                    if ($this->next_step == $this->step_send['webdav']
                        && (!isset($this->webdav_nb_part) || $this->webdav_nb_part == 1)
                    ) {
                        $this->log(
                            sprintf($this->l('Sending backup to %s account', self::PAGE), self::WEBDAV)
                            .' '.$webdav_account['name'].'...'
                        );
                    }

                    if ($this->config->create_on_distant) {
                        $webdav_success = $this->createTarOnWebdav();
                    } else {
                        $webdav_success = $this->sendFileToWebdav();
                    }

                    $this->secondary_next_step      = self::SECONDARY_STEP_TAR_FILE;
                    $this->files_done               = 0;
                    $this->position_file_list_file  = 0;
                    $this->part_number              = 1;
                    $this->old_percent              = 0;
                    $this->distant_tar_content      = '';
                    $this->distant_tar_content_size = 0;

                    if ($webdav_success) {
                        $this->log(
                            sprintf($this->l('Sending to %s account: Finish', self::PAGE), self::WEBDAV)
                        );
                        $this->send_away_success = 1;
                    } else {
                        $this->log(
                            'WAR'
                            .sprintf($this->l('Unable to send backup file to %s account', self::PAGE), self::WEBDAV)
                            .' '.$webdav_account['name']
                        );
                    }

                    // We reset the id so that we will save the id in the next iteration of the loop
                    $this->webdav_account_id = 0;
                    // Next step is to send to a new WebDAV (if there is still one)
                    $this->next_step = $this->step_send['webdav'];
                }

                // If we send to all WebDAV account, then we can go to the next step
                $this->next_step = $this->step_send['googledrive'];

                //refresh
                $this->refreshBackup();
            } else {
                $this->next_step = $this->step_send['googledrive'];
            }
        }

        if ($this->next_step == $this->step_send['googledrive']
            || $this->next_step == $this->step_send['googledrive_resume']
        ) {
            // Get all Google Drive accounts
            $googledrive_accounts = Googledrive::getListActiveGoogledriveAccounts($this->config->id);

            if (count($googledrive_accounts)) {
                foreach ($googledrive_accounts as $googledrive_account) {
                    $this->checkStopScript();

                    if (!$this->googledrive_account_id) {
                        // If we have no account id save we save the current Google Drive account id
                        $this->googledrive_account_id = $googledrive_account['id_ntbr_googledrive'];
                    } elseif ($this->googledrive_account_id != $googledrive_account['id_ntbr_googledrive']) {
                        // If we have an id save we need to find the right account to pursue the sending to that account
                        continue;
                    }

                    //Send backup to Google Drive account
                    if ($this->next_step == $this->step_send['googledrive']
                        && (!isset($this->googledrive_nb_part) || $this->googledrive_nb_part == 1)
                    ) {
                        if ($this->config->create_on_distant) {
                            $this->log(
                                sprintf($this->l('Creating backup on %s account', self::PAGE), self::GOOGLEDRIVE)
                                .' '.$googledrive_account['name'].'...'
                            );
                        } else {
                            $this->log(
                                sprintf($this->l('Sending backup to %s account', self::PAGE), self::GOOGLEDRIVE)
                                .' '.$googledrive_account['name'].'...'
                            );
                        }
                    }

                    if ($this->config->create_on_distant) {
                        $googledrive_success = $this->createTarOnGoogledrive();
                    } else {
                        $googledrive_success = $this->sendFileToGoogledrive();
                    }

                    $this->secondary_next_step      = self::SECONDARY_STEP_TAR_FILE;
                    $this->files_done               = 0;
                    $this->position_file_list_file  = 0;
                    $this->part_number              = 1;
                    $this->old_percent              = 0;
                    $this->distant_tar_content      = '';
                    $this->distant_tar_content_size = 0;

                    if ($googledrive_success) {
                        $this->log(
                            sprintf($this->l('Sending to %s account: Finish', self::PAGE), self::GOOGLEDRIVE)
                        );

                        $this->send_away_success = 1;
                    } else {
                        $this->log(
                            'WAR'
                            .sprintf(
                                $this->l('Unable to send backup file to %s account', self::PAGE),
                                self::GOOGLEDRIVE
                            )
                            .' '.$googledrive_account['name']
                        );
                    }

                    // We reset the id so that we will save the id in the next iteration of the loop
                    $this->googledrive_account_id = 0;
                    // Next step is to send to a new Google Drive (if there is still one)
                    $this->next_step = $this->step_send['googledrive'];
                }

                // If we send to all Google Drive account, then we can go to the next step
                $this->next_step = $this->step_send['onedrive'];

                //refresh
                $this->refreshBackup();
            } else {
                $this->next_step = $this->step_send['onedrive'];
            }
        }

        if ($this->next_step == $this->step_send['onedrive']
            || $this->next_step == $this->step_send['onedrive_resume']
        ) {
            // Get all OneDrive accounts
            $onedrive_accounts = Onedrive::getListActiveOnedriveAccounts($this->config->id);

            if (count($onedrive_accounts)) {
                foreach ($onedrive_accounts as $onedrive_account) {
                    $this->checkStopScript();

                    if (!$this->onedrive_account_id) {
                        // If we have no account id save we save the current OneDrive account id
                        $this->onedrive_account_id = $onedrive_account['id_ntbr_onedrive'];
                    } elseif ($this->onedrive_account_id != $onedrive_account['id_ntbr_onedrive']) {
                        // If we have an id save we need to find the right account to pursue the sending to that account
                        continue;
                    }

                    //Send backup to OneDrive account
                    if ($this->next_step == $this->step_send['onedrive']
                        && (!isset($this->onedrive_nb_part) || $this->onedrive_nb_part == 1)
                    ) {
                        $this->log(
                            sprintf($this->l('Sending backup to %s account', self::PAGE), self::ONEDRIVE)
                            .' '.$onedrive_account['name'].'...'
                        );
                    }

                    if ($this->config->create_on_distant) {
                        $onedrive_success = $this->createTarOnOnedrive();
                    } else {
                        $onedrive_success = $this->sendFileToOnedrive();
                    }

                    $this->secondary_next_step      = self::SECONDARY_STEP_TAR_FILE;
                    $this->files_done               = 0;
                    $this->position_file_list_file  = 0;
                    $this->part_number              = 1;
                    $this->old_percent              = 0;
                    $this->distant_tar_content      = '';
                    $this->distant_tar_content_size = 0;

                    if ($onedrive_success) {
                        $this->log(
                            sprintf($this->l('Sending to %s account: Finish', self::PAGE), self::ONEDRIVE)
                        );
                        $this->send_away_success = 1;
                    } else {
                        $this->log(
                            'WAR'
                            .sprintf($this->l('Unable to send backup file to %s account', self::PAGE), self::ONEDRIVE)
                            .' '.$onedrive_account['name']
                        );
                    }

                    // We reset the id so that we will save the id in the next iteration of the loop
                    $this->onedrive_account_id = 0;
                    // Next step is to send to a new OneDrive (if there is still one)
                    $this->next_step = $this->step_send['onedrive'];
                }

                // If we send to all OneDrive account, then we can go to the next step
                $this->next_step = $this->step_send['hubic'];

                //refresh
                $this->refreshBackup();
            } else {
                $this->next_step = $this->step_send['hubic'];
            }
        }

        if ($this->next_step == $this->step_send['hubic'] || $this->next_step == $this->step_send['hubic_resume']) {
            // Get all hubiC accounts
            $hubic_accounts = Hubic::getListActiveHubicAccounts($this->config->id);

            if (count($hubic_accounts)) {
                foreach ($hubic_accounts as $hubic_account) {
                    $this->checkStopScript();

                    if (!$this->hubic_account_id) {
                        // If we have no account id save we save the current hubiC account id
                        $this->hubic_account_id = $hubic_account['id_ntbr_hubic'];
                    } elseif ($this->hubic_account_id != $hubic_account['id_ntbr_hubic']) {
                        // If we have an id save we need to find the right account to pursue the sending to that account
                        continue;
                    }

                    //Send backup to hubiC account
                    if ($this->next_step == $this->step_send['hubic']
                        && (!isset($this->hubic_nb_part) || $this->hubic_nb_part == 1)
                    ) {
                        $this->log(
                            sprintf($this->l('Sending backup to %s account', self::PAGE), self::HUBIC)
                            .' '.$hubic_account['name'].'...'
                        );
                    }

                    if ($this->config->create_on_distant) {
                        $hubic_success = $this->createTarOnHubic();
                    } else {
                        $hubic_success = $this->sendFileToHubic();
                    }

                    $this->secondary_next_step      = self::SECONDARY_STEP_TAR_FILE;
                    $this->files_done               = 0;
                    $this->position_file_list_file  = 0;
                    $this->part_number              = 1;
                    $this->old_percent              = 0;
                    $this->distant_tar_content      = '';
                    $this->distant_tar_content_size = 0;

                    if ($hubic_success) {
                        $this->log(
                            sprintf($this->l('Sending to %s account: Finish', self::PAGE), self::HUBIC)
                        );
                        $this->send_away_success = 1;
                    } else {
                        $this->log(
                            'WAR'
                            .sprintf($this->l('Unable to send backup file to %s account', self::PAGE), self::HUBIC)
                            .' '.$hubic_account['name']
                        );
                    }

                    // We reset the id so that we will save the id in the next iteration of the loop
                    $this->hubic_account_id = 0;
                    // Next step is to send to a new hubiC (if there is still one)
                    $this->next_step = $this->step_send['hubic'];
                }

                // If we send to all hubic account, then we can go to the next step
                $this->next_step = $this->step_send['aws'];

                //refresh
                $this->refreshBackup();
            } else {
                $this->next_step = $this->step_send['aws'];
            }
        }

        if ($this->next_step == $this->step_send['aws']
            || $this->next_step == $this->step_send['aws_resume']
        ) {
            // Get all AWS accounts
            $aws_accounts = Aws::getListActiveAwsAccounts($this->config->id);

            if (count($aws_accounts)) {
                foreach ($aws_accounts as $aws_account) {
                    $this->checkStopScript();

                    if (!$this->aws_account_id) {
                        // If we have no account id save we save the current AWS account id
                        $this->aws_account_id = $aws_account['id_ntbr_aws'];
                    } elseif ($this->aws_account_id != $aws_account['id_ntbr_aws']) {
                        // If we have an id save we need to find the right account to pursue the sending to that account
                        continue;
                    }

                    //Send backup to AWS account
                    if ($this->next_step == $this->step_send['aws']
                        && (!isset($this->aws_nb_part) || $this->aws_nb_part == 1)
                    ) {
                        $this->log(
                            sprintf($this->l('Sending backup to %s account', self::PAGE), self::AWS)
                            .' '.$aws_account['name'].'...'
                        );
                    }

                    if ($this->config->create_on_distant) {
                        $aws_success = $this->createTarOnAws();
                    } else {
                        $aws_success = $this->sendFileToAws();
                    }

                    $this->secondary_next_step      = self::SECONDARY_STEP_TAR_FILE;
                    $this->files_done               = 0;
                    $this->position_file_list_file  = 0;
                    $this->part_number              = 1;
                    $this->old_percent              = 0;
                    $this->distant_tar_content      = '';
                    $this->distant_tar_content_size = 0;

                    if ($aws_success) {
                        $this->log(
                            sprintf($this->l('Sending to %s account: Finish', self::PAGE), self::AWS)
                        );

                        $this->send_away_success = 1;
                    } else {
                        $this->log(
                            'WAR'
                            .sprintf($this->l('Unable to send backup file to %s account', self::PAGE), self::AWS)
                            .' '.$aws_account['name']
                        );
                    }

                    // We reset the id so that we will save the id in the next iteration of the loop
                    $this->aws_account_id = 0;
                    // Next step is to send to a new AWS (if there is still one)
                    $this->next_step = $this->step_send['aws'];
                }

                // If we send to all AWS account, then we can go to the next step
                $this->next_step = $this->step_send['sugarsync'];

                //refresh
                $this->refreshBackup();
            } else {
                $this->next_step = $this->step_send['sugarsync'];
            }
        }

        if ($this->next_step == $this->step_send['sugarsync']
            || $this->next_step == $this->step_send['sugarsync_resume']
        ) {
            // Get all SugarSync accounts
            $sugarsync_accounts = Sugarsync::getListActiveSugarsyncAccounts($this->config->id);

            if (count($sugarsync_accounts)) {
                foreach ($sugarsync_accounts as $sugarsync_account) {
                    $this->checkStopScript();

                    if (!$this->sugarsync_account_id) {
                        // If we have no account id save we save the current SugarSync account id
                        $this->sugarsync_account_id = $sugarsync_account['id_ntbr_sugarsync'];
                    } elseif ($this->sugarsync_account_id != $sugarsync_account['id_ntbr_sugarsync']) {
                        // If we have an id save we need to find the right account to pursue the sending to that account
                        continue;
                    }

                    //Send backup to SugarSync account
                    if ($this->next_step == $this->step_send['sugarsync']
                        && (!isset($this->sugarsync_nb_part) || $this->sugarsync_nb_part == 1)
                    ) {
                        $this->log(
                            sprintf($this->l('Sending backup to %s account', self::PAGE), self::SUGARSYNC)
                            .' '.$sugarsync_account['name'].'...'
                        );
                    }

                    if ($this->config->create_on_distant) {
                        $sugarsync_success = $this->createTarOnSugarsync();
                    } else {
                        $sugarsync_success = $this->sendFileToSugarsync();
                    }

                    $this->secondary_next_step      = self::SECONDARY_STEP_TAR_FILE;
                    $this->files_done               = 0;
                    $this->position_file_list_file  = 0;
                    $this->part_number              = 1;
                    $this->old_percent              = 0;
                    $this->distant_tar_content      = '';
                    $this->distant_tar_content_size = 0;

                    if ($sugarsync_success) {
                        $this->log(
                            sprintf($this->l('Sending to %s account: Finish', self::PAGE), self::SUGARSYNC)
                        );
                        $this->send_away_success = 1;
                    } else {
                        $this->log(
                            'WAR'
                            .sprintf($this->l('Unable to send backup file to %s account', self::PAGE), self::SUGARSYNC)
                            .' '.$sugarsync_account['name']
                        );
                    }

                    // We reset the id so that we will save the id in the next iteration of the loop
                    $this->sugarsync_account_id = 0;
                    // Next step is to send to a new SugarSync (if there is still one)
                    $this->next_step = $this->step_send['sugarsync'];
                }

                // If we send to all SugarSync account, then we can go to the next step
                $this->next_step = self::STEP_FINISH;

                //refresh
                $this->refreshBackup();
            } else {
                $this->next_step = self::STEP_FINISH;
            }
        }

        if ($this->config->create_on_distant) {
            if (is_resource($this->handle_list_dir_file)) {
                fclose($this->handle_list_dir_file);
            }
            if (is_resource($this->handle_file_list_file)) {
                fclose($this->handle_file_list_file);
            }

            $this->fileDelete($this->dump_file);
            $this->fileDelete($this->list_dir_file);
            $this->fileDelete($this->file_list_file);
        }
    }

    public function saveConfigProfile($is_default, $name, $type)
    {
        $config                 = new Config();
        $config->is_default     = $is_default;
        $config->name           = $name;
        $config->type_backup    = $type;

        $result = array(
            'id_profile'    => 0,
            'success'       => 1,
            'errors'        => array()
        );

        if (!$config->name || $config->name == '') {
            $result['errors'][] = $this->l('The name of the configuration is required.', self::PAGE);
        }

        if (!$config->type_backup || !in_array($config->type_backup, array(
            $this->type_backup_complete,
            $this->type_backup_file,
            $this->type_backup_base
        ))) {
            $result['errors'][] = $this->l('The type of the configuration is no valid', self::PAGE);
        }

        if (count($result['errors'])) {
            $result['success'] = 0;
            return $result;
        }

        $config->add();

        $result['id_profile'] = $config->id;

        return $result;
    }

    public function deleteConfig($id_ntbr_config)
    {
        $config = new Config($id_ntbr_config);
        return (int)$config->delete();
    }

    public function saveConfig(
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
    ) {
        $config->send_restore           = $send_restore;
        $config->activate_xsendfile     = $activate_xsendfile;
        $config->ignore_product_image   = $ignore_product_image;
        $config->ignore_compression     = $ignore_compression;
        $config->delete_local_backup    = $delete_local_backup;
        $config->create_on_distant      = $create_on_distant;
        $config->backup_dir             = $backup_dir;
        $config->ignore_directories     = $ignore_directories;
        $config->ignore_file_types      = $ignore_file_types;
        $config->ignore_tables          = $ignore_tables;

        $this->setConfig('NTBR_MULTI_CONFIG', $multi_config, $id_shop_group, $id_shop);

        if (!$this->getConfig('NTBR_MULTI_CONFIG', $id_shop_group, $id_shop) == $multi_config) {
            return false;
        }

        return $config;
    }

    public function displayFtpAccount($id_ntbr_ftp)
    {
        return Ftp::getFtpAccountById($id_ntbr_ftp);
    }

    public function displayDropboxAccount($id_ntbr_dropbox)
    {
        return Dropbox::getDropboxAccountById($id_ntbr_dropbox);
    }

    public function displayOwncloudAccount($id_ntbr_owncloud)
    {
        $owncloud_account   = Owncloud::getOwncloudAccountById($id_ntbr_owncloud);

        return $owncloud_account;
    }

    public function displayWebdavAccount($id_ntbr_webdav)
    {
        $webdav_account = Webdav::getWebdavAccountById($id_ntbr_webdav);

        return $webdav_account;
    }

    public function displayGoogledriveAccount($id_ntbr_googledrive)
    {
        return Googledrive::getGoogledriveAccountById($id_ntbr_googledrive);
    }

    public function displayOnedriveAccount($id_ntbr_onedrive)
    {
        return Onedrive::getOnedriveAccountById($id_ntbr_onedrive);
    }

    public function displaySugarsyncAccount($id_ntbr_sugarsync)
    {
        return Sugarsync::getSugarsyncAccountById($id_ntbr_sugarsync);
    }

    public function displayHubicAccount($id_ntbr_hubic)
    {
        return Hubic::getHubicAccountById($id_ntbr_hubic);
    }

    public function displayAwsAccount($id_ntbr_aws)
    {
        return Aws::getAwsAccountById($id_ntbr_aws);
    }

    public function saveFtp(
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
    ) {
        $result = array(
            'success'       => 1,
            'errors'        => array(),
            'id_ntbr_ftp'   => 0
        );

        // Required data
        if (!$name || trim($name) == '') {
            $result['errors'][] = $this->l('The name is required.', self::PAGE);
        }

        if (!$server || trim($server) == '') {
            $result['errors'][] = $this->l('The server is required.', self::PAGE);
        }

        if (!$login || trim($login) == '') {
            $result['errors'][] = $this->l('The login is required.', self::PAGE);
        }

        if ($id_ntbr_ftp) {
            $ftp = new Ftp($id_ntbr_ftp);

            if (!$password || trim($password) == '' || $password == self::FAKE_MDP) {
                $password = $this->decrypt($ftp->password);
            }
        } else {
            $ftp = new Ftp();
        }

        if (!$password || trim($password) == '') {
            $result['errors'][] = $this->l('The password is required.', self::PAGE);
        }

        // Data validity
        if (!Validate::isGenericName($name)) {
            $result['errors'][] = $this->l('The account name is not valid. Please do not use those characters', self::PAGE).' "<>={}"';
        } else {
            $name_exists_id = Ftp::getIdByName($id_ntbr_config, $name);

            if ($name_exists_id && $id_ntbr_ftp != $name_exists_id) {
                $result['errors'][] = $this->l('The account name is already used', self::PAGE);
            }
        }

        if ($sftp && $ssl) {
            $result['errors'][] = sprintf($this->l('%s cannot use SSL', self::PAGE), self::SFTP);
        }

        if ($sftp && $passive_mode) {
            $result['errors'][] = sprintf($this->l('%s cannot use passive mode', self::PAGE), self::SFTP);
        }

        if (count($result['errors'])) {
            $result['success'] = 0;
            return $result;
        }

        // Check connection
        if ($sftp) {
            // Connect with SFTP
            if ($active) {
                if (!$this->testSFTP($server, $login, $password, $port)) {
                    $result['errors'][] = sprintf(
                        $this->l('Unable to connect to your %s account', self::PAGE),
                        self::SFTP
                    );
                }
            }
        } else {
            // Connect with FTP
            if ($active) {
                if (!$this->testFTP($server, $login, $password, $port, $ssl, $passive_mode)) {
                    $result['errors'][] = sprintf(
                        $this->l('Unable to connect to your %s account', self::PAGE),
                        self::FTP
                    );
                }
            }
        }

        if (count($result['errors'])) {
            $result['success'] = 0;
            return $result;
        }

        // Save data
        $ftp->id_ntbr_config    = $id_ntbr_config;
        $ftp->name              = $name;
        $ftp->active            = $active;
        $ftp->sftp              = $sftp;
        $ftp->ssl               = $ssl;
        $ftp->passive_mode      = $passive_mode;
        $ftp->config_nb_backup  = $config_nb_backup;
        $ftp->server            = $server;
        $ftp->login             = $login;
        $ftp->password          = $this->encrypt($password);
        $ftp->port              = $port;
        $ftp->directory         = $directory;

        if (!$ftp->save()) {
            $result['success'] = 0;
        }

        $result['id_ntbr_ftp'] = $ftp->id;

        return $result;
    }

    public function saveDropbox(
        $id_ntbr_config,
        $id_ntbr_dropbox,
        $name,
        $active,
        $config_nb_backup,
        $code,
        $directory
    ) {
        $token  = '';

        $result = array(
            'success'           => 1,
            'errors'            => array(),
            'id_ntbr_dropbox'   => 0
        );

        // Required data
        if (!$name || trim($name) == '') {
            $result['errors'][] = $this->l('The name is required.', self::PAGE);
        }

        // Data validity
        if (!Validate::isGenericName($name) || trim($name) == '') {
            $result['errors'][] = $this->l('The account name is not valid. Please do not use those characters', self::PAGE).' "<>={}"';
        } else {
            $name_exists_id = Dropbox::getIdByName($id_ntbr_config, $name);

            if ($name_exists_id && $id_ntbr_dropbox != $name_exists_id) {
                $result['errors'][] = $this->l('The account name is already used', self::PAGE);
            }
        }

        if (count($result['errors'])) {
            $result['success'] = 0;
            return $result;
        }

        if ($id_ntbr_dropbox) {
            $dropbox = new Dropbox($id_ntbr_dropbox);
        } else {
            $dropbox = new Dropbox();
        }

        // Check connection
        $connection = true;

        if ($code != '' && $code != self::FAKE_MDP) {
            // Get new token
            $token = $this->getDropboxAccessToken($code);
        } else {
            // Get current token
            $token = $this->decrypt(Dropbox::getDropboxTokenById($id_ntbr_dropbox));
            if ($token && $token != '' && $active) {
                $connection = $this->testDropboxConnection($token);
            }
        }

        if (!$token || $token == '') {
            $connection = false;
        }

        if (!$connection && $active) {
            $result['errors'][] = sprintf($this->l('Unable to connect to your %s account', self::PAGE), self::DROPBOX);
        }

        if (count($result['errors'])) {
            $result['success'] = 0;
            return $result;
        }

        $dropbox->id_ntbr_config    = $id_ntbr_config;
        $dropbox->name              = $name;
        $dropbox->active            = $active;
        $dropbox->config_nb_backup  = $config_nb_backup;
        $dropbox->directory         = $directory;
        $dropbox->token             = $this->encrypt($token);

        if (!$dropbox->save()) {
            $result['success'] = 0;
        }

        $result['id_ntbr_dropbox'] = $dropbox->id;

        return $result;
    }

    public function saveOwncloud(
        $id_ntbr_config,
        $id_ntbr_owncloud,
        $name,
        $active,
        $config_nb_backup,
        $login,
        $password,
        $server,
        $directory
    ) {
        $result = array(
            'success'           => 1,
            'errors'            => array(),
            'id_ntbr_owncloud'  => 0
        );

        // Required data
        if (!$name || trim($name) == '') {
            $result['errors'][] = $this->l('The name is required.', self::PAGE);
        }

        if (!$server || trim($server) == '') {
            $result['errors'][] = $this->l('The server is required.', self::PAGE);
        }

        if (!$login || trim($login) == '') {
            $result['errors'][] = $this->l('The login is required.', self::PAGE);
        }

        if ($id_ntbr_owncloud) {
            $owncloud = new Owncloud($id_ntbr_owncloud);

            if (!$password || trim($password) == '' || $password == self::FAKE_MDP) {
                $password = $this->decrypt($owncloud->password);
            }
        } else {
            $owncloud = new Owncloud();
        }

        if (!$password || trim($password) == '') {
            $result['errors'][] = $this->l('The password is required.', self::PAGE);
        }

        // Data validity
        if (!Validate::isGenericName($name) || trim($name) == '') {
            $result['errors'][] = $this->l('The account name is not valid. Please do not use those characters', self::PAGE).' "<>={}"';
        } else {
            $name_exists_id = Owncloud::getIdByName($id_ntbr_config, $name);

            if ($name_exists_id && $id_ntbr_owncloud != $name_exists_id) {
                $result['errors'][] = $this->l('The account name is already used', self::PAGE);
            }
        }

        if (count($result['errors'])) {
            $result['success'] = 0;
            return $result;
        }

        // Check connection (if active)
        if ($active) {
            $connection = $this->testOwncloudConnection($server, $login, $password);

            if (!$connection) {
                $result['errors'][] = sprintf(
                    $this->l('Unable to connect to your %s account', self::PAGE),
                    self::OWNCLOUD
                );
            }
        }

        if (count($result['errors'])) {
            $result['success'] = 0;
            return $result;
        }

        // Save data
        $owncloud->id_ntbr_config   = $id_ntbr_config;
        $owncloud->name             = $name;
        $owncloud->active           = $active;
        $owncloud->config_nb_backup = $config_nb_backup;
        $owncloud->login            = $login;
        $owncloud->password         = $this->encrypt($password);
        $owncloud->server           = $server;
        $owncloud->directory        = $directory;

        if (!$owncloud->save()) {
            $result['success'] = 0;
        }

        $result['id_ntbr_owncloud'] = $owncloud->id;

        return $result;
    }

    public function saveWebdav(
        $id_ntbr_config,
        $id_ntbr_webdav,
        $name,
        $active,
        $config_nb_backup,
        $login,
        $password,
        $server,
        $directory
    ) {
        $result = array(
            'success'           => 1,
            'errors'            => array(),
            'id_ntbr_webdav'    => 0
        );

        // Required data
        if (!$name || trim($name) == '') {
            $result['errors'][] = $this->l('The name is required.', self::PAGE);
        }

        if (!$server || trim($server) == '') {
            $result['errors'][] = $this->l('The URL is required.', self::PAGE);
        }

        if (!$login || trim($login) == '') {
            $result['errors'][] = $this->l('The login is required.', self::PAGE);
        }

        if ($id_ntbr_webdav) {
            $webdav = new Webdav($id_ntbr_webdav);

            if (!$password || trim($password) == ''|| $password == self::FAKE_MDP) {
                $password = $this->decrypt($webdav->password);
            }
        } else {
            $webdav = new Webdav();
        }

        if (!$password || trim($password) == '') {
            $result['errors'][] = $this->l('The password is required.', self::PAGE);
        }

        // Data validity
        if (!Validate::isGenericName($name) || trim($name) == '') {
            $result['errors'][] = $this->l('The account name is not valid. Please do not use those characters', self::PAGE).' "<>={}"';
        } else {
            $name_exists_id = Webdav::getIdByName($id_ntbr_config, $name);

            if ($name_exists_id && $id_ntbr_webdav != $name_exists_id) {
                $result['errors'][] = $this->l('The account name is already used', self::PAGE);
            }
        }

        if (count($result['errors'])) {
            $result['success'] = 0;
            return $result;
        }

        // Check connection
        if ($active) {
            $connection = $this->testWebdavConnection($server, $login, $password);

            if (!$connection) {
                $result['errors'][] = sprintf(
                    $this->l('Unable to connect to your %s account', self::PAGE),
                    self::WEBDAV
                );
            }
        }

        if (count($result['errors'])) {
            $result['success'] = 0;
            return $result;
        }

        // Save data
        $webdav->id_ntbr_config     = $id_ntbr_config;
        $webdav->name               = $name;
        $webdav->active             = $active;
        $webdav->config_nb_backup   = $config_nb_backup;
        $webdav->login              = $login;
        $webdav->password           = $this->encrypt($password);
        $webdav->server             = $server;
        $webdav->directory          = $directory;

        if (!$webdav->save()) {
            $result['success'] = 0;
        }

        $result['id_ntbr_webdav'] = $webdav->id;

        return $result;
    }

    public function saveGoogledrive(
        $id_ntbr_config,
        $id_ntbr_googledrive,
        $name,
        $active,
        $config_nb_backup,
        $code,
        $directory_path,
        $directory_key
    ) {
        $token  = '';

        $result = array(
            'success'               => 1,
            'errors'                => array(),
            'id_ntbr_googledrive'   => 0
        );

        // Required data
        if (!$name || trim($name) == '') {
            $result['errors'][] = $this->l('The name is required.', self::PAGE);
        }

        // Data validity
        if (!Validate::isGenericName($name) || trim($name) == '') {
            $result['errors'][] = $this->l('The account name is not valid. Please do not use those characters', self::PAGE).' "<>={}"';
        } else {
            $name_exists_id = Googledrive::getIdByName($id_ntbr_config, $name);

            if ($name_exists_id && $id_ntbr_googledrive != $name_exists_id) {
                $result['errors'][] = $this->l('The account name is already used', self::PAGE);
            }
        }

        if (count($result['errors'])) {
            $result['success'] = 0;
            return $result;
        }

        if ($id_ntbr_googledrive) {
            $googledrive = new Googledrive($id_ntbr_googledrive);
        } else {
            $googledrive = new Googledrive();
        }

        // Check connection
        $connection = true;

        if ($code != '' && $code != self::FAKE_MDP) {
            // Get new token
            $decode_token = $this->getGoogledriveAccessToken($code);

            $decode_token['access_token']   = $this->encrypt($decode_token['access_token']);
            $decode_token['refresh_token']  = $this->encrypt($decode_token['refresh_token']);

            $token = Tools::jsonEncode($decode_token);
        } else {
            // Get current token
            $token = Googledrive::getGoogledriveTokenById($id_ntbr_googledrive);

            if ($token && $token != '' && $active) {
                $connection = $this->testGoogledriveConnection($token);
            }
        }

        if (!$token || $token == '') {
            $connection = false;
        }

        if (!$connection && $active) {
            $result['errors'][] = sprintf(
                $this->l('Unable to connect to your %s account', self::PAGE),
                self::GOOGLEDRIVE
            );
        }

        if (count($result['errors'])) {
            $result['success'] = 0;
            return $result;
        }

        if (!$directory_key || $directory_key == '') {
            $directory_key = NtbrCore::GOOGLEDRIVE_ROOT_ID;
        }

        if (!$directory_path || $directory_path == '') {
            $directory_path = $this->l('Home', self::PAGE);
        }

        $googledrive->id_ntbr_config    = $id_ntbr_config;
        $googledrive->name              = $name;
        $googledrive->active            = $active;
        $googledrive->config_nb_backup  = $config_nb_backup;
        $googledrive->directory_path    = $directory_path;
        $googledrive->directory_key     = $directory_key;
        $googledrive->token             = $token;

        if (!$googledrive->save()) {
            $result['success'] = 0;
        }

        $result['id_ntbr_googledrive'] = $googledrive->id;

        return $result;
    }

    public function saveOnedrive(
        $id_ntbr_config,
        $id_ntbr_onedrive,
        $name,
        $active,
        $config_nb_backup,
        $code,
        $directory_path,
        $directory_key
    ) {
        $token  = '';

        $result = array(
            'success'           => 1,
            'errors'            => array(),
            'id_ntbr_onedrive'  => 0
        );

        // Required data
        if (!$name || trim($name) == '') {
            $result['errors'][] = $this->l('The name is required.', self::PAGE);
        }

        // Data validity
        if (!Validate::isGenericName($name) || trim($name) == '') {
            $result['errors'][] = $this->l('The account name is not valid. Please do not use those characters', self::PAGE).' "<>={}"';
        } else {
            $name_exists_id = Onedrive::getIdByName($id_ntbr_config, $name);

            if ($name_exists_id && $id_ntbr_onedrive != $name_exists_id) {
                $result['errors'][] = $this->l('The account name is already used', self::PAGE);
            }
        }

        if (count($result['errors'])) {
            $result['success'] = 0;
            return $result;
        }

        if ($id_ntbr_onedrive) {
            $onedrive = new Onedrive($id_ntbr_onedrive);
        } else {
            $onedrive = new Onedrive();
        }

        // Check connection
        $connection = true;

        if ($code != '' && $code != self::FAKE_MDP) {
            // Get new token
            $decode_token = $this->getOnedriveAccessToken($code);

            $decode_token['access_token']   = $this->encrypt($decode_token['access_token']);
            $decode_token['refresh_token']  = $this->encrypt($decode_token['refresh_token']);

            $token = Tools::jsonEncode($decode_token);
        } else {
            // Get current token
            $token = Onedrive::getOnedriveTokenById($id_ntbr_onedrive);

            if ($token && $token != '' && $active) {
                $connection = $this->testOnedriveConnection($token, $id_ntbr_onedrive);
            }
        }

        if (!$token || $token == '') {
            $connection = false;
        }

        if (!$connection && $active) {
            $result['errors'][] = sprintf($this->l('Unable to connect to your %s account', self::PAGE), self::ONEDRIVE);
        }

        if (count($result['errors'])) {
            $result['success'] = 0;
            return $result;
        }

        if (!$directory_key || $directory_key == '') {
            $onedrive_lib   = $this->connectToOnedrive($token, $id_ntbr_onedrive);
            $directory_key  = $onedrive_lib->getRootID();
        }

        if (!$directory_path || $directory_path == '') {
            $directory_path = $this->l('Home', self::PAGE);
        }

        $onedrive->id_ntbr_config   = $id_ntbr_config;
        $onedrive->name             = $name;
        $onedrive->active           = $active;
        $onedrive->config_nb_backup = $config_nb_backup;
        $onedrive->directory_path   = $directory_path;
        $onedrive->directory_key    = $directory_key;
        $onedrive->token            = $token;

        if (!$onedrive->save()) {
            $result['success'] = 0;
        }

        $result['id_ntbr_onedrive'] = $onedrive->id;

        return $result;
    }

    public function saveSugarsync(
        $id_ntbr_config,
        $id_ntbr_sugarsync,
        $name,
        $active,
        $config_nb_backup,
        $login,
        $password,
        $directory_path,
        $directory_key
    ) {
        $token  = '';

        $result = array(
            'success'           => 1,
            'errors'            => array(),
            'id_ntbr_sugarsync' => 0
        );

        // Required data
        if (!$name || $name == '') {
            $result['errors'][] = $this->l('The name is required.', self::PAGE);
        }

        if ($login != '' && $password == '') {
            $result['errors'][] = $this->l('The password is required.', self::PAGE);
        } elseif ($login == '' && $password != '') {
            $result['errors'][] = $this->l('The login is required.', self::PAGE);
        }

        // Data validity
        if (!Validate::isGenericName($name) || $name == '') {
            $result['errors'][] = $this->l('The account name is not valid. Please do not use those characters', self::PAGE).' "<>={}"';
        } else {
            $name_exists_id = Sugarsync::getIdByName($id_ntbr_config, $name);

            if ($name_exists_id && $id_ntbr_sugarsync != $name_exists_id) {
                $result['errors'][] = $this->l('The account name is already used', self::PAGE);
            }
        }

        if (count($result['errors'])) {
            $result['success'] = 0;
            return $result;
        }

        if ($id_ntbr_sugarsync) {
            $sugarsync = new Sugarsync($id_ntbr_sugarsync);
        } else {
            $sugarsync = new Sugarsync();
        }

        // Check connection
        $connection = true;

        if ($login != '' && ($password != '' && $password != self::FAKE_MDP)) {
            // Get new token
            $refresh_token  = $this->getSugarsyncRefreshToken($login, $password);
            $decode_token   = $this->getSugarsyncAccessToken($refresh_token);
            $token = Tools::jsonEncode($decode_token);
        } else {
            // Get current token
            $token = Sugarsync::getSugarsyncTokenById($id_ntbr_sugarsync);

            if ($token && $token != '' && $active) {
                $connection = $this->testSugarsyncConnection($token, $id_ntbr_sugarsync);
            }
        }

        if (!$token || $token == '') {
            $connection = false;
        }

        if (!$connection && $active) {
            $result['errors'][] = sprintf(
                $this->l('Unable to connect to your %s account', self::PAGE),
                self::SUGARSYNC
            );
        }

        if (count($result['errors'])) {
            $result['success'] = 0;
            return $result;
        }

        if (!$directory_key || $directory_key == '') {
            $sugarsync_lib  = $this->connectToSugarsync($token, $id_ntbr_sugarsync);
            $root           = $sugarsync_lib->getRoot();

            $directory_key  = basename($root['ref']);
            $directory_path = $root['displayName'];
        }

        $sugarsync->id_ntbr_config      = $id_ntbr_config;
        $sugarsync->name                = $name;
        $sugarsync->active              = $active;
        $sugarsync->config_nb_backup    = $config_nb_backup;
        $sugarsync->directory_path      = $directory_path;
        $sugarsync->directory_key       = $directory_key;
        $sugarsync->token               = $token;
        $sugarsync->login               = $login;

        if (!$sugarsync->save()) {
            $result['success'] = 0;
        }

        $result['id_ntbr_sugarsync'] = $sugarsync->id;

        return $result;
    }

    /**
     * Get SugarSync user informations
     *
     * @param   String      $token              SugarSync token
     * @param   integer     $id_ntbr_sugarsync  ID SugarSync account
     *
     * @return  array                           The information of the user
     */
    public function getSugarsyncUserInformation($token, $id_ntbr_sugarsync)
    {
        $sugarsync_lib = $this->connectToSugarsync($token, $id_ntbr_sugarsync);

        return $sugarsync_lib->getUserInfos();
    }

    public function saveHubic(
        $id_ntbr_config,
        $id_ntbr_hubic,
        $name,
        $active,
        $config_nb_backup,
        $code,
        $directory
    ) {
        $connect_infos  = array();

        $result = array(
            'success'       => 1,
            'errors'        => array(),
            'id_ntbr_hubic' => 0
        );

        // Required data
        if (!$name || trim($name) == '') {
            $result['errors'][] = $this->l('The name is required.', self::PAGE);
        }

        // Data validity
        if (!Validate::isGenericName($name) || trim($name) == '') {
            $result['errors'][] = $this->l('The account name is not valid. Please do not use those characters', self::PAGE).' "<>={}"';
        } else {
            $name_exists_id = Hubic::getIdByName($id_ntbr_config, $name);

            if ($name_exists_id && $id_ntbr_hubic != $name_exists_id) {
                $result['errors'][] = $this->l('The account name is already used', self::PAGE);
            }
        }

        if (count($result['errors'])) {
            $result['success'] = 0;
            return $result;
        }

        if ($id_ntbr_hubic) {
            $hubic = new Hubic($id_ntbr_hubic);
        } else {
            $hubic = new Hubic();
        }

        // Check connection
        $connection = true;

        if ($code != '' && $code != self::FAKE_MDP) {
            // Get new connection infos
            $connect_infos = $this->getHubicAccessToken($code);

            if (isset($connect_infos['token']['access_token'])) {
                $connect_infos['token']['access_token'] = $this->encrypt($connect_infos['token']['access_token']);
            }

            if (isset($connect_infos['token']['refresh_token'])) {
                $connect_infos['token']['refresh_token'] = $this->encrypt($connect_infos['token']['refresh_token']);
            }

            if (isset($connect_infos['credential']['token'])) {
                $connect_infos['credential']['token'] = $this->encrypt($connect_infos['credential']['token']);
            }

            $connect_infos['token']         = Tools::jsonEncode($connect_infos['token']);
            $connect_infos['credential']    = Tools::jsonEncode($connect_infos['credential']);
        } elseif ($id_ntbr_hubic) {
            if ($active) {
                $connection = $this->testHubicConnection($id_ntbr_hubic);
            }
            $connect_infos  = Hubic::getHubicConnectionInfosById($id_ntbr_hubic);
        }

        if (!is_array($connect_infos) || !isset($connect_infos['token']) || !isset($connect_infos['credential'])) {
            $connection = false;
        }

        if (!$connection && $active) {
            $result['errors'][] = sprintf($this->l('Unable to connect to your %s account', self::PAGE), self::HUBIC);
        }

        if (!$active && !isset($connect_infos['token']) && !isset($connect_infos['credential'])) {
            $result['errors'][] = sprintf($this->l('Unvalid %s account', self::PAGE), self::HUBIC);
        }

        if (count($result['errors'])) {
            $result['success'] = 0;
            return $result;
        }

        $hubic->id_ntbr_config      = $id_ntbr_config;
        $hubic->name                = $name;
        $hubic->active              = $active;
        $hubic->config_nb_backup    = $config_nb_backup;
        $hubic->directory           = $directory;
        $hubic->token               = $connect_infos['token'];
        $hubic->credential          = $connect_infos['credential'];

        if (!$hubic->save()) {
            $result['success'] = 0;
        }

        $result['id_ntbr_hubic'] = $hubic->id;

        return $result;
    }

    public function saveAws(
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
    ) {
        $result = array(
            'success'       => 1,
            'errors'        => array(),
            'id_ntbr_aws'   => 0
        );

        // Required data
        if (!$name || trim($name) == '') {
            $result['errors'][] = $this->l('The name is required.', self::PAGE);
        }

        if (!$id_ntbr_aws) {
            $aws = new Aws();

            if (!$access_key_id || trim($access_key_id) == '') {
                $result['errors'][] = $this->l('The access key ID is required.', self::PAGE);
            }

            if (!$secret_access_key || trim($secret_access_key) == '') {
                $result['errors'][] = $this->l('The secret access key is required.', self::PAGE);
            }
        } else {
            $aws = new Aws($id_ntbr_aws);

            if (!$access_key_id || trim($access_key_id) == '' || $access_key_id == NtbrChild::FAKE_MDP) {
                $access_key_id = $this->decrypt($aws->access_key_id);
            }

            if (!$secret_access_key || trim($secret_access_key) == '' || $secret_access_key == NtbrChild::FAKE_MDP) {
                $secret_access_key = $this->decrypt($aws->secret_access_key);
            }
        }

        if (!$region || trim($region) == '') {
            $result['errors'][] = $this->l('The region is required.', self::PAGE);
        }

        if (!$bucket || trim($bucket) == '') {
            $result['errors'][] = $this->l('The bucket is required.', self::PAGE);
        }

        if (!$storage_class
            || !in_array($storage_class, array(
                AwsLib::STORAGE_CLASS_STANDARD,
                AwsLib::STORAGE_CLASS_REDUCED_REDUNDANCY,
                AwsLib::STORAGE_CLASS_STANDARD_IA,
                AwsLib::STORAGE_CLASS_ONEZONE_IA,
                AwsLib::STORAGE_CLASS_INTELLIGENT_TIERING,
                AwsLib::STORAGE_CLASS_GLACIER,
                AwsLib::STORAGE_CLASS_DEEP_ARCHIVE,
            ))
        ) {
            $storage_class = AwsLib::STORAGE_CLASS_STANDARD;
        }

        // Data validity
        if (!Validate::isGenericName($name) || trim($name) == '') {
            $result['errors'][] = $this->l('The account name is not valid. Please do not use those characters', self::PAGE).' "<>={}"';
        } else {
            $name_exists_id = Aws::getIdByName($id_ntbr_config, $name);

            if ($name_exists_id && $id_ntbr_aws != $name_exists_id) {
                $result['errors'][] = $this->l('The account name is already used', self::PAGE);
            }
        }

        if (count($result['errors'])) {
            $result['success'] = 0;
            return $result;
        }

        // Check connection
        if ($active) {
            $connection = $this->testAwsConnection($access_key_id, $secret_access_key, $region, $bucket);

            if (!$connection) {
                $result['errors'][] = sprintf($this->l('Unable to connect to your %s account', self::PAGE), self::AWS);
            }
        }

        if (count($result['errors'])) {
            $result['success'] = 0;
            return $result;
        }

        // Save data
        if (!$directory_key) {
            $directory_key = $bucket;
        }

        if (!$directory_path) {
            $directory_path = $bucket;
        }

        $aws->id_ntbr_config    = $id_ntbr_config;
        $aws->name              = $name;
        $aws->active            = $active;
        $aws->config_nb_backup  = $config_nb_backup;
        $aws->access_key_id     = $this->encrypt($access_key_id);
        $aws->secret_access_key = $this->encrypt($secret_access_key);
        $aws->region            = $region;
        $aws->bucket            = $bucket;
        $aws->storage_class     = $storage_class;
        $aws->directory_key     = $directory_key;
        $aws->directory_path    = $directory_path;

        if (!$aws->save()) {
            $result['success'] = 0;
        }

        $result['id_ntbr_aws'] = $aws->id;

        return $result;
    }

    public function checkConnectionFtp($id_ntbr_ftp)
    {
        if (!$id_ntbr_ftp) {
            return 0;
        }

        $ftp = new Ftp($id_ntbr_ftp);
        $password = $this->decrypt($ftp->password);

        if ($ftp->sftp) {
            // Connect with SFTP
            if ($this->testSFTP($ftp->server, $ftp->login, $password, $ftp->port)) {
                return 1;
            }
        } else {
            // Connect with FTP
            if ($this->testFTP($ftp->server, $ftp->login, $password, $ftp->port, $ftp->ssl, $ftp->passive_mode)) {
                return 1;
            }
        }

        return 0;
    }

    public function checkConnectionDropbox($id_ntbr_dropbox)
    {
        if (!$id_ntbr_dropbox) {
            return 0;
        }

        $token = $this->decrypt(Dropbox::getDropboxTokenById($id_ntbr_dropbox));

        if ($token && $token != '') {
            return (int)(bool)$this->testDropboxConnection($token);
        }

        return 0;
    }

    public function checkConnectionOwncloud($id_ntbr_owncloud)
    {
        if (!$id_ntbr_owncloud) {
            return 0;
        }

        $owncloud   = new Owncloud($id_ntbr_owncloud);
        $password   = $this->decrypt($owncloud->password);

        if ($this->testOwncloudConnection($owncloud->server, $owncloud->login, $password)) {
            return 1;
        }

        return 0;
    }

    public function checkConnectionWebdav($id_ntbr_webdav)
    {
        if (!$id_ntbr_webdav) {
            return 0;
        }

        $webdav     = new Webdav($id_ntbr_webdav);
        $password   = $this->decrypt($webdav->password);

        if ($this->testWebdavConnection($webdav->server, $webdav->login, $password)) {
            return 1;
        }

        return 0;
    }

    public function checkConnectionGoogledrive($id_ntbr_googledrive)
    {
        if (!$id_ntbr_googledrive) {
            return 0;
        }

        $token = Googledrive::getGoogledriveTokenById($id_ntbr_googledrive);
        if ($token && $token != '') {
            return (int)(bool)$this->testGoogledriveConnection($token);
        }

        return 0;
    }

    public function checkConnectionOnedrive($id_ntbr_onedrive)
    {
        if (!$id_ntbr_onedrive) {
            return 0;
        }

        $token = Onedrive::getOnedriveTokenById($id_ntbr_onedrive);
        if ($token && $token != '') {
            return (int)(bool)$this->testOnedriveConnection($token, $id_ntbr_onedrive);
        }

        return 0;
    }

    public function checkConnectionSugarsync($id_ntbr_sugarsync)
    {
        if (!$id_ntbr_sugarsync) {
            return 0;
        }

        $token = Sugarsync::getSugarsyncTokenById($id_ntbr_sugarsync);
        if ($token && $token != '') {
            return (int)(bool)$this->testSugarsyncConnection($token, $id_ntbr_sugarsync);
        }

        return 0;
    }

    public function checkConnectionHubic($id_ntbr_hubic)
    {
        if (!$id_ntbr_hubic) {
            return 0;
        }

        if ($id_ntbr_hubic) {
            return (int)(bool)$this->testHubicConnection($id_ntbr_hubic);
        }

        return 0;
    }

    public function checkConnectionAws($id_ntbr_aws)
    {
        if (!$id_ntbr_aws) {
            return 0;
        }

        $aws = new Aws($id_ntbr_aws);

        return (int)(bool)$this->testAwsConnection(
            $this->decrypt($aws->access_key_id),
            $this->decrypt($aws->secret_access_key),
            $aws->region,
            $aws->bucket
        );
    }

    public function deleteFtp($id_ntbr_ftp)
    {
        if (!$id_ntbr_ftp) {
            return 0;
        }

        $ftp = new Ftp($id_ntbr_ftp);
        return (int)(bool)$ftp->delete();
    }

    public function deleteDropbox($id_ntbr_dropbox)
    {
        if (!$id_ntbr_dropbox) {
            return 0;
        }

        $dropbox = new Dropbox($id_ntbr_dropbox);
        return (int)(bool)$dropbox->delete();
    }

    public function deleteOwncloud($id_ntbr_owncloud)
    {
        if (!$id_ntbr_owncloud) {
            return 0;
        }

        $owncloud = new Owncloud($id_ntbr_owncloud);
        return (int)(bool)$owncloud->delete();
    }

    public function deleteWebdav($id_ntbr_webdav)
    {
        if (!$id_ntbr_webdav) {
            return 0;
        }

        $webdav = new Webdav($id_ntbr_webdav);
        return (int)(bool)$webdav->delete();
    }

    public function deleteGoogledrive($id_ntbr_googledrive)
    {
        if (!$id_ntbr_googledrive) {
            return 0;
        }

        $googledrive = new Googledrive($id_ntbr_googledrive);
        return (int)(bool)$googledrive->delete();
    }

    public function deleteOnedrive($id_ntbr_onedrive)
    {
        if (!$id_ntbr_onedrive) {
            return 0;
        }

        $onedrive = new Onedrive($id_ntbr_onedrive);
        return (int)(bool)$onedrive->delete();
    }

    public function deleteSugarsync($id_ntbr_sugarsync)
    {
        if (!$id_ntbr_sugarsync) {
            return 0;
        }

        $sugarsync = new Sugarsync($id_ntbr_sugarsync);
        return (int)(bool)$sugarsync->delete();
    }

    public function deleteHubic($id_ntbr_hubic)
    {
        if (!$id_ntbr_hubic) {
            return 0;
        }

        $hubic = new Hubic($id_ntbr_hubic);
        return (int)(bool)$hubic->delete();
    }

    public function deleteAws($id_ntbr_aws)
    {
        if (!$id_ntbr_aws) {
            return 0;
        }

        $aws = new Aws($id_ntbr_aws);
        return (int)(bool)$aws->delete();
    }

    public function getDropboxFilesList($id_ntbr_dropbox)
    {
        $files  = $this->l('You need to register a valid authorization code to see the files', self::PAGE);

        if ($id_ntbr_dropbox) {
            $dropbox        = new Dropbox($id_ntbr_dropbox);
            $access_token   = $this->decrypt($dropbox->token);
            $dropbox_lib    = $this->connectToDropbox($access_token);
            $dropbox_dir    = $dropbox->directory;

            //Dropbox dir should end with a "/" except when testing if exist
            if (Tools::substr($dropbox_dir, -1) != '/') {
                $dropbox_dir .= '/';
            }

            // Get informations on the directory and his children
            $files = $this->getDropboxFiles($dropbox_lib, $dropbox_dir);
        } else {
            $files = $this->l('Unknown account', self::PAGE);
        }
//d($files);
        return $files;
    }

    public function deleteDropboxFile($id_ntbr_dropbox, $file_name, $nb_part)
    {
        $dropbox        = new Dropbox($id_ntbr_dropbox);
        $access_token   = $this->decrypt($dropbox->token);
        $dropbox_lib    = $this->connectToDropbox($access_token);

        //Dropbox dir should start with a "/" except for root
        if ($dropbox->directory != '' && $dropbox->directory[0] !== '/') {
            $dropbox->directory = '/'.$dropbox->directory;
        }

        //Dropbox dir should end with a "/" except when testing if exist
        if (Tools::substr($dropbox->directory, -1) != '/') {
            $dropbox->directory .= '/';
        }

        if ($nb_part > 1) {
            $success = true;

            for ($i = 1; $i <= $nb_part; $i++) {
                if (strpos($file_name, '.tar.gz')) {
                    $file_destination   = $dropbox->directory.basename($file_name, '.tar.gz').'.'.$i.'.part.tar.gz';
                } else {
                    $file_destination   = $dropbox->directory.basename($file_name, '.tar').'.'.$i.'.part.tar';
                }

                if ($dropbox_lib->checkExists($file_destination)) {
                    if ($dropbox_lib->deleteFile($file_destination) === false) {
                        $this->log($this->l('Error while deleting the file:', self::PAGE).' '.$file_destination);

                        $success = false;
                    }
                }
            }

            return $success;
        } else {
            $file_destination   = $dropbox->directory.$file_name;

            if ($dropbox_lib->checkExists($file_destination)) {
                if ($dropbox_lib->deleteFile($file_destination) === false) {
                    $this->log($this->l('Error while deleting the file:', self::PAGE).' '.$file_destination);

                    return false;
                }
            }
        }

        return true;
    }

    public function downloadDropboxFile($id_ntbr_dropbox, $id_file)
    {
        $dropbox        = new Dropbox($id_ntbr_dropbox);
        $access_token   = $this->decrypt($dropbox->token);
        $dropbox_lib    = $this->connectToDropbox($access_token);

        $file_infos = $dropbox_lib->downloadFile($id_file);

        if (!isset($file_infos['link'])) {
            return false;
        }

        return $file_infos['link'];
    }

    public function getGoogledriveFilesList($id_ntbr_googledrive)
    {
        $files  = $this->l('You need to register a valid authorization code to see the files', self::PAGE);

        if ($id_ntbr_googledrive) {
            $googledrive        = new Googledrive($id_ntbr_googledrive);
            $access_token       = $googledrive->token;
            $googledrive_lib    = $this->connectToGoogledrive($access_token);

            // Get informations on the directory and his children
            $files = $this->getGoogledriveFiles($googledrive_lib, $googledrive->directory_key);
        } else {
            $files = $this->l('Unknown account', self::PAGE);
        }
//d($files);
        return $files;
    }

    public function downloadGoogledriveFile($id_ntbr_googledrive, $id_file)
    {
        $googledrive        = new Googledrive($id_ntbr_googledrive);
        $access_token       = $googledrive->token;
        $googledrive_lib    = $this->connectToGoogledrive($access_token);

        $link = $googledrive_lib->downloadFile($id_file);

        return $link;
    }

    public function deleteGoogledriveFile($id_ntbr_googledrive, $file_name, $nb_part)
    {
        $googledrive        = new Googledrive($id_ntbr_googledrive);
        $access_token       = $googledrive->token;
        $googledrive_lib    = $this->connectToGoogledrive($access_token);

        // Get informations on the directory and his children
        $files = $this->getGoogledriveFiles($googledrive_lib, $googledrive->directory_key);

        foreach ($files as $file) {
            if ($file['name'] == $file_name) {
                if ($nb_part > 1) {
                    $success = true;

                    foreach ($file['part'] as $part) {
                        if ($googledrive_lib->deleteFile($part['file_id']) === false) {
                            $this->log($this->l('Error while deleting the file:', self::PAGE).' '.$part['name']);

                            $success = false;
                        }
                    }

                    return $success;
                } else {
                    if ($googledrive_lib->deleteFile($file['file_id']) === false) {
                        $this->log($this->l('Error while deleting the file:', self::PAGE).' '.$file['name']);

                        return false;
                    }
                }
            }
        }

        return true;
    }

    public function getOnedriveFilesList($id_ntbr_onedrive)
    {
        $files  = $this->l('You need to register a valid authorization code to see the files', self::PAGE);

        if ($id_ntbr_onedrive) {
            $onedrive       = new Onedrive($id_ntbr_onedrive);
            $access_token   = $onedrive->token;
            $onedrive_lib   = $this->connectToOnedrive($access_token, $id_ntbr_onedrive);

            // Get informations on the directory and his children
            $files = $this->getOnedriveFiles($onedrive_lib, $onedrive->directory_key);
        } else {
            $files = $this->l('Unknown account', self::PAGE);
        }
//d($files);
        return $files;
    }

    public function downloadOnedriveFile($id_ntbr_onedrive, $id_file)
    {
        $onedrive       = new Onedrive($id_ntbr_onedrive);
        $access_token   = $onedrive->token;
        $onedrive_lib   = $this->connectToOnedrive($access_token);

        $infos = $onedrive_lib->getMetadatas($id_file);

        if (isset($infos['@content.downloadUrl'])) {
            return $infos['@content.downloadUrl'];
        }

        return false;
    }

    public function deleteOnedriveFile($id_ntbr_onedrive, $file_name, $nb_part)
    {
        $onedrive       = new Onedrive($id_ntbr_onedrive);
        $access_token   = $onedrive->token;
        $onedrive_lib   = $this->connectToOnedrive($access_token);

        // Get informations on the directory and his children
        $files = $this->getOnedriveFiles($onedrive_lib, $onedrive->directory_key);

        if ($files === false) {
            return true; // No file to delete
        }

        foreach ($files as $file) {
            if ($file['name'] == $file_name) {
                if ($nb_part > 1) {
                    $success = true;

                    foreach ($file['part'] as $part) {
                        if ($onedrive_lib->deleteItem($part['file_id']) === false) {
                            $this->log($this->l('Error while deleting the file:', self::PAGE).' '.$part['name']);

                            $success = false;
                        }
                    }

                    return $success;
                } else {
                    if ($onedrive_lib->deleteItem($file['file_id']) === false) {
                        $this->log($this->l('Error while deleting the file:', self::PAGE).' '.$file['name']);

                        return false;
                    }
                }
            }
        }

        return true;
    }

    public function getOwncloudFilesList($id_ntbr_owncloud)
    {
        $files  = $this->l('You need to register a valid authorization code to see the files', self::PAGE);

        if ($id_ntbr_owncloud) {
            $owncloud           = new Owncloud($id_ntbr_owncloud);
            $owncloud_pass      = $this->decrypt($owncloud->password);
            $owncloud_lib       = $this->connectToOwncloud($owncloud->server, $owncloud->login, $owncloud_pass);

            // Get informations on the directory and his children
            $files = $this->getOwncloudFiles($owncloud_lib, $owncloud->directory);
        } else {
            $files = $this->l('Unknown account', self::PAGE);
        }
//d($files);
        return $files;
    }

    public function downloadOwncloudFile($id_ntbr_owncloud, $id_file, $pos, $length, $file_size)
    {
        $owncloud       = new Owncloud($id_ntbr_owncloud);
        $owncloud_pass  = $this->decrypt($owncloud->password);
        $owncloud_lib   = $this->connectToOwncloud($owncloud->server, $owncloud->login, $owncloud_pass);

        $content = $owncloud_lib->downloadFile($id_file, $pos, $length, $file_size);

        return $content;
    }

    public function deleteOwncloudFile($id_ntbr_owncloud, $file_name, $nb_part)
    {
        $owncloud       = new Owncloud($id_ntbr_owncloud);
        $owncloud_pass  = $this->decrypt($owncloud->password);
        $owncloud_lib   = $this->connectToOwncloud($owncloud->server, $owncloud->login, $owncloud_pass);

        // Get informations on the directory and his children
        $files = $this->getOwncloudFiles($owncloud_lib, $owncloud->directory);

        if ($files === false) {
            return true; // No file to delete
        }

        foreach ($files as $file) {
            if ($file['name'] == $file_name) {
                if ($nb_part > 1) {
                    $success = true;

                    foreach ($file['part'] as $part) {
                        if ($owncloud_lib->deleteFile($part['file_id']) === false) {
                            $this->log($this->l('Error while deleting the file:', self::PAGE).' '.$part['name']);

                            $success = false;
                        }
                    }

                    return $success;
                } else {
                    if ($owncloud_lib->deleteFile($file['file_id']) === false) {
                        $this->log($this->l('Error while deleting the file:', self::PAGE).' '.$file['name']);

                        return false;
                    }
                }
            }
        }

        return true;
    }

    public function getWebdavFilesList($id_ntbr_webdav)
    {
        $files  = $this->l('You need to register a valid authorization code to see the files', self::PAGE);

        if ($id_ntbr_webdav) {
            $webdav           = new Webdav($id_ntbr_webdav);
            $webdav_pass      = $this->decrypt($webdav->password);
            $webdav_lib       = $this->connectToWebdav($webdav->server, $webdav->login, $webdav_pass);

            // Get informations on the directory and his children
            $files = $this->getWebdavFiles($webdav_lib, $webdav->directory);
        } else {
            $files = $this->l('Unknown account', self::PAGE);
        }
//d($files);
        return $files;
    }

    public function downloadWebdavFile($id_ntbr_webdav, $id_file, $pos, $length, $file_size)
    {
        $webdav           = new Webdav($id_ntbr_webdav);
        $webdav_pass      = $this->decrypt($webdav->password);
        $webdav_lib       = $this->connectToWebdav($webdav->server, $webdav->login, $webdav_pass);

        $content = $webdav_lib->downloadFile($id_file, $pos, $length, $file_size);

        return $content;
    }

    public function deleteWebdavFile($id_ntbr_webdav, $file_name, $nb_part)
    {
        $webdav         = new Webdav($id_ntbr_webdav);
        $webdav_pass    = $this->decrypt($webdav->password);
        $webdav_lib     = $this->connectToWebdav($webdav->server, $webdav->login, $webdav_pass);

        // Get informations on the directory and his children
        $files = $this->getWebdavFiles($webdav_lib, $webdav->directory);

        if ($files === false) {
            return true; // No file to delete
        }

        foreach ($files as $file) {
            if ($file['name'] == $file_name) {
                if ($nb_part > 1) {
                    $success = true;

                    foreach ($file['part'] as $part) {
                        if ($webdav_lib->deleteFile($part['file_id']) === false) {
                            $this->log($this->l('Error while deleting the file:', self::PAGE).' '.$part['name']);

                            $success = false;
                        }
                    }

                    return $success;
                } else {
                    if ($webdav_lib->deleteFile($file['file_id']) === false) {
                        $this->log($this->l('Error while deleting the file:', self::PAGE).' '.$file['name']);

                        return false;
                    }
                }
            }
        }

        return true;
    }

    public function getFtpFilesList($id_ntbr_ftp)
    {
        $files  = $this->l('You need to register a valid authorization code to see the files', self::PAGE);

        if ($id_ntbr_ftp) {
            $ftp            = new Ftp($id_ntbr_ftp);
            $ftp_server     = $ftp->server;
            $ftp_login      = $ftp->login;
            $ftp_pass       = $this->decrypt($ftp->password);
            $ftp_port       = $ftp->port;
            $ftp_ssl        = $ftp->ssl;
            $ftp_pasv       = $ftp->passive_mode;
            $ftp_directory  = $ftp->directory;

            //FTP/SFTP dir should start and end with a /
            $ftp_directory = rtrim($this->normalizePath($ftp_directory), '/').'/';
            if ($ftp_directory[0] !== '/') {
                $ftp_directory = '/'.$ftp_directory;
            }

            if (!$ftp->sftp) {
                $connection = $this->connectFtp(
                    $ftp_server,
                    $ftp_login,
                    $ftp_pass,
                    (int)$ftp_port,
                    $ftp_ssl,
                    $ftp_pasv
                );

                if (!$connection) {
                    return $files;
                }

                $ftp_current_directory = ftp_pwd($connection);

                if ($ftp_current_directory != '/') {
                    $ftp_directory = $ftp_current_directory.$ftp_directory;
                }

                ftp_chdir($connection, $ftp_directory);

                // Get informations on the directory and his children
                $files = $this->getFtpFiles($connection);

                ftp_close($connection);
            } else {
                $this->initForSFTP();

                $sftp_lib = new \phpseclib\Net\SFTP($ftp_server, $ftp_port);

                // Beware of the warning from php if failure
                if (!@$sftp_lib->login($ftp_login, $ftp_pass)) {
                    $this->log(
                        'WAR'
                        .sprintf(
                            $this->l('Unable to connect to the %s server, please verify your credentials', self::PAGE),
                            self::SFTP
                        ),
                        true
                    );
                    return $files;
                }

                $ftp_directory = $sftp_lib->pwd().$ftp_directory;

                // Get informations on the directory and his children
                $files = $this->getSftpFiles($sftp_lib, $ftp_directory);

                $this->closeSFTP($sftp_lib);
            }
        } else {
            $files = $this->l('Unknown account', self::PAGE);
        }
//d($files);
        return $files;
    }

    public function downloadFtpFile($id_ntbr_ftp, $id_file, $pos, $length)
    {
        $content    = '';

        if ($id_ntbr_ftp) {
            $ftp            = new Ftp($id_ntbr_ftp);
            $ftp_server     = $ftp->server;
            $ftp_login      = $ftp->login;
            $ftp_pass       = $this->decrypt($ftp->password);
            $ftp_port       = $ftp->port;
            $ftp_ssl        = $ftp->ssl;
            $ftp_pasv       = $ftp->passive_mode;
            $ftp_directory  = $ftp->directory;

            //FTP/SFTP dir should start and end with a /
            $ftp_directory = rtrim($this->normalizePath($ftp_directory), '/').'/';
            if ($ftp_directory[0] !== '/') {
                $ftp_directory = '/'.$ftp_directory;
            }

            if (!$ftp->sftp) {
                if ($ftp_ssl) {
                    $type = 'ftps://';
                } else {
                    $type = 'ftp://';
                }

                $start  = $pos;
                $end    = ($pos + $length) - 1;

                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, $type.$ftp_server.':'.(int)$ftp_port.$ftp_directory.$id_file);
                curl_setopt($curl, CURLOPT_USERPWD, $ftp_login.':'.$ftp_pass);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($curl, CURLOPT_RANGE, $start.'-'.$end);

                if ($ftp_pasv) {
                    curl_setopt($curl, CURLOPT_FTP_USE_EPSV, true);
                }

                $content = curl_exec($curl);
            } else {
                $this->initForSFTP();

                $sftp_lib = new \phpseclib\Net\SFTP($ftp_server, $ftp_port);

                // Beware of the warning from php if failure
                if (!@$sftp_lib->login($ftp_login, $ftp_pass)) {
                    $this->log(
                        'WAR'
                        .sprintf(
                            $this->l('Unable to connect to the %s server, please verify your credentials', self::PAGE),
                            self::SFTP
                        ),
                        true
                    );
                    return $content;
                }

                $ftp_directory = $sftp_lib->pwd().$ftp_directory;

                $content = $sftp_lib->get($ftp_directory.$id_file, false, $pos, $length);

                $this->closeSFTP($sftp_lib);
            }
        }

        return $content;
    }

    public function deleteFtpFile($id_ntbr_ftp, $file_name, $nb_part)
    {
        if ($id_ntbr_ftp) {
            $ftp            = new Ftp($id_ntbr_ftp);
            $ftp_server     = $ftp->server;
            $ftp_login      = $ftp->login;
            $ftp_pass       = $this->decrypt($ftp->password);
            $ftp_port       = $ftp->port;
            $ftp_ssl        = $ftp->ssl;
            $ftp_pasv       = $ftp->passive_mode;
            $ftp_directory  = $ftp->directory;

            //FTP/SFTP dir should start and end with a /
            $ftp_directory = rtrim($this->normalizePath($ftp_directory), '/').'/';
            if ($ftp_directory[0] !== '/') {
                $ftp_directory = '/'.$ftp_directory;
            }

            if (!$ftp->sftp) {
                $connection = $this->connectFtp(
                    $ftp_server,
                    $ftp_login,
                    $ftp_pass,
                    (int)$ftp_port,
                    $ftp_ssl,
                    $ftp_pasv
                );

                if (!$connection) {
                    return false;
                }

                $ftp_current_directory = ftp_pwd($connection);

                if ($ftp_current_directory != '/') {
                    $ftp_directory = $ftp_current_directory.$ftp_directory;
                }

                ftp_chdir($connection, $ftp_directory);

                // Get informations on the directory and his children
                $files = $this->getFtpFiles($connection);

                if ($files === false) {
                    ftp_close($connection);
                    return true; // No file to delete
                }

                $success = true;

                foreach ($files as $file) {
                    if ($file['name'] == $file_name) {
                        if ($nb_part > 1) {
                            foreach ($file['part'] as $part) {
                                if (!ftp_delete($connection, basename($part['name']))) {
                                    $this->log(
                                        $this->l('Error while deleting the file:', self::PAGE).' '.$part['name']
                                    );

                                    $success = false;
                                }
                            }
                        } else {
                            if (!ftp_delete($connection, basename($file['name']))) {
                                $this->log($this->l('Error while deleting the file:', self::PAGE).' '.$file['name']);

                                $success = false;
                            }
                        }
                    }
                }

                ftp_close($connection);

                return $success;
            } else {
                $this->initForSFTP();

                $sftp_lib = new \phpseclib\Net\SFTP($ftp_server, $ftp_port);

                // Beware of the warning from php if failure
                if (!@$sftp_lib->login($ftp_login, $ftp_pass)) {
                    $this->log(
                        'WAR'
                        .sprintf(
                            $this->l('Unable to connect to the %s server, please verify your credentials', self::PAGE),
                            self::SFTP
                        ),
                        true
                    );
                    return false;
                }

                $ftp_directory = $sftp_lib->pwd().$ftp_directory;

                // Get informations on the directory and his children
                $files = $this->getSftpFiles($sftp_lib, $ftp_directory);

                if ($files === false) {
                    $this->closeSFTP($sftp_lib);
                    return true; // No file to delete
                }

                $success = true;

                foreach ($files as $file) {
                    if ($file['name'] == $file_name) {
                        if ($nb_part > 1) {
                            foreach ($file['part'] as $part) {
                                if (!$sftp_lib->delete($ftp_directory.basename($part['name']))) {
                                    $this->log(
                                        $this->l('Delete old backup file failed:', self::PAGE).basename($part['name'])
                                    );
                                    $success = false;
                                }
                            }
                        } else {
                            if (!$sftp_lib->delete($ftp_directory.basename($file['name']))) {
                                $this->log($this->l('Error while deleting the file:', self::PAGE).' '.$file['name']);

                                $success = false;
                            }
                        }
                    }
                }

                $this->closeSFTP($sftp_lib);

                return $success;
            }
        }

        return true;
    }

    public function displayGoogledriveTree($id_ntbr_googledrive)
    {
        $tree   = $this->l('You need to register a valid authorization code to choose a directory', self::PAGE);

        if ($id_ntbr_googledrive) {
            $googledrive    = new Googledrive($id_ntbr_googledrive);
            $tree           = $this->getGoogledriveTree($googledrive->directory_key, $id_ntbr_googledrive);
        } else {
            $tree = $this->l('Unknown account', self::PAGE);
        }

        return $tree;
    }

    public function displayGoogledriveTreeChild(
        $id_ntbr_googledrive,
        $id_parent,
        $googledrive_dir,
        $level,
        $path
    ) {
        $tree   = $this->l('You need to register a valid authorization code to choose a directory', self::PAGE);

        if ($id_ntbr_googledrive) {
            $googledrive = new Googledrive($id_ntbr_googledrive);

            $tree = $this->getGoogledriveTreeChildren(
                $googledrive->token,
                $id_parent,
                $googledrive_dir,
                $level,
                $path,
                $googledrive->id_ntbr_config
            );
        } else {
            $tree = $this->l('Unknown account', self::PAGE);
        }

        return $tree;
    }

    public function displayOnedriveTree($id_ntbr_onedrive)
    {
        $tree   = $this->l('You need to register a valid authorization code to choose a directory', self::PAGE);

        if ($id_ntbr_onedrive) {
            $onedrive = new Onedrive($id_ntbr_onedrive);
            $tree = $this->getOnedriveTree(
                $onedrive->token,
                $onedrive->directory_key,
                $id_ntbr_onedrive
            );
        } else {
            $tree = $this->l('Unknown account', self::PAGE);
        }

        return $tree;
    }

    public function displayOnedriveTreeChild($id_ntbr_onedrive, $id_parent, $onedrive_dir, $level, $path)
    {
        $tree   = $this->l('You need to register a valid authorization code to choose a directory', self::PAGE);

        if ($id_ntbr_onedrive) {
            $onedrive = new Onedrive($id_ntbr_onedrive);
            $tree = $this->getOnedriveTreeChildren(
                $onedrive->token,
                $onedrive_dir,
                $id_parent,
                $level,
                $path,
                $id_ntbr_onedrive
            );
        } else {
            $tree = $this->l('Unknown account', self::PAGE);
        }

        return $tree;
    }

    public function displaySugarsyncTree($id_ntbr_sugarsync)
    {
        $tree   = $this->l('You need to register a valid authorization code to choose a directory', self::PAGE);

        if ($id_ntbr_sugarsync) {
            $sugarsync = new Sugarsync($id_ntbr_sugarsync);
            $tree = $this->getSugarsyncTree(
                $sugarsync->token,
                $sugarsync->directory_key,
                $id_ntbr_sugarsync
            );
        } else {
            $tree = $this->l('Unknown account', self::PAGE);
        }

        return $tree;
    }

    public function displaySugarsyncTreeChild($id_ntbr_sugarsync, $id_parent, $sugarsync_dir, $level, $path)
    {
        $tree   = $this->l('You need to register a valid authorization code to choose a directory', self::PAGE);

        if ($id_ntbr_sugarsync) {
            $sugarsync = new Sugarsync($id_ntbr_sugarsync);
            $tree = $this->getSugarsyncTreeChildren(
                $sugarsync->token,
                $sugarsync_dir,
                $id_parent,
                $level,
                $path,
                $id_ntbr_sugarsync
            );
        } else {
            $tree = $this->l('Unknown account', self::PAGE);
        }

        return $tree;
    }

    public function displayAwsTree($id_ntbr_aws)
    {
        $tree = $this->l('You need to register a valid account to choose a directory', self::PAGE);

        if ($id_ntbr_aws) {
            $tree = $this->getAwsTree($id_ntbr_aws);
        } else {
            $tree = $this->l('Unknown account', self::PAGE);
        }

        return $tree;
    }

    public function displayAwsTreeChild($id_ntbr_aws, $directory_key, $directory_path, $level)
    {
        $tree = $this->l('You need to register a valid account to choose a directory', self::PAGE);

        if ($id_ntbr_aws) {
            $tree = $this->getAwsTreeChildren($directory_key, $level, $directory_path, $id_ntbr_aws);
        } else {
            $tree = $this->l('Unknown account', self::PAGE);
        }

        return $tree;
    }

    public function onlySendBackupAway($nb)
    {
        $current_time   = time();
        $ntbr_ongoing   = $this->getConfig('NTBR_ONGOING');
        $backups        = $this->findThisBackup($nb);

        if (strpos($nb, '.') === false) {
            // We dowload all the files
            if (is_array($backups)) {
                $first_backup = reset($backups);
            }

            if (!is_array($first_backup) || !isset($first_backup['name'])) {
                $this->log('ERR'.$this->l('The backup was not found', self::PAGE));
                return false;
            }

            $backup_name = $first_backup['name'];
            $backup_list = array();

            foreach ($backups as $backup) {
                $backup_list[] = $backup['name'];
            }
        } else {
            // We download only one file
            if (!is_array($backups) || !isset($backups[$nb]) || !isset($backups[$nb]['name'])) {
                $this->log('ERR'.$this->l('The backup was not found', self::PAGE));
                return false;
            }

            $backup_name = $backups[$nb]['name'];
            $backup_list = array($backups[$nb]['name']);
        }

        $clean_file             = preg_replace('/([0-9]+\.part\.)/', '', $backup_name);
        $config                 = new Config(Backups::getBackupIdConfig($clean_file));
        $time_between_backups   = $config->time_between_backups;

        if (!$config->id) {
            $this->log('ERR'.$this->l('Unknown configuration', self::PAGE));
            return false;
        }

        if ($time_between_backups <= 0) {
            $time_between_backups = NtbrCore::MIN_TIME_NEW_BACKUP;
        }

        if ($current_time - $ntbr_ongoing >= $time_between_backups) {
            $this->setConfig('NTBR_ONGOING', time());

            $result = $this->backup($config->id, false, false, NtbrCore::STEP_SEND_AWAY, $backup_name, $backup_list);

            if ($result) {
                $update = $this->updateBackupList();
                return array('backuplist' => $update, 'warnings' => $this->warnings);
            }
        } else {
            $time_to_wait = $time_between_backups - ($current_time - $ntbr_ongoing);
            $this->log(
                'ERR'.sprintf(
                    $this->l('For security reason, some time is needed between two backups. Please wait %d seconds', self::PAGE),
                    $time_to_wait
                )
            );
        }

        return false;
    }

    public function restoreBackup($backup_name, $type_backup)
    {
        if (!$backup_name || $backup_name == '' || !$type_backup || $type_backup == '') {
            return 0;
        }

        $options_restore = $this->startLocalRestore($backup_name, $type_backup);

        if ($options_restore === false) {
            return 0;
        }

        return $options_restore;
    }

    public function generateSecureUrls($id_shop_group, $id_shop)
    {
        return $this->generateUrls(false, $id_shop_group, $id_shop);
    }

    public function getTmpDistFileContent()
    {
        if ($this->config->create_on_distant) {
            if (file_exists($this->tmp_dist_file)) {
                if (!($handle_tmp_dist_file = fopen($this->tmp_dist_file, 'a+'))) {
                    $this->log('ERR'.$this->l('The temporary distant file cannot be opened', self::PAGE));
                    return $this->endWithError();
                }

                // Make sur the file has the correct right
                if (chmod($this->tmp_dist_file, octdec(self::PERM_FILE)) !== true) {
                    $this->log(
                        sprintf(
                            $this->l('The file "%s" permission cannot be updated to %d', self::PAGE),
                            $this->tmp_dist_file,
                            self::PERM_FILE
                        )
                    );
                }

                if (!rewind($handle_tmp_dist_file)) {
                    $this->log('ERR'.$this->l('The temporary distant file cannot be rewind', self::PAGE));
                    return $this->endWithError();
                }

                $this->distant_tar_content = Apparatus::hex2bin(fgets($handle_tmp_dist_file));
                //$this->distant_tar_content = fgets($handle_tmp_dist_file);
                fclose($handle_tmp_dist_file);
            } else {
                $this->distant_tar_content = '';
            }
        }

        return true;
    }

    public function writeTmpDistFile()
    {
        if ($this->config->create_on_distant) {
            if (!isset($this->distant_tar_content) || !$this->distant_tar_content) {
                $this->distant_tar_content = '';
            }

            if (!($handle_tmp_dist_file = fopen($this->tmp_dist_file, 'w+'))) {
                $this->log('ERR'.$this->l('The temporary distant file cannot be opened', self::PAGE));
                return $this->endWithError();
            }

            $content = '';

            if ($this->distant_tar_content) {
                $content = bin2hex($this->distant_tar_content);
            }

            //$content = $this->distant_tar_content;

            if (fwrite($handle_tmp_dist_file, $content) === false) {
                $this->log('ERR'.$this->l('The temporary distant file cannot be written', self::PAGE));
                $this->log($content, true);
                return $this->endWithError();
            }

            fclose($handle_tmp_dist_file);
        }
    }
}
