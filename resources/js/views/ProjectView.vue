<template>
  <div>
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
      <div>
        <h2 class="text-2xl font-bold text-gray-900">Project</h2>
        <p class="text-sm text-gray-500">Kelola semua project</p>
      </div>
      <button @click="openModal()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
        + Tambah Project
      </button>
    </div>

    <div class="mb-6 flex gap-4">
      <input
        v-model="filters.search"
        @input="fetchProjects"
        type="text"
        placeholder="🔍 Cari project..."
        class="w-full sm:w-80 px-4 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
      />
      <select
        v-model="filters.status"
        @change="fetchProjects"
        class="px-4 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none bg-white"
      >
        <option value="all">Semua Status</option>
        <option value="active">Active</option>
        <option value="archived">Archived</option>
      </select>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-gray-600">
          <thead class="bg-gray-50 text-xs uppercase text-gray-500 border-b border-gray-100">
            <tr>
              <th class="px-6 py-4 font-medium">Nama</th>
              <th class="px-6 py-4 font-medium">Deskripsi</th>
              <th class="px-6 py-4 font-medium">Status</th>
              <th class="px-6 py-4 font-medium text-center">Task</th>
              <th class="px-6 py-4 font-medium">Dibuat</th>
              <th class="px-6 py-4 font-medium text-center">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr v-if="projects.length === 0">
              <td colspan="6" class="px-6 py-8 text-center text-gray-500">Tidak ada data project.</td>
            </tr>
            <tr v-for="project in projects" :key="project.id" class="hover:bg-gray-50 transition">
              <td class="px-6 py-4 font-medium text-gray-900">{{ project.name }}</td>
              <td class="px-6 py-4 truncate max-w-xs">{{ project.description || '-' }}</td>
              <td class="px-6 py-4">
                <span :class="project.status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700'" class="px-2.5 py-1 rounded-full text-xs font-medium">
                  {{ project.status }}
                </span>
              </td>
              <td class="px-6 py-4 text-center font-medium">{{ project.tasks_count }}</td>
              <td class="px-6 py-4 text-xs">{{ formatDate(project.created_at) }}</td>
              <td class="px-6 py-4 text-center space-x-2">
                <router-link :to="{ name: 'project-detail', params: { id: project.id } }" class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1.5 rounded transition">
                Detail
                </router-link>
                <button @click="openModal(project)" class="text-xs bg-blue-50 hover:bg-blue-100 text-blue-600 px-3 py-1.5 rounded transition">Edit</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <Modal :isOpen="isModalOpen" :title="isEditing ? 'Edit Project' : 'Tambah Project'" @close="closeModal">
      <form @submit.prevent="submitForm">
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1">Nama Project <span class="text-red-500">*</span></label>
          <input v-model="form.name" type="text" class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none" :class="{'border-red-500': errors.name}" />
          <span v-if="errors.name" class="text-xs text-red-500 mt-1 block">{{ errors.name[0] }}</span>
        </div>

        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
          <textarea v-model="form.description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none"></textarea>
        </div>

        <div class="mb-6"> <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
          <select v-model="form.status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none bg-white">
            <option value="active">Active</option>
            <option value="archived">Archived</option>
          </select>
        </div>

        <div v-if="isEditing" class="mb-6">
          <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
          <select v-model="form.status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none bg-white">
            <option value="active">Active</option>
            <option value="archived">Archived</option>
          </select>
        </div>

        <div class="flex justify-end gap-2">
          <button type="button" @click="closeModal" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md transition">Batal</button>
          <button type="submit" :disabled="isLoading" class="px-4 py-2 text-sm font-medium text-white bg-blue-500 hover:bg-blue-600 rounded-md transition disabled:opacity-50">
            {{ isLoading ? 'Menyimpan...' : 'Simpan' }}
          </button>
        </div>
      </form>
    </Modal>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import api from '@/services/api';
import Modal from '@/components/Modal.vue';

const projects = ref<any[]>([]);
const filters = ref({ search: '', status: 'all' });

// Modal & Form State
const isModalOpen = ref(false);
const isEditing = ref(false);
const isLoading = ref(false);
const errors = ref<Record<string, string[]>>({});
const form = ref({ id: null, name: '', description: '', status: 'active' });

const fetchProjects = async () => {
    try {
        const response = await api.get('/projects', { params: filters.value });
        projects.value = response.data;
    } catch (error) {
        console.error('Failed to fetch projects', error);
    }
};

const openModal = (project: any = null) => {
    errors.value = {};
    if (project) {
        isEditing.value = true;
        form.value = { ...project };
    } else {
        isEditing.value = false;
        form.value = { id: null, name: '', description: '', status: 'active' };
    }
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
};

const submitForm = async () => {
    isLoading.value = true;
    errors.value = {};

    try {
        if (isEditing.value) {
            await api.put(`/projects/${form.value.id}`, form.value);
        } else {
            await api.post('/projects', form.value);
        }
        closeModal();
        fetchProjects(); // Refresh table
    } catch (error: any) {
        if (error.response && error.response.status === 422) {
            errors.value = error.response.data.errors; // Per-field validation errors
        } else {
            alert('Terjadi kesalahan pada server.');
        }
    } finally {
        isLoading.value = false;
    }
};

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
};

onMounted(() => {
    fetchProjects();
});
</script>
