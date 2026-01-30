<!-- Refarrals_blade -->
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
                                <p>Total Earned: <strong class="text-success">{{ number_format($total, 2, '.', '') }}</strong></p>

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
        {{-- {{dd($name)}} --}}
        {{-- <section> --}}
        <div class="container py-5 h-100">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-xl-8 ">
                    <div class="card rounded-3 p-3 text-black">
                        @if ($name == null)
                            <h4 class="mt-1 mb-5 pb-1">You were not referred <span
                                    class="text-success">{{ '' }}</span> <br>
                                <hr>
                            </h4>
                        @else
                            <h4 class="mt-1 mb-5 pb-1">You were referred by: <span
                                    class="text-success">{{ $name }}</span> <br>
                                <hr>
                            </h4>
                        @endif

                        <table class="table">
                            @if ($entry_list->count() > 0)
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- {{dd($entry_list)}} --}}
                                    @foreach ($entry_list as $list)
                                        {{-- {{dd($list)}} --}}
                                        <tr>
                                            <td>{{ $list->id }}</td>
                                            <td>{{ $list->this_refer->name }}</td>
                                            <td>{{ date('d M, Y H:i:s', strtotime($list->created_at)) }}</td>

                                            @if ($list->status == 0)
                                                <td><span class="badge bg-warning">{{ 'Not donated' }} </span></td>
                                            @else
                                                <td><span class="badge bg-success">{{ 'Donated' }} </span></td>
                                            @endif

                                            @if ($list->this_billing == null)
                                                <td><span class="text-danger">{{ 'n/a' }} </span></td>
                                            @else
                                                <td>{{ $list->this_billing->total }}</td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <td>{{ 'Total' }}</td>
                                    <td>{{ '' }}</td>
                                    <td>{{ '' }}</td>
                                    <td>{{ '' }}</td>
                                    <td>{{ number_format($total, 2, '.', '') }}</td>
                                </tfoot>
                            @else
                                <!-- Custom Alert Message -->
                                <tr>
                                    <td>

                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <h3><i class="fas fa-user-friends"></i> No Referrals Yet!</h3>
                                        <p>Share your Donation link and earn rewards.</p>
                                        {{-- <a href="#" class="btn btn-success"><i class="fas fa-share"></i> Invite Now</a> --}}
                                        <div class="col">
                                            <div class="col-md-4">
                                                <button class="btn btn-success mb-3" onclick="copyReferralLink()"><i
                                                        class="fas fa-share"></i>Copy link</button>
                                            </div>

                                            <div class="col-md-3" id="copyAlert"
                                                style="display: none; background: #28a745; color: white; padding: 10px; border-radius: 5px;">
                                                Donation link copied!
                                            </div>

                                        </div>
                                    </td>
                                </tr>
                            @endif
                        </table>

                        <div class="">
                            <p>Share your donation link and earn more.</p>
                            {{-- <a href="#" class="btn btn-success"><i class="fas fa-share"></i> Invite Now</a> --}}
                            <div class="col">
                                <div class="col-md-4">
                                    <button class="btn btn-success mb-3" onclick="copyReferralLink()"><i
                                            class="fas fa-share"></i>Copy link</button>
                                </div>

                                <div class="col-md-3" id="copyAlert"
                                    style="display: none; background: #28a745; color: white; padding: 10px; border-radius: 5px;">
                                    Donation link copied!
                                </div>

                            </div>
                        </div>

                        <p id="referralLink">{{ $getReferralLink }}</p>


                    </div>
                </div>
            </div>
        </div>
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
