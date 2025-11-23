@extends('layouts.app')

@section('content')
    <div class="user-detail-container">
        <!-- Page Header -->
        <div class="page-header mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="page-title">User Details</h2>
                    <p class="text-muted">View complete user information</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.user.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Users
                    </a>
                    <a href="{{ route('admin.user.edit', $user->id) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit User
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-user-circle text-primary"></i> User Information</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="text-center mb-4 pb-4 border-bottom">
                            <div class="user-avatar-large mb-3">
                                {{ $user->initials }}
                            </div>
                            <h3 class="mb-1">{{ $user->full_name }}</h3>
                            <p class="text-muted mb-2">{{ $user->email }}</p>
                            <span
                                class="badge badge-lg bg-{{ \App\Enums\UserType::getBadgeClass($user->user_type->value) }}">
                                <i class="{{ \App\Enums\UserType::getIcon($user->user_type->value) }}"></i>
                                {{ $user->user_type->description }}
                            </span>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="detail-label">First Name</label>
                                <h5 class="detail-value">{{ $user->first_name }}</h5>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="detail-label">Middle Name</label>
                                <h5 class="detail-value">{{ $user->middle_name ?? '—' }}</h5>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="detail-label">Last Name</label>
                                <h5 class="detail-value">{{ $user->last_name }}</h5>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="detail-label">Email Address</label>
                                <h5 class="detail-value">
                                    <span>
                                        <i class="fas fa-envelope"></i> {{ $user->email }}
                                    </span>
                                </h5>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="detail-label">User Role</label>
                                <h5 class="detail-value">
                                    <span
                                        class="badge badge-lg bg-{{ \App\Enums\UserType::getBadgeClass($user->user_type->value) }}">
                                        <i class="{{ \App\Enums\UserType::getIcon($user->user_type->value) }}"></i>
                                        {{ $user->user_type->description }}
                                    </span>
                                </h5>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="detail-label">Account Status</label>
                                <h5 class="detail-value">
                                    <span class="badge badge-lg bg-success">
                                        <i class="fas fa-check-circle"></i> Active
                                    </span>
                                </h5>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="detail-label">Date Joined</label>
                                <h5 class="detail-value">
                                    <i class="fas fa-calendar-alt text-info"></i> {{ $user->created_at->format('F d, Y') }}
                                </h5>
                                <small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="detail-label">Last Updated</label>
                                <h5 class="detail-value">
                                    <i class="fas fa-clock text-warning"></i> {{ $user->updated_at->format('F d, Y') }}
                                </h5>
                                <small class="text-muted">{{ $user->updated_at->diffForHumans() }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-tools text-warning"></i> Quick Actions</h5>
                    </div>
                    <div class="card-body p-3">
                        <a href="{{ route('admin.user.edit', $user->id) }}" class="btn btn-primary w-100 mb-2">
                            <i class="fas fa-edit"></i> Edit User
                        </a>
                        {{-- <button onclick="resetPassword('{{ $user->id }}', '{{ $user->full_name }}')"
                            class="btn btn-warning w-100 mb-2">
                            <i class="fas fa-key"></i> Reset Password
                        </button> --}}
                        <button onclick="deleteUser('{{ $user->id }}', '{{ $user->full_name }}')"
                            class="btn btn-danger w-100 mb-2">
                            <i class="fas fa-trash"></i> Delete User
                        </button>
                        <a href="{{ route('admin.user.index') }}" class="btn btn-secondary w-100">
                            <i class="fas fa-list"></i> Back to List
                        </a>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-chart-line text-success"></i> Account Stats</h5>
                    </div>
                    <div class="card-body p-3">
                        <div class="stat-item">
                            <div class="stat-icon bg-primary">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div class="stat-content">
                                <small>Days Active</small>
                                <h5>{{ $user->created_at->diffInDays(now()) }} days</h5>
                            </div>
                        </div>

                        <div class="stat-item">
                            <div class="stat-icon bg-info">
                                <i class="fas fa-birthday-cake"></i>
                            </div>
                            <div class="stat-content">
                                <small>Account Age</small>
                                <h5>{{ $user->created_at->diffForHumans(null, true) }}</h5>
                            </div>
                        </div>

                        <div class="stat-item">
                            <div class="stat-icon bg-success">
                                <i class="fas fa-sign-in-alt"></i>
                            </div>
                            <div class="stat-content">
                                <small>Last Login</small>
                                <h5>{{ $user->updated_at->diffForHumans() }}</h5>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-shield-alt text-info"></i> Role Permissions</h5>
                    </div>
                    <div class="card-body p-3">
                        @if ($user->isAdmin())
                            <div class="alert alert-danger mb-2">
                                <i class="fas fa-crown"></i> <strong>Full Access</strong><br>
                                <small>Can manage all system features</small>
                            </div>
                        @elseif($user->isManager())
                            <div class="alert alert-primary mb-2">
                                <i class="fas fa-user-tie"></i> <strong>Manager Access</strong><br>
                                <small>Can manage employees and inventory</small>
                            </div>
                        @else
                            <div class="alert alert-success mb-2">
                                <i class="fas fa-user"></i> <strong>Employee Access</strong><br>
                                <small>Can view and update assigned tasks</small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .user-detail-container {
            padding: 2rem;
            background-color: #f5f6fa;
            min-height: calc(100vh - 70px);
            margin-top: 70px;
        }

        .page-title {
            font-size: 2rem;
            font-weight: 700;
            color: #2c3e50;
            margin: 0;
        }

        .card {
            border-radius: 15px;
        }

        .card-header {
            padding: 1.25rem 1.5rem;
            border-radius: 15px 15px 0 0 !important;
        }

        .card-header h5 {
            color: #2c3e50;
            font-weight: 700;
            font-size: 1rem;
        }

        .user-avatar-large {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-green, #1a4d2e) 0%, var(--light-green, #2d7a4e) 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            font-weight: 700;
            margin: 0 auto;
            box-shadow: 0 8px 20px rgba(26, 77, 46, 0.3);
        }

        .detail-label {
            font-size: 0.85rem;
            color: #6c757d;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
            display: block;
        }

        .detail-value {
            color: #2c3e50;
            font-weight: 700;
            margin: 0;
        }

        .badge-lg {
            padding: 0.6rem 1rem;
            font-size: 0.95rem;
            font-weight: 600;
        }

        /* Quick Stats */
        .stat-item {
            display: flex;
            align-items: center;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 10px;
            margin-bottom: 0.75rem;
        }

        .stat-item:last-child {
            margin-bottom: 0;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.3rem;
            margin-right: 1rem;
        }

        .stat-content small {
            color: #6c757d;
            font-size: 0.8rem;
            display: block;
        }

        .stat-content h5 {
            margin: 0;
            color: #2c3e50;
            font-weight: 700;
        }

        .btn {
            padding: 0.65rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .alert {
            border-radius: 8px;
            font-size: 0.9rem;
        }
    </style>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function deleteUser(id, name) {
            Swal.fire({
                title: 'Delete User?',
                html: `Are you sure you want to delete <strong>${name}</strong>?<br>This action cannot be undone.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-trash"></i> Yes, Delete',
                cancelButtonText: '<i class="fas fa-times-circle"></i> Cancel',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/admin/user/${id}`;

                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';

                    const methodField = document.createElement('input');
                    methodField.type = 'hidden';
                    methodField.name = '_method';
                    methodField.value = 'DELETE';

                    form.appendChild(csrfToken);
                    form.appendChild(methodField);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        function resetPassword(id, name) {
            Swal.fire({
                title: 'Reset Password?',
                html: `Send password reset link to <strong>${name}</strong>?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#ffc107',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-key"></i> Yes, Send Reset Link',
                cancelButtonText: '<i class="fas fa-times-circle"></i> Cancel',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Reset Link Sent!',
                        text: 'Password reset link has been sent to the user\'s email.',
                        confirmButtonColor: '#1a4d2e'
                    });
                }
            });
        }
    </script>
@endpush
