<div class="card h-100 border-0 shadow-sm product-card" style="transition: all 0.3s ease; border-radius: 14px; overflow: hidden; background: #ffffff;">
    <a href="{{ route('product.show', $product->product_id) }}" class="text-decoration-none">
        <div class="card-img-wrapper position-relative overflow-hidden" style="height: 190px; background: #ffffff; cursor: pointer; border: 1px solid #f0f0f0;">
            <img src="{{ $product->image_url }}" class="card-img-top w-100 h-100" alt="{{ $product->product_name }}" style="object-fit: contain; padding: 10px; transition: transform 0.3s ease;" onerror="this.src='{{ asset('images/placeholder.png') }}';">
            <div class="overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" style="background: rgba(0, 0, 0, 0); transition: background 0.3s ease; pointer-events: none;">
                <span class="badge bg-dark text-white px-3 py-2" style="opacity: 0; transition: opacity 0.3s ease;">
                    <i class="fas fa-eye me-1"></i> View Details
                </span>
            </div>
        </div>
    </a>
    <div class="card-body d-flex flex-column p-3 p-lg-4" style="background: #ffffff;">
        <a href="{{ route('product.show', $product->product_id) }}" class="text-decoration-none">
            <h5 class="card-title mb-2" style="font-weight: 700; font-size: 0.95rem; color: #111827; line-height: 1.2;">
                {{ $product->product_name }}
            </h5>
        </a>
        @php($priceDisplay = $product->has_variants ? (data_get($product->price_range, 'display') ?: ('₱' . number_format($product->price, 0))) : ('₱' . number_format($product->price, 0)))
        <p class="card-text mb-2" style="font-size: 1.05rem; font-weight: 900; color: #111827;">{{ $priceDisplay }}</p>

        <div class="mb-3" style="margin-top: -8px;">
            @php($state = $product->stock_state)
            <span style="font-size: 0.9rem; color: #6c757d;">
                @if($product->has_variants)
                    Multiple options
                @elseif($state === 'unlimited')
                    In stock
                @elseif($state === 'out_of_stock')
                    Out of stock
                @elseif($state === 'low_stock')
                    <span style="color:#5f6368; font-weight: 600;">Only {{ (int)($product->effective_stock ?? 0) }} left</span>
                @else
                    In stock ({{ (int)($product->effective_stock ?? 0) }})
                @endif
            </span>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-auto product-actions">
            @if($product->category)
                <span class="badge" style="background: linear-gradient(135deg, #06131a 0%, #1a3a52 100%); font-size: 0.75rem; padding: 4px 8px;">{{ $product->category }}</span>
            @else
                <span></span>
            @endif
            <div class="d-flex gap-2 product-actions-buttons">
                <button class="btn btn-outline-warning add-to-cart-btn"
                        data-product-id="{{ $product->product_id }}"
                        data-has-variants="{{ $product->has_variants ? 1 : 0 }}"
                        style="border-radius: 10px; padding: 9px 12px; border: 2px solid #ffc107; transition: all 0.3s ease;"
                        {{ $product->is_out_of_stock ? 'disabled' : '' }}>
                    <i class="fas fa-shopping-cart"></i>
                </button>
                <button class="btn btn-warning buy-now-btn"
                        data-product-id="{{ $product->product_id }}"
                        data-has-variants="{{ $product->has_variants ? 1 : 0 }}"
                        style="border-radius: 10px; font-weight: 800; padding: 9px 14px; background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%); border: none; transition: all 0.3s ease;"
                        {{ $product->is_out_of_stock ? 'disabled' : '' }}>
                    <i class="fas fa-bolt me-1"></i>{{ $product->has_variants ? 'Select Options' : 'Buy Now' }}
                </button>
            </div>
        </div>
    </div>
</div>
