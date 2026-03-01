<div class="menu-search" itemscope="" itemtype="https://schema.org/WebSite">
	<meta itemprop="url" content="https://ukimua.com">
    <form method="get" action="{{route("product/search")}}" itemprop="potentialAction" itemscope="" itemtype="https://schema.org/SearchAction">
		<meta itemprop="target" content="https://ukimua.com/product/search/{keyword}">
        <input name="keyword" type="text" value="" class="keySearch input-search form-control" itemprop="query-input">
        <input type="hidden" value="{{csrf_token()}}" id="tokenSearch"/>
        <button type="submit" aria-label="Button Search" class="btn-search bi bi-search"></button>
    </form>
    <div class="search-result" id="search-result"></div>
</div>