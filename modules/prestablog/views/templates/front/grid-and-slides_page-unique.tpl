{*
   * 2008 - 2024 (c) Prestablog
   *
   * MODULE PrestaBlog
   *
   * @author    Prestablog
   * @copyright Copyright (c) permanent, Prestablog
   * @license   Commercial
     *}
{if isset($news)}
  <div id="prestablogfont" itemprop="articleBody">{PrestaBlogContent return=$news->content|escape:'html':'UTF-8'}</div>
{/if}
