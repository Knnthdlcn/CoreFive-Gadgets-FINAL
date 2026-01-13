@extends('admin.layout')

@section('title', 'Contact Messages')

@section('content')
    <div class="admin-header">
        <h1>Contact Messages</h1>
    </div>

    <div class="admin-card">
        <div class="table-responsive">
            <table class="table admin-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Message Preview</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($contacts as $contact)
                        <tr>
                            <td><strong>{{ $contact->name }}</strong></td>
                            <td>{{ $contact->email }}</td>
                            <td>{{ Str::limit($contact->message, 50) }}</td>
                            <td>{{ $contact->created_at->format('M d, Y') }}</td>
                            <td>
                                <a href="{{ route('admin.contacts.show', $contact) }}" class="btn btn-sm btn-admin btn-admin-primary">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                <form action="{{ route('admin.contacts.destroy', $contact) }}" method="POST" style="display: inline;" onclick="return confirm('Delete this message?');">
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
                            <td colspan="5" style="text-align: center; color: #7f8c8d; padding: 40px;">
                                <i class="fas fa-inbox" style="font-size: 2rem; margin-bottom: 10px;"></i>
                                <p>No messages found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($contacts->hasPages())
            <div class="p-4">
                {{ $contacts->links() }}
            </div>
        @endif
    </div>
@endsection
