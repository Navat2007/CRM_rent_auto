<script setup>
import {onMounted, ref} from "vue";
import axios from "axios";
import buildFormData from "@utils/buildFormData.js";

import Table from "@components/Table/DataTable.vue";
import AlertModal from "@components/Modals/AlertModal.vue";
import BaseModal from "@components/Modals/BaseModal.vue";
import UserAddForm from "@components/Forms/UserAddForm.vue";
import UserEditForm from "@components/Forms/UserEditForm.vue";
import UserService from "@services/UserService.js";
import router from "@router";

const columns = ref([
  {
    label: 'ID',
    field: 'id',
    type: 'number',
    filter: "number",
    sorting: true,
  },
  {
    label: 'Email',
    field: 'email',
    type: 'string',
    filter: "string",
    sorting: true,
  },
  {
    label: 'Статус',
    field: 'status',
    type: 'string',
    filter: "select",
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
  UserService.getUsers(loading).then(data => items.value = data);
}
const handleRowClick = (item) => {
  router.push({ name: 'AdminEditUser', params: { id: item.id } });
}
const handleAdd = (data) => {
  console.log(data);

  UserService.addUser(data).then((response) => {
    console.log(response);
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
      <Table title="Таблица пользователей" :items="items" :columns="columns" @onRowClick="handleRowClick">
        <button @click="isAddModalOpen = true" type="button" class="flex min-w-28 items-center justify-center px-4 py-2 text-sm font-medium text-white rounded-lg bg-primary-600 hover:bg-primary-700 focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 focus:outline-none dark:focus:ring-primary-800">
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
  <BaseModal :isOpen="isAddModalOpen" @close="isAddModalOpen = false" title="Добавление пользователя">
    <UserAddForm @onSubmit="handleAdd"/>
  </BaseModal>
  <BaseModal :isOpen="isEditModalOpen" @close="isEditModalOpen = false" title="Редактирование пользователя">
    <UserEditForm :item="rowItem" @onSubmit="handleEdit" @onDelete="handleDelete"/>
  </BaseModal>
</template>

<style scoped>

</style>