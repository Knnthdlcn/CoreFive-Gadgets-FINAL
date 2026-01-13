@extends('admin.layout')

@section('title', 'Categories')

@section('content')
    <div class="admin-header">
        <h1>Categories</h1>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-admin-primary">
            <i class="fas fa-plus me-2"></i>Add New Category
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="admin-card p-4">
        <div class="table-responsive">
            <table class="table admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Category Name</th>
                        <th>Products</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $categories = [
                            ['name' => 'Phones', 'count' => \App\Models\Product::where('category', 'Phones')->count()],
                            ['name' => 'Computing', 'count' => \App\Models\Product::where('category', 'Computing')->count()],
                            ['name' => 'Accessories', 'count' => \App\Models\Product::where('category', 'Accessories')->count()]
                        ];
                    @endphp
                    @foreach($categories as $category)
                        <tr>
                            <td><strong>#{{ $loop->iteration }}</strong></td>
                            <td>{{ $category['name'] }}</td>
                            <td><span class="badge" style="background: #1565c0; color: white;">{{ $category['count'] }}</span></td>
                            <td>
                                <a href="{{ route('admin.categories.edit', $loop->iteration) }}" class="btn btn-sm btn-admin-primary">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <button class="btn btn-sm btn-admin-danger" onclick="confirmDelete({{ $loop->iteration }})">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function confirmDelete(categoryId) {
            if (confirm('Are you sure you want to delete this category?')) {
                // In a real application, this would submit a DELETE form
                alert('Category deletion would be implemented when categories have a dedicated table.');
            }
        }
    </script>
@endsection
