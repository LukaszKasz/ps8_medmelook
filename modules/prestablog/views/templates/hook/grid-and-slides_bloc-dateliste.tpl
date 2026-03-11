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
<div class="block-categories prestablog" id="prestablog_archives">
	<h4 class="title_block">{l s='Blog archives' d='Modules.Prestablog.Grid'}</h4>
	<div class="block_content" id="prestablog_dateliste">
		{if $ResultDateListe}
			<ul>
			{foreach from=$ResultDateListe key=KeyAnnee item=ValueAnnee name=loopAnnee}
				<li>
					<a href="#" class="prestablog_annee link_block" {if count($ResultDateListe)<=1}style="display:none;"{/if}>{$KeyAnnee|escape:'htmlall':'UTF-8'}&nbsp;<span>({$ValueAnnee.nombre_news|intval})</span></a>
					<ul class="prestablog_mois {if (isset($prestablog_annee) && $prestablog_annee==$KeyAnnee)}prestablog_show{/if}">
				{foreach from=$ValueAnnee.mois key=KeyMois item=ValueMois name=loopMois}
						<li>
							<a href="{PrestaBlogUrl y=$KeyAnnee m=$KeyMois}" class="link_block">{$ValueMois.mois_value|escape:'htmlall':'UTF-8'}&nbsp;<span>({$ValueMois.nombre_news|intval})</span></a>
						</li>
				{/foreach}
					</ul>
				</li>
			{/foreach}
			</ul>
		{else}
			<p>{l s='No news' d='Modules.Prestablog.Grid'}</p>
		{/if}
		{if $prestablog_config.prestablog_datenews_showall}<a href="{PrestaBlogUrl}" class="btn-primary btn_link">{l s='See all' d='Modules.Prestablog.Grid'}</a>{/if}
	</div>
</div>
<!-- /Module Presta Blog -->
