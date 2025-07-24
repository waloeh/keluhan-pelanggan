<template>
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">Keluhan Pelanggan</div>
          <div class="card-body">
            <BaseSpiner v-if="loading"/>
            <div v-else>
              <div class="row mb-3">
                  <div class="col-12">
                      <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                          <!-- Input Search -->
                          <input
                              class="form-control me-2"
                              style="max-width: 300px;"
                              v-model="search"
                              placeholder="Cari order..."
                          />

                          <RouterLink class="btn btn-primary btn-sm ms-auto" :to="{ name: 'form_keluhan' }">Tambah</RouterLink>

                          <!-- Tombol Export -->
                          <div class="dropdown">
                            <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                              Export File
                            </button>
                            <ul class="dropdown-menu">
                              <li><a class="dropdown-item" @click.prevent="downloadFile('txt')">Format Txt</a></li>
                              <li><a class="dropdown-item" @click.prevent="downloadFile('csv')">Format Csv</a></li>
                              <li><a class="dropdown-item" @click.prevent="downloadFile('xlsx')">Format Xls</a></li>
                              <li><a class="dropdown-item" @click.prevent="downloadFile('pdf')">Format Pdf</a></li>
                            </ul>
                          </div>
                      </div>
                  </div>
              </div>
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Nomor HP</th>
                    <th>Status</th>
                    <th>Keluhan</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(item, index) in keluhan" :key="item.id">
                    <td>{{ (pagination.current_page - 1) * pagination.per_page + (index + 1) }}</td>
                    <td>{{ item.nama }}</td>
                    <td>{{ item.email }}</td>
                    <td>{{ item.nomor_hp ?? '-' }}</td>
                    <td>
                      <span class="badge" :class="item.status_keluhan == 0 ? 'bg-warning' : item.status_keluhan == 1 ? 'bg-info' : item.status_keluhan == 2 ? 'bg-success' : 'bg-secondary'">
                        {{ statusText(item.status_keluhan) }}
                      </span>
                    </td>
                    <td>{{ item.keluhan }}</td>
                    <td>{{ item.created_at }}</td>
                    <td>
                      <div class="d-flex gap-1">
                        <button class="btn btn-sm btn-success" @click="detilKeluhan(item.id)">Detil</button>
                        <button class="btn btn-sm btn-warning" @click="editKeluhan(item.id)">Ubah</button>
                        <button class="btn btn-sm btn-danger" @click="hapusKeluhan(item.id)">Hapus</button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
              <nav class="mt-3">
                <ul class="pagination justify-content-end">
                    <!-- Tombol ke halaman pertama -->
                    <li class="page-item" :class="{ disabled: pagination.current_page === 1 }">
                        <button class="page-link" @click="fetchData(1)" :disabled="pagination.current_page === 1">
                            «
                        </button>
                    </li>

                    <!-- Tombol ke halaman sebelumnya -->
                    <li class="page-item" :class="{ disabled: pagination.current_page === 1 }">
                        <button class="page-link" @click="fetchData(pagination.current_page - 1)" :disabled="pagination.current_page === 1">
                            ‹
                        </button>
                    </li>

                    <!-- Nomor halaman -->
                    <li
                        class="page-item"
                        v-for="page in pages"
                        :key="page"
                        :class="{ active: pagination.current_page === page }"
                    >
                        <button class="page-link" @click="fetchData(page)">
                            {{ page }}
                        </button>
                    </li>

                    <!-- Tombol ke halaman selanjutnya -->
                    <li class="page-item" :class="{ disabled: pagination.current_page === pagination.last_page }">
                        <button class="page-link" @click="fetchData(pagination.current_page + 1)" :disabled="pagination.current_page === pagination.last_page">
                            ›
                        </button>
                    </li>

                    <!-- Tombol ke halaman terakhir -->
                    <li class="page-item" :class="{ disabled: pagination.current_page === pagination.last_page }">
                    <button class="page-link" @click="fetchData(pagination.last_page)" :disabled="pagination.current_page === pagination.last_page">
                        »
                    </button>
                    </li>
                </ul>
              </nav>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import BaseSpiner from '../Components/BaseSpiner.vue'
import { statusText } from '../../utils/statustext'
import { ref, onMounted, computed, watch } from 'vue'
import axios from 'axios'
import { debounce } from 'lodash'
import Swal from 'sweetalert2'
import { useRouter } from 'vue-router'

const keluhan = ref([])
const loading = ref(true)
const search = ref('')
const router = useRouter()
const pagination = ref({
    current_page: 1,
    last_page: 1,
    per_page: 0
})

const pages = computed(() => {
    const total = pagination.value.last_page
    const current = pagination.value.current_page
    const delta = 2

    const range = []
    const start = Math.max(1, current - delta)
    const end = Math.min(total, current + delta)

    for (let i = start; i <= end; i++) {
        range.push(i)
    }

    return range
})

const fetchData = async (page = 1) => {
  try {
    const response = await axios.get('/keluhan', {
      params: {
          page,
          search: search.value.trim() || undefined
      }
    })

    keluhan.value = response.data.data
    pagination.value = {
      current_page: response.data.current_page,
      last_page: response.data.last_page,
      per_page: response.data.per_page
    }
  } catch (error) {
    console.error('Gagal mengambil data:', error)
  } finally {
    loading.value = false
  }
}

const debouncedFetch = debounce(() => {
    fetchData(1)
}, 1000)

watch(search, () => {
    debouncedFetch()
})

onMounted(() => {
  fetchData()
})

const hapusKeluhan = async (id) => {
  const result = await Swal.fire({
    title: 'Yakin akan dihapus?',
    text: 'Data yang sudah dihapus tidak bisa dikembalikan!',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Ya, hapus!',
    cancelButtonText: 'Batal'
  })

  if (result.isConfirmed) {
    try {
      const response = await axios.delete(`/keluhan/${id}`)
      keluhan.value = keluhan.value.filter(k => k.id !== id)

      Swal.fire({
        title: 'Berhasil!',
        text: response.data.message,
        icon: 'success',
        confirmButtonText: 'OK'
      })
    } catch (err) {
      Swal.fire({
        icon: 'error',
        title: 'Gagal hapus',
        text: err.response.data.message
      })
    }
  }
}

const editKeluhan = async (id) => {
  router.push({
    name: 'form_keluhan',
    params: {id: id}
  })
}

const detilKeluhan = async (id) => {
  router.push({
    name: 'detail_keluhan',
    params: {id: id}
  })
}

const downloadFile = async (format) => {
  try {
    let response = null
    if (format == 'pdf') {
      response = await axios.get('/export-pdf', {
        responseType: 'blob'
      })
    } else if (format == 'txt') {
      response = await axios.get('/export-txt', {
        responseType: 'blob'
      })
    } else {
      response = await axios.get(`/export/${format}`, {
        responseType: 'blob'
      })
    }

    const contentDisposition = response.headers['content-disposition']
    let filename = `keluhan_pelanggan.${format}`
    if (contentDisposition) {
      const match = contentDisposition.match(/filename="?(.+)"?/)
      if (match) {
        filename = match[1]
      }
    }

    const url = window.URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', filename)
    document.body.appendChild(link)
    link.click()
    link.remove()
  } catch (error) {
    console.error('Gagal mendownload file:', error)
    alert('Export gagal. Coba lagi.')
  }
}
</script>
<style scoped>
  .dropdown-item:hover {
    background-color: #4a76a1 !important;
    color: #868c92 !important;
  }
</style>
