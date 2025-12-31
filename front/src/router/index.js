import {createRouter, createWebHistory} from 'vue-router'
import {useAuthStore} from '@/stores/auth'
import HomeView from '../views/HomeView.vue'
import LoginView from '../views/LoginView.vue'
import SoldiersView from '../views/SoldiersView.vue'
import SoldiersDetailView from '../views/SoldiersDetailView.vue'
import SoldiersCreateView from "@/views/SoldiersCreateView.vue";

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
        {path: '/', name: 'home', component: HomeView},
        {path: '/login', name: 'login', component: LoginView},
        {path: '/soldiers', component: SoldiersView},
        {path: '/soldiers/create', name: 'soldier-create', component: SoldiersCreateView},
        {path: '/soldiers/:id', name: 'soldier-detail', component: SoldiersDetailView},
        {path: '/warehouse', component: HomeView},
        {path: '/assignments', component: HomeView},
        {path: '/duty', component: HomeView},
        {path: '/logs', component: HomeView},
    ]
})

router.beforeEach((to, from, next) => {
    const auth = useAuthStore()

    if (to.name !== 'login' && !auth.token) {
        next({name: 'login'})
    } else if (to.name === 'login' && auth.token) {
        next({name: 'home'})
    } else {
        next()
    }
})

export default router