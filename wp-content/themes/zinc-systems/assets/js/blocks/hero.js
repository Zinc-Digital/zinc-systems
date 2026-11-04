/*** -- Hero block: pinned scroll story -- ***/
// As the page scrolls the hero stays pinned under the header: the copy fades back, the chips
// gather in behind the product UI, and the UI rises to the middle of the screen and builds itself.
// Everything is laid out on the 1440 × 1370 design stage, scaled to the window (--s).
(function () {
	const STAGE_W = 1440, STAGE_H = 1370;
	const TAIL = 110;                                     // = --tail: between the window's bottom and the logo strip
	const CHIPS_X = 80, CHIPS_Y = 77;                     // chip layer on the stage
	const UI = { x: 278, y: 609, w: 885, h: 591 };        // UI frame on the stage
	const DASH_W = 1240;                                  // dashboard's internal width

	const clamp = (v, a = 0, b = 1) => Math.max(a, Math.min(b, v));
	const ease = (t) => t * t * (3 - 2 * t);              // smoothstep
	const easeOut = (t) => 1 - Math.pow(1 - t, 3);
	const span = (p, a, b) => clamp((p - a) / (b - a));
	const raf = (f) => requestAnimationFrame(f);

	const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
	const mobile = window.matchMedia('(max-width: 767px)');

	/* ---------- The dashboard's own animation ---------- */
	function createDashboard(dash) {
		const donutSegs = Array.from(dash.querySelectorAll('.b-hero-dash__donut-seg'));
		const lineSvg = dash.querySelector('[data-dash-line]');
		const counters = Array.from(dash.querySelectorAll('[data-count]'));
		const cursor = dash.querySelector('[data-dash-cursor]');
		const tip = dash.querySelector('[data-dash-tip]');
		const range = dash.querySelector('[data-dash-range]');
		const C = 2 * Math.PI * 89;
		let timers = [];
		const later = (fn, ms) => timers.push(setTimeout(fn, ms));

		function donutAt(f) {
			donutSegs.forEach(function (s) {
				const from = +s.dataset.from, to = +s.dataset.to;
				const shown = Math.max(0, Math.min(to, 360 * f) - from);
				s.setAttribute('stroke-dasharray', ((shown / 360) * C).toFixed(2) + ' ' + C.toFixed(2));
				s.setAttribute('stroke-dashoffset', (-(from / 360) * C).toFixed(2));
			});
		}

		// Line chart: points from the block (data-points), plotted left to right
		const X0 = 44, X1 = 492, Y0 = 210, Y1 = 8;          // plot box inside the card
		let points = [];
		try {
			points = JSON.parse(lineSvg.getAttribute('data-points') || '[]');
		} catch (e) {}
		if (!points.length) points = [{ label: '', value: 0 }];
		const labels = points.map((pt) => pt.label);
		const vals = points.map((pt) => Math.max(0, +pt.value || 0));
		const xs = labels.map((_, i) => (labels.length > 1 ? X0 + ((X1 - X0) * i) / (labels.length - 1) : (X0 + X1) / 2));

		// The axis: six steps of a round size (0.5, 1, 2, 5, 10…) that reach the highest value
		const rough = Math.max(...vals, 0.5) / 6;
		const mag = Math.pow(10, Math.floor(Math.log10(rough)));
		const step = [1, 2, 5, 10].map((m) => m * mag).find((s) => s >= rough);
		const yMax = step * 6;
		const yOf = (v) => Y0 - ((Y0 - Y1) * v) / yMax;
		const tickLabel = (v) => (v === 0 ? '0' : step < 1 ? v.toFixed(1) : String(Math.round(v)));

		const esc = (s) => String(s).replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[c]);
		(function drawLine() {
			let g = '';
			for (let i = 0; i <= 6; i++) {
				const v = step * i, y = yOf(v);
				g += '<line x1="' + X0 + '" x2="' + X1 + '" y1="' + y + '" y2="' + y + '" stroke="#eef1f3"/>';
				g += '<text x="' + (X0 - 8) + '" y="' + (y + 3) + '" font-size="8.5" fill="#6b7177" text-anchor="end">' + tickLabel(v) + '</text>';
			}
			labels.forEach((l, i) => (g += '<text x="' + xs[i] + '" y="' + (Y0 + 16) + '" font-size="8.5" fill="#6b7177" text-anchor="middle">' + esc(l) + '</text>'));
			// A smooth curve through the points
			const pts = vals.map((v, i) => [xs[i], yOf(v)]);
			let d = 'M' + pts[0][0] + ',' + pts[0][1];
			for (let i = 1; i < pts.length; i++) {
				const [x0, y0] = pts[i - 1], [x1, y1] = pts[i];
				const k = (x1 - x0) * 0.42;
				d += ' C' + (x0 + k) + ',' + y0 + ' ' + (x1 - k) + ',' + y1 + ' ' + x1 + ',' + y1;
			}
			g += '<path class="b-hero-dash__line-area" d="' + d + ' L' + X1 + ',' + Y0 + ' L' + X0 + ',' + Y0 + ' Z" fill="#e9f3fa"/>';
			g += '<path class="b-hero-dash__line-path" d="' + d + '" fill="none" stroke="#4ca5da" stroke-width="2"/>';
			pts.forEach(([x, y], i) => (g += '<circle class="b-hero-dash__pt" cx="' + x + '" cy="' + y + '" r="2.6" fill="#fff" stroke="#4ca5da" stroke-width="1.4" style="transition-delay:' + (0.25 + i * 0.12) + 's"/>'));
			lineSvg.innerHTML = g;
		})();
		// The cursor checks the peak: tooltip "<label> · <value> <unit>"
		const peak = vals.indexOf(Math.max(...vals));
		const peakX = 545 + 123 + xs[peak], peakY = 437 + 45 + yOf(vals[peak]);
		tip.innerHTML = esc(labels[peak]) + ' · <b>' + esc(vals[peak]) + '</b> ' + esc(tip.getAttribute('data-unit') || '');

		const linePath = lineSvg.querySelector('.b-hero-dash__line-path');
		const LEN = linePath.getTotalLength();
		function lineAt(f) {
			linePath.style.strokeDasharray = LEN;
			linePath.style.strokeDashoffset = LEN * (1 - f);
		}

		function count(el, to, ms) {
			const start = performance.now();
			const step = function (now) {
				const t = clamp((now - start) / ms);
				el.textContent = Math.round(easeOut(t) * to);
				if (t < 1 && dash.classList.contains('is-built')) raf(step);
			};
			raf(step);
		}

		function moveCursor(x, y, show) {
			cursor.style.transition = 'opacity .3s ease, transform .8s cubic-bezier(.45,0,.2,1)';
			cursor.style.opacity = show;
			cursor.style.transform = 'translate(' + x + 'px, ' + y + 'px)';
		}

		function reset() {
			timers.forEach(clearTimeout);
			timers = [];
			dash.classList.remove('is-built');
			counters.forEach((c) => (c.textContent = '0'));
			donutAt(0);
			lineAt(0);
			cursor.style.opacity = 0;
			tip.classList.remove('is-on');
			range.style.boxShadow = 'none';
		}

		// Plays the build-in; calls done() once it has all played out
		function build(done) {
			dash.classList.add('is-built');
			later(() => counters.forEach((c, i) => setTimeout(() => count(c, +c.dataset.count, 900), i * 70)), 250);
			later(() => {
				const start = performance.now();
				const run = function (now) {
					const t = clamp((now - start) / 1100);
					donutAt(easeOut(t));
					if (t < 1 && dash.classList.contains('is-built')) raf(run);
				};
				raf(run);
			}, 550);
			later(() => lineAt(1), 650);
			// A cursor works the dashboard: changes the date range, then checks the peak
			later(() => moveCursor(980, 330, 0), 1500);
			later(() => moveCursor(530, 132, 1), 1700);
			later(() => { range.style.boxShadow = '0 0 0 3px rgba(61,156,194,.25)'; }, 2500);
			later(() => { range.style.boxShadow = 'none'; lineAt(0.001); }, 2800);
			later(() => lineAt(1), 2950);
			later(() => moveCursor(peakX, peakY, 1), 3300);
			later(() => {
				tip.style.left = peakX + 'px';
				tip.style.top = peakY + 'px';
				tip.classList.add('is-on');
			}, 4200);
			later(done, 5800);
		}

		// Finished state, without the animation
		function showBuilt() {
			reset();
			dash.classList.add('is-built');
			counters.forEach((c) => (c.textContent = c.dataset.count));
			donutAt(1);
			lineAt(1);
		}

		reset();
		return { build: build, reset: reset, showBuilt: showBuilt };
	}

	/* ---------- One hero ---------- */
	function initHero(hero) {
		if (!hero || hero.dataset.heroInit) return;
		hero.dataset.heroInit = '1';

		const scroll = hero.querySelector('[data-hero-scroll]');
		const pin = hero.querySelector('[data-hero-pin]');
		const bg = hero.querySelector('[data-hero-bg]');
		const copy = hero.querySelector('[data-hero-copy]');
		const ui = hero.querySelector('[data-hero-ui]');
		const demo = hero.querySelector('[data-hero-demo]');
		const dashEl = hero.querySelector('[data-hero-dash]');
		const dash = dashEl ? createDashboard(dashEl) : null;
		const isPreview = hero.classList.contains('is-preview');

		const chips = Array.from(hero.querySelectorAll('.b-hero__chip')).map((el, i) => ({
			el: el,
			x: parseFloat(el.style.left) || 0,
			y: parseFloat(el.style.top) || 0,
			w: 0,
			h: 0,
			depth: parseFloat(el.dataset.depth || '1'),
			faint: el.hasAttribute('data-faint'),
			i: i,
		}));
		// Where each chip ends up: a loose cluster around the UI frame's centre, behind it
		const targets = chips.map((c, i) => {
			const a = (i / chips.length) * Math.PI * 2 + 0.6;
			return { dx: Math.cos(a) * 240, dy: Math.sin(a) * 60 };
		});

		let mode = '', scale = 1, viewH = 0, offset = 0;
		let p = 0, built = false, building = false, running = false, visible = true;
		const mouse = { x: 0, y: 0, tx: 0, ty: 0 };

		function setDemo(on) {
			if (demo) demo.classList.toggle('is-on', on);
		}

		// The UI comes alive (the dashboard builds; an image just settles), then the demo CTA follows
		function build() {
			building = true;
			const done = () => { built = true; building = false; setDemo(true); };
			if (dash) dash.build(done);
			else setTimeout(done, 600);
		}

		function reset() {
			built = false;
			building = false;
			if (dash) dash.reset();
			setDemo(false);
		}

		function clearMotion() {
			[ui, bg, copy].concat(chips.map((c) => c.el)).forEach((el) => {
				el.style.transform = '';
				el.style.opacity = '';
				el.style.filter = '';
				el.style.zIndex = '';
			});
		}

		// The sticky header (and admin bar) the pin sits under
		function headerOffset() {
			let o = 0;
			const header = document.querySelector('[data-site-header]');
			const adminBar = document.getElementById('wpadminbar');
			if (header && getComputedStyle(header).position === 'sticky') o += header.offsetHeight;
			if (adminBar && getComputedStyle(adminBar).position === 'fixed') o += adminBar.offsetHeight;
			return o;
		}

		function layout() {
			offset = isPreview ? 0 : headerOffset();
			scale = Math.min(1.25, hero.clientWidth / STAGE_W);
			hero.style.setProperty('--s', scale.toFixed(4));
			hero.style.setProperty('--hero-offset', offset + 'px');
			// Visible stage height (the pin's tail is below it)
			viewH = (window.innerHeight - offset) / scale;

			const nextMode = isPreview || reducedMotion.matches ? 'static' : (mobile.matches ? 'mobile' : 'scroll');
			hero.style.setProperty('--dash-s', nextMode === 'mobile' ? (ui.clientWidth / DASH_W).toFixed(5) : '');
			chips.forEach((c) => { c.w = c.el.offsetWidth; c.h = c.el.offsetHeight; });

			if (nextMode !== mode) {
				mode = nextMode;
				clearMotion();
				reset();
				p = 0;
				if (mode === 'static') {
					if (dash) dash.showBuilt();
					setDemo(true);
				}
				start();
			}
		}

		/* Mouse: chips drift a few pixels toward the cursor */
		pin.addEventListener('pointermove', function (e) {
			const r = pin.getBoundingClientRect();
			mouse.tx = ((e.clientX - r.left) / r.width - 0.5) * 2;
			mouse.ty = ((e.clientY - r.top) / r.height - 0.5) * 2;
		});
		pin.addEventListener('pointerleave', function () {
			mouse.tx = 0;
			mouse.ty = 0;
		});

		/* Scroll progress through the pinned hero: 0 at the start, 1 as the pin lets go */
		function progress() {
			const run = scroll.offsetHeight - pin.offsetHeight;
			return run > 0 ? clamp((offset - scroll.getBoundingClientRect().top) / run) : 0;
		}

		function frame() {
			if (mode !== 'scroll' || !visible) {
				running = false;
				return;
			}

			const target = progress();
			p += (target - p) * 0.16;                       // a touch of smoothing
			if (Math.abs(target - p) < 0.0005) p = target;
			mouse.x += (mouse.tx - mouse.x) * 0.06;
			mouse.y += (mouse.ty - mouse.y) * 0.06;

			// 1. The UI frame rises to the middle of the screen and grows a little
			const rise = ease(span(p, 0.18, 0.7));
			// It ends centred in the window, as large as fits (up to 108%) with room around it
			const endScale = Math.min(1.08, (viewH - 70) / UI.h);
			const endCenter = viewH / 2;
			// It starts smaller (84%), its top edge where it was, and grows as it rises
			const UI_START = 0.84, restLift = (0.96 - UI_START) * UI.h / 2;
			const uiDy = (endCenter - (UI.y + UI.h / 2)) * rise - restLift * (1 - rise);
			const uiScale = UI_START + (endScale - UI_START) * rise;
			ui.style.transform = 'translateY(' + uiDy.toFixed(1) + 'px) scale(' + uiScale.toFixed(4) + ')';
			const uiCx = UI.x + UI.w / 2;
			// The background rises with it: the shapes come up behind the frame, and the bottom of
			// the window lands on 1370 − TAIL; the pin's tail runs on to 1370, the logo strip
			bg.style.transform = 'translateY(' + (-Math.max(0, STAGE_H - TAIL - viewH) * rise).toFixed(1) + 'px)';

			// 2. The headline, body and buttons fade backwards (smaller, softer, gone)
			const back = ease(span(p, 0.08, 0.45));
			copy.style.opacity = (1 - back).toFixed(3);
			copy.style.transform = 'translateY(' + (-30 * back).toFixed(1) + 'px) scale(' + (1 - 0.16 * back).toFixed(4) + ')';
			copy.style.filter = back > 0.01 ? 'blur(' + (back * 7).toFixed(2) + 'px)' : 'none';

			// 3. Chips: the faint ones come up so you can read them, then all of them travel
			//    down to the centre, shrink, and slip behind the UI frame
			chips.forEach(function (c) {
				const t = ease(span(p, 0.14 + c.i * 0.012, 0.62 + c.i * 0.01));
				const cx = CHIPS_X + c.x + c.w / 2, cy = CHIPS_Y + c.y + c.h / 2;
				// They gather just inside the frame's top edge and slip in behind it
				const uiTop = UI.y + uiDy - (uiScale - 1) * UI.h / 2;
				const tx = uiCx + targets[c.i].dx - cx, ty = uiTop + 40 + targets[c.i].dy * 0.35 - cy;
				const s = 1 - 0.58 * t;
				const reveal = c.faint ? 0.1 + 0.9 * ease(span(p, 0.02, 0.16)) : 1;
				const hide = 1 - ease(span(p, 0.56, 0.7));
				const m = (1 - t) * 10 * c.depth;               // mouse drift fades as they travel
				const mx = mouse.x * m, my = mouse.y * m * 0.7;
				c.el.style.transform = 'translate(' + (tx * t + mx).toFixed(1) + 'px, ' + (ty * t + my).toFixed(1) + 'px) scale(' + s.toFixed(4) + ') rotate(' + (mouse.x * (1 - t) * c.depth * 0.8).toFixed(2) + 'deg)';
				c.el.style.opacity = (reveal * hide).toFixed(3);
				c.el.style.zIndex = t > 0.55 ? 1 : 3;
			});

			// 4. The UI frame comes alive once it's arrived; the demo CTA follows
			if (p > 0.62 && !built && !building) build();
			if (p < 0.15 && (built || building)) reset();

			raf(frame);
		}

		function start() {
			if (mode === 'scroll' && visible && !running) {
				running = true;
				raf(frame);
			}
		}

		// Only animate while the hero is on screen
		new IntersectionObserver(function (entries) {
			visible = entries[0].isIntersecting;
			start();
		}, { rootMargin: '200px 0px' }).observe(hero);

		// Mobile: no pin; the UI builds when it scrolls into view
		new IntersectionObserver(function (entries) {
			if (mode !== 'mobile') return;
			const entry = entries[0];
			if (entry.intersectionRatio >= 0.5 && !built && !building) build();
			else if (!entry.isIntersecting && (built || building)) reset();
		}, { threshold: [0, 0.5] }).observe(ui);

		layout();
		window.addEventListener('resize', layout);
		reducedMotion.addEventListener('change', layout);
		if (document.fonts) document.fonts.ready.then(layout);
	}

	function initAll(root) {
		(root || document).querySelectorAll('[data-hero]').forEach(initHero);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', () => initAll());
	} else {
		initAll();
	}

	// Editor preview: ACF re-renders the block when its fields change
	if (window.acf && window.acf.addAction) {
		window.acf.addAction('render_block_preview/type=hero', function ($block) {
			initAll($block[0]);
		});
	}
})();
/*** -- END Hero block -- ***/
