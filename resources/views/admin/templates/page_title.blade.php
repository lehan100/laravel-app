@php
    use App\Helpers\Template as Template;
    $id = @isset($item['id']) ? $item['id'] : 0;
    $buttonAction = Template::showButtomMain($controllerName,$buttomGroup,$id);
@endphp
<div class="page-title row mb-3">
    <div class="title_left col">
        <h3 class="text-uppercase">{{ ucfirst($title) }}</h3>
    </div>
    <div class="title_right col-auto">
        {!!$buttonAction!!}
    </div>
</div>
