<script setup>
import {ref} from "vue";
import router from "@router";
import moment from "moment";

import AlertModal from "@components/Modals/AlertModal.vue";
import PageContainer from "@components/Containers/Admin/PageContainer.vue";
import BookingService from "@services/BookingService.js";
import BookingAddForm from "@components/Forms/Booking/BookingAddForm.vue";

const bookingID = ref(null);
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

  sendingData.start_date = sendingData.start_date && sendingData.start_date !== "Invalid date" ? moment(sendingData.start_date, 'DD.MM.YYYY HH:mm').format('YYYY-MM-DD HH:mm') : null;
  sendingData.end_date = sendingData.end_date && sendingData.end_date !== "Invalid date" ? moment(sendingData.end_date, 'DD.MM.YYYY HH:mm').format('YYYY-MM-DD HH:mm') : null;

  BookingService.add(sendingData).then((response) => {
    if (response.data) {
      if (parseInt(response.data.error) === 0) {
        isSuccessModalOpen.value = true
        bookingID.value = response.data.params.id;
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
  router.push('/Admin/booking/rentalContracts/');
}
</script>

<template>
  <PageContainer :breadcrumbs="breadcrumbs">
    <BookingAddForm @onSubmit="handleAdd" :sending="sending"/>

    <AlertModal :isOpen="isSuccessModalOpen" @close="onSuccess" title="Запрос выполнен" accept/>
    <AlertModal :isOpen="isAlertModalOpen" @close="isAlertModalOpen = false" :title="error" info/>
  </PageContainer>
</template>