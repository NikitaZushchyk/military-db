import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView
    },
    { path: '/soldiers', component: HomeView },
    { path: '/warehouse', component: HomeView },
    { path: '/assignments', component: HomeView },
    { path: '/duty', component: HomeView },
    { path: '/logs', component: HomeView },
  ]
})

export default router
