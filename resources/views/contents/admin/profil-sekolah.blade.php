@extends('layouts.admin.main')

@section('container')

<!-- partial -->

<style>
    :root {
        --primary-color: #1e40af;
        --secondary-color: #64748b;
        --accent-color: #0ea5e9;
        --light-color: #f8fafc;
        --dark-color: #1e293b;
        --success-color: #10b981;
        --border-radius: 12px;
        --box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        --transition: all 0.3s ease;
    }

    body {
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        font-family: 'Inter', 'Segoe UI', system-ui, sans-serif;
        color: #334155;
        min-height: 100vh;
    }

    /* .profile-container {
        max-width: 1200px;
        margin: 2rem auto;
        padding: 0 1.5rem;
    } */

    /* .profile-card {
        background: white;
        border-radius: var(--border-radius);
        box-shadow: var(--box-shadow);
        overflow: hidden;
        margin-bottom: 2rem;
        transition: var(--transition);
    } */

    /* .profile-card:hover {
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.12);
    } */

    .profile-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 2rem;
        border-radius: 5px 5px 0 0;
        color: white;
        /* margin-bottom: 2rem; */
    }

    /* .profile-header {
        background: linear-gradient(135deg, var(--primary-color), #3730a3);
        color: white;
        padding: 3rem 2rem;
        position: relative;
        overflow: hidden;
    } */

    .profile-header::before {
        content: "";
        position: absolute;
        top: 0;
        right: 0;
        width: 200px;
        height: 200px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        transform: translate(30%, -30%);
    }

    .profile-header::after {
        content: "";
        position: absolute;
        bottom: -50px;
        left: -50px;
        width: 150px;
        height: 150px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 50%;
    }

    .profile-avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.2), rgba(255, 255, 255, 0.1));
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        font-weight: bold;
        margin-bottom: 1.5rem;
        border: 4px solid rgba(255, 255, 255, 0.3);
        backdrop-filter: blur(10px);
        position: relative;
        z-index: 2;
    }

    .profile-info h2 {
        margin-bottom: 0.5rem;
        font-weight: 800;
        position: relative;
        z-index: 2;
    }

    .profile-info p {
        opacity: 0.9;
        margin-bottom: 0;
        position: relative;
        z-index: 2;
    }

    .badge-accreditation {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-size: 0.875rem;
        font-weight: 600;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .section-title {
        font-weight: 700;
        color: var(--dark-color);
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid #e2e8f0;
        position: relative;
    }

    .section-title::after {
        content: "";
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 60px;
        height: 2px;
        background: var(--accent-color);
    }

    .upload-section {
        padding: 1rem 0;
        border-bottom: 1px solid #e2e8f0;
    }

    .logo-upload-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 2rem;
        margin-top: 1.5rem;
    }

    .logo-upload-card {
        background: #f8fafc;
        border-radius: var(--border-radius);
        padding: 2rem;
        border: 2px dashed #cbd5e1;
        transition: var(--transition);
        text-align: center;
    }

    .logo-upload-card:hover {
        border-color: var(--accent-color);
        background: rgba(14, 165, 233, 0.05);
        transform: translateY(-2px);
    }

    .logo-upload-icon {
        font-size: 3rem;
        color: var(--secondary-color);
        margin-bottom: 1rem;
    }

    .logo-upload-title {
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: var(--dark-color);
    }

    .logo-upload-desc {
        color: var(--secondary-color);
        font-size: 0.875rem;
        margin-bottom: 1.5rem;
    }

    .info-section {
        padding: 2rem 0;
    }

    .info-item {
        margin-bottom: 0rem;
    }

    .info-item label {
        font-weight: 600;
        color: var(--secondary-color);
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
    }

    .info-item .form-control {
        background-color: white;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 0.75rem 1rem;
        font-weight: 500;
        transition: var(--transition);
    }

    .info-item .form-control:focus {
        border-color: var(--accent-color);
        box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.1);
    }

    .btn-outline-light-muted {
        color: #64748b;
        border-color: #cbd5e1;
        font-weight: 500;
        transition: var(--transition);
    }

    .btn-outline-light-muted:hover {
        color: #475569;
        background-color: #f1f5f9;
        border-color: #94a3b8;
        transform: translateY(-1px);
    }

    .modal-header {
        border-bottom: 1px solid #e2e8f0;
        padding: 1.5rem;
    }

    .modal-footer {
        border-top: 1px solid #e2e8f0;
        padding: 1.25rem 1.5rem;
    }

    .feature-tag {
        display: inline-block;
        background: var(--accent-color);
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 600;
        margin-left: 0.5rem;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        background: #dcfce7;
        color: #166534;
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-size: 0.875rem;
        font-weight: 600;
    }

    .status-badge::before {
        content: "";
        width: 8px;
        height: 8px;
        background: #16a34a;
        border-radius: 50%;
        margin-right: 0.5rem;
    }

    @media (max-width: 768px) {
        .profile-header {
            padding: 2rem 1.5rem;
        }

        .profile-avatar {
            width: 90px;
            height: 90px;
            font-size: 2.25rem;
        }

        .logo-upload-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="content-wrapper pb-0">
    <div class="page-header flex-wrap">
        <h3 class="mb-0"> {{ $title }}
            <span class="pl-0 h6 pl-sm-2 text-muted d-inline-block">
                Profil dan Identitas
            </span>
        </h3>

    </div>

    <!-- LiveWire -->
    <livewire:admin.profil-sekolah />

</div>

<!-- content-wrapper ends -->
<!-- main-panel ends -->

@endsection