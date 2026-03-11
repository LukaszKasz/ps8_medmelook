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
{if $time}
  {literal}
        var dateObj = new Date();
        var hours = dateObj.getHours();
        var mins = dateObj.getMinutes();
        var secs = dateObj.getSeconds();
        if (hours < 10) { hours = '0' + hours; }
        if (mins < 10) { mins = '0' + mins; }
        if (secs < 10) { secs = '0' + secs; }
        var time = ' ' + hours + ':' + mins + ':' + secs;
 {/literal}
{/if}
					  
  $(function() {
    $("#{Tools::htmlentitiesUTF8($class)}").datepicker({
      prevText: "",
      nextText: "",
      dateFormat: "yy-mm-dd" {if $time}+time{/if}
    });
  });
</script>


