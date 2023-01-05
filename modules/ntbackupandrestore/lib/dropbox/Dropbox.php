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

require_once 'dropbox-config.php';

class DropboxLib
{
    const PAGE = 'dropbox';
    /**
     * @var string The base URL for authorization requests.
     */
    const AUTH_URL = 'https://www.dropbox.com/oauth2/authorize';

    /**
     * @var string The base URL for token requests.
     */
    const TOKEN_URL = 'https://api.dropboxapi.com/oauth2/token';

    /**
     * @var string The base URL for API requests.
     */
    const API_URL = 'https://api.dropboxapi.com/2/';

    /**
     * @var string The base URL for files requests.
     */
    const FILES_URL = 'https://content.dropboxapi.com/2/';

    /**
     * @var int The maximal size of a file to upload
     */
    const MAX_FILE_UPLOAD_SIZE = 10485760; // 10Mo (10 * 1024 * 1024 = 10 485 760)

    /**
     * @var int The maximal size of the log file
     */
    const MAX_FILE_LOG_SIZE = 200000000; // environs 200Mo (200 000 000)

    /**
     * @var string The name of the current log file
     */
    const CURRENT_FILE_LOG_NAME = 'current_log_dropbox.txt';

    /**
     * @var string The name of old log file
     */
    const OLD_FILE_LOG_NAME = '_log_dropbox.txt';


    // The current token
    private $token;
    // The app key
    private $app_key;
    // The app secret
    private $app_secret;
    // The sdk uri
    private $sdk_uri;
    // The physic sdk uri
    private $physic_sdk_uri;
    // Instance of NtbrCore
    private $ntbr;


    public function __construct($ntbr, $sdk_uri, $physic_sdk_uri, $token = '')
    {
        $this->app_key          = DROPBOX_APP_KEY;
        $this->app_secret       = DROPBOX_APP_SECRET;
        $this->sdk_uri          = $sdk_uri;
        $this->physic_sdk_uri   = $physic_sdk_uri;
        $this->ntbr             = $ntbr;

        if (!empty($token)) {
            $this->token = $token;
        }
    }

    /**
     * Create a curl with default options and any other given options
     *
     * @param   array       $curl_more_options  Further curl options to set. Default array().
     *
     * @return  resource    The curl
     */
    private function createCurl($curl_more_options = array(), $curl_header = array())
    {
        if (!empty($this->token)) {
            $curl_header[] = 'Authorization: Bearer '.$this->token;
        } else {
            $clientCredentials = $this->app_key.":".$this->app_secret;
            $authHeaderValue = "Basic ".base64_encode($clientCredentials);

            $curl_header[] = 'Authorization: '.$authHeaderValue;
        }

        $curl_default_options = array(
            // Default option (http://php.net/manual/fr/function.curl-setopt.php)
            CURLOPT_HTTPHEADER => $curl_header,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_CAINFO => $this->physic_sdk_uri.'certificat.crt'
        );

        $curl = curl_init();

        curl_setopt_array($curl, $curl_default_options);

        if (count($curl_more_options)) {
            curl_setopt_array($curl, $curl_more_options);
        }

        return $curl;
    }

    /**
     * Execute a curl and return it's result
     *
     * @param   resource    $curl       The curl to execute.
     *
     * @return  array       The result of the execution of the curl.
     */
    private function execCurl($curl)
    {
        return $this->ntbr->execCurl($curl);
    }

    /**
     * Performs a call to the Dropbox API using the POST method.
     *
     * @param   string          $url   The url of the API call.
     * @param   array|object    $data   The data to pass in the body of the request.
     *
     * @return  array           The result of the execution of the curl.
     */
    public function apiPost($url, $data = array(), $header = array())
    {
        $curl = $this->createCurl(array(), $header);

        $options = array(
            CURLOPT_URL        => $url,
            CURLOPT_POST       => true,
            CURLOPT_POSTFIELDS => $data
        );

        curl_setopt_array($curl, $options);

        return $this->execCurl($curl);
    }

    /**
     * Performs a call to the Dropbox API using the GET method.
     *
     * @param   string  $url   The url of the API call.
     *
     * @return  array   The response of the execution of the curl.
     */
    public function apiGet($url)
    {
        $curl = $this->createCurl();

        curl_setopt($curl, CURLOPT_URL, $url);
        return $this->execCurl($curl);
    }

    /**
     * Performs a call to the Dropbox API using the PUT method.
     *
     * @param   string      $url                The path of the API call.
     * @param   string      $data               The data to upload.
     *
     * @return  array       The result of the execution of the curl.
     */
    public function apiPut($url, $data)
    {
        $curl = $this->createCurl(array(), array("Content-Type: application/octet-stream"));

        $options = array(
            CURLOPT_URL                 =>  $url,
            CURLOPT_CUSTOMREQUEST       =>  "PUT",
            CURLOPT_POSTFIELDS          =>  $data
        );

        curl_setopt_array($curl, $options);
        return $this->execCurl($curl);
    }

     /**
     * Gets the URL of the authorize in form.
     *
     * @return  string|bool   The login URL or false.
     *
     */
    public function getLogInUrl()
    {
        $url = self::AUTH_URL
            .'?client_id='.$this->app_key
            .'&response_type=code';

        return $url;
    }

     /**
     * Gets the URL of the authorize in form.
     *
     * @return  string|bool   The login URL or false.
     *
     */
    public function getToken($code)
    {
        $datas = array(
            'code' => $code,
            'grant_type' => 'authorization_code',
        );

        $result = $this->apiPost(self::TOKEN_URL, $datas);

        if ($result['success']) {
            return $result['result']['access_token'];
        }

        return false;
    }

    /**
     * Test the connection to the Dropbox account
     *
     * @return  bool    Connection result
     */
    public function testConnection()
    {
        $result = $this->apiPost(self::API_URL.'users/get_space_usage');

        return $result['success'];
    }

    /**
     * Get the available quota of the current Dropbox account.
     *
     * @return  int     Available quota
     */
    public function getAvailableQuota()
    {
        $quota_available = 0;
        $quota_total = 0;
        $result = $this->apiPost(self::API_URL.'users/get_space_usage');

        if ($result['success'] && !empty($result['result'])) {
            if (isset($result['result']['used']) && isset($result['result']['allocation']['allocated'])) {
                $quota_total = $result['result']['allocation']['allocated']; // The user's total quota allocation (bytes).
                $quota_used = $result['result']['used']; // The user's used quota outside of shared folders (bytes).
                //$quota_shared_used = $result['result']['quota_info']['shared']; // The user's used quota in shared folders (bytes).

                $quota_available = $quota_total - $quota_used;
            }
        }

        $this->log(
            sprintf($this->ntbr->l('Sending to %s account:', self::PAGE), NtbrChild::DROPBOX)
            .' '.$this->ntbr->l('Available quota:', self::PAGE).' '.$quota_available.'/'.$quota_total
        );

        return $quota_available;
    }

    /**
     * Import a file to the module backup directory
     *
     * @param   string  $file_path          The path of the file.
     * @param   string  $file_destination   The destination of the file (backup module directory).
     * @param   int     $nb_part            Current part number.
     * @param   int     $nb_part_total      Total parts to be imported.
     *
     * @return  bool    The success or failure of the action.
     */
    public function importFile($file_path, $file_destination, $nb_part = 1, $nb_part_total = 1)
    {
        // Get file metadata
        $file_size          = 0;
        $header_metadata    = array('Content-Type: application/json');

        $datas_metadata = array(
            'path'                                  => $file_path,
            'include_media_info'                    => false,
            'include_deleted'                       => false,
            'include_has_explicit_shared_members'   => false,
        );

        $metadata_result = $this->apiPost(self::API_URL.'files/get_metadata', Tools::jsonEncode($datas_metadata), $header_metadata);

        if ($metadata_result['success'] && !empty($metadata_result['result']) && isset($metadata_result['result']['size'])) {
            if (!isset($metadata_result['result']['is_downloadable']) || !$metadata_result['result']['is_downloadable']) {
                $this->log($this->ntbr->l('The file does not have the authorisation to be import', self::PAGE), true);
                return false;
            }

            $file_size = $metadata_result['result']['size'];
        }

        if ($file_size < 0) {
            $this->log($this->ntbr->l('The file size is not valid', self::PAGE), true);
            return false;
        }

        // Import file
        $file_part_size_max = self::MAX_FILE_UPLOAD_SIZE;
        $byte_offset        = 0;

        $datas_download = array(
            'path'  => $file_path,
        );

        $header_download    = array('Dropbox-API-Arg: '.Tools::jsonEncode($datas_download));

        //$header[] = 'Range: bytes=0-'.$file_part_size_max;

        $download_result = $this->apiPost(self::FILES_URL.'files/download', array(), $header_download);

        if (($handle = fopen($file_destination, 'w+')) === false) {
            $this->log($this->ntbr->l('The file cannot be created', self::PAGE), true);
            return false;
        }

        if (fwrite($handle, $download_result['result']) === false) {
            if (!is_resource($handle)) {
                $this->log(
                    $this->ntbr->l('Error while creating the file, unable to write data. The file is not valid.', self::PAGE), true
                );
                return false;
            } elseif (!is_string($download_result['result'])) {
                $this->log(
                    $this->ntbr->l('Error while creating the file, unable to write data. The content is not valid.', self::PAGE), true
                );
                return false;
            } else {
                $this->log(
                    $this->ntbr->l('Error while creating files, unable to write data. Please check space available.', self::PAGE), true
                );
                return false;
            }
        }

        if (!fclose($handle)) {
            $this->log($this->ntbr->l('The file was not closed', self::PAGE), true);
        }

        return true;
    }

    /**
     * Resume the import of a file to the module backup directory
     *
     * @param   string  $file_path          The path of the file.
     * @param   string  $file_destination   The destination of the file (backup module directory).
     * @param   int     $position           Position in the file.
     * @param   int     $nb_part            Current part number.
     * @param   int     $nb_part_total      Total parts to be imported.
     *
     * @return  bool    The success or failure of the action.
     */
    public function resumeImportFile($file_path, $file_destination, $position, $nb_part = 1, $nb_part_total = 1)
    {
        $file_part_size_max = self::MAX_FILE_UPLOAD_SIZE;
        $url                = self::FILES_URL.'files/download';
        $header             = array('Content-Type: application/octet-stream');
        $byte_offset        = $position;
    }

    /**
     * Upload a file on the Dropbox account
     *
     * @param   string  $file_path          The path of the file.
     * @param   string  $file_destination   The destination of the file.
     * @param   int     $nb_part            Current part number.
     * @param   int     $nb_part_total      Total parts to be sent.
     *
     * @return  bool    The success or failure of the action.
     */
    public function uploadFile($file_path, $file_destination, $nb_part = 1, $nb_part_total = 1)
    {
        $file_part_size_max = self::MAX_FILE_UPLOAD_SIZE;
        $url_start          = self::FILES_URL.'files/upload_session/start';
        $byte_offset        = 0;
        $header             = array('Content-Type: application/octet-stream');

        $file               = fopen($file_path, "r+");
        $total_file_size    = filesize($file_path);

        if ($file_part_size_max >= $total_file_size) {
            $file_part_size_max = $total_file_size;
        }

        $part_file  = fread($file, $file_part_size_max);
        $result     = $this->apiPost($url_start, $part_file, $header);

        if ($result['success'] === false) {
            return false;
        }

        $uploadId = $result['result']['session_id'];
        $this->ntbr->dropbox_upload_id = $uploadId;
        $byte_offset += $file_part_size_max;
        $this->ntbr->dropbox_position = $byte_offset;

        return $this->resumeUploadFile($file_path, $file_destination, $uploadId, $byte_offset, $nb_part, $nb_part_total);
    }

    /**
     * Resume the upload of a file on the Dropbox account
     *
     * @param   string  $file_path          The path of the file.
     * @param   string  $file_destination   The destination of the file.
     * @param   string  $uploadId           Id of the upload.
     * @param   int     $position           Position in the file.
     * @param   int     $nb_part            Current part number.
     * @param   int     $nb_part_total      Total parts to be sent.
     *
     * @return  bool    The success or failure of the action.
     */
    public function resumeUploadFile($file_path, $file_destination, $uploadId, $position, $nb_part = 1, $nb_part_total = 1)
    {
        $file_part_size_max = self::MAX_FILE_UPLOAD_SIZE;
        $url_append         = self::FILES_URL.'files/upload_session/append_v2';
        $url_finish         = self::FILES_URL.'files/upload_session/finish';
        $header             = array('Content-Type: application/octet-stream');
        $byte_offset        = $position;

        $file               = fopen($file_path, "r+");
        $total_file_size    = filesize($file_path);

        if ($position > 0) {
            // Go to the last position in the file
            $file = $this->ntbr->goToPositionInFile($file, $position, false);

            if ($file === false) {
                return false;
            }
        }

        while (!feof($file)) {
            $part_file = fread($file, $file_part_size_max);
            $rest = $total_file_size - $byte_offset;

            if ($rest < $file_part_size_max) {
                $file_part_size_max = $rest;
            }

            $byte_to_go = $total_file_size - ($byte_offset + $file_part_size_max);

            $percent = ($byte_offset/$total_file_size) * 100;

            if ($nb_part_total > 1) {
                $this->log(
                    sprintf($this->ntbr->l('Sending to %s account:', self::PAGE), NtbrChild::DROPBOX)
                    .' '.$nb_part.'/'.$nb_part_total.$this->ntbr->l(':', self::PAGE).' '.(int)$percent.'%'
                );
            } else {
                $this->log(
                    sprintf($this->ntbr->l('Sending to %s account:', self::PAGE), NtbrChild::DROPBOX)
                    .' '.(int)$percent.'%'
                );
            }

            if ($byte_to_go > 0) {
                $header_append = $header;

                $datas_header_append = array(
                    'cursor' => array(
                        'session_id'    => $uploadId,
                        'offset'        => $byte_offset
                    )
                );

                $header_append[] = 'Dropbox-API-Arg: '.Tools::jsonEncode($datas_header_append);
                $result = $this->apiPost($url_append, $part_file, $header_append);

                if ($result['success'] !== false) {
                    $byte_offset += $file_part_size_max;
                    $this->ntbr->dropbox_position = $byte_offset;
                } else {
                    return false;
                }
            } else {
                $header_finish = $header;
                $datas_header_finish = array(
                    'cursor' => array(
                        'session_id'    => $uploadId,
                        'offset'        => $byte_offset
                    ),
                    'commit' => array(
                        'path'          => $file_destination,
                        'mode'          => 'add',
                        'autorename'    => true
                    )
                );

                $header_finish[] = 'Dropbox-API-Arg: '.Tools::jsonEncode($datas_header_finish);
                $result_commit = $this->apiPost($url_finish, $part_file, $header_finish);
                fclose($file);

                return $result_commit['success'];
            }

            //refresh
            $this->ntbr->refreshBackup(true);
        }
        return false;
    }

    /**
     * Create a resumable session
     *
     * @return  bool                The success or failure of the action.
     */
    public function createSession()
    {
        $url_start  = self::FILES_URL.'files/upload_session/start';
        $header     = array('Content-Type: application/octet-stream');

        $result     = $this->apiPost($url_start, '', $header);

        if ($result['success'] === false) {
            return false;
        }

        $this->ntbr->dropbox_upload_id  = $result['result']['session_id'];
        $this->ntbr->old_percent        = 0;

        return true;
    }

    /**
     * Resume the upload of the content on the Dropbox account
     *
     * @param   string  $content            The content to upload.
     * @param   int     $content_size       The size of the content to upload.
     * @param   string  $file_destination   The destination of the file.
     * @param   int     $total_file_size    The total size of the file the content is part of
     * @param   string  $uploadId           Id of the upload.
     * @param   int     $position           Position in the file.
     *
     * @return  bool    The success or failure of the action.
     */
    public function resumeUploadContent($content, $content_size, $file_destination, $total_file_size, $uploadId, $position)
    {
        $url_append         = self::FILES_URL.'files/upload_session/append_v2';
        $url_finish         = self::FILES_URL.'files/upload_session/finish';
        $header             = array('Content-Type: application/octet-stream');
        $byte_offset        = $position;
        $byte_to_go         = $total_file_size - ($byte_offset + $content_size);

        if ($byte_to_go > 0) {
            $datas_header = array(
                'cursor' => array(
                    'session_id'    => $uploadId,
                    'offset'        => $byte_offset
                )
            );

            $header[] = 'Dropbox-API-Arg: '.Tools::jsonEncode($datas_header);
            $result = $this->apiPost($url_append, $content, $header);

            return $result['success'];
        } else {
            $datas_header = array(
                'cursor' => array(
                    'session_id'    => $uploadId,
                    'offset'        => $byte_offset
                ),
                'commit' => array(
                    'path'          => $file_destination,
                    'mode'          => 'add',
                    'autorename'    => true
                )
            );

            $header[] = 'Dropbox-API-Arg: '.Tools::jsonEncode($datas_header);
            $result_commit = $this->apiPost($url_finish, $content, $header);

            $this->ntbr->old_percent = 0;

            return $result_commit['success'];
        }
        return false;
    }

    /**
     * Delete a file on the Dropbox account
     *
     * @param   string  $file_path          The path of the file on Dropbox.
     *
     * @return  bool    The success or failure of the action.
     */
    public function deleteFile($file_path)
    {
        if ($file_path[0] != '/') {
            $file_path = '/'.$file_path;
        }

        $datas = array(
            'path' => $file_path
        );

        $header = array('Content-Type: application/json');

        $result = $this->apiPost(self::API_URL.'files/delete', Tools::jsonEncode($datas), $header);

        return $result['success'];
    }

    /**
     * Create a folder in the Dropbox account
     *
     * @param   string  $folder_path          The path of the folder on Dropbox.
     *
     * @return  bool    The success or failure of the action.
     */
    public function createFolder($folder_path)
    {
        $datas = array(
            'path' => $folder_path
        );

        $header = array('Content-Type: application/json');

        $result = $this->apiPost(self::API_URL.'files/create_folder_v2', Tools::jsonEncode($datas), $header);

        return $result['success'];
    }

    /**
     * Check if a file or folder exists on the Dropbox account
     *
     * @param   string  $item_path      The path of the file or folder on Dropbox.
     *
     * @return  bool                    If the item exists.
     */
    public function checkExists($item_path = '')
    {
        $datas = array(
            'path' => $item_path
        );

        $header = array('Content-Type: application/json');

        $result = $this->apiPost(self::API_URL.'files/get_metadata', Tools::jsonEncode($datas), $header);

        if ($result['success'] === false) {
            return false;
        }

        // If the result is not file or folder then we did not found what we were looking for
        if (isset($result['result']['.tag'])) {
            if (!in_array($result['result']['.tag'], array('file', 'folder'))) {
                return false;
            }
        } else {
            return false;
        }

        return true;
    }

    /**
     * Get the children of a folder on the Dropbox account
     *
     * @param   string  $item_path  The path of the folder on Dropbox.
     *
     * @return  bool|array          The children of the folder or the failure of the action.
     */
    public function listFolderChildren($item_path = '')
    {
        $datas = array(
            'path' => $item_path,
            'recursive' => true
        );

        $header = array('Content-Type: application/json');

        $result = $this->apiPost(self::API_URL.'files/list_folder', Tools::jsonEncode($datas), $header);

        if ($result['success'] === false) {
            return false;
        }

        if (isset($result['result']['has_more']) && isset($result['result']['entries']) && isset($result['result']['cursor']) && $result['result']['has_more']) {
            $result['result']['entries'] = array_merge($result['result']['entries'], $this->listRestFolderChildren($result['result']['cursor']));
        }

        return (array)$result['result'];
    }

    /**
     * Get the rest of the children of a folder on the Dropbox account
     *
     * @param   string  $cursor     The cursor return by Dropbox from the request to list files
     *
     * @return  bool|array          The children of the folder or the failure of the action.
     */
    public function listRestFolderChildren($cursor)
    {
        $datas = array(
            'cursor' => $cursor
        );

        $header = array('Content-Type: application/json');

        $result = $this->apiPost(self::API_URL.'files/list_folder/continue', Tools::jsonEncode($datas), $header);

        if ($result['success'] === false) {
            return false;
        }

        if (!isset($result['result']['entries'])) {
            return array();
        }

        if (isset($result['result']['has_more']) && isset($result['result']['entries']) && isset($result['result']['cursor']) && $result['result']['has_more']) {
            $result['result']['entries'] = array_merge($result['result']['entries'], $this->listRestFolderChildren($result['result']['cursor']));
        }

        return (array)$result['result']['entries'];
    }

    /**
     * Download a file
     *
     * @param   string  $file_id    The ID (or the path) of the file to download
     *
     * @return  bool|array          Infos of the file to download or the failure of the action.
     */
    public function downloadFile($file_id)
    {
        $datas = array(
            'path' => $file_id,
        );

        $header = array('Content-Type: application/json');

        $result = $this->apiPost(self::API_URL.'files/get_temporary_link', Tools::jsonEncode($datas), $header);

        if ($result['success'] === false) {
            return false;
        }

        return (array)$result['result'];
    }

    /**
     * Log()
     *
     * Log message to file
     *
     * @param   string  $message    Message to log
     *
     * @return void
     *
     */
    public function log($message, $not_display_in_module = false)
    {
        $this->ntbr->log($message, $not_display_in_module);
    }
}
