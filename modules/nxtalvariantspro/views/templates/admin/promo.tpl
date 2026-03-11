{**
 * Product Variants Pro
 *
 * @author    Nxtal <support@nxtal.com>
 * @copyright Nxtal 2023
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 * @version   1.4.0
 *
 *}

<style>
#nxtal-promo .logo-link{
	display: block;
    height: 134px;
}
#nxtal-promo .top-space{
	display: inline-block;
    height: 100%;
    vertical-align: middle;	
}
#nxtal-promo .nxtal-logo{
	width: 150px;
	vertical-align: middle;
}
#nxtal-promo .module-logo{
	text-align:center;
}
#nxtal-promo .module-desc{
	margin-top: 10px;
    text-align: center;
}
#nxtal-promo .module-name{
	min-height: 35px;
	display: block;
}
#nxtal-promo .stars {
    font-family: "Material Icons";
    position: relative;
    top: 2px;
    display: inline-block;
    font-size: 1.2em;
	margin-bottom: 10px;
}
#nxtal-promo .stars:before {
    content: "\E838\E838\E838\E838\E838";
    color: #b8b8b8;
}
#nxtal-promo .stars:after {
    color: #ffa400;
    position: absolute;
    left: 0;
    top: 0;
}
#nxtal-promo .stars.stars-5:after, #nxtal-promo .stars.stars-50:after {
    content: "\E838\E838\E838\E838\E838";
}
#nxtal-promo .stars.stars-45:after {
    content: "\E838\E838\E838\E838\E839";
}
#nxtal-promo .stars.stars-4:after, #nxtal-promo .stars.stars-40:after {
    content: "\E838\E838\E838\E838";
}
#nxtal-promo .stars.stars-35:after {
    content: "\E838\E838\E838\E839";
}
#nxtal-promo .stars.stars-3:after, #nxtal-promo .stars.stars-30:after {
    content: "\E838\E838\E838";
}
#nxtal-promo .stars.stars-25:after {
    content: "\E838\E838\E839";
}
#nxtal-promo .stars.stars-2:after, #nxtal-promo .stars.stars-20:after {
    content: "\E838\E838";
}
#nxtal-promo .stars.stars-15:after {
    content: "\E838\E839";
}
#nxtal-promo .stars.stars-1:after, #nxtal-promo .stars.stars-10:after {
    content: "\E838";
}
#nxtal-promo .demo-button {
    padding: 5px;
    border: 1px solid;
    border-radius: 4px;
    width: 100px;
    display: block;
    text-align: center;
    margin: 0 auto;
}
#nxtal-promo .panel-footer{
	height: 42px;
}
</style>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<div class="panel" id="nxtal-promo">
    <div class="row">
		{*<div class="col-lg-2">
            <a href="https://addons.prestashop.com/en/2_community-developer?contributor=1126640" title="Nxtal Modules" target="_blank" class="logo-link">
				<span class="top-space"></span>
                <img class="nxtal-logo" src="https://addons.prestashop.com/themes/prestastore/img/sellers/logos/610b876e4d141.jpg">
            </a>
        </div>*}
		{foreach from=$modules item="module"}
        <div class="col-lg-2">
			<div class="row">
				<div class="module-logo">
					<a href="{$module.link nofilter}" target="_blank">
						<img src="{$module.image|escape:'htmlall':'UTF-8'}">
					</a>
				</div>
				<div class="module-desc">
					<a href="{$module.link nofilter}" target="_blank" class="module-name">
						{$module.name nofilter}							
					</a>
					<span class="stars-block">
						<div class="stars stars-{$module.rating|escape:'htmlall':'UTF-8'}"></div>
					</span>
					<a class="demo-button" href="{$module.demo|escape:'htmlall':'UTF-8'}" target="_blank">{l s='Demo' mod='nxtalvariantspro'}</a>
				</div>
			</div>
		</div>
		{/foreach}
    </div>			
    <div class="panel-footer">
        <div class="col-md-3 col-sm-6 text-center">
            <a href="https://addons.prestashop.com/en/ratings.php" target="_blank">
				<i class="icon-star"></i> {l s='Rate Us' mod='nxtalvariantspro'}
			</a>
        </div>
        <div class="col-md-3 col-sm-6 text-center">
            <a href="../modules/nxtalvariantspro/readme_en.pdf" target="_blank">
				<i class="icon-book"></i> {l s='Document' mod='nxtalvariantspro'}
			</a>
        </div>
        <div class="col-md-3 col-sm-6 text-center">
            <a href="https://addons.prestashop.com/en/contact-us" target="_blank">
				<i class="icon-comments"></i> {l s='Support' mod='nxtalvariantspro'}
			</a>
        </div>		
        <div class="col-md-3 col-sm-6 text-center">
            <a href="https://addons.prestashop.com/en/2_community-developer?contributor=1126640" target="_blank">
				<i class="icon-eye-open"></i> {l s='See other modules' mod='nxtalvariantspro'}
			</a>
        </div>
    </div>
</div>