@props(['seo'])
<title>{{ $seo->title }}</title>
@if ($seo->description)
    <meta name="description" content="{{ $seo->description }}">
@endif
<link rel="canonical" href="{{ $seo->url }}">

<meta property="og:type" content="{{ $seo->type }}">
<meta property="og:site_name" content="{{ $seo->siteName }}">
<meta property="og:title" content="{{ $seo->title }}">
@if ($seo->description)
    <meta property="og:description" content="{{ $seo->description }}">
@endif
<meta property="og:url" content="{{ $seo->url }}">
<meta property="og:locale" content="{{ str_replace('-', '_', app()->getLocale() === 'en' ? 'en_US' : app()->getLocale()) }}">
@if ($seo->image)
    <meta property="og:image" content="{{ $seo->image }}">
    <meta property="og:image:alt" content="{{ $seo->imageAlt }}">
@endif

<meta name="twitter:card" content="{{ $seo->image ? 'summary_large_image' : 'summary' }}">
<meta name="twitter:title" content="{{ $seo->title }}">
@if ($seo->description)
    <meta name="twitter:description" content="{{ $seo->description }}">
@endif
@if ($seo->image)
    <meta name="twitter:image" content="{{ $seo->image }}">
    <meta name="twitter:image:alt" content="{{ $seo->imageAlt }}">
@endif

@foreach ($seo->structuredData as $data)
    <script type="application/ld+json">{!! \App\Support\Seo\StructuredData::toJson($data) !!}</script>
@endforeach
