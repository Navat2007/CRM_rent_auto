<script setup>
import {onMounted, ref} from "vue";
import router from "@router";
import { useRoute } from 'vue-router';

import DirectoryService from "@services/DirectoryService.js";

import AlertModal from "@components/Modals/AlertModal.vue";
import PageContainer from "@components/Containers/Admin/PageContainer.vue";
import DirectoryEditForm from "@components/Forms/Directory/DirectoryEditForm.vue";
import DirectoryServicesEditForm from "@components/Forms/Directory/Services/DirectoryServicesEditForm.vue";
import DirectoryPaymentTypesEditForm from "@components/Forms/Directory/PaymentTypes/DirectoryPaymentTypesEditForm.vue";

const item = ref(null);
const route = useRoute();
const error = ref('');
const loading = ref(true);
const sending = ref(false);
const isAlertModalOpen = ref(false);
const isSuccessModalOpen = ref(false);
const isDeleteModalOpen = ref(false);

const directoryParentTitle = 'Виды оплаты';
const directoryTitle = 'Редактирование вида оплаты';
const directoryUrl = 'payment_types';
const breadcrumbs = ref([
  {
    name: directoryParentTitle,
    route: '/Admin/directory/' + directoryUrl
  },
  {
    name: directoryTitle,
    route: null
  }
]);

const handleEdit = (data) => {
  data.id = route.params.id;

  DirectoryService.editPaymentType(data).then((response) => {
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
const handleDelete = () => {
  let sendingData = {
    id: route.params.id,
    directory: 'directory_' + directoryUrl,
  };
  isDeleteModalOpen.value = false;
  sending.value = true;

  DirectoryService.delete(sendingData).then((response) => {
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
  router.push('/Admin/directory/' + directoryUrl);
}

async function fetchData() {
  item.value = await DirectoryService.getById({directory: 'directory_' + directoryUrl, id: route.params.id});
  loading.value = false;
}

onMounted(fetchData);
</script>

<template>
  <PageContainer :loading="loading" :breadcrumbs="breadcrumbs">
    <DirectoryPaymentTypesEditForm :title="directoryTitle" @onSubmit="handleEdit" @onDelete="isDeleteModalOpen = true" :item="item"/>

    <AlertModal :isOpen="isSuccessModalOpen" @close="onSuccess" title="Запрос выполнен" accept/>
    <AlertModal :isOpen="isAlertModalOpen" @close="isAlertModalOpen = false" :title="error" info/>
    <AlertModal
        target="#modal2"
        :isOpen="isDeleteModalOpen"
        @close="isDeleteModalOpen = false"
        @accept="handleDelete"
        title="Вы действительно хотите удалить?"
        withButtons info/>
  </PageContainer>
</template>

<style scoped>

</style>