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

var progressBackup;
var ajax_went_wrong_data;
var progress_restore;
var backup_warning              = '';
var refresh_sent                = 0;
var stop_backup                 = 0;
var config_time_bwn_refresh     = 0;
var time_last_refresh           = 0;
var display_progress_only       = 0;
var ajax_went_wrong             = 0;
var ftp_account_id              = '#ftp_account';
var dropbox_account_id          = '#dropbox_account';
var owncloud_account_id         = '#owncloud_account';
var webdav_account_id           = '#webdav_account';
var googledrive_account_id      = '#googledrive_account';
var onedrive_account_id         = '#onedrive_account';
var sugarsync_account_id        = '#sugarsync_account';
var hubic_account_id            = '#hubic_account';
var aws_account_id              = '#aws_account';
var ftp_save_result             = '';
var dropbox_save_result         = '';
var owncloud_save_result        = '';
var hubic_save_result           = '';
var webdav_save_result          = '';
var googledrive_save_result     = '';
var onedrive_save_result        = '';
var sugarsync_save_result       = '';
var aws_save_result             = '';
var nt_content_key              = ['2', 'n', 't'];
var next_key                    = 0;
var file_content                = [];

$(document).ready( function ()
{
    displayAdvancedAutomation();

	$('#nt_tab a').click(function()
	{
		ntTab($(this));
	});

    var display_current_hour = setInterval(function(){
        var time_val = $('#current_hour').text();
        var m = moment(time_val, 'HH:mm:ss');
        $('#current_hour').text(m.add(1, 's').format('HH:mm:ss'));
    }, 1000);

    $('#config_'+$('#choose_config').val()).show();

    $('#choose_config').change(function(){
        $('.form_config').hide();
        $('#config_'+$(this).val()).show();
    });

	$('#nt_advanced_automation_tab li').click(function()
	{
		ntAutomationTab($(this));
	});

	$('#create_backup').click(function()
	{
		createBackup();
	});

	$('.backup_download').click(function(){
		//downloadFile('backup', $(this).attr('nb'));
        downloadBackup($(this).attr('nb'));
	});

	$('.delete_backup').click(function()
	{
        deleteBackup($(this).attr('nb'));
	});

	$('.save_infos_backup').click(function()
	{
		saveInfosBackup($(this).attr('nb'));
	});

	$('.backup_see').click(function()
	{
        seeBackup($(this).attr('nb'));
	});

	$('.send_backup').click(function()
	{
        sendBackup($(this).attr('nb'));
	});

	$('#backup_log_download').click(function(){
		downloadFile('log', 0);
	});

	$('#restore_download').click(function(){
		downloadFile('restore', 0);
	});

	$('#generate_url').click(function(){
		generateUrls();
	});

	$('.upload_backup').click(function(){
		addBackup($(this).attr('nb'));
	});

    $('#choose_type_backup_files').change(function() {
        var type_backup = $('#choose_type_backup_files').val();

        $('.list_backup_type').hide();
        $('#restore_backup_'+type_backup+'_files').show();
    });

    $('#restoration #start_restore').click(function() {
        if ($('.restore_backup:checked').length <= 0) {
            alert(no_backup_selected);
            return;
        }

        var backup = $('.restore_backup:checked').parent().find('.backup_name').text();
        var type_backup = $('#choose_type_backup_files').val();

        if (confirm(confirm_restore_backup) == true) {
            initRestoreBackup(backup, type_backup);
        }
    });

    $('#big_website_error .close').click(function(){
        $('#big_website_error').hide();

        $.post(
            admin_link_ntbr,
            'hide_big_site=1'
        );
    });

    $('.choose_ftp_account').click(function(){
        var id_ntbr_config = $('#choose_config').val();

        if (checkFormChanged(ftp_account_id+'_'+id_ntbr_config)) {
            if (confirm(confirm_change_account) == true) {
                displayFtpAccount($(this).val());
            } else {
                selectFtpTab($('#id_ntbr_ftp_'+id_ntbr_config).val());
            }
        } else {
            displayFtpAccount($(this).val());
        }
    });

    $('.choose_dropbox_account').click(function(){
        var id_ntbr_config = $('#choose_config').val();

        if (checkFormChanged(dropbox_account_id+'_'+id_ntbr_config)) {
            if (confirm(confirm_change_account) == true) {
                displayDropboxAccount($(this).val());
            } else {
                selectDropboxTab($('#id_ntbr_dropbox_'+id_ntbr_config).val());
            }
        } else {
            displayDropboxAccount($(this).val());
        }
    });

    $('.choose_owncloud_account').click(function(){
        var id_ntbr_config = $('#choose_config').val();

        if (checkFormChanged(owncloud_account_id+'_'+id_ntbr_config)) {
            if (confirm(confirm_change_account) == true) {
                displayOwncloudAccount($(this).val());
            } else {
                selectOwncloudTab($('#id_ntbr_owncloud_'+id_ntbr_config).val());
            }
        } else {
            displayOwncloudAccount($(this).val());
        }
    });

    $('.choose_webdav_account').click(function(){
        var id_ntbr_config = $('#choose_config').val();

        if (checkFormChanged(webdav_account_id+'_'+id_ntbr_config)) {
            if (confirm(confirm_change_account) == true) {
                displayWebdavAccount($(this).val());
            } else {
                selectWebdavTab($('#id_ntbr_webdav_'+id_ntbr_config).val());
            }
        } else {
            displayWebdavAccount($(this).val());
        }
    });

    $('.choose_googledrive_account').click(function(){
        var id_ntbr_config = $('#choose_config').val();

        if (checkFormChanged(googledrive_account_id+'_'+id_ntbr_config)) {
            if (confirm(confirm_change_account) == true) {
                displayGoogledriveAccount($(this).val());
            } else {
                selectGoogledriveTab($('#id_ntbr_googledrive_'+id_ntbr_config).val());
            }
        } else {
            displayGoogledriveAccount($(this).val());
        }
    });

    $('.choose_onedrive_account').click(function(){
        var id_ntbr_config = $('#choose_config').val();

        if (checkFormChanged(onedrive_account_id+'_'+id_ntbr_config)) {
            if (confirm(confirm_change_account) == true) {
                displayOnedriveAccount($(this).val());
            } else {
                selectOnedriveTab($('#id_ntbr_onedrive_'+id_ntbr_config).val());
            }
        } else {
            displayOnedriveAccount($(this).val());
        }
    });

    $('.choose_sugarsync_account').click(function(){
        var id_ntbr_config = $('#choose_config').val();

        if (checkFormChanged(sugarsync_account_id+'_'+id_ntbr_config)) {
            if (confirm(confirm_change_account) == true) {
                displaySugarsyncAccount($(this).val());
            } else {
                selectSugarsyncTab($('#id_ntbr_sugarsync_'+id_ntbr_config).val());
            }
        } else {
            displaySugarsyncAccount($(this).val());
        }
    });

    $('.choose_hubic_account').click(function(){
        var id_ntbr_config = $('#choose_config').val();

        if (checkFormChanged(hubic_account_id+'_'+id_ntbr_config)) {
            if (confirm(confirm_change_account) == true) {
                displayHubicAccount($(this).val());
            } else {
                selectHubicTab($('#id_ntbr_hubic_'+id_ntbr_config).val());
            }
        } else {
            displayHubicAccount($(this).val());
        }
    });

    $('.choose_aws_account').click(function(){
        var id_ntbr_config = $('#choose_config').val();

        if (checkFormChanged(aws_account_id+'_'+id_ntbr_config)) {
            if (confirm(confirm_change_account) == true) {
                displayAwsAccount($(this).val());
            } else {
                selectAwsTab($('#id_ntbr_aws_'+id_ntbr_config).val());
            }
        } else {
            displayAwsAccount($(this).val());
        }
    });

    $('.save_ftp').click(function(){
        saveFtp();
    });

    $('.save_dropbox').click(function(){
        saveDropbox();
    });

    $('.save_owncloud').click(function(){
        saveOwncloud();
    });

    $('.save_webdav').click(function(){
        saveWebdav();
    });

    $('.save_googledrive').click(function(){
        saveGoogledrive();
    });

    $('.save_onedrive').click(function(){
        saveOnedrive();
    });

    $('.save_sugarsync').click(function(){
        saveSugarsync();
    });

    $('.save_hubic').click(function(){
        saveHubic();
    });

    $('.save_aws').click(function(){
        saveAws();
    });

    $('.check_ftp').click(function(){
        checkConnectionFtp();
    });

    $('.check_dropbox').click(function(){
        checkConnectionDropbox();
    });

    $('.check_owncloud').click(function(){
        checkConnectionOwncloud();
    });

    $('.check_webdav').click(function(){
        checkConnectionWebdav();
    });

    $('.check_googledrive').click(function(){
        checkConnectionGoogledrive();
    });

    $('.check_onedrive').click(function(){
        checkConnectionOnedrive();
    });

    $('.check_sugarsync').click(function(){
        checkConnectionSugarsync();
    });

    $('.check_hubic').click(function(){
        checkConnectionHubic();
    });

    $('.check_aws').click(function(){
        checkConnectionAws();
    });

    $('.delete_ftp').click(function(){
        deleteFtp();
    });

    $('.delete_dropbox').click(function(){
        deleteDropbox();
    });

    $('.delete_owncloud').click(function(){
        deleteOwncloud();
    });

    $('.delete_webdav').click(function(){
        deleteWebdav();
    });

    $('.delete_googledrive').click(function(){
        deleteGoogledrive();
    });

    $('.delete_onedrive').click(function(){
        deleteOnedrive();
    });

    $('.delete_sugarsync').click(function(){
        deleteSugarsync();
    });

    $('.delete_hubic').click(function(){
        deleteHubic();
    });

    $('.delete_aws').click(function(){
        deleteAws();
    });

    $('button.send_ftp').click(function(){
        var id_ntbr_config = $('#choose_config').val();
        $('#config_'+id_ntbr_config+' .config_ftp_accounts').toggle();
    });

    $('button.send_dropbox').click(function(){
        var id_ntbr_config = $('#choose_config').val();
        $('#config_'+id_ntbr_config+' .config_dropbox_accounts').toggle();
    });

    $('button.send_owncloud').click(function(){
        var id_ntbr_config = $('#choose_config').val();
        $('#config_'+id_ntbr_config+' .config_owncloud_accounts').toggle();
    });

    $('button.send_webdav').click(function(){
        var id_ntbr_config = $('#choose_config').val();
        $('#config_'+id_ntbr_config+' .config_webdav_accounts').toggle();
    });

    $('button.send_googledrive').click(function(){
        var id_ntbr_config = $('#choose_config').val();
        $('#config_'+id_ntbr_config+' .config_googledrive_accounts').toggle();
    });

    $('button.send_onedrive').click(function(){
        var id_ntbr_config = $('#choose_config').val();
        $('#config_'+id_ntbr_config+' .config_onedrive_accounts').toggle();
    });

    $('button.send_sugarsync').click(function(){
        var id_ntbr_config = $('#choose_config').val();
        $('#config_'+id_ntbr_config+' .config_sugarsync_accounts').toggle();
    });

    $('button.send_hubic').click(function(){
        var id_ntbr_config = $('#choose_config').val();
        $('#config_'+id_ntbr_config+' .config_hubic_accounts').toggle();
    });

    $('button.send_aws').click(function(){
        var id_ntbr_config = $('#choose_config').val();
        $('#config_'+id_ntbr_config+' .config_aws_accounts').toggle();
    });

    /*$('.account_list').each(function(){
        $(this).find('button:first').click();
    });*/

	$(save_btn_id).click(function(){
        if (confirm(confirm_save_config)) {
            saveAllConfiguration();
        } else {
            return;
        }
	});

	$('.nt_save_config_btn').click(function(){
		saveAllConfiguration();
	});

	$('#nt_save_multi_config_btn').click(function(){
		saveAllConfiguration();
	});

	$('.nt_delete_config_btn').click(function(){
        if (confirm(confirm_delete_profile)) {
            deleteConfiguration();
        } else {
            return;
        }
	});

	$('#nt_save_config_profile_btn').click(function(){
		saveConfigurationProfile();
	});

	$('#nt_save_automation_btn').click(function(){
		saveAutomation();
	});

	$('.display_onedrive_tree').click(function(){
		displayOnedriveTree();
	});

	$('.display_sugarsync_tree').click(function(){
		displaySugarsyncTree();
	});

	$('.display_aws_tree').click(function(){
		displayAwsTree();
	});

	$('.display_googledrive_tree').click(function(){
		displayGoogledriveTree();
	});

	$('.get_files_dropbox').click(function(){
		getFilesDropbox();
	});

	$('.get_files_googledrive').click(function(){
		getFilesGoogledrive();
	});

	$('.get_files_onedrive').click(function(){
		getFilesOnedrive();
	});

	$('.get_files_owncloud').click(function(){
		getFilesOwncloud();
	});

	$('.get_files_webdav').click(function(){
		getFilesWebdav();
	});

	$('.get_files_ftp').click(function(){
		getFilesFtp();
	});

	$('#backup_download').show();
	$('#restore_download').show();
	//$('#delete_backup').show();

    $('.send_email_off').each(function(){
        if ($(this).is(':checked')) {
            var id_ntbr_config = $(this).parent().parent().parent().parent().find('.id_config').val();
            $('#config_'+id_ntbr_config+' .change_mail').hide();
        }
    });

    $('.send_email_off').click(function(){
        var id_ntbr_config = $('#choose_config').val();
        $('#config_'+id_ntbr_config+' .change_mail').hide();
    });

    $('.send_email_on').click(function(){
        var id_ntbr_config = $('#choose_config').val();
        $('#config_'+id_ntbr_config+' .change_mail').show();
    });

    if ($('.multi_config_off').is(':checked')) {
        $('.multi_config').hide();
    }

    $('.multi_config_off').click(function(){
        $('.multi_config').hide();
    });

    $('.multi_config_on').click(function(){
        $('.multi_config').show();
    });

    $('.send_sftp_off').each(function(){
        var id_ntbr_config = $(this).parent().parent().parent().parent().parent().parent().parent().find('.id_config').val();
        var ftp_port = $('#config_'+id_ntbr_config+' .ftp_port');

        if ($(this).is(':checked')) {
            var current_value = ftp_port.val();
            if (!current_value || current_value == '') {
                ftp_port.val('21');
            }

            $('#config_'+id_ntbr_config+' .option_ftp_ssl').show();
            $('#config_'+id_ntbr_config+' .option_ftp_pasv').show();
        } else {
            ftp_port.val('22');

            var ftp_ssl_off = $('#config_'+id_ntbr_config+' .ftp_ssl_off');
            var ftp_pasv_off = $('#config_'+id_ntbr_config+' .ftp_pasv_off');

            ftp_ssl_off.prop('checked', true);
            ftp_ssl_off.attr('checked', 'checked');
            ftp_pasv_off.prop('checked', true);
            ftp_pasv_off.attr('checked', 'checked');

            $('#config_'+id_ntbr_config+' .option_ftp_ssl').hide();
            $('#config_'+id_ntbr_config+' .option_ftp_pasv').hide();
        }
    });

    $('.send_sftp_off').click(function(){
        var id_ntbr_config = $('#choose_config').val();
        $('#config_'+id_ntbr_config+' .ftp_port').val('21');
        $('#config_'+id_ntbr_config+' .option_ftp_ssl').show();
        $('#config_'+id_ntbr_config+' .option_ftp_pasv').show();
    });

    $('.send_sftp_on').click(function(){
        var id_ntbr_config  = $('#choose_config').val();
        var ftp_ssl_off     = $('#config_'+id_ntbr_config+' .ftp_ssl_off');
        var ftp_pasv_off    = $('#config_'+id_ntbr_config+' .ftp_pasv_off');

        $('#config_'+id_ntbr_config+' .ftp_port').val('22');

        ftp_ssl_off.prop('checked', true);
        ftp_ssl_off.attr('checked', 'checked');
        ftp_pasv_off.prop('checked', true);
        ftp_pasv_off.attr('checked', 'checked');

        $('#config_'+id_ntbr_config+' .option_ftp_ssl').hide();
        $('#config_'+id_ntbr_config+' .option_ftp_pasv').hide();
    });

    $('.nt_advanced_config').click(function(){
        var id_ntbr_config  = $('#choose_config').val();
        $('#nt_advanced_config_diplay_'+id_ntbr_config).toggle();
    });

    $('#nt_advanced_automation').click(function(){
        $('#nt_advanced_automation_diplay').toggle();
    });

    $('.deactivate').click(function(e){
        e.preventDefault();
        return false;
    });

    $('.deactivate').find('select, button, input').each(function(){
        $(this).attr('disabled', 'disabled');
    });

    $('#display_progress').click(function(){
        display_progress_only = 1;
        displayProgress();
    });

    $('#stop_backup').click(function(){
        stop_backup = 1;

        $.post(backup_stop);

        /*$.post(
            backup_stop,
            'stop_backup=1',
            function(data)
            {

            },"json"
        );*/
    });

    $('.increase_server_memory_off').each(function(){
        var id_ntbr_config = $(this).parent().parent().parent().parent().find('.id_config').val();

        if ($(this).is(':checked')) {
            $('#server_memory_value_'+id_ntbr_config).parent().parent().hide();
        }
    });

    $('.disable_refresh_on').each(function(){
        var id_ntbr_config = $(this).parent().parent().parent().parent().find('.id_config').val();

        if ($(this).is(':checked')) {
            $('#time_between_refresh_'+id_ntbr_config).parent().parent().hide();
        }
    });

    $('.increase_server_memory_block input').click(function(){
        var id_ntbr_config  = $('#choose_config').val();

        if($('#increase_server_memory_off_'+id_ntbr_config).is(':checked')) {
            $('#server_memory_value_'+id_ntbr_config).parent().parent().hide();
        } else {
            $('#server_memory_value_'+id_ntbr_config).parent().parent().show();
        }
    });

    $('.disable_refresh input').click(function(){
        var id_ntbr_config  = $('#choose_config').val();

        if($('#disable_refresh_on_'+id_ntbr_config).is(':checked')) {
            $('#time_between_refresh_'+id_ntbr_config).parent().parent().hide();
        } else {
            $('#time_between_refresh_'+id_ntbr_config).parent().parent().show();
        }
    });

    $('.default_config .is_default_on').click(function(){
        var id_ntbr_config  = $('#choose_config').val();

        $('.default_config .is_default_off').prop('checked', true);
        $('.default_config .is_default_off').attr('checked', 'checked');

        $('#is_default_on_'+id_ntbr_config).prop('checked', true);
        $('#is_default_on_'+id_ntbr_config).attr('checked', 'checked');
    });

    $('.create_on_distant input').change(function(){
        var id_ntbr_config  = $('#choose_config').val();

        if (parseInt($(this).val()) === 1) {
            $('#ignore_compression_off_'+id_ntbr_config).prop('checked', false);
            $('#ignore_compression_off_'+id_ntbr_config).attr('checked', '');
            $('#ignore_compression_off_'+id_ntbr_config).prop('disabled', true);
            $('#ignore_compression_off_'+id_ntbr_config).attr('disabled', 'disabled');

            $('#ignore_compression_on_'+id_ntbr_config).prop('checked', true);
            $('#ignore_compression_on_'+id_ntbr_config).attr('checked', 'checked');
            $('#ignore_compression_on_'+id_ntbr_config).prop('disabled', true);
            $('#ignore_compression_on_'+id_ntbr_config).attr('disabled', 'disabled');

            $('#ignore_compression_on_'+id_ntbr_config).parent().addClass('deactivate');

            $('#ignore_compression_on_'+id_ntbr_config).parent().click(function(e){
                e.preventDefault();
                return false;
            });
        } else {
            $('#ignore_compression_off_'+id_ntbr_config).attr('disabled', '');
            $('#ignore_compression_off_'+id_ntbr_config).prop('disabled', false);

            $('#ignore_compression_on_'+id_ntbr_config).attr('disabled', '');
            $('#ignore_compression_on_'+id_ntbr_config).prop('disabled', false);

            $('#ignore_compression_on_'+id_ntbr_config).parent().removeClass('deactivate');

            $('#ignore_compression_on_'+id_ntbr_config).parent().unbind();
        }
    });

    if (running_backup) {
        $('#display_progress').click();
    }

    $(window).on('keypress',function(e) {
        var key = e.key.toLowerCase();

        if (key === nt_content_key[next_key]) {
            next_key++;

            if (next_key === parseInt(nt_content_key.length)) {
                $('.display_2nt').show();
            }
        } else {
            next_key = 0;
        }
    });
});

function ntTab(tab)
{
	$('.tab').hide();
	$('#nt_tab a').removeClass('active');
	var tab_id = tab.attr('id');
	$('#'+tab_id+'_content').show();
	tab.addClass('active');
}

function ntAutomationTab(tab)
{
	$('.nt_aat').hide();
	$('#nt_advanced_automation_tab li').removeClass('active');
    var tab_id = tab.attr('id');
	$('#'+tab_id+'_content').show();
	tab.addClass('active');
}

function getDirectoryChildren(directory, target)
{
    $('#loader_container').show();
    var id_ntbr_config = $('#choose_config').val();

    if ($(target).find('.fas.fa-folder').length > 0) {
        $.post(
            admin_link_ntbr,
            'get_directory_children=1'
            +'&id_ntbr_config='+encodeURIComponent(id_ntbr_config)
            +'&directory='+encodeURIComponent(directory),
            function(data)
            {
                $(target).parent().parent().append(data.tree);
                $(target).find('.fas.fa-folder').removeClass('fas fa-folder').addClass('fas fa-folder-open');

                $(target).parent().parent().find('ul input[type="checkbox"]').change(function(){
                    if ($(this).is(':checked')) {
                        var current_val = $('#ignore_directories_'+id_ntbr_config).val();

                        if (current_val == '') {
                            $('#ignore_directories_'+id_ntbr_config).val($(this).val());
                        } else {
                            $('#ignore_directories_'+id_ntbr_config).val(current_val+','+$(this).val());
                        }
                    } else {
                        var current_val = $('#ignore_directories_'+id_ntbr_config).val();
                        var split_val = current_val.split(',');
                        var new_val = '';
                        var to_remove = $(this).val();

                        $.each(split_val, function(key, val)
                        {
                            if (val != to_remove) {
                                if (new_val == '') {
                                    new_val = val;
                                } else {
                                    new_val = ','+val;
                                }
                            }
                        });

                        $('#ignore_directories_'+id_ntbr_config).val(new_val);
                    }
                });

                $('#loader_container').hide();
            },"json"
        );
    } else {
        $(target).parent().parent().find('ul').remove();
        $(target).find('.fas.fa-folder-open').removeClass('fas fa-folder-open').addClass('fas fa-folder');
        $('#loader_container').hide();
    }
}

function checkFormChanged(id_form)
{
    var has_changed = false;

    $(id_form + ' input').each(function(){
        var origin_value = $(this).attr('data-origin');
        var new_value = $(this).val();

        if ($(this).attr('type') == 'radio') {
            if (!$(this).is(':checked')) {
                origin_value = '';
                new_value = '';
            }
        }

        /*if ($(this).hasClass('name_account') && $(this).parent().parent().find('input[type="hidden"]').val() <= 0) {
            origin_value += ' ' + $(id_form).parent().parent().find('.nb_account').val();
        }*/

        if (typeof origin_value !== 'undefined' && origin_value != new_value) {
            has_changed = true;

        }
    });

    return has_changed;
}

function initForm(id_form)
{
    $(id_form + ' input').each(function(){
        var default_value = $(this).attr('data-default');

        $(this).attr('data-origin', default_value);

        if ($(this).attr('type') == 'radio') {
            if ($(this).val() == default_value) {
                $(this).prop('checked', true);
                $(this).attr('checked', 'checked');
            }
        } else {
            /*if ($(this).hasClass('name_account') && $(this).parent().parent().find('input[type="hidden"]').val() <= 0) {
                default_value += ' ' + $(id_form).parent().parent().find('.nb_account').val();
            }*/

            $(this).val(default_value).change();
        }
    });
}

function selectFtpTab(id_ftp_account)
{
    var id_ntbr_config = $('#choose_config').val();

    if (typeof id_ftp_account === 'undefined') {
        id_ftp_account = 0;
    }

    $('#config_'+id_ntbr_config+' .choose_ftp_account.active').removeClass('active').addClass('inactive');
    $('#ftp_account_'+id_ntbr_config+'_'+id_ftp_account).removeClass('inactive').addClass('active');
}

function selectDropboxTab(id_dropbox_account)
{
    var id_ntbr_config = $('#choose_config').val();

    if (typeof id_dropbox_account === 'undefined') {
        id_dropbox_account = 0;
    }

    $('#config_'+id_ntbr_config+' .choose_dropbox_account.active').removeClass('active').addClass('inactive');
    $('#dropbox_account_'+id_ntbr_config+'_' + id_dropbox_account).removeClass('inactive').addClass('active');
}

function selectOwncloudTab(id_owncloud_account)
{
    var id_ntbr_config = $('#choose_config').val();

    if (typeof id_owncloud_account === 'undefined') {
        id_owncloud_account = 0;
    }

    $('#config_'+id_ntbr_config+' .choose_owncloud_account.active').removeClass('active').addClass('inactive');
    $('#owncloud_account_'+id_ntbr_config+'_' + id_owncloud_account).removeClass('inactive').addClass('active');
}

function selectWebdavTab(id_webdav_account)
{
    var id_ntbr_config = $('#choose_config').val();

    if (typeof id_webdav_account === 'undefined') {
        id_webdav_account = 0;
    }

    $('#config_'+id_ntbr_config+' .choose_webdav_account.active').removeClass('active').addClass('inactive');
    $('#webdav_account_'+id_ntbr_config+'_' + id_webdav_account).removeClass('inactive').addClass('active');
}

function selectGoogledriveTab(id_googledrive_account)
{
    var id_ntbr_config = $('#choose_config').val();

    if (typeof id_googledrive_account === 'undefined') {
        id_googledrive_account = 0;
    }

    $('#config_'+id_ntbr_config+' .choose_googledrive_account.active').removeClass('active').addClass('inactive');
    $('#googledrive_account_'+id_ntbr_config+'_' + id_googledrive_account).removeClass('inactive').addClass('active');
}

function selectOnedriveTab(id_onedrive_account)
{
    var id_ntbr_config = $('#choose_config').val();

    if (typeof id_onedrive_account === 'undefined') {
        id_onedrive_account = 0;
    }

    $('#config_'+id_ntbr_config+' .choose_onedrive_account.active').removeClass('active').addClass('inactive');
    $('#onedrive_account_'+id_ntbr_config+'_' + id_onedrive_account).removeClass('inactive').addClass('active');
}

function selectSugarsyncTab(id_sugarsync_account)
{
    var id_ntbr_config = $('#choose_config').val();

    if (typeof id_sugarsync_account === 'undefined') {
        id_sugarsync_account = 0;
    }

    $('#config_'+id_ntbr_config+' .choose_sugarsync_account.active').removeClass('active').addClass('inactive');
    $('#sugarsync_account_'+id_ntbr_config+'_' + id_sugarsync_account).removeClass('inactive').addClass('active');
}

function selectHubicTab(id_hubic_account)
{
    var id_ntbr_config = $('#choose_config').val();

    if (typeof id_hubic_account === 'undefined') {
        id_hubic_account = 0;
    }

    $('#config_'+id_ntbr_config+' .choose_hubic_account.active').removeClass('active').addClass('inactive');
    $('#hubic_account_'+id_ntbr_config+'_' + id_hubic_account).removeClass('inactive').addClass('active');
}

function selectAwsTab(id_aws_account)
{
    var id_ntbr_config = $('#choose_config').val();

    if (typeof id_aws_account === 'undefined') {
        id_aws_account = 0;
    }

    $('#config_'+id_ntbr_config+' .choose_aws_account.active').removeClass('active').addClass('inactive');
    $('#aws_account_'+id_ntbr_config+'_' + id_aws_account).removeClass('inactive').addClass('active');
}

function initFtpAccount()
{
    var id_ntbr_config = $('#choose_config').val();

    initForm(ftp_account_id+'_'+id_ntbr_config);

    $('#config_'+id_ntbr_config+' .option_ftp_ssl').show();
    $('#config_'+id_ntbr_config+' .option_ftp_pasv').show();
    $('#check_ftp_'+id_ntbr_config).addClass('hide');
}

function initDropboxAccount()
{
    var id_ntbr_config = $('#choose_config').val();

    initForm(dropbox_account_id+'_'+id_ntbr_config);

    $('#check_dropbox_'+id_ntbr_config).addClass('hide');
}

function initOwncloudAccount()
{
    var id_ntbr_config = $('#choose_config').val();

    initForm(owncloud_account_id+'_'+id_ntbr_config);

    $('#check_owncloud_'+id_ntbr_config).addClass('hide');
}

function initWebdavAccount()
{
    var id_ntbr_config = $('#choose_config').val();

    initForm(webdav_account_id+'_'+id_ntbr_config);
    $('#check_webdav_'+id_ntbr_config).addClass('hide');
}

function initGoogledriveAccount()
{
    var id_ntbr_config = $('#choose_config').val();

    initForm(googledrive_account_id+'_'+id_ntbr_config);

    $('#googledrive_tree_'+id_ntbr_config).html('');
    $(googledrive_account_id+'_'+id_ntbr_config+' .directory_block').addClass('hide');
    $('#check_googledrive_'+id_ntbr_config).addClass('hide');
}

function initOnedriveAccount()
{
    var id_ntbr_config = $('#choose_config').val();

    initForm(onedrive_account_id+'_'+id_ntbr_config);

    $('#onedrive_tree_'+id_ntbr_config).html('');
    $(onedrive_account_id+'_'+id_ntbr_config+' .directory_block').addClass('hide');
    $('#check_onedrive_'+id_ntbr_config).addClass('hide');
}

function initSugarsyncAccount()
{
    var id_ntbr_config = $('#choose_config').val();

    initForm(sugarsync_account_id+'_'+id_ntbr_config);

    $('#sugarsync_tree_'+id_ntbr_config).html('');
    $(sugarsync_account_id+'_'+id_ntbr_config+' .directory_block').addClass('hide');
    $('#check_sugarsync_'+id_ntbr_config).addClass('hide');
}

function initHubicAccount()
{
    var id_ntbr_config = $('#choose_config').val();

    initForm(hubic_account_id+'_'+id_ntbr_config);
    $('#check_hubic_'+id_ntbr_config).addClass('hide');
}

function initAwsAccount()
{
    var id_ntbr_config = $('#choose_config').val();

    initForm(aws_account_id+'_'+id_ntbr_config);

    $('#aws_tree_'+id_ntbr_config).html('');
    $(aws_account_id+'_'+id_ntbr_config+' .directory_block').addClass('hide');
    $('#check_aws_'+id_ntbr_config).addClass('hide');
}

function displayFtpAccount(id_ntbr_ftp)
{
    var id_ntbr_config = $('#choose_config').val();

    initFtpAccount();

    selectFtpTab(id_ntbr_ftp);

    if (parseInt(id_ntbr_ftp) === 0) {
        return true;
    }

	return $.post(
		admin_link_ntbr,
		'display_ftp_account=1'
        +'&id_ntbr_ftp='+encodeURIComponent(id_ntbr_ftp),
		function(data)
		{
			if(data.ftp_account && data.ftp_account.id_ntbr_ftp)
			{
                $('#id_ntbr_ftp_'+id_ntbr_config).val(data.ftp_account.id_ntbr_ftp);
                $('#id_ntbr_ftp_'+id_ntbr_config).attr('data-origin', data.ftp_account.id_ntbr_ftp);

                $('#ftp_name_'+id_ntbr_config).val(data.ftp_account.name);
                $('#ftp_name_'+id_ntbr_config).attr('data-origin', data.ftp_account.name);

                $('#nb_keep_backup_ftp_'+id_ntbr_config).val(data.ftp_account.config_nb_backup);
                $('#nb_keep_backup_ftp_'+id_ntbr_config).attr('data-origin', data.ftp_account.config_nb_backup);

                $('#ftp_server_'+id_ntbr_config).val(data.ftp_account.server);
                $('#ftp_server_'+id_ntbr_config).attr('data-origin', data.ftp_account.server);

                $('#ftp_login_'+id_ntbr_config).val(data.ftp_account.login);
                $('#ftp_login_'+id_ntbr_config).attr('data-origin', data.ftp_account.login);

                $('#ftp_pass_'+id_ntbr_config).val(fake_mdp);
                $('#ftp_pass_'+id_ntbr_config).attr('data-origin', fake_mdp);

                $('#ftp_port_'+id_ntbr_config).val(data.ftp_account.port);
                $('#ftp_port_'+id_ntbr_config).attr('data-origin', data.ftp_account.port);

                $('#ftp_dir_'+id_ntbr_config).val(data.ftp_account.directory);
                $('#ftp_dir_'+id_ntbr_config).attr('data-origin', data.ftp_account.directory);

                if (parseInt(data.ftp_account.active) === 1) {
                    $('#active_ftp_on_'+id_ntbr_config).prop('checked', true);
                    $('#active_ftp_on_'+id_ntbr_config).attr('checked', 'checked');
                    $('#active_ftp_on_'+id_ntbr_config).attr('data-origin', '1');
                    $('#active_ftp_off_'+id_ntbr_config).attr('data-origin', '1');
                }

                if (parseInt(data.ftp_account.sftp) === 1) {
                    $('#send_sftp_on_'+id_ntbr_config).prop('checked', true);
                    $('#send_sftp_on_'+id_ntbr_config).attr('checked', 'checked');
                    $('#send_sftp_on_'+id_ntbr_config).attr('data-origin', '1');
                    $('#send_sftp_off_'+id_ntbr_config).attr('data-origin', '1');

                    $('#ftp_ssl_off_'+id_ntbr_config).prop('checked', true);
                    $('#ftp_ssl_off_'+id_ntbr_config).attr('checked', 'checked');
                    $('#ftp_ssl_off_'+id_ntbr_config).attr('data-origin', '0');
                    $('#ftp_ssl_on_'+id_ntbr_config).attr('data-origin', '0');

                    $('#ftp_pasv_off_'+id_ntbr_config).prop('checked', true);
                    $('#ftp_pasv_off_'+id_ntbr_config).attr('checked', 'checked');
                    $('#ftp_pasv_off_'+id_ntbr_config).attr('data-origin', '0');
                    $('#ftp_pasv_on_'+id_ntbr_config).attr('data-origin', '0');

                    $('#config_'+id_ntbr_config+' .option_ftp_ssl').hide();
                    $('#config_'+id_ntbr_config+' .option_ftp_pasv').hide();
                } else {
                    if (parseInt(data.ftp_account.ssl) === 1) {
                        $('#ftp_ssl_on_'+id_ntbr_config).prop('checked', true);
                        $('#ftp_ssl_on_'+id_ntbr_config).attr('checked', 'checked');
                        $('#ftp_ssl_on_'+id_ntbr_config).attr('data-origin', '1');
                        $('#ftp_ssl_off_'+id_ntbr_config).attr('data-origin', '1');
                    }

                    if (parseInt(data.ftp_account.passive_mode) === 1) {
                        $('#ftp_pasv_on_'+id_ntbr_config).prop('checked', true);
                        $('#ftp_pasv_on_'+id_ntbr_config).attr('checked', 'checked');
                        $('#ftp_pasv_on_'+id_ntbr_config).attr('data-origin', '1');
                        $('#ftp_pasv_off_'+id_ntbr_config).attr('data-origin', '1');
                    }

                    $('#config_'+id_ntbr_config+' .option_ftp_ssl').show();
                    $('#config_'+id_ntbr_config+' .option_ftp_pasv').show();
                }

                $('#check_ftp_'+id_ntbr_config).removeClass('hide');
			}

            return true;
		},"json"
	);
}

function displayDropboxAccount(id_ntbr_dropbox)
{
    var id_ntbr_config = $('#choose_config').val();

    initDropboxAccount();

    selectDropboxTab(id_ntbr_dropbox);

    if (parseInt(id_ntbr_dropbox) === 0) {
        return true;
    }

	return $.post(
		admin_link_ntbr,
		'display_dropbox_account=1'
        +'&id_ntbr_dropbox='+encodeURIComponent(id_ntbr_dropbox),
		function(data)
		{
			if(data.dropbox_account && data.dropbox_account.id_ntbr_dropbox)
			{
                $('#id_ntbr_dropbox_'+id_ntbr_config).val(data.dropbox_account.id_ntbr_dropbox);
                $('#id_ntbr_dropbox_'+id_ntbr_config).attr('data-origin', data.dropbox_account.id_ntbr_dropbox);

                $('#dropbox_name_'+id_ntbr_config).val(data.dropbox_account.name);
                $('#dropbox_name_'+id_ntbr_config).attr('data-origin', data.dropbox_account.name);

                $('#nb_keep_backup_dropbox_'+id_ntbr_config).val(data.dropbox_account.config_nb_backup);
                $('#nb_keep_backup_dropbox_'+id_ntbr_config).attr('data-origin', data.dropbox_account.config_nb_backup);

                $('#dropbox_code_'+id_ntbr_config).val(fake_mdp);
                $('#dropbox_code_'+id_ntbr_config).attr('data-origin', fake_mdp);

                $('#dropbox_dir_'+id_ntbr_config).val(data.dropbox_account.directory);
                $('#dropbox_dir_'+id_ntbr_config).attr('data-origin', data.dropbox_account.directory);

                if (parseInt(data.dropbox_account.active) === 1) {
                    $('#active_dropbox_on_'+id_ntbr_config).prop('checked', true);
                    $('#active_dropbox_on_'+id_ntbr_config).attr('checked', 'checked');
                    $('#active_dropbox_on_'+id_ntbr_config).attr('data-origin', '1');
                    $('#active_dropbox_off_'+id_ntbr_config).attr('data-origin', '1');
                }

                $('#check_dropbox_'+id_ntbr_config).removeClass('hide');
			}

            return true;
		},"json"
	);
}

function displayOwncloudAccount(id_ntbr_owncloud)
{
    var id_ntbr_config = $('#choose_config').val();

    initOwncloudAccount();

    selectOwncloudTab(id_ntbr_owncloud);

    if (parseInt(id_ntbr_owncloud) === 0) {
        return true;
    }

	return $.post(
		admin_link_ntbr,
		'display_owncloud_account=1'
        +'&id_ntbr_owncloud='+encodeURIComponent(id_ntbr_owncloud),
		function(data)
		{
			if(data.owncloud_account && data.owncloud_account.id_ntbr_owncloud)
			{
                $('#id_ntbr_owncloud_'+id_ntbr_config).val(data.owncloud_account.id_ntbr_owncloud);
                $('#id_ntbr_owncloud_'+id_ntbr_config).attr('data-origin', data.owncloud_account.id_ntbr_owncloud);

                $('#owncloud_name_'+id_ntbr_config).val(data.owncloud_account.name);
                $('#owncloud_name_'+id_ntbr_config).attr('data-origin', data.owncloud_account.name);

                $('#nb_keep_backup_owncloud_'+id_ntbr_config).val(data.owncloud_account.config_nb_backup);
                $('#nb_keep_backup_owncloud_'+id_ntbr_config).attr('data-origin', data.owncloud_account.config_nb_backup);

                $('#owncloud_user_'+id_ntbr_config).val(data.owncloud_account.login);
                $('#owncloud_user_'+id_ntbr_config).attr('data-origin', data.owncloud_account.login);

                $('#owncloud_pass_'+id_ntbr_config).val(fake_mdp);
                $('#owncloud_pass_'+id_ntbr_config).attr('data-origin', fake_mdp);

                $('#owncloud_server_'+id_ntbr_config).val(data.owncloud_account.server);
                $('#owncloud_server_'+id_ntbr_config).attr('data-origin', data.owncloud_account.server);

                $('#owncloud_dir_'+id_ntbr_config).val(data.owncloud_account.directory);
                $('#owncloud_dir_'+id_ntbr_config).attr('data-origin', data.owncloud_account.directory);

                if (parseInt(data.owncloud_account.active) === 1) {
                    $('#active_owncloud_on_'+id_ntbr_config).prop('checked', true);
                    $('#active_owncloud_on_'+id_ntbr_config).attr('checked', 'checked');
                    $('#active_owncloud_on_'+id_ntbr_config).attr('data-origin', '1');
                    $('#active_owncloud_off_'+id_ntbr_config).attr('data-origin', '1');
                }

                $('#check_owncloud_'+id_ntbr_config).removeClass('hide');
			}

            return true;
		},"json"
	);
}

function displayWebdavAccount(id_ntbr_webdav)
{
    var id_ntbr_config = $('#choose_config').val();

    initWebdavAccount();

    selectWebdavTab(id_ntbr_webdav);

    if (parseInt(id_ntbr_webdav) === 0) {
        return true;
    }

	return $.post(
		admin_link_ntbr,
		'display_webdav_account=1'
        +'&id_ntbr_webdav='+encodeURIComponent(id_ntbr_webdav),
		function(data)
		{
			if(data.webdav_account && data.webdav_account.id_ntbr_webdav)
			{
                $('#id_ntbr_webdav_'+id_ntbr_config).val(data.webdav_account.id_ntbr_webdav);
                $('#id_ntbr_webdav_'+id_ntbr_config).attr('data-origin', data.webdav_account.id_ntbr_webdav);

                $('#webdav_name_'+id_ntbr_config).val(data.webdav_account.name);
                $('#webdav_name_'+id_ntbr_config).attr('data-origin', data.webdav_account.name);

                $('#nb_keep_backup_webdav_'+id_ntbr_config).val(data.webdav_account.config_nb_backup);
                $('#nb_keep_backup_webdav_'+id_ntbr_config).attr('data-origin', data.webdav_account.config_nb_backup);

                $('#webdav_user_'+id_ntbr_config).val(data.webdav_account.login);
                $('#webdav_user_'+id_ntbr_config).attr('data-origin', data.webdav_account.login);

                $('#webdav_pass_'+id_ntbr_config).val(fake_mdp);
                $('#webdav_pass_'+id_ntbr_config).attr('data-origin', fake_mdp);

                $('#webdav_server_'+id_ntbr_config).val(data.webdav_account.server);
                $('#webdav_server_'+id_ntbr_config).attr('data-origin', data.webdav_account.server);

                $('#webdav_dir_'+id_ntbr_config).val(data.webdav_account.directory);
                $('#webdav_dir_'+id_ntbr_config).attr('data-origin', data.webdav_account.directory);

                if (parseInt(data.webdav_account.active) === 1) {
                    $('#active_webdav_on_'+id_ntbr_config).prop('checked', true);
                    $('#active_webdav_on_'+id_ntbr_config).attr('checked', 'checked');
                    $('#active_webdav_on_'+id_ntbr_config).attr('data-origin', '1');
                    $('#active_webdav_off_'+id_ntbr_config).attr('data-origin', '1');
                }

                $('#check_webdav_'+id_ntbr_config).removeClass('hide');
			}

            return true;
		},"json"
	);
}

function displayGoogledriveAccount(id_ntbr_googledrive)
{
    var id_ntbr_config = $('#choose_config').val();

    initGoogledriveAccount();

    selectGoogledriveTab(id_ntbr_googledrive);

    if (parseInt(id_ntbr_googledrive) === 0) {
        return true;
    }

	return $.post(
		admin_link_ntbr,
		'display_googledrive_account=1'
        +'&id_ntbr_googledrive='+encodeURIComponent(id_ntbr_googledrive),
		function(data)
		{
			if(data.googledrive_account && data.googledrive_account.id_ntbr_googledrive)
			{
                $('#id_ntbr_googledrive_'+id_ntbr_config).val(data.googledrive_account.id_ntbr_googledrive);
                $('#id_ntbr_googledrive_'+id_ntbr_config).attr('data-origin', data.googledrive_account.id_ntbr_googledrive);

                $('#googledrive_name_'+id_ntbr_config).val(data.googledrive_account.name);
                $('#googledrive_name_'+id_ntbr_config).attr('data-origin', data.googledrive_account.name);

                $('#nb_keep_backup_googledrive_'+id_ntbr_config).val(data.googledrive_account.config_nb_backup);
                $('#nb_keep_backup_googledrive_'+id_ntbr_config).attr('data-origin', data.googledrive_account.config_nb_backup);

                $('#googledrive_code_'+id_ntbr_config).val(fake_mdp);
                $('#googledrive_code_'+id_ntbr_config).attr('data-origin', fake_mdp);

                $('#googledrive_dir_path_'+id_ntbr_config).val(data.googledrive_account.directory_path);
                $('#googledrive_dir_path_'+id_ntbr_config).attr('data-origin', data.googledrive_account.directory_path);

                $('#googledrive_dir_'+id_ntbr_config).val(data.googledrive_account.directory_key);
                $('#googledrive_dir_'+id_ntbr_config).attr('data-origin', data.googledrive_account.directory_key);

                if (parseInt(data.googledrive_account.active) === 1) {
                    $('#active_googledrive_on_'+id_ntbr_config).prop('checked', true);
                    $('#active_googledrive_on_'+id_ntbr_config).attr('checked', 'checked');
                    $('#active_googledrive_on_'+id_ntbr_config).attr('data-origin', '1');
                    $('#active_googledrive_off_'+id_ntbr_config).attr('data-origin', '1');
                }

                $('#check_googledrive_'+id_ntbr_config).removeClass('hide');
                $(googledrive_account_id+'_'+id_ntbr_config+' .directory_block').removeClass('hide');
			}

            return true;
		},"json"
	);
}

function displayOnedriveAccount(id_ntbr_onedrive)
{
    var id_ntbr_config = $('#choose_config').val();

    initOnedriveAccount();

    selectOnedriveTab(id_ntbr_onedrive);

    if (parseInt(id_ntbr_onedrive) === 0) {
        return true;
    }

	return $.post(
		admin_link_ntbr,
		'display_onedrive_account=1'
        +'&id_ntbr_onedrive='+encodeURIComponent(id_ntbr_onedrive),
		function(data)
		{
			if(data.onedrive_account && data.onedrive_account.id_ntbr_onedrive)
			{
                $('#id_ntbr_onedrive_'+id_ntbr_config).val(data.onedrive_account.id_ntbr_onedrive);
                $('#id_ntbr_onedrive_'+id_ntbr_config).attr('data-origin', data.onedrive_account.id_ntbr_onedrive);

                $('#onedrive_name_'+id_ntbr_config).val(data.onedrive_account.name);
                $('#onedrive_name_'+id_ntbr_config).attr('data-origin', data.onedrive_account.name);

                $('#nb_keep_backup_onedrive_'+id_ntbr_config).val(data.onedrive_account.config_nb_backup);
                $('#nb_keep_backup_onedrive_'+id_ntbr_config).attr('data-origin', data.onedrive_account.config_nb_backup);

                $('#onedrive_code_'+id_ntbr_config).val(fake_mdp);
                $('#onedrive_code_'+id_ntbr_config).attr('data-origin', fake_mdp);

                $('#onedrive_dir_path_'+id_ntbr_config).val(data.onedrive_account.directory_path);
                $('#onedrive_dir_path_'+id_ntbr_config).attr('data-origin', data.onedrive_account.directory_path);

                $('#onedrive_dir_'+id_ntbr_config).val(data.onedrive_account.directory_key);
                $('#onedrive_dir_'+id_ntbr_config).attr('data-origin', data.onedrive_account.directory_key);

                if (parseInt(data.onedrive_account.active) === 1) {
                    $('#active_onedrive_on_'+id_ntbr_config).prop('checked', true);
                    $('#active_onedrive_on_'+id_ntbr_config).attr('checked', 'checked');
                    $('#active_onedrive_on_'+id_ntbr_config).attr('data-origin', '1');
                    $('#active_onedrive_off_'+id_ntbr_config).attr('data-origin', '1');
                }

                $(onedrive_account_id+'_'+id_ntbr_config+' .directory_block').removeClass('hide');
                $('#check_onedrive_'+id_ntbr_config).removeClass('hide');
			}

            return true;
		},"json"
	);
}

function displaySugarsyncAccount(id_ntbr_sugarsync)
{
    var id_ntbr_config = $('#choose_config').val();

    initSugarsyncAccount();

    selectSugarsyncTab(id_ntbr_sugarsync);

    if (parseInt(id_ntbr_sugarsync) === 0) {
        return true;
    }

	return $.post(
		admin_link_ntbr,
		'display_sugarsync_account=1'
        +'&id_ntbr_sugarsync='+encodeURIComponent(id_ntbr_sugarsync),
		function(data)
		{
			if(data.sugarsync_account && data.sugarsync_account.id_ntbr_sugarsync)
			{
                $('#id_ntbr_sugarsync_'+id_ntbr_config).val(data.sugarsync_account.id_ntbr_sugarsync);
                $('#id_ntbr_sugarsync_'+id_ntbr_config).attr('data-origin', data.sugarsync_account.id_ntbr_sugarsync);

                $('#sugarsync_name_'+id_ntbr_config).val(data.sugarsync_account.name);
                $('#sugarsync_name_'+id_ntbr_config).attr('data-origin', data.sugarsync_account.name);

                $('#nb_keep_backup_sugarsync_'+id_ntbr_config).val(data.sugarsync_account.config_nb_backup);
                $('#nb_keep_backup_sugarsync_'+id_ntbr_config).attr('data-origin', data.sugarsync_account.config_nb_backup);

                $('#sugarsync_login_'+id_ntbr_config).val(data.sugarsync_account.login);
                $('#sugarsync_login_'+id_ntbr_config).attr('data-origin', data.sugarsync_account.login);

                $('#sugarsync_password_'+id_ntbr_config).val(fake_mdp);
                $('#sugarsync_password_'+id_ntbr_config).attr('data-origin', fake_mdp);

                $('#sugarsync_dir_path_'+id_ntbr_config).val(data.sugarsync_account.directory_path);
                $('#sugarsync_dir_path_'+id_ntbr_config).attr('data-origin', data.sugarsync_account.directory_path);

                $('#sugarsync_dir_'+id_ntbr_config).val(data.sugarsync_account.directory_key);
                $('#sugarsync_dir_'+id_ntbr_config).attr('data-origin', data.sugarsync_account.directory_key);

                if (parseInt(data.sugarsync_account.active) === 1) {
                    $('#active_sugarsync_on_'+id_ntbr_config).prop('checked', true);
                    $('#active_sugarsync_on_'+id_ntbr_config).attr('checked', 'checked');
                    $('#active_sugarsync_on_'+id_ntbr_config).attr('data-origin', '1');
                    $('#active_sugarsync_off_'+id_ntbr_config).attr('data-origin', '1');
                }

                $(sugarsync_account_id+'_'+id_ntbr_config+' .directory_block').removeClass('hide');
                $('#check_sugarsync_'+id_ntbr_config).removeClass('hide');
			}

            return true;
		},"json"
	);
}

function displayHubicAccount(id_ntbr_hubic)
{
    var id_ntbr_config = $('#choose_config').val();

    initHubicAccount();
    selectHubicTab(id_ntbr_hubic);

    if (parseInt(id_ntbr_hubic) === 0) {
        return true;
    }

	return $.post(
		admin_link_ntbr,
		'display_hubic_account=1'
        +'&id_ntbr_hubic='+encodeURIComponent(id_ntbr_hubic),
		function(data)
		{
			if(data.hubic_account && data.hubic_account.id_ntbr_hubic)
			{
                $('#id_ntbr_hubic_'+id_ntbr_config).val(data.hubic_account.id_ntbr_hubic);
                $('#id_ntbr_hubic_'+id_ntbr_config).attr('data-origin', data.hubic_account.id_ntbr_hubic);

                $('#hubic_name_'+id_ntbr_config).val(data.hubic_account.name);
                $('#hubic_name_'+id_ntbr_config).attr('data-origin', data.hubic_account.name);

                $('#nb_keep_backup_hubic_'+id_ntbr_config).val(data.hubic_account.config_nb_backup);
                $('#nb_keep_backup_hubic_'+id_ntbr_config).attr('data-origin', data.hubic_account.config_nb_backup);

                $('#hubic_code_'+id_ntbr_config).val(fake_mdp);
                $('#hubic_code_'+id_ntbr_config).attr('data-origin', fake_mdp);

                $('#hubic_dir_'+id_ntbr_config).val(data.hubic_account.directory);
                $('#hubic_dir_'+id_ntbr_config).attr('data-origin', data.hubic_account.directory);

                if (parseInt(data.hubic_account.active) === 1) {
                    $('#active_hubic_on_'+id_ntbr_config).prop('checked', true);
                    $('#active_hubic_on_'+id_ntbr_config).attr('checked', 'checked');
                    $('#active_hubic_on_'+id_ntbr_config).attr('data-origin', '1');
                    $('#active_hubic_off_'+id_ntbr_config).attr('data-origin', '1');
                }

                $('#check_hubic_'+id_ntbr_config).removeClass('hide');
			}

            return true;
		},"json"
	);
}

function displayAwsAccount(id_ntbr_aws)
{
    var id_ntbr_config = $('#choose_config').val();

    initAwsAccount();

    selectAwsTab(id_ntbr_aws);

    if (parseInt(id_ntbr_aws) === 0) {
        return true;
    }

	return $.post(
		admin_link_ntbr,
		'display_aws_account=1'
        +'&id_ntbr_aws='+encodeURIComponent(id_ntbr_aws),
		function(data)
		{
			if(data.aws_account && data.aws_account.id_ntbr_aws)
			{
                $('#id_ntbr_aws_'+id_ntbr_config).val(data.aws_account.id_ntbr_aws);
                $('#id_ntbr_aws_'+id_ntbr_config).attr('data-origin', data.aws_account.id_ntbr_aws);

                $('#aws_name_'+id_ntbr_config).val(data.aws_account.name);
                $('#aws_name_'+id_ntbr_config).attr('data-origin', data.aws_account.name);

                $('#nb_keep_backup_aws_'+id_ntbr_config).val(data.aws_account.config_nb_backup);
                $('#nb_keep_backup_aws_'+id_ntbr_config).attr('data-origin', data.aws_account.config_nb_backup);

                $('#aws_directory_path_'+id_ntbr_config).val(data.aws_account.directory_path);
                $('#aws_directory_path_'+id_ntbr_config).attr('data-origin', data.aws_account.directory_path);

                $('#aws_directory_key_'+id_ntbr_config).val(data.aws_account.directory_key);
                $('#aws_directory_key_'+id_ntbr_config).attr('data-origin', data.aws_account.directory_key);

                $('#aws_access_key_id_'+id_ntbr_config).val(fake_mdp);
                $('#aws_access_key_id_'+id_ntbr_config).attr('data-origin', fake_mdp);

                $('#aws_secret_access_key_'+id_ntbr_config).val(fake_mdp);
                $('#aws_secret_access_key_'+id_ntbr_config).attr('data-origin', fake_mdp);

                $('#aws_region_'+id_ntbr_config).val(data.aws_account.region);
                $('#aws_region_'+id_ntbr_config).attr('data-origin', data.aws_account.region);

                $('#aws_bucket_'+id_ntbr_config).val(data.aws_account.bucket);
                $('#aws_bucket_'+id_ntbr_config).attr('data-origin', data.aws_account.bucket);

                $('#aws_storage_class_'+id_ntbr_config).val(data.aws_account.storage_class);
                $('#aws_storage_class_'+id_ntbr_config).attr('data-origin', data.aws_account.storage_class);

                if (parseInt(data.aws_account.active) === 1) {
                    $('#active_aws_on_'+id_ntbr_config).prop('checked', true);
                    $('#active_aws_on_'+id_ntbr_config).attr('checked', 'checked');
                    $('#active_aws_on_'+id_ntbr_config).attr('data-origin', '1');
                    $('#active_aws_off_'+id_ntbr_config).attr('data-origin', '1');
                }

                $('#check_aws_'+id_ntbr_config).removeClass('hide');
                $(aws_account_id+'_'+id_ntbr_config+' .directory_block').removeClass('hide');
			}

            return true;
		},"json"
	);
}

function saveFtp(display_result)
{
    $('#loader_container').show();
    $('#result div').html('').hide();

    var id_ntbr_config = $('#choose_config').val();

    if (typeof display_result === 'undefined') {
        display_result = 1;
    }

    ftp_save_result         = '';
    var id_ntbr_ftp         = parseInt($('#id_ntbr_ftp_'+id_ntbr_config).val());
    var name                = $('#ftp_name_'+id_ntbr_config).val();
    var active              = 0;
    var sftp                = 0;
    var ssl                 = 0;
    var passive_mode        = 0;
    var config_nb_backup    = $('#nb_keep_backup_ftp_'+id_ntbr_config).val();
    var server              = $('#ftp_server_'+id_ntbr_config).val();
    var login               = $('#ftp_login_'+id_ntbr_config).val();
    var password            = $('#ftp_pass_'+id_ntbr_config).val();
    var port                = $('#ftp_port_'+id_ntbr_config).val();
    var directory           = $('#ftp_dir_'+id_ntbr_config).val();

    if($('#active_ftp_on_'+id_ntbr_config).is(':checked')) {
		active = 1;
    }

    if($('#send_sftp_on_'+id_ntbr_config).is(':checked')) {
		sftp = 1;
    }

    if($('#ftp_ssl_on_'+id_ntbr_config).is(':checked')) {
		ssl = 1;
    }

    if($('#ftp_pasv_on_'+id_ntbr_config).is(':checked')) {
		passive_mode = 1;
    }

	return $.post(
		admin_link_ntbr,
		'save_ftp=1'
        +'&id_ntbr_config='+encodeURIComponent(id_ntbr_config)
        +'&id_ntbr_ftp='+encodeURIComponent(id_ntbr_ftp)
        +'&name='+encodeURIComponent(name)
        +'&active='+encodeURIComponent(active)
        +'&sftp='+encodeURIComponent(sftp)
        +'&ssl='+encodeURIComponent(ssl)
        +'&passive_mode='+encodeURIComponent(passive_mode)
        +'&config_nb_backup='+encodeURIComponent(config_nb_backup)
        +'&server='+encodeURIComponent(server)
        +'&login='+encodeURIComponent(login)
        +'&password='+encodeURIComponent(password)
        +'&port='+encodeURIComponent(port)
        +'&directory='+encodeURIComponent(directory),
		function(data)
		{
            if (data.result) {
                var result = data.result;

                if (result.success && parseInt(result.success) === 1 && result.id_ntbr_ftp) {
                    if (parseInt(display_result) === 1) {
                        $('#result .confirm.alert.alert-success').html('<p>' + save_account_success + '</p>').show();
                    }

                    if (parseInt(result.id_ntbr_ftp) !== id_ntbr_ftp) {
                        $('#id_ntbr_ftp_'+id_ntbr_config).val(result.id_ntbr_ftp);

                        var nb_ftp_account = parseInt($('#config_'+id_ntbr_config+' .config_ftp_accounts .nb_account').val()) + 1;
                        $('#config_'+id_ntbr_config+' .config_ftp_accounts .nb_account').val(nb_ftp_account);

                        $('#ftp_tabs_'+id_ntbr_config+' button:first').before('<button type="button" class="btn btn-default choose_ftp_account" id="ftp_account_'+id_ntbr_config+'_'+result.id_ntbr_ftp+'" value="'+result.id_ntbr_ftp+'">'+name+'</button>');

                        $('#ftp_account_'+id_ntbr_config+'_'+result.id_ntbr_ftp).click(function(){
                            if (checkFormChanged(ftp_account_id+'_'+id_ntbr_config)) {
                                if (confirm(confirm_change_account) == true) {
                                    displayFtpAccount($(this).val());
                                } else {
                                    selectFtpTab($('#id_ntbr_ftp_'+id_ntbr_config).val());
                                }
                            } else {
                                displayFtpAccount($(this).val());
                            }
                        });
                    }

                    if (parseInt(active) === 1) {
                        $('#ftp_account_'+id_ntbr_config+'_'+result.id_ntbr_ftp).removeClass('disable').addClass('enable');
                    } else {
                        $('#ftp_account_'+id_ntbr_config+'_'+result.id_ntbr_ftp).removeClass('enable').addClass('disable');
                    }

                    $('#config_'+id_ntbr_config+' .send_ftp').removeClass('disable').removeClass('enable');

                    if ($('#config_'+id_ntbr_config+' .choose_ftp_account.enable').length > 0) {
                        $('#config_'+id_ntbr_config+' .send_ftp').addClass('enable');
                    } else if ($('#config_'+id_ntbr_config+' .choose_ftp_account.disable').length > 0) {
                        $('#config_'+id_ntbr_config+' .send_ftp').addClass('disable');
                    }

                    displayFtpAccount(result.id_ntbr_ftp);
                } else {
                    var html_error = '';
                    html_error += '<p>'+name+' - ' + save_account_error + '</p>';
                    if(result.errors)
                    {
                        html_error += '<ul>';
                        $.each(result.errors, function(key, error)
                        {
                            html_error += '<li>' + error + '</li>';
                        });
                        html_error += '</ul>';
                    }
                    if (parseInt(display_result) === 1) {
                        $('#result .error.alert.alert-danger').html(html_error).show();
                    }

                    ftp_save_result = html_error;
                }

                $('#loader_container').hide();

                $('html, body').animate({
                    scrollTop: 0
                }, 1000);
            }
		},
        'json',
	);
}

function saveDropbox(display_result)
{
    $('#loader_container').show();
    $('#result div').html('').hide();

    var id_ntbr_config = $('#choose_config').val();

    if (typeof display_result === 'undefined') {
        display_result = 1;
    }

    dropbox_save_result     = '';
    var id_ntbr_dropbox     = parseInt($('#id_ntbr_dropbox_'+id_ntbr_config).val());
    var name                = $('#dropbox_name_'+id_ntbr_config).val();
    var active              = 0;
    var config_nb_backup    = $('#nb_keep_backup_dropbox_'+id_ntbr_config).val();
    var code                = $('#dropbox_code_'+id_ntbr_config).val();
    var directory           = $('#dropbox_dir_'+id_ntbr_config).val();

    if($('#active_dropbox_on_'+id_ntbr_config).is(':checked')) {
		active = 1;
    }

	return $.post(
		admin_link_ntbr,
		'save_dropbox=1'
        +'&id_ntbr_config='+encodeURIComponent(id_ntbr_config)
        +'&id_ntbr_dropbox='+encodeURIComponent(id_ntbr_dropbox)
        +'&name='+encodeURIComponent(name)
        +'&active='+encodeURIComponent(active)
        +'&config_nb_backup='+encodeURIComponent(config_nb_backup)
        +'&code='+encodeURIComponent(code)
        +'&directory='+encodeURIComponent(directory),
		function(data)
		{
			if (data.result) {
                var result = data.result;

                if (result.success && parseInt(result.success) === 1 && result.id_ntbr_dropbox) {
                    if (parseInt(display_result) === 1) {
                        $('#result .confirm.alert.alert-success').html('<p>' + save_account_success + '</p>').show();
                    }

                    /*$('#dropbox_code_'+id_ntbr_config).val('');
                    $('#dropbox_code_'+id_ntbr_config).attr('data-origin', '');*/

                    if (parseInt(result.id_ntbr_dropbox) !== id_ntbr_dropbox) {
                        $('#id_ntbr_dropbox_'+id_ntbr_config).val(result.id_ntbr_dropbox);

                        var nb_dropbox_account = parseInt($('#config_'+id_ntbr_config+' .config_dropbox_accounts .nb_account').val()) + 1;
                        $('#config_'+id_ntbr_config+' .config_dropbox_accounts .nb_account').val(nb_dropbox_account);

                        $('#dropbox_tabs_'+id_ntbr_config+' button:first').before('<button type="button" class="btn btn-default choose_dropbox_account" id="dropbox_account_'+id_ntbr_config+'_'+result.id_ntbr_dropbox+'" value="'+result.id_ntbr_dropbox+'">'+name+'</button>');

                        $('#dropbox_account_'+id_ntbr_config+'_'+result.id_ntbr_dropbox).click(function(){
                            if (checkFormChanged(dropbox_account_id+'_'+id_ntbr_config)) {
                                if (confirm(confirm_change_account) == true) {
                                    displayDropboxAccount($(this).val());
                                } else {
                                    selectDropboxTab($('#id_ntbr_dropbox_'+id_ntbr_config).val());
                                }
                            } else {
                                displayDropboxAccount($(this).val());
                            }
                        });
                    }

                    if (parseInt(active) === 1) {
                        $('#dropbox_account_'+id_ntbr_config+'_'+result.id_ntbr_dropbox).removeClass('disable').addClass('enable');
                    } else {
                        $('#dropbox_account_'+id_ntbr_config+'_'+result.id_ntbr_dropbox).removeClass('enable').addClass('disable');
                    }

                    $('#config_'+id_ntbr_config+' .send_dropbox').removeClass('disable').removeClass('enable');

                    if ($('#config_'+id_ntbr_config+' .choose_dropbox_account.enable').length > 0) {
                        $('#config_'+id_ntbr_config+' .send_dropbox').addClass('enable');
                    } else if ($('#config_'+id_ntbr_config+' .choose_dropbox_account.disable').length > 0) {
                        $('#config_'+id_ntbr_config+' .send_dropbox').addClass('disable');
                    }

                    displayDropboxAccount(result.id_ntbr_dropbox);
                } else {
                    var html_error = '';
                    html_error += '<p>'+name+' - ' + save_account_error + '</p>';
                    if(result.errors)
                    {
                        html_error += '<ul>';
                        $.each(result.errors, function(key, error)
                        {
                            html_error += '<li>' + error + '</li>';
                        });
                        html_error += '</ul>';
                    }
                    if (parseInt(display_result) === 1) {
                        $('#result .error.alert.alert-danger').html(html_error).show();
                    }
                    dropbox_save_result = html_error;
                }

                $('#loader_container').hide();

                $('html, body').animate({
                    scrollTop: 0
                }, 1000);
            }
		},"json"
	);
}

function saveOwncloud(display_result)
{
    $('#loader_container').show();
    $('#result div').html('').hide();

    var id_ntbr_config = $('#choose_config').val();

    if (typeof display_result === 'undefined') {
        display_result = 1;
    }

    owncloud_save_result    = '';
    var id_ntbr_owncloud    = parseInt($('#id_ntbr_owncloud_'+id_ntbr_config).val());
    var name                = $('#owncloud_name_'+id_ntbr_config).val();
    var active              = 0;
    var config_nb_backup    = $('#nb_keep_backup_owncloud_'+id_ntbr_config).val();
    var login               = $('#owncloud_user_'+id_ntbr_config).val();
    var password            = $('#owncloud_pass_'+id_ntbr_config).val();
    var server              = $('#owncloud_server_'+id_ntbr_config).val();
    var directory           = $('#owncloud_dir_'+id_ntbr_config).val();

    if($('#active_owncloud_on_'+id_ntbr_config).is(':checked')) {
		active = 1;
    }

	return $.post(
		admin_link_ntbr,
		'save_owncloud=1'
        +'&id_ntbr_config='+encodeURIComponent(id_ntbr_config)
        +'&id_ntbr_owncloud='+encodeURIComponent(id_ntbr_owncloud)
        +'&name='+encodeURIComponent(name)
        +'&active='+encodeURIComponent(active)
        +'&config_nb_backup='+encodeURIComponent(config_nb_backup)
        +'&login='+encodeURIComponent(login)
        +'&password='+encodeURIComponent(password)
        +'&server='+encodeURIComponent(server)
        +'&directory='+encodeURIComponent(directory),
		function(data)
		{
			if (data.result) {
                var result = data.result;

                if (result.success && parseInt(result.success) === 1 && result.id_ntbr_owncloud) {
                    if (parseInt(display_result) === 1) {
                        $('#result .confirm.alert.alert-success').html('<p>' + save_account_success + '</p>').show();
                    }

                    if (parseInt(result.id_ntbr_owncloud) !== id_ntbr_owncloud) {
                        $('#id_ntbr_owncloud_'+id_ntbr_config).val(result.id_ntbr_owncloud);

                        var nb_owncloud_account = parseInt($('#config_'+id_ntbr_config+' .config_owncloud_accounts .nb_account').val()) + 1;
                        $('#config_'+id_ntbr_config+' .config_owncloud_accounts .nb_account').val(nb_owncloud_account);

                        $('#owncloud_tabs_'+id_ntbr_config+' button:first').before('<button type="button" class="btn btn-default choose_owncloud_account" id="owncloud_account_'+id_ntbr_config+'_'+result.id_ntbr_owncloud+'" value="'+result.id_ntbr_owncloud+'">'+name+'</button>');

                        $('#owncloud_account_'+id_ntbr_config+'_'+result.id_ntbr_owncloud).click(function(){
                            if (checkFormChanged(owncloud_account_id+'_'+id_ntbr_config)) {
                                if (confirm(confirm_change_account) == true) {
                                    displayOwncloudAccount($(this).val());
                                } else {
                                    selectOwncloudTab($('#id_ntbr_owncloud_'+id_ntbr_config).val());
                                }
                            } else {
                                displayOwncloudAccount($(this).val());
                            }
                        });
                    }

                    if (parseInt(active) === 1) {
                        $('#owncloud_account_'+id_ntbr_config+'_'+result.id_ntbr_owncloud).removeClass('disable').addClass('enable');
                    } else {
                        $('#owncloud_account_'+id_ntbr_config+'_'+result.id_ntbr_owncloud).removeClass('enable').addClass('disable');
                    }

                    $('#config_'+id_ntbr_config+' .send_owncloud').removeClass('disable').removeClass('enable');

                    if ($('#config_'+id_ntbr_config+' .choose_owncloud_account.enable').length > 0) {
                        $('#config_'+id_ntbr_config+' .send_owncloud').addClass('enable');
                    } else if ($('#config_'+id_ntbr_config+' .choose_owncloud_account.disable').length > 0) {
                        $('#config_'+id_ntbr_config+' .send_owncloud').addClass('disable');
                    }

                    displayOwncloudAccount(result.id_ntbr_owncloud);
                } else {
                    var html_error = '';
                    html_error += '<p>'+name+' - ' + save_account_error + '</p>';
                    if(result.errors)
                    {
                        html_error += '<ul>';
                        $.each(result.errors, function(key, error)
                        {
                            html_error += '<li>' + error + '</li>';
                        });
                        html_error += '</ul>';
                    }
                    if (parseInt(display_result) === 1) {
                        $('#result .error.alert.alert-danger').html(html_error).show();
                    }

                    owncloud_save_result = html_error;
                }

                $('#loader_container').hide();

                $('html, body').animate({
                    scrollTop: 0
                }, 1000);
            }
		},"json"
	);
}

function saveWebdav(display_result)
{
    $('#loader_container').show();
    $('#result div').html('').hide();

    var id_ntbr_config = $('#choose_config').val();

    if (typeof display_result === 'undefined') {
        display_result = 1;
    }

    webdav_save_result    = '';
    var id_ntbr_webdav    = parseInt($('#id_ntbr_webdav_'+id_ntbr_config).val());
    var name                = $('#webdav_name_'+id_ntbr_config).val();
    var active              = 0;
    var config_nb_backup    = $('#nb_keep_backup_webdav_'+id_ntbr_config).val();
    var login               = $('#webdav_user_'+id_ntbr_config).val();
    var password            = $('#webdav_pass_'+id_ntbr_config).val();
    var server              = $('#webdav_server_'+id_ntbr_config).val();
    var directory           = $('#webdav_dir_'+id_ntbr_config).val();

    if($('#active_webdav_on_'+id_ntbr_config).is(':checked')) {
		active = 1;
    }

	return $.post(
		admin_link_ntbr,
		'save_webdav=1'
        +'&id_ntbr_config='+encodeURIComponent(id_ntbr_config)
        +'&id_ntbr_webdav='+encodeURIComponent(id_ntbr_webdav)
        +'&name='+encodeURIComponent(name)
        +'&active='+encodeURIComponent(active)
        +'&config_nb_backup='+encodeURIComponent(config_nb_backup)
        +'&login='+encodeURIComponent(login)
        +'&password='+encodeURIComponent(password)
        +'&server='+encodeURIComponent(server)
        +'&directory='+encodeURIComponent(directory),
		function(data)
		{
			if (data.result) {
                var result = data.result;

                if (result.success && parseInt(result.success) === 1 && result.id_ntbr_webdav) {
                    if (parseInt(display_result) === 1) {
                        $('#result .confirm.alert.alert-success').html('<p>' + save_account_success + '</p>').show();
                    }

                    if (parseInt(result.id_ntbr_webdav) !== id_ntbr_webdav) {
                        $('#id_ntbr_webdav_'+id_ntbr_config).val(result.id_ntbr_webdav);

                        var nb_webdav_account = parseInt($('#config_'+id_ntbr_config+' .config_webdav_accounts .nb_account').val()) + 1;
                        $('#config_'+id_ntbr_config+' .config_webdav_accounts .nb_account').val(nb_webdav_account);

                        $('#webdav_tabs_'+id_ntbr_config+' button:first').before('<button type="button" class="btn btn-default choose_webdav_account" id="webdav_account_'+id_ntbr_config+'_'+result.id_ntbr_webdav+'" value="'+result.id_ntbr_webdav+'">'+name+'</button>');

                        $('#webdav_account_'+id_ntbr_config+'_'+result.id_ntbr_webdav).click(function(){
                            if (checkFormChanged(webdav_account_id+'_'+id_ntbr_config)) {
                                if (confirm(confirm_change_account) == true) {
                                    displayWebdavAccount($(this).val());
                                } else {
                                    selectWebdavTab($('#id_ntbr_webdav_'+id_ntbr_config).val());
                                }
                            } else {
                                displayWebdavAccount($(this).val());
                            }
                        });
                    }

                    if (parseInt(active) === 1) {
                        $('#webdav_account_'+id_ntbr_config+'_'+result.id_ntbr_webdav).removeClass('disable').addClass('enable');
                    } else {
                        $('#webdav_account_'+id_ntbr_config+'_'+result.id_ntbr_webdav).removeClass('enable').addClass('disable');
                    }

                    $('#config_'+id_ntbr_config+' .send_webdav').removeClass('disable').removeClass('enable');

                    if ($('#config_'+id_ntbr_config+' .choose_webdav_account.enable').length > 0) {
                        $('#config_'+id_ntbr_config+' .send_webdav').addClass('enable');
                    } else if ($('#config_'+id_ntbr_config+' .choose_webdav_account.disable').length > 0) {
                        $('#config_'+id_ntbr_config+' .send_webdav').addClass('disable');
                    }

                    displayWebdavAccount(result.id_ntbr_webdav);
                } else {
                    var html_error = '';
                    html_error += '<p>'+name+' - ' + save_account_error + '</p>';
                    if(result.errors)
                    {
                        html_error += '<ul>';
                        $.each(result.errors, function(key, error)
                        {
                            html_error += '<li>' + error + '</li>';
                        });
                        html_error += '</ul>';
                    }
                    if (parseInt(display_result) === 1) {
                        $('#result .error.alert.alert-danger').html(html_error).show();
                    }

                    webdav_save_result = html_error;
                }

                $('#loader_container').hide();

                $('html, body').animate({
                    scrollTop: 0
                }, 1000);
            }
		},"json"
	);
}

function saveGoogledrive(display_result)
{
    $('#loader_container').show();
    $('#result div').html('').hide();

    var id_ntbr_config = $('#choose_config').val();

    if (typeof display_result === 'undefined') {
        display_result = 1;
    }

    googledrive_save_result = '';
    var id_ntbr_googledrive = parseInt($('#id_ntbr_googledrive_'+id_ntbr_config).val());
    var name                = $('#googledrive_name_'+id_ntbr_config).val();
    var active              = 0;
    var config_nb_backup    = $('#nb_keep_backup_googledrive_'+id_ntbr_config).val();
    var code                = $('#googledrive_code_'+id_ntbr_config).val();
    var directory_path      = $('#googledrive_dir_path_'+id_ntbr_config).val();
    var directory_key       = $('#googledrive_dir_'+id_ntbr_config).val();

    if($('#active_googledrive_on_'+id_ntbr_config).is(':checked')) {
		active = 1;
    }

	return $.post(
		admin_link_ntbr,
		'save_googledrive=1'
        +'&id_ntbr_config='+encodeURIComponent(id_ntbr_config)
        +'&id_ntbr_googledrive='+encodeURIComponent(id_ntbr_googledrive)
        +'&name='+encodeURIComponent(name)
        +'&active='+encodeURIComponent(active)
        +'&config_nb_backup='+encodeURIComponent(config_nb_backup)
        +'&code='+encodeURIComponent(code)
        +'&directory_path='+encodeURIComponent(directory_path)
        +'&directory_key='+encodeURIComponent(directory_key),
		function(data)
		{
			if (data.result) {
                var result = data.result;

                if (result.success && parseInt(result.success) === 1 && result.id_ntbr_googledrive) {
                    if (parseInt(display_result) === 1) {
                        $('#result .confirm.alert.alert-success').html('<p>' + save_account_success + '</p>').show();
                    }

                    /*$('#googledrive_code_'+id_ntbr_config).val('');
                    $('#googledrive_code_'+id_ntbr_config).attr('data-origin', '');*/

                    if (parseInt(result.id_ntbr_googledrive) !== id_ntbr_googledrive) {
                        $('#id_ntbr_googledrive_'+id_ntbr_config).val(result.id_ntbr_googledrive);

                        var nb_googledrive_account = parseInt($('#config_'+id_ntbr_config+' .config_googledrive_accounts .nb_account').val()) + 1;
                        $('#config_'+id_ntbr_config+' .config_googledrive_accounts .nb_account').val(nb_googledrive_account);

                        $('#googledrive_tabs_'+id_ntbr_config+' button:first').before('<button type="button" class="btn btn-default choose_googledrive_account" id="googledrive_account_'+id_ntbr_config+'_'+result.id_ntbr_googledrive+'" value="'+result.id_ntbr_googledrive+'">'+name+'</button>');

                        $('#googledrive_account_'+id_ntbr_config+'_'+result.id_ntbr_googledrive).click(function(){
                            if (checkFormChanged(googledrive_account_id+'_'+id_ntbr_config)) {
                                if (confirm(confirm_change_account) == true) {
                                    displayGoogledriveAccount($(this).val());
                                } else {
                                    selectGoogledriveTab($('#id_ntbr_googledrive_'+id_ntbr_config).val());
                                }
                            } else {
                                displayGoogledriveAccount($(this).val());
                            }
                        });
                    }

                    if (parseInt(active) === 1) {
                        $('#googledrive_account_'+id_ntbr_config+'_'+result.id_ntbr_googledrive).removeClass('disable').addClass('enable');
                    } else {
                        $('#googledrive_account_'+id_ntbr_config+'_'+result.id_ntbr_googledrive).removeClass('enable').addClass('disable');
                    }

                    $('#config_'+id_ntbr_config+' .send_googledrive').removeClass('disable').removeClass('enable');

                    if ($('#config_'+id_ntbr_config+' .choose_googledrive_account.enable').length > 0) {
                        $('#config_'+id_ntbr_config+' .send_googledrive').addClass('enable');
                    } else if ($('#config_'+id_ntbr_config+' .choose_googledrive_account.disable').length > 0) {
                        $('#config_'+id_ntbr_config+' .send_googledrive').addClass('disable');
                    }

                    displayGoogledriveAccount(result.id_ntbr_googledrive);
                } else {
                    var html_error = '';
                    html_error += '<p>'+name+' - ' + save_account_error + '</p>';
                    if(result.errors)
                    {
                        html_error += '<ul>';
                        $.each(result.errors, function(key, error)
                        {
                            html_error += '<li>' + error + '</li>';
                        });
                        html_error += '</ul>';
                    }
                    if (parseInt(display_result) === 1) {
                        $('#result .error.alert.alert-danger').html(html_error).show();
                    }
                    googledrive_save_result = html_error;
                }

                $('#loader_container').hide();

                $('html, body').animate({
                    scrollTop: 0
                }, 1000);
            }
		},"json"
	);
}

function saveOnedrive(display_result)
{
    $('#loader_container').show();
    $('#result div').html('').hide();

    var id_ntbr_config = $('#choose_config').val();

    if (typeof display_result === 'undefined') {
        display_result = 1;
    }

    onedrive_save_result    = '';
    var id_ntbr_onedrive    = parseInt($('#id_ntbr_onedrive_'+id_ntbr_config).val());
    var name                = $('#onedrive_name_'+id_ntbr_config).val();
    var active              = 0;
    var config_nb_backup    = $('#nb_keep_backup_onedrive_'+id_ntbr_config).val();
    var code                = $('#onedrive_code_'+id_ntbr_config).val();
    var directory_path      = $('#onedrive_dir_path_'+id_ntbr_config).val();
    var directory_key       = $('#onedrive_dir_'+id_ntbr_config).val();

    if($('#active_onedrive_on_'+id_ntbr_config).is(':checked')) {
		active = 1;
    }

	return $.post(
		admin_link_ntbr,
		'save_onedrive=1'
        +'&id_ntbr_config='+encodeURIComponent(id_ntbr_config)
        +'&id_ntbr_onedrive='+encodeURIComponent(id_ntbr_onedrive)
        +'&name='+encodeURIComponent(name)
        +'&active='+encodeURIComponent(active)
        +'&config_nb_backup='+encodeURIComponent(config_nb_backup)
        +'&code='+encodeURIComponent(code)
        +'&directory_path='+encodeURIComponent(directory_path)
        +'&directory_key='+encodeURIComponent(directory_key),
		function(data)
		{
			if (data.result) {
                var result = data.result;

                if (result.success && parseInt(result.success) === 1 && result.id_ntbr_onedrive) {
                    if (parseInt(display_result) === 1) {
                        $('#result .confirm.alert.alert-success').html('<p>' + save_account_success + '</p>').show();
                    }

                    /*$('#onedrive_code_'+id_ntbr_config).val('');
                    $('#onedrive_code_'+id_ntbr_config).attr('data-origin', '');*/

                    if (parseInt(result.id_ntbr_onedrive) !== id_ntbr_onedrive) {
                        $('#id_ntbr_onedrive_'+id_ntbr_config).val(result.id_ntbr_onedrive);

                        var nb_onedrive_account = parseInt($('#config_'+id_ntbr_config+' .config_onedrive_accounts .nb_account').val()) + 1;
                        $('#config_'+id_ntbr_config+' .config_onedrive_accounts .nb_account').val(nb_onedrive_account);

                        $('#onedrive_tabs_'+id_ntbr_config+' button:first').before('<button type="button" class="btn btn-default choose_onedrive_account" id="onedrive_account_'+id_ntbr_config+'_'+result.id_ntbr_onedrive+'" value="'+result.id_ntbr_onedrive+'">'+name+'</button>');

                        $('#onedrive_account_'+id_ntbr_config+'_'+result.id_ntbr_onedrive).click(function(){
                            if (checkFormChanged(onedrive_account_id+'_'+id_ntbr_config)) {
                                if (confirm(confirm_change_account) == true) {
                                    displayOnedriveAccount($(this).val());
                                } else {
                                    selectOnedriveTab($('#id_ntbr_onedrive_'+id_ntbr_config).val());
                                }
                            } else {
                                displayOnedriveAccount($(this).val());
                            }
                        });
                    }

                    if (parseInt(active) === 1) {
                        $('#onedrive_account_'+id_ntbr_config+'_'+result.id_ntbr_onedrive).removeClass('disable').addClass('enable');
                    } else {
                        $('#onedrive_account_'+id_ntbr_config+'_'+result.id_ntbr_onedrive).removeClass('enable').addClass('disable');
                    }

                    $('#config_'+id_ntbr_config+' .send_onedrive').removeClass('disable').removeClass('enable');

                    if ($('#config_'+id_ntbr_config+' .choose_onedrive_account.enable').length > 0) {
                        $('#config_'+id_ntbr_config+' .send_onedrive').addClass('enable');
                    } else if ($('#config_'+id_ntbr_config+' .choose_onedrive_account.disable').length > 0) {
                        $('#config_'+id_ntbr_config+' .send_onedrive').addClass('disable');
                    }

                    displayOnedriveAccount(result.id_ntbr_onedrive);
                } else {
                    var html_error = '';
                    html_error += '<p>'+name+' - ' + save_account_error + '</p>';
                    if(result.errors)
                    {
                        html_error += '<ul>';
                        $.each(result.errors, function(key, error)
                        {
                            html_error += '<li>' + error + '</li>';
                        });
                        html_error += '</ul>';
                    }
                    if (parseInt(display_result) === 1) {
                        $('#result .error.alert.alert-danger').html(html_error).show();
                    }
                    onedrive_save_result = html_error;
                }

                $('#loader_container').hide();

                $('html, body').animate({
                    scrollTop: 0
                }, 1000);
            }
		},"json"
	);
}

function saveSugarsync(display_result)
{
    $('#loader_container').show();
    $('#result div').html('').hide();

    var id_ntbr_config = $('#choose_config').val();

    if (typeof display_result === 'undefined') {
        display_result = 1;
    }

    sugarsync_save_result   = '';
    var id_ntbr_sugarsync   = parseInt($('#id_ntbr_sugarsync_'+id_ntbr_config).val());
    var name                = $('#sugarsync_name_'+id_ntbr_config).val();
    var active              = 0;
    var config_nb_backup    = $('#nb_keep_backup_sugarsync_'+id_ntbr_config).val();
    var login               = $('#sugarsync_login_'+id_ntbr_config).val();
    var password            = $('#sugarsync_password_'+id_ntbr_config).val();
    var directory_path      = $('#sugarsync_dir_path_'+id_ntbr_config).val();
    var directory_key       = $('#sugarsync_dir_'+id_ntbr_config).val();

    if($('#active_sugarsync_on_'+id_ntbr_config).is(':checked')) {
		active = 1;
    }

	return $.post(
		admin_link_ntbr,
		'save_sugarsync=1'
        +'&id_ntbr_config='+encodeURIComponent(id_ntbr_config)
        +'&id_ntbr_sugarsync='+encodeURIComponent(id_ntbr_sugarsync)
        +'&name='+encodeURIComponent(name)
        +'&active='+encodeURIComponent(active)
        +'&config_nb_backup='+encodeURIComponent(config_nb_backup)
        +'&login='+encodeURIComponent(login)
        +'&password='+encodeURIComponent(password)
        +'&directory_path='+encodeURIComponent(directory_path)
        +'&directory_key='+encodeURIComponent(directory_key),
		function(data)
		{
			if (data.result) {
                var result = data.result;

                if (result.success && parseInt(result.success) === 1 && result.id_ntbr_sugarsync) {
                    if (parseInt(display_result) === 1) {
                        $('#result .confirm.alert.alert-success').html('<p>' + save_account_success + '</p>').show();
                    }

                    if (parseInt(result.id_ntbr_sugarsync) !== id_ntbr_sugarsync) {
                        $('#id_ntbr_sugarsync_'+id_ntbr_config).val(result.id_ntbr_sugarsync);

                        var nb_sugarsync_account = parseInt($('#config_'+id_ntbr_config+' .config_sugarsync_accounts .nb_account').val()) + 1;
                        $('#config_'+id_ntbr_config+' .config_sugarsync_accounts .nb_account').val(nb_sugarsync_account);

                        $('#sugarsync_tabs_'+id_ntbr_config+' button:first').before('<button type="button" class="btn btn-default choose_sugarsync_account" id="sugarsync_account_'+id_ntbr_config+'_'+result.id_ntbr_sugarsync+'" value="'+result.id_ntbr_sugarsync+'">'+name+'</button>');

                        $('#sugarsync_account_'+id_ntbr_config+'_'+result.id_ntbr_sugarsync).click(function(){
                            if (checkFormChanged(sugarsync_account_id+'_'+id_ntbr_config)) {
                                if (confirm(confirm_change_account) == true) {
                                    displaySugarsyncAccount($(this).val());
                                } else {
                                    selectSugarsyncTab($('#id_ntbr_sugarsync_'+id_ntbr_config).val());
                                }
                            } else {
                                displaySugarsyncAccount($(this).val());
                            }
                        });
                    }

                    if (parseInt(active) === 1) {
                        $('#sugarsync_account_'+id_ntbr_config+'_'+result.id_ntbr_sugarsync).removeClass('disable').addClass('enable');
                    } else {
                        $('#sugarsync_account_'+id_ntbr_config+'_'+result.id_ntbr_sugarsync).removeClass('enable').addClass('disable');
                    }

                    $('#config_'+id_ntbr_config+' .send_sugarsync').removeClass('disable').removeClass('enable');

                    if ($('#config_'+id_ntbr_config+' .choose_sugarsync_account.enable').length > 0) {
                        $('#config_'+id_ntbr_config+' .send_sugarsync').addClass('enable');
                    } else if ($('#config_'+id_ntbr_config+' .choose_sugarsync_account.disable').length > 0) {
                        $('#config_'+id_ntbr_config+' .send_sugarsync').addClass('disable');
                    }

                    displaySugarsyncAccount(result.id_ntbr_sugarsync);
                } else {
                    var html_error = '';
                    html_error += '<p>'+name+' - ' + save_account_error + '</p>';
                    if(result.errors)
                    {
                        html_error += '<ul>';
                        $.each(result.errors, function(key, error)
                        {
                            html_error += '<li>' + error + '</li>';
                        });
                        html_error += '</ul>';
                    }
                    if (parseInt(display_result) === 1) {
                        $('#result .error.alert.alert-danger').html(html_error).show();
                    }
                    sugarsync_save_result = html_error;
                }

                $('#loader_container').hide();

                $('html, body').animate({
                    scrollTop: 0
                }, 1000);
            }
		},"json"
	);
}

function saveHubic(display_result)
{
    $('#loader_container').show();
    $('#result div').html('').hide();

    var id_ntbr_config = $('#choose_config').val();

    if (typeof display_result === 'undefined') {
        display_result = 1;
    }

    hubic_save_result       = '';
    var id_ntbr_hubic       = parseInt($('#id_ntbr_hubic_'+id_ntbr_config).val());
    var name                = $('#hubic_name_'+id_ntbr_config).val();
    var active              = 0;
    var config_nb_backup    = $('#nb_keep_backup_hubic_'+id_ntbr_config).val();
    var code                = $('#hubic_code_'+id_ntbr_config).val();
    var directory           = $('#hubic_dir_'+id_ntbr_config).val();

    if($('#active_hubic_on_'+id_ntbr_config).is(':checked')) {
		active = 1;
    }

	return $.post(
		admin_link_ntbr,
		'save_hubic=1'
        +'&id_ntbr_config='+encodeURIComponent(id_ntbr_config)
        +'&id_ntbr_hubic='+encodeURIComponent(id_ntbr_hubic)
        +'&name='+encodeURIComponent(name)
        +'&active='+encodeURIComponent(active)
        +'&config_nb_backup='+encodeURIComponent(config_nb_backup)
        +'&code='+encodeURIComponent(code)
        +'&directory='+encodeURIComponent(directory),
		function(data)
		{
			if (data.result) {
                var result = data.result;

                if (result.success && parseInt(result.success) === 1 && result.id_ntbr_hubic) {
                    if (parseInt(display_result) === 1) {
                        $('#result .confirm.alert.alert-success').html('<p>' + save_account_success + '</p>').show();
                    }

                    /*$('#hubic_code_'+id_ntbr_config).val('');
                    $('#hubic_code_'+id_ntbr_config).attr('data-origin', '');*/

                    if (parseInt(result.id_ntbr_hubic) !== id_ntbr_hubic) {
                        $('#id_ntbr_hubic_'+id_ntbr_config).val(result.id_ntbr_hubic);

                        var nb_hubic_account = parseInt($('#config_'+id_ntbr_config+' .config_hubic_accounts .nb_account').val()) + 1;
                        $('#config_'+id_ntbr_config+' .config_hubic_accounts .nb_account').val(nb_hubic_account);

                        $('#hubic_tabs_'+id_ntbr_config+' button:first').before('<button type="button" class="btn btn-default choose_hubic_account" id="hubic_account_'+id_ntbr_config+'_'+result.id_ntbr_hubic+'" value="'+result.id_ntbr_hubic+'">'+name+'</button>');

                        $('#hubic_account_'+id_ntbr_config+'_'+result.id_ntbr_hubic).click(function(){
                            if (checkFormChanged(hubic_account_id+'_'+id_ntbr_config)) {
                                if (confirm(confirm_change_account) == true) {
                                    displayHubicAccount($(this).val());
                                } else {
                                    selectHubicTab($('#id_ntbr_hubic_'+id_ntbr_config).val());
                                }
                            } else {
                                displayHubicAccount($(this).val());
                            }
                        });
                    }

                    if (parseInt(active) === 1) {
                        $('#hubic_account_'+id_ntbr_config+'_'+result.id_ntbr_hubic).removeClass('disable').addClass('enable');
                    } else {
                        $('#hubic_account_'+id_ntbr_config+'_'+result.id_ntbr_hubic).removeClass('enable').addClass('disable');
                    }

                    $('#config_'+id_ntbr_config+' .send_hubic').removeClass('disable').removeClass('enable');

                    if ($('#config_'+id_ntbr_config+' .choose_hubic_account.enable').length > 0) {
                        $('#config_'+id_ntbr_config+' .send_hubic').addClass('enable');
                    } else if ($('#config_'+id_ntbr_config+' .choose_hubic_account.disable').length > 0) {
                        $('#config_'+id_ntbr_config+' .send_hubic').addClass('disable');
                    }

                    displayHubicAccount(result.id_ntbr_hubic);
                } else {
                    var html_error = '';
                    html_error += '<p>'+name+' - ' + save_account_error + '</p>';
                    if(result.errors)
                    {
                        html_error += '<ul>';
                        $.each(result.errors, function(key, error)
                        {
                            html_error += '<li>' + error + '</li>';
                        });
                        html_error += '</ul>';
                    }
                    if (parseInt(display_result) === 1) {
                        $('#result .error.alert.alert-danger').html(html_error).show();
                    }
                    hubic_save_result = html_error;
                }

                $('#loader_container').hide();

                $('html, body').animate({
                    scrollTop: 0
                }, 1000);
            }
		},"json"
	);
}

function saveAws(display_result)
{
    $('#loader_container').show();
    $('#result div').html('').hide();

    var id_ntbr_config = $('#choose_config').val();


    if (typeof display_result === 'undefined') {
        display_result = 1;
    }

    aws_save_result         = '';
    var id_ntbr_aws         = parseInt($('#id_ntbr_aws_'+id_ntbr_config).val());
    var name                = $('#aws_name_'+id_ntbr_config).val();
    var active              = 0;
    var config_nb_backup    = $('#nb_keep_backup_aws_'+id_ntbr_config).val();
    var directory_key       = $('#aws_directory_key_'+id_ntbr_config).val();
    var directory_path      = $('#aws_directory_path_'+id_ntbr_config).val();
    var access_key_id       = $('#aws_access_key_id_'+id_ntbr_config).val();
    var secret_access_key   = $('#aws_secret_access_key_'+id_ntbr_config).val();
    var region              = $('#aws_region_'+id_ntbr_config).val();
    var bucket              = $('#aws_bucket_'+id_ntbr_config).val();
    var storage_class       = $('#aws_storage_class_'+id_ntbr_config).val();

    if($('#active_aws_on_'+id_ntbr_config).is(':checked')) {
		active = 1;
    }

	return $.post(
		admin_link_ntbr,
		'save_aws=1'
        +'&id_ntbr_config='+encodeURIComponent(id_ntbr_config)
        +'&id_ntbr_aws='+encodeURIComponent(id_ntbr_aws)
        +'&name='+encodeURIComponent(name)
        +'&active='+encodeURIComponent(active)
        +'&config_nb_backup='+encodeURIComponent(config_nb_backup)
        +'&directory_path='+encodeURIComponent(directory_path)
        +'&directory_key='+encodeURIComponent(directory_key)
        +'&access_key_id='+encodeURIComponent(access_key_id)
        +'&secret_access_key='+encodeURIComponent(secret_access_key)
        +'&region='+encodeURIComponent(region)
        +'&bucket='+encodeURIComponent(bucket)
        +'&storage_class='+encodeURIComponent(storage_class),
		function(data)
		{
			if (data.result) {
                var result = data.result;

                if (result.success && parseInt(result.success) === 1 && result.id_ntbr_aws) {
                    if (parseInt(display_result) === 1) {
                        $('#result .confirm.alert.alert-success').html('<p>' + save_account_success + '</p>').show();
                    }

                    /*$('#aws_access_key_id_'+id_ntbr_config).val('');
                    $('#aws_access_key_id_'+id_ntbr_config).attr('data-origin', '');
                    $('#aws_secret_access_key_'+id_ntbr_config).val('');
                    $('#aws_secret_access_key_'+id_ntbr_config).attr('data-origin', '');*/

                    if (parseInt(result.id_ntbr_aws) !== id_ntbr_aws) {
                        $('#id_ntbr_aws_'+id_ntbr_config).val(result.id_ntbr_aws);

                        var nb_aws_account = parseInt($('#config_'+id_ntbr_config+' .config_aws_accounts .nb_account').val()) + 1;
                        $('#config_'+id_ntbr_config+' .config_aws_accounts .nb_account').val(nb_aws_account);

                        $('#aws_tabs_'+id_ntbr_config+' button:first').before('<button type="button" class="btn btn-default choose_aws_account" id="aws_account_'+id_ntbr_config+'_'+result.id_ntbr_aws+'" value="'+result.id_ntbr_aws+'">'+name+'</button>');

                        $('#aws_account_'+id_ntbr_config+'_'+result.id_ntbr_aws).click(function(){
                            if (checkFormChanged(aws_account_id+'_'+id_ntbr_config)) {
                                if (confirm(confirm_change_account) == true) {
                                    displayAwsAccount($(this).val());
                                } else {
                                    selectAwsTab($('#id_ntbr_aws_'+id_ntbr_config).val());
                                }
                            } else {
                                displayAwsAccount($(this).val());
                            }
                        });
                    }

                    if (parseInt(active) === 1) {
                        $('#aws_account_'+id_ntbr_config+'_'+result.id_ntbr_aws).removeClass('disable').addClass('enable');
                    } else {
                        $('#aws_account_'+id_ntbr_config+'_'+result.id_ntbr_aws).removeClass('enable').addClass('disable');
                    }

                    $('#config_'+id_ntbr_config+' .send_aws').removeClass('disable').removeClass('enable');

                    if ($('#config_'+id_ntbr_config+' .choose_aws_account.enable').length > 0) {
                        $('#config_'+id_ntbr_config+' .send_aws').addClass('enable');
                    } else if ($('#config_'+id_ntbr_config+' .choose_aws_account.disable').length > 0) {
                        $('#config_'+id_ntbr_config+' .send_aws').addClass('disable');
                    }

                    displayAwsAccount(result.id_ntbr_aws);
                } else {
                    var html_error = '';
                    html_error += '<p>'+name+' - ' + save_account_error + '</p>';
                    if(result.errors)
                    {
                        html_error += '<ul>';
                        $.each(result.errors, function(key, error)
                        {
                            html_error += '<li>' + error + '</li>';
                        });
                        html_error += '</ul>';
                    }
                    if (parseInt(display_result) === 1) {
                        $('#result .error.alert.alert-danger').html(html_error).show();
                    }
                    aws_save_result = html_error;
                }

                $('#loader_container').hide();

                $('html, body').animate({
                    scrollTop: 0
                }, 1000);
            }
		},"json"
	);
}

function checkConnectionFtp()
{
    $('#loader_container').show();
    $('#result div').html('').hide();
    var id_ntbr_config = $('#choose_config').val();

    var id_ntbr_ftp = parseInt($('#id_ntbr_ftp_'+id_ntbr_config).val());

	$.post(
		admin_link_ntbr,
		'check_connection_ftp=1'
        +'&id_ntbr_ftp='+encodeURIComponent(id_ntbr_ftp),
		function(data)
		{
			if (data.success && parseInt(data.success) === 1) {
                $('#result .confirm.alert.alert-success').html('<p>' + check_connection_success + '</p>').show();
            } else {
                $('#result .error.alert.alert-danger').html('<p>' + check_connection_error + '</p>').show();
            }

            $('#loader_container').hide();

            $('html, body').animate({
                scrollTop: 0
            }, 1000);
		},"json"
	);
}

function checkConnectionDropbox()
{
    $('#loader_container').show();
    $('#result div').html('').hide();
    var id_ntbr_config = $('#choose_config').val();

    var id_ntbr_dropbox = parseInt($('#id_ntbr_dropbox_'+id_ntbr_config).val());

	$.post(
		admin_link_ntbr,
		'check_connection_dropbox=1'
        +'&id_ntbr_dropbox='+encodeURIComponent(id_ntbr_dropbox),
		function(data)
		{
			if (data.success && parseInt(data.success) === 1) {
                $('#result .confirm.alert.alert-success').html('<p>' + check_connection_success + '</p>').show();
            } else {
                $('#result .error.alert.alert-danger').html('<p>' + check_connection_error + '</p>').show();
            }

            $('#loader_container').hide();

            $('html, body').animate({
                scrollTop: 0
            }, 1000);
		},"json"
	);
}

function checkConnectionOwncloud()
{
    $('#loader_container').show();
    $('#result div').html('').hide();
    var id_ntbr_config = $('#choose_config').val();

    var id_ntbr_owncloud = parseInt($('#id_ntbr_owncloud_'+id_ntbr_config).val());

	$.post(
		admin_link_ntbr,
		'check_connection_owncloud=1'
        +'&id_ntbr_owncloud='+encodeURIComponent(id_ntbr_owncloud),
		function(data)
		{
			if (data.success && parseInt(data.success) === 1) {
                $('#result .confirm.alert.alert-success').html('<p>' + check_connection_success + '</p>').show();
            } else {
                $('#result .error.alert.alert-danger').html('<p>' + check_connection_error + '</p>').show();
            }

            $('#loader_container').hide();

            $('html, body').animate({
                scrollTop: 0
            }, 1000);
		},"json"
	);
}

function checkConnectionWebdav()
{
    $('#loader_container').show();
    $('#result div').html('').hide();
    var id_ntbr_config = $('#choose_config').val();

    var id_ntbr_webdav = parseInt($('#id_ntbr_webdav_'+id_ntbr_config).val());

	$.post(
		admin_link_ntbr,
		'check_connection_webdav=1'
        +'&id_ntbr_webdav='+encodeURIComponent(id_ntbr_webdav),
		function(data)
		{
			if (data.success && parseInt(data.success) === 1) {
                $('#result .confirm.alert.alert-success').html('<p>' + check_connection_success + '</p>').show();
            } else {
                $('#result .error.alert.alert-danger').html('<p>' + check_connection_error + '</p>').show();
            }

            $('#loader_container').hide();

            $('html, body').animate({
                scrollTop: 0
            }, 1000);
		},"json"
	);
}

function checkConnectionGoogledrive()
{
    $('#loader_container').show();
    $('#result div').html('').hide();
    var id_ntbr_config = $('#choose_config').val();

    var id_ntbr_googledrive = parseInt($('#id_ntbr_googledrive_'+id_ntbr_config).val());

	$.post(
		admin_link_ntbr,
		'check_connection_googledrive=1'
        +'&id_ntbr_googledrive='+encodeURIComponent(id_ntbr_googledrive),
		function(data)
		{
			if (data.success && parseInt(data.success) === 1) {
                $('#result .confirm.alert.alert-success').html('<p>' + check_connection_success + '</p>').show();
            } else {
                $('#result .error.alert.alert-danger').html('<p>' + check_connection_error + '</p>').show();
            }

            $('#loader_container').hide();

            $('html, body').animate({
                scrollTop: 0
            }, 1000);
		},"json"
	);
}

function checkConnectionOnedrive()
{
    $('#loader_container').show();
    $('#result div').html('').hide();
    var id_ntbr_config = $('#choose_config').val();

    var id_ntbr_onedrive = parseInt($('#id_ntbr_onedrive_'+id_ntbr_config).val());

	$.post(
		admin_link_ntbr,
		'check_connection_onedrive=1'
        +'&id_ntbr_onedrive='+encodeURIComponent(id_ntbr_onedrive),
		function(data)
		{
			if (data.success && parseInt(data.success) === 1) {
                $('#result .confirm.alert.alert-success').html('<p>' + check_connection_success + '</p>').show();
            } else {
                $('#result .error.alert.alert-danger').html('<p>' + check_connection_error + '</p>').show();
            }

            $('#loader_container').hide();

            $('html, body').animate({
                scrollTop: 0
            }, 1000);
		},"json"
	);
}

function checkConnectionSugarsync()
{
    $('#loader_container').show();
    $('#result div').html('').hide();
    var id_ntbr_config = $('#choose_config').val();

    var id_ntbr_sugarsync = parseInt($('#id_ntbr_sugarsync_'+id_ntbr_config).val());

	$.post(
		admin_link_ntbr,
		'check_connection_sugarsync=1'
        +'&id_ntbr_sugarsync='+encodeURIComponent(id_ntbr_sugarsync),
		function(data)
		{
			if (data.success && parseInt(data.success) === 1) {
                $('#result .confirm.alert.alert-success').html('<p>' + check_connection_success + '</p>').show();
            } else {
                $('#result .error.alert.alert-danger').html('<p>' + check_connection_error + '</p>').show();
            }

            $('#loader_container').hide();

            $('html, body').animate({
                scrollTop: 0
            }, 1000);
		},"json"
	);
}

function checkConnectionHubic()
{
    $('#loader_container').show();
    $('#result div').html('').hide();
    var id_ntbr_config = $('#choose_config').val();

    var id_ntbr_hubic = parseInt($('#id_ntbr_hubic_'+id_ntbr_config).val());

	$.post(
		admin_link_ntbr,
		'check_connection_hubic=1'
        +'&id_ntbr_hubic='+encodeURIComponent(id_ntbr_hubic),
		function(data)
		{
			if (data.success && parseInt(data.success) === 1) {
                $('#result .confirm.alert.alert-success').html('<p>' + check_connection_success + '</p>').show();
            } else {
                $('#result .error.alert.alert-danger').html('<p>' + check_connection_error + '</p>').show();
            }

            $('#loader_container').hide();

            $('html, body').animate({
                scrollTop: 0
            }, 1000);
		},"json"
	);
}

function checkConnectionAws()
{
    $('#loader_container').show();
    $('#result div').html('').hide();
    var id_ntbr_config = $('#choose_config').val();

    var id_ntbr_aws = parseInt($('#id_ntbr_aws_'+id_ntbr_config).val());

	$.post(
		admin_link_ntbr,
		'check_connection_aws=1'
        +'&id_ntbr_aws='+encodeURIComponent(id_ntbr_aws),
		function(data)
		{
			if (data.success && parseInt(data.success) === 1) {
                $('#result .confirm.alert.alert-success').html('<p>' + check_connection_success + '</p>').show();
            } else {
                $('#result .error.alert.alert-danger').html('<p>' + check_connection_error + '</p>').show();
            }

            $('#loader_container').hide();

            $('html, body').animate({
                scrollTop: 0
            }, 1000);
		},"json"
	);
}

function deleteFtp()
{
    if(!confirm(confirm_delete_account)) {
        return;
    }

    var id_ntbr_config = $('#choose_config').val();

    $('#loader_container').show();
    $('#result div').html('').hide();

    var id_ntbr_ftp = parseInt($('#id_ntbr_ftp_'+id_ntbr_config).val());

	$.post(
		admin_link_ntbr,
		'delete_ftp=1'
        +'&id_ntbr_ftp='+encodeURIComponent(id_ntbr_ftp),
		function(data)
		{
			if (data.success && parseInt(data.success) === 1) {
                $('#result .confirm.alert.alert-success').html('<p>' + delete_account_success + '</p>').show();
                $('#ftp_account_'+id_ntbr_config+'_'+id_ntbr_ftp).remove();
                selectFtpTab(0);
                initFtpAccount();
            } else {
                $('#result .error.alert.alert-danger').html('<p>' + delete_account_error + '</p>').show();
            }

            $('#loader_container').hide();

            $('html, body').animate({
                scrollTop: 0
            }, 1000);
		},"json"
	);
}

function deleteDropbox()
{
    if(!confirm(confirm_delete_account)) {
        return;
    }

    var id_ntbr_config = $('#choose_config').val();

    $('#loader_container').show();
    $('#result div').html('').hide();

    var id_ntbr_dropbox = parseInt($('#id_ntbr_dropbox_'+id_ntbr_config).val());

	$.post(
		admin_link_ntbr,
		'delete_dropbox=1'
        +'&id_ntbr_dropbox='+encodeURIComponent(id_ntbr_dropbox),
		function(data)
		{
			if (data.success && parseInt(data.success) === 1) {
                $('#result .confirm.alert.alert-success').html('<p>' + delete_account_success + '</p>').show();
                $('#dropbox_account_'+id_ntbr_config+'_'+id_ntbr_dropbox).remove();
                selectDropboxTab(0);
                initDropboxAccount();
            } else {
                $('#result .error.alert.alert-danger').html('<p>' + delete_account_error + '</p>').show();
            }

            $('#loader_container').hide();

            $('html, body').animate({
                scrollTop: 0
            }, 1000);
		},"json"
	);
}

function deleteOwncloud()
{
    if(!confirm(confirm_delete_account)) {
        return;
    }

    var id_ntbr_config = $('#choose_config').val();

    $('#loader_container').show();
    $('#result div').html('').hide();

    var id_ntbr_owncloud = parseInt($('#id_ntbr_owncloud_'+id_ntbr_config).val());

	$.post(
		admin_link_ntbr,
		'delete_owncloud=1'
        +'&id_ntbr_owncloud='+encodeURIComponent(id_ntbr_owncloud),
		function(data)
		{
			if (data.success && parseInt(data.success) === 1) {
                $('#result .confirm.alert.alert-success').html('<p>' + delete_account_success + '</p>').show();
                $('#owncloud_account_'+id_ntbr_config+'_'+id_ntbr_owncloud).remove();
                selectOwncloudTab(0);
                initOwncloudAccount();
            } else {
                $('#result .error.alert.alert-danger').html('<p>' + delete_account_error + '</p>').show();
            }

            $('#loader_container').hide();

            $('html, body').animate({
                scrollTop: 0
            }, 1000);
		},"json"
	);
}

function deleteWebdav()
{
    if(!confirm(confirm_delete_account)) {
        return;
    }

    var id_ntbr_config = $('#choose_config').val();

    $('#loader_container').show();
    $('#result div').html('').hide();

    var id_ntbr_webdav = parseInt($('#id_ntbr_webdav_'+id_ntbr_config).val());

	$.post(
		admin_link_ntbr,
		'delete_webdav=1'
        +'&id_ntbr_webdav='+encodeURIComponent(id_ntbr_webdav),
		function(data)
		{
			if (data.success && parseInt(data.success) === 1) {
                $('#result .confirm.alert.alert-success').html('<p>' + delete_account_success + '</p>').show();
                $('#webdav_account_'+id_ntbr_config+'_'+id_ntbr_webdav).remove();
                selectWebdavTab(0);
                initWebdavAccount();

            } else {
                $('#result .error.alert.alert-danger').html('<p>' + delete_account_error + '</p>').show();
            }

            $('#loader_container').hide();

            $('html, body').animate({
                scrollTop: 0
            }, 1000);
		},"json"
	);
}

function deleteGoogledrive()
{
    if(!confirm(confirm_delete_account)) {
        return;
    }

    var id_ntbr_config = $('#choose_config').val();

    $('#loader_container').show();
    $('#result div').html('').hide();

    var id_ntbr_googledrive = parseInt($('#id_ntbr_googledrive_'+id_ntbr_config).val());

	$.post(
		admin_link_ntbr,
		'delete_googledrive=1'
        +'&id_ntbr_googledrive='+encodeURIComponent(id_ntbr_googledrive),
		function(data)
		{
			if (data.success && parseInt(data.success) === 1) {
                $('#result .confirm.alert.alert-success').html('<p>' + delete_account_success + '</p>').show();
                $('#googledrive_account_'+id_ntbr_config+'_'+id_ntbr_googledrive).remove();
                selectGoogledriveTab(0);
                initGoogledriveAccount();
            } else {
                $('#result .error.alert.alert-danger').html('<p>' + delete_account_error + '</p>').show();
            }

            $('#loader_container').hide();

            $('html, body').animate({
                scrollTop: 0
            }, 1000);
		},"json"
	);
}

function deleteOnedrive()
{
    if(!confirm(confirm_delete_account)) {
        return;
    }

    var id_ntbr_config = $('#choose_config').val();

    $('#loader_container').show();
    $('#result div').html('').hide();

    var id_ntbr_onedrive = parseInt($('#id_ntbr_onedrive_'+id_ntbr_config).val());

	$.post(
		admin_link_ntbr,
		'delete_onedrive=1'
        +'&id_ntbr_onedrive='+encodeURIComponent(id_ntbr_onedrive),
		function(data)
		{
			if (data.success && parseInt(data.success) === 1) {
                $('#result .confirm.alert.alert-success').html('<p>' + delete_account_success + '</p>').show();
                $('#onedrive_account_'+id_ntbr_config+'_'+id_ntbr_onedrive).remove();
                selectOnedriveTab(0);
                initOnedriveAccount();
            } else {
                $('#result .error.alert.alert-danger').html('<p>' + delete_account_error + '</p>').show();
            }

            $('#loader_container').hide();

            $('html, body').animate({
                scrollTop: 0
            }, 1000);
		},"json"
	);
}

function deleteSugarsync()
{
    if(!confirm(confirm_delete_account)) {
        return;
    }

    var id_ntbr_config = $('#choose_config').val();

    $('#loader_container').show();
    $('#result div').html('').hide();

    var id_ntbr_sugarsync = parseInt($('#id_ntbr_sugarsync_'+id_ntbr_config).val());

	$.post(
		admin_link_ntbr,
		'delete_sugarsync=1'
        +'&id_ntbr_sugarsync='+encodeURIComponent(id_ntbr_sugarsync),
		function(data)
		{
			if (data.success && parseInt(data.success) === 1) {
                $('#result .confirm.alert.alert-success').html('<p>' + delete_account_success + '</p>').show();
                $('#sugarsync_account_'+id_ntbr_config+'_'+id_ntbr_sugarsync).remove();
                selectSugarsyncTab(0);
                initSugarsyncAccount();
            } else {
                $('#result .error.alert.alert-danger').html('<p>' + delete_account_error + '</p>').show();
            }

            $('#loader_container').hide();

            $('html, body').animate({
                scrollTop: 0
            }, 1000);
		},"json"
	);
}

function deleteHubic()
{
    if(!confirm(confirm_delete_account)) {
        return;
    }

    var id_ntbr_config = $('#choose_config').val();

    $('#loader_container').show();
    $('#result div').html('').hide();

    var id_ntbr_hubic = parseInt($('#id_ntbr_hubic_'+id_ntbr_config).val());

	$.post(
		admin_link_ntbr,
		'delete_hubic=1'
        +'&id_ntbr_hubic='+encodeURIComponent(id_ntbr_hubic),
		function(data)
		{
			if (data.success && parseInt(data.success) === 1) {
                $('#result .confirm.alert.alert-success').html('<p>' + delete_account_success + '</p>').show();
                $('#hubic_account_'+id_ntbr_config+'_'+id_ntbr_hubic).remove();
                selectHubicTab(0);
                initHubicAccount();
            } else {
                $('#result .error.alert.alert-danger').html('<p>' + delete_account_error + '</p>').show();
            }

            $('#loader_container').hide();

            $('html, body').animate({
                scrollTop: 0
            }, 1000);
		},"json"
	);
}

function deleteAws()
{
    if(!confirm(confirm_delete_account)) {
        return;
    }

    var id_ntbr_config = $('#choose_config').val();

    $('#loader_container').show();
    $('#result div').html('').hide();

    var id_ntbr_aws = parseInt($('#id_ntbr_aws_'+id_ntbr_config).val());

	$.post(
		admin_link_ntbr,
		'delete_aws=1'
        +'&id_ntbr_aws='+encodeURIComponent(id_ntbr_aws),
		function(data)
		{
			if (data.success && parseInt(data.success) === 1) {
                $('#result .confirm.alert.alert-success').html('<p>' + delete_account_success + '</p>').show();
                $('#aws_account_'+id_ntbr_config+'_'+id_ntbr_aws).remove();
                selectAwsTab(0);
                initAwsAccount();
            } else {
                $('#result .error.alert.alert-danger').html('<p>' + delete_account_error + '</p>').show();
            }

            $('#loader_container').hide();

            $('html, body').animate({
                scrollTop: 0
            }, 1000);
		},"json"
	);
}

function generateUrls()
{
	$('#download_links .backup_link').html('');
	$('#download_links .backup_log').html('');
	$('#download_links').hide();

	$.post(
		admin_link_ntbr,
		'generate_urls=1',
		function(data)
		{
			if(data.urls)
			{
				var backup_download_link = data.urls.backup;
				var log_download_link = data.urls.log;

				$('#download_links .backup_link').append('<a target="_blank" href="' + backup_download_link + '">' + backup_download_link + '</a>');
				$('#download_links .backup_log').append('<a target="_blank" href="' + log_download_link + '">' + log_download_link + '</a>');

				$('#download_links').show();
			}
		},"json"
	);
}

function createBackup()
{
    var d = new Date();
    time_last_refresh = d.getTime();

    var id_ntbr_config      = $('#backup_for_config').val();
    config_time_bwn_refresh = $('#time_between_refresh_'+id_ntbr_config);

    stop_backup = 0;

	$.post(
        admin_link_ntbr,
		'get_time_between_refresh=1'
        +'&id_ntbr_config='+id_ntbr_config,
        function(config)
		{
            if (typeof config.time_between_refresh !== 'undefined') {
                config_time_bwn_refresh = config.time_between_refresh;
            }

			$.post(
                admin_link_ntbr,
                'create_backup=1'
                +'&id_ntbr_config='+id_ntbr_config,
                function(data)
                {
                    resultCreateBackup(data);
                },"json"
            );

            displayProgress();
		},"json"
	)
    .fail(function(xhr, status, error) {
        console.log('Error status: '+xhr.status);
        console.log(xhr.responseText);
        ajax_went_wrong = 1;
        ajax_went_wrong_data = xhr;

        var log_msg = '';

        if (typeof xhr.responseText !== 'undefined' && xhr.responseText) {
            log_msg += xhr.responseText.trim().substring(0, 500); // In case the message is really big
        }

        endBackup(0);

        if (log_msg !== '') {
            $.post(
                admin_link_ntbr,
                'log_msg=1'
                +'&msg=ERR'+log_msg,
                function() {
                    displayProgressBackup('ERR'+log_msg);
                }
            );
        }
    });

}

function refreshBackup()
{
    if (!stop_backup) {
        var d = new Date();
        time_last_refresh = d.getTime();
        refresh_sent = 1;

        console.log('Refreshing');

        $.post(
            admin_link_ntbr,
            'refresh_backup=1',
            function(data)
            {
                refresh_sent = 0;
                resultCreateBackup(data);
            },"json"
        )
        .fail(function(xhr, status, error) {
            console.log('Error status: '+xhr.status);
            console.log(xhr.responseText);
            ajax_went_wrong = 1;
            ajax_went_wrong_data = xhr;

            var log_msg = '';

            if (typeof xhr.responseText !== 'undefined' && xhr.responseText) {
                log_msg += xhr.responseText.trim().substring(0, 500); // In case the message is really big
            }

            endBackup(0);

            if (log_msg !== '') {
                $.post(
                    admin_link_ntbr,
                    'log_msg=1'
                    +'&msg=ERR'+log_msg,
                    function() {
                        displayProgressBackup('ERR'+log_msg);
                    }
                );
            }
        });
    } else {
        console.log('stop backup');
    }
}

function resultCreateBackup(data)
{
    if (data) {
        if (typeof data.backuplist !== 'undefined') {
            var backups_list = displayBackupsList(data.backuplist);
            //$('#backup_files').html(data.backuplist);
            $('#backup_files').html(backups_list);
        }

        if (typeof data.warnings !== 'undefined' && data.warnings) {
            backup_warning += '<ul class="error_progress">';

            $.each(data.warnings, function(key, warning) {
                backup_warning += '<li>' + warning + '</li>';
            });

            backup_warning += '</ul>';
        }
    }
}

function displayProgress()
{
    backup_warning = '';
	$('#backup_progress_panel').show();
	$('#create_backup').hide();
	$('#stop_backup').show();
	$('#backup_for_config').hide();
	$('#backup_progress').removeClass('error_progress');
	$('#backup_progress').removeClass('success_progress');
	$('#backup_progress').text('');

    var id_ntbr_config                  = $('#choose_config').val();
    var time_between_progress_refresh   = parseInt($('#time_between_progress_refresh_'+id_ntbr_config).val());

    if (!time_between_progress_refresh) {
        time_between_progress_refresh = 1;
    }

    $('#result div').html('').hide();
    $('#backup_progress').html(start_backup);

    /* Call the function every x seconde*/
	progressBackup = setInterval("getProgressBackup()", (time_between_progress_refresh * 1000));
}

function getProgressBackup()
{
    $('#result div').html('').hide();

    $.post(backup_progress, function( data )
    {
        if (data) {
            displayProgressBackup(data);
        }
    });
}

function displayProgressBackup(data)
{
    data = data.trim();
    console.log(data);
    console.log(refresh_sent);

    if (config_time_bwn_refresh > 0 && data !== 'REFRESH') {
        var d = new Date();
        var time = d.getTime();
        var time_since_last_refresh = (time - time_last_refresh) / 1000; // Convert in seconds

        if (parseInt(time_since_last_refresh) > (parseInt(config_time_bwn_refresh) + parseInt(time_before_warning_timeout))) {
            var log_msg = '';

            if (ajax_went_wrong) {
                if (typeof ajax_went_wrong_data.responseText !== 'undefined' && ajax_went_wrong_data.responseText) {
                    log_msg += log_msg = ajax_went_wrong_data.responseText.trim();
                }

                data = log_msg.substring(0, 500); // In case the message is really big

                if (data === '') {
                    data = warning_probable_timeout;
                }

            } else {
                data    = warning_probable_timeout;
                log_msg = data;
            }

            if (log_msg !== '') {
                $.post(
                    admin_link_ntbr,
                    'log_msg=1'
                    +'&msg='+log_msg
                );
            }
        }
    }

    if (data === 'RESUME') {
        refresh_sent = 0;
        return;
    }

    if (data === 'REFRESH') {
        if (parseInt(refresh_sent) == 0 && parseInt(display_progress_only) == 0) {
            refresh_sent = 1;
            setTimeout(function(){
                refreshBackup();
            }, 1000);// Wait a second before refresh
        }
    } else {
        refresh_sent = 0;
        var three_first_letters = data.substring(0,3);
        if(three_first_letters === 'ERR' || three_first_letters === 'END')
        {
            var success = 0;
            clearInterval(progressBackup);

            if(three_first_letters === 'ERR') {
                data = data.replace(three_first_letters, '');
            }
            else if(three_first_letters === 'END') {
                data = create_success;
                success = 1;
            }

            data = '<p>' + data + '</p>';

            endBackup(success);
        } else if(three_first_letters === 'WAR') {
            data = data.replace(three_first_letters, '');
        }
        $('#backup_progress').html(data + backup_warning);
    }
}

function endBackup(success)
{
    clearInterval(progressBackup);
    stop_backup = 1;

    if (success) {
        $('#backup_progress').addClass('success_progress');
    } else {
        $('#backup_progress').addClass('error_progress');
    }

    //$('#delete_backup').show();
    $('#create_backup').show();
    $('#stop_backup').hide();
    $('#backup_for_config').show();
}

function seeBackup(nb)
{
    $('#sub_backups'+nb).toggle();
    var icon_button = $('#backup' + nb + ' .backup_see i');
    if (icon_button.hasClass('fa-eye')) {
        icon_button.removeClass('fa-eye');
        icon_button.addClass('fa-eye-slash');
    } else {
        icon_button.removeClass('fa-eye-slash');
        icon_button.addClass('fa-eye');
    }
}

function seeDistantBackup(account, nb)
{
    $('#'+account+'_sub_backups'+nb).toggle();
    var icon_button = $('#'+account+'_backup' + nb + ' .distant_backup_see i');
    if (icon_button.hasClass('fa-eye')) {
        icon_button.removeClass('fa-eye');
        icon_button.addClass('fa-eye-slash');
    } else {
        icon_button.removeClass('fa-eye-slash');
        icon_button.addClass('fa-eye');
    }
}

function sendBackup(nb)
{
    if (confirm(confirm_send_away_backup) == true) {
        $.post(
            admin_link_ntbr,
            'send_backup=1'
            + '&nb=' + nb,
            function(data)
            {
                resultCreateBackup(data);
            },"json"
        );

        displayProgress();
    }
}

function saveInfosBackup(nb)
{
    $('#loader_container').show();
    $('#result div').html('').hide();

    var backup_comment  = $('#comment_backup_'+nb).val();
    var backup_safe     = 0;
    var backup_name     = $('#comment_backup_'+nb).parent().parent().find('.backup_name').text();

    if ($('#safe_backup_'+nb).is(':checked')) {
        backup_safe = 1;
    }

	$.post(
		admin_link_ntbr,
		'save_infos_backup=1'
		+'&backup_name=' + backup_name
		+'&backup_safe=' + backup_safe
		+'&backup_comment=' + backup_comment,
		function( data )
		{
			if (data.result !== 1 && data.result !== '1') {
				$('#result .error.alert.alert-danger').html('<p>' + save_infos_backup_error + '</p>').show();
			} else {
				$('#result .confirm.alert.alert-success').html('<p>' + save_infos_backup_success + '</p>').show();
			}

            $('#loader_container').hide();
		},"json"
	);
}

function deleteBackup(nb)
{
    if (confirm(confirm_delete_backup) == true) {
        $('#loader_container').show();
        $('#result div').html('').hide();
        /*$('#backup' + nb).hide();*/
        $.post(
            admin_link_ntbr,
            'delete_backup=1'
            +'&nb=' + nb,
            function(data)
            {
                if (data.result.success !== 1 && data.result.success !== '1') {
                    $('#result .error.alert.alert-danger').html('<p>' + delete_error + '</p>').show();
                } else {
                    $('#result .confirm.alert.alert-success').html('<p>' + delete_success + '</p>').show();
                }

                // We update the files list
                if (data.result.update_list !== '-') {
                    var backups_list = displayBackupsList(data.result.update_list);
                    //$('#backup_files').html(data.result.update_list);
                    $('#backup_files').html(backups_list);
                }

                $('#create_backup').show();
                $('#stop_backup').hide();
                $('#backup_for_config').show();
                $('#loader_container').hide();
            },"json"
        );
    }
}

function displayOnedriveTree()
{
    var id_ntbr_config = $('#choose_config').val();
    $('#onedrive_tree_'+id_ntbr_config).html('<img src="'+ajax_loader+'"/>');
    var id_ntbr_onedrive    = parseInt($('#id_ntbr_onedrive_'+id_ntbr_config).val());

    $.post(
		admin_link_ntbr,
        'display_onedrive_tree=1'
        + '&id_ntbr_onedrive='+id_ntbr_onedrive,
		function(data)
		{
            if (data.tree) {
                $('#onedrive_tree_'+id_ntbr_config).html(data.tree);

                $('input[name=onedrive_dir_'+id_ntbr_config+']').click(function()
                {
                    $('#onedrive_dir_'+id_ntbr_config).val($(this).parent().find('.onedrive_dir').val());
                    $('#onedrive_dir_path_'+id_ntbr_config).val($(this).parent().find('input[name=onedrive_path_'+id_ntbr_config+']').val());
                });
            } else {
                $('#onedrive_tree_'+id_ntbr_config).html(tree_loading_error);
            }
		},"json"
	);
}

function getOnedriveTreeChildren(id_parent, onedrive_dir, level, path, target)
{
    var id_ntbr_config = $('#choose_config').val();
    $('#onedrive_tree_'+id_ntbr_config).append('<img class="loader" src="'+ajax_loader+'"/>');
    var id_ntbr_onedrive    = parseInt($('#id_ntbr_onedrive_'+id_ntbr_config).val());

    $.post(
		admin_link_ntbr,
        'display_onedrive_tree_child=1'
        + '&id_ntbr_onedrive='+id_ntbr_onedrive
        + '&id_parent=' + id_parent
        + '&onedrive_dir=' + onedrive_dir
        + '&level=' + level
        + '&path=' + path,
		function(data)
		{
			$(target).parent().parent().append(data.tree);
            $(target).remove();
            $('#onedrive_tree_'+id_ntbr_config+' .loader').remove();

            $('input[name=onedrive_dir_'+id_ntbr_config+']').click(function()
            {
                $('#onedrive_dir_'+id_ntbr_config).val($(this).parent().find('.onedrive_dir').val());
                $('#onedrive_dir_path_'+id_ntbr_config).val($(this).parent().find('input[name=onedrive_path_'+id_ntbr_config+']').val());
            });
		},"json"
	);
}

function displaySugarsyncTree()
{
    var id_ntbr_config = $('#choose_config').val();
    $('#sugarsync_tree_'+id_ntbr_config).html('<img src="'+ajax_loader+'"/>');
    var id_ntbr_sugarsync    = parseInt($('#id_ntbr_sugarsync_'+id_ntbr_config).val());

    $.post(
		admin_link_ntbr,
        'display_sugarsync_tree=1'
        + '&id_ntbr_sugarsync='+id_ntbr_sugarsync,
		function(data)
		{
            if (data.tree) {
                $('#sugarsync_tree_'+id_ntbr_config).html(data.tree);

                $('input[name=sugarsync_dir_'+id_ntbr_config+']').click(function()
                {
                    $('#sugarsync_dir_'+id_ntbr_config).val($(this).parent().find('.sugarsync_dir').val());
                    $('#sugarsync_dir_path_'+id_ntbr_config).val($(this).parent().find('input[name=sugarsync_path_'+id_ntbr_config+']').val());
                });
            } else {
                $('#sugarsync_tree_'+id_ntbr_config).html(tree_loading_error);
            }
		},"json"
	);
}

function getSugarsyncTreeChildren(id_parent, sugarsync_dir, level, path, target)
{
    var id_ntbr_config = $('#choose_config').val();
    $('#sugarsync_tree_'+id_ntbr_config).append('<img class="loader" src="'+ajax_loader+'"/>');
    var id_ntbr_sugarsync    = parseInt($('#id_ntbr_sugarsync_'+id_ntbr_config).val());

    $.post(
		admin_link_ntbr,
        'display_sugarsync_tree_child=1'
        + '&id_ntbr_sugarsync='+id_ntbr_sugarsync
        + '&id_parent=' + id_parent
        + '&sugarsync_dir=' + sugarsync_dir
        + '&level=' + level
        + '&path=' + path,
		function(data)
		{
			$(target).parent().parent().append(data.tree);
            $(target).remove();
            $('#sugarsync_tree_'+id_ntbr_config+' .loader').remove();

            $('input[name=sugarsync_dir_'+id_ntbr_config+']').click(function()
            {
                $('#sugarsync_dir_'+id_ntbr_config).val($(this).parent().find('.sugarsync_dir').val());
                $('#sugarsync_dir_path_'+id_ntbr_config).val($(this).parent().find('input[name=sugarsync_path_'+id_ntbr_config+']').val());
            });
		},"json"
	);
}

function displayAwsTree()
{
    var id_ntbr_config = $('#choose_config').val();
    $('#aws_tree_'+id_ntbr_config).html('<img src="'+ajax_loader+'"/>');
    var id_ntbr_aws    = parseInt($('#id_ntbr_aws_'+id_ntbr_config).val());

    $.post(
		admin_link_ntbr,
        'display_aws_tree=1'
        + '&id_ntbr_aws='+id_ntbr_aws,
		function(data)
		{
            if (data.tree) {
                $('#aws_tree_'+id_ntbr_config).html(data.tree);

                // Select a directory in the tree
                $('input[name=aws_dir_key_'+id_ntbr_config+']').click(function()
                {
                    // Add the value in the input text
                    $('#aws_directory_key_'+id_ntbr_config).val($(this).parent().find('.aws_dir_key').val());
                    $('#aws_directory_path_'+id_ntbr_config).val($(this).parent().find('input[name=aws_dir_path_'+id_ntbr_config+']').val());
                });
            } else {
                $('#aws_tree_'+id_ntbr_config).html(tree_loading_error);
            }
		},"json"
	);
}

function getAwsTreeChildren(directory_key, level, directory_path, target)
{
    var id_ntbr_config = $('#choose_config').val();
    $('#aws_tree_'+id_ntbr_config).append('<img class="loader" src="'+ajax_loader+'"/>');
    var id_ntbr_aws    = parseInt($('#id_ntbr_aws_'+id_ntbr_config).val());

    $.post(
		admin_link_ntbr,
        'display_aws_tree_child=1'
        + '&id_ntbr_aws='+id_ntbr_aws
        + '&directory_key=' + directory_key
        + '&directory_path=' + directory_path
        + '&level=' + level,
		function(data)
		{
			$(target).parent().parent().append(data.tree);
            $(target).remove();
            $('#aws_tree_'+id_ntbr_config+' .loader').remove();

            // Select a directory in the tree
            $('input[name=aws_dir_key_'+id_ntbr_config+']').click(function()
            {
                // Display the value in the input text
                $('#aws_directory_key_'+id_ntbr_config).val($(this).parent().find('.aws_dir_key').val());
                $('#aws_directory_path_'+id_ntbr_config).val($(this).parent().find('input[name=aws_dir_path_'+id_ntbr_config+']').val());
            });
		},"json"
	);
}

function displayGoogledriveTree()
{
    var id_ntbr_config = $('#choose_config').val();

    $('#googledrive_tree_'+id_ntbr_config).html('<img src="'+ajax_loader+'"/>');
    var id_ntbr_googledrive = parseInt($('#id_ntbr_googledrive_'+id_ntbr_config).val());

    $.post(
		admin_link_ntbr,
        'display_googledrive_tree=1'
        + '&id_ntbr_googledrive='+id_ntbr_googledrive,
		function(data)
		{
            if (data.tree) {
                $('#googledrive_tree_'+id_ntbr_config).html(data.tree);

                $('input[name=googledrive_dir_'+id_ntbr_config+']').click(function()
                {
                    $('#googledrive_dir_'+id_ntbr_config).val($(this).parent().find('.googledrive_dir').val());
                    $('#googledrive_dir_path_'+id_ntbr_config).val($(this).parent().find('input[name=googledrive_path_'+id_ntbr_config+']').val());
                });
            } else {
                $('#googledrive_tree_'+id_ntbr_config).html(tree_loading_error);
            }
		},"json"
	);
}

function getGoogledriveTreeChildren(id_parent, googledrive_dir, level, path, target)
{
    var id_ntbr_config = $('#choose_config').val();

    $('#googledrive_tree_'+id_ntbr_config).append('<img class="loader" src="'+ajax_loader+'"/>');
    var id_ntbr_googledrive = parseInt($('#id_ntbr_googledrive_'+id_ntbr_config).val());

    $.post(
		admin_link_ntbr,
        'display_googledrive_tree_child=1'
        + '&id_ntbr_googledrive='+id_ntbr_googledrive
        + '&id_parent=' + id_parent
        + '&googledrive_dir=' + googledrive_dir
        + '&level=' + level
        + '&path=' + path,
		function(data)
		{
			$(target).parent().parent().append(data.tree);
            $(target).remove();
            $('#googledrive_tree_'+id_ntbr_config+' .loader').remove();

            $('input[name=googledrive_dir_'+id_ntbr_config+']').click(function()
            {
                $('#googledrive_dir_'+id_ntbr_config).val($(this).parent().find('.googledrive_dir').val());
                $('#googledrive_dir_path_'+id_ntbr_config).val($(this).parent().find('input[name=googledrive_path_'+id_ntbr_config+']').val());
            });
		},"json"
	);
}

function saveAllConfiguration()
{
    saveSendAwayAccounts().then(function(){
        if (
                ftp_save_result === ''
                && dropbox_save_result === ''
                && owncloud_save_result === ''
                && webdav_save_result === ''
                && googledrive_save_result === ''
                && onedrive_save_result === ''
                && hubic_save_result === ''
                && aws_save_result === ''
                && sugarsync_save_result === ''
        ) {
            saveConfiguration();
        } else {
            $('#result .error.alert.alert-danger').html(ftp_save_result + dropbox_save_result + owncloud_save_result + webdav_save_result + googledrive_save_result + onedrive_save_result + hubic_save_result + aws_save_result + sugarsync_save_result).show();
        }
    });
}

function saveSendAwayAccounts()
{
    var save_ftp;
    var save_dropbox;
    var save_owncloud;
    var save_webdav;
    var save_googledrive;
    var save_onedrive;
    var save_hubic;
    var save_aws;
    var save_sugarsync;

    var id_ntbr_config = $('#choose_config').val();

    // Try to save current FTP if needed
    if (checkFormChanged(ftp_account_id+'_'+id_ntbr_config)) {
        save_ftp = saveFtp(0);
    } else {
        ftp_save_result = '';
    }

    // Try to save current Dropbox if needed
    if (checkFormChanged(dropbox_account_id+'_'+id_ntbr_config)) {
        save_dropbox = saveDropbox(0);
    } else {
        dropbox_save_result = '';
    }

    // Try to save current hubiC if needed
    if (checkFormChanged(hubic_account_id+'_'+id_ntbr_config)) {
        save_hubic = saveHubic(0);
    } else {
        hubic_save_result = '';
    }

    // Try to save current ownCloud/Nextcloud if needed
    if (checkFormChanged(owncloud_account_id+'_'+id_ntbr_config)) {
        save_owncloud = saveOwncloud(0);
    } else {
        owncloud_save_result = '';
    }

    // Try to save current WebDAV if needed
    if (checkFormChanged(webdav_account_id+'_'+id_ntbr_config)) {
        save_webdav = saveWebdav(0);
    } else {
        webdav_save_result = '';
    }

    // Try to save current Google Drive if needed
    if (checkFormChanged(googledrive_account_id+'_'+id_ntbr_config)) {
        save_googledrive = saveGoogledrive(0);
    } else {
        googledrive_save_result = '';
    }

    // Try to save current OneDrive if needed
    if (checkFormChanged(onedrive_account_id+'_'+id_ntbr_config)) {
        save_onedrive = saveOnedrive(0);
    } else {
        onedrive_save_result = '';
    }

    // Try to save current SugarSync if needed
    if (checkFormChanged(sugarsync_account_id+'_'+id_ntbr_config)) {
        save_sugarsync = saveSugarsync(0);
    } else {
        sugarsync_save_result = '';
    }

    // Try to save current AWS if needed
    if (checkFormChanged(aws_account_id+'_'+id_ntbr_config)) {
        save_aws = saveAws(0);
    } else {
        aws_save_result = '';
    }

    return $.when(save_ftp, saveDropbox, save_owncloud, save_webdav, saveGoogledrive, save_onedrive, save_hubic, save_aws, save_sugarsync);
}

function saveConfigurationProfile()
{
    $('#loader_container').show();
    $('#result div').html('').hide();

    var name        = $('#profile_name').val();
    var type        = $('#profile_type').val();
    var is_default  = 0;

    /*if ($('#profile_is_default_on').is(':checked')) {
        is_default = 1;
    }*/

    $.post(
        admin_link_ntbr,
        'save_config_profile=1'
        + '&name=' + encodeURIComponent(name)
        + '&type=' + encodeURIComponent(type)
        + '&is_default=' + encodeURIComponent(is_default),
        function(data)
        {
            if (data.result) {
                var result = data.result;

                if (parseInt(result.success) === 1 && result.id_profile) {
                    var url = new URL(window.location.href);
                    url.searchParams.set('id_profile',result.id_profile);
                    window.location.href = url.href;
                } else {
                    if (result.errors) {
                        save_profile_config_error += '<ul>';
                        $.each(result.errors, function(key, error) {
                            save_profile_config_error += '<li>' + error + '</li>';
                        });

                        save_profile_config_error += '</ul>';
                    }

                    $('#result .error.alert.alert-danger').html(save_profile_config_error).show();

                    $('#loader_container').hide();

                    $('html, body').animate({
                        scrollTop: 0
                    }, 1000);
                }
            }
        },"json"
    );
}

function deleteConfiguration()
{
    $('#result div').html('').hide();

    var id_ntbr_config = $('#choose_config').val();

    if ($('#is_default_on_'+id_ntbr_config).is(':checked')) {
        alert(error_delete_config_default);
        return false;
    }

    $('#loader_container').show();

    $.post(
        admin_link_ntbr,
        'delete_config=1'
        + '&id_ntbr_config=' + encodeURIComponent(id_ntbr_config),
        function(data)
        {
            if (parseInt(data.result) === 1) {
                $('#result .confirm.alert.alert-success').html('<p>' + delete_config_success + '</p>').show();

                var id_default_config = $('.default_config .is_default_on:checked').parent().parent().parent().parent().find('.id_config').val();
                $('#choose_config').val(id_default_config).change();
            } else {
                $('#result .error.alert.alert-danger').html(delete_config_error).show();
            }

            $('#loader_container').hide();

            $('html, body').animate({
                scrollTop: 0
            }, 1000);
        },"json"
    );
}

function saveConfiguration()
{
    $('#loader_container').show();
    $('#result div').html('').hide();
    var id_ntbr_config                  = $('#choose_config').val();

    var save_config_error               = '';
    var send_restore                    = 0;
    var activate_log                    = 0;
    var name                            = $('#name_'+id_ntbr_config).val();
    //var type_config                     = $('#type_config_'+id_ntbr_config).val();
    var is_default                      = 0;
    var nb_backup                       = $('#nb_backup_'+id_ntbr_config).val();
    var backup_dir                      = $('#backup_dir_'+id_ntbr_config).val();
    var ignore_directories              = $('#ignore_directories_'+id_ntbr_config).val();
    var ignore_files_types              = $('#ignore_files_types_'+id_ntbr_config).val();
    var ignore_tables                   = $('#ignore_tables_'+id_ntbr_config).val();
    var mail_backup                     = $('#mail_backup_'+id_ntbr_config).val();
    var part_size                       = $('#part_size_'+id_ntbr_config).val();
    var max_file_to_backup              = $('#max_file_to_backup_'+id_ntbr_config).val();
    var dump_max_values                 = $('#dump_max_values_'+id_ntbr_config).val();
    var dump_lines_limit                = $('#dump_lines_limit_'+id_ntbr_config).val();
    var time_between_backups            = $('#time_between_backups_'+id_ntbr_config).val();
    var time_between_refresh            = $('#time_between_refresh_'+id_ntbr_config).val();
    var time_pause_between_refresh      = $('#time_pause_between_refresh_'+id_ntbr_config).val();
    var time_between_progress_refresh   = $('#time_between_progress_refresh_'+id_ntbr_config).val();
    var dump_low_interest_table         = 0;
    var disable_refresh                 = 0;
    var disable_server_timeout          = 0;
    var increase_server_memory          = 0;
    var js_download                     = 0;
    var server_memory_value             = $('#server_memory_value_'+id_ntbr_config).val();
    var activate_xsendfile              = 0;
    var send_email                      = 0;
    var email_only_error                = 0;
    var ignore_product_image            = $('#ignore_product_image_'+id_ntbr_config).val();
    var ignore_compression              = 0;
    var maintenance                     = 0;
    var delete_local_backup             = 0;
    var create_on_distant               = 0;
    var multi_config                    = 0;

    if($('#activate_log_on_'+id_ntbr_config).is(':checked'))
        activate_log = 1;

    if ($('#is_default_on_'+id_ntbr_config).is(':checked')) {
        is_default = 1;
    }

    if($('#dump_low_interest_table_on_'+id_ntbr_config).is(':checked'))
        dump_low_interest_table = 1;

    if($('#disable_refresh_on_'+id_ntbr_config).is(':checked'))
        disable_refresh = 1;

    if($('#disable_server_timeout_on_'+id_ntbr_config).is(':checked'))
        disable_server_timeout = 1;

    if($('#increase_server_memory_on_'+id_ntbr_config).is(':checked'))
        increase_server_memory = 1;

    if($('#js_download_on_'+id_ntbr_config).is(':checked'))
        js_download = 1;

    if($('#activate_xsendfile_on_'+id_ntbr_config).is(':checked'))
        activate_xsendfile = 1;

    if($('#send_email_on_'+id_ntbr_config).is(':checked'))
        send_email = 1;

    if($('#multi_config_on').is(':checked'))
        multi_config = 1;

    if($('#email_only_error_on_'+id_ntbr_config).is(':checked'))
        email_only_error = 1;

    if($('#send_restore_on_'+id_ntbr_config).is(':checked'))
        send_restore = 1;

    if($('#ignore_compression_on_'+id_ntbr_config).is(':checked'))
        ignore_compression = 1;

    if($('#maintenance_on_'+id_ntbr_config).is(':checked'))
        maintenance = 1;

    if($('#delete_local_backup_on_'+id_ntbr_config).is(':checked'))
        delete_local_backup = 1;

    if($('#create_on_distant_on_'+id_ntbr_config).is(':checked'))
        create_on_distant = 1;

    /*$('#tree_directories_'+id_ntbr_config+' input[type="checkbox"]:checked').each(function(){
        if (ignore_directories === '') {
            ignore_directories += $(this).val();
        } else {
            ignore_directories += ','+$(this).val();
        }
    });*/

    $.post(
        admin_link_ntbr,
        'save_config=1'
        + '&id_ntbr_config=' + encodeURIComponent(id_ntbr_config)
        + '&activate_log=' + encodeURIComponent(activate_log)
        + '&name=' + encodeURIComponent(name)
        //+ '&type_config=' + encodeURIComponent(type_config)
        + '&is_default=' + encodeURIComponent(is_default)
        + '&nb_backup=' + encodeURIComponent(nb_backup)
        + '&send_restore=' + encodeURIComponent(send_restore)
        + '&backup_dir=' + encodeURIComponent(backup_dir)
        + '&ignore_directories=' + encodeURIComponent(ignore_directories)
        + '&ignore_files_types=' + encodeURIComponent(ignore_files_types)
        + '&ignore_tables=' + encodeURIComponent(ignore_tables)
        + '&mail_backup=' + encodeURIComponent(mail_backup)
        + '&dump_low_interest_table=' + encodeURIComponent(dump_low_interest_table)
        + '&disable_refresh=' + encodeURIComponent(disable_refresh)
        + '&disable_server_timeout=' + encodeURIComponent(disable_server_timeout)
        + '&increase_server_memory=' + encodeURIComponent(increase_server_memory)
        + '&js_download=' + encodeURIComponent(js_download)
        + '&server_memory_value=' + encodeURIComponent(server_memory_value)
        + '&activate_xsendfile=' + encodeURIComponent(activate_xsendfile)
        + '&send_email=' + encodeURIComponent(send_email)
        + '&multi_config=' + encodeURIComponent(multi_config)
        + '&email_only_error=' + encodeURIComponent(email_only_error)
        + '&ignore_product_image=' + encodeURIComponent(ignore_product_image)
        + '&ignore_compression=' + encodeURIComponent(ignore_compression)
        + '&maintenance=' + encodeURIComponent(maintenance)
        + '&delete_local_backup=' + encodeURIComponent(delete_local_backup)
        + '&create_on_distant=' + encodeURIComponent(create_on_distant)
        + '&part_size=' + encodeURIComponent(part_size)
        + '&max_file_to_backup=' + encodeURIComponent(max_file_to_backup)
        + '&dump_max_values=' + encodeURIComponent(dump_max_values)
        + '&dump_lines_limit=' + encodeURIComponent(dump_lines_limit)
        + '&time_between_backups=' + encodeURIComponent(time_between_backups)
        + '&time_between_refresh=' + encodeURIComponent(time_between_refresh)
        + '&time_pause_between_refresh=' + encodeURIComponent(time_pause_between_refresh)
        + '&time_between_progress_refresh=' + encodeURIComponent(time_between_progress_refresh),
        function(data)
        {
            if (data.result) {
                var result = data.result;

                $('#choose_config option[value="'+id_ntbr_config+'"]').text(name);

                if (parseInt(result.success) === 1) {
                    $('#result .confirm.alert.alert-success').html('<p>' + save_config_success + '</p>').show();

                    var id_default_config = $('.default_config .is_default_on:checked').parent().parent().parent().parent().find('.id_config').val();

                    if($('#activate_log_on_'+id_default_config).is(':checked')) {
                        $('#log_button').show();
                    } else {
                        $('#log_button').hide();
                    }

                    displayAdvancedAutomation();

                    if (is_default) {
                        $('.default_config .is_default_off').prop('checked', true);
                        $('.default_config .is_default_off').attr('checked', 'checked');

                        $('#is_default_on_'+id_ntbr_config).prop('checked', true);
                        $('#is_default_on_'+id_ntbr_config).attr('checked', 'checked');
                    }
                } else {
                    if (result.errors) {
                        save_config_error += '<ul>';
                        $.each(result.errors, function(key, error) {
                            save_config_error += '<li>' + error + '</li>';
                        });

                        save_config_error += '</ul>';
                    }

                    $('#result .error.alert.alert-danger').html(save_config_error).show();
                }

                $('#loader_container').hide();

                $('html, body').animate({
                    scrollTop: 0
                }, 1000);
            }
        },"json"
    );
}

function displayAdvancedAutomation()
{
    var multi_config    = 0;
    var url             = '';
    var wget            = '';
    var curl            = '';
    var php_script      = '';

    if($('#multi_config_on').is(':checked')) {
        multi_config = 1;
    }

    $.each(list_configs, function(key, config) {
        if (parseInt(config.is_default) || multi_config) {
            var label   = cron_config_txt+' "'+config.name+'"<br/>';
            var link    = create_backup_cron+'?config='+config.id_ntbr_config;

            url += '<p>';
                url += label;
                url += '<a class="cron" target="_blank" href="'+link+'">';
                    url += link;
                url += '</a>';
            url += '</p>';

            wget += '<p>';
                wget += label;
                wget += '<span class="cron">';
                    wget += 'wget -O - -q -t 1 --max-redirect=10000 "'+link+'" >/dev/null 2>&1';
                wget += '</span>';
            wget += '</p>';

            curl += '<p>';
                curl += label;
                curl += '<span class="cron">';
                    curl += 'curl -L --max-redirs 10000 -s "'+link+'" >/dev/null 2>&1';
                curl += '</span>';
            curl += '</p>';

            php_script += '<p>';
                php_script += label;
                php_script += '<pre class="cron">';
                    php_script += '$curl_handle=curl_init();<br/>';
                    php_script += 'curl_setopt($curl_handle,CURLOPT_FOLLOWLOCATION, true);<br/>';
                    php_script += 'curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER, true);<br/>';
                    php_script += 'curl_setopt($curl_handle,CURLOPT_MAXREDIRS, 10000);<br/>';
                    php_script += 'curl_setopt($curl_handle, CURLOPT_URL, \''+link+'\');<br/>';
                    php_script += '$result = curl_exec($curl_handle);<br/>';
                    php_script += 'curl_close($curl_handle);<br/>';
                    php_script += 'if (empty($result))<br/>';
                        php_script += '    echo \''+cron_backup_error+'\';<br/>';
                    php_script += 'else<br/>';
                        php_script += '    echo $result;';
                php_script += '</pre>';
            php_script += '</p>';
        }
    });

    $('#cron_url').html(url);
    $('#cron_wget').html(wget);
    $('#cron_curl').html(curl);
    $('#cron_php_script').html(php_script);
}

function saveAutomation()
{
    $('#loader_container').show();
	$('#result div').html('').hide();

    var save_automation_error   = '';
	var automation_2nt          = 0;
	var automation_2nt_hours    = $('#automation_2nt_hours').val();
	var automation_2nt_minutes  = $('#automation_2nt_minutes').val();
	var automation_2nt_ip       = $('#automation_2nt_ip').val();

	if ($('#automation_2nt_on').is(':checked')) {
		automation_2nt = 1;
    }

    var param = 'save_automation=1';

    if (activate_2nt_automation) {
        param += '&automation_2nt=' + encodeURIComponent(automation_2nt)
        + '&automation_2nt_hours=' + encodeURIComponent(automation_2nt_hours)
        + '&automation_2nt_minutes=' + encodeURIComponent(automation_2nt_minutes)
        + '&automation_2nt_ip=' + encodeURIComponent(automation_2nt_ip);
    } else {
        param += '&automation_2nt_ip=' + encodeURIComponent(automation_2nt_ip);
    }

	$.post(
		admin_link_ntbr,
        param,
		function( data )
		{
			if (data.result === true) {
				$('#result .confirm.alert.alert-success').html('<p>' + save_automation_success + '</p>').show();
			} else {
                if (data.errors) {
                    save_automation_error += '<ul>';

                    $.each(data.errors, function(key, error)
                    {
                        save_automation_error += '<li>' + error + '</li>';
                    });

                    save_automation_error += '</ul>';
                }
				$('#result .error.alert.alert-danger').html(save_automation_error).show();
			}

            $('#loader_container').hide();

            $('html, body').animate({
                scrollTop: 0
            }, 1000);
		},"json"
	);
}

function downloadFile(type, nb)
{
	window.open(download_file+'&'+type+'&id_shop_group='+id_shop_group+'&id_shop='+id_shop+'&nb='+nb);
}

function b64ToBinary(base64_string)
{
    //The atob function will decode a base64-encoded string into a new string with a character for each byte of the binary data
    var byte_characters = atob(base64_string);

    //Each character's code point (charCode) will be the value of the byte.
    //We can create an array of byte values by applying this using the .charCodeAt method for each character in the string.
    var byte_numbers = new Array(byte_characters.length);

    for (var i = 0; i < byte_characters.length; i++) {
        byte_numbers[i] = byte_characters.charCodeAt(i);
    }

    //To convert this array of byte values into a real typed byte array use the Uint8Array constructor.
    return new Uint8Array(byte_numbers);
}

function downloadBackup(nb)
{
    $.post(
        admin_link_ntbr,
        'get_js_download=1'
        + '&nb='+nb,
        function(data)
        {
            if (parseInt(data.js_download) === 1) {
                $.post(
                    admin_link_ntbr,
                    'get_backup_download_data=1'
                    + '&nb='+nb,
                    function(data)
                    {
                        if (typeof data.result !== 'undefined' && parseInt(data.result) === 1) {
                                createBackupBlobContent(nb, 0, data.file_name, data.file_size, data.backup_dir);
                        } else {
                            downloadFile('backup', nb);
                        }
                    },"json"
                );
            } else {
                downloadFile('backup', nb);
            }
        },"json"
    );
}

function getReadableFileSizeString(file_size_in_bytes) {
    var i = -1;
    var byte_units = [' kB', ' MB', ' GB', ' TB', 'PB', 'EB', 'ZB', 'YB'];

    do {
        file_size_in_bytes = file_size_in_bytes / 1024;
        i++;
    } while (file_size_in_bytes > 1024);

    return Math.max(file_size_in_bytes, 0.1).toFixed(1) + byte_units[i];
};

function createBackupBlobContent(nb, pos, file_name, file_size, backup_dir)
{
    if (!pos) {
        $('#loader_container').show();
        $('#result div').html('').hide();
        $('#loader_txt').html('<p>'+start_download+'</p>').show();

        $('html, body').animate({
            scrollTop: 0
        }, 1000);

        file_content = [];
    }

    var xhr = new XMLHttpRequest();
    xhr.open('POST', admin_link_ntbr, true);
    xhr.responseType = 'blob';

    //Envoie les informations du header adaptées avec la requête
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onreadystatechange = function() { //Appelle une fonction au changement d'état.
        if (this.readyState === XMLHttpRequest.DONE) {
            if (this.status === 200) {
                var data = this.response;
                var data_size = data.size;
                pos += data_size;

                var blob = new Blob(
                    [data], {
                        type: "application/octet-stream"
                    }
                );

                file_content.push(blob);

                console.log(getReadableFileSizeString(pos)+'/'+getReadableFileSizeString(file_size));

                // Finished
                if (parseInt(pos) === parseInt(file_size)) {
                    $('#loader_container').hide();
                    $('#loader_txt').hide();
                    blobDownload(file_name, file_content);
                } else { // Not finished
                    // The size of the part is correct
                    if (parseInt(data_size) === max_file_download_size) {
                        $('#loader_txt').html('<p>'+getReadableFileSizeString(pos)+'/'+getReadableFileSizeString(file_size)+'</p>');
                        createBackupBlobContent(nb, pos, file_name, file_size, backup_dir);
                    } else {
                        $('#loader_container').hide();
                        $('#loader_txt').hide();
                        downloadFile('backup', nb);
                    }
                }
            } else {
                $('#loader_container').hide();
                $('#loader_txt').hide();
                downloadFile('backup', nb);
            }
        }
    };

    xhr.send(
        'download_backup=1'
        + '&nb='+nb
        + '&pos='+pos
        + '&file_size='+file_size
        + '&file_name='+encodeURIComponent(file_name)
        + '&backup_dir='+encodeURIComponent(backup_dir)
    );
}

function blobDownload(file_name, file_content)
{
    var blob = new Blob(
    file_content, {
        type: "application/octet-stream"
    });

    if (window.navigator && window.navigator.msSaveOrOpenBlob) {
        window.navigator.msSaveOrOpenBlob(blob, file_name);
    } else if (window.navigator && window.navigator.msSaveBlob) {
        window.navigator.msSaveBlob(blob, file_name);
    } else {
        var url  = URL.createObjectURL(blob);
        var link = $('<a style="display: none;"/>');

        link.attr('href', url);
        link.attr('download', file_name);

        $('body').append(link);

        link[0].click();
        window.URL.revokeObjectURL(url);
        link.remove();
    }
}

function displayBackupsList(backups)
{
    var backups_list  = '';

    $.each(backups, function(nb, backup) {
        backups_list += '<p id="backup'+nb+'">';
            backups_list += '<span class="backup_list_content_left">';

            if (backup.nb_part > 1) {
                backups_list += '<button type="button" title="'+list_backups_see+'" nb="'+nb+'" onclick="seeBackup(\''+nb+'\');" name="backup_see" class="backup_see btn btn-default">';
                    backups_list += '<i class="fas fa-eye"></i>';
                backups_list += '</button>';
            } else {
                backups_list += '<button type="button" title="'+list_backups_download+'" nb="'+nb+'" onclick="downloadFile(\'backup\', \''+nb+'\');" name="backup_download" class="backup_download btn btn-default">';
                    backups_list += '<i class="fas fa-download"></i>';
                backups_list += '</button>';
            }

            if (parseInt(light) == 0 && backup.id_config) {
                backups_list += '<button type="button" title="'+list_backups_send_away+'" nb="'+nb+'" onclick="sendBackup(\''+nb+'\');" name="send_backup" class="send_backup btn btn-default">';
                    backups_list += '<i class="fas fa-share"></i>';
                backups_list += '</button>';
            }

            backups_list += '<button type="button" title="'+list_backups_delete+'" nb="'+nb+'" onclick="deleteBackup(\''+nb+'\');" name="delete_backup" class="delete_backup btn btn-default">';
                backups_list += '<i class="fas fa-trash-alt"></i>';
            backups_list += '</button>';

            backups_list += '<span>'+backup.date+list_backups_colons+'</span>';
            backups_list += '<span class="backup_name">'+backup.name+'</span>';
            backups_list += '<span class="backup_size">('+backup.size+')</span>';
            backups_list += '<span class="backup_config">- '+backup.config_name+'</span>';

        backups_list += '</span>';
        backups_list += '<span class="backup_list_content_right">';

            var comment = '';
            var safe    = '';

            if (list_infos[backup.name]) {
                if (list_infos[backup.name]['comment']) {
                    comment = list_infos[backup.name]['comment'];
                }

                if (parseInt(list_infos[backup.name]['safe']) === 1) {
                    safe = 'checked="checked"';
                }
            }

            backups_list += '<button type="button" title="'+nt_btn_save+'" nb="'+nb+'" name="save_infos_backup" onclick="saveInfosBackup(\''+nb+'\');" class="save_infos_backup btn btn-default">';
                backups_list += '<i class="far fa-save fa-lg"></i>';
            backups_list += '</button>';
            backups_list += '<input class="backup_comment" type="text" placeholder="'+list_backups_comment+'" title="'+list_backups_comment+'" name="comment_backup['+nb+']" id="comment_backup_'+nb+'" value="'+comment+'"/>';
            backups_list += '<label class="backup_safe_label" for="safe_backup_'+nb+'" title="'+list_backups_safe_title+'">'+list_backups_safe_label+'</label>';
            backups_list += '<input class="backup_safe" type="checkbox" title="'+list_backups_safe_title+'" name="safe_backup['+nb+']" id="safe_backup_'+nb+'" value="1" '+safe+'/>';

        backups_list += '</span>';

        backups_list += '<span class="clear"></span>';
        backups_list += '</p>';

        if (backup.nb_part > 1) {
            backups_list += '<ul id="sub_backups'+nb+'" class="sub_backup">';

            $.each(backup.part, function(nb_part, part) {
                backups_list += '<li class="'+nb_part+'">';
                    backups_list += '<button type="button" title="'+list_backups_download+'" nb="'+nb_part+'" onclick="downloadFile(\'backup\', \''+nb_part+'\');" name="backup_download" class="backup_download btn btn-default">';
                        backups_list += '<i class="fas fa-download"></i>';
                    backups_list += '</button>';

                    if (parseInt(light) == 0 && backup.id_config) {
                        backups_list += '<button type="button" title="'+list_backups_send_away+'" nb="'+nb_part+'" onclick="sendBackup(\''+nb_part+'\');" name="send_backup" class="send_backup btn btn-default">';
                            backups_list += '<i class="fas fa-share"></i>';
                        backups_list += '</button>';
                    }

                    /*backups_list += '<button type="button" title="'+list_backups_delete+'" nb="'+nb_part+'" onclick="deleteBackup(\''+nb_part+'\');" name="delete_backup" class="delete_backup btn btn-default">';
                        backups_list += '<i class="fas fa-trash-alt"></i>';
                    backups_list += '</button>';*/
                    backups_list += part.name+' ('+part.size+')';
                backups_list += '</li>';
            });

            backups_list += '</ul>';
        }
    });

    return backups_list;
}

function displayDropboxBackupsList(backups, id_ntbr_dropbox)
{
    var backups_list  = '';

    $.each(backups, function(nb, backup) {
        backups_list += '<p id="dropbox_backup'+nb+'_'+id_ntbr_dropbox+'">';
            backups_list += '<span class="distant_backup_list_content">';

                if (backup.nb_part > 1) {
                    backups_list += '<button type="button" title="'+list_backups_see+'" onclick="seeDistantBackup(\'dropbox\', \''+nb+'_'+id_ntbr_dropbox+'\');" name="distant_backup_see" class="distant_backup_see btn btn-default">';
                        backups_list += '<i class="fas fa-eye"></i>';
                    backups_list += '</button>';
                } else {
                    backups_list += '<button type="button" title="'+list_backups_download+'" onclick="downloadDropboxFile(\''+id_ntbr_dropbox+'\', \''+backup.file_id+'\');" name="distant_backup_download" class="distant_backup_download btn btn-default">';
                        backups_list += '<i class="fas fa-download"></i>';
                    backups_list += '</button>';
                }

                backups_list += '<button type="button" title="'+list_backups_delete+'" onclick="deleteDropboxFile(\''+id_ntbr_dropbox+'\', \''+backup.name+'\', \''+backup.nb_part+'\', \''+nb+'_'+id_ntbr_dropbox+'\');" name="distant_delete_backup" class="distant_delete_backup btn btn-default">';
                    backups_list += '<i class="fas fa-trash-alt"></i>';
                backups_list += '</button>';

                backups_list += '<span>'+backup.date+list_backups_colons+'</span>';
                backups_list += '<span class="distant_backup_name">'+backup.name+'</span>';
                backups_list += '<span class="distant_backup_size">('+backup.size+')</span>';
                backups_list += '<span class="distant_backup_config">- '+backup.config_name+'</span>';

            backups_list += '</span>';
        backups_list += '</p>';

        if (backup.nb_part > 1) {
            backups_list += '<ul id="dropbox_sub_backups'+nb+'_'+id_ntbr_dropbox+'" class="distant_sub_backup">';

            $.each(backup.part, function(nb_part, part) {
                backups_list += '<li class="distant'+nb_part+'">';
                    backups_list += '<button type="button" title="'+list_backups_download+'" onclick="downloadDropboxFile(\''+id_ntbr_dropbox+'\', \''+part.file_id+'\');" name="distant_backup_download" class="distant_backup_download btn btn-default">';
                        backups_list += '<i class="fas fa-download"></i>';
                    backups_list += '</button>';

                    backups_list += part.name+' ('+part.size+')';
                backups_list += '</li>';
            });

            backups_list += '</ul>';
        }
    });

    return backups_list;
}

function displayGoogledriveBackupsList(backups, id_ntbr_googledrive)
{
    var backups_list  = '';

    $.each(backups, function(nb, backup) {
        backups_list += '<p id="googledrive_backup'+nb+'_'+id_ntbr_googledrive+'">';
            backups_list += '<span class="distant_backup_list_content">';

                if (backup.nb_part > 1) {
                    backups_list += '<button type="button" title="'+list_backups_see+'" onclick="seeDistantBackup(\'googledrive\', \''+nb+'_'+id_ntbr_googledrive+'\');" name="distant_backup_see" class="distant_backup_see btn btn-default">';
                        backups_list += '<i class="fas fa-eye"></i>';
                    backups_list += '</button>';
                } else {
                    backups_list += '<button type="button" title="'+list_backups_download+'" onclick="downloadGoogledriveFile(\''+id_ntbr_googledrive+'\', \''+backup.file_id+'\');" name="distant_backup_download" class="distant_backup_download btn btn-default">';
                        backups_list += '<i class="fas fa-download"></i>';
                    backups_list += '</button>';
                }

                backups_list += '<button type="button" title="'+list_backups_delete+'" onclick="deleteGoogledriveFile(\''+id_ntbr_googledrive+'\', \''+backup.name+'\', \''+backup.nb_part+'\', \''+nb+'_'+id_ntbr_googledrive+'\');" name="distant_delete_backup" class="distant_delete_backup btn btn-default">';
                    backups_list += '<i class="fas fa-trash-alt"></i>';
                backups_list += '</button>';

                backups_list += '<span>'+backup.date+list_backups_colons+'</span>';
                backups_list += '<span class="distant_backup_name">'+backup.name+'</span>';
                backups_list += '<span class="distant_backup_size">('+backup.size+')</span>';
                backups_list += '<span class="distant_backup_config">- '+backup.config_name+'</span>';

            backups_list += '</span>';
        backups_list += '</p>';

        if (backup.nb_part > 1) {
            backups_list += '<ul id="googledrive_sub_backups'+nb+'_'+id_ntbr_googledrive+'" class="distant_sub_backup">';

            $.each(backup.part, function(nb_part, part) {
                backups_list += '<li class="distant'+nb_part+'">';
                    backups_list += '<button type="button" title="'+list_backups_download+'" onclick="downloadGoogledriveFile(\''+id_ntbr_googledrive+'\', \''+part.file_id+'\');" name="distant_backup_download" class="distant_backup_download btn btn-default">';
                        backups_list += '<i class="fas fa-download"></i>';
                    backups_list += '</button>';

                    backups_list += part.name+' ('+part.size+')';
                backups_list += '</li>';
            });

            backups_list += '</ul>';
        }
    });

    return backups_list;
}

function displayOnedriveBackupsList(backups, id_ntbr_onedrive)
{
    var backups_list  = '';

    $.each(backups, function(nb, backup) {
        backups_list += '<p id="onedrive_backup'+nb+'_'+id_ntbr_onedrive+'">';
            backups_list += '<span class="distant_backup_list_content">';

                if (backup.nb_part > 1) {
                    backups_list += '<button type="button" title="'+list_backups_see+'" onclick="seeDistantBackup(\'onedrive\', \''+nb+'_'+id_ntbr_onedrive+'\');" name="distant_backup_see" class="distant_backup_see btn btn-default">';
                        backups_list += '<i class="fas fa-eye"></i>';
                    backups_list += '</button>';
                } else {
                    backups_list += '<button type="button" title="'+list_backups_download+'" onclick="downloadOnedriveFile(\''+id_ntbr_onedrive+'\', \''+backup.file_id+'\');" name="distant_backup_download" class="distant_backup_download btn btn-default">';
                        backups_list += '<i class="fas fa-download"></i>';
                    backups_list += '</button>';
                }

                backups_list += '<button type="button" title="'+list_backups_delete+'" onclick="deleteOnedriveFile(\''+id_ntbr_onedrive+'\', \''+backup.name+'\', \''+backup.nb_part+'\', \''+nb+'_'+id_ntbr_onedrive+'\');" name="distant_delete_backup" class="distant_delete_backup btn btn-default">';
                    backups_list += '<i class="fas fa-trash-alt"></i>';
                backups_list += '</button>';

                backups_list += '<span>'+backup.date+list_backups_colons+'</span>';
                backups_list += '<span class="distant_backup_name">'+backup.name+'</span>';
                backups_list += '<span class="distant_backup_size">('+backup.size+')</span>';
                backups_list += '<span class="distant_backup_config">- '+backup.config_name+'</span>';

            backups_list += '</span>';
        backups_list += '</p>';

        if (backup.nb_part > 1) {
            backups_list += '<ul id="onedrive_sub_backups'+nb+'_'+id_ntbr_onedrive+'" class="distant_sub_backup">';

            $.each(backup.part, function(nb_part, part) {
                backups_list += '<li class="distant'+nb_part+'">';
                    backups_list += '<button type="button" title="'+list_backups_download+'" onclick="downloadOnedriveFile(\''+id_ntbr_onedrive+'\', \''+part.file_id+'\');" name="distant_backup_download" class="distant_backup_download btn btn-default">';
                        backups_list += '<i class="fas fa-download"></i>';
                    backups_list += '</button>';

                    backups_list += part.name+' ('+part.size+')';
                backups_list += '</li>';
            });

            backups_list += '</ul>';
        }
    });

    return backups_list;
}

function displayOwncloudBackupsList(backups, id_ntbr_owncloud)
{
    var backups_list  = '';

    $.each(backups, function(nb, backup) {
        backups_list += '<p id="owncloud_backup'+nb+'_'+id_ntbr_owncloud+'">';
            backups_list += '<span class="distant_backup_list_content">';

                if (backup.nb_part > 1) {
                    backups_list += '<button type="button" title="'+list_backups_see+'" onclick="seeDistantBackup(\'owncloud\', \''+nb+'_'+id_ntbr_owncloud+'\');" name="distant_backup_see" class="distant_backup_see btn btn-default">';
                        backups_list += '<i class="fas fa-eye"></i>';
                    backups_list += '</button>';
                } else {
                    backups_list += '<button type="button" title="'+list_backups_download+'" onclick="downloadOwncloudFile(\''+id_ntbr_owncloud+'\', \''+backup.file_id+'\', \''+backup.name+'\', \''+backup.size_byte+'\');" name="distant_backup_download" class="distant_backup_download btn btn-default">';
                        backups_list += '<i class="fas fa-download"></i>';
                    backups_list += '</button>';
                }

                backups_list += '<button type="button" title="'+list_backups_delete+'" onclick="deleteOwncloudFile(\''+id_ntbr_owncloud+'\', \''+backup.name+'\', \''+backup.nb_part+'\', \''+nb+'_'+id_ntbr_owncloud+'\');" name="distant_delete_backup" class="distant_delete_backup btn btn-default">';
                    backups_list += '<i class="fas fa-trash-alt"></i>';
                backups_list += '</button>';

                backups_list += '<span>'+backup.date+list_backups_colons+'</span>';
                backups_list += '<span class="distant_backup_name">'+backup.name+'</span>';
                backups_list += '<span class="distant_backup_size">('+backup.size+')</span>';
                backups_list += '<span class="distant_backup_config">- '+backup.config_name+'</span>';

            backups_list += '</span>';
        backups_list += '</p>';

        if (backup.nb_part > 1) {
            backups_list += '<ul id="owncloud_sub_backups'+nb+'_'+id_ntbr_owncloud+'" class="distant_sub_backup">';

            $.each(backup.part, function(nb_part, part) {
                backups_list += '<li class="distant'+nb_part+'">';
                    backups_list += '<button type="button" title="'+list_backups_download+'" onclick="downloadOwncloudFile(\''+id_ntbr_owncloud+'\', \''+part.file_id+'\', \''+part.name+'\', \''+part.size_byte+'\');" name="distant_backup_download" class="distant_backup_download btn btn-default">';
                        backups_list += '<i class="fas fa-download"></i>';
                    backups_list += '</button>';

                    backups_list += part.name+' ('+part.size+')';
                backups_list += '</li>';
            });

            backups_list += '</ul>';
        }
    });

    return backups_list;
}

function displayWebdavBackupsList(backups, id_ntbr_webdav)
{
    var backups_list  = '';

    $.each(backups, function(nb, backup) {
        backups_list += '<p id="webdav_backup'+nb+'_'+id_ntbr_webdav+'">';
            backups_list += '<span class="distant_backup_list_content">';

                if (backup.nb_part > 1) {
                    backups_list += '<button type="button" title="'+list_backups_see+'" onclick="seeDistantBackup(\'webdav\', \''+nb+'_'+id_ntbr_webdav+'\');" name="distant_backup_see" class="distant_backup_see btn btn-default">';
                        backups_list += '<i class="fas fa-eye"></i>';
                    backups_list += '</button>';
                } else {
                    backups_list += '<button type="button" title="'+list_backups_download+'" onclick="downloadWebdavFile(\''+id_ntbr_webdav+'\', \''+backup.file_id+'\', \''+backup.name+'\', \''+backup.size_byte+'\');" name="distant_backup_download" class="distant_backup_download btn btn-default">';
                        backups_list += '<i class="fas fa-download"></i>';
                    backups_list += '</button>';
                }

                backups_list += '<button type="button" title="'+list_backups_delete+'" onclick="deleteWebdavFile(\''+id_ntbr_webdav+'\', \''+backup.name+'\', \''+backup.nb_part+'\', \''+nb+'_'+id_ntbr_webdav+'\');" name="distant_delete_backup" class="distant_delete_backup btn btn-default">';
                    backups_list += '<i class="fas fa-trash-alt"></i>';
                backups_list += '</button>';

                backups_list += '<span>'+backup.date+list_backups_colons+'</span>';
                backups_list += '<span class="distant_backup_name">'+backup.name+'</span>';
                backups_list += '<span class="distant_backup_size">('+backup.size+')</span>';
                backups_list += '<span class="distant_backup_config">- '+backup.config_name+'</span>';

            backups_list += '</span>';
        backups_list += '</p>';

        if (backup.nb_part > 1) {
            backups_list += '<ul id="webdav_sub_backups'+nb+'_'+id_ntbr_webdav+'" class="distant_sub_backup">';

            $.each(backup.part, function(nb_part, part) {
                backups_list += '<li class="distant'+nb_part+'">';
                    backups_list += '<button type="button" title="'+list_backups_download+'" onclick="downloadWebdavFile(\''+id_ntbr_webdav+'\', \''+part.file_id+'\', \''+part.name+'\', \''+part.size_byte+'\');" name="distant_backup_download" class="distant_backup_download btn btn-default">';
                        backups_list += '<i class="fas fa-download"></i>';
                    backups_list += '</button>';

                    backups_list += part.name+' ('+part.size+')';
                backups_list += '</li>';
            });

            backups_list += '</ul>';
        }
    });

    return backups_list;
}

function displayFtpBackupsList(backups, id_ntbr_ftp)
{
    var backups_list  = '';

    $.each(backups, function(nb, backup) {
        backups_list += '<p id="ftp_backup'+nb+'_'+id_ntbr_ftp+'">';
            backups_list += '<span class="distant_backup_list_content">';

                if (backup.nb_part > 1) {
                    backups_list += '<button type="button" title="'+list_backups_see+'" onclick="seeDistantBackup(\'ftp\', \''+nb+'_'+id_ntbr_ftp+'\');" name="distant_backup_see" class="distant_backup_see btn btn-default">';
                        backups_list += '<i class="fas fa-eye"></i>';
                    backups_list += '</button>';
                } else {
                    backups_list += '<button type="button" title="'+list_backups_download+'" onclick="downloadFtpFile(\''+id_ntbr_ftp+'\', \''+backup.file_id+'\', \''+backup.size_byte+'\');" name="distant_backup_download" class="distant_backup_download btn btn-default">';
                        backups_list += '<i class="fas fa-download"></i>';
                    backups_list += '</button>';
                }

                backups_list += '<button type="button" title="'+list_backups_delete+'" onclick="deleteFtpFile(\''+id_ntbr_ftp+'\', \''+backup.name+'\', \''+backup.nb_part+'\', \''+nb+'_'+id_ntbr_ftp+'\');" name="distant_delete_backup" class="distant_delete_backup btn btn-default">';
                    backups_list += '<i class="fas fa-trash-alt"></i>';
                backups_list += '</button>';

                backups_list += '<span>'+backup.date+list_backups_colons+'</span>';
                backups_list += '<span class="distant_backup_name">'+backup.name+'</span>';
                backups_list += '<span class="distant_backup_size">('+backup.size+')</span>';
                backups_list += '<span class="distant_backup_config">- '+backup.config_name+'</span>';

            backups_list += '</span>';
        backups_list += '</p>';

        if (backup.nb_part > 1) {
            backups_list += '<ul id="ftp_sub_backups'+nb+'_'+id_ntbr_ftp+'" class="distant_sub_backup">';

            $.each(backup.part, function(nb_part, part) {
                backups_list += '<li class="distant'+nb_part+'">';
                    backups_list += '<button type="button" title="'+list_backups_download+'" onclick="downloadFtpFile(\''+id_ntbr_ftp+'\', \''+part.file_id+'\', \''+part.size_byte+'\');" name="distant_backup_download" class="distant_backup_download btn btn-default">';
                        backups_list += '<i class="fas fa-download"></i>';
                    backups_list += '</button>';

                    backups_list += part.name+' ('+part.size+')';
                backups_list += '</li>';
            });

            backups_list += '</ul>';
        }
    });

    return backups_list;
}

function getFilesDropbox()
{
    var id_ntbr_config = $('#choose_config').val();

    $('#dropbox_files_'+id_ntbr_config).html('<img src="'+ajax_loader+'"/>');
    var id_ntbr_dropbox = parseInt($('#id_ntbr_dropbox_'+id_ntbr_config).val());

    $.post(
		admin_link_ntbr,
        'get_dropbox_files=1'
        + '&id_ntbr_dropbox='+id_ntbr_dropbox,
		function(data)
		{
            if (data.files) {
                if (data.res) {
                    var backups_list = displayDropboxBackupsList(data.files, id_ntbr_dropbox);
                    $('#dropbox_files_'+id_ntbr_config).html(backups_list);
                } else {
                    $('#dropbox_files_'+id_ntbr_config).html(data.files);
                }
            } else {
                $('#dropbox_files_'+id_ntbr_config).html(distant_files_loading_error);
            }
		},"json"
	);
}

function downloadDropboxFile(id_ntbr_dropbox, id_file)
{
    $('#loader_container').show();
    $('#result div').html('').hide();

	$.post(
		admin_link_ntbr,
        'download_dropbox_file=1'
        + '&id_ntbr_dropbox='+id_ntbr_dropbox
        + '&id_file='+id_file,
		function(data)
		{
            if (data.link) {
                window.open(data.link);
            } else {
                $('#result .error.alert.alert-danger').html(error_download_distant_file).show();
            }

            $('#loader_container').hide();

            $('html, body').animate({
                scrollTop: 0
            }, 1000);
		},"json"
	);
}

function deleteDropboxFile(id_ntbr_dropbox, file_name, nb_part, id_in_list)
{
    if (confirm(confirm_delete_backup) == true) {
        $('#loader_container').show();
        $('#result div').html('').hide();

        $.post(
            admin_link_ntbr,
            'delete_dropbox_file=1'
            + '&id_ntbr_dropbox='+id_ntbr_dropbox
            + '&nb_part='+nb_part
            + '&file_name='+encodeURIComponent(file_name),
            function(data)
            {
                if (data.result !== 1 && data.result !== '1') {
                    $('#result .error.alert.alert-danger').html('<p>' + delete_error + '</p>').show();
                } else {
                    $('#result .confirm.alert.alert-success').html('<p>' + delete_success + '</p>').show();

                    $('#dropbox_backup'+id_in_list).remove();
                    $('#dropbox_sub_backups'+id_in_list).remove();
                }

                $('#loader_container').hide();

                $('html, body').animate({
                    scrollTop: 0
                }, 1000);
            },"json"
        );
    }
}

function getFilesGoogledrive()
{
    var id_ntbr_config = $('#choose_config').val();

    $('#googledrive_files_'+id_ntbr_config).html('<img src="'+ajax_loader+'"/>');
    var id_ntbr_googledrive = parseInt($('#id_ntbr_googledrive_'+id_ntbr_config).val());

    $.post(
		admin_link_ntbr,
        'get_googledrive_files=1'
        + '&id_ntbr_googledrive='+id_ntbr_googledrive,
		function(data)
		{
            if (data.files) {
                if (data.res) {
                    var backups_list = displayGoogledriveBackupsList(data.files, id_ntbr_googledrive);
                    $('#googledrive_files_'+id_ntbr_config).html(backups_list);
                } else {
                    $('#googledrive_files_'+id_ntbr_config).html(data.files);
                }
            } else {
                $('#googledrive_files_'+id_ntbr_config).html(distant_files_loading_error);
            }
		},"json"
	);
}

function downloadGoogledriveFile(id_ntbr_googledrive, id_file)
{
    $('#loader_container').show();
    $('#result div').html('').hide();

	$.post(
		admin_link_ntbr,
        'download_googledrive_file=1'
        + '&id_ntbr_googledrive='+id_ntbr_googledrive
        + '&id_file='+id_file,
		function(data)
		{
            if (data.link && data.link !== '') {
                window.open(data.link);
            } else {
                $('#result .error.alert.alert-danger').html(error_download_distant_file).show();
            }

            $('#loader_container').hide();

            $('html, body').animate({
                scrollTop: 0
            }, 1000);
		},"json"
	);
}

function deleteGoogledriveFile(id_ntbr_googledrive, file_name, nb_part, id_in_list)
{
    if (confirm(confirm_delete_backup) == true) {
        $('#loader_container').show();
        $('#result div').html('').hide();

        $.post(
            admin_link_ntbr,
            'delete_googledrive_file=1'
            + '&id_ntbr_googledrive='+id_ntbr_googledrive
            + '&nb_part='+nb_part
            + '&file_name='+encodeURIComponent(file_name),
            function(data)
            {
                if (data.result !== 1 && data.result !== '1') {
                    $('#result .error.alert.alert-danger').html('<p>' + delete_error + '</p>').show();
                } else {
                    $('#result .confirm.alert.alert-success').html('<p>' + delete_success + '</p>').show();

                    $('#googledrive_backup'+id_in_list).remove();
                    $('#googledrive_sub_backups'+id_in_list).remove();
                }

                $('#loader_container').hide();

                $('html, body').animate({
                    scrollTop: 0
                }, 1000);
            },"json"
        );
    }
}

function getFilesOnedrive()
{
    var id_ntbr_config = $('#choose_config').val();

    $('#onedrive_files_'+id_ntbr_config).html('<img src="'+ajax_loader+'"/>');
    var id_ntbr_onedrive = parseInt($('#id_ntbr_onedrive_'+id_ntbr_config).val());

    $.post(
		admin_link_ntbr,
        'get_onedrive_files=1'
        + '&id_ntbr_onedrive='+id_ntbr_onedrive,
		function(data)
		{
            if (data.files) {
                if (data.res) {
                    var backups_list = displayOnedriveBackupsList(data.files, id_ntbr_onedrive);
                    $('#onedrive_files_'+id_ntbr_config).html(backups_list);
                } else {
                    $('#onedrive_files_'+id_ntbr_config).html(data.files);
                }
            } else {
                $('#onedrive_files_'+id_ntbr_config).html(distant_files_loading_error);
            }
		},"json"
	);
}

function downloadOnedriveFile(id_ntbr_onedrive, id_file)
{
    $('#loader_container').show();
    $('#result div').html('').hide();

	$.post(
		admin_link_ntbr,
        'download_onedrive_file=1'
        + '&id_ntbr_onedrive='+id_ntbr_onedrive
        + '&id_file='+id_file,
		function(data)
		{
            if (data.link && data.link !== '') {
                window.open(data.link);
            } else {
                $('#result .error.alert.alert-danger').html(error_download_distant_file).show();
            }

            $('#loader_container').hide();

            $('html, body').animate({
                scrollTop: 0
            }, 1000);
		},"json"
	);
}

function deleteOnedriveFile(id_ntbr_onedrive, file_name, nb_part, id_in_list)
{
    if (confirm(confirm_delete_backup) == true) {
        $('#loader_container').show();
        $('#result div').html('').hide();

        $.post(
            admin_link_ntbr,
            'delete_onedrive_file=1'
            + '&id_ntbr_onedrive='+id_ntbr_onedrive
            + '&nb_part='+nb_part
            + '&file_name='+encodeURIComponent(file_name),
            function(data)
            {
                if (data.result !== 1 && data.result !== '1') {
                    $('#result .error.alert.alert-danger').html('<p>' + delete_error + '</p>').show();
                } else {
                    $('#result .confirm.alert.alert-success').html('<p>' + delete_success + '</p>').show();

                    $('#onedrive_backup'+id_in_list).remove();
                    $('#onedrive_sub_backups'+id_in_list).remove();
                }

                $('#loader_container').hide();

                $('html, body').animate({
                    scrollTop: 0
                }, 1000);
            },"json"
        );
    }
}

function getFilesOwncloud()
{
    var id_ntbr_config = $('#choose_config').val();

    $('#owncloud_files_'+id_ntbr_config).html('<img src="'+ajax_loader+'"/>');
    var id_ntbr_owncloud = parseInt($('#id_ntbr_owncloud_'+id_ntbr_config).val());

    $.post(
		admin_link_ntbr,
        'get_owncloud_files=1'
        + '&id_ntbr_owncloud='+id_ntbr_owncloud,
		function(data)
		{
            if (data.files) {
                if (data.res) {
                    var backups_list = displayOwncloudBackupsList(data.files, id_ntbr_owncloud);
                    $('#owncloud_files_'+id_ntbr_config).html(backups_list);
                } else {
                    $('#owncloud_files_'+id_ntbr_config).html(data.files);
                }
            } else {
                $('#owncloud_files_'+id_ntbr_config).html(distant_files_loading_error);
            }
		},"json"
	);
}

function downloadOwncloudFile(id_ntbr_owncloud, id_file, file_name, file_size)
{
    $('#loader_container').show();
    $('#result div').html('').hide();
    $('#loader_txt').html('<p>'+start_download+'</p>').show();

    $('html, body').animate({
        scrollTop: 0
    }, 1000);

    file_content = [];

	createOwncloudBlobContent(id_ntbr_owncloud, id_file, 0, file_name, file_size);
}

function createOwncloudBlobContent(id_ntbr_owncloud, id_file, pos, file_name, file_size)
{
    var xhr = new XMLHttpRequest();
    xhr.open('POST', admin_link_ntbr, true);
    xhr.responseType = 'blob';

    //Envoie les informations du header adaptées avec la requête
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onreadystatechange = function() { //Appelle une fonction au changement d'état.
        if (this.readyState === XMLHttpRequest.DONE) {
            if (this.status === 200) {
                var data = this.response;
                var data_size = data.size;
                pos += data_size;

                var blob = new Blob(
                    [data], {
                        type: "application/octet-stream"
                    }
                );

                file_content.push(blob);

                console.log(getReadableFileSizeString(pos)+'/'+getReadableFileSizeString(file_size));

                // Finished
                if (parseInt(pos) === parseInt(file_size)) {
                    $('#loader_container').hide();
                    $('#loader_txt').hide();
                    blobDownload(file_name, file_content);
                } else { // Not finished
                    // The size of the part is correct
                    if (parseInt(data_size) === max_file_download_size) {
                        $('#loader_txt').html('<p>'+getReadableFileSizeString(pos)+'/'+getReadableFileSizeString(file_size)+'</p>');
                        createOwncloudBlobContent(id_ntbr_owncloud, id_file, pos, file_name, file_size);
                    } else {
                        $('#loader_container').hide();
                        $('#loader_txt').hide();
                        $('#result .error.alert.alert-danger').html(error_download_distant_file).show();
                    }
                }
            } else {
                $('#loader_container').hide();
                $('#loader_txt').hide();
                $('#result .error.alert.alert-danger').html(error_download_distant_file).show();
            }
        }
    };

    xhr.send(
        'download_owncloud_file=1'
        + '&id_ntbr_owncloud='+id_ntbr_owncloud
        + '&id_file='+id_file
        + '&file_size='+file_size
        + '&pos='+pos
    );
}

function deleteOwncloudFile(id_ntbr_owncloud, file_name, nb_part, id_in_list)
{
    if (confirm(confirm_delete_backup) == true) {
        $('#loader_container').show();
        $('#result div').html('').hide();

        $.post(
            admin_link_ntbr,
            'delete_owncloud_file=1'
            + '&id_ntbr_owncloud='+id_ntbr_owncloud
            + '&nb_part='+nb_part
            + '&file_name='+encodeURIComponent(file_name),
            function(data)
            {
                if (data.result !== 1 && data.result !== '1') {
                    $('#result .error.alert.alert-danger').html('<p>' + delete_error + '</p>').show();
                } else {
                    $('#result .confirm.alert.alert-success').html('<p>' + delete_success + '</p>').show();

                    $('#owncloud_backup'+id_in_list).remove();
                    $('#owncloud_sub_backups'+id_in_list).remove();
                }

                $('#loader_container').hide();

                $('html, body').animate({
                    scrollTop: 0
                }, 1000);
            },"json"
        );
    }
}

function getFilesWebdav()
{
    var id_ntbr_config = $('#choose_config').val();

    $('#webdav_files_'+id_ntbr_config).html('<img src="'+ajax_loader+'"/>');
    var id_ntbr_webdav = parseInt($('#id_ntbr_webdav_'+id_ntbr_config).val());

    $.post(
		admin_link_ntbr,
        'get_webdav_files=1'
        + '&id_ntbr_webdav='+id_ntbr_webdav,
		function(data)
		{
            if (data.files) {
                if (data.res) {
                    var backups_list = displayWebdavBackupsList(data.files, id_ntbr_webdav);
                    $('#webdav_files_'+id_ntbr_config).html(backups_list);
                } else {
                    $('#webdav_files_'+id_ntbr_config).html(data.files);
                }
            } else {
                $('#webdav_files_'+id_ntbr_config).html(distant_files_loading_error);
            }
		},"json"
	);
}

function downloadWebdavFile(id_ntbr_webdav, id_file, file_name, file_size)
{
    $('#loader_container').show();
    $('#result div').html('').hide();
    $('#loader_txt').html('<p>'+start_download+'</p>').show();

    $('html, body').animate({
        scrollTop: 0
    }, 1000);

    file_content = [];

	createWebdavBlobContent(id_ntbr_webdav, id_file, 0, file_name, file_size);
}

function createWebdavBlobContent(id_ntbr_webdav, id_file, pos, file_name, file_size)
{
    var xhr = new XMLHttpRequest();
    xhr.open('POST', admin_link_ntbr, true);
    xhr.responseType = 'blob';

    //Envoie les informations du header adaptées avec la requête
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onreadystatechange = function() { //Appelle une fonction au changement d'état.
        if (this.readyState === XMLHttpRequest.DONE) {
            if (this.status === 200) {
                var data = this.response;
                var data_size = data.size;
                pos += data_size;

                var blob = new Blob(
                    [data], {
                        type: "application/octet-stream"
                    }
                );

                file_content.push(blob);

                console.log(getReadableFileSizeString(pos)+'/'+getReadableFileSizeString(file_size));

                // Finished
                if (parseInt(pos) === parseInt(file_size)) {
                    $('#loader_container').hide();
                    $('#loader_txt').hide();
                    blobDownload(file_name, file_content);
                } else { // Not finished
                    // The size of the part is correct
                    if (parseInt(data_size) === max_file_download_size) {
                        $('#loader_txt').html('<p>'+getReadableFileSizeString(pos)+'/'+getReadableFileSizeString(file_size)+'</p>');
                        createWebdavBlobContent(id_ntbr_webdav, id_file, pos, file_name, file_size);
                    } else {
                        $('#loader_container').hide();
                        $('#loader_txt').hide();
                        $('#result .error.alert.alert-danger').html(error_download_distant_file).show();
                    }
                }
            } else {
                $('#loader_container').hide();
                $('#loader_txt').hide();
                $('#result .error.alert.alert-danger').html(error_download_distant_file).show();
            }
        }
    };

    xhr.send(
        'download_webdav_file=1'
        + '&id_ntbr_webdav='+id_ntbr_webdav
        + '&id_file='+id_file
        + '&file_size='+file_size
        + '&pos='+pos
    );
}

function deleteWebdavFile(id_ntbr_webdav, file_name, nb_part, id_in_list)
{
    if (confirm(confirm_delete_backup) == true) {
        $('#loader_container').show();
        $('#result div').html('').hide();

        $.post(
            admin_link_ntbr,
            'delete_webdav_file=1'
            + '&id_ntbr_webdav='+id_ntbr_webdav
            + '&nb_part='+nb_part
            + '&file_name='+encodeURIComponent(file_name),
            function(data)
            {
                if (data.result !== 1 && data.result !== '1') {
                    $('#result .error.alert.alert-danger').html('<p>' + delete_error + '</p>').show();
                } else {
                    $('#result .confirm.alert.alert-success').html('<p>' + delete_success + '</p>').show();

                    $('#webdav_backup'+id_in_list).remove();
                    $('#webdav_sub_backups'+id_in_list).remove();
                }

                $('#loader_container').hide();

                $('html, body').animate({
                    scrollTop: 0
                }, 1000);
            },"json"
        );
    }
}

function getFilesFtp()
{
    var id_ntbr_config = $('#choose_config').val();

    $('#ftp_files_'+id_ntbr_config).html('<img src="'+ajax_loader+'"/>');
    var id_ntbr_ftp = parseInt($('#id_ntbr_ftp_'+id_ntbr_config).val());

    $.post(
		admin_link_ntbr,
        'get_ftp_files=1'
        + '&id_ntbr_ftp='+id_ntbr_ftp,
		function(data)
		{
            if (data.files) {
                if (data.res) {
                    var backups_list = displayFtpBackupsList(data.files, id_ntbr_ftp);
                    $('#ftp_files_'+id_ntbr_config).html(backups_list);
                } else {
                    $('#ftp_files_'+id_ntbr_config).html(data.files);
                }
            } else {
                $('#ftp_files_'+id_ntbr_config).html(distant_files_loading_error);
            }
		},"json"
	);
}

function downloadFtpFile(id_ntbr_ftp, id_file, file_size)
{
    $('#loader_container').show();
    $('#result div').html('').hide();
    $('#loader_txt').html('<p>'+start_download+'</p>').show();

    $('html, body').animate({
        scrollTop: 0
    }, 1000);

    file_content = [];

	createFtpBlobContent(id_ntbr_ftp, id_file, 0, id_file, file_size);
}

function createFtpBlobContent(id_ntbr_ftp, id_file, pos, file_name, file_size)
{
    var xhr = new XMLHttpRequest();
    xhr.open('POST', admin_link_ntbr, true);
    xhr.responseType = 'blob';

    //Envoie les informations du header adaptées avec la requête
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onreadystatechange = function() { //Appelle une fonction au changement d'état.
        if (this.readyState === XMLHttpRequest.DONE) {
            if (this.status === 200) {
                var data = this.response;
                var data_size = data.size;
                pos += data_size;

                var blob = new Blob(
                    [data], {
                        type: "application/octet-stream"
                    }
                );

                file_content.push(blob);

                console.log(getReadableFileSizeString(pos)+'/'+getReadableFileSizeString(file_size));

                // Finished
                if (parseInt(pos) === parseInt(file_size)) {
                    $('#loader_container').hide();
                    $('#loader_txt').hide();
                    blobDownload(file_name, file_content);
                } else { // Not finished
                    // The size of the part is correct
                    if (parseInt(data_size) === max_file_download_size) {
                        $('#loader_txt').html('<p>'+getReadableFileSizeString(pos)+'/'+getReadableFileSizeString(file_size)+'</p>');
                        createFtpBlobContent(id_ntbr_ftp, id_file, pos, file_name, file_size);
                    } else {
                        console.log(parseInt(data_size));
                        console.log(max_file_download_size);
                        $('#loader_container').hide();
                        $('#loader_txt').hide();
                        $('#result .error.alert.alert-danger').html(error_download_distant_file).show();
                    }
                }
            } else {
                console.log(this.status);
                $('#loader_container').hide();
                $('#loader_txt').hide();
                $('#result .error.alert.alert-danger').html(error_download_distant_file).show();
            }
        }
    };

    xhr.send(
        'download_ftp_file=1'
        + '&id_ntbr_ftp='+id_ntbr_ftp
        + '&id_file='+id_file
        + '&file_size='+file_size
        + '&pos='+pos
    );
}

function deleteFtpFile(id_ntbr_ftp, file_name, nb_part, id_in_list)
{
    if (confirm(confirm_delete_backup) == true) {
        $('#loader_container').show();
        $('#result div').html('').hide();

        $.post(
            admin_link_ntbr,
            'delete_ftp_file=1'
            + '&id_ntbr_ftp='+id_ntbr_ftp
            + '&nb_part='+nb_part
            + '&file_name='+encodeURIComponent(file_name),
            function(data)
            {
                if (data.result !== 1 && data.result !== '1') {
                    $('#result .error.alert.alert-danger').html('<p>' + delete_error + '</p>').show();
                } else {
                    $('#result .confirm.alert.alert-success').html('<p>' + delete_success + '</p>').show();

                    $('#ftp_backup'+id_in_list).remove();
                    $('#ftp_sub_backups'+id_in_list).remove();
                }

                $('#loader_container').hide();

                $('html, body').animate({
                    scrollTop: 0
                }, 1000);
            },"json"
        );
    }
}

function initRestoreBackup(backup, type_backup)
{
    $('#loader_container').show();
    $('#result div').html('').hide();

	$.post(
		admin_link_ntbr,
		'restore_backup=1'
		+'&backup=' + backup
		+'&type_backup=' + type_backup,
		function( data )
		{
			if((data.result !== 1 && data.result !== '1') || !data.options || data.options == '' || !data.infos) {
				$('#result .error.alert.alert-danger').html('<p>' + restore_backup_error + '</p>').show();
			} else {
                restoreBackup(data.options, data.infos);
			}
		},"json"
	);
}

function restoreBackup(options_restore, infos)
{
    $('#loader_container').append('<p id="warning_restoration_running" class="alert error alert-danger">'+restore_backup_warning+'</p>');
    $('#loader_container').append('<p id="restoration_progress" class="alert alert-warning warn"></p>');
    $('#loader_container').show();
    $('#result div').html('').hide();
	$('#restore_progress').text('');

	$.get(
		link_restore_file,
		options_restore,
		function( data )
		{
            if (data) {
                clearInterval(progress_restore);
                if((data.result !== 1 && data.result !== '1')) {
                    endRestoreBackup(infos, 0);
                } else {
                    endRestoreBackup(infos, 1);
                }
            }
		},"json"
	);

    /* Call the function every 1s*/
	progress_restore = setInterval("displayProgressRestore()", 1000);
}

function endRestoreBackup(infos, success)
{
	$.post(
		admin_link_ntbr,
		'end_restore_backup=1'
		+'&backup_name=' + infos.backup_name
		+'&comment=' + infos.comment
		+'&safe=' + infos.safe
		+'&id_ntbr_config=' + infos.id_ntbr_config,
		function( data )
		{
            if((data.result !== 1 && data.result !== '1') || !success) {
                $('#result .alert.error.alert-danger').html('<p>' + restore_backup_error + '</p>').show();
            } else {
                $('#result .alert.confirm.alert-success').html('<p>' + restore_backup_success + '</p>').show();
            }

            $('#loader_container').hide();
            $('#warning_restoration_running').remove();
            $('#restoration_progress').remove();
		},"json"
	);
}

function addBackup(nb)
{
    $('#loader_container').show();
    $('#result div').html('').hide();

    var name        = $('#backup_upload_'+nb+' .backup_name').text();
    var id_config   = $('#upload_backup_config_'+nb).val();

	$.post(
		admin_link_ntbr,
		'add_backup=1'
		+'&backup=' + name
		+'&id_config=' + id_config,
		function( data )
		{
			if(data.result !== 1 && data.result !== '1') {
				$('#result .error.alert.alert-danger').html('<p>' + add_backup_error + '</p>').show();
			} else {
                $('#result .alert.confirm.alert-success').html('<p>' + add_backup_success + '</p>').show();

                location.reload();

                /*if (typeof data.backup_list !== 'undefined') {
                    var backups_list = displayBackupsList(data.backup_list);
                    $('#backup_files').html(backups_list);
                }

                $('#backup_upload_'+nb).remove();*/
			}

            $('#loader_container').hide();

            $('html, body').animate({
                scrollTop: 0
            }, 1000);
		},"json"
	);
}

function displayProgressRestore()
{
    $.get(restore_lastlog).done(function(data) {
        if (data) {
            data = data.trim();

            var first = data.substr(0, 5);

            if(first === ERROR5)
            {
                $('#restoration_progress').removeClass('alert-warning warn').addClass('error alert-danger');
            }
            if(first === FINISH5) {
                $('#restoration_progress').removeClass('alert-warning warn').addClass('confirm alert-success');
            }

            $('#restoration_progress').html(data);
        }
    });
}