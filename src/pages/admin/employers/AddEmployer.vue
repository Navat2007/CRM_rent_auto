<script setup>
import {ref} from "vue";
import router from "@router";
import moment from "moment";

import UserService from "@services/UserService.js";

import AlertModal from "@components/Modals/AlertModal.vue";
import EmployerAddForm from "@components/Forms/Employers/EmployerAddForm.vue";
import PageContainer from "@components/Containers/Admin/PageContainer.vue";

const userID = ref(null);
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
  data.birthday = data.birthday && data.birthday !== "Invalid date" ? moment(data.birthday, 'DD.MM.YYYY').format('YYYY-MM-DD') : null;
  data.hireDate = data.hireDate && data.hireDate !== "Invalid date" ? moment(data.hireDate, 'DD.MM.YYYY').format('YYYY-MM-DD') : null;

  UserService.addUser(data).then((response) => {
    if (response.data) {
      if (parseInt(response.data.error) === 0) {
        isSuccessModalOpen.value = true
        userID.value = response.data.params.id;
      } else {
        error.value = response.data.error_text
        isAlertModalOpen.value = true
      }
    }
  });
}

const onSuccess = () => {
  isSuccessModalOpen.value = false;
  router.push('/Admin/employers/' + userID.value);
}
</script>

<template>
  <PageContainer :breadcrumbs="breadcrumbs">
    <EmployerAddForm @onSubmit="handleAdd" />

    <AlertModal :isOpen="isSuccessModalOpen" @close="onSuccess" title="Запрос выполнен" accept/>
    <AlertModal :isOpen="isAlertModalOpen" @close="isAlertModalOpen = false" :title="error" info/>
  </PageContainer>
</template>