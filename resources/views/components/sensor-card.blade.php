@props(['title', 'value', 'id'])

<div class="bg-white p-4 rounded shadow text-center">
    <h4 class="font-bold mb-2">{{ $title }}</h4>
    <input type="text" value="{{ $value }}" id="{{ $id }}" />
</div>

