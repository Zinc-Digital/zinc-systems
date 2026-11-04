/*** -- Reveal on scroll: Feature showcase cards and Stats blocks -- ***/
// Adds .is-revealed when a [data-reveal] element scrolls into view, which grows the showcase's bars
// (in CSS), and counts up any [data-count-to] numbers, keeping their prefix and suffix ("+550", "60%").
(function () {
	const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
	const DURATION = 1600;

	function countUp(el) {
		const target = el.dataset.countTo;
		const match = target.match(/^(\D*)([\d,]*\.?\d+)(.*)$/);
		if (!match) return;

		const [, prefix, number, suffix] = match;
		const end = parseFloat(number.replace(/,/g, ''));
		const decimals = (number.split('.')[1] || '').length;
		const commas = number.indexOf(',') !== -1;
		const format = (n) => {
			const fixed = n.toFixed(decimals);
			return prefix + (commas ? Number(fixed).toLocaleString('en-GB', { minimumFractionDigits: decimals }) : fixed) + suffix;
		};

		const start = performance.now();
		(function frame(now) {
			const t = Math.min(1, (now - start) / DURATION);
			el.textContent = format(end * (1 - Math.pow(1 - t, 3)));
			if (t < 1) requestAnimationFrame(frame);
			else el.textContent = target;
		})(start);
	}

	function reveal(block) {
		block.classList.add('is-revealed');
		if (!reducedMotion.matches) block.querySelectorAll('[data-count-to]').forEach(countUp);
	}

	const observer = 'IntersectionObserver' in window && new IntersectionObserver(function (entries) {
		entries.forEach(function (entry) {
			if (!entry.isIntersecting) return;
			observer.unobserve(entry.target);
			reveal(entry.target);
		});
	}, { threshold: 0.3 });

	function init(block) {
		if (block.dataset.ready) return;
		block.dataset.ready = '1';

		// Shown as is in the editor, with reduced motion, or without IntersectionObserver
		if (!observer || reducedMotion.matches || block.closest('.is-preview')) {
			block.classList.add('is-revealed');
			return;
		}

		block.classList.add('is-reveal-ready');
		block.querySelectorAll('[data-count-to]').forEach(function (el) {
			el.textContent = el.dataset.countTo.replace(/\d/g, '0').replace(/^(\D*)0+(?=\d)/, '$1');
		});
		observer.observe(block);
	}

	function initAll(root) {
		(root || document).querySelectorAll('[data-reveal]').forEach(init);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', () => initAll());
	} else {
		initAll();
	}
})();
/*** -- END Reveal on scroll -- ***/
