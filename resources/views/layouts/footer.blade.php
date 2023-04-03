<!-- BEGIN: Footer-->
@php
$sitesettings = Helper::getsitesettings();
@endphp
<footer class="footer footer-static footer-light">
    <p class="clearfix mb-0"><span class="float-left d-inline-block">2020 &copy; {{ !empty($sitesettings->SiteName) ? $sitesettings->SiteName : __('languages.sidebar.Scout') }}</span>
        <button class="btn btn-primary btn-icon scroll-top" type="button"><i class="bx bx-up-arrow-alt"></i></button>
    </p>
</footer>
<!-- END: Footer-->