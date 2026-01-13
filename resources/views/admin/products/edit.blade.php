@extends('admin.layout')

@section('title', 'Edit Product')

@section('content')
    <div class="admin-header">
        <h1>Edit Product</h1>
    </div>

    <div class="admin-card p-4">
        <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

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
                                <label for="price" class="form-label fw-semibold">Price ($) *</label>
                                <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price', $product->price) }}" required style="border-radius: 8px; border: 2px solid #e0e0e0; padding: 12px;">
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="category" class="form-label fw-semibold">Category *</label>
                                <select class="form-control @error('category') is-invalid @enderror" id="category" name="category" required style="border-radius: 8px; border: 2px solid #e0e0e0; padding: 12px;">
                                    <option value="">-- Select Category --</option>
                                    <option value="Phones" @if(old('category', $product->category) === 'Phones') selected @endif>Phones</option>
                                    <option value="Computing" @if(old('category', $product->category) === 'Computing') selected @endif>Computing</option>
                                    <option value="Accessories" @if(old('category', $product->category) === 'Accessories') selected @endif>Accessories</option>
                                </select>
                                @error('category')
                                    <div class="invalid-feedback">{{ $message }}</div>
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
                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

    <script>
        const imageInput = document.getElementById('image');
        const imageDropZone = document.getElementById('imageDropZone');
        const imagePreview = document.getElementById('imagePreview');

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
