<template>
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card">
          	<div class="card-header">{{ isEdit ? 'Form Edit Keluhan Pelanggan' : 'Form Tambah Keluhan Pelanggan' }}</div>
			
			<BaseSpiner v-if="loading" />

			<div v-else class="card-body">
				<form @submit.prevent="submitForm">
					<FormInput
						id="nama"
						label="Nama"
						type="text"
						v-model="form.nama"
						:error="errors.nama"
					/>

					<FormInput
						id="email"
						label="Email"
						type="email"
						v-model="form.email"
						:error="errors.email"
					/>

					<FormInput
						id="nomo_hp"
						label="Nomor Hp"
						type="number"
						v-model="form.nomor_hp"
						:error="errors.nomor_hp"
					/>

					<FormTextarea
						id="keluhan"
						label="Keluhan"
						v-model="form.keluhan"
						:error="errors.keluhan"
					/>

				<div class="d-flex gap-1">
					<button type="submit" class="btn btn-sm btn-primary">Kirim Keluhan</button>
					<button type="button" class="btn btn-sm btn-danger" @click="router.back()">Batal</button>
				</div>
				</form>
			</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import BaseSpiner from '../Components/BaseSpiner.vue'
import { onMounted, ref } from 'vue'
import axios from 'axios'
import FormTextarea from '../Components/FormTextarea.vue'
import FormInput from '../Components/FormInput.vue'
import { useRoute, useRouter } from 'vue-router'
import Swal from 'sweetalert2'

const route = useRoute()
const router = useRouter()

const id = route.params.id
const isEdit = ref(false)
const loading = ref(true)
const form = ref({
  nama: '',
  email: '',
  nomor_hp: '',
  keluhan: '',
})

onMounted( async () => {
	if (id) {
		isEdit.value = true
		try {
			const response = await axios.get(`/keluhan/${id}`)
			const keluhan = response.data
	
			form.value.nama = keluhan.nama
			form.value.email = keluhan.email
			form.value.nomor_hp = keluhan.nomor_hp
			form.value.keluhan = keluhan.keluhan
		} catch (err) {
			console.log('Gagal fetch data: ', err)
		}
	}

	loading.value = false
})

const errors = ref({
	nama: '',
	email: '',
	nomor_hp: '',
	keluhan: ''
})

const validationRules = {
	nama: {required: 'nama wajib diisi'},
	email: {required: 'email wajib diisi'},
	nomor_hp: {required: 'nomor hp wajib diisi'},
	keluhan: {required: 'keluhan wajib diisi'}
}

const validate = () => {
	let isValid = true
	for (const field in validationRules) {
		const rules = validationRules[field]
		const value = form.value[field]

		if (rules.required && !value) {
			errors.value[field] = rules.required
			isValid = false
		} else {
			errors.value[field] = ''
		}
	}
	return isValid
}

const submitForm = async () => {
	if (!validate()) return
	try {
		let response = null
		if (isEdit.value) {
			response = await axios.put(`/keluhan/${id}`, form.value)
		} else {
			response = await axios.post('/keluhan', form.value)
		}

		Swal.fire({
			title: 'Berhasil!',
			text: response.data.message,
			icon: 'success',
			confirmButtonText: 'OK'
		})
        router.push({ name: 'keluhan_pelanggan' })
	} catch (err) {
		Swal.fire({
			icon: 'error',
			title: 'Gagal!',
			text: err.response.data.message
		})
	}
}
</script>
