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

const sending = ref(false);
const inputPassport = ref();
const inputDL = ref();
const error = ref('');
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
    // TODO: Send image to OCR API
    sending.value = false;

    console.log(iam);
  }

  return;

  await fetch("", {
    method: "POST",
    headers: {
      "Content-Type": "application/json"
    },
    body: JSON.stringify({
      "mimeType": "",
      "languageCodes": ["*"],
      "model": "passport",
      "content": base64
    })
  })
}
const getOCRDL = async (e) => {
  const base64 = await getBase64(e);
}
</script>

<template>
  <input type="file" class="hidden" ref="inputPassport" @change="getOCRPassport" :accept="accept"/>
  <input type="file" class="hidden" ref="inputDL" @change="getOCRDL" :accept="accept"/>
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