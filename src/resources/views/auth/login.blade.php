@extends('layouts.guest')

@section('content')
    <div class="container-fluid p-0" style="height: 100vh; overflow: hidden;">
        <div class="row g-0" style="height: 100%;">
            <div class="col-lg-4 col-md-5 d-flex align-items-center justify-content-center bg-white">
                <form action="{{ route('login') }}" method="POST">
                    @csrf
                    <div class="login-form-container" style="width: 100%; max-width: 420px; padding: 3rem 2.5rem;">

                        <div class="text-center mb-5">
                            <h2 class="fw-bold mb-2" style="color: #1a4d2e; font-size: 2rem; letter-spacing: -0.5px;">
                                RestoTrack
                            </h2>
                            <p class="text-muted mb-0" style="font-size: 0.95rem;">Welcome back! Please login to your
                                account.
                            </p>
                        </div>

                        <div class="mb-4">
                            <label for="email" class="form-label mb-2"
                                style="color: #2c3e50; font-size: 0.9rem; font-weight: 600;">
                                Email Address <span style="color: #e74c3c;">*</span>
                            </label>
                            <input type="email" class="form-control form-control-lg" id="email" name="email"
                                placeholder="you@example.com" required
                                style="padding: 0.85rem 1rem; border-radius: 10px; border: 1.5px solid #e0e0e0; background-color: #fafafa; font-size: 0.95rem; transition: all 0.3s ease;">
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label mb-2"
                                style="color: #2c3e50; font-size: 0.9rem; font-weight: 600;">
                                Password <span style="color: #e74c3c;">*</span>
                            </label>
                            <input type="password" class="form-control form-control-lg" id="password" name="password"
                                placeholder="Enter your password" required
                                style="padding: 0.85rem 1rem; border-radius: 10px; border: 1.5px solid #e0e0e0; background-color: #fafafa; font-size: 0.95rem; transition: all 0.3s ease;">
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="remember" name="remember"
                                    style="width: 18px; height: 18px; cursor: pointer;">
                                <label class="form-check-label ms-1" for="remember"
                                    style="color: #5a6c7d; font-size: 0.9rem; cursor: pointer;">
                                    Remember me
                                </label>
                            </div>
                            {{-- <a href="#" class="text-decoration-none"
                            style="color: #1a4d2e; font-size: 0.9rem; font-weight: 500;">
                            Forgot password?
                        </a> --}}
                        </div>

                        <button type="submit" class="btn btn-lg w-100 mb-4 login-btn"
                            style="background: linear-gradient(135deg, #1a4d2e 0%, #2d7a4e 100%); color: white; padding: 0.9rem; border-radius: 10px; font-weight: 600; border: none; font-size: 1rem; box-shadow: 0 4px 12px rgba(26, 77, 46, 0.2); transition: all 0.3s ease;">
                            Login
                        </button>

                        <div class="position-relative mb-4">
                            <hr style="border-color: #e0e0e0;">
                            <span class="position-absolute top-50 start-50 translate-middle px-3 bg-white text-muted"
                                style="font-size: 0.85rem;">
                                OR
                            </span>
                        </div>

                        <button type="button"
                            class="btn btn-lg w-100 d-flex align-items-center justify-content-center google-btn"
                            style="padding: 0.85rem; border-radius: 10px; border: 1.5px solid #dadce0; background-color: white; transition: all 0.3s ease; font-size: 0.95rem;">
                            <img src="https://www.google.com/favicon.ico" alt="Google"
                                style="width: 20px; height: 20px; margin-right: 12px;">
                            <span style="color: #3c4043; font-weight: 500;">Continue with Google</span>
                        </button>

                        <div class="text-center mt-4">
                            <p class="mb-0" style="color: #6c757d; font-size: 0.9rem;">
                                Don't have an account?
                                <a href="#" class="text-decoration-none fw-semibold" style="color: #1a4d2e;">Sign
                                    up</a>
                            </p>
                        </div>

                    </div>
                </form>
            </div>

            <div class="col d-none d-md-block" style="height: 100%;">
                <div
                    style="width: 100%; height: 100%; background-image: url('{{ asset('images/login-image.svg') }}'); background-size: cover; background-position: center; background-repeat: no-repeat;">
                </div>
            </div>
        </div>
    </div>

    <style>
        .form-control:focus {
            border-color: #1a4d2e;
            box-shadow: 0 0 0 3px rgba(26, 77, 46, 0.1);
            background-color: #fff;
            outline: none;
        }

        .form-control::placeholder {
            color: #adb5bd;
            font-size: 0.9rem;
        }

        .login-btn:hover {
            background: linear-gradient(135deg, #153d24 0%, #256b42 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(26, 77, 46, 0.3);
        }

        .login-btn:active {
            transform: translateY(0);
        }

        .google-btn:hover {
            background-color: #f8f9fa;
            border-color: #bdc3c7;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .form-check-input:checked {
            background-color: #1a4d2e;
            border-color: #1a4d2e;
        }

        .form-check-input:focus {
            box-shadow: 0 0 0 3px rgba(26, 77, 46, 0.15);
            border-color: #1a4d2e;
        }

        a:hover {
            text-decoration: underline !important;
        }

        @media (max-width: 768px) {
            .login-form-container {
                padding: 2rem 1.5rem !important;
            }
        }
    </style>
@endsection
