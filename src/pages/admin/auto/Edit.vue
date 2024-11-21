<script setup>
import {onMounted, ref} from "vue";
import router from "@router";
import moment from "moment";

import AlertModal from "@components/Modals/AlertModal.vue";
import PageContainer from "@components/Containers/Admin/PageContainer.vue";
import AutoService from "@services/AutoService.js";
import AutoEditForm from "@components/Forms/Auto/AutoEditForm.vue";
import {useRoute} from "vue-router";
import ClientEditForm from "@components/Forms/Clients/ClientEditForm.vue";

const route = useRoute();
const item = ref(null);
const error = ref('');
const loading = ref(true);
const sending = ref(false);
const isAlertModalOpen = ref(false);
const isSuccessModalOpen = ref(false);
const isArchiveModalOpen = ref(false);
const isDeleteModalOpen = ref(false);

const breadcrumbs = ref([
  {
    name: 'Авто',
    route: '/Admin/auto',
  },
  {
    name: 'Редактирование авто',
    route: null
  }
]);

const handleEdit = (data) => {
  sending.value = true;

  let sendingData = {...data};
  sendingData.id = route.params.id;

  sendingData.pts_issued_date = sendingData.pts_issued_date && sendingData.pts_issued_date !== "Invalid date" ? moment(sendingData.pts_issued_date, 'DD.MM.YYYY').format('YYYY-MM-DD') : null;
  sendingData.pts_purchase_date = sendingData.pts_purchase_date && sendingData.pts_purchase_date !== "Invalid date" ? moment(sendingData.pts_purchase_date, 'DD.MM.YYYY').format('YYYY-MM-DD') : null;
  sendingData.sts_issued_date = sendingData.sts_issued_date && sendingData.sts_issued_date !== "Invalid date" ? moment(sendingData.sts_issued_date, 'DD.MM.YYYY').format('YYYY-MM-DD') : null;
  sendingData.sts_expire_date = sendingData.sts_expire_date && sendingData.sts_expire_date !== "Invalid date" ? moment(sendingData.sts_expire_date, 'DD.MM.YYYY').format('YYYY-MM-DD') : null;
  sendingData.date_park_enter = sendingData.date_park_enter && sendingData.date_park_enter !== "Invalid date" ? moment(sendingData.date_park_enter, 'DD.MM.YYYY').format('YYYY-MM-DD') : null;
  sendingData.date_park_exit = sendingData.date_park_exit && sendingData.date_park_exit !== "Invalid date" ? moment(sendingData.date_park_exit, 'DD.MM.YYYY').format('YYYY-MM-DD') : null;

  AutoService.edit(sendingData).then((response) => {
    if (response.data) {
      if (parseInt(response.data.error) === 0) {
        isSuccessModalOpen.value = true
        sending.value = false;
      } else {
        error.value = response.data.error_text
        isAlertModalOpen.value = true
        sending.value = false;
      }
    }
  });
}
const handleArchive = () => {
  sending.value = true;
  isDeleteModalOpen.value = false;

  let sendingData = {id: route.params.id};

  AutoService.archive(sendingData).then((response) => {
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

  AutoService.delete(sendingData).then((response) => {
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
  //router.push('/Admin/auto');
}

async function fetchData() {
  item.value = await AutoService.getById({id: route.params.id});
  item.value.id = parseInt(route.params.id);
  loading.value = false;
}

onMounted(fetchData);
</script>

<template>
  <PageContainer :breadcrumbs="breadcrumbs" :loading="loading">
    <AutoEditForm
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