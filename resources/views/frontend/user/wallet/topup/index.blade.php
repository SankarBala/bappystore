@extends('frontend.layouts.user_panel')

@section('panel_content')
<div class="aiz-titlebar mt-2 mb-4">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3">{{ translate('মোবাইল রিচার্জ') }}</h1>
        </div>
    </div>
</div>
<div class="row gutters-10">
    
    <div class="col-md-4 mx-auto mb-3" >
        <div class="p-3 rounded mb-3 c-pointer text-center bg-white shadow-sm hov-shadow-lg has-transition" onclick="show_topup_modal()">
            <span class="size-60px rounded-circle mx-auto bg-secondary d-flex align-items-center justify-content-center mb-3">
                <i class="las la-mobile-alt la-3x text-white"></i>
            </span>
            <div class="fs-18 text-primary">{{ translate('মোবাইল রিচার্জ করুন') }}</div>
        </div>
    </div>
    
</div>
<div class="card">
    <div class="card-header">
        <h5 class="mb-0 h6">{{ translate('মোবাইল রিচার্জ হিস্টরি')}}</h5>
        <form action="{{ route('topup.index') }}" method="GET">
            <div class="card-header row gutters-5">
                
                <div class="col-md-10">
                    <div class="form-group mb-0">
                        <input type="text" class="form-control form-control-sm aiz-date-range" id="search" name="date_range"@isset($date_range) value="{{ $date_range }}" @endisset placeholder="{{ translate('তারিখ অনুযায়ী হিস্টরি দেখুন') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-md btn-primary" type="submit">
                        {{ translate('দেখুন') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
    <div class="card-body">
        <table class="table aiz-table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ translate('কাস্টমার')}}</th>
                    <th>{{ translate('রিচার্জ টাকা')}}</th>
                    <th data-breakpoints="lg">{{ translate('রিচার্জ স্টাটাস')}}</th>
                    <th data-breakpoints="lg">{{ translate('মোবাইল নাম্বার')}}</th>
                    <th>{{ translate('রিচার্জ তারিখ') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($topups as $key => $topup)
                    <tr>
                        <td>{{ $key+1 }}</td>
                        @if ($topup->user != null)
                            <td>{{ $topup->user->name }}</td>
                        @else
                            <td>{{ translate('কোনো ইউজার পাওয়া যায়নি') }}</td>
                        @endif
                        <td>{{ single_price($topup->topup_amount) }}</td>
                        <td>{{ $topup->status_description }}</td>
                        <td>{{ $topup->mobile_no }}</td>
                        <td>{{ $topup->created_at }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="aiz-pagination mt-4">
            {{ $topups->links() }}
        </div>
    </div>
</div>
@endsection

@section('modal')

<!-- Topup Modal -->
<div class="modal fade" id="topup_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ translate('মোবাইল রিচার্জ ফরমটি ফিলাপ করুন') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <form class="" id="topup-form" onsubmit="return confirm('আপনি কি সত্যিই মোবাইল রিচার্জ করতে চান?');" action="{{ route('wallet.topup') }}" method="post">
                @csrf
                <input id="recharge_pin" type="text" name="recharge_pin" value="" hidden required/>
                <div class="modal-body gry-bg px-3 pt-3">
                    @if(Auth::user()->balance >= 0)
                    <div class="row">
                        <div class="col-md-4">
                            <label>{{ translate('মোবাইল নাম্বার')}} <span class="text-danger">*</span></label>
                        </div>
                        <div class="col-md-8">
                            <input type="number" lang="en" class="form-control mb-3" id="mobile" name="mobile" placeholder="{{ translate('১১ ডিজিট মোবাইল নাম্বার লিখুন')}}" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <label>{{ translate('মোবাইল অপারেটর')}} <span class="text-danger">*</span></label>
                        </div>
                        <div class="col-md-8">
                            <div class="mb-3">
                                <select class="form-control" id="operator" name="operator">
                                    <option value="">{{ translate('মোবাইল অপারেটর সিলেক্ট করুন')}}</option>
                                    <option value="GP">{{ translate('গ্রামীনফোন')}}</option>
                                    <option value="GP ST">{{ translate('গ্রামীনফোন সিকতো')}}</option>
                                    <option value="BL">{{ translate('বাংলালিংক')}}</option>
                                    <option value="RB">{{ translate('রবি')}}</option>
                                    <option value="AT">{{ translate('এয়ারটেল')}}</option>
                                    <option value="TT">{{ translate('টেলিটক')}}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <label>{{ translate('রিচার্জের পরিমান')}} <span class="text-danger">*</span></label>
                        </div>
                        <div class="col-md-8">
                            <input type="number" lang="en" class="form-control mb-3" id="amount" name="amount" placeholder="{{ translate('টাকার পরিমান লিখুন')}}" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <label>{{ translate('অ্যাকাউন্ট টাইপ')}} <span class="text-danger">*</span></label>
                        </div>
                        <div class="col-md-8">
                            <div class="mb-3">
                                <select class="form-control selectpicker" data-minimum-results-for-search="Infinity" name="account_type" data-live-search="true">
                                    <option value="prepaid">{{ translate('প্রিপেইড')}}</option>
                                    <option value="postpaid">{{ translate('পোস্টপেইড')}}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group text-right">
                        <!--<button type="submit" id="send" class="btn btn-sm btn-primary transition-3d-hover mr-1">{{translate('রিচার্জ কনফার্ম করুন')}}</button>-->
                        <button id="next_modal" type="button"  class="btn btn-sm btn-primary transition-3d-hover mr-1">{{translate('Next')}}</button>
                    </div>
                    @else
                        <div class="row">
                            <p>মোবাইল রিচার্জ করার জন্য আপনার ওয়ালেটে পর্যাপ্ত পরিমান ব্যালেন্স নাই <a href="{{ route('wallet.index') }}"> ওয়ালেট রিচার্জ করুন </a></p>
                        </div>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>




<div class="modal fade" id="topup_pin_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ translate('মোবাইল রিচার্জ ফরমটি ফিলাপ করুন') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
            </div>
             
                <div class="modal-body gry-bg px-3 pt-3">
                    <div class="row">
                        <div class="col-md-4">
                            <input class="form-control-plaintext mb-3" value="{{ translate('মোবাইল নাম্বার')}}" readonly />
                        </div>
                        <div class="col-md-8">
                            <input id="show_mobile" class="form-control-plaintext mb-3" value="" readonly >
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <input class="form-control-plaintext mb-3" value="{{ translate('amount')}}" readonly />
                        </div>
                        <div class="col-md-8">
                            <input id="show_amount" class="form-control-plaintext mb-3" value="" readonly >
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <input class="form-control-plaintext mb-3" value="{{ translate('commission')}}" readonly />
                        </div>
                        <div class="col-md-8">
                            <input id="show_commission" class="form-control-plaintext mb-3" value="" readonly >
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <input class="form-control-plaintext mb-3" value="{{ translate('Enter pin')}}" readonly />
                        </div>
                        <div class="col-md-8">
                            <input id="pin" class="form-control mb-3" value="" >
                        </div>
                    </div>
                   
                    
                    
                    <div class="form-group text-right">
                        <button type="button" id="confirm_recharge" class="btn btn-sm btn-primary transition-3d-hover mr-1">{{translate('রিচার্জ কনফার্ম করুন')}}</button>
                    </div>
                    
                </div>
          
        </div>
    </div>
</div>



@endsection

@section('script')
<script type="text/javascript">

    $('#topup-form').bind('submit', function (e) {
        var button = $('#send');
    
        // Disable the submit button while evaluating if the form should be submitted
        button.prop('disabled', true);
    
        var valid = true;
    
        // Do stuff (validations, etc) here and set
        // "valid" to false if the validation fails
    
        if (!valid) {
            // Prevent form from submitting if validation failed
            e.preventDefault();
    
            // Reactivate the button if the form was not submitted
            button.prop('disabled', false);
        }
    });
    
    
    $('#next_modal').click((e)=>{
         $('#topup_modal').modal('hide');
         $('#topup_pin_modal').modal('show');
         $('#show_mobile').val( $('#mobile').val());
         $('#show_amount').val( $('#amount').val());
         $('#show_commission').val($('#amount').val()*{{get_setting('recharge_commission')}}/100);
    });
    
    
    $('#confirm_recharge').click((e)=>{
        $('#recharge_pin').val( $('#pin').val());
        $('#topup-form').submit();
    });
    
    function show_topup_modal() {
        $('#topup_modal').modal('show');
    }

    $("#mobile").on('keyup blur', function (e) {	
        var operator = $("#mobile" ).val().substring(0, 3);
        
        if (operator == '017')	var opt = 'GP';	
        if (operator == '018')	var opt = 'RB';
        if (operator == '015')	var opt = 'TT';
        if (operator == '013')	var opt = 'GP';
        if (operator == '019')	var opt = 'BL';
        if (operator == '014')	var opt = 'BL';
        if (operator == '016')	var opt = 'AT';
        
	$("#operator option[value="+opt+"]").attr("selected", true);
    });
</script>
@endsection
