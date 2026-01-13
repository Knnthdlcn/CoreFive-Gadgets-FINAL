@extends('admin.layout')

@section('title', 'Contact Message')

@section('content')
    <div class="admin-header">
        <h1>Message from {{ $contact->name }}</h1>
        <a href="{{ route('admin.contacts.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Messages
        </a>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="admin-card p-4">
                <h5 style="margin-bottom: 20px; color: #2c3e50; font-weight: 700;">Message Content</h5>
                <p style="line-height: 1.8; white-space: pre-wrap; margin: 0;">{{ $contact->message }}</p>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="admin-card p-4">
                <h5 style="margin-bottom: 20px; color: #2c3e50; font-weight: 700;">
                    <i class="fas fa-user me-2"></i>Sender Details
                </h5>
                <p style="margin: 0;">
                    <strong>{{ $contact->name }}</strong>
                </p>
                <p style="margin: 8px 0 0 0; color: #7f8c8d;">
                    <i class="fas fa-envelope me-2"></i>{{ $contact->email }}
                </p>
                <p style="margin: 15px 0 0 0; padding: 12px; background: #f8f9fa; border-radius: 8px; color: #7f8c8d; margin-bottom: 0;">
                    <strong>Received:</strong><br>
                    {{ $contact->created_at->format('M d, Y h:i A') }}
                </p>

                <form action="{{ route('admin.contacts.destroy', $contact) }}" method="POST" style="margin-top: 15px;" onclick="return confirm('Delete this message?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-admin btn-admin-danger w-100">
                        <i class="fas fa-trash me-2"></i>Delete Message
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
