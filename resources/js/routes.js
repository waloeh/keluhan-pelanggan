export const routes = [
    {
        path: '/:pathMatch(.*)*',
        name: 'NotFound',
        component: () => import(/* webpackChunkName: "404" */ './Pages/404.vue')
    },
    {
        path: '/home',
        name: 'home',
        component: () => import(/* webpackChunkName: "home" */ './Pages/Home.vue')
    },
    {
        path: '/keluhan-pelanggan',
        name: 'keluhan_pelanggan',
        component: () => import(/* webpackChunkName: "keluhan_pelanggan" */ './Pages/KeluhanPelanggan.vue')
    },
    { 
        path: '/form-keluhan/:id?',
        name: 'form_keluhan',
        component: () => import(/* webpackChunkName: "keluhan_pelanggan" */ './Pages/FormKeluhanPelanggan.vue')
    },
    {
        path: '/detail-keluhan/:id?',
        name: 'detail_keluhan',
        component: () => import(/* webpackChunkName: "keluhan_pelanggan" */ './Pages/DetailKeluhanPelanggan.vue')
    },
];