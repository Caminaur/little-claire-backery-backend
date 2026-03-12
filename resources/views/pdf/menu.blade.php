<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Menú - {{ $data['name'] }}</title>

<style>

*{
    box-sizing:border-box;
}

body{
    margin:0;
    padding:40px 0;
    background:#ece7df;
    font-family: DejaVu Sans, sans-serif;
    color:#2e2a26;
    font-size:12px;
    line-height:1.5;
}

.page{
    width:100%;
    max-width:780px;
    margin:0 auto;
    background:white;
    padding:48px 50px;
    border:1px solid #ddd3c7;
    box-shadow:0 6px 25px rgba(0,0,0,0.12);
}

.menu-header{
    text-align:center;
    margin-bottom:42px;
}

.menu-title{
    margin:0;
    font-size:30px;
    letter-spacing:1px;
    text-transform:uppercase;
    color:#3e2c1e;
}

.menu-divider{
    width:80px;
    height:2px;
    background:#c7a77a;
    margin:16px auto;
}

.menu-description{
    font-size:12px;
    color:#6f665e;
    max-width:520px;
    margin:0 auto;
}

.category-section{
    margin-bottom:34px;
    page-break-inside:avoid;
}

.category-header{
    border-bottom:1px solid #e4ddd4;
    padding-bottom:8px;
    margin-bottom:16px;
}

.category-name{
    margin:0;
    font-size:20px;
    color:#4b3526;
}

.category-description{
    margin:4px 0 0 0;
    font-size:11px;
    color:#7a726a;
}

.product-item{
    padding:12px 0;
    border-bottom:1px dashed #ddd4ca;
}

.product-item:last-child{
    border-bottom:none;
}

.product-row{
    display:table;
    width:100%;
    table-layout:fixed;
}

.product-image{
    display:table-cell;
    width:90px;
    padding-right:12px;
    vertical-align:top;
}

.product-image img{
    width:80px;
    height:55px;
    object-fit:cover;
    border-radius:4px;
    border:1px solid #ddd;
}

.product-main{
    display:table-cell;
    vertical-align:top;
}

.product-price-box{
    display:table-cell;
    width:90px;
    text-align:right;
    vertical-align:top;
}

.product-name{
    font-size:14px;
    font-weight:bold;
    margin:0 0 4px;
    color:#2b241f;
}

.product-description{
    font-size:11px;
    margin:0;
    color:#6f6861;
}

.product-price{
    font-size:14px;
    font-weight:bold;
    color:#4b3526;
}

.empty-products{
    font-size:11px;
    font-style:italic;
    color:#8a8179;
}

.footer{
    margin-top:40px;
    padding-top:12px;
    border-top:1px solid #eee6dc;
    text-align:center;
    font-size:10px;
    color:#9a9188;
}

@page{
    margin:24px;
}

</style>
</head>

<body>

<div class="page">

<header class="menu-header">

<h1 class="menu-title">Menu - {{ $data['name'] }}</h1>

<div class="menu-divider"></div>

@if(!empty($data['description']))
<p class="menu-description">
{{ $data['description'] }}
</p>
@endif

</header>


@foreach ($data['categories'] as $category)

<section class="category-section">

<div class="category-header">

<h2 class="category-name">
{{ $category['name'] }}
</h2>

@if(!empty($category['description']))
<p class="category-description">
{{ $category['description'] }}
</p>
@endif

</div>


@if(!empty($category['products']) && count($category['products']) > 0)

@foreach ($category['products'] as $product)

<article class="product-item">

<div class="product-row">

@if(!empty($product['images']) && count($product['images']) > 0)

<div class="product-image">
<img src="{{ $product['images'][0]['url'] }}" alt="{{ $product['name'] }}">
</div>

@endif


<div class="product-main">

<h3 class="product-name">
{{ $product['name'] }}
</h3>

@if(!empty($product['description']))
<p class="product-description">
{{ $product['description'] }}
</p>
@endif

</div>


<div class="product-price-box">

<div class="product-price">
${{ number_format((float)$product['final_price'], 2, ',', '.') }}
</div>

</div>

</div>

</article>

@endforeach

@else

<p class="empty-products">
No hay productos disponibles en esta categoría.
</p>

@endif

</section>

@endforeach


<footer class="footer">
Carta generada automáticamente
</footer>

</div>

</body>
</html>