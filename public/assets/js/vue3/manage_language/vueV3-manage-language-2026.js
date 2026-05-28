const ListDataLanguageVue3 = createApp(
{
	data() 
	{
		return {
			reponseData: '',
			responseDataEditable: [],
			responseDataEditableStatus: [],
			responseDetailData: '',
			responseMessage: '',
			responseDetailMessage: '',
			responseMessageAfterSubmit: '',
			responseStatus: '',
			responseDetailStatus: '',
			responseStatusToast: 'text-bg-danger',
			isArrayMessageAfterSubmit: 0,
			getTotalData: '',
			getPage: 1,
			getCurrentPage: 1,
			loading: '',
			loadingDetail: '',
			loadingNextPage: '',
			pageUrl: '',
			pageCount: '',
			pageRange: '',
			autoRefresh: 'false'
		}
	},
	components:
	{
		paginate: VuejsPaginateNext
	},
	methods:
	{
		submitDataLanguage: function(event, idSubmit) 
		{
			event.preventDefault();

			// Get id form submit
			let getIdFormSubmit = document.getElementById("ph-form-submit-data-"+idSubmit);

			// Get value of attribute in HTML.
			let formActionURL = getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("action");
			let formMethod = getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("method");

			// FormData objects are used to capture HTML form and submit it using fetch or another network method.
			let formData = new FormData(this.$refs['formHTML'+idSubmit]);

			// Auto refresh after submit data
			this.autoRefresh = getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("auto-refresh");

			// Disabled for experimental
			// for (const item of this.$refs.formHTML)
			// {
			// 	console.log(item);
			// }

			// Get class button name to change the button to button loading state .
			getIdFormSubmit.getElementsByClassName("btn-submit-data")[0].insertAdjacentHTML("beforebegin", "<a class=\"btn btn-secondary btn-submit-loading font-size-inherit\">Submitting <div class=\"spinner-border spinner-border-sm text-light ml-1\" role=\"status\"><span class=\"sr-only\">Loading...</span></div></a>");
			getIdFormSubmit.getElementsByClassName("btn-submit-data")[0].remove();

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
							toast.show()
						}, 1);

						for (var i = 0; i < this.responseDataEditable.length; i++) 
						{
							if (this.responseDataEditable[i] !== undefined)
							{
								this.responseDataEditable[i] = 'view';
								this.responseDataEditableStatus[i] = 'success';
								
								// console.log(this.responseDataEditable[i]);
							}
						}

						// console.log(this.responseDataEditable[14]+' asd');

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
						toast.show()
					}, 1);

					getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].insertAdjacentHTML("beforebegin", "<input type=\"submit\" class=\"btn ph-btn-theme btn-submit-data font-size-inherit\" value=\"Submit\">");
					getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].remove();

					// console.log(response.data.message instanceof Object);
				}
			})
			.catch(error => 
			{
				// We set default responStatus to text-bg-danger because absolutely output JSON is error or failed
				this.responseStatusToast = 'ph-callout-danger';

				if (error.response !== undefined) 
				{
					if (error.response.data.message instanceof Object == true ||
						error.response.data.message instanceof Array == true) 
					{
						this.isArrayMessageAfterSubmit = 1;
					}
					else {
						this.isArrayMessageAfterSubmit = 0;
					}
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
					let toastBox = getIdFormSubmit.getElementsByClassName("ph-notice-toast")[0];

					let toast = new bootstrap.Toast(toastBox);
					toast.show()
				}, 1);

				// console.log(getIdFormSubmit.getElementsByClassName("ph-notice-toast")[0]);

				getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].insertAdjacentHTML("beforebegin", "<input type=\"submit\" class=\"btn ph-btn-theme btn-submit-data\" value=\"Submit\">");
				getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].remove();
			});
		},
		listDataLanguage: async function(event)
		{
			if (document.querySelector(".ph-fetch-listdata") !== null &&
				document.querySelector(".ph-fetch-listdata").getAttribute("data-url") !== null) 
			{
				const url = document.querySelector(".ph-fetch-listdata").getAttribute("data-url");

				if (event == 'true')
				{
					this.loading = true;
					this.loadingNextPage = true;
				}

				await axios.get(url)
				.then(response => 
				{
					this.responseData 		= response.data.data;
					
					if (event == 'false')
					{
						this.responseStatus 	= response.data.status;
						this.responseMessage 	= response.data.message;
					}

					this.getTotalData 	= response.data.total;
					this.pageCount 		= response.data.total_page;
					this.pageRange 		= response.data.limit;

					// console.log(this.pageRange);
				})
				.catch(function (error) 
				{
					if (event == 'false')
					{
						this.responseStatus 	= response.data.status;
						this.responseMessage 	= response.data.message;
					}

					// console.log(error.response);
				})
				.finally(() => 
				{
					this.loadDataComplete();

					this.loadToastComplete();
				});
			}
		},
		loadDataComplete: function() 
		{
			this.loading = false;
			this.loadingNextPage = false;

			window.setTimeout(function() 
			{
				if (document.querySelector(".ph-data-load-status") !== null) 
				{
					if (getComputedStyle(document.querySelector('.ph-data-load-status'), null).display == 'none') 
					{
						document.querySelector(".ph-data-load-status").style.display = 'block';
					}
				}

				if (document.querySelector(".ph-data-load-content") !== null) 
				{
					if (getComputedStyle(document.querySelector('.ph-data-load-content'), null).display == 'none') 
					{
						document.querySelector(".ph-data-load-content").style.display = 'block';
					}
				}
			}, 1);
		},
		loadToastComplete: function(idSubmit)
		{
			// Get id form submit
			// let getIdFormSubmit = document.getElementById(idSubmit);

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
		clickPaginate: async function (page) 
		{
			if (document.querySelector(".ph-fetch-listdata") !== null &&
				document.querySelector(".ph-fetch-listdata").getAttribute("data-url") !== null) 
			{
				const url = document.querySelector(".ph-fetch-listdata").getAttribute("data-url");

				let params = (new URL(url)).searchParams;

				if (params.toString() !== '')
				{
					if (page == 1)
					{
						this.pageUrl = '?'+params.toString();
					}
					else
					{
						this.pageUrl = '?'+params.toString()+'&page='+page;
					}
				}
				else
				{
					if (page == 1)
					{
						this.pageUrl = '';
					}
					else
					{
						this.pageUrl = '?page='+page;
					}
				}

				this.getPage = page;
				this.loadingNextPage = true;

				await axios.get(url+this.pageUrl)
				.then(response => 
				{
					if (this.getCurrentPage >= this.pageCount) 
					{
						this.getCurrentPage = '';
					}

					this.responseData 	= response.data.data;
					this.getTotalData 	= response.data.total;
					this.pageCount 		= response.data.total_page;
					this.pageRange 		= response.data.limit;

					// document.querySelector("#dataIndex").scrollIntoView(true);
				})
				.catch(function (error) 
				{
					this.responseMessage = error;
					
					// console.log(error);
				})
				.finally(() => 
				{
					this.loadToastComplete();

					this.loading = false;
					this.loadingNextPage = false;
				});
			}
		},
		editLanguage: function(event, index)
		{
			this.responseDataEditable[index] = 'editable';

			console.log(this.responseDataEditable[index]);
		},
		cancelEditLanguage: function(event, index)
		{
			this.responseDataEditable[index] = 'view';

			console.log(this.responseDataEditable[index]);
		},
		resizeTextarea: function(index)
		{
			let element = this.$refs['textarea_'+index][0];

			element.style.height = "20px";
			element.style.height = element.scrollHeight + "px";
		}
	},
	mounted()
	{
		this.listDataLanguage('false');

		// this.loadToastComplete();
	}
}).mount('#ph-app-data-language');