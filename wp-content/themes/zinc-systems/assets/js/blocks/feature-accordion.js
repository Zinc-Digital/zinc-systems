/*** -- Feature accordion block -- ***/
// One item open at a time; an item with its own image swaps it in for the main image.
(function () {
	function init(block) {
		if (block.dataset.ready) return;
		block.dataset.ready = '1';

		const items = Array.from(block.querySelectorAll('.b-feature-accordion__item'));
		const images = Array.from(block.querySelectorAll('.b-feature-accordion__image'));

		function open(item) {
			items.forEach(function (other) {
				const isOpen = other === item;
				other.classList.toggle('is-open', isOpen);
				other.querySelector('.b-feature-accordion__toggle').setAttribute('aria-expanded', isOpen ? 'true' : 'false');
				other.querySelector('.b-feature-accordion__panel').inert = !isOpen;
			});

			// The item's own image, or the main one (which has no data-item)
			const own = images.find((img) => img.dataset.item === item.dataset.item);
			images.forEach(function (img) {
				img.classList.toggle('is-active', own ? img === own : !img.dataset.item);
			});
		}

		items.forEach(function (item) {
			item.querySelector('.b-feature-accordion__toggle').addEventListener('click', function () {
				if (!item.classList.contains('is-open')) open(item);
			});
		});
	}

	function initAll(root) {
		(root || document).querySelectorAll('[data-feature-accordion]').forEach(init);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', () => initAll());
	} else {
		initAll();
	}

	// Editor preview: ACF re-renders the block when its fields change
	if (window.acf && window.acf.addAction) {
		window.acf.addAction('render_block_preview/type=feature-accordion', function ($block) {
			initAll($block[0]);
		});
	}
})();
/*** -- END Feature accordion block -- ***/
