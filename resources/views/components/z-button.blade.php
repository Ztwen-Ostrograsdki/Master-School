@props(['classes' => 'border border-white w-50', 'bg' => 'btn-info' ])
<div class="p-0 m-0 w-100 mx-auto d-flex justify-content-center mt-2 pb-1 pt-1">
    <button class="btn {{$classes}} {{$bg}}"  type="submit">{{$slot}}</button>
</div>