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
                                Payment Validating
                            </h3>
                            <hr />
                            <div class="row mb-1">
                                <div class="col-12">
                                    <label for="" class="form-label">
                                        If your payment has not been confirmed yet, please click the button below to
                                        confirm
                                        your payment.
                                    </label>
                                </div>
                            </div>

                            <hr class="mb-4 mt-0 mb-3">

                            <a class="btn btn-dark" href="{{ url('payment/confirm?oid=' . $chpter_checkout_id) }}">
                                <i class="fi-rs-box-alt mr-10"></i> Confirm Payment
                            </a>
                        </div>
                    </div>
                </div>
            </div> <!-- Row END -->
        </div>
    </section>
    <!-- Container END -->



    </section>
    <script>
        window.addEventListener('load', () => {
            Swal.fire({
                title: 'Loading...',
                html: 'Confirming payment. Please wait!!',
                timer: 30000,
                timerProgressBar: true,
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                },
                willClose: () => {
                    // location.reload();
                    window.location.href = '<?= url('payment/confirm?oid=' . $chpter_checkout_id) ?>';
                }
            });
        });
    </script>
@endsection
