<?php

namespace App\Support\PageBuilderElementor;

use App\Mail\PageBuilderElementorFormMail;
use App\Models\Page_Builder\Page_Builder;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class FormSubmissionHandler
{
    private const ACTIONS = ['message', 'collect', 'email', 'email2', 'redirect', 'webhook'];

    public function handle(Page_Builder $page, string $nodeId, Request $request): array
    {
        $node = $this->findFormNode($this->layout($page), $nodeId);

        if (! $node) {
            abort(404);
        }

        $settings = is_array($node['settings'] ?? null) ? $node['settings'] : [];
        $definitions = $this->fieldDefinitions($settings['fields'] ?? []);
        $validated = Validator::make($request->all(), $this->rules($definitions))->validate();
        [$fields, $files] = $this->submissionValues($definitions, $validated);
        $actions = array_values(array_intersect((array) ($settings['submitActions'] ?? ['message']), self::ACTIONS));
        $webhookUrl = $this->validateActionConfiguration($actions, $settings);
        $meta = [
            'submitted_at' => now()->toIso8601String(),
            'page_url' => (string) $request->headers->get('referer', ''),
            'user_agent' => (string) $request->userAgent(),
            'remote_ip' => (string) $request->ip(),
        ];

        if (in_array('collect', $actions, true)) {
            DB::table('page_builder_elementor_form_submissions')->insert([
                'page_builder_id' => $page->getKey(),
                'page_uri' => (string) $page->uri,
                'node_id' => $nodeId,
                'form_name' => (string) ($settings['formName'] ?? 'Form'),
                'fields' => json_encode($fields, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
                'meta' => json_encode($meta, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if (in_array('email', $actions, true)) {
            $this->sendEmail($settings, '', $definitions, $fields, $files);
        }

        if (in_array('email2', $actions, true)) {
            $this->sendEmail($settings, '2', $definitions, $fields, $files);
        }

        if (in_array('webhook', $actions, true)) {
            Http::acceptJson()->asJson()->timeout(10)->post($webhookUrl, [
                'form' => [
                    'name' => (string) ($settings['formName'] ?? 'Form'),
                    'id' => (string) ($settings['formId'] ?? $nodeId),
                    'page_uri' => (string) $page->uri,
                    'node_id' => $nodeId,
                ],
                'fields' => $fields,
                'meta' => $meta,
            ])->throw();
        }

        $customMessages = ! empty($settings['customMessages']);

        return [
            'success' => true,
            'message' => in_array('message', $actions, true)
                ? ($customMessages ? (string) ($settings['successMessage'] ?? 'The form was sent successfully.') : 'The form was sent successfully.')
                : '',
            'redirect' => in_array('redirect', $actions, true) ? $this->safeRedirect((string) ($settings['redirectUrl'] ?? '')) : '',
        ];
    }

    private function validateActionConfiguration(array $actions, array $settings): ?string
    {
        foreach (['email' => 'email', 'email2' => 'email2'] as $action => $prefix) {
            if (in_array($action, $actions, true) && $this->emails((string) ($settings[$prefix.'To'] ?? '')) === []) {
                throw ValidationException::withMessages([$prefix.'To' => 'A valid recipient email is required.']);
            }
        }

        return in_array('webhook', $actions, true)
            ? $this->publicHttpUrl((string) ($settings['webhookUrl'] ?? ''))
            : null;
    }

    private function layout(Page_Builder $page): array
    {
        if (is_array($page->vars)) {
            return $page->vars;
        }

        $decoded = json_decode((string) $page->vars, true);

        return is_array($decoded) ? $decoded : [];
    }

    private function findFormNode(array $nodes, string $nodeId): ?array
    {
        foreach ($nodes as $node) {
            if (! is_array($node)) {
                continue;
            }

            if (($node['id'] ?? '') === $nodeId && ($node['type'] ?? '') === 'form') {
                return $node;
            }

            $children = is_array($node['children'] ?? null) ? $node['children'] : [];
            if ($found = $this->findFormNode($children, $nodeId)) {
                return $found;
            }
        }

        return null;
    }

    private function fieldDefinitions(mixed $fields): array
    {
        $definitions = [];

        foreach (is_array($fields) ? $fields : [] as $field) {
            if (! is_array($field) || in_array($field['type'] ?? '', ['html', 'step'], true)) {
                continue;
            }

            $id = preg_replace('/[^A-Za-z0-9_-]/', '', (string) ($field['id'] ?? ''));
            if ($id !== '') {
                $definitions[$id] = $field + ['id' => $id, 'label' => $id, 'type' => 'text'];
            }
        }

        return $definitions;
    }

    private function rules(array $definitions): array
    {
        $rules = [];

        foreach ($definitions as $id => $field) {
            $type = (string) ($field['type'] ?? 'text');
            $multiple = ! empty($field['multiple']) || $type === 'checkbox';
            $options = $this->optionValues($field);
            $fileRule = $this->fileTypeRule($field);
            $fieldRules = [! empty($field['required']) ? 'required' : 'nullable'];

            if ($multiple) {
                $fieldRules[] = 'array';
            } elseif ($type === 'email') {
                $fieldRules[] = 'email:rfc';
            } elseif ($type === 'url') {
                $fieldRules[] = 'url:http,https';
            } elseif ($type === 'number') {
                $fieldRules[] = 'numeric';
                if (is_numeric($field['min'] ?? null)) {
                    $fieldRules[] = 'min:'.$field['min'];
                }
                if (is_numeric($field['max'] ?? null)) {
                    $fieldRules[] = 'max:'.$field['max'];
                }
            } elseif ($type === 'date') {
                $fieldRules[] = 'date';
            } elseif ($type === 'acceptance') {
                $fieldRules[] = 'accepted';
            } elseif ($type === 'file') {
                $fieldRules[] = 'file';
                $fieldRules[] = 'max:10240';
                if ($fileRule) {
                    $fieldRules[] = $fileRule;
                }
            } else {
                $fieldRules[] = 'string';
                $fieldRules[] = 'max:10000';
            }

            if (! $multiple && in_array($type, ['select', 'radio'], true) && $options !== []) {
                $fieldRules[] = Rule::in($options);
            }

            $rules[$id] = $fieldRules;

            if ($multiple) {
                $itemRules = $type === 'file' ? ['file', 'max:10240'] : ['string', 'max:10000'];
                if ($type === 'file' && $fileRule) {
                    $itemRules[] = $fileRule;
                }
                if (in_array($type, ['select', 'checkbox'], true) && $options !== []) {
                    $itemRules[] = Rule::in($options);
                }
                $rules[$id.'.*'] = $itemRules;
            }
        }

        return $rules;
    }

    private function optionValues(array $field): array
    {
        if (is_array($field['options'] ?? null)) {
            return array_values(array_filter(array_map(
                fn ($option) => is_array($option) ? (string) ($option['value'] ?? $option['label'] ?? '') : (string) $option,
                $field['options'],
            ), fn ($value) => $value !== ''));
        }

        $values = [];
        foreach (preg_split('/\r?\n/', (string) ($field['optionsText'] ?? '')) ?: [] as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }
            [$label, $value] = array_pad(explode('|', $line, 2), 2, null);
            $values[] = trim((string) ($value ?? $label));
        }

        return array_values(array_unique($values));
    }

    private function fileTypeRule(array $field): ?string
    {
        $extensions = array_values(array_unique(array_filter(array_map(
            fn ($extension) => preg_replace('/[^a-z0-9]/', '', strtolower(ltrim($extension, '.'))),
            preg_split('/[,\s]+/', (string) ($field['fileTypes'] ?? '')) ?: [],
        ))));

        return $extensions === [] ? null : 'mimes:'.implode(',', $extensions);
    }

    private function submissionValues(array $definitions, array $validated): array
    {
        $fields = [];
        $files = [];

        foreach ($definitions as $id => $definition) {
            $value = $validated[$id] ?? (! empty($definition['multiple']) || ($definition['type'] ?? '') === 'checkbox' ? [] : '');
            $fields[$id] = $this->serializableValue($value, $files);
        }

        return [$fields, $files];
    }

    private function serializableValue(mixed $value, array &$files): mixed
    {
        if ($value instanceof UploadedFile) {
            $files[] = [
                'path' => $value->getRealPath(),
                'name' => $value->getClientOriginalName(),
                'mime' => $value->getMimeType() ?: 'application/octet-stream',
            ];

            return $value->getClientOriginalName();
        }

        if (is_array($value)) {
            return array_map(fn ($entry) => $this->serializableValue($entry, $files), $value);
        }

        return is_scalar($value) || $value === null ? (string) $value : '';
    }

    private function sendEmail(array $settings, string $suffix, array $definitions, array $fields, array $files): void
    {
        $prefix = $suffix === '' ? 'email' : 'email'.$suffix;
        $to = $this->emails((string) ($settings[$prefix.'To'] ?? ''));

        if ($to === []) {
            throw ValidationException::withMessages([$prefix.'To' => 'A valid recipient email is required.']);
        }

        $isHtml = ($settings[$prefix.'ContentType'] ?? 'html') !== 'plain';
        $subject = strip_tags($this->replaceShortcodes((string) ($settings[$prefix.'Subject'] ?? 'New form submission'), $definitions, $fields, false));
        $body = $this->replaceShortcodes((string) ($settings[$prefix.'Content'] ?? '[all-fields]'), $definitions, $fields, $isHtml);
        $mail = new PageBuilderElementorFormMail($subject, $body, $isHtml, $files);
        $from = $this->emails((string) ($settings[$prefix.'From'] ?? ''));
        $replyToSetting = (string) ($settings[$prefix.'ReplyTo'] ?? '');
        $replyToValue = array_key_exists($replyToSetting, $fields) ? (string) $fields[$replyToSetting] : $replyToSetting;
        $replyTo = $this->emails($replyToValue);

        if ($from !== []) {
            $mail->from($from[0], trim((string) ($settings[$prefix.'FromName'] ?? '')) ?: null);
        }
        if ($replyTo !== []) {
            $mail->replyTo($replyTo[0]);
        }

        $pending = Mail::to($to);
        $cc = $this->emails((string) ($settings[$prefix.'Cc'] ?? ''));
        $bcc = $this->emails((string) ($settings[$prefix.'Bcc'] ?? ''));
        if ($cc !== []) {
            $pending->cc($cc);
        }
        if ($bcc !== []) {
            $pending->bcc($bcc);
        }
        $pending->send($mail);
    }

    private function emails(string $value): array
    {
        return array_values(array_unique(array_filter(
            preg_split('/[,;\s]+/', trim($value)) ?: [],
            fn ($email) => filter_var($email, FILTER_VALIDATE_EMAIL) !== false,
        )));
    }

    private function replaceShortcodes(string $template, array $definitions, array $fields, bool $html): string
    {
        $format = function (mixed $value) use ($html): string {
            $text = is_array($value) ? implode(', ', $value) : (string) $value;

            return $html ? nl2br(e($text)) : $text;
        };

        $allFields = [];
        foreach ($fields as $id => $value) {
            $label = (string) ($definitions[$id]['label'] ?? $id);
            $allFields[] = $html
                ? '<tr><th align="left">'.e($label).'</th><td>'.$format($value).'</td></tr>'
                : $label.': '.$format($value);
        }

        $template = str_replace('[all-fields]', $html ? '<table>'.implode('', $allFields).'</table>' : implode("\n", $allFields), $template);

        return preg_replace_callback('/\[field\s+id=["\']?([^"\'\]\s]+)["\']?\]/i', function ($match) use ($fields, $format): string {
            return $format($fields[$match[1]] ?? '');
        }, $template) ?? $template;
    }

    private function publicHttpUrl(string $url): string
    {
        $parts = parse_url(trim($url));
        $host = strtolower((string) ($parts['host'] ?? ''));
        $scheme = strtolower((string) ($parts['scheme'] ?? ''));
        $blockedHost = $host === 'localhost' || str_ends_with($host, '.localhost') || str_ends_with($host, '.local');
        $privateIp = filter_var($host, FILTER_VALIDATE_IP) !== false
            && filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false;

        if (! in_array($scheme, ['http', 'https'], true) || $host === '' || $blockedHost || $privateIp) {
            throw ValidationException::withMessages(['webhookUrl' => 'Webhook URL must use a public HTTP or HTTPS address.']);
        }

        return trim($url);
    }

    private function safeRedirect(string $url): string
    {
        $url = trim($url);

        return preg_match('#^(?:https?://|/|\#)#i', $url) ? $url : '';
    }
}
