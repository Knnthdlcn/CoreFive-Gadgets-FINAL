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
        <div class="table-responsive">
            <table class="table admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
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
                                <span class="badge" style="background: #1565c0; color: white;">{{ $product->category ?? 'N/A' }}</span>
                            </td>
                            <td><strong>${{ number_format($product->price, 2) }}</strong></td>
                            <td>
                                @if($product->image_url)
                                    <img src="{{ $product->image_url }}" alt="{{ $product->product_name }}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 6px;">
                                @else
                                    <span style="color: #7f8c8d;">No image</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-admin btn-admin-primary">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form action="{{ route('admin.products.destroy', $product) }}" method="POST" style="display: inline;" onclick="return confirm('Are you sure?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-admin btn-admin-danger">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; color: #7f8c8d; padding: 40px;">
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
@endsection
