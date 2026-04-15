@extends('layouts.app')

<style type="text/css">
    .validation-settings-wrap .btn-sm{
        height: 30px;
        width: 30px;
    }
    .validation-settings-wrap .gap-2{
        gap: 5px;
        align-items: center;
    }
    .validation-settings-wrap .gap-2 form{
        margin-bottom: 0px;
    }
    .validation-settings-wrap td{
        vertical-align: middle;
    }
</style>



@section('content')
    <!-- CONTENT WRAPPER START -->
    <div class="content-wrapper validation-settings-wrap">

        <div class="w-100">
            <div class="s-b-inner s-b-notifications bg-white b-shadow-4 rounded">
      
                
                    <div class="s-b-n-header" id="tabs">
                        <nav class="tabs px-4 border-bottom-grey">
                            <div class="nav" id="nav-tab" role="tablist">
                                <a class="nav-item nav-link f-15 active validation-manage"
                                    href="{{ route('validation-settings.index') }}" role="tab" aria-controls="nav-validation"
                                    aria-selected="true">Validation Layer
                                </a>

                                
                                    <a class="nav-item nav-link f-15 validation-settings"
                                    href="{{ route('validation-settings.index') }}?tab=validation-settings" role="tab"
                                    aria-controls="nav-validation" aria-selected="true" ajax="false">Validation Settings
                                    </a>
                                
                            </div>
                        </nav>
                    </div>
               

                {{-- include tabs here --}}
                @include($view)
            </div>
        </div>
    </div>
    <!-- CONTENT WRAPPER END -->

@endsection

@push('scripts')



    <script>
        $('.nav-item').removeClass('active');
        const activeTab = "{{ $activeTab }}";
        $('.' + activeTab).addClass('active');

        showBtn(activeTab);

        function showBtn(activeTab) {
            $('.actionBtn').addClass('d-none');
            $('.' + activeTab + '-btn').removeClass('d-none');
        }

        $('.validation-settings-wrap .settings-box').removeClass('settings-box');


    </script>
@endpush
