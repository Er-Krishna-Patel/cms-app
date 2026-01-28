@props(['action' => '', 'confirmMessage' => 'Are you sure?', 'buttonText' => 'Delete', 'buttonClass' => 'text-red-600 hover:underline'])

<form 
    action="{{ $action }}" 
    method="POST" 
    class="inline"
    data-confirm-delete="item"
    data-confirm-message="{{ $confirmMessage }}"
>
    @csrf
    @method('DELETE')
    <button type="submit" class="{{ $buttonClass }}">
        {{ $buttonText }}
    </button>
</form>
