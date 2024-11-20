<script setup>
import {ref} from "vue";
import AlertModal from "@components/Modals/AlertModal.vue";
import DirectoryService from "@services/DirectoryService.js";
import BaseModal from "@components/Modals/BaseModal.vue";
import DirectoryAddForm from "@components/Forms/Directory/DirectoryAddForm.vue";
import DirectoryCarModelsAddForm from "@components/Forms/Directory/Car/Models/DirectoryCarModelsAddForm.vue";

const props = defineProps({
  visible: {
    type: Boolean,
    required: false,
    default: false
  },
});
const emit = defineEmits(['onAdd', 'onClose']);

const id = ref(-1);
const error = ref('');
const isAlertModalOpen = ref(false);
const isSuccessModalOpen = ref(false);

const handleAdd = (data) => {
  DirectoryService.addCarModel(data).then((response) => {
    if (response.data) {
      if (parseInt(response.data.error) === 0) {
        id.value = parseInt(response.data.params.id);
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
  emit('onAdd', id.value);
}
</script>

<template>
  <BaseModal :is-open="visible" @close="emit('onClose')" title="Добавление модели авто">
    <DirectoryCarModelsAddForm @onSubmit="handleAdd" :card="false"  title=""/>
  </BaseModal>

  <AlertModal :isOpen="isSuccessModalOpen" @close="onSuccess" title="Запрос выполнен" accept/>
  <AlertModal :isOpen="isAlertModalOpen" @close="isAlertModalOpen = false" :title="error" info/>
</template>