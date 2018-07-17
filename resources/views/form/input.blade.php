{{--
 * Single line form input field for Bulma css
 *
 * @param  string  $id     The identifier of the field
 * @param  string  $label  Label to be displayed (optional)
 * @param  string  $type   Type of the input field (text [default], password, email, url, etc)
 * @param  string  $placeholder Text placeholder, optional
 * @param  string  $icon   FontAwesome class (without 'fa-' prefix), optional
 * @param  any     $optional If defined, field won't me marked as required
 * @param  any     $autofocus If defined, field will be autofocused when page loads
--}}
@php
    if (!isset($type)) $type = 'text';
    //$errors = new \Illuminate\Support\MessageBag([$id => 'Mensagem de erro']);
    $errorId = str_contains($id, '[') ? str_replace('[','.',str_replace(']','',$id)) : $id;
    $hasError = $errors->has($errorId);
@endphp
<div class="field">
    @if (isset($label))
        <label class="label">{{ $label }}</label>
    @endif
    <p class="control {{ isset($icon) ? 'has-icons-left' : '' }} {{ $hasError ? 'has-icons-right' : '' }}">
        {{-- Using <cleave-input> (a Vue component) for phones; <input> for all others --}}
        <{{ $type == 'tel' ? 'cleave-input' : "input type=$type" }}
               name="{{ $id }}"
               class="input {{ $hasError ? 'is-danger' : '' }}"
               placeholder="{{ $placeholder or '' }}"
               {{--{{ isset($optional) ? '' : 'required' }}--}}
               {{ isset($autofocus) ? 'autofocus' : '' }}
               {!! $type == 'tel' ? ':options="{ phone: true, phoneRegionCode: \'BR\' }"' : '' !!}
               value="{{ trim(old($errorId)) }}"
        >
        @if ($type == 'tel') </cleave-input> @endif
        @if (isset($icon))
            <span class="icon is-left"><i class="fa fa-{{ $icon }}" aria-hidden="true"></i></span>
        @endif
        @if ($hasError)
            <span class="icon is-right is-danger"><i class="fa fa-exclamation-circle" aria-hidden="true"></i></span>
        @endif
    </p>
    @if($hasError)
        <p class="help is-danger">{{ $errors->first($errorId) }}</p>
    @endif
</div>