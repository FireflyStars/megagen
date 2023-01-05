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

class OwncloudLib
{
    const PAGE = 'owncloud';

    /**
     * @var string The URL for API requests.
     */
    const API_URL = 'remote.php/webdav/';

    /**
     * @var string The node for the available quota.
     */
    const AVAILABLE_QUOTA = 'D:QUOTA-AVAILABLE-BYTES';

    /**
     * @var string The response tag.
     */
    const TAG_RESPONSE = 'D:RESPONSE';

    /**
     * @var int The maximal size of a file to upload
     */
    const MAX_FILE_UPLOAD_SIZE = 62914560; // 60Mo (60 * 1024 * 1024 = 62 914 560)

    /**
     * @var int The maximal size of a content to upload
     */
    const MAX_CONTENT_UPLOAD_SIZE = 10485760; // 10Mo (10 * 1024 * 1024 = 10 485 760)

    /**
     * @var string The cryptage key
     */
    const CLE_CRYPTAGE = 'D_T+rW*H`0b84ra.YIen(X|>_Ot&|va;9odG:Gkk3meU=y5kBf3}Yuim';

    /**
     * @var string The cipher
     */
    const CIPHER_CRYPTAGE = 'aes-256-cbc';

    // The current server
    private $server;
    // The current user
    private $user;
    // The current password
    private $pass;
    // The sdk uri
    private $sdk_uri;
    // The physic sdk uri
    private $physic_sdk_uri;
    // Instance of NtbrCore
    private $ntbr;


    public function __construct($ntbr, $server, $user, $pass, $sdk_uri, $physic_sdk_uri)
    {
        if (Tools::substr($server, -1) != '/') {
            $server .= '/';
        }

        $this->server = $server;
        $this->user = $user;
        $this->pass = $pass;
        $this->sdk_uri = $sdk_uri;
        $this->physic_sdk_uri = $physic_sdk_uri;
        $this->ntbr = $ntbr;
    }

    /**
     * Create a curl with default options and any other given options
     *
     * @param   array       $curl_more_options  Further curl options to set. Default array().
     *
     * @return  resource    The curl
     */
    private function createCurl($curl_more_options = array())
    {
        $curl_default_options = array(
            // Default option (http://php.net/manual/fr/function.curl-setopt.php)
            CURLOPT_USERPWD => $this->user.':'.$this->pass,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false
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
     * Performs a call to the ownCloud API using the POST method.
     *
     * @param   string          $url        The url of the API call.
     * @param   array|object    $data       The data to pass in the body of the request.
     * @param   array           $header     The data to pass in the header of the request.
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
     * Performs a call to the ownCloud API using the GET method.
     *
     * @param   string  $url   The url of the API call.
     *
     * @return  array   The response of the execution of the curl.
     */
    public function apiGet($url, $options = array())
    {
        $curl = $this->createCurl($options);

        curl_setopt($curl, CURLOPT_URL, $url);
        return $this->execCurl($curl);
    }

    /**
     * Performs a call to the ownCloud API using the PUT method.
     *
     * @param   string      $url                The path of the API call.
     * @param   ressource   $stream             The data to upload.
     * @param   array       $header             The data to pass in the header of the request.
     * @param   array       $other_options      Other options to use in the request.
     * @param   float       $filesize           The size of the stream
     *
     * @return  array       The result of the execution of the curl.
     */
    public function apiPut($url, $stream, $header = array(), $other_options = array(), $filesize = 0)
    {
        //$header[] = 'Content-Type: application/octet-stream';

        $curl = $this->createCurl(array(), $header);

        if (!(float)$filesize) {
            $stats      = fstat($stream);
            $filesize   = $stats[7];
        }

        $options = array(
            CURLOPT_URL         => $url,
            CURLOPT_HTTPHEADER  => $header,
            CURLOPT_PUT         => true,
            CURLOPT_INFILE      => $stream,
            CURLOPT_INFILESIZE  => $filesize,
        );

        if (is_array($other_options) && count($other_options)) {
            $options = array_merge($options, $other_options);
        }

        curl_setopt_array($curl, $options);
        return $this->execCurl($curl);
    }

    /**
     * Get the available quota of the current ownCloud account.
     *
     * @return  int     Available quota
     */
    public function getAvailableQuota()
    {
        $quota_available = 0;

        $options = array(
            CURLOPT_CUSTOMREQUEST => 'PROPFIND'
        );

        $result = $this->apiGet($this->server.self::API_URL, $options);
        //$this->log($result);

        if ($result['success'] && !empty($result['result'])) {
            if (isset($result['result']['d_response'][0]['d_propstat']['d_prop']['d_quota-available-bytes'])) {
                $quota_available = $result['result']['d_response'][0]['d_propstat']['d_prop']['d_quota-available-bytes'];
            } elseif (isset($result['result']['d_response']['d_propstat']['d_prop']['d_quota-available-bytes'])) {
                $quota_available = $result['result']['d_response']['d_propstat']['d_prop']['d_quota-available-bytes'];
            }
        }

        $this->log(
            sprintf($this->ntbr->l('Sending to %s account:', self::PAGE), NtbrChild::OWNCLOUD)
            .' '.$this->ntbr->l('Available quota:', self::PAGE).' '.$quota_available
        );

        return $quota_available;
    }

    /**
     * Test the connection
     *
     * @return  bool    Connection result
     */
    public function testConnection()
    {
        $options = array(
            CURLOPT_CUSTOMREQUEST => 'PROPFIND'
        );

        $result = $this->apiGet($this->server.self::API_URL, $options);

        return $result['success'];
    }

    /**
     * Upload a file on the ownCloud account
     *
     * @param   string  $file_path          The path of the file.
     * @param   string  $file_destination   The destination of the file.
     * @param   string  $name               The name of the file.
     * @param   int     $position           Position in the file.
     * @param   int     $nb_part            Current part number.
     * @param   int     $nb_part_total      Total parts to be sent.
     *
     * @return  bool    The success or failure of the action.
     */
    public function uploadFile($file_path, $file_destination = '', $name = '', $position = 0, $nb_part = 1, $nb_part_total = 1)
    {
        if (!$name || $name == '') {
            $name = basename($file_path);
        }

        $url                = $this->server.self::API_URL.$file_destination.$name;
        $filesize           = (float)$this->ntbr->getFileSize($file_path);
        $total_file_size    = $filesize;
        $rest_to_upload     = $total_file_size - $position;
        $start_file_part    = $position;
        $chunk_count        = ceil($total_file_size / self::MAX_FILE_UPLOAD_SIZE);

        $file = fopen($file_path, 'r');

        if ($position > 0) {
            // Go to the last position in the file
            $file = $this->ntbr->goToPositionInFile($file, $position, false);

            if ($file === false) {
                return false;
            }
        }

        if ($rest_to_upload > self::MAX_FILE_UPLOAD_SIZE) {
            $file_part_size = self::MAX_FILE_UPLOAD_SIZE;
            $end_file_part  = $start_file_part + $file_part_size - 1;// Size minus 1 cause we start from 0 in the size parts.
        } else {
            $file_part_size = $rest_to_upload;
            $end_file_part  = $start_file_part + $rest_to_upload - 1;// Size minus 1 cause we start from 0 in the size parts.
        }

        while ($rest_to_upload > 0) {
            $options = array();

            if ($chunk_count > 1) {
                $headers  = array(
                    'Content-Length: '.$file_part_size,
                    'OC-Chunked: 1 ',
                    'OC-Total-Length: '.$total_file_size,
                    'OC-Chunk-Size: '.$file_part_size,
                    //'Content-Range: bytes '.$start_file_part.'-'.$end_file_part.'/'.$total_file_size
                );

                $chunk_url = $url.'-chunking-'.$this->ntbr->owncloud_session.'-'.$chunk_count.'-'.$this->ntbr->owncloud_nb_chunk;
            } else {
                $headers  = array(
                    'Content-Length: '.$file_part_size,
                );

                $chunk_url = $url;
            }

            if ($chunk_count > 0) {
                $percent = ($this->ntbr->owncloud_nb_chunk/$chunk_count) * 100;
            } else {
                $percent = 0;
            }

            if ($nb_part_total > 1) {
                $this->log(
                    sprintf($this->ntbr->l('Sending to %s account:', self::PAGE), NtbrChild::OWNCLOUD)
                    .' '.$nb_part.'/'.$nb_part_total.$this->ntbr->l(':', self::PAGE).' '.(int)$percent.'%'
                );
            } else {
                $this->log(
                    sprintf($this->ntbr->l('Sending to %s account:', self::PAGE), NtbrChild::OWNCLOUD)
                    .' '.(int)$percent.'%'
                );
            }

            $part_file = fread($file, $file_part_size);

            $stream = fopen('php://temp/maxmemory:'.self::MAX_FILE_UPLOAD_SIZE, 'rw');

            if (false === $stream) {
                $this->log('WAR'.$this->ntbr->l('Error while creating your file: the temporary file cannot be opened.', self::PAGE).' ('.$file_path.')');
                return false;
            }

            if (false === fwrite($stream, $part_file)) {
                fclose($stream);
                $this->log('WAR'.$this->ntbr->l('Error while creating your file: the temporary file cannot be written.', self::PAGE).' ('.$file_path.')');
                return false;
            }

            if (!rewind($stream)) {
                fclose($stream);
                $this->log('WAR'.$this->ntbr->l('Error while creating your file: the temporary file cannot be rewound.', self::PAGE).' ('.$file_path.')');
                return false;
            }

            $result_upload = $this->apiPut($chunk_url, $stream, $headers, $options, $file_part_size);

            if (!$result_upload['success']) {
                return false;
            }

            $start_file_part = ($end_file_part + 1);
            $rest_to_upload -= $file_part_size;

            if ($rest_to_upload > self::MAX_FILE_UPLOAD_SIZE) {
                $file_part_size = self::MAX_FILE_UPLOAD_SIZE;
                $end_file_part = ($start_file_part + $file_part_size - 1);
            } else {
                $file_part_size = $rest_to_upload;
                $end_file_part = ($start_file_part + $rest_to_upload - 1);
            }

            $this->ntbr->owncloud_nb_chunk++;
            $this->ntbr->owncloud_position = $start_file_part;

            //refresh
            $this->ntbr->refreshBackup(true);
        }


        //$result = $this->apiPut($url, $file, array(), array(), $filesize);

        fclose($file);

        return true;
    }

    /**
     * Upload content on the account
     *
     * @param   string  $content            The content to upload.
     * @param   int     $content_size       The size of the content to upload.
     * @param   int     $total_file_size    The total size of the file the content is part of
     * @param   string  $name               The name of the file.
     * @param   string  $file_destination   The destination of the file.
     *
     * @return  bool    The success or failure of the action.
     */
    public function uploadContent($content, $content_size, $total_file_size, $name, $file_destination = '')
    {
        $url            = $this->server.self::API_URL.$file_destination.$name;
        $chunk_count    = ceil($total_file_size / self::MAX_CONTENT_UPLOAD_SIZE);

        if ($chunk_count > 1) {
            $headers  = array(
                'Content-Length: '.$content_size,
                'OC-Chunked: 1 ',
                'OC-Total-Length: '.$total_file_size,
                'OC-Chunk-Size: '.$content_size,
                //'Content-Range: bytes '.$start_file_part.'-'.$end_file_part.'/'.$total_file_size
            );

            $chunk_url = $url.'-chunking-'.$this->ntbr->owncloud_session.'-'.$chunk_count.'-'.$this->ntbr->owncloud_nb_chunk;
        } else {
            $headers  = array(
                'Content-Length: '.$content_size,
            );

            $chunk_url = $url;
        }

        $stream = fopen('php://temp/maxmemory:'.self::MAX_CONTENT_UPLOAD_SIZE, 'rw');

        if (false === $stream) {
            $this->log('WAR'.$this->ntbr->l('Error while creating your file: the temporary file cannot be opened.', self::PAGE));
            return false;
        }

        if (false === fwrite($stream, $content)) {
            fclose($stream);
            $this->log('WAR'.$this->ntbr->l('Error while creating your file: the temporary file cannot be written.', self::PAGE));
            return false;
        }

        if (!rewind($stream)) {
            fclose($stream);
            $this->log('WAR'.$this->ntbr->l('Error while creating your file: the temporary file cannot be rewound.', self::PAGE));
            return false;
        }

        $result_upload = $this->apiPut($chunk_url, $stream, $headers, array(), $content_size);

        fclose($stream);

        if (!$result_upload['success']) {
            return false;
        }

        $this->ntbr->owncloud_nb_chunk++;

        return true;
    }

    /**
     * Download a file from an ownCloud account
     *
     * @param   string      $file_path          The path of the file.
     * @param   int         $position           Position in the file.
     * @param   int         $lenght             Lenght of the file to get.
     * @param   int         $total_file_size    Total size of the file to get.
     *
     * @return  binary|bool                     Part of the file or false
     */
    public function downloadFile($file_path, $position, $lenght, $total_file_size)
    {
        if (!$lenght) {
            $lenght = NtbrChild::MAX_FILE_DOWNLOAD_SIZE;
        }

        $url = $this->server.self::API_URL.$file_path;
        $start_file_part    = $position;
        $end_file_part      = ($lenght + $position) - 1;

        if ($end_file_part > $total_file_size) {
            $end_file_part = $total_file_size;
        }

        $headers  = array(
            'Range: bytes='.$start_file_part.'-'.$end_file_part,
        );

        $options = array(
            CURLOPT_HTTPHEADER  => $headers,
        );

        $result = $this->apiGet($url, $options);

        return $result['result'];
    }

    /**
     * Delete a file on the ownCloud account
     *
     * @param   string  $file_path          The path of the file on ownCloud.
     *
     * @return  bool    The success or failure of the action.
     */
    public function deleteFile($file_path)
    {
        $options = array(
           CURLOPT_CUSTOMREQUEST => 'DELETE'
        );

        $result = $this->apiGet($this->server.self::API_URL.$file_path, $options);

        return $result['success'];
    }

    /**
     * Create a folder in the ownCloud account
     *
     * @param   string  $folder_path          The path of the folder on ownCloud.
     *
     * @return  bool    The success or failure of the action.
     */
    public function createFolder($folder_path)
    {
        $options = array(
           CURLOPT_CUSTOMREQUEST => 'MKCOL'
        );

        $result = $this->apiGet($this->server.self::API_URL.$folder_path, $options);

        return $result['success'];
    }

    /**
     * Check if the given folder exists
     *
     * @return  bool    If the folder exists
     */
    public function folderExists($folder_path)
    {
        $options = array(
            CURLOPT_CUSTOMREQUEST => 'PROPFIND'
        );

        $result = $this->apiGet($this->server.self::API_URL.$folder_path, $options);

        if ($result['success'] && !empty($result['result'])) {
            $response = array();

            if (isset($result['result']['d_response'][0])) {
                $response = $result['result']['d_response'][0];
            } elseif ($result['result']['d_response']) {
                $response = $result['result']['d_response'];
            }

            if (isset($response['d_propstat']['d_prop']['d_quota-available-bytes'])) {
                return true;
            }
            /*foreach ($result['result'] as $node) {
                if ($node['tag'] === self::AVAILABLE_QUOTA) {
                    return true;
                }
            }*/
        }

        return false;
    }

    /**
     * Check if the given file exists
     *
     * @return  bool    If the file exists
     */
    public function fileExists($file_path)
    {
        $options = array(
            CURLOPT_CUSTOMREQUEST => 'PROPFIND'
        );

        $result = $this->apiGet($this->server.self::API_URL.$file_path, $options);

        if ($result['success'] && !empty($result['result'])) {
            if (isset($result['result']['d_response']['d_propstat']['d_prop']['d_getcontentlength'])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get infos of a file
     *
     * @return  array   Infos of the file
     */
    public function getFileInfo($path)
    {
        $infos = array(
            'name'      => basename($path),
            'size_byte' => 0,
        );

        $options = array(
            CURLOPT_CUSTOMREQUEST => 'PROPFIND'
        );

        $result = $this->apiGet($this->server.self::API_URL.$path, $options);
        $this->log($result, true);

        if ($result['success'] && !empty($result['result'])) {
            if (
                isset($result['result']['d_response'])
                && isset($result['result']['d_response']['d_propstat'])
                && isset($result['result']['d_response']['d_propstat']['d_prop'])
                && isset($result['result']['d_response']['d_propstat']['d_prop']['d_getcontentlength'])
            ) {
                $infos['size_byte'] = $result['result']['d_response']['d_propstat']['d_prop']['d_getcontentlength'];
            }
        }

        return $infos;
    }

    /**
     * Get all of the files children of the given folder
     *
     * @return  array   List of the files
     */
    public function getFileChildren($folder_path = '')
    {
        $list_files = array();

        if (Tools::substr($folder_path, -1) == '/') {
            $folder_path = Tools::substr($folder_path, 0, -1);
        }

        $path_to_clear = '/'.self::API_URL.$folder_path.'/';

        $options = array(
            CURLOPT_CUSTOMREQUEST => 'PROPFIND'
        );

        $result = $this->apiGet($this->server.self::API_URL.$folder_path, $options);

        if ($result['success'] && !empty($result['result'])) {
            if (isset($result['result']['d_response'][0])) {
                $list_nodes = $result['result']['d_response'];
            } elseif (isset($result['result']['d_response'])) {
                $list_nodes[] = $result['result']['d_response'];
            } else {
                return $list_files;
            }

            foreach ($list_nodes as $node) {
                if (!isset($node['d_propstat']['d_prop']['d_resourcetype']['d_collection']) && isset($node['d_href'])) {
                    $size = 0;

                    if (isset($node['d_propstat']) && isset($node['d_propstat'][0]) && isset($node['d_propstat'][0]['d_prop']) && isset($node['d_propstat'][0]['d_prop']['d_getcontentlength'])) {
                        $size = $node['d_propstat'][0]['d_prop']['d_getcontentlength'];
                    }

                    $file = str_ireplace($path_to_clear, '', urldecode($node['d_href']));

                    if ($file) {
                        $list_files[] = array(
                            'name'  => $file,
                            'size'  => $size,
                        );
                    }
                }
            }
        }

        return $list_files;
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
