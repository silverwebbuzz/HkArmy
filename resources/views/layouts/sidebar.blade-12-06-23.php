@php
    $permissions = [];
    $role_id = Session::get('user')['role_id'];
    if($role_id){
        $module_permission = Helper::getPermissions($role_id);
        if($module_permission && !empty($module_permission['permission'])){
            $permissions = $module_permission['permission'];
        }
    }else{
        $permissions = [];
    }
    $sitesettings = Helper::getsitesettings();

@endphp

<div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true">
    <div class="navbar-header">
        <ul class="nav navbar-nav flex-row">
            <li class="nav-item mr-auto">
                <a class="navbar-brand" href="{{ url('/') }}">
                    <div class="brand-logo">
                        @if(!empty($sitesettings->Logo))
                            <img class="logo" src="{{ asset($sitesettings->Logo) }}" />
                        @else
                            {{-- <img class="logo" src="{{ asset('app-assets/images/logo/S_blue.png') }}" /> --}}
                            <img class="logo" src="{{ asset('app-assets/images/ico/hkaca_fav_icon.ico')}}" />
                        @endif
                    </div>
                    <h2 class="brand-text mb-0">{{ !empty($sitesettings->SiteName) ? $sitesettings->SiteName : __('languages.sidebar.Scout') }}</h2>
                </a>
            </li>
            <li class="nav-item nav-toggle"><a class="nav-link modern-nav-toggle pr-0" data-toggle="collapse"><i class="bx bx-x d-block d-xl-none font-medium-4 primary toggle-icon"></i><i class="toggle-icon bx bx-disc font-medium-4 d-none d-xl-block collapse-toggle-icon primary" data-ticon="bx-disc"></i></a></li>
        </ul>
    </div>
    <div class="shadow-bottom"></div>
    <div class="main-menu-content">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation" data-icon-style="">
            {{-- <li class="nav-item {{ (request()->is('/')) ? 'active': ''  }}"><a href="{{ url('/') }}"><i class="bx bx-home-alt"></i><span class="menu-title" data-i18n="Dashboard">{{ __('languages.sidebar.Dashboard') }}</span></a>
            </li> --}}
            <!-- @if (in_array('members_read', $permissions))
                <li class=" nav-item {{ ( request()->is('/') || request()->is('users') || request()->is('users/create') ||request()->is('users/*/edit') || request()->is('members-list') || request()->is('users/*')) ? 'active': ''  }}"><a href="{{ route('users.index') }}"><i class="bx bx-user-plus"></i><span class="menu-title" data-i18n="User">{{ __('languages.sidebar.Members') }}</span></a>
                </li>
            @endif -->

            @if (in_array('members_read', $permissions))
                <li class=" nav-item {{ ( request()->is('/members')) ? 'active': ''  }}"><a href="{{ route('members') }}"><i class="bx bx-user-plus"></i><span class="menu-title" data-i18n="User">{{ __('languages.sidebar.Members') }}</span></a>
                </li>
            @endif

            {{-- @if (in_array('members_read', $permissions)) --}}
                <li class=" nav-item {{ ( request()->is('user-management') || request()->is('user-management/create') ||request()->is('user-management/*/edit') || request()->is('user-management-list') || request()->is('user-management/*')) ? 'active': ''  }}"><a href="{{ route('user-management.index') }}"><i class="bx bx-user-plus"></i><span class="menu-title" data-i18n="User">{{ __('languages.sidebar.user_management') }}</span></a>
                </li>
            {{-- @endif --}}

            @if (in_array('event_management_read', $permissions))
                <li class=" nav-item {{ (request()->is('eventManagement') || request()->is('eventManagement/create') ||request()->is('eventManagement/*/edit') ||request()->is('event-report/*')) ? 'active': ''  }}"><a href="{{ route('eventManagement.index') }}"><i class="bx bx-calendar-event"></i><span class="menu-title" data-i18n="User">{{ __('languages.sidebar.Event Management') }}</span></a>
                </li>
            @endif
            @if (in_array('attendance_management_read', $permissions))
                <li class=" nav-item {{ (request()->is('attendanceManagement') || request()->is('attendanceManagement/create') || request()->is('attendanceManagement/*/edit') || request()->is('token-report')) ? 'active': ''  }}"><a href="{{ route('attendanceManagement.index') }}"><i class="bx bxs-book-open"></i><span class="menu-title" data-i18n="Dashboard">{{ __('languages.sidebar.Attendance Management') }}</span></a>
                </li>
            @endif
            @if (in_array('attendance_management_read', $permissions))
            <li class="nav-item">
                <a href="javascript:void(0);"><i class="bx bxs-book-open"></i><span class="menu-title" data-i18n="User">{{ __('languages.sidebar.Attendance_Report') }}</span></a>
                <ul class="menu-content">
                    <li class=" nav-item {{ (request()->is('attendance-report')) ? 'active': ''  }}"><a href="{{ url('attendance-report') }}"><i class="bx bxs-book-open"></i><span class="menu-title" data-i18n="Dashboard">{{ __('languages.sidebar.Attended Event Member') }}</span></a>
                    </li>
                    <li class=" nav-item {{ (request()->is('assign-user-report') || request()->is('assign-user-report/*')) ? 'active': ''  }}"><a href="{{ url('assign-user-report') }}"><i class="bx bxs-book-open"></i><span class="menu-title" data-i18n="Dashboard">{{ __('languages.sidebar.Enrollment') }}</span></a>
                    </li>
                   
                </ul>
            </li>
            @endif
             
             @if (in_array('attendance_management_read', $permissions))
                <li class=" nav-item {{ (request()->is('token-management') || request()->is('token-management/edit/*')) ? 'active': ''  }}"><a href="{{ url('token-management') }}"><i class="bx bxs-book-open"></i><span class="menu-title" data-i18n="Dashboard">{{ __('languages.sidebar.Token Management') }}</span></a>
                    </li>
                    <li class=" nav-item {{ (request()->is('transaction-history')) ? 'active': ''  }}"><a href="{{ url('transaction-history') }}"><i class="bx bxs-book-open"></i><span class="menu-title" data-i18n="Dashboard">{{ __('languages.sidebar.Transaction History') }}</span></a>
                    </li>
            @endif

            @if (in_array('role_management_read', $permissions))
                <li class="nav-item {{ (request()->is('roleManagement') || request()->is('roleManagement/create') ||request()->is('roleManagement/*/edit')) ? 'active': ''  }}"><a href="{{ route('roleManagement.index') }}"><i class="bx bx-user"></i></i><span class="menu-title" data-i18n="Dashboard">{{ __('languages.sidebar.Role Management') }}</span></a>
                </li>
            @endif

            {{-- @if (in_array('awards_management_read', $permissions))
            <li class=" nav-item {{ (request()->is('awards') || request()->is('awards/create') ||request()->is('awards/*/edit')) ? 'active': ''  }}">
                <a href="{{route('awards.index')}}">
                    <i class="bx bx-trophy"></i>
                    <span class="menu-title" data-i18n="User">{{ __('languages.sidebar.Awards Management') }}</span>
                </a>
            </li>
            @endif

            @if (in_array('badges_management_read', $permissions))
            <li class=" nav-item {{ (request()->is('badges') || request()->is('badges/create') ||request()->is('badges/*/edit')) ? 'active': ''  }}">
                <a href="{{route('badges.index')}}">
                    <i class="bx bx-badge-check"></i>
                    <span class="menu-title" data-i18n="User">{{ __('languages.sidebar.Badges Management') }}</span>
                </a>
            </li>
            @endif --}}

            @if (in_array('award_assigned_member_list_read', $permissions))
            <li class="nav-item {{ (request()->is('award-assigned-member-list')) ? 'active': ''  }}">
                <a href="{{ route('award-assigned-member-list') }}">
                    <i class="bx bx-trophy"></i>
                    <span class="menu-title" data-i18n="">{{ __('languages.award_member_list.award_member_list') }}</span>
                </a>
            </li>
            @endif

            @if (in_array('badge_assigned_member_list_read', $permissions))
            <li class="nav-item {{ (request()->is('badge-assigned-member-list')) ? 'active': ''  }}">
                <a href="{{ route('badge-assigned-member-list') }}">
                    <i class="bx bx-badge-check"></i>
                    <span class="menu-title" data-i18n="">{{ __('languages.badge_member_list.badge_member_list') }}</span>
                </a>
            </li>
            @endif

            @if (in_array('uniform_receiving_management_read', $permissions))
            <li class="nav-item">
                <a href="javascript:void(0);"><i class="bx bxs-user-detail"></i><span class="menu-title" data-i18n="User">{{ __('languages.sidebar.Uniform Receive/Sales') }}</span></a>
                <ul class="menu-content">
                    @if (in_array('product_management_read', $permissions))
                        <li class="nav-item {{ (request()->is('product') || request()->is('product/create') ||request()->is('product/*/edit')) ? 'active': ''  }}"><a href="{{ route('product.index') }}"><i class="bx bx-group"></i><span class="menu-title" data-i18n="Dashboard">{{ __('languages.sidebar.Product') }}</span></a>
                        </li>
                    @endif
                    <li class=" nav-item {{ (request()->is('product-assign-user-report') || request()->is('product-assign-user-report/*')) ? 'active': ''  }}"><a href="{{ url('product-assign-user-report') }}"><i class="bx bxs-book-open"></i><span class="menu-title" data-i18n="Dashboard">{{ __('languages.sidebar.Enrollment_Product') }}</span></a>
                    </li>
                    <!-- <li class="nav-item {{ (request()->is('product-list') || request()->is('product-list')) ? 'active': ''  }}"><a href="{{ url('product-list')}}"><i class="bx bx-right-arrow-alt"></i><span class="menu-item" data-i18n="View">{{ __('languages.sidebar.Product List') }}</span></a>
                    </li>
                    <li class="nav-item {{ (request()->is('cart') || request()->is('cart')) ? 'active': ''  }}"><a href="{{ url('cart') }}"><i class="bx bx-right-arrow-alt"></i><span class="menu-item" data-i18n="View">{{ __('languages.sidebar.Cart') }}</span></a>
                    </li>
                    <li class="nav-item {{ (request()->is('purchase-product') || request()->is('purchase-product/create') ||request()->is('purchase-product/*/edit')) ? 'active': ''  }}"><a href="{{ url('purchase-product') }}"><i class="bx bx-right-arrow-alt"></i><span class="menu-item" data-i18n="View">{{ __('languages.sidebar.Purchase Product') }}</span></a>
                    </li> -->
                    <li class=" nav-item {{ (request()->is('product-history')) ? 'active': ''  }}"><a href="{{ url('product-history') }}"><i class="bx bxs-book-open"></i><span class="menu-title" data-i18n="Dashboard">{{ __('languages.sidebar.Transaction History') }}</span></a>
                    </li>
                    <!-- <li class="nav-item {{ (request()->is('checkout') || request()->is('checkout')) ? 'active': ''  }}"><a href="{{ url('checkout') }}"><i class="bx bx-right-arrow-alt"></i><span class="menu-item" data-i18n="View">{{ __('languages.sidebar.Checkout') }}</span></a>
                    </li> -->
                </ul>
            </li>
            @endif
                @if (in_array('settings_read', $permissions))
                <li class="nav-item">
                    <a href="javascript:void(0);"><i class="bx bxs-cog"></i><span class="menu-title" data-i18n="User">{{ __('languages.sidebar.Configuration') }}</span></a>
                    <ul class="menu-content">
                        <li class="nav-item {{ (request()->is('team') || request()->is('team/create') ||request()->is('team/*/edit')) ? 'active': ''  }}"><a href="{{ route('team.index') }}"><i class="bx bx-group"></i><span class="menu-title" data-i18n="Dashboard">{{ __('languages.sidebar.Team Management') }}</span></a>
                        </li>
                        <li class="nav-item {{ (request()->is('subteam') || request()->is('subteam/create') ||request()->is('subteam/*/edit')) ? 'active': ''  }}" ><a href="{{ route('subteam.index') }}"><i class="bx bxs-cog"></i><span class="menu-title" data-i18n="User">{{ __('languages.sidebar.SubTeam') }}</span></a>
                        </li>
                        <li class="nav-item {{ (request()->is('rank') || request()->is('rank/create') ||request()->is('rank/*/edit')) ? 'active': ''  }}"><a href="{{ route('rank.index') }}"><i class="bx bx-group"></i><span class="menu-title" data-i18n="Dashboard">{{ __('languages.sidebar.Rank Management') }}</span></a>
                        </li>
                        <!-- <li class=" nav-item"><a href="javascript:void(0);"><i class="bx bx-group"></i><span class="menu-title" data-i18n="User">{{ __('languages.sidebar.Rank Management') }}</span></a>
                        </li> -->
                        <!-- <li class=" nav-item"><a href="javascript:void(0);"><i class="bx bxs-time"></i><span class="menu-title" data-i18n="User">{{ __('languages.sidebar.Hours Management') }}</span></a></li> -->


                        <li class=" nav-item {{ (request()->is('awards-badges-categories') || request()->is('awards-badges-categories/create') ||request()->is('awards-badges-categories/*/edit')) ? 'active': ''  }}">
                            <a href="{{route('awards-badges-categories.index')}}">
                                <i class="bx bx-trophy"></i>
                                <span class="menu-title" data-i18n="User">{{ __('languages.awards_badges_categories.award_badges_categories') }}</span>
                            </a>
                        </li>

                        <!-- <li class=" nav-item {{ (request()->is('awards') || request()->is('awards/create') ||request()->is('awards/*/edit')) ? 'active': ''  }}">
                            <a href="{{route('awards.index')}}">
                                <i class="bx bx-trophy"></i>
                                <span class="menu-title" data-i18n="User">{{ __('languages.sidebar.Awards Management') }}</span>
                            </a>
                        </li>
                        <li class=" nav-item {{ (request()->is('badges') || request()->is('badges/create') ||request()->is('badges/*/edit')) ? 'active': ''  }}">
                            <a href="{{route('badges.index')}}">
                                <i class="bx bx-trophy"></i>
                                <span class="menu-title" data-i18n="User">{{ __('languages.sidebar.Badges Management') }}</span>
                            </a>
                        </li> -->

                        <li class="nav-item {{ (request()->is('qualification') || request()->is('qualification/create') ||request()->is('qualification/*/edit')) ? 'active': ''  }}" ><a href="{{ route('qualification.index') }}"><i class="bx bxs-cog"></i><span class="menu-title" data-i18n="User">{{ __('languages.sidebar.Qualification') }}</span></a></li>
                        <li class="nav-item {{ (request()->is('related-activity-history') || request()->is('related-activity-history/create') ||request()->is('related-activity-history/*/edit')) ? 'active': ''  }}" ><a href="{{ route('related-activity-history.index') }}"><i class="bx bxs-cog"></i><span class="menu-title" data-i18n="User">{{ __('languages.sidebar.Related_Activity_History') }}</span></a></li>
                        <li class="nav-item {{ (request()->is('specialty') || request()->is('specialty/create') ||request()->is('specialty/*/edit')) ? 'active': ''  }}" ><a href="{{ route('specialty.index') }}"><i class="bx bxs-cog"></i><span class="menu-title" data-i18n="User">{{ __('languages.sidebar.Specialty') }}</span></a></li>
                        <li class="nav-item {{ (request()->is('remarks') || request()->is('remarks/create') ||request()->is('remarks/*/edit')) ? 'active': ''  }}" ><a href="{{ route('remarks.index') }}"><i class="bx bxs-cog"></i><span class="menu-title" data-i18n="User">{{ __('languages.sidebar.Remarks') }}</span></a></li>
                        <li class="nav-item {{ (request()->is('event-type') || request()->is('event-type/create') ||request()->is('event-type/*/edit')) ? 'active': ''  }}" ><a href="{{ route('event-type.index') }}"><i class="bx bxs-cog"></i><span class="menu-title" data-i18n="User">{{ __('languages.sidebar.EventType') }}</span></a></li>
                        <li class="nav-item {{ (request()->is('audit-log') || request()->is('audit-log/show/*')) ? 'active': ''  }}" ><a href="{{ url('audit-log') }}"><i class="bx bxs-cog"></i><span class="menu-title" data-i18n="User">{{ __('languages.sidebar.Audit_log') }}</span></a></li>
                        <li class="nav-item {{ (request()->is('size-attributes') || request()->is('size-attributes/create') ||request()->is('size-attributes/*/edit')) ? 'active': '' }}">
                            <a href="{{ url('size-attributes') }}">
                                <i class="bx bxs-cog"></i>
                                <span class="menu-title" data-i18n="User">{{ __('languages.sidebar.Size Attributes') }}</span>
                            </a>
                        </li>
                        <li class="nav-item {{ (request()->is('categories') || request()->is('categories/create') ||request()->is('categories/*/edit')) ? 'active': '' }}">
                            <a href="{{ route('categories.index') }}">
                                <i class="bx bxs-cog"></i>
                                <span class="menu-title" data-i18n="User">{{ __('languages.sidebar.Categories') }}</span>
                            </a>
                        </li>
                        <li class="nav-item {{ (request()->is('setting')) ? 'active': ''  }}" ><a href="{{ url('setting') }}"><i class="bx bxs-cog"></i><span class="menu-title" data-i18n="User">{{ __('languages.sidebar.Settings') }}</span></a></li>
                        <li class="nav-item {{ (request()->is('language')) ? 'active': ''  }}" ><a href="{{ url('language') }}"><i class="bx bxs-cog"></i><span class="menu-title" data-i18n="User">{{ __('languages.Language.Language') }}</span></a>
                        </li>
                    </ul>
                </li>
            @endif
        </ul>
    </div>
</div>