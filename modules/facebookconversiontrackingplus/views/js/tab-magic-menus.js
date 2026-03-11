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

// window.addEventListener('beforeunload', (e) => {
//     e.preventDefault();
//     function setCookie(cname, cvalue, exp) {
//         const d = new Date();
//         d.setTime(d.getTime() + (exp*1000));
//         let expires = "expires="+ d.toUTCString();
//         document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
//     }
//     let url = $('#module-nav a.active').prop('href');
//     setCookie(pp_prefix+'last_menu', url.match('#([0-9a-zA-Z-_]*)')[1], 25);
//     return true;
// });

window.onbeforeunload = function() {
    let url = $('#module-nav a.active').prop('href');
    const d = new Date();
    const exp = 25;
    d.setTime(d.getTime() + (exp*1000));
    let expires = "expires="+ d.toUTCString();
    document.cookie = pp_prefix+'last_menu' + "=" + url.match('#([0-9a-zA-Z-_]*)')[1] + ";" + expires + ";path=/";
    return;
}

$(document).ready(function() {
    // Create the Left menú
    let panels = '';
    let sel = '';
    let head_sel = '';
    let item_sel = '';
    let active_class = '';

    function removeTrailingNumber(id) {
        return id.replace(/_\d+$/, '');
    }
    setTimeout(function() {
        if ($('#module-body .panel-heading').length > 0) {
            head_sel = '.panel-heading';
            sel = '.panel';
            item_sel = 'div';
            active_class = 'active';
            panels = $('#module-body .panel').filter(function () {
                //console.log($(this).parents('.panel').length == 0);
                return $(this).parents('.panel').length == 0;
            });
        } else if ($('fieldset legend').length > 0) {
            head_sel = 'legend';
            sel = 'fieldset';
            item_sel = 'ul';
            active_class = 'selected';
            panels = $('#module-body fieldset');
        }

        if (panels.length == 0) {
            console.log('Can\'t Locate the Panels to create the menus');
        }

        let full_item_sel = '<div id="module-nav" class="productTabs col-lg-2 col-md-3"><'+ item_sel + ' class="list-group tab "></'+item_sel+'></div>';

        if ($('#module-body').length > 0) {
            $('#module-body').append(full_item_sel);
        } else {
            panels.first().before(full_item_sel);
        }
        $('#module-nav').after('<div id="module-content" class="col-lg-10 col-md-9"></div>');
        $('#module-body form').appendTo('#module-content');

        /* Add elements, force the position in the menu if configured. */
        panels.each(function(i, e) {

            // Remove the last ID number which starts with lower score and followed by a X digit number from the panels.
            let $el = $(e);
            let newId = $el.attr('id')?.replace(/_\d+$/, '');
            if (newId) $el.attr('id', newId);

            // If the element doesn't have an ID, create one
            if (typeof $(this).attr('id') == 'undefined') {
                $(this).attr('id', 'fieldset_' + i + '_' + i + '_' + i);
            }

            // If the parent element isn't a form move it.
            if ($(this).parents('form').length == 0) {
                $(this).appendTo('#module_form');
            }
            var thisPanelHead = $(this).find(head_sel);
            let menu_item = (item_sel == 'ul' ? '<li class="tab-row">' : '') + '<a class="list-group-item tab-page" href="#' + thisPanelHead.parent(sel).attr('id') + '">' + thisPanelHead.html() + '</a>' + (item_sel == 'ul' ? '</li>' : '');
            if (typeof thisPanelHead.data('position') !== 'undefined' && $('.productTabs .list-group-item').length >= thisPanelHead.data('position')) {
                $('.productTabs .list-group-item').eq(thisPanelHead.data('position')).before(menu_item);
            } else {
                $('.productTabs .list-group').append(menu_item);
            }
        });

        /* Add elements, force the position in the menu if configured. */
        /* Build the navigation menu */

        // Initialize the tabs
        $('.productTabs a:first').addClass(active_class);
        panels.hide();
        panels.first().show();

        $('#conten form').each(function() {
            $(this).append('<input type="hidden" name="selected_menu" value="">');
        });

        $('.productTabs a').click(function(e) {
            $('.productTabs a').removeClass(active_class);
            $(this).addClass(active_class);
            e.preventDefault();
            var searchTab = $(this).attr('href');
            panels.hide();
            $(searchTab).show();
            $('input[name="selected_menu"]').val(searchTab);
        });
        selectLastMenu(panels);
    }, 600);

    /* Target menu to directly access the section */
    /* if the element has the data-scroll-to defined it scrolls to the element */
    $(document).on('click', '.target-menu', function() {
        let dest = $(this).attr('href');
        let scrollTo = $(this).data('scrollTo');
        console.log($('#module-body').find('#module-nav').first().find('a'));
        $('#module-body').find('#module-nav').first().find('a').each(function() {
            console.log($(this).attr('href') + 'VS ' + dest);
            if ($(this).attr('href') === dest) {
                $(this).click();
                setTimeout(function() {
                    if (!scrollTo) {
                        scrollTo = 'content';
                    }
                    let ele = document.getElementById(scrollTo);
                    ele.scrollIntoView({behavior: "smooth", block: "center"});
                    ele.focus();
                }, 100);
                return;
            }
        });
    });

    function selectLastMenu(panels)
    {
        var to_select = '';
        let cookies = document.cookie.split(';');
        for (let cookie of cookies) {
            let val = cookie.match('=([0-9a-zA-Z-_]*)')[1];
            if (cookie.indexOf(pp_prefix+'last_menu') !== -1 && val != '') {
                to_select = val;
                break;
            }
        }
        if (to_select === '') {
            if (window.location.hash != '') {
                to_select = window.location.hash;
            } else if (typeof selected_menu !== 'undefined' && selected_menu != '') {
                to_select = selected_menu;
            }
        }
        if (to_select != '') {
            $('#module-nav a').each(function() {
                if ($(this).attr('href').indexOf(to_select) !== -1) {
                    $(this).click();
                    window.scrollTo(0,0);
                    return false;
                }
            });
        } else {
            $('#module-nav a').first().click();
        }
    }
});