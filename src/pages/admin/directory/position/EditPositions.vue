<script setup>
import {onMounted, ref} from "vue";
import router from "@router";
import { useRoute } from 'vue-router';

import DirectoryService from "@services/DirectoryService.js";

import AlertModal from "@components/Modals/AlertModal.vue";
import PageContainer from "@components/Containers/Admin/PageContainer.vue";
import DirectoryPositionEditForm from "@components/Forms/Directory/DirectoryPositionEditForm.vue";

const item = ref(null);
const route = useRoute();
const error = ref('');
const loading = ref(true);
const isAlertModalOpen = ref(false);
const isSuccessModalOpen = ref(false);

const breadcrumbs = ref([
  {
    name: 'Должности',
    route: '/Admin/directory/positions'
  },
  {
    name: 'Редактирование должности',
    route: null
  }
]);

const handleEdit = (data) => {
  //console.log(data);

  data.id = route.params.id;

  DirectoryService.editPositions(data).then((response) => {
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
  router.push('/Admin/directory/positions');
}

async function fetchData() {
  item.value = await DirectoryService.getPositionsById(route.params.id);
  loading.value = false;
}

onMounted(fetchData);
</script>

<template>
  <PageContainer :loading="loading" :breadcrumbs="breadcrumbs">
    <DirectoryPositionEditForm @onSubmit="handleEdit" :item="item"/>

    <AlertModal :isOpen="isSuccessModalOpen" @close="onSuccess" title="Запрос выполнен" accept/>
    <AlertModal :isOpen="isAlertModalOpen" @close="isAlertModalOpen = false" :title="error" info/>
  </PageContainer>
</template>