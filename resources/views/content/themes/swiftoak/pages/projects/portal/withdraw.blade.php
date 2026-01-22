{{-- Extend the Layout --}}
@extends("$theme_dir.layouts.$layoutName")

{{-- Content --}}
@section('content')
    <style>
        .flash-message {
            animation: fadeOut 3s forwards;
            animation-delay: 3s;
        }

        @keyframes fadeOut {
            to {
                opacity: 0;
                visibility: hidden;
            }
        }
    </style>

    <section class="h-100 gradient-form" style="background-color: #eee;">
        {{-- <div class="position-fixed top-0 start-50 translate-middle-x mt-5 flash-message z-5" style="min-width: 300px;">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        {!! $notify !!}
                        <div class="resp-notify" id="resp-notify"></div>
                    </div>
                </div>
            </div>
        </div> --}}
        <!-- Stats Section -->
        <div class="row position-absolute flash-message top-5 mt-5  start-50 translate-middle-x">
            <div class="col-12">
                <!-- Notification -->
                {!! $notify !!}
                <div class="resp-notify" id ="resp-notify"></div>
            </div>
        </div>
        <div id="stats" class="stats  mt-5 color-gray">

            <div class="container" data-aos="fade-up" data-aos-delay="100">

                <div class="row gy-4">

                    <div class="col-lg-3 col-md-6">
                        <div class="stats-item d-flex align-items-center w-100 h-100">
                            <i class="bi bi-emoji-smile color-blue flex-shrink-0"></i>
                            <div>
                                <span data-purecounter-start="0" data-purecounter-end="232" data-purecounter-duration="1"
                                    class="purecounter"></span>
                                <a href="{{ url('/portal/refarrals') }}">Total Referrals: <strong
                                        class="text-success">{{ $totalreferral }}</strong></a>
                            </div>
                        </div>
                    </div><!-- End Stats Item -->

                    <div class="col-lg-3 col-md-6">
                        <div class="stats-item d-flex align-items-center w-100 h-100">
                            {{-- <i class="bi bi-journal-richtext color-orange flex-shrink-0"></i> --}}
                            <i class="bi bi-currency-dollar color-orange flex-shrink-0"></i>

                            <div>
                                <span data-purecounter-start="0" data-purecounter-end="521" data-purecounter-duration="1"
                                    class="purecounter"></span>
                                <p>Total Earned: <strong
                                        class="text-success">{{ number_format($total, 2, '.', '') }}</strong></p>

                            </div>
                        </div>
                    </div><!-- End Stats Item -->

                    <div class="col-lg-3 col-md-6">
                        <div class="stats-item d-flex align-items-center w-100 h-100">
                            <i class="bi bi-wallet2 color-orange flex-shrink-0"></i>
                            <div>
                                <span data-purecounter-start="0" data-purecounter-end="1463" data-purecounter-duration="1"
                                    class="purecounter"></span>
                                {{-- <a href="{{ url('subs/packages') }}">
                                    <p>Donate</p>
                                </a> --}}
                                <a href="{{ url('donate') }}">
                                    <p>Donate</p>
                                </a>
                            </div>
                        </div>
                    </div><!-- End Stats Item -->

                    <div class="col-lg-3 col-md-6">
                        <div class="stats-item d-flex align-items-center w-100 h-100">
                            <i class="bi bi-cash color-orange flex-shrink-0"></i>
                            <div>
                                <span data-purecounter-start="0" data-purecounter-end="15" data-purecounter-duration="1"
                                    class="purecounter"></span>
                                <a href="{{ url('/withdrawal') }}">
                                    <p>withdrwal</p>
                                </a>
                            </div>
                        </div>
                    </div>
                    <!-- End Stats Item -->

                </div>

            </div>

        </div><!-- /Stats Section -->

        <section class="h-100 gradient-form" style="background-color: #eee;">
            <div class="container py-5 h-100">
                <div class="row d-flex justify-content-center align-items-center h-100 ">
                    <div class="col-md-6 rounded mb-4 mb-md-0">
                        <div class="card py-3 px-5 hero-contents">
                            <div class="card-body text-center">
                                <h3>Withdrawal.</h3>
                                <p class="fs-5 ">The withdraw limit is 1000 KES. The withdraw cost is 10% of the amount to
                                    withdraw.</p>
                                <p>To withdraw your account must be activated by donating <strong>100 KES</strong>.</p>
                                @auth
                                    <div class="mt-4 d-flex justify-content-center justify-content-lg-center">
                                        <a href="{{ url('donate') }}" class="btn btn-outline-danger">Donate</a>
                                    </div>
                                @else
                                    <div class="mt-4 d-flex justify-content-center justify-content-lg-center">
                                        <a href="{{ $links->loginview }}" class="btn btn-outline-danger">Donate</a>
                                    </div>
                                @endauth
                            </div>
                        </div>
                    </div>

                    <div class="position-relative col-md-6 rounded-3 text-black ">
                        <div class="card ">
                            <div class="card-body p-md-5 mx-md-4">
                                {{-- {{dd($links->login)}} --}}
                                <form action="{{ url($links->withdraw) }}" method="POST">
                                    @csrf
                                    <div
                                        class="row position-absolute flash-message top-0 start-50 translate-middle-x d-none">
                                        <div class="col-12">
                                            <!-- Notification -->
                                            {!! $notify !!}
                                            <div class="resp-notify" id ="resp-notify"></div>
                                        </div>
                                    </div>

                                    <div data-mdb-input-init class="form-outline mb-1">
                                        <label class="form-label" for="form2Example11">Withdrawable Amount <strong
                                                class="text-success">{{ $amount_to }}</strong></label>

                                    </div>
                                    <div data-mdb-input-init class="form-outline mb-4">
                                        <label class="form-label" for="form2Example22">phone</label>
                                        <input type="phone" id="phone" class="form-control" name="phone"
                                            value="{{ old('phone') }}" placeholder="Enter phone eg. 07 xx xxx xxx"
                                            required />

                                        @error('phone')
                                            <span class="error">{{ $errors->first('phone') }}</span>
                                        @enderror

                                    </div>

                                    <div class="d-flex align-items-center justify-content-center pb-3">
                                        <button type="submit" data-mdb-button-init data-mdb-ripple-init
                                            class="btn btn-outline-danger">withdraw</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- </section> --}}

    </section>

    <script>
        function copyReferralLink() {
            var copyText = document.getElementById("referralLink").innerText;
            navigator.clipboard.writeText(copyText)
                .then(() => {
                    var alertBox = document.getElementById("copyAlert");
                    alertBox.style.display = "block";

                    setTimeout(() => {
                        alertBox.style.display = "none";
                    }, 700);
                })
                .catch(err => {
                    console.error("Failed to copy: ", err);
                });
        }
    </script>
@endsection
