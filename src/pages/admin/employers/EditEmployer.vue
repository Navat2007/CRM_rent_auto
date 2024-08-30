<script setup>
import {ref, onMounted} from "vue";
import { useRoute } from 'vue-router';
import router from "@router";
import moment from "moment";

import UserService from "@services/UserService.js";

import AlertModal from "@components/Modals/AlertModal.vue";
import EmployerEditForm from "@components/Forms/Employers/EmployerEditForm.vue";
import PageContainer from "@components/Containers/Admin/PageContainer.vue";

const item = ref(null);
const route = useRoute();
const error = ref('');
const loading = ref(true);
const sending = ref(false);
const isAlertModalOpen = ref(false);
const isSuccessModalOpen = ref(false);

const breadcrumbs = ref([
  {
    name: 'Сотрудники',
    route: '/Admin/employers'
  },
  {
    name: 'Редактирование сотрудника',
    route: null
  }
]);

const handleEdit = (data) => {
  sending.value = true;

  let sendingData = {...data};

  sendingData.birthday = sendingData.birthday ? moment(sendingData.birthday, 'DD.MM.YYYY').format('YYYY-MM-DD') : null;
  sendingData.hireDate = sendingData.hireDate ? moment(sendingData.hireDate, 'DD.MM.YYYY').format('YYYY-MM-DD') : null;

  console.log(sendingData);

  return;

  UserService.editUser(sendingData).then((response) => {
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

const handleArchive = () => {
  console.log("send to archive...");
  sending.value = true;
}

const onSuccess = () => {
  sending.value = false;
  isSuccessModalOpen.value = false;
  router.push('/Admin/employers/');
}

async function fetchData() {
  item.value = await UserService.getUserById(route.params.id);
  loading.value = false;
}

onMounted(fetchData);
</script>

<template>
  <PageContainer :loading="loading" :breadcrumbs="breadcrumbs">
    <EmployerEditForm @onSubmit="handleEdit" @onDelete="handleArchive" :item="item" :sending="sending" />

    <AlertModal :isOpen="isSuccessModalOpen" @close="onSuccess" title="Запрос выполнен" accept/>
    <AlertModal :isOpen="isAlertModalOpen" @close="isAlertModalOpen = false" :title="error" info/>
  </PageContainer>
</template>