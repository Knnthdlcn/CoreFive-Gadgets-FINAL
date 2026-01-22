@extends('admin.layout')

@section('title', 'Users')

@section('content')
    <div class="admin-header">
        <h1>Users Management</h1>
    </div>

    <div class="admin-card">
        @if($users->hasPages())
            <div class="p-4" style="padding-bottom: 0 !important;">
                {{ $users->links() }}
            </div>
        @endif

        <div class="table-responsive">
            <table class="table admin-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Join Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>
                                <strong>{{ $user->first_name }} {{ $user->last_name }}</strong>
                                @if(($user->role ?? 'customer') === 'admin')
                                    <br><span class="badge" style="background: #1565c0; color: white;">Admin</span>
                                @endif
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->contact ?? 'N/A' }}</td>
                            <td>{{ $user->created_at->format('M d, Y') }}</td>
                            <td>
                                @if(!empty($user->banned_at))
                                    <span class="badge" style="background:#b91c1c;color:#fff;">Banned</span>
                                @else
                                    <span class="badge" style="background:#16a34a;color:#fff;">Active</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-admin btn-admin-primary">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                @if(($user->role ?? 'customer') !== 'admin')
                                    {{-- Make Admin button --}}
                                    <form action="{{ route('admin.users.make-admin', $user) }}" method="POST" style="display: inline; margin-right:6px;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-admin" style="background:#6b7280;color:#fff;">
                                            <i class="fas fa-user-shield"></i> Make Admin
                                        </button>
                                    </form>

                                    @if(empty($user->banned_at))
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display: inline;" class="ban-form" data-user-name="{{ $user->first_name }} {{ $user->last_name }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-admin btn-admin-danger ban-btn">
                                                <i class="fas fa-ban"></i> Ban
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.users.restore', $user) }}" method="POST" style="display: inline; margin-right:6px;" class="restore-form" data-user-name="{{ $user->first_name }} {{ $user->last_name }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-admin" style="background:#16a34a;color:#fff;">
                                                <i class="fas fa-undo"></i> Unban
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.users.force-destroy', $user) }}" method="POST" style="display: inline;" class="force-delete-form" data-user-name="{{ $user->first_name }} {{ $user->last_name }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-admin btn-admin-danger force-delete-btn" style="background:#b91c1c;color:#fff;">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    @endif
                                @else
                                    {{-- Admin badge shown earlier; allow demote in future if needed --}}
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; color: #7f8c8d; padding: 40px;">
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

    <div id="confirmOverlay" class="confirm-overlay hidden">
        <div class="confirm-card">
            <div class="confirm-icon">!</div>
            <div class="confirm-copy">
                <p class="confirm-title">Ban user</p>
                <p class="confirm-body">You are about to temporarily disable <span id="confirmUserName" style="font-weight:700;"></span>. You can restore the account anytime.</p>
            </div>
            <div class="confirm-actions">
                <button type="button" id="confirmCancel" class="btn btn-sm" style="background:#f2f4f7;color:#2c3e50;border:none;border-radius:8px;padding:10px 14px;font-weight:700;">Cancel</button>
                <button type="button" id="confirmDelete" class="btn btn-sm" style="background:#e74c3c;color:#fff;border:none;border-radius:8px;padding:10px 14px;font-weight:700;box-shadow:0 10px 20px rgba(231,76,60,0.25);">Ban</button>
            </div>
        </div>
    </div>

    <style>
        .hidden { display: none !important; }
        .confirm-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.45); display: flex; align-items: center; justify-content: center; z-index: 9999; padding: 16px; pointer-events: auto; }
        .confirm-card { background: #1f1f1f; color: #f6f8fb; border: 1px solid rgba(255,255,255,0.08); border-radius: 14px; padding: 18px; max-width: 420px; width: 100%; box-shadow: 0 20px 40px rgba(0,0,0,0.35); display: grid; grid-template-columns: auto 1fr; gap: 12px 14px; align-items: center; pointer-events: auto; }
        .confirm-icon { width: 40px; height: 40px; border-radius: 10px; background: #e74c3c; color: #fff; display: grid; place-items: center; font-weight: 800; font-size: 1.1rem; box-shadow: 0 0 0 5px rgba(231,76,60,0.2); flex-shrink: 0; }
        .confirm-title { margin: 0; font-weight: 800; letter-spacing: 0.2px; color: #fefefe; }
        .confirm-body { margin: 4px 0 0 0; color: #d6d9e0; font-size: 0.95rem; line-height: 1.5; }
        .confirm-actions { grid-column: span 2; display: flex; justify-content: flex-end; gap: 10px; margin-top: 6px; }
        .confirm-actions button { min-width: 90px; cursor: pointer; }
        .confirm-actions button:hover { opacity: 0.9; }
    </style>

    <script>
        let confirmState = {
            overlay: null,
            activeForm: null,
            initialized: false
        };

        function initModal() {
            if (confirmState.initialized) return;

            confirmState.overlay = document.getElementById('confirmOverlay');
            let userNameSpan = document.getElementById('confirmUserName');
            const cancelBtn = document.getElementById('confirmCancel');
            const deleteBtn = document.getElementById('confirmDelete');

            if (!confirmState.overlay || !cancelBtn || !deleteBtn) return;

            function closeModal() {
                confirmState.overlay.classList.add('hidden');
                confirmState.activeForm = null;
            }

            function openModal(form, action = 'ban') {
                if (!form) return;
                confirmState.activeForm = form;
                userNameSpan.textContent = form.dataset.userName || 'this user';
                // Adjust copy/button for action
                const titleEl = document.querySelector('.confirm-title');
                const bodyEl = document.querySelector('.confirm-body');
                if (action === 'delete') {
                    titleEl.textContent = 'Delete user';
                    bodyEl.innerHTML = 'You are about to <strong>permanently delete</strong> <span id="confirmUserName" style="font-weight:700;"></span>. This cannot be undone.';
                    deleteBtn.textContent = 'Delete';
                    deleteBtn.style.background = '#b91c1c';
                } else {
                    titleEl.textContent = 'Ban user';
                    bodyEl.innerHTML = 'You are about to temporarily disable <span id="confirmUserName" style="font-weight:700;"></span>. You can restore the account anytime.';
                    deleteBtn.textContent = 'Ban';
                    deleteBtn.style.background = '#e74c3c';
                }

                // re-query the username span because we may have replaced the innerHTML above
                userNameSpan = document.getElementById('confirmUserName');

                confirmState.overlay.classList.remove('hidden');
                deleteBtn.focus();
            }

            cancelBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                closeModal();
            });

            deleteBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                if (confirmState.activeForm) {
                    confirmState.activeForm.submit();
                }
            });

            confirmState.overlay.addEventListener('click', (e) => {
                if (e.target === confirmState.overlay) closeModal();
            }, true);

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && !confirmState.overlay.classList.contains('hidden')) {
                    e.preventDefault();
                    closeModal();
                }
            });

            document.addEventListener('click', (e) => {
                const banBtnClicked = e.target.closest('.ban-btn');
                if (banBtnClicked && !e.defaultPrevented) {
                    e.preventDefault();
                    const form = banBtnClicked.closest('.ban-form');
                    if (form) openModal(form, 'ban');
                }

                const deleteBtnClicked = e.target.closest('.force-delete-btn');
                if (deleteBtnClicked && !e.defaultPrevented) {
                    e.preventDefault();
                    const form = deleteBtnClicked.closest('.force-delete-form');
                    if (form) openModal(form, 'delete');
                }
            }, true);

            confirmState.initialized = true;
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initModal);
        } else {
            initModal();
        }
    </script>
@endsection
