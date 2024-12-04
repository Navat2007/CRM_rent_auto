<script setup>
import {ref} from "vue";
import router from "@router";
import moment from "moment";

import AlertModal from "@components/Modals/AlertModal.vue";
import PageContainer from "@components/Containers/Admin/PageContainer.vue";
import AutoService from "@services/AutoService.js";
import AutoAddForm from "@components/Forms/Auto/AutoAddForm.vue";

const carID = ref(null);
const error = ref('');
const loading = ref(false);
const sending = ref(false);
const isAlertModalOpen = ref(false);
const isSuccessModalOpen = ref(false);

const breadcrumbs = ref([
  {
    name: 'Бронирование',
    route: null
  },
  {
    name: 'Договора проката',
    route: '/Admin/booking/rentalContracts/'
  },
  {
    name: 'Добавление договора проката',
    route: null
  }
]);

const handleAdd = (data) => {
  sending.value = true;

  let sendingData = {...data};

  sendingData.pts_issued_date = sendingData.pts_issued_date && sendingData.pts_issued_date !== "Invalid date" ? moment(sendingData.pts_issued_date, 'DD.MM.YYYY').format('YYYY-MM-DD') : null;
  sendingData.pts_purchase_date = sendingData.pts_purchase_date && sendingData.pts_purchase_date !== "Invalid date" ? moment(sendingData.pts_purchase_date, 'DD.MM.YYYY').format('YYYY-MM-DD') : null;
  sendingData.sts_issued_date = sendingData.sts_issued_date && sendingData.sts_issued_date !== "Invalid date" ? moment(sendingData.sts_issued_date, 'DD.MM.YYYY').format('YYYY-MM-DD') : null;
  sendingData.sts_expire_date = sendingData.sts_expire_date && sendingData.sts_expire_date !== "Invalid date" ? moment(sendingData.sts_expire_date, 'DD.MM.YYYY').format('YYYY-MM-DD') : null;
  sendingData.date_park_enter = sendingData.date_park_enter && sendingData.date_park_enter !== "Invalid date" ? moment(sendingData.date_park_enter, 'DD.MM.YYYY').format('YYYY-MM-DD') : null;
  sendingData.date_park_exit = sendingData.date_park_exit && sendingData.date_park_exit !== "Invalid date" ? moment(sendingData.date_park_exit, 'DD.MM.YYYY').format('YYYY-MM-DD') : null;

  AutoService.add(sendingData).then((response) => {
    if (response.data) {
      if (parseInt(response.data.error) === 0) {
        isSuccessModalOpen.value = true
        carID.value = response.data.params.id;
        sending.value = false;
      } else {
        error.value = response.data.error_text
        isAlertModalOpen.value = true
        sending.value = false;
      }
    }
  });
}

const onSuccess = () => {
  isSuccessModalOpen.value = false;
  router.push('/Admin/auto/' + carID.value);
}
</script>

<template>
  <PageContainer :breadcrumbs="breadcrumbs">
    <AutoAddForm @onSubmit="handleAdd" :sending="sending"/>

    <AlertModal :isOpen="isSuccessModalOpen" @close="onSuccess" title="Запрос выполнен" accept/>
    <AlertModal :isOpen="isAlertModalOpen" @close="isAlertModalOpen = false" :title="error" info/>
  </PageContainer>
</template>