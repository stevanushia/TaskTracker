<template>
  <div>
    <div class="mb-8">
      <h2 class="text-2xl font-bold text-gray-900">Dashboard</h2>
      <p class="text-gray-500">Selamat datang 👋</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
      <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center">
        <div class="w-12 h-12 rounded-lg bg-blue-50 flex items-center justify-center text-2xl mr-4">🗂️</div>
        <div>
          <p class="text-sm font-medium text-gray-500">Project Aktif</p>
          <p class="text-2xl font-bold text-gray-900">{{ stats.active_projects }}</p>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center">
        <div class="w-12 h-12 rounded-lg bg-yellow-50 flex items-center justify-center text-2xl mr-4">⏳</div>
        <div>
          <p class="text-sm font-medium text-gray-500">Task Belum Selesai</p>
          <p class="text-2xl font-bold text-gray-900">{{ stats.incomplete_tasks }}</p>
        </div>
      </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
      <h3 class="text-base font-bold text-gray-900 mb-4">Task Mendekati Due Date</h3>

      <div v-if="stats.upcoming_tasks.length === 0" class="text-gray-500 text-sm py-4">
        Tidak ada task yang mendekati due date.
      </div>

      <div class="space-y-4">
        <div v-for="task in stats.upcoming_tasks" :key="task.id" class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
          <div class="flex items-start">
            <div class="w-2 h-2 mt-2 rounded-full bg-blue-500 mr-3"></div>
            <div>
              <p class="text-sm font-bold text-gray-900">{{ task.title }}</p>
              <p class="text-xs text-gray-500">{{ task.project_name }}</p>
            </div>
          </div>
          <div class="text-xs font-medium text-gray-500">
            {{ formatDate(task.due_date) }}
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import api from '@/services/api';

const stats = ref({
    active_projects: 0,
    incomplete_tasks: 0,
    upcoming_tasks: [] as any[]
});

const fetchDashboardData = async () => {
    try {
        const response = await api.get('/dashboard');
        stats.value = response.data;
    } catch (error) {
        console.error('Failed to fetch dashboard data', error);
    }
};

// Simple date formatter
const formatDate = (dateString: string) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
};

onMounted(() => {
    fetchDashboardData();
});
</script>
