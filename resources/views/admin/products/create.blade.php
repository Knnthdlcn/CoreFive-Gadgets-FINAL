@extends('admin.layout')

@section('title', 'Add Product')

@section('content')
    <div class="admin-header">
        <h1>Add New Product</h1>
    </div>

    <div class="admin-card p-4">
        <form id="productForm" action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" novalidate>
            @csrf

            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="product_name" class="form-label fw-semibold">Product Name *</label>
                        <input type="text" class="form-control @error('product_name') is-invalid @enderror" id="product_name" name="product_name" value="{{ old('product_name') }}" required style="border-radius: 8px; border: 2px solid #e0e0e0; padding: 12px;">
                        @error('product_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label fw-semibold">Description *</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5" required style="border-radius: 8px; border: 2px solid #e0e0e0; padding: 12px; resize: none;">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="price" class="form-label fw-semibold">Price (₱) *</label>
                                <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price') }}" required style="border-radius: 8px; border: 2px solid #e0e0e0; padding: 12px;">
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="stock" class="form-label fw-semibold">Stock Quantity</label>
                                <input type="number" min="0" step="1" class="form-control @error('stock') is-invalid @enderror" id="stock" name="stock" value="{{ old('stock', 0) }}" style="border-radius: 8px; border: 2px solid #e0e0e0; padding: 12px;">
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
                                        <option value="{{ $cat }}" @if(old('category') === $cat) selected @endif>{{ $cat }}</option>
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
                                    <input class="form-check-input" type="checkbox" value="1" id="stock_unlimited" name="stock_unlimited" {{ old('stock_unlimited') ? 'checked' : '' }}>
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
                                    <input class="form-check-input" type="checkbox" value="1" id="is_featured" name="is_featured" {{ old('is_featured') ? 'checked' : '' }}>
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
                    <i class="fas fa-save me-2"></i>Create Product
                </button>
                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Cancel</a>
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
@endsection
