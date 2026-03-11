{*
 * 2008 - 2024 (c) Prestablog
 *
 * MODULE PrestaBlog
 *
 * @author    Prestablog
 * @copyright Copyright (c) permanent, Prestablog
 * @license   Commercial
 *}
<script type="text/javascript">
    $("#articleLinked img.delinked").click(function() {
        var idN = $(this).attr("rel");
        $("#currentArticleLink input.linked_"+idN).remove();
        $("#articleLinked .noInlisted_"+idN).remove();
        ReloadLinkedArticles();
        ReloadLinkedSearchArticles();
    });
</script>
