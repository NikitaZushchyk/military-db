import {createRouter, createWebHistory} from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import HomeView from '../views/HomeView.vue'
import LoginView from '../views/LoginView.vue'

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
        {
            path: '/',
            name: 'home',
            component: HomeView
        },
        {
            path: '/login',
            name: 'login',
            component: LoginView
        },
        { path: '/soldiers', component: HomeView },
        { path: '/warehouse', component: HomeView },
        { path: '/assignments', component: HomeView },
        { path: '/duty', component: HomeView },
        { path: '/logs', component: HomeView },
    ]
})

router.beforeEach((to, from, next) => {
    const auth = useAuthStore()

    if (to.name !== 'login' && !auth.token) {
        next({ name: 'login' })
    }
    else if (to.name === 'login' && auth.token) {
        next({ name: 'home' })
    }
    else {
        next()
    }
})

export default router