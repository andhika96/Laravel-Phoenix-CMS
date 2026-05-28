const ManageCategoryMenuVue3 = createApp(
{
	data() 
	{
		return {
			responseDataCategoryMenu: [{ category_name: '', category_roles: '' }],
			responseNewDataCategoryMenu: { category_name: '', category_roles: '' },
			responseDataRoles: [],
			responseDataRoutes: [],
			responseMessageCategoryMenu: '',
			responseMessageRoles: '',
			responseMessageRoutes: '',
			responseMessageAfterSubmit: '',
			responseStatusCategoryMenu: '',
			responseStatusRoles: '',
			responseStatusRoutes: '',
			responseStatusToast: '',
			isArrayMessageAfterSubmit: 0,
			loading: '',
			customButtonValue: 'Submit',
			autoRefresh: 'false',
			autoLockButton: 'false',
			drag: false,
			dragOptions: 
			{ 
				animation: 200,
				disabled: false,
				ghostClass: "ghost",
				group: "description", 
			},
			myCollapsible: []
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
			let getIdFormSubmit = document.getElementById("ph-form-manage-categorymenu");

			// Get value of attribute in HTML.
			let formActionURL = getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("action");
			let formMethod = getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("method");

			// FormData objects are used to capture HTML form and submit it using fetch or another network method.
			let formData = new FormData(this.$refs.formHTML);

			formData.append('menu_vars', JSON.stringify(this.responseDataCategoryMenu));

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

					// Auto reload after sucess submit data
					this.listDataCategoryMenu();

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

						toast.show()
					}, 1);

					getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].insertAdjacentHTML("beforebegin", "<button type=\"submit\" class=\"btn ph-btn-theme btn-submit-data btn-submit-update-data-list font-size-inherit\">"+this.customButtonValue+"</button>");
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

					toast.show()
				}, 1);

				getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].insertAdjacentHTML("beforebegin", "<button type=\"submit\" class=\"btn ph-btn-theme btn-submit-data btn-submit-update-data-list font-size-inherit\">"+this.customButtonValue+"</button>");
				getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].remove();
			});
		},
		submitDataDelete: function(event) 
		{
			event.preventDefault();

			// Get id form submit
			let getIdFormSubmit = document.getElementById("ph-form-delete-categorymenu");

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
				this.customButtonValue = getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("custom-button-value");
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

							toast.show()
						}, 1);

						const getDataListIndex = getIdFormSubmit.getElementsByClassName("index-data")[0].getAttribute("value");

						// Delete data
						this.responseDataCategoryMenu.splice(getDataListIndex, 1);

						window.setTimeout(function() 
						{
							document.querySelector(".btn-submit-update-data-list").click();
							
						}, 100);

						this.closeDeleteModalCategoryMenuAfterDelete();

						if (this.autoLockButton == 'true')
						{
							getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].insertAdjacentHTML("beforebegin", "<a class=\"btn btn-success btn-logged font-size-inherit\">Success <i class=\"far fa-check-circle fa-fw mr-1\"></i></div></a>");
							getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].remove();
						}
						else
						{
							getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].insertAdjacentHTML("beforebegin", "<button type=\"submit\" class=\"btn ph-btn-theme btn-submit-data btn-submit-update-data-list font-size-inherit\">"+this.customButtonValue+"</button>");
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

					getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].insertAdjacentHTML("beforebegin", "<button type=\"submit\" class=\"btn ph-btn-theme btn-submit-data btn-submit-update-data-list font-size-inherit\">"+this.customButtonValue+"</button>");
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

					toast.show()
				}, 1);

				getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].insertAdjacentHTML("beforebegin", "<button type=\"submit\" class=\"btn ph-btn-theme btn-submit-data btn-submit-update-data-list font-size-inherit\">"+this.customButtonValue+"</button>");
				getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].remove();
			});
		},
		listDataCategoryMenu: async function()
		{
			if (document.querySelector(".ph-fetch-listdata-categorymenu") !== null &&
				document.querySelector(".ph-fetch-listdata-categorymenu").getAttribute("data-url") !== null) 
			{
				const url = document.querySelector(".ph-fetch-listdata-categorymenu").getAttribute("data-url");
				
				await axios.get(url)
				.then(response => 
				{
					this.responseDataCategoryMenu 		= response.data;
					this.responseStatusCategoryMenu 	= response.data.status;
					this.responseMessageCategoryMenu 	= response.data.message;

					// console.log(this.responseDataCategoryMenu);
				})
				.catch(function (error) 
				{
					this.responseStatusCategoryMenu 	= response.data.status;
					this.responseMessageCategoryMenu 	= response.data.message;

					// console.log(error.response);
				})
				.finally(() => 
				{					
					this.loadDataComplete();

					this.initColllapseShowCategoryMenu('collapseHeaderMenu', document.querySelectorAll(".collapse"));
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
		loadToastComplete: function(idSubmit)
		{
			// We detect element HTML with class ph-notice in page if there is form with Toast notice
			// we using this because Toast Bootstrap using Vue JS Code to get status Toast, failed or success
			// so default ph-notice is display none and then display block to prevent unrendered element HTML from first open page
			window.setTimeout(function() 
			{	
				if (idSubmit !== null && idSubmit.querySelector(".ph-notice") !== null) 
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
		addIconCategoryMenu: function(event)
		{
			this.responseNewDataCategoryMenu.category_icon = event.target.files[0];
		},
		addCategoryMenu: function()
		{
			if (this.responseNewDataCategoryMenu.category_name == '')
			{
				this.responseStatusToast 		= 'ph-callout-danger';
				this.responseMessageAfterSubmit = 'Please enter Category Menu Name';
			}
			// else if (this.responseNewDataCategoryMenu.category_roles == '')
			// {
			// 	this.responseStatusToast 		= 'ph-callout-danger';
			// 	this.responseMessageAfterSubmit = 'Please select role menu';
			// }
			else
			{
				this.responseStatusToast 		= 'ph-callout-success';
				this.responseMessageAfterSubmit = 'Successfully add new category menu';

				// Add data new menu to menu list
				this.responseDataCategoryMenu.push(this.responseNewDataCategoryMenu);

				window.setTimeout(function() 
				{
					document.querySelector(".btn-submit-update-data-list").click();
				}, 200);

				window.setTimeout(function() 
				{
					this.initColllapseShowCategoryMenu('collapseHeaderMenu', document.querySelectorAll(".collapse"));
				}.bind(this), 300);

				// Reset form add new menu
				this.responseNewDataCategoryMenu = { category_name: '', category_roles: '' };

				// console.log(this.responseDataMenuCategory);
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
		openDeleteModalCategoryMenu: function(getDataInfo, index, icon_url)
		{
			let getIdFormSubmit = document.getElementById("ph-form-delete-categorymenu");

			const modalDeleteCategoryMenu = bootstrap.Modal.getOrCreateInstance(document.getElementById("modalDeleteCategoryMenu"));

			modalDeleteCategoryMenu.show();

			getIdFormSubmit.getElementsByClassName("index-data")[0].setAttribute("value", index);
			getIdFormSubmit.getElementsByClassName("path-file")[0].setAttribute("value", icon_url);

			this.loadToastComplete(getIdFormSubmit);

			// this.responseDataMenuCategory.splice(index, 1);
		},
		closeDeleteModalCategoryMenu: function()
		{
			// Get id form submit
			let getIdFormSubmit = document.getElementById("ph-form-delete-categorymenu");

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
						const modalDeleteCategoryMenu = bootstrap.Modal.getOrCreateInstance(document.getElementById("modalDeleteCategoryMenu"));

						modalDeleteCategoryMenu.hide();
					});
				}
				else if (toast.isShown() == false)
				{
					// Hide Modal
					const modalDeleteCategoryMenu = bootstrap.Modal.getOrCreateInstance(document.getElementById("modalDeleteCategoryMenu"));

					modalDeleteCategoryMenu.hide();
				}

				if (document.querySelector("#modalDeleteCategoryMenu") !== null)
				{
					let myModalEl = document.getElementById('modalDeleteCategoryMenu');
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
		closeDeleteModalCategoryMenuAfterDelete: function()
		{
			// Get id form submit
			let getIdFormSubmit = document.getElementById("ph-form-delete-categorymenu");

			if (getIdFormSubmit !== null)
			{
				getIdFormSubmit.getElementsByClassName("index-data")[0].setAttribute("value", "");
				getIdFormSubmit.getElementsByClassName("path-file")[0].setAttribute("value", "");

				window.setTimeout(function() 
				{
					// We use toast from Bootstrap 5
					let toastBox = getIdFormSubmit.getElementsByClassName("ph-notice-toast")[0];
					
					let toast = bootstrap.Toast.getOrCreateInstance(toastBox);

					toast.hide();

					toastBox.addEventListener('hidden.bs.toast', () => 
					{
						// Hide Modal
						const modalDeleteCategoryMenu = bootstrap.Modal.getOrCreateInstance(document.getElementById("modalDeleteCategoryMenu"));

						modalDeleteCategoryMenu.hide();

						if (document.querySelector("#modalDeleteCategoryMenu") !== null)
						{
							let myModalEl = document.getElementById('modalDeleteCategoryMenu');
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
		collapseCategoryMenu: function(event, key, index)
		{
			event.preventDefault();

			if (document.querySelector("."+key) !== null &&
				document.querySelector("#"+key) !== null)
			{
				const myCategoryCollapsible 	= document.getElementsByClassName(key)[0];
				const myCollapsible 		= document.getElementById(key);
				const myInstanceCollapsible = bootstrap.Collapse.getOrCreateInstance(myCollapsible);

				myInstanceCollapsible.toggle();
			}
		},
		initColllapseShowCategoryMenu: function(element, getLength)
		{
			for (var i = 0; i < getLength.length; i++)
			{
				const myCollapsible 		= document.getElementById(element+i);
				const myCategoryCollapsible = document.getElementsByClassName(element+i)[0];			

				if (myCollapsible !== null)
				{
					myCollapsible.addEventListener("show.bs.collapse", event =>
					{
						myCategoryCollapsible.classList.add("button-collapse");
						myCategoryCollapsible.classList.remove("button-collapsed");
					});

					myCollapsible.addEventListener("hide.bs.collapse", event =>
					{
						myCategoryCollapsible.classList.add("button-collapsed");
						myCategoryCollapsible.classList.remove("button-collapse");
					});
				}
			}
		},
		selectCategoryType: function(event)
		{
			if (event.target.value == 'page')
			{
				this.listDataRoutes();
			}

			this.responseNewDataCategoryMenu.category_type = event.target.value;
		},
		selectCategoryLink: function(event)
		{
			const getValue = event.target.value;
			const getSpliteValue = getValue.split("-");

			this.responseNewDataCategoryMenu.category_name = getSpliteValue[0];
			this.responseNewDataCategoryMenu.category_link = getSpliteValue[1];
		},
		selectCategoryMenuIconType: function(event)
		{
			this.responseNewDataCategoryMenu.category_icon_type = event.target.value;
		}
	},
	mounted()
	{
		this.listDataCategoryMenu();

		this.listDataRoles();
					
		this.loadToastComplete(document.getElementById("ph-form-manage-categorymenu"));

		this.loadToastComplete(document.getElementById("ph-submit-data-rp"));
	}
}).mount('#ph-app-manage-categorymenu');