/*** -- Carousels: Case studies and Insights blocks -- ***/
// [data-carousel="fade"]: the arrows step through [data-slide]s (and their [data-slide-image]s).
// [data-carousel="scroll"]: the arrows scroll [data-carousel-track] a card at a time; it can also
// be swiped or scrolled, and the arrows disable at either end.
(function () {
	function initFade(block) {
		const slides = Array.from(block.querySelectorAll('[data-slide]'));
		const images = Array.from(block.querySelectorAll('[data-slide-image]'));
		const prev = block.querySelector('[data-carousel-prev]');
		const next = block.querySelector('[data-carousel-next]');
		let current = 0;

		function go(index) {
			current = Math.max(0, Math.min(slides.length - 1, index));
			slides.forEach(function (slide, i) {
				const active = i === current;
				slide.classList.toggle('is-active', active);
				slide.inert = !active;
				slide.setAttribute('aria-hidden', active ? 'false' : 'true');
			});
			images.forEach(function (img) {
				img.classList.toggle('is-active', Number(img.dataset.slideImage) === current);
			});
			prev.disabled = current === 0;
			next.disabled = current === slides.length - 1;
			// e.g. counts up the new slide's stats (assets/js/blocks/reveal.js)
			block.dispatchEvent(new CustomEvent('zinc:slide-change', { bubbles: true, detail: { index: current } }));
		}

		prev.addEventListener('click', () => go(current - 1));
		next.addEventListener('click', () => go(current + 1));
	}

	function initScroll(block) {
		const track = block.querySelector('[data-carousel-track]');
		const prev = block.querySelector('[data-carousel-prev]');
		const next = block.querySelector('[data-carousel-next]');
		if (!track || !prev || !next) return;

		const step = function () {
			const card = track.firstElementChild;
			return card ? card.offsetWidth + parseFloat(getComputedStyle(track).columnGap || 0) : track.clientWidth;
		};

		function update() {
			const max = track.scrollWidth - track.clientWidth;
			prev.disabled = track.scrollLeft <= 1;
			next.disabled = track.scrollLeft >= max - 1;
		}

		prev.addEventListener('click', () => track.scrollBy({ left: -step(), behavior: 'smooth' }));
		next.addEventListener('click', () => track.scrollBy({ left: step(), behavior: 'smooth' }));
		track.addEventListener('scroll', update, { passive: true });
		window.addEventListener('resize', update);
		update();
	}

	function init(block) {
		if (block.dataset.ready) return;
		block.dataset.ready = '1';
		if (block.dataset.carousel === 'fade') initFade(block);
		else initScroll(block);
	}

	function initAll(root) {
		const scope = root || document;
		if (scope.matches && scope.matches('[data-carousel]')) init(scope);
		scope.querySelectorAll('[data-carousel]').forEach(init);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', () => initAll());
	} else {
		initAll();
	}

	// Editor preview: ACF re-renders the block when its fields change
	if (window.acf && window.acf.addAction) {
		['case-studies', 'insights'].forEach(function (type) {
			window.acf.addAction('render_block_preview/type=' + type, function ($block) {
				initAll($block[0]);
			});
		});
	}
})();
/*** -- END Carousels -- ***/
