<div>
    <h6>
        <span class="text-white-50 @if($useIcon) d-none @endif ">{{$title}}</span> 
        <span class="text-white-50 @if($icon) {{$icon}} @endif "></span> 
        <span class="text-white-50 @if(!$useIcon) d-none @endif ">: </span> 
        <span> 
            {{ $value }} 
            <small class="@if(!$smallTitle) d-none @endif text-white-50 "> {{ $smallTitle }} </small>
        </span>
    </h6>
</div>