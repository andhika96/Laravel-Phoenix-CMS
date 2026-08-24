const EVENT_DATE_FIELDS = ['starts_at', 'ends_at', 'registration_open_at', 'registration_close_at'];

const emptyEventOccurrence = () => ({
    id: '',
    label: '',
    starts_at: null,
    ends_at: null,
    timezone: 'Asia/Jakarta',
    location_mode: 'offline',
    location_text: '',
    address: '',
    online_url: '',
    registration_open_at: null,
    registration_close_at: null,
    capacity: 1,
    lifecycle_status: 'scheduled',
    visible: false,
});

const ManageEventFormVue3 = Vue.createApp({
    data() {
        const root = document.getElementById('ph-app-manage-event-form');

        return {
            eventId: root?.dataset.eventId || '',
            detailUrl: root?.dataset.detailUrl || '',
            occurrenceBaseUrl: root?.dataset.occurrenceBaseUrl || '',
            registrationsBaseUrl: root?.dataset.registrationsBaseUrl || '/manage_event/registrations',
            editBaseUrl: root?.dataset.editBaseUrl || '/manage_event/edit',
            timezoneOptions: ['Asia/Jakarta', 'Asia/Makassar', 'Asia/Jayapura', 'Asia/Singapore', 'UTC'],
            uriManuallyEdited: false,
            summaryEditor: null,
            contentEditor: null,
            reminderTime: null,
            cancelCutoffTime: null,
            showThumbnailRemove: false,
            thumbnailOriginalPreview: '',
            thumbnailFileSelected: false,
            thumbnailSource: 'upload',
            thumbnailCkfinderLabel: '',
            form: {
                title: '',
                uri: '',
                summary: '',
                content: '',
                tags: '',
                category_id: '',
                publication_status: 'draft',
                visibility: 'public',
                reminder_lead_minutes: '',
                cancel_cutoff_minutes: '',
                thumbnailPreview: '',
                remove_thumbnail: false,
                thumbnail_ckfinder_url: '',
            },
            occurrences: [],
            occurrenceForm: emptyEventOccurrence(),
            pendingOccurrence: null,
            registrationPanel: { visible: false, occurrence: null, rows: [] },
            notice: { message: '' },
        };
    },
    components: {
        VueDatePicker: window.VueDatePicker,
    },
    methods: {
        async submitEvent() {
            const form = this.$refs.eventForm;
            this.syncRichEditors();

            try {
                const response = await axios.post(form.action, new FormData(form), { headers: { Accept: 'application/json' } });
                const id = response.data.data?.id || this.eventId;
                if (!this.eventId && id) {
                    window.location.href = `${this.editBaseUrl}/` + id;
                    return;
                }
                this.notice.message = response.data.message || 'Event saved successfully';
                await this.loadEvent();
            } catch (error) {
                this.notice.message = this.errorMessage(error, 'Failed to save event');
            }
        },
        async loadEvent() {
            if (!this.detailUrl) return;

            try {
                const response = await axios.get(this.detailUrl, { headers: { Accept: 'application/json' } });
                const data = response.data.data || {};
                Object.keys(this.form).forEach((key) => {
                    if (key in data && key !== 'thumbnailPreview') this.form[key] = data[key] ?? '';
                });
                this.form.thumbnailPreview = data.thumbnail_large_url || data.thumbnail_small_url || '';
                this.form.remove_thumbnail = false;
                this.form.thumbnail_ckfinder_url = '';
                this.thumbnailSource = 'upload';
                this.thumbnailCkfinderLabel = '';
                this.thumbnailOriginalPreview = this.form.thumbnailPreview;
                this.thumbnailFileSelected = false;
                this.showThumbnailRemove = Boolean(this.form.thumbnailPreview);
                this.reminderTime = this.minutesToTime(this.form.reminder_lead_minutes);
                this.cancelCutoffTime = this.minutesToTime(this.form.cancel_cutoff_minutes);
                this.uriManuallyEdited = Boolean(this.form.uri && this.form.uri !== this.slugify(this.form.title));
                this.occurrences = Array.isArray(data.occurrences) ? data.occurrences : (data.occurrences?.data || []);
                this.setEditorData('summaryEditor', 'summary', '#summary-editor', this.form.summary);
                this.setEditorData('contentEditor', 'content', '#editor', this.form.content);
            } catch (error) {
                this.notice.message = this.errorMessage(error, 'Failed to load event');
            }
        },
        resetOccurrence() {
            this.occurrenceForm = emptyEventOccurrence();
            this.occurrenceForm.visible = true;
        },
        editOccurrence(occurrence) {
            this.occurrenceForm = {
                ...emptyEventOccurrence(),
                ...occurrence,
                starts_at: this.toPickerDate(occurrence.starts_at),
                ends_at: this.toPickerDate(occurrence.ends_at),
                registration_open_at: this.toPickerDate(occurrence.registration_open_at),
                registration_close_at: this.toPickerDate(occurrence.registration_close_at),
                visible: true,
            };
        },
        async saveOccurrence() {
            const occurrence = { ...this.occurrenceForm };
            delete occurrence.visible;
            EVENT_DATE_FIELDS.forEach((field) => {
                occurrence[field] = this.toApiDate(occurrence[field]);
            });

            const url = occurrence.id ? `${this.occurrenceBaseUrl}/${this.eventId}/${occurrence.id}` : `${this.occurrenceBaseUrl}/${this.eventId}`;
            try {
                await axios.post(url, occurrence, { headers: { Accept: 'application/json' } });
                this.notice.message = 'Occurrence saved successfully';
                this.occurrenceForm = emptyEventOccurrence();
                await this.loadEvent();
            } catch (error) {
                this.notice.message = this.errorMessage(error, 'Failed to save occurrence');
            }
        },
        openOccurrenceDelete(occurrence) {
            this.pendingOccurrence = occurrence;
            bootstrap.Modal.getOrCreateInstance(document.getElementById('eventOccurrenceDeleteModal')).show();
        },
        openOccurrenceCancel(occurrence) {
            this.pendingOccurrence = occurrence;
            bootstrap.Modal.getOrCreateInstance(document.getElementById('eventOccurrenceCancelModal')).show();
        },
        async cancelOccurrence() {
            if (!this.pendingOccurrence) return;
            try {
                await axios.post(`${this.occurrenceBaseUrl}/${this.eventId}/${this.pendingOccurrence.id}/cancel`, {}, { headers: { Accept: 'application/json' } });
                bootstrap.Modal.getInstance(document.getElementById('eventOccurrenceCancelModal'))?.hide();
                this.notice.message = 'Occurrence cancelled successfully';
                this.pendingOccurrence = null;
                await this.loadEvent();
            } catch (error) {
                this.notice.message = this.errorMessage(error, 'Failed to cancel occurrence');
            }
        },
        async loadRegistrations(occurrence) {
            this.registrationPanel = { visible: true, occurrence, rows: [] };
            try {
                const response = await axios.get(`${this.registrationsBaseUrl}/${this.eventId}`, { headers: { Accept: 'application/json' } });
                const rows = response.data.data || [];
                this.registrationPanel.rows = rows.filter((row) => Number(row.occurrence_id) === Number(occurrence.id));
            } catch (error) {
                this.notice.message = this.errorMessage(error, 'Failed to load registrations');
            }
        },
        async markAttendance(registration, status) {
            try {
                await axios.post(`${this.registrationsBaseUrl}/${registration.id}/attendance`, { status }, { headers: { Accept: 'application/json' } });
                registration.status = status;
                registration.attended_at = new Date().toISOString();
            } catch (error) {
                this.notice.message = this.errorMessage(error, 'Failed to update attendance');
            }
        },
        async deleteOccurrence() {
            if (!this.pendingOccurrence) return;
            try {
                await axios.post(`${this.occurrenceBaseUrl}/${this.eventId}/${this.pendingOccurrence.id}/delete`, {}, { headers: { Accept: 'application/json' } });
                bootstrap.Modal.getInstance(document.getElementById('eventOccurrenceDeleteModal'))?.hide();
                this.pendingOccurrence = null;
                this.notice.message = 'Occurrence deleted successfully';
                await this.loadEvent();
            } catch (error) {
                this.notice.message = this.errorMessage(error, 'Failed to delete occurrence');
            }
        },
        previewThumbnail(event) {
            const file = event.target.files?.[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = (loadEvent) => {
                this.thumbnailSource = 'upload';
                this.thumbnailCkfinderLabel = '';
                this.form.thumbnail_ckfinder_url = '';
                this.form.thumbnailPreview = loadEvent.target.result;
                this.form.remove_thumbnail = false;
                this.thumbnailFileSelected = true;
                this.showThumbnailRemove = true;
            };
            reader.readAsDataURL(file);
        },
        setThumbnailSource(source) {
            const nextSource = source === 'ckfinder' ? 'ckfinder' : 'upload';
            if (nextSource === this.thumbnailSource) return;

            const input = this.$refs.thumbnailInput;
            if (nextSource === 'ckfinder' && this.thumbnailFileSelected) {
                if (input) input.value = '';
                this.thumbnailFileSelected = false;
                this.form.thumbnailPreview = this.thumbnailOriginalPreview || '';
                this.showThumbnailRemove = Boolean(this.form.thumbnailPreview);
            }

            if (nextSource === 'upload' && this.form.thumbnail_ckfinder_url) {
                this.form.thumbnail_ckfinder_url = '';
                this.thumbnailCkfinderLabel = '';
                this.form.thumbnailPreview = this.thumbnailOriginalPreview || '';
                this.thumbnailFileSelected = false;
                this.showThumbnailRemove = Boolean(this.form.thumbnailPreview);
            }

            this.thumbnailSource = nextSource;
        },
        openThumbnailCkfinder() {
            if (typeof CKFinder === 'undefined') {
                this.notice.message = 'CKFinder is not available';
                return;
            }

            CKFinder.modal({
                chooseFiles: true,
                resourceType: 'Events',
                width: 800,
                height: 600,
                onInit: (finder) => finder.on('files:choose', (event) => {
                    const file = event.data.files.first();
                    this.selectCkfinderThumbnail(file.getUrl(), file.get('name'));
                }),
            });
        },
        selectCkfinderThumbnail(url, name = '') {
            const selectedUrl = String(url || '').trim();
            if (!this.isCkfinderEventImage(selectedUrl)) {
                this.notice.message = 'Choose an image from the Events folder';
                return;
            }

            this.thumbnailSource = 'ckfinder';
            this.thumbnailCkfinderLabel = this.ckfinderThumbnailLabel(selectedUrl, name);
            this.form.thumbnail_ckfinder_url = selectedUrl;
            this.form.thumbnailPreview = selectedUrl;
            this.form.remove_thumbnail = false;
            this.thumbnailFileSelected = false;
            this.showThumbnailRemove = true;
        },
        isCkfinderEventImage(url) {
            try {
                return new URL(url, window.location.origin).pathname.startsWith('/storage/ckfinder/events/');
            } catch {
                return false;
            }
        },
        ckfinderThumbnailLabel(url, name = '') {
            const filename = String(name || new URL(url, window.location.origin).pathname.split('/').pop() || '').trim();
            return filename ? `Events / ${filename}` : 'Events';
        },
        removeThumbnailPreview() {
            const input = this.$refs.thumbnailInput;
            if (input) input.value = '';

            const restoreOriginal = this.thumbnailFileSelected && this.thumbnailOriginalPreview;
            this.form.thumbnailPreview = restoreOriginal ? this.thumbnailOriginalPreview : '';
            this.form.remove_thumbnail = !restoreOriginal && Boolean(this.thumbnailOriginalPreview);
            this.form.thumbnail_ckfinder_url = '';
            this.thumbnailCkfinderLabel = '';
            this.thumbnailFileSelected = false;
            if (restoreOriginal) this.thumbnailSource = 'upload';
            this.showThumbnailRemove = Boolean(this.form.thumbnailPreview);
        },
        minutesToTime(value) {
            const total = Number(value);
            if (!Number.isInteger(total) || total < 0 || total >= 24 * 60) return null;

            return { hours: Math.floor(total / 60), minutes: total % 60, seconds: 0 };
        },
        setDurationMinutes(field, value) {
            if (!value || typeof value !== 'object') {
                this.form[field] = '';
                return;
            }

            const hours = Number(value.hours);
            const minutes = Number(value.minutes);
            if (!Number.isInteger(hours) || !Number.isInteger(minutes) || hours < 0 || hours > 23 || minutes < 0 || minutes > 59) {
                this.form[field] = '';
                return;
            }

            this.form[field] = hours * 60 + minutes;
        },
        formatDuration(value) {
            const total = Number(value);
            if (!Number.isInteger(total) || total < 0) return '';

            return `${Math.floor(total / 60)}h ${total % 60}m`;
        },
        syncUriFromTitle() {
            if (!this.uriManuallyEdited) this.form.uri = this.slugify(this.form.title);
        },
        markUriCustomized() {
            this.uriManuallyEdited = Boolean(this.form.uri && this.form.uri !== this.slugify(this.form.title));
        },
        resetAutoUri() {
            this.uriManuallyEdited = false;
            this.syncUriFromTitle();
        },
        slugify(value) {
            return String(value || '')
                .normalize('NFKD')
                .replace(/[\u0300-\u036f]/g, '')
                .toLowerCase()
                .replace(/[^a-z0-9\s-]/g, '')
                .trim()
                .replace(/[\s_-]+/g, '-');
        },
        async initEditor(editorKey, field, selector, wordCountId) {
            const textarea = document.querySelector(selector);
            if (!textarea || typeof ClassicEditor === 'undefined') return;

            const siteBase = String(window.site_url || window.location.origin).replace(/\/$/, '');
            try {
                const editor = await ClassicEditor.create(textarea, {
                    toolbar: {
                        items: [
                            'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'todoList', '|',
                            'outdent', 'indent', 'alignment', 'undo', 'redo', '|', 'CKFinder', 'imageUpload', 'imageInsert',
                            'blockQuote', 'insertTable', 'removeFormat', 'underline', 'fontFamily', 'fontSize', 'fontColor',
                            'highlight', 'selectAll',
                        ],
                    },
                    language: 'en',
                    image: {
                        styles: ['alignCenter', 'alignLeft', 'alignRight'],
                        resizeOptions: [
                            { name: 'resizeImage:original', label: 'Default image width', value: null },
                            { name: 'resizeImage:25', label: '25% page width', value: '25' },
                            { name: 'resizeImage:50', label: '50% page width', value: '50' },
                            { name: 'resizeImage:75', label: '75% page width', value: '75' },
                            { name: 'resizeImage:100', label: '100% page width', value: '100' },
                        ],
                        toolbar: [
                            'imageTextAlternative', 'toggleImageCaption', '|',
                            'imageStyle:inline', 'imageStyle:wrapText', 'imageStyle:breakText', 'imageStyle:side', '|',
                            'resizeImage', 'linkImage',
                        ],
                    },
                    table: {
                        contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells', 'tableCellProperties', 'tableProperties'],
                    },
                    ckfinder: {
                        openerMethod: 'modal',
                        uploadUrl: `${siteBase}/assets/plugins/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Events&responseType=json`,
                    },
                    wordCount: { displayWords: false },
                    licenseKey: '',
                });
                this[editorKey] = Vue.markRaw(editor);

                this[editorKey].model.document.on('change:data', () => this.syncEditorField(editorKey, field, selector));
                this.setEditorData(editorKey, field, selector, this.form[field]);

                const wordCountPlugin = this[editorKey].plugins.get('WordCount');
                const wordCountWrapper = document.getElementById(wordCountId);
                if (wordCountPlugin && wordCountWrapper) wordCountWrapper.replaceChildren(wordCountPlugin.wordCountContainer);
            } catch (error) {
                console.error('Manage Event CKEditor initialization failed', error);
                this.notice.message = 'Text editor could not be loaded';
            }
        },
        syncRichEditors() {
            this.syncEditorField('summaryEditor', 'summary', '#summary-editor');
            this.syncEditorField('contentEditor', 'content', '#editor');
        },
        syncEditorField(editorKey, field, selector) {
            const editor = this[editorKey];
            if (!editor) return;
            const content = editor.getData();
            this.form[field] = content;
            const textarea = document.querySelector(selector);
            if (textarea) textarea.value = content;
        },
        setEditorData(editorKey, field, selector, value) {
            const content = String(value || '');
            const editor = this[editorKey];
            this.form[field] = content;
            if (editor && editor.getData() !== content) editor.setData(content);
            const textarea = document.querySelector(selector);
            if (textarea) textarea.value = content;
        },
        toPickerDate(value) {
            if (!value) return null;
            if (value instanceof Date) return value;
            const parsed = new Date(String(value).includes('T') ? value : String(value).replace(' ', 'T'));
            return Number.isNaN(parsed.getTime()) ? null : parsed;
        },
        toApiDate(value) {
            const date = this.toPickerDate(value);
            if (!date) return null;
            const pad = (number) => String(number).padStart(2, '0');
            return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())} ${pad(date.getHours())}:${pad(date.getMinutes())}:00`;
        },
        formatDate(value) {
            if (!value) return '-';
            return new Intl.DateTimeFormat('id-ID', { dateStyle: 'medium', timeStyle: 'short' }).format(new Date(value));
        },
        errorMessage(error, fallback) {
            const message = error.response?.data?.message;
            if (typeof message === 'string') return message;
            if (message && typeof message === 'object') return Object.values(message).flat().join(' ');
            return fallback;
        },
    },
    mounted() {
        this.initEditor('summaryEditor', 'summary', '#summary-editor', 'summary-word-count');
        this.initEditor('contentEditor', 'content', '#editor', 'word-count');
        this.loadEvent();
    },
    beforeUnmount() {
        this.summaryEditor?.destroy();
        this.contentEditor?.destroy();
    },
}).mount('#ph-app-manage-event-form');

window.ManageEventFormVue3 = ManageEventFormVue3;
