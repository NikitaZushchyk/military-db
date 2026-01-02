import {createRouter, createWebHistory} from 'vue-router'
import {useAuthStore} from '@/stores/auth'
import HomeView from '../views/HomeView.vue'
import LoginView from '../views/LoginView.vue'
import SoldiersView from '../views/soldiers/SoldiersView.vue'
import SoldiersDetailView from '../views/soldiers/SoldiersDetailView.vue'
import SoldiersCreateView from "@/views/soldiers/SoldiersCreateView.vue";
import WarehouseIndexView from "@/views/warehouse/WarehouseIndexView.vue";
import WarehouseCreateView from "@/views/warehouse/WarehouseCreateView.vue";
import WarehouseDetailView from "@/views/warehouse/WarehouseDetailView.vue";
import AssignmentsIndex from "@/views/Assignment/AssignmentsIndex.vue";
import AssignmentCreate from "@/views/Assignment/AssignmentCreate.vue";

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
        {path: '/', name: 'home', component: HomeView},
        {path: '/login', name: 'login', component: LoginView},
        {path: '/soldiers', component: SoldiersView},
        {path: '/soldiers/create', name: 'soldier-create', component: SoldiersCreateView},
        {path: '/soldiers/:id', name: 'soldier-detail', component: SoldiersDetailView},
        {path: '/warehouse', name: 'warehouses.index', component: WarehouseIndexView},
        {path: '/warehouse/create', name: 'warehouse-create', component: WarehouseCreateView},
        {path: '/warehouse/:warehouse', name: 'warehouse-detail', component: WarehouseDetailView},
        {path: '/assignments', name: 'assignments.index', component: AssignmentsIndex},
        {path: '/assignments/issue', name: 'assignments.create', component: AssignmentCreate},
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