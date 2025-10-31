<?php

// Home
Breadcrumbs::register(
    'index',
    function ($breadcrumbs) {
        $breadcrumbs->push('Trang chủ', route('index'));
    }
);
// Home > Tin tuc
Breadcrumbs::register(
    'tintuc',
    function ($breadcrumbs) {
        $breadcrumbs->parent('index');
        $breadcrumbs->push('Tin tức', route('tin-tuc'));
    }
);

// Home > Page
Breadcrumbs::register(
    'page.index',
    function ($breadcrumbs, $pages) {
        $breadcrumbs->parent('index');
        $breadcrumbs->push($pages->title, route('story.detail', $pages->slug));
    }
);

// Home > Page Comics
Breadcrumbs::register(
    'page.comics',
    function ($breadcrumbs) {
        $breadcrumbs->parent('index');
        $breadcrumbs->push('Truyện tranh mới', route('pageStory'));
    }
);

// Home > Page Hot Comics
Breadcrumbs::register(
    'page.hotComics',
    function ($breadcrumbs) {
        $breadcrumbs->parent('index');
        $breadcrumbs->push('Truyện Hot', route('hotStory'));
    }
);

// Home > Truyện Full
Breadcrumbs::register(
    'completedStory',
    function ($breadcrumbs) {
        $breadcrumbs->parent('index');
        $breadcrumbs->push('Truyện Full', route('completedStory'));
    }
);

// Home > Truyện Full
Breadcrumbs::register(
    'search',
    function ($breadcrumbs, $name, $search_string) {
        $breadcrumbs->parent('index');
        $breadcrumbs->push($name, route('search', ['search_string' => $search_string]));
    }
);

// Home > tin tuc> the loai
Breadcrumbs::register(
    'tintuc.category',
    function ($breadcrumbs, $name, $slug) {
        $breadcrumbs->parent('index');
        $breadcrumbs->push($name, route('story.detail', $slug));
    }
);

//Translation Team
Breadcrumbs::register(
    'translateTeam',
    function ($breadcrumbs) {
        $breadcrumbs->parent('index');
        $breadcrumbs->push('Nhóm dịch', route('translateTeam.index'));
    }
);

//translation Team Comics
Breadcrumbs::register(
    'translateTeam.detail',
    function ($breadcrumbs, $author) {
        $breadcrumbs->parent('index');
        $breadcrumbs->push('Nhóm dịch: ' . $author->name, route('translationTeam.detail', $author->id));
    }
);

//User Register
Breadcrumbs::register(
    'user.register',
    function ($breadcrumbs) {
        $breadcrumbs->parent('index');
        $breadcrumbs->push('Đăng ký thành viên', route('registerUser'));
    }
);

// Home > tin tuc> details
Breadcrumbs::register(
    'chapter.detail',
    function ($breadcrumbs, $categoryTitle, $categorySlug, $title, $slug) {
        $breadcrumbs->parent('index');
        $breadcrumbs->push($categoryTitle, route('story.detail', $categorySlug));
        $breadcrumbs->push(
            $title,
            route('chapter.detail', array($categorySlug, $slug))
        );
    }
);

// Home > page
Breadcrumbs::register(
    'default.page',
    function ($breadcrumbs, $data_customers) {
        $breadcrumbs->parent('index');
        //$breadcrumbs->push('Tin tức', route('tin-tuc'));
        $breadcrumbs->push($data_customers->title, route('default.page', $data_customers->slug));
    }
);
?>
