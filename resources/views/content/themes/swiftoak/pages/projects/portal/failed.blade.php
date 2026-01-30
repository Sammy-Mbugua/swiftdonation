<!-- failed_blade -->
{{-- Extend the Layout --}}
@extends("$theme_dir.layouts.$layoutName")

{{-- Content --}}
@section('content')
    <!-- Hero Section -->
    <section class="min-vh-100 gradient-form" style="background-color: #eee;">
        <div class="container  py-5 h-100">            
                <div class="row g-4">
                    <!-- Main content START -->
                    <div class="col-lg-12 vstack gap-4">
                        <div class="card">
                            <div class="card-body m-3">
                                <h3 class="d-flex">
                                    Payment Failed
                                </h3>
                                <hr />
                                <div class="alert alert-danger" role="alert">
                                    <p>Your payment has failed to validate.</p>
                                    <p></p>
                                </div>
                                <p>
                                    <a class="btn btn-dark" href="{{ url('payment/confirm?oid=' . $chpter_checkout_id) }}">
                                        <i class="fi-rs-box-alt mr-10"></i> Confirm Payment Again
                                    </a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div> <!-- Row END -->            
            <!-- Container END -->
        </div>
    </section>
@endsection
