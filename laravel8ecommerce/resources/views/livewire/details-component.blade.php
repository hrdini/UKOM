<main id="main" class="main-site">
	<style>
		.regprice{
			font-weight: 300px;
			font-size: 13px !important;
			color: #aaaaaa !important;
			text-decoration: line-through;
			padding-left: 10px;
		}
	</style>
		<div class="container">

			<div class="wrap-breadcrumb">
				<ul>
					<li class="item-link"><a href="/" class="link">home</a></li>
					<li class="item-link"><span>detail</span></li>
				</ul>
			</div>
			<div class="row">

				<div class="col-lg-9 col-md-8 col-sm-8 col-xs-12 main-content-area">
					<div class="wrap-product-detail">
						<div class="detail-media">
							<div class="product-gallery" wire:ignore>
							<ul class="slides">
								<li data-thumb="{{asset('assets/images/products') }}/{{$product->image}}">
								<img src="{{ asset('assets/images/products') }}/{{$product->image}}" alt="{{$product->name}}" />
								</li>
								@php
								$images = explode(",",$product->images);
								@endphp
								@foreach($images as $image)
								@if($image)
								<li data-thumb="{{asset('assets/images/products') }}/{{$image}}">
								<img src="{{ asset('assets/images/products') }}/{{$image}}" alt="{{$product->name}}" />
								</li>
								@endif
								@endforeach
							</ul>
							</div>
						</div>
						<div class="detail-info">
							<div class="product-rating">
								<style>
									.color-gray{
										color:#e6e6e6 !important;
									}
								</style>
								
								@php
									$avgrating = 0;
								@endphp
								@foreach($product->orderItems->where('rstatus',1) as $orderItem)
									@php
									$avgrating = $avgrating + $orderItem->review->rating;
									@endphp
								@endforeach
								@for($i=1;$i<=5;$i++)
									@if($i<=$avgrating)
									<i class="fa fa-star" aria-hidden="true"></i>
									@else
									<i class="fa fa-star color-gray" aria-hidden="true"></i>
									@endif
								@endfor
                                <a href="#" class="count-review">({{$product->orderItems->where('rstatus',1)->count()}} review)</a>
                            </div>
                            <h2 class="product-name">{{$product->name}}</h2>
                            <div class="short-desc">
							{!!$product->short_description!!}
                            </div>
                            <div class="wrap-social">
                        <a class="link-socail" href="#"><img src="{{ asset('assets/images/social-list.png') }}" alt=""></a>
                            </div>
							@if($product->sale_price > 0 && $sale->status == 1 && $sale->sale_date > Carbon\Carbon::now())
                            <div class="wrap-price">
								<span class="product-price">Rp{{$product->sale_price}}</span>
								<del><span class="product-price regprice">Rp{{$product->regular_price}}</span></del>
							</div>
							@else
							<div class="wrap-price"><span class="product-price">Rp{{$product->regular_price}}</span></div>
							@endif
                            <div class="stock-info in-stock">
                                <p class="availability">Availability: <b>{{$product->stock_status}}</b></p>
                            </div>

							<div>
                            @foreach($product->attributeValue->unique('product_attribute_id') as $av)
                                <div class="row" style="margin-top: 2opx">
                                    <div class="col-xs-2">
                                        <p>{{$av->productAttribute->name}}</p>
                                    </div>

                                    <div class="col-xs-10">
                                        <select width="200px" class="form-control" wire:model="satt.{{$av->productAttribute->name}}">
                                            @foreach ($av->productAttribute->attributeValues->where('product_id', $product->id) as $pav)
                                                <option value="{{$pav->value}}">{{$pav->value}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                            <div class="quantity" style="margin-top:10px;">
                            <span>Quantity:</span>
								<div class="quantity-input">
									<input type="text" name="product-quatity" value="1" data-max="120" pattern="[0-9]*" wire:model="qty" >
									<a class="btn btn-reduce" href="#" wire:click.prevent="decreaseQuantity"></a>
									<a class="btn btn-increase" href="#" wire:click.prevent="increaseQuantity"></a>
								</div>
							</div>
							<div class="wrap-butons">
							@if($product->sale_price > 0 && $sale->status == 1 && $sale->sale_date > Carbon\Carbon::now())
							<a href="#" class="btn add-to-cart" wire:click.prevent="store({{$product->id}},'{{$product->name}}',{{$product->sale_price}})">Add to Cart</a>
							@else
								<a href="#" class="btn add-to-cart" wire:click.prevent="store({{$product->id}},'{{$product->name}}',{{$product->regular_price}})">Add to Cart</a>
								@endif
                                <div class="wrap-btn">
                                    <a href="#" class="btn btn-compare">Add Compare</a>
                                    <a href="#" class="btn btn-wishlist">Add Wishlist</a>
                                </div>
							</div>
						</div>
						<div class="advance-info">
							<div class="tab-control normal">
								<a href="#description" class="tab-control-item active">description</a>
								<a href="#review" class="tab-control-item">Reviews</a>
							</div>
							<div class="tab-contents">
								<div class="tab-content-item active" id="description">
								{!!$product->description!!}
								</div>
								<div class="tab-content-item " id="add_infomation">
									<table class="shop_attributes">
										<tbody>
											
										</tbody>
									</table>
								</div>
								<div class="tab-content-item " id="review">
									
									<div class="wrap-review-form">
										<style>
											.width-0-percent{
												width:0%;
											}
											.width-20-percent{
												width:20%;
											}
											.width-40-percent{
												width:40%;
											}
											.width-60-percent{
												width:60%;
											}
											.width-80-percent{
												width:80%;
											}
											.width-100-percent{
												width:100%;
											}
										</style>
										<div id="comments">
											<h2 class="woocommerce-Reviews-title">{{$product->orderItems->where('rstatus',1)->count()}} review for <span>{{$product->name}}</span></h2>
											<ol class="commentlist">
												@foreach($product->orderItems->where('rstatus',1) as $orderItem)
												<li class="comment byuser comment-author-admin bypostauthor even thread-even depth-1" id="li-comment-20">
													<div id="comment-20" class="comment_container"> 
														<img alt="{{$orderItem->order->user->name}}" src="{{ asset('assets/images/profile') }}/{{$orderItem->order->user->profile->image}}" height="80" width="80">
														<div class="comment-text">
															<div class="star-rating">
																<span class="width-{{ $orderItem->review->rating * 20 }}-percent">Rated <strong class="rating">{{$orderItem->review->rating}}</strong> out of 5</span>
															</div>
															<p class="meta"> 
																<strong class="woocommerce-review__author">{{$orderItem->order->user->name}}</strong> 
																<span class="woocommerce-review__dash">–</span>
																<time class="woocommerce-review__published-date" datetime="2008-02-14 20:00" >{{Carbon\Carbon::parse($orderItem->review->created_at)->format('d F Y g:i A')}}</time>
															</p>
															<div class="description">
																<p>{{$orderItem->review->comment}}</p>
															</div>
														</div>
													</div>
												</li>
												@endforeach
											</ol>
										</div><!-- #comments -->
									</div>
								</div>
							</div>
						</div>
					</div>
				</div><!--end main products area-->

				<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12 sitebar">
					<div class="widget widget-our-services ">
						<div class="widget-content">
							<ul class="our-services">

								<li class="service">
									<a class="link-to-service" href="#">
										<i class="fa fa-truck" aria-hidden="true"></i>
										<div class="right-content">
											<b class="title">Free Shipping</b>
											<span class="subtitle">On Order Over Rp99</span>
											<p class="desc">Get free shipping only add menu over Rp99</p>
										</div>
									</a>
								</li>

								<li class="service">
									<a class="link-to-service" href="#">
										<i class="fa fa-gift" aria-hidden="true"></i>
										<div class="right-content">
											<b class="title">Special Offer</b>
											<span class="subtitle">Get a gift!</span>
											<p class="desc">Special for u</p>
										</div>
									</a>
								</li>

							</ul>
						</div>
					</div><!-- Categories widget-->

					<div class="widget mercado-widget widget-product">
						<h2 class="widget-title">Popular Product</h2>
						<div class="widget-content">
							<ul class="products">
								@foreach ($popular_products as $p_product)
								<li class="product-item">
									<div class="product product-widget-style">
										<div class="thumbnnail">
											<a href="{{route('product.details',['slug'=>$p_product->slug])}}" title="{{$p_product->name}}">
												<figure><img src="{{ asset('assets/images/products') }}/{{$p_product->image}}" alt=""></figure>
											</a>
										</div>
										<div class="product-info">
											<a href="{{route('product.details',['slug'=>$p_product->slug])}}" title="{{$p_product->name}}" class="product-name"><span>{{$p_product->name}}</span></a>
											<div class="wrap-price"><span class="product-price">Rp{{$p_product->regular_price}}</span></div>
										</div>
									</div>
								</li>
								@endforeach
								</ul>
						</div>
					</div>

				</div><!--end sitebar-->

				

			</div><!--end row-->

		</div><!--end container-->

	</main>