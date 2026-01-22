@extends("$theme_dir.layouts.$layoutName")

{{-- Content --}}
@section('content')
    <div class="row">
        <div class="col-12">
            <!-- Notification -->
            {!! $notify !!}
            <div class="resp-notify" id ="resp-notify"></div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-5 col-sm-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">subscriptions</h4>
                    <hr />

                    <form action="{!! url($links->save) !!}" class="form-horizontal" method="post" accept-charset="utf-8"
                        enctype="multipart/form-data" autocomplete="off">
                        @csrf

                        <div class="row">
                            <div class="col-md-12 col-sm-12">
                                <div class="form-group">
                                    <label for="" class="sks-required">
                                        Title
                                    </label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror"
                                        id="title" placeholder="" name="title" value="{{ old('title') }}">

                                    @error('title')
                                        <span class="error">{{ $errors->first('title') }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="" class="sks-required">
                                        Subscription name
                                    </label>
                                    <input type="text" class="form-control @error('subscription') is-invalid @enderror"
                                        id="subscription" placeholder="" name="subscription"
                                        value="{{ old('subscription') }}">

                                    @error('subscription')
                                        <span class="error">{{ $errors->first('subscription') }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="" class="sks-required">
                                        Code
                                    </label>
                                    <input type="text" class="form-control @error('code') is-invalid @enderror"
                                        id="code" placeholder="" name="code" value="{{ old('code') }}">

                                    @error('code')
                                        <span class="error">{{ $errors->first('code') }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 col-sm-12">
                                <div class="form-group">
                                    <label for="" class="sks-required">
                                        Price<small>(Ksh)</small>
                                    </label>
                                    <input type="number" step="0.01"
                                        class="form-control @error('amount') is-invalid @enderror" id="amount"
                                        placeholder="" name="amount" value="{{ old('amount') }}">

                                    @error('amount')
                                        <span class="error">{{ $errors->first('amount') }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>



                        <div class="row">
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="">
                                        Discount
                                    </label>
                                    <input type="number" step="0.01"
                                        class="form-control @error('discount') is-invalid @enderror" id="discount"
                                        placeholder="" name="discount" value="{{ old('discount') }}">

                                    @error('discount')
                                        <span class="error">{{ $errors->first('discount') }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12">                            
                                    <div class="form-group">
                                        <label for="currency" class="sks-required">
                                            Currency<small></small>
                                        </label>
                                        <select name="currency" id="currency" class="form-control @error('currency') is-invalid @enderror">
                                            <option value="">Select currency</option>
                                            @foreach ($currency as $curr)
                                                <option value="{{ $curr->id }}">{{ $curr->name }}</option>
                                            @endforeach
                                        </select>
                                
                                        @error('currency')
                                            <span class="error">{{ $errors->first('currency') }}</span>
                                        @enderror
                                    </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="" class="sks-required">
                                        Duration
                                    </label>
                                    <input type="text" class="form-control @error('duration') is-invalid @enderror"
                                        id="duration" placeholder="" name="duration" value="{{ old('duration') }}">

                                    @error('duration')
                                        <span class="error">{{ $errors->first('duration') }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="">
                                        Post_grace
                                    </label>
                                    <input type="text" class="form-control @error('post_grace') is-invalid @enderror"
                                        id="post_grace" placeholder="" name="post_grace"
                                        value="{{ old('post_grace') }}">

                                    @error('post_grace')
                                        <span class="error">{{ $errors->first('post_grace') }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 col-sm-12">
                                <div class="form-group">
                                    <label for="">
                                        Thumbnail
                                    </label>
                                    <input type="file" class="form-control @error('thumbnail') is-invalid @enderror"
                                        id="thumbnail" placeholder="" name="thumbnail" value="{{ old('thumbnail') }}">

                                    @error('thumbnail')
                                        <span class="error">{{ $errors->first('thumbnail') }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="" class="">
                                        Description <small>(more infomantion)</small>
                                    </label>
                                    <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="2"
                                        spellcheck="false">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <span class="error">{{ $errors->first('notes') }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            {{-- set col-6 and float right --}}
                            <div class="col-12 float-end">
                                <div class="form-group">
                                    <button type="button" onclick="send_donate(form,'')"
                                        class="btn btn-success waves-effect waves-light">
                                        Create package
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
        <div class="col-md-7 col-sm-12">
            <div class="card">
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
                                <th>Title</th>
                                <th>Amount</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($entry_list as $list)
                                {{-- {{dd($list)}} --}}
                                <tr>
                                    <td>{{ $list->id }}</td>
                                    <td>{{ $list->title }}</td>
                                    <td>{{ $list->price }}</td>
                                    <td>
                                        <a href="{{ url($links->edit . '?id=' . $list->id) }}"
                                            class="btn btn-primary waves-effect waves-light btn-sm">
                                            <i class="bx bx-spreadsheet font-size-16 align-middle mr-2"></i> Edit
                                        </a>

                                        <button onclick="deleteData('{{ $list->id }}')"
                                            class="btn
                                            btn-danger waves-effect waves-light btn-sm">
                                            <i class="bx bx-trash font-size-16 align-middle mr-2"></i> Delete
                                        </button>

                                        @if ($list->flag == 1)
                                            <a href="{{ url($links->manage . '/deactivate?id=' . $list->id) }}"
                                                class="btn btn-info waves-effect waves-light btn-sm">
                                                Inactive
                                            </a>
                                        @else
                                            <a href="{{ url($links->manage . '/activate?id=' . $list->id) }}"
                                                class="btn btn-info waves-effect waves-light btn-sm">
                                                Active
                                            </a>
                                        @endif
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
