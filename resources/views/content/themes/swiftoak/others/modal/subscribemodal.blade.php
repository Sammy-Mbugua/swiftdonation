<!-- Modal -->
<div class="modal fade" id="subscribemodal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title text-white" id="staticBackdropLabel">Donate</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i
                        class="fas fa-times " aria-hidden="true"></i></button>
            </div>
            <div class="resp-notify mt-3 mx-3" id ="resp-notify"></div>
            <div class="modal-body">

                {{-- {{ dd(url($links->subscribe)) }} --}}
                <form name="form-input" action="{{ url($links->subscribe) }}" class="form-horizontal custom-essay"
                    method="post" accept-charset="utf-8"enctype="multipart/form-data" autocomplete="off"
                    id="deleteInfo">

                    <input type="hidden" name="subs" id="subs">
                    <input type="hidden" name="amount_m" id="amount_m">
                    <input type="hidden" id="user" name="user" value="{{ auth()->user()->id }}">

                    <div data-mdb-input-init class="form-outline mb-4">
                        <label class="form-label" for="form2Example11">Amount</label>
                        <input type="text" id="modalamount" class="form-control" name="amount" readonly
                            placeholder="amount" />
                        @error('amount')
                            <span class="error">{{ $errors->first('amount') }}</span>
                        @enderror
                    </div>

                    <div data-mdb-input-init class="form-outline mb-4">
                        <label class="form-label" for="form2Example22">phone</label>
                        <input type="phone" id="phone" class="form-control" name="phone"
                            value="{{ old('phone') }}" placeholder="Enter phone eg. 07 xx xxx xxx" />

                        @error('phone')
                            <span class="error">{{ $errors->first('phone') }}</span>
                        @enderror

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" onclick="send_donate(form, '')"
                            class="btn btn-primary btn-block fa-lg gradient-custom-2">Donate</button>
                    </div>
                </form>
            </div>

            {{-- <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary btn-block fa-lg gradient-custom-2">Donate</button>
            </div> --}}
        </div>
    </div>
</div>
