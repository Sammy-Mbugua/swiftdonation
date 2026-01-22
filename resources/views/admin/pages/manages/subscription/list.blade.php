@extends("$theme_dir.layouts.$layoutName")

{{-- Content --}}
@section('content')
    <div class="row">
        <div class="col-12">
            <!-- Notification -->
            {{-- {!! $notify !!} --}}
            <div class="resp-notify" id ="resp-notify"></div>
        </div>
    </div>

    <div class="container py-5 h-100">
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="card col-md-9 col-lg-10 col-sm-12">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-8 col-sm-12">
                            <h4 class="card-title">Recent Suscriptions </h4>
                        </div>
                    </div>

                    <table id="datatable" class="table table-bordered dt-responsive nowrap"
                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr>
                                <th>#ID</th>
                                <th>Name</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>More</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($entry_list as $list)
                                {{-- {{dd($list)}} --}}
                                <tr>
                                    <td>{{ $list->id }}</td>
                                    <td>{{ $list->this_refer->name }}</td>
                            
                                    @if ($list->this_billing == null)
                                        <td><span class="text-danger">{{ 'n/a' }} </span></td>
                                    @else
                                        <td>{{ $list->this_billing->total }}</td>
                                    @endif
                                 
                                    <td>
                                        @if ($list->status== 1)
                                            <button
                                                class="btn btn-success waves-effect waves-light btn-sm">
                                                Donated
                                            </button>
                                        @else
                                            <button 
                                                class="btn btn-warning waves-effect waves-light btn-sm">
                                                Not Donated
                                            </button>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ url($links->manage . '/view?id=' . $list->user) }}"
                                            class="btn btn-success waves-effect waves-light btn-sm">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        // ?  Delete data
        const deleteData = (userId) => {
            // ? Are you sure
            Swal.fire({
                title: 'Are you sure you want to delete?',
                text: "This can't be undone, and will affect related entry!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, cancel!',
            }).then((result) => {
                if (result.isConfirmed) {
                    // If confirmed, send a request to the Laravel route for deletion
                    let base_url = `{{ url($links->delete) }}`;
                    console.log(base_url)
                    window.location.href = `${base_url}?id=${userId}`;
                } else {
                    // If canceled, close the SweetAlert dialog
                    showConfirm = false;
                }
            });
        }


        const send_donate = (this_form, redirect_url = "") => {

            let title = this_form.querySelector("#title").value
            let subscription = this_form.querySelector("#subscription").value
            let code = this_form.querySelector("#code").value
            let amount = this_form.querySelector('#amount').value
            let discount = this_form.querySelector('#discount').value
            let currency = this_form.querySelector('#currency').value
            let duration = this_form.querySelector('#duration').value
            let post_grace = this_form.querySelector('#post_grace').value
            let notes = this_form.querySelector('textarea[name="notes"]').value;

            const thumbnail = this_form.querySelector('input[type="file"]').files;

            // Submit using fetch
            const formData = new FormData()
            formData.append("title", title)
            formData.append("name", subscription)
            formData.append('code', code)
            formData.append('discount', discount)
            formData.append('currency', currency)
            formData.append('duration', duration)
            formData.append('post_grace', post_grace)
            formData.append('description', notes)
            formData.append('price', amount)
            for (let i = 0; i < thumbnail.length; i++) {
                formData.append(`thumbnail[${i}]`, thumbnail[i]);
            }

            // console.log('from data from withdrawal '+ formData)
            // console.log('action url ' + this_form.getAttribute("action"))
            // Url
            let api_url = this_form.getAttribute("action")
            // fetch
            // Loader.show();
            fetch('{{ route('makepackage') }}', {
                    method: "POST",
                    headers: {
                        // 'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData,
                })
                .then((response) => response.json())
                .then((data) => {
                    if (data.message == "Success") {
                        console.log('success message', data.message)
                        successNotifyState(
                            "Success!", data.message,
                        );
                        this_form.reset();
                        setTimeout(() => {
                            window.location.reload();
                        }, 1400);

                    } else {

                        let errorMessage = "Error!\n";

                        if (data.message && typeof data.message === 'object') {
                            // Loop through all errors
                            errorMessage += Object.keys(data.message).map(key => {
                                let messages = data.message[key];

                                // Ensure it's an array before calling .join()
                                if (Array.isArray(messages)) {
                                    return `${key}: ${messages.join(', ')}`;
                                } else {
                                    return `${key}: ${messages}`;
                                }
                            }).join("\n"); // Separate errors by a new line for better readability
                        } else {
                            errorMessage += data.message || "An unknown error occurred.";
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
            // 	$("#adminDraft").modal("hide");
            // }, 5000);
        };
    </script>
@endsection
