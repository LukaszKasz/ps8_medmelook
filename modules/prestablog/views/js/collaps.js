/**
 * 2008 - 2024 (c) Prestablog
 *
 * MODULE PrestaBlog
 *
 * @author    Prestablog
 * @copyright Copyright (c) permanent, Prestablog
 * @license   Commercial
 * @version    3.7.5

 */
/* Collaps Icon script */
$(document).ready(function() {
    $('.blog-collapse-icons').click(function() {
        var $this = $(this);
        var $contcatblockblogDiv = $this.closest('li').find('.blog-collapse-icons');
        var $blogItems = $this.closest('li').find('.blogitems');

        // Basculez les classes sur la div .contcatblockblog
        $contcatblockblogDiv.toggleClass('blogcollapsed');

        // Utiliser l'effet 'slide' pour montrer ou cacher les éléments .blogitems
        $blogItems.toggle('Fade');
    });
});
