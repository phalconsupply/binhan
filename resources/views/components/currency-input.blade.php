@props(['name', 'value' => '', 'placeholder' => 'Nhập số tiền', 'required' => false, 'min' => 0])

<input 
    type="text" 
    name="{{ $name }}" 
    value="{{ old($name, $value) }}" 
    data-currency
    placeholder="{{ $placeholder }}"
    {{ $required ? 'required' : '' }}
    {{ $attributes->merge(['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500']) }}
/>
