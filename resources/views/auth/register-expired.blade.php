@extends('layouts.guest')

@section('title', 'Invitation Expired')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg border-0">
                <div class="card-body p-5 text-center">
                    <div class="mb-4">
                        <i class="bi bi-exclamation-triangle-fill text-warning" style="font-size: 4rem;"></i>
                    </div>
                    <h2 class="fw-bold mb-3">Invitation Link Expired</h2>
                    <p class="text-muted mb-4">
                        This invitation link has either expired or already been used. 
                        Please contact your instructor for a new invitation link.
                    </p>
                    <a href="{{ route('login') }}" class="btn btn-primary">
                        <i class="bi bi-arrow-left"></i> Go to Login
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
