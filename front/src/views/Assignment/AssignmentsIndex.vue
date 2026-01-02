<script setup>
import {onMounted, ref, watch} from 'vue'
import axios from 'axios'
import {useRouter} from 'vue-router'

const router = useRouter()
const assignments = ref([])
const loading = ref(true)
const pagination = ref({})

const activeTab = ref('active')

const fetchAssignments = async (page = 1) => {
  loading.value = true
  try {
    const url = activeTab.value === 'active'
        ? 'http://localhost:8080/api/assignments/active'
        : 'http://localhost:8080/api/assignments'

    const response = await axios.get(url, {params: {page}})
    assignments.value = response.data.data
    if (response.data.meta) pagination.value = response.data.meta
  } catch (error) {
    console.error('Помилка завантаження журналу:', error)
  } finally {
    loading.value = false
  }
}

const returnItem = async (assignment) => {
  if (!confirm(`Повернути ${assignment.item.name} (${assignment.item.serial_number}) на склад?`)) return;

  try {
    await axios.post('http://localhost:8080/api/assignments/return', {
      warehouse_id: assignment.item.id
    })

    fetchAssignments(pagination.value.current_page || 1)
    alert('Майно успішно повернуто!')
  } catch (error) {
    console.error(error)
    const msg = error.response?.data?.message || 'Помилка повернення'
    alert(msg)
  }
}

const changePage = (link) => {
  if (!link.url || link.active) return;
  const url = new URL(link.url);
  fetchAssignments(url.searchParams.get('page'));
}

watch(activeTab, () => {
  pagination.value = {}
  fetchAssignments(1)
})

onMounted(() => fetchAssignments())
</script>

<template>
  <div class="page-container">
    <div class="header-row">
      <div class="title-block">
        <h2>📋 Журнал видач</h2>
      </div>
      <button class="add-btn" @click="router.push({ name: 'assignments.create' })">📤 Видати майно</button>
    </div>

    <div class="tabs">
      <button
          :class="['tab-btn', { active: activeTab === 'active' }]"
          @click="activeTab = 'active'"
      >
        Тільки активні (На руках)
      </button>
      <button
          :class="['tab-btn', { active: activeTab === 'all' }]"
          @click="activeTab = 'all'"
      >
        Вся історія
      </button>
    </div>

    <div v-if="loading" class="loading-state">
      <div class="spinner"></div>
      <span>Завантаження журналу...</span>
    </div>

    <div v-else class="table-wrapper">
      <table>
        <thead>
        <tr>
          <th>Боєць</th>
          <th>Майно / Серійник</th>
          <th>Дата видачі</th>
          <th>Дата повернення</th>
          <th>Дія</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="assign in assignments" :key="assign.id">
          <td class="name-cell">
            <div v-if="assign.soldier">
              <div>{{ assign.soldier.full_name }}</div>
              <small style="color: #64748b">{{ assign.soldier.rank }}</small>
            </div>
            <span v-else style="color:red">Дані бійця відсутні в API</span>
          </td>

          <td>
            <span class="badge type">{{ assign.item.name }}</span>
            <div style="font-size: 0.85rem; margin-top: 4px; color: #475569;">
              {{ assign.item.serial_number }}
            </div>
          </td>

          <td>{{ assign.issue_date }}</td>

          <td>
            <span v-if="assign.return_date" class="date-returned">{{ assign.return_date }}</span>
            <span v-else class="status-active">На руках</span>
          </td>

          <td>
            <button
                v-if="assign.is_active"
                class="return-btn"
                @click="returnItem(assign)"
            >
              ↩️ Повернути
            </button>
            <span v-else class="done-icon">✅</span>
          </td>
        </tr>

        <tr v-if="assignments.length === 0">
          <td colspan="5" style="text-align: center; padding: 30px;">Записів не знайдено</td>
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
  max-width: 1200px;
  margin: 0 auto;
}

.header-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.title-block h2 {
  font-size: 24px;
  color: #1e293b;
}

.add-btn {
  background: #3b82f6;
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
}

.add-btn:hover {
  background: #2563eb;
}

.tabs {
  display: flex;
  gap: 10px;
  margin-bottom: 20px;
  border-bottom: 1px solid #e2e8f0;
  padding-bottom: 10px;
}

.tab-btn {
  background: none;
  border: none;
  padding: 8px 16px;
  font-size: 14px;
  font-weight: 600;
  color: #64748b;
  cursor: pointer;
  border-radius: 6px;
  transition: all 0.2s;
}

.tab-btn:hover {
  background: #f1f5f9;
  color: #334155;
}

.tab-btn.active {
  background: #e0f2fe;
  color: #0284c7;
}

.table-wrapper {
  background: white;
  border-radius: 12px;
  overflow: hidden;
  border: 1px solid #f1f5f9;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
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
  font-size: 13px;
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

.badge {
  background: #f1f5f9;
  padding: 4px 8px;
  border-radius: 6px;
  font-size: 12px;
  border: 1px solid #e2e8f0;
}

.status-active {
  color: #d97706;
  font-weight: 600;
  font-size: 0.9rem;
  background: #fef3c7;
  padding: 4px 10px;
  border-radius: 20px;
}

.date-returned {
  color: #64748b;
}

.return-btn {
  background: white;
  border: 1px solid #cbd5e1;
  color: #475569;
  padding: 6px 12px;
  border-radius: 6px;
  cursor: pointer;
  font-size: 13px;
  font-weight: 500;
  transition: all 0.2s;
}

.return-btn:hover {
  border-color: #ef4444;
  color: #ef4444;
  background: #fef2f2;
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
  border-color: #3b82f6;
}
</style>