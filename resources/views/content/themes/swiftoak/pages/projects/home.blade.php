@extends("$theme_dir.layouts.$layoutName")

@section('content')
    <!-- Hero Section -->
    <section id="hero" class="hero section light-background">

        <img src="{{ asset($theme_assets) }}/img/hero-bg.jpg" alt="" data-aos="fade-in">


        <section id="about" class="about section">
            <div class="container">
                <div class="row h-100 flex-column-reverse flex-md-row ">
                    <div class="col-md-6 h-auto  rounded" data-aos="fade-up" data-aos-delay="100">
                        <div class="card py-3 px-5 hero-contents">
                            <div class="card-body text-center">
                                <h3>Welcome to swift Oak Donations.</h3>
                                <p class="fs-5 ">It's a member to member donation. You join by donating
                                    <strong>100KES</strong>. You can choose to create your account for free and Start
                                    inviting people to donate to you.
                                </p>
                                <p>All the donated amounts belong to you. We only charge withdraw cost. It's a one-time
                                    donation. The invited members can choose what to donate.</p>
                                <div class="mt-4 d-flex justify-content-center justify-content-lg-center">
                                    @auth
                                        <a href="{{ url('donate') }}" class="btn btn-outline-danger">Donate</a>
                                    @else
                                        <a href="{{ $links->loginview }}" class="btn btn-outline-danger">Donate</a>
                                    @endauth
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3 mb-md-0" data-aos="fade-up" data-aos-delay="250">
                        <div class="service-item">
                            <div class="imgg">
                                <img src="{{ asset($theme_assets) }}/img/donate2.png" class=" hero-pic img-fluid rounded-3"
                                    alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </section>

@endsection
