@extends('admin.index')

@section('content')
<style>
    .switch {
        position: relative;
        display: inline-block;
        width: 50px;
        height: 24px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: .4s;
        border-radius: 34px;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 20px;
        width: 20px;
        left: 4px;
        bottom: 2px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }

    input:checked + .slider {
        background-color: #393cdb;
    }

    input:checked + .slider:before {
        transform: translateX(26px);
    }
</style>


<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <!--begin::Content wrapper-->
    <div class="d-flex flex-column flex-column-fluid">
        <!--begin::Toolbar-->
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <!--begin::Toolbar container-->
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                <!--begin::Page title-->
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <!--begin::Title-->
                    <!--end::Title-->
                    <!--begin::Breadcrumb-->
                    <!--end::Breadcrumb-->
                </div>
                <!--end::Page title-->
                <!--begin::Actions-->
                <!--end::Actions-->
            </div>
            <!--end::Toolbar container-->
        </div>
        <!--end::Toolbar-->
        <!--begin::Content-->
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <!--begin::Content container-->
            <div id="kt_app_content_container" class="app-container container-xxl">
                <!--begin::Navbar-->
                <!--end::Navbar-->
                <!--begin::Card-->
                <div class="card">
                    <!--begin::Card header-->
                    <div class="card-header">
                        <!--begin::Card title-->
                        <div class="card-title fs-3 fw-bold">   
                            
                            {{__('All Complains')}}
                      
                </div>
                        <!--end::Card title-->
                    </div>
                

                    <div class="card-body p-9">
                        <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_ecommerce_category_table">
                            <thead>
                                <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                    <th class="min-w-150px">{{ __('Ad-Title') }}</th>
                                    <th class="min-w-150px">{{ __('Customer') }}</th>
                                    <th class="min-w-150px">{{ __('Photo') }}</th>
                                    <th class="min-w-70px">{{ __('complain') }}</th>
                                    <th class="min-w-70px">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="fw-semibold text-gray-600">

                                @foreach($complains as $complain)
                                <tr>
                                    {{-- Title --}}
                                    <td>
                                        {{ $complain->complainable->title ?? 'No Title' }}
                                    </td>
                            
                                    {{-- Customer --}}
                                    <td>
                                        {{ $complain->customer->name }}
                                    </td>
                            
                                    {{-- Photo --}}
                                    <td>
                                        @if($complain->complainable_type == 'App\Models\CommercialAd')
                                            @if($complain->complainable && $complain->complainable->photo_path)
                                                <img src="{{ asset($complain->complainable->photo_path) }}" alt="Photo" width="50" height="50">
                                            @else
                                                No Photo
                                            @endif
                                        @elseif($complain->complainable_type == 'App\Models\NormalAds')
                                            @if($complain->complainable && $complain->complainable->photo)
                                                <img src="{{ asset($complain->complainable->photo) }}" alt="Photo" width="50" height="50">
                                            @else
                                                No Photo
                                            @endif 
                                        @endif
                                    </td> 
                            
                                    {{-- complain --}}
                                    <td>
                                        {{ $complain->text }}
                                    </td>
                                    <td><form action="{{ route('complain.destroy', $complain->id) }}" 
                                        method="POST" class="d-inline-block">
                                      @csrf
                                      @method('DELETE')
                                      <button type="submit" 
                                              class="btn btn-sm btn-light-danger" 
                                              onclick="return confirm('Are you sure you want to delete this comment?')">
                                          <i class="fas fa-trash"></i>
                                      </button>  
                                  </form></td>  
                            
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    

                </form>
                        


                          


                            <!-- Form Actions -->
                          
                        </div>
                    </form>
                    <!--end::Form-->
                </div>
                <!--end::Card-->
            </div>
            <!--end::Content container-->
        </div>
        <!--end::Content-->
    </div>
    <!--end::Content wrapper-->
</div>
@endsection
