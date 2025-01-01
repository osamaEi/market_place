@php
    $normalCount = \App\Models\NormalAds::count();
    $commercialCount = \App\Models\CommercialAd::count();
    $categories = \App\Models\Category::count();
  
@endphp

@extends('admin.index')

@section('content')

<link href="{{ asset('assets/css/dashboard-custom.css') }}" rel="stylesheet">

<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <div class="d-flex flex-column flex-column-fluid">
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxl">
                <!-- Welcome Section -->
                <div class="row mb-5">
                    <div class="col-12">
                        <div class="card bg-gradient-primary text-white">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <h2 class="text-white">{{ __('Welcome Back, Admin!') }}</h2>
                                    <p class="mb-0">{{ __('Here\'s what\'s happening with your platform today.') }}</p>
                                </div>
                                <div class="text-end">
                                    <h4 class="text-white">{{ now()->format('l, F j, Y') }}</h4>
                                </div>
                            </div> 
                        </div> 
                    </div> 
                </div> 

                <!-- Stats Cards Row -->
                <div class="row g-4 mb-5">
                    <!-- Normal Ads Card -->
                    <div class="col-lg-3 col-md-6">
                        <div class="card h-100 shadow-sm hover-elevate-up">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="symbol symbol-50px me-3">
                                        <div class="symbol-label bg-light-primary">
                                            <i class="fas fa-ad fs-2x text-primary"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h3 class="card-title mb-0">{{ __('Normal Ads') }}</h3>
                                        <span class="text-muted fs-7">{{ __('Total Listings') }}</span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span class="fs-2hx fw-bold text-dark me-2">{{ number_format($normalCount) }}</span>
                                    <span class="badge badge-light-success fs-base">
                                        <i class="fas fa-arrow-up me-1"></i>2.1%
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Commercial Ads Card -->
                    <div class="col-lg-3 col-md-6">
                        <div class="card h-100 shadow-sm hover-elevate-up">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="symbol symbol-50px me-3">
                                        <div class="symbol-label bg-light-success">
                                            <i class="fas fa-business-time fs-2x text-success"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h3 class="card-title mb-0">{{ __('Commercial Ads') }}</h3>
                                        <span class="text-muted fs-7">{{ __('Business Listings') }}</span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span class="fs-2hx fw-bold text-dark me-2">{{ number_format($commercialCount) }}</span>
                                    <span class="badge badge-light-success fs-base">
                                        <i class="fas fa-arrow-up me-1"></i>4.3%
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Categories Card -->
                    <div class="col-lg-3 col-md-6">
                        <div class="card h-100 shadow-sm hover-elevate-up">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="symbol symbol-50px me-3">
                                        <div class="symbol-label bg-light-warning">
                                            <i class="fas fa-list fs-2x text-warning"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h3 class="card-title mb-0">{{ __('Categories') }}</h3>
                                        <span class="text-muted fs-7">{{ __('Total Classifications') }}</span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span class="fs-2hx fw-bold text-dark me-2">{{ number_format($categories) }}</span>
                                    <span class="badge badge-light-warning fs-base">
                                        <i class="fas fa-arrow-up me-1"></i>1.9%
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Revenue Card -->
                    <div class="col-lg-3 col-md-6">
                        <div class="card h-100 shadow-sm hover-elevate-up">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="symbol symbol-50px me-3">
                                        <div class="symbol-label bg-light-info">
                                            <i class="fas fa-dollar-sign fs-2x text-info"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h3 class="card-title mb-0">{{ __('Revenue') }}</h3>
                                        <span class="text-muted fs-7">{{ __('Monthly Income') }}</span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span class="fs-2hx fw-bold text-dark me-2">$23,459</span>
                                    <span class="badge badge-light-info fs-base">
                                        <i class="fas fa-arrow-up me-1"></i>8.3%
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Row -->
                <div class="row g-4 mb-5">
                    <!-- Traffic Chart -->
                    <div class="col-lg-8">
                        <div class="card shadow-sm">
                            <div class="card-header border-0">
                                <h3 class="card-title">{{ __('Traffic Analytics') }}</h3>
                                <div class="card-toolbar">
                              
                                </div>
                            </div>
                            <div class="card-body">
                                <div id="traffic_chart" style="height: 350px"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Countries Distribution -->
                    <div class="col-lg-4">
                        <div class="card shadow-sm">
                            <div class="card-header border-0">
                                <h3 class="card-title">{{ __('Geographic Distribution') }}</h3>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-row-dashed align-middle gs-0 gy-3 my-0">
                                        <thead>
                                            <tr class="fs-7 fw-bold text-gray-400 border-bottom-0">
                                                <th>{{ __('COUNTRY') }}</th>
                                                <th class="text-end">{{ __('ADS') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                           @php
$countries = \App\Models\Country::withCount(['NormalAds', 'CommercialAds'])
    ->whereHas('NormalAds')
    ->orWhereHas('CommercialAds')
    ->get(); 
                                                @endphp 
                                         @foreach($countries as $country)
                                         <tr>
                                             <td>
                                                 <div class="d-flex align-items-center">
                                                     <span class="text-gray-800 fw-bold">{{ __($country->name) }}</span>
                                                 </div>
                                             </td>
                                             <td class="text-end">
                                                 <span class="text-gray-800 fw-bold">
                                                    {{ $country->normal_ads_count + $country->commercial_ads_count }}                                                 </span>
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

                <!-- Recent Activity -->
          
            </div>
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.35.3/apexcharts.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Counters Animation
    function animateCounters() {
        const counterElements = document.querySelectorAll('.fs-2hx');
        counterElements.forEach(counter => {
            const target = parseInt(counter.textContent.replace(/,/g, ''));
            const duration = 2000;
            const step = target / duration * 10;
            let current = 0;
            
            const updateCounter = () => {
                if (current < target) {
                    current += step;
                    if (current > target) current = target;
                    counter.textContent = Math.floor(current).toLocaleString();
                    requestAnimationFrame(updateCounter);
                }
            };
            
            updateCounter();
        });
    }

    // Enhanced Traffic Chart
    const trafficChart = new ApexCharts(document.querySelector("#traffic_chart"), {
        series: [{
            name: 'Normal Ads',
            data: [31, 40, 28, 51, 42, 109, 100]
        }, {
            name: 'Commercial Ads',
            data: [11, 32, 45, 32, 34, 52, 41]
        }],
        chart: {
            height: 350,
            type: 'area',
            toolbar: {
                show: false
            },
            animations: {
                enabled: true,
                easing: 'easeinout',
                speed: 800,
                animateGradually: {
                    enabled: true,
                    delay: 150
                },
                dynamicAnimation: {
                    enabled: true,
                    speed: 350
                }
            }
        },
        colors: ['#3699FF', '#2ECC71'],
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth',
            width: 2
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.7,
                opacityTo: 0.3,
                stops: [0, 90, 100]
            }
        },
        grid: {
            borderColor: '#f1f1f1',
            strokeDashArray: 4,
            xaxis: {
                lines: {
                    show: true
                }
            },
            yaxis: {
                lines: {
                    show: true
                }
            }
        },
        xaxis: {
            categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
            labels: {
                style: {
                    colors: '#787878',
                    fontFamily: 'inherit'
                }
            }
        },
        yaxis: {
            labels: {
                style: {
                    colors: '#787878',
                    fontFamily: 'inherit'
                }
            }
        },
        tooltip: {
            theme: 'dark',
            x: {
                format: 'MM/dd/yy'
            },
            y: {
                formatter: function(value) {
                    return value + " ads";
                }
            }
        }
    });
    trafficChart.render();

    // Intersection Observer for Animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate');
                observer.unobserve(entry.target);
                
                // Trigger counter animation when stats cards are visible
                if (entry.target.classList.contains('stats-card')) {
                    animateCounters();
                }
            }
        });
    }, observerOptions);

    // Observe elements for animation
    document.querySelectorAll('.card, .timeline-item').forEach(el => {
        observer.observe(el);
    });

    // Add loading shimmer effect
    function addShimmerEffect() {
        document.querySelectorAll('.card').forEach(card => {
            card.classList.add('shimmer');
            setTimeout(() => {
                card.classList.remove('shimmer');
            }, 2000);
        });
    }

    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl, {
            animation: true,
            delay: { show: 100, hide: 100 }
        });
    });

    // Add smooth scroll behavior
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Initialize
    addShimmerEffect();
    animateCounters();

    // Refresh data periodically (every 5 minutes)
    setInterval(() => {
        addShimmerEffect();
        // Here you would typically fetch new data from the server
        // For demo, we'll just animate the counters again
        animateCounters();
    }, 300000);

    // Handle window resize for responsive charts
    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            trafficChart.updateOptions({
                chart: {
                    height: window.innerWidth < 768 ? 250 : 350
                }
            });
        }, 250);
    });
});
</script>





@endsection

