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
});
const emit = defineEmits(['onDone', 'onClose']);

const id = ref(-1);
const error = ref('');
const sending = ref(false);
const isAlertModalOpen = ref(false);
const isSuccessModalOpen = ref(false);
const isDeleteModalOpen = ref(false);

const handleDone = (data) => {
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
    <BaseModal
        :is-open="visible" :close-on-click-outside="false" @close="emit('onClose')"
        :title="item != null ? 'Редактирование операции' : 'Добавление операции'"
    >
        <BookingOperationAddForm v-if="item === null" @onSubmit="handleDone" :sending="sending" :card="false" title=""/>
        <BookingOperationEditForm v-else @onSubmit="handleDone" @onDelete="isDeleteModalOpen = true"
                                  :item="item" :sending="sending" :card="false" title=""/>
    </BaseModal>

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