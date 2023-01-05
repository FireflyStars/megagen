{*
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
*}

<ul class="tree">
    {foreach $directories as $directory}
        <li class="tree-folder">
            <span class="tree-folder-name {if $directory.ignore || $directory.always_ignore}tree-selected{/if}">
                <input type="checkbox" name="ignore_directories_{$id_ntbr_config|intval}" value="{$directory.path|escape:'html':'UTF-8'}"
                    {if $directory.ignore || $directory.always_ignore}checked="checked"{/if}
                    {if !$directory.always_ignore}onclick="$(this).parent().toggleClass('tree-selected');"{else}onclick="$(this).attr('checked', 'checked').prop('checked', true);"{/if}
                    {if $directory.always_ignore}class="deactivate" readonly="readonly" disabled="disabled"{/if}/>
                <span onclick="getDirectoryChildren('{$directory.path|escape:'html':'UTF-8'}', this);">
                    <i class="fas fa-folder"></i> <label class="tree-toggler">{$directory.name|escape:'html':'UTF-8'}</label>
                </span>
            </span>
        </li>
    {/foreach}
</ul>