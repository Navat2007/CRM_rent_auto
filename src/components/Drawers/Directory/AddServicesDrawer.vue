<script setup>
import {PlusRound} from "@vicons/material";
import {Icon} from "@vicons/utils";
import DirectoryService from "@services/DirectoryService.js";
import {ref} from "vue";
import AlertModal from "@components/Modals/AlertModal.vue";
import DirectoryServicesAddForm from "@components/Forms/Directory/Services/DirectoryServicesAddForm.vue";

const props = defineProps({
  visible: {
    required: true,
    type: Boolean,
    default: false
  },
});
const emit = defineEmits(['onAdd', 'onClose']);

const id = ref(-1);
const error = ref('');
const isAlertModalOpen = ref(false);
const isSuccessModalOpen = ref(false);

const handleAdd = (data) => {
  DirectoryService.addService(data).then((response) => {
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
  emit('onClose');
}
</script>

<template>
  <Drawer position="right" class="w-full md:w-2/5"
          v-bind:visible="visible"
          v-on:update:visible="() => emit('onClose')"
  >
    <template #header>
      <div class="flex items-center gap-2">
        <Icon size="24" color="green"><PlusRound/></Icon>
        <span class="font-bold">Добавление услуги бронирования</span>
      </div>
    </template>
    <DirectoryServicesAddForm @onSubmit="handleAdd" :card="false" title=""/>
  </Drawer>

  <AlertModal :isOpen="isSuccessModalOpen" @close="onSuccess" title="Запрос выполнен" accept/>
  <AlertModal :isOpen="isAlertModalOpen" @close="isAlertModalOpen = false" :title="error" info/>
</template>