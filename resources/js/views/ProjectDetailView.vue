<template>
  <div v-if="project" class="h-full flex flex-col">
    <div class="mb-6">
      <router-link to="/projects" class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 mb-6 transition shadow-sm">
        ← Kembali
      </router-link>

      <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex justify-between items-start mb-4">
          <div>
            <div class="flex items-center gap-3 mb-2">
              <h1 class="text-2xl font-bold text-gray-900">{{ project.name }}</h1>
              <span :class="project.status === 'active' ? 'text-green-600 bg-green-50' : 'text-gray-600 bg-gray-100'" class="px-3 py-1 text-xs font-bold rounded-full">
                {{ project.status }}
              </span>
            </div>
            <p class="text-gray-500 text-sm max-w-2xl">{{ project.description || 'Tidak ada deskripsi.' }}</p>
          </div>
          <button @click="openProjectModal" class="px-4 py-2 border border-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition">
            Edit
          </button>
        </div>

        <div class="flex gap-8 border-t border-gray-100 pt-4 mt-4">
          <div>
            <p class="text-xs text-gray-500 uppercase font-bold mb-1">Dibuat</p>
            <p class="text-sm font-medium text-gray-900">{{ formatDate(project.created_at) }}</p>
          </div>
          <div>
            <p class="text-xs text-gray-500 uppercase font-bold mb-1">Total Task</p>
            <p class="text-sm font-medium text-gray-900">{{ stats.total }}</p>
          </div>
          <div>
            <p class="text-xs text-gray-500 uppercase font-bold mb-1">Selesai</p>
            <p class="text-sm font-medium text-green-600">{{ stats.completed }} / {{ stats.total }}</p>
          </div>
        </div>
      </div>
    </div>

    <div class="flex justify-between items-center mb-4">
      <h2 class="text-lg font-bold text-gray-900">Task</h2>
      <button @click="openTaskModal()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm">
        + Tambah Task
      </button>
    </div>

    <div class="flex gap-6 overflow-x-auto pb-4 flex-1 items-start">
      <div
        v-for="category in categories"
        :key="category.id"
        class="min-w-[300px] w-[300px] bg-gray-50/50 border border-gray-200 rounded-xl p-4 flex flex-col max-h-full"
        @dragover.prevent
        @drop="onDrop($event, category.id)"
      >
        <div class="flex justify-between items-center mb-4 border-b border-gray-200 pb-2">
          <h3 class="font-bold text-sm text-gray-700 uppercase tracking-wider">{{ category.name }}</h3>
          <span class="bg-white px-2 py-0.5 rounded-full text-xs font-bold text-gray-500 border border-gray-200 shadow-sm">
            {{ getTasksByCategory(category.id).length }}
          </span>
        </div>

        <div class="flex-1 overflow-y-auto space-y-3 min-h-[150px]">
          <div
            v-for="task in getTasksByCategory(category.id)"
            :key="task.id"
            draggable="true"
            @dragstart="onDragStart($event, task)"
            class="bg-white p-4 rounded-lg shadow-sm border border-gray-100 cursor-grab active:cursor-grabbing hover:border-blue-300 transition group"
          >
            <h4 class="font-bold text-sm text-gray-900 mb-1">{{ task.title }}</h4>
            <p v-if="task.due_date" class="text-xs text-gray-500 mb-3 flex items-center gap-1">
              📅 {{ formatDate(task.due_date) }}
            </p>
            <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity mt-2">
              <button @click="openTaskModal(task)" class="text-xs px-2 py-1 bg-blue-50 text-blue-600 rounded hover:bg-blue-100">Edit</button>
              <button @click="deleteTask(task.id)" class="text-xs px-2 py-1 bg-red-50 text-red-600 rounded hover:bg-red-100">Hapus</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <Modal :isOpen="isModalOpen" :title="modalType === 'project' ? 'Edit Project' : (isEditingTask ? 'Edit Task' : 'Tambah Task')" @close="closeModal">
      <form v-if="modalType === 'project'" @submit.prevent="submitProjectForm">
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1">Nama Project <span class="text-red-500">*</span></label>
          <input v-model="projectForm.name" type="text" class="w-full px-3 py-2 border rounded-md" :class="{'border-red-500': errors.name}" />
          <span v-if="errors.name" class="text-xs text-red-500 mt-1 block">{{ errors.name[0] }}</span>
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
          <textarea v-model="projectForm.description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md"></textarea>
        </div>
        <div class="mb-6">
          <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
          <select v-model="projectForm.status" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white">
            <option value="active">Active</option>
            <option value="archived">Archived</option>
          </select>
        </div>
        <div class="flex justify-end gap-2">
          <button type="button" @click="closeModal" class="px-4 py-2 text-sm bg-gray-100 hover:bg-gray-200 rounded-md">Batal</button>
          <button type="submit" class="px-4 py-2 text-sm text-white bg-blue-500 hover:bg-blue-600 rounded-md">Simpan</button>
        </div>
      </form>

      <form v-if="modalType === 'task'" @submit.prevent="submitTaskForm">
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1">Judul Task <span class="text-red-500">*</span></label>
          <input v-model="taskForm.title" type="text" class="w-full px-3 py-2 border rounded-md" :class="{'border-red-500': errors.title}" placeholder="Masukkan judul task" />
          <span v-if="errors.title" class="text-xs text-red-500 mt-1 block">{{ errors.title[0] }}</span>
        </div>

        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
          <textarea v-model="taskForm.description" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="Deskripsi singkat..."></textarea>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
            <select v-model="taskForm.category_id" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white">
              <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Due Date</label>
            <input v-model="taskForm.due_date" type="date" class="w-full px-3 py-2 border border-gray-300 rounded-md" />
          </div>
        </div>

        <div class="mb-6">
          <label class="block text-sm font-medium text-gray-700 mb-1">Project</label>
          <select v-model="taskForm.project_id" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white">
            <option v-for="proj in projectsList" :key="proj.id" :value="proj.id">{{ proj.name }}</option>
          </select>
          <span v-if="errors.project_id" class="text-xs text-red-500 mt-1 block">{{ errors.project_id[0] }}</span>
        </div>

        <div class="flex justify-end gap-2">
          <button type="button" @click="closeModal" class="px-4 py-2 text-sm bg-gray-100 hover:bg-gray-200 rounded-md font-medium">Batal</button>
          <button type="submit" class="px-4 py-2 text-sm text-white bg-blue-500 hover:bg-blue-600 rounded-md font-medium">Simpan</button>
        </div>
      </form>
    </Modal>
  </div>
  <div v-else class="flex justify-center items-center h-full">Loading...</div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import api from '@/services/api';
import Modal from '@/components/Modal.vue';

const route = useRoute();
const projectId = route.params.id;

// Data State
const project = ref<any>(null);
const categories = ref<any[]>([]);
const stats = ref({ total: 0, completed: 0 });

// Modal State
const isModalOpen = ref(false);
const modalType = ref<'project' | 'task'>('task');
const isEditingTask = ref(false);
const errors = ref<Record<string, string[]>>({});

// Forms
const projectForm = ref({ name: '', description: '', status: '' });
const taskForm = ref({ id: null, title: '', description: '', due_date: '', category_id: null, project_id: projectId });

// Fetch Initial Data
const fetchDetail = async () => {
    try {
        const response = await api.get(`/projects/${projectId}`);
        project.value = response.data.project;
        categories.value = response.data.categories;
        stats.value = response.data.stats;
    } catch (error) {
        console.error('Error fetching project data', error);
    }
};

const getTasksByCategory = (categoryId: number) => {
    if (!project.value || !project.value.tasks) return [];
    return project.value.tasks.filter((t: any) => t.category_id === categoryId);
};

// --- Drag & Drop Mechanics ---
const onDragStart = (event: DragEvent, task: any) => {
    event.dataTransfer?.setData('taskId', task.id.toString());
};

const onDrop = async (event: DragEvent, newCategoryId: number) => {
    const taskIdStr = event.dataTransfer?.getData('taskId');
    if (!taskIdStr) return;

    const taskId = parseInt(taskIdStr);
    const task = project.value.tasks.find((t: any) => t.id === taskId);

    // If dropped in the same category, do nothing
    if (task && task.category_id !== newCategoryId) {
        // Optimistic UI Update (instant visual change)
        task.category_id = newCategoryId;

        // Update via API
        try {
            await api.put(`/tasks/${taskId}`, { category_id: newCategoryId });
            fetchDetail(); // Refresh stats
        } catch (error) {
            console.error('Failed to move task', error);
            fetchDetail(); // Revert if failed
        }
    }
};

// --- Modals & Actions ---
const closeModal = () => { isModalOpen.value = false; errors.value = {}; };

const openProjectModal = () => {
    modalType.value = 'project';
    projectForm.value = { name: project.value.name, description: project.value.description, status: project.value.status };
    isModalOpen.value = true;
};

const openTaskModal = (task: any = null) => {
    modalType.value = 'task';
    errors.value = {};
    if (task) {
        isEditingTask.value = true;
        taskForm.value = { ...task, project_id: projectId };
    } else {
        isEditingTask.value = false;
        taskForm.value = { id: null, title: '', description: '', due_date: '', category_id: categories.value[0]?.id || null, project_id: projectId };
    }
    isModalOpen.value = true;
};

const submitProjectForm = async () => {
    try {
        await api.put(`/projects/${projectId}`, projectForm.value);
        closeModal();
        fetchDetail();
    } catch (error: any) {
        if (error.response?.status === 422) errors.value = error.response.data.errors;
    }
};

const submitTaskForm = async () => {
    try {
        if (isEditingTask.value) {
            await api.put(`/tasks/${taskForm.value.id}`, taskForm.value);
        } else {
            await api.post('/tasks', taskForm.value);
        }
        closeModal();
        fetchDetail();
    } catch (error: any) {
        if (error.response?.status === 422) errors.value = error.response.data.errors;
    }
};

const deleteTask = async (id: number) => {
    if (confirm('Yakin ingin menghapus task ini?')) {
        await api.delete(`/tasks/${id}`);
        fetchDetail();
    }
};

const formatDate = (dateString: string) => {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
};

// 1. Add a new state variable for the projects list near your other refs
const projectsList = ref<any[]>([]);

// 2. Create a function to fetch the projects
const fetchProjectsList = async () => {
    try {
        const response = await api.get('/projects');
        projectsList.value = response.data;
    } catch (error) {
        console.error('Failed to fetch projects list', error);
    }
};

onMounted(() => {
    fetchDetail();
    fetchProjectsList();
});

</script>
