<script setup>
import {onMounted, ref} from 'vue'
import axios from 'axios'
import {useRouter} from 'vue-router'

const router = useRouter()
const logs = ref([])
const loading = ref(true)
const pagination = ref({})
const searchQuery = ref('')

const fetchLogs = async (page = 1) => {
  loading.value = true
  try {
    const params = {page}

    if (searchQuery.value) {
      params.search = searchQuery.value
    }

    const response = await axios.get('http://localhost:8080/api/logs', {params})

    logs.value = response.data.data
    pagination.value = response.data

  } catch (error) {
    console.error('Помилка завантаження логів:', error)
  } finally {
    loading.value = false
  }
}

const handleSearch = () => {
  fetchLogs(1)
}

const changePage = (link) => {
  if (!link.url || link.active) return;
  const url = new URL(link.url);
  const page = url.searchParams.get('page');
  fetchLogs(url.searchParams.get('page'));
}

const getActionClass = (action) => {
  const map = {
    'WEAPON_ISSUED': 'issued',
    'WEAPON_RETURNED': 'returned',
    'SOLDIER_CREATED': 'created',
    'SOLDIER_DELETED': 'deleted',
    'USER_LOGIN': 'login'
  }
  return map[action] || 'default'
}

const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleString('uk-UA')
}

onMounted(() => fetchLogs())
</script>

<template>
  <div class="page-container">
    <div class="header-row">
      <div class="title-block">
        <h2>⚠️ Системні логи</h2>
        <span class="count-badge" v-if="pagination.total">Всього записів: {{ pagination.total }}</span>
      </div>
      <div class="search-block">
        <div class="input-wrapper">
          <input
              v-model="searchQuery"
              @keyup.enter="handleSearch"
              type="text"
              placeholder="Пошук (прізвище, серійник)..."
          >
          <button class="search-btn" @click="handleSearch">🔍</button>
        </div>
        <button class="refresh-btn" @click="fetchLogs(pagination.current_page)">🔄</button>
      </div>
    </div>

    <div v-if="loading" class="loading-state">
      <div class="spinner"></div>
      <span>Завантаження історії...</span>
    </div>

    <div v-else class="table-wrapper">
      <table>
        <thead>
        <tr>
          <th style="width: 50px">ID</th>
          <th style="width: 200px">Дія</th>
          <th>Опис події</th>
          <th style="width: 180px">Час</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="log in logs" :key="log.id">
          <td style="color: #94a3b8">#{{ log.id }}</td>

          <td>
            <span :class="['log-badge', getActionClass(log.action)]">
              {{ log.action }}
            </span>
          </td>

          <td class="desc-cell">{{ log.description }}</td>

          <td class="date-cell">{{ formatDate(log.created_at) }}</td>
        </tr>

        <tr v-if="logs.length === 0">
          <td colspan="4" style="text-align: center; padding: 30px; color: #64748b;">
            Історія порожня
          </td>
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
  flex-wrap: wrap;
  margin-bottom: 24px;
}

.title-block {
  display: flex;
  align-items: center;
  gap: 12px;
}

.search-block {
  display: flex;
  gap: 10px;
}

.input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.input-wrapper input {
  padding: 8px 12px;
  padding-right: 35px;
  border: 1px solid #cbd5e1;
  border-radius: 8px;
  outline: none;
  width: 250px;
  font-size: 14px;
  transition: border-color 0.2s;
}

.input-wrapper input:focus {
  border-color: #3b82f6;
  box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1);
}

.search-btn {
  position: absolute;
  right: 5px;
  background: none;
  border: none;
  cursor: pointer;
  opacity: 0.6;
  font-size: 14px;
}

.search-btn:hover {
  opacity: 1;
}

h2 {
  font-size: 24px;
  color: #1e293b;
  margin: 0;
}

.count-badge {
  background: #f1f5f9;
  padding: 4px 10px;
  border-radius: 20px;
  font-size: 13px;
  color: #64748b;
  font-weight: 600;
  border: 1px solid #e2e8f0;
}

.refresh-btn {
  background: white;
  border: 1px solid #cbd5e1;
  color: #475569;
  padding: 8px 16px;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.2s;
}

.refresh-btn:hover {
  background: #f8fafc;
  color: #1e293b;
  border-color: #94a3b8;
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
  padding: 14px 16px;
  color: #64748b;
  font-size: 12px;
  text-transform: uppercase;
  border-bottom: 1px solid #e2e8f0;
  font-weight: 700;
}

td {
  padding: 14px 16px;
  border-bottom: 1px solid #f1f5f9;
  vertical-align: middle;
  font-size: 14px;
}

.desc-cell {
  color: #334155;
  font-weight: 500;
}

.date-cell {
  color: #64748b;
  font-variant-numeric: tabular-nums;
}

.log-badge {
  padding: 4px 10px;
  border-radius: 6px;
  font-size: 11px;
  font-weight: 700;
  display: inline-block;
  letter-spacing: 0.5px;
}

.log-badge.issued {
  background: #dcfce7;
  color: #166534;
  border: 1px solid #bbf7d0;
}

.log-badge.returned {
  background: #dbeafe;
  color: #1e40af;
  border: 1px solid #bfdbfe;
}

.log-badge.created {
  background: #f3e8ff;
  color: #6b21a8;
  border: 1px solid #e9d5ff;
}

.log-badge.deleted {
  background: #fee2e2;
  color: #991b1b;
  border: 1px solid #fecaca;
}

.log-badge.default {
  background: #f1f5f9;
  color: #475569;
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
  margin-top: 24px;
}

.pagination-links {
  display: flex;
  background: white;
  border-radius: 8px;
  overflow: hidden;
  border: 1px solid #e2e8f0;
}

.pagination-links button {
  padding: 8px 12px;
  border: none;
  border-right: 1px solid #e2e8f0;
  background: white;
  cursor: pointer;
  color: #64748b;
}

.pagination-links button:last-child {
  border-right: none;
}

.pagination-links button.active {
  background: #3b82f6;
  color: white;
}

.pagination-links button:disabled {
  opacity: 0.5;
  cursor: default;
}
</style>