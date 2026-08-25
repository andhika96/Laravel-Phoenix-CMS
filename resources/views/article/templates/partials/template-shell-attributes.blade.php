@php
    $shellOptions = $templateOptions['shell'] ?? [];
    $shellPadding = $shellOptions['padding'] ?? [];
    $shellMargin = $shellOptions['margin'] ?? [];
    $shellFrame = $shellOptions['frame'] ?? [];
    $shellDevices = ['desktop', 'tablet', 'mobile'];
    $shellSides = ['top', 'right', 'bottom', 'left'];
    $shellStyle = '';

    foreach ($shellDevices as $device) {
        foreach ($shellSides as $side) {
            $shellStyle .= '--article-shell-padding-'.$device.'-'.$side.':'.data_get($shellPadding, $device.'.'.$side, '0px').';';
            $shellStyle .= '--article-shell-margin-'.$device.'-'.$side.':'.data_get($shellMargin, $device.'.'.$side, '0px').';';
        }
    }

    $shellStyle .= '--article-shell-frame-border-color:'.data_get($shellFrame, 'border_color', '#e1e6ee').';';
    $shellStyle .= '--article-shell-frame-border-width:'.data_get($shellFrame, 'border_width', '1px').';';
    $shellStyle .= '--article-shell-frame-radius:'.data_get($shellFrame, 'radius', '1rem').';';
    $shellStyle .= '--article-shell-frame-background:'.data_get($shellFrame, 'background_color', '#ffffff').';';
@endphp
data-article-shell-padding="{{ !empty($shellPadding['enabled']) ? 'true' : 'false' }}"
data-article-shell-margin="{{ !empty($shellMargin['enabled']) ? 'true' : 'false' }}"
data-article-shell-frame="{{ !empty($shellFrame['enabled']) ? 'true' : 'false' }}"
style="{{ $shellStyle }}"
