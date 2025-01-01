@extends('admin.index')
@section('content')

                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif <!--begin::Content-->
                <div id="kt_app_content" class="app-content flex-column-fluid">
                    <!--begin::Content container-->
                    <div id="kt_app_content_container" class="app-container container-xxl">
                        <!--begin::Products Table-->
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">{{ __('PopUp')}}</h3>
                            </div>
                            <div class="card-body">
                           <form action="{{ route('popup.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label for="name">{{__('Name')}}</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                    
                            <div class="form-group">
                                <label for="description">{{__('Description')}}</label>
                                <textarea name="description" class="form-control"></textarea>
                            </div>
                    
                            <div class="form-group">
                                <label for="country_id">{{__('Country')}}</label>
                                <select name="country_id" class="form-control" required>
                                    <option value="">{{__('Select Country')}}</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country->id }}">{{ __($country->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                    
                            <div class="form-group">
                                <label for="photo">{{__('Photo')}}</label>
                                <input type="file" name="photo" class="form-control">
                            </div>
                    
                        
                            <button type="submit" class="btn btn-secondary">{{__('Save')}}</button>
                        </form>
                 
                        

     </div>
            </div>
        </div>
    </div>
</div>

</div>



@endsection



@section('js')

<script src="{{ asset('assets/plugins/custom/formrepeater/formrepeater.bundle.js')}}"></script>
<!--end::Vendors Javascript-->
<!--begin::Custom Javascript(used for this page only)-->
<script src="{{ asset('assets/js/custom/apps/ecommerce/catalog/save-product.js')}}"></script>


@endsection