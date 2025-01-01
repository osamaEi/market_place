@extends('admin.index')

@section('content')
<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <div class="d-flex flex-column flex-column-fluid">
        <!-- Main Content Container -->
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxl">
                
                <!-- Settings Toggle Section -->
                <div class="card mb-5 mb-xl-8">
                    <div class="card-header border-0">
                        <div class="card-title">
                            <h3 class="fw-bold m-0">{{ __('Comment Settings') }}</h3>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        @foreach ($settings as $setting)
                        <form class="form" action="{{ route('settings.update',$setting->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row align-items-center mb-3">
                                <div class="col-lg-4">
                                    <h3 class="fw-semibold text-dark">{{ __($setting->key) }}</h3>
                                </div>
                                <div class="col-lg-8">
                                    <label class="switch">
                                        <input type="checkbox" name="{{ $setting->key }}" value="1" 
                                            {{ $setting->value == 1 ? 'checked' : '' }} onchange="this.form.submit()">
                                        <span class="slider"></span>
                                    </label>
                                </div>
                            </div>
                        </form>
                        @endforeach
                    </div>
                </div>

                <!-- Comments Table Section -->
                <div class="card">
                    <div class="card-header border-0 py-6">
                        <div class="card-title">
                            <h3 class="fw-bold m-0">{{ __('Comments Management') }}</h3>
                        </div>
                        
                        <!-- Filter Section -->
                        <div class="card-toolbar">
                            <div class="d-flex flex-wrap gap-3">
                                <!-- Search Input -->
                                <div class="position-relative">
                                    <span class="position-absolute top-50 translate-middle-y ms-2">
                                        <i class="fas fa-search text-gray-500"></i>
                                    </span>
                                    <input type="text" id="searchInput" class="form-control form-control-solid ps-8" 
                                           placeholder="{{ __('Search comments...') }}">
                                </div>

                                <!-- Status Filter -->
                                <select id="statusFilter" class="form-select form-select-solid">
                                    <option value="">{{ __('All Status') }}</option>
                                    <option value="1">{{ __('Active') }}</option>
                                    <option value="0">{{ __('Inactive') }}</option>
                                </select>

                                <!-- Type Filter -->
                                <select id="typeFilter" class="form-select form-select-solid">
                                    <option value="">{{ __('All Types') }}</option>
                                    <option value="App\Models\CommercialAd">{{ __('Commercial Ads') }}</option>
                                    <option value="App\Models\NormalAds">{{ __('Normal Ads') }}</option>
                                </select>
                            </div>
                        </div>
                    </div> 
 
                    <div class="card-body p-0">
                        <!-- Table Section -->
                        <div class="table-responsive">
                            <table class="table align-middle table-row-dashed fs-6 gy-4" id="commentsTable">
                                <thead>
                                    <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                        <th class="min-w-150px">
                                            <div class="d-flex align-items-center cursor-pointer" onclick="sortTable(0)">
                                                {{ __('Ad-Title') }}
                                                <i class="fas fa-sort ms-2"></i>
                                            </div>
                                        </th>
                                        <th class="min-w-150px">
                                            <div class="d-flex align-items-center cursor-pointer" onclick="sortTable(1)">
                                                {{ __('Customer') }}
                                                <i class="fas fa-sort ms-2"></i>
                                            </div>
                                        </th>
                                        <th class="min-w-150px">{{ __('Photo') }}</th>
                                        <th class="min-w-200px">{{ __('Comment') }}</th>
                                        <th class="text-end min-w-100px">{{ __('Status') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="fw-semibold text-gray-600">
                                    @foreach($comments as $comment)
                                    <tr data-status="{{ $comment->status }}" 
                                        data-type="{{ $comment->commentable_type }}"
                                        data-title="{{ strtolower($comment->commentable->title ?? '') }}"
                                        data-customer="{{ strtolower($comment->customer->name) }}"
                                        data-comment="{{ strtolower($comment->text) }}">
                                        <td>{{ $comment->commentable->title ?? 'No Title' }}</td>
                                        <td>{{ $comment->customer->name }}</td>
                                        <td>
                                            @if($comment->commentable_type == 'App\Models\CommercialAd')
                                                @if($comment->commentable && $comment->commentable->photo_path)
                                                    <div class="symbol symbol-50px">
                                                        <img src="{{ asset($comment->commentable->photo_path) }}" 
                                                             class="rounded-3" alt="Photo">
                                                    </div>
                                                @else
                                                    <span class="badge badge-light-warning">No Photo</span>
                                                @endif
                                            @elseif($comment->commentable_type == 'App\Models\NormalAds')
                                                @if($comment->commentable && $comment->commentable->photo)
                                                    <div class="symbol symbol-50px">
                                                        <img src="{{ asset($comment->commentable->photo) }}" 
                                                             class="rounded-3" alt="Photo">
                                                    </div>
                                                @else
                                                    <span class="badge badge-light-warning">No Photo</span>
                                                @endif
                                            @endif
                                        </td>
                                        <td>{{ $comment->text }}</td>
                                        <td class="text-end">
                                            <form action="{{ route('comment.toggle', $comment->id) }}" 
                                                  method="POST" class="d-inline-block">
                                                @csrf
                                                @method('PUT')
                                                <label class="switch">
                                                    <input type="checkbox" name="comment_toggle" value="1" 
                                                           {{ $comment->status == 1 ? 'checked' : '' }} 
                                                           onchange="this.form.submit()">
                                                    <span class="slider"></span>
                                                </label>
                                            </form>

                                            <form action="{{ route('comment.destroy', $comment->id) }}" 
                                                method="POST" class="d-inline-block">
                                              @csrf
                                              @method('DELETE')
                                              <button type="submit" 
                                                      class="btn btn-sm btn-light-danger" 
                                                      onclick="return confirm('Are you sure you want to delete this comment?')">
                                                  <i class="fas fa-trash"></i>
                                              </button>
                                          </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Enhanced Switch Style */
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
    background-color: #eee;
    transition: .4s;
    border-radius: 34px;
    box-shadow: inset 0 0 4px rgba(0,0,0,0.1);
}

.slider:before {
    position: absolute;
    content: "";
    height: 20px;
    width: 20px;
    left: 2px;
    bottom: 2px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

input:checked + .slider {
    background-color: #4361ee;
}

input:checked + .slider:before {
    transform: translateX(26px);
}

/* Table Styles */
.table {
    margin: 0;
}

.table th {
    font-weight: 600;
    padding: 1rem 1.5rem;
    background: #f9fafb;
    border-bottom: 1px solid #eee;
}

.table td {
    padding: 1rem 1.5rem;
    vertical-align: middle;
    border-bottom: 1px solid #eee;
}

/* Filter Styles */
.form-control, .form-select {
    min-height: 42px;
    border: 1px solid #e4e6ef;
    padding: 0.5rem 1rem;
    font-size: 0.95rem;
    border-radius: 0.475rem;
    transition: all 0.2s ease;
}

.form-control:focus, .form-select:focus {
    border-color: #4361ee;
    box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.25);
}

/* Card Styles */
.card {
    border: none;
    box-shadow: 0 0 20px rgba(76, 87, 125, 0.02);
    border-radius: 0.75rem;
}

.card-header {
    background-color: transparent;
    border-bottom: 1px solid #eee;
}

/* Animation */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

tr {
    animation: fadeIn 0.3s ease-in-out;
}

/* Responsive Design */
@media (max-width: 992px) {
    .card-toolbar {
        flex-direction: column;
        gap: 1rem;
        width: 100%;
    }
    
    .d-flex.flex-wrap {
        flex-direction: column;
        width: 100%;
    }
    
    .position-relative, .form-select {
        width: 100%;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const typeFilter = document.getElementById('typeFilter');
    const table = document.getElementById('commentsTable');
    const rows = table.getElementsByTagName('tr');

    // Function to filter table
    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const statusValue = statusFilter.value;
        const typeValue = typeFilter.value;

        for (let i = 1; i < rows.length; i++) {
            const row = rows[i];
            const title = row.getAttribute('data-title') || '';
            const customer = row.getAttribute('data-customer') || '';
            const comment = row.getAttribute('data-comment') || '';
            const status = row.getAttribute('data-status');
            const type = row.getAttribute('data-type');

            const matchesSearch = title.includes(searchTerm) || 
                                customer.includes(searchTerm) || 
                                comment.includes(searchTerm);
            const matchesStatus = statusValue === '' || status === statusValue;
            const matchesType = typeValue === '' || type === typeValue;

            if (matchesSearch && matchesStatus && matchesType) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        }
    }

    // Event listeners
    searchInput.addEventListener('input', filterTable);
    statusFilter.addEventListener('change', filterTable);
    typeFilter.addEventListener('change', filterTable);

    // Sorting functionality
    window.sortTable = function(n) {
        let switching = true;
        let dir = 'asc';
        let switchcount = 0;

        while (switching) {
            switching = false;
            const rows = table.rows;

            for (let i = 1; i < (rows.length - 1); i++) {
                let shouldSwitch = false;
                const x = rows[i].getElementsByTagName('td')[n];
                const y = rows[i + 1].getElementsByTagName('td')[n];

                if (dir === 'asc') {
                    if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
                        shouldSwitch = true;
                        break;
                    }
                } else if (dir === 'desc') {
                    if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
                        shouldSwitch = true;
                        break;
                    }
                }

                if (shouldSwitch) {
                    rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
                    switching = true;
                    switchcount++;
                } else {
                    if (switchcount === 0 && dir === 'asc') {
                        dir = 'desc';
                        switching = true;
                    }
                }
            }
        }
    }
});
</script>

@endsection