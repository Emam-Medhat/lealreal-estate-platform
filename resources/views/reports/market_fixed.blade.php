@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Data prepared by Blade
    window.marketReportData = {
        marketTrendsLabels: {{ $marketTrends->pluck('month')->toJson() }},
        averagePriceData: {{ $marketTrends->pluck('average_price')->toJson() }},
        propertyCountData: {{ $marketTrends->pluck('property_count')->toJson() }},
        priceByTypeLabels: {{ $priceByType->pluck('property_type')->toJson() }},
        priceByTypeData: {{ $priceByType->pluck('average_price')->toJson() }},
        inventoryLabels: {{ $inventoryAnalysis->pluck('property_type')->toJson() }},
        inventoryData: {{ $inventoryAnalysis->pluck('total_count')->toJson() }},
        priceRangesLabels: {{ $priceRanges->pluck('range')->toJson() }},
        priceRangesData: {{ $priceRanges->pluck('count')->toJson() }},
        searchTrendsLabels: {{ $searchTrends->pluck('date')->toJson() }},
        searchTrendsData: {{ $searchTrends->pluck('volume')->toJson() }},
        demandByTypeLabels: {{ $demandByType->pluck('type')->toJson() }},
        demandByTypeData: {{ $demandByType->pluck('demand_index')->toJson() }}
    };

    // Market Trends Chart
    const marketTrendsCtx = document.getElementById('marketTrendsChart').getContext('2d');
    new Chart(marketTrendsCtx, {
        type: 'line',
        data: {
            labels: window.marketReportData.marketTrendsLabels,
            datasets: [{
                label: 'متوسط السعر',
                data: window.marketReportData.averagePriceData,
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1,
                yAxisID: 'y'
            }, {
                label: 'عدد العقارات',
                data: window.marketReportData.propertyCountData,
                borderColor: 'rgb(255, 99, 132)',
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                tension: 0.1,
                yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'السعر'
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    grid: {
                        drawOnChartArea: false,
                    },
                    title: {
                        display: true,
                        text: 'العدد'
                    }
                }
            }
        }
    });

    // Market Distribution Chart
    const marketDistributionCtx = document.getElementById('marketDistributionChart').getContext('2d');
    new Chart(marketDistributionCtx, {
        type: 'doughnut',
        data: {
            labels: window.marketReportData.priceByTypeLabels,
            datasets: [{
                data: window.marketReportData.priceByTypeData,
                backgroundColor: [
                    'rgb(255, 99, 132)',
                    'rgb(54, 162, 235)',
                    'rgb(255, 205, 86)',
                    'rgb(75, 192, 192)',
                    'rgb(153, 102, 255)'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                }
            }
        }
    });

    // Price Distribution Chart
    const priceDistributionCtx = document.getElementById('priceDistributionChart').getContext('2d');
    new Chart(priceDistributionCtx, {
        type: 'bar',
        data: {
            labels: window.marketReportData.priceRangesLabels,
            datasets: [{
                label: 'عدد العقارات',
                data: window.marketReportData.priceRangesData,
                backgroundColor: 'rgba(54, 162, 235, 0.8)',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Search Trends Chart
    const searchTrendsCtx = document.getElementById('searchTrendsChart').getContext('2d');
    new Chart(searchTrendsCtx, {
        type: 'line',
        data: {
            labels: window.marketReportData.searchTrendsLabels,
            datasets: [{
                label: 'حجم البحث',
                data: window.marketReportData.searchTrendsData,
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Demand by Type Chart
    const demandByTypeCtx = document.getElementById('demandByTypeChart').getContext('2d');
    new Chart(demandByTypeCtx, {
        type: 'bar',
        data: {
            labels: window.marketReportData.demandByTypeLabels,
            datasets: [{
                label: 'مؤشر الطلب',
                data: window.marketReportData.demandByTypeData,
                backgroundColor: 'rgba(255, 99, 132, 0.8)',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});
</script>
@endpush
