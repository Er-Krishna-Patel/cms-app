@props(['name' => '', 'label' => '', 'placeholder' => '', 'value' => '', 'required' => false, 'error' => null, 'rows' => 5])

<div class="form-group">
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 mb-1">
            {{ $label }}
            @if($required)
                <span class="text-red-600">*</span>
            @endif
        </label>
    @endif
    
    <textarea
        id="{{ $name }}"
        name="{{ $name }}"
        rows="{{ $rows }}"
        placeholder="{{ $placeholder }}"
        class="textarea-field @if($error) textarea-error @endif"
        @required($required)
        {{ $attributes }}
    >{{ old($name, $value) }}</textarea>
    
    @if($error)
        <p class="text-red-600 text-sm mt-1">{{ $error }}</p>
    @endif
</div>

<style scoped>
    .form-group {
        margin-bottom: 1rem;
    }

    .textarea-field {
        width: 100%;
        padding: 0.5rem 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        font-size: 1rem;
        font-family: inherit;
        resize: vertical;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .textarea-field:focus {
        outline: none;
        ring: 2px;
        ring-color: #3b82f6;
        border-color: transparent;
    }

    .textarea-field:disabled {
        background-color: #f3f4f6;
        cursor: not-allowed;
    }

    .textarea-error {
        border-color: #dc2626;
    }
</style>
