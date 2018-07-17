{{--
 * Text area form field for Bulma css
 *
 * @param  string  $id     The identifier of the field
 * @param  string  $label  Label to be displayed (optional)
 * @param  string  $placeholder  Text placeholder, optional
 * @param  string  $maxlength    Maximum number of chars (show counter), optional
 * @param  any     $autofocus If defined, field will be autofocused when page loads
--}}
@php
    $errorId = str_contains($id, '[') ? str_replace('[','.',str_replace(']','',$id)) : $id;
    $hasError = $errors->has($errorId);
@endphp
<b-field {{ $hasError ? 'type=is-danger' : '' }}
         label="{{ isset($label) ?  $label : '' }}"
         message="{{ $hasError ? $errors->first($errorId) : '' }}"
>
    <b-input name="{{ $id }}"
             type="textarea"
             value="{{ old($errorId) }}"
             placeholder="{{ $placeholder or '' }}"
             maxlength="{{ $maxlength or '' }}"
             {{ isset($autofocus) ? 'autofocus' : '' }}
    ></b-input>
</b-field>