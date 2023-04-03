
<p>
    <input type="hidden" value="{{$EnrollmentOrder['id']}}" id="hiddenenrollmentorderid">
    <p><strong>Enrollment Order Id</strong> : {{$EnrollmentOrder['order_id']}}</p></br>
    <p><strong>{{ __('Product SKU') }}</strong> : {{$EnrollmentOrder['product']['product_sku']}}</p></br>
    <p><strong>{{ __('languages.Product.Product_name') }}</strong> : {{$EnrollmentOrder['product']['product_name']}}</p></br>
    <p><strong>{{__('Product Cost Method')}}</strong>: 
    <?php
        if(isset($EnrollmentOrder['ProductCostType']) && !empty($EnrollmentOrder['ProductCostType'])){
            if($EnrollmentOrder['ProductCostType']['cost_type'] == 1){
                echo '<li>'.__('languages.member.Money').' : '.$EnrollmentOrder['ProductCostType']['cost_value'].'</li>';
            }
            if($EnrollmentOrder['ProductCostType']['cost_type'] == 2){
                echo '<li>'.__('languages.member.Tokens').' : '.$EnrollmentOrder['ProductCostType']['cost_value'].'</li>';
            }
            if($EnrollmentOrder['ProductCostType']['cost_type'] == 3){
                $explodeProductCostType = explode("+",$EnrollmentOrder['ProductCostType']['cost_value']);
                echo '<li>'.__('languages.member.Money').' : '.$explodeProductCostType[0].' + '.__('languages.member.Tokens').' : '.$explodeProductCostType[1].'</li>';
            }
        }
        ?>
    </p></br>
    <p><strong>{{ __('languages.Product.option_code') }} + {{ __('languages.Product.option_name') }}</strong> : </p>
    @php
    @endphp
    @if(!empty($EnrollmentOrder['child_product_id']))
        @if(!empty($EnrollmentOrder['child_product_id']))
            @if(isset($EnrollmentOrder['product']['combo_product_ids']) && !empty($EnrollmentOrder['product']['combo_product_ids']))
            {!!Helper::get_assign_product_order_child_product($EnrollmentOrder['id'])!!}
            @else
            <li>{{$EnrollmentOrder->childProducts->product_suffix}}+{{$EnrollmentOrder->childProducts->product_suffix_name}}</li>
            @endif
        @endif    
    @else
    <span>Not available</span>
    @endif
    </br>
    <span>Status</span>
    <select class='form-control select_product_assign_status' data-enrollment-id="{{$EnrollmentOrder['id']}}">
        <option value="">{{__('languages.event.Select_status')}}</option>
        <option value='0'>Not Confirm</option>
        <option value='1'>Confirm</option>
    </select>
    <br>

    <a href="javascript:void(0);" class="btn btn-primary btn-block glow remove-assigned-product-members mb-0" data-enrollment-id="{{$EnrollmentOrder['id']}}">Delete Members</a>  

    <div class="table-responsive viewAssignMember">
        <table id="assignMemberList" class="table">
            <thead>
                <tr>
                    <th><input type="checkbox" id="all-assigned-member-products" /></th>
                    <th>{{__('languages.RoleManagement.Sr_No')}}</th>
                    <th>{{__('languages.member.Member_Number')}}</th>
                    {{-- <th>{{__('languages.member.Member_Name')}}</th> --}}
                    <th>{{__('languages.member.English_name')}}</th>
                    <th>{{__('languages.member.Chinese_name')}}</th>
                    <th>{{__('languages.Remarks.Remarks')}}</th>
                    <th>{{__('languages.Status')}}</th>
                    <th>{{__('languages.Action')}}</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($EnrollmentOrder['ProductAssignMembers']) && !empty($EnrollmentOrder['ProductAssignMembers']))
                @foreach($EnrollmentOrder['ProductAssignMembers'] as $assignedProductOrder)
                <tr>
                    <td>
                        <input type="checkbox" name="selectMember" value="{{$assignedProductOrder['user_id']}}" class="checkBoxClass select-member-assigned-product" id="Checkbox{{$assignedProductOrder['id']}}" />
                    </td>
                    <td>{{$loop->iteration}}</td>
                    <td>{{$assignedProductOrder['users']['MemberCode']}}</td>
                    {{-- <td>
                        @if(isset($assignedProductOrder['users']['Chinese_name']) && isset($assignedProductOrder['users']['English_name']))
                            {{$assignedProductOrder['users']['English_name']}} & {{$assignedProductOrder['users']['Chinese_name']}}
                        @elseif(isset($assignedProductOrder['users']['English_name']))
                            {{$assignedProductOrder['users']['English_name']}}
                        @elseif(isset($assignedProductOrder['users']['Chinese_name']))
                            {{$assignedProductOrder['users']['Chinese_name']}}
                        @endif
                    </td> --}}
                    <td>{{$assignedProductOrder['users']['English_name'] ?? ''}}</td>
                    <td>{{$assignedProductOrder['users']['Chinese_name'] ?? ''}}</td>
                    <td>{{$assignedProductOrder['remark']}}</td>
                    <td>
                        <select class="form-control product_assign_status_change" data-enrollmentorder-id="{{$EnrollmentOrder['id']}}" data-assign-product-id="{{$assignedProductOrder['id']}}">
                            <option value="">{{__('languages.event.Select_status')}}</option>
                            <option data-id="{{$assignedProductOrder['id']}}" value="0" @if($assignedProductOrder['status'] == '0') selected @endif>{{__('languages.Not_Confirm')}}</option>
                            <option data-id="{{$assignedProductOrder['id']}}" value="1"	@if($assignedProductOrder['status'] == '1') selected @endif>{{__('languages.Confirm')}}</option>
                        </select>
                    </td>
                    <td>
                        <a href="javascript:void(0);" data-enrollmentorder-id="{{$EnrollmentOrder['id']}}" data-assign-product-id="{{$assignedProductOrder['id']}}" class="deleteproductAssignMember">
                            <i class="bx bx-trash-alt"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>
    </div>
</p>