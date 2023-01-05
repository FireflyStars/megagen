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

<ul class="sugarsync_tree_child">
    {foreach $children as $child}
        <li class="level-{$level|intval}">
            <span>
                <input type="radio" class="sugarsync_dir" name="sugarsync_dir_{$config_id|intval}" value="{$child.id|escape:'html':'UTF-8'}"
                    {if $sugarsync_dir == $child.id}checked="checked"{/if} id="{$child.id|escape:'html':'UTF-8'}_{$config_id|intval}"/>
                <input type="hidden" name="sugarsync_path_{$config_id|intval}" value="{$parent_path|escape:'html':'UTF-8'}/{$child.name|escape:'html':'UTF-8'}"/>
                <label for="{$child.id|escape:'html':'UTF-8'}_{$config_id|intval}">{$child.name|escape:'html':'UTF-8'}</label>
                <i class="far fa-plus-square" onclick="getSugarsyncTreeChildren(
                    '{$child.id|escape:'html':'UTF-8'}', '{$sugarsync_dir|escape:'html':'UTF-8'}', '{$level|intval}',
                    '{$parent_path|escape:'html':'UTF-8'}/{$child.name|escape:'html':'UTF-8'}', this
                );">
                </i>
            </span>
        </li>
    {/foreach}
</ul>