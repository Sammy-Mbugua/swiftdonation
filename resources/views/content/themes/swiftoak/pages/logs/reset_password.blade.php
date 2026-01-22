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
                                        <p>Provide the email address associated with your account to recover your password.
                                        </p>
                                    </div>
                                </div>
                                {{-- {{ dd($links->resetpassword) }} --}}
                                
                                <form action="{{ url($links->resetpassword) }}" method="POST">
                                    @csrf
                                    <!-- Notification -->
                                    {!! $notify !!}

                                    <div class="row form-outline my-3">
                                        <label class="col-sm-2 col-form-label" for="email">Email<span
                                                class="text-danger">*</span></label></label>
                                        <div class="col-sm-10">
                                            <input type="email" id="email" class="form-control" name="email"
                                                value="{{ old('email') }}" placeholder="Email address"/>
                                        </div>
                                        @error('email')
                                            <span class="error">{{ $errors->first('email') }}</span>
                                        @enderror

                                    </div>

                                    <div class="d-flex justify-content-left gap-2 mb-3 mt-4">

                                      <button type="submit" class="btn btn-outline-danger">Reset Password</button>
    
                                      <a href="{{ $links->loginview }}" class="btn btn-outline-danger">Rememberd Password</a>
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
