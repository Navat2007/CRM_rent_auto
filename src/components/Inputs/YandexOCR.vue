<script setup>
import {ref} from "vue";
import AlertModal from "@components/Modals/AlertModal.vue";
import YandexService from "@services/YandexService.js";

const props = defineProps({
  maxFileSize: {
    type: Number,
    required: false,
    default: 20
  },
  accept: {
    type: String,
    required: false,
    default: "image/*"
  }
});
const emit = defineEmits(['onPassportResult', 'onDriverLicenseResult']);

const sending = ref(false);
const inputPassport = ref();
const inputDL = ref();
const error = ref('');
const imageInputKey = ref('');
const isAlertModalOpen = ref(false);

const getBase64 = async (e) => {
  async function readFileAsDataURL(file) {
    return await new Promise((resolve) => {
      let fileReader = new FileReader();
      fileReader.onload = (e) => resolve(fileReader.result);
      fileReader.readAsDataURL(file);
    });
  }

  let match = props.accept.split(",").join("|");

  if (e.target.files.length > 0) {
    let file = e.target.files[0];

    if (props.accept === "*.*" || file.type.match(match)) {
      if (file.size <= props.maxFileSize * 1000000) {
      } else {
        error.value = "Файл больше " + props.maxFileSize + " Мб.";
        isAlertModalOpen.value = true;
        return;
      }
    } else {
      error.value = "Файл должен быть изображением."
      isAlertModalOpen.value = true;
      return;
    }

    return await readFileAsDataURL(file);
  }
};
const getOCRPassport = async (e) => {
  const base64 = await getBase64(e);

  if(base64){
    sending.value = true;
    const iam = await YandexService.getIAM();

    if(!iam){
      error.value = "Не удалось получить IAM-ключ от Яндекс";
      isAlertModalOpen.value = true;
      return;
    }

    const result = await YandexService.recognizePassport(base64);

    if(result && result.error?.message){
      error.value = result.error.message;
      isAlertModalOpen.value = true;
    }
    else {
      result.files = e;
      emit('onPassportResult', result);
    }

    sending.value = false;
  }

  imageInputKey.value = Math.random().toString(36).slice(2);
}
const getOCRDL = async (e) => {
  const base64 = await getBase64(e);

  if(base64){
    sending.value = true;
    const iam = await YandexService.getIAM();

    if(!iam){
      error.value = "Не удалось получить IAM-ключ от Яндекс";
      isAlertModalOpen.value = true;
      return;
    }

    const result = await YandexService.recognizeDriverLicense(base64);

    if(result && result.error?.message){
      error.value = result.error.message;
      isAlertModalOpen.value = true;
    }
    else {
      result.files = e;
      emit('onDriverLicenseResult', result);
    }

    sending.value = false;
  }

  imageInputKey.value = Math.random().toString(36).slice(2);
}
</script>

<template>
  <input type="file" class="hidden" ref="inputPassport" :key={imageInputKey} @change="getOCRPassport" :accept="accept"/>
  <input type="file" class="hidden" ref="inputDL" :key={imageInputKey} @change="getOCRDL" :accept="accept"/>
  <div>
    <Button icon="pi pi-upload" label="Заполнить по Паспорту" :loading="sending" outlined
            @click="inputPassport.click()"/>
    <Button icon="pi pi-upload" label="Заполнить по ВУ" class="ml-4" :loading="sending" outlined
            @click="inputDL.click()"/>
  </div>

  <AlertModal :isOpen="isAlertModalOpen" @close="isAlertModalOpen = false" :title="error" info/>
</template>

<style scoped>

</style>