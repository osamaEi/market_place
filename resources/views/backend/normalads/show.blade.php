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
    .views-container {
        background-color: #f0f0f0; /* Light gray background */
        padding: 5px 10px; /* Padding around the text and icon */
        border-radius: 20px; /* Rounded corners */
        display: inline-flex; /* Align icon and text inline */
        align-items: center; /* Vertically center content */
        font-size: 14px; /* Adjust font size if needed */
        color: #333; /* Text color */
    }
    
    .views-container i {
        margin-left: 5px; /* Space between the count and the icon */
    }
     
    
     
    
     
    </style>
<div class="container">
    <div class="row">
        <!-- Main Ad Details -->
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-secondary text-white">
                  
                    <h5 style="padding-top: 23px;">{{ $normalAd->title }} 
                   
                       <span class="views-container">
                            {{$normalAd->views_count}} <i class="fas fa-eye"></i>
                        </span>
                        <span class="views-container ml-3">
                            {{$normalAd->comments->count()}} <i class="fas fa-comments"></i>
                        </span>
                        <span class="views-container ml-3">
                            {{$normalAd->complains->count()}} <i class="fas fa-exclamation-triangle"></i>
                        </span>
                    </h5>
 
                    <div style="padding-top: 12px;">
                     
                      
                        <form action="{{ route('normalads.destroy', $normalAd->id) }}" method="POST" class="d-inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">{{__('Delete')}}</button>
                        </form>
                        <form action="{{ route('normalads.toggleStatus', $normalAd->id) }}" method="POST" class="d-inline-block">
                            @csrf
                            <button type="submit" class="btn btn-dark btn-sm">
                                @if($normalAd->is_active)
                                {{__('Mark as Not Active')}}
                                @else
                                {{__('Mark as Active')}}
                                @endif
                            </button>
                        </form>

                    </div>
                </div>




                <div class="card-body">

                    @if ($normalAd->photo)
                    <div class="mb-4 text-center position-relative">
                        @if ($normalAd->is_featured)
                            <span class="badge bg-primary position-absolute top-0 start-0 m-2 z-1" style="z-index: 10;">
                                <i class="fas fa-star"></i> Featured
                            </span>
                        @endif
                        
                        <img src="{{ asset('storage/' . $normalAd->photo) }}" 
                             alt="{{ $normalAd->title }}" 
                             class="img-fluid rounded shadow" 
                             id="previewImage" 
                             style="cursor: pointer;" 
                             onclick="showPopup('{{ asset('storage/' . $normalAd->photo) }}')">
                    </div>
                @endif
                    <div class="mb-3">
                        <strong>{{__('Description')}}:</strong>
                        <p>{{ $normalAd->description }}</p>
                    </div>
                    <div class="mb-3">
                        <span class="badge {{ $normalAd->is_active ? 'badge-success' : 'badge-secondary' }}">
                            {{ __($normalAd->is_active ? 'Published' : 'Draft') }}
                        </span>
                    </div>
                
                    <div class="mb-3">
                        <strong>{{__('Additional Images')}}:</strong>
                        @if($normalAd->images->isNotEmpty())
                            <div class="row g-2">
                                @foreach ($normalAd->images as $image)
                                    <div class="col-md-2">
                                        <img src="{{ asset('storage/' . $image->image_path) }}" alt="Image for {{ $normalAd->title }}" class="img-thumbnail shadow-sm" style="cursor: pointer;" onclick="showPopup('{{ asset('storage/' . $image->image_path) }}')">
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted">{{__('No additional images available')}}.</p>
                        @endif
                    </div>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title text-muted">{{__('Category')}}</h6>
                                    <p class="card-text">{{ $normalAd->category->title ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title text-muted">{{__('Country')}}</h6>
                                    <p class="card-text">{{ $normalAd->country->name ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-end">
                        <a href="{{ route('normalads.index') }}" class="btn btn-outline-primary">{{__('Back to List')}}</a>
                    </div>
                </div>
            </div>
            @if($normalAd->comments->count() > 0)
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card shadow">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0" style="  padding-top: 24px;">{{__('Comments')}}</h5>
                        </div>
                        <div class="card-body"> 
                            @foreach($normalAd->comments as $comment)
                            <div class="media mb-3">
                                <div class="media-body">
                                    <h6 class="mt-0">{{ $comment->customer->name ?? 'Anonymous' }}</h6>
                                    <p>{{ $comment->text }}</p>
                                    <small class="text-muted">{{ $comment->created_at->format('M d, Y H:i') }}</small>
                                    <form class="form" action="{{ route('comment.toggle',$comment->id)}} " method="POST" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
        
                                        <label class="switch">
                                            <input 
                                                type="checkbox" 
                                                name="comment_toggle" 
                                                value="1" 
                                                {{ $comment->status == 1 ? 'checked' : '' }} 
                                                onchange="this.form.submit()">
                                            <span class="slider"></span>
                                        </label>
                                    </form>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endif
        
            <!-- Complaints Section -->
            @if($normalAd->complains->count() > 0) 
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card shadow"> 
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0" style="  padding-top: 24px;">{{__('Complaints')}}</h5>
                        </div>
                        <div class="card-body"> 
                            @foreach($normalAd->complains as $complain)
                            <div class="media mb-3"> 
                                <div class="media-body">
                                    <h6 class="mt-0">{{ $complain->customer->name ?? 'Anonymous' }}</h6>
                                    <p>{{ $complain->text }}</p>
                                    <small class="text-muted">{{ $complain->created_at->format('M d, Y H:i') }}</small>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
   
        <!-- Sidebar with Specific Details -->
        <div class="col-md-4">
            @if($normalAd->customer)
            <div class="card shadow">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0" style="padding-top: 22px;">{{__('Customer Information')}}</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle bg-primary text-white p-3 mr-3">
                            <i class="fas fa-user fa-2x"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">{{ $normalAd->customer->name }}</h6>
                            <p class="text-muted mb-0">{{__('Customer ID')}}: #{{ $normalAd->customer->id }}</p>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="mb-3">
                        <h6 class="text-muted mb-2">{{__('Contact Information')}}</h6>
                        <p class="mb-1"><i class="fas fa-envelope mr-2"></i> {{ $normalAd->customer->email ?? 'N/A' }}</p>
                        <p class="mb-1"><i class="fas fa-phone mr-2"></i> {{ $normalAd->customer->phone ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
            @endif

            @foreach (['cars', 'bikes', 'houses', 'mobiles'] as $type)
            @if($normalAd->$type)
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="card-title mb-0">{{ __(ucfirst($type)) }} {{ __('Details') }}</h5>
                    </div>
                    <div class="card-body">
                        @foreach($normalAd->$type->getAttributes() as $key => $value)
                            @if($key != 'id' && $value)
                                <div class="mb-2"> 
                                    <strong>{{ __(ucfirst(str_replace('_', ' ', $key))) }}:</strong> {{ $value }}
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach
        

            <div class="card shadow">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"  style="padding-top: 22px;">{{__('Ad Metadata')}}</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{__('Created At')}}
                            <span class="badge badge-primary">{{ $normalAd->created_at->format('M d, Y H:i') }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{__('Last Updated')}}
                            <span class="badge badge-info">{{ $normalAd->updated_at->format('M d, Y H:i') }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{__('ID')}}
                            <span class="badge badge-secondary">#{{ $normalAd->id }}</span>
                        </li>
                    </ul>
                </div>
            </div>
       
        </div>
    </div>
</div>

<!-- Popup for Image Viewer -->
<div id="popupContainer" class="popup-container d-none">
    <span class="popup-close" onclick="closePopup()">&times;</span>
    <img id="popupImage" class="popup-image" src="" alt="Popup Image">
</div>

<style>
    .popup-container {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.8);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1050;
    }
    .popup-container.d-none {
        display: none;
    }
    .popup-image {
        max-width: 90%;
        max-height: 90%;
        border: 3px solid #fff;
        border-radius: 5px;
    }
    .popup-close {
        position: absolute;
        top: 20px;
        right: 30px;
        font-size: 2rem;
        color: #fff;
        cursor: pointer;
        z-index: 1051;
    }
</style>

<script>
    function showPopup(src) {
        const popup = document.getElementById('popupContainer');
        document.getElementById('popupImage').src = src;
        popup.classList.remove('d-none');
    }

    function closePopup() {
        document.getElementById('popupContainer').classList.add('d-none');
    }
</script>
@endsection
