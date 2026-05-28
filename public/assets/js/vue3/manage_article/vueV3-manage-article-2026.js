const ManageArticleVue3 = createApp(
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
			getQueryStringInURL: '',
			getQuerySearchUser: '',
			getTotalData: '',
			getPage: 1,
			getCurrentPage: '',
			getTotalChecked: 0,
			loading: '',
			loadingNextPage: '',
			loadingDataModal: true,
			pageUrl: '',
			pageCount: '',
			pageRange: '',
			customClass: '',
			customValueButtonCancel: 'Cancel',
			customValueButtonSubmit: 'Submit',
			customButtonValue: 'Submit',
			autoRefreshData: false,
			autoLockButton: false,
			autoResetForm: true,
			autoBlockButton: '',
			autoBlockButtonMobile: '',
			imageEncoded: '',
			showButtonRemoveImage: false,
			showButtonRemoveQuerySearch: false,
			getfilterByStatus: '',
			getfilterByCategory: '',
			getfilterByScheduled: '',
			article:
			{
				id: '',
				uri: '',
				user_id: '',
				category_id: '',
				subcategory_id: '',
				title: '',
				content: '',
				thumb_s: '',
				thumb_l: '',
				visibility: 'public',
				password_protected: '',
				status: 'publish',
				scheduled: 'false',
				updated_at: '',
				created_at: '',
				hours: '00',
				minutes: '00'
			},
			temporary_article:
			{
				id: '',
				uri: '',
				user_id: '',
				category_id: '',
				subcategory_id: '',
				title: '',
				content: '',
				thumb_s: '',
				thumb_l: '',
				visibility: 'public',
				password_protected: '',
				status: 'publish',
				scheduled: 'false',
				updated_at: '',
				created_at: '',
				hours: '00',
				minutes: '00'
			},
			responsiveHiddenCols: [],
			responsiveExpandedRows: {},
			responsiveColDefs: [],
		}
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
			const ths = document.querySelectorAll('#ph-article-table-wrapper thead tr th[data-col-idx]');
			const total = ths.length || 7;
			return total - this.responsiveHiddenCols.length;
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
			getIdFormSubmit.getElementsByClassName("btn-submit-data")[0].insertAdjacentHTML("beforebegin", "<a class=\"btn btn-secondary btn-submit-loading font-size-inherit "+this.autoBlockButton+" "+this.autoBlockButtonMobile+" "+this.autoBlockButtonMobile+"\">Submitting <div class=\"spinner-border spinner-border-sm text-light ml-1\" article=\"status\"><span class=\"sr-only\">Loading...</span></div></a>");
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
			let getIdFormSubmit = document.getElementById("ph-submit-data-article-"+idSubmit);

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
			getIdFormSubmit.getElementsByClassName("btn-submit-data")[0].insertAdjacentHTML("beforebegin", "<a class=\"btn btn-secondary btn-submit-loading font-size-inherit\">Submitting <div class=\"spinner-border spinner-border-sm text-light ml-1\" article=\"status\"><span class=\"sr-only\">Loading...</span></div></a>");
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

						this.closeModalAfterSubmit("ph-submit-data-article-"+idSubmit, "ph-submit-data-article-"+idSubmit);
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
				// Get status response from JSON for Toast Notice
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

			// console.log(this.responseStatusToast);
		},
		submitDataModalCategory: function(event, idSubmit)
		{
			event.preventDefault();

			// Get id form submit
			let getIdFormSubmit = document.getElementById("ph-submit-data-category-"+idSubmit);

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
			getIdFormSubmit.getElementsByClassName("btn-submit-data")[0].insertAdjacentHTML("beforebegin", "<a class=\"btn btn-secondary btn-submit-loading font-size-inherit\">Submitting <div class=\"spinner-border spinner-border-sm text-light ml-1\" article=\"status\"><span class=\"sr-only\">Loading...</span></div></a>");
			getIdFormSubmit.getElementsByClassName("btn-submit-data")[0].remove();

			console.log("Trigered! 1")

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

					console.log("Trigered! 2")

					if (this.autoLockButton == 'true')
					{
						getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].insertAdjacentHTML("beforebegin", "<a class=\"btn btn-success btn-logged font-size-inherit\">Success <i class=\"far fa-check-circle fa-fw mr-1\"></i></div></a>");
						getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].remove();

						// this.closeModalCategoryAfterSubmit("ph-submit-category-article-"+idSubmit, "ph-submit-category-article-"+idSubmit);
						this.closeModalCategoryAfterSubmit("ph-submit-data-category-"+idSubmit, "ph-submit-data-category-"+idSubmit, true, 'ph-submit-data-category-modalRead', 'categoryList')

						console.log("Trigered! 3")
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
				// Get status response from JSON for Toast Notice
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

			// console.log(this.responseStatusToast);
		},
		listData: async function()
		{
			if (document.querySelector(".ph-fetch-listdata-article") !== null &&
				document.querySelector(".ph-fetch-listdata-article").getAttribute("data-url") !== null) 
			{
				const url = document.querySelector(".ph-fetch-listdata-article").getAttribute("data-url");

				this.loading = true;

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
				
				await axios.get(url+this.pageUrl)
				.then(response => 
				{
					this.responseData 		= response.data.data;
					this.getTotalData 		= response.data.total;
					this.pageCount			= response.data.total_page;
					this.pageRange			= response.data.limit;
					
					this.responseStatus 	= response.data.status;
					this.responseMessage 	= response.data.message;
				})
				.catch(function (error) 
				{
					this.responseStatus = response.data.status;
					this.responseMessage = response.data.message;

					// console.log(error.response);
				})
				.finally(() => 
				{
					this.loadDataComplete();
				});
			}
		},
		listDataModal: async function(modalId, dataId)
		{
			if (document.querySelector(".ph-fetch-listdata-article-"+modalId+"-"+dataId) !== null &&
				document.querySelector(".ph-fetch-listdata-article-"+modalId+"-"+dataId).getAttribute("data-url") !== null) 
			{
				const url = document.querySelector(".ph-fetch-listdata-article-"+modalId+"-"+dataId).getAttribute("data-url");

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
				
				await axios.get(url)
				.then(response => 
				{
					this.responseDetailData 		= response.data.data;
					this.responseStatusDetailData 	= response.data.status;
					this.responseMessageDetailData 	= response.data.message;

					this.temporary_article 		= response.data.data;
					this.article 				= response.data.data;
					this.article.hours 			= response.data.data.created_at_hours;
					this.article.minutes 		= response.data.data.created_at_minutes;

					this.imageEncoded 			= this.responseDetailData.thumbnail_large_url;

					// console.log(this.imageEncoded);
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
		detailDataModal: function(modalId, dataId, isWithDataId)
		{
			// console.log(document.querySelector(".ar-fetch-detail-data-article-"+modalId+"-"+dataId));

			if (
				document.querySelector(".ar-fetch-detail-data-article-"+modalId+"-"+dataId) !== null &&
				document.querySelector(".ar-fetch-detail-data-article-"+modalId+"-"+dataId).getAttribute("data-url") !== null) 
			{
				const url = document.querySelector(".ar-fetch-detail-data-article-"+modalId+"-"+dataId).getAttribute("data-url");
				const initAxios = (isWithDataId == true) ? axios.get(url+'/'+dataId) : axios.get(url);

				initAxios
				.then(response => 
				{
					if (response.data.status == 'success')					
					{
						if (modalId == 'modalRead')
						{
							this.responseDataModal.read = response.data.data;

							console.log("Clicked!!");
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
		detailDataModalCategory: function(modalId, dataId, extendData, isWithDataId)
		{
			// console.log(document.querySelector(".ar-fetch-detail-data-article-"+modalId+"-"+dataId));

			if (
				document.querySelector(".ar-fetch-detail-data-category-"+modalId+"-"+dataId) !== null &&
				document.querySelector(".ar-fetch-detail-data-category-"+modalId+"-"+dataId).getAttribute("data-url") !== null) 
			{
				const url = document.querySelector(".ar-fetch-detail-data-category-"+modalId+"-"+dataId).getAttribute("data-url");
				const initAxios = (isWithDataId == true) ? axios.get(url+'/'+dataId) : axios.get(url);

				initAxios
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

				console.log(modalId);
				console.log(this.loadingDataModal);
			}
		},
		showModal: function(modalId, dataId, isWithDataId)
		{
			if (modalId == 'modalRead')
			{
				this.detailDataModal(modalId, dataId, isWithDataId);
			}
			else if (modalId == 'modalUpdate')
			{
				this.detailDataModal(modalId, dataId, isWithDataId);
			}
			else if (modalId == 'modalDelete')
			{
				this.detailDataModal(modalId, dataId, isWithDataId);
			}
		},
		showModalCategory: function(modalId, dataId, isWithDataId)
		{
			if (modalId == 'modalRead')
			{
				this.detailDataModalCategory(modalId, dataId, true, isWithDataId);
			}
			else if (modalId == 'modalUpdate')
			{
				this.detailDataModalCategory(modalId, dataId, true, isWithDataId);
			}
			else if (modalId == 'modalDelete')
			{
				this.detailDataModalCategory(modalId, dataId, true, isWithDataId);
			}
		},
		closeModal: function(idSubmit, modalId, backToPreviousModal, previousModalId, previousDataId)
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

				if (backToPreviousModal == true)
				{
					this.showModal('modalRead', previousDataId, false);

					// Show Previous Modal
					const previousModalDetail = bootstrap.Modal.getOrCreateInstance(document.getElementById(previousModalId));

					previousModalDetail.show();		
				}

				// console.log(backToPreviousModal);
			}
		},
		closeModalCategory: function(idSubmit, modalId, backToPreviousModal, previousModalId, previousDataId)
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

				if (backToPreviousModal == true)
				{
					this.showModalCategory('modalRead', previousDataId, false);

					// Show Previous Modal
					const previousModalDetail = bootstrap.Modal.getOrCreateInstance(document.getElementById(previousModalId));

					previousModalDetail.show();		
				}

				// console.log(backToPreviousModal);
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

						console.log("Trigered!")

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
		closeModalCategoryAfterSubmit: function(idSubmit, modalId, backToPreviousModal, previousModalId, previousDataId)
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

					if (backToPreviousModal == true)
					{
						this.showModalCategory('modalRead', previousDataId, false);

						// Show Previous Modal
						const previousModalDetail = bootstrap.Modal.getOrCreateInstance(document.getElementById(previousModalId));

						previousModalDetail.show();		
					}

					// console.log(backToPreviousModal);
				}.bind(this), 4000);
			}
		},
		searchData: _.debounce(function() 
		{
			const getQuerySearchUser = this.getQuerySearchUser.trim();

			if (document.querySelector(".ph-fetch-listdata-article") !== null &&
				document.querySelector(".ph-fetch-listdata-article").getAttribute("data-url") !== null) 
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

				const url = document.querySelector(".ph-fetch-listdata-article").getAttribute("data-url");

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
		filterByStatus: async function(event) 
		{
			if (document.querySelector(".ph-fetch-listdata-article") !== null &&
				document.querySelector(".ph-fetch-listdata-article").getAttribute("data-url") !== null) 
			{
				this.loadingNextPage = true;
				this.getfilterByStatus = event.target.value;

				const url = document.querySelector(".ph-fetch-listdata-article").getAttribute("data-url");

				let params = (new URL(url)).searchParams;

				if (this.getfilterByCategory !== '')
				{
					params.set('filter_by_category', this.getfilterByCategory);
				}

				if (this.getfilterByScheduled !== '')
				{
					params.set('filter_by_scheduled', this.getfilterByScheduled);
				}

				if (params.toString() !== '')
				{
					this.pageUrl = '?'+params.toString()+'&filter_by_status='+event.target.value;
				}
				else
				{
					this.pageUrl = '?filter_by_status='+event.target.value;
				}

				await axios.get(url+this.pageUrl)
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
		},
		filterByCategory: async function(event) 
		{
			if (document.querySelector(".ph-fetch-listdata-article") !== null &&
				document.querySelector(".ph-fetch-listdata-article").getAttribute("data-url") !== null) 
			{
				this.loadingNextPage = true;
				this.getfilterByCategory = event.target.value;

				const url = document.querySelector(".ph-fetch-listdata-article").getAttribute("data-url");

				let params = (new URL(url)).searchParams;

				if (this.getfilterByStatus !== '')
				{
					params.set('filter_by_status', this.getfilterByStatus);
				}

				if (this.getfilterByScheduled !== '')
				{
					params.set('filter_by_scheduled', this.getfilterByScheduled);
				}

				if (params.toString() !== '')
				{
					this.pageUrl = '?'+params.toString()+'&filter_by_category='+event.target.value;
				}
				else
				{
					this.pageUrl = '?filter_by_category='+event.target.value;
				}

				await axios.get(url+this.pageUrl)
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
		},
		filterByScheduled: async function(event) 
		{
			if (document.querySelector(".ph-fetch-listdata-article") !== null &&
				document.querySelector(".ph-fetch-listdata-article").getAttribute("data-url") !== null) 
			{
				this.loadingNextPage = true;
				this.getfilterByScheduled = event.target.value;

				const url = document.querySelector(".ph-fetch-listdata-article").getAttribute("data-url");

				let params = (new URL(url)).searchParams;

				if (this.getfilterByStatus !== '')
				{
					params.set('filter_by_status', this.getfilterByStatus);
				}

				if (this.getfilterByCategory !== '')
				{
					params.set('filter_by_category', this.getfilterByCategory);
				}

				if (params.toString() !== '')
				{
					this.pageUrl = '?'+params.toString()+'&filter_by_scheduled='+event.target.value;
				}
				else
				{
					this.pageUrl = '?filter_by_scheduled='+event.target.value;
				}

				await axios.get(url+this.pageUrl)
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
		},
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
			const wrapper = document.getElementById('ph-article-table-wrapper');

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
			if (document.querySelector(".ph-fetch-listdata-article") !== null &&
				document.querySelector(".ph-fetch-listdata-article").getAttribute("data-url") !== null) 
			{
				const url = document.querySelector(".ph-fetch-listdata-article").getAttribute("data-url");

				let params = (new URL(url)).searchParams;

				if (this.getQuerySearchUser !== '')
				{
					params.set('search', this.getQuerySearchUser);
				}

				if (this.getfilterByStatus !== '')
				{
					params.set('filter_by_status', this.getfilterByStatus);
				}

				if (this.getfilterByCategory !== '')
				{
					params.set('filter_by_category', this.getfilterByCategory);
				}

				if (this.getfilterByScheduled !== '')
				{
					params.set('filter_by_scheduled', this.getfilterByScheduled);
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

				})
				.catch(function (error) 
				{
					this.responseMessage = error;
				})
				.finally(() => 
				{
					this.loadingData = false;
					this.loadingNextPage = false;
				});
			}
		},
		ckeditor5: function()
		{
			if (typeof ClassicEditor !== 'undefined')
			{
				if (document.querySelector("#editor") !== null)
				{ 
					ClassicEditor
					.create(document.querySelector("#editor"), 
					{
						toolbar: 
						{
							items: [
								'heading',
								'|',
								'bold',
								'italic',
								'link',
								'bulletedList',
								'numberedList',
								'todoList',
								'|',
								'outdent',
								'indent',
								'alignment',
								'undo',
								'redo',
								'|',
								'CKFinder',
								'imageUpload',
								'imageInsert',
								'blockQuote',
								'insertTable',
								// 'mediaEmbed',
								'removeFormat',
								'underline',
								'fontFamily',
								'fontSize',
								'fontColor',
								// 'fontBackgroundColor',
								'highlight',
								// 'code',
								// 'codeBlock',
								// 'sourceEditing',
								'selectAll'
							]
						},
						language: 'en',
						image: {
							styles: [ 'alignCenter', 'alignLeft', 'alignRight' ],
							resizeOptions: [
								{
									name: 'resizeImage:original',
									label: 'Default image width',
									value: null
								},
								{
									name: 'resizeImage:25',
									label: '25% page width',
									value: '25'
								},
								{
									name: 'resizeImage:50',
									label: '50% page width',
									value: '50'
								},
								{
									name: 'resizeImage:75',
									label: '75% page width',
									value: '75'
								},
								{
									name: 'resizeImage:100',
									label: '100% page width',
									value: '100'
								}
							],
							toolbar: [
								'imageTextAlternative', 'toggleImageCaption',
								'|',
								'imageStyle:inline', 'imageStyle:wrapText', 'imageStyle:breakText', 'imageStyle:side',
								'|',
								'resizeImage',
								'linkImage'
							]
						},
						table: {
							contentToolbar: [
								'tableColumn',
								'tableRow',
								'mergeTableCells',
								'tableCellProperties',
								'tableProperties'
							]
						},
						ckfinder: 
						{
							openerMethod: "modal",
							uploadUrl: site_url+"/assets/plugins/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Articles&responseType=json"
						},
						wordCount:
						{
							displayWords: false
						},
						licenseKey: "",
					})
					.then(editor => 
					{
						window.editor = editor;

						editor.model.document.on("change:data", () => 
						{
							document.querySelector("#editor").value = editor.getData();
						});

						const wordCountPlugin = editor.plugins.get("WordCount");
						const wordCountWrapper = document.getElementById("word-count");
						wordCountWrapper.appendChild(wordCountPlugin.wordCountContainer);
					})
					.catch(error => 
					{
						console.error("Oops, something gone wrong!");
						console.error("Please, report the following error in the https://github.com/ckeditor/ckeditor5 with the build id and the error stack trace:" );
						console.warn("Build id: fobzdd17kl4w-7k0k8zs0bnk4");
						console.error(error);
					});
				}
			}
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
			if (document.querySelector(".ar-fetch-detail-article") !== null && 
				document.querySelector(".ar-fetch-detail-article").getAttribute("data-url") !== null)
			{
				const image = new Image();
				const url = document.querySelector(".ar-fetch-detail-article").getAttribute("data-url");

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
			const inputQuerySearch = document.querySelector('input[name="search_article"]');

			inputQuerySearch.value = '';

			this.getQuerySearchUser = '';
			this.showButtonRemoveQuerySearch = false;

			this.searchData();
		
			// console.log(imagePreview);
		},
		selectStatus: function(event)
		{
			this.article.status = event.target.value;

			console.log(event.target.value);
		},		
		selectVisibility: function(event)
		{
			this.temporary_article.visibility = event.target.value;

			console.log(event.target.value);
		},
		selectPublish: function(event)
		{
			this.article.scheduled = event.target.value;

			console.log(event.target.value);
		},
		buttonOkStatusOption: function()
		{
			const setStatus = document.querySelector('select[name="status"]');

			this.article.status = setStatus.value;

			this.hideStatusOptionCollapse();
		},
		buttonCancelStatusOption: function()
		{
			const setStatus = document.querySelector('select[name="status"]');

			setStatus.value = 'publish';

			this.article.status = 'publish';

			this.hideStatusOptionCollapse();
		},
		buttonOkVisibilityOption: function()
		{
			const setVisibility = document.querySelector('select[name="visibility"]');

			this.article.visibility = setVisibility.value;

			this.hideVisibilityOptionCollapse();
		},
		buttonCancelVisibilityOption: function()
		{
			const setVisibility = document.querySelector('select[name="visibility"]');

			setVisibility.value = 'public';

			this.article.visibility = 'public';
			this.temporary_article.visibility = 'public';

			this.hideVisibilityOptionCollapse();
		},
		buttonOkPublishOption: function()
		{
			const setCreatedAt 	= document.querySelector('input[name="created_at"]');
			const setHours 		= document.querySelector('select[name="hours"]');
			const setMinutes 	= document.querySelector('select[name="minutes"]');

			this.article.created_at = setCreatedAt.value;
			this.article.hours 		= (setHours !== null) ? setHours.value : '00';
			this.article.minutes 	= (setMinutes !== null) ? setMinutes.value : '00';

			this.article.scheduled = true;

			this.hidePublishOptionCollapse();
		},
		buttonCancelPublishOption: function()
		{
			const setCreatedAt 	= document.querySelector('input[name="created_at"]');
			const setHours 		= document.querySelector('select[name="hours"]');
			const setMinutes 	= document.querySelector('select[name="minutes"]');

			setCreatedAt.value 	= '';
			setHours.value 		= '00';
			setMinutes.value 	= '00';

			this.article.created_at = '';
			this.article.hours 		= '00';
			this.article.minutes 	= '00';

			this.article.scheduled = false;

			this.hidePublishOptionCollapse();
		},
		hideStatusOptionCollapse: function()
		{
			const collapseHideOption = bootstrap.Collapse.getOrCreateInstance(document.getElementById("collapseEditStatus"));

			collapseHideOption.hide();
		},
		hideVisibilityOptionCollapse: function()
		{
			const collapseVisibilityOption = bootstrap.Collapse.getOrCreateInstance(document.getElementById("collapseEditVisibility"));

			collapseVisibilityOption.hide();
		},
		hidePublishOptionCollapse: function()
		{
			const collapsePublishOption = bootstrap.Collapse.getOrCreateInstance(document.getElementById("collapseEditPublish"));

			collapsePublishOption.hide();
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
		// ============================================================
		// Responsive Table
		// ============================================================

		/**
		 * Returns the usable pixel width for the table.
		 */
		getTableWidth: function()
		{
			const wrapper = document.getElementById('ph-article-table-wrapper');

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
				const table = document.querySelector('#ph-article-table-wrapper table');

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
			if (!this._colNaturalWidths)
			{
				this.measureColWidths(() => { this.recalcResponsive(); });
				return;
			}

			const ths = document.querySelectorAll('#ph-article-table-wrapper thead tr th[data-col-idx]');
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
				2: (info) => info.author || '-',

				3: (info) =>
				{
					if (info.status !== null && info.status_formatted)
					{
						return info.status_formatted;
					}
					return '<span class="badge text-bg-danger">Unknown</span>';
				},

				4: (info) =>
				{
					if (info.scheduled == 'true')
					{
						return '<span class="badge text-bg-warning">Scheduled</span>';
					}
					return '<span class="badge text-bg-secondary">No Scheduled</span>';
				},

				5: (info) =>
				{
					return `${info.created_at_readable || ''}<span class="form-text d-block">(${info.created_at_readforhuman || ''})</span>`;
				},

				6: (info) =>
				{
					const baseUrl = (document.querySelector('.ph-fetch-listdata-article')
						? document.querySelector('.ph-fetch-listdata-article').getAttribute('data-url')
						: '').replace('/listdata', '');

					const editUrl = baseUrl + '/edit/' + info.id;

					return `<a href="${editUrl}" class="btn btn-sm ph-btn-theme-outline py-2 px-3 me-2" style="font-size:.64rem!important"><i class="fas fa-pencil-alt fa-fw"></i></a>
						<button type="button" class="btn btn-sm btn-outline-danger py-2 px-3" style="font-size:.64rem!important"
							onclick="(function(el){if(el)el.click()})(document.querySelector('.ar-fetch-detail-data-article-modalDelete-${info.id}'))">
							<i class="fas fa-trash fa-fw"></i>
						</button>`;
				},
			};

			vm.responsiveColDefs = [];

			const allThs = document.querySelectorAll('#ph-article-table-wrapper thead tr th[data-col-idx]');

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
								'#ph-article-table-wrapper tbody tr:not(.ph-dtr-child-row)'
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

		this.detailData();

		this.ckeditor5();

		const myModalReadEl = document.getElementById('ph-submit-data-article-modalRead');

		if (myModalReadEl !== null)
		{
			myModalReadEl.addEventListener('hidden.bs.modal', event => 
			{
				this.loadingDataModal = true;
			});
		}

		const myModalUpdatelEl = document.getElementById('ph-submit-data-article-modalUpdate');

		if (myModalUpdatelEl !== null)
		{
			myModalUpdatelEl.addEventListener('hidden.bs.modal', event => 
			{
				this.loadingDataModal = true;
			});
		}

		const myModaDeletelEl = document.getElementById('ph-submit-data-article-modalDelete');

		if (myModaDeletelEl !== null)
		{
			myModaDeletelEl.addEventListener('hidden.bs.modal', event => 
			{
				this.loadingDataModal = true;
			});
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
}).mount('#ph-app-manage-article');