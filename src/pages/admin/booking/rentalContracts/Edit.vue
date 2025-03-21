<script setup>
import {onMounted, ref} from "vue";
import router from "@router";
import moment from "moment";
import {useRoute} from "vue-router";

import AlertModal from "@components/Modals/AlertModal.vue";
import PageContainer from "@components/Containers/Admin/PageContainer.vue";
import BookingEditForm from "@components/Forms/Booking/BookingEditForm.vue";
import BookingService from "@services/BookingService.js";

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
        name: 'Бронирование',
        route: null
    },
    {
        name: 'Договора проката',
        route: '/Admin/booking/rentalContracts/'
    },
    {
        name: 'Редактирование договора проката',
        route: null
    }
]);

const handleEdit = (data) => {
    sending.value = true;

    let sendingData = {...data};
    sendingData.id = route.params.id;

    if (!sendingData.deposit || sendingData.deposit === '') {
        sendingData.deposit = 0;
    }

    if (!sendingData.mileage_start || sendingData.mileage_start === '') {
        sendingData.mileage_start = 0;
    }

    if (!sendingData.mileage_end || sendingData.mileage_end === '') {
        sendingData.mileage_end = 0;
    }

    sendingData.start_date = sendingData.start_date && sendingData.start_date !== "Invalid date" ? moment(sendingData.start_date, 'DD.MM.YYYY HH:mm').format('YYYY-MM-DD HH:mm') : null;
    sendingData.end_date = sendingData.end_date && sendingData.end_date !== "Invalid date" ? moment(sendingData.end_date, 'DD.MM.YYYY HH:mm').format('YYYY-MM-DD HH:mm') : null;

    if (sendingData.directory_territory_car_use_id === '' || sendingData.directory_territory_car_use_id === null) {
        sendingData.directory_territory_car_use_id = 0;
    }

    BookingService.edit(sendingData).then((response) => {
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

    BookingService.archive(sendingData).then((response) => {
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

    BookingService.delete(sendingData).then((response) => {
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
    //router.push('/Admin/booking/rentalContracts');
}

async function fetchData() {
    item.value = await BookingService.getById({id: route.params.id});
    item.value.id = parseInt(route.params.id);
    loading.value = false;
}

onMounted(fetchData);
</script>

<template>
    <PageContainer :breadcrumbs="breadcrumbs" :loading="loading">
        <BookingEditForm
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