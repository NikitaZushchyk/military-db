<script setup>
import {onMounted, ref} from 'vue'
import {useRoute, useRouter} from 'vue-router'
import axios from 'axios'

const route = useRoute()
const router = useRouter()
const soldierId = route.params.id

const soldier = ref(null)
const loading = ref(true)
const ranks = ref([])
const units = ref([])

const form = ref({
  first_name: '',
  last_name: '',
  rank_id: '',
  unit_id: '',
  status: ''
})

const fetchSoldier = async () => {
  loading.value = true
  try {
    const response = await axios.get(`http://localhost:8080/api/soldiers/${soldierId}`)
    soldier.value = response.data.data

    form.value = {
      first_name: soldier.value.first_name,
      last_name: soldier.value.last_name,
      rank_id: soldier.value.rank_id || '',
      unit_id: soldier.value.unit_id || '',
      status: soldier.value.status
    }

    const listsResponse = await axios.get('http://localhost:8080/api/soldiers')
    if (listsResponse.data.meta_data.ranks) ranks.value = listsResponse.data.meta_data.ranks
    if (listsResponse.data.meta_data.units) units.value = listsResponse.data.meta_data.units

  } catch (error) {
    console.error('Error fetching soldier:', error)
    alert('Помилка завантаження')
  } finally {
    loading.value = false
  }
}

const updateSoldier = async () => {
  try {
    await axios.put(`http://localhost:8080/api/soldiers/${soldierId}`, form.value)
    alert('Дані оновлено!')
    fetchSoldier()
  } catch (error) {
    alert('Помилка оновлення')
  }
}

const deleteSoldier = async () => {
  if (!confirm('Ви впевнені? Це незворотньо!')) return

  try {
    await axios.delete(`http://localhost:8080/api/soldiers/${soldierId}`)
    router.push('/soldiers')
  } catch (error) {
    alert('Помилка видалення')
  }
}

onMounted(() => {
  fetchSoldier()
})
</script>

<template>
  <div class="page-container" v-if="!loading && soldier">

    <button @click="router.push('/soldiers')" class="back-btn">← До списку</button>

    <div class="content-grid">

      <div class="card edit-card">
        <h2>Редагування профілю</h2>
        <form @submit.prevent="updateSoldier">
          <div class="form-group">
            <label>Ім'я</label>
            <input v-model="form.first_name" type="text" required>
          </div>
          <div class="form-group">
            <label>Прізвище</label>
            <input v-model="form.last_name" type="text" required>
          </div>

          <div class="form-group">
            <label>Звання</label>
            <select v-model="form.rank_id">
              <option v-for="r in ranks" :value="r.id" :key="r.id">{{ r.name }}</option>
            </select>
          </div>

          <div class="form-group">
            <label>Підрозділ</label>
            <select v-model="form.unit_id">
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
            <button type="submit" class="save-btn">Зберегти зміни</button>
            <button type="button" @click="deleteSoldier" class="delete-btn">Видалити бійця</button>
          </div>
        </form>
      </div>

      <div class="info-column">

        <div class="card">
          <h3>📦 Видане майно</h3>
          <div v-if="soldier.assignments && soldier.assignments.length">
            <ul class="list">
              <li v-for="assign in soldier.assignments" :key="assign.id">
                <strong>{{ assign.item?.name || 'Предмет' }}</strong>
                <span class="date">— {{ assign.issue_date }}</span>
                <span v-if="assign.return_date" class="returned"> (Повернуто)</span>
              </li>
            </ul>
          </div>
          <div v-else class="empty">Нічого не видано</div>
        </div>

        <div class="card">
          <h3>📅 Останні наряди</h3>
          <div v-if="soldier.duties && soldier.duties.length">
            <ul class="list">
              <li v-for="duty in soldier.duties" :key="duty.id">
                <strong>{{ duty.type?.name || 'Наряд' }}</strong>
                <br>
                <small>{{ duty.start_time }} — {{ duty.end_time }}</small>
              </li>
            </ul>
          </div>
          <div v-else class="empty">Нарядів немає</div>
        </div>

      </div>
    </div>
  </div>
  <div v-else class="loading">Завантаження профілю...</div>
</template>

<style scoped>
.page-container {
  padding: 20px;
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

.content-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 24px;
}

@media (max-width: 768px) {
  .content-grid {
    grid-template-columns: 1fr;
  }
}

.card {
  background: white;
  padding: 24px;
  border-radius: 12px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
  margin-bottom: 24px;
}

h2, h3 {
  margin-bottom: 20px;
  color: #334155;
}

.form-group {
  margin-bottom: 15px;
}

.form-group label {
  display: block;
  margin-bottom: 5px;
  font-weight: 500;
  color: #64748b;
}

.form-group input, .form-group select {
  width: 100%;
  padding: 10px;
  border: 1px solid #cbd5e1;
  border-radius: 6px;
}

.actions {
  display: flex;
  gap: 10px;
  margin-top: 20px;
}

.save-btn {
  flex: 1;
  background: #22c55e;
  color: white;
  padding: 10px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
}

.save-btn:hover {
  background: #16a34a;
}

.delete-btn {
  background: #ef4444;
  color: white;
  padding: 10px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
}

.delete-btn:hover {
  background: #dc2626;
}

.list {
  list-style: none;
  padding: 0;
}

.list li {
  padding: 10px 0;
  border-bottom: 1px solid #f1f5f9;
}

.list li:last-child {
  border-bottom: none;
}

.empty {
  color: #94a3b8;
  font-style: italic;
}

.returned {
  color: #94a3b8;
  font-size: 0.9em;
}
</style>