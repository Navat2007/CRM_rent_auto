<script setup>
import router from "@router";
import {onMounted, ref} from "vue";

import Table from "@components/Table/DataTable.vue";
import CompanyService from "@services/CompanyService.js";

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
  }
]);
const items = ref([]);
const rowItem = ref({});
const error = ref('');
const loading = ref(true);

const handleAddButtonClick = (item) => {
  router.push({ name: 'AdminAddCompany'});
}
const handleRowClick = (item) => {
  router.push({ name: 'AdminEditCompany', params: { id: item.id } });
}

onMounted(() => {
  CompanyService.getCompanies(loading).then(data => items.value = data);
});
</script>

<template>
  <div class="w-full h-full bg-gray-100 dark:bg-gray-900 pt-4">
    <div v-if="loading" class="text-gray-300 font-bold text-3xl mb-4 text-center items-center">
      <span>Загрузка...</span>
    </div>
    <div v-else>
      <Table title="Таблица компаний" :items="items" :columns="columns" @onRowClick="handleRowClick">
        <button @click="handleAddButtonClick" type="button" class="flex min-w-28 items-center justify-center px-4 py-2 text-sm font-medium text-white rounded-lg bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 focus:outline-none dark:focus:ring-primary-800">
          <svg class="h-5 w-5 mr-2" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <path clip-rule="evenodd" fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" />
          </svg>
          Добавить
        </button>
      </Table>
    </div>
  </div>
</template>