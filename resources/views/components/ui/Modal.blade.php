@props(['id' => '', 'title' => '', 'size' => 'md'])

<div 
    id="{{ $id }}"
    x-data="{ open: false }"
    x-show="open"
    @open-modal.window="if ($event.detail === '{{ $id }}') open = true"
    @close-modal.window="if ($event.detail === '{{ $id }}') open = false"
    class="modal-overlay"
>
    <div class="modal-container modal-{{ $size }}" @click.stop>
        <div class="modal-header">
            <h2 class="modal-title">{{ $title }}</h2>
            <button 
                type="button"
                @click="open = false; dispatchEvent(new CustomEvent('modal:closed', { detail: '{{ $id }}' }))"
                class="modal-close"
                aria-label="Close modal"
            >
                ✕
            </button>
        </div>
        
        <div class="modal-body">
            {{ $slot }}
        </div>
    </div>
</div>

<style scoped>
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
    }

    .modal-container {
        background: white;
        border-radius: 0.5rem;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        max-height: 90vh;
        overflow-y: auto;
    }

    .modal-sm {
        width: 90%;
        max-width: 448px;
    }

    .modal-md {
        width: 90%;
        max-width: 672px;
    }

    .modal-lg {
        width: 90%;
        max-width: 896px;
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.5rem;
        border-bottom: 1px solid #e5e7eb;
    }

    .modal-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #111827;
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: #6b7280;
        transition: color 0.2s;
        padding: 0;
        width: 2rem;
        height: 2rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-close:hover {
        color: #111827;
    }

    .modal-body {
        padding: 1.5rem;
    }
</style>
