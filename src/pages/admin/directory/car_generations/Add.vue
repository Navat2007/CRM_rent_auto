<script setup>
import {ref} from "vue";
import router from "@router";

import DirectoryService from "@services/DirectoryService.js";

import AlertModal from "@components/Modals/AlertModal.vue";
import PageContainer from "@components/Containers/Admin/PageContainer.vue";
import DirectoryCarGenerationsAddForm
  from "@components/Forms/Directory/Car/Generations/DirectoryCarGenerationsAddForm.vue";

const error = ref('');
const loading = ref(false);
const isAlertModalOpen = ref(false);
const isSuccessModalOpen = ref(false);

const directoryParentTitle = 'Поколение авто';
const directoryTitle = 'Добавление поколения авто';
const directoryUrl = 'car_generations';
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
  DirectoryService.addCarGeneration(data).then((response) => {
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
    <DirectoryCarGenerationsAddForm :title="directoryTitle" @onSubmit="handleAdd"/>

    <AlertModal :isOpen="isSuccessModalOpen" @close="onSuccess" title="Запрос выполнен" accept/>
    <AlertModal :isOpen="isAlertModalOpen" @close="isAlertModalOpen = false" :title="error" info/>
  </PageContainer>
</template>

<style scoped>

</style>