{*
 * 2008 - 2024 (c) Prestablog
 *
 * MODULE PrestaBlog
 *
 * @author    Prestablog
 * @copyright Copyright (c) permanent, Prestablog
 * @license   Commercial
 *}
{* langues start *}

{literal}
    <script language="javascript" type="text/javascript">
    $(document).ready(function() {
      $(".catlang").hide();
      $(".catlang[rel="+id_language+"]").show();

      $("div.language_flags img, #check_lang_prestablog img").click(function() {
        $(".catlang").hide();
        $(".catlang[rel="+id_language+"]").show();
        $("#imgCatLang").attr("src", "../img/l/" + id_language + ".jpg");
    });
{/literal}

    {foreach $liste_cat_branches_actives $value}
      $("tr#prestablog_categorie_{$value}").show();
    {/foreach}

    {foreach $liste_cat_no_arbre $value}
      {if in_array((int) $value['parent'], $liste_cat_branches_actives)}
        $("tr#prestablog_categorie_{$value['id_prestablog_categorie']}").show();
      {/if}
    {/foreach}

{literal}
    $("img.expand-cat").click(function() {
      BranchClick=$(this).attr("rel");
      BranchClickSplit = BranchClick.split('.');
      fixBranchClickSplit = "0,"+BranchClickSplit.toString();
      action = $(this).data("action");
      path = $(this).data("path");

      switch (action) {
        case "expand":
        $("tr.prestablog_branch").each(function() {
          BranchParent = $(this).attr("rel");
          BranchParentSplit = BranchParent.split('.');
          fixBranchParentSplit = "0,"+BranchParentSplit.toString();

          if ($.isSubstring(fixBranchParentSplit, fixBranchClickSplit)
          && BranchClick != BranchParent
          && BranchClickSplit.length+1 == BranchParentSplit.length
          ) {
            $(this).show();
          }
          });
          $(this).attr("src", path.concat("collapse.gif"));
          $(this).data("action", "collapse");
          break;

          case "collapse":
          $("tr.prestablog_branch").each(function() {
            BranchParent = $(this).attr("rel");
            BranchParentSplit = BranchParent.split('.');
            fixBranchParentSplit = "0,"+BranchParentSplit.toString();

            if ($.isSubstring(fixBranchParentSplit, fixBranchClickSplit)
            && BranchClick != BranchParent
            ) {
              $(this).hide();
              $(this).find("img.expand-cat").each(function() {
                $(this).attr("src", path.concat("expand.gif"));
                $(this).data("action", "expand");
                });
              }
              });
              $(this).attr("src", path.concat("expand.gif"));
              $(this).data("action", "expand");
              break;
            }
            });
            });
            jQuery.isSubstring = function(haystack, needle) {
             return haystack.indexOf(needle) !== -1;
           };
           </script>
{/literal}
