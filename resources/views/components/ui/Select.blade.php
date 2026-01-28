@props(['name' => '', 'label' => '', 'placeholder' => '', 'value' => '', 'required' => false, 'error' => null, 'options' => []])

<div class="form-group">
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 mb-1">
            {{ $label }}
            @if($required)
                <span class="text-red-600">*</span>
            @endif
        </label>
    @endif
    
    <select
        id="{{ $name }}"
        name="{{ $name }}"
        class="select-field @if($error) select-error @endif"
        @required($required)
        {{ $attributes }}
    >
        @if($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif
        
        @foreach($options as $optValue => $optLabel)
            <option value="{{ $optValue }}" @selected(old($name, $value) == $optValue)>
                {{ $optLabel }}
            </option>
        @endforeach
    </select>
    
    @if($error)
        <p class="text-red-600 text-sm mt-1">{{ $error }}</p>
    @endif
</div>

<style scoped>
    .form-group {
        margin-bottom: 1rem;
    }

    .select-field {
        width: 100%;
        padding: 0.5rem 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        font-size: 1rem;
        background-color: white;
        cursor: pointer;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .select-field:focus {
        outline: none;
        ring: 2px;
        ring-color: #3b82f6;
        border-color: transparent;
    }

    .select-field:disabled {
        background-color: #f3f4f6;
        cursor: not-allowed;
    }

    .select-error {
        border-color: #dc2626;
    }
</style>
