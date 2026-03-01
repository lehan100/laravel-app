    @if (count($menuSiteBar) > 0)
        <div class="block-category mb-2">
            <ul class="nav">
                @foreach ($menuSiteBar as $item)
                @php
                    $name = $item->name;
                    $link = url($item->url['path']);
                @endphp
                <li @class(['nav-item mb-2', 'active' => $item->id == $category_id])>
                   <a class="nav-link border rounded me-2" href="{{$link}}">{{$name}}</a>
                </li>    
                @endforeach
            </ul>
        </div>
    @endif
