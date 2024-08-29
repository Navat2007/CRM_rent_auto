<script setup>
import {onMounted, ref} from "vue";
import router from "@router";

import UserService from "@services/UserService.js";

import AlertModal from "@components/Modals/AlertModal.vue";
import EmployerAddForm from "@components/Forms/Employers/EmployerAddForm.vue";
import PageContainer from "@components/Containers/Admin/PageContainer.vue";

const error = ref('');
const loading = ref(false);
const isAlertModalOpen = ref(false);
const isSuccessModalOpen = ref(false);

const breadcrumbs = ref([
  {
    name: 'Сотрудники',
    route: '/Admin/employers'
  },
  {
    name: 'Добавление сотрудника',
    route: null
  }
]);

const handleAdd = (data) => {
  console.log(data);

  return;

  UserService.addUser(data).then((response) => {
    console.log(response.data);
    if (response.data) {
      if (parseInt(response.data.error) === 0) {
        isSuccessModalOpen.value = true
      } else {
        error.value = response.data.error_text
        isAlertModalOpen.value = true
      }
    }
  });
}

const onSuccess = () => {
  isSuccessModalOpen.value = false;
  //router.push('/Admin/directory/positions');
}
</script>

<template>
  <PageContainer :breadcrumbs="breadcrumbs">
    <EmployerAddForm @onSubmit="handleAdd" />

    <AlertModal :isOpen="isSuccessModalOpen" @close="onSuccess" title="Запрос выполнен" accept/>
    <AlertModal :isOpen="isAlertModalOpen" @close="isAlertModalOpen = false" :title="error" info/>
  </PageContainer>
</template>