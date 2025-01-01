@extends('admin.index')

@section('content')
<div class="container">
    <div class="row">
        <!-- Bike Details -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">{{ $bike->title }}</h3>
                </div>
                <div class="card-body">

                    @if ($bike->images->isNotEmpty())
                        <div class="mb-3">
                            <strong>{{__('Images')}}:</strong><br>
                            <div class="row">
                                @foreach ($bike->images as $image)
                                    <div class="col-md-4 mb-3">
                                        <img src="{{ asset('storage/' . $image->photo) }}" alt="Image for {{ $bike->title }}" class="img-fluid" style="max-height: 200px; cursor: pointer;" onclick="showPopup('{{ asset('storage/' . $image->photo) }}')">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <p>No images available.</p>
                    @endif

                    @if($bike->specifications->isNotEmpty())
                        <ul>
                            @foreach($bike->specifications as $spec)
                                <li><strong>{{__('Model')}}:</strong> {{ $spec->model ?? 'N/A' }}</li>
                                <li><strong>{{__('Year')}}:</strong> {{ $spec->year ?? 'N/A' }}</li>
                                <li><strong>{{__('Kilometers')}}:</strong> {{ $spec->kilo_meters ?? 'N/A' }}</li>
                                <li><strong>{{__('Status')}}:</strong> {{ $spec->status ?? 'N/A' }}</li>
                                <li><strong>{{__('Location')}}:</strong> {{ $spec->location ?? 'N/A' }}</li>
                            @endforeach
                        </ul>
                    @else
                        <p>No specifications</p>
                    @endif

                    <div class="mb-3">
                        <strong>{{__('Description')}}:</strong>
                        <p>{{ $bike->description ?? 'N/A' }}</p>
                    </div>

                    @if ($bike->features->isNotEmpty())
                        <div class="mb-3">
                            <strong>{{__('Features')}}:</strong>
                            <div class="d-flex flex-wrap">
                                @foreach ($bike->features as $feature)
                                    <span class="badge bg-success me-2 mb-2">{{ $feature->title }}</span>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <p>{{__('No features listed')}}.</p>
                    @endif

                </div>
                <div class="card-footer text-end">
                    <a href="{{ route('bike.index') }}" class="btn btn-primary">{{__('Back to List')}}</a>
                    <a href="{{ route('bike.edit', $bike->id) }}" class="btn btn-warning">{{__('Edit')}}</a>
                </div>
            </div>
        </div>

        <!-- Sidebar or additional content -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">{{__('Bike Details')}}</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>{{__('Category')}}:</strong> {{ $bike->category->title ?? 'N/A' }}
                    </div>
                    
                    <div class="mb-3">
                        <strong>{{__('Price')}}:</strong> {{ \App\Helpers\ConvertCurrency::convertPrice($bike->price, session('currency_code', 'USD')) }}
                    </div>
                    
                    <!-- Add more details if needed -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Popup Image Container -->
<div id="popupContainer" class="popup-container" style="display: none;">
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
        background: rgba(0, 0, 0, 0.7);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
    }
    .popup-image {
        max-width: 90%;
        max-height: 80%;
    }
    .popup-close {
        position: absolute;
        top: 10px;
        right: 20px;
        font-size: 2em;
        color: #fff;
        cursor: pointer;
    }
</style>

<script>
    function showPopup(src) {
        document.getElementById('popupImage').src = src;
        document.getElementById('popupContainer').style.display = 'flex';
    }

    function closePopup() {
        document.getElementById('popupContainer').style.display = 'none';
    }
</script>
@endsection