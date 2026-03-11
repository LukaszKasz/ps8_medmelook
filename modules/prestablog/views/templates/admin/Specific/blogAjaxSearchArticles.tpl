{*
 * 2008 - 2024 (c) Prestablog
 *
 * MODULE PrestaBlog
 *
 * @author    Prestablog
 * @copyright Copyright (c) permanent, Prestablog
 * @license   Commercial
 *}
{if count($result_search) > 0}
    <table>
        <thead>
            <tr>
                <th>Action</th>
                <th>ID</th>
                <th>Thumbnail</th>
                <th>Title</th>
            </tr>
        </thead>
        <tbody>
            {foreach from=$result_search item=value}
                {$article_search = new NewsClass((int) $value['id_prestablog_news'], $current_lang)}

                {if file_exists(PrestaBlog::imgUpPath()|cat:'/adminth_'|cat:$article_search->id|cat:'.jpg')}
                    {$imgA = PrestaBlog::imgPathFO()|cat:PrestaBlog::getT()|cat:'/up-img/'|cat:'adminth_'|cat:$article_search->id|cat:'.jpg?'|cat:md5(time())}
                    {$thumbnail = '<img class="imgm img-thumbnail" src="'|cat:$imgA|cat:'" />'}
                {else}
                    {$thumbnail = '-'}
                {/if}

                <tr class="Outlisted noOutlisted_{$article_search->id}">
                    <td class="{if $article_search->actif} {else}noactif{/if} center">
                        <img src="../modules/prestablog/views/img/linked.png" rel="{$article_search->id}" class="linked" />
                    </td>
                    <td class="{if $article_search->actif} {else}noactif{/if} center">{$article_search->id}</td>
                    <td class="{if $article_search->actif} {else}noactif{/if} center" style="width:50px;">{$thumbnail}</td>
                    <td class="{if $article_search->actif} {else}noactif{/if}">{$article_search->title}</td>
                </tr>
            {/foreach}

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

            {assign var='jsAppend' value='$("#currentArticleLink").append(\'<input type="text" name="articlesLink[]"\'+
                \' value="\'+idN+\'" class="linked_\'+idN+\'" />\');'}

            <script type="text/javascript">
                $("span#prestablog-prev-search").click(function() {
                    ReloadLinkedSearchArticles({$start - $pas});
                });
                $("span#prestablog-next-search").click(function() {
                    ReloadLinkedSearchArticles({$start + $pas});
                });
                $("#articleLinkResult img.linked").click(function() {
                    var idN = $(this).attr("rel");
                    {$jsAppend}
                    $("#articleLinkResult .noOutlisted_"+idN).remove();
                    ReloadLinkedArticles();
                    ReloadLinkedSearchArticles();
                });
            </script>
        </tbody>
    </table>
{else}
    <div class="warning">
        <p class="center">
            {$prestablog->message_call_back['no_result_found']}
        </p>
    </div>
{/if}
