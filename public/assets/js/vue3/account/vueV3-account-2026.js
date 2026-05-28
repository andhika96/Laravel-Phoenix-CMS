const ManageAccountVue3 =  createApp(
{
	data()
	{
		return {
			responseData: [],
			responseDetailData: [],
			responseMessage: '',
			responseMessageAfterSubmit: '',
			responseStatus: '',
			responseStatusToast: '',
			getAvatarImageChanged: '',
			getQueryStringInURL: '',
			getQuerySearchUser: '',
			getTotalData: '',
			getPage: 1,
			getCurrentPage: '',
			getTotalChecked: 0,
			loading: '',
			loadingNextPage: '',
			loadingChangeProfilePicture: false,
			pageUrl: '',
			pageCount: '',
			pageRange: '',
			customClass: '',
			customButtonValue: 'Submit',
			autoRefresh: 'false',
			autoLockButton: 'false',
			autoBlockButton: '',
			autoBlockButtonMobile: '',
			editUserTabActive: 'general',
			editProfilePicture: false,
			reinitCroppie: ''
		}
	},
	components:
	{
		paginate: VuejsPaginateNext
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
			if (getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("auto-refresh") !== null)
			{
				this.autoRefresh = getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("auto-refresh");
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
		submitDataProfilePicture: async function(event) 
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
			if (getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("auto-refresh") !== null)
			{
				this.autoRefresh = getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("auto-refresh");
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

			// Get custom class for button after submit
			if (getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("custom-class") !== null)
			{
				this.customClass = getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("custom-class");
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
			getIdFormSubmit.getElementsByClassName("btn-submit-data")[0].insertAdjacentHTML("beforebegin", "<a class=\"btn btn-secondary btn-submit-loading font-size-inherit "+this.customClass+" "+this.autoBlockButton+" "+this.autoBlockButtonMobile+" "+this.autoBlockButtonMobile+"\">Submitting <div class=\"spinner-border spinner-border-sm text-light ml-1\" role=\"status\"><span class=\"sr-only\">Loading...</span></div></a>");
			getIdFormSubmit.getElementsByClassName("btn-submit-data")[0].remove();

			if (document.querySelector(".btn-cancel-submit") !== null)
			{
				document.getElementsByClassName("btn-cancel-submit")[0].setAttribute("disabled", "disabled");
			}

			const getImg = {profilepic:document.getElementsByClassName("cr-image")[0].getAttribute("src")};
			const getExt = getImg.profilepic.match(/[^:/]\w+(?=;|,)/)[0];

			this.reinitCroppie.result(
			{
				type: 'canvas',
				size: 'original',
				format: getExt,
				circle: true
			}).then(function(res)
			{
				formData.append('profilePicture', res);
				formData.append('profilePictureExt', getExt);

				// console.log(res);

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

					console.log(this.responseStatusToast);

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

						this.getAvatarImageChanged = response.data.image;
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

							this.editProfilePicture = false;

							if (this.editProfilePicture == false)
							{
								window.setTimeout(function() 
								{
									if (document.querySelectorAll(".ph-avatar") !== null)
									{
										const phAvatar = document.querySelectorAll(".ph-avatar");

										for (const itemAvatar of phAvatar)
										{
											itemAvatar.src = site_url+'/storage/'+response.data.image;
										}
									}

								}, 1);
							}

							getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].insertAdjacentHTML("beforebegin", "<button type=\"submit\" class=\"btn ph-btn-theme btn-submit-data font-size-inherit "+this.customClass+" "+this.autoBlockButton+" "+this.autoBlockButtonMobile+" "+this.autoBlockButtonMobile+"\">"+this.customButtonValue+"</button>");
							getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].remove();
						}
						else
						{
							window.setTimeout(function() 
							{
								window.location.href = response.data.redirect_url;

							}, 500);

							// this.editProfilePicture = false;

							// We use toast from Bootstrap 5
							let toastBox = getIdFormSubmit.getElementsByClassName("ph-notice-toast")[0];

							let toast = new bootstrap.Toast(toastBox);

							toast.hide();

							getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].insertAdjacentHTML("beforebegin", "<a class=\"btn btn-success btn-logged font-size-inherit "+this.customClass+" "+this.autoBlockButton+" "+this.autoBlockButtonMobile+"\">Success <i class=\"far fa-check-circle fa-fw mr-1\"></i></div></a>");
							getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].remove();
						}

						if (getIdFormSubmit.querySelector(".btn-cancel-submit") !== null)
						{
							getIdFormSubmit.getElementsByClassName("btn-cancel-submit")[0].removeAttribute("disabled");
						}

						console.log(this.editProfilePicture);
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

						getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].insertAdjacentHTML("beforebegin", "<button type=\"submit\" class=\"btn ph-btn-theme btn-submit-data font-size-inherit "+this.customClass+" "+this.autoBlockButton+" "+this.autoBlockButtonMobile+"\">"+this.customButtonValue+"</button>");
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

					getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].insertAdjacentHTML("beforebegin", "<button type=\"submit\" class=\"btn ph-btn-theme btn-submit-data font-size-inherit "+this.customClass+" "+this.autoBlockButton+" "+this.autoBlockButtonMobile+"\">"+this.customButtonValue+"</button>");
					getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].remove();
				});
			}.bind(this));
		},
		listData: async function()
		{
			if (document.querySelector(".ph-fetch-listdata") !== null &&
				document.querySelector(".ph-fetch-listdata").getAttribute("data-url") !== null) 
			{
				const url = document.querySelector(".ph-fetch-listdata").getAttribute("data-url");

				if (this.autoRefresh == 'false')
				{
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
				}
				
				await axios.get(url+this.pageUrl)
				.then(response => 
				{
					this.responseData 		= response.data.data;
					this.getTotalData 		= response.data.total;
					this.pageCount			= response.data.total_page;
					this.pageRange			= response.data.limit;
					
					if (this.autoRefresh == 'false')
					{
						this.responseStatus 	= response.data.status;
						this.responseMessage 	= response.data.message;
					}

					// console.log(this.responseData);
				})
				.catch(function (error) 
				{
					this.responseStatus = response.data.status;
					this.responseMessage = response.data.message;

					// console.log(error.response);
				})
				.finally(() => 
				{
					if (this.autoRefresh == 'false')
					{
						this.loadDataComplete();
					}

					// this.loadToastComplete();
					
					// console.log(this.responseStatus);
					// console.log(this.responseMessage);
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

				const url = document.querySelector(".ph-fetch-listdata").getAttribute("data-url");

				axios.get(url+'?fullname='+getQuerySearchUser)
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
		loadDataDynamicComplete: function(idSubmit) 
		{
			// Get id form submit
			let getIdFormSubmit = document.getElementById(idSubmit);

			if (getIdFormSubmit !== null)
			{
				this.loading = false;

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
		changeUserEditTab: function(event)
		{
			this.editUserTabActive = event;

			// console.log(this.editUserTabActive);
		},
		changeProfilePictureUser: function()
		{
			this.editProfilePicture = true;

			this.loadingChangeProfilePicture = true;

			window.setTimeout(function() 
			{				
				this.loadingChangeProfilePicture = false;

				if (this.loadingChangeProfilePicture == false)
				{
					window.setTimeout(function() 
					{			
						this.initCroppie();

					}.bind(this), 1);
				}

			}.bind(this), 500);
		},
		changeProfilePictureUserCancel: function()
		{
			this.editProfilePicture = false;

			if (this.getAvatarImageChanged !== '')
			{
				window.setTimeout(function() 
				{
					if (document.querySelectorAll(".ph-avatar") !== null)
					{
						const phAvatar = document.querySelectorAll(".ph-avatar");

						for (const itemAvatar of phAvatar)
						{
							itemAvatar.src = site_url+'/storage/'+this.getAvatarImageChanged;
						}
					}
					
				}.bind(this), 1);
			}
		},
		initCroppie: function() 
		{
			if (document.querySelector(".croppie") !== null &&
				document.querySelector(".upload") !== null)
			{
				const croppie = document.querySelector('.croppie');
				const initCroppie = new Croppie(croppie, 
				{
					url: '../assets/images/undraw_profile_pic_ic5t.svg',
					enableExif: true,
					viewport: 
					{
						width: 130,
						height: 130,
						type: 'circle'
					},
					boundary:
					{
						width: 130,
						height: 130
					}
				});

				this.reinitCroppie = initCroppie;

				document.querySelector('.upload').addEventListener('change', function(ev)
				{
					if (this.files && this.files[0]) 
					{
						if (/^image/.test(this.files[0].type)) 
						{
							const reader = new FileReader();
							reader.onload = function(e) 
							{
								initCroppie.bind( 
								{
									url: e.target.result
								});
							}

							reader.readAsDataURL(this.files[0]);
						}
					}
				});

				console.log('Croppie initiated!');
			}
		}
	},
	mounted()
	{
		this.listData();

		this.loadToastComplete();

		this.loadDataDynamicComplete('edit-user-page');

		if (document.querySelector(".croppie") !== null &&
			document.querySelector(".upload") !== null)
		{
			const croppie = document.querySelector('.croppie');
			const initCroppie = new Croppie(croppie, 
			{
				url: '../assets/images/undraw_profile_pic_ic5t.svg',
				enableExif: true,
				viewport: 
				{
					width: 130,
					height: 130,
					type: 'circle'
				},
				boundary:
				{
					width: 130,
					height: 130
				}
			});

			this.reinitCroppie = initCroppie;

			document.querySelector('.upload').addEventListener('change', function(ev)
			{
				if (this.files && this.files[0]) 
				{
					if (/^image/.test(this.files[0].type)) 
					{
						const reader = new FileReader();
						reader.onload = function(e) 
						{
							initCroppie.bind( 
							{
								url: e.target.result
							});
						}

						reader.readAsDataURL(this.files[0]);
					}
				}
			});
		}

		// console.log('Testing mounted');
	}
}).mount('#ph-app-account');