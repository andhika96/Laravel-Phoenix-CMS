const SiteConfigVue3 = createApp(
{
	data() 
	{
		return {
			responseData: '',
			responseDataFont: '',
			responseMessage: '',
			responseMessageFont: '',
			responseMessageAfterSubmit: '',
			responseStatus: '',
			responseStatusFont: '',
			isArrayMessageAfterSubmit: 0,
			showForm:
			{
				offlineReasonForm: false
			},
			fontFamilySelected: '',
			Deselect: h('span', { class: 'fal fa-times fa-lg' }),
		}
	},
	components:
	{
		vSelect: window["vue-select"]
	},
	methods:
	{
		submitData: function(event) 
		{
			event.preventDefault();

			// Get id form submit
			let getIdFormSubmit = document.getElementById("ph-form-config-data");

			// Get value of attribute in HTML.
			let formActionURL = getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("action");
			let formMethod = getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("method");

			// FormData objects are used to capture HTML form and submit it using fetch or another network method.
			let formData = new FormData(this.$refs.formHTML);

			// Disabled for experimental
			// for (const item of this.$refs.formHTML)
			// {
			// 	console.log(item);
			// }

			// Get class button name to change the button to button loading state .
			document.getElementsByClassName("btn-submit-data")[0].insertAdjacentHTML("beforebegin", "<a class=\"btn btn-secondary btn-submit-loading font-size-inherit\">Submitting <div class=\"spinner-border spinner-border-sm text-light ml-1\" role=\"status\"><span class=\"sr-only\">Loading...</span></div></a>");
			document.getElementsByClassName("btn-submit-data")[0].remove();

			axios(
			{
				url: formActionURL,
				method: formMethod,
				data: formData,
				headers: { "Content-Type": "multipart/form-data", 'X-Requested-With': 'XMLHttpRequest' }
			})
			.then(response => 
			{
				// Get status response from JSON for Toast Notice
				this.responseStatus = (response.data.status == 'success') ? 'ph-callout-success' : 'ph-callout-danger';

				if (response.data.status == 'success') 
				{
					if (response.data.message instanceof Object == true ||
						response.data.message instanceof Array == true) 
					{
						this.isArrayMessageAfterSubmit = 1;
					}
					else 
					{
						this.isArrayMessageAfterSubmit = 0;
					}

					this.responseMessageAfterSubmit = response.data.message;

					if ( ! response.data.redirect_url)
					{
						// We use setTimeout to give responseStatus time set status in Toast and then show the Toast
						// base on responStatus
						window.setTimeout(function() 
						{
							// We use toast from Bootstrap 5
							let toastBox = getIdFormSubmit.getElementsByClassName("ph-notice-toast")[0];

							let toast = new bootstrap.Toast(toastBox);

							toast.show();

						}, 1);

						getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].insertAdjacentHTML("beforebegin", "<input type=\"submit\" class=\"btn ph-btn-theme btn-submit-data font-size-inherit\" value=\"Submit\">");
						getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].remove();
					}
					else
					{
						window.setTimeout(function() 
						{
							window.location.href = response.data.redirect_url;

						}, 500);

						// We use toast from Bootstrap 5
						let toastBox = getIdFormSubmit.getElementsByClassName("ph-notice-toast")[0];

						let toast = new bootstrap.Toast(toastBox);

						toast.hide();

						getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].insertAdjacentHTML("beforebegin", "<a class=\"btn btn-success btn-logged font-size-inherit\">Success <i class=\"far fa-check-circle fa-fw mr-1\"></i></div></a>");
						getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].remove();
					}

					// console.log(response.data);
				}
				else if (response.data.status == 'failed') 
				{
					if (response.data.message instanceof Object == true ||
						response.data.message instanceof Array == true) 
					{
						this.isArrayMessageAfterSubmit = 1;
					}
					else 
					{
						this.isArrayMessageAfterSubmit = 0;
					}

					this.responseMessageAfterSubmit = response.data.message;

					// We use setTimeout to give responseStatus time set status in Toast and then show the Toast
					// base on responStatus
					window.setTimeout(function() 
					{
						// We use toast from Bootstrap 5
						let toastBox = getIdFormSubmit.getElementsByClassName("ph-notice-toast")[0];

						let toast = new bootstrap.Toast(toastBox);

						toast.show();

					}, 1);

					getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].insertAdjacentHTML("beforebegin", "<input type=\"submit\" class=\"btn ph-btn-theme btn-submit-data font-size-inherit\" value=\"Submit\">");
					getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].remove();

					// console.log(response.data.message instanceof Object);
				}
			})
			.catch(error => 
			{
				// We set default responStatus to text-bg-danger because absolutely output JSON is error or failed
				this.responseStatus = 'ph-callout-danger';

				if (error.response.data.message instanceof Object == true ||
					error.response.data.message instanceof Array == true) 
				{
					this.isArrayMessageAfterSubmit = 1;
				}
				else 
				{
					this.isArrayMessageAfterSubmit = 0;
				}

				if (error.response !== undefined) 
				{
					this.responseMessageAfterSubmit = error.response.data.message;
				}
				else 
				{
					this.responseMessageAfterSubmit = error.message;
				}

				// We use setTimeout to give responseStatus time set status in Toast and then show the Toast
				// base on responStatus
				window.setTimeout(function() 
				{
					// We use toast from Bootstrap 5
					let toastBox = document.getElementsByClassName("ph-notice-toast")[0];

					let toast = new bootstrap.Toast(toastBox);

					toast.show();

				}, 1);

				document.getElementsByClassName("btn-submit-loading")[0].insertAdjacentHTML("beforebegin", "<input type=\"submit\" class=\"btn ph-btn-theme btn-submit-data\" value=\"Submit\">");
				document.getElementsByClassName("btn-submit-loading")[0].remove();
			});
		},
		listData: async function()
		{
			if (document.querySelector(".ph-fetch-listdata") !== null &&
				document.querySelector(".ph-fetch-listdata").getAttribute("data-url") !== null) 
			{
				const url = document.querySelector(".ph-fetch-listdata").getAttribute("data-url");
				
				await axios.get(url)
				.then(response => 
				{
					this.responseData 		= response.data.data;
					this.responseStatus 	= response.data.status;
					this.responseMessage 	= response.data.message;

					if (this.responseData.offline_mode == 0)
					{
						this.showForm.offlineReasonForm = true;
					}

					console.log(this.responseData.id);
				})
				.catch(function (error) 
				{
					this.responseStatus = response.data.status;
					this.responseMessage = response.data.message;

					// console.log(error.response);
				})
				.finally(() => 
				{
					// Empty
				});
			}
		},
		listDataFont: async function()
		{
			if (document.querySelector(".ph-fetch-listdata-font") !== null &&
				document.querySelector(".ph-fetch-listdata-font").getAttribute("data-url") !== null) 
			{
				const url = document.querySelector(".ph-fetch-listdata-font").getAttribute("data-url");
				
				await axios.get(url)
				.then(response => 
				{
					this.responseDataFont 		= response.data.data;
					this.responseStatusFont 	= response.data.status;
					this.responseMessageFont 	= response.data.message;

					// console.log(this.responseData);
				})
				.catch(function (error) 
				{
					this.responseStatusFont = response.data.status;
					this.responseMessageFont = response.data.message;

					// console.log(error.response);
				})
				.finally(() => 
				{
					// Empty
				});
			}
		},
		loadToastComplete: function()
		{
			// We detect element HTML with class ph-notice in page if there is form with Toast notice
			// we using this because Toast Bootstrap using Vue JS Code to get status Toast, failed or success
			// so default ph-notice is display none and then display block to prevent unrendered element HTML from first open page
			window.setTimeout(function() 
			{	
				if (document.querySelector(".ph-notice") !== null) 
				{
					if (getComputedStyle(document.querySelector('.ph-notice'), null).display == 'none') 
					{
						document.querySelector(".ph-notice").style.display = 'block';

						// console.log('Function loaded');
					}
				}
			}, 1);
		},
		offlineReasonForm: function(event)
		{
			if (event.target.value == 0)
			{
				this.showForm.offlineReasonForm = true;
			}
			else
			{
				this.showForm.offlineReasonForm = false;
			}
		},
		inputOnlyNumber: function(event)
		{
			let charCode = (event.which) ? event.which : event.keyCode;

			if (charCode > 31 && (charCode < 48 || charCode > 57))
			{
				return event.preventDefault();

				// console.log('Pressed');
			}
			else
			{
				if (event.target.textLength == 3)
				{
					// console.log(event.target.textLength);

					return event.preventDefault();
				}

				return true;			
			}
		}
	},
	mounted()
	{
		this.listData();

		this.listDataFont();

		this.loadToastComplete();

		window.setTimeout(function() 
		{
			if (document.querySelector(".ph-box-offline-reason") !== null) 
			{
				if (getComputedStyle(document.querySelector('.ph-box-offline-reason'), null).display == 'none') 
				{
					document.querySelector(".ph-box-offline-reason").style.display = 'block';
				}
			}
		}, 1);
	}
}).mount('#ph-app-site-config');