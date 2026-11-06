const AuthVue3 = createApp(
{
	data() 
	{
		return {
			reponseData: '',
			responseDetailData: 
			{
				email: '',
				username: '',
				fullname: '',
				password: '',
				status: '',
				roles: [ { name: '' } ],
			},
			responseCheckData: 
			{
				email: [],
				username: []
			},
			responseMessage: '',
			responseMessageAfterSubmit: '',
			responseMessageDetailData: '',
			responseMessageCheckData: 
			{
				email: '',
				username: ''
			},
			responseStatus: '',
			responseStatusToast: '',
			responseStatusDetailData: '',
			responseStatusCheckData: 
			{
				email: '',
				username: ''
			},
			getCurrentUserData:
			{
				email: '',
				username: ''
			},
			getQueryCheckData: 
			{
				email: '',
				username: ''
			},
			loadingDetailData: '',
			loadingCheckData: 
			{
				email: '',
				username: ''
			},
			isArrayMessageAfterSubmit: 0,
			customButtonClass: ''
		}
	},
	methods:
	{
		login: function(event) 
		{
			event.preventDefault();

			// Get id form submit
			let getIdFormSubmit = document.getElementById("ph-form-login-submit");

			// Get value of attribute in HTML.
			let formActionURL = getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("action");
			let formMethod = getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("method");

			// FormData objects are used to capture HTML form and submit it using fetch or another network method.
			let formData = new FormData(this.$refs.formHTML);

			// Custom class button
			if (getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("custom-button-class") !== null)
			{
				this.customButtonClass = getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("custom-button-class");
			}

			// Disabled for experimental
			// for (const item of this.$refs.formHTML)
			// {
			// 	console.log(item);
			// }

			// Get class button name to change the button to button loading state .
			document.getElementsByClassName("btn-submit-login")[0].insertAdjacentHTML("beforebegin", "<a class=\"btn btn-secondary btn-submit-loading "+this.customButtonClass+" p-2 w-100\">Submitting <div class=\"spinner-border spinner-border-sm text-light ml-1\" role=\"status\"><span class=\"sr-only\">Loading...</span></div></a>");
			document.getElementsByClassName("btn-submit-login")[0].remove();

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
				this.responseStatusToast = (response.data.status == 'success') ? 'ph-callout-success' : 'ph-callout-danger';

				if (response.data.status == 'success') 
				{
					window.setTimeout(function() 
					{
						window.location.href = response.data.redirect_url;

					}, 500);

					// We use toast from Bootstrap 5
					let toastBox = document.getElementsByClassName("ph-notice-toast")[0];

					let toast = new bootstrap.Toast(toastBox);

					toast.hide();

					document.getElementsByClassName("btn-submit-loading")[0].insertAdjacentHTML("beforebegin", "<a class=\"btn btn-success btn-logged "+this.customButtonClass+" p-2 w-100\">Success <i class=\"far fa-check-circle fa-fw mr-1\"></i></div></a>");
					document.getElementsByClassName("btn-submit-loading")[0].remove();

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
						let toastBox = document.getElementsByClassName("ph-notice-toast")[0];

						let toast = new bootstrap.Toast(toastBox);

						toast.show();

					}, 1);

					document.getElementsByClassName("btn-submit-loading")[0].insertAdjacentHTML("beforebegin", "<input type=\"submit\" class=\"btn btn-larapx btn-submit-login "+this.customButtonClass+" p-2 w-100\" value=\"Log In\">");
					document.getElementsByClassName("btn-submit-loading")[0].remove();

					// console.log(response.data.message instanceof Object);
				}
			})
			.catch(error => 
			{
				// We set default responStatus to text-bg-danger because absolutely output JSON is error or failed
				this.responseStatusToast = 'ph-callout-danger';
				
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
				
				document.getElementsByClassName("btn-submit-loading")[0].insertAdjacentHTML("beforebegin", "<input type=\"submit\" class=\"btn btn-larapx btn-submit-login "+this.customButtonClass+" p-2 w-100\" value=\"Log In\">");
				document.getElementsByClassName("btn-submit-loading")[0].remove();
			});

			this.reloadTokenRECAPTCHA(reCaptchaSiteKey, 'login');
		},
		signup: function(event) 
		{
			event.preventDefault();

			// Get id form submit
			let getIdFormSubmit = document.getElementById("ph-form-signup-submit");

			// Get value of attribute in HTML.
			let formActionURL = getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("action");
			let formMethod = getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("method");

			// FormData objects are used to capture HTML form and submit it using fetch or another network method.
			let formData = new FormData(this.$refs.formHTML);

			// Custom class button
			if (getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("custom-button-class") !== null)
			{
				this.customButtonClass = getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("custom-button-class");
			}

			// Disabled for experimental
			// for (const item of this.$refs.formHTML)
			// {
			// 	console.log(item);
			// }

			// Get class button name to change the button to button loading state .
			document.getElementsByClassName("btn-submit-signup")[0].insertAdjacentHTML("beforebegin", "<a class=\"btn btn-secondary btn-submit-loading "+this.customButtonClass+" p-2 w-100\">Submitting <div class=\"spinner-border spinner-border-sm text-light ml-1\" role=\"status\"><span class=\"sr-only\">Loading...</span></div></a>");
			document.getElementsByClassName("btn-submit-signup")[0].remove();

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
				this.responseStatusToast = (response.data.status == 'success') ? 'ph-callout-success' : 'ph-callout-danger';

				if (response.data.status == 'success') 
				{
					window.setTimeout(function() 
					{
						window.location.href = response.data.redirect_url;

					}, 500);

					// We use toast from Bootstrap 5
					let toastBox = document.getElementsByClassName("ph-notice-toast")[0];

					let toast = new bootstrap.Toast(toastBox);

					toast.hide();

					document.getElementsByClassName("btn-submit-loading")[0].insertAdjacentHTML("beforebegin", "<a class=\"btn btn-success btn-logged "+this.customButtonClass+" p-2 w-100\">Success <i class=\"far fa-check-circle fa-fw mr-1\"></i></div></a>");
					document.getElementsByClassName("btn-submit-loading")[0].remove();

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
						let toastBox = document.getElementsByClassName("ph-notice-toast")[0];

						let toast = new bootstrap.Toast(toastBox);

						toast.show();

					}, 1);

					document.getElementsByClassName("btn-submit-loading")[0].insertAdjacentHTML("beforebegin", "<input type=\"submit\" class=\"btn btn-larapx btn-submit-signup "+this.customButtonClass+" p-2 w-100\" value=\"Log In\">");
					document.getElementsByClassName("btn-submit-loading")[0].remove();

					// console.log(response.data.message instanceof Object);
				}
			})
			.catch(error => 
			{
				// We set default responStatus to text-bg-danger because absolutely output JSON is error or failed
				this.responseStatusToast = 'ph-callout-danger';

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

				document.getElementsByClassName("btn-submit-loading")[0].insertAdjacentHTML("beforebegin", "<input type=\"submit\" class=\"btn btn-larapx btn-submit-signup "+this.customButtonClass+" p-2 w-100\" value=\"Log In\">");
				document.getElementsByClassName("btn-submit-loading")[0].remove();
			});

			this.reloadTokenRECAPTCHA(reCaptchaSiteKey, 'signup');
		},
		recoveryPassword: function(event) 
		{
			event.preventDefault();

			// Get id form submit
			let getIdFormSubmit = document.getElementById("ph-form-recovery-password-submit");

			// Get value of attribute in HTML.
			let formActionURL = getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("action");
			let formMethod = getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("method");

			// FormData objects are used to capture HTML form and submit it using fetch or another network method.
			let formData = new FormData(this.$refs.formHTML);

			// Custom class button
			if (getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("custom-button-class") !== null)
			{
				this.customButtonClass = getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("custom-button-class");
			}

			// Disabled for experimental
			// for (const item of this.$refs.formHTML)
			// {
			// 	console.log(item);
			// }

			// Get class button name to change the button to button loading state .
			document.getElementsByClassName("btn-submit-recovery-password")[0].insertAdjacentHTML("beforebegin", "<a class=\"btn btn-secondary btn-submit-loading "+this.customButtonClass+" p-2 w-100\">Submitting <div class=\"spinner-border spinner-border-sm text-light ml-1\" role=\"status\"><span class=\"sr-only\">Loading...</span></div></a>");
			document.getElementsByClassName("btn-submit-recovery-password")[0].remove();

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
				this.responseStatusToast = (response.data.status == 'success') ? 'ph-callout-success' : 'ph-callout-danger';

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

						getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].insertAdjacentHTML("beforebegin", "<input type=\"submit\" class=\"btn btn-larapx btn-submit-recovery-password "+this.customButtonClass+" p-2 w-100\" value=\"Submit\">");
						getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].remove();
					}
					else
					{
						window.setTimeout(function() 
						{
							window.location.href = response.data.redirect_url;

						}, 500);

						// We use setTimeout to give responseStatus time set status in Toast and then show the Toast
						// base on responStatus
						window.setTimeout(function() 
						{
							// We use toast from Bootstrap 5
							let toastBox = document.getElementsByClassName("ph-notice-toast")[0];

							let toast = new bootstrap.Toast(toastBox);

							toast.hide();

						}, 1);

						document.getElementsByClassName("btn-submit-loading")[0].insertAdjacentHTML("beforebegin", "<a class=\"btn btn-success btn-logged "+this.customButtonClass+" p-2 w-100\">Success <i class=\"far fa-check-circle fa-fw mr-1\"></i></div></a>");
						document.getElementsByClassName("btn-submit-loading")[0].remove();
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
						let toastBox = document.getElementsByClassName("ph-notice-toast")[0];

						let toast = new bootstrap.Toast(toastBox);

						toast.show();
					
					}, 1);

					document.getElementsByClassName("btn-submit-loading")[0].insertAdjacentHTML("beforebegin", "<input type=\"submit\" class=\"btn btn-larapx btn-submit-recovery-password "+this.customButtonClass+" p-2 w-100\" value=\"Submit\">");
					document.getElementsByClassName("btn-submit-loading")[0].remove();

					// console.log(response.data.message instanceof Object);
				}

				console.log(this.customButtonClass);
			})
			.catch(error => 
			{
				// We set default responStatus to text-bg-danger because absolutely output JSON is error or failed
				this.responseStatusToast = 'ph-callout-danger';
				
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

				document.getElementsByClassName("btn-submit-loading")[0].insertAdjacentHTML("beforebegin", "<input type=\"submit\" class=\"btn btn-larapx btn-submit-recovery-password "+this.customButtonClass+" p-2 w-100\" value=\"Submit\">");
				document.getElementsByClassName("btn-submit-loading")[0].remove();
			});
		},
		checkData: _.debounce(function(checkType) 
		{
			const getQueryCheckDataEmail = (this.getQueryCheckData.email !== '') ? this.getQueryCheckData.email.trim() : this.responseDetailData.email.trim();
			const getQueryCheckDataUsername = (this.getQueryCheckData.username !== '') ? this.getQueryCheckData.username.trim() : this.responseDetailData.username.trim();

			if (document.querySelector(".ph-fetch-checkdata-"+checkType) !== null &&
				document.querySelector(".ph-fetch-checkdata-"+checkType).getAttribute("data-url") !== null) 
			{
				const url = document.querySelector(".ph-fetch-checkdata-"+checkType).getAttribute("data-url");
				
				let urlWithQuery = '';

				if (checkType == 'email')
				{
					this.loadingCheckData.email = true;

					urlWithQuery = url+'?checkType=email&email='+getQueryCheckDataEmail;
				}
				else if (checkType == 'username')
				{
					this.loadingCheckData.username = true;

					urlWithQuery = url+'?checkType=username&username='+getQueryCheckDataUsername;
				}

				axios.get(urlWithQuery)
				.then(response => 
				{
					if (checkType == 'email')
					{
						// this.responseCheckData.email 		= response.data.data;
						this.responseStatusCheckData.email 	= response.data.status;
						this.responseMessageCheckData.email = response.data.message;

						// console.log('checkType: email');
						// console.log(this.responseMessageCheckData.email);
					}
					else if (checkType == 'username')
					{
						// this.responseCheckData.username 		= response.data.data;
						this.responseStatusCheckData.username 	= response.data.status;
						this.responseMessageCheckData.username 	= response.data.message;

						// console.log('checkType: username');
						// console.log(this.responseMessageCheckData.username);
					}

					// console.log(this.responseStatus);
				})
				.catch(function (error) 
				{
					if (checkType == 'email')
					{
						this.responseStatusCheckData.email 	= response.data.status;
						this.responseMessageCheckData.email = response.data.message;
					}
					else if (checkType == 'username')
					{
						this.responseStatusCheckData.username 	= response.data.status;
						this.responseMessageCheckData.username 	= response.data.message;
					}
				})
				.finally(() => 
				{
					if (checkType == 'email')
					{
						this.loadingCheckData.email = false;
					}
					else if (checkType == 'username')
					{
						this.loadingCheckData.username = false;
					}
				});
			}
		}, 500),
		focusForm: function(event)
		{
			if (event.target.parentElement.classList.contains('input-group') == true)
			{
				event.target.parentElement.classList.add('input-group-focus');
			}

			if (event.target.parentElement.classList.contains('input-group-focus-is-valid') == true)
			{
				event.target.parentElement.classList.add('is-valid');

				// console.log('Focused');	
			}

			if (event.target.parentElement.classList.contains('input-group-focus-is-invalid') == true)
			{
				event.target.parentElement.classList.add('is-invalid');

				// console.log('Focused');	
			}

			console.log(event.target.parentElement.classList.contains('input-group-focus-is-invalid'));
		},
		blurForm: function()
		{
			if (event.target.parentElement.classList.contains('input-group') == true)
			{
				event.target.parentElement.classList.remove('input-group-focus');
			}

			if (event.target.parentElement.classList.contains('input-group-focus-is-valid') == true)
			{
				event.target.parentElement.classList.remove('is-valid');

				// console.log('Blured');	
			}

			if (event.target.parentElement.classList.contains('input-group-focus-is-invalid') == true)
			{
				event.target.parentElement.classList.remove('is-invalid');

				// console.log('Blured');	
			}
		},
		reloadTokenRECAPTCHA: function(siteKey, actionType)
		{
			if (typeof grecaptcha !== 'undefined')
			{
				grecaptcha.ready(function() 
				{
					grecaptcha.execute(siteKey, {action: actionType}).then(function(token) 
					{
						// Add your logic to submit to your backend server here.
						document.querySelector('input[name="g-recaptcha-response"]').value = token;
					});
				});
			}
		}
	},
	mounted()
	{
		let inputFormDefaultView = document.querySelectorAll("input.form-control-default-view");

		for (var i = 0; i < inputFormDefaultView.length; i++) 
		{
			inputFormDefaultView[i].addEventListener("focus", function(event)
			{
				event.target.parentElement.classList.toggle('input-group-focus');
			});

			inputFormDefaultView[i].addEventListener("blur", function(event)
			{
				event.target.parentElement.classList.toggle('input-group-focus');
			});
		};
	}
}).mount('#ph-app-auth');