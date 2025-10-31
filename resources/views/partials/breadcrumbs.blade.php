@if (count($breadcrumbs))
    <ol class="breadcrumb" itemscope itemtype="http://schema.org/BreadcrumbList">
        <?php $k = 1; ?>
        @foreach ($breadcrumbs as $breadcrumb)
            @if ($breadcrumb->url && !$loop->last)
                <li class="breadcrumb-item" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
                    <a href="{{ $breadcrumb->url }}" itemprop="item" itemtype="http://schema.org/Thing">
                        <span itemprop="name">{{ $breadcrumb->title }}</span>
                    </a>
                    <meta itemprop="position" content="{{ $k }}">
                </li>
            @else
                <li class="breadcrumb-item active" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
                    <a href="{{ $breadcrumb->url }}" itemprop="item" itemtype="http://schema.org/Thing">
                        <span itemprop="name">{{ $breadcrumb->title }}</span>
                    </a>
                    <meta itemprop="position" content="{{ $k }}">
                </li>
            @endif
            <?php $k++; ?>
        @endforeach
    </ol>
@endif
