<script setup>
import {onMounted, ref} from "vue";
import router from "@router";

import DirectoryService from "@services/DirectoryService.js";

import AlertModal from "@components/Modals/AlertModal.vue";
import PageContainer from "@components/Containers/Admin/PageContainer.vue";
import DirectoryAdvertisingTypesAddForm from "@components/Forms/Directory/DirectoryAdvertisingTypesAddForm.vue";

const error = ref('');
const loading = ref(false);
const isAlertModalOpen = ref(false);
const isSuccessModalOpen = ref(false);

const breadcrumbs = ref([
  {
    name: 'Виды рекламы',
    route: '/Admin/directory/advertising_types'
  },
  {
    name: 'Добавление вида рекламы',
    route: null
  }
]);

const handleAdd = (data) => {
  //console.log(data);

  DirectoryService.addAdvertisingTypes(data).then((response) => {
    //console.log(response.data);
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
  router.push('/Admin/directory/advertising_types');
}
</script>

<template>
  <PageContainer :breadcrumbs="breadcrumbs">
    <DirectoryAdvertisingTypesAddForm @onSubmit="handleAdd"/>

    <AlertModal :isOpen="isSuccessModalOpen" @close="onSuccess" title="Запрос выполнен" accept/>
    <AlertModal :isOpen="isAlertModalOpen" @close="isAlertModalOpen = false" :title="error" info/>
  </PageContainer>
</template>

<style scoped>

</style>