<script setup>
import {ref, onMounted} from 'vue'
import axios from 'axios'
import {useRouter} from 'vue-router'

const router = useRouter()
const loading = ref(false)

const ranks = ref([])
const units = ref([])

const form = ref({
  first_name: '',
  last_name: '',
  rank_id: '',
  unit_id: '',
  status: 'active'
})

const fetchLists = async () => {
  try {
    const response = await axios.get('http://localhost:8080/api/soldiers')
    if (response.data.meta_data) {
      if (response.data.meta_data.ranks) ranks.value = response.data.meta_data.ranks
      if (response.data.meta_data.units) units.value = response.data.meta_data.units
    }
  } catch (error) {
    console.error('Помилка завантаження списків:', error)
  }
}

const createSoldier = async () => {
  loading.value = true
  try {
    await axios.post('http://localhost:8080/api/soldiers', form.value)

    router.push('/soldiers')
  } catch (error) {
    console.error(error)
    alert('Помилка при створенні. Перевірте, чи заповнені всі поля.')
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchLists()
})
</script>

<template>
  <div class="page-container">
    <button @click="router.push('/soldiers')" class="back-btn">← До списку</button>

    <div class="card create-card">
      <h2>Новий боєць</h2>
      <p class="subtitle">Внесіть дані для зарахування до особового складу</p>

      <form @submit.prevent="createSoldier">
        <div class="form-row">
          <div class="form-group">
            <label>Ім'я</label>
            <input v-model="form.first_name" type="text" required placeholder="Іван">
          </div>
          <div class="form-group">
            <label>Прізвище</label>
            <input v-model="form.last_name" type="text" required placeholder="Петренко">
          </div>
        </div>

        <div class="form-group">
          <label>Звання</label>
          <select v-model="form.rank_id" required>
            <option value="" disabled>Оберіть звання</option>
            <option v-for="r in ranks" :value="r.id" :key="r.id">{{ r.name }}</option>
          </select>
        </div>

        <div class="form-group">
          <label>Підрозділ</label>
          <select v-model="form.unit_id" required>
            <option value="" disabled>Оберіть підрозділ</option>
            <option v-for="u in units" :value="u.id" :key="u.id">{{ u.name }}</option>
          </select>
        </div>

        <div class="form-group">
          <label>Статус</label>
          <select v-model="form.status">
            <option value="active">В строю 🟢</option>
            <option value="hospital">Шпиталь 🏥</option>
            <option value="vacation">Відпустка 🏖</option>
            <option value="fired">Звільнений ❌</option>
          </select>
        </div>

        <div class="actions">
          <button type="submit" class="save-btn" :disabled="loading">
            {{ loading ? 'Збереження...' : 'Додати бійця' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<style scoped>
.page-container {
  padding: 20px;
  max-width: 600px;
  margin: 0 auto;
}

.back-btn {
  background: none;
  border: none;
  color: #64748b;
  cursor: pointer;
  margin-bottom: 20px;
  font-size: 16px;
}

.back-btn:hover {
  color: #3b82f6;
}

.card {
  background: white;
  padding: 30px;
  border-radius: 12px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
}

h2 {
  margin-bottom: 5px;
  color: #1e293b;
}

.subtitle {
  color: #64748b;
  margin-bottom: 25px;
  font-size: 14px;
}

.form-row {
  display: flex;
  gap: 15px;
}

.form-row .form-group {
  flex: 1;
}

.form-group {
  margin-bottom: 20px;
}

.form-group label {
  display: block;
  margin-bottom: 8px;
  font-weight: 500;
  color: #475569;
}

.form-group input, .form-group select {
  width: 100%;
  padding: 12px;
  border: 1px solid #cbd5e1;
  border-radius: 8px;
  font-size: 15px;
  transition: 0.2s;
  background: #fff;
}

.form-group input:focus, .form-group select:focus {
  border-color: #3b82f6;
  outline: none;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.actions {
  margin-top: 30px;
}

.save-btn {
  width: 100%;
  background: #3b82f6;
  color: white;
  padding: 14px;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-size: 16px;
  font-weight: 600;
  transition: 0.2s;
}

.save-btn:hover {
  background: #2563eb;
  transform: translateY(-1px);
}

.save-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}
</style>