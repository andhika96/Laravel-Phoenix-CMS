const ManageCoverImageVue3 = createApp(
{
	data()
	{
		return {
			responseData: [],
			responseGetData: [],
			responseDetailData: [],
			responseDataModal: 
			{
				create: [],
				read: [],
				update: [],
				delete: [],
			},
			responseMessage: '',
			responseMessageDetailData: '',
			responseMessageAfterSubmit: '',
			responseStatus: '',
			responseStatusDetailData: '',
			responseStatusToast: '',
			responseStatusAfterSubmit: '',
			getFormCoverImage:
			{
				uri: '',
				cover_type: 'background_image', 
				cover_page_name: '',
				cover_slideshow_direction: 'horizontal',
				cover_desktop_slideshow_direction: 'horizontal',
				cover_mobile_slideshow_direction: 'horizontal',
				cover_autoplay_slideshow: 'active',
				cover_autoplay_slideshow_interval: 3000,
				cover_looping_slideshow: 'active' 				
			},
			getListFormBackgroundImage: 
			[
				{ 
					id: 0, 
					cover_type: '', 
					cover_page_name: '', 
					desktop_image: '', 
					desktop_image_file: '', 
					desktop_image_full_url: '',
					desktop_content_position: 'center-center',
					mobile_image: '', 
					mobile_image_file: '',
					mobile_image_full_url: '',
					mobile_content_position: 'center-center',
					title: '',
					description: '',
					background_overlay: 'rgba(0, 0, 0, 0.3)',
					background_size: 'md_size',
					disable_content: 'inactive',
					cover_is_active: 'active',
					link:
					{
						content: '',
						is_active: 'inactive'
					},
					second_content:
					{
						type: 'text',
						text: '',
						link: '',
						desktop_position: 'center-center',
						mobile_position: 'center-center',
						is_active: 'inactive'
					},
					countdown:
					{
						content: '',
						content_default: ref(new Date()),
						is_active: 'inactive',
						desktop_position: 'default',
						mobile_position: 'default',
						position: 'default'
					},
					button:
					[
						{
							title: '',
							link: '',
							is_active: 'inactive'
						},
						{
							title: '',
							link: '',
							is_active: 'inactive'
						}
					]
				}
			],	
			getListFormBackgroundImageOriginal: 
			[
				{ 
					id: 0, 
					cover_type: '', 
					cover_page_name: '', 
					desktop_image: '', 
					desktop_image_file: '', 
					desktop_image_full_url: '',
					desktop_content_position: 'center-center',
					mobile_image: '', 
					mobile_image_file: '',
					mobile_image_full_url: '',
					mobile_content_position: 'center-center',
					title: '',
					description: '',
					background_overlay: 'rgba(0, 0, 0, 0.3)',
					background_size: 'md_size',
					disable_content: 'inactive',
					cover_is_active: 'active',
					link:
					{
						content: '',
						is_active: 'inactive'
					},
					second_content:
					{
						type: 'text',
						text: '',
						link: '',
						desktop_position: 'center-center',
						mobile_position: 'center-center',
						is_active: 'inactive'
					},
					countdown:
					{
						content: '',
						content_default: ref(new Date()),
						is_active: 'inactive',
						desktop_position: 'default',
						mobile_position: 'default',
						position: 'default'
					},
					button:
					[
						{
							title: '',
							link: '',
							is_active: 'inactive'
						},
						{
							title: '',
							link: '',
							is_active: 'inactive'
						}
					]
				}
			],	
			getListFormSlideshow: 
			[
				{ 
					id: 0, 
					cover_type: '', 
					cover_page_name: '', 
					desktop_image: '', 
					desktop_image_file: '', 
					desktop_image_full_url: '',
					desktop_content_position: 'center-center',
					mobile_image: '', 
					mobile_image_file: '',
					mobile_image_full_url: '',
					mobile_content_position: 'center-center',
					title: '',
					description: '',
					background_overlay: 'rgba(0, 0, 0, 0.3)',
					background_size: 'md_size',
					disable_content: 'inactive',
					cover_is_active: 'active',
					link:
					{
						content: '',
						is_active: 'inactive'
					},
					second_content:
					{
						type: 'text',
						text: '',
						link: '',
						desktop_position: 'center-center',
						mobile_position: 'center-center',
						is_active: 'inactive'
					},
					countdown:
					{
						content: '',
						content_default: ref(new Date()),
						is_active: 'inactive',
						desktop_position: 'default',
						mobile_position: 'default',
						position: 'default'
					},
					button:
					[
						{
							title: '',
							link: '',
							is_active: 'inactive'
						},
						{
							title: '',
							link: '',
							is_active: 'inactive'
						}
					]
				}
			],	
			getListFormSlideshowOriginal: 
			[
				{ 
					id: 0, 
					cover_type: '', 
					cover_page_name: '', 
					desktop_image: '', 
					desktop_image_file: '', 
					desktop_content_position: 'center-center',
					mobile_image: '', 
					mobile_image_file: '',
					mobile_content_position: 'center-center',
					title: '',
					description: '',
					background_overlay: 'rgba(0, 0, 0, 0.3)',
					background_size: 'md_size',
					disable_content: 'inactive',
					cover_is_active: 'active',
					link:
					{
						content: '',
						is_active: 'inactive'
					},
					second_content:
					{
						type: 'text',
						text: '',
						link: '',
						desktop_position: 'center-center',
						mobile_position: 'center-center',
						is_active: 'inactive'
					},
					countdown:
					{
						content: '',
						content_default: ref(new Date()),
						is_active: 'inactive',
						desktop_position: 'default',
						mobile_position: 'default',
						position: 'default'
					},
					button:
					[
						{
							title: '',
							link: '',
							is_active: 'inactive'
						},
						{
							title: '',
							link: '',
							is_active: 'inactive'
						}
					]
				}
			],		
			getQueryStringInURL: '',
			getQuerySearchUser: '',
			getTotalData: '',
			getPage: 1,
			getCurrentPage: '',
			getTotalChecked: 0,
			loading: '',
			loadingNextPage: '',
			loadingDataModal: true,
			loadingDetailData: '',
			pageUrl: '',
			pageCount: '',
			pageRange: '',
			customClass: '',
			customValueButtonCancel: 'Cancel',
			customValueButtonSubmit: 'Submit',
			customButtonValue: 'Submit',
			autoRefreshData: 'false',
			autoLockButton: 'false',
			autoResetForm: 'true',
			autoBlockButton: '',
			autoBlockButtonMobile: '',
			imageEncoded: '',
			showButtonRemoveImage: false,
			showButtonRemoveQuerySearch: false,
			drag: false,
			dragOptions: 
			{ 
				animation: 200,
				disabled: false,
				ghostClass: "ghost",
				group: "description", 
			},
			asdasd: ref(new Date())
		}
	},
	components:
	{
		draggable: vuedraggable,
		paginate: VuejsPaginateNext,
		vSelect: window["vue-select"],
		VueDatePicker
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

			formData.append('cover_image_bgimage_js', JSON.stringify(this.getListFormBackgroundImage));
			formData.append('cover_image_slideshow_js', JSON.stringify(this.getListFormSlideshow));

			// console.log(this.getListFormSlideshow);

			// Auto refresh after submit data
			if (getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("auto-refresh") !== null)
			{
				this.autoRefreshData = getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("auto-refresh");
			}

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
			getIdFormSubmit.getElementsByClassName("btn-submit-data")[0].insertAdjacentHTML("beforebegin", "<a class=\"btn btn-secondary btn-submit-loading font-size-inherit "+this.autoBlockButton+" "+this.autoBlockButtonMobile+" "+this.autoBlockButtonMobile+"\">Submitting <div class=\"spinner-border spinner-border-sm text-light ml-1\" coverimage=\"status\"><span class=\"sr-only\">Loading...</span></div></a>");
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

							toast.show();

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

					if (this.autoRefreshData == 'true')
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

					toast.show();

				}, 1);

				getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].insertAdjacentHTML("beforebegin", "<button type=\"submit\" class=\"btn ph-btn-theme btn-submit-data font-size-inherit "+this.autoBlockButton+" "+this.autoBlockButtonMobile+"\">"+this.customButtonValue+"</button>");
				getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].remove();
			});
		},
		submitDataModal: function(event, idSubmit)
		{
			event.preventDefault();

			// Get id form submit
			let getIdFormSubmit = document.getElementById("ph-submit-data-coverimage-"+idSubmit);

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
			getIdFormSubmit.getElementsByClassName("btn-submit-data")[0].insertAdjacentHTML("beforebegin", "<a class=\"btn btn-secondary btn-submit-loading font-size-inherit\">Submitting <div class=\"spinner-border spinner-border-sm text-light ml-1\" coverimage=\"status\"><span class=\"sr-only\">Loading...</span></div></a>");
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

						this.closeModalAfterSubmit("ph-submit-data-coverimage-"+idSubmit, "ph-submit-data-coverimage-"+idSubmit);
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
				this.responseStatusToast = 'ph-callout-danger';
				this.responseStatusAfterSubmit = ref(false);

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
		submitDataModalDetailData: function(event, idSubmit)
		{
			event.preventDefault();

			// Get id form submit
			let getIdFormSubmit = document.getElementById("ph-submit-data-coverimage-"+idSubmit);

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
			getIdFormSubmit.getElementsByClassName("btn-submit-data")[0].insertAdjacentHTML("beforebegin", "<a class=\"btn btn-secondary btn-submit-loading font-size-inherit\">Submitting <div class=\"spinner-border spinner-border-sm text-light ml-1\" coverimage=\"status\"><span class=\"sr-only\">Loading...</span></div></a>");
			getIdFormSubmit.getElementsByClassName("btn-submit-data")[0].remove();

			const getIndexValue = document.getElementsByClassName('index-form-list')[0].value;
			const getTypeValue = document.getElementsByClassName('type-form-list')[0].value;

			axios(
			{
				url: formActionURL+'?cover_type='+getTypeValue+'&index_key='+getIndexValue,
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

						this.deleteFormCoverImage(getIdFormSubmit, 'edit');

						window.setTimeout(function() 
						{
							document.querySelector(".btn-submit-data").click();
						
						}, 200);

						this.closeModalAfterSubmit("ph-submit-data-coverimage-"+idSubmit, "ph-submit-data-coverimage-"+idSubmit);
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
		listData: async function()
		{
			if (document.querySelector(".ph-fetch-listdata-coverimage") !== null &&
				document.querySelector(".ph-fetch-listdata-coverimage").getAttribute("data-url") !== null) 
			{
				// Get id form submit
				let getIdFormSubmit = document.getElementById("ph-form-submit-data");

				const url = document.querySelector(".ph-fetch-listdata-coverimage").getAttribute("data-url");

				// // Auto refresh after submit data
				// if (getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("auto-refresh") !== null)
				// {
				// 	this.autoRefreshData = getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("auto-refresh");
				// }

				this.autoRefreshData = 'false';

				if (this.autoRefreshData == 'false')
				{
					let params = (new URL(url)).searchParams;

					if (this.getQuerySearchUser !== '')
					{
						params.set('title', this.getQuerySearchUser);
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
				}
				
				await axios.get(url+this.pageUrl)
				.then(response => 
				{
					this.responseData 		= response.data.data;
					this.getTotalData 		= response.data.total;
					this.pageCount			= response.data.total_page;
					this.pageRange			= response.data.limit;
					
					if (this.autoRefreshData == 'false')
					{
						this.responseStatus 	= response.data.status;
						this.responseMessage 	= response.data.message;
					}					
				})
				.catch(function (error) 
				{
					this.responseStatus = response.data.status;
					this.responseMessage = response.data.message;

					// console.log(error.response);
				})
				.finally(() => 
				{
					if (this.autoRefreshData == 'false')
					{
						this.loadDataComplete();
					}
				});
			}
		},
		detailData: async function()
		{
			if (document.querySelector(".ph-fetch-detail-data") !== null &&
				document.querySelector(".ph-fetch-detail-data").getAttribute("data-url") !== null) 
			{
				const url = document.querySelector(".ph-fetch-detail-data").getAttribute("data-url");

				this.loadingDetailData = true;
				
				await axios.get(url)
				.then(response => 
				{
					this.responseDetailData 		= response.data.data;
					this.responseStatusDetailData 	= response.data.status;
					this.responseMessageDetailData 	= response.data.message;

					if (this.responseDetailData.cover_bgimage_vars !== 'null')
					{
						this.getListFormBackgroundImage = JSON.parse(this.responseDetailData.cover_bgimage_vars);
					}

					if (this.responseDetailData.cover_slideshow_vars !== 'null')
					{
						this.getListFormSlideshow = JSON.parse(this.responseDetailData.cover_slideshow_vars);
					
						for (let i = 0; i < this.getListFormSlideshow.length; i++)
						{
							if (this.getListFormSlideshow[i]['second_content'] == null)
							{
								this.getListFormSlideshow[i]['second_content'] = 
								{
									type: 'text',
									text: '',
									link: '',
									desktop_position: 'center-center',
									mobile_position: 'center-center',
									is_active: 'inactive'
								};
							}
						}
					}

					// console.log(this.getListFormSlideshow[1]['second_content']);

					this.getFormCoverImage.cover_type = this.responseDetailData.cover_type;

					// if (this.responseDetailData.cover_slideshow_vars !== 'null')
					// {
					// 	this.getListFormSlideshow = this.responseDetailData.cover_slideshow_vars;
					// }

					// console.log('Background Image: '+this.getListFormBackgroundImage);
					// console.log('Slideshow: '+this.getListFormSlideshow);

					// console.log(this.responseDetailData.cover_bgimage_vars);

					// console.log(this.getFormCoverImage.cover_type);
				
					// console.log(this.responseDetailData);
				})
				.catch(function (error) 
				{
					this.responseStatusDetailData 	= 'failed';
					this.responseMessageDetailData 	= error.response;

					// console.log(error.response);
				})
				.finally(() => 
				{
					window.setTimeout(function() 
					{
						if (document.querySelector("#color-picker") !== undefined)
						{
							$(function() 
							{
								$(".color-picker").spectrum(
								{
									type: "component",
									preferredFormat: "rgb",
									showInput: true,
									showInitial: true,
									showPalette: true,
									change: function(color) 
									{								
										// Create native event
										const nativeEventInputBackgroundOverlay = new Event('input', { bubbles: true });

										// Get index form
										const getDataIndexBackgroundOverlay = this.attributes['data-index-form'].value;

										console.log(getDataIndexBackgroundOverlay);

										if (color !== null)
										{
											if (document.querySelector('input[name="cover_image[background_image]['+getDataIndexBackgroundOverlay+'][background_overlay]"]') !== null)
											{
												document.querySelector('input[name="cover_image[background_image]['+getDataIndexBackgroundOverlay+'][background_overlay]"]').value = color.toRgbString();
												document.querySelector('input[name="cover_image[background_image]['+getDataIndexBackgroundOverlay+'][background_overlay]"]').dispatchEvent(nativeEventInputBackgroundOverlay);
											}
											else if (document.querySelector('input[name="cover_image[slideshow]['+getDataIndexBackgroundOverlay+'][background_overlay]"]') !== null)
											{
												document.querySelector('input[name="cover_image[slideshow]['+getDataIndexBackgroundOverlay+'][background_overlay]"]').value = color.toRgbString();
												document.querySelector('input[name="cover_image[slideshow]['+getDataIndexBackgroundOverlay+'][background_overlay]"]').dispatchEvent(nativeEventInputBackgroundOverlay);
											}
										}
									}
								});
							});
						}

					}, 1);

					this.loadingDetailData = false;
				});
			}
		},
		detailDataForCoverType: async function()
		{
			if (document.querySelector(".ph-fetch-detail-data") !== null &&
				document.querySelector(".ph-fetch-detail-data").getAttribute("data-url") !== null) 
			{
				const url = document.querySelector(".ph-fetch-detail-data").getAttribute("data-url");
				
				await axios.get(url)
				.then(response => 
				{
					this.responseDetailData 		= response.data.data;
					this.responseStatusDetailData 	= response.data.status;
					this.responseMessageDetailData 	= response.data.message;

					if (this.responseDetailData.cover_bgimage_vars !== 'null')
					{
						this.getListFormBackgroundImage = JSON.parse(this.responseDetailData.cover_bgimage_vars);
					}

					if (this.responseDetailData.cover_slideshow_vars !== 'null')
					{
						this.getListFormSlideshow = JSON.parse(this.responseDetailData.cover_slideshow_vars);
					}

					console.log(this.getListFormSlideshow);

					// this.getFormCoverImage.cover_type = this.responseDetailData.cover_type;

					// console.log(this.responseDetailData.cover_bgimage_vars);
				})
				.catch(function (error) 
				{
					this.responseStatusDetailData 	= response.data.status;
					this.responseMessageDetailData 	= response.data.message;

					// console.log(error.response);
				})
				.finally(() => 
				{
					// Empty
				});
			}
		},
		detailDataModal: function(modalId, dataId, extendData)
		{
			if (
				document.querySelector(".ar-fetch-detail-data-coverimage-"+modalId+"-"+dataId) !== null &&
				document.querySelector(".ar-fetch-detail-data-coverimage-"+modalId+"-"+dataId).getAttribute("data-url") !== null) 
			{
				const url = document.querySelector(".ar-fetch-detail-data-coverimage-"+modalId+"-"+dataId).getAttribute("data-url");

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
		showModalDetailData: function(index, type, modalId)
		{
			// Hide Modal
			const modalDetail = bootstrap.Modal.getOrCreateInstance(document.getElementById(modalId));

			const setIndexValue = document.getElementsByClassName('index-form-list')[0].value = index;
			const setTypeValue = document.getElementsByClassName('type-form-list')[0].value = type;

			modalDetail.show();	
		},
		closeModal: function(modalId)
		{
			// Hide Modal
			const modalDetail = bootstrap.Modal.getOrCreateInstance(document.getElementById(modalId));

			modalDetail.hide();	
		},
		closeModalBeforeSubmit: function(idSubmit, modalId)
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

					if (toast.isShown() == true)
					{
						toast.hide();

						toastBox.addEventListener('hidden.bs.toast', () => 
						{
							// Hide Modal
							const modalDetail = bootstrap.Modal.getOrCreateInstance(document.getElementById(modalId));

							modalDetail.hide();
						});
					}
					else
					{
						// Hide Modal
						const modalDetail = bootstrap.Modal.getOrCreateInstance(document.getElementById(modalId));

						modalDetail.hide();
					}

					// console.log(toast.isShown());

				}.bind(this), 1);
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
		searchData: _.debounce(function() 
		{
			const getQuerySearchUser = this.getQuerySearchUser.trim();

			if (document.querySelector(".ph-fetch-listdata-coverimage") !== null &&
				document.querySelector(".ph-fetch-listdata-coverimage").getAttribute("data-url") !== null) 
			{
				this.loadingNextPage = true;
				
				if (this.getQuerySearchUser !== '')
				{
					this.showButtonRemoveQuerySearch = true;
				}

				const url = document.querySelector(".ph-fetch-listdata-coverimage").getAttribute("data-url");

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

				if (document.querySelector(".ph-data-options") !== null) 
				{
					if (getComputedStyle(document.querySelector('.ph-data-options'), null).display == 'block') 
					{
						document.querySelector(".ph-data-options").style.display = 'none';
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

			if (document.querySelector(".ph-data-options") !== null) 
			{
				if (event.target.checked == true)
				{
					this.getTotalChecked += 1;
				}
				else if (event.target.checked == false)
				{
					this.getTotalChecked -= 1;
				}
			}
		},
		clickPaginate: async function (page) 
		{
			if (document.querySelector(".ph-fetch-listdata-coverimage") !== null &&
				document.querySelector(".ph-fetch-listdata-coverimage").getAttribute("data-url") !== null) 
			{
				const url = document.querySelector(".ph-fetch-listdata-coverimage").getAttribute("data-url");

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

				this.page = page;
				this.loadingNextPage = true;

				await axios.get(url+this.pageUrl)
				.then(response => 
				{
					if (this.currentPage >= this.pageCount) 
					{
						this.currentPage = '';
					}

					this.responseData 	= response.data.data;
					this.totalData 		= response.data.total;
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
					this.loadingData = false;
					this.loadingNextPage = false;
				});
			}
		},
		addFormCoverimage: function(part_section)
		{
			if (this.getListFormSlideshow.length < 12)
			{
				this.getListFormSlideshow.push(
					{ 
						id: undefined, 
						cover_type: '', 
						cover_page_name: '', 
						desktop_image: '', 
						desktop_image_file: '', 
						desktop_image_full_url: '',
						desktop_content_position: 'center-center',
						mobile_image: '', 
						mobile_image_file: '',
						mobile_image_full_url: '',
						mobile_content_position: 'center-center',
						title: '',
						description: '',
						background_overlay: 'rgba(0, 0, 0, 0.3)',
						background_size: 'md_size',
						disable_content: 'inactive',
						cover_is_active: 'active',
						link:
						{
							content: '',
							is_active: 'inactive'
						},
						second_content:
						{
							type: 'text',
							text: '',
							link: '',
							desktop_position: 'center-center',
							mobile_position: 'center-center',
							is_active: 'inactive'
						},
						countdown:
						{
							content: '',
							content_default: ref(new Date()),
							is_active: 'inactive',
							desktop_position: 'default',
							mobile_position: 'default',
							position: 'default'
						},
						button:
						[
							{
								title: '',
								link: '',
								is_active: 'inactive'
							},
							{
								title: '',
								link: '',
								is_active: 'inactive'
							}
						]					
					});

				$(document).ready(function() 
				{
					if (document.querySelector(".color-picker") !== undefined)
					{
						$(".color-picker").spectrum(
						{
							type: "component",
							preferredFormat: "rgb",
							showInput: true,
							showInitial: true,
							showPalette: true
						});
					}
				});
			}
			else
			{
				alert('Maximum form for cover image is '+this.getListFormSlideshow.length);
			}
		},
		deleteFormCoverImage: function(modalId, form)
		{
			const getTypeValue = document.getElementsByClassName('type-form-list')[0].value;

			if (getTypeValue == 'background_image')
			{
				if (this.getListFormBackgroundImage.length > 1)
				{
					const getIndexValue = document.getElementsByClassName('index-form-list')[0].value;

					this.getListFormBackgroundImage.splice(getIndexValue, 1);

					// this.closeModal(modalId);
				}
				else
				{
					// Get id form submit
					let getIdFormSubmit = document.getElementById(modalId);

					if (getIdFormSubmit !== null)
					{
						// We set default responStatus to text-bg-danger because absolutely output JSON is error or failed
						this.responseStatusToast = 'ph-callout-danger';

						this.responseMessageAfterSubmit = 'You cannot delete this item because it is the primary data. Please delete it directly from the cover image list.';

						// We use setTimeout to give responseStatus time set status in Toast and then show the Toast
						// base on responStatus
						window.setTimeout(function() 
						{
							// We use toast from Bootstrap 5
							let toastBox = getIdFormSubmit.getElementsByClassName("ph-notice-toast")[0];

							let toast = new bootstrap.Toast(toastBox);

							toast.show();

						}, 1);

						// console.log('Cannot delete this data.');
					}
				}
			}

			if (getTypeValue == 'slideshow')
			{
				if (this.getListFormSlideshow.length > 1)
				{
					const getIndexValue = document.getElementsByClassName('index-form-list')[0].value;

					this.getListFormSlideshow.splice(getIndexValue, 1);

					if (form == 'add')
					{
						this.closeModal(modalId);
					}

					// window.setTimeout(function() 
					// {
					// 	document.querySelector(".btn-submit-data").click();
					
					// }, 100);

					// this.closeModal(modalId);
				}
				else
				{
					// Get id form submit
					let getIdFormSubmit = document.getElementById(modalId);

					if (getIdFormSubmit !== null)
					{
						// We set default responStatus to text-bg-danger because absolutely output JSON is error or failed
						this.responseStatusToast = 'ph-callout-danger';

						this.responseMessageAfterSubmit = 'You cannot delete this item because it is the primary data. Please delete it directly from the cover image list.';

						// We use setTimeout to give responseStatus time set status in Toast and then show the Toast
						// base on responStatus
						window.setTimeout(function() 
						{
							// We use toast from Bootstrap 5
							let toastBox = getIdFormSubmit.getElementsByClassName("ph-notice-toast")[0];

							let toast = new bootstrap.Toast(toastBox);

							toast.show();

						}, 1);

						// console.log('Cannot delete this data.');
					}
				}
			}

			// console.log(this.getListFormSlideshow.length);
		},
		changeCoverType: function(event)
		{
			this.detailDataForCoverType();

			this.getFormCoverImage.cover_type = event.target.value;

			// console.log(id);
		},
		changeSecondContentType: function(index, event)
		{
			this.getListFormSlideshow[index]['second_content']['type'] = event.target.value;

			// console.log(id);
			console.log(this.getListFormSlideshow[index]['second_content']['type']);
		},
		previewImage: function(event)
		{
			const fileReader = new FileReader();
			const image = new Image();
			const files = event.target.files;

			const imagePreview = document.getElementById("img-preview");

			const filename = files[0].name;
			fileReader.addEventListener('load', () => 
			{
				image.src = fileReader.result;

				image.addEventListener('load', () => 
				{
					// console.log(image.width+' x '+image.height);

					if (image.width > image.height)
					{
						imagePreview.classList.remove("h-100");

						// console.log('Its Landscape');
					} 
					else if (image.width < image.height)
					{
						imagePreview.classList.add("h-100");

						// console.log('Its Portrait');
					}
					else if (image.width == 0 && image.height == 0)
					{
						imagePreview.classList.add("h-100");
					}
					else
					{
						imagePreview.classList.remove("h-100");

						// console.log('Its Square');
					}

					this.imageEncoded = fileReader.result;

					this.showButtonRemoveImage = true;

					// console.log(this.imageEncoded);
				});
			});

			fileReader.readAsDataURL(files[0]);
		},
		previewImageExist: function()
		{
			if (document.querySelector(".ar-fetch-detail-coverimage") !== null && 
				document.querySelector(".ar-fetch-detail-coverimage").getAttribute("data-url") !== null)
			{
				const image = new Image();
				const url = document.querySelector(".ar-fetch-detail-coverimage").getAttribute("data-url");

				axios.get(url)
				.then(response => 
				{
					const imagePreview = document.getElementById("img-preview");

					if (response.data[0].status != 'failed')
					{
						this.imageEncode = response.data[0].get_thumbnail;

						this.showButtonRemoveImage = true;

						image.src = this.imageEncode;

						image.onload = function()
						{
							if (image.width > image.height)
							{
								imagePreview.classList.remove("h-100");
								// imagePreview.classList.add("h-100");

								// console.log('Its Landscape');
							} 
							else if (image.width >= image.height)
							{
								imagePreview.classList.remove("h-100");
								imagePreview.classList.add("h-100");

								console.log('Its Landscape 2');
							} 
							else if (image.width < image.height)
							{
								imagePreview.classList.add("h-100");

								// console.log('Its Portrait');
							}
							else if (image.width == 0 && image.height == 0)
							{
								imagePreview.classList.add("h-100");
							}
							else
							{
								imagePreview.classList.remove("h-100");

								// console.log('Its Square');
							}
						}
					}
				})
				.catch(function(error) 
				{
					console.log(error);
				})
				.finally(() => 
				{ 
					// Empty
				});
			}
		},
		removePreviewImage: function()
		{
			const inputThumbnail = document.querySelector('input[name="thumbnail"]');
			const imagePreview = document.querySelector('img[id="img-preview"]');

			inputThumbnail.value = '';
			imagePreview.src = '';

			this.imageEncode = '';
			this.showButtonRemoveImage = false;
		
			// console.log(imagePreview);
		},
		removeQuerySearch: function()
		{
			const inputQuerySearch = document.querySelector('input[name="search_coverimage"]');

			inputQuerySearch.value = '';

			this.getQuerySearchUser = '';
			this.showButtonRemoveQuerySearch = false;

			this.searchData();
		
			// console.log(imagePreview);
		},
		ucFirst: function(str) 
		{
			if (! str) return ""; // Handle empty or null strings
			return str.charAt(0).toUpperCase() + str.slice(1);
		},
		ucWords: function(str) 
		{
			if (! str) return ""; // Handle empty or null strings
			return String(str).toLowerCase().replace(/\b[a-z]/g, (l) => l.toUpperCase());
		},
		strReplace: function(old_str, new_str, str)
		{
			if (! str) return ""; // Handle empty or null strings
			return str.replace(old_str, new_str);
		},
		removeHtmlTag: function(htmlString)
		{
			if (! htmlString) return "";
			// Regular expression to match HTML tags (e.g., <div>, <p>, <span>)
			return htmlString.replace(/(<([^>]+)>)/ig, '');
		},
		truncateText: function(text, maxLength)
		{
			if (!text || text.length <= maxLength) return text;
			return text.substring(0, maxLength).trim() + '...';
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
		accordionButton: function(index)
		{
			const accordionButtonItem = document.querySelector(".ph-draggable-icon-"+index);
			const accordionButtonItem2 = document.querySelector(".ph-end-icon-"+index);

			if (accordionButtonItem !== null)
			{
				if (accordionButtonItem.classList.contains("ph-draggable-icon-not-collapsed"))
				{
					accordionButtonItem.classList.remove("ph-draggable-icon-not-collapsed");
				}
				else
				{
					accordionButtonItem.classList.add("ph-draggable-icon-not-collapsed");
				}
			}

			if (accordionButtonItem2 !== null)
			{
				if (accordionButtonItem2.classList.contains("ph-end-icon-not-collapsed"))
				{
					accordionButtonItem2.classList.remove("ph-end-icon-not-collapsed");
				}
				else
				{
					accordionButtonItem2.classList.add("ph-end-icon-not-collapsed");
				}
			}

			// console.log(accordionButtonItem);
		},
		updateDateForCountDownBackgroundImage: function(index, date)
		{
			const day = date.getDate();
			const month = date.getMonth() + 1;
			const year = date.getFullYear();

			const hours = date.getHours();
			const minutes = date.getMinutes();

			// Format the day with a leading zero if it's a single digit
			const formattedDay = String(day).padStart(2, '0');

			this.getListFormBackgroundImage[index]['countdown']['content'] = formattedDay+'/'+month+'/'+year+' '+hours+':'+minutes;

			// console.log(this.getListFormBackgroundImage[index]['countdown']['content']);
		},
		updateDateForCountDownSlideshow: function(index, date)
		{
			// const index = 0;
			// this.getListFormSlideshow[index]['countdown']['content'] = '';

			// console.log(date);

			const day = date.getDate();
			const month = date.getMonth() + 1;
			const year = date.getFullYear();

			const hours = date.getHours();
			const minutes = date.getMinutes();

			// Format the day with a leading zero if it's a single digit
			const formattedDay = String(day).padStart(2, '0');

			this.getListFormSlideshow[index]['countdown']['content'] = formattedDay+'/'+month+'/'+year+' '+hours+':'+minutes;

			// console.log(this.getListFormSlideshow[index]['countdown']['content']);

			// console.log(date.getUTCDate());
			// console.log(date);

			// console.log('index: '+index+' => date: '+date);
		}
	},
	mounted()
	{
		this.listData(false);

		this.detailData();

		const myModalReadEl = document.getElementById('ph-submit-data-coverimage-modalRead');

		if (myModalReadEl !== null)
		{
			myModalReadEl.addEventListener('hidden.bs.modal', event => 
			{
				this.loadingDataModal = true;
			});
		}

		const myModalUpdatelEl = document.getElementById('ph-submit-data-coverimage-modalUpdate');

		if (myModalUpdatelEl !== null)
		{
			myModalUpdatelEl.addEventListener('hidden.bs.modal', event => 
			{
				this.loadingDataModal = true;
			});
		}

		const myModaDeletelEl = document.getElementById('ph-submit-data-coverimage-modalDelete');

		if (myModaDeletelEl !== null)
		{
			myModaDeletelEl.addEventListener('hidden.bs.modal', event => 
			{
				this.loadingDataModal = true;
			});
		}

		if (document.querySelector("#color-picker") !== undefined)
		{
			$(function() 
			{
				$(".color-picker").spectrum(
				{
					type: "component",
					preferredFormat: "rgb",
					showInput: true,
					showInitial: true,
					showPalette: true,
					change: function(color) 
					{								
						// Create native event
						const nativeEventInputBackgroundOverlay = new Event('input', { bubbles: true });

						// Get index form
						const getDataIndexBackgroundOverlay = this.attributes['data-index-form'].value;

						// console.log(color.toHexString());
						// console.log(color.toRgbString());
						// console.log(getDataIndexBackgroundOverlay);

						if (color !== null)
						{
							if (document.querySelector('input[name="cover_image[background_image]['+getDataIndexBackgroundOverlay+'][background_overlay]"]') !== null)
							{
								document.querySelector('input[name="cover_image[background_image]['+getDataIndexBackgroundOverlay+'][background_overlay]"]').value = color.toRgbString();
								document.querySelector('input[name="cover_image[background_image]['+getDataIndexBackgroundOverlay+'][background_overlay]"]').dispatchEvent(nativeEventInputBackgroundOverlay);
							}
							else if (document.querySelector('input[name="cover_image[slideshow]['+getDataIndexBackgroundOverlay+'][background_overlay]"]') !== null)
							{
								document.querySelector('input[name="cover_image[slideshow]['+getDataIndexBackgroundOverlay+'][background_overlay]"]').value = color.toRgbString();
								document.querySelector('input[name="cover_image[slideshow]['+getDataIndexBackgroundOverlay+'][background_overlay]"]').dispatchEvent(nativeEventInputBackgroundOverlay);
							}
						}
					}
				});
			});
		}
	},
	watch: 
	{
		getFormCoverImage: 
		{
			handler: function(newValue, oldValue) 
			{
				// console.log('User object changed:', newValue);

				window.setTimeout(function() 
				{
					if (document.querySelector("#color-picker") !== undefined)
					{
						$(function() 
						{
							$(".color-picker").spectrum(
							{
								type: "component",
								preferredFormat: "rgb",
								showInput: true,
								showInitial: true,
								showPalette: true,
								change: function(color) 
								{								
									// Create native event
									const nativeEventInputBackgroundOverlay = new Event('input', { bubbles: true });

									// Get index form
									const getDataIndexBackgroundOverlay = this.attributes['data-index-form'].value;

									console.log(getDataIndexBackgroundOverlay);

									if (color !== null)
									{
										if (document.querySelector('input[name="cover_image[background_image]['+getDataIndexBackgroundOverlay+'][background_overlay]"]') !== null)
										{
											document.querySelector('input[name="cover_image[background_image]['+getDataIndexBackgroundOverlay+'][background_overlay]"]').value = color.toRgbString();
											document.querySelector('input[name="cover_image[background_image]['+getDataIndexBackgroundOverlay+'][background_overlay]"]').dispatchEvent(nativeEventInputBackgroundOverlay);
										}
										else if (document.querySelector('input[name="cover_image[slideshow]['+getDataIndexBackgroundOverlay+'][background_overlay]"]') !== null)
										{
											document.querySelector('input[name="cover_image[slideshow]['+getDataIndexBackgroundOverlay+'][background_overlay]"]').value = color.toRgbString();
											document.querySelector('input[name="cover_image[slideshow]['+getDataIndexBackgroundOverlay+'][background_overlay]"]').dispatchEvent(nativeEventInputBackgroundOverlay);
										}
									}
								}
							});
						});
					}

				}, 200);
			},
			deep: true // Watch for changes within the object
		}
	}
}).mount('#ph-app-manage-coverimage');