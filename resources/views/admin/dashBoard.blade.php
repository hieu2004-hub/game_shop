<!DOCTYPE html>
<html lang="en">

<head>
    @include('admin.admincss')
    <link rel="stylesheet" href="./assets/css/dashboard.css">
    <title>Admin Dashboard</title>
    <style>
        /* Style để biến card thành link có thể click */
        .card-link {
            text-decoration: none;
            color: inherit;
            display: block;
            transition: transform 0.2s ease-in-out;
        }
        .card-link:hover .card {
            transform: translateY(-5px);
            box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
        }
    </style>
</head>

<body class="g-sidenav-show  bg-gray-100">
  @include('admin.sidebar')

  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12">
                <h6 class="font-weight-bolder mb-0">Dashboard</h6>
            </div>
        </div>

      <!-- SỬA: Hàng card hiển thị thông tin mới -->
      <div class="row">
        <!-- Card 1: Đơn hàng chờ xử lý (Clickable) -->
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
          <a href="{{ route('admin.viewOrders', ['status' => 'Chờ Xử Lý']) }}" class="card-link">
              <div class="card">
                <div class="card-body p-3">
                  <div class="row">
                    <div class="col-8">
                      <div class="numbers">
                        <p class="text-sm mb-0 text-capitalize font-weight-bold">Đơn hàng chờ xử lý</p>
                        <h5 class="font-weight-bolder mb-0">
                          {{ number_format($pendingOrdersCount, 0, ',', '.') }}
                        </h5>
                      </div>
                    </div>
                    <div class="col-4 text-end">
                      <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md">
                        <i class="ni ni-box-2 text-lg opacity-10" aria-hidden="true"></i>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
          </a>
        </div>
        <!-- Card 2: Doanh thu hôm nay -->
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
          <div class="card">
            <div class="card-body p-3">
              <div class="row">
                <div class="col-8">
                  <div class="numbers">
                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Doanh thu hôm nay</p>
                    <h5 class="font-weight-bolder mb-0">
                      {{ number_format($dailyRevenue, 0, ',', '.') }} <span class="text-sm">VNĐ</span>
                    </h5>
                  </div>
                </div>
                <div class="col-4 text-end">
                  <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                    <i class="ni ni-money-coins text-lg opacity-10" aria-hidden="true"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- Card 3: Doanh thu tháng này -->
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
          <div class="card">
            <div class="card-body p-3">
              <div class="row">
                <div class="col-8">
                  <div class="numbers">
                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Doanh thu tháng này</p>
                    <h5 class="font-weight-bolder mb-0">
                      {{ number_format($monthlyRevenue, 0, ',', '.') }} <span class="text-sm">VNĐ</span>
                    </h5>
                  </div>
                </div>
                <div class="col-4 text-end">
                  <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                    <i class="ni ni-calendar-grid-58 text-lg opacity-10" aria-hidden="true"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- Card 4: Lợi nhuận tháng này -->
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
          <div class="card">
            <div class="card-body p-3">
              <div class="row">
                <div class="col-8">
                  <div class="numbers">
                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Lợi nhuận tháng này</p>
                    <h5 class="font-weight-bolder mb-0">
                      {{ number_format($monthlyProfit, 0, ',', '.') }} <span class="text-sm">VNĐ</span>
                    </h5>
                  </div>
                </div>
                <div class="col-4 text-end">
                  <div class="icon icon-shape bg-gradient-success shadow text-center border-radius-md">
                    <i class="ni ni-chart-bar-32 text-lg opacity-10" aria-hidden="true"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- SỬA: Biểu đồ doanh thu 7 ngày qua -->
      <div class="row mt-4">
          <div class="col-12">
              <div class="card z-index-2 ">
                  <div class="card-header p-3">
                      <h6 class="mb-0">Doanh thu 7 ngày qua</h6>
                  </div>
                  <div class="card-body p-3">
                      <div class="chart">
                          <canvas id="revenueChart7Days" class="chart-canvas" height="300"></canvas>
                      </div>
                  </div>
              </div>
          </div>
      </div>

    </div>
  </main>

  @include('admin.adminscript')

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
        var ctx = document.getElementById('revenueChart7Days');
        if (ctx) {
            var revenueChart = new Chart(ctx, {
                type: 'bar', // hoặc 'line' tùy bạn thích
                data: {
                    labels: {!! json_encode($chartLabels) !!}, // Lấy nhãn từ controller
                    datasets: [{
                        label: 'Doanh thu',
                        data: {!! json_encode($chartValues) !!}, // Lấy dữ liệu từ controller
                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return value.toLocaleString('vi-VN') + ' VNĐ';
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        label += context.parsed.y.toLocaleString('vi-VN') + ' VNĐ';
                                    }
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        }
    });
  </script>
</body>

</html>
