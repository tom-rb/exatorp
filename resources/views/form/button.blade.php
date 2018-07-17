{{--
 * Button with icon for Bulma css
 *
 * @param  string  $label  Button's label
 * @param  string  $icon   FontAwesome class (without 'fa-' prefix), optional
--}}
<div class="field">
    <p class="control">
        <button class="button {{ $class or '' }}">
            @if (isset($icon))
                <span class="icon"><i class="fa fa-{{ $icon }}" aria-hidden="true"></i></span>
            @endif
            <span>{{ $label }}</span>
        </button>
    </p>
</div>