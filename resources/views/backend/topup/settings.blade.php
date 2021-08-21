@extends('backend.layouts.app')

@section('content')

<h4 class="text-center text-muted mt-4">{{translate('Mobile recharge settings')}}</h4>
<div class="row">
  
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h3 class="mb-0 h6 text-center">{{translate('Recharge System Activation')}}</h3>
            </div>
            <div class="card-body text-center">
                <label class="aiz-switch aiz-switch-success mb-0">
                    <input type="checkbox" onchange="updateSettings(this, 'recharge_system')" <?php if(get_setting('recharge_system') == 1) echo "checked";?>>
                    <span class="slider round"></span>
                </label>

            </div>
            <div class="card-header">
                    <p class="mb-0 h6 text-center">{{translate('Commission')}} % </p>
                    <input id="recharge_commission_text" type="text"  value="{{get_setting('recharge_commission')}}" class="p-1"/>
                    <button type="button" class="btn btn-xs btn-success p-1" onclick="update_recharge_commission()">Update</button>
                   
            </div>
        </div>
    </div>
    

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h3 class="mb-0 h6 text-center">{{translate('Use KDRL')}}</h3>
            </div>
            <div class="card-body text-center">
                <label class="aiz-switch aiz-switch-success mb-0">
                    <input type="checkbox" onchange="updateSettings(this, 'use_kdrl')" <?php if(get_setting('use_kdrl') == 1) echo "checked";?>>
                    <span class="slider round"></span>
                </label>

            </div>
        </div>
    </div>
    
   
</div>



@endsection

@section('script')
    <script type="text/javascript">
        function updateSettings(el, type){
            if($(el).is(':checked')){
                var value = 1;
            }
            else{
                var value = 0;
            }
            
            $.post('{{ route('business_settings.update.activation') }}', {_token:'{{ csrf_token() }}', type:type, value:value}, function(data){
                if(data == '1'){
                    AIZ.plugins.notify('success', '{{ translate('Settings updated successfully') }}');
                }
                else{
                    AIZ.plugins.notify('danger', 'Something went wrong');
                }
            });
        }
        
            function update_recharge_commission(){
            
            var value = $('#recharge_commission_text').val();

            $.post('{{ route('business_settings.update.activation') }}', {_token:'{{ csrf_token() }}', type:'recharge_commission', value:value}, function(data){
                if(data == '1'){
                    AIZ.plugins.notify('success', '{{ translate('Settings updated successfully') }}');
                }
                else{
                    AIZ.plugins.notify('danger', 'Something went wrong');
                }
            });
        }
    </script>
@endsection
