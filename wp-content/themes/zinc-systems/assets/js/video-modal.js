/*** -- Pop-up video player -- ***/
// Any element with data-video-embed (a YouTube/Vimeo embed URL) or data-video-src (an uploaded file)
// opens the video in a <dialog>, playing. Esc, the close button or a click outside closes it; the
// dialog is removed on close, which stops the video, and focus goes back to the button that opened it.
(function () {
	const CLOSE_ICON = '<svg class="c-icon c-icon--close" width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false"><path d="m6 6 12 12M18 6 6 18" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>';

	function open(trigger) {
		const dialog = document.createElement('dialog');
		dialog.className = 'c-video-modal';
		dialog.setAttribute('aria-label', trigger.dataset.videoTitle || trigger.getAttribute('aria-label') || 'Video');

		const close = document.createElement('button');
		close.type = 'button';
		close.className = 'c-video-modal__close';
		close.setAttribute('aria-label', 'Close video');
		close.innerHTML = CLOSE_ICON;

		const frame = document.createElement('div');
		frame.className = 'c-video-modal__frame';

		if (trigger.dataset.videoEmbed) {
			const iframe = document.createElement('iframe');
			iframe.src = trigger.dataset.videoEmbed;
			iframe.title = dialog.getAttribute('aria-label');
			iframe.allow = 'autoplay; fullscreen; picture-in-picture; encrypted-media';
			iframe.allowFullscreen = true;
			frame.appendChild(iframe);
		} else {
			const video = document.createElement('video');
			video.src = trigger.dataset.videoSrc;
			video.controls = true;
			video.autoplay = true;
			video.playsInline = true;
			frame.appendChild(video);
		}

		dialog.append(close, frame);
		document.body.appendChild(dialog);
		document.documentElement.classList.add('has-video-modal');
		dialog.showModal();
		close.focus();

		close.addEventListener('click', () => dialog.close());
		// A click on the backdrop lands on the dialog itself, outside the frame
		dialog.addEventListener('click', function (event) {
			if (event.target === dialog) dialog.close();
		});
		dialog.addEventListener('close', function () {
			dialog.remove();
			document.documentElement.classList.remove('has-video-modal');
			// The trigger may sit in a menu that's closed meanwhile
			if (trigger.offsetParent !== null) trigger.focus({ preventScroll: true });
		});
	}

	document.addEventListener('click', function (event) {
		const trigger = event.target.closest('[data-video-embed], [data-video-src]');
		if (!trigger) return;
		event.preventDefault();
		open(trigger);
	});
})();
/*** -- END Pop-up video player -- ***/
