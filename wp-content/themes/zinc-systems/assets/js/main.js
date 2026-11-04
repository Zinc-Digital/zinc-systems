window.addEventListener("load", function () {


		jQuery(".usp_box ul li").click(function () {
			jQuery(this).find('.info_box').slideToggle();
		});

		jQuery(".close").click(function () {
			jQuery(this).find('.info_box').hide();
		});


	/*** -- Scroll animation -- ***/
	function scrollInit() {
		//Display animted on scroll elements if they are within the viewport on load
		let window_bottom = jQuery(window).scrollTop() + jQuery(window).height();
		jQuery('.scroll-animated').each(function () {
			if (jQuery(this).offset().top <= window_bottom) {
				jQuery(this).addClass('animate');
			}
		});
	}
	scrollInit();

	jQuery(window).scroll(function (e) {
		//Animate in elements with the "scroll-animated" class
		let window_bottom = jQuery(window).scrollTop() + jQuery(window).height();

		jQuery('.scroll-animated').each(function () {
			if (jQuery(this).offset().top < window_bottom) {
				jQuery(this).addClass('animate');
			} else if (jQuery(this).offset().top > window_bottom) {
				jQuery(this).removeClass('animate');
			}
		});
	});
	/*** -- END Scroll animation -- ***/

	/*** -- Read more -- ***/
	jQuery(".hidden-content").hide();
	jQuery(".show_hide").on("click", function () {
		jQuery(".show_hide").toggleClass('active');
		jQuery(this).parents('.body').find('.hidden-content').slideToggle(200);
	});


	/*** -- END Read more -- ***/

	/*** -- Footer tagline: shrink to fit on one line -- ***/
	let tagline = document.querySelector('.c-site-footer__tagline');
	if (tagline) {
		function fitTagline() {
			tagline.style.fontSize = '';
			let available = tagline.clientWidth;
			let needed = tagline.scrollWidth;
			if (needed > available) {
				let size = parseFloat(getComputedStyle(tagline).fontSize);
				tagline.style.fontSize = Math.floor(size * available / needed) + 'px';
			}
		}
		fitTagline();
		window.addEventListener('resize', fitTagline);
		if (document.fonts) {
			document.fonts.ready.then(fitTagline);
		}
	}
	/*** -- END Footer tagline -- ***/
});