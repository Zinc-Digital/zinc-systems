/*** -- Stacking cards: Feature showcase block -- ***/
// The cards are position: sticky in CSS, so each pins under the header and the next slides up over it.
// They're all made as tall as the tallest one.
// A card taller than the space below the header would pin with its bottom (and its buttons) off
// screen and get covered before it's been read, so it's given a lower top that pins it by its
// bottom edge instead.
(function () {
	const GAP = 24; // between a tall card's bottom edge and the bottom of the window

	function init(block) {
		if (block.dataset.stackReady) return;
		block.dataset.stackReady = '1';

		const cards = Array.from(block.querySelectorAll('[data-stack-card]'));

		function layout() {
			// Every card as tall as the tallest, so a card never leaves the one under it peeking out below
			cards.forEach((card) => { card.style.minHeight = ''; card.style.top = ''; });
			const height = Math.max.apply(null, cards.map((card) => card.offsetHeight));

			cards.forEach(function (card) {
				card.style.minHeight = height + 'px';
				const top = parseFloat(getComputedStyle(card).top) || 0;
				const fit = window.innerHeight - height - GAP;
				if (fit < top) card.style.top = fit + 'px';
			});
		}

		layout();
		window.addEventListener('resize', layout);
		if (document.fonts) document.fonts.ready.then(layout);
	}

	function initAll(root) {
		(root || document).querySelectorAll('[data-stack]').forEach(init);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', () => initAll());
	} else {
		initAll();
	}
})();
/*** -- END Stacking cards -- ***/
