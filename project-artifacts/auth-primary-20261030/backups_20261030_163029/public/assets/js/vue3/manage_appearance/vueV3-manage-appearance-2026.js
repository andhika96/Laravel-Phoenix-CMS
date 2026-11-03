const AppearanceConfigVue3 = createApp(
{
	data() 
	{
		return {
			responseData: '',
			responseDataMultiple: 
			{
				login: '',
				signup: '',
				forgotPassword: '',
				resetPassword: ''				
			},
			responseMessage: '',
			responseMessageMultiple: 
			{
				login: '',
				signup: '',
				forgotPassword: '',
				resetPassword: ''				
			},
			responseMessageAfterSubmit: '',
			responseStatus: '',
			responseStatusMultiple: 
			{
				login: '',
				signup: '',
				forgotPassword: '',
				resetPassword: ''				
			},
			isArrayMessageAfterSubmit: 0,
			listAppearance:
			[
				'login',
				'signup',
				'forgotPassword',
				'resetPassword'
			],
			loadDataAppearance:
			{
				login: true,
				signup: true,
				forgotPassword: true,
				resetPassword: true
			},
			selectedCard:
			{
				login: '',
				signup: '',
				forgotPassword: '',
				resetPassword: ''
			},
			selectedColorNuances: 
			{
				login: '',
				signup: '',
				forgotPassword: '',
				resetPassword: ''
			},
			isWithColorNuances: 
			{
				login: '',
				signup: '',
				forgotPassword: '',
				resetPassword: ''
			},
		}
	},
	methods:
	{
		submitData: function(event) 
		{
			event.preventDefault();

			// Get id form submit
			let getIdFormSubmit = document.getElementById("ph-form-config-appearance");

			// Get value of attribute in HTML.
			let formActionURL = getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("action");
			let formMethod = getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("method");

			// FormData objects are used to capture HTML form and submit it using fetch or another network method.
			let formData = new FormData(this.$refs.formHTML);

			formData.append('interface_theme', JSON.stringify(this.selectedCard));
			formData.append('interface_theme_options', JSON.stringify(this.selectedColorNuances));

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

					// console.log(this.responseData);
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
		listDataMultiple: async function(key)
		{
			if (document.querySelector(".ph-fetch-listdata-"+key) !== null &&
				document.querySelector(".ph-fetch-listdata-"+key).getAttribute("data-url") !== null) 
			{
				const url = document.querySelector(".ph-fetch-listdata-"+key).getAttribute("data-url");
				
				await axios.get(url)
				.then(response => 
				{
					if (response.data.status == undefined)
					{
						this.responseDataMultiple[key] = response.data;
						this.selectedColorNuances[key] = this.responseDataMultiple[key]['page_color_nuances'];
					}
					else
					{
						this.responseStatusMultiple[key] 	= response.data.status;
						this.responseMessageMultiple[key] 	= response.data.message;
					}
				})
				.catch(function(error) 
				{
					console.log(error.response);
				})
				.finally(() => 
				{
					this.loadDataAppearance[key] = false;

					window.setTimeout(function() 
					{
						this.initiateColorisPlugin();

						this.selectColorNuancesPage(key);

					}.bind(this), 1);
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
		initiateColorisPlugin: function()
		{
			// Initiate coloris class plugin
			if (document.querySelector(".coloris") !== null)
			{
				Coloris(
				{
					el: '.coloris',
				});
			}
		},
		selectAppearance: function(key, value)
		{
			// console.log(value);

			/**
			 * Loop through all radio cards, and remove the class "selected" from those elements.
			 */
			const allRadioCards = document.querySelectorAll(".radio-card-"+key);
			allRadioCards.forEach((element, index) => 
			{
				element.classList.remove(["selected"]);
			});

			/**
			 * Add the class "selected" to the card which user has clicked on.
			 */
			const selectedCard = document.querySelector(".radio-card-"+key+"-"+value);
			selectedCard.classList.add(["selected"]);

			const ActiveColorisForm = document.getElementsByClassName("arv7-color-"+key+"-page")[0];
			const ActiveColorNuances = selectedCard.getElementsByClassName("is_active_color_nuances")[0];
			const withColorNuances = ActiveColorNuances.getAttribute("is-with-color-nuances");

			if (withColorNuances == 0)
			{
				this.isWithColorNuances[key] = 0;

				ActiveColorisForm.removeAttribute("disabled");
			}
			else if (withColorNuances == 1)
			{
				this.isWithColorNuances[key] = 1;

				ActiveColorisForm.setAttribute("disabled", true);
			}

			console.log(this.isWithColorNuances[key]);

			this.selectedCard[key] = value;
		},
		selectColorNuancesPage: function(key)
		{
			this.initiateColorisPlugin();

			if (document.querySelector(".arv7-color-"+key+"-page") !== null)
			{
				Coloris.setInstance(".arv7-color-"+key+"-page",
				{
					theme: 'pill',
					formatToggle: true,
					closeButton: true,
					clearButton: true,
				});
			}
		},
		lowerCasedString: function(value)
		{
			let lowercasedString = value.toLowerCase();

			return lowercasedString;
		}
	},
	mounted()
	{
		this.listData();

		this.loadToastComplete();

		this.listAppearance.forEach((element, index) => 
		{
			this.listDataMultiple(element);
		});

		// Set default active color nuances value for options
		const selectedCardList = document.querySelectorAll(".selected_class");

		for (var i = 0; i < selectedCardList.length; i++) 
		{
			const getClassName = document.getElementsByClassName(selectedCardList[i].getAttribute("selected-class"))[0];
			const getClassNameUri = document.getElementsByClassName("selected_class_uri")[i].getAttribute("selected-class-uri");
			
			const ActiveColorNuances = getClassName.getElementsByClassName("is_active_color_nuances")[0];
			const withColorNuances = ActiveColorNuances.getAttribute("is-with-color-nuances");

			if (withColorNuances == 0)
			{
				this.isWithColorNuances[getClassNameUri] = 0;
			}
			else if (withColorNuances == 1)
			{
				this.isWithColorNuances[getClassNameUri] = 1;
			}
		}
	}
}).mount('#ph-app-appearance-config');