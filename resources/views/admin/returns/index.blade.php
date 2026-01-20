@extends('admin.layout')

@section('title', 'Returns')

@section('content')
    <div class="admin-header">
        <div>
            <h1>Returns</h1>
            @php($active = request('status'))
            <div class="mt-2" style="display:flex; gap:8px; flex-wrap: wrap;">
                <a href="{{ route('admin.returns.index') }}" class="btn btn-sm btn-admin" style="background: {{ empty($active) ? '#1565c0' : '#f2f4f7' }}; color: {{ empty($active) ? '#fff' : '#2c3e50' }}; border-radius: 10px; font-weight: 700;">All</a>
                @foreach(['requested' => '#ff9800', 'approved' => '#1565c0', 'in_transit' => '#17a2b8', 'received' => '#2e7d32', 'rejected' => '#6c757d', 'closed' => '#455a64'] as $s => $c)
                    <a href="{{ route('admin.returns.index', ['status' => $s]) }}" class="btn btn-sm btn-admin" style="background: {{ $active === $s ? $c : '#f2f4f7' }}; color: {{ $active === $s ? '#fff' : '#2c3e50' }}; border-radius: 10px; font-weight: 700;">
                        {{ ucfirst(str_replace('_',' ', $s)) }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <div class="admin-card">
        <div class="table-responsive">
            <table class="table admin-table">
                <thead>
                    <tr>
                        <th>Return ID</th>
                        <th>Order</th>
                        <th>Customer</th>
                        <th>Status</th>
                        <th>Deadline</th>
                        <th>Requested</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($returns as $r)
                        <tr>
                            <td><strong>#{{ $r->id }}</strong></td>
                            <td>
                                <a href="{{ route('admin.orders.show', $r->order) }}" style="text-decoration: none; color: #1565c0; font-weight: 700;">
                                    {{ $r->order->display_order_number ?? ('Order #' . $r->order_id) }}
                                </a>
                            </td>
                            <td>
                                <strong>{{ $r->order->user->first_name ?? '' }} {{ $r->order->user->last_name ?? '' }}</strong>
                                <br>
                                <small style="color: #7f8c8d;">{{ $r->order->user->email ?? '' }}</small>
                            </td>
                            <td>
                                <span class="badge" style="background: @if($r->status === 'requested') #ff9800 @elseif($r->status === 'approved') #1565c0 @elseif($r->status === 'in_transit') #17a2b8 @elseif($r->status === 'received') #2e7d32 @elseif($r->status === 'closed') #455a64 @else #6c757d @endif; color: white; padding: 6px 12px;">
                                    {{ ucfirst(str_replace('_',' ', $r->status)) }}
                                </span>
                            </td>
                            <td>{{ $r->deadline_at ? $r->deadline_at->format('M d, Y') : '-' }}</td>
                            <td>{{ ($r->requested_at ?? $r->created_at)->format('M d, Y') }}</td>
                            <td>
                                <a href="{{ route('admin.returns.show', $r) }}" class="btn btn-sm btn-admin btn-admin-primary">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; color: #7f8c8d; padding: 40px;">
                                <i class="fas fa-inbox" style="font-size: 2rem; margin-bottom: 10px;"></i>
                                <p>No return requests found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($returns->hasPages())
            <div class="p-4">
                {{ $returns->links() }}
            </div>
        @endif
    </div>
@endsection
