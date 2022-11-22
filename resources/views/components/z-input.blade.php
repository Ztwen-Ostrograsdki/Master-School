@php
    $inputId = Str::random(15);
    if($hasLabel){
        $placeholder_text = "Veuillez renseigner " . strtolower($labelTitle) . '...';
    }
    if($placeholder){
        $placeholder_text = ucfirst($placeholder) . '...';
    }
@endphp

<div class="p-0 m-0 mt-0 mb-2 row {{$width}}">
    <label class="z-text-cyan m-0 {{$hideLabel}} p-0 w-100 cursor-pointer" for="{{$inputId}}">{{$labelTitle}}</label>
    <input autofocus placeholder="{{$placeholder_text}}" class="text-white form-control bg-transparent border border-white px-2 @error('{{$modelName}}') text-danger border-danger @enderror" wire:model.defer="{{$modelName}}" type="{{$type}}" name="{{$modelName}}" id="{{$inputId}}">
    @if($error)
        <small class="py-1 z-text-orange">{{$error}}</small>
    @endif
</div>