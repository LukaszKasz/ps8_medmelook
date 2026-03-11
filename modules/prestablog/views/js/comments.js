/**
 * 2008 - 2024 (c) Prestablog
 *
 * MODULE PrestaBlog
 *
 * @author    Prestablog
 * @copyright Copyright (c) permanent, Prestablog
 * @license   Commercial

 */

$(document).ready(function() {
	if ( $("#submitOk").length ) {
		$('html, body').animate({scrollTop: $("#submitOk").offset().top}, 750);

	}

	if ( $("#errors").length ) {
		$('html, body').animate({scrollTop: $("#errors").offset().top}, 750);
	}

 	$('#comments').show();

	$("#with-http").hide();

	$("#url").focus(function() { $("#with-http").fadeIn(); });

	$("#url").focusout(function() { $("#with-http").fadeOut(); });
});
        document.addEventListener('DOMContentLoaded', function() {
            let activeReplyForm = null;

            function toggleHeight(element) {
                if (element.classList.contains('show')) {
                    element.style.height = element.scrollHeight + 'px';
                    window.getComputedStyle(element).height;
                    element.style.height = '0px';
                    element.classList.remove('show');
                } else {
                    element.style.height = element.scrollHeight + 'px';
                    element.classList.add('show');
                    element.addEventListener('transitionend', function onTransitionEnd() {
                        element.style.height = 'auto';
                        element.removeEventListener('transitionend', onTransitionEnd);
                    });
                }
            }

            const toggleButtons = document.querySelectorAll('.toggle-replies');
            toggleButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const targetId = this.getAttribute('data-target');
                    const repliesSection = document.getElementById(targetId);
                    toggleHeight(repliesSection);
                });
            });

            const replyLinks = document.querySelectorAll('.reply-link');
            replyLinks.forEach(link => {
                link.addEventListener('click', function() {
                    const targetId = this.getAttribute('data-target');
                    const replyContainer = document.getElementById(targetId);

                    if (activeReplyForm && activeReplyForm !== replyContainer) {
                        toggleHeight(activeReplyForm);
                    }

                    toggleHeight(replyContainer);

                    if (replyContainer.style.height !== '0px') {
                        activeReplyForm = replyContainer;
                    } else {
                        activeReplyForm = null;
                    }
                });
            });
        });
