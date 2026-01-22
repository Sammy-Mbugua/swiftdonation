@extends("$theme_dir.layouts.$layoutName")

{{-- Content --}}
@section('content')
    <form action="{!! url($links->update) !!}" class="form-horizontal" method="post" accept-charset="utf-8"
        enctype="multipart/form-data" autocomplete="off">
        @csrf

        {{-- hidden userId --}}
        <input type="hidden" name="id" value="{{ $resultFound->id }}">

        <!-- Notification -->
        {!! $notify !!}

        <div class="row justify-content-center">
            <div class="col-md-7 col-sm-12">
                {{-- {{ dd($resultFound) }} --}}
                <div class="row">
                    <div class="col-lg-12 col-sm-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">subscriptions</h4>
                                <hr />
                                {{-- <p class="card-title-desc"></p> --}}
                                <form action="{!! url($links->update) !!}" class="form-horizontal" method="post"
                                    accept-charset="utf-8" enctype="multipart/form-data" autocomplete="off">
                                    @csrf

                                    <div class="row">
                                        <div class="col-md-12 col-sm-12">
                                            <div class="form-group">
                                                <label for="" class="sks-required">
                                                    Title
                                                </label>
                                                <input type="text"
                                                    class="form-control @error('title') is-invalid @enderror" id="title"
                                                    placeholder="" name="title" value="{{ $resultFound->title }}">

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
                                                <input type="text"
                                                    class="form-control @error('subscription') is-invalid @enderror"
                                                    id="subscription" placeholder="" name="name"
                                                    value="{{ $resultFound->name }}">

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
                                                <input type="text"
                                                    class="form-control @error('code') is-invalid @enderror" id="code"
                                                    placeholder="" name="code" value="{{ $resultFound->code }}">

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
                                                    class="form-control @error('amount') is-invalid @enderror"
                                                    id="amount" placeholder="" name="price"
                                                    value="{{ $resultFound->price }}">

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
                                                    class="form-control @error('discount') is-invalid @enderror"
                                                    id="discount" placeholder="" name="discount"
                                                    value="{{ $resultFound->discount }}">

                                                @error('discount')
                                                    <span class="error">{{ $errors->first('discount') }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-12">
                                            <div class="form-group">
                                                <label for="" class="sks-required">
                                                    Currency<small></small>
                                                </label>
                                                <input type="text"
                                                    class="form-control @error('currency') is-invalid @enderror"
                                                    id="currency" placeholder="" name="currency"
                                                    value="{{ $resultFound->currency }}">

                                                @error('currency')
                                                    <span class="error">{{ $errors->first('currency') }}</span>
                                                @enderror
                                            </div>

                                            {{-- <div class="form-group ">
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
                                            </div> --}}
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 col-sm-12">
                                            <div class="form-group">
                                                <label for="" class="sks-required">
                                                    Duration
                                                </label>
                                                <input type="text"
                                                    class="form-control @error('duration') is-invalid @enderror"
                                                    id="duration" placeholder="" name="duration"
                                                    value="{{ $resultFound->duration }}">

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
                                                <input type="text"
                                                    class="form-control @error('post_grace') is-invalid @enderror"
                                                    id="post_grace" placeholder="" name="post_grace"
                                                    value="{{ $resultFound->post_grace }}">

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
                                                <input type="file"
                                                    class="form-control @error('thumbnail') is-invalid @enderror"
                                                    id="thumbnail" placeholder="" name="thumbnail[]"
                                                    value="{{ $resultFound->thumbnail }}">
                                                <small>Upload a thumbnail image</small>

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
                                                <textarea name="description" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="2"
                                                    spellcheck="false">{{ $resultFound->description }}</textarea>
                                                @error('notes')
                                                    <span class="error">{{ $errors->first('notes') }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row d-none">
                                        {{-- set col-6 and float right --}}
                                        {{-- <div class="col-12 float-end">
                                            <div class="form-group">
                                                <button type="button" onclick="send_donate(form,'')"
                                                    class="btn btn-success waves-effect waves-light">
                                                    Save
                                                </button>
                                            </div>
                                        </div> --}}
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-6">
                        <div class=" float-end">
                            <div class="form-group">
                                <button type="submit" class="btn btn-success waves-effect waves-light">
                                    Update Package
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </form>
@endsection
