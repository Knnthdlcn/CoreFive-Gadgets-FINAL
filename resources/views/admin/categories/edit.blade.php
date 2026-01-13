@extends('admin.layout')

@section('title', 'Edit Category')

@section('content')
    <div class="admin-header">
        <h1>Edit Category</h1>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Categories
        </a>
    </div>

    <div class="admin-card p-4">
        <form method="POST" action="{{ route('admin.categories.update', request()->route('id')) }}">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="category_name" class="form-label" style="font-weight: 600; color: #2c3e50;">
                    Category Name <span style="color: #dc3545;">*</span>
                </label>
                <input 
                    type="text" 
                    class="form-control @error('category_name') is-invalid @enderror" 
                    id="category_name" 
                    name="category_name" 
                    value="{{ old('category_name', request()->route('id') === '1' ? 'Phones' : (request()->route('id') === '2' ? 'Computing' : 'Accessories')) }}"
                    placeholder="e.g., Electronics, Clothing, Books"
                    required
                    style="border: 1px solid #e0e6ed; padding: 12px 15px; border-radius: 8px; font-size: 14px;"
                >
                @error('category_name')
                    <div class="invalid-feedback d-block" style="color: #dc3545; margin-top: 5px;">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="description" class="form-label" style="font-weight: 600; color: #2c3e50;">
                    Description
                </label>
                <textarea 
                    class="form-control @error('description') is-invalid @enderror" 
                    id="description" 
                    name="description" 
                    rows="4"
                    placeholder="Brief description of this category"
                    style="border: 1px solid #e0e6ed; padding: 12px 15px; border-radius: 8px; font-size: 14px;"
                >{{ old('description') }}</textarea>
                @error('description')
                    <div class="invalid-feedback d-block" style="color: #dc3545; margin-top: 5px;">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div style="display: flex; gap: 10px; margin-top: 30px;">
                <button type="submit" class="btn btn-admin-primary">
                    <i class="fas fa-save me-2"></i>Update Category
                </button>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

    <style>
        .form-control {
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        .form-control:focus {
            border-color: #1565c0;
            box-shadow: 0 0 0 0.2rem rgba(21, 101, 192, 0.25);
        }

        .form-control.is-invalid {
            border-color: #dc3545;
        }

        .form-control.is-invalid:focus {
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }
    </style>
@endsection
