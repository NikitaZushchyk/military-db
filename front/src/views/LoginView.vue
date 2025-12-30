<script setup>
import {ref} from 'vue'
import {useAuthStore} from '../stores/auth'

const auth = useAuthStore()
const email = ref('admin@military.com')
const password = ref('password')

const handleLogin = async () => {
  await auth.login(email.value, password.value)
}
</script>

<template>
  <div class="login-container">
    <div class="login-card">
      <div class="logo">🛡️ Military DB</div>
      <h2>Вхід у систему</h2>

      <form @submit.prevent="handleLogin">
        <div class="form-group">
          <label>Email</label>
          <input type="email" v-model="email" required placeholder="email@example.com">
        </div>

        <div class="form-group">
          <label>Пароль</label>
          <input type="password" v-model="password" required>
        </div>

        <button type="submit">Увійти</button>
      </form>
    </div>
  </div>
</template>

<style scoped>
.login-container {
  height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  background-color: #1e293b;
}

.login-card {
  background: white;
  padding: 40px;
  border-radius: 12px;
  width: 100%;
  max-width: 400px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
  text-align: center;
}

.logo {
  font-size: 40px;
  margin-bottom: 20px;
  border: none;
  background: white;
}

h2 {
  margin-bottom: 30px;
  color: #334155;
}

.form-group {
  margin-bottom: 20px;
  text-align: left;
}

label {
  display: block;
  margin-bottom: 8px;
  font-weight: 500;
  color: #64748b;
}

input {
  width: 100%;
  padding: 12px;
  border: 1px solid #cbd5e1;
  border-radius: 6px;
  font-size: 16px;
  transition: 0.2s;
}

input:focus {
  border-color: #3b82f6;
  outline: none;
}

button {
  width: 100%;
  padding: 14px;
  background-color: #1e293b;
  color: white;
  border: none;
  border-radius: 6px;
  font-size: 16px;
  cursor: pointer;
  transition: 0.2s;
}

button:hover {
  background-color: #0f172a;
}
</style>