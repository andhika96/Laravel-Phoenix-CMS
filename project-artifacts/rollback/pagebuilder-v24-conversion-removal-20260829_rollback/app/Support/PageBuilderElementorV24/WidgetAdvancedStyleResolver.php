<?php

namespace App\Support\PageBuilderElementorV24;

use Illuminate\Http\Request;

final class WidgetAdvancedStyleResolver
{
    public function resolve(array $settings, string $nodeId, ?Request $request = null): array
    {
        $request ??= request();
        $safeNodeToken = preg_replace('/[^A-Za-z0-9_-]+/', '-', trim($nodeId)) ?: 'widget';
        $internalId = 'pb-node-'.$safeNodeToken;
        $requestedId = trim((string) ($settings['cssId'] ?? ''));
        $requestedId = preg_match('/^[A-Za-z][A-Za-z0-9_-]*$/', $requestedId) ? $requestedId : '';
        $usedIds = $request->attributes->get('_pagebuilder_elementor_css_ids', []);
        $usedIds = is_array($usedIds) ? $usedIds : [];
        $domId = $internalId;

        if ($requestedId !== '' && ! in_array($requestedId, $usedIds, true)) {
            $domId = $requestedId;
            $usedIds[] = $requestedId;
            $request->attributes->set('_pagebuilder_elementor_css_ids', $usedIds);
        }

        $classes = ['pb-advanced-widget'];
        $preserveCssClasses = ($settings['preserveCssClasses'] ?? false) === true;
        foreach (preg_split('/\s+/', trim((string) ($settings['cssClass'] ?? ''))) ?: [] as $class) {
            $class = $preserveCssClasses
                ? preg_replace('/[^A-Za-z0-9_:\/\[\]\.\-%]/', '', $class)
                : preg_replace('/[^A-Za-z0-9_-]/', '', $class);
            if ($class !== '') {
                $classes[] = $class;
            }
        }
        if ($this->truthy($settings['hideDesktop'] ?? false)) $classes[] = 'pb-hide-desktop';
        if ($this->truthy($settings['hideTablet'] ?? false)) $classes[] = 'pb-hide-tablet';
        if ($this->truthy($settings['hideMobile'] ?? false)) $classes[] = 'pb-hide-mobile';
        $fullBleedByDevice = [
            'desktop' => $this->fullBleedEnabled($this->responsive($settings, 'fullBleed', '', false)),
            'tablet' => $this->fullBleedEnabled($this->responsive($settings, 'fullBleed', 'Tablet', false)),
            'mobile' => $this->fullBleedEnabled($this->responsive($settings, 'fullBleed', 'Mobile', false)),
        ];
        if (in_array(true, $fullBleedByDevice, true)) $classes[] = 'pb-full-bleed';
        foreach ($fullBleedByDevice as $device => $enabled) {
            if ($enabled) $classes[] = 'pb-full-bleed-'.$device;
        }
        if ($this->truthy($settings['scrollingEffects'] ?? false)) {
            $classes[] = 'pb-motion-scroll';
            if (! $this->truthy($settings['scrollApplyDesktop'] ?? true)) $classes[] = 'pb-scroll-off-desktop';
            if (! $this->truthy($settings['scrollApplyTablet'] ?? true)) $classes[] = 'pb-scroll-off-tablet';
            if (! $this->truthy($settings['scrollApplyMobile'] ?? true)) $classes[] = 'pb-scroll-off-mobile';
        }
        if ($this->truthy($settings['mouseEffects'] ?? false)) {
            $classes[] = 'pb-motion-mouse';
            if (! $this->truthy($settings['mouseApplyDesktop'] ?? true)) $classes[] = 'pb-mouse-off-desktop';
            if (! $this->truthy($settings['mouseApplyTablet'] ?? true)) $classes[] = 'pb-mouse-off-tablet';
            if (! $this->truthy($settings['mouseApplyMobile'] ?? true)) $classes[] = 'pb-mouse-off-mobile';
        }
        $sticky = strtolower(trim((string) ($settings['sticky'] ?? 'none')));
        if (in_array($sticky, ['top', 'bottom'], true)) {
            if (! $this->truthy($settings['stickyOnDesktop'] ?? true)) $classes[] = 'pb-sticky-off-desktop';
            if (! $this->truthy($settings['stickyOnTablet'] ?? true)) $classes[] = 'pb-sticky-off-tablet';
            if (! $this->truthy($settings['stickyOnMobile'] ?? true)) $classes[] = 'pb-sticky-off-mobile';
        }
        $entranceAnimation = preg_replace('/[^A-Za-z0-9_-]/', '', (string) ($settings['entranceAnimation'] ?? ''));
        if ($entranceAnimation !== '') {
            $classes[] = 'pb-advanced-entrance';
            $classes[] = 'pb-anim-'.$entranceAnimation;
        }

        $normalRules = $this->layoutRules($settings, '');
        $normalRules = array_merge($normalRules, $this->backgroundRules($settings));
        $normalRules = array_merge($normalRules, $this->borderRules($settings));
        $normalRules = array_merge($normalRules, $this->maskRules($settings, ''));
        $normalRules[] = '--pb-advanced-transform:'.$this->transform($settings, '', '');
        $normalRules[] = 'transform-origin:'.$this->transformOrigin($settings);
        $normalRules[] = 'transition:background '.$this->duration($settings['advancedBackgroundHoverDuration'] ?? 0.3).'s ease,border '.$this->duration($settings['advancedBorderHoverDuration'] ?? 0.3).'s ease,box-shadow '.$this->duration($settings['advancedBorderHoverDuration'] ?? 0.3).'s ease,transform '.$this->duration($settings['transformHoverDuration'] ?? 0.3).'s ease';

        $hoverRules = array_merge(
            $this->backgroundRules($settings, 'Hover'),
            $this->borderRules($settings, 'Hover'),
            [
                '--pb-advanced-transform:'.$this->transform($settings, 'Hover', ''),
                'border-radius:'.$this->length($this->responsive($settings, 'advancedBorderRadiusHover', '', '0px'), '0px'),
            ],
        );

        $css = ['#'.$domId.'{'.implode(';', array_filter($normalRules)).'}'];
        $css[] = '#'.$domId.':hover{'.implode(';', array_filter($hoverRules)).'}';

        foreach (['Tablet' => 1024, 'Mobile' => 767] as $suffix => $breakpoint) {
            $rules = $this->layoutRules($settings, $suffix);
            $rules = array_merge($rules, $this->maskRules($settings, $suffix));
            $rules[] = 'border-radius:'.$this->length($this->responsive($settings, 'advancedBorderRadius', $suffix, '0px'), '0px');
            $rules[] = '--pb-advanced-transform:'.$this->transform($settings, '', $suffix);
            $hoverResponsive = [
                'border-radius:'.$this->length($this->responsive($settings, 'advancedBorderRadiusHover', $suffix, '0px'), '0px'),
                '--pb-advanced-transform:'.$this->transform($settings, 'Hover', $suffix),
            ];
            $css[] = '@media (max-width: '.$breakpoint.'px){#'.$domId.'{'.implode(';', array_filter($rules)).'}}';
            $css[] = '@media (max-width: '.$breakpoint.'px){#'.$domId.':hover{'.implode(';', array_filter($hoverResponsive)).'}}';
        }

        $customCss = $this->customCss($settings['customCssCode'] ?? '', $domId);
        if ($customCss !== '') {
            $css[] = $customCss;
        }

        $attributes = $this->attributes($settings['attributes'] ?? []);
        $importNodeKey = trim((string) ($settings['importNodeKey'] ?? ''));
        if (preg_match('/^import-node-[A-Za-z0-9_-]+$/', $importNodeKey)) {
            $attributes['data-pb-import-node'] = $importNodeKey;
        }

        return [
            'id' => $domId,
            'classes' => array_values(array_unique($classes)),
            'attributes' => $attributes,
            'css' => implode('', $css),
            'motion' => json_encode($this->motion($settings), JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?: '{}',
            'entranceDelay' => max(0, (int) ($settings['entranceDelay'] ?? 0)),
            'entranceDuration' => in_array(($settings['entranceDuration'] ?? 'normal'), ['slow', 'normal', 'fast'], true) ? ($settings['entranceDuration'] ?? 'normal') : 'normal',
        ];
    }

    private function layoutRules(array $settings, string $suffix): array
    {
        $rules = [];
        foreach (['Top' => 'top', 'Right' => 'right', 'Bottom' => 'bottom', 'Left' => 'left'] as $key => $side) {
            $rules[] = 'margin-'.$side.':'.$this->space($this->responsive($settings, 'margin'.$key, $suffix, '0px'), '0');
            $rules[] = 'padding-'.$side.':'.$this->length($this->responsive($settings, 'padding'.$key, $suffix, '0px'), '0');
        }

        $widthMode = strtolower(trim((string) $this->responsive($settings, 'widthMode', $suffix, 'default')));
        $rules[] = 'width:'.match ($widthMode) {
            'full' => '100%',
            'inline' => 'fit-content',
            'custom' => $this->length($this->responsive($settings, 'customWidth', $suffix, ''), 'auto'),
            default => 'auto',
        };
        if ($this->fullBleedEnabled($this->responsive($settings, 'fullBleed', $suffix, false))) {
            $rules[] = 'width:100%';
            $rules[] = 'max-width:100%';
            $rules[] = 'align-self:stretch';
        }

        $alignSelf = strtolower(trim((string) $this->responsive($settings, 'alignSelf', $suffix, 'auto')));
        if (in_array($alignSelf, ['auto', 'flex-start', 'center', 'flex-end', 'stretch'], true)) {
            $rules[] = 'align-self:'.$alignSelf;
        }

        $orderMode = strtolower(trim((string) $this->responsive($settings, 'orderMode', $suffix, 'default')));
        if ($orderMode === 'start') $rules[] = 'order:-9999';
        if ($orderMode === 'end') $rules[] = 'order:9999';
        if ($orderMode === 'custom' && is_numeric($this->responsive($settings, 'order', $suffix, ''))) {
            $rules[] = 'order:'.(int) $this->responsive($settings, 'order', $suffix, 0);
        }

        $sizeMode = strtolower(trim((string) $this->responsive($settings, 'sizeMode', $suffix, 'none')));
        if ($sizeMode === 'grow') $rules[] = 'flex:1 1 0';
        if ($sizeMode === 'shrink') $rules[] = 'flex:0 1 auto';
        if ($sizeMode === 'custom') {
            $grow = is_numeric($this->responsive($settings, 'flexGrow', $suffix, 0)) ? (float) $this->responsive($settings, 'flexGrow', $suffix, 0) : 0;
            $shrink = is_numeric($this->responsive($settings, 'flexShrink', $suffix, 1)) ? (float) $this->responsive($settings, 'flexShrink', $suffix, 1) : 1;
            $rules[] = 'flex:'.$grow.' '.$shrink.' auto';
        }

		$columnSpan = $this->clamp($this->responsive($settings, 'gridColumnSpan', $suffix, 1), 1, 12);
		$rowSpan = $this->clamp($this->responsive($settings, 'gridRowSpan', $suffix, 1), 1, 12);
		$rules[] = 'grid-column:span '.$columnSpan;
		$rules[] = 'grid-row:span '.$rowSpan;

        $position = strtolower(trim((string) ($settings['position'] ?? 'default')));
        if (in_array($position, ['absolute', 'fixed'], true)) {
            $rules[] = 'position:'.$position;
            $rules[] = (($settings['horizontalOrientation'] ?? 'left') === 'right' ? 'right:' : 'left:').$this->space($this->responsive($settings, 'positionX', $suffix, '0px'), '0');
            $rules[] = (($settings['verticalOrientation'] ?? 'top') === 'bottom' ? 'bottom:' : 'top:').$this->space($this->responsive($settings, 'positionY', $suffix, '0px'), '0');
        }

        $sticky = strtolower(trim((string) ($settings['sticky'] ?? 'none')));
        if (in_array($sticky, ['top', 'bottom'], true)) {
            $rules[] = 'position:sticky';
            $rules[] = $sticky.':'.$this->space($this->responsive($settings, 'stickyOffset', $suffix, '0px'), '0');
        }

        $zIndex = $this->responsive($settings, 'zIndex', $suffix, '');
        if ($zIndex !== '' && is_numeric($zIndex)) {
            $rules[] = 'z-index:'.(int) $zIndex;
        }

        return $rules;
    }

    private function backgroundRules(array $settings, string $suffix = ''): array
    {
        $type = strtolower(trim((string) ($settings['advancedBackgroundType'.$suffix] ?? 'none')));
        if ($type === 'classic') {
            $rules = [
                'background-color:'.$this->color($settings['advancedBackgroundColor'.$suffix] ?? '', 'transparent'),
            ];
            $image = $this->safeImageUrl($settings['advancedBackgroundImage'.$suffix] ?? '');
            if ($image !== '') {
                $rules[] = 'background-image:url("'.$image.'")';
				$position = ($settings['advancedBackgroundPosition'.$suffix] ?? 'center center') === 'custom'
					? $this->length($settings['advancedBackgroundPositionX'.$suffix] ?? '50%', '50%').' '.$this->length($settings['advancedBackgroundPositionY'.$suffix] ?? '50%', '50%')
					: $this->backgroundPosition($settings['advancedBackgroundPosition'.$suffix] ?? 'center center');
				$rules[] = 'background-position:'.$position;
                $rules[] = 'background-attachment:'.(($settings['advancedBackgroundAttachment'.$suffix] ?? 'scroll') === 'fixed' ? 'fixed' : 'scroll');
                $rules[] = 'background-repeat:'.$this->enum($settings['advancedBackgroundRepeat'.$suffix] ?? 'no-repeat', ['no-repeat', 'repeat', 'repeat-x', 'repeat-y'], 'no-repeat');
				$size = ($settings['advancedBackgroundSize'.$suffix] ?? 'cover') === 'custom'
					? $this->length($settings['advancedBackgroundCustomSize'.$suffix] ?? '100%', '100%')
					: $this->enum($settings['advancedBackgroundSize'.$suffix] ?? 'cover', ['auto', 'cover', 'contain'], 'cover');
				$rules[] = 'background-size:'.$size;
            }

            return $rules;
        }

        if ($type === 'gradient') {
            $first = $this->color($settings['advancedGradientColorOne'.$suffix] ?? '#ffffff', '#ffffff');
            $second = $this->color($settings['advancedGradientColorTwo'.$suffix] ?? '#000000', '#000000');
            $firstLocation = $this->clamp($settings['advancedGradientLocationOne'.$suffix] ?? 0, 0, 100);
            $secondLocation = $this->clamp($settings['advancedGradientLocationTwo'.$suffix] ?? 100, 0, 100);
            $gradientType = strtolower(trim((string) ($settings['advancedGradientType'.$suffix] ?? 'linear')));
            $gradient = $gradientType === 'radial'
                ? 'radial-gradient(circle, '.$first.' '.$firstLocation.'%, '.$second.' '.$secondLocation.'%)'
                : 'linear-gradient('.$this->clamp($settings['advancedGradientAngle'.$suffix] ?? 180, 0, 360).'deg, '.$first.' '.$firstLocation.'%, '.$second.' '.$secondLocation.'%)';

            return ['background-image:'.$gradient];
        }

        return [];
    }

    private function borderRules(array $settings, string $suffix = ''): array
    {
        $type = $this->enum($settings['advancedBorderType'.$suffix] ?? 'none', ['none', 'solid', 'double', 'dotted', 'dashed', 'groove'], 'none');
        $rules = [
            'border-style:'.$type,
			'border-width:'.($type === 'none' ? '0' : $this->borderWidth($settings['advancedBorderWidth'.$suffix] ?? '1px')),
            'border-color:'.$this->color($settings['advancedBorderColor'.$suffix] ?? 'transparent', 'transparent'),
        ];
        if ($suffix === '') {
            $rules[] = 'border-radius:'.$this->length($this->responsive($settings, 'advancedBorderRadius', '', '0px'), '0px');
        }

        if ($this->truthy($settings['advancedBoxShadowEnabled'.$suffix] ?? false)) {
            $rules[] = 'box-shadow:'.implode(' ', array_filter([
                $this->length($settings['advancedBoxShadowX'.$suffix] ?? '0px', '0'),
                $this->length($settings['advancedBoxShadowY'.$suffix] ?? '4px', '4px'),
                $this->length($settings['advancedBoxShadowBlur'.$suffix] ?? '16px', '16px'),
                $this->length($settings['advancedBoxShadowSpread'.$suffix] ?? '0px', '0'),
                $this->color($settings['advancedBoxShadowColor'.$suffix] ?? 'rgba(0,0,0,.2)', 'rgba(0,0,0,.2)'),
                $this->truthy($settings['advancedBoxShadowInset'.$suffix] ?? false) ? 'inset' : '',
            ]));
        }

        return $rules;
    }

    private function maskRules(array $settings, string $suffix): array
    {
        if (! $this->truthy($settings['maskEnabled'] ?? false)) {
            return [];
        }

        $shape = strtolower(trim((string) ($settings['maskShape'] ?? 'circle')));
        $custom = $shape === 'custom' ? $this->safeImageUrl($settings['maskCustomImage'] ?? '') : '';
        $image = $custom !== '' ? 'url("'.$custom.'")' : 'radial-gradient(circle, #000 60%, transparent 61%)';
        $sizeMode = strtolower(trim((string) $this->responsive($settings, 'maskSize', $suffix, 'fit')));
        $size = match ($sizeMode) {
            'fill' => 'cover',
            'custom' => $this->clamp($this->responsive($settings, 'maskScale', $suffix, 100), 1, 300).'%',
            default => 'contain',
        };
        $position = strtolower(trim((string) $this->responsive($settings, 'maskPosition', $suffix, 'center center')));
        if ($position === 'custom') {
            $position = $this->length($this->responsive($settings, 'maskPositionX', $suffix, '50%'), '50%').' '.$this->length($this->responsive($settings, 'maskPositionY', $suffix, '50%'), '50%');
        } else {
            $position = $this->backgroundPosition($position);
        }
        $repeat = $this->enum($this->responsive($settings, 'maskRepeat', $suffix, 'no-repeat'), ['no-repeat', 'repeat', 'repeat-x', 'repeat-y', 'round', 'space'], 'no-repeat');

        return [
            'mask-image:'.$image,
            '-webkit-mask-image:'.$image,
            'mask-size:'.$size,
            '-webkit-mask-size:'.$size,
            'mask-position:'.$position,
            '-webkit-mask-position:'.$position,
            'mask-repeat:'.$repeat,
            '-webkit-mask-repeat:'.$repeat,
        ];
    }

    private function transform(array $settings, string $stateSuffix, string $responsiveSuffix): string
    {
		$scaleValue = $this->responsive($settings, 'transformScale'.$stateSuffix, $responsiveSuffix, 1);
		$scale = is_numeric($scaleValue) ? (float) $scaleValue : 1;
        $parts = [
			'perspective('.$this->length($this->responsive($settings, 'transformPerspective'.$stateSuffix, $responsiveSuffix, '0px'), '0px').')',
            'translate('.$this->space($this->responsive($settings, 'transformOffsetX'.$stateSuffix, $responsiveSuffix, '0px'), '0').','.$this->space($this->responsive($settings, 'transformOffsetY'.$stateSuffix, $responsiveSuffix, '0px'), '0').')',
			'rotate('.$this->angle($this->responsive($settings, 'transformRotate'.$stateSuffix, $responsiveSuffix, '0deg')).')',
			'rotateX('.$this->angle($this->responsive($settings, 'transformRotateX'.$stateSuffix, $responsiveSuffix, '0deg')).')',
			'rotateY('.$this->angle($this->responsive($settings, 'transformRotateY'.$stateSuffix, $responsiveSuffix, '0deg')).')',
            'scale('.$scale.')',
			'skew('.$this->angle($this->responsive($settings, 'transformSkewX'.$stateSuffix, $responsiveSuffix, '0deg')).','.$this->angle($this->responsive($settings, 'transformSkewY'.$stateSuffix, $responsiveSuffix, '0deg')).')',
        ];
		if ($this->truthy($this->responsive($settings, 'transformFlipHorizontal'.$stateSuffix, $responsiveSuffix, false))) $parts[] = 'scaleX(-1)';
		if ($this->truthy($this->responsive($settings, 'transformFlipVertical'.$stateSuffix, $responsiveSuffix, false))) $parts[] = 'scaleY(-1)';

        return implode(' ', $parts);
    }

    private function transformOrigin(array $settings): string
    {
        $allowed = ['left', 'center', 'right', 'top', 'bottom'];
        $x = $this->enum($settings['transformOriginX'] ?? 'center', $allowed, 'center');
        $y = $this->enum($settings['transformOriginY'] ?? 'center', $allowed, 'center');

        return $x.' '.$y;
    }

    private function motion(array $settings): array
    {
        return [
            'scrollingEffects' => $this->truthy($settings['scrollingEffects'] ?? false),
            'verticalScrollEnabled' => $this->truthy($settings['verticalScrollEnabled'] ?? false),
            'verticalScrollDirection' => $this->enum($settings['verticalScrollDirection'] ?? 'up', ['up', 'down'], 'up'),
            'verticalScrollSpeed' => (float) ($settings['verticalScrollSpeed'] ?? 4),
            'horizontalScrollEnabled' => $this->truthy($settings['horizontalScrollEnabled'] ?? false),
            'horizontalScrollDirection' => $this->enum($settings['horizontalScrollDirection'] ?? 'left', ['left', 'right'], 'left'),
            'horizontalScrollSpeed' => (float) ($settings['horizontalScrollSpeed'] ?? 4),
            'transparencyEnabled' => $this->truthy($settings['transparencyEnabled'] ?? false),
            'transparencyDirection' => (string) ($settings['transparencyDirection'] ?? 'fade-in'),
            'transparencyLevel' => (float) ($settings['transparencyLevel'] ?? 5),
            'blurEnabled' => $this->truthy($settings['blurEnabled'] ?? false),
            'blurDirection' => (string) ($settings['blurDirection'] ?? 'fade-in'),
            'blurLevel' => (float) ($settings['blurLevel'] ?? 5),
            'rotateEnabled' => $this->truthy($settings['rotateEnabled'] ?? false),
            'rotateDirection' => (string) ($settings['rotateDirection'] ?? 'left'),
            'rotateSpeed' => (float) ($settings['rotateSpeed'] ?? 4),
            'scaleEnabled' => $this->truthy($settings['scaleEnabled'] ?? false),
            'scaleDirection' => (string) ($settings['scaleDirection'] ?? 'up'),
            'scaleSpeed' => (float) ($settings['scaleSpeed'] ?? 4),
            'applyDesktop' => $this->truthy($settings['scrollApplyDesktop'] ?? true),
            'applyTablet' => $this->truthy($settings['scrollApplyTablet'] ?? true),
            'applyMobile' => $this->truthy($settings['scrollApplyMobile'] ?? true),
            'mouseEffects' => $this->truthy($settings['mouseEffects'] ?? false),
            'mouseTrackEnabled' => $this->truthy($settings['mouseTrackEnabled'] ?? false),
            'mouseTrackDirection' => (string) ($settings['mouseTrackDirection'] ?? 'direct'),
            'mouseTrackSpeed' => (float) ($settings['mouseTrackSpeed'] ?? 1),
            'tilt3dEnabled' => $this->truthy($settings['tilt3dEnabled'] ?? false),
            'tilt3dDirection' => (string) ($settings['tilt3dDirection'] ?? 'direct'),
            'tilt3dSpeed' => (float) ($settings['tilt3dSpeed'] ?? 1),
        ];
    }

    private function attributes(mixed $attributes): array
    {
        $output = [];
        if (! is_array($attributes)) return $output;

        foreach ($attributes as $attribute) {
            if (! is_array($attribute)) continue;
            $name = strtolower(trim((string) ($attribute['name'] ?? $attribute['key'] ?? '')));
            if (! preg_match('/^[a-z][a-z0-9_:.-]*$/', $name)) continue;
            if (str_starts_with($name, 'on') || in_array($name, ['style', 'id', 'class', 'href', 'src', 'action', 'formaction', 'xlink:href'], true)) continue;
            $output[$name] = (string) ($attribute['value'] ?? '');
        }

        return $output;
    }

    private function customCss(mixed $css, string $domId): string
    {
        $css = trim((string) $css);
        if ($css === '') return '';
        $css = preg_replace('/@import\b[^;]*;?/i', '', $css) ?? '';
        $css = preg_replace('/<\/?style\b[^>]*>/i', '', $css) ?? '';
        $css = preg_replace('/(?:javascript\s*:|expression\s*\()/i', '', $css) ?? '';

        return trim(preg_replace('/\bselector\b/', '#'.$domId, $css) ?? '');
    }

    private function responsive(array $settings, string $base, string $suffix, mixed $fallback): mixed
    {
        $keys = $suffix === 'Mobile'
            ? [$base.'Mobile', $base.'Tablet', $base]
            : ($suffix === 'Tablet' ? [$base.'Tablet', $base] : [$base]);
        foreach ($keys as $key) {
            $value = $settings[$key] ?? null;
            if ($value !== '' && $value !== null) return $value;
        }

        return $fallback;
    }

    private function length(mixed $value, string $fallback): string
    {
        $raw = trim((string) $value);
        if ($raw === '') return $fallback;
        if (preg_match('/^-?\d+(?:\.\d+)?(?:px|pt|%|em|rem|vw|vh)?$/i', $raw)) return $raw;
        $tokens = preg_split('/\s+/', $raw) ?: [];
        if (count($tokens) >= 2 && count($tokens) <= 4) {
            foreach ($tokens as $token) {
                if (! preg_match('/^-?\d+(?:\.\d+)?(?:px|pt|%|em|rem|vw|vh)?$/i', $token)) return $fallback;
            }

            return implode(' ', $tokens);
        }

        return $fallback;
    }

	private function borderWidth(mixed $value): string
	{
		$tokens = preg_split('/\s+/', trim((string) $value)) ?: [];
		$tokens = array_slice(array_values(array_filter($tokens, fn ($token) => $token !== '')), 0, 4);
		if ($tokens === []) return '1px';

		return implode(' ', array_map(fn ($token) => $this->length($token, '1px'), $tokens));
	}

    private function space(mixed $value, string $fallback): string
    {
        return strtolower(trim((string) $value)) === 'auto' ? 'auto' : $this->length($value, $fallback);
    }

    private function angle(mixed $value): string
    {
        $raw = trim((string) $value);

        return preg_match('/^-?\d+(?:\.\d+)?(?:deg|rad|turn)$/i', $raw) ? $raw : '0deg';
    }

    private function color(mixed $value, string $fallback): string
    {
        $raw = trim((string) $value);

        return $raw !== '' && preg_match('/^[#a-z0-9(),.%\s-]+$/i', $raw) ? $raw : $fallback;
    }

    private function backgroundPosition(mixed $value): string
    {
        $allowed = ['left top', 'center top', 'right top', 'left center', 'center center', 'right center', 'left bottom', 'center bottom', 'right bottom'];

        return $this->enum(strtolower(trim((string) $value)), $allowed, 'center center');
    }

    private function safeImageUrl(mixed $value): string
    {
        $url = trim((string) $value);
        if ($url === '' || str_starts_with($url, '//')) return '';
        if (! preg_match('/^(?:https?:|data:image\/(?:png|gif|jpe?g|webp);base64,|\/)/i', $url)) return '';

        return str_replace(['"', "'", '\\'], '', $url);
    }

    private function enum(mixed $value, array $allowed, string $fallback): string
    {
        $value = strtolower(trim((string) $value));

        return in_array($value, $allowed, true) ? $value : $fallback;
    }

    private function duration(mixed $value): float
    {
        return $this->clamp($value, 0, 10);
    }

    private function clamp(mixed $value, float $min, float $max): float
    {
        $number = is_numeric($value) ? (float) $value : $min;

        return max($min, min($max, $number));
    }

    private function truthy(mixed $value): bool
    {
        return in_array($value, [true, 1, '1', 'true'], true);
    }

    private function fullBleedEnabled(mixed $value): bool
    {
        return $this->truthy($value) || strtolower(trim((string) $value)) === 'full';
    }
}
