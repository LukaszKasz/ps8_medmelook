{*
 * 2008 - 2024 (c) Prestablog
 *
 * MODULE PrestaBlog
 *
 * @author    Prestablog
 * @copyright Copyright (c) permanent, Prestablog
 * @license   Commercial
 *}
<tr class="prestablog-footer-search">
    <td colspan="4">
        {$prestablog->message_call_back['total_results']} : {$count_search['value']}
        {if $end < (int) $count_search['value']}
            <span id="prestablog-next-search" class="prestablog-search">
                {$prestablog->message_call_back['next_results']}
                <img src="../modules/prestablog/views/img/list-next2.gif" />
            </span>
        {/if}
        {if $start > 0}
            <span id="prestablog-prev-search" class="prestablog-search">
                <img src="../modules/prestablog/views/img/list-prev2.gif" />
                {$prestablog->message_call_back['prev_results']}
            </span>
        {/if}
    </td>
</tr>

{assign var='jsAppend' value='$("#currentProductLink").append(\'<input type="text" name="productsLink[]"\'+
    \' value="\'+idP+\'" class="linked_\'+idP+\'" />\');'}

<script type="text/javascript">
    $("span#prestablog-prev-search").click(function() {
        ReloadLinkedSearchProducts({$start - $pas});
    });
    $("span#prestablog-next-search").click(function() {
        ReloadLinkedSearchProducts({$start + $pas});
    });
    $("#productLinkResult img.linked").click(function() {
        var idP = $(this).attr("rel");
        {$jsAppend}
        $("#productLinkResult .noOutlisted_"+idP).remove();
        ReloadLinkedProducts();
        ReloadLinkedSearchProducts();
    });
</script>
