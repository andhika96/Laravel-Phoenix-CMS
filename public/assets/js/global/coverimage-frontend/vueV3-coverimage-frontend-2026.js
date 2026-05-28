const CoverImageVue3 = createApp(
{
	data()
	{
		return {
			getData: []
		}
	},
	methods:
	{
		getNewTime: function(value, utc) 
		{
			// Split the date and time parts (if time exists)
			// Example: "23/10/2025 10:30" -> ["23/10/2025", "10:30"]
			let dateTimeParts = value.split(' ');
			let dateString = dateTimeParts[0]; // Date part: "23/10/2025"
			let timeString = dateTimeParts[1]; // Time part: "10:30" (or undefined)

			// 1. Parse Date (dd/mm/yyyy)
			let dateParts = dateString.split('/');

			// dateParts[0] = dd, dateParts[1] = mm, dateParts[2] = yyyy
			let year = parseInt(dateParts[2], 10);
			let month = parseInt(dateParts[1], 10) - 1; // Date() uses zero-based months (0-11)
			let day = parseInt(dateParts[0], 10);

			// 2. Parse Time (HH:MM)
			let hours = 0;
			let minutes = 0;

			if (timeString) 
			{
				let timeParts = timeString.split(':');
				hours = parseInt(timeParts[0], 10) || 0;   // Default to 0 if parsing fails
				minutes = parseInt(timeParts[1], 10) || 0; // Default to 0 if parsing fails
			}

			// 3. Create the Date object
			if (utc) 
			{
				// Use Date.UTC() with full parameters
				return Date.UTC(year, month, day, hours, minutes);
			}

			// Use new Date() with full parameters and get .getTime()
			return new Date(year, month, day, hours, minutes).getTime();
		},
		countDownVue: function(index, data)
		{
			// Set the date we're counting down to
			let countDownDate = new Date(this.getNewTime(data, false)).getTime();

			let output = 0;

			// Helper: render countdown ke semua elemen target yang bukan clone Splide
			function renderCountdown(index, template)
			{
				const targets = document.querySelectorAll('[data-countdown-idx="' + index + '"]');

				targets.forEach(function(el) 
				{
					// Lewati elemen yang berada di dalam slide clone Splide
					// Slide clone memiliki class 'is-clone' pada elemen .splide__slide parent-nya
					const slideParent = el.closest('.splide__slide');

					if (slideParent && slideParent.classList.contains('is-clone')) 
					{
						return;
					}

					el.innerHTML = template;
				});
			}

			// Helper: hitung dan build template HTML countdown
			function buildTemplate(distance)
			{
				let days    = String(Math.floor(distance / (1000 * 60 * 60 * 24))).padStart(2, '0');
				let hours   = String(Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))).padStart(2, '0');
				let minutes = String(Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60))).padStart(2, '0');
				let seconds = String(Math.floor((distance % (1000 * 60)) / 1000)).padStart(2, '0');

				return `<div class="row g-4">
					<div class="col-3 text-center">
						<div class="d-block h4">`+days+`</div>
						<div>Days</div>
					</div>

					<div class="col-3 text-center">
						<div class="d-block h4">`+hours+`</div>
						<div>Hours</div>
					</div>

					<div class="col-3 text-center">
						<div class="d-block h4">`+minutes+`</div>
						<div>Minutes</div>
					</div>

					<div class="col-3 text-center">
						<div class="d-block h4">`+seconds+`</div>
						<div>Seconds</div>
					</div>
				</div>`;
			}

			// Render sekali langsung agar tidak ada jeda blank
			const initialDistance = countDownDate - new Date().getTime();

			if (initialDistance < 0)
			{
				renderCountdown(index, `<div class="h5 text-center text-danger mb-0">EXPIRED</div>`);
				return;
			}

			renderCountdown(index, buildTemplate(initialDistance));

			// Update the count down every 1 second
			let x = setInterval(function() 
			{
				// Get today's date and time
				let now = new Date().getTime();

				// Find the distance between now and the count down date
				let distance = countDownDate - now;

				// If the count down is over, write some text 
				if (distance < 0) 
				{
					clearInterval(x);
					renderCountdown(index, `<div class="h5 text-center text-danger mb-0">EXPIRED</div>`);
					return;
				}

				// Output the result
				renderCountdown(index, buildTemplate(distance));

				// console.log(data);

				// console.log(output);

			}, 1000);

			// return output;
		},

		/**
		 * FIX: Inisialisasi semua countdown dari data-countdown-date attribute.
		 * Dipanggil dari mounted() — saat Vue sudah pasti siap dan DOM sudah ada.
		 * Menggantikan pemanggilan @{{ countDownVue(...) }} dari inline blade
		 * yang menyebabkan jeda karena Vue belum tentu mount saat HTML dirender.
		 */
		initAllCountdowns: function()
		{
			const vm = this;

			// Cari semua elemen countdown yang bukan di dalam clone Splide
			const allTargets = document.querySelectorAll('[data-countdown-idx]');

			// Kumpulkan index unik yang sudah diinisialisasi agar tidak duplikat
			const initiated = new Set();

			allTargets.forEach(function(el)
			{
				const slideParent = el.closest('.splide__slide');

				// Skip clone
				if (slideParent && slideParent.classList.contains('is-clone'))
				{
					return;
				}

				const idx  = el.getAttribute('data-countdown-idx');
				const data = el.getAttribute('data-countdown-date');

				if (!data || initiated.has(idx))
				{
					return;
				}

				initiated.add(idx);

				vm.countDownVue(idx, data);
			});
		}
	},
	mounted()
	{
		// FIX: Jalankan semua countdown setelah Vue mount — tidak ada jeda blank
		// karena Vue sudah pasti siap dan DOM sudah tersedia saat mounted() dipanggil.
		this.initAllCountdowns();

		// console.log("Mounted");
	}
}).mount('#ph-app-coverimage');
