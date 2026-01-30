<!-- Success_blade -->
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
                                Donation Successful
                            </h3>
                            <hr />
                            <div class="alert alert-success" role="alert">
                                <p>Your donation was successful.</p>
                                <p></p>
                                <p>Your donation has been received by the creator. We appreciate your support</p>
                            </div>
                            <p>
                                {{-- <a class="btn btn-success"
                                    href="{{ url('profile/' . $username) }}">
                                    <i class="fi-rs-box-alt mr-10"></i> View profile
                                </a> --}}
                            </p>
                        </div>
                    </div>
                </div>
            </div> <!-- Row END -->
        </div>
        <!-- Container END -->
    </section>
@endsection
