@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'bg-gray-100 border-gray-200 text-gray-800 focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-20 rounded-md shadow-sm']) !!}>
