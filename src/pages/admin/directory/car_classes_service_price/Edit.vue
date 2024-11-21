<script setup>
import {onMounted, ref} from "vue";
import router from "@router";
import { useRoute } from 'vue-router';

import DirectoryService from "@services/DirectoryService.js";

import AlertModal from "@components/Modals/AlertModal.vue";
import PageContainer from "@components/Containers/Admin/PageContainer.vue";
import DirectoryCarClassesEditForm from "@components/Forms/Directory/Car/Classes/DirectoryCarClassesEditForm.vue";
import DirectoryCarClassesServicePriceEditForm
  from "@components/Forms/Directory/Car/ClassesServicePrice/DirectoryCarClassesServicePriceEditForm.vue";

const item = ref(null);
const route = useRoute();
const error = ref('');
const loading = ref(true);
const sending = ref(false);
const isAlertModalOpen = ref(false);
const isSuccessModalOpen = ref(false);
const isDeleteModalOpen = ref(false);

const directoryParentTitle = 'Классы авто и цены на услуги';
const directoryTitle = 'Редактирование цены на услуги для класса авто';
const directoryUrl = 'car_classes_service_price';
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

  DirectoryService.editCarClassServicePrice(data).then((response) => {
    if (response.data) {
      if (parseInt(response.data.error) === 0) {
        isSuccessModalOpen.value = true
      } else {
        error.value = response.data.error_text || response.data
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
  item.value = await DirectoryService.getCarClassServicePriceById({id: route.params.id});
  loading.value = false;
}

onMounted(fetchData);
</script>

<template>
  <PageContainer :loading="loading" :breadcrumbs="breadcrumbs">
    <DirectoryCarClassesServicePriceEditForm :title="directoryTitle" @onSubmit="handleEdit" @onDelete="isDeleteModalOpen = true" :item="item"/>

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