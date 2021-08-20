@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class=" align-items-center">
        <h1 class="h3">{{translate('Topup Transaction Report')}}</h1>
    </div>
</div>

<div class="row">
    <div class="col-md-10 mx-auto">
        <div class="card">
            <form action="{{ route('recharge-history.index') }}" method="GET">
                <div class="card-header row gutters-5">
                    <div class="col text-center text-md-left">
                        <h5 class="mb-md-0 h6">{{ translate('Topup Transaction') }}</h5>
                    </div>
                    @if(Auth::user()->user_type != 'seller')
                    <div class="col-md-3 ml-auto">
                        <select id="demo-ease" class="form-control form-control-sm aiz-selectpicker mb-2 mb-md-0" name="user_id">
                            <option value="">{{ translate('Choose User') }}</option>
                            @foreach ($users_with_wallet as $key => $user)
                                <option value="{{ $user->id }}" @if($user->id == $user_id) selected @endif >
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    <div class="col-md-3">
                        <div class="form-group mb-0">
                            <input type="text" class="form-control form-control-sm aiz-date-range" id="search" name="date_range"@isset($date_range) value="{{ $date_range }}" @endisset placeholder="{{ translate('Daterange') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-md btn-primary" type="submit">
                            {{ translate('Filter') }}
                        </button>
                    </div>
                </div>
            </form>
            <div class="card-body">

                <table class="table aiz-table mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ translate('Customer')}}</th>
                            <th>{{ translate('Amount')}}</th>
                            <th data-breakpoints="lg">{{ translate('Status')}}</th>
                            <th data-breakpoints="lg">{{ translate('Mobile No')}}</th>
                            <th>{{ translate('Date') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($topups as $key => $topup)
                            <tr>
                                <td>{{ $key+1 }}</td>
                                @if ($topup->user != null)
                                    <td>{{ $topup->user->name }}</td>
                                @else
                                    <td>{{ translate('User Not found') }}</td>
                                @endif
                                <td>{{ single_price($topup->topup_amount) }}</td>
                                <td>{{ $topup->status_description }}</td>
                                <td>{{ $topup->mobile_no }}</td>
                                <td>{{ date('Y-m-d h:i A', strtotime($topup->created_at)) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="aiz-pagination mt-4">
                    {{ $topups->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection


