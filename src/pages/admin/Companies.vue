<script setup>
import {onMounted, ref} from "vue";
import axios from "axios";
import buildFormData from "@utils/buildFormData.js";

import Table from "@components/Table/DataTable.vue";
import AlertModal from "@components/Modals/AlertModal.vue";
import BaseModal from "@components/Modals/BaseModal.vue";
import UserAddForm from "@components/Forms/UserAddForm.vue";
import UserEditForm from "@components/Forms/UserEditForm.vue";

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

const isAlertModalOpen = ref(false);
const isSuccessModalOpen = ref(false);
const isAddModalOpen = ref(false);
const isEditModalOpen = ref(false);

const loadData = async () => {
  items.value = [];
  error.value = '';
  loading.value = true

  const response = await axios.post(`${import.meta.env.VITE_API_URL}/admin/companies/load.php`);

  if(response.data.params)
  {
    items.value = response.data.params;
  }

  loading.value = false
}
const handleRowClick = (item) => {
  //rowItem.value = item
  //isEditModalOpen.value = true
}
const handleAdd = (data) => {
  let form = new FormData();
  buildFormData(form, data);

  console.log(data);

  axios.post(`${import.meta.env.VITE_API_URL}/admin/users/add.php`, form).then((response) => {
    isAddModalOpen.value = false

    if (response.data) {
      if(parseInt(response.data.error) === 0){
        isSuccessModalOpen.value = true
        loadData();
      }
      else {
        error.value = response.data.error_text
        isAlertModalOpen.value = true
      }
    }
  })
}
const handleEdit = (data) => {
  let form = new FormData();
  buildFormData(form, data);

  return;

  axios.post(`${import.meta.env.VITE_API_URL}/admin/login/check.php`, form).then((response) => {
    isEditModalOpen.value = false

    if (response.data) {
      if(response.data.error === ''){
        isSuccessModalOpen.value = true
        loadData();
      }
      else {
        error.value = response.data.error
        isAlertModalOpen.value = true
      }
    }
  })
}
const handleDelete = (data) => {
  let form = new FormData();
  buildFormData(form, data);

  return;

  axios.post(`${import.meta.env.VITE_API_URL}/admin/login/check.php`, form).then((response) => {
    isEditModalOpen.value = false
    if (response.data) {
      if(response.data.error === ''){
        isSuccessModalOpen.value = true
        loadData();
      }
      else {
        error.value = response.data.error
        isAlertModalOpen.value = true
      }
    }
  })
}

onMounted(() => {
  loadData();
});
</script>

<template>
  <div class="w-full h-full bg-gray-100 dark:bg-gray-900 pt-4">
    <div v-if="loading" class="text-gray-300 font-bold text-3xl mb-4 text-center items-center">
      <span>Загрузка...</span>
    </div>
    <div v-else>
      <Table title="Таблица компаний" :items="items" :columns="columns" @onRowClick="handleRowClick">
        <button @click="isAddModalOpen = true" type="button" class="hidden flex min-w-28 items-center justify-center px-4 py-2 text-sm font-medium text-white rounded-lg bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 focus:outline-none dark:focus:ring-primary-800">
          <svg class="h-5 w-5 mr-2" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <path clip-rule="evenodd" fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" />
          </svg>
          Добавить
        </button>
      </Table>
    </div>
  </div>

  <AlertModal :isOpen="isSuccessModalOpen" @close="isSuccessModalOpen = false" title="Запрос выполнен" accept/>
  <AlertModal :isOpen="isAlertModalOpen" @close="isAlertModalOpen = false" :title="error" info/>
  <BaseModal :isOpen="isAddModalOpen" @close="isAddModalOpen = false" title="Добавление компании">

  </BaseModal>
  <BaseModal :isOpen="isEditModalOpen" @close="isEditModalOpen = false" title="Редактирование компании">

  </BaseModal>
</template>

<style scoped>

</style>