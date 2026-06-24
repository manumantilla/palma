<span
    {{ $attributes->merge(['class' => 'inline-block']) }}
>
    {!! file_get_contents(resource_path('manuel.svg')) !!}
</span>

<style>
span svg {
    width: 40px;
    height: auto;
}
</style>