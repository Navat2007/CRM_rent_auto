<script setup>
import {ref} from "vue";
import router from "@router";

import DirectoryService from "@services/DirectoryService.js";

import AlertModal from "@components/Modals/AlertModal.vue";
import PageContainer from "@components/Containers/Admin/PageContainer.vue";
import DirectoryCarClassesAddForm from "@components/Forms/Directory/Car/Classes/DirectoryCarClassesAddForm.vue";
import DirectoryCarClassesServicePriceAddForm
  from "@components/Forms/Directory/Car/ClassesServicePrice/DirectoryCarClassesServicePriceAddForm.vue";

const error = ref('');
const loading = ref(false);
const isAlertModalOpen = ref(false);
const isSuccessModalOpen = ref(false);

const directoryParentTitle = 'Классы авто и цены на услуги';
const directoryTitle = 'Добавление цены на услуги для класса авто';
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

const handleAdd = (data) => {
  DirectoryService.addCarClassServicePrice(data).then((response) => {
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

const onSuccess = () => {
  isSuccessModalOpen.value = false;
  router.push('/Admin/directory/' + directoryUrl);
}
</script>

<template>
  <PageContainer :breadcrumbs="breadcrumbs">
    <DirectoryCarClassesServicePriceAddForm :title="directoryTitle" @onSubmit="handleAdd"/>

    <AlertModal :isOpen="isSuccessModalOpen" @close="onSuccess" title="Запрос выполнен" accept/>
    <AlertModal :isOpen="isAlertModalOpen" @close="isAlertModalOpen = false" :title="error" info/>
  </PageContainer>
</template>

<style scoped>

</style>