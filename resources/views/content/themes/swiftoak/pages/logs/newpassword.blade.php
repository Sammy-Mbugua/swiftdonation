@extends("$theme_dir.layouts.$layoutName")

@section('content')
    <section id="hero" class="hero section light-background">

        <section id="about" class="about section d-flex align-items-center justify-content-center"
            style="min-height: 80vh;">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-12 " data-aos="fade-up" data-aos-delay="100">
                        <div class="card py-4 px-5 hero-contents text-center">
                            <div class="card-body">
                                <h3>Reset Password.</h3>
                                <div class="row gy-3 mb-2">
                                    <div class="col-8">
                                        <div class="text-center">
                                            <a href="#!">
                                                {{-- <img src="./assets/img/bsb-logo.svg" alt="BootstrapBrain Logo"
                                                    width="175" height="57"> --}}
                                            </a>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        {{-- <p>Please provide a new password.</p> --}}
                                        <p>please enter a new password below. Make sure it is strong and unique.</p>
                                    </div>
                                </div>
                                {{-- {{ dd($code ) }} --}}
                                
                                <form action="{{ url($links->updatepassword) }}" method="POST">
                                    @csrf
                                    <!-- Notification -->
                                    {!! $notify !!}
                                    <input type="hidden" name="code" value="{{ $code ?? 'null' }}">
                                    <div class="row form-outline my-3">
                                        <label class="col-sm-4 col-form-label" for="email">New Password<span
                                                class="text-danger">*</span></label></label>
                                        <div class="col-sm-8">
                                            <input type="password" id="password" class="form-control" name="password"
                                                value="{{ old('password') }}" placeholder="New password"/>
                                        </div>
                                        @error('password')
                                            <span class="error">{{ $errors->first('password') }}</span>
                                        @enderror

                                    </div>

                                    <div class="row form-outline my-3">
                                        <label class="col-sm-4 col-form-label" for="email">Confirm Password<span
                                                class="text-danger">*</span></label></label>
                                        <div class="col-sm-8">
                                            <input type="password" id="password" class="form-control" name="password_confirmation"
                                                value="{{ old('password_confirmation') }}" placeholder="New password_confirmation"/>
                                        </div>
                                        @error('password_confirmation')
                                            <span class="error">{{ $errors->first('password_confirmation') }}</span>
                                        @enderror

                                    </div>                                  

                                    <div class="d-flex justify-content-left gap-2 mb-3 mt-4">

                                      <button type="submit" class="btn btn-outline-danger">Reset Password</button>
    
                                  </div>
                                    
                                </form>
                               
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>



    </section>
@endsection
