@extends('admin.layout')

@section('title', 'Edit Product')

@section('content')
    <div class="admin-header">
        <h1>Edit Product</h1>
    </div>

    <div class="admin-card p-4">
        <form id="productForm" action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" novalidate>
            @csrf
            @method('PUT')

            <input type="hidden" name="redirect_to" value="{{ request('redirect_to', route('admin.products.index')) }}">

            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="product_name" class="form-label fw-semibold">Product Name *</label>
                        <input type="text" class="form-control @error('product_name') is-invalid @enderror" id="product_name" name="product_name" value="{{ old('product_name', $product->product_name) }}" required style="border-radius: 8px; border: 2px solid #e0e0e0; padding: 12px;">
                        @error('product_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label fw-semibold">Description *</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5" required style="border-radius: 8px; border: 2px solid #e0e0e0; padding: 12px; resize: none;">{{ old('description', $product->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="price" class="form-label fw-semibold">Price (₱) *</label>
                                <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price', $product->price) }}" required style="border-radius: 8px; border: 2px solid #e0e0e0; padding: 12px;">
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="stock" class="form-label fw-semibold">Stock Quantity</label>
                                <input type="number" min="0" step="1" class="form-control @error('stock') is-invalid @enderror" id="stock" name="stock" value="{{ old('stock', $product->stock ?? 0) }}" style="border-radius: 8px; border: 2px solid #e0e0e0; padding: 12px;">
                                @error('stock')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Set to 0 for out of stock.</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="category" class="form-label fw-semibold">Category *</label>
                                <select class="form-control @error('category') is-invalid @enderror" id="category" name="category" required style="border-radius: 8px; border: 2px solid #e0e0e0; padding: 12px;">
                                    <option value="">-- Select Category --</option>
                                    @forelse($categories as $cat)
                                        <option value="{{ $cat }}" @if(old('category', $product->category) === $cat) selected @endif>{{ $cat }}</option>
                                    @empty
                                        <option disabled>No categories available</option>
                                    @endforelse
                                </select>
                                @error('category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Unlimited Stock</label>
                                <div class="form-check" style="margin-top: 10px;">
                                    <input class="form-check-input" type="checkbox" value="1" id="stock_unlimited" name="stock_unlimited" {{ old('stock_unlimited', $product->stock_unlimited) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="stock_unlimited">Mark as unlimited</label>
                                </div>
                                @error('stock_unlimited')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Homepage</label>
                                <div class="form-check" style="margin-top: 10px;">
                                    <input class="form-check-input" type="checkbox" value="1" id="is_featured" name="is_featured" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_featured">Show on home page (Featured)</label>
                                </div>
                                <div class="form-text">Only featured products appear in the Featured section on the homepage.</div>
                                @error('is_featured')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="image" class="form-label fw-semibold">Product Image</label>
                        @if($product->image_url)
                            <div style="margin-bottom: 10px; text-align: center;">
                                <img src="{{ $product->image_url }}" alt="{{ $product->product_name }}" style="max-width: 100%; max-height: 200px; border-radius: 8px; object-fit: cover;">
                                <p style="margin: 8px 0 0 0; font-size: 0.85rem; color: #7f8c8d;">Current image</p>
                            </div>
                        @endif
                        <div class="border-2 border-dashed" style="border-radius: 8px; padding: 30px; text-align: center; border-color: #e0e0e0; background: #f8f9fa; cursor: pointer;" id="imageDropZone">
                            <input type="file" class="form-control d-none @error('image') is-invalid @enderror" id="image" name="image" accept="image/*" style="border-radius: 8px;">
                            <i class="fas fa-cloud-upload-alt" style="font-size: 2.5rem; color: #1565c0; margin-bottom: 10px;"></i>
                            <p style="margin: 10px 0 0 0; color: #7f8c8d;">Click or drag image here</p>
                            <small style="color: #999;">JPG, PNG, GIF (Max 2MB)</small>
                            <div id="imagePreview" style="margin-top: 10px;"></div>
                        </div>
                        @error('image')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-admin btn-admin-primary">
                    <i class="fas fa-save me-2"></i>Update Product
                </button>
                <a href="{{ request('redirect_to', route('admin.products.index')) }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

    <style>
        .field-error { border-color: #e74c3c !important; box-shadow: 0 0 0 3px rgba(231,76,60,0.18); }
    </style>

    <script>
        const imageInput = document.getElementById('image');
        const imageDropZone = document.getElementById('imageDropZone');
        const imagePreview = document.getElementById('imagePreview');
        const form = document.getElementById('productForm');
        const categorySelect = document.getElementById('category');
        const unlimitedCheckbox = document.getElementById('stock_unlimited');
        const stockInput = document.getElementById('stock');

        imageDropZone.addEventListener('click', () => imageInput.click());

        imageDropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            imageDropZone.style.background = '#e3f2fd';
        });

        imageDropZone.addEventListener('dragleave', () => {
            imageDropZone.style.background = '#f8f9fa';
        });

        imageDropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            imageDropZone.style.background = '#f8f9fa';
            if (e.dataTransfer.files.length) {
                imageInput.files = e.dataTransfer.files;
                handleImagePreview();
            }
        });

        imageInput.addEventListener('change', handleImagePreview);

        form.addEventListener('submit', (e) => {
            if (!categorySelect.value) {
                e.preventDefault();
                categorySelect.classList.add('field-error');
                categorySelect.focus();
            }
        });

        categorySelect.addEventListener('change', () => {
            if (categorySelect.value) {
                categorySelect.classList.remove('field-error');
            }
        });

        function syncStockToggle() {
            if (!unlimitedCheckbox || !stockInput) return;
            const unlimited = unlimitedCheckbox.checked;
            stockInput.disabled = unlimited;
            stockInput.style.opacity = unlimited ? '0.6' : '1';
        }

        if (unlimitedCheckbox) {
            unlimitedCheckbox.addEventListener('change', syncStockToggle);
            syncStockToggle();
        }

        function handleImagePreview() {
            imagePreview.innerHTML = '';
            if (imageInput.files.length) {
                const file = imageInput.files[0];
                const reader = new FileReader();
                reader.onload = (e) => {
                    imagePreview.innerHTML = `<img src="${e.target.result}" style="width: 100%; max-width: 150px; border-radius: 6px; object-fit: cover;">`;
                };
                reader.readAsDataURL(file);
            }
        }
    </script>

    <div class="admin-card p-4 mt-4">
        <h3 class="mb-2" style="margin: 0;">Variants (Option A)</h3>
        <p class="text-muted" style="margin-top: 6px;">
            Add variants like <strong>Color</strong> or <strong>Storage</strong>. When a product has variants, customers must pick one before checkout.
            Leave <strong>Price Override</strong> blank to use the base product price.
        </p>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th style="width: 220px;">Name</th>
                        <th style="width: 170px;">Price Override</th>
                        <th style="width: 170px;">Stock</th>
                        <th style="width: 140px;">Unlimited</th>
                        <th style="width: 120px;">Active</th>
                        <th style="width: 120px;">Disable</th>
                    </tr>
                </thead>
                <tbody id="variantsTbody">
                    @forelse($product->variants ?? [] as $v)
                        <tr>
                            <td>
                                <input type="text" class="form-control" form="productForm" name="variants[{{ $v->id }}][name]" value="{{ old('variants.' . $v->id . '.name', $v->name) }}" style="border-radius: 8px;" required>
                            </td>
                            <td>
                                <input type="number" step="0.01" min="0" class="form-control" form="productForm" name="variants[{{ $v->id }}][price]" value="{{ old('variants.' . $v->id . '.price', $v->price) }}" placeholder="(use base)" style="border-radius: 8px;">
                            </td>
                            <td>
                                <input type="number" step="1" min="0" class="form-control" form="productForm" name="variants[{{ $v->id }}][stock]" value="{{ old('variants.' . $v->id . '.stock', $v->stock ?? 0) }}" style="border-radius: 8px;" {{ $v->stock_unlimited ? 'disabled' : '' }}>
                            </td>
                            <td>
                                <input type="hidden" form="productForm" name="variants[{{ $v->id }}][stock_unlimited]" value="0">
                                <div class="form-check">
                                    <input class="form-check-input variant-unlimited" type="checkbox" form="productForm" name="variants[{{ $v->id }}][stock_unlimited]" value="1" {{ old('variants.' . $v->id . '.stock_unlimited', $v->stock_unlimited) ? 'checked' : '' }}>
                                    <label class="form-check-label">Unlimited</label>
                                </div>
                            </td>
                            <td>
                                <input type="hidden" form="productForm" name="variants[{{ $v->id }}][is_active]" value="0">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" form="productForm" name="variants[{{ $v->id }}][is_active]" value="1" {{ old('variants.' . $v->id . '.is_active', $v->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label">Active</label>
                                </div>
                            </td>
                            <td>
                                <input type="hidden" form="productForm" name="variants[{{ $v->id }}][_delete]" value="0">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" form="productForm" name="variants[{{ $v->id }}][_delete]" value="1">
                                    <label class="form-check-label">Disable</label>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-muted">No variants yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-admin btn-admin-primary" id="addVariantRowBtn">
                <i class="fas fa-plus me-2"></i>Add Variant
            </button>
            <div class="text-muted small">Add rows, then click <strong>Update Product</strong> to save.</div>
        </div>
    </div>

    <script>
        (function() {
            const tbody = document.getElementById('variantsTbody');
            const addBtn = document.getElementById('addVariantRowBtn');
            if (!tbody || !addBtn) return;

            let newIndex = 0;

            function buildRow(i) {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td><input type="text" class="form-control" form="productForm" name="variants_new[${i}][name]" placeholder="e.g., 128GB" style="border-radius: 8px;" required></td>
                    <td><input type="number" step="0.01" min="0" class="form-control" form="productForm" name="variants_new[${i}][price]" placeholder="(use base)" style="border-radius: 8px;"></td>
                    <td><input type="number" step="1" min="0" class="form-control variant-stock" form="productForm" name="variants_new[${i}][stock]" value="0" style="border-radius: 8px;"></td>
                    <td>
                        <input type="hidden" form="productForm" name="variants_new[${i}][stock_unlimited]" value="0">
                        <div class="form-check">
                            <input class="form-check-input variant-unlimited" type="checkbox" form="productForm" name="variants_new[${i}][stock_unlimited]" value="1">
                            <label class="form-check-label">Unlimited</label>
                        </div>
                    </td>
                    <td>
                        <input type="hidden" form="productForm" name="variants_new[${i}][is_active]" value="0">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" form="productForm" name="variants_new[${i}][is_active]" value="1" checked>
                            <label class="form-check-label">Active</label>
                        </div>
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-outline-danger remove-variant-row">Remove</button>
                    </td>
                `;
                return tr;
            }

            function syncUnlimitedStates() {
                tbody.querySelectorAll('tr').forEach(tr => {
                    const unlimited = tr.querySelector('.variant-unlimited');
                    const stockInput = tr.querySelector('input[name$="[stock]"]');
                    if (!unlimited || !stockInput) return;
                    stockInput.disabled = unlimited.checked;
                    stockInput.style.opacity = unlimited.checked ? '0.6' : '1';
                });
            }

            tbody.addEventListener('change', (e) => {
                if (e.target && e.target.classList.contains('variant-unlimited')) {
                    syncUnlimitedStates();
                }
            });
            tbody.addEventListener('click', (e) => {
                if (e.target && e.target.classList.contains('remove-variant-row')) {
                    e.preventDefault();
                    const tr = e.target.closest('tr');
                    if (tr) tr.remove();
                }
            });

            addBtn.addEventListener('click', () => {
                // If the table only has the "No variants yet" row, clear it.
                const onlyRow = tbody.querySelector('tr td[colspan]');
                if (onlyRow) tbody.innerHTML = '';

                tbody.appendChild(buildRow(newIndex++));
                syncUnlimitedStates();
            });

            syncUnlimitedStates();
        })();
    </script>

    <div class="admin-card p-4 mt-4">
        <h3 class="mb-3" style="margin: 0;">Stock Change Audit</h3>
        <p class="text-muted" style="margin-top: 6px;">Shows who changed stock, before/after values, and timestamp.</p>

        @php($audits = $product->stockAudits ?? collect())

        @if($audits->isEmpty())
            <div class="text-muted">No stock changes yet.</div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th style="width: 220px;">When</th>
                            <th>Admin</th>
                            <th>Before</th>
                            <th>After</th>
                            <th>Note</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($audits as $audit)
                            <tr>
                                <td>{{ optional($audit->created_at)->format('Y-m-d H:i') }}</td>
                                <td>{{ $audit->adminUser?->email ?? ('User #' . $audit->admin_user_id) }}</td>
                                <td>
                                    @if($audit->before_unlimited)
                                        Unlimited
                                    @else
                                        {{ $audit->before_quantity ?? 0 }}
                                    @endif
                                </td>
                                <td>
                                    @if($audit->after_unlimited)
                                        Unlimited
                                    @else
                                        {{ $audit->after_quantity ?? 0 }}
                                    @endif
                                </td>
                                <td class="text-muted">{{ $audit->note }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
