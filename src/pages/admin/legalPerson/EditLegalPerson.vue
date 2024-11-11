<script setup>
import {ref, onMounted} from "vue";
import { useRoute } from 'vue-router';
import router from "@router";
import moment from "moment";

import LegalPersonsService from "@services/LegalPersonsService.js";

import AlertModal from "@components/Modals/AlertModal.vue";
import PageContainer from "@components/Containers/Admin/PageContainer.vue";
import LegalPersonEditForm from "@components/Forms/LegalPerson/LegalPersonEditForm.vue";

const item = ref(null);
const route = useRoute();
const error = ref('');
const loading = ref(true);
const sending = ref(false);
const isAlertModalOpen = ref(false);
const isSuccessModalOpen = ref(false);
const isArchiveModalOpen = ref(false);
const isDeleteModalOpen = ref(false);

const breadcrumbs = ref([
  {
    name: 'Юр лица',
    route: '/Admin/legalPerson'
  },
  {
    name: 'Редактирование юр лица',
    route: null
  }
]);

const validateData = (data) => {
  data.registration_date = data.registration_date && data.registration_date !== "Invalid date" ? moment(data.registration_date, 'DD.MM.YYYY').format('YYYY-MM-DD') : null;

  return data;
}

const handleEdit = (data) => {
  sending.value = true;

  let sendingData = {...data};
  sendingData.id = route.params.id;

  sendingData = validateData(sendingData);

  //console.log(sendingData);

  LegalPersonsService.editLegalPerson(sendingData).then((response) => {
    if (response.data) {
      if (parseInt(response.data.error) === 0) {
        isSuccessModalOpen.value = true
      } else {
        console.log(response.data);
        error.value = response.data.error_text
        isAlertModalOpen.value = true;
        sending.value = false;
      }
    }
  });
}
const handleArchive = () => {
  sending.value = true;
  isArchiveModalOpen.value = false;

  let sendingData = {id: route.params.id};

  LegalPersonsService.archiveLegalPerson(sendingData).then((response) => {
    if (response.data) {
      if (parseInt(response.data.error) === 0) {
        isSuccessModalOpen.value = true
      } else {
        error.value = response.data.error_text
        isAlertModalOpen.value = true
        sending.value = false;
      }
    }
  });
}
const handleDelete = () => {
  sending.value = true;
  isDeleteModalOpen.value = false;

  let sendingData = {id: route.params.id};

  LegalPersonsService.deleteLegalPerson(sendingData).then((response) => {
    if (response.data) {
      if (parseInt(response.data.error) === 0) {
        isSuccessModalOpen.value = true
      } else {
        error.value = response.data.error_text
        isAlertModalOpen.value = true
        sending.value = false;
      }
    }
  });
}

const onSuccess = () => {
  sending.value = false;
  isSuccessModalOpen.value = false;
  router.push('/Admin/legalPerson/');
}

async function fetchData() {
  item.value = await LegalPersonsService.getLegalPersonById(route.params.id);
  loading.value = false;
}

onMounted(fetchData);
</script>

<template>
  <PageContainer :loading="loading" :breadcrumbs="breadcrumbs">
    <LegalPersonEditForm @onSubmit="handleEdit" @onArchive="isArchiveModalOpen = true" @onDelete="isDeleteModalOpen = true" :item="item" :sending="sending" />

    <AlertModal :isOpen="isSuccessModalOpen" @close="onSuccess" title="Запрос выполнен" accept/>
    <AlertModal :isOpen="isAlertModalOpen" @close="isAlertModalOpen = false" :title="error" info/>
    <AlertModal
        target="#modal2"
        :isOpen="isArchiveModalOpen"
        @close="isArchiveModalOpen = false"
        @accept="handleArchive"
        title="Вы действительно хотите отправить в архив?"
        withButtons info/>
    <AlertModal
        target="#modal2"
        :isOpen="isDeleteModalOpen"
        @close="isDeleteModalOpen = false"
        @accept="handleDelete"
        title="Вы действительно хотите удалить?"
        withButtons info/>
  </PageContainer>
</template>