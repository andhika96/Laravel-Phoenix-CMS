const ListUserVue3 =  createApp(
{
	data()
	{
		return {
			responseData: [],
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
			responseSelectUserStatus:
			{
				listData: '',
				formAdd: '',
				formEdit: ''
			},
			responseMessage: '',
			responseMessageDetailData: '',
			responseMessageCheckData: 
			{
				email: '',
				username: ''
			},
			responseMessageAfterSubmit: '',
			responseStatus: '',
			responseStatusDetailData: '',
			responseStatusCheckData: 
			{
				email: '',
				username: ''
			},
			responseStatusToast: '',
			getCurrentUserData:
			{
				email: '',
				username: ''
			},
			getQueryStringInURL: '',
			getQuerySearchUser: '',
			getQueryCheckData: 
			{
				email: '',
				username: ''
			},
			getTotalData: '',
			getPage: 1,
			getCurrentPage: '',
			getTotalChecked: 0,
			loading: '',
			loadingDetailData: '',
			loadingCheckData: 
			{
				email: '',
				username: ''
			},
			loadingNextPage: '',
			pageUrl: '',
			pageCount: '',
			pageRange: '',
			showModalAddUser: false,
			showModalEditUser: false,
			customButtonValue: 'Submit',
			autoResetForm: 'false',
			autoRefreshData: 'false',
			autoLockButton: 'false',
			autoBlockButton: '',
			autoBlockButtonMobile: '',
			showForm:
			{
				passwordForm: false,
				autoSetPassword: true,
			},
			autoFill:
			{
				username: false
			},
			showButtonRemoveQuerySearch: false,
			responsiveHiddenCols: [],
			responsiveExpandedRows: {},
			responsiveColDefs: [],
		}
	},
	components:
	{
		paginate: VuejsPaginateNext
	},
	watch:
	{
		responseData: function()
		{
			this.$nextTick(() =>
			{
				if (this._colNaturalWidths)
				{
					// Width masih valid (ukuran layar tidak berubah saat pindah halaman/search/filter)
					// Langsung recalc saja — tidak perlu measure ulang
					this.recalcResponsive();
				}
				else
				{
					// Belum pernah diukur (load pertama) — hiddenCols pasti masih []
					// sehingga guard di measureColWidths akan terlewati
					this.measureColWidths(() =>
					{
						this.recalcResponsive();
					});
				}
			});
		}
	},
	computed:
	{
		/**
		 * Total kolom yang saat ini visible — dipakai untuk colspan child row.
		 * Dihitung dari DOM (th[data-col-idx]) agar otomatis saat kolom ditambah.
		 */
		responsiveVisibleColCount: function()
		{
			const ths = document.querySelectorAll('#ph-user-table-wrapper thead tr th[data-col-idx]');
			const total = ths.length || 10;
			return total - this.responsiveHiddenCols.length;
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

			// Auto refresh after submit data
			this.autoRefresh = getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("auto-refresh");

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

			// Get class button name to change the button to button loading state .
			getIdFormSubmit.getElementsByClassName("btn-submit-data")[0].insertAdjacentHTML("beforebegin", "<a class=\"btn btn-secondary btn-submit-loading font-size-inherit "+this.autoBlockButton+" "+this.autoBlockButtonMobile+" "+this.autoBlockButtonMobile+"\">Submitting <div class=\"spinner-border spinner-border-sm text-light ml-1\" role=\"status\"><span class=\"sr-only\">Loading...</span></div></a>");
			getIdFormSubmit.getElementsByClassName("btn-submit-data")[0].remove();

			if (document.querySelector(".btn-cancel-submit") !== null)
			{
				document.getElementsByClassName("btn-cancel-submit")[0].setAttribute("disabled", "disabled");
			}

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

						getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].insertAdjacentHTML("beforebegin", "<button type=\"submit\" class=\"btn ph-btn-theme btn-submit-data font-size-inherit "+this.autoBlockButton+" "+this.autoBlockButtonMobile+" "+this.autoBlockButtonMobile+"\">"+this.customButtonValue+"</button>");
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

						getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].insertAdjacentHTML("beforebegin", "<a class=\"btn btn-success btn-logged font-size-inherit "+this.autoBlockButton+" "+this.autoBlockButtonMobile+"\">Success <i class=\"far fa-check-circle fa-fw mr-1\"></i></div></a>");
						getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].remove();
					}

					if (getIdFormSubmit.querySelector(".btn-cancel-submit") !== null)
					{
						getIdFormSubmit.getElementsByClassName("btn-cancel-submit")[0].removeAttribute("disabled");
					}

					if (this.autoRefresh == 'true')
					{
						this.listData();
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

					if (getIdFormSubmit.querySelector(".btn-cancel-submit") !== null)
					{
						getIdFormSubmit.getElementsByClassName("btn-cancel-submit")[0].removeAttribute("disabled");
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

					getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].insertAdjacentHTML("beforebegin", "<button type=\"submit\" class=\"btn ph-btn-theme btn-submit-data font-size-inherit "+this.autoBlockButton+" "+this.autoBlockButtonMobile+"\">"+this.customButtonValue+"</button>");
					getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].remove();
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

				if (getIdFormSubmit.querySelector(".btn-cancel-submit") !== null)
				{
					getIdFormSubmit.getElementsByClassName("btn-cancel-submit")[0].removeAttribute("disabled");
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

				getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].insertAdjacentHTML("beforebegin", "<button type=\"submit\" class=\"btn ph-btn-theme btn-submit-data font-size-inherit "+this.autoBlockButton+" "+this.autoBlockButtonMobile+"\">"+this.customButtonValue+"</button>");
				getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].remove();
			});
		},
		submitDataAddUser: function(event) 
		{
			event.preventDefault();

			// Get id form submit
			let getIdFormSubmit = document.getElementById("ph-form-submit-data-adduser");

			// Get value of attribute in HTML.
			let formActionURL = getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("action");
			let formMethod = getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("method");

			// FormData objects are used to capture HTML form and submit it using fetch or another network method.
			let formData = new FormData(this.$refs.formHTMLAddUser);

			// Disabled for experimental
			// for (const item of this.$refs.formHTMLAddUser)
			// {
			// 	console.log(item);
			// }

			// Auto reset after submit data
			this.autoLockButton = getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("auto-lock-button");

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

			// Get custom button value after submit
			if (getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("custom-button-value") !== null)
			{
				this.customButtonValue = getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("custom-button-value");
			}

			// Get class button name to change the button to button loading state .
			getIdFormSubmit.getElementsByClassName("btn-submit-data")[0].insertAdjacentHTML("beforebegin", "<a class=\"btn btn-secondary btn-submit-loading font-size-inherit "+this.autoBlockButton+" "+this.autoBlockButtonMobile+"\">Submitting <div class=\"spinner-border spinner-border-sm text-light ml-1\" role=\"status\"><span class=\"sr-only\">Loading...</span></div></a>");
			getIdFormSubmit.getElementsByClassName("btn-submit-data")[0].remove();

			if (getIdFormSubmit.querySelector(".btn-cancel-submit") !== null)
			{
				getIdFormSubmit.getElementsByClassName("btn-cancel-submit")[0].setAttribute("disabled", "disabled");
			}

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

					if (this.autoResetForm == 'true')
					{
						this.autoFill.username = false;
						this.showForm.passwordForm = false;

						this.getQueryCheckData.email = '';
						this.getQueryCheckData.username = '';

						this.responseStatusCheckData.email = '';
						this.responseStatusCheckData.username = '';

						if (document.querySelector(".form-control-username") !== null)
						{
							document.getElementsByClassName("form-control-username")[0].removeAttribute("disabled");
							document.getElementsByClassName("form-control-username")[0].removeAttribute("placeholder");
						}

						getIdFormSubmit.getElementsByTagName("form")[0].reset();
					}

					if (this.autoRefreshData == 'true')
					{
						this.listData();
					}

					this.closeModalAddUserAfterAdd();

					if (getIdFormSubmit.querySelector(".btn-cancel-submit") !== null)
					{
						getIdFormSubmit.getElementsByClassName("btn-cancel-submit")[0].removeAttribute("disabled");
					}

					if (this.autoLockButton == 'true')
					{
						getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].insertAdjacentHTML("beforebegin", "<a class=\"btn btn-success btn-logged font-size-inherit "+this.autoBlockButton+" "+this.autoBlockButtonMobile+"\">Success <i class=\"far fa-check-circle fa-fw mr-1\"></i></div></a>");
						getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].remove();
					}
					else
					{
						getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].insertAdjacentHTML("beforebegin", "<button type=\"submit\" class=\"btn ph-btn-theme btn-submit-data font-size-inherit "+this.autoBlockButton+" "+this.autoBlockButtonMobile+"\">"+this.customButtonValue+"</button>");
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

					if (getIdFormSubmit.querySelector(".btn-cancel-submit") !== null)
					{
						getIdFormSubmit.getElementsByClassName("btn-cancel-submit")[0].removeAttribute("disabled");
					}

					// We use setTimeout to give responseStatus time set status in Toast and then show the Toast
					// base on responStatus
					window.setTimeout(function() 
					{
						// We use toast from Bootstrap 5
						let toastBox = getIdFormSubmit.getElementsByClassName("ph-notice-toast")[0];

						let toast = new bootstrap.Toast(toastBox);

						toast.show();

					}, 1);

					getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].insertAdjacentHTML("beforebegin", "<button type=\"submit\" class=\"btn ph-btn-theme btn-submit-data font-size-inherit "+this.autoBlockButton+" "+this.autoBlockButtonMobile+"\">"+this.customButtonValue+"</button>");
					getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].remove();
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

				if (getIdFormSubmit.querySelector(".btn-cancel-submit") !== null)
				{
					getIdFormSubmit.getElementsByClassName("btn-cancel-submit")[0].removeAttribute("disabled");
				}

				// We use setTimeout to give responseStatus time set status in Toast and then show the Toast
				// base on responStatus
				window.setTimeout(function() 
				{
					// We use toast from Bootstrap 5
					let toastBox = getIdFormSubmit.getElementsByClassName("ph-notice-toast")[0];

					let toast = new bootstrap.Toast(toastBox);

					toast.show();

				}, 1);

				getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].insertAdjacentHTML("beforebegin", "<button type=\"submit\" class=\"btn ph-btn-theme btn-submit-data font-size-inherit "+this.autoBlockButton+" "+this.autoBlockButtonMobile+"\">"+this.customButtonValue+"</button>");
				getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].remove();
			});
		},
		submitDataEditUser: function(event) 
		{
			event.preventDefault();

			// Get id form submit
			let getIdFormSubmit = document.getElementById("ph-form-submit-data-edituser");

			// Get value of attribute in HTML.
			let formActionURL = getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("action");
			let formMethod = getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("method");

			// FormData objects are used to capture HTML form and submit it using fetch or another network method.
			let formData = new FormData(this.$refs.formHTMLEditUser);

			// Disabled for experimental
			// for (const item of this.$refs.formHTMLAddUser)
			// {
			// 	console.log(item);
			// }

			// Auto reset after submit data
			this.autoLockButton = getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("auto-lock-button");

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

			// Get custom button value after submit
			if (getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("custom-button-value") !== null)
			{
				this.customButtonValue = getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("custom-button-value");
			}

			// Get class button name to change the button to button loading state .
			getIdFormSubmit.getElementsByClassName("btn-submit-data")[0].insertAdjacentHTML("beforebegin", "<a class=\"btn btn-secondary btn-submit-loading font-size-inherit "+this.autoBlockButton+" "+this.autoBlockButtonMobile+"\">Submitting <div class=\"spinner-border spinner-border-sm text-light ml-1\" role=\"status\"><span class=\"sr-only\">Loading...</span></div></a>");
			getIdFormSubmit.getElementsByClassName("btn-submit-data")[0].remove();

			if (getIdFormSubmit.querySelector(".btn-cancel-submit") !== null)
			{
				getIdFormSubmit.getElementsByClassName("btn-cancel-submit")[0].setAttribute("disabled", "disabled");
			}

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

					if (this.autoResetForm == 'true')
					{
						this.getQueryCheckData.email = '';
						this.getQueryCheckData.username = '';

						this.responseStatusCheckData.email = '';
						this.responseStatusCheckData.username = '';

						getIdFormSubmit.getElementsByTagName("form")[0].reset();
					}

					if (this.autoRefreshData == 'true')
					{
						this.listData();
					}

					this.closeModalEditUserAfterEdit();

					if (getIdFormSubmit.querySelector(".btn-cancel-submit") !== null)
					{
						getIdFormSubmit.getElementsByClassName("btn-cancel-submit")[0].removeAttribute("disabled");
					}

					if (this.autoLockButton == 'true')
					{
						getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].insertAdjacentHTML("beforebegin", "<a class=\"btn btn-success btn-logged font-size-inherit "+this.autoBlockButton+" "+this.autoBlockButtonMobile+"\">Success <i class=\"far fa-check-circle fa-fw mr-1\"></i></div></a>");
						getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].remove();
					}
					else
					{
						getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].insertAdjacentHTML("beforebegin", "<button type=\"submit\" class=\"btn ph-btn-theme btn-submit-data font-size-inherit "+this.autoBlockButton+" "+this.autoBlockButtonMobile+"\">"+this.customButtonValue+"</button>");
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

					if (getIdFormSubmit.querySelector(".btn-cancel-submit") !== null)
					{
						getIdFormSubmit.getElementsByClassName("btn-cancel-submit")[0].removeAttribute("disabled");
					}

					// We use setTimeout to give responseStatus time set status in Toast and then show the Toast
					// base on responStatus
					window.setTimeout(function() 
					{
						// We use toast from Bootstrap 5
						let toastBox = getIdFormSubmit.getElementsByClassName("ph-notice-toast")[0];

						let toast = new bootstrap.Toast(toastBox);

						toast.show();

					}, 1);

					getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].insertAdjacentHTML("beforebegin", "<button type=\"submit\" class=\"btn ph-btn-theme btn-submit-data font-size-inherit "+this.autoBlockButton+" "+this.autoBlockButtonMobile+"\">"+this.customButtonValue+"</button>");
					getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].remove();
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

				if (getIdFormSubmit.querySelector(".btn-cancel-submit") !== null)
				{
					getIdFormSubmit.getElementsByClassName("btn-cancel-submit")[0].removeAttribute("disabled");
				}

				// We use setTimeout to give responseStatus time set status in Toast and then show the Toast
				// base on responStatus
				window.setTimeout(function() 
				{
					// We use toast from Bootstrap 5
					let toastBox = getIdFormSubmit.getElementsByClassName("ph-notice-toast")[0];

					let toast = new bootstrap.Toast(toastBox);

					toast.show();

				}, 1);

				getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].insertAdjacentHTML("beforebegin", "<button type=\"submit\" class=\"btn ph-btn-theme btn-submit-data font-size-inherit "+this.autoBlockButton+" "+this.autoBlockButtonMobile+"\">"+this.customButtonValue+"</button>");
				getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].remove();
			});
		},
		listData: async function()
		{
			if (document.querySelector(".ph-fetch-listdata") !== null &&
				document.querySelector(".ph-fetch-listdata").getAttribute("data-url") !== null) 
			{
				const url = document.querySelector(".ph-fetch-listdata").getAttribute("data-url");

				this.loading = true;

				// if (this.autoRefresh == 'false')
				// {
					let params = (new URL(url)).searchParams;

					if (this.getQuerySearchUser !== '')
					{
						params.set('fullname', this.getQuerySearchUser);
					}

					if (params.toString() !== '')
					{
						if (this.getPage == 1)
						{
							this.pageUrl = '?'+params.toString();
						}
						else
						{
							this.pageUrl = '?'+params.toString()+'&page='+this.getPage;
						}
					}
					else
					{
						if (this.getPage == 1)
						{
							this.pageUrl = '';
						}
						else
						{
							this.pageUrl = '?page='+this.getPage;
						}
					}					
				// }
				
				await axios.get(url+this.pageUrl)
				.then(response => 
				{
					this.responseData 		= response.data.data;
					this.getTotalData 		= response.data.total;
					this.pageCount			= response.data.total_page;
					this.pageRange			= response.data.limit;
					
					// if (this.autoRefresh == 'false')
					// {
						this.responseStatus 	= response.data.status;
						this.responseMessage 	= response.data.message;
					// }

					// console.log(this.responseData);
				})
				.catch(function (error) 
				{
					this.responseStatus = 'failed';
					this.responseMessage = error.response.data.message;

					// console.log(error.response);
				})
				.finally(() => 
				{
					// if (this.autoRefresh == 'false')
					// {
						this.loadDataComplete();
					// }
				});
			}
		},
		getUserData: async function(userId)
		{
			if (document.querySelector(".ph-fetch-userdata-"+userId) !== null &&
				document.querySelector(".ph-fetch-userdata-"+userId).getAttribute("data-url") !== null) 
			{
				this.loadingDetailData = true;

				const url = document.querySelector(".ph-fetch-userdata-"+userId).getAttribute("data-url");
				
				await axios.get(url+'?user_id='+userId)
				.then(response => 
				{
					this.responseDetailData 		= response.data.data;
					this.responseStatusDetailData 	= response.data.status;
					this.responseMessageDetailData 	= response.data.message;

					this.getCurrentUserData.email 		= this.responseDetailData.email;
					this.getCurrentUserData.username 	= this.responseDetailData.username;

					// console.log(this.responseDetailData);
				})
				.catch(function (error) 
				{
					this.responseStatusDetailData 	= 'failed';

					if (error.response !== undefined) 
					{
						this.responseMessageDetailData = error.response.data.message;
					}
					else 
					{
						this.responseMessageDetailData = error.message;
					}

					// console.log(error.response);
				})
				.finally(() => 
				{
					this.loadingDetailData = false;

					if (this.loadingDetailData == false)
					{
						window.setTimeout(function() 
						{
							document.getElementsByClassName("user_id")[0].value = this.responseDetailData.id;
						
						}.bind(this), 1);
					}
				});
			}
		},
		searchData: _.debounce(function() 
		{
			const getQuerySearchUser = this.getQuerySearchUser.trim();

			if (document.querySelector(".ph-fetch-listdata") !== null &&
				document.querySelector(".ph-fetch-listdata").getAttribute("data-url") !== null) 
			{
				this.loadingNextPage = true;

				if (this.getQuerySearchUser !== '')
				{
					this.showButtonRemoveQuerySearch = true;
				}
				else
				{
					this.showButtonRemoveQuerySearch = false;
				}

				const url = document.querySelector(".ph-fetch-listdata").getAttribute("data-url");

				axios.get(url+'?search='+getQuerySearchUser)
				.then(response => 
				{
					this.responseData 		= response.data.data;
					this.getTotalData 		= response.data.total;
					this.getCurrentPage		= response.data.current_page;
					this.pageCount 			= response.data.total_page;
					this.pageRange 			= response.data.limit;
					this.responseStatus 	= response.data.status;
					this.responseMessage 	= response.data.message;

					// console.log(this.responseStatus);
				})
				.catch(function (error) 
				{
					this.responseStatus = error.data.status;
					this.responseMessage = error.data.message;
				})
				.finally(() => 
				{
					this.loadDataComplete();
				});
			}
		}, 500),
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
		checkDataForUpdate: _.debounce(function(checkType) 
		{
			const getQueryCheckDataEmail = (this.getQueryCheckData.email !== '') ? this.getQueryCheckData.email.trim() : this.responseDetailData.email.trim();
			const getQueryCheckDataUsername = (this.getQueryCheckData.username !== '') ? this.getQueryCheckData.username.trim() : this.responseDetailData.username.trim();

			if (document.querySelector(".ph-fetch-checkdatafor-update-"+checkType) !== null &&
				document.querySelector(".ph-fetch-checkdatafor-update-"+checkType).getAttribute("data-url") !== null) 
			{
				const url = document.querySelector(".ph-fetch-checkdatafor-update-"+checkType).getAttribute("data-url");
				
				let urlWithQuery = '';

				if (checkType == 'email')
				{
					this.loadingCheckData.email = true;

					urlWithQuery = url+'?checkType=email&currentUserEmail='+this.getCurrentUserData.email+'&email='+getQueryCheckDataEmail;
				}
				else if (checkType == 'username')
				{
					this.loadingCheckData.username = true;

					urlWithQuery = url+'?checkType=username&currentUsername='+this.getCurrentUserData.username+'&username='+getQueryCheckDataUsername;
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
		loadDataComplete: function() 
		{
			this.loading = false;
			this.loadingNextPage = false;

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

			// Reset expanded rows when data reloads
			this.responsiveExpandedRows = {};

			// Build colDefs dari DOM — hanya sekali, saat loading selesai pertama kali
			// dan DOM thead sudah visible (tidak terhalang v-if/v-cloak loading state).
			if ( ! this._colDefsBuilt)
			{
				this._colDefsBuilt = true;
				this.$nextTick(() =>
				{
					this.buildColDefs();
				});
			}

			// ============================================================
			// Responsive Table: gunakan ResizeObserver pada wrapper element.
			// ResizeObserver fire SETELAH browser selesai layout element di ukuran baru,
			// sehingga measureColWidths selalu baca ukuran yang sudah akurat.
			// Tidak perlu setTimeout atau $nextTick berlapis seperti pada window.resize.
			// ============================================================
			const wrapper = document.getElementById('ph-user-table-wrapper');

			if (wrapper && typeof ResizeObserver !== 'undefined')
			{
				let resizeTimer = null;

				this._resizeObserver = new ResizeObserver(() =>
				{
					// Debounce: batalkan timer sebelumnya, set ulang
					// Hanya eksekusi setelah resize berhenti 100ms
					clearTimeout(resizeTimer);
					resizeTimer = setTimeout(() =>
					{
						this.responsiveHiddenCols = [];
						this._colNaturalWidths    = null;

						this.$nextTick(() =>
						{
							this.measureColWidths(() =>
							{
								this.recalcResponsive();
							});
						});
					}, 1);
				});

				this._resizeObserver.observe(wrapper);
			}
			else
			{
				// Fallback untuk browser lama yang tidak support ResizeObserver
				this._responsiveResizeHandler = () =>
				{
					this.responsiveHiddenCols = [];
					this._colNaturalWidths    = null;

					this.$nextTick(() =>
					{
						setTimeout(() =>
						{
							this.measureColWidths(() =>
							{
								this.recalcResponsive();
							});
						}, 50);
					});
				};

				window.addEventListener('resize', this._responsiveResizeHandler);
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
		clickSelectAll: function()
		{
			const getSelectAll = document.querySelector("#clickSelectAll");

			if (getSelectAll.checked == true) 
			{
				const checkbox = document.querySelectorAll(".checkids");

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
				const checkbox = document.querySelectorAll(".checkids");

				for (i = 0; i < checkbox.length; i++)
				{
					if (checkbox[i].checked == true)
					{
						checkbox[i].checked = false;

						this.getTotalChecked -= 1;
					}
				}

				if (document.querySelector(".ph-change-user-options") !== null) 
				{
					if (getComputedStyle(document.querySelector('.ph-change-user-options'), null).display == 'block') 
					{
						document.querySelector(".ph-change-user-options").style.display = 'none';
					}
				}
			}
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
		selectStatus: function(event, type)
		{
			if (type == 'listData')
			{
				this.responseSelectUserStatus.listData = event.target.value;
			}
			else if (type == 'formAdd')
			{
				this.responseSelectUserStatus.formAdd = event.target.value;
			}
			else if (type == 'formEdit')
			{
				this.responseSelectUserStatus.formEdit = event.target.value;
			}

			// console.log(this.responseSelectUserStatus);
		},
		autoFillUsername: function(event)
		{
			const autoFillUsername = document.getElementById('autoFillUsername'); 

			if (autoFillUsername.checked == true)
			{
				this.autoFill.username = true;
				this.getQueryCheckData.username = '';
				this.responseStatusCheckData.username = '';

				if (document.querySelector("#autofill_username") !== null)
				{
					document.getElementById("autofill_username").setAttribute("value", "false");
				}

				if (document.querySelector(".form-control-username") !== null)
				{
					document.getElementsByClassName("form-control-username")[0].value = '';
					document.getElementsByClassName("form-control-username")[0].setAttribute("disabled", "true");
					document.getElementsByClassName("form-control-username")[0].setAttribute("placeholder", "The system will create a username automatically.");
				}

				// console.log(event.target.form[2]);
			}
			else
			{
				this.autoFill.username = false;

				if (document.querySelector("#autofill_username") !== null)
				{
					document.getElementById("autofill_username").setAttribute("value", "true");
				}

				if (document.querySelector(".form-control-username") !== null)
				{
					document.getElementsByClassName("form-control-username")[0].removeAttribute("disabled");
					document.getElementsByClassName("form-control-username")[0].removeAttribute("placeholder");
				}
			}

			// console.log(autoFillUsername.checked);
		},
		showPasswordForm: function(event)
		{
			const showFormPassword = document.getElementById('showFormPassword'); 

			if (showFormPassword.checked == true)
			{
				this.showForm.passwordForm = true;
				this.showForm.autoSetPassword = false;
			}
			else
			{
				this.showForm.passwordForm = false;
				this.showForm.autoSetPassword = true;
			}
		},
		modalAddUser: function()
		{
			const modalAddNewUser = bootstrap.Modal.getOrCreateInstance(document.getElementById("modalAddNewUser"));

			modalAddNewUser.show();
		},
		closeModalAddUser: function()
		{
			// Get id form submit
			let getIdFormSubmit = document.getElementById("ph-form-submit-data-adduser");

			if (getIdFormSubmit !== null)
			{
				// Reset form
				getIdFormSubmit.getElementsByTagName("form")[0].reset();

				// We use toast from Bootstrap 5
				let toastBox = getIdFormSubmit.getElementsByClassName("ph-notice-toast")[0];
				
				let toast = bootstrap.Toast.getOrCreateInstance(toastBox);

				toast.hide();

				if (toast.isShown() == true)
				{
					toastBox.addEventListener('hidden.bs.toast', () => 
					{
						// Hide Modal
						const modalAddNewUser = bootstrap.Modal.getOrCreateInstance(document.getElementById("modalAddNewUser"));

						modalAddNewUser.hide();
					});
				}
				else if (toast.isShown() == false)
				{
					// Hide Modal
					const modalAddNewUser = bootstrap.Modal.getOrCreateInstance(document.getElementById("modalAddNewUser"));

					modalAddNewUser.hide();
				}

				if (document.querySelector("#modalAddNewUser") !== null)
				{
					let myModalEl = document.getElementById('modalAddNewUser');
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
		closeModalAddUserAfterAdd: function()
		{
			// Get id form submit
			let getIdFormSubmit = document.getElementById("ph-form-submit-data-adduser");

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
						const modalAddNewUser = bootstrap.Modal.getOrCreateInstance(document.getElementById("modalAddNewUser"));

						modalAddNewUser.hide();

						if (document.querySelector("#modalAddNewUser") !== null)
						{
							let myModalEl = document.getElementById('modalAddNewUser');
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
		modalEditUser: function(userId)
		{
			const modalEditUser = bootstrap.Modal.getOrCreateInstance(document.getElementById("modalEditUser"));

			modalEditUser.show();

			this.getUserData(userId);

			// console.log('userId: '+userId);
		},
		closeModalEditUser: function()
		{
			// Get id form submit
			let getIdFormSubmit = document.getElementById("ph-form-submit-data-edituser");

			if (getIdFormSubmit !== null)
			{
				// Reset form
				getIdFormSubmit.getElementsByTagName("form")[0].reset();

				// We use toast from Bootstrap 5
				let toastBox = getIdFormSubmit.getElementsByClassName("ph-notice-toast")[0];
				
				let toast = bootstrap.Toast.getOrCreateInstance(toastBox);

				toast.hide();

				if (toast.isShown() == true)
				{
					toastBox.addEventListener('hidden.bs.toast', () => 
					{
						// Hide Modal
						const modalEditUser = bootstrap.Modal.getOrCreateInstance(document.getElementById("modalEditUser"));

						modalEditUser.hide();
					});
				}
				else if (toast.isShown() == false)
				{
					// Hide Modal
					const modalEditUser = bootstrap.Modal.getOrCreateInstance(document.getElementById("modalEditUser"));

					modalEditUser.hide();
				}

				if (document.querySelector("#modalEditUser") !== null)
				{
					let myModalEl = document.getElementById('modalEditUser');
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
		closeModalEditUserAfterEdit: function()
		{
			// Get id form submit
			let getIdFormSubmit = document.getElementById("ph-form-submit-data-edituser");

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
						const modalEditUser = bootstrap.Modal.getOrCreateInstance(document.getElementById("modalEditUser"));

						modalEditUser.hide();

						if (document.querySelector("#modalEditUser") !== null)
						{
							let myModalEl = document.getElementById('modalEditUser');
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
		clickPaginate: async function (page) 
		{
			if (document.querySelector(".ph-fetch-listdata") !== null &&
				document.querySelector(".ph-fetch-listdata").getAttribute("data-url") !== null) 
			{
				const url = document.querySelector(".ph-fetch-listdata").getAttribute("data-url");

				let params = (new URL(url)).searchParams;

				if (this.getQuerySearchUser !== '')
				{
					params.set('search', this.getQuerySearchUser);
				}

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
					this.getTotalChecked = 0;

					if (document.querySelector(".ph-change-user-options") !== null) 
					{
						if (getComputedStyle(document.querySelector('.ph-change-user-options'), null).display == 'block') 
						{
							document.querySelector(".ph-change-user-options").style.display = 'none';
						}
					}
				});
			}
		},
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
		removeQuerySearch: function()
		{
			const inputQuerySearch = document.querySelector('input[name="search_data"]');

			inputQuerySearch.value = '';

			this.getQuerySearchUser = '';
			this.showButtonRemoveQuerySearch = false;

			this.searchData();
		
			// console.log(imagePreview);
		},
		// ============================================================
		// Responsive Table
		// ============================================================

		/**
		 * Returns the usable pixel width for the table.
		 */
		getTableWidth: function()
		{
			const wrapper = document.getElementById('ph-user-table-wrapper');

			if (wrapper && wrapper.offsetWidth > 0)
			{
				return wrapper.offsetWidth;
			}

			return window.innerWidth;
		},

		/**
		 * Ukur lebar natural setiap kolom dari DOM.
		 * HANYA diukur saat semua kolom visible — karena Title column akan stretch
		 * mengisi ruang kosong saat kolom lain hidden, memberikan nilai yang salah.
		 * Guard ini memastikan width yang diukur adalah width natural sebenarnya.
		 */
		measureColWidths: function(callback)
		{
			requestAnimationFrame(() =>
			{
				const table = document.querySelector('#ph-user-table-wrapper table');

				if (!table) { if (callback) callback(); return; }

				const ths = table.querySelectorAll('thead tr th[data-col-idx]');

				if (!ths.length) { if (callback) callback(); return; }

				// Guard: hanya ukur saat semua kolom visible
				// Saat ada kolom hidden, Title stretch dan width-nya tidak akurat
				if (this.responsiveHiddenCols.length > 0) { if (callback) callback(); return; }

				const widths = {};
				ths.forEach((th, i) =>
				{
					widths[i] = th.offsetWidth || 80;
				});

				this._colNaturalWidths = widths;
				if (callback) callback();
			});
		},

		/**
		 * Recalculate kolom mana yang harus di-hide.
		 */
		recalcResponsive: function()
		{
			if ( ! this._colNaturalWidths)
			{
				this.measureColWidths(() => { this.recalcResponsive(); });
				return;
			}

			const ths = document.querySelectorAll('#ph-user-table-wrapper thead tr th[data-col-idx]');
			if (!ths.length) return;

			const nw   = this._colNaturalWidths;
			const COLS = [];

			ths.forEach((th, domPos) =>
			{
				const idx      = parseInt(th.getAttribute('data-col-idx'));
				const priAttr  = th.getAttribute('data-col-priority');
				const priority = priAttr === 'all' ? 'all' : parseInt(priAttr);
				const minWidth = nw[domPos] || 80;
				COLS.push({ idx, priority, minWidth });
			});

			const containerWidth = this.getTableWidth();
			let remaining = containerWidth;

			COLS.forEach((col) => { if (col.priority === 'all') remaining -= col.minWidth; });

			const flexCols = COLS
				.filter(c => c.priority !== 'all')
				.sort((a, b) => a.priority - b.priority);

			const hidden = [];
			let bail = false;

			flexCols.forEach((col) =>
			{
				if (bail || remaining - col.minWidth < 0)
				{
					hidden.push(col.idx);
					bail = true;
				}
				else
				{
					remaining -= col.minWidth;
				}
			});

			this.responsiveHiddenCols = hidden;
			if (hidden.length === 0) this.responsiveExpandedRows = {};
		},

		/**
		 * Build responsiveColDefs dari DOM.
		 * Scan semua th[data-col-idx], skip idx 0 (Checkbox) dan idx 1 (Title).
		 * - Idx 2-6: render dari data API via renderMap
		 * - Idx 7+: render dari innerHTML td[data-col-idx] di DOM
		 *
		 * Dipanggil dari loadDataComplete().$nextTick — saat loading=false
		 * dan DOM tabel (thead) sudah benar-benar visible dan bisa di-query.
		 */
		buildColDefs: function()
		{
			const vm = this;

			const renderMap = {
				// 2: (info) => info.author || '-',

				// 3: (info) =>
				// {
				// 	if (info.status !== null && info.status_formatted)
				// 	{
				// 		return info.status_formatted;
				// 	}
				// 	return '<span class="badge text-bg-danger">Unknown</span>';
				// },

				// 4: (info) =>
				// {
				// 	if (info.scheduled == 'true')
				// 	{
				// 		return '<span class="badge text-bg-warning">Scheduled</span>';
				// 	}
				// 	return '<span class="badge text-bg-secondary">No Scheduled</span>';
				// },

				// 5: (info) =>
				// {
				// 	return `${info.created_at_readable || ''}<span class="form-text d-block">(${info.created_at_readforhuman || ''})</span>`;
				// },

				// 6: (info) =>
				// {
				// 	const baseUrl = (document.querySelector('.ph-fetch-listdata-article')
				// 		? document.querySelector('.ph-fetch-listdata-article').getAttribute('data-url')
				// 		: '').replace('/listdata', '');

				// 	const editUrl = baseUrl + '/edit/' + info.id;

				// 	return `<a href="${editUrl}" class="btn btn-sm ph-btn-theme-outline py-2 px-3 me-2" style="font-size:.64rem!important"><i class="fas fa-pencil-alt fa-fw"></i></a>
				// 		<button type="button" class="btn btn-sm btn-outline-danger py-2 px-3" style="font-size:.64rem!important"
				// 			onclick="(function(el){if(el)el.click()})(document.querySelector('.ar-fetch-detail-data-article-modalDelete-${info.id}'))">
				// 			<i class="fas fa-trash fa-fw"></i>
				// 		</button>`;
				// },
			};

			vm.responsiveColDefs = [];

			const allThs = document.querySelectorAll('#ph-user-table-wrapper thead tr th[data-col-idx]');

			allThs.forEach((th) =>
			{
				const idxAttr = th.getAttribute('data-col-idx');
				if (idxAttr === null) return;

				const idx      = parseInt(idxAttr);
				const priority = th.getAttribute('data-col-priority');

				// Skip Checkbox (idx=0) dan Title (priority=all)
				if (priority === 'all' || idx === 0) return;

				const label = th.textContent.trim() || ('Col ' + idx);

				if (renderMap[idx])
				{
					vm.responsiveColDefs.push({
						idx,
						label,
						render: renderMap[idx]
					});
				}
				else
				{
					const capturedIdx = idx;

					vm.responsiveColDefs.push({
						idx,
						label,
						render: function(info)
						{
							const trs = document.querySelectorAll(
								'#ph-user-table-wrapper tbody tr:not(.ph-dtr-child-row)'
							);

							for (const tr of trs)
							{
								const cb = tr.querySelector('input.checkids');
								if (cb && cb.value == info.id)
								{
									const td = tr.querySelector('td[data-col-idx="' + capturedIdx + '"]');
									return td ? td.innerHTML : '';
								}
							}

							return '';
						}
					});
				}
			});
		},

		/**
		 * Toggle child detail row.
		 */
		toggleExpandRow: function(id)
		{
			const now = Date.now();

			if (this._lastToggleTime && (now - this._lastToggleTime) < 80)
			{
				return;
			}

			this._lastToggleTime = now;

			const next = Object.assign({}, this.responsiveExpandedRows);
			next[id] = !next[id];
			this.responsiveExpandedRows = next;
		},

		/**
		 * Returns true when the column at idx should be hidden.
		 */
		isColHidden: function(idx)
		{
			return this.responsiveHiddenCols.includes(idx);
		},
	},
	mounted()
	{
		this.listData();

		this.loadToastComplete();

		const myModaEditlEl = document.getElementById('modalEditUser');

		if (myModaEditlEl !== null)
		{
			myModaEditlEl.addEventListener('hidden.bs.modal', event => 
			{
				this.loadingDetailData = true;
			});
		}
		else
		{
			console.info('ID modalEditUser doesn\'t exist');
		}
	},
	beforeUnmount()
	{
		// Disconnect ResizeObserver untuk mencegah memory leak
		if (this._resizeObserver)
		{
			this._resizeObserver.disconnect();
		}

		// Fallback: cleanup window resize listener jika ResizeObserver tidak tersedia
		if (this._responsiveResizeHandler)
		{
			window.removeEventListener('resize', this._responsiveResizeHandler);
		}
	}
}).mount('#ph-app-list-user');