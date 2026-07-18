const ListDataSMTPVue3 = createApp(
{
	data() 
	{
		return {
			reponseData: '',
			responseDetailData: '',
			responseDetailDataSetService: '',
			responseDetailDataSetService2: '',
			responseMessage: '',
			responseDetailMessage: '',
			responseDetailSetServiceMessage: '',
			responseMessageAfterSubmit: '',
			responseStatus: '',
			responseDetailStatus: '',
			responseDetailSetServiceStatus: '',
			responseStatusToast: 'ph-callout-danger',
			isArrayMessageAfterSubmit: 0,
			getTotalData: '',
			getPage: 1,
			getCurrentPage: '',
			loading: '',
			loadingDetail: '',
			loadingNextPage: '',
			loadingNameSetService: '',
			pageUrl: '',
			pageCount: '',
			pageRange: '',
			autoReset: 'true',
			autoRefresh: 'false',
			autoRefreshSetService: 'false',
			autoLockButton: 'false',
			autoBlockButton: '',
			autoBlockButtonMobile: '',
			showModalAddUser: false,
			customButtonValue: 'Submit',
			getLengthTableTh: ''
		}
	},
	components:
	{
		paginate: VuejsPaginateNext
	},
	methods:
	{
		submitDataSMTP: function(event, idSubmit) 
		{
			event.preventDefault();

			// Get id form submit
			let getIdFormSubmit = document.getElementById("ph-form-app-data-"+idSubmit);

			// Get value of attribute in HTML.
			let formActionURL = getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("action");
			let formMethod = getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("method");

			// FormData objects are used to capture HTML form and submit it using fetch or another network method.
			let formData = new FormData(this.$refs['formHTML'+idSubmit]);

			// Auto refresh after submit data
			this.autoRefresh = getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("auto-refresh");

			// Auto refresh after submit data
			this.autoRefreshSetService = getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("auto-refresh-setservice");

			// Auto reset after submit data
			this.autoReset = getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("auto-reset");

			// Auto reset after submit data
			this.autoLockButton = getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("auto-lock-button");

			// Get custom block button after submit
			if (getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("auto-block-button") !== null)
			{
				this.autoBlockButton = getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("auto-block-button") == 'true' ? 'w-100' : '';
			}

			// Get custom responsive block button after submit
			if (getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("auto-block-button-mobile") !== null)
			{
				this.autoBlockButtonMobile = getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("auto-block-button-mobile") == 'true' ? 'w-sm-auto w-100' : '';
			}

			// Get custom button value after submit
			if (getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("custom-button-value") !== null)
			{
				this.customButtonValue = getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("custom-button-value");
			}

			// Disabled for experimental
			// for (const item of this.$refs.formHTML)
			// {
			// 	console.log(item);
			// }

			if (this.autoRefreshSetService == 'true')
			{
				this.loadingNameSetService = true;
			}

			// Get class button name to change the button to button loading state .
			getIdFormSubmit.getElementsByClassName("btn-submit-data")[0].insertAdjacentHTML("beforebegin", "<a class=\"btn btn-secondary btn-submit-loading font-size-inherit "+this.autoBlockButton+"\">Submitting <div class=\"spinner-border spinner-border-sm text-light ml-1\" role=\"status\"><span class=\"sr-only\">Loading...</span></div></a>");
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

					if (this.autoRefresh == 'true')
					{
						this.listDataSMTP('true');
					}

					if (this.autoRefreshSetService == 'true')
					{
						this.listDetailDataSMTPSetService();
					}

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

						if (this.autoReset == 'true')
						{
							// Auto reset form after success
							getIdFormSubmit.getElementsByTagName("form")[0].reset();
						}

						/*
						if (document.querySelector("#modalAddNewSMTP") !== null ||
							document.querySelector("#modalEditSMTP") !== null ||
							document.querySelector("#modalDeleteSMTP") !== null)
						{	
							if (document.querySelector("#modalDeleteSMTP") !== null)
							{
								let myModalEl = document.getElementById('modalDeleteSMTP');
								myModalEl.addEventListener('hidden.bs.modal', function(event) 
								{
									if (this.autoLockButton == 'true')
									{
										if (getIdFormSubmit.getElementsByClassName("btn-logged")[0] !== undefined)
										{
											getIdFormSubmit.getElementsByClassName("btn-logged")[0].insertAdjacentHTML("beforebegin", "<button type=\"submit\" class=\"btn ph-btn-theme btn-submit-data font-size-inherit "+this.autoBlockButton+" "+this.autoBlockButtonMobile+"\">"+this.customButtonValue+"</button>");
											getIdFormSubmit.getElementsByClassName("btn-logged")[0].remove();
										}
									}
								}.bind(this));
							}

							// Auto hide Toast
							window.setTimeout(function() 
							{
								// We use toast from Bootstrap 5
								let toastBox = getIdFormSubmit.getElementsByClassName("ph-notice-toast")[0];
							
								let toast = bootstrap.Toast.getOrCreateInstance(toastBox);

								toast.hide()
							}, 1300);

							// After Toast is hidden then hide the modal
							window.setTimeout(function() 
							{
								// Auto hide modal add new SMTP
								if (document.querySelector("#modalAddNewSMTP") !== null)
								{
									const modalAddNewSMTP = bootstrap.Modal.getOrCreateInstance(document.getElementById("modalAddNewSMTP"));

									modalAddNewSMTP.hide();
								}

								// Auto hide modal edit SMTP
								if (document.querySelector("#modalEditSMTP") !== null)
								{
									const modalEditSMTP = bootstrap.Modal.getOrCreateInstance(document.getElementById("modalEditSMTP"));

									modalEditSMTP.hide();
								}

								// Auto hide modal delete SMTP
								if (document.querySelector("#modalDeleteSMTP") !== null)
								{
									const modalDeleteSMTP = bootstrap.Modal.getOrCreateInstance(document.getElementById("modalDeleteSMTP"));

									modalDeleteSMTP.hide();
								}
							}.bind(this), 1800);
						}
						*/

						if (document.querySelector("#modalAddNewSMTP") !== null)
						{
							this.closeModalAddSMTPAfterAdd();
						}

						if (document.querySelector("#modalEditSMTP") !== null)
						{
							this.closeModalEditSMTPAfterEdit();
						}

						if (document.querySelector("#modalDeleteSMTP") !== null)
						{
							this.closeModalDeleteSMTPAfterDelete();
						}

						if (this.autoLockButton == 'true')
						{
							getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].insertAdjacentHTML("beforebegin", "<a class=\"btn btn-success btn-logged font-size-inherit "+this.autoBlockButton+"\">Success <i class=\"far fa-check-circle fa-fw mr-1\"></i></div></a>");
							getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].remove();
						}
						else
						{
							getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].insertAdjacentHTML("beforebegin", "<button type=\"submit\" class=\"btn ph-btn-theme btn-submit-data font-size-inherit\">"+this.customButtonValue+"</button>");
							getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].remove();
						}
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

						getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].insertAdjacentHTML("beforebegin", "<a class=\"btn btn-success btn-logged font-size-inherit "+this.autoBlockButton+"\">Success <i class=\"far fa-check-circle fa-fw mr-1\"></i></div></a>");
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

					getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].insertAdjacentHTML("beforebegin", "<button type=\"submit\" class=\"btn ph-btn-theme btn-submit-data font-size-inherit "+this.autoBlockButton+" "+this.autoBlockButtonMobile+"\">"+this.customButtonValue+"</button>");
					getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].remove();

					this.loadingNameSetService = false;
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
				else {
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
					let toastBox = getIdFormSubmit.getElementsByClassName("ph-notice-toast")[0];

					let toast = new bootstrap.Toast(toastBox);

					toast.show()
				}, 1);

				// console.log(getIdFormSubmit.getElementsByClassName("ph-notice-toast")[0]);

				getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].insertAdjacentHTML("beforebegin", "<button type=\"submit\" class=\"btn ph-btn-theme btn-submit-data font-size-inherit "+this.autoBlockButton+"\">"+this.customButtonValue+"</button>");
				getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].remove();

				this.loadingNameSetService = false;
			});
		},
		listDataSMTP: function(event)
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

				axios.get(url)
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

					this.loadToastComplete('modalAddNewSMTP');
				});
			}
		},
		listDetailDataSMTP: function(event, data_id)
		{
			if (document.querySelector(".ph-fetch-detaildata") !== null &&
				document.querySelector(".ph-fetch-detaildata").getAttribute("data-url") !== null) 
			{
				const url = document.querySelector(".ph-fetch-detaildata").getAttribute("data-url");

				axios.get(url+'/'+data_id)
				.then(response => 
				{
					this.responseDetailData 	= response.data.data;
					this.responseDetailStatus 	= response.data.status;
					this.responseDetailMessage 	= response.data.message;
					
					// console.log(this.responseData);
				})
				.catch(function (error) 
				{
					this.responseDetailStatus 	= response.data.status;
					this.responseDetailMessage 	= response.data.message;
				})
				.finally(() => 
				{
					this.loadingDetail = false;

					// Set value data_id
					document.querySelector("#data_id").setAttribute("value", data_id);
				});
			}
		},
		listDetailDataSMTPSetService: async function(event)
		{
			if (document.querySelector(".ph-fetch-detaildata-setservice") !== null &&
				document.querySelector(".ph-fetch-detaildata-setservice").getAttribute("data-url") !== null) 
			{
				const url = document.querySelector(".ph-fetch-detaildata-setservice").getAttribute("data-url");

				// await axios.get(url+'/'+this.responseDetailDataSetService2)
				await axios.get(url)
				.then(response => 
				{
					this.responseDetailDataSetService 		= response.data.data;
					this.responseDetailSetServiceStatus 	= response.data.status;
					this.responseDetailSetServiceMessage 	= response.data.message;
					
					// console.log(this.responseData);
				})
				.catch(function (error) 
				{
					this.responseDetailSetServiceStatus 	= response.data.status;
					this.responseDetailSetServiceMessage 	= response.data.message;
				})
				.finally(() => 
				{
					this.loadingNameSetService = false;

					window.setTimeout(function() 
					{
						if (document.querySelector(".ph-data-load-content-setservicesmtp") !== null) 
						{
							if (getComputedStyle(document.querySelector('.ph-data-load-content-setservicesmtp'), null).display == 'none') 
							{
								document.querySelector(".ph-data-load-content-setservicesmtp").style.display = 'inline-block';
							}
						}
					}, 1);
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
			let getIdFormSubmit = document.getElementById(idSubmit);

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
					this.loading = false;
					this.loadingNextPage = false;
				});
			}
		},
		openModdalAddSMTP: function()
		{
			const modalAddNewSMTP = bootstrap.Modal.getOrCreateInstance(document.getElementById("modalAddNewSMTP"));

			modalAddNewSMTP.show();
		},
		closeModalAddSMTP: function()
		{
			// Get id form submit
			let getIdFormSubmit = document.getElementById("ph-form-app-data-add");

			if (getIdFormSubmit !== null)
			{
				// Reset form
				getIdFormSubmit.getElementsByTagName("form")[0].reset();
				
				// Reset value data_id
				document.querySelector("#data_id").setAttribute("value", '');

				// We use toast from Bootstrap 5
				let toastBox = getIdFormSubmit.getElementsByClassName("ph-notice-toast")[0];
				
				let toast = bootstrap.Toast.getOrCreateInstance(toastBox);

				toast.hide();

				if (toast.isShown() == true)
				{
					toastBox.addEventListener('hidden.bs.toast', () => 
					{
						// Hide Modal
						const modalAddNewSMTP = bootstrap.Modal.getOrCreateInstance(document.getElementById("modalAddNewSMTP"));

						modalAddNewSMTP.hide();
					});
				}
				else if (toast.isShown() == false)
				{
					// Hide Modal
					const modalAddNewSMTP = bootstrap.Modal.getOrCreateInstance(document.getElementById("modalAddNewSMTP"));

					modalAddNewSMTP.hide();
				}
						
				if (document.querySelector("#modalAddNewSMTP") !== null)
				{
					let myModalEl = document.getElementById('modalAddNewSMTP');
					myModalEl.addEventListener('hidden.bs.modal', function(event) 
					{
						if (this.autoLockButton == 'true')
						{
							if (getIdFormSubmit.getElementsByClassName("btn-logged")[0] !== undefined)
							{
								getIdFormSubmit.getElementsByClassName("btn-logged")[0].insertAdjacentHTML("beforebegin", "<button type=\"submit\" class=\"btn ph-btn-theme btn-submit-data font-size-inherit\">"+this.customButtonValue+"</button>");
								getIdFormSubmit.getElementsByClassName("btn-logged")[0].remove();
							}
						}
					}.bind(this));
				}
			}
		},
		closeModalAddSMTPAfterAdd: function()
		{
			// Get id form submit
			let getIdFormSubmit = document.getElementById("ph-form-app-data-add");

			if (getIdFormSubmit !== null)
			{
				// Reset form
				getIdFormSubmit.getElementsByTagName("form")[0].reset();
				
				// Reset value data_id
				document.querySelector("#data_id").setAttribute("value", '');

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
						const modalAddNewSMTP = bootstrap.Modal.getOrCreateInstance(document.getElementById("modalAddNewSMTP"));

						modalAddNewSMTP.hide();	

						if (document.querySelector("#modalAddNewSMTP") !== null)
						{
							let myModalEl = document.getElementById('modalAddNewSMTP');
							myModalEl.addEventListener('hidden.bs.modal', function(event) 
							{
								if (this.autoLockButton == 'true')
								{
									if (getIdFormSubmit.getElementsByClassName("btn-logged")[0] !== undefined)
									{
										getIdFormSubmit.getElementsByClassName("btn-logged")[0].insertAdjacentHTML("beforebegin", "<button type=\"submit\" class=\"btn ph-btn-theme btn-submit-data font-size-inherit\">"+this.customButtonValue+"</button>");
										getIdFormSubmit.getElementsByClassName("btn-logged")[0].remove();
									}
								}
							}.bind(this));
						}
					});
				}.bind(this), 1300);
			}
		},
		openModalEditSMTP: function(event, data_id)
		{
			this.loadingDetail = true;

			this.listDetailDataSMTP(event, data_id);
			this.loadToastComplete('modalEditSMTP');

			const modalEditSMTP = bootstrap.Modal.getOrCreateInstance(document.getElementById("modalEditSMTP"));

			modalEditSMTP.show();
		},
		closeModalEditSMTP: function()
		{
			// Get id form submit
			let getIdFormSubmit = document.getElementById("ph-form-app-data-edit");

			if (getIdFormSubmit !== null)
			{
				// Reset form
				getIdFormSubmit.getElementsByTagName("form")[0].reset();
				
				// Reset value data_id
				document.querySelector("#data_id").setAttribute("value", '');

				// We use toast from Bootstrap 5
				let toastBox = getIdFormSubmit.getElementsByClassName("ph-notice-toast")[0];
				
				let toast = bootstrap.Toast.getOrCreateInstance(toastBox);

				toast.hide();

				if (toast.isShown() == true)
				{
					toastBox.addEventListener('hidden.bs.toast', () => 
					{
						// Hide Modal
						const modalEditSMTP = bootstrap.Modal.getOrCreateInstance(document.getElementById("modalEditSMTP"));

						modalEditSMTP.hide();
					});
				}
				else if (toast.isShown() == false)
				{
					// Hide Modal
					const modalEditSMTP = bootstrap.Modal.getOrCreateInstance(document.getElementById("modalEditSMTP"));

					modalEditSMTP.hide();
				}
						
				if (document.querySelector("#modalEditSMTP") !== null)
				{
					let myModalEl = document.getElementById('modalEditSMTP');
					myModalEl.addEventListener('hidden.bs.modal', function(event) 
					{
						if (this.autoLockButton == 'true')
						{
							if (getIdFormSubmit.getElementsByClassName("btn-logged")[0] !== undefined)
							{
								getIdFormSubmit.getElementsByClassName("btn-logged")[0].insertAdjacentHTML("beforebegin", "<button type=\"submit\" class=\"btn ph-btn-theme btn-submit-data font-size-inherit\">"+this.customButtonValue+"</button>");
								getIdFormSubmit.getElementsByClassName("btn-logged")[0].remove();
							}
						}
					}.bind(this));
				}
			}
		},
		closeModalEditSMTPAfterEdit: function()
		{
			// Get id form submit
			let getIdFormSubmit = document.getElementById("ph-form-app-data-edit");

			if (getIdFormSubmit !== null)
			{
				// Reset form
				getIdFormSubmit.getElementsByTagName("form")[0].reset();

				// Reset value data_id
				document.querySelector("#data_id").setAttribute("value", '');

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
						const modalEditSMTP = bootstrap.Modal.getOrCreateInstance(document.getElementById("modalEditSMTP"));

						modalEditSMTP.hide();

						if (document.querySelector("#modalEditSMTP") !== null)
						{
							let myModalEl = document.getElementById('modalEditSMTP');
							myModalEl.addEventListener('hidden.bs.modal', function(event) 
							{
								if (this.autoLockButton == 'true')
								{
									if (getIdFormSubmit.getElementsByClassName("btn-logged")[0] !== undefined)
									{
										getIdFormSubmit.getElementsByClassName("btn-logged")[0].insertAdjacentHTML("beforebegin", "<button type=\"submit\" class=\"btn ph-btn-theme btn-submit-data font-size-inherit\">"+this.customButtonValue+"</button>");
										getIdFormSubmit.getElementsByClassName("btn-logged")[0].remove();
									}
								}
							}.bind(this));
						}
					});
				}.bind(this), 1300);
			}
		},
		openModalDeleteSMTP: function(event, data_id)
		{
			this.loadingDetail = true;

			this.listDetailDataSMTP(event, data_id);
			this.loadToastComplete('modalDeleteSMTP');

			const modalDeleteSMTP = bootstrap.Modal.getOrCreateInstance(document.getElementById("modalDeleteSMTP"));

			modalDeleteSMTP.show();
		},
		closeModalDeleteSMTP: function()
		{
			// Get id form submit
			let getIdFormSubmit = document.getElementById("ph-form-app-data-delete");

			if (getIdFormSubmit !== null)
			{
				// Reset value data_id
				document.querySelector("#data_id").setAttribute("value", '');

				// We use toast from Bootstrap 5
				let toastBox = getIdFormSubmit.getElementsByClassName("ph-notice-toast")[0];
				
				let toast = bootstrap.Toast.getOrCreateInstance(toastBox);

				toast.hide();

				if (toast.isShown() == true)
				{
					toastBox.addEventListener('hidden.bs.toast', () => 
					{
						// Hide Modal
						const modalDeleteSMTP = bootstrap.Modal.getOrCreateInstance(document.getElementById("modalDeleteSMTP"));

						modalDeleteSMTP.hide();
					});
				}
				else if (toast.isShown() == false)
				{
					// Hide Modal
					const modalDeleteSMTP = bootstrap.Modal.getOrCreateInstance(document.getElementById("modalDeleteSMTP"));

					modalDeleteSMTP.hide();
				}	

				if (document.querySelector("#modalDeleteSMTP") !== null)
				{
					let myModalEl = document.getElementById('modalDeleteSMTP');
					myModalEl.addEventListener('hidden.bs.modal', function(event) 
					{
						if (this.autoLockButton == 'true')
						{
							if (getIdFormSubmit.getElementsByClassName("btn-logged")[0] !== undefined)
							{
								getIdFormSubmit.getElementsByClassName("btn-logged")[0].insertAdjacentHTML("beforebegin", "<button type=\"submit\" class=\"btn ph-btn-theme btn-submit-data font-size-inherit\">"+this.customButtonValue+"</button>");
								getIdFormSubmit.getElementsByClassName("btn-logged")[0].remove();
							}
						}
					}.bind(this));
				}
			}
		},
		closeModalDeleteSMTPAfterDelete: function()
		{
			// Get id form submit
			let getIdFormSubmit = document.getElementById("ph-form-app-data-delete");

			if (getIdFormSubmit !== null)
			{
				// Reset value data_id
				document.querySelector("#data_id").setAttribute("value", '');

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
						const modalDeleteSMTP = bootstrap.Modal.getOrCreateInstance(document.getElementById("modalDeleteSMTP"));

						modalDeleteSMTP.hide();

						if (document.querySelector("#modalDeleteSMTP") !== null)
						{
							let myModalEl = document.getElementById('modalDeleteSMTP');
							myModalEl.addEventListener('hidden.bs.modal', function(event) 
							{
								if (this.autoLockButton == 'true')
								{
									if (getIdFormSubmit.getElementsByClassName("btn-logged")[0] !== undefined)
									{
										getIdFormSubmit.getElementsByClassName("btn-logged")[0].insertAdjacentHTML("beforebegin", "<button type=\"submit\" class=\"btn ph-btn-theme btn-submit-data font-size-inherit\">"+this.customButtonValue+"</button>");
										getIdFormSubmit.getElementsByClassName("btn-logged")[0].remove();
									}
								}
							}.bind(this));
						}
					});
				}.bind(this), 1300);
			}
		}
	},
	mounted()
	{
		this.listDataSMTP('false');

		this.listDetailDataSMTPSetService();

		this.loadToastComplete('ph-form-app-data-setsmtpservice');
	}
}).mount('#ph-app-data-smtp');