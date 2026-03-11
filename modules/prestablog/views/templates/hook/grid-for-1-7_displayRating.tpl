{*
 * 2008 - 2024 (c) Prestablog
 *
 * MODULE PrestaBlog
 *
 * @author    Prestablog
 * @copyright Copyright (c) permanent, Prestablog
 * @license   Commercial
 *}
<!-- Module Presta Blog -->
<div id="prestablog-rating">
{if ($isLogged)}
	{if ({$validate}) && ({$validate} == 'true')}
		<form action="{$LinkReal|escape:'html':'UTF-8'}&id={$news->id|intval}" method="post" class="rating">
 	<input type="radio" id="star5" name="rate" value="5" /><label class = "material-icons" for="star5" title="5 stars">star</label>
    <input type="radio" id="star4" name="rate" value="4" /><label class = "material-icons" for="star4" title="4 stars">star</label>
    <input type="radio" id="star3" name="rate" value="3" /><label class = "material-icons" for="star3" title="3 stars">star</label>
    <input type="radio" id="star2" name="rate" value="2" /><label class = "material-icons" for="star2" title="2 stars">star</label>
    <input type="radio" id="star1" name="rate" value="1" /><label class = "material-icons" for="star1" title="1 star">star</label>
				<p class="submit">
					<input type="submit" class="btn-primary" name="submitRating" id="submitRating" value="{l s='Rate the article' d='Modules.Prestablog.Grid'}" />
				</p>
</form>
{else}
<div id="prestablogfont">
  <p>
{l s='You have already rated this article' d='Modules.Prestablog.Grid'}
</p>
</div>
{/if}
{else}
<div id="prestablogfont">
  <p>
{l s='Please log in to rate this article' d='Modules.Prestablog.Grid'}
</p>
</div>
{/if}
</div>
  <div class="clearfix" style="margin-bottom:30px;"></div>

<!-- /Module Presta Blog -->
