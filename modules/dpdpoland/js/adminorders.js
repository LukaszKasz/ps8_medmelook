/**
* 2019 DPD Polska Sp. z o.o.
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* prestashop@dpd.com.pl so we can send you a copy immediately.
*
*  @author    DPD Polska Sp. z o.o.
*  @copyright 2019 DPD Polska Sp. z o.o.
*  @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
*  International Registered Trademark & Property of DPD Polska Sp. z o.o.
*/

$(document).ready(function(){
    $('#form-order').on('click', '.bulk-actions a', function(event){
        event.preventDefault();

        var selector = $(this);

        if (selector.find('i').hasClass('dpd-label')) {
            var current_url = $('#form-order').attr('action');
            var new_url = current_url.replace('&submitBulkprint_a4order', '');
            $('#form-order').attr('action', new_url);
            $('#form-order').submit();

            return true;
        }

        if (selector.find('i').hasClass('dpd-a4')) {
            var current_url = $('#form-order').attr('action');
            var new_url = current_url.replace('&submitBulkprint_labelorder', '');
            $('#form-order').attr('action', new_url);
            $('#form-order').submit();

            return true;
        }

        var current_url = $('#form-order').attr('action');
        var new_url = current_url.replace('&submitBulkprint_labelorder', '');
        new_url = new_url.replace('&submitBulkprint_a4order', '');
        $('#form-order').attr('action', new_url);

        return true;
    });
});
