<script setup>
import {onMounted, ref} from 'vue'
import axios from 'axios'
import {useRouter} from 'vue-router'

const router = useRouter()
const duties = ref([])
const loading = ref(true)
const pagination = ref({})

const filters = ref({
  date_from: '',
  date_to: ''
})

const fetchRoster = async (page = 1) => {
  loading.value = true
  try {
    const params = {page, ...filters.value}
    Object.keys(params).forEach(key => (params[key] === '' || params[key] == null) && delete params[key]);

    const response = await axios.get('http://localhost:8080/api/roster', {params})

    duties.value = response.data.data
    if (response.data.meta) pagination.value = response.data.meta

  } catch (error) {
    console.error('Помилка:', error)
  } finally {
    loading.value = false
  }
}

const applyFilters = () => fetchRoster(1)

const exportPDF = async () => {
  try {
    const params = { ...filters.value }
    Object.keys(params).forEach(key => (params[key] === '' || params[key] == null) && delete params[key]);

    const response = await axios.get('http://localhost:8080/api/roster/pdfExport', {
      params,
      responseType: 'blob'
    })

    const url = window.URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', 'duties_report.pdf')

    document.body.appendChild(link)
    link.click()
    link.parentNode.removeChild(link)
    window.URL.revokeObjectURL(url)

  } catch (error) {
    console.error('Помилка при експорті PDF:', error)
    alert('Не вдалося згенерувати PDF-звіт нарядів')
  }
}

const changePage = (link) => {
  if (!link.url || link.active) return;
  const url = new URL(link.url);
  fetchRoster(url.searchParams.get('page'));
}

const formatDateTime = (dateStr) => {
  if (!dateStr) return '—'
  return new Date(dateStr).toLocaleString('uk-UA', {
    day: '2-digit', month: '2-digit', year: 'numeric',
    hour: '2-digit', minute: '2-digit'
  })
}

const getStatus = (start, end) => {
  const now = new Date()
  const s = new Date(start)
  const e = new Date(end)

  if (now < s) return {label: 'Заплановано', class: 'future'}
  if (now >= s && now <= e) return {label: 'В процесі 🔥', class: 'active'}
  return {label: 'Завершено', class: 'past'}
}

onMounted(() => fetchRoster())
</script>

<template>
  <div class="page-container">
    <div class="header-row">
      <div class="title-block">
        <h2>🛡 Графік нарядів</h2>
      </div>
      <div class="action-buttons">
        <button class="export-btn" @click="exportPDF">📄 Експорт в PDF</button>
        <button class="add-btn" @click="router.push({ name: 'duty-create' })">Призначити наряд</button>
      </div>
    </div>

    <div class="filters-bar">
      <div class="filter-group">
        <label>З:</label>
        <input type="date" v-model="filters.date_from" @change="applyFilters">
      </div>
      <div class="filter-group">
        <label>По:</label>
        <input type="date" v-model="filters.date_to" @change="applyFilters">
      </div>
    </div>

    <div v-if="loading" class="loading-state">
      <div class="spinner"></div>
      <span>Завантаження графіку...</span>
    </div>

    <div v-else class="table-wrapper">
      <table>
        <thead>
        <tr>
          <th>Боєць</th>
          <th>Вид наряду</th>
          <th>Початок</th>
          <th>Кінець</th>
          <th>Статус</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="duty in duties" :key="duty.id">
          <td class="name-cell">{{ duty.soldier }}</td>
          <td><span class="badge type">{{ duty.duty_type }}</span></td>
          <td class="time-cell">{{ formatDateTime(duty.start_time) }}</td>
          <td class="time-cell">{{ formatDateTime(duty.end_time) }}</td>
          <td>
            <span :class="['status-badge', getStatus(duty.start_time, duty.end_time).class]">
              {{ getStatus(duty.start_time, duty.end_time).label }}
            </span>
          </td>
        </tr>
        <tr v-if="duties.length === 0">
          <td colspan="5" class="empty-state">Нарядів не знайдено за цей період</td>
        </tr>
        </tbody>
      </table>
    </div>

    <div class="pagination-container" v-if="pagination.links && pagination.links.length > 3">
      <div class="pagination-links">
        <button
            v-for="(link, i) in pagination.links"
            :key="i"
            :disabled="!link.url"
            :class="{ 'active': link.active }"
            @click="changePage(link)"
            v-html="link.label"
        ></button>
      </div>
    </div>
  </div>
</template>

<style scoped>
.page-container {
  padding: 20px;
  max-width: 1000px;
  margin: 0 auto;
}

.header-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
}

h2 {
  font-size: 24px;
  color: #1e293b;
  margin: 0;
}

.add-btn {
  background: #3b82f6;
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
  transition: 0.2s;
}

.add-btn:hover {
  background: #2563eb;
}

.filters-bar {
  display: flex;
  gap: 20px;
  background: white;
  padding: 15px;
  border-radius: 12px;
  border: 1px solid #f1f5f9;
  margin-bottom: 20px;
}

.filter-group {
  display: flex;
  align-items: center;
  gap: 10px;
}

.filter-group label {
  font-weight: 600;
  color: #64748b;
  font-size: 14px;
}

.filter-group input {
  padding: 8px;
  border: 1px solid #cbd5e1;
  border-radius: 6px;
  color: #334155;
}

.table-wrapper {
  background: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
  border: 1px solid #f1f5f9;
}

table {
  width: 100%;
  border-collapse: collapse;
}

th {
  background: #f8fafc;
  text-align: left;
  padding: 16px;
  color: #64748b;
  font-size: 12px;
  text-transform: uppercase;
  border-bottom: 1px solid #e2e8f0;
}

td {
  padding: 16px;
  border-bottom: 1px solid #f1f5f9;
  vertical-align: middle;
}

.name-cell {
  font-weight: 600;
  color: #0f172a;
}

.type {
  background: #f1f5f9;
  color: #475569;
  padding: 4px 8px;
  border-radius: 6px;
  font-size: 13px;
  font-weight: 600;
  border: 1px solid #e2e8f0;
}

.time-cell {
  font-family: monospace;
  color: #334155;
  font-size: 14px;
}

.status-badge {
  padding: 4px 10px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 700;
  display: inline-block;
}

.status-badge.future {
  background: #e0f2fe;
  color: #0369a1;
}

.status-badge.active {
  background: #dcfce7;
  color: #15803d;
  box-shadow: 0 0 0 2px #bbf7d0;
}

.status-badge.past {
  background: #f1f5f9;
  color: #94a3b8;
}

.empty-state {
  text-align: center;
  padding: 30px;
  color: #94a3b8;
}

.loading-state {
  text-align: center;
  padding: 40px;
  color: #94a3b8;
}

.spinner {
  width: 30px;
  height: 30px;
  border: 3px solid #e2e8f0;
  border-top-color: #3b82f6;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 10px;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.pagination-container {
  display: flex;
  justify-content: center;
  margin-top: 20px;
}

.pagination-links button {
  padding: 8px 12px;
  border: 1px solid #e2e8f0;
  background: white;
  cursor: pointer;
}

.pagination-links button.active {
  background: #3b82f6;
  color: white;
}

.action-buttons {
  display: flex;
  gap: 12px;
}

.export-btn {
  background: white;
  color: #334155;
  border: 1px solid #cbd5e1;
  padding: 10px 20px;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.2s;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

.export-btn:hover {
  background: #f8fafc;
  border-color: #94a3b8;
  transform: translateY(-1px);
}
</style>