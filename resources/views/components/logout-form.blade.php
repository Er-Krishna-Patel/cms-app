@props(['text' => 'Log Out', 'class' => ''])

<form 
    method="POST" 
    action="{{ route('logout') }}" 
    class="inline"
    x-data
    @submit.prevent="$el.submit()"
>
    @csrf
    <button 
        type="submit" 
        class="{{ $class }}"
    >
        {{ $text }}
    </button>
</form>
