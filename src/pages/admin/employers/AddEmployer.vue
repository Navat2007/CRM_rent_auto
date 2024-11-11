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
const sending = ref(false);
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
  sending.value = true;

  let sendingData = {...data};

  sendingData.birthday = sendingData.birthday && sendingData.birthday !== "Invalid date" ? moment(sendingData.birthday, 'DD.MM.YYYY').format('YYYY-MM-DD') : null;
  sendingData.hireDate = sendingData.hireDate && sendingData.hireDate !== "Invalid date" ? moment(sendingData.hireDate, 'DD.MM.YYYY').format('YYYY-MM-DD') : null;
  sendingData.firingDate = sendingData.firingDate && sendingData.firingDate !== "Invalid date" ? moment(sendingData.firingDate, 'DD.MM.YYYY').format('YYYY-MM-DD') : null;
  sendingData.passport_date_of_issue = sendingData.passport_date_of_issue && sendingData.passport_date_of_issue !== "Invalid date" ? moment(sendingData.passport_date_of_issue, 'DD.MM.YYYY').format('YYYY-MM-DD') : null;
  sendingData.dl_issued_date = sendingData.dl_issued_date && sendingData.dl_issued_date !== "Invalid date" ? moment(sendingData.dl_issued_date, 'DD.MM.YYYY').format('YYYY-MM-DD') : null;
  sendingData.dl_expire_date = sendingData.dl_expire_date && sendingData.dl_expire_date !== "Invalid date" ? moment(sendingData.dl_expire_date, 'DD.MM.YYYY').format('YYYY-MM-DD') : null;

  UserService.addUser(sendingData).then((response) => {
    if (response.data) {
      if (parseInt(response.data.error) === 0) {
        isSuccessModalOpen.value = true
        userID.value = response.data.params.id;
        sending.value = false;
      } else {
        error.value = response.data.error_text
        isAlertModalOpen.value = true
        sending.value = false;
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
    <EmployerAddForm @onSubmit="handleAdd" :sending="sending"/>

    <AlertModal :isOpen="isSuccessModalOpen" @close="onSuccess" title="Запрос выполнен" accept/>
    <AlertModal :isOpen="isAlertModalOpen" @close="isAlertModalOpen = false" :title="error" info/>
  </PageContainer>
</template>