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

class Backups extends ObjectModel
{
    /** @var    integer     id_ntbr_config */
    public $id_ntbr_config;

    /** @var    String      backup_name */
    public $backup_name;

    /** @var    String      comment */
    public $comment;

    /** @var    Bool        safe */
    public $safe;

    /** @var    String      date_add */
    public $date_add;

    /** @var    String      date_upd */
    public $date_upd;

/**********************************************************/

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table'             => 'ntbr_backups',
        'primary'           => 'id_ntbr_backups',
        'multilang'         => false,
        'multilang_shop'    => false,
        'fields'            => array(
            'id_ntbr_config'    =>  array(
                'type'      => self::TYPE_INT,
                'validate'  => 'isUnsignedInt',
                'required'  => true,
            ),
            'backup_name'       =>  array(
                'type'      => self::TYPE_STRING,
                'validate'  => 'isString',
                'required'  => true,
            ),
            'comment'           =>  array(
                'type'      => self::TYPE_STRING,
                'validate'  => 'isString',
            ),
            'safe'              =>  array(
                'type'      => self::TYPE_BOOL,
                'validate'  => 'isBool',
                'default'   => '0',
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
     * Get the comment of a backup
     *
     * @param   String   $backup_name   Name of the backup
     *
     * @return  String                  Comment of the backup
     */
    public static function getBackupComment($backup_name)
    {
        return Db::getInstance()->getValue('
            SELECT `comment`
            FROM `'._DB_PREFIX_.'ntbr_backups`
            WHERE `backup_name` = "'.pSQL($backup_name).'"
        ');
    }

    /**
     * Get the id_config of a backup
     *
     * @param   String   $backup_name   Name of the backup
     *
     * @return  integer                 ID of the config used to create this backup
     */
    public static function getBackupIdConfig($backup_name)
    {
        return (int)Db::getInstance()->getValue('
            SELECT `id_ntbr_config`
            FROM `'._DB_PREFIX_.'ntbr_backups`
            WHERE `backup_name` = "'.pSQL($backup_name).'"
        ');
    }

    /**
     * Check if the backup exist in database
     *
     * @param   String   $backup_name   Name of the backup
     *
     * @return  integer                 ID of the config used to create this backup
     */
    public static function backupExist($backup_name)
    {
        return (int)Db::getInstance()->getValue('
            SELECT COUNT(`id_ntbr_config`)
            FROM `'._DB_PREFIX_.'ntbr_backups`
            WHERE `backup_name` = "'.pSQL($backup_name).'"
        ');
    }

    /**
     * Get infos of a backup
     *
     * @param   String   $backup_name   Name of the backup
     *
     * @return  array                   Infos of the backup
     */
    public static function getBackupInfos($backup_name)
    {
        $infos = Db::getInstance()->getRow('
            SELECT `id_ntbr_backups`, `id_ntbr_config`, `backup_name`, `comment`, `safe`
            FROM `'._DB_PREFIX_.'ntbr_backups`
            WHERE `backup_name` = "'.pSQL($backup_name).'"
        ');

        if (!is_array($infos)) {
            return array();
        }

        return $infos;
    }

    /**
     * Get list of backups infos
     *
     * @return  array   List of backups infos
     */
    public static function getListBackupsInfos()
    {
        $backups = array();

        $list = Db::getInstance()->executeS('
            SELECT `id_ntbr_backups`, `id_ntbr_config`, `backup_name`, `comment`, `safe`
            FROM `'._DB_PREFIX_.'ntbr_backups`
        ');

        if (!is_array($list)) {
            return array();
        }

        foreach ($list as $item) {
            $backups[$item['backup_name']] = $item;
        }

        return $backups;
    }

    /**
     * Get list of backups infos for a given config ID
     *
     * @return  array   List of backups infos
     */
    public static function getListBackupsInfosByConfig($id_ntbr_config)
    {
        $backups = Db::getInstance()->executeS('
            SELECT `id_ntbr_backups`, `backup_name`, `comment`, `safe`
            FROM `'._DB_PREFIX_.'ntbr_backups`
            WHERE `id_ntbr_config` = '.(int)$id_ntbr_config.'
        ');

        if (!is_array($backups)) {
            return array();
        }

        return $backups;
    }
}
