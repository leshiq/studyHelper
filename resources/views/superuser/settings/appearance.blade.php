@extends('layouts.sidebar')

@section('title', 'Portal Appearance')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="bi bi-palette"></i> Portal Appearance
        </h2>
        <a href="{{ route('superuser.settings.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Settings
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle"></i> Please fix the following errors:
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <form action="{{ route('superuser.settings.appearance.update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row">
            <div class="col-lg-8">
                <!-- Logo Settings -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0">
                            <i class="bi bi-image"></i> Logo Settings
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="portal_logo" class="form-label">Portal Logo</label>
                            @if($portalLogo)
                                <div class="mb-2">
                                    <img src="{{ asset('portal-assets/logos/' . $portalLogo) }}" alt="Portal Logo" style="max-height: 80px;" class="border rounded p-2">
                                    <div class="form-text">Current logo</div>
                                </div>
                            @endif
                            <input type="file" class="form-control" id="portal_logo" name="portal_logo" accept="image/*">
                            <div class="form-text">Upload a new logo (PNG, JPG, SVG recommended). Displayed in sidebar and login page.</div>
                        </div>
                    </div>
                </div>

                <!-- Login Page Background -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0">
                            <i class="bi bi-box-arrow-in-right"></i> Login Page Background
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small mb-3">
                            <i class="bi bi-info-circle"></i> Fallback order: Image → Gradient → Color → Default
                        </p>

                        <div class="mb-3">
                            <label for="login_bg_image" class="form-label">Background Image</label>
                            @if($loginBgImage)
                                <div class="mb-2">
                                    <img src="{{ asset('portal-assets/backgrounds/' . $loginBgImage) }}" alt="Login Background" style="max-height: 100px; max-width: 200px;" class="border rounded">
                                    <div class="form-text">Current background image</div>
                                </div>
                            @endif
                            <input type="file" class="form-control" id="login_bg_image" name="login_bg_image" accept="image/*">
                            <div class="form-text">Highest priority. If set, this will be used.</div>
                        </div>

                        <div class="mb-3">
                            <label for="login_bg_gradient" class="form-label">Background Gradient (CSS)</label>
                            <input type="text" class="form-control font-monospace" id="login_bg_gradient" name="login_bg_gradient" value="{{ old('login_bg_gradient', $loginBgGradient) }}" placeholder="linear-gradient(135deg, #667eea, #764ba2)">
                            <div class="form-text">Used if no image is set. Example: linear-gradient(135deg, #667eea, #764ba2)</div>
                        </div>

                        <div class="mb-3">
                            <label for="login_bg_color" class="form-label">Background Color</label>
                            <div class="input-group">
                                <input type="color" class="form-control form-control-color" id="login_bg_color" name="login_bg_color" value="{{ old('login_bg_color', $loginBgColor) }}">
                                <input type="text" class="form-control font-monospace" value="{{ old('login_bg_color', $loginBgColor) }}" readonly>
                            </div>
                            <div class="form-text">Used if no image or gradient is set.</div>
                        </div>
                    </div>
                </div>

                <!-- All Pages Background -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0">
                            <i class="bi bi-file-earmark"></i> All Pages Background
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small mb-3">
                            <i class="bi bi-info-circle"></i> Fallback order: Image → Gradient → Color → Default (white)
                        </p>

                        <div class="mb-3">
                            <label for="pages_bg_image" class="form-label">Background Image</label>
                            @if($pagesBgImage)
                                <div class="mb-2">
                                    <img src="{{ asset('portal-assets/backgrounds/' . $pagesBgImage) }}" alt="Pages Background" style="max-height: 100px; max-width: 200px;" class="border rounded">
                                    <div class="form-text">Current background image</div>
                                </div>
                            @endif
                            <input type="file" class="form-control" id="pages_bg_image" name="pages_bg_image" accept="image/*">
                            <div class="form-text">Applied to all authenticated pages.</div>
                        </div>

                        <div class="mb-3">
                            <label for="pages_bg_gradient" class="form-label">Background Gradient (CSS)</label>
                            <input type="text" class="form-control font-monospace" id="pages_bg_gradient" name="pages_bg_gradient" value="{{ old('pages_bg_gradient', $pagesBgGradient) }}" placeholder="linear-gradient(to bottom, #f8f9fa, #e9ecef)">
                            <div class="form-text">Used if no image is set.</div>
                        </div>

                        <div class="mb-3">
                            <label for="pages_bg_color" class="form-label">Background Color</label>
                            <div class="input-group">
                                <input type="color" class="form-control form-control-color" id="pages_bg_color" name="pages_bg_color" value="{{ old('pages_bg_color', $pagesBgColor) }}">
                                <input type="text" class="form-control font-monospace" value="{{ old('pages_bg_color', $pagesBgColor) }}" readonly>
                            </div>
                            <div class="form-text">Used if no image or gradient is set.</div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Settings -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0">
                            <i class="bi bi-layout-sidebar"></i> Sidebar Color/Gradient
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="sidebar_color" class="form-label">Sidebar Background</label>
                            <input type="text" class="form-control font-monospace" id="sidebar_color" name="sidebar_color" value="{{ old('sidebar_color', $sidebarColor) }}" placeholder="linear-gradient(180deg, #667eea 0%, #764ba2 100%)">
                            <div class="form-text">Can be a solid color (#667eea) or gradient (linear-gradient(...))</div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mb-4">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-check-lg"></i> Save Appearance Settings
                    </button>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Preview Card -->
                <div class="card shadow-sm sticky-top" style="top: 20px;">
                    <div class="card-header bg-light border-bottom">
                        <h6 class="mb-0">
                            <i class="bi bi-eye"></i> Quick Preview
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="small text-muted">Login Background</label>
                            <div class="border rounded p-3" style="height: 100px; background: {{ $loginBgGradient ?: $loginBgColor }};"></div>
                        </div>

                        <div class="mb-3">
                            <label class="small text-muted">Sidebar Color</label>
                            <div class="border rounded p-3" style="height: 60px; background: {{ $sidebarColor }};"></div>
                        </div>

                        <div class="mb-3">
                            <label class="small text-muted">Pages Background</label>
                            <div class="border rounded p-3" style="height: 80px; background: {{ $pagesBgGradient ?: $pagesBgColor }};"></div>
                        </div>
                    </div>
                </div>

                <!-- Tips -->
                <div class="card shadow-sm mt-3 border-info">
                    <div class="card-body">
                        <h6 class="text-info">
                            <i class="bi bi-lightbulb"></i> Tips
                        </h6>
                        <ul class="small text-muted mb-0 ps-3">
                            <li>Use high-quality images for best results</li>
                            <li>Test gradients before saving</li>
                            <li>Background images should be optimized for web</li>
                            <li>Logo works best as PNG with transparency</li>
                            <li>Changes apply immediately after saving</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    // Update color input text when color picker changes
    document.querySelectorAll('input[type="color"]').forEach(colorPicker => {
        colorPicker.addEventListener('input', function() {
            const textInput = this.nextElementSibling;
            if (textInput) {
                textInput.value = this.value;
            }
        });
    });
</script>
@endpush
@endsection
