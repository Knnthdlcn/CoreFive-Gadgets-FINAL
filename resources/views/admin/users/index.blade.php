@extends('admin.layout')

@section('title', 'Users')

@section('content')
    <div class="admin-header">
        <h1>Users Management</h1>
    </div>

    <div class="admin-card">
        <div class="table-responsive">
            <table class="table admin-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Join Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>
                                <strong>{{ $user->first_name }} {{ $user->last_name }}</strong>
                                @if($user->id === 1)
                                    <br><span class="badge" style="background: #1565c0; color: white;">Admin</span>
                                @endif
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->phone ?? 'N/A' }}</td>
                            <td>{{ $user->created_at->format('M d, Y') }}</td>
                            <td>
                                <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-admin btn-admin-primary">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: #7f8c8d; padding: 40px;">
                                <i class="fas fa-inbox" style="font-size: 2rem; margin-bottom: 10px;"></i>
                                <p>No users found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="p-4">
                {{ $users->links() }}
            </div>
        @endif
    </div>
@endsection
