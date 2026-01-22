@extends("$theme_dir.layouts.$layoutName")

{{-- Content --}}
@section('content')
    <div class="container py-5 h-100">
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col-xl-10 ">
                <div class="card rounded-3 p-3 text-black">
                    @if ($name == null)
                        <h4 class="mt-1 mb-5 pb-1">{{ strtoupper($userName->name) }} was Not referred <span
                                class="text-success">{{ '' }}</span>
                            <br>
                            <hr>
                        </h4>
                    @else
                        <h4 class="mt-1 mb-5 pb-1">{{ strtoupper($userName->name) }} was referred by: <span
                                class="text-success">{{ strtoupper($name) }}</span>
                            <br>
                            <hr>
                        </h4>
                    @endif

                    <table class="table">
                        @if ($entry_list->count() > 0)
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- {{dd($entry_list)}} --}}
                                @foreach ($entry_list as $list)
                                    {{-- {{dd($list)}} --}}
                                    <tr>
                                        <td>{{ $list->id }}</td>
                                        <td>{{ $list->this_refer->name }}</td>
                                        <td>{{ date('d M, Y H:i:s', strtotime($list->created_at)) }}</td>
                                        <td>
                                            @if ($list->status == 1)
                                                <button class="btn btn-success waves-effect waves-light btn-sm">
                                                    Donated
                                                </button>
                                            @else
                                                <button class="btn btn-warning waves-effect waves-light btn-sm">
                                                    Not Donated
                                                </button>
                                            @endif

                                            @if ($list->this_billing == null)
                                        <td><span class="text-danger">{{ 'n/a' }} </span></td>
                                    @else
                                        <td>{{ $list->this_billing->total }}</td>
                                @endif
                                </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                            <td>{{ 'Total' }}</td>
                            <td>{{ '' }}</td>
                            <td>{{ '' }}</td>
                            <td>{{ '' }}</td>
                            <td>{{ number_format($total, 2, '.', '') }}</td>
                        </tfoot>
                    @else
                        <tr>
                            <td>
                                <h3><i class="fas fa-user-friends"></i> No Referrals Yet!</h3>
                                {{-- <a href="#" class="btn btn-success"><i class="fas fa-share"></i> Invite Now</a> --}}
                            </td>
                        </tr>
                        @endif
                    </table>

                    <div class="">
                        <p class=" text-success">{{ strtoupper($userName->name) }} referal link: {{ $getReferralLink }}</p>
                        {{-- <a href="#" class="btn btn-success"><i class="fas fa-share"></i> Invite Now</a> --}}

                    </div>
                    <div class="">
                        
                        <a href="{{ url('/vrm/setup/manage-subscription') }}" class="btn btn-success"><i class="fas fa-share"></i>Back</a>

                    </div>


                </div>
            </div>
        </div>
    </div>
@endsection
