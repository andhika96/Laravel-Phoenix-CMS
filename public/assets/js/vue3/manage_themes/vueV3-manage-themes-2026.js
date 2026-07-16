const ThemeManagerApp = createApp(
{
	data()
	{
		const root = document.getElementById('ph-app-theme-manager');
		const themes = JSON.parse(root.dataset.themes || '[]').map((theme) =>
		({
			...theme,
			preview_url: new URL('/'+theme.preview_image.replace(/^\/+/, ''), window.location.origin).href,
		}));
		const activeThemeCode = root.dataset.activeTheme || (themes[0] ? themes[0].code : '');

		return {
			themes: themes,
			activeThemeCode: activeThemeCode,
			selectedThemeCode: activeThemeCode,
			previewTheme: null,
			isSubmitting: false,
			responseStatus: 'ph-callout-success',
			responseMessage: '',
		};
	},
	computed:
	{
		hasChanges()
		{
			return this.selectedThemeCode !== '' && this.selectedThemeCode !== this.activeThemeCode;
		},
	},
	methods:
	{
		isSelected(themeCode)
		{
			return this.selectedThemeCode === themeCode;
		},
		isActive(themeCode)
		{
			return this.activeThemeCode === themeCode;
		},
		themeStatus(themeCode)
		{
			if (this.isActive(themeCode))
			{
				return 'Active';
			}

			return this.isSelected(themeCode) ? 'Selected' : 'Available';
		},
		selectTheme(themeCode)
		{
			if (this.themes.some((theme) => theme.code === themeCode))
			{
				this.selectedThemeCode = themeCode;
			}
		},
		cancelChanges()
		{
			this.selectedThemeCode = this.activeThemeCode;
		},
		openPreview(themeCode)
		{
			this.previewTheme = this.themes.find((theme) => theme.code === themeCode) || null;

			if (this.previewTheme)
			{
				bootstrap.Modal.getOrCreateInstance(document.getElementById('themeManagerPreviewModal')).show();
			}
		},
		closePreview()
		{
			const previewModal = document.getElementById('themeManagerPreviewModal');
			bootstrap.Modal.getOrCreateInstance(previewModal).hide();
		},
		showNotice(status, message)
		{
			this.responseStatus = status === 'success' ? 'ph-callout-success' : 'ph-callout-danger';
			this.responseMessage = message;

			this.$nextTick(() =>
			{
				bootstrap.Toast.getOrCreateInstance(this.$refs.noticeToast).show();
			});
		},
		saveChanges()
		{
			if ( ! this.hasChanges || this.isSubmitting)
			{
				return;
			}

			const root = document.getElementById('ph-app-theme-manager');
			const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
			this.isSubmitting = true;

			axios.post(root.dataset.updateUrl,
			{
				theme_code: this.selectedThemeCode,
			},
			{
				headers:
				{
					'Accept': 'application/json',
					'X-Requested-With': 'XMLHttpRequest',
					'X-CSRF-TOKEN': csrfToken,
				},
			})
			.then((response) =>
			{
				this.activeThemeCode = response.data.active_theme;
				this.selectedThemeCode = response.data.active_theme;
				this.showNotice('success', response.data.message);

				if (response.data.redirect_url)
				{
					window.setTimeout(() =>
					{
						window.location.href = response.data.redirect_url;
					}, 900);
				}
			})
			.catch((error) =>
			{
				const errors = error.response?.data?.errors;
				const message = errors ? Object.values(errors).flat()[0] : (error.response?.data?.message || error.message);
				this.showNotice('failed', message);
			})
			.finally(() =>
			{
				this.isSubmitting = false;
			});
		},
	},
});

const ThemeManagerVue3 = ThemeManagerApp.mount('#ph-app-theme-manager');
