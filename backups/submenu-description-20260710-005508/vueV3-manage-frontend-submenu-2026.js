const ManageSubmenuVue3 = createApp(
{
	data() 
	{
		return {
			responseDataMenu : [],
			responseDataSubmenu: [{ parent_code: '', submenu_type: '', submenu_name: '', submenu_link: '', submenu_icon_type: '', submenu_icon: '', submenu_icon_url: '', submenu_icon_path: '', submenu_icon_custom: '', submenu_roles: [], submenu_permissions: '' }],
			responseNewDataSubmenu: { parent_code: '', submenu_type: '', submenu_name: '', submenu_link: '', submenu_icon_type: '', submenu_icon: '', submenu_icon_url: '', submenu_icon_path: '', submenu_icon_custom: '', submenu_roles: [], submenu_permissions: '' },
			responseDataParentMenu : [],
			responseDataRoutes: [],
			responseDataRoles: [],
			responseDataPermissions: [],
			responseMessageMenu: '',
			responseMessageSubmenu: '',
			responseMessageParentMenu: '',
			responseMessageRoutes: '',
			responseMessageRoles: '',
			responseMessagePermissions: '',
			responseMessageAfterSubmit: '',
			responseStatusMenu: '',
			responseStatusSubmenu: '',
			responseStatusParentMenu: '',
			responseStatusRoutes: '',
			responseStatusRoles: '',
			responseStatusPermissions: '',
			responseStatusToast: '',
			isArrayMessageAfterSubmit: 0,
			loading: '',
			customButtonValue: 'Submit',
			customButtonValueModal: 
			{
				create: 'Submit',
				read: 'Submit',
				update: 'Submit',
				delete: 'Submit'
			},
			customButtonSubmenuDetailDeleteValue: 'Submit',
			autoRefresh: 'false',
			autoLockButton: 'false',
			autoLockButtonModalAdd: 'false',
			autoLockButtonModalDelete: 'false',
			drag: false,
			dragOptions: 
			{ 
				animation: 200,
				disabled: false,
				ghostClass: "ghost",
				group: "description", 
			}
		}
	},
	components:
	{
		draggable: vuedraggable,
		vSelect: window["vue-select"]
	},
	methods:
	{
		submitData: function(event) 
		{
			event.preventDefault();

			// Get id form submit
			let getIdFormSubmit = document.getElementById("ph-form-manage-menu");

			// Get value of attribute in HTML.
			let formActionURL = getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("action");
			let formMethod = getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("method");

			// FormData objects are used to capture HTML form and submit it using fetch or another network method.
			let formData = new FormData(this.$refs.formHTML);

			formData.append('menu_vars', JSON.stringify(this.responseDataSubmenu));

			// Auto reset after submit data
			this.autoLockButton = getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("auto-lock-button");

			// Auto refresh after submit data
			this.autoRefresh = getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("auto-refresh");

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

							toast.show();

						}, 1);

						getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].insertAdjacentHTML("beforebegin", "<button type=\"submit\" class=\"btn ph-btn-theme btn-submit-data btn-submit-update-data-list font-size-inherit\">"+this.customButtonValue+"</button>");
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

					getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].insertAdjacentHTML("beforebegin", "<button type=\"submit\" class=\"btn ph-btn-theme btn-submit-data btn-submit-update-data-list font-size-inherit\">"+this.customButtonValue+"</button>");
					getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].remove();
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
					else 
					{
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

					toast.show();

				}, 1);

				getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].insertAdjacentHTML("beforebegin", "<button type=\"submit\" class=\"btn ph-btn-theme btn-submit-data btn-submit-update-data-list font-size-inherit\">"+this.customButtonValue+"</button>");
				getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].remove();
			});
		},
		submitDataSubmenu: function(event) 
		{
			event.preventDefault();

			// Get id form submit
			let getIdFormSubmit = document.getElementById("ph-form-manage-menu");

			// Get value of attribute in HTML.
			let formActionURL = getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("action");
			let formMethod = getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("method");

			// FormData objects are used to capture HTML form and submit it using fetch or another network method.
			let formData = new FormData(this.$refs.formHTML);

			formData.append('menu_vars', JSON.stringify(this.responseDataMenuParent));

			// Auto reset after submit data
			this.autoLockButton = getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("auto-lock-button");

			// Auto refresh after submit data
			this.autoRefresh = getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("auto-refresh");

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

					this.listDataMenu();

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

						getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].insertAdjacentHTML("beforebegin", "<button type=\"submit\" class=\"btn ph-btn-theme btn-submit-data btn-submit-update-data-list font-size-inherit\">"+this.customButtonValue+"</button>");
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

					getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].insertAdjacentHTML("beforebegin", "<button type=\"submit\" class=\"btn ph-btn-theme btn-submit-data btn-submit-update-data-list font-size-inherit\">"+this.customButtonValue+"</button>");
					getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].remove();
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
					else 
					{
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

					toast.show();

				}, 1);

				getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].insertAdjacentHTML("beforebegin", "<button type=\"submit\" class=\"btn ph-btn-theme btn-submit-data btn-submit-update-data-list font-size-inherit\">"+this.customButtonValue+"</button>");
				getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].remove();
			});
		},
		submitDataDeleteParentMenu: function(event) 
		{
			event.preventDefault();

			let getDataId = '';

			// Get id form submit
			let getIdFormSubmit = document.getElementById("ph-form-delete-submenu");

			// Get value of attribute in HTML.
			let formActionURL = getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("action");
			let formMethod = getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("method");

			// FormData objects are used to capture HTML form and submit it using fetch or another network method.
			let formData = new FormData(this.$refs.formHTMLdelete);

			// Auto reset after submit data
			this.autoLockButton = getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("auto-lock-button");

			// Auto refresh after submit data
			this.autoRefresh = getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("auto-refresh");

			// Get custom button value after submit
			if (getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("custom-button-value") !== null)
			{
				this.customButtonValueModal.delete = getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("custom-button-value");
			}

			// Get data_id if exist
			if (getIdFormSubmit.querySelector('.data-id') !== null)
			{
				getDataId = '/'+getIdFormSubmit.getElementsByClassName("data-id")[0].getAttribute("value");
			}
			else if (getIdFormSubmit.querySelector('.data-id') == null)
			{
				// If data data_id doesn't exist
				getDataId = '/0';
			}

			// Get class button name to change the button to button loading state .
			getIdFormSubmit.getElementsByClassName("btn-submit-data")[0].insertAdjacentHTML("beforebegin", "<a class=\"btn btn-secondary btn-submit-loading font-size-inherit\">Submitting <div class=\"spinner-border spinner-border-sm text-light ml-1\" role=\"status\"><span class=\"sr-only\">Loading...</span></div></a>");
			getIdFormSubmit.getElementsByClassName("btn-submit-data")[0].remove();

			axios(
			{
				url: formActionURL+getDataId,
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

					// Auto reload after sucess submit data
					// this.listDataMenuParent();

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

						// Auto hide Toast
						// window.setTimeout(function() 
						// {
						// 	// We use toast from Bootstrap 5
						// 	let toastBox = getIdFormSubmit.getElementsByClassName("ph-notice-toast")[0];

						// 	let toast = new bootstrap.Toast(toastBox);

						// 	toast.hide();
						// }, 1500);

						// if (document.querySelector("#modalDeleteSubmenu") !== null ||
						// 	document.querySelector("#modalDeleteSubmenuDetail") !== null)
						// {
						// 	// After Toast is hidden then hide the modal
						// 	window.setTimeout(function() 
						// 	{
						// 		// Auto hide modal delete Submenu
						// 		if (document.querySelector("#modalDeleteSubmenu") !== null)
						// 		{
						// 			const modalDeleteSubmenu = bootstrap.Modal.getOrCreateInstance(document.getElementById("modalDeleteSubmenu"));

						// 			modalDeleteSubmenu.hide();
						// 		}

						// 		// Auto hide modal delete Submenu Detail
						// 		if (document.querySelector("#modalDeleteSubmenuDetail") !== null)
						// 		{
						// 			const modalDeleteSubmenuDetail = bootstrap.Modal.getOrCreateInstance(document.getElementById("modalDeleteSubmenuDetail"));

						// 			modalDeleteSubmenuDetail.hide();
						// 		}
						// 	}, 1800);
						// }

						this.closeDeleteModalSubmenuAfterDelete();

						this.listDataMenu();

						this.listDataSubmenu();

						if (this.autoLockButton == 'true')
						{
							getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].insertAdjacentHTML("beforebegin", "<a class=\"btn btn-success btn-logged font-size-inherit\">Success <i class=\"far fa-check-circle fa-fw mr-1\"></i></div></a>");
							getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].remove();
						}
						else
						{
							getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].insertAdjacentHTML("beforebegin", "<button type=\"submit\" class=\"btn ph-btn-theme btn-submit-data font-size-inherit\">"+this.customButtonValueModal.delete+"</button>");
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

					getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].insertAdjacentHTML("beforebegin", "<button type=\"submit\" class=\"btn ph-btn-theme btn-submit-data font-size-inherit\">"+this.customButtonValueModal.delete+"</button>");
					getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].remove();
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
					else 
					{
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

					toast.show();

				}, 1);

				getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].insertAdjacentHTML("beforebegin", "<button type=\"submit\" class=\"btn ph-btn-theme btn-submit-data font-size-inherit\">"+this.customButtonValueModal.delete+"</button>");
				getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].remove();
			});
		},
		submitDataDeleteSubmenu: function(event) 
		{
			event.preventDefault();

			let getDataId = '';

			// Get id form submit
			let getIdFormSubmit = document.getElementById("ph-form-delete-submenu-detail");
			// let getIdFormSubmitListSubMenuDetail = document.getElementById("ph-form-manage-menu");

			// Get value of attribute in HTML.
			let formActionURL = getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("action");
			let formMethod = getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("method");

			// FormData objects are used to capture HTML form and submit it using fetch or another network method.
			let formData = new FormData(this.$refs.formHTMLdelete);

			// Auto reset after submit data
			this.autoLockButtonModalDelete = getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("auto-lock-button");

			// Auto refresh after submit data
			this.autoRefresh = getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("auto-refresh");

			// Get custom button value after submit
			if (getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("custom-button-value") !== null)
			{
				this.customButtonValueModal.delete = getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("custom-button-value");
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

						const getDataListIndex = getIdFormSubmit.getElementsByClassName("index-data")[0].getAttribute("value");
						const getDataMenuCode = getIdFormSubmit.getElementsByClassName("menu-code")[0].getAttribute("value");

						// Delete data
						this.responseDataSubmenu.splice(getDataListIndex, 1);

						// document.querySelector(".btn-submit-update-data-list").click();

						window.setTimeout(function() 
						{
							document.querySelector(".btn-submit-update-data-list").click();

						}, 200);

						this.closeDeleteModalSubmenuDetailAfterDelete();

						if (this.autoLockButtonModalDelete == 'true')
						{
							getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].insertAdjacentHTML("beforebegin", "<a class=\"btn btn-success btn-logged font-size-inherit\">Success <i class=\"far fa-check-circle fa-fw mr-1\"></i></div></a>");
							getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].remove();
						}
						else
						{
							getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].insertAdjacentHTML("beforebegin", "<input type=\"submit\" class=\"btn ph-btn-theme btn-submit-data font-size-inherit\" value=\""+this.customButtonValueModal.delete+"\">");
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

					getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].insertAdjacentHTML("beforebegin", "<input type=\"submit\" class=\"btn ph-btn-theme btn-submit-data font-size-inherit\" value=\""+this.customButtonValueModal.delete+"\">");
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
					else 
					{
						this.isArrayMessageAfterSubmit = 0;
					}
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
					let toastBox = getIdFormSubmit.getElementsByClassName("ph-notice-toast")[0];

					let toast = new bootstrap.Toast(toastBox);
					
					toast.show();

				}, 1);

				getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].insertAdjacentHTML("beforebegin", "<input type=\"submit\" class=\"btn ph-btn-theme btn-submit-data font-size-inherit\" value=\""+this.customButtonValueModal.delete+"\">");
				getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].remove();
			});
		},
		selectParentMenuforSubmenu: function(event)
		{
			// console.log(event.target.value);

			if (document.querySelector(".parent_name_for_submenu") !== null)
			{
				document.querySelector(".parent_name_for_submenu").setAttribute("value", event.target.value);
			}
		},
		listDataMenu: async function()
		{
			if (document.querySelector(".ph-fetch-listdata-menu") !== null &&
				document.querySelector(".ph-fetch-listdata-menu").getAttribute("data-url") !== null) 
			{
				const url = document.querySelector(".ph-fetch-listdata-menu").getAttribute("data-url");
				
				await axios.get(url)
				.then(response => 
				{
					this.responseDataMenu 		= response.data.data;
					this.responseStatusMenu 	= response.data.status;
					this.responseMessageMenu 	= response.data.message;

					// console.log(this.responseDataMenu);
				})
				.catch(function (error) 
				{
					this.responseStatusMenu = response.data.status;
					this.responseMessageMenu = response.data.message;

					// console.log(error.response);
				})
				.finally(() => 
				{
					this.loadDataComplete();
				});
			}
		},
		listDataSubmenu: async function()
		{
			if (document.querySelector(".ph-fetch-listdata-submenu") !== null &&
				document.querySelector(".ph-fetch-listdata-submenu").getAttribute("data-url") !== null) 
			{
				const url = document.querySelector(".ph-fetch-listdata-submenu").getAttribute("data-url");
				
				await axios.get(url)
				.then(response => 
				{
					this.responseDataSubmenu 	= response.data;
					this.responseStatusSubmenu 	= response.data.status;
					this.responseMessageSubmenu = response.data.message;

					// console.log(this.responseDataMenu);
				})
				.catch(function (error) 
				{
					this.responseStatusSubmenu 	= response.data.status;
					this.responseMessageSubmenu = response.data.message;

					// console.log(error.response);
				})
				.finally(() => 
				{
					this.loadDataComplete();

					this.initColllapseShowSubmenu('collapseHeaderMenu', document.querySelectorAll(".collapse"));
				});
			}
		},
		listDataParentMenu: async function()
		{
			if (document.querySelector(".ph-fetch-listdata-parentmenu") !== null &&
				document.querySelector(".ph-fetch-listdata-parentmenu").getAttribute("data-url") !== null) 
			{
				const url = document.querySelector(".ph-fetch-listdata-parentmenu").getAttribute("data-url");
				
				await axios.get(url)
				.then(response => 
				{
					this.responseDataParentMenu 	= response.data.data;
					this.responseStatusParentMenu 	= response.data.status;
					this.responseMessageParentMenu 	= response.data.message;

					// console.log(this.responseDataMenu);
				})
				.catch(function (error) 
				{
					this.responseStatusParentMenu 	= response.data.status;
					this.responseMessageParentMenu 	= response.data.message;

					// console.log(error.response);
				})
				.finally(() => 
				{
					// this.loadDataComplete();
				});
			}
		},
		listDataRoutes: async function()
		{
			if (document.querySelector(".ph-fetch-listdata-routes") !== null &&
				document.querySelector(".ph-fetch-listdata-routes").getAttribute("data-url") !== null) 
			{
				const url = document.querySelector(".ph-fetch-listdata-routes").getAttribute("data-url");
				
				await axios.get(url)
				.then(response => 
				{
					this.responseDataRoutes 	= response.data.data;
					this.responseStatusRoutes 	= response.data.status;
					this.responseMessageRoutes 	= response.data.message;

					console.log(this.responseDataRoutes);
				})
				.catch(function (error) 
				{
					this.responseStatusRoutes 	= response.data.status;
					this.responseMessageRoutes 	= response.data.message;

					// console.log(error.response);
				})
				.finally(() => 
				{
					// this.loadDataComplete();
				});
			}
		},
		listDataRoles: async function()
		{
			if (document.querySelector(".ph-fetch-listdata-roles") !== null &&
				document.querySelector(".ph-fetch-listdata-roles").getAttribute("data-url") !== null) 
			{
				const url = document.querySelector(".ph-fetch-listdata-roles").getAttribute("data-url");
				
				await axios.get(url)
				.then(response => 
				{
					this.responseDataRoles 		= response.data.data;
					this.responseStatusRoles 	= response.data.status;
					this.responseMessageRoles 	= response.data.message;

					// console.log(this.responseDataMenu);
				})
				.catch(function (error) 
				{
					this.responseStatusRoles 	= response.data.status;
					this.responseMessageRoles 	= response.data.message;

					// console.log(error.response);
				})
				.finally(() => 
				{
					// this.loadDataComplete();
				});
			}
		},
		listDataPermissions: async function()
		{
			if (document.querySelector(".ph-fetch-listdata-permissions") !== null &&
				document.querySelector(".ph-fetch-listdata-permissions").getAttribute("data-url") !== null) 
			{
				const url = document.querySelector(".ph-fetch-listdata-permissions").getAttribute("data-url");
				
				await axios.get(url)
				.then(response => 
				{
					this.responseDataPermissions 		= response.data.data;
					this.responseStatusPermissions 		= response.data.status;
					this.responseMessagePermissions 	= response.data.message;

					// console.log(this.responseDataMenu);
				})
				.catch(function (error) 
				{
					this.responseStatusPermissions 		= response.data.status;
					this.responseMessagePermissions 	= response.data.message;

					// console.log(error.response);
				})
				.finally(() => 
				{
					// this.loadDataComplete();
				});
			}
		},
		loadToastComplete: function(idSubmit)
		{
			// We detect element HTML with class ph-notice in page if there is form with Toast notice
			// we using this because Toast Bootstrap using Vue JS Code to get status Toast, failed or success
			// so default ph-notice is display none and then display block to prevent unrendered element HTML from first open page
			window.setTimeout(function() 
			{	
				if (idSubmit.querySelector(".ph-notice") !== null) 
				{
					if (getComputedStyle(idSubmit.querySelector('.ph-notice'), null).display == 'none') 
					{
						idSubmit.querySelector(".ph-notice").style.display = 'block';

						// console.log('Function loaded');
					}
				}
			}, 1);
		},
		loadDataComplete: function() 
		{
			this.loading = false;

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
		addIconSubmenu: function(event)
		{
			this.responseNewDataSubmenu.submenu_icon = event.target.files[0];
		},
		addSubmenuOld: function()
		{
			if (this.responseNewDataSubmenu.submenu_type == '')
			{
				this.responseStatusToast 		= 'ph-callout-danger';
				this.responseMessageAfterSubmit = 'Please select Submenu Type';
			}
			else if (this.responseNewDataSubmenu.submenu_name == '')
			{
				this.responseStatusToast 		= 'ph-callout-danger';
				this.responseMessageAfterSubmit = 'Please enter Submenu Name';
			}
			else if (this.responseNewDataSubmenu.submenu_link == '')
			{
				this.responseStatusToast 		= 'ph-callout-danger';
				this.responseMessageAfterSubmit = 'Please enter Submenu Link';
			}
			else
			{
				this.responseStatusToast 		= 'ph-callout-success';
				this.responseMessageAfterSubmit = 'Successfully add new submenu';

				// Add data new menu to menu list
				this.responseDataSubmenu.push(this.responseNewDataSubmenu);

				// Check parent_icon is undefined or not
				if (this.responseNewDataSubmenu.submenu_icon !== 'undefined' && 
					this.responseNewDataSubmenu.submenu_icon !== '')
				{
					// Create a new File object
					const myFile 				= new File([this.responseNewDataSubmenu.submenu_icon.slice(0, this.responseNewDataSubmenu.submenu_icon.size, this.responseNewDataSubmenu.submenu_icon.type)], this.responseNewDataSubmenu.submenu_icon.name);
					const getResetFile 			= document.querySelector('.form-control-new-icon').value = '';
					const getBeforeLatestIndex 	= this.responseDataSubmenu.length-1;

					// We using setTimeout to give responseDataMenuParent variable time to add responseNewDataSubmenu data
					// and then insert the file image or icon
					setTimeout(function()
					{
						const fileInput = document.querySelector(".submenu_icon_"+getBeforeLatestIndex);

						// Now let's create a DataTransfer to get a FileList
						const dataTransfer = new DataTransfer();
						dataTransfer.items.add(myFile);
						fileInput.files = dataTransfer.files;

					}, 100);
				}

				window.setTimeout(function() 
				{
					document.querySelector(".btn-submit-update-data-list").click();

				}, 200);

				window.setTimeout(function() 
				{
					this.initColllapseShowSubmenu('collapseHeaderMenu', document.querySelectorAll(".collapse"));
				}.bind(this), 300);

				// Reset form add new menu
				this.responseNewDataSubmenu = { parent_code: '', parent_name: '', submenu_type: '', submenu_name: '', submenu_link: '', submenu_icon_type: '', submenu_icon: '', submenu_icon_url: '', submenu_icon_path: '', submenu_icon_custom: '', submenu_roles: '', submenu_permissions: '' };

				// console.log(this.responseDataMenuParent);
			}

			// We use setTimeout to give responseStatus time set status in Toast and then show the Toast
			// base on responStatus
			window.setTimeout(function() 
			{
				// We use toast from Bootstrap 5
				let toastBox = document.getElementsByClassName("ph-notice-toast")[0];

				let toast = new bootstrap.Toast(toastBox);
				toast.show()
			}, 1);
		},
		addSubmenu: async function()
		{
			// Get id form submit
			let getIdFormSubmit = document.getElementById("ph-submit-data-rp");

			// Get value of attribute in HTML.
			let formActionURL = getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("action");
			let formMethod = getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("method");

			let formData = new FormData(this.$refs.formHTMLCreateNewSubmenu);

			formData.append('submenu_vars_first', JSON.stringify(this.responseNewDataSubmenu));
			formData.append('submenu_link', this.responseNewDataSubmenu.submenu_link);

			// Auto refresh after submit data
			this.autoRefresh = getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("auto-refresh");

			// Get custom button value after submit
			if (getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("custom-button-value") !== null)
			{
				this.customButtonValue = getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("custom-button-value");
			}

			// Get class button name to change the button to button loading state .
			getIdFormSubmit.getElementsByClassName("btn-submit-data")[0].insertAdjacentHTML("beforebegin", "<a class=\"btn btn-secondary btn-submit-loading font-size-inherit\">Submitting <div class=\"spinner-border spinner-border-sm text-light ml-1\" role=\"status\"><span class=\"sr-only\">Loading...</span></div></a>");
			getIdFormSubmit.getElementsByClassName("btn-submit-data")[0].remove();

			await axios(
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

						getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].insertAdjacentHTML("beforebegin", "<button type=\"submit\" class=\"btn ph-btn-theme btn-submit-data font-size-inherit\">"+this.customButtonValue+"</button>");
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

					// Add data new menu to menu list
					this.responseDataSubmenu.push(this.responseNewDataSubmenu);

					// Check parent_icon is undefined or not
					if (this.responseNewDataSubmenu.submenu_icon !== 'undefined' && 
						this.responseNewDataSubmenu.submenu_icon !== '')
					{
						// Create a new File object
						const myFile 				= new File([this.responseNewDataSubmenu.submenu_icon.slice(0, this.responseNewDataSubmenu.submenu_icon.size, this.responseNewDataSubmenu.submenu_icon.type)], this.responseNewDataSubmenu.submenu_icon.name);
						const getResetFile 			= document.querySelector('.form-control-new-icon').value = '';
						const getBeforeLatestIndex 	= this.responseDataSubmenu.length-1;

						// We using setTimeout to give responseDataMenuParent variable time to add responseNewDataSubmenu data
						// and then insert the file image or icon
						setTimeout(function()
						{
							const fileInput = document.querySelector(".submenu_icon_"+getBeforeLatestIndex);

							// Now let's create a DataTransfer to get a FileList
							const dataTransfer = new DataTransfer();
							dataTransfer.items.add(myFile);
							fileInput.files = dataTransfer.files;

						}, 100);
					}

					window.setTimeout(function() 
					{
						document.querySelector(".btn-submit-update-data-list").click();

					}, 200);

					window.setTimeout(function() 
					{
						this.initColllapseShowSubmenu('collapseHeaderMenu', document.querySelectorAll(".collapse"));
					
					}.bind(this), 300);

					// Reset form add new menu
					this.responseNewDataSubmenu = { parent_code: '', parent_name: '', submenu_type: '', submenu_name: '', submenu_link: '', submenu_icon_type: '', submenu_icon: '', submenu_icon_url: '', submenu_icon_path: '', submenu_icon_custom: '', submenu_roles: '', submenu_permissions: '' };
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

					getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].insertAdjacentHTML("beforebegin", "<button type=\"submit\" class=\"btn ph-btn-theme btn-submit-data font-size-inherit\">"+this.customButtonValue+"</button>");
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

				// We use setTimeout to give responseStatus time set status in Toast and then show the Toast
				// base on responStatus
				window.setTimeout(function() 
				{
					// We use toast from Bootstrap 5
					let toastBox = getIdFormSubmit.getElementsByClassName("ph-notice-toast")[0];

					let toast = new bootstrap.Toast(toastBox);

					toast.show();

				}, 1);

				getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].insertAdjacentHTML("beforebegin", "<button type=\"submit\" class=\"btn ph-btn-theme btn-submit-data font-size-inherit\">"+this.customButtonValue+"</button>");
				getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].remove();
			});
		},
		openDeleteModalSubmenu: function(getDataInfo, data_id)
		{
			let getIdFormSubmit = document.getElementById("ph-form-delete-submenu");

			const modalDeleteSubmenu = bootstrap.Modal.getOrCreateInstance(document.getElementById("modalDeleteSubmenu"));

			modalDeleteSubmenu.show();

			if (getIdFormSubmit.querySelector('.data-id') !== null)
			{
				getIdFormSubmit.getElementsByClassName("data-id")[0].setAttribute("value", data_id);
			}

			this.loadToastComplete(getIdFormSubmit);
		},
		closeDeleteModalSubmenu: function()
		{
			// Get id form submit
			let getIdFormSubmit = document.getElementById("ph-form-delete-submenu");

			if (getIdFormSubmit !== null)
			{
				getIdFormSubmit.getElementsByClassName("data-id")[0].setAttribute("value", "");

				// We use toast from Bootstrap 5
				let toastBox = getIdFormSubmit.getElementsByClassName("ph-notice-toast")[0];
				
				let toast = bootstrap.Toast.getOrCreateInstance(toastBox);

				toast.hide();

				if (toast.isShown() == true)
				{
					toastBox.addEventListener('hidden.bs.toast', () => 
					{
						// Hide Modal
						const modalDeleteSubmenu = bootstrap.Modal.getOrCreateInstance(document.getElementById("modalDeleteSubmenu"));

						modalDeleteSubmenu.hide();
					});
				}
				else if (toast.isShown() == false)
				{
					// Hide Modal
					const modalDeleteSubmenu = bootstrap.Modal.getOrCreateInstance(document.getElementById("modalDeleteSubmenu"));

					modalDeleteSubmenu.hide();					
				}

				if (document.querySelector("#modalDeleteSubmenu") !== null)
				{
					let myModalEl = document.getElementById('modalDeleteSubmenu');
					myModalEl.addEventListener('hidden.bs.modal', function(event) 
					{
						if (this.autoLockButton == 'true')
						{
							if (getIdFormSubmit.getElementsByClassName("btn-logged")[0] !== undefined)
							{
								getIdFormSubmit.getElementsByClassName("btn-logged")[0].insertAdjacentHTML("beforebegin", "<button type=\"submit\" class=\"btn ph-btn-theme btn-submit-data font-size-inherit\">"+this.customButtonValueModal.delete+"</button>");
								getIdFormSubmit.getElementsByClassName("btn-logged")[0].remove();
							}
						}
					}.bind(this));
				}
			}
		},
		closeDeleteModalSubmenuAfterDelete:  function()
		{
			// Get id form submit
			let getIdFormSubmit = document.getElementById("ph-form-delete-submenu");

			if (getIdFormSubmit !== null)
			{
				// Give toast time to hide before modal close
				window.setTimeout(function() 
				{
					// We use toast from Bootstrap 5
					let toastBox = getIdFormSubmit.getElementsByClassName("ph-notice-toast")[0];
					
					let toast = bootstrap.Toast.getOrCreateInstance(toastBox);

					toast.hide();

					getIdFormSubmit.getElementsByClassName("data-id")[0].setAttribute("value", "");

					toastBox.addEventListener('hidden.bs.toast', () => 
					{
						// Hide Modal
						const modalDeleteSubmenu = bootstrap.Modal.getOrCreateInstance(document.getElementById("modalDeleteSubmenu"));

						modalDeleteSubmenu.hide();

						if (document.querySelector("#modalDeleteSubmenu") !== null)
						{
							let myModalEl = document.getElementById('modalDeleteSubmenu');
							myModalEl.addEventListener('hidden.bs.modal', function(event) 
							{
								if (this.autoLockButton == 'true')
								{
									if (getIdFormSubmit.getElementsByClassName("btn-logged")[0] !== undefined)
									{
										getIdFormSubmit.getElementsByClassName("btn-logged")[0].insertAdjacentHTML("beforebegin", "<button type=\"submit\" class=\"btn ph-btn-theme btn-submit-data font-size-inherit\">"+this.customButtonValueModal.delete+"</button>");
										getIdFormSubmit.getElementsByClassName("btn-logged")[0].remove();
									}
								}
							}.bind(this));
						}
					});
				}.bind(this), 1300);
			}
		},
		openDeleteModalSubmenuDetail: function(getDataInfo, index, icon_url)
		{
			let getIdFormSubmit = document.getElementById("ph-form-delete-submenu-detail");

			const modalDeleteSubmenuDetail = bootstrap.Modal.getOrCreateInstance(document.getElementById("modalDeleteSubmenuDetail"));

			modalDeleteSubmenuDetail.show();

			getIdFormSubmit.getElementsByClassName("index-data")[0].setAttribute("value", index);
			getIdFormSubmit.getElementsByClassName("path-file")[0].setAttribute("value", icon_url);
			getIdFormSubmit.getElementsByClassName("menu-code")[0].setAttribute("value", getDataInfo.submenu_code);
		
			console.log(getDataInfo.submenu_code);

			this.loadToastComplete(getIdFormSubmit);

			// this.responseDataMenuParent.splice(index, 1);
		},
		closeDeleteModalSubmenuDetail: function()
		{
			// Get id form submit
			let getIdFormSubmit = document.getElementById("ph-form-delete-submenu-detail");

			if (getIdFormSubmit !== null)
			{
				getIdFormSubmit.getElementsByClassName("index-data")[0].setAttribute("value", "");
				getIdFormSubmit.getElementsByClassName("path-file")[0].setAttribute("value", "");

				// We use toast from Bootstrap 5
				let toastBox = getIdFormSubmit.getElementsByClassName("ph-notice-toast")[0];
				
				let toast = bootstrap.Toast.getOrCreateInstance(toastBox);

				toast.hide();

				if (toast.isShown() == true)
				{
					toastBox.addEventListener('hidden.bs.toast', () => 
					{
						// Hide Modal
						const modalDeleteSubmenuDetail = bootstrap.Modal.getOrCreateInstance(document.getElementById("modalDeleteSubmenuDetail"));

						modalDeleteSubmenuDetail.hide();
					});
				}
				else if (toast.isShown() == false)
				{
					// Hide Modal
					const modalDeleteSubmenuDetail = bootstrap.Modal.getOrCreateInstance(document.getElementById("modalDeleteSubmenuDetail"));

					modalDeleteSubmenuDetail.hide();
				}

				if (document.querySelector("#modalDeleteSubmenuDetail") !== null)
				{
					let myModalEl = document.getElementById('modalDeleteSubmenuDetail');
					myModalEl.addEventListener('hidden.bs.modal', function(event) 
					{
						if (this.autoLockButton == 'true')
						{
							if (getIdFormSubmit.getElementsByClassName("btn-logged")[0] !== undefined)
							{
								getIdFormSubmit.getElementsByClassName("btn-logged")[0].insertAdjacentHTML("beforebegin", "<button type=\"submit\" class=\"btn ph-btn-theme btn-submit-data font-size-inherit\">"+this.customButtonValueModal.delete+"</button>");
								getIdFormSubmit.getElementsByClassName("btn-logged")[0].remove();
							}
						}
					}.bind(this));
				}
			}
		},
		closeDeleteModalSubmenuDetailAfterDelete: function()
		{
			// Get id form submit
			let getIdFormSubmit = document.getElementById("ph-form-delete-submenu-detail");

			if (getIdFormSubmit !== null)
			{
				getIdFormSubmit.getElementsByClassName("index-data")[0].setAttribute("value", "");
				getIdFormSubmit.getElementsByClassName("path-file")[0].setAttribute("value", "");

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
						const modalDeleteSubmenuDetail = bootstrap.Modal.getOrCreateInstance(document.getElementById("modalDeleteSubmenuDetail"));

						modalDeleteSubmenuDetail.hide();

						if (document.querySelector("#modalDeleteSubmenuDetail") !== null)
						{
							let myModalEl = document.getElementById('modalDeleteSubmenuDetail');
							myModalEl.addEventListener('hidden.bs.modal', function(event) 
							{
								if (this.autoLockButtonModalDelete == 'true')
								{
									if (getIdFormSubmit.getElementsByClassName("btn-logged")[0] !== undefined)
									{
										getIdFormSubmit.getElementsByClassName("btn-logged")[0].insertAdjacentHTML("beforebegin", "<button type=\"submit\" class=\"btn ph-btn-theme btn-submit-data font-size-inherit\">"+this.customButtonValueModal.delete+"</button>");
										getIdFormSubmit.getElementsByClassName("btn-logged")[0].remove();
									}
								}
							}.bind(this));
						}
					});
				}.bind(this), 1300);
			}
		},
		collapseSubmenu: function(event, key, index)
		{
			event.preventDefault();

			if (document.querySelector("."+key) !== null &&
				document.querySelector("#"+key) !== null)
			{
				const myParentCollapsible 	= document.getElementsByClassName(key)[0];
				const myCollapsible 		= document.getElementById(key);
				const myInstanceCollapsible = bootstrap.Collapse.getOrCreateInstance(myCollapsible);

				myInstanceCollapsible.toggle();
			}
		},
		initColllapseShowSubmenu: function(element, getLength)
		{
			for (var i = 0; i < getLength.length; i++)
			{
				const myCollapsible 		= document.getElementById(element+i);
				const myParentCollapsible 	= document.getElementsByClassName(element+i)[0];			

				if (myCollapsible !== null)
				{
					myCollapsible.addEventListener("show.bs.collapse", event =>
					{
						myParentCollapsible.classList.add("button-collapse");
						myParentCollapsible.classList.remove("button-collapsed");
					});

					myCollapsible.addEventListener("hide.bs.collapse", event =>
					{
						myParentCollapsible.classList.add("button-collapsed");
						myParentCollapsible.classList.remove("button-collapse");
					});
				}
			}
		},
		selectSubmenuType: function(event)
		{
			if (event.target.value == 'page')
			{
				this.listDataRoutes();
			}

			this.responseNewDataSubmenu.submenu_type = event.target.value;
		},
		selectSubmenuLink: function(event)
		{
			const getValue = event.target.value;
			const getSpliteValue = getValue.split("-");

			this.responseNewDataSubmenu.submenu_name = getSpliteValue[0];
			this.responseNewDataSubmenu.submenu_link = getSpliteValue[1];
		},
		selectSubmenuIconType: function(event)
		{
			this.responseNewDataSubmenu.submenu_icon_type = event.target.value;
		}
	},
	mounted()
	{
		this.listDataMenu();

		this.listDataSubmenu();

		this.listDataParentMenu();

		this.listDataRoles();

		this.listDataPermissions();

		this.loadToastComplete(document.getElementById("ph-form-manage-menu"));

		// this.loadToastComplete(document.getElementById("ph-form-manage-menu"));
	}
}).mount('#ph-app-manage-menu');