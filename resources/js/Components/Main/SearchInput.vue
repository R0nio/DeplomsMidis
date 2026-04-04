<script setup>
import { ref } from 'vue'
import { FwbAutocomplete } from 'flowbite-vue'

const selectedUser = ref(null)
const users = [
  {
    id: 1,
    name: 'John Doe',
    email: 'john@example.com',
    avatar: 'https://i.pravatar.cc/150?img=1'
  },
  {
    id: 2,
    name: 'Jane Smith',
    email: 'jane@example.com',
    avatar: 'https://i.pravatar.cc/150?img=2'
  },
]
</script>
<template>
  <fwb-autocomplete
    v-model="selectedUser"
    :options="users"
    :search-fields="['name', 'email']"
    display="name"
    noResultsText = "Не найдено"
    size="lg"
    loadingText="Загрузка..."
    placeholder="Поиск..."
    :class="bg-gray-200"
  >
    <template #option="{ option }">
      <div class="flex items-center">
        <img
          :src="option.avatar"
          :alt="option.name"
          class="w-8 h-8 rounded-full mr-3"
        >
        <div>
          <div class="font-medium">{{ option.name }}</div>
          <div class="text-sm text-gray-500">{{ option.email }}</div>
        </div>
      </div>
    </template>
  </fwb-autocomplete>
</template>
