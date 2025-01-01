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

<div class="container-fluid">
    <!-- Breadcrumb -->
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <!-- Breadcrumb content here -->
            </nav>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{__('Commercial Ad')}}
                        <span class="views-container">
                            {{$commercial->views_count}} <i class="fas fa-eye ms-1"></i>
                        </span>
                        <span class="views-container ml-3">
                            {{$commercial->comments->count()}} <i class="fas fa-comments ms-1"></i>
                        </span>
                        <span class="views-container ml-3">
                            {{$commercial->complains->count()}} <i class="fas fa-exclamation-triangle ms-1"></i>
                        </span>
                    </h5>

                    <div>
                        <form action="{{ route('commercialads.destroy', $commercial->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this ad?')">
                                <i class="fas fa-trash"></i> {{__('Delete')}}
                            </button>
                        </form>
                        <form action="{{ route('commercial.toggleStatus', $commercial->id) }}" method="POST" class="d-inline-block">
                            @csrf
                            <button type="submit" class="btn btn-dark btn-sm">
                                @if($commercial->is_active)
                                    {{ __('Mark as Not Active') }}
                                @else
                                    {{ __('Mark as Active') }}
                                @endif
                            </button>
                        </form>
                    </div>
                </div>
                
                <div class="card-body">
                    @if($commercial->photo_path)
                    <div class="text-center mb-4">
                        <img src="{{ asset('storage/' . $commercial->photo_path) }}" 
                             alt="{{ $commercial->title }}"
                             class="img-fluid rounded shadow-sm"
                             style="max-height: 400px;">
                    </div>
                    @endif

                    <div class="row">
                        <div class="col-md-12">
                            <h3 class="text-primary mb-3">{{ $commercial->title }}</h3>
                            
                            <div class="mb-4">
                                <h6 class="text-muted mb-2">{{__('Description')}}</h6>
                                <p class="lead">{{ $commercial->description ?? 'No description available' }}</p>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6 class="card-title text-muted">{{__('Category')}}</h6>
                                            <p class="card-text">{{ $commercial->category->title ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6 class="card-title text-muted">{{__('Country')}}</h6>
                                            <p class="card-text">{{ $commercial->country->name ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @if($commercial->comments->count() > 0)
            <div class="card shadow">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0" style="padding-top:24px;">{{__('Comments')}}</h5>
                </div>
                <div class="card-body">
                    @foreach($commercial->comments as $comment) 
                    <div class="media mb-3">
                        <div class="media-body">
                            <h6 class="mt-0">{{ $comment->customer->name ?? 'Anonymous' }}</h6>
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
                            <p>{{ $comment->text }}</p>
                            <small class="text-muted">{{ $comment->created_at->format('M d, Y H:i') }}</small>
                        
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
            <!-- Complaints Section -->
            @if($commercial->complains->count() > 0)
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card shadow">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0" style="padding-top:24px; ">{{__('complaints')}}</h5>
                        </div>
                        <div class="card-body"> 
                            @foreach($commercial->complains as $complain)
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

        <div class="col-md-4">
            <!-- Customer Information -->
            @if($commercial->customer)
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
                            <h6 class="mb-1">{{ $commercial->customer->name }}</h6>
                            <p class="text-muted mb-0">{{__('Customer ID')}}: #{{ $commercial->customer->id }}</p>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="mb-3">
                        <h6 class="text-muted mb-2">{{__('Contact Information')}}</h6>
                        <p class="mb-1"><i class="fas fa-envelope mr-2"></i> {{ $commercial->customer->email ?? 'N/A' }}</p>
                        <p class="mb-1"><i class="fas fa-phone mr-2"></i> {{ $commercial->customer->phone ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
            @endif
           
            <!-- Metadata -->
            <div class="card shadow">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"  style="padding-top: 22px;">{{__('Ad Metadata')}}</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{__('Created At')}}
                            <span class="badge badge-primary">{{ $commercial->created_at->format('M d, Y H:i') }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{__('Last Updated')}}
                            <span class="badge badge-info">{{ $commercial->updated_at->format('M d, Y H:i') }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{__('ID')}}
                            <span class="badge badge-secondary">#{{ $commercial->id }}</span>
                        </li>
                    </ul>
                </div>
            </div>

          
        </div>
    </div>
</div>

<style>
    .card {
        border: none;
        border-radius: 0.5rem;
    }
    
    .card-header {
        border-radius: 0.5rem 0.5rem 0 0 !important;
    }
    
    .img-fluid {
        border-radius: 0.5rem;
    }
    
    .breadcrumb {
        background-color: transparent;
        padding: 0;
    }
    
    .breadcrumb-item + .breadcrumb-item::before {
        content: "›";
    } 
</style>

@endsection 
 