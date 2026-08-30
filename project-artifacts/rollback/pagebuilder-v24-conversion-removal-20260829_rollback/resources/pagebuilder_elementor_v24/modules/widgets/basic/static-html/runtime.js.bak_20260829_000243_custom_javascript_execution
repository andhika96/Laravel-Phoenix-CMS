(function () {
	'use strict';
	window.addEventListener('message', function (event) {
		if (!event.data || event.data.type !== 'pb-static-html-height') return;
		var height = Number(event.data.height);
		if (!Number.isFinite(height)) return;
		document.querySelectorAll('[data-pb-static-html="true"] iframe').forEach(function (frame) {
			if (frame.contentWindow === event.source) frame.style.height = Math.max(320, Math.min(30000, Math.ceil(height))) + 'px';
		});
	});
})();
