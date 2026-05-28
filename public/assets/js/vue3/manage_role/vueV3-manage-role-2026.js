const ManageRoleVue3 = createApp(
{
	data()
	{
		return {
			responseData: [],
			responseDataPermissions: [],
			responseDataModal: 
			{
				create: [],
				read: [],
				update: [],
				delete: [],
			},
			responseDetailDataPermission: [],
			responseDetailDataPermissionModal: [],
			responseStatus: '',
			responseStatusModal: '',
			responseStatusToast: 'ph-callout-danger',
			responseStatusPermission: '',
			responseStatusAfterSubmit: '',
			responseMessage: '',
			responseMessageModal: '',
			responseMessagePermission: '',
			responseMessageAfterSubmit: '',
			loadingData: true,
			loadingDataModal: true,
			loadingNextPage: false,
			autoRefreshData: true,
			autoResetForm: true,
			autoLockButton: true,
			customValueButtonCancel: 'Cancel',
			customValueButtonSubmit: 'Submit',
			getTotalChecked: 0,
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
			let getIdFormSubmit = document.getElementById("ph-submit-data-role");

			// Get value of attribute in HTML.
			let formActionURL = getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("action");
			let formMethod = getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("method");

			// FormData objects are used to capture HTML form and submit it using fetch or another network method.
			let formData = new FormData(this.$refs.formHTML);

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

					window.setTimeout(function() 
					{
						// We use toast from Bootstrap 5
						let toastBox = getIdFormSubmit.getElementsByClassName("ph-notice-toast")[0];

						let toast = new bootstrap.Toast(toastBox);

						toast.show();

					}, 1);

					if (this.autoResetForm == 'true')
					{
						getIdFormSubmit.getElementsByTagName("form")[0].reset();
					}

					getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].insertAdjacentHTML("beforebegin", "<input type=\"submit\" class=\"btn ph-btn-theme btn-submit-data font-size-inherit\" value="+this.customValueButtonSubmit+">");
					getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].remove();
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

					window.setTimeout(function() 
					{
						// We use toast from Bootstrap 5
						let toastBox = getIdFormSubmit.getElementsByClassName("ph-notice-toast")[0];

						let toast = new bootstrap.Toast(toastBox);

						toast.show();

					}, 1);

					getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].insertAdjacentHTML("beforebegin", "<input type=\"submit\" class=\"btn ph-btn-theme btn-submit-data font-size-inherit\" value="+this.customValueButtonSubmit+">");
					getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].remove();
				}
			})
			.catch(error => 
			{
				// We set default responStatus to text-bg-danger because absolutely output JSON is error or failed
				this.responseStatusToast = 'ph-callout-danger';

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
					this.responseMessageAfterSubmit = error.response.data.message;
				}
				else 
				{
					this.responseMessageAfterSubmit = error.message;
				}
				
				window.setTimeout(function() 
				{
					// We use toast from Bootstrap 5
					let toastBox = getIdFormSubmit.getElementsByClassName("ph-notice-toast")[0];

					let toast = new bootstrap.Toast(toastBox);

					toast.show();

				}, 1);

				getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].insertAdjacentHTML("beforebegin", "<input type=\"submit\" class=\"btn ph-btn-theme btn-submit-data font-size-inherit\" value="+this.customValueButtonSubmit+">");
				getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].remove();
			});
		},
		submitDataModal: function(event, idSubmit)
		{
			event.preventDefault();

			// Get id form submit
			let getIdFormSubmit = document.getElementById("ph-submit-data-role-"+idSubmit);

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

						this.closeModalAfterSubmit("ph-submit-data-role-"+idSubmit, "ph-submit-data-role-"+idSubmit);
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
		listData: function(event)
		{
			if (document.querySelector(".ar-fetch-listdata-role") !== null &&
				document.querySelector(".ar-fetch-listdata-role").getAttribute("data-url") !== null) 
			{
				const url = document.querySelector(".ar-fetch-listdata-role").getAttribute("data-url");

				let getIdFormSubmit = document.getElementById("ar-fetch-listdata-role");

				axios.get(url)
				.then(response => 
				{
					this.responseData = response.data.data;
					this.responseStatus = response.data.status;
					this.responseMessage = response.data.message;
				})
				.catch(function (error) 
				{
					if (error.response !== undefined) 
					{
						this.responseStatus = 'failed';
						this.responseMessage = error.response.data.message;
					}
					else 
					{
						this.responseStatus = 'failed';
						this.responseMessage = error.message;
					}
				})
				.finally(() => 
				{	
					this.loadingData = false;
					this.loadToastComplete('ph-submit-data-role');

					if (this.loadingData == false)
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

						}, 100);
					}
				});
			}
		},
		listDataPermission: function(event)
		{
			if (document.querySelector(".ar-fetch-listdata-permission") !== null &&
				document.querySelector(".ar-fetch-listdata-permission").getAttribute("data-url") !== null) 
			{
				const url = document.querySelector(".ar-fetch-listdata-permission").getAttribute("data-url");

				axios.get(url)
				.then(response => 
				{
					this.responseDataPermissions 	= response.data.data;
					this.responseStatusPermission 	= response.data.status;
					this.responseMessagePermission 	= response.data.message;
				})
				.catch(function (error) 
				{
					this.responseStatusPermission 	= response.data.status;
					this.responseMessagePermission 	= response.data.message;

					// console.log(error.response);
				})
				.finally(() => 
				{
					// console.log(this.responseStatus);
					// console.log(this.responseMessage);
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
		clickSelectAllPermission: function()
		{
			const getSelectAll = document.querySelector("#clickSelectAllPermission");

			if (getSelectAll.checked == true) 
			{
				const checkbox = document.querySelectorAll(".check-all-permission");

				for (i = 0; i < checkbox.length; i++)
				{
					if (checkbox[i].checked == false)
					{
						checkbox[i].checked = true;

						this.getTotalChecked += 1;
					}
				}
			} 
			else 
			{
				const checkbox = document.querySelectorAll(".check-all-permission");

				for (i = 0; i < checkbox.length; i++)
				{
					if (checkbox[i].checked == true)
					{
						checkbox[i].checked = false;

						this.getTotalChecked -= 1;
					}
				}
			}
		},
		clickSubSelectAllPermission: function(key)
		{
			console.log(key);

			const getSubSelectAll = document.querySelector("#clickSubSelectAll"+key);

			if (getSubSelectAll.checked == true) 
			{
				const checkbox = document.querySelectorAll(".check-permission-"+key);

				for (i = 0; i < checkbox.length; i++)
				{
					if (checkbox[i].checked == false)
					{
						checkbox[i].checked = true;

						this.getTotalChecked += 1;
					}
				}

				// if (document.querySelector(".ph-change-user-options") !== null) 
				// {
				// 	if (getComputedStyle(document.querySelector('.ph-change-user-options'), null).display == 'none') 
				// 	{
				// 		document.querySelector(".ph-change-user-options").style.display = 'block';
				// 	}
				// }
			} 
			else 
			{
				const checkbox = document.querySelectorAll(".check-permission-"+key);

				for (i = 0; i < checkbox.length; i++)
				{
					if (checkbox[i].checked == true)
					{
						checkbox[i].checked = false;

						this.getTotalChecked -= 1;
					}
				}
			}

			console.log(this.getTotalChecked);
		},
		clickCheckbox: function(id, event)
		{
			const selectAll = document.querySelector("#clickSelectAll");
			const selectPerEach = document.querySelectorAll(".checkids");

			if (selectAll.checked == true)
			{
				selectAll.checked = false;
			}

			if (document.querySelector(".ph-change-user-options") !== null) 
			{
				if (event.target.checked == true)
				{
					this.getTotalChecked += 1;
				}
				else if (event.target.checked == false)
				{
					this.getTotalChecked -= 1;
				}

				// if (this.getTotalChecked > 0)
				// {
				// 	if (getComputedStyle(document.querySelector('.ph-change-user-options'), null).display == 'none') 
				// 	{
				// 		document.querySelector(".ph-change-user-options").style.display = 'block';
				// 	}
				// }

				// if (this.getTotalChecked == 0)
				// {
				// 	if (getComputedStyle(document.querySelector('.ph-change-user-options'), null).display == 'block') 
				// 	{
				// 		document.querySelector(".ph-change-user-options").style.display = 'none';
				// 	}
				// }
			}
		},
		detailData: function(event)
		{
			// Empty for now
		},
		detailDataModal: function(modalId, dataId, extendData)
		{
			if (
				document.querySelector(".ar-fetch-detail-data-role-"+modalId+"-"+dataId) !== null &&
				document.querySelector(".ar-fetch-detail-data-role-"+modalId+"-"+dataId).getAttribute("data-url") !== null) 
			{
				const url = document.querySelector(".ar-fetch-detail-data-role-"+modalId+"-"+dataId).getAttribute("data-url");

				axios.get(url+'/'+dataId)
				.then(response => 
				{
					if (response.data.status == 'success')					
					{
						if (modalId == 'modalRead')
						{
							this.responseDataModal.read = response.data.data;
						}
						else if (modalId == 'modalUpdate')
						{
							this.responseDataModal.update = response.data.data;
						}
						else if (modalId == 'modalDelete')
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
			if (modalId == 'modalRead')
			{
				this.detailDataModal(modalId, dataId, true);
			}
			else if (modalId == 'modalUpdate')
			{
				this.detailDataModal(modalId, dataId, true);
			}
			else if (modalId == 'modalDelete')
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

		this.listDataPermission();

		const myModalReadEl = document.getElementById('ph-submit-data-role-modalRead');

		if (myModalReadEl !== null)
		{
			myModalReadEl.addEventListener('hidden.bs.modal', event => 
			{
				this.loadingDataModal = true;
			});
		}
		else
		{
			console.info('ID ph-submit-data-role-modalRead doesn\'t exist');
		}

		const myModalUpdatelEl = document.getElementById('ph-submit-data-role-modalUpdate');

		if (myModalUpdatelEl !== null)
		{
			myModalUpdatelEl.addEventListener('hidden.bs.modal', event => 
			{
				this.loadingDataModal = true;
			});
		}
		else
		{
			console.info('ID ph-submit-data-role-modalUpdate doesn\'t exist');
		}

		const myModaDeletelEl = document.getElementById('ph-submit-data-role-modalDelete');

		if (myModaDeletelEl !== null)
		{
			myModaDeletelEl.addEventListener('hidden.bs.modal', event => 
			{
				this.loadingDataModal = true;
			});
		}
		else
		{
			console.info('ID ph-submit-data-role-modalDelete doesn\'t exist');
		}
	}
}).mount('#ph-app-manage-role');