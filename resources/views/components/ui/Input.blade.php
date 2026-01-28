@props(['name' => '', 'label' => '', 'type' => 'text', 'placeholder' => '', 'value' => '', 'required' => false, 'error' => null])

<div class="form-group">
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 mb-1">
            {{ $label }}
            @if($required)
                <span class="text-red-600">*</span>
            @endif
        </label>
    @endif
    
    <input
        type="{{ $type }}"
        id="{{ $name }}"
        name="{{ $name }}"
        value="{{ old($name, $value) }}"
        placeholder="{{ $placeholder }}"
        class="input-field @if($error) input-error @endif"
        @required($required)
        {{ $attributes }}
    />
    
    @if($error)
        <p class="text-red-600 text-sm mt-1">{{ $error }}</p>
    @endif
</div>

<style scoped>
    .form-group {
        margin-bottom: 1rem;
    }

    .input-field {
        width: 100%;
        padding: 0.5rem 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        font-size: 1rem;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .input-field:focus {
        outline: none;
        ring: 2px;
        ring-color: #3b82f6;
        border-color: transparent;
    }

    .input-field:disabled {
        background-color: #f3f4f6;
        cursor: not-allowed;
    }

    .input-error {
        border-color: #dc2626;
    }
</style>
