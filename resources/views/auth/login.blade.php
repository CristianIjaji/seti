@extends('layouts.app')

@section('content')
    <div id="carouselExampleSlidesOnly" class="carousel slide carousel-fade" data-bs-ride="carousel">
        <div class="carousel-inner vh-100">
            <div class="carousel-item active">
                <img src="../images/bg-1.jpg" class="d-block w-100 vh-100 img-carousel" alt="...">
            </div>
            <div class="carousel-item">
                <img src="../images/bg-2.jpg" class="d-block w-100 vh-100 img-carousel" alt="...">
            </div>
            <div class="carousel-item">
                <img src="https://www.ecosiete.com/wp-content/uploads/2020/10/ADFSDVS-2048x1400.jpg" class="d-block w-100 vh-100 img-carousel" alt="...">
            </div>
        </div>
    </div>
    <div class="position-absolute w-100" style="top: 0; z-index: 1;">
        <div class="row vh-100">
            <div class="col-2 col-sm-2 col-md-3 col-lg-4 col-xl-4"></div>
            <div class="col-8 col-sm-8 col-md-6 col-lg-4 col-xl-4 my-auto">
                <form action="{{ route('login') }}" method="POST" id="login-form" class="p-4 border rounded shadow-lg" style="background-color: #ffffff59;">
                    <div class="col-12 py-3">
                        <label for="" class="fw-bold fs-4 mx-auto">Iniciar Sesión</label>

                        <div class="alert alert-danger alert-dismissible pb-0 my-2" role="alert"></div>
                    </div>
                    @csrf

                    <div class="d-flex rounded-pill border mb-3 bg-white">
                        <i class="fa-solid fa-at fs-4 p-3"></i>
                        <input id="email" type="email" class="form-control bg-transparent border-0 @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder="correo" required autocomplete="email" autofocus>
                    </div>

                    <div class="d-flex rounded-pill border mb-3 bg-white">
                        <i class="fa-solid fa-lock fs-4 p-3"></i>
                        <input id="password" type="password" class="form-control bg-transparent border-0 @error('password') is-invalid @enderror" name="password" placeholder="contraseña" required autocomplete="current-password">
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                <label class="form-check-label" for="remember">
                                    {{ __('Remember Me') }}
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-0">
                        <div class="col-md-12 modal-footer">
                            <button type="submit" class="btn bg-primary bg-gradient fw-bolder text-light">
                                {{ __('Login') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-2 col-sm-2 col-md-3 col-lg-4 col-xl-4"></div>
        </div>
    </div>
    {{-- <div class="container-fluid background">
        <div class="row vh-100">
            <div class="col-2 col-sm-2 col-md-3 col-lg-4 col-xl-4"></div>
            <div class="col-8 col-sm-8 col-md-6 col-lg-4 col-xl-4 my-auto">
                <div class="alert alert-danger alert-dismissible pb-0" role="alert"></div>
                <form id="login-form" class="p-4 bg-white border rounded shadow-lg bg-body">
                    <div class="col-12 text-center">
                        <label for="" class="fw-bold fs-3 mx-auto">Iniciar Sesión</label>
                    </div>
                    @csrf

                    <div class="d-flex rounded-pill border mb-3 bg-white">
                        <i class="fa-solid fa-at fs-4 p-3"></i>
                        <input id="email" type="email" class="form-control bg-transparent border-0 @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder="correo" required autocomplete="email" autofocus>
                    </div>

                    <div class="d-flex rounded-pill border mb-3 bg-white">
                        <i class="fa-solid fa-lock fs-4 p-3"></i>
                        <input id="password" type="password" class="form-control bg-transparent border-0 @error('password') is-invalid @enderror" name="password" placeholder="contraseña" required autocomplete="current-password">
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                <label class="form-check-label" for="remember">
                                    {{ __('Remember Me') }}
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-0">
                        <div class="col-md-12 modal-footer">
                            <button type="submit" class="btn bg-primary bg-gradient fw-bolder text-light">
                                {{ __('Login') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-2 col-sm-2 col-md-3 col-lg-4 col-xl-4"></div>
        </div>
    </div> --}}
@endsection