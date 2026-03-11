{*
 * 2008 - 2024 (c) Prestablog
 *
 * MODULE PrestaBlog
 *
 * @author    Prestablog
 * @copyright Copyright (c) permanent, Prestablog
 * @license   Commercial
 *}
{assign "ret" "" }  

{$prestablog->get_displayFormOpen('icon-edit', $legend_title, $confpath)}
{$prestablog->get_displayFormSelectAuthor('col-lg-2', $addauthor_text,'employees','',$employees, null ,'col-lg-5')}

            <button class="btn btn-primary" name="submitAddAuthor" type="submit">
                <i class="icon-plus"></i>&nbsp; {l s='Add the author' d='Modules.Prestablog.Prestablog'}
            </button>
        </form>
    </fieldset>
</div>