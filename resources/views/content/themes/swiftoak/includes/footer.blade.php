<script src="{{ asset($plugin_assets) }}/datepicker/js/datepicker-full.min.js"></script>

<input type="hidden" id="base_url_link" value="{{ url('/') }}">

<script src='{{ asset("$theme_assets") }}/js/main.js'></script>



<!-- Vendor JS Files -->
<script src="{{ asset($theme_assets) }}/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset($theme_assets) }}/vendor/php-email-form/validate.js"></script>
<script src="{{ asset($theme_assets) }}/vendor/aos/aos.js"></script>
<script src="{{ asset($theme_assets) }}/vendor/glightbox/js/glightbox.min.js"></script>
<script src="{{ asset($theme_assets) }}/vendor/purecounter/purecounter_vanilla.js"></script>
<script src="{{ asset($theme_assets) }}/vendor/swiper/swiper-bundle.min.js"></script>
<script src="{{ asset($theme_assets) }}/vendor/imagesloaded/imagesloaded.pkgd.min.js"></script>
<script src="{{ asset($theme_assets) }}/vendor/isotope-layout/isotope.pkgd.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>


{{-- <script type="text/javascript">
	// Datepicker
	const picker = document.querySelectorAll('.date');
	if (picker) {
		// Loop through each picker
		picker.forEach(function(picked) {
			// Create a new datepicker
			const datepicker = new Datepicker(picked, {
				minDate: new Date(),
				autohide: true,
				format: 'dd/mm/yyyy',
				clearBtn: true,
			});
		});
	}
</script> --}}
