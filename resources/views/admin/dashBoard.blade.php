<!DOCTYPE html>
<html lang="en">

<head>
    @include('admin.admincss')
    <link rel="stylesheet" href="./assets/css/dashboard.css">
    <title>Admin Dashboard</title>
</head>

<body class="g-sidenav-show  bg-gray-100">
  @include('admin.sidebar')


  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
    <!-- Navbar (Phần này đã được bạn xóa hoặc comment) -->
    <!-- ... -->
    <!-- End Navbar -->
    <div class="container-fluid py-4">
        <!-- Dashboard Header -->
        <div class="row mb-4">
            <div class="col-12">
                <h6 class="font-weight-bolder mb-0">Dashboard</h6>
            </div>
        </div>

      {{-- Hàng 1: Đơn hàng chờ xử lý, Tổng số đơn hàng, Tổng số sản phẩm, Tổng số người dùng --}}
      <div class="row">
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
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
        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
          <div class="card">
            <div class="card-body p-3">
              <div class="row">
                <div class="col-8">
                  <div class="numbers">
                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Tổng số đơn hàng</p>
                    <h5 class="font-weight-bolder mb-0">
                      {{ number_format($totalOrdersCount, 0, ',', '.') }}
                    </h5>
                  </div>
                </div>
                <div class="col-4 text-end">
                  <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                    <i class="ni ni-cart text-lg opacity-10" aria-hidden="true"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
          <div class="card">
            <div class="card-body p-3">
              <div class="row">
                <div class="col-8">
                  <div class="numbers">
                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Tổng số sản phẩm</p>
                    <h5 class="font-weight-bolder mb-0">
                      {{ number_format($totalProductsCount, 0, ',', '.') }}
                    </h5>
                  </div>
                </div>
                <div class="col-4 text-end">
                  <div class="icon icon-shape bg-gradient-danger shadow text-center border-radius-md">
                    <i class="ni ni-tag text-lg opacity-10" aria-hidden="true"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
          <div class="card">
            <div class="card-body p-3">
              <div class="row">
                <div class="col-8">
                  <div class="numbers">
                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Tổng số người dùng</p>
                    <h5 class="font-weight-bolder mb-0">
                      {{ number_format($totalUsersCount, 0, ',', '.') }}
                    </h5>
                  </div>
                </div>
                <div class="col-4 text-end">
                  <div class="icon icon-shape bg-gradient-dark shadow text-center border-radius-md">
                    <i class="ni ni-single-02 text-lg opacity-10" aria-hidden="true"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- Hàng 2: Doanh thu, Lợi nhuận (căn giữa) --}}
      <div class="row mt-4 justify-content-center"> {{-- Thêm class justify-content-center để căn giữa các cột --}}
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
          <div class="card">
            <div class="card-body p-3">
              <div class="row">
                <div class="col-8">
                  <div class="numbers">
                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Doanh thu</p>
                    <h5 class="font-weight-bolder mb-0">
                      {{ number_format($totalRevenue, 0, ',', '.') }} VNĐ
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
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
          <div class="card">
            <div class="card-body p-3">
              <div class="row">
                <div class="col-8">
                  <div class="numbers">
                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Lợi nhuận</p>
                    <h5 class="font-weight-bolder mb-0">
                      {{ number_format($totalProfit, 0, ',', '.') }} VNĐ
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

      <!-- NEW CHART SECTION -->
      <div class="row mt-4">
          <div class="col-lg-8 col-md-10 mx-auto"> <!-- Centered chart, adjust col-lg-X as needed -->
              <div class="card z-index-2 ">
                  <div class="card-header p-3">
                      <h6 class="mb-0">Biểu đồ Doanh thu & Lợi nhuận</h6>
                  </div>
                  <div class="card-body p-3">
                      <div class="chart">
                          <canvas id="revenueProfitChart" class="chart-canvas" height="300"></canvas>
                      </div>
                  </div>
              </div>
          </div>
      </div>

    </div>
  </main>
  <!--   Core JS Files   -->
  @include('admin.adminscript')

  <!-- Chart.js CDN -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
        var ctx = document.getElementById('revenueProfitChart');
        if (ctx) { // Check if the canvas element exists
            var revenueProfitChart = new Chart(ctx, {
                type: 'line', // Changed to 'line' chart type
                data: {
                    labels: ['Doanh thu', 'Lợi nhuận'], // Labels for the x-axis
                    datasets: [{
                        label: 'Giá trị (VNĐ)', // Label for the dataset
                        data: [{{ $totalRevenue }}, {{ $totalProfit }}], // Data from controller
                        borderColor: 'rgba(75, 192, 192, 1)', // Line color
                        backgroundColor: 'rgba(75, 192, 192, 0.2)', // Fill color under the line
                        borderWidth: 2, // Thickness of the line
                        pointRadius: 5, // Size of the data points
                        pointBackgroundColor: 'rgba(75, 192, 192, 1)', // Color of the data points
                        tension: 0.4, // Smoothness of the line (0 for straight, 0.4 for slight curve)
                        fill: true // Fill the area under the line
                    }]
                },
                options: {
                    responsive: true, // Chart will adjust its size
                    maintainAspectRatio: false, // Do not maintain aspect ratio, use defined height/width
                    scales: {
                        y: {
                            beginAtZero: true, // Start from 0 on the Y-axis
                            ticks: {
                                callback: function(value, index, values) {
                                    // Format Y-axis values as VNĐ
                                    return value.toLocaleString('vi-VN') + ' VNĐ';
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false // Hide grid lines for x-axis, common in line charts
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true // Display legend for line chart to show dataset label
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        // Format tooltip value as VNĐ
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
