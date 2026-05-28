const LaraPhoenixCMSSetupVue3 = createApp(
{
	data()
	{
		return {
			responseData: '',
			responseStatus: '',
			responseMessage: '',
			responseMessageError: '',
			responseStatusToast: '',
			isArrayMessageAfterSubmit: 0,
			customButtonClass: ''
		}
	},
	methods:
	{
		submitData: async function(event) 
		{
			event.preventDefault();

			// Get id form submit
			let getIdFormSubmit = document.getElementById("ph-form-submit-data");

			// Get value of attribute in HTML.
			let formActionURL = getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("action");
			let formMethod = getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("method");

			// FormData objects are used to capture HTML form and submit it using fetch or another network method.
			let formData = new FormData(this.$refs.formHTML);

			console.log(formData);

			// Custom class button
			if (getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("custom-button-class") !== null)
			{
				this.customButtonClass = getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("custom-button-class");
			}

			// Get class button name to change the button to button loading state .
			document.getElementsByClassName("btn-submit-data")[0].insertAdjacentHTML("beforebegin", "<a class=\"btn btn-secondary btn-submit-loading "+this.customButtonClass+"\">Waiting...</a>");
			document.getElementsByClassName("btn-submit-data")[0].remove();

			await axios(
			{
				url: formActionURL,
				method: formMethod,
				data: formData,
				headers: { "Accept": "text/event-stream", "Content-Type": "multipart/form-data", 'X-Requested-With': 'XMLHttpRequest' },
				responseType: 'stream',
				adapter: 'fetch'
			})
			.then(async (response) => 
			{
				// console.log('Submitted!');

				// this.responseMessage = response.data;
				const stream = response.data;
				const ctrl = new AbortController();

				// consume response
				const reader = stream.pipeThrough(new TextDecoderStream()).getReader();

				while (true) 
				{
					const { value, done } = await reader.read();
					
					this.responseMessage = '<div class=\"spinner-border spinner-border-sm text-primary\" role=\"status\"><span class=\"sr-only\">Loading...</span></div> <span class="ms-1">'+value+'</span>';

					if (done === true)
					{
						document.getElementsByClassName("btn-submit-loading")[0].insertAdjacentHTML("beforebegin", "<a class=\"btn btn-success btn-logged "+this.customButtonClass+"\">Redirecting...</div></a>");
						document.getElementsByClassName("btn-submit-loading")[0].remove();

						this.responseMessageError = '';
						this.responseMessage = '<span class=\"text-success\"><i class=\"far fa-check-circle fa-lg fa-fw me-1\"></i> CMS successfully installed!';
						
						break;
					}

					if (value.includes('SQLSTATE'))
					{
						this.responseMessageError = value;
						this.responseMessage = '<span class=\"text-danger\"><i class=\"far fa-times-circle fa-lg fa-fw me-1\"></i> CMS cannot be installed!';

						document.getElementsByClassName("btn-submit-loading")[0].insertAdjacentHTML("beforebegin", "<input type=\"submit\" class=\"btn btn-larapx btn-submit-data "+this.customButtonClass+"\" value=\"Install\">");
						document.getElementsByClassName("btn-submit-loading")[0].remove();

						ctrl.abort(); 
						break;
					}
				}
			})
			.catch(async (error) => 
			{
				const stream = error.response.data;
				const ctrl = new AbortController();

				// consume response
				const reader = stream.pipeThrough(new TextDecoderStream()).getReader();

				while (true) 
				{
					const { value, done } = await reader.read();

					if (done === false)
					{
						this.responseMessageError = value;

						// console.log(value);

						break;
					}
				}
				
				this.responseMessage = '<span class=\"text-danger\"><i class=\"far fa-times-circle fa-lg fa-fw me-1\"></i> CMS cannot be installed!';

				document.getElementsByClassName("btn-submit-loading")[0].insertAdjacentHTML("beforebegin", "<input type=\"submit\" class=\"btn btn-larapx btn-submit-data "+this.customButtonClass+"\" value=\"Install\">");
				document.getElementsByClassName("btn-submit-loading")[0].remove();

				// console.log(error);
			});
		}
	},
	mounted()
	{
		// Empty
	}
}).mount('#ph-app-setup');