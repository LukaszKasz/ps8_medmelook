{**
* ColorFeatures
*
* @category  Module
* @author    silbersaiten <info@silbersaiten.de>
* @support   silbersaiten <support@silbersaiten.de>
* @copyright 2019 silbersaiten
* @version   1.0.1
* @link      http://www.silbersaiten.de
* @license   See joined file licence.txt
*}

<script type="application/javascript">
    var af_feature_names = "{$feature_names}";
    function containsText(selector, text) {
        var elements = document.querySelectorAll(selector);
        return Array.prototype.filter.call(elements, function(element){
            return RegExp(text).test(element.textContent);
        });
    }
    function findFacetedBlocks(){
        if(!af_feature_names.length) return false;

        af_feature_names = af_feature_names.split(",");

        for(var i=0; i<af_feature_names.length; i++){
            containsText('p.facet-title', af_feature_names[i])[0].parentNode.classList.add("af_facet_features");
        }
    }
    document.addEventListener("DOMContentLoaded", findFacetedBlocks);
</script>
