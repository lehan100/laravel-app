<nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0 pt-0 px-0" itemscope="" itemtype="https://schema.org/BreadcrumbList">
        <li class="breadcrumb-item" aria-current="page" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
            <a itemscope itemtype="https://schema.org/Thing" itemid="/" itemprop="item" href="{{url("/")}}">
                <span itemprop="name">Trang chủ</span>
            </a>
            <meta itemprop="position" content="0">
        </li>
        <li class="breadcrumb-item" aria-current="page" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
            <a itemscope itemtype="https://schema.org/Thing" itemid="{{url()->full()}}" itemprop="item" href="{{url()->full()}}">
                <span itemprop="name">Kết quả tìm kiếm: {{$keyword}}</span>
            </a>
            <meta itemprop="position" content="1">
        </li>
    </ol>
</nav>
