<script setup>
import {onMounted, ref} from "vue";
import {useAuthStore} from "@stores";
import {useForm} from "vue-hooks-form";
import router from "@router";

import Checkbox from "@components/Inputs/Checkbox.vue";
import Table from "@components/Table/DataTable.vue";
import DirectoryService from "@services/DirectoryService.js";

const {useField, validateFields} = useForm({
  defaultValues: {
    login: '',
    password: '',
    passwordConfirm: '',
    active: true
  },
  validateMode: 'submit'
});

const {user} = useAuthStore();
const columns = ref([
  {
    label: 'ID',
    field: 'id',
    type: 'number',
    filter: "number",
    sorting: true,
  },
  {
    label: 'Название',
    field: 'name',
    type: 'string',
    filter: "string",
    sorting: true,
  },
  {
    label: 'Статус',
    field: 'archive',
    type: 'string',
    filter: "select",
    sorting: true,
  }
]);
const baseItems = ref([]);
const items = ref([]);
const rowItem = ref({});
const error = ref('');
const loading = ref(true);

const archive = useField('active');

const handleAddButtonClick = (item) => {
  router.push('/admin/directory/positions/new');
}
const handleRowClick = (item) => {
  //router.push({ name: 'AdminEditUser', params: { id: item.id } });
  router.push('/admin/directory/positions/' + item.id);
}

const handleFilters = () => {
  items.value = baseItems.value.filter(item => item.archive === "Активен" || (archive.value && item.archive === "В архиве"));
}

async function fetchData() {
  baseItems.value = await DirectoryService.getPositions(user.company_id);
  handleFilters(archive.value);
  loading.value = false;
}

onMounted(() => {
  fetchData();
  archive.value = false;
});
</script>

<template>
  <div class="w-full h-full bg-gray-100 dark:bg-gray-900 pt-4">
    <div v-if="loading" class="text-gray-300 font-bold text-3xl mb-4 text-center items-center">
      <span>Загрузка...</span>
    </div>
    <div v-else>
      <Table title="Таблица должностей" :items="items" :columns="columns" @onRowClick="handleRowClick">
        <template #buttons>
          <button @click="handleAddButtonClick" type="button" class="flex min-w-28 items-center justify-center px-4 py-2 text-sm font-medium text-white rounded-lg bg-primary-600 hover:bg-primary-700 focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 focus:outline-none dark:focus:ring-primary-800">
            <svg class="h-5 w-5 mr-2" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
              <path clip-rule="evenodd" fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" />
            </svg>
            Добавить
          </button>
        </template>
        <template #filter>
          <Checkbox title="Показывать архивные значения?" id="archive" :model="archive" @onChange="handleFilters"/>
        </template>
      </Table>
    </div>
  </div>
</template>

<style scoped>

</style>