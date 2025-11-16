@extends('layouts.admin.main')

@section('container')

<!-- partial -->

<div class="content-wrapper pb-0">
    <div class="page-header flex-wrap">
        <h3 class="mb-0"> {{ $title ?? 'Manajemen Pengguna' }}
            <span class="pl-0 h6 pl-sm-2 text-muted d-inline-block">
                Profile & Settings
            </span>
        </h3>
    </div>

    <!-- Main Content -->
    <div class="row">
        <div class="col-xl-12 col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    {{-- Livewire Component --}}
                    <livewire:wali.profil-pengguna />
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        :root {
            --primary-color: #3b71ca;
            --secondary-color: #9fa6b2;
            --success-color: #14a44d;
            --border-color: #e5e7eb;
            --bg-light: #f8f9fa;
        }

        .main-content {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            padding: 30px;
        }

        .profile-header {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border-color);
        }

        .profile-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background-color: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            font-weight: bold;
            margin-right: 20px;
        }

        .profile-info h2 {
            margin: 0;
            font-weight: 600;
        }

        .profile-info p {
            margin: 5px 0 0;
            color: var(--secondary-color);
        }

        .section-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #2d3748;
        }

        .upload-section {
            background-color: var(--bg-light);
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
        }

        .upload-btn {
            background-color: white;
            border: 1px dashed var(--border-color);
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
        }

        .upload-btn:hover {
            border-color: var(--primary-color);
        }

        .upload-icon {
            font-size: 24px;
            color: var(--secondary-color);
            margin-bottom: 10px;
        }

        .form-label {
            font-weight: 500;
            margin-bottom: 8px;
        }

        .form-control,
        .form-select {
            border-radius: 8px;
            padding: 10px 15px;
            border: 1px solid var(--border-color);
        }

        .form-control[readonly] {
            background-color: #f8f9fa;
            cursor: not-allowed;
        }

        .progress-section {
            background-color: var(--bg-light);
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
        }

        .progress {
            height: 8px;
            margin-bottom: 15px;
        }

        .progress-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid var(--border-color);
        }

        .progress-item:last-child {
            border-bottom: none;
        }

        .progress-percent {
            font-weight: 600;
            color: var(--primary-color);
        }

        .bio-section {
            margin-bottom: 30px;
        }

        .bio-text {
            line-height: 1.6;
            color: #4a5568;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 500;
        }

        .btn-outline {
            background-color: white;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 500;
            color: #4a5568;
        }

        .btn-outline-light-muted {
            background-color: white;
            border: 1px solid var(--border-color);
            color: #6c757d;
        }

        .btn-outline-light-muted:hover {
            background-color: #f8f9fa;
            border-color: var(--border-color);
            color: #495057;
        }

        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .dropdown-menu {
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border: 1px solid var(--border-color);
        }

        .dropdown-item {
            padding: 10px 15px;
            transition: all 0.2s;
        }

        .dropdown-item:hover {
            background-color: var(--bg-light);
        }

        .dropdown-item i {
            font-size: 16px;
            width: 20px;
        }

        .modal-content {
            border-radius: 12px;
            border: none;
        }

        .modal-header {
            background-color: var(--bg-light);
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
            border-bottom: 1px solid var(--border-color);
        }

        .modal-footer {
            border-top: 1px solid var(--border-color);
        }

        hr {
            opacity: 0.3;
        }

        /* Style untuk toggle password button */
        .position-relative .btn:hover i {
            color: #0d6efd;
        }

        .position-relative .btn:focus {
            outline: none;
            box-shadow: none;
        }

        /* Ukuran icon */
        .mdi-eye,
        .mdi-eye-off {
            font-size: 18px;
            color: #6c757d;
            transition: color 0.2s;
        }

        /* Input dengan icon */
        .position-relative input.form-control {
            padding-right: 2.5rem !important;
        }
    </style>
    @endpush
</div>

<!-- content-wrapper ends -->
<!-- main-panel ends -->

@endsection