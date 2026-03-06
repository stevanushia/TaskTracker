<template>
  <div>
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
      <div>
        <h2 class="text-2xl font-bold text-gray-900">Task</h2>
        <p class="text-sm text-gray-500">Semua task lintas project</p>
      </div>
      <button @click="openModal()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
        + Tambah Task
      </button>
    </div>

    <div class="mb-6 flex flex-wrap gap-4">
      <input
        v-model="filters.search"
        @input="fetchTasks"
        type="text"
        placeholder="🔍 Cari task..."
        class="w-full sm:w-80 px-4 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
      />
      <select
        v-model="filters.category_id"
        @change="fetchTasks"
        class="px-4 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none bg-white min-w-[150px]"
      >
        <option value="all">Semua Kategori</option>
        <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
      </select>
      <select
        v-model="filters.project_id"
        @change="fetchTasks"
        class="px-4 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none bg-white min-w-[150px]"
      >
        <option value="all">Semua Project</option>
        <option v-for="proj in projects" :key="proj.id" :value="proj.id">{{ proj.name }}</option>
      </select>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-gray-600">
          <thead class="bg-gray-50 text-xs uppercase text-gray-500 border-b border-gray-100">
            <tr>
              <th class="px-6 py-4 font-medium">Judul Task</th>
              <th class="px-6 py-4 font-medium">Project</th>
              <th class="px-6 py-4 font-medium">Kategori</th>
              <th class="px-6 py-4 font-medium">Due Date</th>
              <th class="px-6 py-4 font-medium text-center">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr v-if="tasks.length === 0">
              <td colspan="5" class="px-6 py-8 text-center text-gray-500">Tidak ada data task.</td>
            </tr>
            <tr v-for="task in tasks" :key="task.id" class="hover:bg-gray-50 transition">
              <td class="px-6 py-4 font-medium text-gray-900">{{ task.title }}</td>
              <td class="px-6 py-4">{{ task.project?.name || '-' }}</td>
              <td class="px-6 py-4">
                <span :class="getCategoryColor(task.category?.name)" class="px-2.5 py-1 rounded-full text-xs font-bold">
                  {{ task.category?.name || '-' }}
                </span>
              </td>
              <td class="px-6 py-4 font-medium" :class="isOverdue(task.due_date) ? 'text-red-500' : 'text-gray-600'">
                {{ formatDate(task.due_date) }} <span v-if="isOverdue(task.due_date)">⚠️</span>
              </td>
              <td class="px-6 py-4 text-center space-x-2">
                <button @click="openModal(task)" class="text-xs bg-blue-50 hover:bg-blue-100 text-blue-600 px-3 py-1.5 rounded transition font-medium">Edit</button>
                <button @click="confirmDeleteTask(task)" class="text-xs bg-red-50 hover:bg-red-100 text-red-600 px-3 py-1.5 rounded transition font-medium">Hapus</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <Modal :isOpen="isModalOpen" :title="isEditing ? 'Edit Task' : 'Tambah Task Baru'" @close="closeModal">
      <form @submit.prevent="submitForm">
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1">Judul Task <span class="text-red-500">*</span></label>
          <input v-model="form.title" type="text" class="w-full px-3 py-2 border rounded-md" :class="{'border-red-500': errors.title}" placeholder="Masukkan judul task" />
          <span v-if="errors.title" class="text-xs text-red-500 mt-1 block">{{ errors.title[0] }}</span>
        </div>

        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
          <textarea v-model="form.description" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="Deskripsi singkat..."></textarea>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
            <select v-model="form.category_id" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white">
              <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Due Date</label>
            <input v-model="form.due_date" type="date" class="w-full px-3 py-2 border border-gray-300 rounded-md" />
          </div>
        </div>

        <div class="mb-6">
          <label class="block text-sm font-medium text-gray-700 mb-1">Project <span class="text-red-500">*</span></label>
          <select v-model="form.project_id" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white" :class="{'border-red-500': errors.project_id}">
            <option :value="null" disabled>Pilih Project</option>
            <option v-for="proj in projects" :key="proj.id" :value="proj.id">{{ proj.name }}</option>
          </select>
          <span v-if="errors.project_id" class="text-xs text-red-500 mt-1 block">{{ errors.project_id[0] }}</span>
        </div>

        <div class="flex justify-end gap-2">
          <button type="button" @click="closeModal" class="px-4 py-2 text-sm bg-gray-100 hover:bg-gray-200 rounded-md font-medium">Batal</button>
          <button type="submit" class="px-4 py-2 text-sm text-white bg-blue-500 hover:bg-blue-600 rounded-md font-medium">Simpan</button>
        </div>
      </form>
    </Modal>
    
    <ConfirmDeleteModal
      :isOpen="isDeleteModalOpen"
      :taskTitle="taskToDelete?.title || ''"
      :isLoading="isDeleting"
      @close="isDeleteModalOpen = false"
      @confirm="executeDeleteTask"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import api from '@/services/api';
import Modal from '@/components/Modal.vue';
import ConfirmDeleteModal from '@/components/ConfirmDeleteModal.vue';

const tasks = ref<any[]>([]);
const categories = ref<any[]>([]);
const projects = ref<any[]>([]);
const filters = ref({ search: '', category_id: 'all', project_id: 'all' });

const isDeleteModalOpen = ref(false);
const taskToDelete = ref<any>(null);
const isDeleting = ref(false);

// Modal State
const isModalOpen = ref(false);
const isEditing = ref(false);
const errors = ref<Record<string, string[]>>({});
const form = ref({ id: null, title: '', description: '', due_date: '', category_id: null, project_id: null });

const fetchTasks = async () => {
    try {
        const response = await api.get('/tasks', { params: filters.value });
        tasks.value = response.data.tasks;
        categories.value = response.data.categories;
        projects.value = response.data.projects;
    } catch (error) {
        console.error('Failed to fetch tasks', error);
    }
};

const openModal = (task: any = null) => {
    errors.value = {};
    if (task) {
        isEditing.value = true;
        form.value = { ...task };
    } else {
        isEditing.value = false;
        form.value = { id: null, title: '', description: '', due_date: '', category_id: categories.value[0]?.id || null, project_id: null };
    }
    isModalOpen.value = true;
};

const closeModal = () => { isModalOpen.value = false; };

const submitForm = async () => {
    errors.value = {};
    try {
        if (isEditing.value) {
            await api.put(`/tasks/${form.value.id}`, form.value);
        } else {
            await api.post('/tasks', form.value);
        }
        closeModal();
        fetchTasks();
    } catch (error: any) {
        if (error.response?.status === 422) errors.value = error.response.data.errors;
    }
};

const confirmDeleteTask = (task: any) => {
    taskToDelete.value = task;
    isDeleteModalOpen.value = true;
};

const executeDeleteTask = async () => {
    if (!taskToDelete.value) return;

    isDeleting.value = true;
    try {
        await api.delete(`/tasks/${taskToDelete.value.id}`);
        isDeleteModalOpen.value = false;
        fetchTasks(); // Refresh the table
    } catch (error) {
        console.error('Failed to delete task', error);
        alert('Terjadi kesalahan saat menghapus task.');
    } finally {
        isDeleting.value = false;
        taskToDelete.value = null;
    }
};

// UI Helpers
const formatDate = (dateString: string) => {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
};

const isOverdue = (dateString: string) => {
    if (!dateString) return false;
    return new Date(dateString) < new Date(new Date().setHours(0,0,0,0));
};

const getCategoryColor = (name: string) => {
    switch(name) {
        case 'Done': return 'bg-green-100 text-green-700';
        case 'InProgress': return 'bg-yellow-100 text-yellow-700';
        case 'Testing': return 'bg-purple-100 text-purple-700';
        default: return 'bg-gray-100 text-gray-700';
    }
};

onMounted(() => fetchTasks());
</script>
