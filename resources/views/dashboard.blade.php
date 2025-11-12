@extends('layouts.app')

@section('title', 'Dashboard - ATIN Admin')

@section('content')
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h2 class="mb-0">
                    <i class="bi bi-speedometer2 me-2"></i>
                    Dashboard
                </h2>
                <p class="text-muted mb-0 mt-1">Welcome back, {{ session('user_name') }}! ({{ session('user_role') }})</p>
            </div>
            <div class="col-md-6 text-end">
                <div class="text-muted">
                    Logged in as: <strong>{{ session('username') }}</strong>
                </div>
            </div>
        </div>
    </div>
@endsection