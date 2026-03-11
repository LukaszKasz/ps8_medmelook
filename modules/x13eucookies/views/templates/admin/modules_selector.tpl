{if $is_required_category}
<div class="alert alert-info">
{l s='You cannot block any modules for the required category.' mod='x13eucookies'}
</div>
{else}
<script type="text/javascript">
document.addEventListener("DOMContentLoaded", function(event) {
    let moduleCards = document.querySelectorAll('.x13eucookies-module')
    function liveSearch() {
        let search_query = document.getElementById("module_search").value;

        //Use innerText if all contents are visible
        //Use textContent for including hidden elements
        for (var i = 0; i < moduleCards.length; i++) {
            if(moduleCards[i].textContent.toLowerCase()
                    .includes(search_query.toLowerCase())) {
                moduleCards[i].classList.remove("hide");
            } else {
                moduleCards[i].classList.add("hide");
            }
        }
    }

    //A little delay
    let typingTimer;
    let typeInterval = 500;
    let moduleSearchInput = document.getElementById('module_search');

    moduleSearchInput.addEventListener('keyup', () => {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(liveSearch, typeInterval);
    });
    moduleSearchInput.addEventListener('keydown', function(event) {
      if (event.keyCode === 13) {
        event.preventDefault();
      }
    });
});
</script>
<style>
{literal}
.x13eucookies-module-wrapper {
    position: relative;
    height: 100%;
}

.x13eucookies-module-wrapper .x13eucookies-module-custom-checkbox {
    width: 13px;
    height: 13px;
    flex: 0 0 13px;
    background-color: #fff;
    border: 1px solid #767676;
    border-radius: 3px;
}

.x13eucookies-module-wrapper .x13eucookies-module-custom-checkbox::after {
    position: relative;
    top: -1px;
    left: -1px;
    display: block;
    width: 13px;
    height: 13px;
    content: "";
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' height='48' viewBox='0 96 960 960' width='48'%3E%3Cpath d='M378 834 130 586l68-68 180 180 383-383 68 68-451 451Z' fill='%23fff'/%3E%3C/svg%3E");    background-size: contain;
    opacity: 0;
}

.x13eucookies-module-wrapper .x13eucookies-module-checkbox {
    position: absolute;
    top: 0;
    left: 0;
    opacity: 0;
    visibility: hidden;
}

.x13eucookies-module-wrapper .x13eucookies-module-label {
    display: flex;
    width: 100%;
    height: 100%;
    align-items: center;
    padding: 5px 10px;
    border: 1px solid #dfdfdf;
    -webkit-border-radius: 4px;
    -moz-border-radius: 4px;
    border-radius: 4px;
    height: 100%;
    margin-bottom:10px;
    cursor: pointer;
}

.x13eucookies-module-label:hover{
    background: #fcfdfe;
}

.x13eucookies-module-label .x13eucookies-module-img {
    width: 32px;
    height: 32px;
    flex: 0 0 32px;
    margin: 0 10px;
    overflow: hidden;
    margin-top: 5px;
}
.x13eucookies-module-label .x13eucookies-module-img img{
    max-width: 32px;
    height: auto;
}

.x13eucookies-module-label .x13eucookies-module-name {
    font-weight: normal;
}

.x13eucookies-module-checkbox:checked + .x13eucookies-module-label {
    background: #f4f8fb;
    border-color: #a3a3a3;
}

.x13eucookies-module-checkbox:checked + .x13eucookies-module-label .x13eucookies-module-custom-checkbox {
    background-color: #0075ff;
    border-color: #0075ff;
}

.x13eucookies-module-checkbox:checked + .x13eucookies-module-label .x13eucookies-module-custom-checkbox::after {
    opacity: 1;
}

.x13eucookies-modules-list > .row {
    margin: 0 -5px;
    display: flex;
    flex-wrap: wrap;
}
.x13eucookies-modules-list .x13eucookies-module {
    padding: 0 5px;
    margin-bottom: 10px;
    flex: 0 0 33.33333%;
    max-width: 33.33333%;
}

@media (min-width: 1200px) and (max-width: 1519.98px) {
    .x13eucookies-modules-list .x13eucookies-module {
        flex: 0 0 50%;
        max-width: 50%;
    }
}

@media (max-width: 991.98px) {
    .x13eucookies-modules-list .x13eucookies-module {
        flex: 0 0 50%;
        max-width: 50%;
    }
}

@media (max-width: 767.98px) {
    .x13eucookies-modules-list .x13eucookies-module {
        flex: 0 0 100%;
        max-width: 100%;
    }
}
{/literal}
</style>

<div class="alert alert-info">
{l s='The modules you select below will not be displayed if the user does not consent to cookies from this category.' mod='x13eucookies'}
</div>
<div class="x13eucookies-modules-list">
    <div class="form-group">
        <div class="col-xs-12">
            <label for="module_search">{l s='Filter module list' mod='x13eucookies'}</label>
            <input type="text" class="form-control" id="module_search" placeholder="{l s='Type to filter...' mod='x13eucookies'}">
        </div>
    </div>
    <div class="row">
    {foreach $modules as $module}
        <div class="x13eucookies-module">
            <div class="x13eucookies-module-wrapper">
                <input
                    id="x13eucookies-chekcbox-{$module->name}"
                    class="x13eucookies-module-checkbox"
                    type="checkbox"
                    name="blocked_modules[]"
                    value="{$module->name}"
                    {if $module->checked == 1}checked{/if}
                />
                <label for="x13eucookies-chekcbox-{$module->name}" class="x13eucookies-module-label">
                    {* todo: we should probably search for the logo in the back-end first, have a placeholder for modules without them *}
                    <span class="x13eucookies-module-custom-checkbox"></span>
                    <span class="x13eucookies-module-img"><img src="../modules/{$module->name}/logo.png"
                    alt="{$module->displayName}"/></span>
                    <span class="x13eucookies-module-name"><strong>{$module->displayName}</strong><br/> ({$module->name})</span>
                </label>
            </div>
        </div>
    {/foreach}
    </div>
</div>
{/if}