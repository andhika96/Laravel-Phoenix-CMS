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
			getTotalData: 0,
			getPage: 1,
			getCurrentPage: 1,
			getTotalChecked: 0,
			loading: true,
			loadingNextPage: false,
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
			thumbnailOriginalPreview: '',
			thumbnailFileSelected: false,
			thumbnailSource: 'upload',
			thumbnailCkfinderUrl: '',
			thumbnailCkfinderLabel: '',
			articleThumbnailRemove: false,
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
			selectedArticleIds: [],
            articleStatuses: [],
            statusCounts: {},
            articlePageSize: 15,
            articleSort: 'newest',
            showArticleFilters: false,
            listError: '',
            bulkStatus: '',
            bulkSubmitting: false,
            bulkNotice: '',
            bulkFailed: false,
		}
	},
    computed:
    {
        responsiveVisibleColCount() { return 7 - this.responsiveHiddenCols.length; },
        allArticlesSelected() { return this.responseData.length > 0 && this.responseData.every(info => this.selectedArticleIds.includes(info.id)); },
        someArticlesSelected() { return this.selectedArticleIds.length > 0 && !this.allArticlesSelected; },
        activeArticleFilterCount() { return Number(this.getfilterByCategory !== '') + Number(this.getfilterByScheduled !== ''); },
        articleRangeStart() { return this.getTotalData ? (this.getCurrentPage - 1) * this.articlePageSize + 1 : 0; },
        articleRangeEnd() { return Math.min(this.getCurrentPage * this.articlePageSize, this.getTotalData); },
        articleStatusLabel() { return this.articleStatuses.find(status => status.code_name === this.getfilterByStatus)?.name || 'All'; },
        bulkStatusLabel() { return this.articleStatuses.find(status => status.code_name === this.bulkStatus)?.name || ''; },
        articleCategoryLabel()
        {
            const select = document.getElementById('ph-article-category');
            return select ? [...select.options].find(option => option.value === String(this.getfilterByCategory))?.textContent || '' : '';
        },
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
            const source = document.querySelector('.ph-fetch-listdata-article');
            if (!source) return;
            const requestId = this._articleRequestId = (this._articleRequestId || 0) + 1;
            const params = new URLSearchParams({ page: this.getPage, per_page: this.articlePageSize, sort: this.articleSort });
            for (const [key, value] of Object.entries({ search: this.getQuerySearchUser.trim(), filter_by_status: this.getfilterByStatus, filter_by_category: this.getfilterByCategory, filter_by_scheduled: this.getfilterByScheduled }))
            {
                if (value !== '') params.set(key, value);
            }
            this.loading = !this._articleListLoaded;
            this.loadingNextPage = true;
            this.listError = '';
            this.selectedArticleIds = [];
            this.responsiveExpandedRows = {};
            try
            {
                const response = await axios.get(source.getAttribute('data-url'), { params });
                if (requestId !== this._articleRequestId) return;
                const data = response.data;
                if (!Array.isArray(data.data)) throw new Error('Invalid article list response');
                const lastPage = Math.max(1, Number(data.total_page) || 1);
                if (this.getPage > lastPage)
                {
                    this.getPage = lastPage;
                    return this.listData();
                }
                this.responseData = data.data;
                this.getTotalData = Number(data.total) || 0;
                this.getCurrentPage = Number(data.current_page) || 1;
                this.getPage = this.getCurrentPage;
                this.pageCount = lastPage;
                this.statusCounts = data.status_counts || { all: this.getTotalData };
                this.responseStatus = data.status;
                this.responseMessage = data.message;
                this._articleListLoaded = true;
            }
            catch (error)
            {
                if (requestId !== this._articleRequestId) return;
                this.listError = error.response?.data?.message || 'Daftar artikel gagal dimuat. Silakan coba lagi.';
            }
            finally
            {
                if (requestId === this._articleRequestId) this.loadDataComplete();
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

					this.imageEncoded 			= this.responseDetailData.thumbnail_large_url || this.responseDetailData.thumbnail_small_url || '';
					this.thumbnailOriginalPreview = this.imageEncoded;
					this.thumbnailSource = 'upload';
					this.thumbnailCkfinderUrl = '';
					this.thumbnailCkfinderLabel = '';
					this.thumbnailFileSelected = false;
					this.articleThumbnailRemove = false;
					this.showButtonRemoveImage = Boolean(this.imageEncoded);

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
            this.refreshArticleList();
        }, 300),
        refreshArticleList: function()
        {
            if (this.bulkSubmitting) return;
            this.getPage = 1;
            this.getCurrentPage = 1;
            return this.listData();
        },
        filterByStatus: function(value)
        {
            if (this.bulkSubmitting) return;
            this.getfilterByStatus = typeof value === 'string' ? value : value.target.value;
            return this.refreshArticleList();
        },
        resetArticleFilters: function()
        {
            if (this.bulkSubmitting) return;
            this.getQuerySearchUser = '';
            this.getfilterByStatus = '';
            this.getfilterByCategory = '';
            this.getfilterByScheduled = '';
            return this.refreshArticleList();
        },
        sortArticles: function(column)
        {
            if (this.bulkSubmitting) return;
            this.articleSort = column === 'title'
                ? (this.articleSort === 'title_asc' ? 'title_desc' : 'title_asc')
                : (this.articleSort === 'newest' ? 'oldest' : 'newest');
            return this.refreshArticleList();
        },
        loadDataComplete: function()
        {
            this.loading = false;
            this.loadingNextPage = false;
            this.$nextTick(() =>
            {
                const wrapper = document.getElementById('ph-article-table-wrapper');
                if (!wrapper) return;
                if (this._responsiveWrapper !== wrapper)
                {
                    this._resizeObserver?.disconnect();
                    this._responsiveWrapper = wrapper;
                    if (typeof ResizeObserver !== 'undefined')
                    {
                        this._resizeObserver = new ResizeObserver(() => this.recalcResponsive());
                        this._resizeObserver.observe(wrapper);
                    }
                    else if (!this._responsiveResizeHandler)
                    {
                        this._responsiveResizeHandler = () => this.recalcResponsive();
                        window.addEventListener('resize', this._responsiveResizeHandler);
                    }
                }
                this.recalcResponsive();
            });
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
        clickSelectAll: function(event)
        {
            this.selectedArticleIds = event.target.checked ? this.responseData.map(info => info.id) : [];
        },
        clickPaginate: function(page)
        {
            if (this.bulkSubmitting) return;
            this.getPage = page;
            return this.listData();
        },
        isArticleScheduled: function(info)
        {
            return info.scheduled === true || info.scheduled === 'true' || info.scheduled === 1 || info.scheduled === '1';
        },
        openBulkConfirmation: function()
        {
            if (!this.selectedArticleIds.length || !this.bulkStatus || this.bulkSubmitting || this.loadingNextPage) return;
            this.bulkNotice = '';
            this.bulkFailed = false;
            bootstrap.Modal.getOrCreateInstance(document.getElementById('ph-article-bulk-confirm')).show();
        },
        submitArticleBulk: async function()
        {
            if (!this.selectedArticleIds.length || !this.bulkStatus || this.bulkSubmitting) return;
            this.bulkSubmitting = true;
            this.bulkNotice = '';
            this.bulkFailed = false;
            const form = new FormData(this.$refs.formHTML);
            form.set('mode', 'bulk_update');
            try
            {
                const response = await axios.post(this.$refs.formHTML.action, form, { headers: { 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' } });
                if (response.data.status !== 'success') throw new Error(response.data.message || 'Perubahan status gagal.');
                bootstrap.Modal.getOrCreateInstance(document.getElementById('ph-article-bulk-confirm')).hide();
                this.bulkNotice = response.data.message;
                this.bulkStatus = '';
                await this.listData();
            }
            catch (error)
            {
                this.bulkFailed = true;
                this.bulkNotice = error.response?.data?.message || error.message || 'Perubahan status gagal. Silakan coba lagi.';
            }
            finally
            {
                this.bulkSubmitting = false;
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
								'strikethrough',
								'fontFamily',
								'fontSize',
								'fontColor',
								// 'fontBackgroundColor',
								'highlight',
								'code',
								'codeBlock',
								'horizontalLine',
								// 'sourceEditing',
								'selectAll'
							]
						},
						language: 'en',
						image: {
							styles: { options: ['inline', 'block', 'side', 'alignLeft', 'alignCenter', 'alignRight', 'alignBlockLeft', 'alignBlockRight'] },
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
								'imageStyle:inline', 'imageStyle:block', 'imageStyle:side',
								'imageStyle:alignLeft', 'imageStyle:alignCenter', 'imageStyle:alignRight', 'imageStyle:alignBlockLeft', 'imageStyle:alignBlockRight',
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
								'tableProperties'
							]
						},
						ckfinder: 
						{
							openerMethod: "modal",
							options: {
								basePath: site_url + '/assets/plugins/ckfinder/',
								connectorPath: site_url + '/assets/plugins/ckfinder/core/connector/php/connector.php',
								resourceType: 'Articles',
							},
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
						editor.ui.getEditableElement().classList.add('article-rich-content');

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
		previewArticleThumbnail: function(event)
		{
			const file = event.target.files?.[0];

			if (!file)
			{
				return;
			}

			const fileReader = new FileReader();
			fileReader.addEventListener('load', () =>
			{
				this.thumbnailSource = 'upload';
				this.thumbnailCkfinderUrl = '';
				this.thumbnailCkfinderLabel = '';
				this.imageEncoded = fileReader.result;
				this.articleThumbnailRemove = false;
				this.thumbnailFileSelected = true;
				this.showButtonRemoveImage = true;
			});

			fileReader.readAsDataURL(file);
		},
		previewImage: function(event)
		{
			this.previewArticleThumbnail(event);
		},
		setArticleThumbnailSource: function(source)
		{
			const nextSource = source === 'ckfinder' ? 'ckfinder' : 'upload';
			if (nextSource === this.thumbnailSource)
			{
				return;
			}

			const input = this.$refs.thumbnailInput;
			if (nextSource === 'ckfinder' && this.thumbnailFileSelected)
			{
				if (input) input.value = '';
				this.thumbnailFileSelected = false;
				this.imageEncoded = this.thumbnailOriginalPreview || '';
				this.showButtonRemoveImage = Boolean(this.imageEncoded);
			}

			if (nextSource === 'upload' && this.thumbnailCkfinderUrl)
			{
				this.thumbnailCkfinderUrl = '';
				this.thumbnailCkfinderLabel = '';
				this.imageEncoded = this.thumbnailOriginalPreview || '';
				this.thumbnailFileSelected = false;
				this.showButtonRemoveImage = Boolean(this.imageEncoded);
			}

			this.thumbnailSource = nextSource;
		},
		openArticleThumbnailCkfinder: function()
		{
			if (typeof CKFinder === 'undefined')
			{
				console.warn('CKFinder is not available');
				return;
			}

			CKFinder.modal({
				chooseFiles: true,
				resourceType: 'Articles',
				width: 800,
				height: 600,
				onInit: (finder) => finder.on('files:choose', (event) =>
				{
					const file = event.data.files.first();
					this.selectArticleCkfinderThumbnail(file.getUrl(), file.get('name'));
				}),
			});
		},
		selectArticleCkfinderThumbnail: function(url, name = '')
		{
			const selectedUrl = String(url || '').trim();
			if (!this.isArticleCkfinderImage(selectedUrl))
			{
				console.warn('Choose an image from the Articles folder');
				return;
			}

			this.thumbnailSource = 'ckfinder';
			this.thumbnailCkfinderUrl = selectedUrl;
			this.thumbnailCkfinderLabel = this.articleCkfinderThumbnailLabel(selectedUrl, name);
			this.imageEncoded = selectedUrl;
			this.articleThumbnailRemove = false;
			this.thumbnailFileSelected = false;
			this.showButtonRemoveImage = true;
		},
		isArticleCkfinderImage: function(url)
		{
			try
			{
				return new URL(url, window.location.origin).pathname.startsWith('/storage/ckfinder/articles/');
			}
			catch
			{
				return false;
			}
		},
		articleCkfinderThumbnailLabel: function(url, name = '')
		{
			const filename = String(name || new URL(url, window.location.origin).pathname.split('/').pop() || '').trim();
			return filename ? `Articles / ${filename}` : 'Articles';
		},
		removeArticleThumbnailPreview: function()
		{
			const input = this.$refs.thumbnailInput;
			if (input) input.value = '';

			const restoreOriginal = this.thumbnailFileSelected && this.thumbnailOriginalPreview;
			this.imageEncoded = restoreOriginal ? this.thumbnailOriginalPreview : '';
			this.articleThumbnailRemove = !restoreOriginal && Boolean(this.thumbnailOriginalPreview);
			this.thumbnailCkfinderUrl = '';
			this.thumbnailCkfinderLabel = '';
			this.thumbnailFileSelected = false;
			if (restoreOriginal) this.thumbnailSource = 'upload';
			this.showButtonRemoveImage = Boolean(this.imageEncoded);
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
						this.imageEncoded = response.data[0].get_thumbnail;
						this.thumbnailOriginalPreview = this.imageEncoded;

						this.showButtonRemoveImage = true;

						image.src = this.imageEncoded;

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
			this.removeArticleThumbnailPreview();
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
        getTableWidth: function()
        {
            const wrapper = document.getElementById('ph-article-table-wrapper');
            return wrapper ? wrapper.clientWidth : 0;
        },
        recalcResponsive: function()
        {
            const width = this.getTableWidth();
            if (!width) return;
            this.responsiveHiddenCols = [
                ...(width <= 940 ? [2] : []),
                ...(width <= 740 ? [5] : []),
                ...(width <= 600 ? [4] : []),
            ];
        },
        onArticleRowClick: function(id, event)
        {
            if (event.defaultPrevented || event.button !== 0) return;
            if (event.target.closest('a, button, input, label, select, textarea, [contenteditable], [role="button"], .ph-article-check-col, .ph-article-actions-col')) return;
            if (window.getSelection()?.toString().trim()) return;
            this.toggleExpandRow(id);
        },
        toggleExpandRow: function(id)
        {
            this.responsiveExpandedRows = { ...this.responsiveExpandedRows, [id]: !this.responsiveExpandedRows[id] };
        },
        isColHidden: function(idx)
        {
            return this.responsiveHiddenCols.includes(idx);
        },
	},
	mounted()
	{
        const manager = document.getElementById('ph-app-manage-article');
        this.articleStatuses = JSON.parse(manager?.getAttribute('data-statuses') || '[]');
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
