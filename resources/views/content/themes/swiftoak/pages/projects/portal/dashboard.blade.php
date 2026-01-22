{{-- Extend the Layout --}}
@extends("$theme_dir.layouts.$layoutName")

{{-- Content --}}
@section('content')
    <section class="h-100 gradient-form" style="background-color: #eee;">

        <!-- Stats Section -->
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
                                {{-- <a href="{{url('subs/packages')}}"><p>Donate</p></a> --}}
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
        <div class="container py-5 h-100">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-xl-6 ">
                    <div class="card rounded-3 p-3 text-black">
                        <div class="d-flex justify-content-between">
                            <h4 class="mt-1 mb-5 pb-1">Swift Oak Donations</h4>

                            <a href="{{ env('FACEBOOK_URL') }}" target="_blank">
                                <div class="d-flex justify-content-center">
                                    <i class="fab fa-facebook-square fs-5 text-primary"></i>
                                    <h4 class="fs-6 mx-2 text-capitalize">Join our Facebook Group</h4>
                                </div>
                            </a>
                        </div>

                        <p class="mt-1 mb-5 pb-1">Your Referral Link: {{ $getReferralLink }}</p>
                        <input type="text" value="{{ $getReferralLink }}" readonly>
                        <button class="col-2 mt-2" onclick="copyReferralLink()">Copy</button>
                    </div>
                </div>
            </div>
        </div>

    </section>

    <script>
        function copyReferralLink() {
            var copyText = document.querySelector("input");
            copyText.select();
            document.execCommand("copy");
            alert("Referral link copied!");
        }
    </script>
@endsection
