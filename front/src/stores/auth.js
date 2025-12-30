import {ref, computed} from 'vue'
import {defineStore} from 'pinia'
import axios from 'axios'
import router from '../router'

export const useAuthStore = defineStore('auth', () => {
    const token = ref(localStorage.getItem('token') || null)
    const user = ref(JSON.parse(localStorage.getItem('user')) || null)

    const isAuthenticated = computed(() => !!token.value)

    async function login(email, password) {
        try {
            const response = await axios.post('http://localhost:8080/login', {
                email,
                password
            })

            token.value = response.data.token
            user.value = response.data.user

            localStorage.setItem('token', token.value)
            localStorage.setItem('user', JSON.stringify(user.value))

            axios.defaults.headers.common['Authorization'] = `Bearer ${token.value}`

            router.push('/')
        } catch (error) {
            alert('Помилка входу: ' + (error.response?.data?.message || 'Невірні дані'))
            throw error
        }
    }

    function logout() {
        token.value = null
        user.value = null
        localStorage.removeItem('token')
        localStorage.removeItem('user')
        delete axios.defaults.headers.common['Authorization']
        router.push('/login')
    }

    return {token, user, isAuthenticated, login, logout}
})