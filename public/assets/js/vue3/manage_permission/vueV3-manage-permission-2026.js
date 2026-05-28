const ManagePermissionVue3 = createApp(
{
	data()
	{
		return {
			responseData: [],
			responseDataModal: 
			{
				view: [],
				add: [],
				edit: [],
				delete: []
			},
			responseMessage: '',
			responseMessageModal: '',
			responseMessageAfterSubmit: '',
			responseMessageAfterSubmitModal: '',
			responseStatus: '',
			responseStatusToast: 'ph-callout-danger',
			responseStatusModal: '',
			responseStatusAfterSubmit: '',
			responseStatusAfterSubmitModal: '',
			isArrayMessageAfterSubmit: '',
			loadingData: true,
			loadingDataModal: true,
			autoRefreshData: true,
			autoResetForm: true,
			autoLockButton: true,
			customValueButtonCancel: 'Cancel',
			customValueButtonSubmit: 'Submit'
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
			let getIdFormSubmit = document.getElementById("ph-submit-data-permission");

			// Get value of attribute in HTML.
			let formActionURL = getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("action");
			let formMethod = getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("method");

			// FormData objects are used to capture HTML form and submit it using fetch or another network method.
			let formData = new FormData(this.$refs.formHTML);

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

					this.listData();

					this.responseStatusAfterSubmit = true;
					this.responseMessageAfterSubmit = response.data.message;

					// We use toast from Bootstrap 5
					let toastBox = getIdFormSubmit.getElementsByClassName("ph-notice-toast")[0];

					let toast = new bootstrap.Toast(toastBox);

					window.setTimeout(function() 
					{
						toast.show();

					}, 100);

					getIdFormSubmit.getElementsByTagName("form")[0].reset();

					getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].insertAdjacentHTML("beforebegin", "<input type=\"submit\" class=\"btn ph-btn-theme btn-submit-data font-size-inherit\" value=\"Submit\">");
					getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].remove();

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

					this.responseStatusAfterSubmit = false;
					this.responseMessageAfterSubmit = response.data.message;

					// We use toast from Bootstrap 5
					let toastBox = getIdFormSubmit.getElementsByClassName("ph-notice-toast")[0];

					let toast = new bootstrap.Toast(toastBox);
					
					window.setTimeout(function() 
					{
						toast.show();

					}, 100);

					getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].insertAdjacentHTML("beforebegin", "<input type=\"submit\" class=\"btn ph-btn-theme btn-submit-data font-size-inherit\" value=\"Submit\">");
					getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].remove();
				}
			})
			.catch(error => 
			{
				this.responseStatusAfterSubmit = ref(false);

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
					this.responseStatusAfterSubmit = false;
					this.responseMessageAfterSubmit = error.response.data.message;
				}
				else 
				{
					this.responseStatusAfterSubmit = false;
					this.responseMessageAfterSubmit = error.message;
				}

				// We use toast from Bootstrap 5
				let toastBox = getIdFormSubmit.getElementsByClassName("ph-notice-toast")[0];

				let toast = new bootstrap.Toast(toastBox);
				
				window.setTimeout(function() 
				{
					toast.show();

				}, 100);

				getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].insertAdjacentHTML("beforebegin", "<input type=\"submit\" class=\"btn ph-btn-theme btn-submit-data font-size-inherit\" value=\"Submit\">");
				getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].remove();
			});
		},
		submitDataModal: function(event, idSubmit)
		{
			event.preventDefault();

			// Get id form submit
			let getIdFormSubmit = document.getElementById("ph-submit-data-permission-"+idSubmit);

			// Get value of attribute in HTML.
			let formActionURL = getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("action");
			let formMethod = getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("method");

			// FormData objects are used to capture HTML form and submit it using fetch or another network method.
			let formData = new FormData(this.$refs['formHTML-'+idSubmit]);

			// Get auto refresh data option
			if (getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("auto-refresh-data") !== null)
			{
				this.autoRefreshData = getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("auto-refresh-data");
			}

			// Get auto reset form option
			if (getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("auto-reset-form") !== null)
			{
				this.autoResetForm = getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("auto-reset-form");
			}

			// Get custom value button lock
			if (getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("auto-lock-button") !== null)
			{
				this.autoLockButton = getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("auto-lock-button");
			}

			// Get custom value button cancel
			if (getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("cs-value-button-cancel") !== null)
			{
				this.customValueButtonCancel = getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("cs-value-button-cancel");
			}

			// Get custom value button submit
			if (getIdFormSubmit.querySelector('button[type="submit"]') !== null)
			{
				this.customValueButtonSubmit = getIdFormSubmit.querySelector('button[type="submit"]').innerHTML;
			}
			else if (getIdFormSubmit.querySelector('input[type="submit"]') !== null)
			{
				this.customValueButtonSubmit = getIdFormSubmit.querySelector('input[type="submit"]').value;
			}

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

					if (this.autoRefreshData == 'true')
					{
						this.listData();
					}

					this.responseStatusAfterSubmit = true;
					this.responseMessageAfterSubmit = response.data.message;

					// We use toast from Bootstrap 5
					let toastBox = getIdFormSubmit.getElementsByClassName("ph-notice-toast")[0];

					let toast = new bootstrap.Toast(toastBox);

					window.setTimeout(function() 
					{
						toast.show();

					}, 100);

					if (this.autoResetForm == 'true')
					{
						getIdFormSubmit.getElementsByTagName("form")[0].reset();
					}

					if (this.autoLockButton == 'true')
					{
						getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].insertAdjacentHTML("beforebegin", "<a class=\"btn btn-success btn-logged font-size-inherit\">Success <i class=\"far fa-check-circle fa-fw mr-1\"></i></div></a>");
						getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].remove();

						this.closeModalAfterSubmit("ph-submit-data-permission-"+idSubmit, "ph-submit-data-permission-"+idSubmit);
					}
					else if (this.autoLockButton == 'false')
					{
						getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].insertAdjacentHTML("beforebegin", "<button type=\"submit\" class=\"btn ph-btn-theme btn-submit-data font-size-inherit\">"+this.customValueButtonSubmit+"</button>");
						getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].remove();
					}
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

					this.responseStatusAfterSubmit = false;
					this.responseMessageAfterSubmit = response.data.message;

					// We use toast from Bootstrap 5
					let toastBox = getIdFormSubmit.getElementsByClassName("ph-notice-toast")[0];

					let toast = new bootstrap.Toast(toastBox);
					
					window.setTimeout(function() 
					{
						toast.show();

					}, 100);

					getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].insertAdjacentHTML("beforebegin", "<button type=\"submit\" class=\"btn ph-btn-theme btn-submit-data font-size-inherit\">"+this.customValueButtonSubmit+"</button>");
					getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].remove();
				}
			})
			.catch(error => 
			{
				this.responseStatusAfterSubmit = ref(false);

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
					this.responseStatusAfterSubmit = false;
					this.responseMessageAfterSubmit = error.response.data.message;
				}
				else 
				{
					this.responseStatusAfterSubmit = false;
					this.responseMessageAfterSubmit = error.message;
				}

				// We use toast from Bootstrap 5
				let toastBox = getIdFormSubmit.getElementsByClassName("ph-notice-toast")[0];

				let toast = new bootstrap.Toast(toastBox);
				
				window.setTimeout(function() 
				{
					toast.show();

				}, 100);

				getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].insertAdjacentHTML("beforebegin", "<button type=\"submit\" class=\"btn ph-btn-theme btn-submit-data font-size-inherit\">"+this.customValueButtonSubmit+"</button>");
				getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].remove();
			});
		},
		listData: function()
		{
			if (
				document.querySelector(".ar-fetch-listdata-permission") !== null &&
				document.querySelector(".ar-fetch-listdata-permission").getAttribute("data-url") !== null) 
			{
				const url = document.querySelector(".ar-fetch-listdata-permission").getAttribute("data-url");

				let getIdFormSubmit = document.getElementById("ar-fetch-listdata-permission");

				this.loadingData = true;

				axios.get(url)
				.then(response => 
				{
					this.responseData 		= response.data.data;
					this.responseStatus 	= response.data.status;
					this.responseMessage 	= response.data.message;
				})
				.catch(function (error) 
				{
					this.responseStatus 	= response.data.status;
					this.responseMessage 	= response.data.message;
				})
				.finally(() => 
				{	
					this.loadDataComplete("ar-fetch-listdata-permission");

					this.loadToastComplete("ph-submit-data-permission");
				});
			}
		},
		loadToastComplete: function(idSubmit)
		{
			// Get id form submit
			let getIdFormSubmit = document.getElementById(idSubmit);

			if (getIdFormSubmit !== null)
			{
				// We detect element HTML with class ph-notice in page if there is form with Toast notice
				// we using this because Toast Bootstrap using Vue JS Code to get status Toast, failed or success
				// so default ph-notice is display none and then display block to prevent unrendered element HTML from first open page
				window.setTimeout(function() 
				{	
					if (getIdFormSubmit.querySelector(".ph-notice") !== null) 
					{
						if (getComputedStyle(getIdFormSubmit.querySelector('.ph-notice'), null).display == 'none') 
						{
							getIdFormSubmit.querySelector(".ph-notice").style.display = 'block';

							// console.log('Function loaded');
						}
					}
				}, 1);
			}
		},
		loadDataComplete: function(idSubmit) 
		{
			this.loadingData = false;

			if (this.loadingData == false)
			{
				// Get id form submit
				let getIdFormSubmit = document.getElementById(idSubmit);

				if (getIdFormSubmit !== null)
				{
					window.setTimeout(function() 
					{
						if (getIdFormSubmit.querySelector(".ph-data-load-status") !== null) 
						{
							if (getComputedStyle(getIdFormSubmit.querySelector('.ph-data-load-status'), null).display == 'none') 
							{
								getIdFormSubmit.querySelector(".ph-data-load-status").style.display = 'block';
							}
						}

						if (getIdFormSubmit.querySelector(".ph-data-load-content") !== null) 
						{
							if (getComputedStyle(getIdFormSubmit.querySelector('.ph-data-load-content'), null).display == 'none') 
							{
								getIdFormSubmit.querySelector(".ph-data-load-content").style.display = 'block';
							}
						}		
					}, 1);
				}
			}
		},
		detailDataModal: function(modalId, dataId, extendData)
		{
			if (
				document.querySelector(".ar-fetch-detail-data-permission-"+modalId+"-"+dataId) !== null &&
				document.querySelector(".ar-fetch-detail-data-permission-"+modalId+"-"+dataId).getAttribute("data-url") !== null) 
			{
				const url = document.querySelector(".ar-fetch-detail-data-permission-"+modalId+"-"+dataId).getAttribute("data-url");

				axios.get(url+'/'+dataId)
				.then(response => 
				{
					if (response.data.status == 'success')					
					{
						if (modalId == 'viewModal')
						{
							this.responseDataModal.view = response.data.data;
						}
						else if (modalId == 'editModal')
						{
							this.responseDataModal.edit = response.data.data;
						}
						else if (modalId == 'deleteModal')
						{
							this.responseDataModal.delete = response.data.data;
						}
					}

					this.responseStatusModal = response.data.status;
					this.responseMessageModal = response.data.message;

					// console.log(this.responseDataModal.delete);
				})
				.catch(function (error) 
				{
					if (error.response !== undefined)
					{
						this.responseStatusModal = error.response.data.status;
						this.responseMessageModal = error.response.data.message;
					}
					else
					{
						this.responseStatusModal = 'failed';
						this.responseMessageModal = error.message;
					}

					console.log(error);
				})
				.finally(() => 
				{
					this.loadingDataModal = false;

					// console.log(this.responseStatusModal);
				});
			}
		},
		showModal: function(modalId, dataId)
		{
			if (modalId == 'viewModal')
			{
				this.detailDataModal(modalId, dataId, true);
			}
			else if (modalId == 'editModal')
			{
				this.detailDataModal(modalId, dataId, true);
			}
			else if (modalId == 'deleteModal')
			{
				this.detailDataModal(modalId, dataId, true);
			}
		},
		closeModal: function(idSubmit, modalId)
		{
			// Get id form submit
			let getIdFormSubmit = document.getElementById(idSubmit);

			if (getIdFormSubmit !== null)
			{
				// We use toast from Bootstrap 5
				let toastBox = getIdFormSubmit.getElementsByClassName("ph-notice-toast")[0];
				
				let toast = bootstrap.Toast.getOrCreateInstance(toastBox);

				toast.hide();

				if (toast.isShown() == true)
				{
					toastBox.addEventListener('hidden.bs.toast', () => 
					{
						// Hide Modal
						const modalDetail = bootstrap.Modal.getOrCreateInstance(document.getElementById(modalId));

						modalDetail.hide();
					});
				}
				else if (toast.isShown() == false)
				{
					// Hide Modal
					const modalDetail = bootstrap.Modal.getOrCreateInstance(document.getElementById(modalId));

					modalDetail.hide();					
				}

				if (document.querySelector("#"+modalId) !== null)
				{
					let myModalEl = document.getElementById(modalId);

					myModalEl.addEventListener('hidden.bs.modal', function(event) 
					{
						if (getIdFormSubmit.getElementsByClassName("btn-logged")[0] !== undefined)
						{
							getIdFormSubmit.getElementsByClassName("btn-logged")[0].insertAdjacentHTML("beforebegin", "<button type=\"submit\" class=\"btn ph-btn-theme btn-submit-data font-size-inherit\">"+this.customValueButtonSubmit+"</button>");
							getIdFormSubmit.getElementsByClassName("btn-logged")[0].remove();
						}

					}.bind(this));
				}
			}
		},
		closeModalAfterSubmit: function(idSubmit, modalId)
		{
			// Get id form submit
			let getIdFormSubmit = document.getElementById(idSubmit);

			if (getIdFormSubmit !== null)
			{
				// Give toast time to hide before modal close
				window.setTimeout(function() 
				{
					// We use toast from Bootstrap 5
					let toastBox = getIdFormSubmit.getElementsByClassName("ph-notice-toast")[0];
					
					let toast = bootstrap.Toast.getOrCreateInstance(toastBox);

					toast.hide();

					toastBox.addEventListener('hidden.bs.toast', () => 
					{
						// Hide Modal
						const modalDetail = bootstrap.Modal.getOrCreateInstance(document.getElementById(modalId));

						modalDetail.hide();

						if (document.querySelector("#"+modalId) !== null)
						{
							let myModalEl = document.getElementById(modalId);

							myModalEl.addEventListener('hidden.bs.modal', function(event) 
							{
								if (this.autoLockButton == 'true')
								{
									if (getIdFormSubmit.getElementsByClassName("btn-logged")[0] !== undefined)
									{
										getIdFormSubmit.getElementsByClassName("btn-logged")[0].insertAdjacentHTML("beforebegin", "<button type=\"submit\" class=\"btn ph-btn-theme btn-submit-data font-size-inherit\">"+this.customValueButtonSubmit+"</button>");
										getIdFormSubmit.getElementsByClassName("btn-logged")[0].remove();
									}
								}
							}.bind(this));
						}
					});
				}.bind(this), 1300);
			}
		},
	},
	mounted: function()
	{
		this.listData();

		const myModaEditlEl = document.getElementById('ph-submit-data-permission-editModal');

		if (myModaEditlEl !== null)
		{
			myModaEditlEl.addEventListener('hidden.bs.modal', event => 
			{
				this.loadingDataModal = true;
			});
		}
		else
		{
			console.info('ID ph-submit-data-permission-editModal doesn\'t exist');
		}

		const myModaDeletelEl = document.getElementById('ph-submit-data-permission-deleteModal');

		if (myModaDeletelEl !== null)
		{
			myModaDeletelEl.addEventListener('hidden.bs.modal', event => 
			{
				this.loadingDataModal = true;
			});
		}
		else
		{
			console.info('ID ph-submit-data-permission-deleteModal doesn\'t exist');
		}
	}
}).mount('#ph-manage-permission');