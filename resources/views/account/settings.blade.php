@extends('layouts.app')

@section('title', 'Account Settings - ATIN')

@push('styles')
<style>
        .settings-card {
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border: none;
            margin-bottom: 20px;
        }

    .admin-note {
        background-color: #fff3cd;
        border-left: 4px solid #ffc107;
    }
</style>
@endpush

@section('content')
@include('components.alerts')

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold" style="color: #06448a;">
            <i class="bi bi-person-gear me-2"></i>Account Settings
        </h2>
    </div>

    @if(!$user)
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle me-2"></i>
            User not found. Please <a href="{{ route('login') }}" class="alert-link">log in again</a>.
        </div>
    @else
    <div class="row">
        <!-- Personal Information -->
        <div class="col-lg-8">
            <div class="card settings-card">
                <div class="card-header settings-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-person-vcard me-2"></i>Personal Information
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('account.settings.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <!-- First Name -->
                            <div class="col-md-6 mb-3">
                                <label for="f_name" class="form-label">First Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('f_name') is-invalid @enderror" 
                                       id="f_name" name="f_name" 
                                       value="{{ old('f_name', $user->f_name) }}" 
                                       placeholder="Enter first name" 
                                       maxlength="100" required>
                                @error('f_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Middle Name -->
                            <div class="col-md-6 mb-3">
                                <label for="m_name" class="form-label">Middle Name</label>
                                <input type="text" class="form-control @error('m_name') is-invalid @enderror" 
                                       id="m_name" name="m_name" 
                                       value="{{ old('m_name', $user->m_name) }}" 
                                       placeholder="Enter middle name" 
                                       maxlength="100">
                                @error('m_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Last Name -->
                            <div class="col-md-6 mb-3">
                                <label for="l_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('l_name') is-invalid @enderror" 
                                       id="l_name" name="l_name" 
                                       value="{{ old('l_name', $user->l_name) }}" 
                                       placeholder="Enter last name" 
                                       maxlength="100" required>
                                @error('l_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Contact Number -->
                            <div class="col-md-6 mb-3">
                                <label for="contactNo" class="form-label">Contact Number</label>
                                <input type="text" class="form-control @error('contactNo') is-invalid @enderror" 
                                       id="contactNo" name="contactNo" 
                                       value="{{ old('contactNo', $user->contactNo) }}" 
                                       placeholder="Enter contact number" 
                                       maxlength="11"
                                       pattern="[0-9]{0,11}"
                                       oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11)">
                                @error('contactNo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Maximum 11 digits</div>
                            </div>

                            <!-- Email -->
                            <div class="col-12 mb-3">
                                <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" 
                                       value="{{ old('email', $user->email) }}" 
                                       placeholder="Enter email address" 
                                       maxlength="255" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>Update Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Password Change -->
            <div class="card settings-card">
                <div class="card-header settings-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-shield-lock me-2"></i>Change Password
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('account.settings.password') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <!-- Current Password -->
                            <div class="col-12 mb-3">
                                <label for="current_password" class="form-label">Current Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control @error('current_password') is-invalid @enderror" 
                                       id="current_password" name="current_password" 
                                       placeholder="Enter current password" 
                                       required>
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- New Password -->
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">New Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                       id="password" name="password" 
                                       placeholder="Enter new password" 
                                       required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Minimum 8 characters</div>
                            </div>
                            

                            <!-- Confirm New Password -->
                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label">Confirm New Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" 
                                       id="password_confirmation" name="password_confirmation" 
                                       placeholder="Confirm new password" 
                                       required>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-key me-2"></i>Change Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- System Information & Admin-Managed Fields -->
        <div class="col-lg-4">
            <!-- System Information -->
            <div class="card settings-card">
                <div class="card-header settings-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-info-circle me-2"></i>System Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <small class="text-muted">User ID:</small>
                            <span class="fw-semibold">{{ $user->id }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <small class="text-muted">Username:</small>
                            <span class="fw-semibold">{{ $user->username }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <small class="text-muted">Role:</small>
                            <span class="fw-semibold">{{ $user->role->name }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <small class="text-muted">Account Status:</small>
                            <span class="fw-semibold">{{ $user->is_active ? 'Active' : 'Inactive' }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <small class="text-muted">Account created:</small>
                            <span class="fw-semibold">{{ $user->created_at->format('M d, Y') }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <small class="text-muted">Last Updated:</small>
                            <span class="fw-semibold">{{ $user->updated_at->format('M d, Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Admin-Managed Fields Note -->
            <div class="card settings-card">
                <div class="card-body admin-note">
                    <div class="d-flex">
                        <i class="bi bi-tools text-warning me-3" style="font-size: 1.5rem;"></i>
                        <div>
                            <h6 class="fw-bold text-warning mb-2">Admin-Managed Fields</h6>
                            <p class="small mb-2">The following fields can only be modified by administrators:</p>
                            <ul class="small mb-0">
                                <li><strong>Username</strong> - Your unique login identifier</li>
                                <li><strong>User Role</strong> - Your system permissions and access level</li>
                            </ul>
                            <p class="small mt-2 mb-0">
                                Contact your system administrator if you need changes to these fields.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Contact number validation
        const contactInput = document.getElementById('contactNo');
        if (contactInput) {
            contactInput.addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11);
            });
        }

        // Password confirmation validation
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('password_confirmation');
        
        function validatePassword() {
            if (password && confirmPassword && password.value !== confirmPassword.value) {
                confirmPassword.setCustomValidity("Passwords don't match");
            } else if (confirmPassword) {
                confirmPassword.setCustomValidity('');
            }
        }

        if (password && confirmPassword) {
            password.addEventListener('input', validatePassword);
            confirmPassword.addEventListener('input', validatePassword);
        }

        // Auto-hide alerts after 5 seconds
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 5000);
        });
    });
</script>
@endpush