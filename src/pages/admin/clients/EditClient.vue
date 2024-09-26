<script setup>
import {ref, onMounted} from "vue";
import { useRoute } from 'vue-router';
import router from "@router";
import moment from "moment";

import ClientsService from "@services/ClientsService.js";

import AlertModal from "@components/Modals/AlertModal.vue";
import EmployerEditForm from "@components/Forms/Employers/EmployerEditForm.vue";
import PageContainer from "@components/Containers/Admin/PageContainer.vue";
import ClientEditForm from "@components/Forms/Clients/ClientEditForm.vue";

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
    name: 'Клиенты',
    route: '/Admin/clients'
  },
  {
    name: 'Редактирование клиента',
    route: null
  }
]);

const handleEdit = (data) => {
  sending.value = true;

  let sendingData = {...data};
  sendingData.id = route.params.id;

  sendingData.birthday = sendingData.birthday && sendingData.birthday !== "Invalid date" ? moment(sendingData.birthday, 'DD.MM.YYYY').format('YYYY-MM-DD') : null;
  sendingData.passport_date_of_issue = sendingData.passport_date_of_issue && sendingData.passport_date_of_issue !== "Invalid date" ? moment(sendingData.passport_date_of_issue, 'DD.MM.YYYY').format('YYYY-MM-DD') : null;
  sendingData.dl_issued_date = sendingData.dl_issued_date && sendingData.dl_issued_date !== "Invalid date" ? moment(sendingData.dl_issued_date, 'DD.MM.YYYY').format('YYYY-MM-DD') : null;
  sendingData.dl_expire_date = sendingData.dl_expire_date && sendingData.dl_expire_date !== "Invalid date" ? moment(sendingData.dl_expire_date, 'DD.MM.YYYY').format('YYYY-MM-DD') : null;

  ClientsService.editClient(sendingData).then((response) => {
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
  isDeleteModalOpen.value = false;

  let sendingData = {id: route.params.id};

  ClientsService.archiveClient(sendingData).then((response) => {
    if (response.data) {
      if (parseInt(response.data.error) === 0) {
        isSuccessModalOpen.value = true
        router.push('/Admin/clients/');
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

  ClientsService.deleteClient(sendingData).then((response) => {
    if (response.data) {
      if (parseInt(response.data.error) === 0) {
        isSuccessModalOpen.value = true
        router.push('/Admin/clients/');
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
  // router.push('/Admin/employers/');
}

async function fetchData() {
  item.value = await ClientsService.getClientById(route.params.id);
  loading.value = false;
}

onMounted(fetchData);
</script>

<template>
  <PageContainer :loading="loading" :breadcrumbs="breadcrumbs">
    <ClientEditForm
        @onSubmit="handleEdit"
        @onArchive="isArchiveModalOpen = true"
        @onDelete="isDeleteModalOpen = true"
        :item="item" :sending="sending"
    />

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