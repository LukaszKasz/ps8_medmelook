/**
 * Facebook Conversion Pixel Tracking Plus
 *
 * NOTICE OF LICENSE
 *
 * @author    Pol Rué
 * @copyright Smart Modules 2014
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * @category Marketing & Advertising
 * Registered Trademark & Property of smart-modules.com
 *
 * ***************************************************
 * *     Facebook Conversion Trackcing Pixel Plus    *
 * *          http://www.smart-modules.com           *
 * ***************************************************
 */

if (typeof pp_prefix === 'undefined') {
    var pp_prefix = 'pp_';
}
document.addEventListener('DOMContentLoaded', function() {
    let c; // Interval itendifier
    /* Selected options in the GDPR */
    $(document).on('change', '#FCTP_BLOCK_SCRIPT_MODE', function() {
        showGDPROptions();
    });
    showGDPROptions();

    function showGDPROptions() {
        let b = '.fctp_block_';
        let val = $('#FCTP_BLOCK_SCRIPT_MODE').val();
        let options = ['cookies', 'local_storage'];
        for (option of options) {
            // console.log(val + ' VS '+ option);
            // console.log(b + option);
            if (val == option) {
                $(b + option).show();
            } else {
                $(b + option).hide();
            }
        }
    }

    /* Only switches */
    let advanced_modes = [{
            'selector': '#FCTP_BLOCK_SCRIPT',
            'target': '.fctp_cookies',
        },
        {
            'selector': '#FCTP_ADVANCED_MATCHING_OPTIONS',
            'target': '.amo_options',
        }
    ];

    /*for (switch of advanced_modes) {

    }*/
    /*showCookieOptions($('#FCTP_BLOCK_SCRIPT_on').prop('checked'));
    $(document).on('change', '#FCTP_BLOCK_SCRIPT_on, #FCTP_BLOCK_SCRIPT_off', function() {
        showCookieOptions(parseInt($(this).val()));
    });

    function showCookieOptions(show) {
        if (show) {
            console.log('show');
            $('.fctp_cookies').show();
        } else {
            console.log('hide');
            $('.fctp_cookies').hide();
        }
    }*/
    // Advanced modes
    var enable_disable_modes = {
        FCTP_COOKIE_RELOAD: '.fctp_cookie_reload_inverted',
    };

    function enableDisableOptions(disable, selector) {
        if (selector.indexOf('inverted') !== -1) {
            disable = !disable;
        }
        // console.log(selector + ': ' + disable);
        $(selector + ' input, ' + selector + ' select, ' + selector + ' button').prop('disabled', disable);
    }

    for (const selector in enable_disable_modes) {
        $(document).on('change', '#' + selector + '_on, #' + selector + '_off', function() {
            enableDisableOptions(!+$(this).val(), enable_disable_modes[selector]);
        });
        if ($('#' + selector + '_off').prop('checked')) {
            enableDisableOptions(true, enable_disable_modes[selector]);
        }
    }

    /* Show or hide subelements from the main option configuration (a switch) */
    var show_hide_modes = {
        FCTP_BLOCK_SCRIPT: '.fctp_cookies',
        FCTP_ADVANCED_MATCHING_OPTIONS : '.amo_options',
    };
    function showHideOptions(hide, selector) {
        if (hide) {
            $(selector).hide();
        } else {
            $(selector).show();
        }
    }

    for (const selector in show_hide_modes) {
        $(document).on('change', '#' + selector + '_on, #' + selector + '_off', function() {
            showHideOptions(!+$(this).val(), show_hide_modes[selector]);
            showGDPROptions();
        });
        if ($('#' + selector + '_off').prop('checked')) {
            showHideOptions(true, show_hide_modes[selector]);
        }
    }
    $(document).on('click', '.generate-cookies-token', function() {
        $.ajax({
            url: window.location.href,
            data: {
                ajax:1,
                action: 'generateToken',
                pp_token: ajax_token
            },
        }).done(function(data) {
            data = JSON.parse(data);
            if (data.success) {
                $('.print-front-cookies').html('Print Front Cookies' + ' (<span class="countdown" data-key="'+data.key+'" data-time="'+data.countdown+'">'+data.countdown+'s</span>)');
                // console.log(data);
                c = setInterval(() => {
                    data.countdown--;
                    $('.print-front-cookies .countdown').text(data.countdown + 's');
                    if (data.countdown == 0) {
                        clearIntervalAndClose();
                    }
                }, 1000);
            }
        })
    });
    function clearIntervalAndClose() {
        clearInterval(c);
        $('.print-front-cookies').removeAttr('data-time').empty();
    }
    $(document).on('click', '.print-front-cookies', function() {
        let data = $(this).find('.countdown').data();
        $.ajax({
            url: fctp_front_ajax_url,
            data: {
                ajax:1,
                action: 'viewCookies',
                pp_token: data.key
            },
        }).done(function(data) {
            if (typeof data === 'string') {
                data = JSON.parse(data);
            }
            // console.log(data);
            if (data.success) {
                $('#content').append('<dialog id="cookies_list">' +
                    '<div class="card">' +
                    '<div class="card-header"><h4>'+cookies_list+'</h4></div>' +
                    '<div class="card-body">'+
                    '<p>'+cookies_list_intro+'</p>'+
                    '<p>'+cookies_list_intro2+'</p>'+
                    '<div class="available_cookies_list"><table class="table"><thead><tr><th>'+cookie_name+'</th><th>'+cookie_value+'</th><th>'+cookie_actions+'</th></tr></thead><tbody></tbody></table></div>'+
                    '</div>' +
                    '<div class="card-footer"><button class="btn btn-secondary close-dialog" type="button">'+close_dialog+'</button> </div>' +
                    '</div>' +
                    '</dialog>');
                Object.keys(data.cookies_list).map(key => {
                    const value = data.cookies_list[key];
                    let newElement = $('<tr>' +
                        '<td><span class="cookie_name">' + key + '</span></td>' +
                        '<td><span class="cookie_value">' + value + '</span></td>' +
                        '<td><span class="set_configs badge badge-primary cookie-'+key+'">+ADD</span></td>' +
                        '</tr>').appendTo('.available_cookies_list tbody');
                    newElement.find('.cookie-'+key).data({cookieName: key, cookieValue: value});
                });
                showSuccessMessage(display_cookies_list);
                $('.available_cookies_list').show();
                setTimeout(() => {
                    $('#cookies_list')[0].showModal();
                }, 250)
                clearIntervalAndClose();
            } else {
                showErrorMessage(could_not_retrieve_cookies);
            }
        })
    });
    $(document).on('click', '.close-dialog', function() {
        closeDialog($(this));
    })
    $(document).on('click', '.set_configs', function() {
        let data = $(this).data();
        let name = '';
        let value = '';
        try {
            var parsedCookieValue = JSON.parse(data.cookieValue);
            $('.available_cookies_list').hide().after('<div id="selectionContainer"></div>');
            var listItems = '';
            for (var key in parsedCookieValue) {
                listItems += '<div class="list-item" data-key="' + key + '" data-value="' + parsedCookieValue[key] + '">' + key + ': ' + parsedCookieValue[key] + '</div>';
            }
            $('#selectionContainer').html('<h4 class="modal-text text-info">'+cookie_select_pair+'</h4>' + listItems);

            $('#selectionContainer').on('click', '.list-item', function() {
                var selectedKey = $(this).data('key');
                var selectedValue = $(this).data('value');
                // console.log('Selected pair:', selectedKey, selectedValue);
                // Assign the values and execute the function
                $('#FCTP_COOKIE_NAME').val(data.cookieName);
                $('#FCTP_COOKIE_VALUE').val(selectedKey+':"'+selectedValue+'"');
                highlightFieldsAndHideDialog();
            });

        } catch (error) {
            console.error('Error parsing cookieValue:', error);
            $('#FCTP_COOKIE_NAME').val(data.cookieName);
            $('#FCTP_COOKIE_VALUE').val(data.cookieValue);
            highlightFieldsAndHideDialog();
        }
    })
    function highlightFieldsAndHideDialog() {
        $('#FCTP_COOKIE_NAME').addClass('value-updated');
        $('#FCTP_COOKIE_VALUE').addClass('value-updated');

        setTimeout(() => {
            $('#FCTP_COOKIE_NAME').removeClass('value-updated');
            $('#FCTP_COOKIE_VALUE').removeClass('value-updated');
        }, 10000);
        showSuccessMessage(values_updated);
        closeDialog($('#cookies_list'));
    }
    function closeDialog(e) {
        let dialog = e.tagName === 'DIALOG' ? e : e.closest('dialog');
        // dialog.fade
        dialog.get(0).close();
        dialog.find('tbody').empty();
        $('#selectionContainer').remove(); // Remove the selectionContainer
        return false
    }
    // When the user clicks anywhere outside of the dialog, close it
    $(document).on('click', 'dialog', function(event) {
        // Check if the click is outside the dialog
        // console.log(event.target);
        if (!$(event.target).closest('.card').length) {
            closeDialog($('#cookies_list'));
        }
    });

    stickyMenu();

    /* Sticky menu */
    window.addEventListener('resize', function() {
        stickyMenu();
    });

    function stickyMenu() {
        if ($('#module-nav .list-group').length === 0) {
            setTimeout(stickyMenu, 200);
            return;
        }
        let nav = $('#module-nav .list-group');
        let availableHeight = window.innerHeight - nav.offset().top - 25;

        if (nav.height() >= availableHeight) {
            $('#module-body').css('display', 'block');
            nav.removeClass('sticky-menu');
        } else {
            $('#module-body').css('display', 'flex');
            nav.addClass('sticky-menu');
        }
    }
});

/* ADD IP to Logger */
function addRemoteAddr(input_name) {
    var length = $('input[name='+input_name+']').attr('value').length;
    if (length > 0) {
        if ($('input[name='+input_name+']').attr('value').indexOf(remoteAddr) < 0) {
            $('input[name='+input_name+']').attr('value',$('input[name='+input_name+']').attr('value') + ',' + remoteAddr);
        }
    } else {
        $('input[name='+input_name+']').attr('value', remoteAddr);
    }
}