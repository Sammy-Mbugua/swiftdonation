@extends("$theme_dir.layouts.$layoutName")

@section('content')
    <section class="h-100 gradient-form" style="background-color: #eee;">
        <div class="container py-5 h-100">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-xl-10">
                    <div class="card rounded-3 text-black">
                        <div class="row g-0">
                            <div class="col-lg-6">
                                <div class="card-body p-md-5 mx-md-4">

                                    <div class="text-center">
                                        <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-login-form/lotus.webp"
                                            style="width: 185px;" alt="logo">
                                        <h4 class="mt-1 mb-5 pb-1">Swift Oak Donations</h4>
                                    </div>
                                    {{-- {{dd($links)}} --}}
                                    <form action="{{ url($links->register) }}" method="POST">
                                        @csrf

                                        <!-- Notification -->
                                        {!! $notify !!}
                                        <p>Create account</p>

                                        <input type="hidden" name="ref" value="{{ $ref ?? 'null' }}">
                                        <div data-mdb-input-init class="form-outline mb-4">
                                            <input type="fullname" id="form2Example11" class="form-control" name="fullname"
                                                value="{{ old('fullname') }}" placeholder="E.g Margaret Nduta " />
                                            @error('fullname')
                                                <span class="error">{{ $errors->first('fullname') }}</span>
                                            @enderror
                                            <label class="form-label" for="form2Example11">Full name</label>
                                        </div>

                                        <div data-mdb-input-init class="form-outline mb-4">
                                            <input type="email" id="form2Example11" class="form-control" name="email"
                                                value="{{ old('email') }}" placeholder="Email address" required />
                                            @error('email')
                                                <span class="error">{{ $errors->first('email') }}</span>
                                            @enderror
                                            <label class="form-label" for="form2Example11">Email</label>
                                        </div>

                                        <div data-mdb-input-init class="form-outline mb-4">
                                            <input type="phone" id="form2Example11" class="form-control" name="phone"
                                                value="{{ old('phone') }}" placeholder="E.g 2547 12 345 678" />
                                            @error('phone')
                                                <span class="error">{{ $errors->first('phone') }}</span>
                                            @enderror
                                            <label class="form-label" for="form2Example11">Phone No</label>
                                        </div>

                                        <div data-mdb-input-init class="form-outline mb-4">
                                            <input type="password"
                                                id="form2Example22 @error('password') is-invalid @enderror"
                                                class="form-control" name="password" value="{{ old('password') }}"
                                                placeholder="Enter password" required />

                                            @error('password')
                                                <span class="error">{{ $errors->first('password') }}</span>
                                            @enderror
                                            <label class="form-label" for="form2Example22">Password</label>
                                        </div>

                                        <div data-mdb-input-init class="form-outline mb-4">
                                            <input type="password"
                                                id="form2Example22 @error('password_confirmation') is-invalid @enderror"
                                                class="form-control" name="password_confirmation"
                                                value="{{ old('password_confirmation') }}" placeholder="Confirm password"
                                                required />

                                            @error('password_confirmation')
                                                <span class="error">{{ $errors->first('password_confirmation') }}</span>
                                            @enderror
                                            <label class="form-label" for="form2Example22">Confirm Password</label>
                                        </div>

                                        <div class="d-flex justify-content-left gap-2 mb-3">
                                            <button data-mdb-button-init data-mdb-ripple-init
                                                class="btn btn-primary btn-block fa-lg gradient-custom-2 mb-3"
                                                type="submit">Register</button>

                                        </div>
                                    </form>
                                    <div class="d-flex align-items-center justify-content-center pb-4">
                                        <p class="mb-0 me-2">Already have an account?</p>
                                        <a href="{{ $links->loginview }}" data-mdb-button-init data-mdb-ripple-init
                                            class="btn btn-outline-danger">Login</a>
                                    </div>

                                </div>
                            </div>
                            <div class="col-lg-6 d-flex align-items-center gradient-custom-2">
                                <div class="text-white px-3 py-4 p-md-5 mx-md-4">
                                    <h4 class="mb-4 text-white">We are more than just an organization </h4>
                                    <p class="small mb-0">The withdraw limit is 1000 KES. The withdraw cost is 10% of the
                                        amount to withdraw. To withdraw your account must be activated by donating 100 KES.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
