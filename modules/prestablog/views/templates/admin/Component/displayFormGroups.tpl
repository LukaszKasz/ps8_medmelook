{*
 * 2008 - 2024 (c) Prestablog
 *
 * MODULE PrestaBlog
 *
 * @author    Prestablog
 * @copyright Copyright (c) permanent, Prestablog
 * @license   Commercial
 *}
<div class="blocmodule">
    <table cellspacing="0" cellpadding="0" class="table">
        <thead>
            <tr>
                <th style="width:20px;"><input type="checkbox" name="checkme" class="noborder"
                        onclick="checkDelBoxes(this.form, 'groupBox[]', this.checked)" /></th>
                <th style="width:20px;">{l s='ID' d='prestablog'}</th>
                <th>{l s='Group name' d='prestablog'}</th>
            </tr>
        </thead>
        <tbody>
            {foreach $groups as $key => $group}
                <tr>
                    <td>
                        <input type="checkbox" name="groupBox[]" class="groupBox" id="groupBox_{(int) $group['id_group']}"
                               value="{(int) $group['id_group']}" 
                               {if $active_group|@count > 0}
                                   {if in_array((int) $group['id_group'], $active_group)}checked="checked"{/if}
                               {elseif $key == 0}checked="checked"{/if}>
                    </td>
                    <td>{(int) $group['id_group']}</td>
                    <td>
                        <label for="groupBox_{(int) $group['id_group']}">{$group['name']}</label>
                    </td>
                </tr>
            {/foreach}
        </tbody>
    </table>
</div>
