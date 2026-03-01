@if(count($itemsBreadcrumbs)>0)
<nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0 pt-0 px-0" itemscope="" itemtype="https://schema.org/BreadcrumbList">
        <li class="breadcrumb-item" aria-current="page" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
            <a itemscope itemtype="https://schema.org/Thing" itemid="/" itemprop="item" href="{{url("/")}}">
                <span itemprop="name">Trang chủ</span>
            </a>
            <meta itemprop="position" content="0">
        </li>
        @php
            $position = 0;
        @endphp
        @foreach($itemsBreadcrumbs as $val)
        @php
           $name = $val->name;
           $link = url($val->url->path);
        @endphp
        <li class="breadcrumb-item" aria-current="page" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
            <a itemscope itemtype="https://schema.org/Thing" itemid="{{$link}}" itemprop="item" href="{{$link}}">
                <span itemprop="name">{{$name}}</span>
            </a>
            <meta itemprop="position" content="{{++$position;}}">
        </li>
       @endforeach
    </ol>
</nav>
@endif
