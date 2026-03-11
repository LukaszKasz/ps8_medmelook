{*
* 2012-2022 Patryk Marek PrestaDev.pl
*
* Patryk Marek PrestaDev.pl - PD Google Analytycs 4.0 Pro 1.6.x and 1.7.x Module © All rights reserved.
*
* DISCLAIMER
*
* Do not edit, modify or copy this file.
* If you wish to customize it, contact us at info@prestadev.pl.
*
* @author    Patryk Marek <info@prestadev.pl>
* @copyright 2012-2022 Patryk Marek @ PrestaDev.pl
* @license   Do not edit, modify or copy this file, if you wish to customize it, contact us at info@prestadev.pl.
* @link      http://prestadev.pl
* @package   PD Google Analytycs 4.0 Pro 1.6.x and 1.7.x Module
* @version   1.0.2
* @date      01-05-2021
*}

<!-- PD Google Analytycs 4 Pro - EVENT > refund -->
<script type="text/javascript">
    console.log('Fired up event GA4: refund');

    gtag('event', 'refund', {
        currency: '{$currency|escape:'htmlall':'UTF-8'}',
        transaction_id: '{$transaction_id|escape:'htmlall':'UTF-8'}',
        value: {$value|escape:'htmlall':'UTF-8'},
        affiliation: '{$affiliation|escape:'htmlall':'UTF-8'}',
        coupon: '{$coupon|escape:'htmlall':'UTF-8'}',
        shipping: {$shipping|escape:'htmlall':'UTF-8'},
        tax: {$tax|escape:'htmlall':'UTF-8'}
    });
</script>
<!-- PD Google Analytycs 4 Pro - EVENT > refund -->
