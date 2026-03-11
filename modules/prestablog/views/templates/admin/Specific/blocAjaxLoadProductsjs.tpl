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
    $("#productLinked img.delinked").click(function() {
        var idP = $(this).attr("rel");
        $("#currentProductLink input.linked_"+idP).remove();
        $("#productLinked .noInlisted_"+idP).remove();
        ReloadLinkedProducts();
        ReloadLinkedSearchProducts();
    });
</script>
