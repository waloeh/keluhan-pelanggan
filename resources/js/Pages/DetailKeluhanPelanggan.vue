<template>
	<div class="container mt-5">
	    <div class="row justify-content-center">
	        <div class="col-md-8">
	            <div class="card">
	                <div class="card-header">Detail Keluhan Pelangan</div>
	                <div class="card-body">
	                    <BaseSpiner v-if="loading"/>
                        <div v-else>
                            <p><strong>Nama : </strong> {{ keluhan.nama }}</p>
                            <p><strong>Email : </strong> {{ keluhan.email }}</p>
                            <p><strong>Nomor HP : </strong> {{ keluhan.nomor_hp || '-' }}</p>
                            <p><strong>Status  : </strong>
                                <span class="badge" :class="{'bg-warning': keluhan.status_keluhan == 0, 'bg-info': keluhan.status_keluhan == 1, 'bg-success': keluhan.status_keluhan == 2}">
                                    {{ statusText(keluhan.status_keluhan) }}
                                </span>
                            </p>
                            <p><strong>Keluhan : </strong> {{ keluhan.keluhan }}</p>

                            <h5 class="mt-4">Riwayat Status</h5>
                            <ul class="timeline list-unstyled">
                                <li v-for="(item, index) in keluhan.histori" :key="index" class="timeline-item position-relative ps-4 mb-4">
                                    <div class="timeline-icon position-absolute top-0 start-0 translate-middle rounded-circle"
                                        :class="{
                                        'bg-warning': item.status_keluhan == 0,
                                        'bg-info': item.status_keluhan == 1,
                                        'bg-success': item.status_keluhan == 2,
                                        'bg-secondary': item.status_keluhan > 2
                                        }" style="width: 14px; height: 14px;">
                                    </div>
                                    <div>
                                    <strong>{{ statusText(item.status_keluhan) }}</strong>
                                    <div class="text-muted small">{{ item.updated_at }}</div>
                                    </div>
                                </li>
                            </ul>
                            <button class="btn btn-sm btn-danger mt-3" @click="router.back()">Kembali</button>
                        </div>
	                </div>
	            </div>
	        </div>
	    </div>
	</div>
</template>
<script setup>
    import BaseSpiner from '../Components/BaseSpiner.vue';
    import { statusText } from '../../utils/statustext';
    import { useRoute, useRouter } from 'vue-router';
    import axios from 'axios';
    import { onMounted, ref } from 'vue';

    const route = useRoute();
    const router = useRouter()
    const keluhan = ref({})
    const loading = ref(true)

    const getKeluhan = async () => {
        try {
            const id = route.params.id
            const response = await axios.get(`/keluhan/${id}`)
            keluhan.value = response.data
        } catch (err) {

        } finally {
            loading.value = false
        }
    }

    onMounted(getKeluhan)
</script>

<style scoped>
    .timeline {
    border-left: 2px solid #dee2e6;
    margin-left: 10px;
    padding-left: 15px;
    position: relative;
    }

    .timeline-item {
    position: relative;
    }

    .timeline-icon {
    left: -7px;
    top: 0.35rem;
    }
</style>
