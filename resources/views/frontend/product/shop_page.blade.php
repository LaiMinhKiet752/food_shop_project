@extends('frontend.master_dashboard')
@section('main')
@section('title')
    Shop
@endsection
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
<div class="page-header mt-30 mb-50">
    <div class="container">
        <div class="archive-header">
            <div class="row align-items-center">
                <div class="col-xl-3">
                    <h1 class="mb-15"> Shop</h1>
                    <div class="breadcrumb">
                        <a href="{{ url('/') }}" rel="nofollow"><i class="fi-rs-home mr-5"></i>Trang chủ</a>
                        <span></span> Shop
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container mb-30">
    <div class="row flex-row-reverse">
        <div class="col-lg-4-5">
            <div class="shop-product-fillter">
                <div class="totall-product">
                    {{-- <p>We found <strong class="text-brand" style="font-weight: bold;">{{ count($count_products) }}</strong>
                        items for you!</p> --}}
                </div>
                <div class="sort-by-product-area">
                    <div class="sort-by-cover mr-10">
                        <div class="sort-by-product-wrap">
                            <div class="sort-by">
                                <span><i class="fi-rs-apps"></i>Hiển thị:</span>
                            </div>
                            <div class="sort-by-dropdown-wrap">
                                <span> 50 <i class="fi-rs-angle-small-down"></i></span>
                            </div>
                        </div>
                        <div class="sort-by-dropdown">
                            <ul>
                                <li><a class="active" href="#">50</a></li>
                                <li><a href="#">100</a></li>
                                <li><a href="#">150</a></li>
                                <li><a href="#">200</a></li>
                                <li><a href="#">Tất cả</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="sort-by-cover">
                        <div class="sort-by-product-wrap">
                            <div class="sort-by">
                                <span><i class="fi-rs-apps-sort"></i>Sắp xếp theo:</span>
                            </div>
                            <div class="sort-by-dropdown-wrap">
                                <span> Nổi bật <i class="fi-rs-angle-small-down"></i></span>
                            </div>
                        </div>
                        <div class="sort-by-dropdown">
                            <ul>
                                <li><a class="active" href="#">Nổi bật</a></li>
                                <li><a href="#">Giá: Từ Thấp Đến Cao</a></li>
                                <li><a href="#">Giá: Từ Cao Đến Thấp</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row product-grid">
                @foreach ($products as $product)
                    <div class="col-lg-1-5 col-md-4 col-12 col-sm-6">
                        <div class="product-cart-wrap mb-30 wow animate__animated animate__fadeIn shop_page_data"
                            data-wow-delay=".1s" style="height: 430px;">
                            <div class="product-img-action-wrap">
                                <div class="product-img product-img-zoom">
                                    <a
                                        href="{{ url('product/details/' . $product->id . '/' . $product->product_slug) }}">
                                        <img class="default-img" src="{{ asset($product->product_thumbnail) }}"
                                            alt="" />

                                    </a>
                                </div>
                                <div class="product-action-1">
                                    <a aria-label="Thêm vào yêu thích" class="action-btn" id="{{ $product->id }}"
                                        onclick="addToWishlist(this.id)"><i class="fi-rs-heart"></i></a>

                                    <a aria-label="Thêm vào so sánh" class="action-btn" id="{{ $product->id }}"
                                        onclick="addToCompare(this.id)"><i class="fi-rs-shuffle"></i></a>

                                    <a aria-label="Xem nhanh" class="action-btn" data-bs-toggle="modal"
                                        data-bs-target="#quickViewModal" id="{{ $product->id }}"
                                        onclick="productView(this.id)"><i class="fi-rs-eye"></i></a>
                                </div>

                                @php
                                    $amount = $product->selling_price - $product->discount_price;
                                    $discount = ($amount / $product->selling_price) * 100;

                                @endphp


                                <div class="product-badges product-badges-position product-badges-mrg">

                                    @if ($product->discount_price == null)
                                        <span class="new">Mới</span>
                                    @else
                                        <span class="hot"> - {{ round($discount) }} %</span>
                                    @endif


                                </div>
                            </div>
                            @php
                                $category = \App\Models\Category::where('id', $product->category_id)->first();
                            @endphp
                            <div class="product-content-wrap">
                                <div class="product-category">
                                    <a
                                        href="{{ url('product/category/' . $category->id . '/' . $category->category_slug) }}">{{ $product['category']['category_name'] }}</a>
                                </div>
                                <h2
                                    style="overflow: hidden;
                                line-height: 1.3em;
                                padding: 16px 0 0;
                                margin-bottom: 12px;
                                text-overflow: ellipsis;
                                white-space: initial;
                                display: -webkit-box;
                                -webkit-line-clamp: 2;
                                -webkit-box-orient: vertical;
                                min-height: 58px;
                                height: 30px;
                                box-sizing: border-box;">
                                    <a
                                        href="{{ url('product/details/' . $product->id . '/' . $product->product_slug) }}">
                                        {{ $product->product_name }} </a>
                                </h2>



                                @php
                                    $average = \App\Models\Review::where('product_id', $product->id)
                                        ->where('status', 1)
                                        ->avg('rating');
                                    $count_review = \App\Models\Review::where('product_id', $product->id)
                                        ->where('status', 1)
                                        ->count('rating');
                                @endphp
                                <div class="product-rate-cover">
                                    @if ($average == 0)
                                        <div class="product-rate d-inline-block">
                                            <div class="product-rating" style="width: 0%"></div>
                                        </div>
                                        <span class="font-small ml-5 text-muted">
                                            ({{ $count_review }} đánh giá)
                                        </span>
                                    @elseif($average == 1)
                                        <div class="product-rate d-inline-block">
                                            <div class="product-rating" style="width: 20%"></div>
                                        </div>
                                        <span class="font-small ml-5 text-muted">
                                            ({{ $count_review }} đánh giá)
                                        </span>
                                    @elseif($average > 1 && $average < 2)
                                        <div class="product-rate d-inline-block">
                                            <div class="product-rating" style="width: 30%"></div>
                                        </div>
                                        <span class="font-small ml-5 text-muted">
                                            ({{ $count_review }} đánh giá)
                                        </span>
                                    @elseif($average == 2)
                                        <div class="product-rate d-inline-block">
                                            <div class="product-rating" style="width: 40%"></div>
                                        </div>
                                        <span class="font-small ml-5 text-muted">
                                            ({{ $count_review }}
                                            đánh giá)
                                        </span>
                                    @elseif($average > 2 && $average < 3)
                                        <div class="product-rate d-inline-block">
                                            <div class="product-rating" style="width: 50%"></div>
                                        </div>
                                        <span class="font-small ml-5 text-muted">
                                            ({{ $count_review }} đánh giá)
                                        </span>
                                    @elseif($average == 3)
                                        <div class="product-rate d-inline-block">
                                            <div class="product-rating" style="width: 60%"></div>
                                        </div>
                                        <span class="font-small ml-5 text-muted">
                                            ({{ $count_review }} đánh giá)
                                        </span>
                                    @elseif($average > 3 && $average < 4)
                                        <div class="product-rate d-inline-block">
                                            <div class="product-rating" style="width: 70%"></div>
                                        </div>
                                        <span class="font-small ml-5 text-muted">
                                            ({{ $count_review }} đánh giá)
                                        </span>
                                    @elseif($average == 4)
                                        <div class="product-rate d-inline-block">
                                            <div class="product-rating" style="width: 80%"></div>
                                        </div>
                                        <span class="font-small ml-5 text-muted">
                                            ({{ $count_review }} đánh giá)
                                        </span>
                                    @elseif($average > 4 && $average < 5)
                                        <div class="product-rate d-inline-block">
                                            <div class="product-rating" style="width: 90%"></div>
                                        </div>
                                        <span class="font-small ml-5 text-muted">
                                            ({{ $count_review }} đánh giá)
                                        </span>
                                    @elseif($average == 5)
                                        <div class="product-rate d-inline-block">
                                            <div class="product-rating" style="width: 100%"></div>
                                        </div>
                                        <span class="font-small ml-5 text-muted">
                                            ({{ $count_review }} đánh giá)
                                        </span>
                                    @endif
                                </div>



                                <div>

                                </div>
                                <div class="product-card-bottom">
                                    @if ($product->discount_price == null)
                                        <div class="product-price">
                                            <span>{{ number_format($product->selling_price, 0, ',', '.') }}đ</span>
                                        </div>
                                    @else
                                        <div class="product-price">
                                            <span>{{ number_format($product->discount_price, 0, ',', '.') }}đ</span>
                                            <span
                                                class="old-price">{{ number_format($product->selling_price, 0, ',', '.') }}đ</span>
                                        </div>
                                    @endif

                                    <div class="add-cart">
                                        <input type="hidden" value="{{ $product->id }}" class="shop_page_prod_id">

                                        <input type="hidden" class="shop_page_pname"
                                            value="{{ $product->product_name }}">
                                        <input type="hidden" class="shop_page_brand_id"
                                            value="{{ $product->brand_id }}">
                                        <a class="add ShopPageAddToCart" type="submit"
                                            style="padding: 4px 15px 4px 15px; top: 20%;"><i
                                                class="fi-rs-shopping-cart mr-5"></i>Mua </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end product card-->
                @endforeach

            </div>
            <!--product grid-->
            {{-- <div class="pagination-area mt-20 mb-20">
                {{ $products->appends($_GET)->links('vendor.pagination.custom') }}
            </div> --}}

            <!--End Deals-->
        </div>

        <div class="col-lg-1-5 primary-sidebar sticky-sidebar">
            <!-- Filter By Price -->
            <div class="sidebar-widget price_range range mb-30">
                <form action="{{ route('shop.filter') }}" method="post">
                    @csrf


                    <div class="list-group">
                        <div class="list-group-item mb-10 mt-10">
                            @if (!empty($_GET['category']))
                                @php
                                    $filterCat = explode(',', $_GET['category']);
                                @endphp
                            @endif
                            <label class="fw-900"
                                style="font-family: Quicksand; font-size: 25px; margin-top: 10px;">DANH MỤC</label>
                            @foreach ($categories as $category)
                                @php
                                    $products = App\Models\Product::where('category_id', $category->id)
                                        ->where('status', 1)
                                        ->get();
                                @endphp

                                <div class="custome-checkbox" style="margin-top: 10px;">
                                    <input class="form-check-input" type="checkbox" name="category[]"
                                        id="exampleCategory{{ $category->id }}"
                                        value="{{ $category->category_slug }}"
                                        @if (!empty($filterCat) && in_array($category->category_slug, $filterCat)) checked @endif
                                        onchange="this.form.submit()" />
                                    <label class="form-check-label"
                                        for="exampleCategory{{ $category->id }}"><span>{{ $category->category_name }}
                                            ({{ count($products) }})
                                        </span></label>

                                </div>
                            @endforeach


                            @if (!empty($_GET['brand']))
                                @php
                                    $filterBrand = explode(',', $_GET['brand']);
                                @endphp
                            @endif
                            <label class="fw-900 mt-15"
                                style="font-family: Quicksand; font-size: 25px; margin-top: 10px;">THƯƠNG HIỆU</label>
                            @foreach ($brands as $brand)
                                @php
                                    $products = \App\Models\Product::where('brand_id', $brand->id)
                                        ->where('status', 1)
                                        ->get();
                                @endphp
                                <div class="custome-checkbox" style="margin-top: 10px;">
                                    <input class="form-check-input" type="checkbox" name="brand[]"
                                        id="exampleBrand{{ $brand->id }}" value="{{ $brand->brand_slug }}"
                                        @if (!empty($filterBrand) && in_array($brand->brand_slug, $filterBrand)) checked @endif
                                        onchange="this.form.submit()" />
                                    <label class="form-check-label"
                                        for="exampleBrand{{ $brand->id }}"><span>{{ $brand->brand_name }}
                                            ({{ count($products) }})
                                        </span></label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </form>
            </div>
            <!-- Product sidebar Widget -->
            <div class="sidebar-widget product-sidebar mb-30 p-30 bg-grey border-radius-10">
                <h5 class="section-title style-1 mb-30">Sản phẩm mới</h5>

                @foreach ($newProduct as $product)
                    <div class="single-post clearfix">
                        <div class="image">
                            <img src="{{ asset($product->product_thumbnail) }}" alt="#" />
                        </div>
                        <div class="content pt-10">
                            <p><a
                                    href="{{ url('product/details/' . $product->id . '/' . $product->product_slug) }}">{{ $product->product_name }}</a>
                            </p>

                            @if ($product->discount_price == null)
                                <p class="price mb-0 mt-5">{{ number_format($product->selling_price, 0, ',', '.') }}đ</p>
                            @else
                                <p class="price mb-0 mt-5">{{ number_format($product->discount_price, 0, ',', '.') }}đ</p>
                            @endif
                            @php
                                $average = \App\Models\Review::where('product_id', $product->id)
                                    ->where('status', 1)
                                    ->avg('rating');
                                $count_review = \App\Models\Review::where('product_id', $product->id)
                                    ->where('status', 1)
                                ->count('rating'); @endphp
                            <div class="product-rate-cover">
                                @if ($average == 0)
                                    <div class="product-rate d-inline-block">
                                        <div class="product-rating" style="width: 0%"></div>
                                    </div>
                                    <span class="font-small ml-5 text-muted">
                                        ({{ $count_review }})
                                    </span>
                                @elseif($average == 1)
                                    <div class="product-rate d-inline-block">
                                        <div class="product-rating" style="width: 20%"></div>
                                    </div>
                                    <span class="font-small ml-5 text-muted">
                                        ({{ $count_review }})
                                    </span>
                                @elseif($average > 1 && $average < 2)
                                    <div class="product-rate d-inline-block">
                                        <div class="product-rating" style="width: 30%"></div>
                                    </div>
                                    <span class="font-small ml-5 text-muted">
                                        ({{ $count_review }})
                                    </span>
                                @elseif($average == 2)
                                    <div class="product-rate d-inline-block">
                                        <div class="product-rating" style="width: 40%"></div>
                                    </div>
                                    <span class="font-small ml-5 text-muted">
                                        ({{ $count_review }})
                                    </span>
                                @elseif($average > 2 && $average < 3)
                                    <div class="product-rate d-inline-block">
                                        <div class="product-rating" style="width: 50%"></div>
                                    </div>
                                    <span class="font-small ml-5 text-muted">
                                        ({{ $count_review }})
                                    </span>
                                @elseif($average == 3)
                                    <div class="product-rate d-inline-block">
                                        <div class="product-rating" style="width: 60%"></div>
                                    </div>
                                    <span class="font-small ml-5 text-muted">
                                        ({{ $count_review }})
                                    </span>
                                @elseif($average > 3 && $average < 4)
                                    <div class="product-rate d-inline-block">
                                        <div class="product-rating" style="width: 70%"></div>
                                    </div>
                                    <span class="font-small ml-5 text-muted">
                                        ({{ $count_review }})
                                    </span>
                                @elseif($average == 4)
                                    <div class="product-rate d-inline-block">
                                        <div class="product-rating" style="width: 80%"></div>
                                    </div>
                                    <span class="font-small ml-5 text-muted">
                                        ({{ $count_review }})
                                    </span>
                                @elseif($average > 4 && $average < 5)
                                    <div class="product-rate d-inline-block">
                                        <div class="product-rating" style="width: 90%"></div>
                                    </div>
                                    <span class="font-small ml-5 text-muted">
                                        ({{ $count_review }})
                                    </span>
                                @elseif($average == 5)
                                    <div class="product-rate d-inline-block">
                                        <div class="product-rating" style="width: 100%"></div>
                                    </div>
                                    <span class="font-small ml-5 text-muted">
                                        ({{ $count_review }})
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>

    </div>
</div>


{{-- Start Category Product Add To Cart --}}
<script type="text/javascript">
    $(document).ready(function() {
        $('.ShopPageAddToCart').click(function(e) {
            e.preventDefault();
            var id = $(this).closest('.shop_page_data').find('.shop_page_prod_id').val();
            var product_name = $(this).closest('.shop_page_data').find(
                '.shop_page_pname').val();
            var brand_id = $(this).closest('.shop_page_data').find(
                '.shop_page_brand_id').val();
            var quantity = 1;
            $.ajax({
                type: "POST",
                url: "/shop/page/product/cart/store/" + id,
                data: {
                    quantity: quantity,
                    product_name: product_name,
                    brand_id: brand_id,
                },
                dataType: "json",
                success: function(data) {
                    if (data.error_quantity) {
                        // Start Message
                        const Toast = Swal.mixin({
                            position: 'center',
                            title: 'Sorry!',
                            timerProgressBar: true,
                            showConfirmButton: true,
                            timer: 3000,
                            confirmButtonText: "OK",
                            confirmButtonColor: '#3BB77E',
                        })
                        Toast.fire({
                            icon: 'error',
                            text: data.error_quantity,
                        })
                        // End Message
                    } else {
                        miniCart();
                        // Start Message
                        const Toast = Swal.mixin({
                            position: 'top-end',
                            toast: true,
                            showConfirmButton: false,
                            timer: 3000,
                        })
                        if ($.isEmptyObject(data.error)) {
                            Toast.fire({
                                icon: 'success',
                                title: data.success,
                            })
                        } else {
                            Toast.fire({
                                icon: 'error',
                                title: data.error,
                            })
                        }
                        // End Message
                    }
                }
            });
        });
    });
</script>
{{-- Start Category Product Add To Cart --}}
@endsection
