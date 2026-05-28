/**
 * pb-right-sidebar.js
 * Logic Vue untuk Right Sidebar — Floating Properties Panel
 * Diload sebelum main app.js, expose via window.PBRightSidebar
 *
 * Berisi: showRightSidebar, toggleRightSidebar,
 *         popupPos, startDrag (drag-to-move panel)
 */
window.PBRightSidebar = (function () {
	const { ref } = Vue;

	function setup() {

		// ============================================================
		// RIGHT SIDEBAR STATE
		// ============================================================
		const showRightSidebar = ref(false);

		const toggleRightSidebar = () => {
			showRightSidebar.value = !showRightSidebar.value;
		};

		// ============================================================
		// FLOATING PANEL — Drag to Move Logic
		// Panel posisi default: kanan atas layar
		// ============================================================
		const popupPos = ref({
			top:  85,
			left: window.innerWidth - 447
		});

		let isDragging  = false;
		let dragOffset  = { x: 0, y: 0 };

		// 1. Mulai Drag (MouseDown pada header panel)
		const startDrag = (e) => {
			isDragging    = true;
			dragOffset.x  = e.clientX - popupPos.value.left;
			dragOffset.y  = e.clientY - popupPos.value.top;

			window.addEventListener('mousemove', onDrag);
			window.addEventListener('mouseup',   stopDrag);
		};

		// 2. Proses Drag (MouseMove)
		const onDrag = (e) => {
			if (!isDragging) return;
			popupPos.value.left = e.clientX - dragOffset.x;
			popupPos.value.top  = e.clientY - dragOffset.y;
		};

		// 3. Stop Drag (MouseUp)
		const stopDrag = () => {
			isDragging = false;
			window.removeEventListener('mousemove', onDrag);
			window.removeEventListener('mouseup',   stopDrag);
		};

		// ============================================================
		// RETURN
		// ============================================================
		return {
			showRightSidebar,
			toggleRightSidebar,
			popupPos,
			startDrag,
		};
	}

	return { setup };
})();
