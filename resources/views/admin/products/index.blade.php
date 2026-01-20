@extends('admin.layout')

@section('title', 'Products')

@section('content')
    <div class="admin-header">
        <h1>Products Management</h1>
        <a href="{{ route('admin.products.create') }}" class="btn btn-admin btn-admin-primary">
            <i class="fas fa-plus me-2"></i>Add New Product
        </a>
    </div>

    <div class="admin-card">
        <div class="p-4" style="padding-bottom: 0 !important;">
            <form method="GET" action="{{ route('admin.products.index') }}" class="d-flex flex-wrap gap-2 align-items-center">
                @if(!empty($selectedCategory))
                    <input type="hidden" name="category" value="{{ $selectedCategory }}">
                @endif

                @if(!empty($onlyFeatured))
                    <input type="hidden" name="featured" value="1">
                @endif

                <div class="input-group" style="max-width: 520px;">
                    <span class="input-group-text" style="border-radius: 12px 0 0 12px; border: 1px solid rgba(31,45,58,0.12); background: #f6f8fb;">
                        <i class="fas fa-search"></i>
                    </span>
                    <input
                        type="search"
                        name="q"
                        value="{{ request('q') }}"
                        class="form-control"
                        placeholder="Search products (name, description, or ID)"
                        style="border-radius: 0 12px 12px 0; border: 1px solid rgba(31,45,58,0.12);"
                    >
                </div>

                <button type="submit" class="btn btn-sm btn-admin btn-admin-primary" style="border-radius: 12px; font-weight: 800; padding: 10px 14px;">
                    Search
                </button>

                @if(request()->has('q') || request()->has('category'))
                    <a href="{{ route('admin.products.index') }}" class="btn btn-sm" style="border-radius: 12px; font-weight: 800; padding: 10px 14px; background: rgba(127,140,141,0.12); color:#2c3e50;">
                        Clear
                    </a>
                @endif
            </form>
        </div>

        @if(isset($categories) && $categories->count())
            <div class="p-4" style="padding-bottom: 0 !important;">
                <div class="d-flex flex-wrap gap-2">
                    @php($featuredActive = request()->boolean('featured'))

                    <a
                        href="{{ route('admin.products.index', array_filter(['q' => request('q'), 'category' => request('category'), 'featured' => $featuredActive ? null : 1])) }}"
                        class="btn btn-sm"
                        style="border-radius: 999px; font-weight: 800; padding: 8px 14px; border: 1px solid rgba(255, 193, 7, 0.35); {{ $featuredActive ? 'background:#ffc107;color:#1f2d3a;' : 'background:rgba(255, 193, 7, 0.10);color:#9a6a00;' }}"
                    >Featured</a>

                    <a
                        href="{{ route('admin.products.index', array_filter(['q' => request('q'), 'featured' => request('featured')])) }}"
                        class="btn btn-sm"
                        style="border-radius: 999px; font-weight: 800; padding: 8px 14px; border: 1px solid rgba(21,101,192,0.25); {{ empty($selectedCategory) ? 'background:#1565c0;color:#fff;' : 'background:rgba(21,101,192,0.08);color:#1565c0;' }}"
                    >All</a>

                    @foreach($categories as $cat)
                        <a
                            href="{{ route('admin.products.index', array_filter(['category' => $cat, 'q' => request('q'), 'featured' => request('featured')])) }}"
                            class="btn btn-sm"
                            style="border-radius: 999px; font-weight: 800; padding: 8px 14px; border: 1px solid rgba(21,101,192,0.25); {{ ($selectedCategory ?? '') === $cat ? 'background:#1565c0;color:#fff;' : 'background:rgba(21,101,192,0.08);color:#1565c0;' }}"
                        >{{ $cat }}</a>
                    @endforeach
                </div>
            </div>
        @endif

        @if($products->hasPages())
            <div class="p-4" style="padding-bottom: 0 !important;">
                {{ $products->links() }}
            </div>
        @endif

        <div class="table-responsive">
            <table class="table admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Featured</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Image</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td>#{{ $product->product_id }}</td>
                            <td>
                                <strong>{{ $product->product_name }}</strong>
                                <br>
                                <small style="color: #7f8c8d;">{{ Str::limit($product->description, 50) }}</small>
                            </td>
                            <td>
                                @if($product->is_featured)
                                    <span class="badge" style="background: rgba(255, 193, 7, 0.18); color: #9a6a00; border: 1px solid rgba(255, 193, 7, 0.35);">Yes</span>
                                @else
                                    <span class="badge" style="background: rgba(148, 163, 184, 0.18); color: #1f2d3a; border: 1px solid rgba(148, 163, 184, 0.35);">No</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge" style="background: #1565c0; color: white;">{{ $product->category ?? 'N/A' }}</span>
                            </td>
                            <td><strong>₱{{ number_format($product->price, 2) }}</strong></td>
                            <td>
                                @if($product->has_variants)
                                    <span class="badge" style="background: rgba(21,101,192,0.12); color: #1565c0; border: 1px solid rgba(21,101,192,0.25);">Variants</span>
                                @endif

                                @if($product->effective_stock_unlimited)
                                    <span class="badge" style="background: #2c3e50; color: white;">Unlimited</span>
                                @else
                                    @php($qty = (int)($product->effective_stock ?? 0))
                                    @if($qty <= 0)
                                        <span class="badge" style="background: #7f8c8d; color: white;">Out</span>
                                    @elseif($qty <= 5)
                                        <span class="badge" style="background: #f39c12; color: #1f2d3a;">Low: {{ $qty }}</span>
                                    @else
                                        <span class="badge" style="background: #16a085; color: white;">{{ $qty }}</span>
                                    @endif
                                @endif
                            </td>
                            <td>
                                @if($product->image_url)
                                    <img src="{{ $product->image_url }}" alt="{{ $product->product_name }}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 6px;">
                                @else
                                    <span style="color: #7f8c8d;">No image</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.products.edit', ['product' => $product, 'redirect_to' => request()->fullUrl()]) }}" class="btn btn-sm btn-admin btn-admin-primary">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form action="{{ route('admin.products.destroy', $product) }}" method="POST" style="display: inline;" class="delete-form" data-product-name="{{ $product->product_name }}">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="redirect_to" value="{{ request()->fullUrl() }}">
                                    <button type="button" class="btn btn-sm btn-admin btn-admin-danger delete-btn">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; color: #7f8c8d; padding: 40px;">
                                <i class="fas fa-inbox" style="font-size: 2rem; margin-bottom: 10px;"></i>
                                <p>No products found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($products->hasPages())
            <div class="p-4">
                {{ $products->links() }}
            </div>
        @endif
    </div>
    
    <div id="confirmOverlay" class="confirm-overlay hidden">
        <div class="confirm-card">
            <div class="confirm-icon">!</div>
            <div class="confirm-copy">
                <p class="confirm-title">Delete product</p>
                <p class="confirm-body">You are about to remove <span id="confirmProductName" style="font-weight:700;"></span>. This action cannot be undone.</p>
            </div>
            <div class="confirm-actions">
                <button type="button" id="confirmCancel" class="btn btn-sm" style="background:#f2f4f7;color:#2c3e50;border:none;border-radius:8px;padding:10px 14px;font-weight:700;">Cancel</button>
                <button type="button" id="confirmDelete" class="btn btn-sm" style="background:#e74c3c;color:#fff;border:none;border-radius:8px;padding:10px 14px;font-weight:700;box-shadow:0 10px 20px rgba(231,76,60,0.25);">Delete</button>
            </div>
        </div>
    </div>

    <style>
        .hidden { display: none !important; }
        .confirm-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.45); display: flex; align-items: center; justify-content: center; z-index: 9999; padding: 16px; pointer-events: auto; }
        .confirm-card { background: #1f1f1f; color: #f6f8fb; border: 1px solid rgba(255,255,255,0.08); border-radius: 14px; padding: 18px; max-width: 420px; width: 100%; box-shadow: 0 20px 40px rgba(0,0,0,0.35); display: grid; grid-template-columns: auto 1fr; gap: 12px 14px; align-items: center; pointer-events: auto; }
        .confirm-icon { width: 40px; height: 40px; border-radius: 10px; background: #e74c3c; color: #fff; display: grid; place-items: center; font-weight: 800; font-size: 1.1rem; box-shadow: 0 0 0 5px rgba(231,76,60,0.2); flex-shrink: 0; }
        .confirm-title { margin: 0; font-weight: 800; letter-spacing: 0.2px; color: #fefefe; }
        .confirm-body { margin: 4px 0 0 0; color: #d6d9e0; font-size: 0.95rem; line-height: 1.5; }
        .confirm-actions { grid-column: span 2; display: flex; justify-content: flex-end; gap: 10px; margin-top: 6px; }
        .confirm-actions button { min-width: 90px; cursor: pointer; }
        .confirm-actions button:hover { opacity: 0.9; }
    </style>

    <script>
        let confirmState = {
            overlay: null,
            activeForm: null,
            initialized: false
        };

        function initModal() {
            if (confirmState.initialized) return;
            
            confirmState.overlay = document.getElementById('confirmOverlay');
            const productNameSpan = document.getElementById('confirmProductName');
            const cancelBtn = document.getElementById('confirmCancel');
            const deleteBtn = document.getElementById('confirmDelete');
            
            if (!confirmState.overlay || !cancelBtn || !deleteBtn) {
                console.warn('Modal elements not found');
                return;
            }

            function closeModal() {
                confirmState.overlay.classList.add('hidden');
                confirmState.activeForm = null;
            }

            function openModal(form) {
                if (!form) return;
                confirmState.activeForm = form;
                const productName = form.dataset.productName || 'this product';
                productNameSpan.textContent = productName;
                confirmState.overlay.classList.remove('hidden');
                deleteBtn.focus();
            }

            // Close on Cancel button
            cancelBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                closeModal();
            });

            // Submit on Delete button
            deleteBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                if (confirmState.activeForm) {
                    confirmState.activeForm.submit();
                }
            });

            // Close on outside click
            confirmState.overlay.addEventListener('click', (e) => {
                if (e.target === confirmState.overlay) {
                    closeModal();
                }
            }, true);

            // Close on Escape
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && !confirmState.overlay.classList.contains('hidden')) {
                    e.preventDefault();
                    closeModal();
                }
            });

            // Open on delete button click
            document.addEventListener('click', (e) => {
                const deleteBtnClicked = e.target.closest('.delete-btn');
                if (deleteBtnClicked && !e.defaultPrevented) {
                    e.preventDefault();
                    const form = deleteBtnClicked.closest('.delete-form');
                    if (form) {
                        openModal(form);
                    }
                }
            }, true);

            confirmState.initialized = true;
        }

        // Initialize on DOMContentLoaded
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initModal);
        } else {
            initModal();
        }
    </script>
@endsection
