const ManageParentMenuVue3 = createApp(
{
	data() 
	{
		return {
			responseDataParentMenu: [{ is_for_parent_menu: '', category_code: '', parent_type: '', parent_code: '', parent_name: '', parent_link: '', parent_icon_type: '', parent_icon: '', parent_icon_url: '', parent_icon_path: '', parent_icon_custom: '', parent_roles: [], parent_permissions: '' }],
			responseNewDataParentMenu: { is_for_parent_menu: '', category_code: '', parent_type: '', parent_code: '', parent_name: '', parent_link: '', parent_icon_type: '', parent_icon: '', parent_icon_url: '', parent_icon_path: '', parent_icon_custom: '', parent_roles: [], parent_permissions: '' },
			dropdownModal: null,
			dropdownModalLoading: false,
			dropdownParentIndex: '',
			dropdownParentMenu: {},
			dropdownSubmenus: [],
			dropdownConfig:
			{
				dropdown_type: 'none',
				mega_layout: 'columns',
				config_json:
				{
					dropdown: { show_arrow: true, margin_top: 12, arrow_size: 12, width: 760, width_mode: 'container', align: 'center' },
					bootstrap: { width: 260, align: 'start' },
					mega: { columns: 3, max_items: 12, image_position: 'left', title_position: 'left', description_position: 'left', show_images: true, show_description: true, featured_index: 0 }
				}
			},
			responseDataRoles: [],
			responseDataPermissions: [],
			responseDataRoutes: [],
			responseDataVersionMenu: '',
			responseMessageParentMenu: '',
			responseMessageRoles: '',
			responseMessagePermissions: '',
			responseMessageRoutes: '',
			responseMessageAfterSubmit: '',
			responseStatusAddNewMenu: true,
			responseStatusParentMenu: '',
			responseStatusRoles: '',
			responseStatusPermissions: '',
			responseStatusRoutes: '',
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
			let getIdFormSubmit = document.getElementById("ph-form-manage-parentmenu");

			// Get value of attribute in HTML.
			let formActionURL = getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("action");
			let formMethod = getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("method");

			// FormData objects are used to capture HTML form and submit it using fetch or another network method.
			let formData = new FormData(this.$refs.formHTML);

			formData.append('menu_vars', JSON.stringify(this.responseDataParentMenu));

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
					this.listDataParentMenu();

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

				getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].insertAdjacentHTML("beforebegin", "<button type=\"submit\" class=\"btn ph-btn-theme btn-submit-data btn-submit-update-data-list font-size-inherit\">"+this.customButtonValue+"</button>");
				getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].remove();
			});
		},
		submitDataDelete: function(event) 
		{
			event.preventDefault();

			// Get id form submit
			let getIdFormSubmit = document.getElementById("ph-form-delete-parentmenu");

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
						this.responseDataParentMenu.splice(getDataListIndex, 1);

						window.setTimeout(function() 
						{
							document.querySelector(".btn-submit-update-data-list").click();

						}, 100);

						this.closeDeleteModalParentMenuAfterDelete();

						if (this.autoLockButton == 'true')
						{
							getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].insertAdjacentHTML("beforebegin", "<a class=\"btn btn-success btn-logged font-size-inherit\">Success <i class=\"far fa-check-circle fa-fw mr-1\"></i></div></a>");
							getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].remove();
						}
						else
						{
							getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].insertAdjacentHTML("beforebegin", "<button type=\"submit\" class=\"btn ph-btn-theme btn-submit-data btn-submit-update-data-list font-size-inherit\">"+this.this.customButtonValueModal.delete+"</button>");
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

						toast.show();

					}, 1);

					getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].insertAdjacentHTML("beforebegin", "<button type=\"submit\" class=\"btn ph-btn-theme btn-submit-data btn-submit-update-data-list font-size-inherit\">"+this.customButtonValueModal.delete+"</button>");
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

				getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].insertAdjacentHTML("beforebegin", "<button type=\"submit\" class=\"btn ph-btn-theme btn-submit-data btn-submit-update-data-list font-size-inherit\">"+this.customButtonValueModal.delete+"</button>");
				getIdFormSubmit.getElementsByClassName("btn-submit-loading")[0].remove();
			});
		},
		defaultDropdownConfig: function()
		{
			return {
				dropdown_type: 'none',
				mega_layout: 'columns',
				config_json:
				{
					dropdown: { show_arrow: true, margin_top: 12, arrow_size: 12, width: 760, width_mode: 'container', align: 'center' },
					bootstrap: { width: 260, align: 'start' },
					mega: { columns: 3, max_items: 12, image_position: 'left', title_position: 'left', description_position: 'left', show_images: true, show_description: true, featured_index: 0 }
				}
			};
		},
		mergeDropdownConfig: function(config)
		{
			const defaultConfig = this.defaultDropdownConfig();
			const configJson = (config !== undefined && config !== null && config.config_json !== undefined && config.config_json !== null) ? config.config_json : {};

			return {
				dropdown_type: (config !== undefined && config !== null && config.dropdown_type !== undefined) ? config.dropdown_type : defaultConfig.dropdown_type,
				mega_layout: (config !== undefined && config !== null && config.mega_layout !== undefined) ? config.mega_layout : defaultConfig.mega_layout,
				config_json:
				{
					dropdown: Object.assign({}, defaultConfig.config_json.dropdown, configJson.dropdown || {}),
					bootstrap: Object.assign({}, defaultConfig.config_json.bootstrap, configJson.bootstrap || {}),
					mega: Object.assign({}, defaultConfig.config_json.mega, configJson.mega || {})
				}
			};
		},
		openDropdownModalParentMenu: function(element, index)
		{
			if (element.parent_code === undefined || element.parent_code === '')
			{
				this.responseStatusToast = 'ph-callout-danger';
				this.responseMessageAfterSubmit = 'Parent menu code is missing. Please save parent menu first.';
				return;
			}

			this.dropdownParentIndex = index;
			this.dropdownParentMenu = Object.assign({}, element);
			this.dropdownSubmenus = [];
			this.dropdownConfig = this.mergeDropdownConfig(element.dropdown_config);
			this.dropdownModalLoading = true;

			const modalElement = document.getElementById('modalDropdownParentMenu');
			this.dropdownModal = bootstrap.Modal.getOrCreateInstance(modalElement);
			this.dropdownModal.show();

			this.listDataParentMenuDropdown(element.parent_code);
		},
		closeDropdownModalParentMenu: function()
		{
			if (this.dropdownModal !== null)
			{
				this.dropdownModal.hide();
			}
		},
		listDataParentMenuDropdown: async function(parentCode)
		{
			if (document.querySelector(".ph-fetch-listdata-parentmenu") !== null &&
				document.querySelector(".ph-fetch-listdata-parentmenu").getAttribute("data-dropdown-detail-url") !== null)
			{
				const baseUrl = document.querySelector(".ph-fetch-listdata-parentmenu").getAttribute("data-dropdown-detail-url");
				const url = baseUrl+'/'+encodeURIComponent(parentCode);

				await axios.get(url)
				.then(response =>
				{
					if (response.data.status == 'success')
					{
						this.dropdownParentMenu = response.data.data.parent_menu;
						this.dropdownSubmenus = response.data.data.submenus;
						this.dropdownConfig = this.mergeDropdownConfig(response.data.data.dropdown_config);
					}
					else
					{
						this.responseStatusToast = 'ph-callout-danger';
						this.responseMessageAfterSubmit = response.data.message;
					}
				})
				.catch(error =>
				{
					this.responseStatusToast = 'ph-callout-danger';

					if (error.response !== undefined)
					{
						this.responseMessageAfterSubmit = error.response.data.message;
					}
					else
					{
						this.responseMessageAfterSubmit = error.message;
					}
				})
				.finally(() =>
				{
					this.dropdownModalLoading = false;
				});
			}
		},
		submitDataDropdown: function(event)
		{
			event.preventDefault();

			let getIdFormSubmit = document.getElementById("ph-form-dropdown-parentmenu");
			let formActionURL = getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("action");
			let formMethod = getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("method");
			let formData = new FormData(this.$refs.formHTMLDropdown);
			let buttonSubmit = getIdFormSubmit.getElementsByClassName("btn-submit-data")[0];

			formData.append('dropdown_type', this.dropdownConfig.dropdown_type);
			formData.append('mega_layout', this.dropdownConfig.mega_layout);
			formData.append('config_json', JSON.stringify(this.dropdownConfig.config_json));

			if (buttonSubmit !== undefined)
			{
				buttonSubmit.disabled = true;
				buttonSubmit.innerHTML = 'Submitting <div class="spinner-border spinner-border-sm text-light ms-1" role="status"><span class="sr-only">Loading...</span></div>';
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
				this.responseStatusToast = (response.data.status == 'success') ? 'ph-callout-success' : 'ph-callout-danger';
				this.isArrayMessageAfterSubmit = (response.data.message instanceof Object == true || response.data.message instanceof Array == true) ? 1 : 0;
				this.responseMessageAfterSubmit = response.data.message;

				if (response.data.status == 'success')
				{
					this.dropdownConfig = this.mergeDropdownConfig(response.data.data);

					if (this.responseDataParentMenu[this.dropdownParentIndex] !== undefined)
					{
						this.responseDataParentMenu[this.dropdownParentIndex].dropdown_config = this.dropdownConfig;
						this.responseDataParentMenu[this.dropdownParentIndex].dropdown_type = this.dropdownConfig.dropdown_type;
					}

					this.listDataParentMenu();
				}

				this.showDropdownToastNotice(getIdFormSubmit);
			})
			.catch(error =>
			{
				this.responseStatusToast = 'ph-callout-danger';

				if (error.response !== undefined)
				{
					this.isArrayMessageAfterSubmit = (error.response.data.message instanceof Object == true || error.response.data.message instanceof Array == true) ? 1 : 0;
					this.responseMessageAfterSubmit = error.response.data.message;
				}
				else
				{
					this.isArrayMessageAfterSubmit = 0;
					this.responseMessageAfterSubmit = error.message;
				}

				this.showDropdownToastNotice(getIdFormSubmit);
			})
			.finally(() =>
			{
				if (buttonSubmit !== undefined)
				{
					buttonSubmit.disabled = false;
					buttonSubmit.innerHTML = 'Save Dropdown';
				}
			});
		},
		showDropdownToastNotice: function(getIdFormSubmit)
		{
			this.loadToastComplete(getIdFormSubmit);

			window.setTimeout(function()
			{
				let toastBox = getIdFormSubmit.getElementsByClassName("ph-notice-toast")[0];
				let toast = new bootstrap.Toast(toastBox);

				toast.show();
			}, 1);
		},
		dropdownTypeLabel: function(type)
		{
			if (type == 'bootstrap')
			{
				return 'Bootstrap 5';
			}

			if (type == 'mega')
			{
				return 'Mega Menu';
			}

			return 'None';
		},
		limitedDropdownSubmenus: function()
		{
			const items = Array.isArray(this.dropdownSubmenus) ? this.dropdownSubmenus : [];
			const maxItems = parseInt(this.dropdownConfig.config_json.mega.max_items) || 12;

			return items.slice(0, maxItems);
		},
		featuredDropdownItem: function()
		{
			const items = this.limitedDropdownSubmenus();

			if (items.length < 1)
			{
				return null;
			}

			const index = Math.min(items.length - 1, Math.max(0, parseInt(this.dropdownConfig.config_json.mega.featured_index) || 0));

			return items[index];
		},
		dropdownItemIconUrl: function(item)
		{
			if (item === null || item === undefined)
			{
				return '';
			}

			if (item.submenu_icon_url !== undefined && item.submenu_icon_url !== '')
			{
				return item.submenu_icon_url;
			}

			return '';
		},
		dropdownItemIconHtml: function(item)
		{
			if (item === null || item === undefined)
			{
				return '';
			}

			if (item.submenu_icon_custom !== undefined && item.submenu_icon_custom !== '')
			{
				return item.submenu_icon_custom;
			}

			if (item.icon_html !== undefined && item.icon_html !== '')
			{
				return item.icon_html;
			}

			return '';
		},
		dropdownItemHasIcon: function(item)
		{
			return this.dropdownItemIconUrl(item) !== '' || this.dropdownItemIconHtml(item) !== '';
		},
		dropdownItemDescription: function(item)
		{
			if (item.submenu_description !== undefined && item.submenu_description !== '')
			{
				return item.submenu_description;
			}

			if (item.description !== undefined && item.description !== '')
			{
				return item.description;
			}

			return '';
		},
		dropdownTextAlign: function(position)
		{
			if (position == 'center')
			{
				return 'text-center';
			}

			if (position == 'right')
			{
				return 'text-end';
			}

			return 'text-start';
		},
		dropdownPreviewShellStyle: function()
		{
			const dropdownConfig = this.dropdownConfig.config_json.dropdown;
			const bootstrapConfig = this.dropdownConfig.config_json.bootstrap;
			const align = dropdownConfig.align || 'center';
			const widthMode = dropdownConfig.width_mode || 'container';
			const parentName = (this.dropdownParentMenu.parent_name || 'Parent Menu').toString();
			const stageWidth = 1180;
			const safeInset = 96;
			const isMegaFullWidth = this.dropdownConfig.dropdown_type == 'mega' && widthMode != 'custom';
			const configuredWidth = (this.dropdownConfig.dropdown_type == 'bootstrap') ? (parseInt(bootstrapConfig.width) || 260) : (isMegaFullWidth ? stageWidth - (safeInset * 2) : (parseInt(dropdownConfig.width) || 760));
			const dropdownWidth = Math.min(configuredWidth, stageWidth - (safeInset * 2));
			const anchorCenter = Math.round(stageWidth / 2);
			const anchorOffset = Math.round(Math.min(220, Math.max(116, (parentName.length * 9) + 54)) / 2);
			const minLeft = safeInset;
			const maxLeft = Math.max(minLeft, stageWidth - dropdownWidth - safeInset);
			let dropdownLeft = Math.round(anchorCenter - (dropdownWidth / 2));

			if (align == 'start')
			{
				dropdownLeft = anchorCenter - anchorOffset;
			}
			else if (align == 'end')
			{
				dropdownLeft = anchorCenter + anchorOffset - dropdownWidth;
			}

			dropdownLeft = Math.min(maxLeft, Math.max(minLeft, Math.round(dropdownLeft)));

			return {
				'--ph-preview-dropdown-margin': (parseInt(dropdownConfig.margin_top) || 0)+'px',
				'--ph-preview-dropdown-width': (parseInt(dropdownConfig.width) || 760)+'px',
				'--ph-preview-dropdown-width-mode': widthMode,
				'--ph-preview-dropdown-safe-space': (safeInset * 2)+'px',
				'--ph-preview-bootstrap-width': (parseInt(bootstrapConfig.width) || 260)+'px',
				'--ph-preview-dropdown-arrow-size': (parseInt(dropdownConfig.arrow_size) || 0)+'px',
				'--ph-preview-dropdown-anchor-offset': anchorOffset+'px',
				'--ph-preview-dropdown-left': dropdownLeft+'px',
				'--ph-preview-dropdown-arrow-left': (anchorCenter - dropdownLeft)+'px'
			};
		},
		dropdownPreviewArrowStyle: function()
		{
			const config = this.dropdownConfig.config_json.dropdown;

			return {
				'--ph-preview-dropdown-arrow-size': (parseInt(config.arrow_size) || 0)+'px'
			};
		},
		bootstrapPreviewStyle: function()
		{
			return {
				width: '100%'
			};
		},
		megaPreviewGridStyle: function()
		{
			const columns = Math.min(6, Math.max(1, parseInt(this.dropdownConfig.config_json.mega.columns) || 3));

			return {
				'--ph-preview-mega-columns': columns
			};
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
					this.responseDataParentMenu 	= response.data;
					this.responseStatusParentMenu 	= response.data.status;
					this.responseMessageParentMenu 	= response.data.message;

					// console.log(this.responseDataParentMenu);
				})
				.catch(function (error) 
				{
					this.responseStatusParentMenu 	= response.data.status;
					this.responseMessageParentMenu 	= response.data.message;

					// console.log(error.response);
				})
				.finally(() => 
				{					
					this.loadDataComplete();

					this.initColllapseShowParentMenu('collapseHeaderMenu', document.querySelectorAll(".collapse"));
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
		getVersionMenu: async function()
		{				
			await axios.get(site_url+'/awesome_admin/menu/fe/getversionmenu')
			.then(response => 
			{
				this.responseDataVersionMenu = response.data;

				// console.log(response.data);
			})
			.catch(function (error) 
			{
				console.log(error.response);
			})
			.finally(() => 
			{
				// Empty
			});
		},
		addIconParentMenu: function(event)
		{
			this.responseNewDataParentMenu.parent_icon = event.target.files[0];
		},
		addParentMenuOld: function()
		{
			console.log(this.responseNewDataParentMenu);

			if (this.responseNewDataParentMenu.parent_type == '')
			{
				this.responseStatusToast 		= 'ph-callout-danger';
				this.responseMessageAfterSubmit = 'Please select Parent Menu Type';
			}
			else if (this.responseNewDataParentMenu.parent_name == '')
			{
				this.responseStatusToast 		= 'ph-callout-danger';
				this.responseMessageAfterSubmit = 'Please enter Parent Menu Name';
			}
			else if (this.responseNewDataParentMenu.is_for_parent_menu == 'single')
			{
				if (this.responseNewDataParentMenu.parent_link == '')
				{
					this.responseStatusToast 		= 'ph-callout-danger';
					this.responseMessageAfterSubmit = 'Please enter Parent Menu Link';
				}

				console.log(this.responseNewDataParentMenu.parent_link);
			}
			else if (this.responseNewDataParentMenu.is_for_parent_menu == '')
			{
				this.responseStatusToast 		= 'ph-callout-danger';
				this.responseMessageAfterSubmit = 'Please select menu Single or Parent';
			}
			else if (this.responseDataVersionMenu == 'v1') 
			{
				if (this.responseNewDataParentMenu.parent_roles == '')
				{
					this.responseStatusToast 		= 'ph-callout-danger';
					this.responseMessageAfterSubmit = 'Please select role menu';
				}
			}

			/*
			else if (this.responseNewDataParentMenu.category_code == '')
			{
				this.responseStatusToast 		= 'ph-callout-danger';
				this.responseMessageAfterSubmit = 'Please select category menu';
			}
			*/
			// else
			// {
				this.responseStatusToast 		= 'ph-callout-success';
				this.responseMessageAfterSubmit = 'Successfully add new parent menu';

				// Add data new menu to menu list
				this.responseDataParentMenu.push(this.responseNewDataParentMenu);

				// Check parent_icon is undefined or not
				if (this.responseNewDataParentMenu.parent_icon !== 'undefined' && 
					this.responseNewDataParentMenu.parent_icon !== '')
				{
					// Create a new File object
					const myFile 				= new File([this.responseNewDataParentMenu.parent_icon.slice(0, this.responseNewDataParentMenu.parent_icon.size, this.responseNewDataParentMenu.parent_icon.type)], this.responseNewDataParentMenu.parent_icon.name);
					const getResetFile 			= document.querySelector('.form-control-new-icon').value = '';
					const getBeforeLatestIndex 	= this.responseDataParentMenu.length-1;

					// We using setTimeout to give responseDataParentMenu variable time to add responseNewDataParentMenu data
					// and then insert the file image or icon
					setTimeout(function()
					{
						const fileInput = document.querySelector(".parent_icon_"+getBeforeLatestIndex);

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
					this.initColllapseShowParentMenu('collapseHeaderMenu', document.querySelectorAll(".collapse"));
				
				}.bind(this), 300);

				// Reset form add new menu
				this.responseNewDataParentMenu = { is_for_parent_menu: '', category_code: '', parent_type: '', parent_name: '', parent_link: '', parent_icon_type: '', parent_icon: '', parent_icon_url: '', parent_icon_path: '', parent_icon_custom: '', parent_roles: '', parent_permissions: '' };

				// console.log(this.responseDataMenuParent);
			// }

			// We use setTimeout to give responseStatus time set status in Toast and then show the Toast
			// base on responStatus
			window.setTimeout(function() 
			{
				// We use toast from Bootstrap 5
				let toastBox = document.getElementsByClassName("ph-notice-toast")[0];

				let toast = new bootstrap.Toast(toastBox);

				toast.show();

			}, 1);
		},
		addParentMenu: async function()
		{
			// Get id form submit
			let getIdFormSubmit = document.getElementById("ph-submit-data-rp");

			// Get value of attribute in HTML.
			let formActionURL = getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("action");
			let formMethod = getIdFormSubmit.getElementsByTagName("form")[0].getAttribute("method");

			let formData = new FormData(this.$refs.formHTMLCreateNewMenu);

			formData.append('menu_vars_first', JSON.stringify(this.responseNewDataParentMenu));
			formData.append('parent_link', this.responseNewDataParentMenu.parent_link);

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
					this.responseDataParentMenu.push(this.responseNewDataParentMenu);

					// Check parent_icon is undefined or not
					if (this.responseNewDataParentMenu.parent_icon !== 'undefined' && 
						this.responseNewDataParentMenu.parent_icon !== '')
					{
						// Create a new File object
						const myFile 				= new File([this.responseNewDataParentMenu.parent_icon.slice(0, this.responseNewDataParentMenu.parent_icon.size, this.responseNewDataParentMenu.parent_icon.type)], this.responseNewDataParentMenu.parent_icon.name);
						const getResetFile 			= document.querySelector('.form-control-new-icon').value = '';
						const getBeforeLatestIndex 	= this.responseDataParentMenu.length-1;

						// We using setTimeout to give responseDataParentMenu variable time to add responseNewDataParentMenu data
						// and then insert the file image or icon
						setTimeout(function()
						{
							const fileInput = document.querySelector(".parent_icon_"+getBeforeLatestIndex);

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
						this.initColllapseShowParentMenu('collapseHeaderMenu', document.querySelectorAll(".collapse"));
					
					}.bind(this), 300);

					// Reset form add new menu
					this.responseNewDataParentMenu = { is_for_parent_menu: '', category_code: '', parent_type: '', parent_name: '', parent_link: '', parent_icon_type: '', parent_icon: '', parent_icon_url: '', parent_icon_path: '', parent_icon_custom: '', parent_roles: [], parent_permissions: '' };
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
		openDeleteModalParentMenu: function(getDataInfo, index, icon_url)
		{
			let getIdFormSubmit = document.getElementById("ph-form-delete-parentmenu");

			const modalDeleteParentMenu = bootstrap.Modal.getOrCreateInstance(document.getElementById("modalDeleteParentMenu"));

			modalDeleteParentMenu.show();

			getIdFormSubmit.getElementsByClassName("index-data")[0].setAttribute("value", index);
			getIdFormSubmit.getElementsByClassName("menu-code")[0].setAttribute("value", getDataInfo.parent_code);
			getIdFormSubmit.getElementsByClassName("path-file")[0].setAttribute("value", icon_url);

			this.loadToastComplete(getIdFormSubmit);

			// console.log(getDataInfo.parent_code);

			// this.responseDataMenuParent.splice(index, 1);
		},
		closeDeleteModalParentMenu: function()
		{
			// Get id form submit
			let getIdFormSubmit = document.getElementById("ph-form-delete-parentmenu");

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
						const modalDeleteParentMenu = bootstrap.Modal.getOrCreateInstance(document.getElementById("modalDeleteParentMenu"));

						modalDeleteParentMenu.hide();
					});
				}
				else if (toast.isShown() == false)
				{
					// Hide Modal
					const modalDeleteParentMenu = bootstrap.Modal.getOrCreateInstance(document.getElementById("modalDeleteParentMenu"));

					modalDeleteParentMenu.hide();
				}

				if (document.querySelector("#modalDeleteParentMenu") !== null)
				{
					let myModalEl = document.getElementById('modalDeleteParentMenu');
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
		closeDeleteModalParentMenuAfterDelete: function()
		{
			// Get id form submit
			let getIdFormSubmit = document.getElementById("ph-form-delete-parentmenu");

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
						const modalDeleteParentMenu = bootstrap.Modal.getOrCreateInstance(document.getElementById("modalDeleteParentMenu"));

						modalDeleteParentMenu.hide();

						if (document.querySelector("#modalDeleteParentMenu") !== null)
						{
							let myModalEl = document.getElementById('modalDeleteParentMenu');
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
		collapseParentMenu: function(event, key, index)
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
		initColllapseShowParentMenu: function(element, getLength)
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
		selectParentType: function(event)
		{
			if (event.target.value == 'page')
			{
				this.listDataRoutes();
			}

			this.responseNewDataParentMenu.parent_type = event.target.value;
		},
		selectParentLink: function(event)
		{
			const getValue = event.target.value;
			const getSpliteValue = getValue.split("-");

			this.responseNewDataParentMenu.parent_name = getSpliteValue[0];
			this.responseNewDataParentMenu.parent_link = getSpliteValue[1];
		},
		selectParentMenuIconType: function(event)
		{
			this.responseNewDataParentMenu.parent_icon_type = event.target.value;
		}
	},
	mounted()
	{
		this.getVersionMenu();

		this.listDataParentMenu();

		this.listDataRoles();

		this.listDataPermissions();
					
		this.loadToastComplete(document.getElementById("ph-form-manage-parentmenu"));

		this.loadToastComplete(document.getElementById("ph-submit-data-rp"));
	}
}).mount('#ph-app-manage-parentmenu');
