<script setup>
import {ref} from "vue";
import AlertModal from "@components/Modals/AlertModal.vue";
import DirectoryService from "@services/DirectoryService.js";
import BaseModal from "@components/Modals/BaseModal.vue";
import DirectoryAddForm from "@components/Forms/Directory/DirectoryAddForm.vue";
import BookingOperationsService from "@services/BookingOperationsService.js";
import AutoService from "@services/AutoService.js";
import BookingOperationAddForm from "@components/Forms/Booking/BookingOperationAddForm.vue";
import BookingOperationEditForm from "@components/Forms/Booking/BookingOperationEditForm.vue";
import AutoEditForm from "@components/Forms/Auto/AutoEditForm.vue";
import {useRoute} from "vue-router";
import moment from "moment/moment.js";

const route = useRoute();
const props = defineProps({
    visible: {
        type: Boolean,
        required: false,
        default: false
    },
    item: {
        type: Object,
        required: false,
        default: null
    },
    addItem: {
        type: Object,
        required: false,
        default: null
    },
    carState:{
        type: Object,
        required: true
    },
});
const emit = defineEmits(['onDone', 'onClose']);

const id = ref(-1);
const error = ref('');
const sending = ref(false);
const isAlertModalOpen = ref(false);
const isSuccessModalOpen = ref(false);
const isDeleteModalOpen = ref(false);

const handleDone = (data) => {
    data.booking_id = route.params.id;
    console.log(data);

    data.accrued = data.accrued === null ? 0 : data.accrued;
    data.paid = data.paid === null ? 0 : data.paid;
    data.tariff = data.tariff === null ? 0 : data.tariff;
    data.date = data.date && data.date !== "Invalid date" ? moment(data.date, 'DD.MM.YYYY HH:mm').format('YYYY-MM-DD HH:mm') : null;
    data.period_from = data.period_from && data.period_from !== "Invalid date" ? moment(data.period_from, 'DD.MM.YYYY HH:mm').format('YYYY-MM-DD HH:mm') : null;
    data.period_to = data.period_to && data.period_to !== "Invalid date" ? moment(data.period_to, 'DD.MM.YYYY HH:mm').format('YYYY-MM-DD HH:mm') : null;

    if (props.item) {
        data.id = props.item.id;

        BookingOperationsService.edit(data).then((response) => {
            if (response.data) {
                if (parseInt(response.data.error) === 0) {
                    isSuccessModalOpen.value = true
                } else {
                    error.value = response.data.error_text
                    isAlertModalOpen.value = true
                }
            }
        });
    } else {
        BookingOperationsService.add(data).then((response) => {
            if (response.data) {
                if (parseInt(response.data.error) === 0) {
                    id.value = parseInt(response.data.params.id);
                    isSuccessModalOpen.value = true
                } else {
                    error.value = response.data.error_text
                    isAlertModalOpen.value = true
                }
            }
        });
    }
}
const handleDelete = () => {
    sending.value = true;
    isDeleteModalOpen.value = false;

    let sendingData = {id: props.item.id};

    BookingOperationsService.delete(sendingData).then((response) => {
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
    isSuccessModalOpen.value = false;
    emit('onDone', id.value);
}
</script>

<template>
    <Dialog
        v-model:visible="props.visible" modal
        v-on:update:visible="() => emit('onClose')"
        :header="item != null ? 'Редактирование операции' : 'Добавление операции'"
        :style="{ width: '50vw' }" :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
        pt:mask:class="backdrop-blur-sm"
    >
        <BookingOperationAddForm v-if="item === null" :item="addItem" :carState="carState" @onSubmit="handleDone" :sending="sending" :card="false" title=""/>
        <BookingOperationEditForm v-else :carState="carState" @onSubmit="handleDone" @onDelete="isDeleteModalOpen = true"
                                  :item="item" :sending="sending" :card="false" title=""/>
    </Dialog>

    <AlertModal :isOpen="isSuccessModalOpen" @close="onSuccess" title="Запрос выполнен" accept/>
    <AlertModal :isOpen="isAlertModalOpen" @close="isAlertModalOpen = false" :title="error" info/>
    <AlertModal
        target="#modal2"
        :isOpen="isDeleteModalOpen"
        @close="isDeleteModalOpen = false"
        @accept="handleDelete"
        title="Вы действительно хотите удалить?"
        withButtons info/>
</template>