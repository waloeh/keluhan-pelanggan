<template>
	<div class="container mt-5">
    <div class="row">
      <div class="col-md-12">
        <div class="row">
          <div class="col-md-4">
            <div class="card h-100">
              <div class="card-header text-center"><strong>Keluhan By Status</strong></div>
              
              <canvas ref="pieCanvas"></canvas>
            </div>
          </div>
          <div class="col-md-8">
            <div class="card h-100">
              <div class="card-header text-center"><strong>Time Series By Status</strong></div>
              <canvas ref="barCanvas"></canvas>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-12 mt-4">
        <div class="row">
          <div class="col-md-12"> 
            <div class="card">
              <div class="card-header text-center"><strong>Top 10 Keluhan</strong></div>
              <div class="card-body table-responsive">
                <table class="table table-bordered table-hover">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Nama</th>
                      <th>Email</th>
                      <th>Tanggal</th>
                      <th>Usia</th>
                    </tr>
                  </thead>
                  <tbody>
                    <!-- baris data -->
                    <tr v-for="(keluhan, index) in tableKeluhan">
                      <td>{{ index + 1 }}</td>
                      <td>{{ keluhan.nama }}</td>
                      <td>{{ keluhan.email }}</td>
                      <td>{{ keluhan.created_at }}</td>
                      <td>{{ keluhan.selisih_hari }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div> 
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>

  import { onMounted, ref } from 'vue'
  import {
    Chart,
    PieController,
    BarController,
    ArcElement,
    BarElement,
    CategoryScale,
    LinearScale,
    Tooltip,
    Legend
  } from 'chart.js'
  import axios from 'axios'

  // Daftarkan elemen-elemen Chart.js yang diperlukan
  Chart.register(
    PieController,
    BarController,
    ArcElement,
    BarElement,
    CategoryScale,
    LinearScale,
    Tooltip,
    Legend
  )

  const pieCanvas = ref(null)
  const barCanvas = ref(null)

  const labelPie = ref([])
  const nilaiPie = ref([])
  const totalPie = ref(0)

  const tableKeluhan = ref()

  const labelBar = ref([])
  const dataBar = ref([])

  onMounted( async () => {
    try {
      const response = await axios.get('/summary')
      //data pie chart
      const pieData = response.data.pie || []
      totalPie.value = response.data.total_pie || 0
      labelPie.value = pieData.map(item => item.label)
      nilaiPie.value = pieData.map(item => item.jumlah)

      //data bar chart
      labelBar.value = response.data.bar.label
      dataBar.value = response.data.bar.data

      //data table keluhan
      tableKeluhan.value = response.data.table

    } catch (err) {
      alert(err)
    } finally {

    }
    // PIE CHAR
    const datasetData = nilaiPie.value
    const total = totalPie.value
    new Chart(pieCanvas.value, {
      type: 'pie',
      data: {
        labels: labelPie.value,
        datasets: [{
          data: datasetData,
          backgroundColor: ['#f5ce0a', '#34c9eb', '#229620']
        }]
      },
      options: {
        responsive: true,
        plugins: {
          title: {
            display: true,
            text: `Total Data: ${total}`
          },
          legend: {
            position: 'top'
          },
          tooltip: {
            callbacks: {
              label: function(context) {
                const label = context.label || '';
                const value = context.raw;
                const percentage = ((value / total) * 100).toFixed(1) + '%';
                return `${label}: ${value} (${percentage})`;
              }
            }
          }
        }
      }
    })

    // BAR CHART
    new Chart(barCanvas.value, {
      type: 'bar',
      data: {
        labels: labelBar.value,
        datasets: dataBar.value
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            position: 'top'
          }
        },
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    })
  })
</script>

