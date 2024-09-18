<script setup>
import {ref} from "vue";
import router from "@router";
import moment from "moment";

import LegalPersonsService from "@services/LegalPersonsService.js";

import AlertModal from "@components/Modals/AlertModal.vue";
import PageContainer from "@components/Containers/Admin/PageContainer.vue";
import LegalPersonAddForm from "@components/Forms/LegalPerson/LegalPersonAddForm.vue";

const userID = ref(null);
const error = ref('');
const loading = ref(false);
const sending = ref(false);
const isAlertModalOpen = ref(false);
const isSuccessModalOpen = ref(false);

const breadcrumbs = ref([
  {
    name: 'Юр лица',
    route: '/Admin/legalPerson'
  },
  {
    name: 'Добавление юр лица',
    route: null
  }
]);

const validateData = (data) => {
  data.registration_date = data.registration_date && data.registration_date !== "Invalid date" ? moment(data.registration_date, 'DD.MM.YYYY').format('YYYY-MM-DD') : null;

  return data;
}

const handleAdd = (data) => {
  sending.value = true;

  let sendingData = {...data};

  sendingData = validateData(sendingData);

  LegalPersonsService.addLegalPerson(sendingData).then((response) => {
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
  router.push('/Admin/legalPerson/');
}
</script>

<template>
  <PageContainer :breadcrumbs="breadcrumbs">
    <LegalPersonAddForm @onSubmit="handleAdd" />

    <AlertModal :isOpen="isSuccessModalOpen" @close="onSuccess" title="Запрос выполнен" accept/>
    <AlertModal :isOpen="isAlertModalOpen" @close="isAlertModalOpen = false" :title="error" info/>
  </PageContainer>
</template>