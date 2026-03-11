/**
* 2013-2016 Patryk Marek PrestaDev.pl
*
* Patryk Marek PrestaDev.pl - PD Google Merchant Center Pro © All rights reserved.
*
* DISCLAIMER
*
* Do not edit, modify or copy this file.
* If you wish to customize it, contact us at info@prestadev.pl.
*
* @author Patryk Marek PrestaDev.pl <info@prestadev.pl>
* @copyright 2013-2016 Patryk Marek PrestaDev.pl
* @version   Release: 2.1.2
*/

	function convertHtmlToText(returnText) {

		if (checkIfStringIsHTML(returnText)) {
			return jQuery(returnText).text();
		} else {
			return returnText;
		}

	}

	function checkIfStringIsHTML(str) {
	  	var doc = new DOMParser().parseFromString(str, "text/html");
	  	return Array.from(doc.body.childNodes).some(node => node.nodeType === 1);
	}