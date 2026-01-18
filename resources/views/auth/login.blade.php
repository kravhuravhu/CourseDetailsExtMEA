@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg border-0 rounded-lg mt-5">
                <div class="card-header bg-primary text-white">
                    <h3 class="text-center font-weight-light my-2">
                        <i class="fas fa-graduation-cap mr-2"></i>
                        CourseDetailsExtMEA Login
                    </h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="form-floating mb-3">
                            <input class="form-control @error('email') is-invalid @enderror" 
                                   id="email" type="email" name="email" 
                                   value="{{ old('email') }}" 
                                   placeholder="name@example.com" required autofocus>
                            <label for="email">Email Address</label>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-floating mb-3">
                            <input class="form-control @error('password') is-invalid @enderror" 
                                   id="password" type="password" 
                                   name="password" placeholder="Password" required>
                            <label for="password">Password</label>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" id="remember" type="checkbox" name="remember">
                            <label class="form-check-label" for="remember">Remember Me</label>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                            <a class="small" href="#">Forgot Password?</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-sign-in-alt mr-2"></i> Login
                            </button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center py-3">
                    <div class="small">
                        <strong>Default Credentials:</strong><br>
                        Email: admin@coursedetailsmea.com<br>
                        Password: admin123
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection