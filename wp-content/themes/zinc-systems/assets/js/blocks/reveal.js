/*** -- Reveal on scroll: Feature showcase cards and Stats blocks -- ***/
// Adds .is-revealed when a [data-reveal] element scrolls into view, which grows the showcase's bars
// (in CSS), and counts up any [data-count-to] numbers, keeping their prefix and suffix ("+550", "60%").
// With data-reveal-repeat (the showcase cards) it runs backwards too, keyed to its [data-reveal-anchor]
// (the stats panel): up once that's above 70% of the window, back down to zero (and the bars shrink)
// once you scroll back up and it drops below 80%, while it's still in view. A repeating block inside
// an inactive carousel slide (inert) resets, so it counts up again when its slide comes round; the
// carousel fires zinc:slide-change to say so.
(function () {
	const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
	const DURATION = 1600;
	const DURATION_DOWN = 900;
	const SHOW_AT = 0.7, HIDE_AT = 0.8;   // anchor's top, as a share of the window height

	// "+1,250.5%" -> how to show any value in between, and the value it ends on
	function parse(el) {
		if (el._count) return el._count;
		const target = el.dataset.countTo;
		const match = target.match(/^(\D*)([\d,]*\.?\d+)(.*)$/);
		if (!match) return (el._count = null);

		const [, prefix, number, suffix] = match;
		const decimals = (number.split('.')[1] || '').length;
		const commas = number.indexOf(',') !== -1;
		return (el._count = {
			target,
			end: parseFloat(number.replace(/,/g, '')),
			format(n) {
				const fixed = n.toFixed(decimals);
				return prefix + (commas ? Number(fixed).toLocaleString('en-GB', { minimumFractionDigits: decimals }) : fixed) + suffix;
			},
		});
	}

	// Animates from wherever the number is now, so reversing mid-count is smooth
	function countTo(el, up) {
		const c = parse(el);
		if (!c) return;
		cancelAnimationFrame(el._raf);

		const from = el._value || 0;
		const to = up ? c.end : 0;
		const ms = (up ? DURATION : DURATION_DOWN) * (Math.abs(to - from) / (c.end || 1));
		const start = performance.now();

		(function frame(now) {
			const t = ms > 0 ? Math.min(1, (now - start) / ms) : 1;
			el._value = from + (to - from) * (1 - Math.pow(1 - t, 3));
			el.textContent = t === 1 && up ? c.target : c.format(el._value);
			if (t < 1) el._raf = requestAnimationFrame(frame);
		})(start);
	}

	function reveal(block, show) {
		block.classList.toggle('is-revealed', show);
		block.querySelectorAll('[data-count-to]').forEach((el) => countTo(el, show));
	}

	const observer = 'IntersectionObserver' in window && new IntersectionObserver(function (entries) {
		entries.forEach(function (entry) {
			const block = entry.target;
			if (entry.isIntersecting && entry.intersectionRatio >= 0.3) {
				if (!block.classList.contains('is-revealed')) reveal(block, true);
				observer.unobserve(block);
			}
		});
	}, { threshold: [0, 0.3] });

	// Repeating blocks follow the scroll both ways
	const repeating = [];
	let ticking = false;
	function check() {
		ticking = false;
		const vh = window.innerHeight;
		repeating.forEach(function (block) {
			const top = (block.querySelector('[data-reveal-anchor]') || block).getBoundingClientRect().top;
			const shown = block.classList.contains('is-revealed');
			const active = !block.closest('[inert]');
			if (!shown && active && top < vh * SHOW_AT) reveal(block, true);
			else if (shown && (!active || top > vh * HIDE_AT)) reveal(block, false);
		});
	}
	function onScroll() {
		if (!ticking) {
			ticking = true;
			requestAnimationFrame(check);
		}
	}

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
			const c = parse(el);
			el._value = 0;
			el.textContent = c ? c.format(0) : el.textContent;
		});
		if ('revealRepeat' in block.dataset) {
			if (!repeating.length) {
				window.addEventListener('scroll', onScroll, { passive: true });
				window.addEventListener('resize', onScroll);
				document.addEventListener('zinc:slide-change', onScroll);
			}
			repeating.push(block);
			onScroll();
		} else {
			observer.observe(block);
		}
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
