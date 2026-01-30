<!-- Packages_blade -->
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

        <div class="pricing-table mt-5">

            {{-- {{ dd($package) }} --}}

            @if ($packages->count() > 0)
                @foreach ($packages as $package)
                    {{-- {{ dd($package) }} --}}
                    <div class="ptable-item">
                        <form name="form-input" action="#" class="form-horizontal custom-essay" method="post"
                            accept-charset="utf-8"enctype="multipart/form-data" autocomplete="off" id="deleteInfo">
                            <div class="ptable-single">
                                <div class="ptable-header">
                                    <div class="ptable-title">
                                        <h2>{{ $package->duration }} Days Package</h2>
                                    </div>
                                    <input type="hidden" id="amount" name="amount" value="{{ $package->price }}">
                                    <input type="hidden" name="subscription" id="subscription" value="{{ $package->id }}">
                                    <div class="ptable-price">
                                        <h2>
                                            <span class="icon icon-sm rounded-circle">
                                                <img class="img"
                                                    src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-login-form/lotus.webp"
                                                    alt="">
                                            </span>
                                            {{ $package->price }}
                                        </h2>
                                    </div>
                                    <small class="text-white">Package ID: {{ $package->code }}</small><br>
                                </div>

                                <div class="ptable-body">
                                    <div class="ptable-description">


                                        <h2>{{ strtoupper($package->title) }} </h2>

                                        <hr>
                                        <p class="pt-3">{{ $package->description }} </p>


                                    </div>
                                </div>
                                <hr>
                                <div class="pb-3 d-none">
                                    <p class="text-center">
                                        <span>Status: </span>
                                        <span class="badge rounded-pill alert-danger text-danger">
                                            Not Donated
                                        </span>
                                    </p>
                                    <hr>
                                </div>

                                <div class="ptable-footer">
                                    <div class="ptable-action">
                                        <button type="button" onclick="donate(form)"
                                            class="btn btn-primary">Donate</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                @endforeach
            @else
                <div class="ptable-item">
                    <h3><i class="fas fa-user-friends"></i> No package Yet!</h3>
                </div>
            @endif
            @include("$theme_dir.others.modal.subscribemodal")
        </div>

    </section>




    <script>
        function copyReferralLink() {
            var copyText = document.querySelector("input");
            copyText.select();
            document.execCommand("copy");
            alert("Referral link copied!");
        }

        const donate = (this_form) => {

            // amount
            let amt = this_form.querySelector("#amount").value
            // subscrption id
            let sub = this_form.querySelector("#subscription").value

            // console.log(amt, sub)

            const withdrawalcodeModal = new bootstrap.Modal(document.getElementById('subscribemodal'));
            document.getElementById('amount_m').value = amt
            document.getElementById('subs').value = sub
            document.getElementById('modalamount').value = amt
            // console.log('withdrawalcodeModal', withdrawalcodeModal)
            withdrawalcodeModal.show();

            // console.log('action url ' + this_form.getAttribute("action"))
            // Url
            let api_url = this_form.getAttribute("action")
            // fetch
            // Loader.show();

        }

        const send_donate = (this_form, redirect_url = "") => {

            let subscr = this_form.querySelector("#subs").value
            let user = this_form.querySelector("#user").value
            let amount = this_form.querySelector("#modalamount").value
            let phone = this_form.querySelector('#phone').value


            // console.log(subscr, user, amount, phone)
            // Submit using fetch
            const formData = new FormData()
            formData.append("package", subscr)
            formData.append("user", user)
            formData.append('amt', amount)
            formData.append('phone', phone)

            // console.log('from data from withdrawal '+ formData)
            // console.log('action url ' + this_form.getAttribute("action"))
            // Url
            let api_url = this_form.getAttribute("action")
            // fetch
            // Loader.show();
            fetch(`${api_url}`, {
                    method: "POST",
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData,
                })
                .then((response) => response.json())
                .then((data) => {

                    if (data.status) {
                        console.log('success message', data.response.message)
                        successNotifyState(
                            "Success!", data.response.message,
                        );
                        this_form.reset();
                        console.log('success message', data)
                        redirect_url = `{{ url('/pay_load') }}` + '/' + data.response.billing
                        console.log('redirect url', redirect_url)
                        window.location.href = redirect_url
                        // show modal
                        // const withdrawalcodeModal = new bootstrap.Modal(document.getElementById('withdrawalcode'));
                        // console.log('withdrawalcodeModal', withdrawalcodeModal)
                        // withdrawalcodeModal.show();

                        // window.location.reload();
                        // window.location.href = redirect_url;
                    } else {

                        let errorMessage = "Error!\n";

                        if (data.response.message && typeof data.response.message === 'object') {
                            // Loop through all errors
                            errorMessage += Object.keys(data.response.message).map(key => {
                                let messages = data.response.message[key];

                                // Ensure it's an array before calling .join()
                                if (Array.isArray(messages)) {
                                    return `${key}: ${messages.join(', ')}`;
                                } else {
                                    return `${key}: ${messages}`;
                                }
                            }).join("\n"); // Separate errors by a new line for better readability
                        } else {
                            errorMessage += data.response.message || "An unknown error occurred.";
                        }

                        errorNotifyState("Error!", errorMessage);
                    }
                    // Loader.hide();
                })
                .catch((error) => {
                    console.error("Error:", error);
                    // Loader.hide();
                });
        }

        /**
         * Success Message
         */
        const successNotifyState = (
            sms = "Success!",
            this_resp = null
        ) => {
            let message = `
				<div class="alert alert-success alert-dismissible fade show" role="alert">
					<strong>${sms}</strong> <p>${this_resp}</p>
				</div>`;

            // response
            let resp = document.querySelector("#resp-notify")

            resp.innerHTML = "";
            resp.innerHTML = message;

            // Close the modal in 5 second
            // setTimeout(() => {
            //     $("#adminDraft").modal("hide");
            // }, 3000);
        };


        /**
         * Error Message
         */
        const errorNotifyState = (
            sms = " ",
            this_resp = null
        ) => {

            console.log(sms, this_resp)
            let message = `
				<div class="alert alert-danger alert-dismissible fade show" role="alert">
					<strong>${sms}</strong> <p>${this_resp}</p>
				</div>`;

            // response
            let resp = document.querySelector("#resp-notify")

            resp.innerHTML = "";
            resp.innerHTML = message;

            // Close the modal in 5 second
            // setTimeout(() => {
            //     $("#adminDraft").modal("hide");
            // }, 5000);
        };
    </script>
@endsection
