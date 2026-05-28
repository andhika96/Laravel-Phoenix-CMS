	// --- CKEDITOR COMPONENT (TIDAK BERUBAH) ---
	const CkEditorComponent = {
		props: ['modelValue', 'baseUrl'],
		template: `<div><div ref="editorRef"></div></div>`,
		setup(props, { emit }) {
			const editorRef = ref(null);
			let editorInstance = null;
			watch(() => props.modelValue, (newVal) => { if (editorInstance && newVal !== editorInstance.getData()) { editorInstance.setData(newVal || ''); } });
			onMounted(() => {
				ClassicEditor.create(editorRef.value, { 
					toolbar: { 
						items: ['heading', '|', 'fontFamily', 'fontSize', 'fontColor', 'fontBackgroundColor', '|', 'bold', 'italic', 'underline', 'strikethrough', 'subscript', 'superscript', 'code', 'removeFormat', '|', 'link', 'bulletedList', 'numberedList', 'todoList', '|', 'outdent', 'indent', 'alignment', '|', 'CKFinder', 'imageUpload', 'blockQuote', 'insertTable', 'mediaEmbed', 'horizontalLine', 'specialCharacters', '|', 'findAndReplace', 'sourceEditing', 'htmlEmbed', 'codeBlock', 'highlight', '|', 'undo', 'redo'], 
						shouldNotGroupWhenFull: true 
					},
					language: 'en',
					image: { styles: [ 'alignCenter', 'alignLeft', 'alignRight' ], resizeOptions: [ { name: 'resizeImage:original', label: 'Default', value: null }, { name: 'resizeImage:100', label: '100%', value: '100' } ], toolbar: [ 'imageTextAlternative', 'toggleImageCaption', '|', 'imageStyle:inline', 'imageStyle:wrapText', 'imageStyle:breakText', 'imageStyle:side', '|', 'resizeImage', 'linkImage' ] },
					table: { contentToolbar: [ 'tableColumn', 'tableRow', 'mergeTableCells', 'tableCellProperties', 'tableProperties' ] },
					ckfinder: { openerMethod: "modal", uploadUrl: props.baseUrl + "assets/plugins/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images&responseType=json" }
				}).then(editor => { 
					editorInstance = editor; editor.setData(props.modelValue || ''); 
					editor.model.document.on("change:data", () => { emit('update:modelValue', editor.getData()); }); 
					editor.ui.view.element.addEventListener('mousedown', (e) => { e.stopPropagation(); });
				});
			});
			onBeforeUnmount(() => { if (editorInstance) editorInstance.destroy(); });
			return { editorRef };
		}
	};

