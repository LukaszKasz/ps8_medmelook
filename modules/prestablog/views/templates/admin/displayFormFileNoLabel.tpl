{*
 * 2008 - 2024 (c) Prestablog
 *
 * MODULE PrestaBlog
 *
 * @author    Prestablog
 * @copyright Copyright (c) permanent, Prestablog
 * @license   Commercial
 *}
<input id="{$name_item}" type="file" name="{$name_item}" class="hide" />
<div class="dummyfile input-group {$size_bootstrap}" >
<span class="input-group-addon"><i class="icon-file"></i></span>
<input id="{$name_item}-name" type="text" class="disabled" name="filename" readonly />
<span class="input-group-btn">
<button
id="{$name_item}-selectbutton"
type="button"
name="submitAddAttachments"
class="btn btn-default"
>
<i class="icon-folder-open"></i>{l s='Choose a file' d='Modules.Prestablog.Prestablog'}
</button>
</span>
</div>
{if $help}
  <p class="help-block">{$help}</p>
{/if}
<script>
$(document).ready(function(){
  $('#{$name_item}-selectbutton').click(function(e) {
    $('#{$name_item}').trigger('click');
    });

    $('#{$name_item}-name').click(function(e) {
      $('#{$name_item}').trigger('click');
      });

      $('#{$name_item}-name').on('dragenter', function(e) {
        e.stopPropagation();
        e.preventDefault();
        });

        $('#{$name_item}-name').on('dragover', function(e) {
          e.stopPropagation();
          e.preventDefault();
          });

          $('#{$name_item}-name').on('drop', function(e) {
            e.preventDefault();
            var files = e.originalEvent.dataTransfer.files;
            $('#{$name_item}')[0].files = files;
            $(this).val(files[0].name);
            });

            $('#{$name_item}').change(function(e) {
              if ($(this)[0].files !== undefined)
              {
                var files = $(this)[0].files;
                var name  = '';

                $.each(files, function(index, value) {
                  name += value.name+', ';
                  });

                  $('#{$name_item}-name').val(name.slice(0, -2));
                  } else // Internet Explorer 9 Compatibility
                  {
                    var name = $(this).val().split(/[\\/]/);
                    $('#{$name_item}-name').val(name[name.length-1]);
                  }
                  });
                  });
</script>